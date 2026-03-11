<?php

use Imoje\Payment\Api;
use Imoje\Payment\CartData;
use Imoje\Payment\Installments;
use Imoje\Payment\Invoice;
use Imoje\Payment\LeaseNow;
use Imoje\Payment\Paywall;
use Imoje\Payment\Util;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;

include_once(_PS_MODULE_DIR_ . 'imoje/libraries/Helper.php');

if(!defined('_PS_VERSION_')) {
	exit;
}

/**
 * Class Imoje
 *
 * @property string name
 * @property string tab
 * @property string version
 * @property string author
 * @property int    need_instance
 * @property bool   currencies
 * @property string currencies_mode
 * @property array  ps_versions_compliancy
 * @property int    is_eu_compatible
 * @property string displayName
 * @property string description
 * @property string confirm_uninstall
 */
class Imoje extends PaymentModule
{

	/**
	 * Sets the Information for the Module manager
	 * Also creates an instance of this class
	 */
	public function __construct()
	{
		$this->name = 'imoje';
		$this->displayName = 'imoje';
		$this->tab = 'payments_gateways';
		$this->version = '3.12.1';
		$this->author = 'imoje';
		$this->need_instance = 1;
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';
		$this->ps_versions_compliancy = [
			'min' => '1.7',
		];
		$this->is_eu_compatible = 1;

		parent::__construct();

		$this->displayName = $this->l('imoje');
		$this->description = $this->l('Visa, MasterCard, BLIK ect.');
		$this->confirm_uninstall = $this->l('Are you sure you want to uninstall imoje module?');
	}

	/**
	 * @param string $name
	 * @param string $type
	 *
	 * @return string
	 */
	public static function buildTemplatePath($name, $type)
	{
		return 'module:imoje/views/templates/' . $type . '/' . $name . '.tpl';
	}

	/**
	 * This function installs the imoje Module
	 *
	 * @return boolean
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function install()
	{

		if(!extension_loaded('curl')) {
			$this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');

			return false;
		}

		require_once(dirname(__FILE__) . '/sql/ImojeSql.php');

		if(!ImojeSql::install()) {
			$this->_errors[] = $this->l('Something went wrong with creating table imoje_transaction_list.');

			return false;
		}

		if(!(
			parent::install()
			&& $this->registerHook('paymentOptions')
			&& $this->registerHook('paymentReturn')
			&& $this->registerHook('displayBackOfficeHeader')
			&& $this->registerHook('displayHeader')
			&& $this->registerHook('displayReassurance')
			&& $this->registerHook('displayProductAdditionalInfo')

			&& Configuration::updateValue('IMOJE_SANDBOX', 0)
			&& Configuration::updateValue('IMOJE_CREATE_ORDER_ARRANGEMENT', 0)
			&& Configuration::updateValue('IMOJE_ING_KSIEGOWOSC', 0)
			&& Configuration::updateValue('IMOJE_ING_LEASE_NOW', 0)
			&& Configuration::updateValue('IMOJE_SERVICE_ID', '')
			&& Configuration::updateValue('IMOJE_SERVICE_KEY', '')
			&& Configuration::updateValue('IMOJE_MERCHANT_ID', '')
			&& Configuration::updateValue('IMOJE_CANCEL_ORDER', 0)

			&& Configuration::updateValue('IMOJE_GA_KEY', '')

			&& Configuration::updateValue('IMOJE_IMOJE_BUTTON', 1)
			&& Configuration::updateValue('IMOJE_PAYMENT_TITLE', '')
			&& Configuration::updateValue('IMOJE_CURRENCIES', [])
			&& Configuration::updateValue('IMOJE_HIDE_BRAND', 0)

			&& Configuration::updateValue('IMOJE_ASSIGN_STOCK_NEGATIVE_STATUS', 0)

			&& Configuration::updateValue('IMOJE_BLIK_BUTTON', 0)
			&& Configuration::updateValue('IMOJE_BLIK_PAYMENT_TITLE', '')
			&& Configuration::updateValue('IMOJE_BLIK_HIDE_BRAND', 0)
			&& Configuration::updateValue('IMOJE_BLIK_CODE_CHECKOUT', 0)
			&& Configuration::updateValue('IMOJE_BLIK_CURRENCIES', [])
			//            && Configuration::updateValue('IMOJE_BLIK_ONECLICK', 0)

			&& Configuration::updateValue('IMOJE_PBL_BUTTON', 0)
			&& Configuration::updateValue('IMOJE_PBL_PAYMENT_TITLE', '')
			&& Configuration::updateValue('IMOJE_PBL_HIDE_BRAND', 0)
			&& Configuration::updateValue('IMOJE_PBL_CURRENCIES', [])

			&& Configuration::updateValue('IMOJE_CARDS_BUTTON', 0)
			&& Configuration::updateValue('IMOJE_CARDS_PAYMENT_TITLE', '')
			&& Configuration::updateValue('IMOJE_CARDS_HIDE_BRAND', 0)
			&& Configuration::updateValue('IMOJE_CARDS_CURRENCIES', [])

			&& Configuration::updateValue('IMOJE_PAYLATER_BUTTON', 0)
			&& Configuration::updateValue('IMOJE_PAYLATER_PAYMENT_TITLE', '')
			&& Configuration::updateValue('IMOJE_PAYLATER_HIDE_BRAND', 0)
			&& Configuration::updateValue('IMOJE_PAYLATER_CURRENCIES', [])

			&& Configuration::updateValue('IMOJE_INSTALLMENTS_BUTTON', 0)
			&& Configuration::updateValue('IMOJE_INSTALLMENTS_PAYMENT_TITLE', '')
			&& Configuration::updateValue('IMOJE_INSTALLMENTS_HIDE_BRAND', 0)
			&& Configuration::updateValue('IMOJE_INSTALLMENTS_CURRENCIES', [])

			&& Configuration::updateValue('IMOJE_WALLET_BUTTON', 0)
			&& Configuration::updateValue('IMOJE_WALLET_PAYMENT_TITLE', '')
			&& Configuration::updateValue('IMOJE_WALLET_HIDE_BRAND', 0)
			&& Configuration::updateValue('IMOJE_WALLET_CURRENCIES', [])

			&& Configuration::updateValue('IMOJE_WT_BUTTON', 0)
			&& Configuration::updateValue('IMOJE_WT_PAYMENT_TITLE', '')
			&& Configuration::updateValue('IMOJE_WT_HIDE_BRAND', 0)
			&& Configuration::updateValue('IMOJE_WT_CURRENCIES', [])

			&& Configuration::updateValue('IMOJE_LEASENOW_BUTTON', 0)
			&& Configuration::updateValue('IMOJE_LEASENOW_PAYMENT_TITLE', '')
			&& Configuration::updateValue('IMOJE_LEASENOW_HIDE_BRAND', 0)
			&& Configuration::updateValue('IMOJE_LEASENOW_CURRENCIES', [])
			&& Configuration::updateValue('IMOJE_LEASENOW_BUTTON_WIDTH', 50)
			&& Configuration::updateValue('IMOJE_LEASENOW_PRODUCT_DISPLAY_CARD', 0)
			&& Configuration::updateValue('IMOJE_LEASENOW_PRODUCT_MIN_AMOUNT', 0)

			&& Configuration::updateValue('IMOJE_TOKEN', '')
		)) {

			return false;
		}

		if(Validate::isInt(Configuration::get('PAYMENT_IMOJE_NEW_STATUS'))
			xor (Validate::isLoadedObject(new OrderState(Configuration::get('PAYMENT_IMOJE_NEW_STATUS'))))) {

			$orderStateNew = new OrderState();
			$missingLang = true;

			$langs = [
				'en' => 'Payment imoje: awaiting for confirmation',
				'pl' => 'Płatność imoje: oczekuje na potwierdzenie',
			];

			foreach($langs as $lang => $message) {
				$langId = Language::getIdByIso($lang);
				if(isset($langId)) {
					$orderStateNew->name[$langId] = $message;
					$missingLang = false;
				}
			}

			if($missingLang) {
				$langId = $this->context->language->id;
				$orderStateNew->name[$langId] = $langs['en'];
			}

			$orderStateNew->send_email = false;
			$orderStateNew->invoice = false;
			$orderStateNew->unremovable = false;
			$orderStateNew->color = "lightblue";

			if(!$orderStateNew->add()) {
				$this->_errors[] = $this->l('There was an Error installing the module. Cannot add new order state.');

				return false;
			}
			if(!Configuration::updateGlobalValue('PAYMENT_IMOJE_NEW_STATUS', $orderStateNew->id)) {
				$this->_errors[] = $this->l('There was an Error installing the module. Cannot update new order state.');

				return false;
			}

			copy(
				sprintf('%s%s/imoje.gif', _PS_MODULE_DIR_, $this->name),
				sprintf('%sos/%s.gif', _PS_IMG_DIR_, $orderStateNew->id)
			);
		}

		return true;
	}

	/**
	 * This function uninstalls the imoje Module
	 *
	 * @return boolean
	 */
	public function uninstall()
	{
		return Configuration::deleteByName('IMOJE_SANDBOX')

			&& Configuration::deleteByName('IMOJE_CREATE_ORDER_ARRANGEMENT')
			&& Configuration::deleteByName('IMOJE_ING_KSIEGOWOSC')
			&& Configuration::deleteByName('IMOJE_ING_LEASE_NOW')
			&& Configuration::deleteByName('IMOJE_SERVICE_ID')
			&& Configuration::deleteByName('IMOJE_SERVICE_KEY')
			&& Configuration::deleteByName('IMOJE_MERCHANT_ID')
			&& Configuration::deleteByName('IMOJE_CANCEL_ORDER')

			&& Configuration::deleteByName('IMOJE_GA_KEY')

			&& Configuration::deleteByName('IMOJE_IMOJE_BUTTON')
			&& Configuration::deleteByName('IMOJE_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_CURRENCIES')

			&& Configuration::deleteByName('IMOJE_ASSIGN_STOCK_NEGATIVE_STATUS')

			&& Configuration::deleteByName('IMOJE_PAYLATER_BUTTON')
			&& Configuration::deleteByName('IMOJE_PAYLATER_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_PAYLATER_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_PAYLATER_CURRENCIES')

			&& Configuration::deleteByName('IMOJE_INSTALLMENTS_BUTTON')
			&& Configuration::deleteByName('IMOJE_INSTALLMENTS_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_INSTALLMENTS_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_INSTALLMENTS_CURRENCIES')

			&& Configuration::deleteByName('IMOJE_BLIK_BUTTON')
			&& Configuration::deleteByName('IMOJE_BLIK_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_BLIK_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_BLIK_CODE_CHECKOUT')
			&& Configuration::deleteByName('IMOJE_BLIK_CURRENCIES')
			//            && Configuration::deleteByName('IMOJE_BLIK_ONECLICK')

			&& Configuration::deleteByName('IMOJE_PBL_BUTTON')
			&& Configuration::deleteByName('IMOJE_PBL_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_PBL_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_PBL_CURRENCIES')

			&& Configuration::deleteByName('IMOJE_CARDS_BUTTON')
			&& Configuration::deleteByName('IMOJE_CARDS_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_CARDS_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_CARDS_CURRENCIES')

			&& Configuration::deleteByName('IMOJE_VISA_BUTTON')
			&& Configuration::deleteByName('IMOJE_VISA_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_VISA_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_VISA_CURRENCIES')

			&& Configuration::deleteByName('IMOJE_WALLET_BUTTON')
			&& Configuration::deleteByName('IMOJE_WALLET_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_WALLET_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_WALLET_CURRENCIES')

			&& Configuration::deleteByName('IMOJE_WT_BUTTON')
			&& Configuration::deleteByName('IMOJE_WT_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_WT_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_WT_CURRENCIES')

			&& Configuration::deleteByName('IMOJE_LEASENOW_BUTTON')
			&& Configuration::deleteByName('IMOJE_LEASENOW_PAYMENT_TITLE')
			&& Configuration::deleteByName('IMOJE_LEASENOW_HIDE_BRAND')
			&& Configuration::deleteByName('IMOJE_LEASENOW_CURRENCIES')
			&& Configuration::deleteByName('IMOJE_LEASENOW_PRODUCT_MIN_AMOUNT')
			&& Configuration::deleteByName('IMOJE_LEASENOW_BUTTON_WIDTH')
			&& Configuration::deleteByName('IMOJE_LEASENOW_DISPLAY_CARD')

			&& Configuration::deleteByName('IMOJE_TOKEN')

			&& parent::uninstall();
	}

