<?php

/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2021 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  @version  Release: $Revision$
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once "src/TrustMateInvitations.php";
require_once "src/Api.php";
require_once "src/Account.php";
require_once "src/PlatformApi.php";
require_once "src/TrustMateForms.php";

class TrustMate extends Module
{
    public const TRUSTMATE_HOST = 'https://trustmate.io';
    private $hornet_already_displayed = false;

    public function __construct()
    {
        $this->name = 'trustmate';
        $this->tab = 'front_office_features';
        $this->version = '1.6.1';
        $this->author = 'TrustMate SA';

        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6');
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('TrustMate Reviews Plugin');
        $this->description = $this->l('TrustMate.io helps you collect keyword-filled customer reviews to improve your search engine results, entice potential customers, and build a rock-solid online image.');
        $this->module_key = '969a53aca2785062383b87b0ca33e876';

        $this->forms = new TrustMateForms($this, $this->context->language);
        $this->papi = new PlatformApi();
    }

    /**
     * @return bool
     */
    public function install()
    {
        $this->papi->install();

        return parent::install()
            && Configuration::updateValue('TRUSTMATE_HAVEACCOUNT', false)
            && Configuration::updateValue('TRUSTMATE_UUID', null)
            && $this->registerHook('actionOrderStatusPostUpdate')
            && $this->registerHook('displayHome')
            && $this->registerHook('displayCustomWidgetPosition')
            && $this->registerHook('displayContentWrapperBottom')
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayProductPriceBlock')
            && $this->registerHook('displayFooterCategory')
            && $this->registerHook('displayFooter')
            && $this->registerHook('displayOrderConfirmation')
        ;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $this->papi->uninstall();

        return parent::uninstall()
            && $this->unregisterHook('actionOrderStatusPostUpdate')
            && $this->unregisterHook('displayHome')
            && $this->unregisterHook('displayContentWrapperBottom')
            && $this->unregisterHook('displayCustomWidgetPosition')
            && $this->unregisterHook('displayFooterProduct')
            && $this->unregisterHook('displayProductPriceBlock')
            && $this->unregisterHook('displayFooterCategory')
            && $this->unregisterHook('displayFooter')
            && $this->unregisterHook('displayOrderConfirmation')
        ;
    }

    /**
     * @return bool
     */
    public function enable($force_all = false)
    {
        $this->papi->install();

        return parent::enable($force_all);
    }

    /**
     * @return bool
     */
    public function disable($force_all = false)
    {
        $this->papi->uninstall();

        return parent::disable($force_all) && TrustmateAccount::reset();
    }

