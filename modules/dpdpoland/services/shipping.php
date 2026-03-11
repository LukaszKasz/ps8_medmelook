<?php

namespace DpdPoland\Service;

use Address;
use Carrier;
use Cart;
use Combination;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Db;
use DpdPoland;
use DpdPolandConfiguration;
use DpdPolandLog;
use DpdPolandPackage;
use DpdPolandPackageWS;
use DpdPolandParcel;
use DpdPolandParcelProduct;
use DpdPolandSenderAddress;
use Hook;
use Mail;
use Order;
use OrderCarrier;
use Product;
use Tools;
use Translate;
use Validate;

require_once(_PS_MODULE_DIR_ . 'dpdpoland/services/pudo.php');
require_once(_PS_MODULE_DIR_ . 'dpdpoland/services/configuration.php');
require_once(_PS_MODULE_DIR_ . 'dpdpoland/dpdpoland.php');


class ShippingModel
{
    public $id_order;
    public $dpdpoland_SessionType;
    public $dpdpoland_pudo_code;
    public $dpdpoland_id_address_delivery;
    /**
     * @var false|mixed
     */
    public $dpdpoland_address_company;
    /**
     * @var false|mixed
     */
    public $dpdpoland_address_country;
    /**
     * @var false|mixed
     */
    public $dpdpoland_address_firstname;
    /**
     * @var false|mixed
     */
    public $dpdpoland_address_lastname;
    /**
     * @var false|mixed
     */
    public $dpdpoland_address_street;
    /**
     * @var bool|string
     */
    public $dpdpoland_address_postcode;
    /**
     * @var false|mixed
     */
    public $dpdpoland_address_city;
    /**
     * @var false|mixed
     */
    public $dpdpoland_address_email;
    /**
     * @var false|mixed
     */
    public $dpdpoland_address_phone;
    /**
     * @var int
     */
    public $sender_address_selection;
    /**
     * @var false|mixed
     */
    public $additional_info;
    /**
     * @var false|mixed
     */
    public $dpdpoland_PayerNumber;
    /**
     * @var false|mixed
     */
    public $dpdpoland_ref1;
    /**
     * @var false|mixed
     */
    public $dpdpoland_ref2;
    /**
     * @var int
     */
    public $cud;
    /**
     * @var int
     */
    public $rod;
    /**
     * @var int
     */
    public $dpde;
    /**
     * @var int
     */
    public $dpdnd;
    /**
     * @var int
     */
    public $dpdsaturday;
    /**
     * @var int
     */
    public $dpdfood;
    /**
     * @var string
     */
    public $dpdfood_limit_date;
    /**
     * @var int
     */
    public $dpdlq;
    /**
     * @var int
     */
    public $dpdtoday;
    /**
     * @var float
     */
    public $dpdpoland_COD_amount;
    /**
     * @var false|mixed
     */
    public $dpdpoland_DeclaredValue_amount;
    /**
     * @var int
     */
    public $duty;
    /**
     * @var float
     */
    public $dpdpoland_duty_amount;
    /**
     * @var string
     */
    public $dpdpoland_duty_currency;
    /**
     * @var false|mixed
     */
    public $parcels;
    /**
     * @var false|mixed
     */
    public $dpdpoland_products;
}

class ShippingService
{
    /** @var Context */
    private $context;

    /**
     * @var array Error messages
     */
    public static $errors = array();

    /**
     * @var PudoService
     */
    private $pudoService;

    private $webservice;


    /** @var ConfigurationService */
    private $configurationService;

    public function __construct()
    {
        $this->context = Context::getContext();

        $this->pudoService = new PudoService;
        $this->configurationService = new ConfigurationService;
    }

