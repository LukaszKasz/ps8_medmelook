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

if (!defined('_PS_VERSION_'))
    exit;

/**
 * Class DpdPolandConfiguration Responsible for module settings management
 */
class DpdPolandConfiguration
{
    const WSDL_URL_LIVE = 'https://dpdservices.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?wsdl';
    const WSDL_URL_DEMO = 'https://dpdservicesdemo.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?wsdl';
    const LOGIN = 'DPDPOLAND_LOGIN';
    const PASSWORD = 'DPDPOLAND_PASSWORD';
    const CLIENT_NUMBER = 'DPDPOLAND_CLIENT_NUMBER';
    const CLIENT_NAME = 'DPDPOLAND_CLIENT_NAME';
    const NAME_SURNAME = 'DPDPOLAND_NAME_SURNAME';
    const ADDRESS = 'DPDPOLAND_ADDRESS';
    const POSTCODE = 'DPDPOLAND_POSTCODE';
    const CITY = 'DPDPOLAND_CITY';
    const EMAIL = 'DPDPOLAND_EMAIL';
    const PHONE = 'DPDPOLAND_PHONE';

    const CARRIER_STANDARD = 'DPDPOLAND_CARRIER_STANDARD';
    const CARRIER_STANDARD_COD = 'DPDPOLAND_CARRIER_STANDARD_COD';
    const CARRIER_CLASSIC = 'DPDPOLAND_CARRIER_CLASSIC';
    const CARRIER_PUDO = 'DPDPOLAND_CARRIER_PUDO';
    const CARRIER_PUDO_COD = 'DPDPOLAND_CARRIER_PUDO_COD';

    const PRICE_CALCULATION_TYPE = 'DPDPOLAND_PRICE_CALCULATION';

    const WEIGHT_CONVERSATION_RATE = 'DPDPOLAND_WEIGHT_RATE';
    const DIMENSION_CONVERSATION_RATE = 'DPDPOLAND_DIMENSION_RATE';
    const WS_URL = 'DPDPOLAND_WS_URL';

    const CARRIER_STANDARD_ID = 'DPDPOLAND_STANDARD_ID';
    const CARRIER_STANDARD_COD_ID = 'DPDPOLAND_STANDARD_COD_ID';
    const CARRIER_CLASSIC_ID = 'DPDPOLAND_CLASSIC_ID';
    const CARRIER_PUDO_ID = 'DPDPOLAND_PUDO_ID';
    const CARRIER_PUDO_COD_ID = 'DPDPOLAND_PUDO_COD_ID';

    const CUSTOMER_COMPANY = 'DPDPOLAND_CUSTOMER_COMPANY';
    const CUSTOMER_NAME = 'DPDPOLAND_CUSTOMER_NAME';
    const CUSTOMER_PHONE = 'DPDPOLAND_CUSTOMER_PHONE';
    const CUSTOMER_FID = 'DPDPOLAND_CUSTOMER_FID';

    const FILE_NAME = 'Configuration';
    const MEASUREMENT_ROUND_VALUE = 6;
    const COD_MODULE_PREFIX = 'DPDPOLAND_COD_';
    const PRICE_CALCULATION_CSV = 'csv_calculation';
    const PRICE_CALCULATION_PRESTASHOP = 'prestashop_calculation';

    const DEFAULT_WEIGHT = 'DEFAULT_WEIGHT';

    const DEFAULT_PRINTER_TYPE = 'DEFAULT_PRINTER_TYPE';
    const PRINTOUT_FORMAT_A4 = 'A4';
    const PRINTOUT_FORMAT_LABEL = 'LBL_PRINTER';

    const ADDITIONAL_REF1 = 'DPDPOLAND_ADDITIONAL_REF1';
    const ADDITIONAL_REF2 = 'DPDPOLAND_ADDITIONAL_REF2';
    const ADDITIONAL_CUSTOMER_DATA_1 = 'DPDPOLAND_ADDITIONAL_DATA_1';

    const ADDITIONAL_TYPE_NONE = 'DPDPOLAND_NONE';
    const ADDITIONAL_TYPE_DYNAMIC = 'DPDPOLAND_DYNAMIC';
    const ADDITIONAL_TYPE_STATIC = 'DPDPOLAND_STATIC';

