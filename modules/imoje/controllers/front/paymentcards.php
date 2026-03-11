<?php

use Imoje\Payment\Paywall;
use Imoje\Payment\Util;

/**
 * Class ImojePaymentcardsModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class ImojePaymentcardsModuleFrontController extends ModuleFrontController
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

		$paywall = new Paywall(
			Configuration::get('IMOJE_SANDBOX')
				? Util::ENVIRONMENT_SANDBOX
				: Util::ENVIRONMENT_PRODUCTION,
			Configuration::get('IMOJE_SERVICE_KEY'),
			Configuration::get('IMOJE_SERVICE_ID'),
			Configuration::get('IMOJE_MERCHANT_ID')
		);

		$cart = $this->context->cart;

		if(!$cart->date_upd) {
			Tools::redirect('/');

			return;
		}

		$imoje = new Imoje();

		$this->context->smarty->assign([
			'form'                    => $paywall->buildOrderForm(
				$imoje->getDataForRequestToPaywall($cart,
					Util::getPaymentMethod('card'))
			),
			'checkout_link'           => $this->context->link->getPageLink(Configuration::get('PS_ORDER_PROCESS_TYPE')
				? 'order-opc'
				: 'order'),
			'text_return_to_checkout' => $this->module->l('Please wait, you will be returned to checkout.', 'payment'),
			'ga_key'                  => Configuration::get('IMOJE_GA_KEY'),
		]);

		$this->setTemplate(Imoje::buildTemplatePath('pay', 'front'));
	}
}