    public function mapShippingModel()
    {
        $result = array();
        $shippingModel = new ShippingModel;
        $shippingModel->id_order = (int)Tools::getValue('id_order');
        $shippingModel->dpdpoland_SessionType = Tools::getValue('dpdpoland_SessionType');
        $shippingModel->dpdpoland_pudo_code = Tools::isSubmit('dpdpoland_pudo_code') ? Tools::getValue('dpdpoland_pudo_code') : null;
        $shippingModel->dpdpoland_id_address_delivery = Tools::getValue('dpdpoland_id_address_delivery');
        $shippingModel->dpdpoland_address_country = Tools::getValue('dpdpoland_address_country');
        $shippingModel->dpdpoland_address_company = Tools::getValue('dpdpoland_address_company');
        $shippingModel->dpdpoland_address_firstname = Tools::getValue('dpdpoland_address_firstname');
        $shippingModel->dpdpoland_address_lastname = Tools::getValue('dpdpoland_address_lastname');
        $shippingModel->dpdpoland_address_street = Tools::getValue('dpdpoland_address_street');
        $shippingModel->dpdpoland_address_postcode = DpdPoland::convertPostcode(Tools::getValue('dpdpoland_address_postcode'));
        $shippingModel->dpdpoland_address_city = Tools::getValue('dpdpoland_address_city');
        $shippingModel->dpdpoland_address_email = Tools::getValue('dpdpoland_address_email');
        $shippingModel->dpdpoland_address_phone = Tools::getValue('dpdpoland_address_phone');
        $shippingModel->sender_address_selection = (int)Tools::getValue('sender_address_selection');

        $shippingModel->additional_info = Tools::getValue('additional_info', null);
        $shippingModel->dpdpoland_SessionType = Tools::getValue('dpdpoland_SessionType');
        $shippingModel->dpdpoland_PayerNumber = Tools::getValue('dpdpoland_PayerNumber');
        $shippingModel->dpdpoland_ref1 = Tools::getValue('dpdpoland_ref1', null);
        $shippingModel->dpdpoland_ref2 = Tools::getValue('dpdpoland_ref2', null);
        $shippingModel->cud = (int)Tools::isSubmit('cud');
        $shippingModel->rod = (int)Tools::isSubmit('rod');
        $shippingModel->dpde = (int)Tools::isSubmit('dpde');
        $shippingModel->dpdnd = (int)Tools::isSubmit('dpdnd');
        $shippingModel->dpdsaturday = (int)Tools::isSubmit('dpdsaturday');
        $shippingModel->dpdfood = (int)Tools::isSubmit('dpdfood');
        $shippingModel->dpdfood_limit_date = (string)Tools::getValue('dpdfood_limit_date');
        $shippingModel->dpdlq = (int)Tools::isSubmit('dpdlq');
        $shippingModel->dpdtoday = (int)Tools::isSubmit('dpdtoday');

        $shippingModel->dpdpoland_COD_amount = (float)Tools::getValue('dpdpoland_COD_amount');
        $shippingModel->dpdpoland_DeclaredValue_amount = Tools::getValue('dpdpoland_DeclaredValue_amount');

        $shippingModel->duty = (int)Tools::isSubmit('duty');
        $shippingModel->dpdpoland_duty_amount = (float)Tools::getValue('dpdpoland_duty_amount');
        $shippingModel->dpdpoland_duty_currency = (string)Tools::getValue('dpdpoland_duty_currency');
        $shippingModel->parcels = Tools::getValue('parcels');
        $shippingModel->dpdpoland_products = Tools::getValue('dpdpoland_products');

        array_push($result, $shippingModel);
        return $result;
    }

    public function mapShippingListModel($id_orders)
    {
        $result = array();
        foreach ($id_orders as $id_order) {
            $order = new Order((int)$id_order);
            $products = DpdPolandParcelProduct::getShippedProducts((int)$order->id);
            $address = new Address((int)$order->id_address_delivery);
            $customer = new Customer((int)$address->id_customer);

            $shippingModel = new ShippingModel;
            $shippingModel->id_order = $order->id;
            $shippingModel->dpdpoland_SessionType = $this->getSessionType($order);
            if ($shippingModel->dpdpoland_SessionType == "pudo")
                $shippingModel->dpdpoland_pudo_code = $this->getPudoCode($order);

            $shippingModel->dpdpoland_id_address_delivery = $address->id;
            $shippingModel->dpdpoland_address_country = $address->country;
            $shippingModel->dpdpoland_address_company = $address->company;
            $shippingModel->dpdpoland_address_firstname = $address->firstname;
            $shippingModel->dpdpoland_address_lastname = $address->lastname;
            $shippingModel->dpdpoland_address_street = $address->address1 . ' ' . $address->address2;
            $shippingModel->dpdpoland_address_postcode = DpdPoland::convertPostcode($address->postcode);
            $shippingModel->dpdpoland_address_city = $address->city;
            $shippingModel->dpdpoland_address_email = $customer->email;
            $shippingModel->dpdpoland_address_phone = isset($address->phone_mobile) && !empty(trim($address->phone_mobile)) ? $address->phone_mobile : $address->phone;

            $shippingModel->additional_info = $this->configurationService->getDefaultCustomerData1($order, $products);
            $shippingModel->dpdpoland_PayerNumber = Tools::getValue('dpdpoland_PayerNumber');
            $shippingModel->dpdpoland_ref1 = $this->configurationService->getDefaultRef1($order, $products);
            $shippingModel->dpdpoland_ref2 = $this->configurationService->getDefaultRef2($order, $products);
            $shippingModel->cud = 0;
            $shippingModel->rod = 0;
            $shippingModel->dpde = 0;
            $shippingModel->dpdnd = 0;
            $shippingModel->dpdsaturday = 0;
            $shippingModel->dpdfood = 0;
            $shippingModel->dpdfood_limit_date = null;
            $shippingModel->dpdlq = 0;
            $shippingModel->dpdfood = 0;
            $shippingModel->dpdtoday = 0;
            $shippingModel->duty = 0;

            if ($shippingModel->dpdpoland_SessionType == "domestic_with_cod") {
                $id_currency_pl = Currency::getIdByIsoCode(_DPDPOLAND_CURRENCY_ISO_, (int)Context::getContext()->shop->id);
                $currency_from = new Currency((int)$order->id_currency);
                $currency_to = new Currency((int)$id_currency_pl);

                $shippingModel->dpdpoland_COD_amount = Tools::convertPriceFull($order->total_paid_tax_incl, $currency_from, $currency_to);
            }

            $parcels = array();
            foreach ($products as $product) {
                $productArray = array();
                array_push($productArray, $product);
                array_push($parcels,
                    array(
                        'content' => DpdPolandParcel::getParcelContent($productArray),
                        'number' => $product['id_product'],
                        'length' => $product['length'],
                        'width' => $product['width'],
                        'height' => $product['height'],
                        'weight' => $product['weight'],
                    )
                );
            }
            $shippingModel->parcels = $parcels;
            $shippingModel->dpdpoland_products = $products;
            $shippingModel->sender_address_selection = 1;

            array_push($result, $shippingModel);
        }

        return $result;
    }

