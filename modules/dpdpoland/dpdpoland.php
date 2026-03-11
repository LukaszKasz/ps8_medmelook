<?php
/**
 * 2019 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 * @author    DPD Polska Sp. z o.o.
 * @copyright 2019 DPD Polska Sp. z o.o.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska Sp. z o.o.
 */

use DpdPoland\Service\ConfigurationService;
use DpdPoland\Service\PudoService;
use DpdPoland\Service\ShippingService;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;

if (!defined('_PS_VERSION_')) {
    exit;
}


/**
 * Escapes database fields names
 * Used in SQL queries
 */
if (!function_exists('bqSQL')) {
    function bqSQL($string)
    {
        return str_replace('`', '\`', pSQL($string));
    }
}

require_once(dirname(__FILE__) . '/config.api.php');
require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'controller.php');
require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'webservice.php');
require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'manifest.webservice.php');
require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'package.webservice.php');
require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'pickup.webservice.php');
require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'messages.controller.php');
require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'configuration.controller.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'ObjectModel.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'Manifest.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'Package.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'CSV.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'Configuration.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'Parcel.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'ParcelProduct.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'PayerNumber.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'Country.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'Carrier.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'SenderAddress.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'Log.php');
require_once(_DPDPOLAND_CLASSES_DIR_ . 'PickupHistory.php');
require_once(_DPDPOLAND_SERVICES_DIR_ . 'shipping.php');
require_once(_DPDPOLAND_SERVICES_DIR_ . 'pudo.php');
require_once(_DPDPOLAND_SERVICES_DIR_ . 'label.php');
require_once(_DPDPOLAND_SERVICES_DIR_ . 'configuration.php');

if (version_compare(_PS_VERSION_, '1.5', '<'))
    require_once(dirname(__FILE__) . '/backward_compatibility/backward.php');

/**
 * Class DpdPoland Main module class
 */
class DpdPoland extends CarrierModule
{

    /**
     * @var string Page HTML content
     */
    private $html = '';

    /**
     * @var string Link to module configuration page
     */
    public $module_url;

    /**
     * @var array Error messages
     */
    public static $errors = array();

    /**
     * @var int|null Carrier ID, mandatory field for carrier recognision in front office
     */
    public $id_carrier;

    /**
     * @var array DPD carriers prices cache, used in front office
     */
    private static $carriers = array();

    /**
     * Current module index
     */
    const CURRENT_INDEX = 'index.php?tab=AdminModules&token=';

    /**
     * Poland country ISO code
     */
    const POLAND_ISO_CODE = 'PL';

    /**
     * @var PudoService
     */
    public $pudoService;

    /** @var LabelService */
    private $labelService;

    /** @var ConfigurationService */
    private $configurationService;

    /**
     * DpdPoland class constructor
     */
    public function __construct()
    {
        $this->name = 'dpdpoland';
        $this->tab = 'shipping_logistics';
        $this->version = '4.1.0';
        $this->author = 'DPD Polska Sp. z o.o.';

        parent::__construct();

        $this->displayName = $this->l('DPD Polska Sp. z o.o.');
        $this->description = $this->l('DPD Polska Sp. z o.o. shipping module');
        $this->bootstrap = true;

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $this->context = new Context;
            $this->smarty = $this->context->smarty;
            $this->context->smarty->assign('ps14', true);
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            $this->context->smarty->assign('ps17', true);
        }

        $this->pudoService = new PudoService;

        $this->setModuleUrl();

        $this->labelService = new LabelService;
        $this->configurationService = new ConfigurationService;