    public function getContent()
    {
        $output = '';
        $outputType = 'success';
        $currentActive = 'account';

        if (Tools::isSubmit('create_account')) {
            $api = new TrustMateApi();
            $resp = $api->register(array(
                'url' => isset($_POST['url']) ? $_POST['url'] : null,
                'name' => isset($_POST['name']) ? $_POST['name'] : null,
                'email' => isset($_POST['email']) ? $_POST['email'] : null,
                'street' => isset($_POST['street']) ? $_POST['street'] : null,
                'city' => isset($_POST['city']) ? $_POST['city'] : null,
                'zip_code' => isset($_POST['zip_code']) ? $_POST['zip_code'] : null,
                'country' => isset($_POST['country']) ? $_POST['country'] : null,
                'nip' => isset($_POST['nip']) ? $_POST['nip'] : null,
                'source' => 'presta',
            ));

            if (isset($resp['error'])) {
                $outputType = 'danger';
                $output .= $this->l($resp['error']);
            } else {
                if (isset($resp['uuid'])) {
                    Configuration::updateValue('TRUSTMATE_HAVEACCOUNT', true);
                    Configuration::updateValue('TRUSTMATE_UUID', $resp['uuid']);
                    Configuration::updateValue('TRUSTMATE_INVITATIONS', 2);
                    $output .= $this->l('Account created. UUID: ') . $resp['uuid'];
                    $this->papi->install();
                } else {
                    $outputType = 'danger';
                    $output .= $this->l('Unexpected error during registration');
                }
            }
        }

        if (Tools::isSubmit('have_account')) {
            Configuration::updateValue('TRUSTMATE_HAVEACCOUNT', true);
            $currentActive = 'account';
            $this->papi->install();
        }

        if (Tools::isSubmit('reset_account')) {
            Configuration::updateValue('TRUSTMATE_HAVEACCOUNT', false);
            Configuration::updateValue('TRUSTMATE_UUID', null);
            $currentActive = 'account';
            $output .= $this->l('Congiuration reset');
        }

        if (Tools::isSubmit('sandbox_mode')) {
            Configuration::updateValue('TRUSTMATE_DOMAIN', 'https://trustmate.tech');
            Configuration::updateValue('PLATFORM_API_DOMAIN', 'https://papi.trustmate.tech');
            $currentActive = 'account';
            $output .= $this->l('Sandbox mode enabled');
        }

        if (Tools::isSubmit('developer_mode')) {
            Configuration::updateValue('TRUSTMATE_DOMAIN', 'http://trustmate.test');
            Configuration::updateValue('PLATFORM_API_DOMAIN', 'http://localhost:8666');
            $currentActive = 'account';
            $output .= $this->l('Developer mode enabled');
        }

        if (Tools::isSubmit('submit' . $this->name)) {
            Configuration::updateValue('TRUSTMATE_UUID', Tools::getValue('TRUSTMATE_UUID'));

            $output .= $this->l('Settings updated.');
            $currentActive = 'account';
            $this->papi->install();
        }

        if (Tools::isSubmit('save_invitations')) {
            Configuration::updateValue(
                'TRUSTMATE_INSTANT_REVIEW',
                Tools::getValue('TRUSTMATE_INSTANT_REVIEW')
            );

            Configuration::updateValue(
                'TRUSTMATE_DISPATCH_TRIGGERED_BY',
                Tools::getValue('TRUSTMATE_DISPATCH_TRIGGERED_BY')
            );

            Configuration::updateValue(
                'TRUSTMATE_LANGUAGE',
                Tools::getValue('TRUSTMATE_LANGUAGE')
            );

            $wasApiEnabled = Configuration::get('TRUSTMATE_INVITATIONS') == TrustMateInvitations::STATUS_FROM_API;
            Configuration::updateValue('TRUSTMATE_INVITATIONS', Tools::getValue('TRUSTMATE_INVITATIONS'));

            if (Tools::getValue('TRUSTMATE_INVITATIONS') == TrustMateInvitations::STATUS_FROM_API) {
                $this->papi->enableApi();
                $output .= $this->l('API integration enabled').'. ';
            }

            if ($wasApiEnabled && Tools::getValue('TRUSTMATE_INVITATIONS') != TrustMateInvitations::STATUS_FROM_API) {
                $this->papi->disableApi();
                $output .= $this->l('API integration disabled').'. ';
            }

            $this->papi->install();

            $api = new TrustMateApi();
            $api->updateSettings(Configuration::get('TRUSTMATE_INSTANT_REVIEW'));

            $output .= $this->l('Settings updated.');
            $currentActive = 'invitations';
        }

        if (Tools::isSubmit('save_widget')) {
            Configuration::updateValue('TRUSTMATE_BEE', $_POST['TRUSTMATE_BEE']);
            Configuration::updateValue('TRUSTMATE_MUSKRAT2', $_POST['TRUSTMATE_MUSKRAT2']);
            Configuration::updateValue('TRUSTMATE_PRODUCT_FERRET2', $_POST['TRUSTMATE_PRODUCT_FERRET2']);
            Configuration::updateValue('TRUSTMATE_ALPACA', $_POST['TRUSTMATE_ALPACA']);
            Configuration::updateValue('TRUSTMATE_BADGER2', $_POST['TRUSTMATE_BADGER2']);
            Configuration::updateValue('TRUSTMATE_FERRET2', $_POST['TRUSTMATE_FERRET2']);
            Configuration::updateValue('TRUSTMATE_CHUPACABRA', $_POST['TRUSTMATE_CHUPACABRA']);
            Configuration::updateValue('TRUSTMATE_LEMUR', $_POST['TRUSTMATE_LEMUR']);
            Configuration::updateValue('TRUSTMATE_OWL', $_POST['TRUSTMATE_OWL']);
            Configuration::updateValue('TRUSTMATE_HORNET', $_POST['TRUSTMATE_HORNET']);
            Configuration::updateValue('TRUSTMATE_HYDRA', $_POST['TRUSTMATE_HYDRA']);
            Configuration::updateValue('TRUSTMATE_HORNET_POSITION', $_POST['TRUSTMATE_HORNET_POSITION']);
            Configuration::updateValue('TRUSTMATE_MULTIHORNET', $_POST['TRUSTMATE_MULTIHORNET']);
            Configuration::updateValue('TRUSTMATE_MULTIHORNET_PAGES', $_POST['TRUSTMATE_MULTIHORNET_PAGES']);
            $output .= $this->l('Settings updated.');
            $currentActive = 'widgets';
            $this->papi->updateMetadata();

            // Migrate old widgets
            if (Configuration::get('TRUSTMATE_MUSKRAT') && !Configuration::get('TRUSTMATE_MUSKRAT2')) {
                Configuration::updateValue('TRUSTMATE_MUSKRAT2', 1);
            }
            if (Configuration::get('TRUSTMATE_BADGER') && !Configuration::get('TRUSTMATE_BADGER2')) {
                Configuration::updateValue('TRUSTMATE_BADGER2', 1);
            }
            if (Configuration::get('TRUSTMATE_FERRET') && !Configuration::get('TRUSTMATE_FERRET2')) {
                Configuration::updateValue('TRUSTMATE_FERRET2', 1);
            }
            if (Configuration::get('TRUSTMATE_PRODUCT_FERRET') && !Configuration::get('TRUSTMATE_PRODUCT_FERRET2')) {
                Configuration::updateValue('TRUSTMATE_PRODUCT_FERRET2', 1);
            }
            if (Configuration::get('TRUSTMATE_GORILLA') && !Configuration::get('TRUSTMATE_HYDRA')) {
                Configuration::updateValue('TRUSTMATE_HYDRA', 1);
            }

            Configuration::updateValue('TRUSTMATE_MUSKRAT', 0);
            Configuration::updateValue('TRUSTMATE_BADGER', 0);
            Configuration::updateValue('TRUSTMATE_FERRET', 0);
            Configuration::updateValue('TRUSTMATE_PRODUCT_FERRET', 0);
            Configuration::updateValue('TRUSTMATE_GORILLA', 0);
        }

        $have_account = false;
        if (Configuration::get('TRUSTMATE_HAVEACCOUNT')) {
            $have_account = true;
        }

        if (Configuration::get('TRUSTMATE_UUID') != null) {
            $have_account = true;
        }

        $context = Context::getContext();

        $apiAccess = null;
        if ($apiAccessId = Configuration::get('TRUSTMATE_API_KEY_ID')) {
            $apiAccess = new WebserviceKey($apiAccessId);
        }

        $params = array(
            'output' => $output,
            'outputType' => $outputType,
            'have_account' => $have_account,
            'current_active' => $currentActive,
            'display_create_account' => Configuration::get('TRUSTMATE_UUID') == null,
            'api_access' => $apiAccess,
            'current_iso_lang' => Language::getIsoById($context->language->id),
        );

        if (strpos(_PS_VERSION_, '1.6') === 0) {
            $params['compatibility_warning'] = $this->l('Please be aware that PrestaShop 1.6.1.20+ (but below 1.7) have known issues on PHP 7.2 or greater. Webservice (API) mode may not work properly.');
        }

        if (!$have_account) {
            $params['account_data'] = TrustmateAccount::getRegistrationData();
            $params['countries'] = Country::getCountries($context->language->id, false);
        } else {
            $params['form_account'] = $this->displayForm('account');
            $params['form_invitations'] = $this->displayForm('invitations');
            $params['form_widgets'] = $this->displayForm('widgets');
        }

        $this->context->smarty->assign($params);

        return $this->display(__FILE__, 'views/templates/admin/configuration.tpl');
    }