    const REF1_DYNAMIC = 'DPDPOLAND_REF1_DYNAMIC';
    const REF1_STATIC = 'DPDPOLAND_REF1_STATIC';
    const REF2_DYNAMIC = 'DPDPOLAND_REF2_DYNAMIC';
    const REF2_STATIC = 'DPDPOLAND_REF2_STATIC';
    const CUSTOMER_DATA_DYNAMIC = 'DPDPOLAND_CUSTOMER_DATA_DYNAMIC';
    const CUSTOMER_DATA_STATIC = 'DPDPOLAND_CUSTOMER_DATA_STATIC';

    const DYNAMIC_ORDER_ID = 'DPDPOLAND_DYNAMIC_ORDER_ID';
    const DYNAMIC_ORDER_REFERENCE = 'DPDPOLAND_DYNAMIC_ORDER_REF';
    const DYNAMIC_INVOICE_ID = 'DPDPOLAND_DYNAMIC_INVOICE_ID';
    const DYNAMIC_SHIPPING_ADDRESS = 'DPDPOLAND_DYNAMIC_SHIPPING';
    const DYNAMIC_PRODUCT_NAME = 'DPDPOLAND_DYNAMIC_PRODUCT_NAME';

    const DECLARED_VALUE = 'DPDPOLAND_DECLARED_VALUE';
    const CUD = 'DPDPOLAND_CUD';
    const ROD = 'DPDPOLAND_ROD';
    const DPDE = 'DPDPOLAND_DPDE';
    const DPDND = 'DPDPOLAND_DPDND';
    const DPDSATURDAY = 'DPDPOLAND_DPDSATURDAY';
    const DPDFOOD = 'DPDPOLAND_DPDFOOD';
    const DPDLQ = 'DPDPOLAND_DPDLQ';
    const DPDTODAY = 'DPDPOLAND_DPDTODAY';
    const DUTY = 'DPDPOLAND_DUTY';
    const LOG_MODE = 'DPDPOLAND_LOG_MODE';
    const DISABLE_SEND_SHIPPING_MAIL = 'DPDPOLAND_DISABLE_SEND_SHIPPING_MAIL';
    const CUSTOM_CART = 'DPDPOLAND_CUSTOM_CART';
    const DEFAULT_CART = 'DEFAULT_CART';
    const SUPERCHECKOUT_CART = 'SUPERCHECKOUT_CART';

    const PARCEL_CONTENT_SOURCE = 'PARCEL_CONTENT_SOURCE';
    const PARCEL_CONTENT_SOURCE_SKU = 'PARCEL_CONTENT_SOURCE_SKU';
    const PARCEL_CONTENT_SOURCE_PRODUCT_ID = 'PARCEL_CONTENT_SOURCE_PRODUCT_ID';
    const PARCEL_CONTENT_SOURCE_PRODUCT_NAME = 'PARCEL_CONTENT_SOURCE_PRODUCT_NAME';

    public $login = '';
    public $password = '';
    public $client_number = '';
    public $client_name = '';
    public $customer_name = '';
    public $customer_company = '';
    public $customer_phone = '';
    public $customer_fid = '';
    public $price_calculation_type = self::PRICE_CALCULATION_PRESTASHOP;
    public $carrier_standard = 0;
    public $carrier_standard_cod = 0;
    public $carrier_classic = 0;
    public $carrier_pudo = 0;
    public $carrier_pudo_cod = 0;
    public $weight_conversation_rate = 1;
    public $dimension_conversation_rate = 1;
    public $ws_url = '';

    public $ref1 = self::ADDITIONAL_TYPE_DYNAMIC;
    public $ref2 = self::ADDITIONAL_TYPE_DYNAMIC;
    public $customer_data_1 = self::ADDITIONAL_TYPE_NONE;

    public $ref1_dynamic = self::DYNAMIC_ORDER_ID;
    public $ref2_dynamic = self::DYNAMIC_INVOICE_ID;
    public $customer_data_dynamic = self::DYNAMIC_SHIPPING_ADDRESS;

    public $ref1_static = '';
    public $ref2_static = '';
    public $customer_data_static = '';

    public $declared_value = 0;
    public $cud = 0;
    public $rod = 0;
    public $dpde = 0;
    public $dpdnd = 0;
    public $dpdsaturday = 0;
    public $dpdfood = 0;
    public $dpdlq = 0;
    public $dpdtoday = 0;
    public $duty = 0;
    public $log_mode = '';
    public $disable_send_shipping_mail = '';
    public $custom_cart = self::DEFAULT_CART;

