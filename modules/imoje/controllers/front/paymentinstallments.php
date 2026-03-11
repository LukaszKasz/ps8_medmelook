<?php

use Imoje\Payment\Api;
use Imoje\Payment\Util;

/**
 * Class ImojePaymentinstallmentsModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class ImojePaymentinstallmentsModuleFrontController extends ModuleFrontController
{

	const IMOJE_INSTALLMENTS = 'imoje_installments';

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

		if(!$cart->date_upd) {
			PrestaShopLogger::addLog($this->module->l('Missing cart', 'paymentinstallments'));
			Tools::redirect('index');

			return;
		}

		$imojeSelectedChannel = Tools::getValue('imoje-selected-channel');
		$imojeInstallmentsPeriod = Tools::getValue('imoje-installments-period');;

		if (!$imojeSelectedChannel) {
			$imojeSelectedChannel = 'inbank';
		}

		if (!$imojeInstallmentsPeriod) {
			$imojeInstallmentsPeriod = 3;
		}

		$imojeToken = Configuration::get('IMOJE_TOKEN');
		$merchantId = Configuration::get('IMOJE_MERCHANT_ID');
		$serviceId = Configuration::get('IMOJE_SERVICE_ID');

		if(!$imojeToken
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
			PrestaShopLogger::addLog($this->module->l('Bad response in api, errors:', 'paymentinstallments')
				. ' '
				. $service['data']['body']);
			Tools::redirect('index');

			return;
		}

		if(!isset($service['body']['service']['isActive']) && $service['body']['service']['isActive']) {
			PrestaShopLogger::addLog($this->module->l('Service is inactive in imoje', 'paymentinstallments'));

			Tools::redirect('index');

			return;
		}

		$paymentMethodList = $service['body']['service']['paymentMethods'];

		$isValid = false;

		foreach($paymentMethodList as $paymentMethod) {

			if(($paymentMethod['paymentMethodCode'] === $imojeSelectedChannel)
				&& ($paymentMethod['paymentMethod'] === self::IMOJE_INSTALLMENTS)
				&& $paymentMethod['isActive']) {

				$isValid = true;
			}
		}

		if(!$isValid) {
			PrestaShopLogger::addLog(
				sprintf('%s %s %s, %s %s.',
					$this->module->l('Payment channel is inactive or offline or was not found.', 'paymentinstallments'),
					$this->module->l('Code:', 'paymentinstallments'),
					$imojeSelectedChannel,
					$this->module->l('Method:', 'paymentinstallments'),
					self::IMOJE_INSTALLMENTS)
			);

			Tools::redirect('index');

			return;
		}

		$imoje = new Imoje();

		$transaction = $imojeApi->createTransaction($imoje->getDataForRequestToApi(
			$cart,
			self::IMOJE_INSTALLMENTS,
			$imojeSelectedChannel,
			'',
			$imojeInstallmentsPeriod)
		);

		if(!$transaction['success']) {
			PrestaShopLogger::addLog(
				$this->module->l('Could not make imoje transaction. Details:', 'paymentinstallments')
				. ' '
				. json_encode($transaction)
			);

			Tools::redirect('index');

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