	/**
	 * Display a configuration form.
	 *
	 * @return false|string
	 */
	public function getContent()
	{

		$msg = '';

		if(Tools::isSubmit('submitImoje')) {

			$saveConfiguration = $this->saveConfiguration();
			if($saveConfiguration === true) {
				$msg = [
					'type'    => 'success',
					'message' => $this->l('Configuration updated successfully'),
				];
			} else {
				$msg = [
					'type'    => 'error',
					'message' => $saveConfiguration,
				];
			}
		}

		$this->context->smarty->assign([
			'imoje_form' => sprintf(
				'./index.php?tab=AdminModules&configure=%s&token=%s&tab_module=%s&module_name=%s',
				$this->name,
				Tools::getAdminTokenLite('AdminModules'),
				$this->tab,
				$this->name
			),

			'imoje_enabled' => Configuration::get('IMOJE_ENABLED'),

			'imoje_token' => Configuration::get('IMOJE_TOKEN'),

			'imoje_assign_stock_negative_status' => Configuration::get('IMOJE_ASSIGN_STOCK_NEGATIVE_STATUS'),

			'imoje_sandbox' => Configuration::get('IMOJE_SANDBOX'),

			'imoje_create_order_arrangement' => Configuration::get('IMOJE_CREATE_ORDER_ARRANGEMENT'),

			'imoje_ing_ksiegowosc' => Configuration::get('IMOJE_ING_KSIEGOWOSC'),

			'imoje_ing_lease_now' => Configuration::get('IMOJE_ING_LEASE_NOW'),

			'imoje_service_id'  => Configuration::get('IMOJE_SERVICE_ID'),
			'imoje_service_key' => Configuration::get('IMOJE_SERVICE_KEY'),

			'imoje_merchant_id' => Configuration::get('IMOJE_MERCHANT_ID'),

			'imoje_cancel_order' => Configuration::get('IMOJE_CANCEL_ORDER', 0),

			'imoje_msg' => $msg,

			'imoje_ga_key' => Configuration::get('IMOJE_GA_KEY'),

			'imoje_available_currencies' => Util::getSupportedCurrencies(),

			'imoje_imoje_button'          => Configuration::get('IMOJE_IMOJE_BUTTON'),
			'imoje_payment_title'         => Configuration::get('IMOJE_PAYMENT_TITLE'),
			'imoje_payment_title_default' => $this->getPaymentTitleDefault(),
			'imoje_hide_brand'            => Configuration::get('IMOJE_HIDE_BRAND'),
			'imoje_currencies'            => explode(",", Configuration::get('IMOJE_CURRENCIES')),

			'imoje_is_token' => Configuration::get('IMOJE_TOKEN'),

			'imoje_blik_button'                => Configuration::get('IMOJE_BLIK_BUTTON'),
			'imoje_blik_hide_brand'            => Configuration::get('IMOJE_BLIK_HIDE_BRAND'),
			'imoje_blik_code_checkout'         => Configuration::get('IMOJE_BLIK_CODE_CHECKOUT'),
			'imoje_blik_payment_title'         => Configuration::get('IMOJE_BLIK_PAYMENT_TITLE'),
			//            'imoje_blik_oneclick' => Configuration::get('IMOJE_BLIK_ONECLICK'),
			'imoje_blik_oneclick'              => false,
			'imoje_blik_payment_title_default' => $this->getBlikPaymentTitleDefault(),
			'imoje_blik_currencies'            => explode(",", Configuration::get('IMOJE_BLIK_CURRENCIES')),

			'imoje_cards_button'                => Configuration::get('IMOJE_CARDS_BUTTON'),
			'imoje_cards_payment_title'         => Configuration::get('IMOJE_CARDS_PAYMENT_TITLE'),
			'imoje_cards_payment_title_default' => $this->getCardsPaymentTitleDefault(),
			'imoje_cards_hide_brand'            => Configuration::get('IMOJE_CARDS_HIDE_BRAND'),
			'imoje_cards_currencies'            => explode(",", Configuration::get('IMOJE_CARDS_CURRENCIES')),

			'imoje_visa_button'                => Configuration::get('IMOJE_VISA_BUTTON'),
			'imoje_visa_payment_title'         => Configuration::get('IMOJE_VISA_PAYMENT_TITLE'),
			'imoje_visa_payment_title_default' => $this->getVisaPaymentTitleDefault(),
			'imoje_visa_hide_brand'            => Configuration::get('IMOJE_VISA_HIDE_BRAND'),
			'imoje_visa_currencies'            => explode(",", Configuration::get('IMOJE_VISA_CURRENCIES')),

			'imoje_paylater_button'                => Configuration::get('IMOJE_PAYLATER_BUTTON'),
			'imoje_paylater_payment_title'         => Configuration::get('IMOJE_PAYLATER_PAYMENT_TITLE'),
			'imoje_paylater_payment_title_default' => $this->getPaylaterPaymentTitleDefault(),
			'imoje_paylater_hide_brand'            => Configuration::get('IMOJE_PAYLATER_HIDE_BRAND'),
			'imoje_paylater_currencies'            => explode(",", Configuration::get('IMOJE_PAYLATER_CURRENCIES')),

			'imoje_installments_button'                => Configuration::get('IMOJE_INSTALLMENTS_BUTTON'),
			'imoje_installments_payment_title'         => Configuration::get('IMOJE_INSTALLMENTS_PAYMENT_TITLE'),
			'imoje_installments_payment_title_default' => $this->getInstallmentsPaymentTitleDefault(),
			'imoje_installments_hide_brand'            => Configuration::get('IMOJE_INSTALLMENTS_HIDE_BRAND'),
			'imoje_installments_currencies'            => explode(",", Configuration::get('IMOJE_INSTALLMENTS_CURRENCIES')),

			'imoje_pbl_button'                => Configuration::get('IMOJE_PBL_BUTTON'),
			'imoje_pbl_payment_title'         => Configuration::get('IMOJE_PBL_PAYMENT_TITLE'),
			'imoje_pbl_payment_title_default' => $this->getPblPaymentTitleDefault(),
			'imoje_pbl_hide_brand'            => Configuration::get('IMOJE_PBL_HIDE_BRAND'),
			'imoje_pbl_currencies'            => explode(",", Configuration::get('IMOJE_PBL_CURRENCIES')),

			'imoje_wallet_button'                => Configuration::get('IMOJE_WALLET_BUTTON'),
			'imoje_wallet_payment_title'         => Configuration::get('IMOJE_WALLET_PAYMENT_TITLE'),
			'imoje_wallet_payment_title_default' => $this->getWalletPaymentTitleDefault(),
			'imoje_wallet_hide_brand'            => Configuration::get('IMOJE_WALLET_HIDE_BRAND'),
			'imoje_wallet_currencies'            => explode(",", Configuration::get('IMOJE_WALLET_CURRENCIES')),

			'imoje_wt_button'                => Configuration::get('IMOJE_WT_BUTTON'),
			'imoje_wt_payment_title'         => Configuration::get('IMOJE_WT_PAYMENT_TITLE'),
			'imoje_wt_payment_title_default' => $this->getWtPaymentTitleDefault(),
			'imoje_wt_hide_brand'            => Configuration::get('IMOJE_WT_HIDE_BRAND'),
			'imoje_wt_currencies'            => explode(",", Configuration::get('IMOJE_WT_CURRENCIES')),

			'imoje_leasenow_button'                => Configuration::get('IMOJE_LEASENOW_BUTTON'),
			'imoje_leasenow_payment_title'         => Configuration::get('IMOJE_LEASENOW_PAYMENT_TITLE'),
			'imoje_leasenow_payment_title_default' => $this->getLeasenowPaymentTitleDefault(),
			'imoje_leasenow_hide_brand'            => Configuration::get('IMOJE_LEASENOW_HIDE_BRAND'),
			'imoje_leasenow_currencies'            => explode(",", Configuration::get('IMOJE_LEASENOW_CURRENCIES')),
			'imoje_leasenow_display_card'          => Configuration::get('IMOJE_LEASENOW_DISPLAY_CARD'),
			'imoje_leasenow_button_width'          => Configuration::get('IMOJE_LEASENOW_BUTTON_WIDTH'),
		]);

		return $this->fetchTemplate('admin.tpl');
	}