        $this->translate();

    }

    private function translate()
    {
        $this->l('Package in transit', 'dpdpoland');
        $this->l('Delivery address', 'dpdpoland');
    }

    /**
     * Sets module BackOffice URL
     */
    private function setModuleUrl()
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $this->module_url = self::CURRENT_INDEX . Tools::getValue('token') . '&configure=' . $this->name .
                '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        }
    }

    /**
     * Module installation function
     *
     * @return bool Module is installed successfully
     */
    public function install()
    {
        if (version_compare(_PS_VERSION_, '8', '<')) {
            $this->_errors[] = $this->l('Module version not supported. Please install module dedicated for this Prestashop version');

            return false;
        }

        if (!extension_loaded('soap')) {
            $this->_errors[] = $this->l('Soap Client lib is not installed');

            return false;
        }

        if (!$this->installDatabaseTables()) {
            $this->_errors[] = $this->l('Could not install database tables');

            return false;
        }

        $current_date = date('Y-m-d H:i:s');
        $shops = Shop::getShops();

        foreach (array_keys($shops) as $id_shop) {
            if (!$this->saveCSVRule((int)$id_shop, 'PL', '0', '0.5', '0', '', _DPDPOLAND_STANDARD_ID_, $current_date, $current_date, '0', '1000')
                || !$this->saveCSVRule((int)$id_shop, 'PL', '0', '0.5', '0', '0', _DPDPOLAND_STANDARD_COD_ID_, $current_date, $current_date, '0', '1000')
                || !$this->saveCSVRule((int)$id_shop, 'GB', '0', '0.5', '0', '', _DPDPOLAND_CLASSIC_ID_, $current_date, $current_date, '0', '1000')
                || !$this->saveCSVRule((int)$id_shop, '*', '0', '0.5', '0', '', _DPDPOLAND_CLASSIC_ID_, $current_date, $current_date, '0', '1000')
            ) {
                return false;
            }
        }

        if (!parent::install() ||
            !$this->registerHook('adminOrder') ||
            !$this->registerHook('paymentTop') ||
            !$this->registerHook('header') ||
            !$this->registerHook('displayCarrierExtraContent') ||
            !$this->registerHook('actionFrontControllerSetMedia')
        ) {
            return false;
        }

        $isPrestaShopVersionOver177 = version_compare(_PS_VERSION_, '1.7.7', '>=');
        $isPrestaShopVersionBefore15 = version_compare(_PS_VERSION_, '1.5', '<');
        if (!$isPrestaShopVersionBefore15) {
            if (!$this->registerHook('displayBeforeCarrier') ||
                !$this->registerHook('actionValidateOrder')
            ) {
                return false;
            }
        }

        if (!$isPrestaShopVersionBefore15) {
            if ($isPrestaShopVersionOver177 && !$this->registerHook('actionOrderGridDefinitionModifier'))
                return false;
            else
                if (!$this->registerHook('displayAdminOrdersListAfter')) //TODO handle this hook
                    return false;
        }

        /**
         * this hook is needed only in PS 1.4
         * used to track DpdPoland carriers references
         * higher versions than 1.4 already have this functionality
         */
        if (version_compare(_PS_VERSION_, '1.5', '<') && !$this->registerHook('updateCarrier')) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            if (!$this->registerHook('beforeCarrier')) {
                return false;
            }
        }

        if (version_compare(_PS_VERSION_, '1.5', '<') && !$this->installModuleOverrides()) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.5', '>=') && !$this->registerHook('displayBackofficeHeader')) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.5', '<') && !$this->registerHook('newOrder')) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.', '>=') && !$this->registerHook('displayBackofficeHeader')) {
            return false;
        }

        require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'countryList.controller.php');

        if (!DpdPolandCountryListController::disableDefaultCountries()) {
            return false;
        }

        DpdPolandLog::addLog('Installed module');
        return true;
    }

    /**
     * Sets specific class name for admin orders list override
     *
     * @return bool Override class name set successfully
     */
    private function installModuleOverrides()
    {
        return Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'tab`
            SET `class_name` = "AdminOrdersOverride",
                `module` = "' . pSQL($this->name) . '"
            WHERE `class_name` = "AdminOrders"
        ');
    }

    /**
     * Removes specific class name used to set override for orders list
     *
     * @return bool Override class name removed successfully
     */
    private function uninstallModuleOverrides()
    {
        if (!Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'tab`
            SET `class_name` = "AdminOrders",
                `module` = ""
            WHERE `class_name` = "AdminOrdersOverride"
        ')) {
            return false;
        }

        return true;
    }

    /**
     * Creates database tables used for module
     *
     * @return bool Database tables created successfully
     */
    private function installDatabaseTables()
    {
        $sql = array();

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_PRICE_RULE_DB_ . '` (
				`id_csv` int(11) NOT NULL AUTO_INCREMENT,
				`id_shop` int(11) NOT NULL,
				`date_add` datetime DEFAULT NULL,
				`date_upd` datetime DEFAULT NULL,
				`iso_country` varchar(255) NOT NULL,
				`price_from` decimal(20,6) NOT NULL,
				`price_to` decimal(20,6) NOT NULL,
				`weight_from` decimal(20,6) NOT NULL,
				`weight_to` decimal(20,6) NOT NULL,
				`parcel_price` float NOT NULL,
				`cod_price` varchar(255) NOT NULL,
				`id_carrier` varchar(11) NOT NULL,
				PRIMARY KEY (`id_csv`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_PAYER_NUMBERS_DB_ . '` (
				`id_dpdpoland_payer_number` int(11) NOT NULL AUTO_INCREMENT,
				`id_shop` int(11) NOT NULL,
				`payer_number` varchar(255) NOT NULL,
				`name` varchar(255) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_dpdpoland_payer_number`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_COUNTRY_DB_ . '` (
				`id_dpdpoland_country` int(11) NOT NULL AUTO_INCREMENT,
				`id_shop` int(11) NOT NULL,
				`id_country` int(11) NOT NULL,
				`enabled` tinyint(1) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_dpdpoland_country`,`id_shop`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_MANIFEST_DB_ . '` (
			  `id_manifest` int(11) NOT NULL AUTO_INCREMENT,
			  `id_manifest_ws` int(11) NOT NULL,
			  `id_package_ws` int(11) NOT NULL,
			  `date_add` datetime NOT NULL,
			  `date_upd` datetime NOT NULL,
			  PRIMARY KEY (`id_manifest`),
			  UNIQUE KEY `id_manifest_ws` (`id_manifest_ws`,`id_package_ws`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_PACKAGE_DB_ . '` (
			  `id_package` int(11) NOT NULL AUTO_INCREMENT,
			  `id_package_ws` int(11) NOT NULL,
			  `id_order` int(10) NOT NULL,
			  `sessionId` int(11) NOT NULL,
			  `sessionType` varchar(50) NOT NULL,
			  `payerNumber` varchar(255) NOT NULL,
			  `id_address_sender` int(10) NOT NULL,
			  `id_address_delivery` int(10) NOT NULL,
			  `cod_amount` decimal(17,2) DEFAULT NULL,
			  `declaredValue_amount` decimal(17,2) DEFAULT NULL,
			  `ref1` varchar(255) DEFAULT NULL,
			  `ref2` varchar(255) DEFAULT NULL,
			  `additional_info` text,
			  `labels_printed` tinyint(1) NOT NULL DEFAULT "0",
			  `id_sender_address` int(11) NOT NULL,
			  `cud` tinyint(1) NOT NULL,
			  `rod` tinyint(1) NOT NULL,
			  `dpde` tinyint(1) NOT NULL, 
              `dpdnd` tinyint(1) NOT NULL,
              `dpdtoday` tinyint(1) NOT NULL DEFAULT "0",
              `dpdsaturday` tinyint(1) NOT NULL DEFAULT "0",
              `dpdfood` tinyint(1) NOT NULL DEFAULT "0",
              `dpdfood_limit_date` varchar(15) DEFAULT NULL,
              `dpdlq` tinyint(1) NOT NULL DEFAULT "0",
              `duty` tinyint(1) NOT NULL,
              `duty_amount` decimal(17,2) DEFAULT NULL,
              `duty_currency` varchar(3) DEFAULT NULL,
			  `date_add` datetime NOT NULL,
			  `date_upd` datetime NOT NULL,
			  PRIMARY KEY (`id_package`),
			  UNIQUE KEY `id_package_ws` (`id_package_ws`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_PARCEL_DB_ . '` (
				`id_parcel` int(11) NOT NULL,
				`id_package_ws` int(11) NOT NULL,
				`waybill` varchar(50) NOT NULL,
				`content` text NOT NULL,
				`weight` decimal(20,6) NOT NULL,
				`weight_adr` decimal(20,6) NULL,
				`height` decimal(20,6) NOT NULL,
				`length` decimal(20,6) NOT NULL,
				`width` decimal(20,6) NOT NULL,
				`number` int(5) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_parcel`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_CARRIER_DB_ . '` (
				`id_dpdpoland_carrier` int(10) NOT NULL AUTO_INCREMENT,
				`id_carrier` int(10) NOT NULL,
				`id_reference` int(10) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_dpdpoland_carrier`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_PARCEL_PRODUCT_DB_ . '` (
				`id_parcel_product` int(10) NOT NULL AUTO_INCREMENT,
				`id_parcel` int(11) NOT NULL,
				`id_product` int(10) NOT NULL,
				`id_product_attribute` int(10) NOT NULL,
				`name` varchar(255) NOT NULL,
				`weight` decimal(20,6) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_parcel_product`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_SENDER_ADDRESS_DB_ . '` (
				`id_sender_address` int(10) NOT NULL AUTO_INCREMENT,
				`alias` varchar(50) NOT NULL,
				`company` varchar(50) NOT NULL,
				`name` varchar(80) NOT NULL,
				`address` varchar(255) NOT NULL,
				`city` varchar(50) NOT NULL,
				`email` varchar(80) NOT NULL,
				`postcode` varchar(20) NOT NULL,
				`phone` varchar(50) NOT NULL,
				`id_shop` int(10) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_sender_address`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

        $sql[] = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDPOLAND_PICKUP_HISTORY_DB_ . '` (
				`id_pickup_history` int(10) NOT NULL AUTO_INCREMENT,
				`order_number` varchar(255) NULL,
				`sender_address` varchar(255) NULL,
				`sender_company` varchar(255) NULL,
				`sender_name` varchar(255) NULL,
				`sender_phone` varchar(50) NULL,
				`pickup_date` datetime NULL,
				`pickup_time` varchar(50) NULL,
				`type` varchar(50) NULL,
				`envelope` int(10) NULL,
                `package` int(10) NULL,
                `package_weight_all` decimal(20,6) NULL,
				`package_heaviest_weight` decimal(20,6) NULL,
				`package_heaviest_width` decimal(20,6) NULL,
				`package_heaviest_length` decimal(20,6) NULL,
				`package_heaviest_height` decimal(20,6) NULL,
				`pallet` int(10) NULL,
				`pallet_weight` decimal(20,6) NULL,
				`pallet_heaviest_weight` decimal(20,6) NULL,
				`pallet_heaviest_height` decimal(20,6) NULL,
				`id_shop` int(10) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_pickup_history`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

        $sql[] = '
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dpdpoland_pudo_cart` (
              `pudo_code` VARCHAR(255) NOT NULL,
              `id_cart` int(11) NOT NULL,
              PRIMARY KEY (`id_cart`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        foreach ($sql as $query) {
            if (!$r = Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Creates CSV price rule
     *
     * @param int $id_shop Shop ID
     * @param string $iso_country Country ISO code
     * @param float $weight_from Amount of weight from which price rule is applied
     * @param float $weight_to Amount of weight to which price rule is applied
     * @param float $parcel_price Carrier price
     * @param float $cod_price Additional COD carrier price value
     * @param int $id_carrier Carrier ID
     * @param string|datetime $date_add Date when CSV price is saved
     * @param string|datetime $date_upd Last date when CSV price was updated
     * @param $price_from
     * @param $price_to
     * @return bool CSV price rule created successfully
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function saveCSVRule($id_shop, $iso_country, $weight_from, $weight_to, $parcel_price, $cod_price, $id_carrier, $date_add, $date_upd, $price_from, $price_to)
    {
        DpdPolandLog::addLog('SaveCSVRule');

        $csv = new DpdPolandCSV();
        $csv->id_shop = (int)$id_shop;
        $csv->iso_country = $iso_country;
        $csv->weight_from = $weight_from;
        $csv->weight_to = $weight_to;
        $csv->price_from = $price_from;
        $csv->price_to = $price_to;
        $csv->parcel_price = $parcel_price;
        $csv->cod_price = $cod_price;
        $csv->id_carrier = $id_carrier;
        $csv->date_add = $date_add;
        $csv->date_upd = $date_upd;

        return $csv->add();
    }

    /**
     * Uninstalls module
     *
     * @return bool Module uninstalled successfully
     */
    public function uninstall()
    {
        DpdPolandLog::addLog('uninstall module');

        require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'service.php');
        require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'dpd_classic.service.php');
        require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'dpd_standard.service.php');
        require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'dpd_standard_cod.service.php');
        require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'dpd_pudo.service.php');
        require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'dpd_pudo_cod.service.php');

        if (!$this->uninstallModuleOverrides()) {
            return false;
        }

        return
            parent::uninstall() &&
            DpdPolandCarrierClassicService::delete() &&
            DpdPolandCarrierStandardService::delete() &&
            DpdPolandCarrierStandardCODService::delete() &&
            DpdPolandCarrierPudoService::delete() &&
            DpdPolandCarrierPudoCodService::delete() &&
            DpdPolandConfiguration::deleteConfiguration() &&
            $this->dropTables() &&
            Configuration::deleteByName(DpdPolandWS::DEBUG_FILENAME);
    }

    /**
     * Removes module database tables
     *
     * @return bool Module database tables removed successfully
     */
    private function dropTables()
    {
        return DB::getInstance()->execute('
			DROP TABLE IF EXISTS
				`' . _DB_PREFIX_ . _DPDPOLAND_PRICE_RULE_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDPOLAND_PAYER_NUMBERS_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDPOLAND_COUNTRY_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDPOLAND_MANIFEST_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDPOLAND_PACKAGE_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDPOLAND_PARCEL_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDPOLAND_PARCEL_PRODUCT_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDPOLAND_CARRIER_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDPOLAND_SENDER_ADDRESS_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDPOLAND_PICKUP_HISTORY_DB_ . '`,
				`' . _DB_PREFIX_ . 'dpdpoland_pudo_cart`
		');
    }

    /**
     * Checks if Soap Client is installed and enabled in current server
     *
     * @return bool Soap Client exists in server
     */
    private function soapClientExists()
    {
        return (bool)class_exists('SoapClient');
    }

    /**
     * Main module function used to display pages content
     *
     * @return bool|string Module content
     * @throws PrestaShopException
     */
    public function getContent()
    {
        $this->html .= $this->getContentHeader();

        if (!$this->soapClientExists())
            return $this->adminDisplayWarning($this->l('SoapClient class is missing'));

        if (_DPDPOLAND_DEBUG_MODE_)
            $this->displayDebugInfo();

        $this->displayFlashMessagesIfIsset();

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $this->addJS(_DPDPOLAND_JS_URI_ . 'backoffice.js');
            $this->addCSS(_DPDPOLAND_CSS_URI_ . 'backoffice.css');
            $this->addCSS(_DPDPOLAND_CSS_URI_ . 'toolbar.css');
        } else {
            $this->context->controller->addJS(_DPDPOLAND_JS_URI_ . 'backoffice.js');
            $this->context->controller->addCSS(_DPDPOLAND_CSS_URI_ . 'backoffice.css');
        }

        $this->setGlobalVariablesForAjax();
        $this->html .= $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/global_variables.tpl');

        $country_currency_error_message_text = $this->l('PL country and PLN currency must be installed; CURL must be enabled');
        $configuration_error_message_text = $this->l('Module is not configured yet. Please check required settings and add at least one sender address');

        $current_page = Tools::getValue('menu');
        $required_configuration = DpdPolandConfiguration::checkRequiredConfiguration();

        if (!$current_page && !$required_configuration)
            $current_page = 'configuration';

        switch ($current_page) {
            case 'arrange_pickup':
                $this->addDateTimePickerPlugins();
                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'arrange_pickup.controller.php');

                $controller = new DpdPolandArrangePickUpController;

                if (Tools::isSubmit('requestPickup')) {
                    $data = $controller->getData();

                    if ($controller->validate()) {
                        $pickup = new DpdPolandPickup;

                        foreach ($data as $element => $value)
                            $pickup->$element = $value;

                        if (!$pickup->arrange())
                            $this->html .= $this->displayError(reset(DpdPolandPickup::$errors));
                        else {
                            require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'pickupHistoryList.controller.php');
                            $pickupHistoryListController = new DpdPolandPickupHistoryListController;
                            $pickupHistoryListController->save($pickup);

                            $error_message = sprintf($this->l('Pickup was successfully arranged. Number of order is: %d'), $pickup->id_pickup);
                            self::addFlashMessage($error_message);

                            $redirect_uri = $this->module_url . '&menu=arrange_pickup';
                            Tools::redirectAdmin($redirect_uri);
                        }
                    } else
                        $this->html .= $this->displayError(reset(DpdPolandArrangePickUpController::$errors));
                }

                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('Arrange PickUp')));
                $this->displayNavigation();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!$required_configuration)
                    return $this->html .= $this->displayErrors(array($configuration_error_message_text));

                $this->html .= $controller->getPage();
                break;
            case 'pickup_history':
                $this->addDateTimePickerPlugins();
                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'pickupHistoryList.controller.php');

                $controller = new DpdPolandPickupHistoryListController;

                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('Pickup history')));
                $this->displayNavigation();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!$required_configuration)
                    return $this->html .= $this->displayErrors(array($configuration_error_message_text));

                $this->html .= $controller->getListHTML();
                break;
            case 'sender_address_form':
                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'senderAddressForm.controller.php');

                $controller = new DpdPolandSenderAddressFormController();
                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('Sender address')));
                $this->displayNavigation();

                $controller->controllerActions();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!$required_configuration)
                    $this->html .= $this->displayErrors(array($configuration_error_message_text));

                if (!version_compare(_PS_VERSION_, '1.5', '<'))
                    $this->displayShopRestrictionWarning();

                $this->html .= $controller->getForm();
                break;
            case 'configuration':
                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'configuration.controller.php');

                $controller = new DpdPolandConfigurationController;

                if (Tools::isSubmit(DpdPolandConfigurationController::SETTINGS_SAVE_ACTION)) {
                    $controller->validateSettings();
                    $controller->createDeleteCarriers();

                    if (!DpdPolandConfigurationController::$errors)
                        $controller->saveSettings();
                    else
                        $this->html .= $this->displayErrors(DpdPolandConfigurationController::$errors);
                }

                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('Settings')));
                $this->displayNavigation();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!$required_configuration)
                    $this->html .= $this->displayErrors(array($configuration_error_message_text));

                if (!version_compare(_PS_VERSION_, '1.5', '<'))
                    $this->displayShopRestrictionWarning();

                $this->html .= $controller->getSettingsPage();
                break;
            case 'csv':
                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'csv.controller.php');

                $controller = new DpdPolandCSVController;

                if (Tools::isSubmit(DpdPolandCSVController::SETTINGS_SAVE_CSV_ACTION)) {
                    $csv_data = $controller->readCSVData();
                    if ($csv_data === false) {
                        self::addFlashError($this->l('Wrong CSV file'));
                        Tools::redirectAdmin($this->module_url . '&menu=csv');
                    }

                    $message = $controller->validateCSVData($csv_data);
                    if ($message !== true)
                        $this->html .= $this->displayErrors($message);
                    else {
                        if ($controller->saveCSVData($csv_data))
                            self::addFlashMessage($this->l('CSV data was successfully saved'));
                        else
                            self::addFlashError($this->l('CSV data could not be saved'));

                        Tools::redirectAdmin($this->module_url . '&menu=csv');
                    }
                }

                if (Tools::isSubmit(DpdPolandCSVController::SETTINGS_DELETE_CSV_ACTION))
                    $controller->deleteCSV();

                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('CSV prices import')));
                $this->displayNavigation();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!$required_configuration)
                    return $this->html .= $this->displayErrors(array($configuration_error_message_text));

                if (!version_compare(_PS_VERSION_, '1.5', '<') && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $this->html .= $this->displayWarnings(array(
                        $this->l('CSV management is disabled when all shops or group of shops are selected')));
                    break;
                }

                $this->html .= $controller->getCSVPage();
                break;
            case 'help':
                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('Help')));
                $this->displayNavigation();
                $this->html .= $this->displayHelp();
                break;
            case 'manifest_list':
                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('Manifest list')));
                $this->displayNavigation();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!$required_configuration)
                    return $this->html .= $this->displayErrors(array($configuration_error_message_text));

                if (!version_compare(_PS_VERSION_, '1.5', '<') && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $this->html .= $this->displayWarnings(array(
                        $this->l('Manifests functionality is disabled when all shops or group of shops are chosen')));
                    break;
                }

                $this->addDateTimePickerPlugins();
                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'manifestList.controller.php');
                $manifest_list_controller = new DpdPolandManifestListController();

                if (Tools::isSubmit('printManifest')) {
                    $id_manifest_ws = (int)Tools::getValue('id_manifest_ws');
                    $manifest_list_controller->printManifest((int)$id_manifest_ws);
                }

                $this->html .= $manifest_list_controller->getListHTML();
                break;
            case 'sender_address':
                $this->context->smarty->assign(array(
                    'breadcrumb' => array($this->displayName, $this->l('Sender addresses')),
                    'form_url' => $this->module_url . '&menu=sender_address_form'
                ));
                $this->displayNavigation();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!version_compare(_PS_VERSION_, '1.5', '<') && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $this->html .= $this->displayWarnings(array(
                        $this->l('Addresses management is disabled when all shops or group of shops are chosen')));
                    break;
                }

                if (!$required_configuration)
                    $this->html .= $this->displayErrors(array($configuration_error_message_text));

                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'senderAddressList.controller.php');
                $sender_address_controller = new DpdPolandSenderAddressListController();

                $sender_address_controller->controllerActions();

                $this->html .= $sender_address_controller->getListHTML();
                break;
            case 'parcel_history_list':
                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('Parcels history')));
                $this->displayNavigation();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!$required_configuration)
                    return $this->html .= $this->displayErrors(array($configuration_error_message_text));

                if (!version_compare(_PS_VERSION_, '1.5', '<') && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $this->html .= $this->displayWarnings(array(
                        $this->l('Parcels functionality is disabled when all shops or group of shops are chosen')));
                    break;
                }

                $this->addDateTimePickerPlugins();
                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'parcelHistoryList.controller.php');
                $parcel_history_list_controller = new DpdPolandParcelHistoryController();
                $this->html .= $parcel_history_list_controller->getList();
                break;
            case 'country_list':
                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('Shipment countries')));
                $this->displayNavigation();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!$required_configuration)
                    return $this->html .= $this->displayErrors(array($configuration_error_message_text));

                if (!version_compare(_PS_VERSION_, '1.5', '<') && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $this->html .= $this->displayWarnings(array(
                        $this->l('Countries functionality is disabled when all shops or group of shops are chosen')));
                    break;
                }

                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'countryList.controller.php');
                $country_list_controller = new DpdPolandCountryListController();

                if (Tools::getValue('disable_country') && $id_country = Tools::getValue('id_country'))
                    if ($country_list_controller->changeEnabled((int)$id_country, true))
                        $country_list_controller->displaySuccessStatusChangingMessage();
                    else
                        $this->displayError($this->l('Could not change country status'));

                if (Tools::getValue('enable_country') && $id_country = Tools::getValue('id_country'))
                    if ($country_list_controller->changeEnabled((int)$id_country))
                        $country_list_controller->displaySuccessStatusChangingMessage();
                    else
                        $this->displayError($this->l('Could not change country status'));

                if (Tools::isSubmit('disableCountries')) {
                    if ($countries = Tools::getValue('CountriesBox'))
                        $country_list_controller->changeEnabledMultipleCountries($countries, true);
                    else
                        $this->html .= $this->displayError($this->l('No selected countries'));
                }

                if (Tools::isSubmit('enableCountries')) {
                    if ($countries = Tools::getValue('CountriesBox'))
                        $country_list_controller->changeEnabledMultipleCountries($countries);
                    else
                        $this->html .= $this->displayError($this->l('No selected countries'));
                }

                $this->html .= $country_list_controller->getListHTML();
                break;
            case 'packages_list':
            default:
                $this->context->smarty->assign('breadcrumb', array($this->displayName, $this->l('Packages')));
                $this->displayNavigation();

                if (!$this->checkModuleAvailability())
                    return $this->html .= $this->displayErrors(array($country_currency_error_message_text));

                if (!$required_configuration)
                    return $this->html .= $this->displayErrors(array($configuration_error_message_text));

                if (!version_compare(_PS_VERSION_, '1.5', '<') && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $this->html .= $this->displayWarnings(array(
                        $this->l('Packages functionality is disabled when all shops or group of shops are chosen')));
                    break;
                }

                $this->addDateTimePickerPlugins();
                require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'packageList.controller.php');

                if (Tools::isSubmit('printManifest'))
                    DpdPolandPackageListController::printManifest($this);

                if (Tools::isSubmit('printLabelsA4Format'))
                    DpdPolandPackageListController::printLabels(DpdPolandConfiguration::PRINTOUT_FORMAT_A4);

                if (Tools::isSubmit('printLabelsLabelFormat'))
                    DpdPolandPackageListController::printLabels(DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL);

                $package_list_controller = new DpdPolandPackageListController();
                $this->html .= $package_list_controller->getList();
                break;
        }

        return $this->html;
    }

    /**
     * Displays specific DPD info block in every module page
     *
     * @return string DPD info block HTML content
     */
    private function getContentHeader()
    {
        $this->context->smarty->assign(array(
            'module_display_name' => $this->displayName,
            'ps_16' => version_compare(_PS_VERSION_, '1.6', '>=')
        ));

        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/content_header.tpl');
    }

    /**
     * Displays warning message that module is in DEBUG mode
     */
    private function displayDebugInfo()
    {
        $warning_message = $this->l('Module is in DEBUG mode');

        if (DpdPoland::getConfig(DpdPolandWS::DEBUG_FILENAME)) {
            if (version_compare(_PS_VERSION_, '1.5', '<'))
                $warning_message .= $this->l(', file:') . ' ' . _DPDPOLAND_MODULE_URI_ . DpdPoland::getConfig(DpdPolandWS::DEBUG_FILENAME);
            else
                $warning_message .= '<br />
				<a target="_blank" href="' . _DPDPOLAND_MODULE_URI_ . DpdPoland::getConfig(DpdPolandWS::DEBUG_FILENAME) . '">
					' . $this->l('View debug file') . '
				</a>';
        }

        if (version_compare(_PS_VERSION_, '1.5', '<'))
            $this->html .= $this->displayWarnings(array($warning_message));
        else
            $this->adminDisplayWarning($warning_message);
    }

    /**
     * Displays help page content
     *
     * @return string Help page HTML content
     */
    private function displayHelp()
    {
        if (Tools::isSubmit('print_pdf')) {
            $filename = 'dpdpoland_eng.pdf';
            if (Tools::isSubmit('polish'))
                $filename = 'dpdpoland_pol.pdf';

            ob_end_clean();
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $this->l('manual') . '.pdf"');
            readfile(_PS_MODULE_DIR_ . 'dpdpoland/manual/' . $filename);
            exit;
        }

        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/help.tpl');
    }

    /**
     * Checks if module fits into minimal requirements to be available for use
     *
     * @return bool Module is available for management
     */
    private function checkModuleAvailability()
    {
        return (bool)Country::getByIso(self::POLAND_ISO_CODE) &&
            (bool)Currency::getIdByIsoCode(_DPDPOLAND_CURRENCY_ISO_) && (bool)function_exists('curl_init');
    }

    /**
     * Assigns into Smarty some variables which are used for AJAX
     */
    private function setGlobalVariablesForAjax()
    {
        $this->context->smarty->assign(array(
            'dpdpoland_ajax_uri' => _DPDPOLAND_AJAX_URI_,
            'dpdpoland_pdf_uri' => _DPDPOLAND_PDF_URI_,
            'dpdpoland_token' => sha1(_COOKIE_KEY_ . $this->name),
            'dpdpoland_id_shop' => (int)$this->context->shop->id,
            'dpdpoland_id_lang' => (int)$this->context->language->id,
            'dpdpoland_order_uri' => $this->getCurrentOrderLink()
        ));
    }

    /**
     * Builds a link of current order
     *
     * @return string Link of current order
     */
    private function getCurrentOrderLink()
    {
        $id_order = (int)Tools::getValue('id_order');

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            return 'index.php?tab=AdminOrdersOverride&id_order=' . (int)$id_order . '&vieworder&token=' .
                Tools::getAdminTokenLite('AdminOrdersOverride');
        }

        return 'index.php?controller=AdminOrders&id_order=' . (int)$id_order . '&vieworder&token=' .
            Tools::getAdminTokenLite('AdminOrders');
    }

    /**
     * Saves package from order management page
     * Used via AJAX
     *
     * @return bool Package saved successfully
     */
    public function savePackageFromPost()
    {
        $savePackages = new ShippingService;
        $result = $savePackages->savePackageFromPost((array)$savePackages->mapShippingModel(), Tools::getValue('dpdpoland_PayerNumber'));
        if (count($result) == 0) {
            self::$errors = $savePackages::$errors;
            return null;
        }

        return $result[0];
    }

    /**
     * Validates address
     *
     * @param int $id_address Address ID
     * @param int $id_method Service ID
     * @return bool Address is valid
     */
    public static function validateAddressForPackageSession($id_address, $id_method)
    {
        $address = new Address($id_address);
        $country = Country::getIsoById((int)$address->id_country);

        if ($country == self::POLAND_ISO_CODE && $id_method == _DPDPOLAND_CLASSIC_ID_ ||
            $country != self::POLAND_ISO_CODE && $id_method == _DPDPOLAND_STANDARD_ID_ ||
            $country != self::POLAND_ISO_CODE && $id_method == _DPDPOLAND_STANDARD_COD_ID_ ||
            $country != self::POLAND_ISO_CODE && $id_method == _DPDPOLAND_PUDO_ID_ ||
            $country != self::POLAND_ISO_CODE && $id_method == _DPDPOLAND_PUDO_COD_ID_)
            return false;

        return true;
    }


    /**
     * Loads datepicker / timepicker plugins
     */
    private function addDateTimePickerPlugins()
    {
        if (version_compare(_PS_VERSION_, '1.5', '<'))
            return includeDatepicker(null);

        $this->context->controller->addJqueryUI(array(
            'ui.slider', // for datetimepicker
            'ui.datepicker' // for datetimepicker
        ));

        $this->context->controller->addJS(array(
            _DPDPOLAND_JS_URI_ . 'jquery.bpopup.min.js',
            _PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js' // for datetimepicker
        ));

        if (version_compare(_PS_VERSION_, '1.6', '<'))
            $this->addCSS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css'); // for datetimepicker
    }

    /**
     * Displays warning message when all shops or group of shops is chosen
     */
    private function displayShopRestrictionWarning()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $this->html .= $this->displayWarnings(array($this->l('You have chosen a group of shops, all the changes will be set for all shops in this group')));
            DpdPolandLog::addLog('displayShopRestrictionWarning() You have chosen a group of shops, all the changes will be set for all shops in this group');
        }

        if (Shop::getContext() == Shop::CONTEXT_ALL) {
            $this->html .= $this->displayWarnings(array($this->l('You have chosen all shops, all the changes will be set for all shops')));
            DpdPolandLog::addLog('displayShopRestrictionWarning() You have chosen all shops, all the changes will be set for all shops');
        }
    }

    /**
     * Displays HTML content in current page
     *
     * @param string $html HTML content
     */
    public function outputHTML($html)
    {
        $this->html .= $html;
    }

    /**
     * Loads CSS files on PS 1.4
     *
     * @param string $css_uri CSS file URL address
     */
    public static function addCSS($css_uri)
    {
        echo '<link href="' . $css_uri . '" rel="stylesheet" type="text/css">';
    }

    /**
     * Loads JS files on PS 1.4
     *
     * @param string $js_uri JS file URL address
     */
    public static function addJS($js_uri)
    {
        echo '<script src="' . $js_uri . '" type="text/javascript"></script>';
    }

    /**
     * Displays navigation menu
     */
    private function displayNavigation()
    {
        if (version_compare(_PS_VERSION_, '1.6', '>='))
            $this->context->smarty->assign('meniutabs', $this->initNavigation16());

        $this->context->smarty->assign('module_link', $this->module_url);
        $this->html .= $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/navigation.tpl');
    }

    /**
     * Assigns menu items
     *
     * @return array Menu items
     */
    private function initNavigation16()
    {
        $menu_tabs = array(
            'arrange_pickup' => array(
                'short' => 'Arrange Pickup',
                'desc' => $this->l('Arrange Pickup'),
                'href' => $this->module_url . '&menu=arrange_pickup',
                'active' => false,
                'imgclass' => 'icon-calendar'
            ),
            'pickup_history' => array(
                'short' => 'Pickup history',
                'desc' => $this->l('Pickup history'),
                'href' => $this->module_url . '&menu=pickup_history',
                'active' => false,
                'imgclass' => 'icon-truck'
            ),
            'packages_list' => array(
                'short' => 'Packages list',
                'desc' => $this->l('Packages list'),
                'href' => $this->module_url . '&menu=packages_list',
                'active' => false,
                'imgclass' => 'icon-list'
            ),
            'manifest_list' => array(
                'short' => 'Manifest list',
                'desc' => $this->l('Manifest list'),
                'href' => $this->module_url . '&menu=manifest_list',
                'active' => false,
                'imgclass' => 'icon-th'
            ),
            'parcel_history_list' => array(
                'short' => 'Parcels history',
                'desc' => $this->l('Parcels history'),
                'href' => $this->module_url . '&menu=parcel_history_list',
                'active' => false,
                'imgclass' => 'icon-history'
            ),
            'country_list' => array(
                'short' => 'Shipment countries',
                'desc' => $this->l('Shipment countries'),
                'href' => $this->module_url . '&menu=country_list',
                'active' => false,
                'imgclass' => 'icon-globe'
            ),
            'csv' => array(
                'short' => 'CSV prices import',
                'desc' => $this->l('CSV prices import'),
                'href' => $this->module_url . '&menu=csv',
                'active' => false,
                'imgclass' => 'icon-file'
            ),
            'sender_address' => array(
                'short' => 'Sender',
                'desc' => $this->l('Sender addresses'),
                'href' => $this->module_url . '&menu=sender_address',
                'active' => false,
                'imgclass' => 'icon-home'
            ),
            'configuration' => array(
                'short' => 'Settings',
                'desc' => $this->l('Settings'),
                'href' => $this->module_url . '&menu=configuration',
                'active' => false,
                'imgclass' => 'icon-cogs'
            ),
            'help' => array(
                'short' => 'Help',
                'desc' => $this->l('Help'),
                'href' => $this->module_url . '&menu=help',
                'active' => false,
                'imgclass' => 'icon-info-circle'
            ),
        );

        $current_page = Tools::getValue('menu');
        $required_configuration = DpdPolandConfiguration::checkRequiredConfiguration();

        if (!$current_page) {
            if (!$required_configuration)
                $current_page = 'configuration';
            else
                $current_page = 'packages_list';
        }

        if ($current_page == 'sender_address_form') {
            $current_page = 'sender_address';
        }

        if (in_array($current_page, array(
            'arrange_pickup',
            'pickup_history',
            'packages_list',
            'manifest_list',
            'parcel_history_list',
            'country_list',
            'configuration',
            'csv',
            'help',
            'sender_address'
        )))
            $menu_tabs[$current_page]['active'] = true;

        return $menu_tabs;
    }

    /**
     * Adds success message into current session
     *
     * @param string $msg Success message text
     */
    public static function addFlashMessage($msg)
    {
        $messages_controller = new DpdPolandMessagesController();
        $messages_controller->setSuccessMessage($msg);

        DpdPolandLog::addLog($msg);
    }

    /**
     * Adds error message into current session
     *
     * @param string $msg Error message text
     */
    public static function addFlashError($msg)
    {
        $messages_controller = new DpdPolandMessagesController();

        if (is_array($msg)) {
            DpdPolandLog::addError(json_encode($msg));
            foreach ($msg as $message)
                $messages_controller->setErrorMessage($message);
        } else {
            DpdPolandLog::addError($msg);
            $messages_controller->setErrorMessage($msg);
        }

    }

    /**
     * Displays success message only until page reload
     */
    private function displayFlashMessagesIfIsset()
    {
        $messages_controller = new DpdPolandMessagesController();

        if ($success_message = $messages_controller->getSuccessMessage())
            $this->html .= $this->displayConfirmation($success_message);

        if ($error_message = $messages_controller->getErrorMessage())
            $this->html .= $this->displayErrors($error_message);
    }

    /**
     * Displays error message
     *
     * @param string|array $errors Error message(s)
     * @return string Error message
     */
    public function displayErrors($errors)
    {
        if (!is_array($errors))
            $errors = array($errors);

        DpdPolandLog::addError(json_encode($errors));
        $this->context->smarty->assign('errors', $errors);
        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/errors.tpl');
    }

    /**
     * Displays warning message
     *
     * @param string|array $warnings Warning message(s)
     * @return string
     */
    public function displayWarnings($warnings)
    {
        DpdPolandLog::addLog($warnings);
        $this->context->smarty->assign('warnings', $warnings);
        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/warnings.tpl');
    }

    /**
     * Checks if field exists in GET or POST
     * Returns value from GET or POST if exists
     *
     * @param string $name Field name
     * @param null|string $default_value Field default value
     * @return mixed|null Field value
     */
    public static function getInputValue($name, $default_value = null)
    {
        return (Tools::isSubmit($name)) ? Tools::getValue($name) : $default_value;
    }

    /**
     * Returns service ID according to carrier
     *
     * @param int $id_carrier Carrier ID
     * @return bool|int Service ID
     */
    public static function getMethodIdByCarrierId($id_carrier)
    {
        if (!$id_reference = self::getReferenceIdByCarrierId($id_carrier))
            return false;

        switch ($id_reference) {
            case DpdPoland::getConfig(DpdPolandConfiguration::CARRIER_STANDARD_ID):
                return _DPDPOLAND_STANDARD_ID_;
            case DpdPoland::getConfig(DpdPolandConfiguration::CARRIER_STANDARD_COD_ID):
                return _DPDPOLAND_STANDARD_COD_ID_;
            case DpdPoland::getConfig(DpdPolandConfiguration::CARRIER_CLASSIC_ID):
                return _DPDPOLAND_CLASSIC_ID_;
            case DpdPoland::getConfig(DpdPolandConfiguration::CARRIER_PUDO_ID):
                return _DPDPOLAND_PUDO_ID_;
            case DpdPoland::getConfig(DpdPolandConfiguration::CARRIER_PUDO_COD_ID):
                return _DPDPOLAND_PUDO_COD_ID_;
            default:
                return false;
        }
    }

    /**
     * Returns carrier reference according to its ID
     *
     * @param int $id_carrier Carrier ID
     * @return false|null|string Carrier reference
     */
    public static function getReferenceIdByCarrierId($id_carrier)
    {
        if (version_compare(_PS_VERSION_, '1.5', '<'))
            return DpdPolandCarrier::getReferenceByIdCarrier($id_carrier);

        return Db::getInstance()->getValue('
			SELECT `id_reference`
			FROM `' . _DB_PREFIX_ . 'carrier`   
			WHERE `id_carrier`=' . (int)$id_carrier
        );
    }

    /**
     * Returns formatted sender address
     *
     * @param int $id_sender_address Sender address ID
     * @param bool|int $id_address Existing PrestaShop address ID
     * @return array Formatted sender address
     */
    private function getSenderAddress($id_sender_address, $id_address = false)
    {
        if ($id_address) {
            $address = new Address((int)$id_address);
            return array(
                'company' => $address->company,
                'name' => $address->firstname,
                'street' => $address->address1 . ' ' . $address->address2,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'country' => $address->address2,
                'email' => $address->other,
                'phone' => $address->phone
            );
        }

        $id_lang = version_compare(_PS_VERSION_, '1.5', '>=') ? $this->context->language->id : 1;

        $id_country = Country::getByIso('PL');
        $country = new Country($id_country, $id_lang);

        $sender_address = new DpdPolandSenderAddress((int)$id_sender_address);

        return array(
            'company' => $sender_address->company,
            'name' => $sender_address->name,
            'street' => $sender_address->address,
            'postcode' => self::convertPostcode($sender_address->postcode),
            'city' => $sender_address->city,
            'country' => $country->name,
            'email' => $sender_address->email,
            'phone' => $sender_address->phone
        );
    }

    /**
     * Returns formatted recipient address
     *
     * @param int $id_address Recipient address ID
     * @return array Formatted address
     */
    private function getRecipientAddress($id_address)
    {
        $address = new Address((int)$id_address);
        $country = new Country((int)$address->id_country, $this->context->language->id);
        $customer = new Customer((int)$address->id_customer);

        return array(
            'company' => $address->company,
            'firstname' => $address->firstname,
            'lastname' => $address->lastname,
            'street' => $address->address1 . ' ' . $address->address2,
            'postcode' => $address->postcode,
            'city' => $address->city,
            'country' => $country->name,
            'email' => isset($address->other) && !empty(trim($address->other)) ? $address->other : $customer->email,
            'phone' => isset($address->phone) && !empty(trim($address->phone)) && str_replace(' ', '', $address->phone) ? $address->phone : $address->phone_mobile
        );
    }

    /**
     * Returns recipient address in HTML format
     *
     * @param int $id_address Address ID
     * @return string Address in HTML format
     */
    public function getFormattedAddressHTML($id_address)
    {
        $this->context->smarty->assign('address', $this->getRecipientAddress($id_address));

        if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
            $template = 'address_input_17';
        else
            $template = version_compare(_PS_VERSION_, '1.6', '>=') ? 'address_input_16' : 'address_input';

        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/' . $template . '.tpl');
    }

    /**
     * Returns sender address in HTML format
     *
     * @param int $id_address Address ID
     * @return string Address in HTML format
     */
    public function getFormattedSenderAddressHTML($id_address)
    {
        $this->context->smarty->assign('address', $this->getSenderAddress($id_address));

        if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
            $template = 'address_17';
        else
            $template = version_compare(_PS_VERSION_, '1.6', '>=') ? 'address_16' : 'address';

        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/' . $template . '.tpl');
    }

    /**
     * Checks if there are any COD payment methods installed in PrestaShop
     *
     * @return bool COD method is available
     */
    public static function CODMethodIsAvailable()
    {
        return (bool)count(DpdPoland::getPaymentModules());
    }

    /**
     * Searches for products information according to query
     *
     * @param string $query Keyword for products search
     * @return array Products information
     */
    public function searchProducts($query)
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $sql = '
				SELECT p.`id_product`, pl.`name`, p.`weight`
				FROM `' . _DB_PREFIX_ . 'category_product` cp
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = cp.`id_product`)
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = "' .
                (int)$this->context->language->id . '")
				WHERE pl.`name` LIKE \'%' . pSQL($query) . '%\'
					OR p.`ean13` LIKE \'%' . pSQL($query) . '%\'
					OR p.`upc` LIKE \'%' . pSQL($query) . '%\'
					OR p.`reference` LIKE \'%' . pSQL($query) . '%\'
					OR p.`supplier_reference` LIKE \'%' . pSQL($query) . '%\'
				GROUP BY `id_product`
				ORDER BY pl.`name` ASC
			';
        } else {
            $sql = new DbQuery();
            $sql->select('p.`id_product`, pl.`name`, p.`weight`');
            $sql->from('category_product', 'cp');
            $sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
            $sql->join(Shop::addSqlAssociation('product', 'p'));
            $sql->leftJoin('product_lang', 'pl', '
				p.`id_product` = pl.`id_product`
				AND pl.`id_lang` = ' . (int)$this->context->language->id . Shop::addSqlRestrictionOnLang('pl')
            );

            $where = 'pl.`name` LIKE \'%' . pSQL($query) . '%\'
			OR p.`ean13` LIKE \'%' . pSQL($query) . '%\'
			OR p.`upc` LIKE \'%' . pSQL($query) . '%\'
			OR p.`reference` LIKE \'%' . pSQL($query) . '%\'
			OR p.`supplier_reference` LIKE \'%' . pSQL($query) . '%\'
			OR  p.`id_product` IN (SELECT id_product FROM ' . _DB_PREFIX_ . 'product_supplier sp WHERE `product_supplier_reference` LIKE \'%' .
                pSQL($query) . '%\')';
            $sql->groupBy('`id_product`');
            $sql->orderBy('pl.`name` ASC');

            if (Combination::isFeatureActive()) {
                $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product` = p.`id_product`');
                $sql->join(Shop::addSqlAssociation('product_attribute', 'pa', false));
                $where .= ' OR pa.`reference` LIKE \'%' . pSQL($query) . '%\'';
            }
            $sql->where($where);
        }

        $result = Db::getInstance()->executeS($sql);

        if (!$result)
            return array('found' => false, 'notfound' => $this->l('No product has been found.'));

        foreach ($result as &$product) {
            $product['id_product_attribute'] = Product::getDefaultAttribute($product['id_product']);
            $product['weight_numeric'] = $product['weight'];
            $product['parcel_content'] = DpdPolandParcel::getParcelContent((array)$product);
            $product['weight'] = sprintf('%.3f', $product['weight']) . ' ' . _DPDPOLAND_DEFAULT_WEIGHT_UNIT_;
        }

        return array(
            'products' => $result,
            'found' => true
        );
    }

    /**
     * Saves DPD client number
     * Used via AJAX
     *
     * @return array Success / Error messages
     */
    public function addDPDClientNumber()
    {
        $number = Tools::getValue('client_number');
        $name = Tools::getValue('name');
        $id_shop = (int)Tools::getValue('id_shop', Context::getContext()->shop->id);
        $error = '';

        if (!$number)
            $error .= $this->l('DPD client number is required') . '<br />';
        elseif (!ctype_alnum($number))
            $error .= $this->l('DPD client number is not valid') . '<br />';

        if (!$name)
            $error .= $this->l('Client name is required') . '<br />';
        elseif (!Validate::isName($name))
            $error .= $this->l('Client name is not valid') . '<br />';

        if (empty($error)) {
            require_once(_DPDPOLAND_CLASSES_DIR_ . 'PayerNumber.php');

            if (DpdPolandPayerNumber::payerNumberExists($number, $id_shop))
                $error .= $this->l('DPD client number already exists') . '<br />';
            else {
                $payer_number_obj = new DpdPolandPayerNumber();
                $payer_number_obj->payer_number = $number;
                $payer_number_obj->name = $name;
                $payer_number_obj->id_shop = $id_shop;
                if (!$payer_number_obj->save())
                    $error .= $this->l('DPD client number / name could not be saved') . '<br />';
            }
        }

        $success = $this->l('DPD client number / name saved successfully');

        if ($error != '')
            DpdPolandLog::addError($error);

        return array(
            'error' => $error,
            'message' => $success
        );
    }

    /**
     * Deletes DPD client number
     * Used via AJAX
     *
     * @return array Success / Error messages
     */
    public function deleteDPDClientNumber()
    {
        $id_number = Tools::getValue('client_number');
        $error = '';

        $configuration_obj = new DpdPolandConfiguration();

        $payer_number_obj = new DpdPolandPayerNumber((int)$id_number);
        $current_number = $payer_number_obj->payer_number;
        if (!$payer_number_obj->delete())
            $error .= $this->l('Could not delete DPD client number / name');

        if ($current_number == $configuration_obj->client_number)
            if (!DpdPolandConfiguration::deleteByName(DpdPolandConfiguration::CLIENT_NUMBER) ||
                !DpdPolandConfiguration::deleteByName(DpdPolandConfiguration::CLIENT_NAME))
                $error .= $this->l('Could not delete default client number setting');

        $success = $this->l('DPD client number / name deleted successfully');

        if ($error != '')
            DpdPolandLog::addError($error);

        return array(
            'error' => $error,
            'message' => $success
        );
    }

    /**
     * Returns information about payment modules
     * which are installed into PrestaShop
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource Payment modules
     */
    public static function getPaymentModules()
    {
        if (version_compare(_PS_VERSION_, '1.5', '<'))
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT DISTINCT h.`id_hook`, m.`name`, hm.`position`
				FROM `' . _DB_PREFIX_ . 'module_country` mc
				LEFT JOIN `' . _DB_PREFIX_ . 'module` m ON m.`id_module` = mc.`id_module`
				INNER JOIN `' . _DB_PREFIX_ . 'module_group` mg ON (m.`id_module` = mg.`id_module`)
				LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_module` = m.`id_module`
				LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON hm.`id_hook` = h.`id_hook`
				WHERE h.`name` = \'payment\'
				AND m.`active` = 1
				ORDER BY hm.`position`, m.`name` DESC
			');
        return Module::getPaymentModules();
    }

    /**
     * Returns payer numbers in HTML format
     *
     * @return string Payer numbers in HTML format
     */
    public function getPayerNumbersTableHTML()
    {
        $configuration_obj = new DpdPolandConfiguration();

        $this->context->smarty->assign(array(
            'settings' => $configuration_obj,
            'payer_numbers' => DpdPolandPayerNumber::getPayerNumbers()
        ));

        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/payer_numbers_table.tpl');
    }

    /**
     * Calculates how much time it is left for arrange pickup
     *
     * @return bool|float|int Calculated time
     */
    public function calculateTimeLeft()
    {
        $current_timeframe = Tools::getValue('timeframe');
        $current_date = Tools::getValue('date');

        if (!$current_timeframe)
            return false;

        $end_time = explode('-', $current_timeframe);
        if (!isset($end_time[1]))
            return 0;

        $end_time_in_seconds = strtotime($end_time[1]);
        $poland_time_obj = new DateTime(null, new DateTimeZone('Europe/Warsaw'));
        $poland_time_in_seconds = strtotime($poland_time_obj->format('H:i:s'));
        $days_left = strtotime($current_date) - strtotime(date('Y-m-d'));
        $time_left = round(($end_time_in_seconds + $days_left - $poland_time_in_seconds) / 60);

        if ($time_left < 0)
            $time_left = 0;

        return $time_left;
    }

    /**
     * Collects information about time frames when carrier is available for arrange pickup
     *
     * @return string Timeframes in HTML format
     */
    public function getTimeFrames()
    {
        require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'arrange_pickup.controller.php');

        $current_date = Tools::getValue('date');

        $is_date_valid = true;

        if (!Validate::isDate($current_date)) {
            DpdPolandPickup::$errors = array($this->l('Wrong date format'));
            $is_date_valid = false;
        } elseif (strtotime($current_date) < strtotime(date('Y-m-d'))) {
            DpdPolandPickup::$errors = array($this->l('Date can not be earlier than') . ' ' . date('Y-m-d'));
            $is_date_valid = false;
        } elseif (DpdPolandArrangePickUpController::isWeekend($current_date)) {
            DpdPolandPickup::$errors = array($this->l('Weekends can not be chosen'));
            $is_date_valid = false;
        }

        if (!$is_date_valid) {
            $this->context->smarty->assign(array(
                'settings' => new DpdPolandConfiguration,
                'timeFrames' => false
            ));

            return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/timeframes.tpl');
        }

        $pickup = new DpdPolandPickup;
        $is_today = (bool)(date('Ymd') == date('Ymd', strtotime($current_date)));

        $pickup_timeframes = $pickup->getCourierTimeframes();

        $system_timezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Warsaw');

        $poland_time_in_seconds = strtotime(date('H:i:s'));

        DpdPolandArrangePickUpController::validateTimeframes($pickup_timeframes, $poland_time_in_seconds, $is_today);
        if (empty($pickup_timeframes)) {
            DpdPolandPickup::$errors = array($this->l('No timeframes'));
            $pickup_timeframes = false;
        }

        $this->context->smarty->assign(array(
            'settings' => new DpdPolandConfiguration,
            'timeFrames' => $pickup_timeframes
        ));

        $extra_timeframe = DpdPolandArrangePickUpController::createExtraTimeframe($pickup_timeframes);
        if ($extra_timeframe !== false)
            $this->context->smarty->assign('extra_timeframe', $extra_timeframe);

        date_default_timezone_set($system_timezone);

        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/timeframes.tpl');
    }

    /**
     * Converts PrestaShop weight units into DPD weight units
     *
     * @param float $weight Weight
     * @return float Converted weight
     */
    public static function convertWeight($weight)
    {
        if (!$conversation_rate = DpdPoland::getConfig(DpdPolandConfiguration::WEIGHT_CONVERSATION_RATE))
            $conversation_rate = 1;

        return (float)$weight * (float)$conversation_rate;
    }

    /**
     * Converts PrestaShop dimension units into DPD weight units
     *
     * @param float $value Dimension
     * @return string Converted dimension
     */
    public static function convertDimension($value)
    {
        if (!$conversation_rate = DpdPoland::getConfig(DpdPolandConfiguration::DIMENSION_CONVERSATION_RATE))
            $conversation_rate = 1;

        return sprintf('%.6f', (float)$value * (float)$conversation_rate);
    }

    /**
     * Formats postcode into the one which is accepted by WebServices
     *
     * @param string $postcode Postcode
     * @return bool|string Converted postcode
     */
    public static function convertPostcode($postcode)
    {
        if (!$postcode)
            return null;
        return Tools::strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '', $postcode));
    }

    /**
     * Module block in order management page
     *
     * @param $params Hook parameters
     * @return string Module content in HTML format
     */
    public function hookAdminOrder($params)
    {
        if (!$this->soapClientExists())
            return '';

        if (!DpdPolandConfiguration::checkRequiredConfiguration()) {
            $settings_page_link = version_compare(_PS_VERSION_, '1.5', '<') ? $this->getModuleSettingsPageLink14() :
                $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&menu=configuration';

            $this->context->smarty->assign(array(
                'displayBlock' => false,
                'moduleSettingsLink' => $settings_page_link
            ));
        } else {
            $this->displayFlashMessagesIfIsset(); // PDF error might be set as flash error

            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                $this->addJS(_PS_JS_DIR_ . 'jquery/jquery.scrollTo-1.4.2-min.js');
                $this->addJS(_PS_JS_DIR_ . 'jquery/jquery-ui-1.8.10.custom.min.js');
                $this->addJS(_PS_JS_DIR_ . 'jquery/accordion/accordion.ui.js');
                $this->addJS(_PS_JS_DIR_ . 'jquery/jquery.autocomplete.js');
                $this->addJS(_DPDPOLAND_JS_URI_ . 'adminOrder.js');
                $this->addCSS(_DPDPOLAND_CSS_URI_ . 'adminOrder.css');
                $this->addCSS(_PS_CSS_DIR_ . 'jquery-ui-1.8.10.custom.css');
                $this->addCSS(_DPDPOLAND_CSS_URI_ . 'pudo-common.css');
            } else {
                $this->context->controller->addJqueryUI(array(
                    'ui.core',
                    'ui.widget',
                    'ui.accordion'
                ));

                $this->context->controller->addJqueryPlugin('scrollTo');

                $this->context->controller->addJS(_DPDPOLAND_JS_URI_ . 'adminOrder.js');
                $this->context->controller->addCSS(_DPDPOLAND_CSS_URI_ . 'adminOrder.css');
                $this->context->controller->addCSS(_DPDPOLAND_CSS_URI_ . 'pudo-common.css');
            }

            $order = new Order((int)$params['id_order']);
            $package = DpdPolandPackage::getInstanceByIdOrder((int)$order->id);
            $parcels = DpdPolandParcel::getParcels((int)$order->id, $package->id_package_ws);
            $products = DpdPolandParcelProduct::getShippedProducts((int)$order->id, DpdPolandParcelProduct::getProductDetailsByParcels($parcels));

            $settings = new DpdPolandConfiguration;
            $customer = new Customer((int)$order->id_customer);

            $selectedPayerNumber = ($package->payerNumber) ? $package->payerNumber : $settings->client_number;
            $selectedRecipientIdAddress = ($package->id_address_delivery) ? $package->id_address_delivery : $order->id_address_delivery;

            $id_currency_pl = Currency::getIdByIsoCode(_DPDPOLAND_CURRENCY_ISO_, (int)$this->context->shop->id);
            $currency_to = new Currency((int)$id_currency_pl);
            $currency_from = new Currency($order->id_currency);

            $id_method = $this->getMethodIdByCarrierId((int)$order->id_carrier);
            $isPudoWithCod = false;
            if ($id_method) // if order shipping method is one of DPD shipping methods
            {
                $payment_method_compatible = false;

                $is_cod_module = Configuration::get(DpdPolandConfiguration::COD_MODULE_PREFIX . $order->module, null, $order->id_shop_group, $order->id_shop);

                if ($id_method == _DPDPOLAND_STANDARD_COD_ID_ && $is_cod_module ||
                    $id_method == _DPDPOLAND_STANDARD_ID_ && !$is_cod_module ||
                    $id_method == _DPDPOLAND_CLASSIC_ID_ && !$is_cod_module ||
                    $id_method == _DPDPOLAND_PUDO_ID_ && !$is_cod_module ||
                    $id_method == _DPDPOLAND_PUDO_COD_ID_ && $is_cod_module)
                    $payment_method_compatible = true;

                if ($id_method == _DPDPOLAND_PUDO_ID_ && $is_cod_module)
                    $isPudoWithCod = true;
            } else
                $payment_method_compatible = true;

            if (!$payment_method_compatible || $isPudoWithCod) {
                $error_message = '';
                if ($isPudoWithCod) {
                    $error_message .= $this->l('Selected payment method is COD.') . '<br />';
                }
                if (!$payment_method_compatible) {
                    $error_message .= $this->l('Your payment method and Shipping method is not compatible.') . '<br />';
                    $error_message .= ' ' . $this->l('If delivery address is not Poland, then COD payment method is not supported.') . '<br />';
                    $error_message .= ' ' . $this->l('If delivery address is Poland and payment method is COD please use shipping method DPD Domestic + COD.') .
                        '<br />';
                    $error_message .= ' ' . $this->l('If delivery address is Poland and no COD payment is used please select DPD Domestic shipping method.');
                }

                $this->context->smarty->assign('compatibility_warning_message', $error_message);

                DpdPolandLog::addLog($error_message);
            }

            if (!DpdPoland::validateAddressForPackageSession((int)$selectedRecipientIdAddress, (int)$id_method)) {
                $error_message = $this->l('Your delivery address is not compatible with the selected shipping method.') . ' ' .
                    $this->l('DPD Poland Domestic is available only if delivery address is Poland.') . ' ' .
                    $this->l('DPD International shipping method is available only if delivery address is not Poland.');
                $this->context->smarty->assign('address_warning_message', $error_message);

                DpdPolandLog::addLog($error_message);
            }

            $cookie = new Cookie(_DPDPOLAND_COOKIE_);
            require_once(_DPDPOLAND_CONTROLLERS_DIR_ . 'service.php');
            $dpdpoland_id_pudo_carrier = DpdPolandService::getCarrierByReference(Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_ID, null, $order->id_shop_group, $order->id_shop));
            $dpdpoland_id_pudo_cod_carrier = DpdPolandService::getCarrierByReference(Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_COD_ID, null, $order->id_shop_group, $order->id_shop));
            $sender_addresses = DpdPolandSenderAddress::getAddresses();

            $order_price_in_default_currency = Tools::convertPrice($order->total_paid_tax_incl, $currency_from);
            $order_price_in_pln = Tools::convertPrice($order_price_in_default_currency, $currency_to);
            $order_price_in_pln = round($order_price_in_pln, 2);

            $pudo_code = $id_method == _DPDPOLAND_PUDO_ID_ || $id_method == _DPDPOLAND_PUDO_COD_ID_ || $package->id_package_ws != null ? $this->getPudoCode($order) : null;

            $this->context->smarty->assign(array(
                'sender_addresses' => $sender_addresses,
                'displayBlock' => true,
                'order' => $order,
                'id_order' => $order->id,
                'messages' => $this->html, // Flash messages
                'package' => $package,
                'loaded_object' => Validate::isLoadedObject($package),
                'selected_id_method' => self::getMethodIdByCarrierId($order->id_carrier),
                'settings' => $settings,
                'payerNumbers' => DpdPolandPayerNumber::getPayerNumbers(),
                'selectedPayerNumber' => $selectedPayerNumber,
                'products' => $products,
                'parcels' => $parcels,
                'recipientAddresses' => $this->getRecipientAddresses($customer, $selectedRecipientIdAddress),
                'selectedRecipientIdAddress' => $selectedRecipientIdAddress,
                'recipientAddress' => $this->getRecipientAddress($selectedRecipientIdAddress),
                'currency_from' => $currency_from,
                'currency_to' => $currency_to,
                'redirect_and_open' => $cookie->dpdpoland_package_id,
                'printout_format' => $cookie->dpdpoland_printout_format,
                'default_ref1' => $this->configurationService->getDefaultRef1($order, $products),
                'default_ref2' => $this->configurationService->getDefaultRef2($order, $products),
                'default_customer_data_1' => $this->configurationService->getDefaultCustomerData1($order, $products),
                'dpdpoland_id_pudo_carrier' => $dpdpoland_id_pudo_carrier,
                'dpdpoland_id_pudo_cod_carrier' => $dpdpoland_id_pudo_cod_carrier,
                'order_price_pl' => $order_price_in_pln,
                'parcel_content_source' => $settings->parcel_content_source,
                'is_cod_payment_method' => Configuration::get(DpdPolandConfiguration::COD_MODULE_PREFIX . $order->module, null, $order->id_shop_group, $order->id_shop),
                'pudo_code' => $pudo_code

            ));

            $this->setGlobalVariablesForAjax();
        }

        if (version_compare(_PS_VERSION_, '1.6', '<'))
            return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'hook/adminOrder.tpl');
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
            return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'hook/adminOrder_17.tpl');

        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'hook/adminOrder_16.tpl');
    }

    private function getRecipientAddresses($customer, $selectedRecipientIdAddress)
    {
        $customerAddresses = $customer->getAddresses($this->context->language->id);

        if (!$selectedRecipientIdAddress)
            return $customerAddresses;

        $selectedAddress = new Address((int)$selectedRecipientIdAddress);

        if (!$this->addressExistsInArray($selectedRecipientIdAddress, $customerAddresses)) {
            array_push($customerAddresses,
                array(
                    'id_address' => $selectedAddress->id,
                    'id_country' => $selectedAddress->id_country,
                    'id_state' => $selectedAddress->id_state,
                    'id_manufacturer' => $selectedAddress->id_manufacturer,
                    'id_supplier' => $selectedAddress->id_supplier,
                    'id_warehouse' => $selectedAddress->id_warehouse,
                    'alias' => $selectedAddress->alias,
                    'company' => $selectedAddress->company,
                    'firstname' => $selectedAddress->firstname,
                    'lastname' => $selectedAddress->lastname,
                    'address1' => $selectedAddress->address1,
                    'address2' => $selectedAddress->address2,
                    'postcode' => $selectedAddress->postcode,
                    'city' => $selectedAddress->city,
                    'phone' => $selectedAddress->phone,
                    'phone_mobile' => isset($selectedAddress->phone_mobile) && !empty(trim($selectedAddress->phone_mobile)) ? $selectedAddress->phone_mobile : $selectedAddress->phone,
                    'country' => $selectedAddress->country)
            );
        }

        return $customerAddresses;
    }

    private function addressExistsInArray($entry, $array)
    {
        foreach ($array as $compare) {
            if ($compare['id_address'] == $entry) {
                return true;
            }
        }
        return false;
    }

    /**
     * Hook used to filter out non COD payment methods if DPD COD carrier was selected
     *
     * @param $params
     * @return null|void
     */
    public function hookPaymentTop($params)
    {
        if (version_compare(_PS_VERSION_, '1.5', '<'))
            return $this->disablePaymentMethods();

        if (!Validate::isLoadedObject($this->context->cart) || !$this->context->cart->id_carrier)
            return null;

        $method_id = self::getMethodIdByCarrierId((int)$this->context->cart->id_carrier);

        $cache_id = 'exceptionsCache';
        $exceptionsCache = (Cache::isStored($cache_id)) ? Cache::retrieve($cache_id) : array(); // existing cache
        $controller = (DpdPoland::getConfig('PS_ORDER_PROCESS_TYPE') == 0) ? 'order' : 'orderopc';
        $id_hook = Hook::getIdByName('displayPayment'); // ID of hook we are going to manipulate

        if ($paymentModules = DpdPoland::getPaymentModules()) {
            foreach ($paymentModules as $module) {
                $is_cod_module = DpdPoland::getConfig(DpdPolandConfiguration::COD_MODULE_PREFIX . $module['name']);

                if ($method_id == _DPDPOLAND_STANDARD_COD_ID_ && !$is_cod_module ||
                    $method_id == _DPDPOLAND_STANDARD_ID_ && $is_cod_module ||
                    $method_id == _DPDPOLAND_CLASSIC_ID_ && $is_cod_module ||
                    $method_id == _DPDPOLAND_PUDO_ID_ && $is_cod_module ||
                    $method_id == _DPDPOLAND_PUDO_COD_ID_ && !$is_cod_module) {
                    $module_instance = Module::getInstanceByName($module['name']);

                    if (Validate::isLoadedObject($module_instance)) {
                        $key = (int)$id_hook . '-' . (int)$module_instance->id;
                        $exceptionsCache[$key][$this->context->shop->id][] = $controller;
                    }
                }
            }

            Cache::store($cache_id, $exceptionsCache);
        }

        return null;
    }

    /**
     * Filters out non COD payment methods if DPD COD carrier was selected on PS 1.4
     */
    private function disablePaymentMethods()
    {
        $method_id = self::getMethodIdByCarrierId((int)$this->context->cart->id_carrier);

        if ($paymentModules = DpdPoland::getPaymentModules()) {
            foreach ($paymentModules as $module) {
                $is_cod_module = DpdPoland::getConfig(DpdPolandConfiguration::COD_MODULE_PREFIX . $module['name']);

                if ($method_id == _DPDPOLAND_STANDARD_COD_ID_ && !$is_cod_module ||
                    $method_id == _DPDPOLAND_STANDARD_ID_ && $is_cod_module ||
                    $method_id == _DPDPOLAND_CLASSIC_ID_ && $is_cod_module ||
                    $method_id == _DPDPOLAND_PUDO_ID_ && $is_cod_module ||
                    $method_id == _DPDPOLAND_PUDO_COD_ID_ && !$is_cod_module) {
                    $module_instance = Module::getInstanceByName($module['name']);

                    if (Validate::isLoadedObject($module_instance))
                        $module_instance->currencies = array();
                }
            }
        }
    }

    /**
     * Saves carrier ID according to its reference on PS 1.4
     *
     * @param $params Hook parameters with carrier information
     */
    public function hookUpdateCarrier($params)
    {
        $id_reference = (int)DpdPolandCarrier::getReferenceByIdCarrier((int)$params['id_carrier']);
        $id_carrier = (int)$params['carrier']->id;

        $dpdpoland_carrier = new DpdPolandCarrier();
        $dpdpoland_carrier->id_carrier = (int)$id_carrier;
        $dpdpoland_carrier->id_reference = (int)$id_reference;
        $dpdpoland_carrier->save();
    }

    /**
     * Sets carrier price
     *
     * @param Cart $cart Cart object
     * @param float $shipping_cost Shipping cost
     * @return bool|mixed Carrier price
     */
    public function getOrderShippingCost($cart, $shipping_cost)
    {
        return $this->getOrderShippingCostExternal($cart);
    }

    /**
     * Sets carrier price
     *
     * @param Cart $cart Cart object
     * @return bool|mixed Calculated carrier price
     */
    public function getOrderShippingCostExternal($cart)
    {
        if (!$this->soapClientExists() || !$this->checkModuleAvailability())
            return false;

        $disabled_countries_ids = DpdPolandCountry::getDisabledCountriesIDs();

        $id_country = (int)Tools::getValue('id_country');

        if (!$id_country) {
            $country = Address::getCountryAndState((int)$cart->id_address_delivery);

            $id_country = is_array($country) ? $country['id_country'] : $country;

            if (!$id_country) {
                $id_country = Country::getByIso('PL');
            }
        }

        if (!$id_method = self::getMethodIdByCarrierId($this->id_carrier)) {
            self::$carriers[$this->id_carrier] = false;
            return false;
        }

        if (!$id_country || in_array($id_country, $disabled_countries_ids) && $id_method == _DPDPOLAND_CLASSIC_ID_)
            return false;

        if ($id_country)
            $zone = Country::getIdZone($id_country);
        else
            return false;

        if (!$this->id_carrier)
            return false;

        $is_poland_country = $this->isPolandCountry((int)$id_country);

        if ($is_poland_country && $id_method == _DPDPOLAND_CLASSIC_ID_ ||
            !$is_poland_country && $id_method == _DPDPOLAND_STANDARD_COD_ID_ ||
            !$is_poland_country && $id_method == _DPDPOLAND_STANDARD_ID_ ||
            !$is_poland_country && $id_method == _DPDPOLAND_PUDO_ID_ ||
            !$is_poland_country && $id_method == _DPDPOLAND_PUDO_COD_ID_
        ) {
            return false;
        }

        if (isset(self::$carriers[$this->id_carrier]))
            return self::$carriers[$this->id_carrier];

        $total_weight = self::convertWeight($cart->getTotalWeight());
        $products = $cart->getProducts();
        $additional_shipping_cost = $this->getAdditionalShippingCost($products);

        if (DpdPoland::getConfig(DpdPolandConfiguration::PRICE_CALCULATION_TYPE) == DpdPolandConfiguration::PRICE_CALCULATION_PRESTASHOP) {
            $carrier = new Carrier($this->id_carrier);

            $price = null;
            if ($carrier->shipping_method == Carrier::SHIPPING_METHOD_WEIGHT || $carrier->shipping_method == Carrier::SHIPPING_METHOD_DEFAULT) {
                $deliveryPrice = $carrier->getDeliveryPriceByWeight($total_weight, $zone);
                $price = $deliveryPrice + $additional_shipping_cost;
            } else if ($carrier->shipping_method == Carrier::SHIPPING_METHOD_PRICE) {
                $order_total = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                $price = $carrier->getDeliveryPriceByPrice($order_total, $zone) + $additional_shipping_cost;
            }

            if ($price === null && $carrier->shipping_method != Carrier::SHIPPING_METHOD_FREE)
                return false;

            $id_currency_pl = Currency::getIdByIsoCode(_DPDPOLAND_CURRENCY_ISO_, (int)$this->context->shop->id);
            $currency_from = new Currency((int)$id_currency_pl);
            $currency_to = $this->context->currency;

            self::$carriers[$this->id_carrier] = Tools::convertPriceFull($price, $currency_from, $currency_to);
            return self::$carriers[$this->id_carrier];
        }

        $price = DpdPolandCSV::getPrice($total_weight, $id_method, $cart);

        if ($price === false)
            return false;

        $id_currency_pl = Currency::getIdByIsoCode(_DPDPOLAND_CURRENCY_ISO_, (int)$this->context->shop->id);
        $currency_from = new Currency((int)$id_currency_pl);
        $currency_to = $this->context->currency;

        self::$carriers[$this->id_carrier] = Tools::convertPriceFull($price, $currency_from, $currency_to);
        return self::$carriers[$this->id_carrier];
    }

    /**
     * Checks if country ID belongs to PL country
     *
     * @param int $id_country Country ID
     * @return bool ID belongs to PL country
     */
    private function isPolandCountry($id_country)
    {
        if (!$id_country) {
            return false;
        }

        return $id_country == Country::getByIso(self::POLAND_ISO_CODE);
    }

    /**
     * Unserializes serialized value
     *
     * @param string $serialized Serialized value
     * @param bool $object
     * @return bool|mixed Unserialized value
     */
    public function unSerialize($serialized, $object = false)
    {
        if (method_exists('Tools', 'unSerialize'))
            return Tools::unSerialize($serialized, $object);

        if (is_string($serialized) && (strpos($serialized, 'O:') === false || !preg_match('/(^|;|{|})O:[0-9]+:"/', $serialized)) && !$object || $object)
            return @unserialize($serialized);

        return false;
    }


    /**
     * Prints multiple labels for selected orders
     *
     * @param string $printout_format Printout format (A4 or label)
     * @return array|null Error message
     */
    public function printMultipleLabels($printout_format = DpdPolandConfiguration::PRINTOUT_FORMAT_A4, $orders = null)
    {
        return $this->labelService->printMultipleLabels($printout_format, $orders);
    }


    /**
     * Prints labels for selected orders
     *
     * @param array $packages Selected packages
     * @param string $printout_format Printout format (A4, label)
     * @return array|void Error message
     */
    private function printLabelsFromOrdersList(array $packages, $printout_format)
    {
        $pdf_directory = $pdf_directory = _PS_MODULE_DIR_ . 'dpdpoland/pdf/';

        foreach ($packages as $id_package) {
            $package = new DpdPolandPackage((int)$id_package);

            if ($pdf_file_contents = $package->generateLabels('PDF', $printout_format)) {
                $fp = fopen($pdf_directory . 'label_' . (int)$id_package . '.pdf', 'a');

                if (!$fp) {
                    if (version_compare(_PS_VERSION_, '1.5', '<')) {
                        return array($this->l('Could not create PDF file. Please check module folder permissions'));
                    }

                    $this->context->controller->errors[] =
                        $this->l('Could not create PDF file. Please check module folder permissions');

                    return;
                }

                fwrite($fp, $pdf_file_contents);
                fclose($fp);
            }
        }

        include_once(_PS_MODULE_DIR_ . 'dpdpoland/libraries/PDFMerger/PDFMerger.php');

        $pdf = new PDFMerger;

        foreach ($packages as $id_package) {
            $label_pdf_path = $pdf_directory . 'label_' . (int)$id_package . '.pdf';
            $pdf->addPDF($label_pdf_path, 'all');
        }

        $pdf->merge('file', $pdf_directory . 'multiple_label.pdf');

        ob_end_clean();
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="labels_' . time() . '.pdf"');
        readfile($pdf_directory . 'multiple_label.pdf');

        foreach ($packages as $id_package) {
            if (file_exists($pdf_directory . 'label_' . $id_package . '.pdf') &&
                is_writable($pdf_directory . 'label_' . $id_package . '.pdf')
            ) {
                unlink($pdf_directory . 'label_' . $id_package . '.pdf');
            }
        }

        if (file_exists($pdf_directory . 'multiple_label.pdf') && is_writable($pdf_directory . 'multiple_label.pdf')) {
            unlink($pdf_directory . 'multiple_label.pdf');
        }

        $url = $this->context->link->getAdminLink('AdminOrders');
        Tools::redirectAdmin($url);
    }

    /**
     * Prints label in PDF format
     *
     * @param string $printout_format Printout format (A4, label)
     * @return array|null|string|void Error message
     */
    public function printSingleLabel($printout_format = DpdPolandConfiguration::PRINTOUT_FORMAT_A4)
    {
        $id_order = (int)Tools::getValue('id_order');
        $order = new Order((int)$id_order);

        if (!Validate::isLoadedObject($order)) {

            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                return $this->l('Order does not exist');
            }

            $this->context->controller->errors[] = $this->l('Order does not exist');

            return null;
        }

        $package = DpdPolandPackage::getInstanceByIdOrder((int)$id_order);

        if (!$package->id_package_ws) {

            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                DpdPolandLog::addError('Label is not saved for #%d order');
                return sprintf($this->l('Label is not saved for #%d order'), (int)$id_order);
            }

            $this->context->controller->errors[] =
                sprintf($this->l('Label is not saved for #%d order'), (int)$id_order);

            DpdPolandLog::addError(json_encode($this->context->controller->errors));
            return null;
        }

        return $this->printLabelsFromOrdersList(array($package->id_package_ws), $printout_format);
    }

    /**
     * Loads CSS / JS files in PrestaShop BackOffice
     */
    public function hookDisplayBackofficeHeader()
    {
        if (Tools::getValue('controller') == 'AdminOrders') {
            $this->context->controller->addJS(_DPDPOLAND_JS_URI_ . 'adminorders.js');

            if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
                $this->context->controller->addJS(_DPDPOLAND_JS_URI_ . 'adminOrder_1.7.js');
        }
    }

    public function hookActionOrderGridDefinitionModifier($params)
    {
        $params['definition']->getBulkActions()->add(
            (new SubmitBulkAction('dpdpoland_generate_shipping_list_with_label'))
                ->setName($this->l('DPD Polska - generate shipping and labels'))
                ->setOptions([
                    'submit_route' => 'dpdpoland_generate_shipping_list_with_label',
                    'submit_method' => 'POST'
                ])
        );

        $params['definition']->getBulkActions()->add(
            (new SubmitBulkAction('dpdpoland_generate_shipping_list'))
                ->setName($this->l('DPD Polska - generate shipping'))
                ->setOptions([
                    'submit_route' => 'dpdpoland_generate_shipping_list',
                    'submit_method' => 'POST'
                ])
        );

        $params['definition']->getBulkActions()->add(
            (new SubmitBulkAction('dpdpoland_generate_shipping_label'))
                ->setName($this->l('DPD Polska - generate labels'))
                ->setOptions([
                    'submit_route' => 'dpdpoland_generate_shipping_label',
                    'submit_method' => 'POST'
                ])
        );
    }

    /**
     * Loads CSS / JS files in PrestaShop FrontOffice
     */
    public function hookHeader()
    {
    }

    /**
     * Generates module settings page link
     * Used on PS 1.4
     *
     * @return string Settings page link
     */
    private function getModuleSettingsPageLink14()
    {
        return 'index.php?tab=AdminModules&token=' . Tools::getAdminTokenLite('AdminModules') .
            '&configure=dpdpoland&menu=configuration';
    }

    /**
     * @param array $params Available keys: cart, order, customer, currency, orderStatus
     */
    public function hookActionValidateOrder($params)
    {
        /** @var Order $order */
        $order = $params['order'];

        $dpdpoland_id_pudo_carrier = Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_ID, null, $order->id_shop_group, $order->id_shop);
        $dpdpoland_id_pudo_cod_carrier = Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_COD_ID, null, $order->id_shop_group, $order->id_shop);

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $id_order_carrier = (int)DpdPolandCarrier::getReferenceByIdCarrier((int)$order->id_carrier);
        } else {
            $carrier = new Carrier($order->id_carrier);
            $id_order_carrier = $carrier->id_reference;
        }

        // If order carrier is not pudo service then do nothing
        if ($id_order_carrier != (int)$dpdpoland_id_pudo_carrier && $id_order_carrier != (int)$dpdpoland_id_pudo_cod_carrier) {
            return;
        }

        $pudoCode = $this->getPudoCode($order);

        if (!$pudoCode) {
            return;
        }

        $currentAddress = new Address($order->id_address_delivery);

        $address = $this->pudoService->getPudoAddress(
            $pudoCode,
            $order->id_customer,
            isset($currentAddress->phone_mobile) && !empty(trim($currentAddress->phone_mobile)) ?
                $currentAddress->phone_mobile :
                $currentAddress->phone
        );

        if (null == $address) {
            return;
        }

        $order->id_address_delivery = $address->id;
        $order->save();
    }

    /**
     * @param array $params
     */
    public function hookNewOrder(array $params)
    {
        /** @var Order $order */
        $order = $params['order'];

        $dpdpoland_id_pudo_carrier = Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_ID, null, $order->id_shop_group, $order->id_shop);
        $dpdpoland_id_pudo_cod_carrier = Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_COD_ID, null, $order->id_shop_group, $order->id_shop);

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $id_order_carrier = (int)DpdPolandCarrier::getReferenceByIdCarrier((int)$order->id_carrier);
        } else {
            $carrier = new Carrier($order->id_carrier);
            $id_order_carrier = $carrier->id_reference;
        }

        // If order carrier is not pudo service then do nothing
        if ($id_order_carrier != (int)$dpdpoland_id_pudo_carrier && $id_order_carrier != (int)$dpdpoland_id_pudo_cod_carrier) {
            return;
        }

        $pudoCode = $this->getPudoCode($order);

        if (!$pudoCode) {
            return;
        }

        $currentAddress = new Address($order->id_address_delivery);

        $address = $this->pudoService->getPudoAddress(
            $pudoCode,
            $order->id_customer,
            isset($currentAddress->phone_mobile) && !empty(trim($currentAddress->phone_mobile)) ? $currentAddress->phone_mobile : $currentAddress->phone
        );

        if (null == $address) {
            return;
        }

        $order->id_address_delivery = $address->id;
        $order->save();
    }

    /**
     * This method will save pudo code selected in FrontOffice and associate it with cart for later use
     *
     * @param string|null $pudo_code
     * @param int|null $id_cart
     *
     * @return bool
     */
    public function savePudoMapCodeFrontend()
    {
        $savePackages = new ShippingService;
        return $savePackages->savePudoMapCodeFrontend();
    }

    public static function getConfig($key)
    {
        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);
        return Configuration::get($key, null, $id_shop_group, $id_shop);
    }

    /**
     * @param Order $order
     * @return false|string
     */
    private function getPudoCode(Order $order)
    {
        $pudoCode = Db::getInstance()->getValue('
            SELECT `pudo_code`
            FROM `' . _DB_PREFIX_ . 'dpdpoland_pudo_cart`
            WHERE `id_cart` = ' . (int)$order->id_cart . '
        ');
        return $pudoCode;
    }

    /**
     * @param array|null $products
     * @return float
     */
    private function getAdditionalShippingCost(array $products)
    {
        $additional_shipping_cost = 0;
        foreach ($products as $product) {
            if (!$product['is_virtual']) {
                $additional_shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];
            }
        }
        return $additional_shipping_cost;
    }

    public function hookActionFrontControllerSetMedia()
    {
        $dpdpoland_id_pudo_carrier = Carrier::getCarrierByReference(DpdPoland::getConfig(DpdPolandConfiguration::CARRIER_PUDO_ID));
        $dpdpoland_id_pudo_cod_carrier = Carrier::getCarrierByReference(DpdPoland::getConfig(DpdPolandConfiguration::CARRIER_PUDO_COD_ID));

        Media::addJsDef([
            'dpdpoland_id_pudo_carrier' => $dpdpoland_id_pudo_carrier != null ? (int)$dpdpoland_id_pudo_carrier->id : null,
            'dpdpoland_id_pudo_cod_carrier' => $dpdpoland_id_pudo_cod_carrier != null ? (int)$dpdpoland_id_pudo_cod_carrier->id : null,
            'dpdpoland_ajax_uri' => _DPDPOLAND_AJAX_URI_,
            'dpdpoland_token' => sha1(_COOKIE_KEY_ . $this->name),
            'dpdpoland_session' => Tools::getToken(false),
            'dpdpoland_cart' => (int)$this->context->cart->id
        ]);

        $this->context->controller->registerJavascript(
            'dpdpoland-pudo-common-js',
            'modules/' . $this->name . '/js/pudo-common.js'
        );

        $this->context->controller->registerStylesheet(
            'dpdpoland-pudo-common-css',
            'modules/' . $this->name . '/css/pudo-common.css'
        );

        $custom_cart = DpdPoland::getConfig(DpdPolandConfiguration::CUSTOM_CART);
        if($custom_cart == DpdPolandConfiguration::SUPERCHECKOUT_CART){
            $this->context->controller->registerJavascript(
                'dpdpoland-pudo-supercheckout-js',
                'modules/' . $this->name . '/js/pudo-supercheckout.js'
            );

            $this->context->controller->registerStylesheet(
                'dpdpoland-pudo-supercheckout-css',
                'modules/' . $this->name . '/css/pudo-supercheckout.css'
            );
        }
        else{
            $this->context->controller->registerJavascript(
                'dpdpoland-pudo-default-js',
                'modules/' . $this->name . '/js/pudo-default.js'
            );

            $this->context->controller->registerStylesheet(
                'dpdpoland-pudo-default-css',
                'modules/' . $this->name . '/css/pudo-default.css'
            );
        }

    }

    public function hookDisplayCarrierExtraContent($params)
    {
        $id_method = $this->getMethodIdByCarrierId((int)$params['carrier']['id']);
        if ($id_method == _DPDPOLAND_PUDO_ID_ ) {

            return $this->display(
                __FILE__,
                'views/templates/hook/carrier-extra-content-pudo.tpl'
            );
        }

        if ($id_method == _DPDPOLAND_PUDO_COD_ID_ ) {
            return $this->display(
                __FILE__,
                'views/templates/hook/carrier-extra-content-pudo-cod.tpl'
            );
        }

        return '';
    }

}