    /**
     * Saves package from order management page
     * Used via AJAX
     *
     * @param ShippingModel $shippingModel
     * @return array Package saved successfully
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function savePackageFromPost(array $shippingModel, $payerNumber)
    {
        DpdPolandLog::addLog('savePackageFromPost');
        $packages = array();
        foreach ($shippingModel as $shipping) {
            $newPackage = $this->prepareShipping($shipping);
            if ($newPackage->id_order == null)
                return array();
            array_push($packages, $newPackage);
        }

        if (!$result = $this->createMultiple($packages, $payerNumber)) {
            self::$errors = DpdPolandPackageWS::$errors;
            DpdPolandLog::addError('error in package create');
            DpdPolandLog::addError(json_encode(self::$errors));
            return array();
        }

        $isMultiShipping = isset($result[0]);

        if ($isMultiShipping) {
            foreach ($packages as $index => $package) {
                if (!$this->saveParcelsIntoPackage($package->id_package_ws,
                    $package->parcels, $result[$index]['Parcels'], $package->dpdpoland_products)) {
                    DpdPolandLog::addError('error in saveParcelsIntoPackage');
                    return array();
                }

                if (!$waybill = $this->saveWaybills($result, $package))
                    return array();
            }
        } else {
            if (!$this->saveParcelsIntoPackage($packages[0]->id_package_ws,
                $packages[0]->parcels, $result['Parcels'], $packages[0]->dpdpoland_products)) {
                DpdPolandLog::addError('error in saveParcelsIntoPackage');
                return array();
            }

            $waybill = isset($result['Parcels']['Parcel']['Waybill']) ?
                $result['Parcels']['Parcel']['Waybill'] : $result['Parcels']['Parcel'][0]['Waybill'];

            if (!$this->addTrackingNumber($packages[0]->id_order, $waybill)) {
                DpdPolandLog::addError('error in addTrackingNumber');
                return array();
            }

            $packages[0]->removeOrderDuplicates();
        }


        return array_column($packages, 'id_package_ws');
    }

    /**
     * Creates package
     * @param array $shippingList
     * @return bool Shipping list
     *
     */
    public function createMultiple($shippingList, $payerNumber)
    {
        if (!$this->webservice)
            $this->webservice = new DpdPolandPackageWS;

        return $this->webservice->create($shippingList, $payerNumber);
    }

    /**
     * Returns service ID according to session type
     *
     * @param string $session_type Session (service) type
     * @return bool|int Service ID
     */
    private function getMethodBySessionType($session_type)
    {
        switch ($session_type) {
            case 'domestic':
                return _DPDPOLAND_STANDARD_ID_;
            case 'domestic_with_cod':
                return _DPDPOLAND_STANDARD_COD_ID_;
            case 'international':
                return _DPDPOLAND_CLASSIC_ID_;
            case 'pudo':
                return _DPDPOLAND_PUDO_ID_;
            default:
                return false;
        }
    }