	/**
	 * @return bool
	 */
	private function saveConfiguration()
	{

		$merchantId = pSQL(Tools::getValue('imoje_merchant_id'));
		$serviceId = pSQL(Tools::getValue('imoje_service_id'));
		$authorizationToken = pSQL(Tools::getValue('imoje_token'));

		if(!$merchantId || !$serviceId || !$authorizationToken) {

			return $this->l('Missing authorization data');
		}

		$api = new Api($authorizationToken, $merchantId, $serviceId, pSQL(Tools::getValue('imoje_sandbox')) === "0"
			? Util::ENVIRONMENT_PRODUCTION
			: Util::ENVIRONMENT_SANDBOX);

		$serviceInfo = $api->getServiceInfo();

		if(!$serviceInfo['success']) {

			PrestaShopLogger::addLog("Invalid imoje service data: " . json_encode($serviceInfo));

			return $this->l('Invalid authorization data');
		}

		$serviceInfo = $serviceInfo['body'];

		// save for leasenow amount to compare product price
		foreach($serviceInfo['service']['paymentMethods'] as $paymentMethod) {
			if($paymentMethod['paymentMethodCode'] === Util::getPaymentMethodCode('lease_now') && $paymentMethod['currency'] === 'PLN') {

				Configuration::updateValue('IMOJE_LEASENOW_PRODUCT_MIN_AMOUNT', $paymentMethod['transactionLimits']['minTransaction']['value']);
			}
		}

		$configValues = [
			'IMOJE_SANDBOX'                      => 'imoje_sandbox',
			'IMOJE_ASSIGN_STOCK_NEGATIVE_STATUS' => 'imoje_assign_stock_negative_status',
			'IMOJE_CREATE_ORDER_ARRANGEMENT'     => 'imoje_create_order_arrangement',
			'IMOJE_ING_KSIEGOWOSC'               => 'imoje_ing_ksiegowosc',
			'IMOJE_ING_LEASE_NOW'                => 'imoje_ing_lease_now',
			'IMOJE_SERVICE_ID'                   => 'imoje_service_id',
			'IMOJE_SERVICE_KEY'                  => 'imoje_service_key',
			'IMOJE_MERCHANT_ID'                  => 'imoje_merchant_id',
			'IMOJE_TOKEN'                        => 'imoje_token',
			'IMOJE_CANCEL_ORDER'                 => 'imoje_cancel_order',
			'IMOJE_GA_KEY'                       => 'imoje_ga_key',
			'IMOJE_IMOJE_BUTTON'                 => 'imoje_imoje_button',
			'IMOJE_HIDE_BRAND'                   => 'imoje_hide_brand',
			'IMOJE_PAYMENT_TITLE'                => 'imoje_payment_title',
			'IMOJE_PAYLATER_BUTTON'              => 'imoje_paylater_button',
			'IMOJE_PAYLATER_HIDE_BRAND'          => 'imoje_paylater_hide_brand',
			'IMOJE_PAYLATER_PAYMENT_TITLE'       => 'imoje_paylater_payment_title',
			'IMOJE_INSTALLMENTS_BUTTON'          => 'imoje_installments_button',
			'IMOJE_INSTALLMENTS_HIDE_BRAND'      => 'imoje_installments_hide_brand',
			'IMOJE_INSTALLMENTS_PAYMENT_TITLE'   => 'imoje_installments_payment_title',
			'IMOJE_BLIK_BUTTON'                  => 'imoje_blik_button',
			'IMOJE_BLIK_HIDE_BRAND'              => 'imoje_blik_hide_brand',
			'IMOJE_BLIK_CODE_CHECKOUT'           => 'imoje_blik_code_checkout',
			'IMOJE_BLIK_PAYMENT_TITLE'           => 'imoje_blik_payment_title',
			//'IMOJE_BLIK_ONECLICK' => 'imoje_blik_oneclick',
			'IMOJE_PBL_BUTTON'                   => 'imoje_pbl_button',
			'IMOJE_PBL_HIDE_BRAND'               => 'imoje_pbl_hide_brand',
			'IMOJE_PBL_PAYMENT_TITLE'            => 'imoje_pbl_payment_title',
			'IMOJE_CARDS_BUTTON'                 => 'imoje_cards_button',
			'IMOJE_CARDS_HIDE_BRAND'             => 'imoje_cards_hide_brand',
			'IMOJE_CARDS_PAYMENT_TITLE'          => 'imoje_cards_payment_title',
			'IMOJE_VISA_BUTTON'                  => 'imoje_visa_button',
			'IMOJE_VISA_HIDE_BRAND'              => 'imoje_visa_hide_brand',
			'IMOJE_VISA_PAYMENT_TITLE'           => 'imoje_visa_payment_title',
			'IMOJE_WALLET_BUTTON'                => 'imoje_wallet_button',
			'IMOJE_WALLET_HIDE_BRAND'            => 'imoje_wallet_hide_brand',
			'IMOJE_WALLET_PAYMENT_TITLE'         => 'imoje_wallet_payment_title',
			'IMOJE_WT_BUTTON'                    => 'imoje_wt_button',
			'IMOJE_WT_HIDE_BRAND'                => 'imoje_wt_hide_brand',
			'IMOJE_WT_PAYMENT_TITLE'             => 'imoje_wt_payment_title',
			'IMOJE_LEASENOW_BUTTON'              => 'imoje_leasenow_button',
			'IMOJE_LEASENOW_HIDE_BRAND'          => 'imoje_leasenow_hide_brand',
			'IMOJE_LEASENOW_PAYMENT_TITLE'       => 'imoje_leasenow_payment_title',
			'IMOJE_LEASENOW_DISPLAY_CARD'        => 'imoje_leasenow_display_card',
			'IMOJE_LEASENOW_BUTTON_WIDTH'        => 'imoje_leasenow_button_width',
		];

		foreach($configValues as $configKey => $postKey) {

			$postKey = pSQL(Tools::getValue($postKey));

			if(!strpos($configKey, "_PAYMENT_TITLE")) {
				$postKey = preg_replace('/\s+/', '', $postKey);
			}

			if(strpos($configKey, "_BUTTON_WIDTH")) {
				$postKey = max(0, min(100, (int) $postKey));
			}

			Configuration::updateValue($configKey, $postKey);
		}

		$additionalCurrencies = [
			'IMOJE_BLIK_CURRENCIES'         => 'imoje_blik_currencies',
			'IMOJE_CURRENCIES'              => 'imoje_currencies',
			'IMOJE_PAYLATER_CURRENCIES'     => 'imoje_paylater_currencies',
			'IMOJE_INSTALLMENTS_CURRENCIES' => 'imoje_installments_currencies',
			'IMOJE_CARDS_CURRENCIES'        => 'imoje_cards_currencies',
			'IMOJE_PBL_CURRENCIES'          => 'imoje_pbl_currencies',
			'IMOJE_VISA_CURRENCIES'         => 'imoje_visa_currencies',
			'IMOJE_WALLET_CURRENCIES'       => 'imoje_wallet_currencies',
			'IMOJE_WT_CURRENCIES'           => 'imoje_wt_currencies',
			'IMOJE_LEASENOW_CURRENCIES'     => 'imoje_leasenow_currencies',
		];

		foreach($additionalCurrencies as $configKey => $postKey) {
			if(is_array($currencies = Tools::getValue($postKey))) {
				Configuration::updateValue($configKey, pSQL(implode(',', $currencies)));
			}
		}

		return true;
	}

