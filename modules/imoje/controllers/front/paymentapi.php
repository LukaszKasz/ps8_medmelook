<?php

use Imoje\Payment\Api;
use Imoje\Payment\Util;

/**
 * Class ImojePaymentapiModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class ImojePaymentapiModuleFrontController extends ModuleFrontController
{

	/**
	 * @throws PrestaShopException
	 * @see FrontController::init()
	 */
	public function init()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;
		parent::init();
	}

	/**
	 * @throws PrestaShopException
	 * @throws Exception
	 */
	public function initContent()
	{
		parent::initContent();

		$cart = $this->context->cart;

		$imojePm = Tools::getValue('imoje-pm');

		if(!is_string($imojePm) || (strpos($imojePm, ':') === false)) {

			Tools::redirect('index');

			return;
		}

		$explodeImojePm = explode(':', $imojePm);
		$pm = $explodeImojePm[0];
		$pmc = $explodeImojePm[1];

		$imojeToken = Configuration::get('IMOJE_TOKEN');
		$merchantId = Configuration::get('IMOJE_MERCHANT_ID');
		$serviceId = Configuration::get('IMOJE_SERVICE_ID');

		if(!$cart->date_upd
			|| !$pm
			|| !$pmc
			|| !$imojeToken
			|| !$merchantId
			|| !$serviceId) {
			Tools::redirect('index');

			return;
		}

		$imojeApi = new Api(
			$imojeToken,
			$merchantId,
			$serviceId,
			Configuration::get('IMOJE_SANDBOX')
				? Util::ENVIRONMENT_SANDBOX
				: Util::ENVIRONMENT_PRODUCTION
		);

		$service = $imojeApi->getServiceInfo();

		if(!$service['success']) {
			PrestaShopLogger::addLog($this->module->l('Bad response in api, errors:', 'pbl')
				. ' '
				. $service['data']['body'], 3, null, 'imoje');
			Tools::redirect('index');
		}

		if(!isset($service['body']['service']['isActive']) && $service['body']['service']['isActive']) {
			PrestaShopLogger::addLog($this->module->l('Service is inactive in imoje', 'pbl'), 2, null, 'imoje');

			Tools::redirect('index');
		}

		$paymentMethodList = $service['body']['service']['paymentMethods'];

		$isValid = false;

		foreach($paymentMethodList as $paymentMethod) {

			if(($paymentMethod['paymentMethodCode'] === $pmc)
				&& ($paymentMethod['paymentMethod'] === $pm)
				&& $paymentMethod['isActive']) {

				$isValid = true;
			}
		}

		if(!$isValid) {
			PrestaShopLogger::addLog(
				sprintf('%s %s %s, %s %s.',
					$this->module->l('Payment channel is inactive or offline or was not found.', 'paymentpbl'),
					$this->module->l('Code:', 'paymentpbl'),
					$pmc,
					$this->module->l('Method:', 'paymentpbl'),
					$pm), 2, null, 'imoje'
			);

			Tools::redirect('index');
		}

		$imoje = new Imoje();

		$transaction = $imojeApi->createTransaction($imoje->getDataForRequestToApi(
			$cart,
			$pm,
			$pmc)
		);

		if(!$transaction['success']) {

			PrestaShopLogger::addLog($this->module->l('Could not initialize transaction:', 'pbl')
				. ' '
				. json_encode( $transaction ), 3, null, 'imoje');

			Tools::redirect($this->context->link->getPageLink('history', true));

			return;
		}

		$this->context->smarty->assign([
			'form'                    => $imojeApi->buildOrderForm($transaction),
			'ga_key'                  => Configuration::get('IMOJE_GA_KEY'),
			'pbl_msg'                 => true,
			'checkout_link'           => $this->context->link->getPageLink(Configuration::get('PS_ORDER_PROCESS_TYPE')
				? 'order-opc'
				: 'order'),
			'text_return_to_checkout' => $this->module->l('Please wait, you will be returned to checkout.', 'paymentpbl'),
		]);

		$this->setTemplate(Imoje::buildTemplatePath('pay', 'front'));
	}
}