    /**
     * @param string $which
     */
    public function displayForm($type = 'account')
    {
        if ($type == 'account') {
            $helper = $this->forms->getHelperForm('submit' . $this->name);
            $helper->fields_value['TRUSTMATE_UUID'] = Configuration::get('TRUSTMATE_UUID');

            return $helper->generateForm($this->forms->getAccountFormFields());
        } elseif ($type == 'invitations') {
            $helper = $this->forms->getHelperForm('save_invitations');
            $helper->fields_value['TRUSTMATE_DISPATCH_TRIGGERED_BY'] = Configuration::get('TRUSTMATE_DISPATCH_TRIGGERED_BY');
            $helper->fields_value['TRUSTMATE_FEED'] = $this->context->link->getModuleLink('trustmate', 'feed');
            $helper->fields_value['TRUSTMATE_INVITATIONS'] = Configuration::get('TRUSTMATE_INVITATIONS');
            $helper->fields_value['TRUSTMATE_INSTANT_REVIEW'] = (bool) Configuration::get('TRUSTMATE_INSTANT_REVIEW');
            $helper->fields_value['TRUSTMATE_LANGUAGE'] = Configuration::get('TRUSTMATE_LANGUAGE');

            return $helper->generateForm($this->forms->getInvitationsFormFields());
        } elseif ($type == 'widgets') {
            $params = array(
                'current_iso_lang' => Language::getIsoById($this->context->language->id),
                'TRUSTMATE_HORNET_POSITION' => Configuration::get('TRUSTMATE_HORNET_POSITION'),
                'TRUSTMATE_FERRET2' => Configuration::get('TRUSTMATE_FERRET2'),
                'TRUSTMATE_PRODUCT_FERRET2' => Configuration::get('TRUSTMATE_PRODUCT_FERRET2'),
                'TRUSTMATE_BADGER2' => Configuration::get('TRUSTMATE_BADGER2'),
                'TRUSTMATE_BEE' => Configuration::get('TRUSTMATE_BEE'),
                'TRUSTMATE_MUSKRAT2' => Configuration::get('TRUSTMATE_MUSKRAT2'),
                'TRUSTMATE_HORNET' => Configuration::get('TRUSTMATE_HORNET'),
                'TRUSTMATE_HYDRA' => Configuration::get('TRUSTMATE_HYDRA'),
                'TRUSTMATE_MULTIHORNET' => Configuration::get('TRUSTMATE_MULTIHORNET'),
                'TRUSTMATE_CHUPACABRA' => Configuration::get('TRUSTMATE_CHUPACABRA'),
                'TRUSTMATE_LEMUR' => Configuration::get('TRUSTMATE_LEMUR'),
                'TRUSTMATE_OWL' => Configuration::get('TRUSTMATE_OWL'),
                'TRUSTMATE_MULTIHORNET_PAGES' => Configuration::get('TRUSTMATE_MULTIHORNET_PAGES'),
                'TRUSTMATE_ALPACA' => Configuration::get('TRUSTMATE_ALPACA'),
            );

            $this->context->smarty->assign($params);
            $helper = $this->forms->getHelperForm(
                'save_widgets',
                _PS_MODULE_DIR_ . 'trustmate/views/templates/admin/',
                'form_widget.tpl'
            );

            $helper->fields_value['TRUSTMATE_LEMUR'] = Configuration::get('TRUSTMATE_LEMUR');
            $helper->fields_value['TRUSTMATE_OWL'] = Configuration::get('TRUSTMATE_OWL');
            $helper->fields_value['TRUSTMATE_HORNET'] = Configuration::get('TRUSTMATE_HORNET');
            $helper->fields_value['TRUSTMATE_HYDRA'] = Configuration::get('TRUSTMATE_HYDRA');
            $helper->fields_value['TRUSTMATE_MULTIHORNET'] = Configuration::get('TRUSTMATE_MULTIHORNET');
            $helper->fields_value['TRUSTMATE_PRODUCT_FERRET2'] = Configuration::get('TRUSTMATE_PRODUCT_FERRET2');
            $helper->fields_value['TRUSTMATE_ALPACA'] = Configuration::get('TRUSTMATE_ALPACA');
            $helper->fields_value['TRUSTMATE_BEE'] = Configuration::get('TRUSTMATE_BEE');
            $helper->fields_value['TRUSTMATE_MUSKRAT2'] = Configuration::get('TRUSTMATE_MUSKRAT2');
            $helper->fields_value['TRUSTMATE_BADGER2'] = Configuration::get('TRUSTMATE_BADGER2');
            $helper->fields_value['TRUSTMATE_FERRET2'] = Configuration::get('TRUSTMATE_FERRET2');
            $helper->fields_value['TRUSTMATE_CHUPACABRA'] = Configuration::get('TRUSTMATE_CHUPACABRA');

            return $helper->generateForm(array());
        }
    }