	/**
	 * @return string
	 */
	private function getPaymentTitleDefault()
	{

		return $this->l('Pay for purchases using the most convenient online payment methods - PayByLink, cards, BLIK.');
	}

	/**
	 * @return string
	 */
	private function getPaylaterPaymentTitleDefault()
	{

		return $this->l('imoje pay later');
	}

	/**
	 * @return string
	 */
	private function getInstallmentsPaymentTitleDefault()
	{

		return $this->l('imoje installments');
	}

	/**
	 * @return string
	 */
	private function getBlikPaymentTitleDefault()
	{

		return $this->l('BLIK');
	}

	/**
	 * @return string
	 */
	private function getCardsPaymentTitleDefault()
	{

		return $this->l('Payment cards');
	}

	/**
	 * @return string
	 */
	private function getPblPaymentTitleDefault()
	{

		return $this->l('Pay-By-Link with imoje');
	}

	/**
	 * @return string
	 */
	private function getVisaPaymentTitleDefault()
	{

		return $this->l('Visa Mobile payment with imoje');
	}

	/**
	 * @return string
	 */
	private function getWalletPaymentTitleDefault()
	{
		return $this->l('Electronic wallet');
	}

	/**
	 * @return string
	 */
	private function getWtPaymentTitleDefault()
	{

		return $this->l('Wire transfer via imoje');
	}

	/**
	 * @return string
	 */
	private function getLeasenowPaymentTitleDefault()
	{

		return $this->l('Pay with LeaseNow');
	}

	/**
	 * @param string $name
	 *
	 * @return false|string
	 */
	public function fetchTemplate($name)
	{

		return $this->display(__FILE__, $name);
	}

	/**
	 * Hook that run on mobile version of Prestashop.
	 */
	public function hookDisplayMobileHeader()
	{
		$this->hookHeader();
	}

	/**
	 * @param ProductLazyArray $product
	 *
	 * @return void
	 */
	public function hookDisplayProductAdditionalInfo($product)
	{
		if(
			Configuration::get('IMOJE_LEASENOW_DISPLAY_CARD')
			&& Util::convertAmountToFractional($product['product']->price_amount) >= (int) Configuration::get('IMOJE_LEASENOW_PRODUCT_MIN_AMOUNT')
		) {

			$this->context->smarty->assign('imoje_leasenow_button_url', self::getMediaPath(sprintf('%s%s/assets/img/leasenow_button.png', _PS_MODULE_DIR_, $this->name)));

			$buttonWidth = (int) Configuration::get('IMOJE_LEASENOW_BUTTON_WIDTH');

			if ($buttonWidth < 1 || $buttonWidth > 100) {
				$buttonWidth = 50;
			}
			$this->context->smarty->assign('imoje_leasenow_button_width', $buttonWidth);

			echo $this->fetchTemplate('leasenow-block.tpl');
		}
	}

	/**
	 * Hook that adds CSS for old versions of Prestashop.
	 */
	public function hookHeader()
	{
		$this->context->controller->addCSS($this->_path . 'assets/css/imoje-front.min.css');
	}

	/**
	 * Hook that adds CSS and js in an admin panel for a newer version of PrestaShop.
	 */
	public function hookBackOfficeHeader()
	{

		if(version_compare(_PS_VERSION_, '9.0.0', '<')) {
			$this->context->controller->addJquery();
		}

		$this->context->controller->addCSS($this->_path . 'assets/css/imoje-admin.min.css');
		$this->context->controller->addCSS($this->_path . 'assets/css/font-awesome.min.css');

		if(!Configuration::get('IMOJE_TOKEN')
			|| !Configuration::get('IMOJE_MERCHANT_ID')
			|| !Configuration::get('IMOJE_SERVICE_ID')) {
			return '';
		}

		$orderId = Tools::getValue('id_order');

		if($orderId === false) {
			return '';
		}

		try {
			$order = new Order($orderId);
		} catch(PrestaShopDatabaseException $e) {
			PrestaShopLogger::addLog($e->getMessage(), 3);

			return '';
		} catch(PrestaShopException $e) {
			PrestaShopLogger::addLog($e->getMessage(), 3);

			return '';
		}
		$orderCurrentState = (string) $order->getCurrentState();

		if($order->module !== 'imoje'
			|| ($orderCurrentState === Configuration::get('PAYMENT_IMOJE_NEW_STATUS'))
		) {
			return '';
		}

		require_once(dirname(__FILE__) . '/sql/ImojeSql.php');

		$idTransaction = ImojeSql::getTransactionId($orderId);

		if(!$idTransaction) {
			return '';
		}

		$api = new Api(
			Configuration::get('IMOJE_TOKEN'),
			Configuration::get('IMOJE_MERCHANT_ID'),
			Configuration::get('IMOJE_SERVICE_ID'),
			Configuration::get('IMOJE_SANDBOX')
				? Util::ENVIRONMENT_SANDBOX
				: Util::ENVIRONMENT_PRODUCTION
		);

		$apiTransaction = $api->getTransaction($idTransaction);

		if(!$apiTransaction['success']) {
			return '';
		}

		$amountToRefund = Util::calculateAmountToRefund($apiTransaction);

		$msg = '';
		$refundError = '';

		$imojeSubmitRefund = Tools::getValue('imoje_submit_refund');
		$imojeRefundAmount = Tools::getValue('imoje_refund_amount');

		$this->context->smarty->assign('imoje_amount_refundable', Util::convertAmountToMain($amountToRefund));
		$this->context->smarty->assign('new_layout_refund', version_compare(_PS_VERSION_, '1.7.7.0', '>='));

		if(!$imojeSubmitRefund || !$imojeRefundAmount) {

			$this->context->smarty->assign('imoje_can_refund', $amountToRefund);
			$this->context->smarty->assign('imoje_refund_message', $msg);
			$this->context->smarty->assign('imoje_refund_error', $refundError);

			return $this->fetchTemplate('/views/templates/admin/refunds.tpl');
		}

		$imojeChangeStatus = Tools::getValue('imoje_change_status');

		$currencyInfo = Currency::getCurrency($order->id_currency);

		$imojeRefundAmountFractional = Util::convertAmountToFractional($imojeRefundAmount);

		$refund = $api->createRefund(
			$api->prepareRefundData(
				$imojeRefundAmountFractional
			),
			$idTransaction
		);

		if(!$refund['success']) {

			if(isset($refund['data']['body']) && $refund['data']['body']) {
				$refundError = $this->l('Refund error:') . ' ' . $refund['data']['body'];
			} else {
				$refundError = $this->l('Refund error.');
			}

			PrestaShopLogger::addLog(
				sprintf('%s %s %s', $refundError, $this->l('order ID:'), $orderId)
			);

			http_response_code(400);

			echo $refundError;
			die();
		}

		include_once(_PS_MODULE_DIR_ . 'imoje/libraries/payment-core/vendor/autoload.php');

		switch($refund['body']['transaction']['status']) {
			case \Imoje\Payment\Notification::TRS_SETTLED:
				if($imojeChangeStatus || $imojeRefundAmountFractional === Util::calculateAmountToRefund($apiTransaction)) {
					$history = new OrderHistory();
					$history->id_order = $orderId;
					try {
						$history->changeIdOrderState(Configuration::get('PS_OS_REFUND'), $orderId);
					} catch(PrestaShopException $e) {
						PrestaShopLogger::addLog($e->getMessage(), 3);
						echo $this->l('Something went wrong, check PrestaShop log');
						die();
					}
					$history->addWithemail(true, []);
				}

				$msg = sprintf($this->l('Refund for amount %1$d %2$s has been created. Order ID: %3$s, refund ID: %4$s'),
					round($imojeRefundAmount, 2),
					$currencyInfo['iso_code'],
					$orderId,
					$refund['body']['transaction']['id']
				);
				break;

			case \Imoje\Payment\Notification::TRS_NEW:
				$msg = sprintf($this->l('Refund for amount %1$d %2$s has been created and awaiting for confirmation. Order ID: %3$s, refund ID: %4$s'),
					round($imojeRefundAmount, 2),
					$currencyInfo['iso_code'],
					$orderId,
					$refund['body']['transaction']['id']
				);
				break;
			default:

				$msg = sprintf($this->l('Refund for amount %1$d %2$s has been not created. Order ID: %3$s. Check response in PrestaShop logs.'),
					round($imojeRefundAmount, 2),
					$currencyInfo['iso_code'],
					$orderId
				);

				PrestaShopLogger::addLog(json_decode($refund['body']['transaction']['message'], true));
				break;
		}

		PrestaShopLogger::addLog($msg);

		http_response_code(200);

		echo $msg;
		die();
	}

