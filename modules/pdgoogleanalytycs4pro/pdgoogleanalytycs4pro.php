<?php
/**
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Analytycs 4 Pro 1.7.x Module © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek @ PrestaDev.pl
* @license   Do not edit, modify or copy this file, if you wish to customize it, contact us at info@prestadev.pl.
* @link      http://prestadev.pl
* @package   PD Google Analytycs 4 Pro 1.7.x Module
* @version   1.0.2
* @date      01-05-2021
*/

require_once(dirname(__FILE__).'/models/PdGA4PModel.php');
require_once(dirname(__FILE__).'/models/PdGA4PModelCart.php');
require_once(dirname(__FILE__).'/models/PdGA4PRegistrationModel.php');
require_once(dirname(__FILE__) . '/vendor/autoload.php');

use Br33f\Ga4\MeasurementProtocol\Service;
use Br33f\Ga4\MeasurementProtocol\Dto\Request\BaseRequest;
use Br33f\Ga4\MeasurementProtocol\Dto\Event\PurchaseEvent;
use Br33f\Ga4\MeasurementProtocol\Dto\Event\RefundEvent;
use Br33f\Ga4\MeasurementProtocol\Dto\Parameter\ItemParameter;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PdGoogleAnalytycs4Pro extends Module
{
    public $html = '';
    public $errors = array();
    public $ga4_id = '';
    public $ga4_id2 = '';
    public $ga4_id_aw = '';
    public $ga4_id_aw_label = '';
    public $ga4_api_secret = '';
    public $product_ids_type;
    public $ga4_send_page_view;
    public $ga4_allow_enhanced_conversion;
    public $os_statuses_refund;
    public $os_statuses_send;
    public $transaction_send_type;
    public $http_referer = '';
    private static $order_confirmation_exec = false;
    public static $products_hook_exec;
    public static $footer_hook_exec;

    public function __construct()
    {
        $this->name = 'pdgoogleanalytycs4pro';
        $this->tab = 'advertising_marketing';
        $this->version = '1.2.9';
        $this->author = 'PrestaDev.pl';
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->module_key = 'a4000a48d0c08e3a4fb45aeaa2bdbbd8';

        parent::__construct();

        $this->displayName = $this->l('PD Google Analytycs 4 Pro');
        $this->description = $this->l('Module that places the Google Analytycs 4 code in Your Website with new events tracking');

        $this->secure_key = Tools::encrypt($this->name);

        $this->ga4_id = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_ID'));
        $this->ga4_id2 = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_ID2'));
        $this->ga4_api_secret = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_API_SECRET'));
        $this->ga4_id_aw = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_ID_AW'));
        $this->ga4_id_aw_label = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_ID_AW_LABEL'));
        $this->product_ids_type = Configuration::get('PD_GA4P_PRODUCT_ID_TYPE');
        $this->ga4_send_page_view = Configuration::get('PD_GA4P_GOOGLE_ANAL_SPV');
        $this->os_statuses_refund = !empty(Configuration::get('PD_GA4P_OS_REFUND_ORDER')) ? explode(',', Configuration::get('PD_GA4P_OS_REFUND_ORDER')) : array();
        $this->os_statuses_send = !empty(Configuration::get('PD_GA4P_OS_SEND_ORDER')) ? explode(',', Configuration::get('PD_GA4P_OS_SEND_ORDER')) : array();
        $this->transaction_send_type = Configuration::get('PD_GA4P_TRANSACTION_SEND_TYPE');
        $this->ga4_allow_enhanced_conversion = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_AEC'));

        if (isset($this->context->cookie->id_connections)) {
            $this->getRefererFromIdConnection($this->context->cookie->id_connections);
        }
    }

    public function getRefererFromIdConnection($id_connections)
    {
        $sql = 'SELECT SQL_NO_CACHE `http_referer` FROM `'._DB_PREFIX_.'connections`
            WHERE `id_connections` = '.(int)$id_connections.'
            AND `date_add` > \''.pSQL(date('Y-m-d H:i:00', time() - 1800)).'\'
            '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
            ORDER BY `date_add` DESC';
        if ($referer = Db::getInstance()->getValue($sql, false)) {
            $this->http_referer = $referer;
        }
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('displayAfterBodyOpeningTag') ||
            !$this->registerHook('displayOrderConfirmation') ||
            !$this->registerHook('displayCustomerAccount') ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('actionObjectOrderAddAfter') ||
            !$this->registerHook('actionObjectCartAddAfter') ||
            !$this->registerHook('actionProductSearchAfter') ||
            !$this->registerHook('actionOrderStatusPostUpdate') ||
            !$this->registerHook('displayFooter') ||
            !$this->registerHook('actionCustomerAccountAdd') ||
            !Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_SPV', 1) ||
            !Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_AEC', 1) ||
            !Configuration::updateValue('PD_GA4P_PRODUCT_ID_TYPE', 0) ||
            !Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_ID', '') ||
            !Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_ID2', '') ||
            !Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_API_SECRET', '') ||
            !Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_ID_AW', '') ||
            !Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_ID_AW_LABEL', '') ||
            !Configuration::updateValue('PD_GA4P_OS_REFUND_ORDER', 7) ||
            !Configuration::updateValue('PD_GA4P_OS_SEND_ORDER', '5,11,2') ||
            !Configuration::updateValue('PD_GA4P_TRANSACTION_SEND_TYPE', 1) ||
            !PdGA4PModel::installDB() ||
            !PdGA4PModelCart::installDB() ||
            !PdGA4PRegistrationModel::installDB()
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !PdGA4PModel::uninstallDB() ||
            !PdGA4PModelCart::uninstallDB() ||
            !PdGA4PRegistrationModel::uninstallDB()) {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->postValidation();
            if (!count($this->errors)) {
                $this->postProcess();
            } else {
                foreach ($this->errors as $err) {
                    $this->html .= $this->displayError($err);
                }
            }
        } else {
            $this->html .= '<br />';
        }

        $this->html .= '<h2>'.$this->displayName.' (v'.$this->version.')</h2><p>'.$this->description.'</p>';
        $this->html .= $this->renderForm();
        $this->html .= '<br />';

        return $this->html;
    }

    private function postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue('PD_GA4P_GOOGLE_ANAL_ID')) {
                $this->errors[] = $this->l('You must enter Your Google Analytycs ID.');
            }

            if (Tools::getValue('PD_GA4P_TRANSACTION_SEND_TYPE') == 2 && !Tools::getValue('PD_GA4P_GOOGLE_ANAL_API_SECRET')) {
                $this->errors[] = $this->l('You must enter Your Google Analytics API secret key if You want to send transactions after order status change.');
            }
        }
    }

    private function postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_ID', trim(Tools::getValue('PD_GA4P_GOOGLE_ANAL_ID')));
            Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_ID2', trim(Tools::getValue('PD_GA4P_GOOGLE_ANAL_ID2')));
            Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_ID_AW', trim(Tools::getValue('PD_GA4P_GOOGLE_ANAL_ID_AW')));
            Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_ID_AW_LABEL', trim(Tools::getValue('PD_GA4P_GOOGLE_ANAL_ID_AW_LABEL')));
            Configuration::updateValue('PD_GA4P_PRODUCT_ID_TYPE', Tools::getValue('PD_GA4P_PRODUCT_ID_TYPE'));
            Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_SPV', Tools::getValue('PD_GA4P_GOOGLE_ANAL_SPV'));
            if (is_array(Tools::getValue('PD_GA4P_OS_REFUND_ORDER'))) {
                Configuration::updateValue('PD_GA4P_OS_REFUND_ORDER', implode(',', Tools::getValue('PD_GA4P_OS_REFUND_ORDER')));
            }
            if (is_array(Tools::getValue('PD_GA4P_OS_SEND_ORDER'))) {
                Configuration::updateValue('PD_GA4P_OS_SEND_ORDER', implode(',', Tools::getValue('PD_GA4P_OS_SEND_ORDER')));
            }
            Configuration::updateValue('PD_GA4P_TRANSACTION_SEND_TYPE', Tools::getValue('PD_GA4P_TRANSACTION_SEND_TYPE'));
            Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_API_SECRET', Tools::getValue('PD_GA4P_GOOGLE_ANAL_API_SECRET'));
            Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_AEC', Tools::getValue('PD_GA4P_GOOGLE_ANAL_AEC'));

            $this->ga4_id = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_ID'));
            $this->ga4_id2 = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_ID2'));
            $this->ga4_id_aw = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_ID_AW'));
            $this->product_ids_type = Configuration::get('PD_GA4P_PRODUCT_ID_TYPE');
            $this->ga4_send_page_view = Configuration::get('PD_GA4P_GOOGLE_ANAL_SPV');
            $this->no_delivry_cost = Configuration::get('PD_GA4P_GOOGLE_ANAL_NO_DELIVERY');
            $this->os_statuses_refund = explode(',', Configuration::get('PD_GA4P_OS_REFUND_ORDER'));
            $this->os_statuses_send = explode(',', Configuration::get('PD_GA4P_OS_SEND_ORDER'));
            $this->transaction_send_type = Configuration::get('PD_GA4P_TRANSACTION_SEND_TYPE');
            $this->ga4_api_secret = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_API_SECRET'));
            $this->ga4_allow_enhanced_conversion = htmlspecialchars_decode(Configuration::get('PD_GA4P_GOOGLE_ANAL_AEC'));
        }
        $this->html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    public function renderForm()
    {
        $switch = version_compare(_PS_VERSION_, '1.6.0', '>=') ? 'switch' : 'radio';
        $order_states = OrderState::getOrderStates($this->context->language->id);
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Module configuration'),
                    'icon' => 'icon-cogs'
                ),
                'tabs' => array(
                    'configAnalitycs4' => $this->l('Google Analytics 4 configuration'),
                    'configGA4AdsSale' => $this->l('Google Ads conversion sale configuration'),
                    'configGA4Transactions' => $this->l('Transactions settings'),
                    'configGA4Refunds' => $this->l('Orders refund settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google Analytics ID 1'),
                        'name' => 'PD_GA4P_GOOGLE_ANAL_ID',
                        'desc' => $this->l('Please enter Analytics id 1, example: G-LS3XNT1111, you will find them in the Google Analytics 4 interface in the section: Administration > Data Streams > Select a stream > Measurement ID'),
                        'required' => true,
                        'tab' => 'configAnalitycs4',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google Analytics ID 2'),
                        'name' => 'PD_GA4P_GOOGLE_ANAL_ID2',
                        'desc' => $this->l('Please enter Analytics id 2, for agency purpose or other need to track 2 accounts, example: G-LS3XNT2222, you will find them in the Google Analytics 4 interface in the section: Administration > Data Streams > Select a stream > Measurement ID'),
                        'required' => false,
                        'tab' => 'configAnalitycs4',
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Send page view'),
                        'class' => 't',
                        'name' => 'PD_GA4P_GOOGLE_ANAL_SPV',
                        'desc' => $this->l('Send page view to Google Analytics? if you don’t want the snippet to send a pageview hit to Google Analytic please disable this option'),
                        'tab' => 'configAnalitycs4',
                        'values' => array(
                            array(
                                'id' => 'yes',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'no',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google Adwords Coversion ID'),
                        'name' => 'PD_GA4P_GOOGLE_ANAL_ID_AW',
                        'tab' => 'configGA4AdsSale',
                        'desc' => $this->l('Please enter Google Adwords Conversion ID id You want to track Adwords Conversions, this field is optional use when needed, example: AW-LS3XNT2222'),
                        'required' => false
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google Adwords Coversion Label'),
                        'name' => 'PD_GA4P_GOOGLE_ANAL_ID_AW_LABEL',
                        'tab' => 'configGA4AdsSale',
                        'desc' => $this->l('Enter your label for that conversion (alphanumeric code generated by Google in event snipet ex. AW-123456789/1A234C56789 where string after "/" which is 1A234C56789 are conversion label)'),
                        'required' => false
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Product identifier'),
                        'name' => 'PD_GA4P_PRODUCT_ID_TYPE',
                        'tab' => 'configAnalitycs4',
                        'class' => 'fixed-width-xxl',
                        'desc' => $this->l('You can choose which product identifier we want to pass as a item_id to Google Analitycs, if must match Your feed products identifiers'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => '0',
                                    'name' => $this->l('Id product (default)')
                                ),
                                array(
                                    'id' => '1',
                                    'name' => $this->l('Id product-id product attribute')
                                ),
                                array(
                                    'id' => '2',
                                    'name' => $this->l('Id product_id product attribute')
                                ),
                                array(
                                    'id' => '3',
                                    'name' => $this->l('Product reference')
                                ),
                                array(
                                    'id' => '4',
                                    'name' => $this->l('Product EAN')
                                ),

                            ),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('How to send order transaction / order details to GA4'),
                        'class' => 't',
                        'name' => 'PD_GA4P_TRANSACTION_SEND_TYPE',
                        'tab' => 'configGA4Transactions',
                        'desc' => $this->l('Please select if module should send transaction to Google Analitycs 4 on order confirmation page when customer get back from payment gateway or on selected order status change'),
                        'values' => array(
                            array(
                                'id' => '1',
                                'value' => 1,
                                'label' => $this->l('Send on order confirmation page')
                            ),
                            array(
                                'id' => '2',
                                'value' => 2,
                                'label' => $this->l('Send on order status change')
                            ),
                            array(
                                'id' => '3',
                                'value' => 3,
                                'label' => $this->l('Send on order confirmation button click')
                            ),
                        )
                    ),
                    array(
                        'type' => 'text',
                        'class' => 'fixed-width-xxl',
                        'label' => $this->l('Google Analytics 4 API Secret'),
                        'name' => 'PD_GA4P_GOOGLE_ANAL_API_SECRET',
                        'desc' => $this->l('Please enter Analytics 4 API secret key, you will find API secret in the Google Analytics 4 interface in the section:: Administration > Data Streams > select a stream > Measurement Protocol > Create, option is used for php transactions tracking on order status change'),
                        'required' => false,
                        'tab' => 'configGA4Transactions',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Order statuses to send order'),
                        'desc' => $this->l('Order statuses to send order to Google Analytics on order status change (orders are send in by PHP)'),
                        'name' => 'PD_GA4P_OS_SEND_ORDER',
                        'tab' => 'configGA4Transactions',
                        'class' => 'fixed-width-xxl',
                        'multiple' => true,
                        'required' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Allow enhanced conversions'),
                        'class' => 't',
                        'name' => 'PD_GA4P_GOOGLE_ANAL_AEC',
                        'desc' => $this->l('Allow enhanced conversions by adding user data to events like: phone,, email address'),
                        'tab' => 'configGA4Transactions',
                        'values' => array(
                            array(
                                'id' => 'yes',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'no',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Order statuses for refund order'),
                        'desc' => $this->l('Order statuses for refund order in Google Analytics 4 on order status change'),
                        'name' => 'PD_GA4P_OS_REFUND_ORDER',
                        'tab' => 'configOtherOptions',
                        'required' => true,
                        'class' => 'fixed-width-xxl',
                        'tab' => 'configGA4Refunds',
                        'multiple' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'name' => 'btnSubmit',
                    'title' => $this->l('Save settings'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $admin_link = $this->context->link->getAdminLink('AdminModules', false);
        $helper->currentIndex = $admin_link.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function getConfigFieldsValues()
    {
        $return = array();
        $return['PD_GA4P_GOOGLE_ANAL_ID'] = htmlspecialchars(Tools::getValue('PD_GA4P_GOOGLE_ANAL_ID', Configuration::get('PD_GA4P_GOOGLE_ANAL_ID')));
        $return['PD_GA4P_GOOGLE_ANAL_ID2'] = htmlspecialchars(Tools::getValue('PD_GA4P_GOOGLE_ANAL_ID2', Configuration::get('PD_GA4P_GOOGLE_ANAL_ID2')));
        $return['PD_GA4P_GOOGLE_ANAL_ID_AW'] = htmlspecialchars(Tools::getValue('PD_GA4P_GOOGLE_ANAL_ID_AW', Configuration::get('PD_GA4P_GOOGLE_ANAL_ID_AW')));
        $return['PD_GA4P_GOOGLE_ANAL_ID_AW_LABEL'] = htmlspecialchars(Tools::getValue('PD_GA4P_GOOGLE_ANAL_ID_AW_LABEL', Configuration::get('PD_GA4P_GOOGLE_ANAL_ID_AW_LABEL')));
        $return['PD_GA4P_PRODUCT_ID_TYPE'] = Tools::getValue('PD_GA4P_PRODUCT_ID_TYPE', Configuration::get('PD_GA4P_PRODUCT_ID_TYPE'));
        $return['PD_GA4P_GOOGLE_ANAL_SPV'] = htmlspecialchars(Tools::getValue('PD_GA4P_GOOGLE_ANAL_SPV', Configuration::get('PD_GA4P_GOOGLE_ANAL_SPV')));
        $return['PD_GA4P_OS_REFUND_ORDER[]'] = Tools::getValue('PD_GA4P_OS_REFUND_ORDER', explode(',', Configuration::get('PD_GA4P_OS_REFUND_ORDER')));
        $return['PD_GA4P_TRANSACTION_SEND_TYPE'] = htmlspecialchars(Tools::getValue('PD_GA4P_TRANSACTION_SEND_TYPE', Configuration::get('PD_GA4P_TRANSACTION_SEND_TYPE')));
        $return['PD_GA4P_GOOGLE_ANAL_API_SECRET'] = htmlspecialchars(Tools::getValue('PD_GA4P_GOOGLE_ANAL_API_SECRET', Configuration::get('PD_GA4P_GOOGLE_ANAL_API_SECRET')));
        $return['PD_GA4P_OS_SEND_ORDER[]'] = Tools::getValue('PD_GA4P_OS_SEND_ORDER', explode(',', Configuration::get('PD_GA4P_OS_SEND_ORDER')));
        $return['PD_GA4P_GOOGLE_ANAL_AEC'] = htmlspecialchars(Tools::getValue('PD_GA4P_GOOGLE_ANAL_AEC', Configuration::get('PD_GA4P_GOOGLE_ANAL_AEC')));
        return $return;
    }

    public static function getControlerName()
    {
        $page_name = 'index';
        if (!empty(Context::getContext()->controller->php_self)) {
            $page_name = (string)Context::getContext()->controller->php_self;
        } else {
            $page_name = (string)Tools::getValue('controller');
        }
        return $page_name;
    }

    public static function getModuleNameFromPost()
    {
        $module_name = '';
        $module_name = (string)Tools::getValue('module');
        return $module_name;
    }

    public function getProductIdStringByType($product, $id_product_attribute = false)
    {
        $id_lang = (int)$this->context->language->id;
        $ids_type = $this->product_ids_type;

        if ($product instanceof Product) {
            if (isset($product->id)) {
                $id_product = (int)$product->id;
            } else {
                $id_product = (int)$product->id_product;
            }
            $ean13 = (string)$product->ean13;
            $reference = (string)$product->reference;
            if (!is_numeric($id_product_attribute)) {
                $id_product_attribute = (int)$product->cache_default_attribute;
            }



            if (is_numeric($id_product_attribute)) {
                $attribute_combination_resume = $product->getAttributeCombinationsById($id_product_attribute, $id_lang, true);

                if ($attribute_combination_resume && is_array($attribute_combination_resume) && sizeof($attribute_combination_resume)) {
                    if (isset($attribute_combination_resume['ean13'])) {
                        $ean13 = (string)$attribute_combination_resume['ean13'];
                    }
                    if (isset($attribute_combination_resume['reference'])) {
                        $reference = (string)$attribute_combination_resume['reference'];
                    }
                }
            }
        } elseif (is_array($product)) {
            $id_product = (int)$product['id_product'];
            $ean13 = (string)$product['ean13'];
            $reference = (string)$product['reference'];
            if (!$id_product_attribute && isset($product['id_product_attribute'])) {
                $id_product_attribute = (int)$product['id_product_attribute'];
            }

            if (!$id_product_attribute && isset($product['product_attribute_id'])) {
                $id_product_attribute = (int)$product['product_attribute_id'];
            }

            if ($id_product_attribute) {
                $product = new Product($id_product);
                $attribute_combination_resume = $product->getAttributeCombinationsById($id_product_attribute, $id_lang, true);
                if ($attribute_combination_resume && sizeof($attribute_combination_resume)) {
                    $ean13 = (string)$attribute_combination_resume[0]['ean13'];
                    $reference = (string)$attribute_combination_resume[0]['reference'];
                }
            }
        } else {
            if (isset($product->id)) {
                $id_product = (int)$product->id;
            } else {
                $id_product = (int)$product->id_product;
            }

            $ean13 = (string)$product->ean13;
            $reference = (string)$product->reference;
            if (isset($product->cache_default_attribute)) {
                $id_product_attribute = (int)$product->cache_default_attribute;
            }
        }

        $ecomm_prodid = '';
        switch ($ids_type) {
            case '0':
                $ecomm_prodid = $id_product;
                break;
            case '1':
                if ($id_product_attribute &&  ($id_product_attribute != 0)) {
                    $ecomm_prodid = $id_product.'-'.$id_product_attribute;
                } elseif ($id_product_attribute == 0) {
                    $ecomm_prodid = $id_product;
                }
                break;
            case '2':
                if ($id_product_attribute &&  ($id_product_attribute != 0)) {
                    $ecomm_prodid = $id_product.'_'.$id_product_attribute;
                } elseif ($id_product_attribute == 0) {
                    $ecomm_prodid = $id_product;
                }
                break;
            case '3':
                $ecomm_prodid = $reference;
                break;
            case '4':
                $ecomm_prodid = $ean13;
                break;
            default:
                $ecomm_prodid = $id_product;
                break;
        }
        return $ecomm_prodid;
    }

    public function hookDisplayHeader($params)
    {
        if (!empty($this->ga4_id)) {
            Media::addJsDef(array(
                'pdgoogleanalytycs4pro_secure_key' => $this->secure_key,
                'pd_google_analitycs_controller' => self::getControlerName(),
                'pdgoogleanalytycs4pro_ajax_link' => $this->context->link->getModuleLink('pdgoogleanalytycs4pro', 'ajax', array()),
            ));

            $this->context->controller->registerJavascript(
                'modules-pdgoogleanalytycs4pro-front',
                'modules/'.$this->name.'/views/js/scripts_17.js',
                array('position' => 'bottom', 'priority' => 1)
            );

            $country_iso = $this->context->country->iso_code;
            $currency_iso = $this->context->currency->iso_code;

            $order = false;

            if (isset($this->context->cart->id_address_delivery)
                && is_numeric($this->context->cart->id_address_delivery)) {
                $address = new Address((int)$this->context->cart->id_address_delivery);
                $country = new Country($address->id_country);
                $address_country_iso = $country->iso_code;

                $address_phone = isset($address->phone) ? $address->phone : $address->phone_mobile;
                if (!empty($address_phone)) {
                    try {
                        $phoneUtil = PhoneNumberUtil::getInstance();
                        $numberPrototype = $phoneUtil->parse($address_phone, $address_country_iso);
                        $address_phone = $phoneUtil->format($numberPrototype, PhoneNumberFormat::E164);
                    } catch (NumberParseException $e) {
                        // handle any errors, sems to be not needed here
                    }
                }
                $address_email = trim($this->context->customer->email);
                $address_firstname = trim($address->firstname);
                $address_lastname = trim($address->lastname);
                $address_street =  trim($address->address1.' '.$address->address2);
                $address_city = trim($address->city);
                $address_postcode = trim($address->postcode);
            } elseif ($order = $this->tryToGetOrderObjectFromParams($params)) {
                if ($order) {
                    $address = new Address((int)$order->id_address_delivery);
                    $country = new Country($address->id_country);
                    $address_country_iso = $country->iso_code;

                    $address_phone = isset($address->phone) ? trim($address->phone) : trim($address->phone_mobile);
                    if (!empty($address_phone)) {
                        try {
                            $phoneUtil = PhoneNumberUtil::getInstance();
                            $numberPrototype = $phoneUtil->parse($address_phone, $address_country_iso);
                            $address_phone = $phoneUtil->format($numberPrototype, PhoneNumberFormat::E164);
                        } catch (NumberParseException $e) {
                            // handle any errors, sems to be not needed here
                        }
                    }
                    $address_email = trim($this->context->customer->email);
                    $address_firstname = trim($address->firstname);
                    $address_lastname = trim($address->lastname);
                    $address_street =  trim($address->address1.' '.$address->address2);
                    $address_city = trim($address->city);
                    $address_postcode = trim($address->postcode);
                }
            }

            $this->smarty->assign(array(
                'pd_google_analytics_id' => $this->ga4_id,
                'pd_google_analytics_id2' => $this->ga4_id2,
                'pd_google_analytics_id_aw' => $this->ga4_id_aw,
                'pd_google_analytics_id_aw_label' => $this->ga4_id_aw_label,
                'pd_google_analytics_currency_iso' => $currency_iso,
                'pd_google_analytics_country_iso' => $country_iso,
                'pd_google_analytics_spv' => $this->ga4_send_page_view ? 'true' : 'false',
                'pd_google_analytics_aec' => $this->ga4_allow_enhanced_conversion ? 'true' : 'false',
                'pd_google_analytics_email' => (isset($address_email) && !empty($address_email)) ? addslashes($address_email) : '',
                'pd_google_analytics_phone' => (isset($address_phone) && !empty($address_phone)) ? addslashes($address_phone) : '',
                'pd_google_analytics_firstname' => (isset($address_firstname) && !empty($address_firstname)) ? addslashes($address_firstname) : '',
                'pd_google_analytics_lastname' => (isset($address_lastname) && !empty($address_lastname)) ? addslashes($address_lastname) : '',
                'pd_google_analytics_street' => (isset($address_street) && !empty($address_street)) ? addslashes($address_street) : '',
                'pd_google_analytics_postcode' => (isset($address_postcode) && !empty($address_postcode)) ? addslashes($address_postcode) : '',
                'pd_google_analytics_city' => (isset($address_city) && !empty($address_city)) ? addslashes($address_city) : '',
                'pd_google_analytics_country' => (isset($address_country_iso) && !empty($address_country_iso)) ? addslashes($address_country_iso) : ''
            ));

            return $this->display(__FILE__, 'displayHeader.tpl');
        }
    }

    public function getCartRuleWithCoupon($id_cart = false)
    {
        $cart_rules = false;
        if ($id_cart) {
            $cart = new Cart($id_cart);
            $cart_rules = $cart->getCartRules(CartRule::FILTER_ACTION_ALL, false);
        } elseif (isset($this->context->cart->id)) {
            $cart_rules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_ALL, false);
        }

        $out = array();
        if ($cart_rules) {
            foreach ($cart_rules as $cr) {
                $out['name'] = $cr['name'];
                $out['code'] = $cr['code'];
            }
        }
        return $out;
    }

    public function getCategoryPath($id_category)
    {
        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? (int)$this->context->shop->id : false;
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $category = new Category((int)$id_category, $id_lang, $id_shop);
        $parent = new Category($category->id_parent, $id_lang, $id_shop);
        while (Validate::isLoadedObject($category) && Validate::isLoadedObject($parent) && $category->id_parent > 1 && $category->id_parent != Category::getRootCategory()->id) {
            return $this->getCategoryPath($category->id_parent).' / '.$category->name;
        }
        return $category->name;
    }

    public function hookDisplayAfterBodyOpeningTag($params)
    {
        if (self::$footer_hook_exec == false) {
            return $this->hookDisplayFooter($params);
        }
    }

    public function hookDisplayFooter($params)
    {
        if (empty($this->ga4_id)) {
            return;
        }

        if (self::$footer_hook_exec) {
            return;
        }
        self::$footer_hook_exec = true;

        $cn = self::getControlerName();
        $mn = self::getModuleNameFromPost();
        $cart_rules = $this->getCartRuleWithCoupon();
        $currency_iso = (string)$this->context->currency->iso_code;
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_shop = (int)$this->context->shop->id;

        // determine if user loged in
        $id_customer = false;
        if (isset($this->context->customer->id)) {
            $id_customer = (int)$this->context->customer->id;
            $this->context->smarty->assign('account_created', 0);
            if (isset($this->context->customer->id)) {
                $isRegistered = PdGA4PRegistrationModel::getRegisteredValueByIdCustomer($id_customer);
                if ($isRegistered) {
                    $this->context->smarty->assign('account_created', 1);
                    $this->context->smarty->assign('registration_content_name', addslashes(Configuration::get('PS_SHOP_NAME')));
                    $obj = new PdGA4PRegistrationModel($id_customer);
                    $obj->registered_send = 1;
                    $obj->update();
                }
            }
        }

        switch ($mn) {

            case 'steasycheckout':
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }
                    $cart_products = $cart->getProducts();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                        $cp['content_ids'] = $this->getProductIdStringByType($cp);
                        $cp['content_coupon'] = sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';

                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);

                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }

                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);

                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }
                    //dump($cart_products);
                    $this->smarty->assign(array(
                        'content_products' => $cart_products,
                        'content_value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'tagType' => 'order',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
            break;
        }

        switch ($cn) {

            case 'product':

                $id_product = (int)Tools::getValue('id_product');
                if (!is_numeric($id_product)) {
                    return;
                }

                $product = new Product($id_product, false, $id_lang);
                if (!isset($product->id)) {
                    return;
                }

                $id_product_attribute = (int)Tools::getValue('id_product_attribute');
                $price = Product::getPriceStatic($product->id, true, $id_product_attribute, 6, null, false, true);
                $price_old = Product::getPriceStatic($product->id, true, $id_product_attribute, 6, null, false, false);
                $discount = 0;
                $discount = $price_old - $price;
                $variant = '';

                if ($id_product_attribute) {
                    $attribute_combination_resume = $product->getAttributeCombinationsById($id_product_attribute, $id_lang, true);
                    if ($attribute_combination_resume) {
                        foreach ($attribute_combination_resume as $acr) {
                            $variant .= $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $variant = mb_substr($variant, 0, -3);
                    }
                }

                if (!empty($variant)) {
                    $product_name = addslashes($product->name.' ('.$variant.')');
                } else {
                    $product_name = addslashes($product->name);
                }

                $content_category = explode('/', $this->getCategoryPath($product->id_category_default));
                $content_category = array_map('trim', $content_category);

                $this->smarty->assign(array(
                    'item_list_id' => $cn,
                    'item_list_name' => $cn,
                    'content_manufacturer' => $product->id_manufacturer ? addslashes(Manufacturer::getNameById($product->id_manufacturer)) : '',
                    'content_ids' =>  $this->getProductIdStringByType($product, $id_product_attribute),
                    'content_name' =>  $product_name,
                    'content_category' => isset($content_category[0]) ? addslashes($content_category[0]) : '',
                    'content_category2' => isset($content_category[1]) ? addslashes($content_category[1]) : '',
                    'content_category3' => isset($content_category[2]) ? addslashes($content_category[2]) : '',
                    'content_category4' => isset($content_category[3]) ? addslashes($content_category[3]) : '',
                    'content_category5' => isset($content_category[4]) ? addslashes($content_category[4]) : '',
                    'content_discount' => Tools::ps_round($discount, 2),
                    'content_variant' => $variant,
                    'content_value' => Tools::ps_round($price, 2),
                    'currency' => $currency_iso,
                    'http_referer' => $this->http_referer,
                    'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                    'tagType' => 'product',

                ));

                return $this->display(__FILE__, 'displayFooter.tpl');

            case 'category':

                $id_category = (int)Tools::getValue('id_category');
                $category = new Category($id_category, $id_lang, $id_shop);
                $category_products = self::$products_hook_exec;

                $content_category = explode('/', $this->getCategoryPath($id_category));
                $content_category = array_map('trim', $content_category);

                foreach ($category_products as &$cp) {
                    $cp['content_ids'] = $this->getProductIdStringByType($cp);
                    $cp['manufacturer'] = $cp['id_manufacturer'] ? addslashes(Manufacturer::getNameById($cp['id_manufacturer'])) : '';
                    $cp['content_coupon'] = sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                    $cp['discount'] = 0;
                    $cp['discount'] = Tools::ps_round($cp['price_without_reduction'] - $cp['price_amount'], 2);
                    $product = new Product($cp['id_product'], false, $id_lang);
                    $attribute_combination_resume = $product->getAttributeCombinationsById($cp['cache_default_attribute'], $id_lang, true);
                    if ($attribute_combination_resume) {
                        $cp['variant'] = '';
                        foreach ($attribute_combination_resume as $acr) {
                            $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                    }

                    if (!empty($cp['variant'])) {
                        $cp['product_name'] = addslashes($product->name.' ('.$cp['variant'].')');
                    } else {
                        $cp['product_name'] = addslashes($product->name);
                    }

                    $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_amount'], 2);

                    $cp['item_list_id'] = $cn;
                    $cp['item_list_name'] = $cn;
                }

                if (isset($category->id)) {
                    $this->smarty->assign(array(
                        'product_ids_type' => $this->product_ids_type,
                        'content_name' => 'category',
                        'content_category' => isset($content_category[0]) ? addslashes($content_category[0]) : '',
                        'content_category2' => isset($content_category[1]) ? addslashes($content_category[1]) : '',
                        'content_category3' => isset($content_category[2]) ? addslashes($content_category[2]) : '',
                        'content_category4' => isset($content_category[3]) ? addslashes($content_category[3]) : '',
                        'content_category5' => isset($content_category[4]) ? addslashes($content_category[4]) : '',
                        'content_products' => $category_products,
                        'currency' => $currency_iso,
                        'page' => $cn,
                        'content_type' => 'product',
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'tagType' => 'category',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'prices-drop':

                $pd_products = self::$products_hook_exec;

                foreach ($pd_products as &$cp) {
                    $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                    $content_category = array_map('trim', $content_category);

                    $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                    $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                    $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                    $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                    $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';

                    $cp['content_ids'] = $this->getProductIdStringByType($cp);
                    $cp['manufacturer'] = $cp['id_manufacturer'] ? addslashes(Manufacturer::getNameById($cp['id_manufacturer'])) : '';
                    $cp['content_coupon'] = sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                    $cp['discount'] = 0;
                    $cp['discount'] = Tools::ps_round($cp['price_without_reduction'] - $cp['price_amount'], 2);
                    $product = new Product($cp['id_product'], false, $id_lang);
                    $attribute_combination_resume = $product->getAttributeCombinationsById($cp['cache_default_attribute'], $id_lang, true);
                    if ($attribute_combination_resume) {
                        $cp['variant'] = '';
                        foreach ($attribute_combination_resume as $acr) {
                            $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                    }

                    if (!empty($cp['variant'])) {
                        $cp['product_name'] = addslashes($product->name.' ('.$cp['variant'].')');
                    } else {
                        $cp['product_name'] = addslashes($product->name);
                    }

                    $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_amount'], 2);

                    $cp['item_list_id'] = $cn;
                    $cp['item_list_name'] = $cn;
                }

                if (is_array($pd_products) && sizeof($pd_products)) {
                    $this->smarty->assign(array(
                        'product_ids_type' => $this->product_ids_type,
                        'content_name' => 'prices-drop',
                        'content_products' => $pd_products,
                        'currency' => $currency_iso,
                        'page' => $cn,
                        'content_type' => 'product',
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'tagType' => 'prices-drop',
                    ));

                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'new-products':

                $new_products = self::$products_hook_exec;
                foreach ($new_products as &$cp) {
                    $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                    $content_category = array_map('trim', $content_category);

                    $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                    $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                    $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                    $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                    $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';

                    $cp['content_ids'] = $this->getProductIdStringByType($cp);
                    $cp['manufacturer'] = $cp['id_manufacturer'] ? addslashes(Manufacturer::getNameById($cp['id_manufacturer'])) : '';
                    $cp['content_coupon'] = sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                    $cp['discount'] = 0;
                    $cp['discount'] = Tools::ps_round($cp['price_without_reduction'] - $cp['price_amount'], 2);
                    $product = new Product($cp['id_product'], false, $id_lang);
                    $attribute_combination_resume = $product->getAttributeCombinationsById($cp['cache_default_attribute'], $id_lang, true);
                    if ($attribute_combination_resume) {
                        $cp['variant'] = '';
                        foreach ($attribute_combination_resume as $acr) {
                            $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                    }

                    if (!empty($cp['variant'])) {
                        $cp['product_name'] = addslashes($product->name.' ('.$cp['variant'].')');
                    } else {
                        $cp['product_name'] = addslashes($product->name);
                    }

                    $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_amount'], 2);

                    $cp['item_list_id'] = $cn;
                    $cp['item_list_name'] = $cn;
                }

                if (is_array($new_products) && sizeof($new_products)) {
                    $this->smarty->assign(array(
                        'product_ids_type' => $this->product_ids_type,
                        'content_name' => 'new-products',
                        'content_products' => $new_products,
                        'currency' => $currency_iso,
                        'page' => $cn,
                        'content_type' => 'product',
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'tagType' => 'new-products',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'best-sales':

                $best_sales_products = self::$products_hook_exec;

                foreach ($best_sales_products as &$cp) {
                    $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                    $content_category = array_map('trim', $content_category);

                    $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                    $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                    $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                    $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                    $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';

                    $cp['content_ids'] = $this->getProductIdStringByType($cp);
                    $cp['manufacturer'] = $cp['id_manufacturer'] ? addslashes(Manufacturer::getNameById($cp['id_manufacturer'])) : '';
                    $cp['content_coupon'] = sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                    $cp['discount'] = 0;
                    $cp['discount'] = Tools::ps_round($cp['price_without_reduction'] - $cp['price_amount'], 2);
                    $product = new Product($cp['id_product'], false, $id_lang);
                    $attribute_combination_resume = $product->getAttributeCombinationsById($cp['cache_default_attribute'], $id_lang, true);
                    if ($attribute_combination_resume) {
                        $cp['variant'] = '';
                        foreach ($attribute_combination_resume as $acr) {
                            $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                    }

                    if (!empty($cp['variant'])) {
                        $cp['product_name'] = addslashes($product->name.' ('.$cp['variant'].')');
                    } else {
                        $cp['product_name'] = addslashes($product->name);
                    }

                    $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_amount'], 2);

                    $cp['item_list_id'] = $cn;
                    $cp['item_list_name'] = $cn;
                }

                if (is_array($best_sales_products) && sizeof($best_sales_products)) {
                    $this->smarty->assign(array(
                        'product_ids_type' => $this->product_ids_type,
                        'content_name' => 'best-sales',
                        'content_products' => $best_sales_products,
                        'currency' => $currency_iso,
                        'page' => $cn,
                        'content_type' => 'product',
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'tagType' => 'best-sales',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'cart':
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }

                    $cart_products = $cart->getProducts();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';

                        $cp['content_ids'] = $this->getProductIdStringByType($cp);
                        $cp['content_coupon'] = sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';

                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);

                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }
                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);
                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }

                    //dump($cart_products);
                    //die();


                    $this->smarty->assign(array(
                        'content_products' => $cart_products,
                        'content_value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'tagType' => 'cart',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'order':
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }
                    $cart_products = $cart->getProducts();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                        $cp['content_ids'] = $this->getProductIdStringByType($cp);
                        $cp['content_coupon'] = sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);
                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }
                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);

                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }

                    //dump($cart_products);
                    //die();


                    //dump($cart_products);
                    $this->smarty->assign(array(
                        'content_products' => $cart_products,
                        'content_value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'tagType' => 'order',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'order-opc':
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }
                    $cart_products = $cart->getProducts();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                        $cp['content_ids'] = $this->getProductIdStringByType($cp);
                        $cp['content_coupon'] = sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);
                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }
                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);

                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }

                    $this->smarty->assign(array(
                        'content_products' => $cart_products,
                        'content_value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'tagType' => 'order',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;


            case 'onepagecheckout':
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }
                    $cart_products = $cart->getProducts();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                        $cp['content_ids'] = $this->getProductIdStringByType($cp);
                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);
                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }
                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);

                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }

                    $this->smarty->assign(array(
                        'content_products' => $cart_products,
                        'content_value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'http_referer' => $this->http_referer,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'tagType' => 'order',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'onepagecheckoutps':
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }
                    $cart_products = $cart->getProducts();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                        $cp['content_ids'] = $this->getProductIdStringByType($cp);
                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);
                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }

                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);

                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }
                    $this->smarty->assign(array(
                        'content_products' => $cart_products,
                        'content_value' => Tools::ps_round($value, 2),
                        'currency' => $currency_iso,
                        'http_referer' => $this->http_referer,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'tagType' => 'order',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'thecheckout':
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }
                    $cart_products = $cart->getProducts();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                        $cp['content_ids'] = $this->getProductIdStringByType($cp);
                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);
                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }

                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);

                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }

                    $this->smarty->assign(array(
                        'content_products' => $cart_products,
                        'content_value' => Tools::ps_round($value, 2),
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'currency' => $currency_iso,
                        'http_referer' => $this->http_referer,
                        'tagType' => 'order',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'supercheckout':
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }
                    $cart_products = $cart->getProducts();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $this->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                        $cp['content_ids'] = $this->getProductIdStringByType($cp);
                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);

                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }

                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);
                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }
                    $this->smarty->assign(array(
                        'content_products' => $cart_products,
                        'content_value' => Tools::ps_round($value, 2),
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'currency' => $currency_iso,
                        'http_referer' => $this->http_referer,
                        'tagType' => 'order',
                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'search':
                $search_string = Tools::getValue('s');
                if (!empty($search_string)) {
                    $this->smarty->assign(array(
                        'item_list_id' => $cn,
                        'item_list_name' => $cn,
                        'tagType' => 'search',
                        'currency' => $currency_iso,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'search_string' => pSQL($search_string),

                    ));
                    return $this->display(__FILE__, 'displayFooter.tpl');
                }
                break;

            case 'cms':
                $id_cms = (int)Tools::getValue('id_cms');
                if ($id_cms) {
                    $cms = new Cms($id_cms, $id_lang);
                    if ($cms instanceof CMS) {
                        $cms_category = new CmsCategory($cms->id_cms_category);

                        if ($cms_category instanceof CmsCategory) {
                            $this->smarty->assign(array(
                                'item_list_id' => $cn,
                                'item_list_name' => $cn,
                                'tagType' => 'cms',
                                'content_ids' => $id_cms,
                                'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                                'http_referer' => $this->http_referer,
                                'content_name' => addslashes($cms->meta_title),
                            ));
                            return $this->display(__FILE__, 'displayFooter.tpl');
                        }
                    }
                }
                break;

            default:
                $other_actions_html = '';
                // $other_pages = array('pagenotfound', 'contact', 'index', 'manufacturer', 'new-products', 'password',
                //     'sitemap', 'supplier', 'address', 'addresses', 'authentication', 'discount', 'history', 'identity',
                //     'my-account', 'order-follow', 'order-slip', 'stores', 'guest-tracking'
                // );
                // if (in_array($cn, $other_pages)) {
                //     $this->smarty->assign(array(
                //         'tagType' => 'other',
                //         'content_ids' => $cn,
                //         'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                //         'http_referer' => $this->http_referer,
                //         'content_name' => addslashes('Page viewed: '.$cn),
                //     ));
                //     $other_actions_html .=  $this->display(__FILE__, 'displayFooter.tpl');
                // }

                // try to fire up event of orderConfirmation if not executed on normal hook call
                if (self::$order_confirmation_exec == false) {
                    $other_actions_html .= $this->hookDisplayOrderConfirmation($params);
                }
                return $other_actions_html;
        }
    }

    public function tryToGetOrderObjectFromParams($params)
    {
        $id_cart = false;
        $id_order = false;
        $order = false;

        if (isset($params['objOrder'])) {
            $order = $params['objOrder'];
            $id_order = $order->id;
        } elseif (isset($params['order'])) {
            $order = $params['order'];
            $id_order = $order->id;
        } elseif (Tools::getValue('id_order')) {
            $id_order = (int)Tools::getValue('id_order');
            if (is_numeric($id_order)) {
                $order = new Order($id_order);
            }
        } elseif (Tools::getValue('id')) {
            $id_order = Tools::getValue('id');
            $id_order = strstr($id_order, '-', true);
            $order = new Order($id_order);
        } elseif (Tools::getValue('id_cart')) {
            $id_cart = (int)Tools::getValue('id_cart');
            $id_order = Order::getOrderByCartId($id_cart);
            $order = new Order($id_order);
        }

        if ($order == false) {
            if (isset($this->context->customer->id)) {
                $id_order = PdGA4PModel::getLastOrderIdByIdCustomer($this->context->customer->id);
                if ($id_order && is_numeric($id_order)) {
                    $order = new Order($id_order);
                }
            }
        }

        if (Validate::isLoadedObject($order)) {
            return $order;
        } else {
            return false;
        }
    }

    public function hookDisplayOrderConfirmation($params)
    {
        if ($this->transaction_send_type == 1) {
            if (!empty($this->ga4_id)) {
                $order = $this->tryToGetOrderObjectFromParams($params);

                if ($order == false) {
                    return;
                }

                $id_order = $order->id;
                $cn = self::getControlerName();

                $obj = new PdGA4PModel($id_order);
                if (isset($obj->id_order) && !$obj->order_send) {
                    $cart_rules = $this->getCartRuleWithCoupon((int)$order->id_cart);
                    $cart = new Cart((int)$order->id_cart);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);

                    $order_value = $order->total_paid - $order->total_shipping_tax_incl;
                    $order_tax = ($order->total_paid_tax_incl - $order->total_paid_tax_excl) - ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl);
                    $shipping_cost = $order->total_shipping_tax_incl;

                    $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
                    $id_shop = (int)$this->context->shop->id;
                    $id_currency = $order->id_currency;
                    $currency = new Currency($id_currency);
                    $currency_iso = $currency->iso_code;
                    $order_products = $order->getProducts();

                    foreach ($order_products as &$op) {
                        $price_old = Product::getPriceStatic($op['id_product'], true, $op['product_attribute_id'], 6, null, false, false);
                        $content_category = explode('/', $this->getCategoryPath($op['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $op['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $op['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $op['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $op['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $op['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                        $op['content_ids'] = $this->getProductIdStringByType($op);
                        $op['content_coupon'] = sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                        $discount = 0;
                        $discount = $price_old - $op['product_price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $op['price_old'] = $price_old;
                        $op['discount'] = Tools::ps_round($discount, 2);

                        $op['price'] = Tools::ps_round($op['unit_price_tax_incl'], 2);
                        $op['manufacturer'] = $op['id_manufacturer'] ? addslashes(Manufacturer::getNameById($op['id_manufacturer'])) : '';
                        $product = new Product($op['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($op['product_attribute_id'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $op['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $op['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $op['variant'] = mb_substr($op['variant'], 0, -3);
                        }
                        $op['item_list_id'] = $cn;
                        $op['item_list_name'] = $cn;

                        if (!empty($op['variant'])) {
                            $op['product_name'] = addslashes($product->name.' ('.$op['variant'].')');
                        } else {
                            $op['product_name'] = addslashes($product->name);
                        }
                    }

                    //dump($order_products);
                    //die();


                    $this->smarty->assign(array(
                        'pd_google_analytics_id_aw' => $this->ga4_id_aw,
                        'pd_google_analytics_id_aw_label' => $this->ga4_id_aw_label,
                        'content_transaction_id' =>  $order->reference,
                        'content_shipping' => Tools::ps_round($shipping_cost, 2),
                        'content_value' => Tools::ps_round($order_value, 2),
                        'content_tax' => Tools::ps_round($order_tax, 2),
                        'content_products' => $order_products,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'http_referer' => $this->http_referer,
                        'currency' => $currency_iso,
                    ));

                    $obj->order_send = 1;
                    $obj->date_upd = date('Y-m-d H:i:s');
                    $obj->update();

                    self::$order_confirmation_exec = true;
                    return $this->display(__FILE__, 'displayOrderConfirmation.tpl');
                }
            }
        }
    }

    public function hookActionProductSearchAfter($params)
    {
        if (isset($params['products'])) {
            self::$products_hook_exec = $params['products'];
        }
    }

    public function hookDisplayCustomerAccount($params)
    {
        return $this->display(__FILE__, 'displayCustomerAccount.tpl');
    }

    public function getCarriersArray()
    {
        $sql = '
        SELECT c.`id_carrier`, c.`name`
        FROM `' . _DB_PREFIX_ . 'carrier` c
        ' . Shop::addSqlAssociation('carrier', 'c') . '
        WHERE c.`deleted` = 0';

        $carriers = Db::getInstance()->executeS($sql);
        $out = array();
        foreach ($carriers as $carrier) {
            if ($carrier['name'] == '0') {
                $out[$carrier['id_carrier']] = Carrier::getCarrierNameFromShopName();
            } else {
                $out[$carrier['id_carrier']] = $carrier['name'];
            }
        }

        return $out;
    }

    public function getGAClientId()
    {
        if (isset($_COOKIE['_ga'])) {
            $ga_client_id = $_COOKIE['_ga'];
            $lenght = strlen($ga_client_id);
            $ga_client_id = substr($ga_client_id, 6, $lenght);
            return $ga_client_id;
        }
        return '';
    }

    public function hookActionObjectOrderAddAfter($params)
    {
        $order = isset($params['object']) ? $params['object'] : false;
        if ($order && Validate::isLoadedObject($order)) {
            $obj = new PdGA4PModel();
            $obj->id_order = (int)$order->id;
            $obj->client_id = $this->getGAClientId();
            $obj->session_id = $this->getGASessionId();
            $obj->order_send = 0;
            $obj->refund_send = 0;
            $obj->to_refund = 0;
            $obj->date_add = date('Y-m-d H:i:s');
            $obj->date_upd = '0000-00-00 00:00:00';
            $obj->add();
        }
    }


    public function hookActionObjectCartAddAfter($params)
    {
        $cart = isset($params['object']) ? $params['object'] : false;
        if ($cart && Validate::isLoadedObject($cart)) {
            $obj = new PdGA4PModelCart();
            $obj->id_cart = (int)$cart->id;
            $obj->id_order = '';
            $obj->client_id = $this->getGAClientId();
            $obj->session_id = $this->getGASessionId();
            $obj->order_send = 0;
            $obj->refund_send = 0;
            $obj->to_refund = 0;
            $obj->date_add = date('Y-m-d H:i:s');
            $obj->date_upd = '0000-00-00 00:00:00';
            $obj->add();
        }
    }

    public function getGASessionId()
    {
        $cookie_name = '_ga_' . str_replace('G-', '', $this->ga4_id);
        if (isset($_COOKIE[$cookie_name])) {
            $parts = explode('.', $_COOKIE[$cookie_name]);

            if (isset($parts[2])) {
                return $parts[2];
            } else {
                return false;
            }
        } else {
            return '';
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $newOrderStatus = $params['newOrderStatus'];
        $id_order_status_new = (int)$newOrderStatus->id;
        $id_order = (int)$params['id_order'];

        $order = new Order($id_order);

        $user_id = '';
        if (isset($this->context->customer->id)) {
            $user_id = (int)$this->context->customer->id;
        } else {
            $user_id = (int)$order->id_customer;
        }

        $ga4Service = new Service($this->ga4_api_secret);
        $ga4Service->setMeasurementId($this->ga4_id);

        $baseRequest = new BaseRequest();

        $clientId = PdGA4PModel::getGAClientIdByIdOrder($id_order);

        if (!$clientId) {
            return;
        }


        $baseRequest->setClientId(PdGA4PModel::getGAClientIdByIdOrder($id_order));
        $baseRequest->setUserId($user_id);


        // order
        if ($id_order
            && $id_order_status_new
            && in_array($id_order_status_new, $this->os_statuses_send)
            && $this->transaction_send_type == 2
        ) {
            $obj = new PdGA4PModel($id_order);
            if (isset($obj->id_order) && !$obj->order_send) {
                $order = new Order($id_order);
                if (!(Validate::isLoadedObject($order))) {
                    return;
                }

                $cart = new Cart((int)$order->id_cart);
                $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);

                $order_value = $order->total_paid - $order->total_shipping_tax_incl;
                $order_tax = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
                $shipping_cost = $order->total_shipping_tax_incl;

                $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
                $id_shop = (int)$this->context->shop->id;
                $id_currency = $order->id_currency;
                $currency = new Currency($id_currency);
                $currency_iso = $currency->iso_code;
                $order_products = $order->getProducts();
                $cart_rules = $this->getCartRuleWithCoupon($order->id_cart);

                $purchaseEventData = new PurchaseEvent();
                $purchaseEventData->setParamValue('session_id', PdGA4PModel::getGASessionIdByIdOrder($id_order));

                $purchaseEventData
                    ->setValue(Tools::ps_round($order_value, 2))
                    ->setCurrency($currency_iso)
                    ->setTransactionId($order->reference)
                    ->setAffiliation($this->http_referer)
                    ->setCoupon(sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '')
                    ->setShipping(Tools::ps_round($shipping_cost, 2))
                    ->setTax(Tools::ps_round($order_tax, 2));


                $content_category  = array();
                foreach ($order_products as &$op) {
                    $content_category = explode('/', $this->getCategoryPath($op['id_category_default']));
                    $op['content_category'] = array_map('trim', $content_category);
                    $op['content_ids'] = $this->getProductIdStringByType($op);
                    $op['price'] = Tools::ps_round($op['unit_price_tax_incl'], 2);

                    $price_old = Product::getPriceStatic($op['id_product'], true, $op['product_attribute_id'], 6, null, false, false);

                    $discount = 0;
                    $discount = $price_old - $op['unit_price_tax_incl'];
                    if ($discount_cart > 0) {
                        $discount += $discount_cart;
                    }
                    $op['discount'] = Tools::ps_round($discount, 2);

                    $op['manufacturer'] = $op['id_manufacturer'] ? addslashes(Manufacturer::getNameById($op['id_manufacturer'])) : '';
                    $product = new Product($op['id_product'], false, $id_lang, $id_shop);
                    $attribute_combination_resume = $product->getAttributeCombinationsById($op['product_attribute_id'], $id_lang, true);

                    $op['variant'] = '';
                    if ($attribute_combination_resume) {
                        foreach ($attribute_combination_resume as  $acr) {
                            $op['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $op['variant'] = mb_substr($op['variant'], 0, -3);
                    }

                    if (!empty($op['variant'])) {
                        $op['product_name'] = addslashes(Tools::replaceAccentedChars($product->name.' ('.$op['variant'].')'));
                    } else {
                        $op['product_name'] = addslashes($product->name);
                    }
                }

                $pos = 0;
                $purchasedItem = '';
                foreach ($order_products as $o) {
                    $purchasedItem = new ItemParameter();
                    $purchasedItem
                        ->setItemId($o['content_ids'])
                        ->setItemName($o['product_name'])
                        ->setAffiliation($this->http_referer)
                        ->setCoupon(sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '')
                        ->setIndex($pos)
                        ->setDiscount($o['discount'])
                        ->setItemBrand($o['manufacturer'])
                        ->setItemCategory(isset($o['content_category'][0]) ? addslashes($o['content_category'][0]) : '')
                        ->setItemCategory2(isset($o['content_category'][1]) ? addslashes($o['content_category'][1]) : '')
                        ->setItemCategory3(isset($o['content_category'][2]) ? addslashes($o['content_category'][2]) : '')
                        ->setItemCategory4(isset($o['content_category'][3]) ? addslashes($o['content_category'][3]) : '')
                        ->setItemCategory5(isset($o['content_category'][4]) ? addslashes($o['content_category'][4]) : '')
                        ->setItemVariant($o['variant'])
                        ->setPrice($o['price'])
                        ->setCurrency($currency_iso)
                        ->setQuantity($o['product_quantity']);

                    $purchaseEventData->addItem($purchasedItem);
                    $pos++;
                }

                $baseRequest->addEvent($purchaseEventData);
                $ga4Service->send($baseRequest); // need comment this line out to debug belowe

                // DEBUG START
                $debugResponse = $ga4Service->sendDebug($baseRequest);
                dump($baseRequest);
                //Now debug response contains status code, and validation messages if request is invalid
                dump($debugResponse->getStatusCode());
                dump($debugResponse->getValidationMessages());
                dump($debugResponse->getBody());
                die();
                // DEBUG END

                $obj->order_send = 1;
                $obj->date_upd = date('Y-m-d H:i:s');
                $obj->update();
            }
        }

        // refund
        if ($id_order
            && $id_order_status_new
            && in_array($id_order_status_new, $this->os_statuses_refund)
        ) {
            $obj = new PdGA4PModel($id_order);
            if (isset($obj->id_order) && !$obj->refund_send) {
                $order_value = $order->total_paid;
                $order_tax = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
                $shipping_cost = $order->total_shipping_tax_incl;

                $id_lang = (int)$this->context->language->id;
                $id_shop = (int)$this->context->shop->id;
                $id_currency = $order->id_currency;
                $currency = new Currency($id_currency);
                $currency_iso = $currency->iso_code;
                $order_products = $order->getProducts();
                $cart_rules = $this->getCartRuleWithCoupon($order->id_cart);

                $cart = new Cart((int)$order->id_cart);
                $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);

                $refundEventData = new RefundEvent();

                $refundEventData->setParamValue('session_id', PdGA4PModel::getGASessionIdByIdOrder($id_order));
                $refundEventData
                    ->setValue(Tools::ps_round($order_value, 2))
                    ->setCurrency($currency_iso)
                    ->setTransactionId($order->reference)
                    ->setAffiliation($this->http_referer)
                    ->setCoupon(sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '')
                    ->setShipping(Tools::ps_round($shipping_cost, 2))
                    ->setTax(Tools::ps_round($order_tax, 2));


                $content_category  = array();
                foreach ($order_products as &$op) {
                    $content_category = explode('/', $this->getCategoryPath($op['id_category_default']));
                    $op['content_category'] = array_map('trim', $content_category);
                    $op['content_ids'] = $this->getProductIdStringByType($op);
                    $op['price'] = Tools::ps_round($op['unit_price_tax_incl'], 2);
                    $op['manufacturer'] = $op['id_manufacturer'] ? addslashes(Manufacturer::getNameById($op['id_manufacturer'])) : '';

                    $price_old = Product::getPriceStatic($op['id_product'], true, $op['product_attribute_id'], 6, null, false, false);
                    $discount = 0;
                    $discount = $price_old - $op['unit_price_tax_incl'];
                    if ($discount_cart > 0) {
                        $discount += $discount_cart;
                    }
                    $op['discount'] = Tools::ps_round($discount, 2);

                    $product = new Product($op['id_product'], false, $id_lang, $id_shop);
                    $attribute_combination_resume = $product->getAttributeCombinationsById($op['product_attribute_id'], $id_lang, true);
                    $op['variant'] = '';
                    if ($attribute_combination_resume) {
                        foreach ($attribute_combination_resume as  $acr) {
                            $op['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $op['variant'] = mb_substr($op['variant'], 0, -3);
                    }

                    if (!empty($op['variant'])) {
                        $op['product_name'] = addslashes(Tools::replaceAccentedChars($product->name.' ('.$op['variant'].')'));
                    } else {
                        $op['product_name'] = addslashes($product->name);
                    }
                }

                $pos = 0;
                $returnedItem = '';
                foreach ($order_products as $o) {
                    $returnedItem = new ItemParameter();
                    $returnedItem
                        ->setItemId($o['content_ids'])
                        ->setItemName($o['product_name'])
                        ->setAffiliation($this->http_referer)
                        ->setCoupon(sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '')
                        ->setIndex($pos)
                        ->setDiscount($o['discount'])
                        ->setItemBrand($o['manufacturer'])
                        ->setItemCategory(isset($o['content_category'][0]) ? addslashes($o['content_category'][0]) : '')
                        ->setItemCategory2(isset($o['content_category'][1]) ? addslashes($o['content_category'][1]) : '')
                        ->setItemCategory3(isset($o['content_category'][2]) ? addslashes($o['content_category'][2]) : '')
                        ->setItemCategory4(isset($o['content_category'][3]) ? addslashes($o['content_category'][3]) : '')
                        ->setItemCategory5(isset($o['content_category'][4]) ? addslashes($o['content_category'][4]) : '')
                        ->setItemVariant($o['variant'])
                        ->setPrice($o['price'])
                        ->setCurrency($currency_iso)
                        ->setQuantity($o['product_quantity']);

                    $refundEventData->addItem($returnedItem);
                    $pos++;
                }

                $baseRequest->addEvent($refundEventData);
                $ga4Service->send($baseRequest);

                $obj->to_refund = 0;
                $obj->date_upd = date('Y-m-d H:i:s');
                $obj->update();
            }
        }
    }

    public function hookActionCustomerAccountAdd($params)
    {
        if (isset($params['newCustomer'])) {
            $customer = new PdGA4PRegistrationModel($params['newCustomer']->id);
            $customer->registered = 1;
            $customer->id_customer = $params['newCustomer']->id;
            $customer->date_add = date('Y-m-d H:i:s');
            $customer->date_upd = '0000-00-00 00:00:00';
            $customer->save();
        }
    }
}