    public function hookActionOrderStatusPostUpdate($data)
    {
        if (!TrustMateInvitations::isDispatchEnabled()) {
            return;
        }

        /** @var OrderState $status */
        $status = $data['newOrderStatus'];
        if (!TrustMateInvitations::isDispatchTriggeredBy($status)) {
            return;
        }

        try {
            $api = new TrustMateApi();
            $api->dispatchInvitation(new Order($data['id_order']));
        } catch (Exception $exception) {
            // gotta catch'em all
        }
    }

    /**
     * Hook for edge widgets for Prestashop 1.7
     *
     * @return string
     */
    public function hookDisplayContentWrapperBottom()
    {
        if (version_compare(_PS_VERSION_, "1.7", ">=")) {
            return $this->getWidgetsOnBottom();
        }

        return "";
    }

    /**
     * Hook for edge widgets on Prestashop 1.6
     *
     * @return string
     */
    public function hookDisplayHome()
    {
        if (version_compare(_PS_VERSION_, "1.7", "<")) {
            return $this->getWidgetsOnBottom();
        }

        return "";
    }

    /**
     * @return string
     */
    public function hookDisplayCustomWidgetPosition()
    {
        return $this->getWidgetsOnBottom();
    }

    public function hookDisplayFooterProduct($params)
    {
        $query = array('product' => $this->getProductIdFromParams($params));

        $result = '';
        if (Configuration::get('TRUSTMATE_HYDRA')) {
            $result .= $this->displayTrustMateWidget('hydra', $query);
        }

        if (Configuration::get('TRUSTMATE_PRODUCT_FERRET2')) {
            $result .= $this->displayTrustMateWidget('productFerret2', $query);
        }

        if (Configuration::get('TRUSTMATE_BADGER2')) {
            $result .= $this->displayTrustMateWidget('badger2', $query);
        }

        return $result;
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if (!Configuration::get('TRUSTMATE_HORNET')) {
            return '';
        }

        $hook = null;
        $type = null;
        $result = '';

        if (strpos(Configuration::get('TRUSTMATE_HORNET_POSITION'), '@') !== false) {
            list($hook, $type) = explode('@', Configuration::get('TRUSTMATE_HORNET_POSITION'));
        } else { // bc
            $hook = 'DisplayProductPriceBlock';
            $type = 'price';
        }

        if ($hook != 'DisplayProductPriceBlock') {
            return '';
        }

        if ($type !== $params['type']) {
            return '';
        }

        $productId = $this->getProductIdFromParams($params);

        if ($productId) {
            $result .= "
                <span data-id-product='{$productId}'></span>
            ";
        } else {
            return '';
        }

        if ($this->hornet_already_displayed) {
            return '';
        }

        // For product list only add ids
        if (!Context::getContext()->controller instanceof ProductController) {
            return $result;
        }

        $query = array('product' => $productId);

        $result .= $this->displayTrustMateWidget('hornet', $query);
        $this->hornet_already_displayed = true;

        return $result;
    }