	/**
	 * Hook that adds CSS and js in the admin panel.
	 */
	public function hookBackOfficeFooter()
	{
		echo '<link type="text/css" rel="stylesheet" href="' . $this->_path . 'assets/css/imoje-admin.min.css">'
			. '<link type="text/css" rel="stylesheet" href="' . $this->_path . 'assets/font-awesome.min.css">'
			. '<script src="' . $this->_path . 'assets/js/imoje-legacy-admin.js"></script>';
	}

	/**
	 * @param string $isoCode
	 * @param string $currencies
	 *
	 * @return bool
	 */
	public function checkIsSetCurrency($isoCode, $currencies)
	{
		return in_array(
			$isoCode,
			explode(
				',',
				$currencies
			)
		);
	}

	/**
	 * @param array $params
	 *
	 * @return array|bool
	 * @throws SmartyException
	 * @throws Exception
	 */
	public function hookPaymentOptions($params)
	{

		if(!$this->active) {
			return false;
		}

		$merchantId = Configuration::get('IMOJE_MERCHANT_ID');
		$serviceId = Configuration::get('IMOJE_SERVICE_ID');
		$serviceKey = Configuration::get('IMOJE_SERVICE_KEY');
		$imojeToken = Configuration::get('IMOJE_TOKEN');

		if(
			!$merchantId
			|| !$serviceId
			|| !$serviceKey
			|| !$imojeToken
		) {
			return false;
		}

		$environment = Configuration::get('IMOJE_SANDBOX') == 1
			? Util::ENVIRONMENT_SANDBOX
			: Util::ENVIRONMENT_PRODUCTION;

		$paymentOptions = [];

		$currency = Currency::getCurrency($params['cart']->id_currency);

		if(!Util::canUseForCurrency($currency['iso_code'])) {
			return $paymentOptions;
		}

		$imojeApi = new Api(
			$imojeToken,
			$merchantId,
			$serviceId,
			Configuration::get('IMOJE_SANDBOX')
				? Util::ENVIRONMENT_SANDBOX
				: Util::ENVIRONMENT_PRODUCTION
		);

		$imojeServiceInfo = $imojeApi->getServiceInfo();

		if(!$imojeServiceInfo['success']) {
			return $paymentOptions;
		}

		if(empty($imojeServiceInfo['body']['service']['isActive']) || empty($imojeServiceInfo['body']['service']['paymentMethods'])) {
			return $paymentOptions;
		}

		$imojeService = $imojeServiceInfo['body']['service'];

		$cart = $this->context->cart;
		$currencyInfo = Currency::getCurrency($cart->id_currency);
		$currencyIsoCode = $currencyInfo['iso_code'];

		$cartTotal = $cart->getOrderTotal();

		$paymentMethodAvailable = [];

		foreach($imojeService['paymentMethods'] as $pm) {

			if(empty($pm['paymentMethod']) || empty($pm['paymentMethodCode'])) {
				continue;
			}

			$logo = Util::getPaymentMethodCodeLogo($pm['paymentMethodCode']);

			foreach($pm['paymentMethodCodeImage'] as $imgList) {
				if(isset($imgList['png']) && $imgList['png']) {
					$logo = $imgList['png'];
				}
			}

			$paymentMethodAvailable[$pm['paymentMethod'] . ':' . $pm['paymentMethodCode']] = [
				'available' => $pm['isOnline']
					&& $pm['isActive']
					&& (isset($pm['transactionLimits']) && $pm['transactionLimits'])
					&& $imojeApi->verifyTransactionLimits($pm['transactionLimits'], $cartTotal)
					&& (isset($pm['currency']) && $pm['currency'] && strtolower($currencyIsoCode) === strtolower($pm['currency'])),
				'logo'      => $logo,
			];
		}

		$blikName = Util::getPaymentMethod('blik') . ':' . Util::getPaymentMethodCode('blik');

		if(
			Configuration::get('IMOJE_BLIK_BUTTON')
			&& (isset($paymentMethodAvailable[$blikName]['available']) && $paymentMethodAvailable[$blikName]['available'])
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_BLIK_CURRENCIES'))
		) {
			$paymentOptionBlik = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
			$paymentOptionBlik = $paymentOptionBlik->setCallToActionText(
				(Configuration::get('IMOJE_BLIK_PAYMENT_TITLE')
					?: $this->getBlikPaymentTitleDefault()) . ($environment === Util::ENVIRONMENT_SANDBOX
					? ' ' . $this->l('Sandbox is enabled.')
					: ''))
				->setLogo($this->getLogoPath(
					Util::getPaymentMethod('blik'),
					Configuration::get('IMOJE_BLIK_HIDE_BRAND')

				))
				->setModuleName($this->name)
				->setAction($this->context->link->getModuleLink('imoje', 'paymentblik'));

			if(Configuration::get('IMOJE_BLIK_CODE_CHECKOUT')) {

				$createOrderArrangement = Configuration::get('IMOJE_CREATE_ORDER_ARRANGEMENT');
				$activeProfile = [];
				$profileId = null;

				$this->context->smarty->assign([
					'create_order_arrangement' => $createOrderArrangement,
					'payment_blik_url'         => $this->context->link->getModuleLink(
						'imoje',
						'paymentblik'),
					'profile_id'               => $profileId,
					'payment_tip'              => $this->l('Please wait, payment is processing.', 'paymentblik'),
					'accept_tip1'              => $this->l('Now accept your payment in bank application.', 'paymentblik'),
					'accept_tip2'              => $this->l('You will be informed via e-mail about it end status, or you can check your payment status later.', 'paymentblik'),
					'failure_tip1'             => $this->l('Unable to complete your payment request.', 'paymentblik'),
					'failure_tip2'             => $this->l('Try again later or contact with shop staff.', 'paymentblik'),
					'active_profile'           => $activeProfile,
					'is_customer_logged'       => $this->context->customer->isLogged(),
					'is_imoje_blik_oneclick'   => false,
					'cart_id'                  => $params['cart']->id,
				]);
				$paymentOptionBlik->setForm($this->context->smarty->fetch('module:imoje/views/templates/front/pay-blik-code.tpl'));
			} else {
				$paymentOptionBlik->setAdditionalInformation(
					$this->context->smarty->fetch('module:imoje/views/templates/hook/payment-imoje-terms.tpl')
				);
			}

			$paymentOptions[] = $paymentOptionBlik;
		}

		$tipSandbox = $environment === Util::ENVIRONMENT_SANDBOX
			? ' ' . $this->l('Sandbox is enabled.')
			: '';

		if(
			Configuration::get('IMOJE_IMOJE_BUTTON')
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_CURRENCIES'))
		) {
			$paymentOptionImoje = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();

			$paymentOptions[] = $paymentOptionImoje->setCallToActionText(
				(Configuration::get('IMOJE_PAYMENT_TITLE')
					?: $this->getPaymentTitleDefault()) . ($environment === Util::ENVIRONMENT_SANDBOX
					? ' ' . $this->l('Sandbox is enabled.')
					: ''))
				->setModuleName($this->name)
				->setAction($this->context->link->getModuleLink('imoje', 'payment'))
				->setLogo($this->getLogoPath(
					'imoje',
					Configuration::get('IMOJE_HIDE_BRAND')
				));
		}

		if(
			Configuration::get('IMOJE_CARDS_BUTTON')
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_CARDS_CURRENCIES'))
		) {
			$paymentOptionCards = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
			$paymentOptions[] = $paymentOptionCards->setCallToActionText(
				(Configuration::get('IMOJE_CARDS_PAYMENT_TITLE')
					?: $this->getCardsPaymentTitleDefault()) . $tipSandbox)
				->setLogo($this->getLogoPath(
					Util::getPaymentMethod('card'),
					Configuration::get('IMOJE_CARDS_HIDE_BRAND')
				))
				->setModuleName($this->name)
				->setAction($this->context->link->getModuleLink('imoje', 'paymentcards'));
		}

		if(
			Configuration::get('IMOJE_VISA_BUTTON')
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_VISA_CURRENCIES'))
		) {
			$paymentOptionVisa = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
			$paymentOptions[] = $paymentOptionVisa->setCallToActionText(
				(Configuration::get('IMOJE_VISA_PAYMENT_TITLE')
					?: $this->getVisaPaymentTitleDefault()) . $tipSandbox)
				->setLogo($this->getLogoPath(
					'visa',
					Configuration::get('IMOJE_VISA_HIDE_BRAND')
				))
				->setModuleName($this->name)
				->setAction($this->context->link->getModuleLink('imoje', 'paymentvisa'));
		}

		if(
			Configuration::get('IMOJE_WT_BUTTON')
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_WT_CURRENCIES'))
		) {
			$paymentOptionWt = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
			$paymentOptions[] = $paymentOptionWt->setCallToActionText(
				(Configuration::get('IMOJE_WT_PAYMENT_TITLE')
					?: $this->getWtPaymentTitleDefault()) . $tipSandbox)->setLogo($this->getLogoPath(
				Util::getPaymentMethod('wt'),
				Configuration::get('IMOJE_WT_HIDE_BRAND')
			))
				->setModuleName($this->name)
				->setAction($this->context->link->getModuleLink('imoje', 'paymentwt'));
		}

		if(
			Configuration::get('IMOJE_LEASENOW_BUTTON')
			&& Util::convertAmountToFractional($params['cart']->getOrderTotal()) >= Configuration::get('IMOJE_LEASENOW_PRODUCT_MIN_AMOUNT')
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_LEASENOW_CURRENCIES'))
		) {
			$paymentOptionLeasenow = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
			$paymentOptions[] = $paymentOptionLeasenow->setCallToActionText(
				(Configuration::get('IMOJE_LEASENOW_PAYMENT_TITLE')
					?: $this->getLeasenowPaymentTitleDefault()) . $tipSandbox)->setLogo($this->getLogoPath(
				Util::getPaymentMethod('leasenow'),
				Configuration::get('IMOJE_LEASENOW_HIDE_BRAND')
			))
				->setModuleName($this->name)
				->setAction($this->context->link->getModuleLink('imoje', 'paymentleasenow'));
		}

		if(
			Configuration::get('IMOJE_INSTALLMENTS_BUTTON')
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_INSTALLMENTS_CURRENCIES'))
		) {

			$installments = new Installments(
				$merchantId,
				$serviceId,
				$serviceKey,
				$environment);

			$installmentsData = $installments->getData(
				$cart->getOrderTotal(),
				$currencyIsoCode
			);

			$installmentsData['url'] = $installments->getScriptUrl();

			$installmentsController = $this->context->link->getModuleLink('imoje', 'paymentinstallments');

			$this->context->smarty->assign([
				'imoje_installments_data' => $installmentsData,
				'payment_link'            => $installmentsController,
			]);

			$paymentOptionInstallments = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
			$paymentOptions[] = $paymentOptionInstallments->setCallToActionText(
				(Configuration::get('IMOJE_INSTALLMENTS_PAYMENT_TITLE')
					?: $this->getInstallmentsPaymentTitleDefault()) . $tipSandbox)
				->setLogo($this->getLogoPath(
					'installments',
					Configuration::get('IMOJE_INSTALLMENTS_HIDE_BRAND')
				))
				->setModuleName($this->name)
				->setAction($installmentsController)
				->setForm(
					$this->context->smarty->fetch('module:imoje/views/templates/hook/payment-imoje-installments.tpl')
				);
		}

		$pblList = [];
		$paylaterList = [];
		$walletList = [];

		$pblEnabled = Configuration::get('IMOJE_PBL_BUTTON')
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_PBL_CURRENCIES'));
		$paylaterEnabled = Configuration::get('IMOJE_PAYLATER_BUTTON')
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_PAYLATER_CURRENCIES'));
		$walletEnabled = Configuration::get('IMOJE_WALLET_BUTTON')
			&& $this->checkIsSetCurrency($currencyIsoCode, Configuration::get('IMOJE_WALLET_CURRENCIES'));

		if($paylaterEnabled || $pblEnabled || $walletEnabled) {

			$pmIng = Util::getPaymentMethod('ing');
			$pmWallet = Util::getPaymentMethod('wallet');
			$pmcGpay = Util::getPaymentMethodCode('gpay');
			$pmcApplepay = Util::getPaymentMethodCode('applepay');
			$pmPbl = Util::getPaymentMethod('pbl');
			$pmImojePaylater = Util::getPaymentMethod('imoje_paylater');

			foreach($imojeService['paymentMethods'] as $paymentMethod) {

				$pm = strtolower($paymentMethod['paymentMethod']);

				if($paymentMethod['isActive']
					&& $paymentMethod['isOnline']
					&& (isset($paymentMethod['transactionLimits']) && $paymentMethod['transactionLimits'])
					&& $imojeApi->verifyTransactionLimits($paymentMethod['transactionLimits'], $cartTotal)
					&& (isset($paymentMethod['currency']) && $paymentMethod['currency'] && strtolower($currencyIsoCode) === strtolower($paymentMethod['currency']))
				) {

					$logo = Util::getPaymentMethodCodeLogo($paymentMethod['paymentMethodCode']);

					foreach($paymentMethod['paymentMethodCodeImage'] as $imgList) {
						if(isset($imgList['png']) && $imgList['png']) {
							$logo = $imgList['png'];
						}
					}

					if($pblEnabled && ($pm === $pmPbl
							|| $pm === $pmIng)) {
						$pblList[] = [
							'paymentMethod'     => $paymentMethod['paymentMethod'],
							'paymentMethodCode' => $paymentMethod['paymentMethodCode'],
							'description'       => $paymentMethod['description'],
							'isAvailable'       => $paymentMethod['isOnline'],
							'logo'              => $logo,
						];
					}

					if($paylaterEnabled && $pm === $pmImojePaylater) {
						$paylaterList[] = [
							'paymentMethod'     => $paymentMethod['paymentMethod'],
							'paymentMethodCode' => $paymentMethod['paymentMethodCode'],
							'description'       => $paymentMethod['description'],
							'isAvailable'       => $paymentMethod['isOnline'],
							'logo'              => $logo,
						];
					}

					if($walletEnabled && $this->checkPaymentMethodWithInclusions($pm, $paymentMethod['paymentMethodCode'], $pmWallet, [
							$pmcGpay,
							$pmcApplepay,
						])) {

						$walletList[] = [
							'paymentMethod'     => $paymentMethod['paymentMethod'],
							'paymentMethodCode' => $paymentMethod['paymentMethodCode'],
							'description'       => $paymentMethod['description'],
							'isAvailable'       => $paymentMethod['isOnline'],
							'logo'              => $logo,
						];
					}
				}
			}
		}

		$tSandbox = $this->l('Sandbox is enabled.');

		if($pblList) {

			$this->configureCheckoutPaymentChannelOption(
				$pblList,
				$paymentOptions,
				(Configuration::get('IMOJE_PBL_PAYMENT_TITLE')
					?: $this->getPblPaymentTitleDefault()) . ($environment === Util::ENVIRONMENT_SANDBOX
					? ' ' . $tSandbox
					: ''),
				$this->getLogoPath(
					Util::getPaymentMethod('pbl'),
					Configuration::get('IMOJE_PBL_HIDE_BRAND')
				)
			);
		}

		if($paylaterList) {

			$this->configureCheckoutPaymentChannelOption(
				$paylaterList,
				$paymentOptions,
				(Configuration::get('IMOJE_PAYLATER_PAYMENT_TITLE')
					?: $this->getPaylaterPaymentTitleDefault()) . ($environment === Util::ENVIRONMENT_SANDBOX
					? ' ' . $tSandbox
					: ''),
				$this->getLogoPath(
					Util::getPaymentMethod('imoje_paylater'),
					Configuration::get('IMOJE_PAYLATER_HIDE_BRAND')
				)
			);
		}

		if($walletList) {

			$this->configureCheckoutPaymentChannelOption(
				$walletList,
				$paymentOptions,
				(Configuration::get('IMOJE_WALLET_PAYMENT_TITLE')
					?: $this->getWalletPaymentTitleDefault()) . ($environment === Util::ENVIRONMENT_SANDBOX
					? ' ' . $tSandbox
					: ''),
				$this->getLogoPath(
					Util::getPaymentMethod('wallet'),
					Configuration::get('IMOJE_WALLET_HIDE_BRAND')
				)
			);
		}

		return $paymentOptions;
	}

