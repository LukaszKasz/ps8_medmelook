<?php
/**
 *  Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @version 2.6.1
 *
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 * ****************************************************
 * *                    Pixel Plus                    *
 * *          http://www.smart-modules.com            *
 * *                     V 2.6.1                      *
 * ************************************************** *
 * Versions:
 * To check the complete changelog. open versions.txt file
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/autoload.php';
class FacebookConversionTrackingPlus extends Module
{
    const CONVERSION_WINDOW = 7; // In days
    public $type = '';
    public $contirmUninstall;
    public $pixelparams = '';
    private $extras_type;
    private $pixels_printed = false;
    private $pixel_header_printed = false;
    private $schema;
    private $og;
    private $schema_structure;
    private $ssl;
    private $content_displayed = false;
    private $displayMicrodata;
    private $rmd;
    private $extras_type_lang;
    private $form_fields = [];
    public static $feed_v2 = false;
    private $conversionPixelAdded = false;
    protected $api;
    protected $logOthers = false;
    protected $logIssues = false;
    protected $amo;
    protected $is_15 = false;
    protected $is_17 = false;
    // Prefered Image Format
    private $image_format;

    public function __construct()
    {
        $this->is_17 = version_compare(_PS_VERSION_, '1.7', '>=');
        $this->is_15 = version_compare(_PS_VERSION_, '1.6', '<');
        /* $tab = $this->is_17 ? 'market_place' : 'advertising_marketing'; */
        $this->name = 'facebookconversiontrackingplus';
        $this->tab = 'advertising_marketing';
        $this->version = '2.6.1';
        $this->author = 'Smart Modules';
        //        $this->author_address = '0x29aAc34Cc2542b6816fF066E1Da67924EF9e56f6';
        $this->need_instance = 0;
        $this->module_key = '3e316ca70bb2494f37010fc46feb2f4d';
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.5', 'max' => _PS_VERSION_];
        $this->displayName = $this->l('Pixel Plus for Facebook: Events + Conversions API + Pixel Catalogue');
        $this->description = $this->l('Track Facebook events with the Pixel & Conversion API. Measure the ROI of your ads, create catalogues, dynamic ads, tag products on Instagram, create shops on Facebook, microdata... GDPR Ready, iOS 14.5 Ready');
        $this->confirmUninstall = $this->l('Are you sure about removing all your Facebook Pixels?');
        /* Microdata to Create Pixel Catalogues */
        $this->displayMicrodata = (bool) Configuration::get('FCTP_FILL_MICRO_DATA');
        $this->rmd = json_decode(Configuration::get('FCTP_MICRODATA'), true);
        $this->logOthers = Configuration::get('FCTP_CONVERSION_LOG_OTHER');
        $this->logIssues = Configuration::get('FCTP_CONVERSION_LOG_ISSUES');
        $this->controllers = ['AdminExportCustomers'];
        $this->active = $this->isModuleActive();
        parent::__construct();
        $this->type = [
            1 => $this->l('Key Page'),
        ];
        $this->extras_type = [
            1 => 'index',
            4 => 'cms',
            5 => 'contact',
        ];
        $this->extras_type_lang = [
            1 => $this->l('Index'),
            4 => $this->l('CMS'),
            5 => $this->l('Contact'),
        ];
        $this->pixelparams = ['pixel_active' => '0', 'pixel_name' => '', 'pixel_extras' => '', 'pixel_extras_type' => '', 'pixel_extras_name' => ''];
        if (Configuration::get('FCTP_CONVERSION_API') && (@$this->context->controller->controller_type == 'front' || @$this->context->controller->controller_type == 'modulefront')) {
            $this->tryLoadingAPI();
        }

        // Check if facebookproductsfeed is installed to improve the compatibility
        if (Module::isEnabled('facebookproductsfeed')) {
            $fpf = Module::getInstanceByName('facebookproductsfeed');
            if (version_compare($fpf->version, '2.0.5', '>=')) {
                self::$feed_v2 = true;
            }
        }
        if (!isset(self::$feed_v2)) {
            self::$feed_v2 = false;
        }
        $this->form_fields = $this->getFormFields();
        /* Backward compatibility */
        if (_PS_VERSION_ < '1.5') {
            require _PS_MODULE_DIR_ . $this->name . '/backward_compatibility/backward.php';
        }
        SmartForm::init($this);
    }

    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';
        if (!parent::install()) {
            return false;
        }

        foreach ($this->hooksList() as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }
        foreach ($this->form_fields as $field) {
            if ($field['global']) {
                Configuration::updateGlobalValue($field['name'], $field['def']);
            }
        }
        Configuration::updateValue('pixel_account_on', '');
        // TODO MultiShop with multiple themes
        $this->checkMicroData();
        $this->installTabs();

        return true;
    }

    private function hooksList()
    {
        $hooks = [
            'actionCustomerAccountAdd',
            'header',
            'displayHeader',
            'displayFooter',
            'actionCartSave',
            'actionAdminControllerSetMedia',
            'actionFrontControllerSetMedia',
            'displayBeforeBodyClosingTag',
            'displayOrderConfirmation',
            'actionValidateOrder',
            'actionOrderStatusPostUpdate',
            'displayProductAdditionalInfo',
            'displayRightColumnProduct',
            'displayLeftColumnProduct',
            'actionCustomerLogoutAfter',
        ];

        return $hooks;
    }

    public function uninstall($delete = false)
    {
        if ($delete == true) {
            include dirname(__FILE__) . '/sql/uninstall.php';
        }
        foreach ($this->form_fields as $field) {
            Configuration::deleteByName($field['name']);
        }
        Configuration::deleteByName('pixel_account_on');
        Configuration::deleteByName('FCTP_MICRODATA');

        return parent::uninstall() && $this->uninstallTabs();
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install()) {
            return false;
        }

        return true;
    }

    /**
     * This method is often use to create an ajax controller
     *
     * @return bool
     */
    public function installTabs()
    {
        $installTabCompleted = true;
        $tab = new Tab();
        foreach ($this->controllers as $controllerName) {
            if (Tab::getIdFromClassName($controllerName)) {
                continue;
            }
            $tab->class_name = $controllerName;
            $tab->active = true;
            $tab->name = [];
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = $this->name;
            }
            $tab->id_parent = -1;
            $tab->module = $this->name;
            $installTabCompleted = $installTabCompleted && $tab->add();
        }

        return $installTabCompleted;
    }

    /**
     * uninstall tabs
     *
     * @return bool
     */
    public function uninstallTabs()
    {
        $uninstallTabCompleted = true;
        foreach ($this->controllers as $controllerName) {
            $id_tab = (int) Tab::getIdFromClassName($controllerName);
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                $uninstallTabCompleted = $uninstallTabCompleted && $tab->delete();
            }
        }

        return $uninstallTabCompleted;
    }

    public function tryLoadingAPI()
    {
        if (!class_exists('ConversionApi')) {
            require_once _PS_MODULE_DIR_ . $this->name . '/classes/conversion-api.php';
        }
        if (!empty($this->api)) {
            return;
        }
        if (Configuration::get('FCTP_CONVERSION_API')) {
            $this->api = new ConversionApi($this);
            if (!$this->api->getActive() || empty(array_filter($this->api->getPixelsIds()))) {
                $this->api = false;
            }
        } else {
            $this->api = false;
        }
    }

    /* Check if the module is in Sandbox Mode to restrict the access to the alloed IPs */
    private function isModuleActive()
    {
        if (Configuration::get('FCTP_TEST_MODE')) {
            $ips = explode(',', Configuration::get('FCTP_TEST_MODE_IPS'));
            $current_ips = $this->getCurrentUserIp();
            $ip_count = count($ips);
            for ($i = 0; $i < $ip_count; ++$i) {
                if (in_array(trim($ips[$i]), $current_ips)) {
                    return true;
                }
            }

            return false;
        } else {
            return true;
        }
    }

    /* Get the current user IP */
    private function getCurrentUserIP()
    {
        $keys_to_check = ['HTTP_X_REMOTE_IP', 'HTTP_REMOTE_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        $ret = [];
        foreach ($keys_to_check as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ret[] = $_SERVER[$key];
            }
        }

        return $ret;
    }

    public function getContent()
    {
        $css = '';

        // Check if the URL has the deletePixelPlusLogs parameter and call the deletion function
        if (Tools::getValue('deletePixelPlusLogs') == 1) {
            $this->deletePixelPlusLogs();

            // After deleting, redirect back to the page without the parameter to prevent repeated deletions
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&conf=1');
            exit;  // Ensure no further execution happens after redirection
        }

        /* Check custom auciences customer generation CSV folder permissions */
        if (!is_writable(dirname(__FILE__) . '/csv/')) {
            $this->context->controller->errors[] = Tools::displayError($this->l('Please make') . ' /modules/' . $this->name . '/csv/ ' . $this->l('folder writable'));
        }
        $this->context->controller->addJS(dirname(__FILE__) . 'views/js/download.js');
        $this->context->smarty->assign(
            [
                'old_ps' => version_compare(_PS_VERSION_, '1.6', '<='),
                'is_17' => $this->is_17,
                'selected_menu' => Tools::getValue('selected_menu'),
            ]
        );
        if (Tools::isSubmit('submit' . $this->name) && Tools::getValue('FCTP_CHECK_MICRO_DATA')) {
            $this->checkMicroData();
            // Update the RMD value
            $this->rmd = json_decode(Configuration::get('FCTP_MICRODATA'), true);
        }
        // Show the basic configuration page
        // Check if Blocknewsletter Module is activated
        if (Module::isEnabled('blocknewsletter')) {
            $this->context->smarty->assign('newsletter', 1);
        } else {
            $this->context->smarty->assign('newsletter', 0);
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->smarty->assign(['oldps' => 1]);
        } else {
            $this->context->smarty->assign(['oldps' => 0]);
        }
        $this->context->smarty->assign('myurl', $this->getCurrentUrl());
        $this->context->smarty->assign('mytoken', Tools::getAdminTokenLite('AdminModules'));
        $export_customer_url = Context::getContext()->link->getAdminLink('AdminExportCustomers');
        // Download customer url
        $this->context->smarty->assign(
            [
                'export_customer_url' => $export_customer_url,
                'remoteAddr' => Tools::getRemoteAddr(),
            ]
        );
        $output = $this->display(__FILE__, 'views/templates/admin/configuration.tpl');
        $test_pixels = '';
        if (Configuration::get('FCTP_PIXEL_ID') != '') {
            $pixel_ids = explode(',', preg_replace('/[^,0-9]/', '', Configuration::get('FCTP_PIXEL_ID')));
            $this->context->smarty->assign([
                'fctpid' => $pixel_ids,
                'pixelsetup' => 1,
                'fctp_test_values' => $this->getDefaultValuesForPixelTests(),
                'product_catalog_id' => $this->getCatalogueIdForTest(),
                'currency' => $this->context->currency->iso_code,
            ]);
            $test_pixels = $this->display(__FILE__, 'views/templates/admin/test-pixels.tpl');
        }
        if (Tools::isSubmit('submit' . $this->name)) {
            $this->postProcess();
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                // For 1.5 and below, redirect to avoid conflicts
                $redUrl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/index.php?controller=AdminModules&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&conf=4';
                Tools::redirect($redUrl);
            }
        }

        // Generate warnings and information messages related to possible conflicts with the configurations
        $this->generateHelpfulMessages();
        // Generate the basic Pixel configuration
        $output .= $this->getBasicForm();
        // Check if requiested to generate a CSV customer list
        $typexp = Tools::getValue('typexp');
        if ($typexp != '') {
            if (self::getProcess($typexp) == true) {
                $relative_url = 'modules/facebookconversiontrackingplus/download.php?typexp=' . (int) Tools::getValue('typexp') . '&token=' . Tools::getAdminTokenLite('AdminModules');
                $this->context->smarty->assign(
                    [
                        'fctp_rurl' => '/' . $relative_url,
                        'fctp_url' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' . $this->context->shop->domain_ssl : 'http://' . $this->context->shop->domain) . __PS_BASE_URI__ . $relative_url,
                    ]
                );
                $output .= $this->display(__FILE__, 'views/templates/admin/clients-export.tpl');
            }
        }
        $output .= $this->getGoogleProductCategories();
        $output .= $test_pixels;
        $output .= $this->displayVideos();
        $output .= $this->displayFAQ();
        $output .= $this->displayLogs();
        if (Tools::getIsset('showPendingOrders')) {
            $output .= $this->showPendingOrders();
        }
        if (Configuration::get('FCTP_CONVERSION_IP_LOG') == '') {
            if (Configuration::get('FCTP_ENABLE_TEST_EVENTS')) {
                $this->context->controller->warnings[] =
                    SmartForm::genDesc($this->l('You have globally enabled the test mode for your CAPI events, Facebook will not track any event outside the "Test Events" tool until you disable the test code events or use the IP restriction tool.'), 'p');
            }
            if (Configuration::get('FCTP_CONVERSION_LOG')) {
                $this->context->controller->warnings[] =
                    SmartForm::genDesc($this->l('You have enabled the %s, but you haven\'t added an IP to restrict the log feature to only that IP. This will considerably increase the page load time as it will wait for Facebook\'s response for any event sent'), 'p', false, [$this->l('Log API Events')]) .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Click here to add you IP into the box'), ['a', 'href="#fieldset_conversion_api" class="target-menu" data-scroll-to="FCTP_CONVERSION_IP_LOG"']);
            }
        }
        // Check if test code has been set, but the feature isn't active.
        if (!empty($pixel_ids)) {
            $pixel_count = count($pixel_ids) + 1;
            if ($pixel_count > 1) {
                if (!($tmode = Configuration::get('FCTP_ENABLE_TEST_EVENTS'))) {
                    for ($i = 1; $i < $pixel_count; ++$i) {
                        if (Configuration::get('FCTP_CONVERSION_API_TEST_' . $i) != '') {
                            $tmode = true;
                            break;
                        }
                    }
                    if ($tmode) {
                        $this->context->controller->warnings[] = $this->l('You have configured the test codes but you haven\'t enabled the test mode feature.') .
                            SmartForm::openTag('br', true) .
                            $this->l('Go to the Conversion API section and scroll to the test & debugging options and enable the test mode');
                    }
                }
            }
        }
        $output .= $this->display(__FILE__, 'views/templates/admin/js-vars.tpl');

        return SmartForm::openTag('div', 'id="module-body" class="clearfix"') . $output . $css . SmartForm::closeTag('div');
    }

    private function generateHelpfulMessages()
    {
        if (Configuration::get('FCTP_TEST_MODE')) {
            $msg = sprintf($this->l('%s is currently enabled'), SmartForm::openTag('strong') . $this->l('Test Mode') . SmartForm::closeTag('strong'));
            $ips = Configuration::get('FCTP_TEST_MODE_IPS');
            $w = false;
            if (!$this->validateIpsList($ips)) {
                $w = true;
                if ($ips == '') {
                    $msg .= ' ' . $this->l('but no IPS have been added, this will block the module for all visits.');
                } else {
                    $msg .= ' ' . $this->l('but there aren\'t any valid IPs on the list');
                }
                $msg .= SmartForm::openTag('br', true) . SmartForm::openTag('br', true) . sprintf($this->l('It\'s highly advised to disable the %s or add at least one valid IP'), SmartForm::openTag('strong') . $this->l('Test Mode') . SmartForm::closeTag('strong'));
            } else {
                // valid IPs
                if (PixelTools::strpos($ips, Tools::getRemoteAddr()) !== false) {
                    $ip_msg = $this->l('allowed');
                } else {
                    $w = true;
                    $ip_msg = $this->l('not allowed');
                }
                $msg .= ', ' . $this->l('only allowed IPs will be able to see the module in action.') .
                    SmartForm::openTag('br', true) .
                    SmartForm::openTag('br', true) .
                    sprintf($this->l('Your IP is currently %s'), SmartForm::openTag('strong') . $ip_msg . SmartForm::closeTag('strong'));
            }
            if ($w) {
                $this->context->controller->warnings[] = $msg;
            } else {
                $this->context->controller->informations[] = $msg;
            }
        }
        // Alert messages for GDPR misconfigured
        if (Configuration::get('FCTP_BLOCK_SCRIPT')
            && ((Configuration::get('FCTP_BLOCK_SCRIPT_MODE') == 'cookies' && Configuration::get('FCTP_COOKIE_NAME') == '') || (Configuration::get('FCTP_BLOCK_SCRIPT_MODE') == 'local_storage' && (Configuration::get('FCTP_LOCAL_STORAGE_VAR_PATH') == '' || Configuration::get('FCTP_LOCAL_STORAGE_VALUE') == '')))) {
            $this->context->controller->warnings[] = sprintf($this->l('You have enabled the %s option but you haven\'t configured it.') .
                SmartForm::openTag('br', true) .
                SmartForm::openTag('br', true) .
                $this->l('With the current settings all visits will be blocked. It\'s highly advised to finish the configuration the fields or disable the feature'), SmartForm::openTag('strong') . $this->l('GDPR & Cookies consent') . SmartForm::closeTag('strong'));
        }
        if (Configuration::get('FCTP_FILL_MICRO_DATA') && !empty($this->rmd) && !Configuration::get('FCTP_MICRO_OG') && !Configuration::get('FCTP_MICRO_SCHEMA')) {
            $this->context->controller->warnings[] = sprintf($this->l('You have enabled the %s option but you haven\'t enabled the %s or the %s generation options so no microadata will be added.'), $this->l('Fill in the missing microdata?'), 'OG', 'Schema') .
                SmartForm::openTag('br') .
                SmartForm::genDesc($this->l('Click here to go to the %s section and review the settings'), ['a', 'href="#fieldset_micro_data" class="target-menu"'], false, [$this->l('Micro Data')]);
        }
    }

    private function showPendingOrders()
    {
        $output = '';
        $delayed_orders = array_reverse(json_decode(Configuration::get('FCTP_ORDER_DELAYED_LIST'), true), true);
        $delayed_content = [];
        foreach ($delayed_orders as $key => $value) {
            $o = new Order((int) $key);
            $delayed_content[] = [
                'id_order' => $key,
                'id_customer' => $o->id_customer,
                'order_link' => $this->context->link->getAdminLink('AdminOrders') . '&id_order=' . $key . '&vieworder',
            ];
        }
        $fields_list = [];
        $fields_list['id_order'] = [
            'title' => $this->l('ID order'),
            'type' => 'int',
            'search' => false,
            'orderby' => false,
        ];
        $fields_list['id_customer'] = [
            'title' => $this->l('Id Customer'),
            'type' => 'int',
            'search' => false,
            'orderby' => false,
        ];
        $fields_list['order_link'] = [
            'title' => $this->l('Order Link'),
            'type' => 'link',
            'search' => false,
            'orderby' => false,
            'align' => 'text-center',
        ];
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->icon = 'order';
        $helper->identifier = 'pending_orders';
        $helper->actions = ['delete'];
        $helper->show_toolbar = false;
        $helper->no_link = true;
        $helper->imageType = 'jpg';
        $helper->title = $this->l('Pending Orders List');
        $helper->table = $this->name;
        $helper->list_id = 'pending_orders';
        $helper->class = 'class_test';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $output .= $helper->generateList($delayed_content, $fields_list);

        $pending_orders = array_reverse(json_decode(Configuration::get('FCP_ORDER_CONVERSION'), true));
        $content = [];
        if (!empty($pending_orders)) {
            foreach ($pending_orders as $pending_order) {
                $content[] = [
                    'id_order' => $pending_order[0],
                    'id_customer' => $pending_order[1],
                    'is_guest' => $pending_order[2],
                    'event_id' => $pending_order[3],
                ];
            }
            $fields_list = [];
            $fields_list['id_order'] = [
                'title' => $this->l('ID order'),
                'type' => 'int',
                'search' => false,
                'orderby' => false,
            ];
            $fields_list['id_customer'] = [
                'title' => $this->l('id_customer'),
                'type' => 'int',
                'search' => false,
                'orderby' => false,
            ];
            $fields_list['is_guest'] = [
                'title' => $this->l('Is Guest?'),
                'type' => 'bool',
                'search' => false,
                'orderby' => false,
                'align' => 'text-center',
                'active' => 'status',
            ];
            $fields_list['event_id'] = [
                'title' => $this->l('Event ID'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
            ];
            $helper = new HelperList();
            $helper->shopLinkType = '';
            $helper->simple_header = false;
            $helper->identifier = 'pending_purchases';
            $helper->actions = ['delete'];
            $helper->show_toolbar = false;
            $helper->no_link = true;
            $helper->imageType = 'jpg';
            $helper->title = $this->l('Pending purchase events list');
            $helper->table = $this->name;
            $helper->list_id = 'pending_purchases';
            $helper->class = 'class_test';
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
            $output .= $helper->generateList($content, $fields_list);
        }

        return $output;
    }

    private function getFormFields()
    {
        // Name: Name of the field
        // Def: Default Value
        // Type: Type of data
        // Global: Need to saved in the Global scope?
        $form_fields = [
            ['name' => 'FCTP_TEST_MODE', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_TEST_MODE_IPS', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_CONV', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_ADD_TO_CART', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_SEARCH', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CATEGORY', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_SEARCH_ITEMS', 'def' => 5, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CATEGORY_ITEMS', 'def' => 5, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CATEGORY_TOP_SALES', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_WISH', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_REG', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_START', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_START_ORD', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_PIXEL_ID', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_SINGLE_EVENT_TRACKING', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_VIEWED', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_FORCE_HEADER', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCP_CUST_ADD_TO_CART', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCP_PRODUCT_CUSTOM_SELECTOR', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCP_CUST_SEARCH', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCP_CUST_SEARCH_P', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCP_CUST_CHECKOUT', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_LIMIT_CONF', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_INIT_CHECKOUT_MODE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_AJAX', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_AJAX_REG', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_COOKIE_CONTROL', 'def' => 0, 'type' => 'int', 'global' => 1],
            ['name' => 'FCTP_EXCLUDE_BO_ORDERS', 'def' => 1, 'type' => 'int', 'global' => 1],
            ['name' => 'FCTP_PURCHASE_SHIPPING_EXCLUDE', 'def' => 0, 'type' => 'int', 'global' => 1],
            ['name' => 'FCTP_PURCHASE_TAX', 'def' => 1, 'type' => 'int', 'global' => 1],
            ['name' => 'FCTP_PURCHASE_VALID_ONLY', 'def' => 0, 'type' => 'int', 'global' => 1],
            ['name' => 'FCTP_ORDER_DELAYED_LIST', 'def' => [], 'type' => 'array', 'global' => 0],
            ['name' => 'FCTP_ORDER_DELAY_LIMIT_DAYS', 'def' => 7, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_ORDER_STATUS_EXCLUDE', 'def' => [], 'type' => 'array', 'global' => 0],
            ['name' => 'FCTP_ORDER_CUSTOMER_GROUP_EXC', 'def' => [], 'type' => 'array', 'global' => 0],
            ['name' => 'FCTP_ORDER_CUSTOMER_ID_EXCLUDE', 'def' => '', 'type' => 'text', 'global' => 1],
            ['name' => 'FCTP_PURCHASE_DEBUG', 'def' => 0, 'type' => 'int', 'global' => 1],
            // array('name' => 'FCTP_FORCE_BASIC', 'def' => 0, 'type' => 'int', 'global' => 0),
            ['name' => 'FCTP_FORCE_BASIC_MODE_LIST', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_REG_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_START_ORD_VALUE', 'def' => 1, 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_CATEGORY_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_SEARCH_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_WISH_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_START_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_FORCE_REFRESH_AFTER_ORDER', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CONVERSION_API', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_VERIFY_DOMAIN', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'PP_IP_TYPE', 'def' => '', 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_ADVANCED_MATCHING_OPTIONS', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CONVERSION_LOG', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CONVERSION_LOG_ISSUES', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CONVERSION_LOG_OTHER', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_DISABLE_CURL_MULTI', 'def' => 0, 'type' => 'int', 'global' => 1],
            ['name' => 'FCTP_DISABLE_SHORT_TIMEOUT', 'def' => 0, 'type' => 'int', 'global' => 1],
            ['name' => 'FCTP_FILL_MICRO_DATA', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_BLOCK_SCRIPT', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_BLOCK_SCRIPT_MODE', 'def' => 'cookies', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_COOKIE_NAME', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_COOKIE_VALUE', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_COOKIE_EXTERNAL', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_COOKIE_RELOAD', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_COOKIE_BUTTON', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_BLOCK_BASIC', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_LOCAL_STORAGE_VAR_PATH', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_LOCAL_STORAGE_VALUE', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_MICRO_OG', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_MICRO_SCHEMA', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_MICRO_IGNORE_COVER', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_MICRO_IMG_LIMIT', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_CONVERSION_IP_LOG', 'def' => '', 'type' => 'text', 'global' => 0],
            ['name' => 'FCTP_CONVERSION_PAYLOAD', 'def' => '', 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_ENABLE_TEST_EVENTS', 'def' => '', 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CMS', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CMS_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CONTACT_US', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_CONTACT_US_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_NEWSLETTER', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_NEWSLETTER_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_PAGETIME', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_PAGETIME_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_PAGEVIEW_COUNT', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_PAGEVIEW_COUNT_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_PAGEVIEW_COUNT_COOKIE_DAYS', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCP_DEFERRED_LOADING', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCP_DEFERRED_SECONDS', 'def' => 0, 'type' => 'int', 'global' => 0],
            ['name' => 'FCP_DEFER_FIRST_TIME', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_DISCOUNT', 'def' => 1, 'type' => 'int', 'global' => 0], // New Event added discount event
            ['name' => 'FCTP_DISCOUNT_VALUE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCP_EXTERNAL_ID_USAGE', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_AMO_DATA_contact', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_AMO_DATA_personal', 'def' => 1, 'type' => 'int', 'global' => 0],
            ['name' => 'FCTP_AMO_DATA_address', 'def' => 0, 'type' => 'int', 'global' => 0],
        ];

        $pixel_value = (Tools::getValue('FCTP_PIXEL_ID') != '') ? Tools::getValue('FCTP_PIXEL_ID') : Configuration::get('FCTP_PIXEL_ID');
        $pixels_ids = explode(',', $pixel_value);
        $pix_count = count($pixels_ids) + 1;

        for ($i = 1; $i < $pix_count; ++$i) {
            $form_fields[] = ['name' => 'FCTP_CAPI_TOKEN_' . $i, 'def' => '', 'type' => 'text', 'global' => 0];
            $form_fields[] = ['name' => 'FCTP_CONVERSION_API_TEST_' . $i, 'def' => '', 'type' => 'text', 'global' => 0];
        }

        $langs = Language::getLanguages();
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $form_fields[] = ['name' => 'FPF_PREFIX_' . $shop['id_shop'], 'def' => '', 'type' => 'text', 'global' => 1];
            $form_fields[] = ['name' => 'FCTP_COMBI_' . $shop['id_shop'], 'def' => 0, 'type' => 'int', 'global' => 1];
            $form_fields[] = ['name' => 'FCTP_COMBI_PREFIX_' . $shop['id_shop'], 'def' => '', 'type' => 'text', 'global' => 1];
            if (self::$feed_v2) {
                $form_fields[] = ['name' => 'FCTP_FEED_' . $shop['id_shop'], 'def' => '', 'type' => 'text', 'global' => 1];
            } else {
                foreach ($langs as $lang) {
                    $form_fields[] = ['name' => 'FPF_' . $shop['id_shop'] . '_' . $lang['id_lang'], 'def' => '', 'type' => 'text', 'global' => 1];
                }
            }
        }

        return $form_fields;
    }

    private function getGoogleProductCategories()
    {
        $gc = new GoogleCategories();

        return $gc->buildGoogleCategories();
    }

    private function getDefaultValuesForPixelTests()
    {
        $sql = 'SELECT id_product, name, price FROM ' . _DB_PREFIX_ . 'product LEFT JOIN ' . _DB_PREFIX_ . 'product_lang USING (id_product) WHERE id_lang = ' . (int) $this->context->language->id . ' AND active = 1';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    private function getCatalogueIdForTest()
    {
        $languages = Language::getLanguages(true, false, true);
        $clang = count($languages);
        if (is_array($languages[0])) {
            // It's 1.6 before the get_as_id option
            $tmp = [];
            foreach ($languages as $l) {
                $tmp[] = $l['id_lang'];
            }
            $languages = $tmp;
            unset($tmp);
        }
        if (self::$feed_v2) {
            return Configuration::getGlobalValue('FCTP_FEED_' . $this->context->shop->id);
        } else {
            if ($clang == 1) {
                return Configuration::getGlobalValue('FPF_' . $this->context->shop->id . '_' . $languages[0]);
            } else {
                for ($i = 0; $i < $clang; ++$i) {
                    $tmp = Configuration::getGlobalValue('FPF_' . $this->context->shop->id . '_' . $languages[$i]);
                    if ((int) $tmp > 0) {
                        return $tmp;
                    }
                }
            }
        }
        $this->context->controller->_warnings[] = $this->l('To send test pixels with Dynamic Ads features fill the catalog IDs fields by getting your catalog ID from Facebook');

        return 0;
    }

    /**
     * Save the form data.
     */
    protected function postProcess()
    {
        // Save the Google Categories Association
        $gc = new GoogleCategories();
        $gc->assignGoogleTaxonomies();
        // Save the form fields
        foreach ($this->form_fields as $field) {
            $key = $field['name'];
            if ($key === 'FCTP_ORDER_STATUS_EXCLUDE') {
                $this->postProcessArrayFieldAsList(
                    OrderState::getOrderStates($this->context->language->id),
                    $field,
                    'id_order_state',
                    ['logable' => 1]
                );
            } elseif ($key === 'FCTP_ORDER_CUSTOMER_GROUP_EXC') {
                $this->postProcessArrayFieldAsList(
                    Group::getGroups($this->context->language->id),
                    $field,
                    'id_group'
                );
            } elseif ($key === 'FCTP_FORCE_BASIC_MODE_LIST') {
                // Save to keep the global variable value
                $pms = PaymentModule::getInstalledPaymentModules();
                $forced_modules = [];
                foreach ($pms as $pm) {
                    if (Tools::getIsset('FCTP_FORCE_BASIC_MODE_LIST_' . $pm['id_module'])) {
                        $forced_modules[] = $pm['id_module'];
                    }
                }
                Configuration::updateValue($key, json_encode($forced_modules));
            } elseif ($key === 'FCTP_VERIFY_DOMAIN') {
                $verify_domain = Tools::getValue($key);
                if ($verify_domain != '' && (Tools::getValue($key) !== Configuration::get($key))) {
                    if (PixelTools::strpos($verify_domain, 'content') !== false) {
                        if (!preg_match('/content\=\"([a-zA-Z0-9]*)\"/', $verify_domain, $verify_domain)) {
                            $this->context->controller->errors[] = $this->l('The Domain Verification code is not correct, please review it and add the meta-data tag again');
                            continue;
                        }
                        $verify_domain = $verify_domain[1];
                    }
                    if (preg_match('/[[:^alnum:]]/', $verify_domain, $invalid_characters)) {
                        $this->context->controller->errors[] = $this->l('The Domain Verification code is not correct, please review it and add the key inside the content tag again');
                        continue;
                    }
                    Configuration::updateValue($key, $verify_domain);
                }
            } elseif ($field['global']) {
                Configuration::updateGlobalValue($key, trim(Tools::getValue($key)));
            } else {
                Configuration::updateValue($key, trim(Tools::getValue($key)));
            }
        }
        if (empty($this->context->controller->errors)) {
            $this->context->controller->confirmations[] = $this->l('Configurations Updated Successfully');
        }
    }

    /**
     * Post process several fields and saves them as a comma separated value in the database
     *
     * @param $items array The list of items
     * @param $field string The Configuration field
     * @param $id int ID field to save
     * @param $exclude_array Condition pair option => value for the items to be skipped
     */
    private function postProcessArrayFieldAsList($items, $field, $id, $exclude_array = [])
    {
        $values = [];
        foreach ($items as $item) {
            if (!empty($exclude_array)) {
                foreach ($exclude_array as $index => $value) {
                    if (isset($item[$index]) && $item[$index] == $value) {
                        continue 2;
                    }
                }
            }
            if (Tools::getIsset($field['name'] . '_' . $item[$id])) {
                $values[] = $item[$id];
            }
        }
        if ($field['global']) {
            Configuration::updateGlobalValue($field['name'], implode(',', $values));
        } else {
            Configuration::updateValue($field['name'], implode(',', $values));
        }
    }

    public function newpixelJS()
    {
        $this->context->smarty->assign(
            [
                'old_ps' => version_compare(_PS_VERSION_, '1.6', '<'),
                /*'msg_automatic_value' => $this->l('Automatic Value'),
            'msg_must_be_number' => $this->l('Error: Value must be a number'),
            'msg_enter_id_cms' => $this->l('Enter the ID of the CMS you want to track'),
            'msg_enter_id_track' => $this->l('Enter the ID of the CMS you want to track'),*/
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/add-extra-type.tpl');
    }

    private function getBasicForm()
    {
        $langs = Language::getLanguages();
        $shops = Shop::getShops();
        // $fields_value = array();
        $switch_options = [
            [
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled'),
            ],
            [
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled'),
            ],
        ];
        $cookie_options = [
            [
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('PrestaShop'),
            ],
            [
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('External'),
            ],
        ];
        $num_items_options = [];
        for ($i = 5; $i <= 10; ++$i) {
            $num_items_options[] = [
                'id_option' => $i,
                'name' => $i,
            ];
        }
        $checkout_options = [
            [
                'id_option' => '1',
                'name' => $this->l('Initial page of the checkout process'),
            ],
            [
                'id_option' => '2',
                'name' => $this->l('Click on the button that leads to checkout page'),
            ],
        ];

        $wishlist_modules = $this->getWishlistOptions();

        $external_id_uage = [
            [
                'id_option' => '1',
                'name' => $this->l('Only on registered users (recommended)'),
            ],
            [
                'id_option' => '2',
                'name' => $this->l('On all users'),
            ],
        ];

        /* Build the order status list */
        $order_states = OrderState::getOrderStates($this->context->language->id);
        $order_statuses = [];
        // $order_statuses = array(array('id_option' => '0', 'name' => '----' . $this->l('None') . '----'));
        foreach ($order_states as $order_state) {
            $os = [];
            // we filter the status that are not considerd as valid by settings, to avoid the too large lists in the selection
            if ($order_state['logable'] != 1) {
                $os['id_option'] = $order_state['id_order_state'];
                $os['name'] = $order_state['name'];
                $order_statuses[] = $os;
            }
        }

        /* Build the Customer Group options */
        $groups = Group::getGroups($this->context->language->id);
        $customer_groups = [];
        // $order_statuses = array(array('id_option' => '0', 'name' => '----' . $this->l('None') . '----'));
        foreach ($groups as $group) {
            $customer_groups[] = [
                'id_option' => $group['id_group'],
                'name' => $group['name'],
            ];
        }

        $payment_modules = [];
        /* Build the Payment modules list */
        $pms = PaymentModule::getInstalledPaymentModules();
        foreach ($pms as $pm) {
            $payment_modules[] =
                [
                    'id_option' => $pm['id_module'],
                    'name' => Module::getModuleName($pm['name']),
                ];
        }
        $advanced_matching_data = [
            [
                'id_option' => 'contact',
                'name' => $this->l('User\'s contact data') . ' (' . $this->l('email, phone...') . ')',
            ],
            [
                'id_option' => 'personal',
                'name' => $this->l('User\'s personal data') . ' (' . $this->l('name, last name, gender, birthdate...') . ')',
            ],
            [
                'id_option' => 'address',
                'name' => $this->l('User\'s address data') . ' (' . $this->l('city, state, country, zip..') . ')',
            ],
        ];
        $select_options = [];
        $extra_options = [];
        foreach ($this->type as $i => $type) {
            $select_options[] = ['id_option' => $i, 'name' => $this->type[$i]];
        }
        foreach ($this->extras_type as $i => $extra) {
            $extra_options[] = ['id_extra_option' => $i, 'extra' => $this->extras_type_lang[$i]];
        }
        $missing_micro = '';
        $og = $schema = false;
        if (is_array($this->rmd) && count($this->rmd) > 0) {
            $this->context->smarty->assign('missing_micro', $this->rmd);
            $missing_micro = $this->display(__FILE__, 'views/templates/admin/form-missing-microdata.tpl');
            if (isset($this->rmd['og'])) {
                $og = true;
            }
            if (isset($this->rmd['schema'])) {
                $schema = true;
            }
        }
        $fields_form = [];

        $fields_form['pixel_id'] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Facebook Pixel\'s ID'),
                    'icon' => 'icon-cog',
                ],
                'input' => [
                    [
                        'label' => '',
                        'type' => 'free',
                        'class' => '',
                        'name' => 'FCTP_FREE',
                        'desc' => SmartForm::closeTag('p') . SmartForm::genDesc($this->l('Video: How to get the Pixel ID'), ['h3', 'class="modal-title text-info"']) . SmartForm::genYoutubeVideo('https://www.youtube-nocookie.com/embed/KpuiRTUjGdM'),
                    ],
                    [
                        'label' => $this->l('Pixel Identifier'),
                        'type' => 'text',
                        'name' => 'FCTP_PIXEL_ID',
                        'desc' => $this->l('Here you have to put your Facebook\'s Pixel identifier, you can get it anytime from your ') .
                            SmartForm::genDesc($this->l('Facebook ads Manager'), ['a', 'href="https://www.facebook.com/events_manager2/" title="' . $this->l('Facebook ads Manager') . '" target="_blank"', 'br']) .
                            SmartForm::genDesc($this->l('New:'), 'strong') . $this->l('Now you can add multiple IDs by separating them with a comma, although Facebook doesn\'t recommend it.'),
                    ],
                    [
                        'label' => $this->l('Individualize Events'),
                        'type' => 'switch',
                        'name' => 'FCTP_SINGLE_EVENT_TRACKING',
                        'desc' => $this->l('Disabled by default') .
                            SmartForm::openTag('br') .
                            SmartForm::genDesc($this->l('If you enable this setting, each event sent using the Pixel will be triggered only for the configured pixels above.'), 'strong') .
                            SmartForm::openTag('br') .
                            $this->l('This can be useful if you have another pixel installed and you don\'t want the event calls to be shared') . '. ' . $this->l('Use with caution and remember to individualize the events from the other pixel to prevent unwanted event duplicates'),
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit' . $this->name,
                    'id' => 'submit' . $this->name,
                ],
            ],
        ];

        $pixels_ids = explode(',', Configuration::get('FCTP_PIXEL_ID'));
        if (count($pixels_ids) == 0) {
            $pixels_ids = [''];
        }
        $inputFields = [];

        $inputFields[] = [
            'label' => $this->l('Enable/Disable the Conversion API'),
            'type' => 'switch',
            'name' => 'FCTP_CONVERSION_API',
            'desc' => $this->l('enable / disable the usage of the Conversion API to send the events'),
            'is_bool' => true,
            'values' => $switch_options,
        ];

        $pix_id = 1;
        foreach ($pixels_ids as $pixel_id) {
            $inputFields[] = [
                'label' => '',
                'type' => 'free',
                'class' => '',
                'name' => 'FCTP_FREE',
                'desc' => SmartForm::genDesc($this->l('Conversion API for Pixel ID:') . ' ' .
                    $pixel_id, ['h3', 'class="modal-title text-info"']),
            ];

            $inputFields[] = [
                'label' => $this->l('Access Token'),
                'type' => 'text',
                'class' => '',
                'name' => 'FCTP_CAPI_TOKEN_' . $pix_id,
                'desc' => $this->l('Add the access token provided by Facebook') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('When you configure the Conversion API for the first time, Facebook will generate an access token') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc(sprintf($this->l('Click here to get the Conversions API Token for the Pxel ID (%s)'), $pixel_id), ['a', 'href="https://www.facebook.com/events_manager2/list/pixel/' . $pixel_id . '/settings" target="_blank"']) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('On the Conversions API options you will find a text link to "Generate Access Token", click once to generate the Token. Click again to copy it.') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Once copied, past it here.') .
                    SmartForm::openTag('br') .
                    SmartForm::closeTag('p') .
                    SmartForm::genDesc($this->l('If the token is not added, the conversions won\'t be sent.'), 'p'),
            ];

            $inputFields[] = [
                'label' => $this->l('Test Code'),
                'type' => 'text',
                'class' => 'input fixed-width-lg mt-18',
                'name' => 'FCTP_CONVERSION_API_TEST_' . $pix_id,
                'desc' => SmartForm::genDesc($this->l('Only for testing purposes'), 'strong', 'br') .
                    SmartForm::openTag('br') .
                    $this->l('Facebook allows to test the Conversions API before going to production') .
                    sprintf($this->l('In the %s, go to the TEST Events section and click over the Test code for the Conversion API.'), SmartForm::genDesc($this->l('Events Manager'), ['a', 'href="https://business.facebook.com/events_manager2" target="_blank"'])) .
                    SmartForm::openTag('br') .
                    $this->l('This will copy the code') . SmartForm::openTag('br') .
                    $this->l('Enter here your Test Event code. This will enable the test mode for the conversion API') .
                    SmartForm::openTag('br') .
                    $this->l('Empty this field to disable the test mode for the conversion API'),
            ];
            ++$pix_id;
        }

        $morefields = [
            [
                'label' => $this->l('Advanced Matching Options'),
                'type' => 'switch',
                'name' => 'FCTP_ADVANCED_MATCHING_OPTIONS',
                'desc' => $this->l('The advanced matching options are useful to be able to properly match the Pixel Events and the Conversion API to make it easier for Facebook to deduplicate the events (prevent event duplications)') .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('If you enable this setting, make sure you specify it on your Privacy Policy'), 'strong'),
                'is_bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'amo_options',
                'label' => $this->l('Advanced Options Data'),
                'type' => 'checkbox',
                'name' => 'FCTP_AMO_DATA',
                'hint' => $this->l('Select the options to set up the data to be sent'),
                'desc' => SmartForm::genDesc($this->l('Select which kind of data you want to sent to Facebook'), 'strong'),
                'values' => [
                    'query' => $advanced_matching_data,
                    'id' => 'id_option',
                    'name' => 'name',
                ],
            ],
            [
                'label' => SmartForm::genDesc($this->l('Domain Verification'), ['h3', 'class="modal-title text-info"']),
                'type' => 'free',
                'class' => '',
                'name' => 'FCTP_FREE',
                'desc' => SmartForm::openTag('br') .
                    SmartForm::openTag('hr', null, true) .
                    $this->l('In order to use the Aggregated Events Measurement and the Conversion API facebook will ask to validate the domain') .
                    SmartForm::openTag('br') .
                    $this->l('The domain validation can be done by any of this 3 ways:') .
                    SmartForm::genList([
                        $this->l('Upload a file'),
                        $this->l('Add a TXT register on the domain\'s DNS'),
                        SmartForm::genDesc($this->l('Adding a Metadata with the validation key'), 'strong'),
                    ]) .
                    $this->l('The module allows you to validate the domain by adding the validation key inside a specific a metadata, if you want to validate it this way just copy the content value of the meta-data in the following field') . '.',
            ],
            [
                'label' => $this->l('Domain verification'),
                'type' => 'text',
                'class' => '',
                'name' => 'FCTP_VERIFY_DOMAIN',
                'desc' => $this->l('If your domain is not verified, Facebook will ask you to pass the verification') .
                    SmartForm::openTag('br') .
                    $this->l('Go to the %s and click on the meta-tag verification, then copy the meta-tag and paste it here.') .
                    SmartForm::openTag('br') .
                    $this->l('Save and go back to Facebook to validate the your domain') . '.',
            ],
        ];

        $inputFields = array_merge($inputFields, $morefields);

        $inputFields[] = [
            'label' => SmartForm::openTag('h3', 'class="modal-title text-info"') . $this->l('IP related options') . SmartForm::closeTag('h3'),
            'type' => 'free',
            'class' => '',
            'name' => 'FCTP_FREE',
            'desc' => SmartForm::openTag('br') .
                SmartForm::openTag('hr', null, true),
        ];
        $inputFields[] = [
            'label' => $this->l('IP Management'),
            'type' => 'select',
            'name' => 'PP_IP_TYPE',
            'desc' => $this->l('Select how the module should treat the IP') .
                SmartForm::openTag('br') .
                $this->l('Send RAW IP') . ': ' . $this->l('Send to Facebok the first IP obtained from the visitor') .
                SmartForm::openTag('br') .
                $this->l('Try to get IPV6') . ': ' . $this->l('Try to check if the visitor\'s provider has a valid IPV6, if it\'s found, send the IPV6 instead of the IPV4') .
                SmartForm::openTag('br') .
                $this->l('Force IPV6') . ': ' . $this->l('Try to check if the visitor\'s provider has a valid IPV6, if it\'s found, send the IPV6 if not, convert the visitor\'s IPV4 to a valid IPV6'),
            'options' => [
                'query' => [
                    [
                        'id' => 'raw',
                        'name' => $this->l('Send RAW IP'),
                    ],
                    [
                        'id' => 'try',
                        'name' => $this->l('Try to get IPV6'),
                    ],
                    [
                        'id' => 'force',
                        'name' => $this->l('Force IPV6'),
                    ],
                ],
                'id' => 'id',
                'name' => 'name',
            ],
        ];
        $inputFields[] = [
            'label' => SmartForm::openTag('h3', 'class="modal-title text-info"') . $this->l('Test & Debugging Options') . SmartForm::closeTag('h3'),
            'type' => 'free',
            'class' => '',
            'name' => 'FCTP_FREE',
            'desc' => SmartForm::openTag('br') .
                SmartForm::openTag('hr', null, true),
        ];
        $inputFields[] = [
            'label' => $this->l('Restrict Tests & debug by IP') . ' (' . $this->l('Recommended') . ')',
            'type' => 'textbutton',
            'name' => 'FCTP_CONVERSION_IP_LOG',
            'desc' => SmartForm::genDesc($this->l('Highly recommended'), 'strong') .
                SmartForm::openTag('br') .
                $this->l('Limit the logging feature to an IP and prevent excessive logs') .
                SmartForm::openTag('br') .
                $this->l('Not limiting the logging feature to certain IPs may slow down your entire site as the module will wait for the Facebook\'s response if the log is enabled') . '.',
            'validation' => 'isGenericName',
            'size' => 20,
            'button' => [
                'attributes' => [
                    'class' => 'btn btn-outline-primary add_ip_button',
                    'onclick' => 'addRemoteAddr(\'FCTP_CONVERSION_IP_LOG\');',
                ],
                'label' => $this->l('Add my IP'),
                'icon' => 'plus',
            ],
        ];
        $inputFields[] = [
            'label' => $this->l('Enable test code Events'),
            'type' => 'switch',
            'class' => '',
            'is_bool' => true,
            'values' => $switch_options,
            'name' => 'FCTP_ENABLE_TEST_EVENTS',
            'desc' => $this->l('Enable the settings to track test events. You have to fill the test code as well  before enabling this option.'),
        ];
        $inputFields[] = [
            'label' => $this->l('Log API Events'),
            'type' => 'switch',
            'class' => '',
            'is_bool' => true,
            'values' => $switch_options,
            'name' => 'FCTP_CONVERSION_LOG',
            'desc' => $this->l('Enable this setting to generate a log on PrestaShop each time an event has been sent through the API.'),
        ];
        $inputFields[] = [
            'label' => $this->l('Save the Payload in the Log'),
            'type' => 'switch',
            'class' => '',
            'is_bool' => true,
            'values' => $switch_options,
            'name' => 'FCTP_CONVERSION_PAYLOAD',
            'desc' => $this->l('Save to the PrestaShop log the exact payload sent to Facebook with the API Conversion') . '. ' . $this->l('Should only be enabled for debug purposes'),
        ];
        $inputFields[] = [
            'label' => $this->l('Log API Issues / aborts'),
            'type' => 'switch',
            'class' => '',
            'is_bool' => true,
            'values' => $switch_options,
            'name' => 'FCTP_CONVERSION_LOG_ISSUES',
            'desc' => $this->l('Enable this setting to generate a log on PrestaShop each time the API has found an issue or skiped an event.') . '.' .
                SmartForm::openTag('br') .
                $this->l('This is merely informative and doesn\'t mean there is any issue with the module, but it can be used to see which events have been discarded and for what reasons') .
                SmartForm::openTag('br') .
                SmartForm::genDesc($this->l('If you have enabled the logs and you don\'t see anything, you may need to enable this setting to see if there is any error'), 'strong'),
        ];
        $inputFields[] = [
            'label' => $this->l('Log API Process (Other Events)'),
            'type' => 'switch',
            'class' => '',
            'is_bool' => true,
            'values' => $switch_options,
            'name' => 'FCTP_CONVERSION_LOG_OTHER',
            'desc' => $this->l('Enable this option to generate a detailed log in PrestaShop for key steps in the API process. This is useful for troubleshooting issues with sending events to Facebook. Ensure IP restrictions are enabled to generate secure and comprehensive logs.') . '.' .
                SmartForm::openTag('br') .
                $this->l('It is recommended to enable this feature only for a few minutes, as excessive logging can occur. It will automatically disable after 10 minutes to prevent large log files.') . '.',
        ];
        if (function_exists('curl_multi_init')) {
            $inputFields[] = [
                'label' => $this->l('Disable Curl-Multi'),
                'type' => 'switch',
                'class' => '',
                'is_bool' => true,
                'values' => $switch_options,
                'name' => 'FCTP_DISABLE_CURL_MULTI',
                'desc' => $this->l('Enable this setting to generate a log on PrestaShop each time the API has found an issue or skiped an event.') . '.' .
                    SmartForm::openTag('br') .
                    $this->l('This is merely informative and doesn\'t mean there is any issue with the module, but it can be used to see which events have been discarded and for what reasons'),
            ];
        }
        $inputFields[] = [
            'label' => $this->l('Disable Short Timeout on TESTS') . ' - CURL',
            'type' => 'switch',
            'class' => '',
            'is_bool' => true,
            'values' => $switch_options,
            'name' => 'FCTP_DISABLE_SHORT_TIMEOUT',
            'desc' => $this->l('Enable this setting from wait the regular 30 seconds for a response instead of using the 2 second limit predefined in the module.') . '.' .
                SmartForm::openTag('br') .
                $this->l('This will give more time to Facebook to send the response and allow the server to increate the time while it waits for it') . '.' .
                SmartForm::openTag('br') .
                $this->l('It\'s recommended to enable this only for testing purposes and only in case you can\'t get a response from Facebook') . '.',
        ];
        $inputFields[] = [
            'label' => SmartForm::genDesc($this->l('Log Access')),
            'type' => 'free',
            'class' => '',
            'name' => 'FCTP_FREE',
            'desc' => sprintf($this->l('The logs generated will be saved in the PrestaShop log which you can access from here: %s'), '<a target="_blank" href="' . $this->context->link->getAdminLink('AdminLogs') . '">' . $this->l('Advanced Parameters > Logs') . '</a>') .
                SmartForm::openTag('br') .
                SmartForm::openTag('br') .
                sprintf($this->l('You can also check a more readable version in the %s section inside the module'), SmartForm::openTag('a', 'href="#pixel_plus_logs" class="target-menu"') . $this->l('CAPI Logs') . SmartForm::closeTag('a')),
        ];

        $fields_form['conversion_api'] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Conversion API'),
                    'icon' => 'icon-cog',
                ],
                'description' => $this->l('Enable / disable the usage of the Conversion API') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Nowadays there are a lot of situations where the pixel falls behind. For example the Ad Blockers and the iOS +14.5 users') .
                    SmartForm::openTag('br') .
                    $this->l('The Conversion API sends the event directly from the Server to Facebook, skipping some of the barriers that pervents the pixel from functioning normally') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('When the event is send from the pixel and the Conversion API Facebook will try to deduplicate it, keeping only one of the two'),
                'input' => $inputFields,
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit' . $this->name,
                    'id' => 'submit' . $this->name,
                ],
            ],
        ];
        $fields_form['test_mode'] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Test Mode'),
                    'icon' => 'icon-flask',
                ],
                'description' => $this->l('Enable the test mode if you want to try some module options') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('The test mode will only allow the module to be viewed by the IPS added in the field below'),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Test Mode'),
                        'desc' => $this->l('Also known as sandbox mode, enable this option to only display the module for the allowed IPs') .
                            SmartForm::openTag('br') .
                            $this->l('Enter the allowed IPs in the following field'),
                        'name' => 'FCTP_TEST_MODE',
                        'hint' => $this->l('The sandbox mode or test mode is recommended when installing the module for the first time or when trying a beta feature'),
                        'bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => $this->l('Restrict Tests & debug by IP') . ' (' . $this->l('Recommended') . ')',
                        'type' => 'textbutton',
                        'name' => 'FCTP_TEST_MODE_IPS',
                        'hint' => $this->l('Limit the module to few IPs while testing it to not affect your users experience meanwhile'),
                        'desc' => SmartForm::genDesc($this->l('Restrict the module by IP'), 'strong', 'br') .
                            $this->l('This is highly recommended when you want to "play" or "test" the module configuration and options.'),
                        'validation' => 'isGenericName',
                        'size' => 20,
                        'button' => [
                            'attributes' => [
                                'class' => 'btn btn-outline-primary add_ip_button',
                                'onclick' => 'addRemoteAddr(\'FCTP_TEST_MODE_IPS\');',
                            ],
                            'label' => $this->l('Add my IP'),
                            'icon' => 'plus',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit' . $this->name,
                    'id' => 'submit' . $this->name,
                ],
            ],
        ];

        $empty_default_values = SmartForm::openTag('br') .
            $this->l('Leave empty to use the current cart value');
        $values_allowed = SmartForm::openTag('br') .
            $this->l('Positive numbers allowed, use points for decimal separator.') .
            SmartForm::openTag('br') .
            $this->l('Examples:') . ' 0, 1, 1.5, 1.8, 2.5, 3.99, ...';

        $fields_form['track_events'] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Trackable Events'),
                    'icon' => 'icon-facebook',
                ],
                'description' => $this->l('Enable / disable the events you want to track through the pixel and the API') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Some events may take the value of the current item/s (dynaimc value), but some others may require to establish a value for each recorded action') .
                    SmartForm::openTag('br') .
                    $this->l('Use the field Value to establish each event worth'),
                'input' => [
                    [
                        'label' => SmartForm::genDesc($this->l('Basic Events'), ['h3', 'class="modal-title text-info"']),
                        'type' => 'free',
                        'class' => '',
                        'name' => 'FCTP_FREE',
                        'desc' => SmartForm::openTag('br') .
                            SmartForm::openTag('hr', null, true) .
                            SmartForm::genDesc($this->l('Dynamic Value Events'), 'h4') .
                            SmartForm::genDesc($this->l('Events that depends on the value of the product/s'), 'p'),
                    ],
                    [
                        'label' => 'ViewContent',
                        'type' => 'switch',
                        'name' => 'FCTP_VIEWED',
                        'desc' => SmartForm::genDesc($this->l('Track all Viewed Products'), 'strong') .
                            SmartForm::openTag('br') .
                            $this->l('Enable this option to track all products viewed') . '. ' .
                            $this->l('Product listings like category or search are excluded'),
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => 'AddToCart',
                        'type' => 'switch',
                        'name' => 'FCTP_ADD_TO_CART',
                        'desc' => SmartForm::openTag('strong') .
                            $this->l('Track add to cart') .
                            SmartForm::openTag('br') .
                            $this->l('Dynamic Event:') .
                            SmartForm::closeTag('strong') .
                            $this->l('See more information at the end of this page') .
                            SmartForm::openTag('br') .
                            $this->l('Will trigger every time a user adds an item to their cart') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => 'Purchase',
                        'type' => 'switch',
                        'name' => 'FCTP_CONV',
                        'desc' => SmartForm::genDesc($this->l('Track Conversions'), 'strong') .
                            SmartForm::openTag('br') .
                            $this->l('Enable this option to track all conversions made from your site') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => SmartForm::genDesc($this->l('Order Process related events'), ['h3', 'class="modal-title text-info"']),
                        'type' => 'free',
                        'class' => '',
                        'name' => 'FCTP_FREE',
                        'desc' => SmartForm::openTag('br') .
                            SmartForm::openTag('hr', null, true) .
                            SmartForm::genDesc($this->l('Custom Value Events'), 'h4') .
                            SmartForm::genDesc($this->l('Events that can send a static value each time they are performed'), 'p'),
                    ],
                    [
                        'label' => $this->l('Start Order') . ' (InitiateCheckout)',
                        'type' => 'switch',
                        'name' => 'FCTP_START_ORD',
                        'desc' => $this->l('Will trigger when a user starts the order\'s funnel process') . ' (' . $this->l('When a customer clicks on "Proceed to Checkout"') . ')',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_START_ORD_VALUE',
                        'desc' => $this->l('Set a numeric value for this event.') .
                            $empty_default_values .
                            $values_allowed,
                    ],
                    [
                        'label' => $this->l('Track Registrations') . ' (CompleteRegistration)',
                        'type' => 'switch',
                        'name' => 'FCTP_REG',
                        'desc' => $this->l('Will trigger when a user registers to your site') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_REG_VALUE',
                        'desc' => $this->l('Set a numeric value for this event.') .
                            $values_allowed,
                    ],
                    [
                        'label' => $this->l('Start Payment') . ' (AddPaymentInfo)',
                        'type' => 'switch',
                        'name' => 'FCTP_START',
                        'desc' => $this->l('Will trigger when a user starts the order\'s payment process') . ' (' . $this->l('beta feature, it may not work with all payment methods') . ')',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_START_VALUE',
                        'desc' => $this->l('Set a numeric value for this event.') .
                            $values_allowed,
                    ],
                    [
                        'label' => SmartForm::genDesc($this->l('Content Viewed related events'), ['h3', 'class="modal-title text-info"']),
                        'type' => 'free',
                        'class' => '',
                        'name' => 'FCTP_FREE',
                        'desc' => SmartForm::openTag('br') .
                            SmartForm::genDesc($this->l('Custom Value Events'), 'h4') .
                            SmartForm::genDesc($this->l('Events that can send a static value each time they are performed'), 'p'),
                    ],
                    [
                        'label' => 'ViewCategory',
                        'type' => 'switch',
                        'name' => 'FCTP_CATEGORY',
                        'desc' => SmartForm::genDesc($this->l('Track Categories'), 'strong') .
                            SmartForm::openTag('br') .
                            $this->l('Enable this option to track all categories viewed') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_CATEGORY_VALUE',
                        'desc' => $values_allowed,
                    ],
                    [
                        'label' => 'ViewCMS',
                        'type' => 'switch',
                        'name' => 'FCTP_CMS',
                        'desc' => SmartForm::genDesc($this->l('Track Cms Pages'), 'strong') .
                            SmartForm::openTag('br') .
                            $this->l('Enable this option to track cms pages viewed') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_CMS_VALUE',
                        'desc' => $values_allowed,
                    ],
                    [
                        'label' => $this->l('Track Searches'),
                        'type' => 'switch',
                        'name' => 'FCTP_SEARCH',
                        'desc' => $this->l('Will trigger when a user performs a search on your site') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_SEARCH_VALUE',
                        'desc' => $values_allowed,
                    ],
                    [
                        'label' => SmartForm::genDesc($this->l('Actions on page related events'), ['h3', 'class="modal-title text-info"']),
                        'type' => 'free',
                        'class' => '',
                        'name' => 'FCTP_FREE',
                        'desc' => SmartForm::openTag('br') .
                            SmartForm::genDesc($this->l('Custom Value Events'), 'h4') .
                            SmartForm::genDesc($this->l('Events that can send a static value each time they are performed'), 'p'),
                    ],
                    [
                        'label' => $this->l('Add to Wishlist'),
                        'type' => 'switch',
                        'name' => 'FCTP_WISH',
                        'desc' => $this->l('Will trigger when a user adds an item to a wishlist') . SmartForm::openTag('br') . $this->l('It\'s mandatory to have the Prestashop\'s Block Wishlist Module activated for this to work') . $this->l('Wishlist event will trigger only on the custom selector added in the Advanced options'),
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_WISH_VALUE',
                        'desc' => $values_allowed,
                    ],
                    [
                        'label' => $this->l('Contact Form') . ' (Contact)',
                        'type' => 'switch',
                        'name' => 'FCTP_CONTACT_US',
                        'desc' => SmartForm::genDesc($this->l('Track Contact Form'), 'strong') .
                            SmartForm::openTag('br') .
                            $this->l('Enable this option to track contact form used') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_CONTACT_US_VALUE',
                        'desc' => $values_allowed,
                    ],
                    [
                        'label' => $this->l('Used a discount') . ' (Discount)',
                        'type' => 'switch',
                        'name' => 'FCTP_DISCOUNT',
                        'desc' => SmartForm::genDesc($this->l('Track discount added'), 'strong') .
                            SmartForm::openTag('br') .
                            $this->l('Enable this option to track discount added') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_DISCOUNT_VALUE',
                        'desc' => $values_allowed,
                    ],
                    [
                        'label' => $this->l('Subscribed to the Newsletter') . ' (Newsletter)',
                        'type' => 'switch',
                        'name' => 'FCTP_NEWSLETTER',
                        'desc' => SmartForm::genDesc($this->l('Track newsletter'), 'strong') .
                            SmartForm::openTag('br') . $this->l('Enable this option to track contact form used') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_NEWSLETTER_VALUE',
                        'desc' => $values_allowed,
                    ],
                    [
                        'label' => SmartForm::genDesc($this->l('Events to improve the Audiences quality'), ['h3', 'class="modal-title text-info"']),
                        'type' => 'free',
                        'class' => '',
                        'name' => 'FCTP_FREE',
                        'desc' => SmartForm::openTag('br') .
                            SmartForm::genDesc($this->l('Custom Value Events'), 'h4') .
                            SmartForm::genDesc($this->l('Events that can send a static value each time they are performed'), 'p'),
                    ],
                    [
                        'label' => $this->l('Time Spent on page') . ' (Time)',
                        'type' => 'switch',
                        'name' => 'FCTP_PAGETIME',
                        'desc' => SmartForm::genDesc($this->l('Track time spent on page'), 'strong') .
                            SmartForm::openTag('br', '', true) .
                            $this->l('Enable this option to track the time user spends on page') .
                            SmartForm::openTag('br') .
                            $this->l('This setting will create a specific event every 30 seconds') . ':' .
                            SmartForm::openTag('br', '', true) .
                            'Time30, Time60, Time90...' .
                            SmartForm::openTag('br', '', true) .
                            SmartForm::openTag('br', '', true) .
                            $this->l('Use it to create audiences for those users who have spent more than... in your pages'),
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_PAGETIME_VALUE',
                        'desc' => $values_allowed,
                    ],
                    [
                        'label' => $this->L('Number of pages viewed') . '(PagesViewed)',
                        'type' => 'switch',
                        'name' => 'FCTP_PAGEVIEW_COUNT',
                        'desc' => SmartForm::genDesc($this->l('Track the number of pages viewed'), 'strong') .
                            SmartForm::openTag('br', '', true) .
                            $this->l('Specify a duration to let the module keep the number of pages viewed in that period') .
                            SmartForm::openTag('br') .
                            $this->l('This setting will create a specific event every 5 pages viewed') . ':' .
                            SmartForm::openTag('br', '', true) .
                            'PagesViewed5, PagesViewed10, PagesViewed15, PagesViewed20 and PagesViewedMore20...' .
                            SmartForm::openTag('br', '', true) .
                            SmartForm::openTag('br', '', true) .
                            $this->l('Use it to create audiences for those users who have visited more than X pages on your site') . '.',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-18',
                        'prefix' => $this->l('Value:'),
                        'suffix' => $this->l('€'),
                        'name' => 'FCTP_PAGEVIEW_COUNT_VALUE',
                        'desc' => $values_allowed,
                    ],
                    [
                        'label' => '',
                        'type' => 'text',
                        'class' => 'input fixed-width-lg mt-16',
                        'prefix' => $this->l('Duration:'),
                        'suffix' => $this->l('Day/s'),
                        'name' => 'FCTP_PAGEVIEW_COUNT_COOKIE_DAYS',
                        'desc' => $this->l('Set a numeric value for this event.') .
                            SmartForm::openTag('br') .
                            $this->l('Days to keep the count.') .
                            SmartForm::openTag('br') .
                            $this->l('Examples:') . ' 1, 2, 3, ...',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit' . $this->name,
                    'id' => 'submit' . $this->name,
                ],
            ],
        ];
        $fields_form['additional_events'] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Additional options for events'),
                    'icon' => 'icon-sliders',
                ],
                'description' => $this->l('Fine tune the options for the Search and ViewCategory events'),
                'input' => [
                    [
                        'label' => $this->l('Search Results Items?'),
                        'type' => 'select',
                        'name' => 'FCTP_SEARCH_ITEMS',
                        'desc' => $this->l('Set up the number of items that will be sent to Facebook') .
                            ' (' . $this->l('between 5 and 10') . ')',
                        'options' => [
                            'query' => $num_items_options,
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'label' => $this->l('Category Results Items?'),
                        'type' => 'select',
                        'name' => 'FCTP_CATEGORY_ITEMS',
                        'desc' => $this->l('Set up the number of items that will be sent to Facebook') .
                            ' (' . $this->l('between 5 and 10') . ')',
                        'options' => [
                            'query' => $num_items_options,
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'label' => $this->l('Use Category Top sellers') . ' / ' . $this->l('Use Category default listing'),
                        'type' => 'switch',
                        'name' => 'FCTP_CATEGORY_TOP_SALES',
                        'desc' => $this->l('Choose yes if you want to send Facebook the top selling products for dynamic ads') .
                            SmartForm::openTag('br') .
                            $this->l('Choose no if you want to send the products ordered by position inside the category'),
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit' . $this->name,
                    'id' => 'submit' . $this->name,
                ],
            ],
        ];
        $fields = [];
        foreach ($shops as $shop) {
            // Start with the Dynamic Product Ads options
            if (Shop::isFeatureActive()) {
                $fields[] = [
                    'label' => SmartForm::genDesc($this->l('%s Product Options'), ['h3', 'class="modal-title text-info"'], null, [$shop['name']]),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true) . sprintf($this->l('Configure Product and combination options for Shop "%s"'), $shop['name']),
                ];
            }
            $fields[] = [
                'type' => 'text',
                'label' => $this->l('Product identifier Prefix'),
                'name' => 'FPF_PREFIX_' . $shop['id_shop'],
                'size' => 20,
                'desc' => $this->l('If your feed does have a Prefix for IDs just enter it here') . '. ' . $this->l('Otherwise you can leave it blank') . '.',
            ];
            $fields[] = [
                'label' => $this->l('Enable combinations tracking?'),
                'type' => 'switch',
                'name' => 'FCTP_COMBI_' . $shop['id_shop'],
                'is_bool' => true,
                'values' => $switch_options,
            ];
            $fields[] = [
                'type' => 'text',
                'label' => $this->l('Combinations Prefix'),
                'name' => 'FCTP_COMBI_PREFIX_' . $shop['id_shop'],
                'size' => 20,
                'desc' => $this->l('If you want to use the combinations tracking they will be added after the product ID, use this prefix to separate the actual ID from the combination ID') . '. ' . $this->l('Otherwise you can leave it blank') . '.',
            ];
        }
        $fields_form['product_options'] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Product & Combination Options'),
                    'icon' => 'icon-sliders',
                ],
                'description' => $this->l('Set-up how the product data will be sent') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Set up a Prefix for your products, send the current product combination information (if you use combinations) and even set up a prefix for the combinations to prevent duplicates in the ID field.'),
                'input' => $fields,
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit' . $this->name,
                    'id' => 'submit' . $this->name,
                ],
            ],
        ];
        $fields = [];
        foreach ($shops as $shop) {
            if (Shop::isFeatureActive()) {
                $fields[] = [
                    'label' => SmartForm::genDesc($this->l('%s Catalogue IDs'), ['h3', 'class="modal-title text-info"'], null, $shop['name']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true) . sprintf($this->l('Configure Product and combination options for Shop "%s"'), $shop['name']),
                ];
            }
            if (self::$feed_v2) {
                $fields[] = [
                    'type' => 'text',
                    'label' => sprintf($this->l('%s\'s Catalogue ID'), $shop['name']),
                    'name' => 'FCTP_FEED_' . $shop['id_shop'],
                    'size' => 20,
                    'placeholder' => $this->l('Facebook\'s catalogue identifier'),
                    'desc' => $this->l('Enter the ID of the Product Catalogue from Facebook in order to link your pixel data to a catalogue.') .
                        SmartForm::openTag('br') .
                        $this->l('To get your catalogue ID go to Facebook Ads Manager > Catalogues') . '.' .
                        $this->l('Then under the catalogue name you will see a large number, that is the ID, copy the ID and paste it here'),
                ];
            } else {
                foreach ($langs as $lang) {
                    // $fields_value[] = 'FPF_'.$shop['id_shop'].'_'.$lang['id_lang'];
                    // Init Fields form array
                    $fields[] = [
                        'type' => 'text',
                        'label' => $lang['name'] . ' ' . $this->l('Catalogue ID'),
                        'name' => 'FPF_' . $shop['id_shop'] . '_' . $lang['id_lang'],
                        'size' => 20,
                        'placeholder' => $this->l('Facebook\'s feed identifier'),
                        'desc' => $this->l('Enter the ID of the Product Catalogue from Facebook in order to link your pixel data to a catalogue.') .
                            SmartForm::openTag('br') .
                            $this->l('To get your catalogue ID go to Facebook Ads Manager > Catalogues') . '.' .
                            $this->l('Then under the catalogue name you will see a large number, that is the ID, copy the ID and paste it here') .
                            SmartForm::openTag('br') .
                            $this->l('If your shop use a multi-language Feed, just fill all the languages with the same ID'),
                    ];
                }
            }
        }
        // Generate a form for each Shop
        $fields_form['catalogue_ids']['form'] = [
            'legend' => [
                'title' => SmartForm::genDesc($this->l('Catalogue ID Association'), ['span', 'class="shop_name"']),
                'icon' => 'icon-link',
            ],
            'description' => $this->l('Set up the Catalogue ID for each language in your shop') . '.' .
                SmartForm::openTag('br') . SmartForm::openTag('br') .
                $this->l('You can get the catalogue ID from the Commerce Manager in your Facebook Business Manager'),
            'input' => $fields,
            'submit' => [
                'title' => $this->l('Save Configuration'),
                'name' => 'submit' . $this->name,
                'id' => 'submit' . $this->name,
                'class' => 'button',
            ],
        ];
        $random_product_url = $this->getRandomProductURL($this->context->shop->id, true);
        $fields_form['micro_data'] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Micro Data'),
                    'icon' => 'icon-code',
                ],
                'description' => $this->l('Let the module fill in the missing microdata') . '.' .
                    SmartForm::openTag('br') . SmartForm::openTag('br') .
                    $this->l('In order to allow the pixel to be used as a catalogue source the microdata has to be complete') .
                    SmartForm::openTag('br') .
                    $this->l('Here you will find several options to check your themes missing microdata and let the module add the missing one') . '.' .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    sprintf($this->l('If your theme generates bad content for any particular microdata you just need to comment it on the TPLs and then use the %s tool once. The module will detect the missing microdata and will generate it according to Facebook requirements'), $this->l('Fill in the missing microdata?')),
                'input' => [
                    [
                        'label' => $this->l('Fill in the missing microdata?'),
                        'type' => 'switch',
                        'name' => 'FCTP_FILL_MICRO_DATA',
                        'desc' => $this->l('If this option is active, the module will fill in the missing microdata that has been detected.') . SmartForm::closeTag('p') .
                            SmartForm::genDesc($this->l('Disable this setting if you don\'t want the module to insert the missing microdata.'), ['p', 'class="help-block"']),
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => $this->l('Current Missing Micro Data detected:'),
                        'type' => 'free',
                        'name' => 'FCTP_FREE',
                        'desc' => SmartForm::closeTag('p') . $missing_micro,
                    ],
                    [
                        'label' => $this->l('Review Micro Data?'),
                        'type' => 'switch',
                        'name' => 'FCTP_CHECK_MICRO_DATA',
                        'desc' => $this->l('Activate this option and save to force the module to check and fix the theme\'s microdata') .
                            SmartForm::closeTag('p') .
                            SmartForm::openTag('p', 'class="help-block"') .
                            SmartForm::genDesc($this->l('This setting won\'t keep active, just activate it once to check your theme. After reviewing it the missing data will be displayed below'), 'strong') .
                            SmartForm::openTag('p', 'class="help-block"') .
                            SmartForm::closeTag('p') .
                            $this->l('This process is automatically done on module installation and you will only need to activate it if you have changed your theme or the theme structure.') .
                            SmartForm::closeTag('p') .
                            SmartForm::openTag('hr') .
                            SmartForm::openTag('p', 'class="help-block"') .
                            $this->l('Micro Data is used by Facebook to generate a product catalogue from the pixel evens. Pixel Plus reviews the current theme and fix all the missing micro data.') . '. ' .
                            $this->l('This have several benefits besides being able to generate the Product Catalogues. Correct micro data will improve the page\'s SEO and the quality of the shared content.') .
                            SmartForm::closeTag('p'),
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'label' => SmartForm::genDesc($this->l('Microdata manipulation options'), ['h3', 'class="modal-title text-info"']),
                        'type' => 'free',
                        'class' => '',
                        'name' => 'FCTP_FREE',
                        'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true) . $this->l('Alter the microdata generation to fit your needs'),
                    ],
                    [
                        'label' => $this->l('Ignore cover?'),
                        'type' => 'switch',
                        'name' => 'FCTP_MICRO_IGNORE_COVER',
                        'desc' => $this->l('Activate this option to ignore the cover image and use the fist one as the cover') . ' (' . $this->l('use the product images order') . ')',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Images limit'),
                        'name' => 'FCTP_MICRO_IMG_LIMIT',
                        'size' => 20,
                        'placeholder' => $this->l('Enter the number of images to export'),
                        'desc' => $this->l('Enter a value in this field if you need to limit the number of images you want to send to Facebook.'),
                    ],
                    [
                        'label' => $this->l('Microdata Debug tool'),
                        'type' => 'free',
                        'name' => 'FCTP_FREE',
                        'desc' => SmartForm::genDesc($this->l('Facebook has a tool to debug the microdata, this tool will help you to find if the microdata from your products comply with Facebook requirements'), 'p') .
                            SmartForm::openTag('p') . $this->l('To use the tool you first need a product URL, like this one:') .
                            SmartForm::genDesc($this->l('Click to copy the product URL'), ['a', 'class="badge link_copy" href="' . $random_product_url . '"']) .
                            SmartForm::closeTag('p') .
                            SmartForm::openTag('p') .
                            sprintf($this->l('Then, open the %s'), SmartForm::genDesc($this->l('Microdata Debug Tool'), ['a', 'href="https://business.facebook.com/ads/microdata/debug?url=' . urlencode($random_product_url) . '" target="_blank"'])) .
                            SmartForm::genDesc($this->l('And run the test'), 'p') .
                            SmartForm::genDesc($this->l('If you don\'t see any error message (in red), then your products microdata is ready and soon the pixel will be elegible as a product source'), 'p'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit' . $this->name,
                    'id' => 'submit' . $this->name,
                ],
            ],
        ];
        if ($og || $schema) {
            $tmp = [];
            if ($og) {
                $tmp[] = [
                    'label' => $this->l('Fill in the OG-related microdatadata?'),
                    'type' => 'switch',
                    'name' => 'FCTP_MICRO_OG',
                    'desc' => $this->l('Activate this setting to generate the Microdata for Open Graph'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ];
            }
            if ($schema) {
                $tmp[] = [
                    'label' => $this->l('Fill in the Schema-related microdatadata?'),
                    'type' => 'switch',
                    'name' => 'FCTP_MICRO_SCHEMA',
                    'desc' => $this->l('Activate this setting to generate the Microdata for Schema'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ];
            }
            $fields_form['micro_data']['form']['input'] = PixelTools::insertItems($fields_form['micro_data']['form']['input'], $tmp, 2);
        }
        $fields_form['gdpr'] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('GDPR & Cookies consent'),
                    'icon' => 'icon-lock',
                ],
                'description' => SmartForm::genDesc($this->l('Completely block the module') . ':', 'h4') .
                    $this->l('Do you use a module to block the cookies or a third party App?') . '.' .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('In Europe, the cookies have to be blocked before the consent is granted and therefore the module should be blocked too') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('It is recommended that you don\'t use a third-party module to block the Pixel Plus module. Instead, configure the Pixel Plus module to work alongside your GDPR cookie consent module.'), 'strong') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Use the following options to match your cookie blocking system and let the module work accordingly') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('You can contact us if you need assistance in configuring this section') .
                    SmartForm::openTag('hr') .
                    SmartForm::genDesc($this->l('Block the module through a JS variable') . ':', 'h4') .
                    $this->l('The module also has a JS variable to block the module, this will block all JS related events but it not block completely the API.') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    sprintf($this->l(' To use it, just set up the %s variable %s to %s'), 'JS', SmartForm::genDesc('doNotConsentToPixel', 'strong'), SmartForm::genDesc('true', 'strong')),
                'input' => [
                    [
                        'label' => $this->l('Block the script?'),
                        'type' => 'switch',
                        'name' => 'FCTP_BLOCK_SCRIPT',
                        'desc' => $this->l('Block the Pixel Events script if the cookies are not allowed') . SmartForm::openTag('br') .
                            $this->l('Enabling this option will pause the Facebook Pixel Events until a certain cookie is found') . '. ' . $this->l(' You can configure the options in the following fields.') . SmartForm::openTag('br'),
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                    [
                        'form_group_class' => 'fctp_cookies',
                        'label' => $this->l('Initiate Checkout Mode'),
                        'type' => 'select',
                        'name' => 'FCTP_BLOCK_SCRIPT_MODE',
                        'desc' => sprintf($this->l('Choose what will trigger the %s event'), 'InitiateCheckout'),
                        'options' => [
                            'query' => [
                                [
                                    'id_option' => 'cookies',
                                    'name' => $this->l('Cookies'),
                                ],
                                [
                                    'id_option' => 'local_storage',
                                    'name' => $this->l('Local Storage'),
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'form_group_class' => 'fctp_cookies fctp_block_cookies',
                        'type' => 'text',
                        'label' => $this->l('Add the cookie name to look for'),
                        'name' => 'FCTP_COOKIE_NAME',
                        'size' => 20,
                        'desc' => $this->l('Enter the exact name of the cookie that will be set once the Cookie consent has been accepted'),
                    ],
                    [
                        'form_group_class' => 'fctp_cookies fctp_block_cookies',
                        'type' => 'text',
                        'label' => $this->l('Specific Cookie Value?'),
                        'name' => 'FCTP_COOKIE_VALUE',
                        'size' => 5,
                        'class' => 'fixed-width-xl',
                        'desc' => $this->l('Leave it blank if you don\'t need to look for a specific value inside the cookie') . SmartForm::openTag('br') .
                            $this->l('Otherwise, enter the value to search inside the cookie') .
                            SmartForm::openTag('br') .
                            $this->l('If you want to look for multiple values, separate them with a triple pipe |||'),
                    ],
                    [
                        'form_group_class' => 'fctp_cookies fctp_block_cookies',
                        'label' => $this->l('Is an External Cookie?'),
                        'type' => 'switch',
                        'name' => 'FCTP_COOKIE_EXTERNAL',
                        'desc' => $this->l('Enable this setting if you use an external service to handle the cookies') . SmartForm::openTag('br') .
                            $this->l('Disable this setting if the confirmation cookie is set within the PrestaShop cookie') .
                            SmartForm::openTag('br') .
                            SmartForm::openTag('br') .
                            sprintf($this->l('If you\'re using a module that generates the cookie and you don\'t know the cookie name and value you can  %s to generate a temporal token to access the cookies on the front'), SmartForm::genDesc($this->l('click here'), ['a', 'class="badge badge-primary generate-cookies-token"'])) .
                            SmartForm::openTag('br') .
                            SmartForm::openTag('br') .
                            SmartForm::genDesc('', ['span', 'class="badge badge-success print-front-cookies"']),
                        'is_bool' => true,
                        'values' => $cookie_options,
                    ],
                    [
                        'form_group_class' => 'fctp_cookies fctp_block_local_storage',
                        'type' => 'text',
                        'label' => $this->l('Variable path to look for'),
                        'name' => 'FCTP_LOCAL_STORAGE_VAR_PATH',
                        'size' => 5,
                        'class' => 'fixed-width-xl',
                        'desc' => $this->l('Type the path to the internal storage data') .
                            SmartForm::openTag('br') .
                            SmartForm::openTag('br') .
                            $this->l('Type the name of the variable. If it\'s a multidimensional array you can use the ">>" to access a subarray item') .
                            SmartForm::openTag('br') .
                            SmartForm::openTag('br') .
                            sprintf($this->l('For example if the variable name was "%s" and the value was an array in which when the "%s" field value "%s", the consent was granted you should enter:'), 'consent', 'marketing', 'true') . ' consent >> marketing',
                    ],
                    [
                        'form_group_class' => 'fctp_cookies fctp_block_local_storage',
                        'type' => 'text',
                        'label' => $this->l('Expected Value/s'),
                        'name' => 'FCTP_LOCAL_STORAGE_VALUE',
                        'size' => 5,
                        'class' => 'fixed-width-xl',
                        'desc' => $this->l('Type the expected value for the internal storage data when the consent is granted. ') . SmartForm::openTag('br') .
                            SmartForm::openTag('br') .
                            $this->l('Type the name of the variable. If it\'s a multidimensional array you can use the ">>" to access a subarray item') .
                            SmartForm::openTag('br') .
                            SmartForm::openTag('br') .
                            sprintf($this->l('For example if the variable name was "%s" and the value was an array in which when the "%s" field value "%s", the consent was granted you should enter:'), 'consent', 'marketing', 'true') . ' true',
                    ],
                    [
                        'form_group_class' => 'fctp_cookies',
                        'label' => $this->l('Page reloads after the consent?'),
                        'type' => 'switch',
                        'name' => 'FCTP_COOKIE_RELOAD',
                        'desc' => $this->l('Enable this setting if you use an external service to handle the cookies') . SmartForm::openTag('br') .
                            $this->l('Disable this setting if the confirmation cookie is set within the PrestaShop cookie'),
                        'is_bool' => true,
                        'values' => $cookie_options,
                    ],
                    [
                        'form_group_class' => 'fctp_cookies fctp_cookie_reload_inverted',
                        'label' => $this->l('Selector for the Cookies Button'),
                        'type' => 'text',
                        'name' => 'FCTP_COOKIE_BUTTON',
                        'desc' => $this->l('Only if the page does not reload') . SmartForm::openTag('br') .
                            $this->l('Enter the unique selector for the consent button') . SmartForm::openTag('br') .
                            $this->l('If your page does not reload, the module will need to know which button is pressed to accept or decline the cookies to dynamically check if the cookies have been accepted'),
                    ],
                    [
                        'label' => $this->l('Disable advanced consent validation'),
                        'type' => 'switch',
                        'name' => 'FCTP_BLOCK_BASIC',
                        'desc' => $this->l('The consent checking process relies on the id_guest or id_customer cookies. Allowing a greate security when validating the module') . SmartForm::openTag('br') .
                            $this->l('In some rare cases, the validation may fail. Enabling this option will disable the validation and allowing a more plain validation') . '. ',
                        'is_bool' => true,
                        'values' => $switch_options,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit' . $this->name,
                    'id' => 'submit' . $this->name,
                ],
            ],
        ];

        $advanced_options_inputs = [
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('Pixel Load Optimization'), ['h3', 'class="modal-title text-info"']),
                'size' => 20,
                'name' => 'FCTP_FREE',
                'desc' => SmartForm::openTag('br') .
                    SmartForm::openTag('hr') .
                    SmartForm::genDesc($this->l('In order to improve the page load time the Pixel Plus module offers you several optimization techniques'), 'p') .
                    SmartForm::genDesc($this->l('Depending on the page, server and other factors different pages may benefit from different settings.'), 'p') .
                    SmartForm::openTag('p') . SmartForm::genDesc($this->l('Those are the available options:'), 'strong') . SmartForm::closeTag('p') .
                    SmartForm::genList([
                        SmartForm::openTag('p') . SmartForm::genDesc($this->l('Footer'), 'u') . ': ' . $this->l('the module tries to load the pixel on the footer to not block the load time. Your theme must use the footer\'s hook') . SmartForm::closeTag('p'),
                        SmartForm::openTag('p') . SmartForm::genDesc($this->l('Force Header'), 'u') . ': ' .
                        $this->l('Recommended') . '. ' . $this->l('You can also force it on the header take advantage of the asynchronous loading. This may speed up the loading time and reduce the page load time in most cases') . SmartForm::closeTag('p'),
                        SmartForm::openTag('p') . SmartForm::genDesc($this->l('Deferred loading'), 'u') . ': ' .
                        $this->l('This third option allows you to load the pixel after the page is completely load. In some cases this option may reduce the page load time greatly, but it has the counterpart that any action prior to the pixel loading will be not tracked') . SmartForm::closeTag('p'),
                        SmartForm::openTag('p') . SmartForm::genDesc($this->l('Defer only the first time'), 'u') . ': ' .
                        $this->l('Highly recommendable if you use the deferrer mode. This will limit the defer to the first time, so once the script is loaded an in the browser\'s cache the script will be loaded without delay and therefore all the actions will be ready on a faster pace') . SmartForm::closeTag('p')]) .
                    SmartForm::openTag('br'),
            ],
            [
                'label' => $this->l('Force Pixels on Header'),
                'type' => 'switch',
                'name' => 'FCTP_FORCE_HEADER',
                'desc' => $this->l('Disabled by default') .
                    SmartForm::openTag('br') .
                    $this->l('The module tries to output the pixels on the footer of the page, this way the JS doesn\'t block the page render') . ', ' .
                    $this->l(' this way the customer feels the page loading faster and you will also improve your page\'s SEO') .
                    SmartForm::openTag('br') .
                    $this->l('Some themes and customized pages do not have the footer hook, if it\'s your case enable this option') . '.',
                'is_bool' => true,
                'values' => $switch_options,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Deferred loading'),
                'name' => 'FCP_DEFERRED_LOADING',
                'size' => 20,
                'desc' => $this->l('Enable this setting to load the pixel only after the page is loaded'),
                'values' => $switch_options,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Deferred seconds'),
                'name' => 'FCP_DEFERRED_SECONDS',
                'prefix' => $this->l('Value:'),
                'suffix' => $this->l('seconds'),
                'class' => 'input fixed-width-lg mt-18',
                'desc' => $this->l('How many seconds after the page is completely loaded should the script wait for to be loaded, recommended value 2-3 seconds') . SmartForm::openTag('br') .
                    $this->l('Integer values is allowed only.') .
                    SmartForm::openTag('br'),
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Defer only the first time'),
                'name' => 'FCP_DEFER_FIRST_TIME',
                'size' => 20,
                'desc' => $this->l('Facebook ensures the fb_evens.js script will only be downloaded the first time and after that it will be loaded from the cache. This means, theoretically, only the first time we should apply the defer option since the following ones will not take time to be downloaded.'),
                'values' => $switch_options,
            ],
        ];
        $advanced_options_inputs = array_merge(
            $advanced_options_inputs,
            [
                [
                    'label' => SmartForm::genDesc($this->l('Conversion options'), ['h3', 'class="modal-title text-info"']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true),
                ],
                [
                    'label' => $this->l('Exclude Back Office orders'),
                    'type' => 'switch',
                    'name' => 'FCTP_EXCLUDE_BO_ORDERS',
                    'desc' => $this->l('Enabled by default') . '. ' . $this->l('When this option is active all orders created from the Back Office interface will be excluded from the tracking.'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ],
                [
                    'label' => $this->l('Use Ajax to confirm the conversion is sent'),
                    'type' => 'switch',
                    'name' => 'FCTP_AJAX',
                    'desc' => $this->l('Recommended') . '. ' . $this->l('Enable this option to make the browser validate the pixel availability, avoiding missing events from payment methods with redirections'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ],
                [
                    'label' => $this->l('Use Cookies to prevent conversion duplicates'),
                    'type' => 'switch',
                    'name' => 'FCTP_COOKIE_CONTROL',
                    'desc' => $this->l('Activate this setting if you are receiving duplicated conversions due to a Cache Module or 3rd party Cache system like Cloudflare'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ],
                [
                    'label' => $this->l('Force Purchase event\'s Basic mode for'),
                    'type' => 'checkbox',
                    'name' => 'FCTP_FORCE_BASIC_MODE_LIST',
                    'hint' => $this->l('Basic mode forces the module to send the Purchase event when the user visits the Order Confirmation\'s page'),
                    'desc' => SmartForm::genDesc($this->l('Basic mode forces the module to send the Purchase event when the user visits the Order Confirmation\'s page'), 'strong') .
                        SmartForm::openTag('br') .
                        SmartForm::openTag('br') .
                        $this->l('When a payment method is not captured by the module standard\'s methods, the basic mode is required') . '. ' .
                        $this->l('The basic mode relies entirely on order confirmation page, this means it will not prevent duplications in the event unless you enable the cookie mode') . '.' .
                        SmartForm::openTag('br') .
                        SmartForm::openTag('br') .
                        $this->l('In the following list you will see the modules grouped by the Module name. If you miss a payment option, it will probably be generated by one of the modules already in the list') . '.' .
                        SmartForm::openTag('br') .
                        SmartForm::openTag('br') .
                        $this->l('It\'s highly advised to only activate the payment methods you need to use in this method.'),
                    'form_group_class' => 'os_checkbox',
                    'values' => [
                        'query' => $payment_modules,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                ],
                [
                    'label' => $this->l('Reload after order?'),
                    'type' => 'switch',
                    'name' => 'FCTP_FORCE_REFRESH_AFTER_ORDER',
                    'desc' => $this->l('Usually not needed') . '. ' . $this->l('In some minor payment modules the confirmation is displayed before the order is validated.') . SmartForm::openTag('br') .
                        $this->l('Activate this setting to force a fast one time reload and the pixel will be tracked'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ],
                [
                    'label' => $this->l('Exclude shipping in the order total'),
                    'type' => 'switch',
                    'name' => 'FCTP_PURCHASE_SHIPPING_EXCLUDE',
                    'desc' => $this->l('Enable this setting to exclude the shipping price from the order total and save it as a custom value inside the event.'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ],
                [
                    'label' => $this->l('Send order amount with taxes?'),
                    'type' => 'switch',
                    'name' => 'FCTP_PURCHASE_TAX',
                    'desc' => $this->l('Enable to send the product prices and the order total with Taxes.') .
                        SmartForm::openTag('br') .
                        $this->l('Disable this feature to send the product prices and the order total without Taxes.'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ],
                [
                    'type' => 'switch',
                    'name' => 'FCTP_PURCHASE_VALID_ONLY',
                    'label' => $this->l('Track only Payment Validated orders') .
                        SmartForm::openTag('br') .
                        $this->l('Save the others until validation'),
                    'desc' => SmartForm::genDesc($this->l('Beta Feature'), 'strong') . '. ' .
                        $this->l('Enable to track only validated Payment order status and save the non validated to be sent later') . '. ' .
                        SmartForm::openTag('br') .
                        SmartForm::openTag('br') .
                        $this->l('Non validated orders will be saved for 7 days, if they are validated within that period they will be sent to Facebook') .
                        SmartForm::openTag('br') .
                        $this->l('Orders included in any of the excluding settings below') .
                        SmartForm::openTag('br') .
                        SmartForm::genDesc($this->l('This setting will only work if the CAPI is configured'), 'strong'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Non-Validated Orders Validation limit (days)'),
                    'desc' => $this->l('Time limit to be able to send the Purchase event for an order with non-validated payment') .
                        SmartForm::openTag('br') .
                        SmartForm::genDesc($this->l('Default value is 7'), 'u'),
                    'name' => 'FCTP_ORDER_DELAY_LIMIT_DAYS',
                    'suffix' => $this->l('days'),
                    'form_group_class' => 'os_checkbox',
                    'class' => 'chosen-container fixed-width-xxl',
                ],
                [
                    'type' => 'checkbox',
                    'label' => $this->l('Order states to exclude'),
                    'desc' => $this->l('Select the order states to exclude from the Purchase event.'),
                    'name' => 'FCTP_ORDER_STATUS_EXCLUDE',
                    'form_group_class' => 'os_checkbox',
                    'values' => [
                        'query' => $order_statuses,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                    'class' => 'chosen-container',
                ],
                [
                    'type' => 'checkbox',
                    'label' => $this->l('Customer Groups to exclude'),
                    'desc' => $this->l('Select the customer groups to exclude from the Purchase event.'),
                    'name' => 'FCTP_ORDER_CUSTOMER_GROUP_EXC',
                    'form_group_class' => 'os_checkbox',
                    'values' => [
                        'query' => $customer_groups,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                    'class' => 'chosen-container',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Customer ID to exclude'),
                    'desc' => $this->l('Enter a comma separated list of user IDs, to be excluded from the Purchase event tracking'),
                    'name' => 'FCTP_ORDER_CUSTOMER_ID_EXCLUDE',
                    'form_group_class' => 'os_checkbox',
                    'class' => 'chosen-container',
                ],
                [
                    'label' => $this->l('Purchase event Debug Mode'),
                    'type' => 'switch',
                    'name' => 'FCTP_PURCHASE_DEBUG',
                    'desc' => $this->l('Enable this option to allow the debug of the Purchase event') .
                        SmartForm::openTag('br') .
                        $this->l('Allow the module to send multiple times the purchase event without using the duplication prevention methods. This setting is useful when you need to review the Purchase event generated by the module') . '.' .
                        SmartForm::openTag('br') .
                        $this->l('Warning: I\'ll cause event duplicates on the Events Manager\'s Statistics') . '.',
                    'is_bool' => true,
                    'values' => $switch_options,
                ],
                [
                    'label' => SmartForm::genDesc($this->l('Display Options'), ['h3', 'class="modal-title text-info"']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true),
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('External ID usage'),
                    'options' => [
                        'query' => $external_id_uage,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                    'name' => 'FCP_EXTERNAL_ID_USAGE',
                    'class' => 'fixed-width-xl',
                    'desc' => SmartForm::genDesc($this->l('external_id is used to match a user actions from your page to Facebook.'), 'p') .
                        SmartForm::genDesc($this->l('We do recommend to use it only on registered users.'), 'p') .
                        SmartForm::genDesc($this->l('If you select all users the system will generate an external_id for a guest user and another external_id once the user registers.'), 'p') .
                        SmartForm::genDesc($this->l('Having two external_ids from the same user may lead to issues on attribution.'), 'p'),
                ],
                [
                    'label' => SmartForm::genDesc($this->l('Registration Options'), ['h3', 'class="modal-title text-info"']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true),
                ],
                [
                    'label' => $this->l('Use Ajax to confirm Customer Registrations'),
                    'type' => 'switch',
                    'name' => 'FCTP_AJAX_REG',
                    'desc' => $this->l('Recommended'),
                    'is_bool' => true,
                    'values' => $switch_options,
                ],
                [
                    'label' => SmartForm::genDesc($this->l('Initiate Checkout detection'), ['h3', 'class="modal-title text-info"']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true),
                ],
                [
                    'label' => $this->l('Initiate Checkout Mode'),
                    'type' => 'select',
                    'name' => 'FCTP_INIT_CHECKOUT_MODE',
                    'desc' => sprintf($this->l('Choose what will trigger the %s event'), 'InitiateCheckout'),
                    'options' => [
                        'query' => $checkout_options,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                ],
            ]
        );
        $advanced_options_inputs = array_merge(
            $advanced_options_inputs,
            [
                [
                    'label' => SmartForm::genDesc($this->l('Custom add to cart'), ['h3', 'class="modal-title text-info"']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Add To Cart custom selector'),
                    'name' => 'FCP_CUST_ADD_TO_CART',
                    'size' => 20,
                    'desc' => $this->l('If your theme has a customized add to cart button use this box to enter the jQuery selector/s'),
                ],
                [
                    'label' => SmartForm::genDesc($this->l('Product customization'), ['h3', 'class="modal-title text-info"']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Start customization selector'),
                    'name' => 'FCP_PRODUCT_CUSTOM_SELECTOR',
                    'size' => 5,
                    'class' => 'fixed-width-xl',
                    'desc' => $this->l(' Set up this option if your users have to click a button prior to start the customization of a product. Use a CSS selector that matches the button to click'),
                ],
                [
                    'label' => SmartForm::genDesc($this->l('Custom Checkout Page'), ['h3', 'class="modal-title text-info"']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Module name'),
                    'name' => 'FCP_CUST_CHECKOUT',
                    'size' => 5,
                    'class' => 'fixed-width-xl',
                    'desc' => $this->l('Specify module name if you are using any custom module to trigger the Initiate Checkout events'),
                ],
                [
                    'label' => SmartForm::genDesc($this->l('Custom Search'), ['h3', 'class="modal-title text-info"']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Search controller'),
                    'name' => 'FCP_CUST_SEARCH',
                    'size' => 5,
                    'class' => 'fixed-width-xl',
                    'desc' => sprintf($this->l('If your theme has a customized search controller or it uses a module to perform the searches enter here the module\'s controller name. Usually visible when you perform a search in the URL after the keyword %s'), 'controller') .
                        SmartForm::openTag('br') .
                        $this->l('Leave it blank to use the default controller'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Search query parameter'),
                    'name' => 'FCP_CUST_SEARCH_P',
                    'size' => 5,
                    'class' => 'fixed-width-xl',
                    'desc' => $this->l('Usually identificable on the URL when you perform a search, the most common values for it are "s", "q", "term" (without the quotes)'),
                ],
                [
                    'label' => SmartForm::genDesc($this->l('Theme Supported Wishlist'), ['h3', 'class="modal-title text-info"']),
                    'type' => 'free',
                    'class' => '',
                    'name' => 'FCTP_FREE',
                    'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr', null, true),
                ],
            ]
        );
        if (!$wishlist_modules) {
            $advanced_options_inputs[] = [
                'type' => 'free',
                'label' => $this->l('Wishlist module'),
                'name' => 'FCTP_FREE',
                'class' => 'fixed-width-xl',
                'desc' => $this->l('We haven\'t detected any compatible Wishlist module, if you use one please contact us and we will add the compatibility with the event.'),
            ];
        }
        $fields_form['advanced_options'] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Advanced Options'),
                    'icon' => 'icon-warning',
                ],
                'description' => $this->l('Options to fine-tune the module\'s behaviour') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('From the pixel load method to how the conversions should be tracked'),
                'input' => $advanced_options_inputs,
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit' . $this->name,
                    'id' => 'submit' . $this->name,
                ],
            ],
        ];
        // Retrocompatibility for Switch in Prestashop 1.5
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            foreach ($fields_form as &$form) {
                foreach ($form['form']['input'] as &$input) {
                    if ($input['type'] == 'switch') {
                        $input['type'] = 'radio';
                    }
                }
            }
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $form_fields = $this->getConfigFormValues();
        $form_fields['FCTP_FREE'] = '';
        $helper->tpl_vars = [
            'fields_value' => $form_fields, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        // Generate the FORM
        return $helper->generateForm($fields_form);
    }

    public function getConfigFormValues()
    {
        $form_values = [];
        foreach ($this->form_fields as $field) {
            if ($field['name'] === 'FCTP_ORDER_STATUS_EXCLUDE') {
                $form_values = $this->getArrayValuesToField(
                    $form_values,
                    OrderState::getOrderStates($this->context->language->id),
                    $field,
                    'id_order_state',
                    ['logable => 1']
                );
            } elseif ($field['name'] === 'FCTP_ORDER_CUSTOMER_GROUP_EXC') {
                $form_values = $this->getArrayValuesToField(
                    $form_values,
                    Group::getGroups($this->context->language->id),
                    $field,
                    'id_group'
                );
            } elseif ($field['name'] === 'FCTP_FORCE_BASIC_MODE_LIST') {
                $pms = PaymentModule::getInstalledPaymentModules();
                $forced_modules_list = json_decode(Configuration::get('FCTP_FORCE_BASIC_MODE_LIST'), true);
                if ($forced_modules_list) {
                    foreach ($pms as $pm) {
                        $input = $field['name'] . '_' . $pm['id_module'];
                        $form_values[$input] = in_array($pm['id_module'], $forced_modules_list) ? 1 : 0;
                    }
                }
            } else {
                $form_values[$field['name']] = Configuration::get($field['name'], null, null, null, $field['def']);
            }
        }
        $form_values['FCTP_FREE'] = '';
        $form_values['FCTP_CHECK_MICRO_DATA'] = 0;

        return $form_values;
    }

    private function getArrayValuesToField($form_values, $items, $field, $id = '', $exclude_array = [])
    {
        if ($field['global']) {
            $configured_values = explode(',', Configuration::getGlobalValue($field['name']));
        } else {
            $configured_values = explode(',', Configuration::get($field['name']));
        }
        foreach ($items as $item) {
            if (!empty($exclude_array)) {
                foreach ($exclude_array as $index => $value) {
                    if (isset($item[$index]) && $item[$index] == $value) {
                        continue 2;
                    }
                }
            }
            $form_values[$field['name'] . '_' . $item[$id]] = (in_array($item[$id], $configured_values)) ? 1 : 0;
        }

        return $form_values;
    }

    private function getWishlistOptions()
    {
        $modules = [
            [
                'name' => 'blockwishlist',
                'class' => 'BlockWishList',
                'selector' => 'a.addToWishlist',
            ],
            [
                'name' => 'iqitwishlist',
                'class' => 'IqitWishlist',
                'selector' => '.btn-iqitwishlist-add',
            ],
            [
                'name' => 'jwishlist',
                'class' => 'Jwishlist',
                'selector' => '.submitSelectWishlist',
            ],
        ];
        foreach ($modules as &$module) {
            if (Module::isEnabled($module['name'])) {
                return $module;
                /*$m = Module::getInstanceByName($module['id_option']);
            $module['name'] = $m->displayName;*/
            }
        }

        return false;
    }

    /**
     * Return true if at least one of the IPs in the box is a valid IP
     *
     * @param $ips A list of IPs
     *
     * @return bool If at least one IP is valid
     */
    private function validateIpsList($ips)
    {
        $ips = explode(',', str_replace(' ', '', $ips));
        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                return true;
            }
        }

        return false;
    }

    private function prepareCustomer()
    {
        // print_r($this->context->cookie);
        if (Configuration::get('pixel_account_on') == $this->context->cookie->id_customer) {
            $this->context->smarty->assign(
                [
                    'registeron' => 1,
                    'is_guest' => (int) $this->context->cookie->is_guest,
                ]
            );

            return true;
        }

        return false;
    }

    private function addConversionPixel()
    {
        // Check if it's an ajax request
        if ((!$this->isAjaxRequest() || $this->allowedCacheModule()) && !$this->conversionPixelAdded) {
            // Order Successfull
            // from the previous hook actionvalidate order we will get the order id of the ast one from user
            // IF VALUE PRESENT FOR $this->context->cookie->FCP_ORDER_CONVERSION OR $this->context->cookie->fb_event_purchase_page refers the the order is not sent yet via pixel to the fb
            $pending_orders = json_decode(Configuration::get('FCP_ORDER_CONVERSION'), true);
            $conversion = $this->customerHasPendingOrder($pending_orders);

            if ($conversion !== false) {
                // If more than 48h has passed remove the event from the pending list
                $max_hours = self::CONVERSION_WINDOW * 24;
                $order = new Order((int) $conversion['id_order']);
                if ($order->id > 0) {
                    $today = new DateTime();
                    $order = new DateTime($order->date_add);
                    $diff = $order->diff($today);

                    if (($diff->h + ($diff->days * 24)) > $max_hours) {
                        $this->clearPendingOrder($pending_orders, $conversion['id_customer']);

                        return;
                    }
                }

                // Necessary to prevent duplications when the Basic mode is used
                if (Configuration::get('FCTP_COOKIE_CONTROL')
                    && isset($_COOKIE['pp_purchaseSent'])
                    && $_COOKIE['pp_purchaseSent'] == $conversion['id_order']) {
                    return '';
                }

                // What when is a guest order?
                $ordervars = $this->getOrdervars($conversion['id_order']);
                if (Configuration::get('FCTP_AJAX')) {
                    $ordervars['aurl'] = $this->getAjaxURL();
                } else {
                    $this->clearPendingOrder($pending_orders, $conversion['id_customer']);
                }
                if ($conversion['id_customer'] == 0) {
                    $ordervars['id_customer'] = 0;
                }

                $this->assignCartProductsToSmarty();
                $this->smarty->assign(
                    [
                        'ordervars' => $ordervars,
                        'fctp_cookie_control' => Configuration::get('FCTP_COOKIE_CONTROL'),
                        'purchase_token' => Tools::encrypt('Conversion' . $ordervars['id_customer'] . ':' . $ordervars['id_order']),
                        'fb_event_purchase_page' => $conversion['event_id'],
                    ]
                );
                $this->conversionPixelAdded = true;

                return $this->display(__FILE__, 'views/templates/hook/purchase.tpl');
            }
        }
    }

    private function getOrderVars($id_order)
    {
        $order = new Order((int) $id_order);
        if ($order !== false) {
            $prefix = Configuration::getGlobalValue('FPF_PREFIX_' . $this->context->shop->id) ? Configuration::getGlobalValue('FPF_PREFIX_' . $this->context->shop->id) : '';
            $combi = (bool) Configuration::getGlobalValue('FCTP_COMBI_' . $this->context->shop->id);
            if ($combi !== false) {
                $combi = Configuration::getGlobalValue('FCTP_COMBI_PREFIX_' . $this->context->shop->id);
            }
            $product_quantity = 0;
            $product_list = [];
            // $prefix = Configuration::get('FPF_PREFIX');
            // Get the total numbers of products purchased
            $products = $order->getProducts();
            foreach ($products as $product) {
                $product_quantity += $product['product_quantity'];
                for ($i = 0; $i < $product['product_quantity']; ++$i) {
                    $id_product_attribute = 0;
                    if (isset($product['product_id'])) {
                        $id_product = $product['product_id'];
                        $id_product_attribute = $product['product_attribute_id'];
                    } elseif (isset($product['id_product'])) {
                        $id_product = $product['id_product'];
                        $id_product_attribute = $product['id_product_attribute'];
                    }
                    if (isset($id_product)) {
                        if ($combi !== false && $id_product_attribute > 0) {
                            $product_list[] = $prefix . $id_product . $combi . $id_product_attribute;
                        } else {
                            $product_list[] = $prefix . $id_product;
                        }
                    }
                }
            }
            $product_list = array_unique($product_list);

            $inc_shipping = !Configuration::getGlobalValue('FCTP_PURCHASE_SHIPPING_EXCLUDE');
            if (Configuration::getGlobalValue('FCTP_PURCHASE_TAX')) {
                $totalorder_value = $order->total_paid - (!$inc_shipping ? $order->total_shipping_tax_incl : 0);
                $shipping_value = ($inc_shipping ? 0 : $order->total_shipping_tax_incl);
            } else {
                $totalorder_value = $order->total_paid_tax_excl - (!$inc_shipping ? $order->total_shipping_tax_excl : 0);
                $shipping_value = ($inc_shipping ? 0 : $order->total_shipping_tax_excl);
            }

            return [
                'ordervalue' => $totalorder_value,
                'shipping_value' => $shipping_value,
                'currency' => $this->context->currency->iso_code,
                'product_quantity' => $product_quantity,
                'product_list' => json_encode($product_list),
                'id_order' => (int) $order->id,
                'id_customer' => (int) $order->id_customer,
                'order_reference' => $order->reference,
                'payment_module' => $order->module,
            ];
        }
    }

    private function customerHasPendingOrder($orders_list, $id_customer = 0)
    {
        // $this->context->cookie->id_guest = 0 after order created for guest users on version 1.7
        // $this->context->cookie->id_guest = 1 after order created for guest users on version 1.6
        // So we track guest account only on 1.6 version where 1.7 will be treated as customer account
        if ($id_customer == 0) {
            $id_customer = (int) $this->context->cookie->id_customer;
        }
        if (!isset($orders_list[$id_customer])) {
            return false;
        }

        $orders_list = $orders_list[$id_customer];

        if (is_array($orders_list[0])) {
            $orders_list = end($orders_list);
        }

        return [
            'id_customer' => $id_customer,
            'id_order' => $orders_list[0],
            'event_id' => $orders_list[3],
        ];
    }

    /**
     * Clear the order in the pending orders list if the debug mode for the purchase event is not active
     *
     * @param $orders_list Array The list of orders
     * @param $id_customer Int The Customer identificator
     *
     * @return bool Returns true if the order has been cleared or the DEBUG mode is active
     */
    private function clearPendingOrder($orders_list, $id_customer)
    {
        // Skip order clearing for test purposes
        if (Configuration::get('FCTP_PURCHASE_DEBUG')) {
            return true;
        }

        if (isset($orders_list[$id_customer])) {
            unset($orders_list[$id_customer]);
            Configuration::updateValue('FCP_ORDER_CONVERSION', json_encode($orders_list));

            return true;
        }

        return false;
    }

    public function getCategoryProducts($id_category)
    {
        $ret = [];
        $product_prefix = Configuration::get('FPF_PREFIX_' . (int) Context::getContext()->shop->id);
        $combi = (bool) Configuration::getGlobalValue('FCTP_COMBI_' . (int) Context::getContext()->shop->id);
        if ($combi !== false) {
            $combi = Configuration::getGlobalValue('FCTP_COMBI_PREFIX_' . (int) Context::getContext()->shop->id);
        }
        $nb_products = (int) Configuration::get('FCTP_CATEGORY_ITEMS', null, null, null, 5);
        if (Configuration::get('FCTP_CATEGORY_TOP_SALES')) {
            $sql = 'SELECT product_id as id_product, COUNT(product_id) AS sales
            FROM `' . _DB_PREFIX_ . 'order_detail`
            WHERE product_id IN (
                SELECT id_product
                FROM `' . _DB_PREFIX_ . 'category_product`
                LEFT JOIN ' . _DB_PREFIX_ . 'product USING (id_product)
                WHERE id_category = ' . (int) $id_category . '
                AND active = 1
            )
            GROUP BY product_id
            ORDER BY sales DESC LIMIT ' . $nb_products;
            $results = Db::getInstance()->executeS($sql);
        } else {
            $cat = new Category((int) $id_category);
            $results = $cat->getProducts($this->context->language->id, 1, $nb_products);
        }
        if ($results !== false) {
            foreach ($results as $result) {
                if ($combi === false) {
                    $ret[] = $product_prefix . $result['id_product'];
                } else {
                    $p = new Product($result['id_product']);
                    if ($p->cache_default_attribute > 0) {
                        $ret[] = $product_prefix . $p->id . $combi . $p->cache_default_attribute;
                    } else {
                        $ret[] = $product_prefix . $p->id;
                    }
                }
            }
        }

        return $ret;
    }

    private function getCategories($id_lang, $id_shop)
    {
        $sql = 'SELECT id_category, id_parent, level_depth, name, is_root_category, active FROM ' . _DB_PREFIX_ . 'category LEFT JOIN ' . _DB_PREFIX_ . 'category_lang AS cl USING (id_category) LEFT JOIN ' . _DB_PREFIX_ . 'category_shop AS cs USING (id_category) WHERE cs.id_shop = ' . (int) $id_shop . ' AND cl.id_lang = ' . (int) $id_lang . ' ORDER BY `' . _DB_PREFIX_ . 'category`.`id_parent` ASC, `' . _DB_PREFIX_ . 'category`.`id_category` ASC';
        $cat = [];
        if (!($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(pSQL($sql)))) {
            return '';
        } else {
            foreach ($results as $result) {
                $cat[$result['id_category']] = $result;
            }
        }

        return $cat;
    }

    public function tryGetBreadcrumb($id_product)
    {
        $p = new Product($id_product);

        return str_replace('\'', '\\\'', self::getBreadcrumb($p->id_category_default));
    }

    private function getBreadcrumb($idCatToFind, $ret = '')
    {
        $categories = self::getCategories(Context::getContext()->cookie->id_lang, Context::getContext()->shop->getContextShopGroupID());
        if (isset($categories[$idCatToFind])) {
            if (is_numeric($idCatToFind)) {
                if ($ret != '') {
                    $ret = ' > ' . $ret;
                }
                $ret = str_replace('&', '&amp;', $categories[$idCatToFind]['name']) . $ret;
                if (!$categories[$idCatToFind]['is_root_category']) {
                    if (isset($categories[$idCatToFind]['id_parent']) && $categories[$idCatToFind]['id_parent'] != '') {
                        return self::getBreadcrumb($categories[$idCatToFind]['id_parent'], $ret);
                    }
                }
            }
            if ($categories[$idCatToFind]['is_root_category']) {
                if (function_exists('mb_convert_case')) {
                    return mb_convert_case($ret, MB_CASE_TITLE, 'UTF-8');
                } else {
                    return $this->stringTitleFormat($ret, ' > ');
                }
            }
        }

        return '';
    }

    private function stringTitleFormat($text, $delimiter)
    {
        $text = explode($delimiter, $text);
        foreach ($text as $k => $v) {
            $text[$k] = ucwords(Tools::strtolower($v));
        }

        return implode($delimiter, $text);
    }

    /**
     * Check if it's a cron job call, in that case no user_agent will be present
     */
    private function allowedCacheModule()
    {
        // Page cache module
        if (Tools::getIsset('page_cache_dynamics_mods')) {
            return true;
        } elseif (Tools::getIsset('ets_superseed_load_content')) {
            return true;
        } elseif (method_exists('Tools', 'getUserBrowser') && Tools::getUserBrowser() == 'unknown') {
            if ($this->context->isMobile() != 1) {
                return true;
            }
        } else {
            if ($this->getControllerName() != '') {
                return false;
            } else {
                return true;
            }
        }
    }

    public function printPixels($params)
    {
        if (!$this->active || $this->pixels_printed) {
            return '';
        }

        if (!isset($this->api) || $this->api == false) {
            $this->tryLoadingAPI();
        }

        if ($this->api && $this->api->getIsBot()) {
            return '';
        }
        $mt = microtime(true);
        if (!$this->isAjaxRequest()
            || (Tools::getIsset('content_only') && Tools::getValue('content_only') == 1)
            || $this->allowedCacheModule()) {
            $time = time();
            $output = '';
            // Get Current Controller (only in 1.6)
            $entity = $this->getControllerName();
            if (!$entity) {
                return false;
            }
            $lang_id = $this->context->cookie->id_lang;
            // Get known One Page Checkout modules
            $opc_modules = $this->getOPCModules();
            $search = Configuration::get('FCP_CUST_SEARCH') != '' ? Configuration::get('FCP_CUST_SEARCH') : 'search';
            if ($entity == '') {
                $entity = Tools::getValue('controller');
            }
            $content_category = '';
            $hascombi = 0;
            switch ($entity) {
                case 'product':
                    $pp = Configuration::get('PS_PRICE_DISPLAY_PRECISION') == false ? 2 : Configuration::get('PS_PRICE_DISPLAY_PRECISION');
                    $usetax = !Group::getPriceDisplayMethod(Group::getCurrent()->id);
                    $ipa = Tools::getIsset('id_product_attribute') ? Tools::getValue('id_product_attribute') : 0;
                    $product = new Product(Tools::getValue('id_product'));
                    $entity_id = $product->id;
                    // $facebook_pixel_id = $this->getPixelFromCategoryId((int)$product->id_category_default);
                    $entity_name = $product->name[$lang_id];
                    //                    if (Module::isEnabled('dynamicproduct') && $_SERVER['REMOTE_ADDR'] == '188.79.18.18') {
                    //                        $row = [
                    //                            'id_product' => $product->id,
                    //                            'id_product_attribute' => $ipa,
                    //                        ];
                    //                        $product_properties = Product::getProductProperties($this->context->language->id, $row);
                    //                        Tools::dieObject($product_properties);
                    //                    } else {
                    // Price calculation instead of $product->price since the results where not reliable enough
                    $entity_price = Product::getPriceStatic((int) $product->id, $usetax, $ipa, $pp, null, false, true, 1, false, null, null, null);
                    //                    }
                    $combinations = $product->getAttributeCombinations($lang_id);
                    $hascombi = empty($combinations) ? '0' : '1';
                    $this->context->smarty->assign(
                        [
                            'hascombi' => $hascombi,
                        ]
                    );
                    $content_category = str_replace('&amp;', '&', $this->tryGetBreadcrumb(Tools::getValue('id_product')));
                    break;
                case 'category':
                    $entity_id = (int) Tools::getValue('id_category');
                    $entity_name = new Category($entity_id);
                    $entity_name = $entity_name->name[$lang_id];
                    $this->context->smarty->assign(['content_ids' => json_encode($this->getCategoryProducts((int) Tools::getValue('id_category')))]);
                    $this->context->smarty->assign([
                        'max_cat_items' => Configuration::get('FCTP_CATEGORY_ITEMS'),
                        'category_value' => Configuration::get('FCTP_CATEGORY_VALUE'),
                    ]);
                    $content_category = str_replace('&amp;', '&', $this->getBreadcrumb(Tools::getValue('id_category')));
                    break;
                case 'cms':
                    $entity_id = (int) Tools::getValue('id_cms');
                    $entity_name = new CMS($entity_id);
                    $entity_name = $entity_name->meta_title[$lang_id];

                    $this->context->smarty->assign([
                        'cms_value' => Configuration::get('FCTP_CMS_VALUE'),
                    ]);
                    $content_category = str_replace('&amp;', '&', $this->getBreadcrumb(Tools::getValue('id_cms')));
                    break;
            }

            $custom_order_entity = false;
            if (Configuration::get('FCP_CUST_CHECKOUT') != '' && Validate::isModuleName(Tools::getValue('module'))) {
                array_unshift($opc_modules, [Configuration::get('FCP_CUST_CHECKOUT'), Configuration::get('FCP_CUST_CHECKOUT')]);
            }
            if (count($opc_modules) > 0 && !empty($opc_modules)) {
                foreach ($opc_modules as $opc) {
                    if ($opc == $entity) {
                        $custom_order_entity = true;
                    }
                }
            }
            /* Get deferred options */
            $deferred_loading = Configuration::get('FCP_DEFERRED_LOADING');
            $deferred_seconds = (int) Configuration::get('FCP_DEFERRED_SECONDS') * 1000; // for use in JS

            $deferred_loading_once = Configuration::get('FCP_DEFER_FIRST_TIME');
            if ($deferred_loading_once == 1) {
                // Deffer load cookie will last a week
                $this->setCookie('pp_deferred_loading_once', $deferred_loading_once, time() + 60 * 60 * 24 * 7, '/');
            }

            $this->context->smarty->assign(
                [
                    'pixel_consent' => PixelTools::getConsent(),
                    'is_17' => $this->is_17,
                    'capi' => Configuration::get('FCTP_CONVERSION_API'),
                    'fctpid' => explode(',', preg_replace('/[^,0-9]/', '', Configuration::get('FCTP_PIXEL_ID'))),
                    'entity' => $entity,
                    'entityname' => (isset($entity_name) ? str_replace('\'', '\\\'', $entity_name) : ''),
                    'entityprice' => (isset($entity_price) ? $entity_price : ''),
                    'use_tax' => !Group::getPriceDisplayMethod(Group::getCurrent()->id),
                    'fctp_currency' => $this->context->currency->iso_code,
                    'content_category' => $content_category,
                    'event_time' => $time,
                    'id_customer_or_guest' => (Context::getContext()->customer->isLogged() ? Context::getContext()->cookie->id_customer : Context::getContext()->cookie->id_guest),
                    'combi_enabled' => Configuration::getGlobalValue('FCTP_COMBI_' . $this->context->shop->id),
                    'combi_prefix' => Configuration::getGlobalValue('FCTP_COMBI_PREFIX_' . $this->context->shop->id),
                    'product_combi' => isset($ipa) ? $ipa : 0,
                    'fbp_custom_checkout' => (int) $custom_order_entity,
                    'deferred_loading' => $deferred_loading,
                    'deferred_seconds' => $deferred_seconds,
                    'cookie_token' => Tools::encrypt('CookieValidate' . ($this->context->cookie->id_customer > 0 ? $this->context->cookie->id_customer : $this->context->cookie->id_guest)),
                ]
            );
            if (isset($this->api) && $this->api !== false) {
                $user_data = $this->api->getUserData();
                $this->context->smarty->assign(['user_data' => json_encode($user_data)]);
                //                    $this->setCookie('pp_pageview_event_id', Tools::passwdGen(12), time() + 10, '/');
            }
            // Set up the external ID usage depending on the module configuration
            $this->checkDeleteCookie('pp_external_id');

            // $this->checkDeleteCookie('pp_pageview_event_id');
            if ($this->context->cookie->__isset('pp_ipv6')) {
                $this->context->smarty->assign('pp_ipv6', $this->context->cookie->__get('pp_ipv6'));
            }
            $this->context->smarty->assign(
                [
                    'pp_advanced_match' => Configuration::get('FCTP_ADVANCED_MATCHING_OPTIONS'),
                ]
            );

            if (!$this->pixel_header_printed) {
                // Add Pixel Header
                $output .= $this->display(__FILE__, 'views/templates/hook/pixelheader.tpl');
            }

            // If the consent is not set, do not add the Pixels
            // Only add the header without the consent
            if (!$consent = PixelTools::getConsent()) {
                $this->pixel_header_printed = true;
                $this->removeTemporalCookies();
                $event_data = [
                    'entity' => $entity,
                    'entity_id' => $entity_id ?? 0,
                    'entity_name' => $entity_name ?? '',
                    'entity_price' => $entity_price ?? 0,
                    'content_category' => $content_category,
                ];

                $encoded_data = json_encode($event_data);
                if ($encoded_data === false) {
                    error_log('Failed to encode event data: ' . json_last_error_msg());

                    return $output;
                }

                CAPICookieHelper::setCookie('pending_event_data', $encoded_data, 0);

                return $output;
            }

            if (Configuration::get('FCTP_PAGEVIEW_COUNT')) {
                $this->context->smarty->assign(['pageviewcountvalue' => Configuration::get('FCTP_PAGEVIEW_COUNT_VALUE')]);
                $output .= $this->display(__FILE__, 'views/templates/hook/events/pages_viewed.tpl');
            }

            // If there is a new order print it!
            if (Configuration::get('FCTP_ADD_TO_CART') == 1) {
                $cart_url = explode('/', $this->context->link->getPageLink('cart'));
                $ps_round_mode = Configuration::get('PS_PRICE_ROUND_MODE');
                $this->context->smarty->assign([
                    'custom_add_to_cart' => Configuration::get('FCP_CUST_ADD_TO_CART'),
                    'currency_format_add_to_cart' => $this->context->currency->iso_code,
                    'fp_cart_endpoint' => end($cart_url),
                    'fp_round_mode' => $ps_round_mode,
                    'attributewizardpro' => Module::isEnabled('attributewizardpro'),
                    'cdesigner' => Module::isEnabled('cdesigner'),
                ]);

                if (!Configuration::get('PS_BLOCK_CART_AJAX') || Module::isEnabled('configurator')) {
                    $output .= $this->display(__FILE__, 'views/templates/hook/addtocart-no-ajax.tpl');
                } else {
                    if ($this->is_17) {
                        $output .= $this->display(__FILE__, 'views/templates/hook/addtocart17.tpl');
                    } else {
                        // Ignore combination check for modules that dynamically generates the idCombination
                        if (Module::isEnabled('pm_advancedpack')) {
                            $this->context->smarty->assign('module_ignore_combi', 1);
                        }
                        $output .= $this->display(__FILE__, 'views/templates/hook/addtocart.tpl');
                    }
                }
            }

            $this->assignCartProductsToSmarty();

            // Order related
            if ($entity == 'order' || $entity == 'order-opc' || $custom_order_entity) {
                // Assign the Cart products into a json variable
                // Include the one page checkout template to control registering.
                if (Configuration::get('FCTP_REG') && ($entity == 'order-opc' || $custom_order_entity)) {
                    $this->context->smarty->assign('complete_registration_value', Configuration::get('FCTP_REG_VALUE'));
                    $output .= $this->display(__FILE__, 'views/templates/hook/opc-registration.tpl');
                }
                if (Configuration::get('FCTP_START_ORD')) {
                    // It just started the Order process
                    if (!$this->context->cookie->__isset('InitiateCheckout')) {
                        if (Configuration::get('FCTP_INIT_CHECKOUT_MODE') == 1) {
                            $fb_event_checkout_page = Tools::passwdGen(12);
                            $this->context->smarty->assign(
                                ['fb_event_checkout_page' => $fb_event_checkout_page]
                            );

                            $this->context->smarty->assign(
                                [
                                    'initiate_checkout_value' => Configuration::get('FCTP_START_ORD_VALUE'),
                                    'id_cart' => $this->context->cart->id,
                                ]
                            );
                            $output .= $this->display(__FILE__, 'views/templates/hook/initiate_checkout.tpl');
                        } elseif (!Tools::getIsset('step') && Configuration::get('FCTP_INIT_CHECKOUT_MODE') == 2) {
                            // Trigger when click on start order
                            $this->context->smarty->assign('initiate_checkout_value', Configuration::get('FCTP_START_ORD_VALUE'));
                            $output .= $this->display(__FILE__, 'views/templates/hook/initiate_checkout.tpl');
                        }
                    }
                }
                // Choose payment method (start payment of the order)
                if (Tools::getValue('step') == 3 || ($entity == 'order' && $this->is_17) || $entity == 'order-opc' || $custom_order_entity) {
                    if (Configuration::get('FCTP_START') == 1) {
                        $fb_event = Tools::passwdGen(12);
                        $this->setCookie('pp_event_start_payment', $fb_event, time() + 60, '/');
                        $value = ((int) trim(Configuration::get('FCTP_START_VALUE') > 0)) ? Configuration::get('FCTP_START_VALUE') : $this->context->cart->getOrderTotal(true, Cart::BOTH);
                        $this->context->smarty->assign('initiate_payment_value', $value);
                        $output .= $this->display(__FILE__, 'views/templates/hook/add_payment_info.tpl');
                    }
                }
            }

            $output .= $this->addConversionPixel();

            // Wishlist
            if (Configuration::get('FCTP_WISH') == 1) {
                $wishlist = $this->getWishlistOptions();
                if ($wishlist) {
                    $wishlist['value'] = !empty(Configuration::get('FCTP_WISH_VALUE')) ? Configuration::get('FCTP_WISH_VALUE') : 1;
                    $this->context->smarty->assign(['fctp_wishlist' => json_encode($wishlist)]);
                    $output .= $this->display(__FILE__, 'views/templates/front/wishlist-' . $wishlist['name'] . '.tpl');
                }
            }

            if (Configuration::get('FCTP_VIEWED') == 1 && $entity == 'product') {
                $nprod = new Product(Tools::getValue('id_product'));
                $this->context->smarty->assign(
                    [
                        'product_id' => Tools::getValue('id_product'),
                        'id_product_attribute' => Tools::getIsset('id_product_attribute') ? Tools::getValue('id_product_attribute') : Product::getDefaultAttribute($nprod->id),
                        'price' => $nprod->getPrice(Group::getDefaultPriceDisplayMethod(), Tools::getIsset('id_product_attribute') ? Tools::getValue('id_product_attribute') : null, Configuration::get('PRICE_DISPLAY_PRECISION')),
                        'name' => str_replace('\'', '\\\'', $nprod->name[$this->context->language->id]),
                        'custom_vc_module' => PixelTools::getCustomModuleForViewContent() ?: '',
                    ]
                );
                //                $fb_pixel_event_id = Tools::passwdGen(12);
                //                $this->checkDeleteCookie('pp_pixel_event_id_view');
                //                if (isset($this->api) && $this->api !== false) {
                //                    $this->api->viewContentTrigger($fb_pixel_event_id, (int)$lang_id, 'product', (int)Tools::getValue('id_product'), $ipa);
                //                }
                $output .= $this->display(__FILE__, 'views/templates/hook/viewcontent.tpl');
            }

            // Registered Customer
            if (Configuration::get('FCTP_REG')) {
                // var_dump($this->context->cookie->account_created, $this->context->cookie->__isset('account_created'));
                if (Configuration::get('FCTP_AJAX_REG')) {
                    $this->context->smarty->assign(['fctp_ajaxurl' => $this->getAjaxURL()]);
                }
                if ($entity == 'guest-tracking') {
                    $this->context->smarty->assign('complete_registration_value', Configuration::get('FCTP_REG_VALUE'));
                    $output .= $this->display(__FILE__, 'views/templates/hook/registration.tpl');
                } elseif (Configuration::get('pixel_account_on') != '') {
                    if ($this->prepareCustomer()) {
                        $this->context->smarty->assign('complete_registration_value', Configuration::get('FCTP_REG_VALUE'));
                        $output .= $this->display(__FILE__, 'views/templates/hook/registration.tpl');
                        if (!Configuration::get('FCTP_AJAX_REG')) {
                            Configuration::updateValue('pixel_account_on', '');
                        }
                    }
                }
            }

            // is a search event
            if ($entity == $search) {
                if (Configuration::get('FCTP_SEARCH') == 1) {
                    $max_cat_items = Configuration::get('FCTP_CATEGORY_ITEMS');
                    $search_query = $search == 'search' ? Tools::getValue('search_query') : Tools::getValue(Configuration::get('FCP_CUST_SEARCH_P'));
                    $this->context->smarty->assign([
                        'max_cat_items' => $max_cat_items,
                        'search_keywords' => $search_query,
                    ]);
                    $this->context->smarty->assign('search_value', Configuration::get('FCTP_SEARCH_VALUE'));
                    $this->checkDeleteCookie('pp_pixel_event_id_search');
                    $content_ids_list = [];
                    if (isset($this->context->smarty->tpl_vars['listing'])) {
                        if (isset($this->context->smarty->tpl_vars['listing']->value)) {
                            $results = $this->context->smarty->tpl_vars['listing']->value;
                        } elseif (isset($this->context->smarty->tpl_vars['listing']['result'])) {
                            $results = $this->context->smarty->tpl_vars['listing']['result'];
                        }
                    }
                    if (!isset($results) && isset($this->context->smarty->tpl_vars['search_products'])) {
                        if (isset($this->context->smarty->tpl_vars['search_products']->value)) {
                            $results = $this->context->smarty->tpl_vars['search_products']->value;
                        } elseif (isset($this->context->smarty->tpl_vars['search_products']['result'])) {
                            $results = $this->context->smarty->tpl_vars['search_products']['result'];
                        }
                    }
                    if (isset($results)) {
                        if (isset($results['result'])) {
                            $results = $results['result'];
                        }
                        // If its an object, get the products
                        if (is_object($results) && method_exists(get_class($results), 'getProducts')) {
                            $products = $results->getProducts();
                        } else {
                            $products = $results;
                        }

                        $prefix = Configuration::getGlobalValue('FPF_PREFIX_' . $this->context->shop->id);
                        $combi = Configuration::getGlobalValue('FCTP_COMBI_' . $this->context->shop->id);
                        $combi_prefix = Configuration::getGlobalValue('FCTP_COMBI_PREFIX_' . $this->context->shop->id);
                        $i = 0;
                        while ($i < $max_cat_items) {
                            if (isset($products[$i]['id_product'])) {
                                $prod = $prefix . $products[$i]['id_product'];
                                if ($combi && $products[$i]['id_product_attribute'] > 0) {
                                    $prod .= $combi_prefix . $products[$i]['id_product_attribute'];
                                }
                                $content_ids_list[] = $prod;
                            } else {
                                break;
                            }
                            ++$i;
                        }
                    }
                    if (isset($this->api) && $this->api !== false && !empty($content_ids_list)) {
                        $this->api->searchEventTrigger($search_query, $content_ids_list);
                    }
                    $this->context->smarty->assign('content_ids_list', json_encode($content_ids_list));
                    $output .= $this->display(__FILE__, 'views/templates/hook/search.tpl');
                }
            }

            // New event viewCategory
            if ($entity == 'category') {
                $id_category = (int) Tools::getValue('id_category');
                if ($id_category > 0) {
                    $fb_pixel_event_id = Tools::passwdGen(12);
                    $this->checkDeleteCookie('pp_pixel_viewcategory_event_id');
                    $this->setCookie('pp_pixel_viewcategory_event_id', $fb_pixel_event_id, time() + 10, '/');
                    if (Configuration::get('FCTP_CATEGORY') == 1) {
                        if (isset($this->api) && $this->api !== false) {
                            $this->api->viewContentTrigger($fb_pixel_event_id, (int) $lang_id, 'category', $id_category);
                        }
                        $this->context->smarty->assign('pix_category_id', $id_category);
                        $output .= $this->display(__FILE__, 'views/templates/hook/category.tpl');
                    }
                }
            }
            /* New event of CMS page */
            if ($entity == 'cms') {
                $id_cms = (int) Tools::getValue('id_cms');
                if (Configuration::get('FCTP_CMS') == 1 && $id_cms > 0) {
                    if (isset($this->api) && $this->api !== false) {
                        $entity_name = new CMS($id_cms);
                        $entity_name = $entity_name->meta_title[$lang_id];
                        $cms_event_id = Tools::passwdGen(12);

                        $this->api->viewContentTrigger($cms_event_id, (int) $lang_id, 'CMS', $id_cms);
                        $this->checkDeleteCookie('pp_pixel_event_id_view');
                        $this->setCookie('pp_pixel_event_id_view', $cms_event_id, time() + 10, '/');
                        $this->context->smarty->assign([
                            'entityname' => $entity_name,
                            'content_ids' => $id_cms,
                            'cms_event_id' => $cms_event_id,
                        ]);
                        $output .= $this->display(__FILE__, 'views/templates/hook/cms.tpl');
                    }
                }
            }

            /* New event of Contact US page */
            if (Configuration::get('FCTP_CONTACT_US') == 1 && $entity == 'contact') {
                $fb_pixel_event_id = Tools::passwdGen(12);

                $this->context->smarty->assign(['fctp_ajaxurl' => $this->getAjaxURL()]);
                $this->context->smarty->assign([
                    'entity' => $entity,
                    'fb_event_contact_page' => $fb_pixel_event_id,
                    'lang_iso_code' => $this->context->language->iso_code,
                    'contact_value' => Configuration::get('FCTP_CONTACT_US_VALUE'),
                ]);
                $output .= $this->display(__FILE__, 'views/templates/hook/contact.tpl');
            }
            // New event newsletter subscription
            if (Configuration::get('FCTP_NEWSLETTER') == 1) {
                $this->checkDeleteCookie('pp_pixel_newsletter_event_id');
                $this->setCookie('pp_pixel_newsletter_event_id', Tools::passwdGen(12), time() + 10, '/');
                $this->context->smarty->assign([
                    'FCTP_NEWSLETTER_VALUE' => Configuration::get('FCTP_NEWSLETTER_VALUE'),
                    'register_newsletter' => 0,
                ]);

                $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
                if ($ref != null) {
                    $data = explode('?', $ref);
                    if (isset($data[1])) {
                        $data = explode('=', $data[1]);

                        if ($data[0] == 'create_account') {
                            $customerObj = new Customer($this->context->cookie->id_customer);
                            if ($customerObj->newsletter == 1) {
                                $this->context->smarty->assign([
                                    'register_newsletter' => 1,
                                ]);
                            }
                        }
                    }
                }
                $this->context->smarty->assign(['fctp_ajaxurl' => $this->getAjaxURL()]);

                $output .= $this->display(__FILE__, 'views/templates/hook/newsletter.tpl');
            }
            // New event page time trigger - HR
            if (Configuration::get('FCTP_PAGETIME') == 1) {
                $fb_pixel_time_event = Tools::passwdGen(12);
                $this->checkDeleteCookie('pp_pixel_time_event');
                $this->setCookie('pp_pixel_time_event', $fb_pixel_time_event, time() + 10, '/');
                $this->context->smarty->assign([
                    'FCTP_PAGETIME_VALUE' => Configuration::get('FCTP_PAGETIME_VALUE'),
                ]);

                $this->context->smarty->assign(['fctp_ajaxurl' => $this->getAjaxURL()]);

                $output .= $this->display(__FILE__, 'views/templates/hook/page_time_event.tpl');
            }
            // New Event discount used on cart - HR
            if ($entity == 'cart' && Configuration::get('FCTP_DISCOUNT') == 1) {
                $fb_pixel_discount_event = Tools::passwdGen(12);
                $this->checkDeleteCookie('pp_pixel_discount_event_id');
                $this->setCookie('pp_pixel_discount_event_id', $fb_pixel_discount_event, time() + 10, '/');

                $this->context->smarty->assign([
                    'fctp_discount_value' => Configuration::get('FCTP_DISCOUNT_VALUE'),
                    'fctp_ajaxurl' => $this->getAjaxURL(),
                ]);

                $output .= $this->display(__FILE__, 'views/templates/hook/discount.tpl');
            }

            if (Configuration::get('FCP_PRODUCT_CUSTOM_SELECTOR') != '' && $entity == 'product') {
                $this->context->smarty->assign([
                    'fcp_product_custom_selector' => Configuration::get('FCP_PRODUCT_CUSTOM_SELECTOR'),
                ]);
                $output .= $this->display(__FILE__, 'views/templates/hook/product_custom.tpl');
            }

            if (isset($this->api) && $this->api !== false) {
                $this->api->sendQueuedEvents();
            }
            $this->pixels_printed = true;

            return $output;
        }

        return '';
    }

    private function getAjaxURL()
    {
        if (!isset($this->ssl)) {
            $this->setSSL();
        }

        return $this->context->link->getModuleLink('facebookconversiontrackingplus', 'AjaxConversion', [], $this->ssl);
    }

    private function setSSL()
    {
        if (!isset($this->ssl)) {
            $this->ssl = (bool) Configuration::get('PS_SSL_ENABLED');
        }
    }

    private function isAllowedControllersForPurchase($entity)
    {
        if (Configuration::get('FCTP_LIMIT_CONF') && $entity == 'order-confirmation') {
            return true;
        }

        return false;
    }

    private function getOPCModules()
    {
        // Module name >> Controller used on the order page
        $modules_list = ['supercheckout' => 'supercheckout', 'onepagecheckout' => 'order', 'onepagecheckoutps' => 'order', 'steasycheckout' => 'default', 'thecheckout' => 'order', 'idxropc' => 'order', 'pwpaysoncheckout' => 'checkout', 'easycheckout' => 'checkout'];
        foreach ($modules_list as $module => $controller) {
            if (!Module::isEnabled($module)) {
                unset($modules_list[$module]);
            }
        }

        return $modules_list;
    }

    public function updateConsent($consent)
    {
        $this->context->cookie->__set('pp_consent', (int) $consent);
        $this->context->cookie->write();

        if (!$consent) {
            $this->removeTemporalCookies();

            // Early return if consent has not been granted
            return;
        }

        $pending_event = CAPICookieHelper::getCookie('pending_event_data');
        if ($pending_event) {
            $event_data = json_decode($pending_event, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($event_data)) {
                // Delegate event handling to a dedicated method
                $this->handlePendingEvents($event_data);
            } else {
                error_log('Invalid event data in cookie: ' . json_last_error_msg());
            }
        }

        return true;
    }

    private function handlePendingEvents(array $event_data)
    {
        // Reinstantiate the API if consent has been granted
        $this->api = new ConversionApi($this);
        $this->api->sendQueuedEventsOnConsent($event_data);
    }

    /**
     * If consent hasn't been granted or revoked, remove temporally functional cookies
     *
     * @return void
     */
    private function removeTemporalCookies()
    {
        $cookies_list = ['fbp', '_fbp', 'pp_deferred_loading_once', 'pp_pageview_event_id', 'pp_event_start_payment', 'pp_pixel_event_id_view', 'pp_pixel_viewcategory_event_id', 'pp_pixel_newsletter_event_id', 'pp_pixel_time_event', 'pp_pixel_discount_event_id', 'pp_reload', 'pp_custom_product_sent', 'pp_external_id', 'pp_pixel_event_id_search'];
        foreach ($cookies_list as $cookie) {
            $this->checkDeleteCookie($cookie);
        }
    }

    /*
 * Checks if a cookie exists and removes it.
 */
    private function checkDeleteCookie($name)
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
            setcookie($name, '', time() - 3600, '/');
        }
    }

    private function setFeedV2()
    {
        // TODO Change feed_ve from static to no static
    }

    public static function getFeedId()
    {
        if (self::$feed_v2) {
            $feed_id = Configuration::getGlobalValue('FCTP_FEED_' . Context::getContext()->shop->id);
        } else {
            $feed_id = Configuration::getGlobalValue('FPF_' . Context::getContext()->shop->id . '_' . Context::getContext()->cookie->id_lang);
        }

        return $feed_id;
    }

    public function hookDisplayHeader($params)
    {
        if (!$this->active) {
            return false;
        }
        $entity = $this->getControllerName();
        $output = '';
        if ($this->is_15) {
            $output .= $this->display(__FILE__, 'views/templates/front/js_vars.tpl');
        }
        $url = Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . '/' . ltrim($_SERVER['REQUEST_URI'], '/');
        if (!$this->isAjaxRequest() && $this->context->cookie->__isset('InitiateCheckout')) {
            if ($this->context->cookie->__get('InitiateCheckout') != $url) {
                $this->context->cookie->__unset('InitiateCheckout');
            }
        }
        if (Configuration::get('FCTP_VERIFY_DOMAIN') != '') {
            $output .= '<meta name="facebook-domain-verification" content="' . Configuration::get('FCTP_VERIFY_DOMAIN') . '" />';
        }
        $price_precision = Configuration::get('PS_PRICE_DISPLAY_PRECISION') == false ? 2 : Configuration::get('PS_PRICE_DISPLAY_PRECISION');
        // Add necessary variables to smarty
        $combi = Configuration::getGlobalValue('FCTP_COMBI_' . $this->context->shop->id);
        $this->context->smarty->assign(
            [
                'pp_api' => (bool) $this->api,
                'prefix' => Configuration::getGlobalValue('FPF_PREFIX_' . $this->context->shop->id),
                'id_prefix' => Configuration::getGlobalValue('FPF_PREFIX_' . $this->context->shop->id),
                'combi' => $combi,
                'combi_prefix' => ($combi ? Configuration::getGlobalValue('FCTP_COMBI_PREFIX_' . $this->context->shop->id) : ''),
                'price_precision' => $price_precision,
                'ajax_events_url' => $this->getAjaxURL(),
            ]
        );
        $feed_id = self::getFeedId();
        if (!empty($feed_id)) {
            $this->context->smarty->assign(['fpf_id' => $feed_id]);
        }

        if (Configuration::get('FCTP_FORCE_HEADER') == 1) {
            $output .= $this->printPixels($params);
        }
        if (Tools::getIsset('id_product')) {
            $p = new Product((int) Tools::getValue('id_product'));
            if (Validate::isLoadedObject($p) && $p->active) {
                /* Micro Data Init */
                if ($this->displayMicrodata && !isset($this->og)) {
                    // Fill in the data and the og variable
                    $this->getRequiredProductData();
                    if (isset($this->og) && (count($this->og) > 0) && Configuration::get('FCTP_MICRO_OG') && (!Tools::getIsset('mdata') || Tools::getValue('mdata') != 0)) {
                        $this->context->smarty->assign(
                            [
                                'og_data' => $this->og,
                                'localization_info' => ' country: ' . $this->context->country->id . ' - Cust ID Zone' . $this->context->country->id_zone . '- Cust ISO Code' . (int) $this->context->country->iso_code,
                                'ip' => Tools::getRemoteAddr(),
                                'country' => $this->context->country->id,
                                'req' => isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : '',
                                'tax' => !Group::getPriceDisplayMethod(Group::getCurrent()->id),
                            ]
                        );
                        //                        if ($_SERVER['REMOTE_ADDR'] == '79.117.209.75') {
                        //                            echo $this->display(__FILE__, 'views/templates/front/add-missing-og.tpl');
                        //                            die();
                        //                        }
                        $output .= $this->display(__FILE__, 'views/templates/front/add-missing-og.tpl');
                    }
                }
            }
        }
        if (Configuration::get('FCTP_FORCE_REFRESH_AFTER_ORDER') && $entity == 'order-confirmation' && !isset($_COOKIE['pp_reload'])) {
            $output .= '<meta http-equiv="refresh" content="0.1">';
            // The cookie will last 60 seconds only, this is to prevent issues with new possible orders
            $this->setCookie('pp_reload', 1, time() + 60, '/');
        }

        if (Configuration::get('FCP_PRODUCT_CUSTOM_SELECTOR')
            && isset($_COOKIE['pp_custom_product_sent'])
            && isset($this->api)
            && $this->api !== false
            && $entity != 'pagenotfound'
            && in_array($entity, ['product', 'category', 'cms', 'order', 'cart'])) {
            $jsonObj = json_decode($_COOKIE['pp_custom_product_sent'], true);
            if (is_array($jsonObj) && count($jsonObj)) {
                foreach ($jsonObj as $eventId => $data) {
                    $this->api->customizeProductTrigger($data, $eventId);
                }
                $this->setCookie('pp_custom_product_sent', '{}', time() - 1, '/');
            }
        }

        return $output;
    }

    private function assignCartProductsToSmarty($id_cart = 0)
    {
        // Retrieve id_order (casting a non-existing value will yield 0)
        $orderId = (int) Tools::getValue('id_order');

        if ($id_cart == 0) {
            if ($orderId > 0) {
                $entity = new Order($orderId);
            } elseif (!empty($this->context->cart->id)) {
                $entity = new Cart($this->context->cart->id);
            } else {
                return false;
            }
        } else {
            $entity = new Cart($id_cart);
        }

        // Now retrieve products from whichever entity was instantiated
        $productsCart = $entity->getProducts();

        // var_dump($productsCart);
        $prefix = Configuration::getGlobalValue('FPF_PREFIX_' . $this->context->shop->id);
        $combi = Configuration::getGlobalValue('FCTP_COMBI_' . $this->context->shop->id);
        $combi_prefix = Configuration::getGlobalValue('FCTP_COMBI_PREFIX_' . $this->context->shop->id);
        $pcart = [];
        $content_products = [];
        foreach ($productsCart as $product) {
            if (isset($product['product_attribute_id'])) {
                $product['id_product_attribute'] = (int) $product['product_attribute_id'];
            }
            $p_id = $prefix . $product['id_product'] . (isset($product['id_product_attribute']) && $product['id_product_attribute'] > 0 && $combi ? $combi_prefix . (int) $product['id_product_attribute'] : '');
            $pcart[] = $p_id;
            $p_data = [
                'id' => $p_id,
                'quantity' => isset($product['cart_quantity']) ? $product['cart_quantity'] : $product['product_quantity'],
                'category' => str_replace('&amp;', '&', $this->tryGetBreadcrumb($product['id_product'])),
                'price' => Tools::ps_round($product['total_wt'], _PS_PRICE_DISPLAY_PRECISION_),
            ];
            $content_products[] = $p_data;
        }
        // If it's a cart based entity the getOrderTotal method will exist, otherwise it's an order object and we will need the total_paid
        $total = method_exists($entity, 'getOrderTotal') ? $entity->getOrderTotal(true, Cart::BOTH) : $entity->total_paid;
        $this->context->smarty->assign(
            [
                'pcart' => json_encode($pcart),
                'pcart_value' => $total,
                'pcart_currency' => $this->context->currency->iso_code,
                'pcart_contents' => json_encode($content_products),
                'ic_mode' => Configuration::get('FCTP_INIT_CHECKOUT_MODE'),
            ]
        );
    }

    private function getControllerName()
    {
        if (Tools::getIsset('controller') && Tools::getValue('controller') !== '') {
            return Tools::getValue('controller');
        } elseif (isset($this->context->controller->php_self) && $this->context->controller->php_self != '') {
            return $this->context->controller->php_self;
        } elseif (isset($this->context->controller->controller_name) && $this->context->controller->controller_name != '') {
            return $this->context->controller->controller_name;
        } elseif (isset($this->context->controller->module->name) && $this->context->controller->module->name != '') {
            return $this->context->controller->module->name;
        } else {
            return '';
        }
    }

    public function hookDisplayFooter($params)
    {
        // print_r($this->context->cookie);
        if ($this->active && Configuration::get('FCTP_FORCE_HEADER') != 1 && $this->pixels_printed == false) {
            return $this->printPixels($params);
        }
    }

    public function hookDisplayBeforeBodyClosingTag($params)
    {
        if ($this->active && $this->pixels_printed == false) {
            return $this->printPixels($params);
        }
    }

    public function hookActionCartSave($params)
    {
        if (!isset($this->context->controller->controller_type) || in_array($this->context->controller->controller_type, ['admin', 'moduleadmin'])) {
            return;
        }
        /* Deactivated to prevent event duplication. The JS calls directly the API now */
        if ($this->active && !$this->is_17) {
            $is_gift = [];
            if (isset($params['cart'])) {
                $cart = $params['cart'];
            } elseif (isset($this->context->cart)) {
                $cart = $this->context->cart;
            }
            if (isset($cart)) {
                if (method_exists('cart', 'getProductsWithSeparatedGifts')) {
                    // Check if it's a gift product to avoid triggering addtocart event.
                    $is_gift_product = ['is_gift' => 1];
                    $is_gift = array_intersect_key($cart->getProductsWithSeparatedGifts(), array_flip($is_gift_product));
                } else {
                    if (method_exists($cart, 'getSummaryDetails')) {
                        $gifts = $cart->getSummaryDetails(null, true);
                        $gifts = $gifts['gift_products'];
                        $last_product = $cart->getLastProduct();
                        if (count($gifts) > 0) {
                            foreach ($gifts as $gift) {
                                if ($gift['id_product'] == $last_product['id_product']) {
                                    $is_gift[] = $gift['id_product'];
                                }
                            }
                        }
                    }
                }
            }

            $entity = $this->getControllerName();
            if (Configuration::get('FCTP_ADD_TO_CART')
                && ($entity == 'index' || $entity == 'product' || $entity == 'category' || $entity == 'search' || $entity == 'cart')) {
                if (isset($this->api) && $this->api !== false && Tools::getValue('delete') != 1 && count($is_gift) == 0) {
                    $this->api->addToCartTrigger();
                }
            }
        }
    }

    public function hookActionCustomerAccountAdd($params)
    {
        if ($this->active) {
            Configuration::updateValue('pixel_account_on', $params['newCustomer']->id);
            $lang_id = $this->context->cookie->id_lang;
            if (Configuration::get('FCTP_REG')) {
                if (isset($this->api) && $this->api !== false) {
                    $this->api->accountRegisterTrigger((int) $params['newCustomer']->id, (int) $lang_id, $params['newCustomer']->is_guest);
                }
            }
        }
    }

    public function hookActionValidateOrder($params)
    {
        if ($this->active) {
            if (Configuration::get('FCTP_EXCLUDE_BO_ORDERS') && isset($this->context->cookie->id_employee)) { // || ($this->is_17 && !Tools::getIsset('fc')))) {
                // Additional fields to exclude if needed
                // cookie >> shopContext, profile,
                // Tools::getValue() >> no presence of module, controller
                return;
            }
            $order = $this->getOrderFromParams($params);
            if (!$order) {
                return;
            }
            // Abort if it's the basic mode and it's not the order confirmation page
            if ($this->paymentNeedsBasicMode($order->module) && PixelTools::strpos((Tools::usingSecureMode() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $this->context->link->getPageLink('order-confirmation.php')) === false) {
                return;
            }
            $exc_order_status = explode(',', Configuration::get('FCTP_ORDER_STATUS_EXCLUDE'));
            $exc_customer_groups = explode(',', Configuration::get('FCTP_ORDER_CUSTOMER_GROUP_EXC'));
            $exc_id_customers = explode(',', Configuration::get('FCTP_ORDER_CUSTOMER_ID_EXCLUDE'));
            $order_status = (int) $params['orderStatus']->id;
            if (in_array($order_status, $exc_order_status)) {
                if ($this->logOthers) {
                    PrestaShopLogger::addLog('[Pixel Plus] Order State (' . $params['orderStatus']->name . ') excluded by module configuration', 1, null, 'PixelPlus');
                }

                return;
            }
            if (in_array($order->id_customer, $exc_id_customers)) {
                if ($this->logOthers) {
                    PrestaShopLogger::addLog('[Pixel Plus] Customer ID (' . $order->id_customer . ') excluded by module configuration', 1, null, 'PixelPlus');
                }

                return;
            }
            if (!empty(array_intersect(Customer::getGroupsStatic($order->id_customer), $exc_customer_groups))) {
                if ($this->logOthers) {
                    PrestaShopLogger::addLog('[Pixel Plus] Customer Group Excluded (' . Customer::getGroupsStatic($order->id_customer) . ') excluded by module configuration', 1, null, 'PixelPlus');
                }

                return;
            }
            if (Configuration::get('FCTP_PURCHASE_VALID_ONLY') && isset($params['orderStatus']) && !$params['orderStatus']->logable) {
                PrestaShopLogger::addLog('[Pixel Plus] Purchase event will not trigger until the Payment is validated (configured on module\s advanced parameters).', 1, null, 'PixelPlus');
                $this->addOrderForDelayedValidation($order);

                return;
            }
            $purchase_event_id = Tools::passwdGen(12);
            $this->context->cookie->fb_event_purchase_page = $purchase_event_id;
            if (isset($order->id)) {
                $id = $order->id;
                // Compatibility check
                if (empty($id)) {
                    $id = $order->id_order;
                }

                // Necessary to prevent duplications when the Basic mode is used
                if (Configuration::get('FCTP_COOKIE_CONTROL')
                    && isset($_COOKIE['pp_purchaseSent'])
                    && $_COOKIE['pp_purchaseSent'] == $order->id) {
                    return;
                }

                $this->addPendingOrder($id, $order->id_customer, !$order->id_customer, $purchase_event_id);
                if (!isset($this->api) || $this->api == false) {
                    $this->tryLoadingAPI();
                }
                if (Configuration::get('FCTP_CONV') && $this->api !== false && $this->api->getActive() !== false) {
                    $this->api->purchaseEventTrigger($purchase_event_id, $id);
                }
            }
        }
    }

    /**
     * Adds an order to the pending orders array. Used to store customer pending orders, only the last order is kept
     *
     * @param $id_order
     * @param $id_customer
     * @param $guest Bool is a guest account
     * @param $event
     */
    private function addPendingOrder($id_order, $id_customer, $guest, $event_id)
    {
        $pending_orders = json_decode(Configuration::get('FCP_ORDER_CONVERSION'), true);
        if (!is_array($pending_orders)) {
            $pending_orders = [];
        }

        // Order Id Customer
        $pending_orders[$id_customer] = [
            $id_order,
            $id_customer,
            $guest,
            $event_id,
        ];
        Configuration::updateValue('FCP_ORDER_CONVERSION', json_encode($pending_orders));
    }

    /* Customer CSV Export Start */
    private function getCurrentUrl()
    {
        $url = Tools::strlen($_SERVER['QUERY_STRING']) ? basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING'] : basename($_SERVER['PHP_SELF']);
        $pos = strpos($url, 'typexp');
        if ($pos === false) {
            return $url;
        } else {
            return Tools::substr($url, 0, $pos - 1);
        }
    }

    private function addOrderForDelayedValidation($order)
    {
        $delayed_orders = json_decode(Configuration::get('FCTP_ORDER_DELAYED_LIST'), true);
        $id_order = is_array($order) ? (int) $order['id_order'] : (int) $order->id;
        $delayed_orders[$id_order] = 1;
        Configuration::updateValue('FCTP_ORDER_DELAYED_LIST', json_encode($delayed_orders));
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        if (!Configuration::get('FCTP_PURCHASE_VALID_ONLY')) {
            return;
        }

        $this->tryLoadingAPI();
        if ($this->api === false) {
            return;
        }

        // Proceed only if the new order status is loggable
        if ($params['newOrderStatus']->logable) {
            $delayedOrders = json_decode(Configuration::get('FCTP_ORDER_DELAYED_LIST'), true);
            $orderId = (int) $params['id_order'];

            // If the order is not in the delayed orders list, return early
            if (!isset($delayedOrders[$orderId])) {
                return;
            }

            // Load the order and check if it's within the delay limit
            $order = new Order($orderId);
            $orderDate = new DateTime($order->date_add);
            $today = new DateTime();
            $daysLimit = (int) Configuration::get('FCTP_ORDER_DELAY_LIMIT_DAYS');

            if ($today->diff($orderDate)->format('%a') <= $daysLimit) {
                // Trigger the API event for a valid order
                $this->api->purchaseEventTrigger(Tools::passwdGen(12), $orderId);
            } else {
                // Log if the delay limit has been exceeded
                PrestaShopLogger::addLog(sprintf(
                    '[Pixel Plus] The order %d won\'t be sent through the pixel because the maximum days (%d) has been exceeded',
                    $orderId,
                    $daysLimit
                ), 2, null, 'PixelPlus', 1);
            }

            // Remove the order from the delayed list and save the updated list
            unset($delayedOrders[$orderId]);
            Configuration::updateValue('FCTP_ORDER_DELAYED_LIST', json_encode($delayedOrders));
        }
    }

    public static function getProcess($typexp)
    {
        // Process the files
        $res = [];
        if ($typexp > 0 && $typexp <= 3) {
            if (class_exists('DbQuery')) {
                $dbquery = new DbQuery();
                $dbquery->select('c.`email`');
                if ($typexp == 1 || $typexp == 3) {
                    $dbquery->from('customer', 'c');
                }
                if ($typexp == 2) {
                    $dbquery->from('newsletter', 'c');
                }
                $dbquery->groupBy('c.`email`');
                if (Context::getContext()->cookie->shopContext) {
                    $dbquery->where('c.id_shop = ' . (int) Context::getContext()->shop->id);
                }
                $rq = Db::getInstance()->executeS($dbquery->build());
            } else {
                $dbquery = 'SELECT c.email ';
                if ($typexp == 1 || $typexp == 3) {
                    $dbquery .= 'FROM ' . _DB_PREFIX_ . 'customer c ';
                }
                if ($typexp == 2) {
                    $dbquery .= 'FROM ' . _DB_PREFIX_ . 'newsletter c ';
                }
                $dbquery .= 'GROUP BY c.email';
                $rq = Db::getInstance()->executeS($dbquery);
            }
            // Newsletter customers for Export all
            if ($typexp == 3) {
                if (class_exists('DbQuery')) {
                    $dbquery = new DbQuery();
                    $dbquery->select('c.`email`');
                    $dbquery->from('newsletter', 'c');
                    $dbquery->groupBy('c.`email`');
                    $dbquery->where('c.id_shop = ' . (int) Context::getContext()->shop->id);
                    $rs = Db::getInstance()->executeS($dbquery->build());
                } else {
                    $dbquery = 'SELECT c.email FROM ' . _DB_PREFIX_ . 'newsletter c GROUP BY c.email';
                    $rs = Db::getInstance()->executeS($dbquery);
                }
            }
            if (is_array($rq)) {
                // Initialize the arrays
                $array1 = [];
                $array2 = [];
                foreach ($rq as $item) {
                    $array1[] = $item['email'];
                }
                // If we have the Newsletter array, merge it
                if (!empty($rs)) {
                    if (is_array($rs)) {
                        foreach ($rs as $item) {
                            $array2[] = $item['email'];
                        }
                        $array1 = array_unique(array_merge($array1, $array2));
                    }
                }
                $res = self::createCSV($array1, $typexp);
            }

            return $res;
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('configure') == 'facebookconversiontrackingplus') {
            if (!version_compare(_PS_VERSION_, '1.6', '<')) {
                Media::addJsDef([
                    'prefix' => 'pp_',
                    'ajax_token' => Tools::getAdminTokenLite($this->context->controller->controller_name),
                    'fctp_front_ajax_url' => $this->getAjaxURL(),
                ]);
                Media::addJsDefL('display_cookies_list', $this->l('Displaying the cookies found'));
                Media::addJsDefL('values_updated', $this->l('The values have been updated. Updated fields will temporarily appear with a green border'));
                Media::addJsDefL('could_not_retrieve_cookies', $this->l('Could not retrieve the front cookies. Try again after a few seconds'));
                Media::addJsDefL('cookies_list', $this->l('Cookies Available on the Front'));
                Media::addJsDefL('cookies_list_intro', $this->l('Make sure you have allowed the Marketing cookies in the front before opening the cookies list.'));
                Media::addJsDefL('cookies_list_intro2', $this->l('Once you find the cookie, click on the icon to automatically set up the module parameters'));
                Media::addJsDefL('close_text', $this->l('close'));
                Media::addJsDefL('cookie_name', $this->l('Cookie Name'));
                Media::addJsDefL('cookie_value', $this->l('Cookie Value'));
                Media::addJsDefL('cookie_actions', $this->l('Actions'));
                Media::addJsDefL('close_dialog', $this->l('Close Dialog'));
                Media::addJsDefL('cookie_select_pair', $this->l('Select the item which represents the right pair'));
            }
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->context->controller->addCSS($this->_path . 'views/css/back-1.5.css');
            } else {
                $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            }
            $this->context->controller->addCSS($this->_path . 'views/css/' . $this->name . '.css');
            $this->context->controller->addJS($this->_path . 'views/js/bo-form.js');
            $this->context->controller->addJS($this->_path . 'views/js/tab-magic-menus.js');
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        if ($this->active) {
            $this->tryLoadingAPI();
            if (!empty($this->api) && $this->api !== false) {
                Media::addJsDef(['pp_event_debug' => $this->api->getLogEvents()]);
            }
            $this->context->controller->addJS($this->_path . 'views/js/events.js');
            $js_vars = [];
            // 1.6 detect custom add to cart
            $js_vars['pp_custom_add_to_cart'] = Configuration::get('FCP_CUST_ADD_TO_CART');
            $js_vars['single_event_tracking'] = (int) Configuration::get('FCTP_SINGLE_EVENT_TRACKING');
            $js_vars['pixel_ids'] = preg_replace('/[^,0-9]/', '', Configuration::get('FCTP_PIXEL_ID'));
            if (!$this->is_17) {
                $js_vars['currencySign'] = $this->context->currency->sign;
                $js_vars['currencyFormat'] = $this->context->currency->format;
                $js_vars['currencyISO'] = $this->context->currency->iso_code;
            }

            if (!$this->is_15) {
                Media::addJsDef($js_vars);
            } else {
                $this->context->smarty->assign(['js_vars' => $js_vars]);
            }
            $this->context->smarty->assign(['pp_custom_add_to_cart' => Configuration::get('FCP_CUST_ADD_TO_CART')]);
        }
    }

    /**
     * Adds the conversion pixel on the order confirmation page if the force basic mode is set for the module
     * or if the conversion hasn't been sent but it's ready.
     *
     * @param $params
     *
     * @return false|string|void
     */
    public function hookDisplayOrderConfirmation($params)
    {
        if ($this->active) {
            $order = $this->getOrderFromParams($params);
            $this->context->cookie->__unset('pp_event_purchase_page');
            if ($order !== false && $this->paymentNeedsBasicMode($order->module) && !$this->conversionPixelAdded) {
                // Manually trigger the process to add the validation
                $this->assignCartProductsToSmarty($order->id_cart);
                $params['orderStatus'] = new OrderState($order->current_state);
                $this->hookActionValidateOrder($params);

                return $this->addConversionPixel();
            }
        }
    }

    private function getOrderFromParams($params)
    {
        if (isset($params['id_order'])) {
            return new Order((int) $params['id_order']);
        } elseif (isset($params['order']->id)) {
            return $params['order'];
        } elseif (isset($params['objOrder']->id)) {
            return $params['objOrder'];
        }

        return false;
    }

    private function paymentNeedsBasicMode($module)
    {
        return in_array(Module::getModuleIdByName($module), json_decode(Configuration::get('FCTP_FORCE_BASIC_MODE_LIST', null, null, null, '{}'), true));
    }

    public function trackAjaxConversion($id_customer)
    {
        $pending_orders = json_decode(Configuration::get('FCP_ORDER_CONVERSION'), true);
        //        if ($_SERVER['REMOTE_ADDR'] == '79.117.209.7') {
        //            var_dump([
        //                Tools::getValue('fctp_token'),
        //                Tools::encrypt('Conversion' . Tools::getValue('id_customer') . ':' . Tools::getValue('id_order')),
        //                Tools::getValue('id_customer') . ':' . Tools::getValue('id_order')
        //            ]);
        //            die();
        //        }
        if (Tools::getValue('fctp_token') == Tools::encrypt('Conversion' . Tools::getValue('id_customer') . ':' . Tools::getValue('id_order'))) {
            return $this->clearPendingOrder($pending_orders, $id_customer);
        }

        return false;
    }

    public function trackAjaxRegistration()
    {
        Configuration::updateValue('pixel_account_on', '');

        return true;
    }

    private function displayFAQ()
    {
        // FAQ Answers moved to faq-answers.tpl
        $this->context->smarty->assign(
            [
                'faq' => [
                    'all_yellow' => [
                        'id' => 'all_yellow',
                        'question' => 'Pixel Helper: ' . $this->l('All events display a yellow triangle'),
                        'image' => 'all-yellow-events.jpg',
                    ],
                    'some_events_yellow' => [
                        'id' => 'some_events_yellow',
                        'image' => 'some-events-yellow.jpg',
                        'question' => 'Pixel Helper: ' . $this->l('Some events are yellow, what to do?'),
                    ],
                    'dynamic_event' => [
                        'id' => 'dynamic_event',
                        'image' => 'dynamic-event.jpg',
                        'question' => 'Pixel Helper: ' . $this->l('We detected event code but the pixel has not activated for this event...'),
                    ],
                    'wrong_catalogue_id' => [
                        'id' => 'wrong_catalogue_id',
                        'image' => 'wrong-catalogue-id.jpg',
                        'question' => 'Pixel Helper :' . $this->l('The specified product catalog ID is not valid...'),
                    ],
                    'purchase_duplicates' => [
                        'id' => 'purchase_duplicates',
                        'question' => $this->l('I see some duplicate / missing Purchases in Facebook'),
                    ],
                    'custom_events' => [
                        'id' => 'custom_events',
                        'question' => $this->l('Can I use custom Events?'),
                    ],
                ],
                'img_path' => $this->_path . 'views/img/',
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/faq.tpl');
    }

    private function displayVideos()
    {
        $this->context->smarty->assign(
            [
                'fctp_videos' => [
                    [
                        'title' => $this->l('Installing Pixel Plus'),
                        'embed_url' => 'https://www.youtube.com/embed/KpuiRTUjGdM',
                    ],
                    [
                        'title' => $this->l('Testing the events with Pixel Helper'),
                        'embed_url' => 'https://www.youtube.com/embed/fjbO2RA-OTc',
                    ],
                ],
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/videos.tpl');
    }

    private function displayLogs()
    {
        $sql = 'SELECT message, date_add FROM ' . _DB_PREFIX_ . 'log WHERE object_type LIKE \'Pixel Plus\' OR POSITION(\'[EV-ID \' IN message) > 0 ORDER BY id_log DESC LIMIT 10';
        $results = Db::getInstance()->executeS($sql);
        if (empty($results)) {
            return '';
        }
        $logs = [];
        foreach ($results as $result) {
            $logs[] = $this->formatLogMessage($result);
        }
        if (empty($logs)) {
            return '';
        }

        // Assign logs and the link to the template
        $this->context->smarty->assign(
            [
                'pp_logs' => $logs,
                'logs_link' => $this->context->link->getAdminLink('AdminLogs'),
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/logs.tpl');
    }

    public function deletePixelPlusLogs()
    {
        // SQL query for deleting logs
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'log WHERE object_type LIKE "Pixel Plus" OR message LIKE "%[Conversion API]%" OR message LIKE "%[Pixel Plus]%" OR message LIKE "%[EV-ID%"';

        // Execute the query
        Db::getInstance()->execute($sql);
    }

    /**
     * Format the Log Messages
     * If it's a paypload save the pretty JSON into an array with a json key instead of returning only the string
     *
     * @param $message
     *
     * @return array|false|string[]
     */
    private function formatLogMessage($log)
    {
        $message = $log['message'];
        $line_break = '__break__'; // SmartForm::openTag('br')."\n";
        $as = ['[', ']', '\n', 'events_received=>', 'messages=>', 'fbtrace_id=>'];
        $ar = ['', $line_break, $line_break . ' events_received: ', $line_break . 'Events Received by Facebook: ', $line_break . ' fbtrace_id: '];
        if ($is_json = PixelTools::isJson(stripslashes($message))) {
            $message = json_encode(json_decode(stripslashes($message)), JSON_PRETTY_PRINT);
        }

        return [
            'is_json' => $is_json,
            'date_add' => PixelTools::formatDateStr($log['date_add'], true),
            'message' => array_filter(explode('__break__', str_replace($as, $ar, $message)), 'trim'),
        ];
    }

    /* Customer Export to CSV */
    protected static function createCSV($res, $typexp)
    {
        $fctp = Module::getInstanceByName('facebookconversiontrackingplus');
        $_file = [1 => 'export-customers.csv', 2 => 'export-newsletter.csv', 3 => 'export-all.csv'];
        if (count($res) > 0) {
            $line = implode("\n", $res);
            if ($file = @fopen(dirname(__FILE__) . '/csv/' . (string) $_file[$typexp], 'w')) {
                if (!fwrite($file, $line)) {
                    echo Tools::displayError($fctp->l('Error: cannot write') . ' ' . dirname(__FILE__) . '/csv/' . $_file[$typexp] . ' !');
                    fclose($file);
                } else {
                    fclose($file);

                    return true;
                }
            } else {
                echo Tools::displayError($fctp->l('Bad permissions, can\'t create the file'));
            }

            return false;
        } else {
            return false;
            // echo $this->context->smarty->display(__FILE__, 'views/templates/admin/csv-creation-alert.tpl');
        }
    }

    /* Start Micro Data Features */
    public function checkMicroData()
    {
        $msg = [];
        $mdata = $this->getMetadataArray();
        if (Shop::isFeatureActive()) {
            $shops = Shop::getShops();
            foreach ($shops as $shop) {
                $url = $this->getRandomProductURL($shop['id_shop']);
                Configuration::updateValue('FCTP_MICRODATA', json_encode($this->reviewMicroData($mdata, $url)), false, $shop['id_shop_group'], $shop['id_shop']);
                $msg[] = sprintf($this->l('Microdata for Shop %s reviewed. Now product catalogues can be created with the Facebook\'s pixel events'), $shop['name']);
            }
        } else {
            $url = $this->getRandomProductURL();
            Configuration::updateValue('FCTP_MICRODATA', json_encode($this->reviewMicroData($mdata, $url)));
            $msg[] = $this->l('Microdata reviewed. Now product catalogues can be created with the Facebook\'s pixel events');
        }
        $this->context->controller->confirmations[] = implode('<br>', $msg);
    }

    private function reviewMicroData($mdata, $url)
    {
        // preg_replace to remove HTML comments and prevent the module from finding tags commented in the HTML
        $product_html = preg_replace("~<!--(?!<!)[^\[>].*?-->~s", '', $this->getProductHTML($url));
        foreach ($mdata as $type => $fields) {
            foreach ($fields as $key => $item) {
                if ($type == 'og') {
                    $str = '&lt;meta property=&quot;' . $key . '&quot;';
                } elseif ($type == 'schema') {
                    $str = 'itemprop=&quot;' . $key . '&quot;';
                } else { // ItemType
                    $str = 'itemtype=&quot;' . $item . '&quot;';
                }
                // If it extists and we found it, we don't need to add this value
                if (PixelTools::strpos($product_html, $str) !== false) {
                    unset($mdata[$type][$key]);
                }
            }
        }

        return $mdata;
    }

    private function getRandomProductURL($id_shop = null, $mdata = false)
    {
        if ($id_shop === null) {
            $id_shop = Configuration::get('PS_SHOP_DEFAULT');
        }
        $sql = 'SELECT p.id_product 
            FROM `' . _DB_PREFIX_ . 'product` p' .
            Shop::addSqlAssociation('product', 'p') .
            ' WHERE product_shop.`visibility` IN ("both", "catalog")' .
            ' AND product_shop.`active` = 1' . '
            ORDER BY RAND()';
        $id_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if ($id_product > 0) {
            $p = new Product($id_product);
            $l = $this->context->language->id;
            $url = $this->context->link->getProductLink($p, $p->link_rewrite[$l], Category::getLinkRewrite($p->id_category_default, $l));
            if ($mdata == false) {
                $url .= (PixelTools::strpos($url, '?') === false ? '?' : '&') . 'mdata=0';
            }

            return $url;
        }

        return false;
    }

    private function getProductHTML($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
        // avoid error on conversion api -user_agent variable
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $output = curl_exec($ch);

        return htmlspecialchars($output);
    }

    private function getMetadataArray()
    {
        return [
            'og' => [
                'og:type' => 'type',
                'og:title' => 'title',
                'og:url' => 'url',
                'og:description' => 'description',
                'og:image' => 'image',
                'og:locale' => 'locale',
                'product:retailer_item_id' => 'retailer_item_id',
                'product:item_group_id' => 'groupID',
                'product:price:amount' => 'price',
                'product:price:currency' => 'priceCurrency',
                'product:condition' => 'condition',
                'product:availability' => 'availability',
                'brand' => 'brand',
                'product:category' => 'google_category',
                'product:sale_price:amount' => 'sale_price_amount',
                'product:sale_price:currency' => 'sale_price_currency',
                'product:sale_price_dates:start' => 'sale_price_dates_start',
                'product:sale_price_dates:end' => 'sale_price_dates_end',
            ],
            'schema' => [
                'url' => 'url',
                'image' => 'image',
                'description' => 'description',
                'productID' => 'productID',
                'title' => 'title',
                'brand' => 'brand',
                'price' => 'price',
                'salePrice' => 'sale_price_amount',
                'priceValidUntil' => 'sale_price_dates_end',
                'originalPrice' => 'price',
                'priceCurrency' => 'priceCurrency',
                'itemCondition' => 'conditionURL',
                'availability' => 'availability',
            ],
            'itemtype' => [
                'product' => 'https://schema.org/Product',
                'offers' => 'https://schema.org/Offer',
            ],
        ];
    }

    private function addMissingMicroData()
    {
        if ($this->displayMicrodata && $this->content_displayed === false && (!Tools::getIsset('mdata') || Tools::getValue('mdata') != 0)) {
            $this->content_displayed = true;
            // If not data is required return
            if (empty($this->schema) || !Configuration::get('FCTP_MICRO_SCHEMA')) {
                return;
            }

            // Check if there is an offer and update the fields as necessary
            if (isset($this->schema['offers']['salePrice']) && $this->schema['offers']['salePrice'] < $this->schema['offers']['price']) {
                $this->schema['offers']['price'] = $this->schema['offers']['salePrice'];
                unset($this->schema['offers']['salePrice']);
            } else {
                // If threre are no offers, remove the originalPrice tag
                unset($this->schema['offers']['originalPrice']);
                unset($this->schema['offers']['priceValidUntil']);
            }
            //            if ($_SERVER['REMOTE_ADDR'] == '79.117.167.118') {
            //                Tools::dieObject([
            //                    'micro_data' => $this->schema,
            //                    'schema' => $this->schema_structure,
            //                ]);
            //            }
            $this->context->smarty->assign(
                [
                    'micro_data' => $this->schema,
                    'schema' => $this->schema_structure,
                ]
            );

            return $this->display(dirname(__FILE__), 'views/templates/front/add-missing-microdata.tpl');
        }
    }

    private function addCombiInfo($params)
    {
        $ipa = false;
        $id_product = (int) Tools::getValue('id_product');
        if (is_object($params['product'])) {
            $ipa = $params['product']->id_product_attribute;
        } elseif (is_array($params['product'])) {
            $ipa = $params['product']['id_product_attribute'];
        } elseif (Tools::getIsset('id_product_attribute')) {
            $ipa = Tools::getValue('id_product_attribute');
        }
        $refresh_pixel_id = Tools::passwdGen(12);
        if (isset($this->api) && $this->api !== false) {
            $this->api->viewContentTrigger($refresh_pixel_id, $this->context->language->id, 'product', $id_product, $ipa);
        }
        if ($ipa !== false && $ipa > 0) { // WAS  && Tools::getValue('ajax') == 1
            $this->context->smarty->assign([
                'id_product_attribute' => $ipa,
                'refresh_pixel_id' => $refresh_pixel_id,
            ]);

            return $this->display(__FILE__, 'views/templates/front/productvars.tpl');
        }
    }

    private function dateFormatPixel($date)
    {
        $convertitme = strtotime($date);

        return date('Y-m-d', $convertitme) . 'T' . date('H:i:s', $convertitme) . '/' . date('Y-m-d', $convertitme) . 'T' . date('H:i:s', $convertitme);
    }

    private function getRequiredProductData()
    {
        if (empty($this->rmd) && !Tools::getValue('mdata')) {
            $this->content_displayed = true;

            return;
        }
        if (Tools::getIsset('id_product') && Tools::getValue('id_product') > 0) {
            $id = (int) Tools::getValue('id_product');
            $ipa = Tools::getIsset('id_product_attribute') ? Tools::getValue('id_product_attribute') : null;
            $l = $this->context->language->id;
            $prefix = Configuration::getGlobalValue('FPF_PREFIX_' . $this->context->shop->id) ? Configuration::getGlobalValue('FPF_PREFIX_' . $this->context->shop->id) : '';
            $p = new Product($id);
            if (Configuration::getGlobalValue('FCTP_COMBI_' . $this->context->shop->id) && (Tools::getValue('id_product_attribute') > 0)) {
                $id = $prefix . $p->id . Configuration::getGlobalValue('FCTP_COMBI_PREFIX_' . $this->context->shop->id) . (int) Tools::getValue('id_product_attribute');
            }
            $this->image_format = $this->getImageFormat(ImageType::getImagesTypes('products', true));
            $images = $this->prepareProductImages($p, $p->getImages($this->context->language->id), $ipa);
            $description = $this->prepareProductDescription($p);
            $pp = Configuration::get('PS_PRICE_DISPLAY_PRECISION') ? Configuration::get('PS_PRICE_DISPLAY_PRECISION') : 2;
            $usetax = !Group::getPriceDisplayMethod(Group::getCurrent()->id);
            $brand = ($p->manufacturer_name != '' ? $p->manufacturer_name : ((int) $p->id_manufacturer > 0 ? Manufacturer::getNameById($p->id_manufacturer) : $this->context->shop->name));
            $priceDisplay = Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer);

            // Tax only needs to be included if equals to 1
            $isTaxIncluded = ($priceDisplay == 1);
            $productPriceWithoutReduction = $p->getPriceWithoutReduct($isTaxIncluded, null);
            $productPriceWithoutReduction = Tools::ps_round($productPriceWithoutReduction, _PS_PRICE_DISPLAY_PRECISION_);

            $microdata = [
                'productID' => $p->id,
                'groupID' => $prefix . $p->id,
                'retailer_item_id' => $id,
                'title' => $this->removeHTML($p->name[$this->context->language->id]),
                'description' => $description,
                'condition' => $p->condition,
                'itemCondition' => $p->condition == 'new' ? 'NewCondition' : ($p->condition == 'used' ? 'UsedCondition' : 'RefurbishedCondition'),
                'conditionURL' => $p->condition == 'new' ? 'http://schema.org/NewCondition' : ($p->condition == 'used' ? 'https://schema.org/UsedCondition' : 'https://schema.org/RefurbishedCondition'),
                'availability' => $this->getProductAvailability($p->id, $ipa),
                'url' => $this->context->link->getProductLink($p, $p->link_rewrite[$l], Category::getLinkRewrite($p->id_category_default, $l), null, null, null, $ipa),
                'image' => $images,
                'type' => 'product.item', // 'og:product',
                'price' => $productPriceWithoutReduction,
                // 'price' => Product::getPriceStatic((int) $p->id, $usetax, $ipa, $pp, null, false, true, 1, false, null, null, null, $p->specificPrice),
                // was $p->getPrice(!Group::getDefaultPriceDisplayMethod(), null, (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION'))
                'priceCurrency' => $this->context->currency->iso_code,
                'locale' => $this->getLocale(),
                'brand' => $brand,
            ];
            $google_cat = GoogleCategories::getCategoryNameById($p->id_category_default);
            if (!empty($google_cat)) {
                $microdata['google_category'] = $google_cat;
            }
            $id_att = (int) Tools::getValue('id_product_attribute');
            if (is_object($p) && Product::isDiscounted($p->id)) {
                $discountAll = SpecificPrice::getByProductId($id);
                // handle discounts
                foreach ($discountAll as $discount) {
                    if ($id_att > 0 && $id_att != $discount['id_product_attribute']) {
                        continue;
                    }
                    $addfrom = false;
                    $addto = false;
                    if ($discount['to'] == '0000-00-00 00:00:00' && $discount['from'] == '0000-00-00 00:00:00') {
                        // no limit on both end so okay to conitnue the process
                        $addfrom = false;
                        $addto = false;
                    }
                    if (strtotime($discount['to']) >= strtotime(date('Y-m-d')) && strtotime($discount['from']) <= strtotime(date('Y-m-d'))) {
                        // coupon is valid
                        $addfrom = true;
                        $addto = true;
                    }
                    if (strtotime($discount['to']) <= strtotime(date('Y-m-d')) || strtotime($discount['from']) >= strtotime(date('Y-m-d'))) {
                        // coupon ends already
                        $addfrom = false;
                        $addto = false;
                    }
                    $microdata['sale_price_amount'] = Product::getPriceStatic((int) $p->id, $usetax, $ipa, $pp, null, false, true, 1, false, null, null, null, $p->specificPrice);
                    if ($addto) {
                        $microdata['sale_price_dates_end'] = $this->dateFormatPixel($discount['to']);
                    }
                    if ($addfrom) {
                        $microdata['sale_price_dates_start'] = $this->dateFormatPixel($discount['to']);
                    }
                    $microdata['sale_price_currency'] = $this->context->currency->iso_code;
                }
            }
            $offer_keys = ['price', 'priceCurrency', 'itemCondition', 'availability', 'priceValidUntil', 'originalPrice', 'salePrice'];
            foreach ($this->rmd as $type => $fields) {
                foreach ($fields as $key => $item) {
                    if ($type == 'og') {
                        // ignore the item which are associated with sale but discount is not available for this product
                        if (isset($microdata[$item])) {
                            $this->og[$key] = $microdata[$item];
                        }
                    } elseif ($type == 'schema') {
                        if (in_array($key, $offer_keys)) {
                            $base = 'offers';
                        } else {
                            $base = 'product';
                        }
                        if (isset($microdata[$item])) {
                            $this->schema[$base][$key] = $microdata[$item];
                        }
                    } else { // ItemType
                        $this->schema_structure[$key] = 1;
                    }
                }
            }
        }
    }

    private function getImageFormat($images_types)
    {
        foreach ($images_types as $image_type) {
            if (preg_match('/large|thickbox/i', $image_type['name'])) {
                return $image_type;
            }
        }

        return $images_types[0]['name'];
    }

    private function getProductAvailability($id, $ipa)
    {
        $sa = StockAvailable::outOfStock($id);
        $quantity = StockAvailable::getQuantityAvailableByProduct($id, $ipa);
        // echo '<!-- SmartQuantity: '.$quantity.' ID: '.$id.' IPA: '.$ipa.' -->';
        if ($quantity <= 0) {
            if ($sa == 1 || ($sa == 2 && (int) Configuration::get('PS_ORDER_OUT_OF_STOCK'))) {
                return 'available for order';
            } else {
                return 'out of stock';
            }
        }

        return 'in stock';
    }

    private function prepareProductImages($p, $images, $ipa = 0)
    {
        if (!empty($images)) {
            if (Configuration::get('FCTP_MICRO_IGNORE_COVER')) {
                $cover = $images[0];
            } else {
                $cover = Image::getCover($p->id);
            }
            $total = count($images);
            $found = false;
            $ret = [];
            if ($total > 1) {
                for ($i = 0; $i < $total; ++$i) {
                    if ($images[$i]['id_image'] == $cover['id_image']) {
                        unset($images[$i]);
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    return false;
                }
                array_unshift($images, $cover);
            } else {
                $images = [$cover];
            }
            unset($cover, $total);
            if ((int) Configuration::get('FCTP_MICRO_IMG_LIMIT') > 0) {
                $images = array_slice($images, 0, (int) Configuration::get('FCTP_MICRO_IMG_LIMIT'));
            }
            foreach ($images as $image) {
                $ret[] = $this->context->link->getImageLink($p->link_rewrite[$this->context->language->id], $image['id_image'], $this->image_format['name']);
            }

            return $ret;
        }

        return [];
    }

    /**
     * Get the first description available between the short description
     * the long description or the product name in this order
     * It also removes all HTML and replaces the paragraphs for line breaks.
     *
     * @param $p Product
     *
     * @return the formatted text
     */
    private function prepareProductDescription($p)
    {
        $lang_id = (int) $this->context->language->id;
        $desc = $this->removeHTML($p->description_short[$lang_id]);
        if ($desc == '') {
            $desc = $this->removeHTML($p->description[$lang_id]);
            if ($desc == '') {
                $desc = $this->removeHTML($p->name[$lang_id]);
            }
        }

        return $desc;
    }

    private function removeHTML($string)
    {
        $search = ['</p>', '<br>', '<br/>', '<br />'];
        $replace = ['</p>' . "\n\n", '<br>' . "\n", '<br/>' . "\n", '<br />' . "\n"];
        $string = trim(strip_tags(str_replace($search, $replace, $string)));
        if ($this->countUpperCase($string) > (Tools::strlen($string) * 0.5)) {
            // return $this->removeUppercaseWords(strip_tags(str_replace($search, $replace, $str)));
            return $this->fixUppercase($string);
        }

        return $string;
    }

    /*private function removeUppercaseWords($desc)
{
    $words = explode(' ', $desc);
    foreach ($words as &$w) {
        if ($this->countUpperCase($w) > (Tools::strlen($w) * 0.8)) {
            $w = $this->fixUppercase($w);
        }
    }
    return implode(' ', $words);
}*/
    private function fixUppercase($string)
    {
        if (function_exists('mb_convert_case')) {
            return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
        } else {
            return ucwords($string);
        }
    }

    private function countUpperCase($string)
    {
        return Tools::strlen(preg_replace('![^A-Z]+!', '', $string));
    }

    private function getLocale()
    {
        $lang = $this->context->language;
        // $special_cases = array('en', 'es', 'fr', 'ja', 'nl', 'no', 'pt', 'tl');
        if (isset($lang->locale)) {
            $code = $lang->locale;
        } elseif (isset($lang->language_code)) {
            $code = $lang->language_code;
        } else {
            $code = $lang->iso_code;
        }
        $code = preg_split('/(\-|\_)/', $code);
        if (!isset($code[1])) {
            $code[1] = $code[0];
        }
        $code[1] = Tools::strtoupper($code[1]);

        return $code[0] . '_' . $code[1];
    }

    private function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && Tools::strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function ajaxProcessGenerateToken()
    {
        $success = false;
        $key = '';
        $time = 15;
        if (Tools::getValue('pp_token') == Tools::getAdminTokenLite($this->context->controller->controller_name)) {
            $success = true;
            $key = PixelTools::hash('getCookies' . time() . _COOKIE_KEY_);
            Configuration::deleteByName('FCTP_MICRO_TOKEN');
            Configuration::updateGlobalValue('FCTP_MICRO_TOKEN', $key);
        }
        echo json_encode([
            'success' => $success,
            'key' => $key,
            'countdown' => $time,
        ]);
        exit;
    }

    private function setCookie($cookieName, $cookieValue, $cookieExpires, $cookiePath)
    {
        ob_start();
        setcookie($cookieName, $cookieValue, $cookieExpires, $cookiePath);
        ob_end_flush();
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        if ($this->active) {
            $output = '';
            if ($this->isAjaxRequest()) {
                $output .= $this->addCombiInfo($params);
            }
            if (!$this->content_displayed) {
                $output .= $this->addMissingMicroData();
            }

            return $output;
        }
    }

    public function hookDisplayLeftColumnProduct()
    {
        if ($this->active && !$this->content_displayed) {
            return $this->addMissingMicroData();
        }
    }

    public function hookDisplayRightColumnProduct()
    {
        if ($this->active && !$this->content_displayed) {
            return $this->addMissingMicroData();
        }
    }

    public function hookActionCustomerLogoutAfter()
    {
        if (isset($_COOKIE['pp_external_id'])) {
            unset($_COOKIE['pp_external_id']);
            $this->setCookie('pp_external_id', null, time() - 3600, '/');
        }
    }
}