    public function hookDisplayFooterCategory($params)
    {
        if (Configuration::get('TRUSTMATE_MULTIHORNET') === '0') {
            return '';
        }

        if (Configuration::get('TRUSTMATE_MULTIHORNET_PAGES') === 'DisplayFooterCategory'
            || !Configuration::get('TRUSTMATE_MULTIHORNET_PAGES')) {
            $context = Context::getContext();

            $this->context->smarty->assign(array(
                'uuid' => Configuration::get('TRUSTMATE_UUID'),
                'current_iso_lang' => Language::getIsoById($context->language->id),
                'trustmate_domain' => TrustMateApi::domain(),
            ));

            return $this->display(__FILE__, 'multihornet.tpl');
        }
    }

    public function hookDisplayFooter($params)
    {
        if (Configuration::get('TRUSTMATE_MULTIHORNET') === '0') {
            return '';
        }

        if (Configuration::get('TRUSTMATE_MULTIHORNET_PAGES') === 'DisplayFooter') {
            $context = Context::getContext();

            $this->context->smarty->assign(array(
                'uuid' => Configuration::get('TRUSTMATE_UUID'),
                'current_iso_lang' => Language::getIsoById($context->language->id),
                'trustmate_domain' => TrustMateApi::domain(),
            ));

            return $this->display(__FILE__, 'multihornet.tpl');
        }
    }