	/**
	 * @param array  $paymentMethodList
	 * @param array  $paymentOptions
	 * @param string $title
	 * @param string $logo
	 * @param bool   $form
	 *
	 * @return void
	 * @throws SmartyException
	 */
	protected function configureCheckoutPaymentChannelOption($paymentMethodList, &$paymentOptions, $title, $logo, $form = true)
	{

		$paymentApiController = $this->context->link->getModuleLink('imoje', 'paymentapi');

		$this->context->smarty->assign([
			'payment_method_list' => $paymentMethodList,
			'payment_link'        => $paymentApiController,
		]);

		$paymentOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
		$paymentOption = $paymentOption->setCallToActionText(
			$title)
			->setLogo($logo)
			->setModuleName($this->name)
			->setAction($paymentApiController);

		if($form) {
			$paymentOption->setForm(
				$this->context->smarty->fetch('module:imoje/views/templates/hook/payment-method-list.tpl')
			);
		}

		$paymentOptions[] = $paymentOption;
	}

	/**
	 * @param string $paymentOption
	 * @param bool   $isHidden
	 *
	 * @return array|bool|string
	 */
	public function getLogoPath($paymentOption, $isHidden)
	{

		if($isHidden || !$paymentOption) {
			return '';
		}

		return self::getMediaPath(sprintf('%s%s/assets/img/%s.png', _PS_MODULE_DIR_, $this->name, $paymentOption));
	}