    public $default_weight = 1;
    public $default_printer_type = self::PRINTOUT_FORMAT_A4;
    public $parcel_content_source = self::PARCEL_CONTENT_SOURCE_SKU;

    /**
     * DpdPolandConfiguration class constructor
     */
    public function __construct()
    {
        $this->getSettings();
    }

    /**
     * Saves module settings into database
     *
     * @return bool Module settings saved successfully
     */
    public static function saveConfiguration()
    {
        $success = true;
        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);

        $success &= Configuration::updateValue(self::LOGIN, Tools::getValue(self::LOGIN));

        $password = Tools::getValue(self::PASSWORD);
        if (isset($password) && $password != null && $password != "") {
            $phpEncryption = null;
            if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
                $phpEncryption = new Rijndael('_RIJNDAEL_KEY_', '_RIJNDAEL_IV_');
            else
                $phpEncryption = new PhpEncryption(_NEW_COOKIE_KEY_);

            $encryptedPassword = $phpEncryption->encrypt($password);
            $success &= Configuration::updateValue(self::PASSWORD, $encryptedPassword);
        }

        $success &= Configuration::updateValue(self::CLIENT_NUMBER, Tools::getValue(self::CLIENT_NUMBER));

        $client_name = DB::getInstance()->getValue('
			SELECT `name`
			FROM `' . _DB_PREFIX_ . _DPDPOLAND_PAYER_NUMBERS_DB_ . '`
			WHERE `payer_number` = "' . pSQL(Configuration::get(self::CLIENT_NUMBER, null, $id_shop_group, $id_shop)) . '"
				AND `id_shop` = "' . (int)Context::getContext()->shop->id . '"
		');

        $success &= Configuration::updateValue(self::CLIENT_NAME, $client_name);
        $success &= Configuration::updateValue(self::NAME_SURNAME, Tools::getValue(self::NAME_SURNAME));
        $success &= Configuration::updateValue(self::ADDRESS, Tools::getValue(self::ADDRESS));
        $success &= Configuration::updateValue(self::POSTCODE, Tools::getValue(self::POSTCODE));
        $success &= Configuration::updateValue(self::CITY, Tools::getValue(self::CITY));
        $success &= Configuration::updateValue(self::EMAIL, Tools::getValue(self::EMAIL));
        $success &= Configuration::updateValue(self::PHONE, Tools::getValue(self::PHONE));
        $success &= Configuration::updateValue(self::CUSTOMER_COMPANY, Tools::getValue(self::CUSTOMER_COMPANY));
        $success &= Configuration::updateValue(self::CUSTOMER_NAME, Tools::getValue(self::CUSTOMER_NAME));
        $success &= Configuration::updateValue(self::CUSTOMER_PHONE, Tools::getValue(self::CUSTOMER_PHONE));
        $success &= Configuration::updateValue(self::CUSTOMER_FID, Tools::getValue(self::CUSTOMER_FID));
        $success &= Configuration::updateValue(self::PRICE_CALCULATION_TYPE, Tools::getValue(self::PRICE_CALCULATION_TYPE));
        $success &= Configuration::updateValue(self::CARRIER_STANDARD, (int)Tools::isSubmit(self::CARRIER_STANDARD));
        $success &= Configuration::updateValue(self::CARRIER_STANDARD_COD, (int)Tools::isSubmit(self::CARRIER_STANDARD_COD));
        $success &= Configuration::updateValue(self::CARRIER_CLASSIC, (int)Tools::isSubmit(self::CARRIER_CLASSIC));
        $success &= Configuration::updateValue(self::CARRIER_PUDO, (int)Tools::isSubmit(self::CARRIER_PUDO));
        $success &= Configuration::updateValue(self::CARRIER_PUDO_COD, (int)Tools::isSubmit(self::CARRIER_PUDO_COD));
        $success &= Configuration::updateValue(self::WEIGHT_CONVERSATION_RATE, Tools::getValue(self::WEIGHT_CONVERSATION_RATE));
        $success &= Configuration::updateValue(self::DIMENSION_CONVERSATION_RATE, Tools::getValue(self::DIMENSION_CONVERSATION_RATE));
        $success &= Configuration::updateValue(self::WS_URL, Tools::getValue(self::WS_URL));

        $success &= Configuration::updateValue(self::ADDITIONAL_REF1, Tools::getValue(self::ADDITIONAL_REF1));
        $success &= Configuration::updateValue(self::ADDITIONAL_REF2, Tools::getValue(self::ADDITIONAL_REF2));
        $success &= Configuration::updateValue(self::ADDITIONAL_CUSTOMER_DATA_1, Tools::getValue(self::ADDITIONAL_CUSTOMER_DATA_1));

        $success &= Configuration::updateValue(self::REF1_DYNAMIC, Tools::getValue(self::REF1_DYNAMIC));
        $success &= Configuration::updateValue(self::REF2_DYNAMIC, Tools::getValue(self::REF2_DYNAMIC));
        $success &= Configuration::updateValue(self::CUSTOMER_DATA_DYNAMIC, Tools::getValue(self::CUSTOMER_DATA_DYNAMIC));

        $success &= Configuration::updateValue(self::REF1_STATIC, Tools::getValue(self::REF1_STATIC));
        $success &= Configuration::updateValue(self::REF2_STATIC, Tools::getValue(self::REF2_STATIC));
        $success &= Configuration::updateValue(self::CUSTOMER_DATA_STATIC, Tools::getValue(self::CUSTOMER_DATA_STATIC));
        $success &= Configuration::updateValue(self::DECLARED_VALUE, Tools::getValue(self::DECLARED_VALUE));
        $success &= Configuration::updateValue(self::CUD, Tools::getValue(self::CUD));
        $success &= Configuration::updateValue(self::ROD, Tools::getValue(self::ROD));
        $success &= Configuration::updateValue(self::DPDE, Tools::getValue(self::DPDE));
        $success &= Configuration::updateValue(self::DPDND, Tools::getValue(self::DPDND));
        $success &= Configuration::updateValue(self::DPDFOOD, Tools::getValue(self::DPDFOOD));
        $success &= Configuration::updateValue(self::DPDLQ, Tools::getValue(self::DPDLQ));
        $success &= Configuration::updateValue(self::DPDTODAY, Tools::getValue(self::DPDTODAY));
        $success &= Configuration::updateValue(self::DPDSATURDAY, Tools::getValue(self::DPDSATURDAY));
        $success &= Configuration::updateValue(self::DUTY, Tools::getValue(self::DUTY));
        $success &= Configuration::updateValue(self::LOG_MODE, Tools::getValue(self::LOG_MODE));
        $success &= Configuration::updateValue(self::DEFAULT_PRINTER_TYPE, Tools::getValue(self::DEFAULT_PRINTER_TYPE));
        $success &= Configuration::updateValue(self::DEFAULT_WEIGHT, Tools::getValue(self::DEFAULT_WEIGHT));
        $success &= Configuration::updateValue(self::DISABLE_SEND_SHIPPING_MAIL, Tools::getValue(self::DISABLE_SEND_SHIPPING_MAIL));
        $success &= Configuration::updateValue(self::PARCEL_CONTENT_SOURCE, Tools::getValue(self::PARCEL_CONTENT_SOURCE));
        $success &= Configuration::updateValue(self::CUSTOM_CART, Tools::getValue(self::CUSTOM_CART));


        foreach (DpdPoland::getPaymentModules() as $payment_module)
            $success &= Configuration::updateValue(
                self::COD_MODULE_PREFIX . $payment_module['name'], (int)Tools::isSubmit(self::COD_MODULE_PREFIX . $payment_module['name'])
            );

        return $success;
    }

