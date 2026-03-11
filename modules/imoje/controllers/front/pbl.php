<?php

use Imoje\Payment\Util;

/**
 * Class ImojePblModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class ImojePblModuleFrontController extends ModuleFrontController
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

		$imojeToken = Configuration::get('IMOJE_TOKEN');
		$merchantId = Configuration::get('IMOJE_MERCHANT_ID');
		$serviceId = Configuration::get('IMOJE_SERVICE_ID');

		if(!$cart->date_upd
			|| !$imojeToken
			|| !$merchantId
			|| !$serviceId) {
			Tools::redirect('index');

			return;
		}

		$imojeApi = new \Imoje\Payment\Api(
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
				. $service['data']['body']);
			Tools::redirect('index');
		}

		if(!isset($service['body']['service']['isActive']) && $service['body']['service']['isActive']) {
			PrestaShopLogger::addLog($this->module->l('Service is inactive in imoje', 'pbl'));

			Tools::redirect('index');
		}

		$imoje = new Imoje();

		$service = $service['body']['service'];

		$currencyInfo = Currency::getCurrency($cart->id_currency);
		$currencyIsoCode = $currencyInfo['iso_code'];

		// TODO: uncomment when api will be fine with limits
		//        $cartTotal = Util::convertAmountToFractional($cart->getOrderTotal());

		$paymentMethodList = [];

		foreach($service['paymentMethods'] as $paymentMethod) {

			$pm = strtolower($paymentMethod['paymentMethod']);

			if($paymentMethod['isActive'] && (
					($pm === Util::getPaymentMethod('pbl'))
					|| ($pm === Util::getPaymentMethod('ing'))
				)
				&& strtolower($paymentMethod['currency']) === strtolower($currencyIsoCode)

				// TODO: uncomment when api will be fine with limits
				//                && (
				//                    isset($paymentMethod['transactionLimits']['maxTransaction']['value'])
				//                    && $paymentMethod['transactionLimits']['maxTransaction']['value']
				//                )
				//                && $paymentMethod['transactionLimits']['maxTransaction']['value'] > $cartTotal
				//                && (
				//                    isset($paymentMethod['transactionLimits']['minTransaction']['value'])
				//                    && $paymentMethod['transactionLimits']['minTransaction']['value']
				//                )
				//                && $paymentMethod['transactionLimits']['minTransaction']['value'] < $cartTotal
			) {

				$logo = Util::getPaymentMethodCodeLogo($paymentMethod['paymentMethodCode']);

				foreach($paymentMethod['paymentMethodCodeImage'] as $imgList) {
					if(isset($imgList['png']) && $imgList['png']) {
						$logo = $imgList['png'];
					}
				}

				$paymentMethodList[] = [
					'paymentMethod'     => $paymentMethod['paymentMethod'],
					'paymentMethodCode' => $paymentMethod['paymentMethodCode'],
					'description'       => $paymentMethod['description'],
					'isAvailable'       => $paymentMethod['isOnline'],
					'logo'              => $logo,
				];
			}
		}

		$this->context->smarty->assign([
			'payment_method_list' => $paymentMethodList,
			'loading_gif'         => Imoje::getMediaPath(_PS_MODULE_DIR_ . $imoje->name . '/assets/img/loading.gif'),
			'payment_link'        => $this->context->link->getModuleLink('imoje', 'paymentpbl'),
		]);

		$this->setTemplate(Imoje::buildTemplatePath('pbl', 'front'));
	}
}