	/**
	 * @return null
	 */
	public function hookPaymentReturn()
	{

		return null;
	}

	/**
	 * @return null
	 */
	public function hookDisplayReassurance()
	{

		return null;
	}

	/**
	 * @param Cart   $cart
	 * @param string $visibleMethod
	 *
	 * @return array
	 * @throws PrestaShopException
	 * @throws Exception
	 */
	public function getDataForRequestToPaywall($cart, $visibleMethod = '', $preselectMethodCode = '')
	{

		$cartId = $cart->id;

		$customer = new Customer($cart->id_customer);

		$addressBilling = new Address($cart->id_address_invoice);
		$addressDelivery = new Address($cart->id_address_delivery);

		$currencyInfo = Currency::getCurrency($cart->id_currency);

		$cartTotal = $cart->getOrderTotal();
		$total = Util::convertAmountToFractional($cartTotal);

		$failureUrl = $this->context->link->getModuleLink('imoje', 'failure');

		$imojeVersion = $this->version . ';prestashop_' . _PS_VERSION_;

		$customerPhone = ($addressDelivery->phone_mobile
			?: ($addressBilling->phone_mobile
				?: ''));

		$invoice = Configuration::get('IMOJE_ING_KSIEGOWOSC')
			? Invoice::get($this->getCart($cart), $customer->email, false)
			: '';

		$leasenow = Configuration::get('IMOJE_ING_LEASE_NOW') || $preselectMethodCode === 'lease_now'
			? $this->getLeaseNow($cart)
			: '';

		$notificationUrl = $this->context->link->getModuleLink('imoje', 'notification');

		// 0 - after checkout(order), 1 - after ipn(cart)
		if(Configuration::get('IMOJE_CREATE_ORDER_ARRANGEMENT')) {

			return Paywall::prepareData(
				$total,
				$currencyInfo['iso_code'],
				(int) $cartId,
				$customer->firstname,
				$customer->lastname,
				$notificationUrl,
				'',
				$customer->email,
				$customerPhone,
				Configuration::get('IMOJE_GA_KEY')
					? $this->context->link->getModuleLink('imoje', 'success', [
					'ga_cart_id' => $cartId,
					'ga_hash'    => hash('sha256', $cartId . $cart->secure_key),
				])
					: $this->context->link->getModuleLink('imoje', 'success'),
				$failureUrl,
				$this->context->link->getBaseLink() . urlencode(Dispatcher::getInstance()->createUrl('order')),
				$imojeVersion,
				'',
				$visibleMethod,
				$invoice,
				$leasenow,
				$preselectMethodCode
			);
		}

		$this->validateOrder($cartId,
			Configuration::get('PAYMENT_IMOJE_NEW_STATUS'),
			$cartTotal,
			$this->displayName,
			null,
			[],
			null,
			false,
			$customer->secure_key
		);

		$orderId = Order::getIdByCartId(($cart->id));

		self::checkAndChangeNegativeOrderStatus($orderId);

		return Paywall::prepareData(
			$total,
			$currencyInfo['iso_code'],
			$orderId,
			$customer->firstname,
			$customer->lastname,
			$notificationUrl,
			'',
			$customer->email,
			$customerPhone,
			$this->context->link->getPageLink(
				'order-confirmation',
				true,
				$this->context->language->id,
				[
					'id_cart'   => (int) $cart->id,
					'id_module' => (int) $this->id,
					'id_order'  => (int) $orderId,
					'key'       => $customer->secure_key,
				]
			),
			$failureUrl,
			'',
			$imojeVersion,
			'',
			$visibleMethod,
			$invoice,
			$leasenow,
			$preselectMethodCode
		);
	}

	/**
	 * @param object $cart
	 *
	 * @return string
	 * @throws PrestaShopException
	 */
	private function getLeaseNow($cart)
	{
		$leasenow = new LeaseNow();

		foreach($cart->getProducts() as $product) {

			$link = new Link();
			$url = $link->getProductLink($product);

			$category = new Category($product['id_category_default']);
			$categoryName = reset($category->name);

			$price = Util::convertAmountToFractional(round($product['price'], 2));
			$priceWt = Util::convertAmountToFractional(round($product['price_wt'], 2));

			$leasenow->addItem(
				$product['id_product'],
				$categoryName,
				$product['name'] . (empty($product['attributes'])
					? ''
					: ' - ' . $product['attributes']),
				$price,
				$priceWt - $price,
				$product['rate'],
				$product['quantity'],
				$url
			);
		}

		return $leasenow->prepare(false);
	}