    public function hookDisplayOrderConfirmation($params)
    {
        if (Configuration::get('TRUSTMATE_INSTANT_REVIEW')) {
            if (!empty($params['order'])) {
                $order = $params['order'];
            } elseif (!empty($params['objOrder'])) {
                $order = $params['objOrder'];
            } else {
                return '';
            }

            $customer = $order->getCustomer();

            return "<script>
                TRUST_MATE_USER_NAME = '{$customer->firstname}';
                TRUST_MATE_USER_EMAIL = '{$customer->email}';
                TRUST_MATE_ORDER_NUMBER = '{$order->reference}';
                TRUST_MATE_COMPANY_UUID = '".Configuration::get('TRUSTMATE_UUID')."';
            </script>
            <script defer type='text/javascript' src='".TrustMateApi::domain()."/api/invitation/script'></script>
            ";
        }
    }

    private function displayTrustMateWidget($widget, $query = array())
    {
        $this->context->smarty->assign(array(
            'host' => TrustMateApi::domain(),
            'uuid' => Configuration::get('TRUSTMATE_UUID'),
            'widget' => $widget,
            'query' => http_build_query($query),
        ));

        return $this->display(__FILE__, 'widget.tpl');
    }

    /**
     * @return string
     */
    private function getWidgetsOnBottom()
    {
        $result = '';

        if (Configuration::get('TRUSTMATE_ALPACA')) {
            $result .= $this->displayTrustMateWidget('alpaca');
        }

        if (Configuration::get('TRUSTMATE_BEE')) {
            $result .= $this->displayTrustMateWidget('bee');
        }

        if (Configuration::get('TRUSTMATE_CHUPACABRA')) {
            $result .= $this->displayTrustMateWidget('chupacabra');
        }

        if (Configuration::get('TRUSTMATE_FERRET2')) {
            $result .= $this->displayTrustMateWidget('ferret2');
        }

        if (Configuration::get('TRUSTMATE_MUSKRAT2')) {
            $result .= $this->displayTrustMateWidget('muskrat2');
        }

        if (Configuration::get('TRUSTMATE_LEMUR')) {
            $result .= $this->displayTrustMateWidget('lemur');
        }

        if (Configuration::get('TRUSTMATE_OWL')) {
            $result .= $this->displayTrustMateWidget('owl2');
        }

        return $result;
    }

    private function getProductIdFromParams($params)
    {
        if (!isset($params['product'])) {
            return null;
        }

        $id_product = is_object($params['product']) && $params['product'] instanceof Product
            ? $params['product']->id
            : $params['product']['id_product'];

        if ($id_product === null) {
            if (($id_product = $params['product']->specificPrice['id_product']) === null) {
                if (!Validate::isReference($params['product']->reference)) {
                    $id_product = null;
                }

                $result = Db::getInstance()->getRow('
                    SELECT `id_product`
                    FROM `' . _DB_PREFIX_ . 'product` p
                    WHERE p.`reference` = \'' . pSQL($params['product']->reference) . '\'
                ');

                if (!isset($result['id_product'])) {
                    $id_product = null;
                } else {
                    $id_product = $result['id_product'];
                }
            }
        }

        return $id_product;
    }
}