    /**
     * Saves parcels into package
     *
     * @param int $id_package_ws What package will parcels be saved to
     * @param array $parcels Parcels data from POST
     * @param array $parcels_ws Parcels data from WebServices
     * @param array $parcelProducts Parcels content
     * @return bool Parcels saved successfully
     */
    private function saveParcelsIntoPackage($id_package_ws, $parcels, $parcels_ws, $parcelProducts)
    {
        $parcels_ws = isset($parcels_ws[0]) ? $parcels_ws : (isset($parcels_ws['Parcel']['Status']) ? array($parcels_ws) : $parcels_ws['Parcel']); // array must be multidimentional

        foreach ($parcels_ws as $parcel_data) {
            $parcel_data = isset($parcel_data['Parcel']) ? $parcel_data['Parcel'] : $parcel_data;
            $parcel_number = $this->searchForReference($parcel_data['Reference'], $parcels);
            if ($parcel_number === null) {
                // parcel number received from ws does not match with any we have locally. Because of that we do not know what data should be saved
                self::$errors[] = sprintf($this->translate('Parcel #%d does not exists'), $parcel_number);
                DpdPolandLog::addError(json_encode(self::$errors));
                return false;
            } else {
                $parcel = new DpdPolandParcel;
                $parcel->id_parcel = (int)$parcel_data['ParcelId'];
                $parcel->id_package_ws = (int)$id_package_ws;
                $parcel->waybill = $parcel_data['Waybill'];
                $parcel->content = $parcels[$parcel_number]['customerData1'];
                $parcel->weight = (float)$parcels[$parcel_number]['weight'];
                $parcel->weight_adr = (float)$parcels[$parcel_number]['weightAdr'];
                $parcel->height = (float)$parcels[$parcel_number]['sizeZ'];
                $parcel->length = (float)$parcels[$parcel_number]['sizeX'];
                $parcel->width = (float)$parcels[$parcel_number]['sizeY'];
                $parcel->number = (int)$parcel_number + 1;

                if ($parcel->add()) {
                    $this->saveProductsIntoParcel($parcel, $parcelProducts);
                } else
                    return false;
            }
        }

        return true;
    }

    function searchForReference($reference, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['reference'] == $reference) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Saves order tracking number on PS 1.5 and PS 1.6
     *
     * @param int $id_order Order ID
     * @param string $tracking_number Tracking number
     * @return bool Tracking number saved successfully
     */
    private function addTrackingNumber($id_order, $tracking_number)
    {
        if (version_compare(_PS_VERSION_, '1.5', '<'))
            return $this->addShippingNumber($id_order, $tracking_number);

        $order = new Order((int)$id_order);
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (!Validate::isLoadedObject($order_carrier)) {
            self::$errors[] = $this->translate('The order carrier ID is invalid.');
            DpdPolandLog::addError(json_encode(self::$errors));
            return false;
        } elseif (!Validate::isTrackingNumber($tracking_number)) {
            self::$errors[] = $this->translate('The tracking number is incorrect.');
            DpdPolandLog::addError(json_encode(self::$errors));
            return false;
        } else {
            $order->shipping_number = $tracking_number;
            $order->update();

            $order_carrier->tracking_number = $tracking_number;
            if ($order_carrier->update()) {
                $customer = new Customer((int)$order->id_customer);
                $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
                if (!Validate::isLoadedObject($customer)) {
                    self::$errors[] = $this->translate('Can\'t load Customer object');
                    return false;
                }
                if (!Validate::isLoadedObject($carrier))
                    return false;

                $templateVars = array(
                    '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                    '{firstname}' => $customer->firstname,
                    '{lastname}' => $customer->lastname,
                    '{id_order}' => $order->id,
                    '{shipping_number}' => $order->shipping_number,
                    '{order_name}' => $order->getUniqReference(),
                    '{meta_products}' => ''
                );

                if (Configuration::get(DpdPolandConfiguration::DISABLE_SEND_SHIPPING_MAIL) == 1) {
                    DpdPolandLog::addLog('DISABLE_SEND_SHIPPING_MAIL = 1');
                    Hook::exec('actionAdminOrdersTrackingNumberUpdate', array('order' => $order, 'customer' => $customer, 'carrier' => $carrier));
                    return true;
                }

                if (@Mail::Send((int)$order->id_lang, 'in_transit', $this->translate('Package in transit'), $templateVars,
                    $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null,
                    _PS_MAIL_DIR_, false, (int)$order->id_shop)) {
                    DpdPolandLog::addLog('DISABLE_SEND_SHIPPING_MAIL = 0');
                    Hook::exec('actionAdminOrdersTrackingNumberUpdate', array('order' => $order, 'customer' => $customer, 'carrier' => $carrier));
                    return true;
                } else {
                    DpdPoland::addFlashError($this->translate('An error occurred while sending an email to the customer.'));
                    DpdPolandLog::addError('addTrackingNumber An error occurred while sending an email to the customer.');
                    return true;
                }
            } else {
                self::$errors[] = $this->translate('The order carrier cannot be updated.');
                DpdPolandLog::addError('addTrackingNumber The order carrier cannot be updated.');
                return false;
            }
        }
    }