	/**
	 * @param Cart   $cart
	 * @param string $paymentMethod
	 * @param string $paymentMethodCode
	 * @param string $blikCode
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getDataForRequestToApi($cart, $paymentMethod, $paymentMethodCode, $blikCode = '', $installmentsPeriod = null)
	{

		$customer = new Customer($cart->id_customer);

		$currencyInfo = Currency::getCurrency($cart->id_currency);

		$cartId = (string) $cart->id;

		$isBlik = $paymentMethod === Util::getPaymentMethod('blik')
			&& ($paymentMethodCode === Util::getPaymentMethodCode('blik')
				|| $paymentMethodCode === Util::getPaymentMethodCode('blik_oneclick'));

		$imojeApi = new Api(
			Configuration::get('IMOJE_AUTHORIZATION_TOKEN'),
			Configuration::get('IMOJE_MERCHANT_ID'),
			Configuration::get('IMOJE_SERVICE_ID'),
			Configuration::get('IMOJE_SANDBOX')
				? Util::ENVIRONMENT_SANDBOX
				: Util::ENVIRONMENT_PRODUCTION
		);

		$total = $cart->getOrderTotal();
		$currency = $currencyInfo['iso_code'];
		$failureUrl = $this->context->link->getModuleLink('imoje', 'failure');
		$imojeVersion = $this->version . ';prestashop_' . _PS_VERSION_;
		$address = $this->getAddressData($cart);

		$cid = $this->context->customer->isLogged()
			? hash('md5', $this->context->customer->id . $this->context->customer->email)
			: '';

		$invoice = Configuration::get('IMOJE_ING_KSIEGOWOSC')
			? Invoice::get($this->getCart($cart), $customer->email)
			: '';

		$notificationUrl = $this->context->link->getModuleLink('imoje', 'notification');

		// 0 - after checkout(order), 1 - after ipn(cart)
		if(Configuration::get('IMOJE_CREATE_ORDER_ARRANGEMENT')
			|| ($isBlik && Configuration::get('IMOJE_BLIK_CODE_CHECKOUT'))) {

			return $imojeApi->prepareData(
				$total,
				$currency,
				$cartId,
				$paymentMethod,
				$paymentMethodCode,
				Configuration::get('IMOJE_GA_KEY')
					? $this->context->link->getModuleLink('imoje', 'success', [
					'ga_cart_id' => $cartId,
					'ga_hash'    => hash('sha256', $cartId . $cart->secure_key),
				])
					: $this->context->link->getModuleLink('imoje', 'success'),
				$failureUrl,
				$customer->firstname,
				$customer->lastname,
				$customer->email,
				$notificationUrl,
				$imojeVersion,
				Api::TRANSACTION_TYPE_SALE,
				'',
				($blikCode && $isBlik)
					? $blikCode
					: '',
				$address,
				$cid,
				$invoice,
				$installmentsPeriod
			);
		}

		$imoje = new Imoje();

		$imoje->validateOrder($cartId,
			Configuration::get('PAYMENT_IMOJE_NEW_STATUS'),
			$total,
			$imoje->displayName,
			null,
			[],
			null,
			false,
			$customer->secure_key
		);

		$orderId = (string) Order::getIdByCartId((int) $cartId);

		self::checkAndChangeNegativeOrderStatus($orderId);

		return $imojeApi->prepareData(
			$total,
			$currency,
			$orderId,
			$paymentMethod,
			$paymentMethodCode,
			$this->context->link->getPageLink(
				'order-confirmation',
				true,
				$this->context->language->id,
				[
					'id_cart'   => (int) $cart->id,
					'id_module' => (int) $imoje->id,
					'id_order'  => (int) $orderId,
					'key'       => $customer->secure_key,
				]
			),
			$failureUrl,
			$customer->firstname,
			$customer->lastname,
			$customer->email,
			$notificationUrl,
			$imojeVersion,
			Api::TRANSACTION_TYPE_SALE,
			'',
			($blikCode && $isBlik)
				? $blikCode
				: '',
			$address,
			$cid,
			$invoice,
			$installmentsPeriod
		);
	}

	/**
	 * @param string $orderId
	 *
	 * @return void
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public static function checkAndChangeNegativeOrderStatus($orderId)
	{
		if(!Configuration::get('IMOJE_ASSIGN_STOCK_NEGATIVE_STATUS')) {

			return;
		}

		$order = new Order($orderId);

		$currentState = (string) $order->getCurrentState();

		$viableStates = [
			_PS_OS_OUTOFSTOCK_PAID_   => _PS_OS_OUTOFSTOCK_PAID_,
			_PS_OS_OUTOFSTOCK_UNPAID_ => _PS_OS_OUTOFSTOCK_UNPAID_,
		];

		if(!isset($viableStates[$currentState])) {
			return;
		}

		switch($currentState) {
			case _PS_OS_OUTOFSTOCK_PAID_:
				$order->setCurrentState(_PS_OS_PAYMENT_);
				break;
			case _PS_OS_OUTOFSTOCK_UNPAID_:
				$order->setCurrentState(Configuration::get('PAYMENT_IMOJE_NEW_STATUS'));
				break;
		}
	}

	/**
	 * @param object $cart
	 *
	 * @return array
	 */
	private static function getAddressData($cart)
	{

		$addressBilling = new Address((int) $cart->id_address_invoice);
		$addressDelivery = new Address((int) $cart->id_address_delivery);

		return [
			'billing'  => Api::prepareAddressData(
				$addressBilling->firstname,
				$addressBilling->lastname,
				empty($addressBilling->address2)
					? $addressBilling->address1
					: sprintf('%s, %s', $addressBilling->address1, $addressBilling->address2),
				$addressBilling->city,
				State::getNameById($addressBilling->id_state),
				$addressBilling->postcode,
				CountryCore::getIsoById($addressBilling->id_country)
			),
			'shipping' => Api::prepareAddressData(
				$addressDelivery->firstname,
				$addressDelivery->lastname,
				empty($addressDelivery->address2)
					? $addressDelivery->address1
					: sprintf('%s, %s', $addressDelivery->address1, $addressDelivery->address2),
				$addressDelivery->city,
				State::getNameById($addressDelivery->id_state),
				$addressDelivery->postcode,
				CountryCore::getIsoById($addressDelivery->id_country)
			),
		];
	}

	/**
	 * @param Cart $cart
	 *
	 * @return CartData
	 * @throws Exception
	 */
	public function getCart($cart)
	{

		$cartData = new CartData();

		$basis = '';
		$exempt_postfix = explode('_', Invoice::TAX_EXEMPT)[1];

		foreach($cart->getProducts() as $product) {

			$rate = (float) $product['rate'];

			$tax_name_lower = strtolower($product['tax_name']);

			$is_exempted = false;

			if(strpos($tax_name_lower, 'zw_') === 0) {

				$basis_exempt = Invoice::getBasisExempt(substr($tax_name_lower, strpos($tax_name_lower, '_') + 1));
				if($basis_exempt) {
					$basis = $basis_exempt;
				}

				$is_exempted = true;
			}

			if($tax_name_lower === Invoice::SHOP_TAX_EXEMPT || $is_exempted) {
				$rate = $exempt_postfix;
			}

			$cartData->addItem(
				$product['id_product'] . (empty($product['attributes_small'])
					? ''
					: ' - ' . $product['attributes_small']),
				$rate,
				$product['name'],
				Util::convertAmountToFractional(round($product['total_wt'], 2)),
				(float) $product['quantity'],
				false
			);
		}

		if($basis) {
			$cartData->setBasis($basis);
		}

		$cartData->setAmount(Util::convertAmountToFractional($cart->getOrderTotal()));
		$cartData->setCreatedAt(strtotime($cart->date_add));

		$carrier = new Carrier($cart->id_carrier);

		$addressBilling = new Address((int) $cart->id_address_invoice);
		$cartData->setAddressBilling(
			$addressBilling->city,
			$addressBilling->firstname . ' ' . $addressBilling->lastname,
			empty($addressBilling->phone_mobile)
				? (empty($addressBilling->phone)
				? ''
				: $addressBilling->phone)
				: $addressBilling->phone_mobile,
			empty($addressBilling->address2)
				? $addressBilling->address1
				: $addressBilling->address1 . ', ' . $addressBilling->address2,
			CountryCore::getIsoById($addressBilling->id_country),
			$addressBilling->postcode,
			empty($addressBilling->vat_number)
				? ''
				: $addressBilling->vat_number);

		$addressDelivery = new Address((int) $cart->id_address_delivery);
		$cartData->setAddressDelivery(
			$addressDelivery->city,
			$addressDelivery->firstname . ' ' . $addressDelivery->lastname,
			empty($addressDelivery->phone_mobile)
				? (empty($addressDelivery->phone)
				? ''
				: $addressDelivery->phone)
				: $addressDelivery->phone_mobile,
			empty($addressDelivery->address2)
				? $addressDelivery->address1
				: $addressDelivery->address1 . ', ' . $addressDelivery->address2,
			CountryCore::getIsoById($addressDelivery->id_country),
			$addressDelivery->postcode,
			empty($addressDelivery->vat_number)
				? ''
				: $addressDelivery->vat_number);

		$cartData->setShipping(
			$carrier->getTaxesRate(new Address((int) $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')})),
			$carrier->name,
			Util::convertAmountToFractional($cart->getTotalShippingCost())
		);

		$discount = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
		if($discount > 0) {
			$cartData->setDiscount(
				0,
				'discount',
				Util::convertAmountToFractional($discount)
			);
		}

		return $cartData;
	}

	/**
	 * Get a media path.
	 *
	 * @param string $mediaUri
	 * @param null   $cssMediaType
	 *
	 * @return array|bool|string
	 */
	public static function getMediaPath($mediaUri, $cssMediaType = null)
	{
		if(is_array($mediaUri) || empty($mediaUri)) {
			return false;
		}

		$urlData = parse_url($mediaUri);
		if(!is_array($urlData)) {
			return false;
		}

		if(!array_key_exists('host', $urlData)) {
			$mediaUriHostMode = '/' . ltrim(str_replace(str_replace([
					'/',
					'\\',
				], DIRECTORY_SEPARATOR, _PS_CORE_DIR_), __PS_BASE_URI__, $mediaUri), '/\\');
			$mediaUri = '/' . ltrim(str_replace(str_replace([
					'/',
					'\\',
				], DIRECTORY_SEPARATOR, _PS_ROOT_DIR_), __PS_BASE_URI__, $mediaUri), '/\\');
			// remove PS_BASE_URI on _PS_ROOT_DIR_ for following
			$fileUri = _PS_ROOT_DIR_ . Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $mediaUri);
			$fileUriHostMode = _PS_CORE_DIR_ . Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, Tools::str_replace_once(_PS_CORE_DIR_, '', $mediaUri));

			if(!@filemtime($fileUri) || @filesize($fileUri) === 0) {
				if(!defined('_PS_HOST_MODE_')) {
					return false;
				} elseif(!@filemtime($fileUriHostMode) || @filesize($fileUriHostMode) === 0) {
					return false;
				} else {
					$mediaUri = $mediaUriHostMode;
				}
			}

			$mediaUri = str_replace('//', '/', $mediaUri);
		}

		if($cssMediaType) {
			return [$mediaUri => $cssMediaType];
		}

		return $mediaUri;
	}

	/**
	 * @param string $paymentMethod
	 * @param string $paymentMethodCode
	 * @param string $expectedMethod
	 * @param array  $includedPaymentMethodCodes
	 *
	 * @return bool
	 */
	private function checkPaymentMethodWithInclusions($paymentMethod, $paymentMethodCode, $expectedMethod, $includedPaymentMethodCodes)
	{
		return $paymentMethod === $expectedMethod
			&& in_array($paymentMethodCode, $includedPaymentMethodCodes);
	}
}