    /**
     * Collects settings from database
     * Assigns settings values for class variables
     */
    private function getSettings()
    {
        $this->login = $this->getSetting(self::LOGIN, $this->login);
        $password = $this->getSetting(self::PASSWORD, $this->password);
        if (isset($password)) {
            $phpEncryption = null;
            if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
                $phpEncryption = new Rijndael('_RIJNDAEL_KEY_', '_RIJNDAEL_IV_');
            else
                $phpEncryption = new PhpEncryption(_NEW_COOKIE_KEY_);
            $this->password = $phpEncryption->decrypt($password);
        }

        $this->client_number = $this->getSetting(self::CLIENT_NUMBER, $this->client_number);
        $this->client_name = $this->getSetting(self::CLIENT_NAME, $this->client_name);
        $this->customer_company = $this->getSetting(self::CUSTOMER_COMPANY, $this->customer_company);
        $this->customer_name = $this->getSetting(self::CUSTOMER_NAME, $this->customer_name);
        $this->customer_phone = $this->getSetting(self::CUSTOMER_PHONE, $this->customer_phone);
        $this->customer_fid = $this->getSetting(self::CUSTOMER_FID, $this->customer_fid);
        $this->price_calculation_type = $this->getSetting(self::PRICE_CALCULATION_TYPE, $this->price_calculation_type);
        $this->carrier_standard = $this->getSetting(self::CARRIER_STANDARD, $this->carrier_standard);
        $this->carrier_standard_cod = $this->getSetting(self::CARRIER_STANDARD_COD, $this->carrier_standard_cod);
        $this->carrier_classic = $this->getSetting(self::CARRIER_CLASSIC, $this->carrier_classic);
        $this->carrier_pudo = $this->getSetting(self::CARRIER_PUDO, $this->carrier_pudo);
        $this->carrier_pudo_cod = $this->getSetting(self::CARRIER_PUDO_COD, $this->carrier_pudo_cod);
        $this->weight_conversation_rate = $this->getSetting(self::WEIGHT_CONVERSATION_RATE, $this->weight_conversation_rate);
        $this->dimension_conversation_rate = $this->getSetting(self::DIMENSION_CONVERSATION_RATE, $this->dimension_conversation_rate);
        $this->ws_url = $this->getSetting(self::WS_URL, $this->ws_url);

        $this->ref1 = $this->getSetting(self::ADDITIONAL_REF1, $this->ref1);
        $this->ref2 = $this->getSetting(self::ADDITIONAL_REF2, $this->ref2);
        $this->customer_data_1 = $this->getSetting(self::ADDITIONAL_CUSTOMER_DATA_1, $this->customer_data_1);

        $this->ref1_dynamic = $this->getSetting(self::REF1_DYNAMIC, $this->ref1_dynamic);
        $this->ref2_dynamic = $this->getSetting(self::REF2_DYNAMIC, $this->ref2_dynamic);
        $this->customer_data_dynamic = $this->getSetting(self::CUSTOMER_DATA_DYNAMIC, $this->customer_data_dynamic);

        $this->ref1_static = $this->getSetting(self::REF1_STATIC, $this->ref1_static);
        $this->ref2_static = $this->getSetting(self::REF2_STATIC, $this->ref2_static);
        $this->customer_data_static = $this->getSetting(self::CUSTOMER_DATA_STATIC, $this->customer_data_static);
        $this->declared_value = $this->getSetting(self::DECLARED_VALUE, $this->declared_value);
        $this->cud = $this->getSetting(self::CUD, $this->cud);
        $this->rod = $this->getSetting(self::ROD, $this->rod);
        $this->dpde = $this->getSetting(self::DPDE, $this->dpde);
        $this->dpdnd = $this->getSetting(self::DPDND, $this->dpdnd);
        $this->dpdtoday = $this->getSetting(self::DPDTODAY, $this->dpdtoday);
        $this->dpdfood = $this->getSetting(self::DPDFOOD, $this->dpdfood);
        $this->dpdlq = $this->getSetting(self::DPDLQ, $this->dpdlq);
        $this->dpdsaturday = $this->getSetting(self::DPDSATURDAY, $this->dpdsaturday);
        $this->duty = $this->getSetting(self::DUTY, $this->duty);
        $this->log_mode = $this->getSetting(self::LOG_MODE, $this->log_mode);
        $this->default_printer_type = $this->getSetting(self::DEFAULT_PRINTER_TYPE, $this->default_printer_type);
        $this->default_weight = $this->getSetting(self::DEFAULT_WEIGHT, $this->default_weight);
        $this->disable_send_shipping_mail = $this->getSetting(self::DISABLE_SEND_SHIPPING_MAIL, $this->disable_send_shipping_mail);
        $this->parcel_content_source = $this->getSetting(self::PARCEL_CONTENT_SOURCE, $this->parcel_content_source);
        $this->custom_cart = $this->getSetting(self::CUSTOM_CART, $this->custom_cart);

    }