    /**
     * Saves order shipping number on PS 1.4
     *
     * @param int $id_order Order ID
     * @param string $shipping_number Shipping number
     * @return bool Shipping number saved successfully
     */
    private function addShippingNumber($id_order, $shipping_number)
    {
        $order = new Order((int)$id_order);

        $order->shipping_number = $shipping_number;
        $order->update();
        if ($shipping_number) {
            $customer = new Customer((int)$order->id_customer);
            $carrier = new Carrier((int)$order->id_carrier);
            if (!Validate::isLoadedObject($customer) || !Validate::isLoadedObject($carrier)) {
                self::$errors[] = $this->translate('Customer / Carrier not found');
                DpdPolandLog::addError(json_encode(self::$errors));
                return false;
            }
            $templateVars = array(
                '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{order_name}' => sprintf('#%06d', (int)$order->id),
                '{id_order}' => (int)$order->id
            );
            @Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,
                $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null,
                _PS_MAIL_DIR_);
        }

        return true;
    }

    /**
     * Saves products into parcel
     *
     * @param DpdPolandParcel $parcel Object of parcel that products will be saved to
     * @param array $products Parcels content
     * @return bool Products saved into parcels
     */
    private function saveProductsIntoParcel(DpdPolandParcel $parcel, $products)
    {
        foreach ($products as $index => $product) {

            if ($index == $parcel->number) //product belongs to this parcel
            {
                $parcelProduct = new DpdPolandParcelProduct;
                $parcelProduct->id_parcel = (int)$parcel->id_parcel;
                $parcelProduct->id_product = (int)$product['id_product'];
                $parcelProduct->id_product_attribute = (int)$product['id_product_attribute'];
                $productObj = new Product((int)$product['id_product']);
                $combination = new Combination((int)$product['id_product_attribute']);
                $parcelProduct->name = (version_compare(_PS_VERSION_, '1.5', '<') ? $productObj->name[(int)Context::getContext()->language->id] :
                    Product::getProductName($product['id_product'], $product['id_product_attribute']));
                $parcelProduct->weight = (float)$combination->weight + (float)$productObj->weight;

                if (!$parcelProduct->add()) {
                    self::$errors[] = sprintf($this->translate('Unable to save product #%s to parcel #%d'), $parcelProduct->id_product . '-' .
                        $parcelProduct->id_product_attribute, $parcelProduct->id_parcel);
                    DpdPolandLog::addError(json_encode(self::$errors));
                    return false;
                }
            }
        }

        return true;
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
        $pudoCode = Tools::getValue('pudo_code');
        $idCart = (int)Tools::getValue('id_cart');

        if (empty($pudoCode) || empty($idCart)) {
            echo 'VALIDATION PARAMS ERROR';
            return false;
        }

        $tokenFrontend = Tools::getValue('session');
        $sessionToken = Tools::getToken(false);

        if ($tokenFrontend != $sessionToken) {
            echo 'VALIDATION SESSION ERROR';
            return false;
        }

        $cart = new Cart($idCart);

        if (!Validate::isLoadedObject($cart)) {
            echo 'VALIDATION CART ERROR';
            return false;
        }

        if ($cart->orderExists()) {
            echo 'VALIDATION ORDER ERROR';
            return false;
        }

        if (Validate::isLoadedObject($this->context->customer) && $cart->id_customer != $this->context->customer->id) {
            echo 'VALIDATION ERROR';
            echo $this->context->customer->id;
            echo $cart->id_customer;
            return false;
        }

        $sql = '
            INSERT INTO `' . _DB_PREFIX_ . 'dpdpoland_pudo_cart` (`pudo_code`, `id_cart`)
            VALUES ("' . pSQL($pudoCode) . '", ' . (int)$cart->id . ')
            ON DUPLICATE KEY UPDATE pudo_code = "' . pSQL($pudoCode) . '"
        ';

        return Db::getInstance()->execute($sql);
    }

    public function savePudoMapCodeBackend($pudoCode = null, $idCart = null)
    {
        if (empty($pudoCode) || empty($idCart)) {
            return false;
        }

        $cart = new Cart($idCart);
        if (!Validate::isLoadedObject($cart)) {
            return false;
        }

        $sql = '
            INSERT INTO `' . _DB_PREFIX_ . 'dpdpoland_pudo_cart` (`pudo_code`, `id_cart`)
            VALUES ("' . pSQL($pudoCode) . '", ' . (int)$cart->id . ')
            ON DUPLICATE KEY UPDATE pudo_code = "' . pSQL($pudoCode) . '"
        ';

        return Db::getInstance()->execute($sql);
    }

    /**
     * Check if addresses are the same
     *
     * @param Address $address1
     * @param Address $address2
     *
     * @return bool
     */
    private function isSameAddresses(Address $address1, Address $address2)
    {
        $checksum1 = sha1($address1->address1 . $address1->city . $address1->postcode);
        $checksum2 = sha1($address2->address1 . $address2->city . $address2->postcode);

        return $checksum1 == $checksum2;
    }

    private $WEIGHT_ADR_INCORRECT_TRANSLATE = "WEIGHT_ADR_INCORRECT";
    private $WEIGHT_ADR_TOO_LARGE_TRANSLATE = "WEIGHT_ADR_TOO_LARGE";
    private $ADR_NO_WEIGHT = "ADR_NO_WEIGHT";

    private function translate($string)
    {
        if ($string == $this->WEIGHT_ADR_INCORRECT_TRANSLATE)
            return "Waga ADR dla przesyłki jest nieprawidłowa";
        else if ($string == $this->WEIGHT_ADR_TOO_LARGE_TRANSLATE)
            return "Waga ADR nie może być większa od wagi przesyłki";
        else if ($string == $this->ADR_NO_WEIGHT)
            return "Usługa DPD Materiały niebezpieczne wymaga wprowadzenia wagi ADR";

        return Translate::getModuleTranslation('dpdpoland', $string, 'dpdpoland');
    }

    /**
     * @param ShippingModel $shippingModel
     * @return DpdPolandPackage|false
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function prepareShipping(ShippingModel $shippingModel)
    {
        $id_current_order = $shippingModel->id_order;
        $current_session_type = $shippingModel->dpdpoland_SessionType;
        $order = new Order($id_current_order);
        if ($current_session_type == 'pudo' || $current_session_type == 'pudo_cod') {
            if ($shippingModel->dpdpoland_pudo_code != null) {
                $pudoCode = $shippingModel->dpdpoland_pudo_code;

                $currentAddress = new Address($order->id_address_delivery);

                $pudoAddress = $this->pudoService->getPudoAddress(
                    $pudoCode,
                    $order->id_customer,
                    isset($currentAddress->phone_mobile) && !empty(trim($currentAddress->phone_mobile)) ?
                        $currentAddress->phone_mobile :
                        $currentAddress->phone
                );

                $idDeliveryAddress = $shippingModel->dpdpoland_id_address_delivery;
                $currentDeliveryAddress = new Address($idDeliveryAddress);

                if (!$this->isSameAddresses($pudoAddress, $currentDeliveryAddress)) {
                    $this->savePudoMapCodeBackend($pudoCode, (int)$order->id_cart);

                    $order->id_address_delivery = $pudoAddress->id;
                    $order->save();

                    $pudoAddress->id_customer = (int)$order->id_customer;
                    $pudoAddress->save();

                    $shippingModel->dpdpoland_id_address_delivery = $pudoAddress->id;
                } else {
                    $pudoAddress->delete();
                }

            } else {
                $shippingModel->dpdpoland_id_address_delivery = $order->id_address_delivery;
            }
        } else {
            if ($order->id_address_delivery != $shippingModel->dpdpoland_id_address_delivery) {
                $order->id_address_delivery = $shippingModel->dpdpoland_id_address_delivery;
                $order->save();
            }
        }

        $id_country = Country::getIdByName(null, $shippingModel->dpdpoland_address_country);

        $address_delivery = new Address();
        $address_delivery->id_customer = $order->id_customer;
        $address_delivery->alias = $this->translate('Delivery address');
        $address_delivery->id_country = $id_country;
        $address_delivery->company = $shippingModel->dpdpoland_address_company;
        $address_delivery->firstname = $shippingModel->dpdpoland_address_firstname;
        $address_delivery->lastname = $shippingModel->dpdpoland_address_lastname;
        $address_delivery->address1 = $shippingModel->dpdpoland_address_street;
        $address_delivery->postcode = $shippingModel->dpdpoland_address_postcode;
        $address_delivery->city = $shippingModel->dpdpoland_address_city;
        $address_delivery->other = $shippingModel->dpdpoland_address_email;
        $address_delivery->phone = $shippingModel->dpdpoland_address_phone;

        $address_validation_errors = $address_delivery->validateFields(false, true);

        if ($address_validation_errors !== true) {
            self::$errors[] = $this->translate('Client address is not valid:') . ' ' . $address_validation_errors . '. ' .
                $this->translate('Please update your client address with required fields.');

            DpdPolandLog::addError(json_encode(self::$errors));
            return new DpdPolandPackage();
        }

        $address_delivery->id = 0;
        $address_delivery->deleted = 1;

        if (!$address_delivery->save()) {
            self::$errors[] = $this->translate('Could not save client address');
            DpdPolandLog::addError(json_encode(self::$errors));
            return new DpdPolandPackage();
        }

        $id_method = (int)$this->getMethodBySessionType($current_session_type);
        if (!DpdPoland::validateAddressForPackageSession($address_delivery->id, (int)$id_method)) {
            self::$errors[] = $this->translate('Your delivery address is not compatible with the selected shipping method.') . ' ' .
                $this->translate('DPD Poland Domestic is available only if delivery address is Poland.') . ' ' .
                $this->translate('DPD International shipping method is available only if delivery address is not Poland.');

            DpdPolandLog::addError(json_encode(self::$errors));
            return new DpdPolandPackage();
        }

        $id_sender_address = $shippingModel->sender_address_selection;

        if (!$id_sender_address) {
            self::$errors[] = $this->translate('Sender address must be selected');
            DpdPolandLog::addError(json_encode(self::$errors));
            return new DpdPolandPackage();
        }

        $sender_address = new DpdPolandSenderAddress((int)$id_sender_address);
        $address_sender = new Address();

        $id_country = Country::getByIso(DpdPoland::POLAND_ISO_CODE);
        $countryName = Country::getNameById(null, $id_country);
        $address_sender->id_country = $id_country;
        $address_sender->company = $sender_address->company;
        $address_sender->firstname = $sender_address->name;
        $address_sender->lastname = $sender_address->name;
        $address_sender->address1 = $sender_address->address;
        $address_sender->address2 = $countryName;
        $address_sender->postcode = DpdPoland::convertPostcode($sender_address->postcode);
        $address_sender->city = $sender_address->city;
        $address_sender->other = $sender_address->email;
        $address_sender->phone = $sender_address->phone;
        $address_sender->alias = $this->translate('Sender address');
        $address_sender->deleted = 1;
        $address_sender->vat_number = $address_delivery->vat_number;
        $address_sender->phone_mobile = isset($address_delivery->phone_mobile) && !empty(trim($address_delivery->phone_mobile)) ? $address_delivery->phone_mobile : $address_delivery->phone;
        $address_sender->dni = $address_delivery->dni;

        $address_validation_errors = $address_sender->validateFields(false, true);

        if ($address_validation_errors !== true) {
            self::$errors[] = $this->translate('Sender address is not valid') . ' ' . $address_validation_errors;
            DpdPolandLog::addError(json_encode(self::$errors));
            return new DpdPolandPackage();
        }

        if (!$address_sender->save()) {
            self::$errors[] = $this->translate('Could save sender address');
            DpdPolandLog::addError(json_encode(self::$errors));
            return new DpdPolandPackage();
        }

        $additional_info = $shippingModel->additional_info;
        $old_package = DpdPolandPackage::getInstanceByIdOrder((int)$id_current_order);
        $id_package = $old_package->id_package ? $old_package->id_package : 0;

        $package = new DpdPolandPackage((int)$id_package);
        $package->id_order = (int)$id_current_order;
        $package->sessionType = $shippingModel->dpdpoland_SessionType;
        $package->payerNumber = $shippingModel->dpdpoland_PayerNumber;
        $package->id_address_delivery = (int)$address_delivery->id;
        $package->id_address_sender = (int)$address_sender->id;
        $package->additional_info = $additional_info;
        $package->ref1 = (string)$shippingModel->dpdpoland_ref1;
        $package->ref2 = (string)$shippingModel->dpdpoland_ref2;
        $package->id_sender_address = (int)$id_sender_address;
        $package->cud = $shippingModel->cud;
        $package->rod = $shippingModel->rod;
        $package->dpde = $shippingModel->dpde;
        $package->dpdnd = $shippingModel->dpdnd;
        $package->dpdsaturday = $shippingModel->dpdsaturday;
        $package->dpdfood = $shippingModel->dpdfood;
        $package->dpdfood_limit_date = $shippingModel->dpdfood_limit_date;
        $package->dpdlq = $shippingModel->dpdlq;
        $package->dpdtoday = $shippingModel->dpdtoday;
        $package->dpdpoland_products = $shippingModel->dpdpoland_products;

        $is_cod_module = Configuration::get(DpdPolandConfiguration::COD_MODULE_PREFIX . $order->module, null, $order->id_shop_group, $order->id_shop);

        if ($package->sessionType == 'domestic_with_cod' || $package->sessionType == 'pudo_cod' || ($is_cod_module && $shippingModel->dpdpoland_COD_amount > 0))
            $package->cod_amount = $shippingModel->dpdpoland_COD_amount;

        if ($declaredValue_amount = $shippingModel->dpdpoland_DeclaredValue_amount)
            $package->declaredValue_amount = (float)$declaredValue_amount;

        if ($shippingModel->duty == 1) {
            $package->duty = true;
            $package->duty_amount = $shippingModel->dpdpoland_duty_amount;
            $package->duty_currency = $shippingModel->dpdpoland_duty_currency;
        }

        if ($shippingModel->parcels) {
            foreach ($shippingModel->parcels as $parcel)
                $package->addParcel($parcel, $additional_info, $package->dpdlq);
        }

        if ($shippingModel->dpdlq == 1) {
            $hasWeightAdr = false;
            foreach ($shippingModel->parcels as $parcel) {
                $hasAdr = isset($parcel['adr']) && ($parcel['adr'] == 'true' || $parcel['adr'] == 'on');
                if (!$hasWeightAdr && $hasAdr)
                    $hasWeightAdr = true;
                if ($hasAdr && (!isset($parcel['weight_adr']) || (float)$parcel['weight_adr'] <= 0)) {
                    self::$errors[] = $this->translate($this->WEIGHT_ADR_INCORRECT_TRANSLATE);
                    DpdPolandLog::addError(json_encode(self::$errors));
                    return new DpdPolandPackage();
                } else if ($hasAdr && (float)$parcel['weight_adr'] > (float)$parcel['weight']) {
                    self::$errors[] = $this->translate($this->WEIGHT_ADR_TOO_LARGE_TRANSLATE);
                    DpdPolandLog::addError(json_encode(self::$errors));
                    return new DpdPolandPackage();
                }
            }
            if (!$hasWeightAdr) {
                self::$errors[] = $this->translate($this->ADR_NO_WEIGHT);
                DpdPolandLog::addError(json_encode(self::$errors));
                return new DpdPolandPackage();
            }
        }

        return $package;
    }

    private function getSessionType(Order $order)
    {
        $carrier = new Carrier((int)$order->id_carrier);

        if ($carrier->external_module_name !== "dpdpoland") {
            self::$errors[] = $this->translate('One of selected order have not been generated with DPD carrier');
            return null;
        }


        $standard_id = Configuration::get(DpdPolandConfiguration::CARRIER_STANDARD_ID, null, $order->id_shop_group, $order->id_shop);
        $standard_cod_id = Configuration::get(DpdPolandConfiguration::CARRIER_STANDARD_COD_ID, null, $order->id_shop_group, $order->id_shop);
        $classic_id = Configuration::get(DpdPolandConfiguration::CARRIER_CLASSIC_ID, null, $order->id_shop_group, $order->id_shop);
        $pudo_id = Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_ID, null, $order->id_shop_group, $order->id_shop);

        if ($order->id_carrier == $standard_id ||
            $order->id_carrier == $standard_cod_id ||
            $order->id_carrier == $classic_id ||
            $order->id_carrier == $pudo_id) {
            if ($order->id_carrier == Configuration::get(DpdPolandConfiguration::CARRIER_STANDARD_ID, null, $order->id_shop_group, $order->id_shop))
                return "domestic";
            else if ($order->id_carrier == Configuration::get(DpdPolandConfiguration::CARRIER_STANDARD_COD_ID, null, $order->id_shop_group, $order->id_shop))
                return "domestic_with_cod";
            else if ($order->id_carrier == Configuration::get(DpdPolandConfiguration::CARRIER_CLASSIC_ID, null, $order->id_shop_group, $order->id_shop))
                return "international";
            else if ($order->id_carrier == Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_ID, null, $order->id_shop_group, $order->id_shop))
                return "pudo";
        }

        self::$errors[] = $this->translate('One of the selected orders does not have an active DPD carrier. You can generate shipping individually.');
        return null;
    }

    /**
     * @param Order $order
     * @param ShippingModel $shippingModel
     */
    public function getPudoCode(Order $order)
    {
        $pudoCode = Db::getInstance()->getValue('
            SELECT `pudo_code`
            FROM `' . _DB_PREFIX_ . 'dpdpoland_pudo_cart`
            WHERE `id_cart` = ' . (int)$order->id_cart . '
        ');

        return $pudoCode;
    }

    private function saveWaybills($result, $package)
    {
        $waybills = array();
        if (isset($result['Parcels']['Parcel']['Waybill'])) {
            array_push($waybills, $result['Parcels']['Parcel']['Waybill']);
        } else if (isset($result[0]['Parcels']['Parcel'][0]['Waybill'])) {
            array_push($waybills, $result[0]['Parcels']['Parcel'][0]['Waybill']);
        } else {
            array_push($waybills, $result['Parcels']['Parcel'][0]['Waybill']);
        }

        $result = 1;
        foreach ($waybills as $waybill) {
            if (!$this->addTrackingNumber($package->id_order, $waybill)) {
                DpdPolandLog::addError('error in addTrackingNumber');
                $result = 0;
            }
        }

        $package->removeOrderDuplicates();

        return $result;
    }
}