    /**
     * Returns a setting from database
     *
     * @param string $name Setting name
     * @param string $default_value Default setting value
     * @return string Setting value
     */
    private function getSetting($name, $default_value)
    {
        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);

        return Configuration::get($name, null, $id_shop_group, $id_shop) !== false ? Configuration::get($name, null, $id_shop_group, $id_shop) : $default_value;
    }

    /**
     * Deletes settings from database
     *
     * @return bool Settings deleted successfully
     */
    public static function deleteConfiguration()
    {
        $success = true;

        $success &= self::deleteByNames(array(
            self::LOGIN, self::PASSWORD, self::CLIENT_NUMBER, self::CLIENT_NAME, self::NAME_SURNAME, self::ADDRESS,
            self::POSTCODE, self::CITY, self::EMAIL, self::PHONE, self::CUSTOMER_COMPANY, self::CUSTOMER_NAME, self::CUSTOMER_PHONE,
            self::CUSTOMER_FID, self::PRICE_CALCULATION_TYPE, self::CARRIER_STANDARD, self::CARRIER_STANDARD_COD,
            self::CARRIER_CLASSIC, self::WEIGHT_CONVERSATION_RATE, self::DIMENSION_CONVERSATION_RATE, self::WS_URL, self::CARRIER_STANDARD_ID,
            self::CARRIER_STANDARD_COD_ID, self::CARRIER_CLASSIC_ID, self::ADDITIONAL_REF1, self::ADDITIONAL_REF2, self::ADDITIONAL_CUSTOMER_DATA_1,
            self::REF1_DYNAMIC, self::REF2_DYNAMIC, self::CUSTOMER_DATA_DYNAMIC, self::REF1_STATIC, self::REF2_STATIC, self::CUSTOMER_DATA_STATIC,
            self::CARRIER_PUDO, self::CARRIER_PUDO_ID, self::DECLARED_VALUE, self::CUD, self::ROD, self::DPDE, self::DPDND, self::DUTY, self::LOG_MODE,
            self::DEFAULT_WEIGHT, self::DEFAULT_PRINTER_TYPE, self::DISABLE_SEND_SHIPPING_MAIL, self::CARRIER_PUDO_COD, self::CARRIER_PUDO_COD_ID, self::CUSTOM_CART
        ));

        foreach (DpdPoland::getPaymentModules() as $payment_module)
            $success &= Configuration::deleteByName(self::COD_MODULE_PREFIX . $payment_module['name']);

        return $success;
    }

    /**
     * Deletes settings from database by their names
     *
     * @param array $names Settings names list
     * @return bool Settings deleted successfully
     */
    private static function deleteByNames($names)
    {
        $success = true;

        foreach ($names as $name)
            $success &= Configuration::deleteByName($name);

        return $success;
    }

    /**
     * Deletes a single setting from database
     *
     * @param string $name Setting name
     * @return bool Setting deleted successfully
     */
    public static function deleteByName($name)
    {
        return Configuration::deleteByName($name);
    }

    /**
     * Checks if required settings are filled
     *
     * @return bool Required settings are filled
     */
    public static function checkRequiredConfiguration()
    {
        $configuration_obj = new DpdPolandConfiguration();

        if (!$configuration_obj->login ||
            !$configuration_obj->password ||
            !$configuration_obj->client_number ||
            !$configuration_obj->client_name ||
            !$configuration_obj->weight_conversation_rate ||
            !$configuration_obj->dimension_conversation_rate ||
            !$configuration_obj->ws_url ||
            !$configuration_obj->customer_fid)
            return false;

        if (DpdPolandSenderAddress::getAddressesCount() <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Returns zones which are assigned to a carrier
     *
     * @param int $id_carrier Carrier ID
     * @return array|false|mysqli_result|null|PDOStatement|resource Carrier zones
     */
    public static function getCarrierZones($id_carrier)
    {
        return DB::getInstance()->executeS('
			SELECT `id_zone`
			FROM `' . _DB_PREFIX_ . 'carrier_zone`
			WHERE `id_carrier` = "' . (int)$id_carrier . '"
		');
    }

    /**
     * Assigns PrestaShop zones for DPD carriers
     *
     * @return bool Zones assigned successfully
     */
    public static function saveZonesForCarriers()
    {
        $configuration = new DpdPolandConfiguration();

        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);

        if ($configuration->carrier_classic) {
            $id_carrier_classic = (int)Configuration::get(DpdPolandConfiguration::CARRIER_CLASSIC_ID, null, $id_shop_group, $id_shop);
            $classic_carrier_obj = DpdPolandService::getCarrierByReference((int)$id_carrier_classic);

            if (Validate::isLoadedObject($classic_carrier_obj))
                $id_carrier_classic = $classic_carrier_obj->id;

            if (!self::removeZonesForCarrier($id_carrier_classic) || !self::saveZoneForCarrier('classic', $id_carrier_classic))
                return false;
        }

        if ($configuration->carrier_pudo) {
            $id_carrier_pudo = (int)Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_ID, null, $id_shop_group, $id_shop);
            $pudo_carrier_obj = DpdPolandService::getCarrierByReference((int)$id_carrier_pudo);

            if (Validate::isLoadedObject($pudo_carrier_obj))
                $id_carrier_pudo = $pudo_carrier_obj->id;

            if (!self::removeZonesForCarrier($id_carrier_pudo) || !self::saveZoneForCarrier('pudo', $id_carrier_pudo))
                return false;
        }

        if ($configuration->carrier_pudo_cod) {
            $id_carrier_pudo_cod = (int)Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_COD_ID, null, $id_shop_group, $id_shop);
            $pudo_cod_carrier_obj = DpdPolandService::getCarrierByReference((int)$id_carrier_pudo_cod);

            if (Validate::isLoadedObject($pudo_cod_carrier_obj))
                $id_carrier_pudo_cod = $pudo_cod_carrier_obj->id;

            if (!self::removeZonesForCarrier($id_carrier_pudo_cod) || !self::saveZoneForCarrier('pudo_cod', $id_carrier_pudo_cod))
                return false;
        }

        if ($configuration->carrier_standard) {
            $id_carrier_standard = (int)Configuration::get(DpdPolandConfiguration::CARRIER_STANDARD_ID, null, $id_shop_group, $id_shop);
            $carrier_standard_obj = DpdPolandService::getCarrierByReference((int)$id_carrier_standard);

            if (Validate::isLoadedObject($carrier_standard_obj))
                $id_carrier_standard = $carrier_standard_obj->id;

            if (!self::removeZonesForCarrier($id_carrier_standard) || !self::saveZoneForCarrier('standard', $id_carrier_standard))
                return false;
        }

        if ($configuration->carrier_standard_cod) {
            $id_carrier_standard_cod = (int)Configuration::get(DpdPolandConfiguration::CARRIER_STANDARD_COD_ID, null, $id_shop_group, $id_shop);
            $carrier_standard_cod_obj = DpdPolandService::getCarrierByReference((int)$id_carrier_standard_cod);

            if (Validate::isLoadedObject($carrier_standard_cod_obj))
                $id_carrier_standard_cod = $carrier_standard_cod_obj->id;

            if (!self::removeZonesForCarrier($id_carrier_standard_cod) || !self::saveZoneForCarrier('standard_cod', $id_carrier_standard_cod))
                return false;
        }

        return true;
    }

    /**
     * Removes zones for carrier
     *
     * @param int $id_carrier Carrier ID
     * @return bool Zones removed from carrier
     */
    private static function removeZonesForCarrier($id_carrier)
    {
        return DB::getInstance()->execute('
			DELETE FROM `' . _DB_PREFIX_ . 'carrier_zone`
			WHERE `id_carrier` = "' . (int)$id_carrier . '"
		');
    }

    /**
     * Saves zone for carrier into database
     *
     * @param string $type Service type
     * @param int $id_carrier Carrier ID
     * @return bool Carrier zones saved successfully
     */
    private static function saveZoneForCarrier($type, $id_carrier)
    {
        foreach (Zone::getZones() as $zone) {
            if (Tools::getValue($type . '_' . (int)$zone['id_zone'])) {
                if (!DB::getInstance()->execute('
					INSERT INTO `' . _DB_PREFIX_ . 'carrier_zone`
						(`id_carrier`, `id_zone`)
					VALUES
						("' . (int)$id_carrier . '", "' . (int)$zone['id_zone'] . '")
				')) {
                    return false;
                }
            }
        }

        return true;
    }
}
