<?php
/**
* 2012-2015 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Merchant Center Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2015 Patryk Marek - PrestaDev.pl
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @link      http://prestadev.pl
* @package   PD Google Merchant Center Pro - PrestaShop 1.5.x and 1.6.x Module
* @version   2.2.0
* @date      04-03-2016
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/models/GoogleMerchantCenterProModel.php');
require_once(dirname(__FILE__).'/models/GoogleMerchantCenterProModelDictionary.php');
require_once(dirname(__FILE__).'/models/GoogleMerchantCenterProModelTaxonomy.php');

require_once(dirname(__FILE__).'/classes/Html2Text.php');

class PdGoogleMerchantCenterPro extends Module
{
    const GOOGLE_TAXONOMY_DATA_URL = 'http://www.google.com/basepages/producttype/';

    private $html = '';
    public $secure_key;

    public $limit = 1000;

    public $ps_ver_15;
    public $ps_ver_16;

    public $order_out_of_stock;
    public $stock_management;
    public $advanced_stock_management;

    private $source_dictionary_array = array();
    private $destination_dictionary_array = array();

    public $languagesIsoTranslation;
    public $countriesIsoTranslation;

    protected static $_prices = array();
    protected static $_pricesLevel2 = array();

    protected static $tax_rates_cache = array();

    protected static $psEcotaxTaxRulesGroupId = null;

    public static $_taxCalculationMethod = null;

    // Google taxonomies curently available and their associations
    public $googleTaxonomiesCorelations = array(
        'pl-PL' => array('languages' => 'pl',                           'countries' => 'PL',                                'currencies' => 'PLN'),
        'en-US' => array('languages' => 'en, el, fi, sv, ro',           'countries' => 'US, CA, CL, GR, FI, RO',                                'currencies' => 'USD, CAD, CLP. EURO, RON'),
        'en-GB' => array('languages' => 'en, gb',                       'countries' => 'GB, AU, IN, CH',                                'currencies' => 'GBP, AUD, INR, CHF'),
        'fr-FR' => array('languages' => 'fr',                           'countries' => 'FR, CH, CA, BE',                                'currencies' => 'EUR, CHF, CAD'),
        'de-DE' => array('languages' => 'de',                           'countries' => 'DE, CH, AT',                                'currencies' => 'EUR, CHF'),
        'it-IT' => array('languages' => 'it',                           'countries' => 'IT, CH',                                'currencies' => 'EUR, CHF'),
        'nl-NL' => array('languages' => 'nl',                           'countries' => 'NL, BE',                                'currencies' => 'EUR'),
        'es-ES' => array('languages' => 'es',                           'countries' => 'ES, MX, CL',                                'currencies' => 'EUR, MXN, CLP'),
        'zh-CN' => array('languages' => 'zh',                           'countries' => 'CN',                                'currencies' => 'CNY'),
        'ja-JP' => array('languages' => 'ja',                           'countries' => 'JP',                                'currencies' => 'JPY'),
        'pt-BR' => array('languages' => 'br',                           'countries' => 'BR',                                'currencies' => 'BRL'),
        'cs-CZ' => array('languages' => 'cs',                           'countries' => 'CZ',                                'currencies' => 'CSK'),
        'ru-RU' => array('languages' => 'ru',                           'countries' => 'RU',                                'currencies' => 'RUB'),
        'sv-SE' => array('languages' => 'sv',                           'countries' => 'SE',                                'currencies' => 'SEK'),
        'da-DK' => array('languages' => 'da',                           'countries' => 'DK',                                'currencies' => 'DKK'),
        'no-NO' => array('languages' => 'no',                           'countries' => 'NO',                                'currencies' => 'NOK'),
        'tr-TR' => array('languages' => 'tr',                           'countries' => 'TR',                                'currencies' => 'TRY'),
        'pt-BR' => array('languages' => 'hu',                           'countries' => 'HU',                                'currencies' => 'EUR'),
        'sk-SK' => array('languages' => 'sk', 'countries' => 'SK', 'currencies' => 'EUR'),
        'pt-PT' => array('languages' => 'pt', 'countries' => 'PT', 'currencies' => 'EUR'),
        'fi-FI' => array('languages' => 'fi, sv', 'countries' => 'FI', 'currencies' => 'EUR'),
        'ro-RO' => array('languages' => 'ro', 'countries' => 'RO', 'currencies' => 'RON'),
        'gr-GR' => array('languages' => 'gr', 'countries' => 'GR', 'currencies' => 'EUR'),

    );

    public function __construct()
    {
        $this->name = 'pdgooglemerchantcenterpro';
        $this->author = 'PrestaDev.pl';
        $this->tab = 'seo';
        $this->version = '2.6.3';

        $this->bootstrap = true;
        $this->module_key = '9dc4b5105bfdb2784bdabd2bbb5ca95f';
        $this->secure_key = Tools::encrypt(_COOKIE_KEY_);

        parent::__construct();

        $this->displayName = $this->l('PD Google Shopping Pro');
        $this->description = $this->l('Module generating XML feeds for Google Merchant Center, per country, language, currency, shop');

        $this->prefix = 'PD_GMCP_';

        $this->ps_ver_15 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.5', '=')) ? true : false;
        $this->ps_ver_16 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')) ? true : false;
        $this->ps_ver_17 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '=')) ? true : false;
        $this->ps_ver_8 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '8.0', '>=')) ? true : false;

        $this->ps_ver_1770_gt = (version_compare(Tools::substr(_PS_VERSION_, 0, 7), '1.7.7.0', '>=')) ? true : false;

        // Stock options and allow buy when ouf off stock
        $this->order_out_of_stock = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
        $this->stock_management = (int)Configuration::get('PS_STOCK_MANAGEMENT');
        $this->advanced_stock_management = (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');

        $this->assignLangTranslationsArr();
        $this->assignCountriesTranslationsArr();
    }


    public function assignLangTranslationsArr()
    {
        $this->languagesIsoTranslation = array(
            'pl' => $this->l('Polish'),
            'en' => $this->l('English (US)'),
            'gb' => $this->l('English (GB)'),
            'fr' => $this->l('French'),
            'de' => $this->l('Deutsch'),
            'it' => $this->l('Italian'),
            'nl' => $this->l('Dutch'),
            'es' => $this->l('Spanish'),
            'zh' => $this->l('Chinese'),
            'ja' => $this->l('Japanese'),
            'br' => $this->l('Breton'),
            'cs' => $this->l('Czech'),
            'ru' => $this->l('Russian'),
            'sv' => $this->l('Swedish'),
            'da' => $this->l('Danish'),
            'no' => $this->l('Norwegian'),
            'tr' => $this->l('Turkish'),
            'sk' => $this->l('Slovakia'),
            'el' => $this->l('Greek'),
            'fi' => $this->l('Finnish'),
            'hu' => $this->l('Hungarian'),
            'ro' => $this->l('Romanian'),
            'sk' => $this->l('Slovak'),
            'pt' => $this->l('Portuguese'),
            'fi' => $this->l('Finnish'),
            'ro' => $this->l('Romanian'),
            'gr' => $this->l('Greek'),
        );
    }

    public function assignCountriesTranslationsArr()
    {
        $this->countriesIsoTranslation = array(
            'PL' => $this->l('Poland'),
            'US' => $this->l('United States'),
            'CA' => $this->l('Canada'),
            'CL' => $this->l('Chile'),
            'GB' => $this->l('United Kingdom'),
            'AU' => $this->l('Australia'),
            'IN' => $this->l('India'),
            'CH' => $this->l('Switzerland'),
            'FR' => $this->l('France'),
            'BE' => $this->l('Belgium'),
            'DE' => $this->l('Germany'),
            'AT' => $this->l('Austria'),
            'IT' => $this->l('Italy'),
            'NL' => $this->l('Netherlands'),
            'ES' => $this->l('Spain'),
            'MX' => $this->l('Mexico'),
            'CN' => $this->l('China'),
            'JP' => $this->l('Japan'),
            'BR' => $this->l('Brazil'),
            'RU' => $this->l('Russian Federation'),
            'CZ' => $this->l('Czech Republic'),
            'SE' => $this->l('Sweden'),
            'DK' => $this->l('Denmark'),
            'NO' => $this->l('Norway'),
            'TR' => $this->l('Turkey'),
            'SK' => $this->l('Slovakia'),
            'GR' => $this->l('Greece'),
            'FI' => $this->l('Finland'),
            'HU' => $this->l('Hungary'),
            'PT' => $this->l('Portugal'),
            'RO' => $this->l('Romania'),
        );
    }


    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('actionCarrierUpdate')
            || !$this->registerHook('displayAdminProductsExtra')
            || !$this->registerHook('actionProductAdd')
            || !$this->registerHook('actionAdminProductsListingFieldsModifier')
            || !$this->registerHook('actionGetProductPropertiesAfterUnitPrice')
            || !$this->alterProductTable()
            || !GoogleMerchantCenterProModel::createTables()
            || !GoogleMerchantCenterProModelDictionary::createTables()
            || !GoogleMerchantCenterProModelTaxonomy::createTables()
            || !GoogleMerchantCenterProModelTaxonomy::createTablesTaxonomyData()
            || !GoogleMerchantCenterProModelTaxonomy::createTablesTaxonomyCategory()
            || !GoogleMerchantCenterProModelTaxonomy::addTaxonomyCorelations()
            || !$this->installModuleTabs()) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->unAlterProductTable()
            || !$this->uninstallModuleTab('AdminGoogleMerchantCenterPro')
            || !$this->uninstallModuleTab('AdminGoogleMerchantCenterProNew')
            || !$this->uninstallModuleTab('AdminGoogleMerchantCenterProDictionary')
            || !$this->uninstallModuleTab('AdminGoogleMerchantCenterProTaxonomy')
            || !GoogleMerchantCenterProModel::dropTables()
            || !GoogleMerchantCenterProModelDictionary::dropTables()
            || !GoogleMerchantCenterProModelTaxonomy::dropTables()
            || !GoogleMerchantCenterProModelTaxonomy::dropTablesTaxonomyCategory()
            || !GoogleMerchantCenterProModelTaxonomy::dropTablesTaxonomyData()) {
            return false;
        }
        return true;
    }

    public function alterProductTable()
    {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `product_name_google_shopping` varchar(256) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `product_short_desc_google_shopping` TEXT');

        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product` ADD `in_google_shopping` tinyint(1) NOT NULL default 1');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_shop` ADD `in_google_shopping` tinyint(1) NOT NULL default 1');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_0` varchar(100) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_1` varchar(100) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_2` varchar(100) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_3` varchar(100) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_4` varchar(100) NOT NULL');

        return true;
    }

    public function unAlterProductTable()
    {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `product_name_google_shopping`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product` DROP `in_google_shopping`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_shop` DROP `in_google_shopping`');

        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_0`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_1`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_2`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_3`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_4`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `product_short_desc_google_shopping`');



        return true;
    }


    private function installModuleTabs()
    {
        $languages = Language::getLanguages();

        $tabs = array(
                'AdminGoogleMerchantCenterProNew' => array(
                    'en' => 'Add new / View list',
                    'pl' => 'Dodaj nową / przeglądaj'),
                'AdminGoogleMerchantCenterProTaxonomy' => array(
                    'en' => 'Category mapping / import',
                    'pl' => 'Mapowanie kategorii'),
                'AdminGoogleMerchantCenterProDictionary' => array(
                    'en' => 'Dictionary management',
                    'pl' => 'Zarządzanie słownikiem'),
                );

        $main_tab_lang = array(
                'en' => 'Google Shopping Pro',
                'pl' => 'Google Shopping Pro');

        $main_tab_names_array = array();
        foreach ($main_tab_lang as $tab_iso => $main_tab_name) {
            foreach ($languages as $language) {
                if ($language['iso_code'] == $tab_iso) {
                    $main_tab_names_array[$language['id_lang']] = $main_tab_name;
                } else {
                    $main_tab_names_array[$language['id_lang']] = $this->l('Google Shopping Pro');
                }
            }
        }

        $main_tab_id = $this->installModuleTab('AdminGoogleMerchantCenterPro', $main_tab_names_array, 0);

        if ($main_tab_id) {
            foreach ($tabs as $class => $tab) {
                // tabs names as array where key is an a id_language
                $tab_names_array = array();
                foreach ($tab as $tab_iso => $tab_name) {
                    foreach ($languages as $language) {
                        if ($language['iso_code'] == $tab_iso) {
                            $tab_names_array[$language['id_lang']] = $tab_name;
                        } else {
                            if ($class == 'AdminGoogleMerchantCenterProNew') {
                                $tab_names_array[$language['id_lang']] = $this->l('Add new / View list');
                            } elseif ($class == 'AdminGoogleMerchantCenterProTaxonomy') {
                                $tab_names_array[$language['id_lang']] = $this->l('Category mapping / import');
                            } else {
                                $tab_names_array[$language['id_lang']] = $this->l('Dictionary management');
                            }
                        }
                    }
                }

                $this->installModuleTab($class, $tab_names_array, $main_tab_id);
            }
        }
        return true;
    }

    private function installModuleTab($tabClass, $tab_name, $id_tab_parent)
    {
        file_put_contents('../img/t/'.$tabClass.'.gif', Tools::file_get_contents('logo.gif'));

        $tab = new Tab();
        $tab->name = $tab_name;
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $id_tab_parent;

        if (!$tab->save()) {
            return false;
        }

        return $tab->id;
    }

    private function uninstallModuleTab($tabClass)
    {
        $id_tab = Tab::getIdFromClassName($tabClass);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }
        return false;
    }

    public function getContent()
    {
        if (Tools::isSubmit('save_auto_assign')) {
            $this->_postProcess();
        } else {
            $this->html .= '<br />';
        }

        $this->html .= '<h2>'.$this->displayName.' (v'.$this->version.')</h2><p>'.$this->description.'</p>';
        $this->html .= $this->renderForm();

        return $this->html;
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('save_auto_assign')) {
            Configuration::updateValue($this->prefix.'ASSIGN_ON_ADD', Tools::getValue($this->prefix.'ASSIGN_ON_ADD'));
            $this->html .= $this->displayConfirmation($this->l('Setting was updated'));
        }
    }


    public function renderForm()
    {
        $switch = version_compare(_PS_VERSION_, '1.6.0', '>=') ? 'switch' : 'radio';

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Automatic option assigment "In Google Shopping" for new products'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => $switch,
                        'label' => $this->l('Active'),
                        'name' => $this->prefix.'ASSIGN_ON_ADD',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l('Set if every new product added should get assigned in generated feed for Google Shopping automaticly'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),

                'submit' => array(
                    'name' => 'save_auto_assign',
                    'title' => $this->l('Save auto assign'),
                )
            ),
        );


        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
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
        $return[$this->prefix.'ASSIGN_ON_ADD'] = Configuration::get($this->prefix.'ASSIGN_ON_ADD');

        return $return;
    }

    public function getServicesList()
    {
        $sql = 'SELECT pdgmcp.*FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro` pdgmcp';
        return Db::getInstance()->executeS($sql);
    }


    public function getCategories($id_lang, $id_shop, $active = true, $sql_filter = '')
    {
        $sql = 'SELECT c.`id_category`, c.`id_parent`, cl.`name`, cl.`id_shop`, cl.`id_lang`
                FROM `'._DB_PREFIX_.'category` c
                INNER JOIN `'._DB_PREFIX_.'category_shop` category_shop ON (category_shop.id_category = c.id_category AND category_shop.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category` AND cl.id_shop = '.(int)$id_shop.'
                WHERE 1 '.$sql_filter.'
                AND `id_lang` = '.(int)$id_lang.'
                '.($active ? 'AND `active` = 1' : '').'
                '.(!$id_lang ? 'GROUP BY c.id_category' : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function generateCategoryPath($id_lang, $id_shop, $taxonomy_lang = false)
    {
        //Get all categories and add FILTER clausule for PS 1.5 for greater that 1 because 1 is root
        $c_tmp = $this->getCategories($id_lang, $id_shop, false, 'AND c.`id_category` > 1');

        //d($c_tmp);
        $c_arr = array();

        foreach ($c_tmp as $c) {
            $c_arr[$c['id_category']] = $c;
        }

        //Add root category to categories array
        $shop = new Shop($id_shop);
        $root_c = Category::getRootCategory($id_lang, $shop);
        $c_arr[$root_c->id] = array('id_category' => $root_c->id, 'id_parent' => $root_c->id_parent, 'name' =>  $root_c->name);
        //END

        foreach ($c_arr as $c) {
            // if ($this->ps_ver_17) {
            //     $c_arr[$c['id_category']]['path'] = $this->getCategoryPathPs17($c['id_category'], $id_lang, $id_shop);
            // } else {
            $c_arr[$c['id_category']]['path'] = $this->getCategoryPath($c['id_category'], $id_lang, $id_shop);
            //}

            if ($taxonomy_lang) {
                $path_google_taxonomy = $this->getGoogleTaxonomyCategoryValue($c['id_category'], $taxonomy_lang);
                $c_arr[$c['id_category']]['path_google_taxonomy'] = $path_google_taxonomy['txt_taxonomy'];
            } else {
                $c_arr[$c['id_category']]['path_google_taxonomy'] = '';
            }
        }

        return $c_arr;
    }

    public function getCategoryPath($id_category, $id_lang, $id_shop)
    {
        $interval = Category::getInterval($id_category);
        $shop = new Shop($id_shop);
        $root_category = Category::getRootCategory($id_lang, $shop);
        $id_root_category = $root_category->id;
        $interval_root = Category::getInterval($id_root_category);
        $pipe = ' > ';

        if ($interval) {
            $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
                    FROM '._DB_PREFIX_.'category c
                    INNER JOIN `'._DB_PREFIX_.'category_shop` category_shop ON (category_shop.id_category = c.id_category AND category_shop.id_shop = '.(int)$id_shop.')
                    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category` AND cl.id_shop = '.(int)$id_shop.'
                    WHERE c.nleft <= '.$interval['nleft'].'
                        AND c.nright >= '.$interval['nright'].'
                        AND c.nleft >= '.$interval_root['nleft'].'
                        AND c.nright <= '.$interval_root['nright'].'
                        AND cl.id_lang = '.(int)$id_lang.'
                        AND c.active = 1
                        AND c.level_depth > '.(int)$interval_root['level_depth'].'
                    ORDER BY c.level_depth ASC';

            $categories = Db::getInstance()->executeS($sql);
            $n = 1;
            $n_categories = count($categories);
            $full_path = '';
            $path = '';
            $return = '';

            foreach ($categories as $category) {
                $full_path .= $category['name'].(($n++ != $n_categories || !empty($path)) ? $pipe : '');
            }

            $return = $full_path.$path;

            // Sort out products with category default assign to home
            if (!empty($return)) {
                return $full_path.$path;
            } else {
                return $root_category->name;
            }
        } else {
            return $root_category->name;
        }
    }

    public function getCategoryPathPs17($id_category, $id_lang, $id_shop, $home = true)
    {
        $shop = new Shop($id_shop);
        $root_category = Category::getRootCategory($id_lang, $shop);
        $id_root_category = $root_category->id;
        $pipe = ' > ';

        $context = Context::getContext();

        $category = Db::getInstance()->getRow(
            '
            SELECT id_category, level_depth, nleft, nright
            FROM '._DB_PREFIX_.'category
            WHERE id_category = '.(int)$id_category
        );


        if (isset($category['id_category'])) {
            $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
                    FROM '._DB_PREFIX_.'category c
                    LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category'.Shop::addSqlRestrictionOnLang('cl').')
                    WHERE c.nleft <= '.(int)$category['nleft'].'
                        AND c.nright >= '.(int)$category['nright'].'
                        AND cl.id_lang = '.(int)$context->language->id.
                       ($home ? ' AND c.id_category='.(int)$id_category : '').'
                        AND c.id_category != '.(int)Category::getTopCategory()->id.'
                        AND c.active = 1
                    GROUP BY c.id_category
                    ORDER BY c.level_depth ASC
                    LIMIT '.(!$home ? (int)$category['level_depth'] + 1 : 1);

            $categories = Db::getInstance()->executeS($sql);
            $path = '';
            $return = '';
            $full_path = '';
            $n = 1;
            $n_categories = (int)count($categories);

            foreach ($categories as $category) {
                $full_path .= $category['name'].(($n++ != $n_categories || !empty($path)) ? $pipe : '');
            }

            $return = $full_path.$path;

            // Sort out products with category default assign to home
            if (!empty($return)) {
                return $full_path.$path;
            } else {
                return $root_category->name;
            }
        } else {
            return $root_category->name;
        }
    }


    public function generateFeedFromConfig($all = false, $id_pdgooglemerchantcenterpro = false)
    {
        $generated = false;
        $feeds = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro`
            WHERE `active` = 1'. (!$all ? ' AND `id_pdgooglemerchantcenterpro` = '.(int)$id_pdgooglemerchantcenterpro.'' : ''));

        foreach ($feeds as $feed) {
            $id_pdgooglemerchantcenterpro = $feed['id_pdgooglemerchantcenterpro'];
            $generated = $this->generateFile($id_pdgooglemerchantcenterpro);

            // Last generating date set in db per id_pdgooglemerchantcenterpro
            if ($generated) {
                $this->updateGeneratingTime($id_pdgooglemerchantcenterpro);
            }
        }

        return $generated;
    }

    public function setSourceAndDestinationDictionaryArrays()
    {
        $this->source_dictionary_array = Db::getInstance()->executeS('
            SELECT source_word
            FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro_dictionary`
            WHERE `active` = 1');

        $this->destination_dictionary_array = Db::getInstance()->executeS('
            SELECT destination_word
            FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro_dictionary`
            WHERE `active` = 1');
    }


    public function useDictionaryForString($string)
    {
        if (count($this->source_dictionary_array) && count($this->destination_dictionary_array)) {
            foreach ($this->source_dictionary_array as $key => $source) {
                $string = str_replace($source, $this->destination_dictionary_array[$key]['destination_word'], $string);
            }
        }

        return  $string;
    }



    public function updateGeneratingTime($id_pdgooglemerchantcenterpro)
    {
        Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'pdgooglemerchantcenterpro`
                SET `date_gen`= "'.date('Y-m-d H:i:s').'"
                WHERE `id_pdgooglemerchantcenterpro`= '.(int)$id_pdgooglemerchantcenterpro);
    }

    private function generateFile($id_pdgooglemerchantcenterpro)
    {
        // Set as cache source and destination arrays to avoid db queries
        $this->setSourceAndDestinationDictionaryArrays();

        $obj = new GoogleMerchantCenterProModel($id_pdgooglemerchantcenterpro);

        $id_lang = $obj->id_lang;
        $id_shop = $obj->id_shop;
        $id_currency = $obj->id_currency;
        $description_conf = $obj->description;
        $adults_conf = $obj->adults;
        $gtin_conf = $obj->gtin;
        $mpn_conf = $obj->mpn;
        $mpn_prefix = $obj->mpn_prefix;
        $gid_prefix = $obj->gid_prefix;
        $include_shipping_cost_conf = $obj->include_shipping_cost;
        $rewrite_url = $obj->rewrite_url;
        $available_for_order = $obj->available_for_order;
        $id_pdgooglemerchantcenterpro_taxonomy = $obj->id_pdgooglemerchantcenterpro_taxonomy;
        $products_attributes = $obj->products_attributes;
        $id_image_type = $obj->id_image_type;

        $ean_validiation = $obj->ean_validiation;
        $html_desc_cleaner = $obj->html_desc_cleaner;
        $img_limit = $obj->image_limit;

        if (isset($id_image_type) && is_numeric($id_image_type) && $id_image_type > 0) {
            $type = Db::getInstance()->getValue('SELECT `name` FROM `'._DB_PREFIX_.'image_type` WHERE id_image_type =' .$id_image_type);
        } else {
            if ($this->ps_ver_16) {
                $type = ImageType::getFormatedName('thickbox');
            } else {
                $type = ImageType::getFormattedName('large');
            }
        }

        // Show memory usage for testing
        // p('Memory usage at the begining:');
        // self::echoMemoryUsage();

        $currency = Currency::getCurrencyInstance($id_currency);

        $path_parts = pathinfo(__FILE__);

        $xml_writer = new XMLWriter();
        $xml_writer->openMemory();
        $xml_writer->setIndent(true);

        $generate_file_path = $path_parts['dirname'].'/../../google-merchant_id-'.$id_pdgooglemerchantcenterpro.'.xml';

        $xml_writer->startDocument('1.0', 'UTF-8');
        $xml_writer->text('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">');
        $xml_writer->startElement('channel');

        $xml_writer->writeElement('title', Configuration::get('PS_SHOP_NAME', null, null, $id_shop));

        // get google etaxonomy iso lang form taxonomy id
        $object = new GoogleMerchantCenterProModelTaxonomy($id_pdgooglemerchantcenterpro_taxonomy);
        $taxonomy_lang = $object->taxonomy_lang;

        $cat_path_arr = $this->generateCategoryPath($id_lang, $id_shop, $taxonomy_lang);
        $feed_counter = 0;

        // count products to for loop
        $count_products = self::getProductsDBLightCount($obj);
        //d($count_products);

        // get products from sql by 100
        for ($offset = 0; $offset < $count_products; $offset += $this->limit) {
            $products = $this->getProducts($obj, $offset);
            foreach ($products as $p) {
                $feed_counter++;

                $product_description = '';
                if ($description_conf == 1) {
                    $product_description = trim($p['product_description_short']);
                    $product_description = $this->useDictionaryForString($product_description);
                    $product_description = self::html2txt($product_description, $html_desc_cleaner);
                    $product_description = self::splitWords($product_description, 4995, '...');

                    // try to add some description anyway is any of them is empty
                    if ($product_description == '') {
                        $product_description = trim($p['description']);
                        $product_description = $this->useDictionaryForString($product_description);
                        $product_description = self::html2txt($product_description, $html_desc_cleaner);
                        $product_description = self::splitWords($product_description, 4995, '...');
                    }
                } elseif ($description_conf == 2) {
                    $product_description = trim($p['description']);
                    $product_description = $this->useDictionaryForString($product_description);
                    $product_description = self::html2txt($product_description, $html_desc_cleaner);
                    $product_description = self::splitWords($product_description, 4995, '...');

                    // try to add some description anyway is any of them is empty
                    if ($product_description == '') {
                        $product_description = trim($p['product_description_short']);
                        $product_description = $this->useDictionaryForString($product_description);
                        $product_description = self::html2txt($product_description, $html_desc_cleaner);
                        $product_description = self::splitWords($product_description, 4995, '...');
                    }
                } elseif ($description_conf == 3) {
                    $product_description = $p['meta_description'];
                    $product_description = $this->useDictionaryForString($product_description);
                    $product_description = self::html2txt($product_description, $html_desc_cleaner);
                    $product_description = self::splitWords($product_description, 4995, '...');
                }


                // Open new item element
                $xml_writer->startElement('item');

                // GTIN / ID
                if (isset($p['id_product_attribute']) && $p['id_product_attribute'] > 0) {
                    $xml_writer->writeElement('g:id', $gid_prefix.$p['id_product'].($p['id_product_attribute'] ? '-'.$p['id_product_attribute'] : ''));
                } else {
                    $xml_writer->writeElement('g:id', $gid_prefix.$p['id_product']);
                }

                // Title
                $product_name = $this->useDictionaryForString($p['product_name']);
                $xml_writer->writeElement('g:title', Tools::substr($product_name, 0, 150));

                // Link
                $xml_writer->writeElement('g:link', self::getProductLink($p['id_product'], $id_lang, $id_shop, isset($p['id_product_attribute']) ? $p['id_product_attribute'] : 0, $rewrite_url, $p['link_rewrite']));

                // Price
                $xml_writer->writeElement('g:price', $p['price'].' '.$currency->iso_code);

                // Sale price

                if ($p['price'] > $p['price_sale']) {
                    $xml_writer->writeElement('g:sale_price', $p['price_sale'].' '.$currency->iso_code);
                }


                if (isset($p['sale_price_effective_date'])) {
                    $xml_writer->writeElement('g:sale_price_effective_date', $p['sale_price_effective_date']);
                }


                // Unit price
                if (!empty($p['unit_pricing_measure'])) {
                    $xml_writer->writeElement('g:unit_pricing_measure', $p['unit_pricing_measure']);
                    $xml_writer->writeElement('g:unit_pricing_base_measure', $p['unit_pricing_base_measure']);
                }

                // Description
                $xml_writer->writeElement('g:description', Tools::stripslashes($product_description));

                // Condition
                $xml_writer->writeElement('g:condition', $p['condition']);

                // Custom labels 0 - 4
                $xml_writer->writeElement('g:custom_label_0', $p['custom_label_0']);
                $xml_writer->writeElement('g:custom_label_1', $p['custom_label_1']);
                $xml_writer->writeElement('g:custom_label_2', $p['custom_label_2']);
                $xml_writer->writeElement('g:custom_label_3', $p['custom_label_3']);
                $xml_writer->writeElement('g:custom_label_4', $p['custom_label_4']);

                // Adults
                if ($adults_conf) {
                    $xml_writer->writeElement('g:adult', 'TRUE');
                }

                // Image link and additional image link
                if (count($p['images'])) {
                    $img_counter = 0;
                    foreach ($p['images'] as $i) {
                        $img_counter++;

                        if ($img_limit && ($img_counter > $img_limit)) {
                            break;
                        }

                        if ($img_counter == 1) {
                            $xml_writer->writeElement('g:image_link', self::getImageLink($p['link_rewrite'], $p['id_product'].'-'.$i['id_image'], $id_shop, $rewrite_url, $type));
                        }

                        if ($img_counter > 1) {
                            $xml_writer->writeElement('g:additional_image_link', self::getImageLink($p['link_rewrite'], $p['id_product'].'-'.$i['id_image'], $id_shop, $rewrite_url, $type));
                        }

                        if ($img_counter == 10) {
                            break;
                        }  // only 10 additional images are possible to add
                    }
                }

                // Availability and available_date
                if ($p['quantity'] > 0 && $p['available_date'] == '0000-00-00') {
                    $xml_writer->writeElement('g:availability', 'in stock');
                } elseif ($p['quantity'] == 0 && $p['available_date'] == '0000-00-00') {
                    $xml_writer->writeElement('g:availability', 'out of stock');
                // } elseif ($p['available_date'] !== '0000-00-00') {
                //     $xml_writer->writeElement('g:availability', 'preorder');
                //     $xml_writer->writeElement('g:availability_date', $p['available_date'].'T00:00:00');
                } else {
                    $xml_writer->writeElement('g:availability', 'in stock');
                }

                // Brand
                $brand_exists = false;
                if (!empty($p['manufacturer_name'])) {
                    $xml_writer->writeElement('g:brand', htmlspecialchars($p['manufacturer_name'], ENT_COMPAT, 'UTF-8'));
                    $brand_exists = true;
                }

                // Gtin
                $gtin_exists = false;

                if ($gtin_conf == 1 && !empty($p['ean13'])) {
                    $gtin = $p['ean13'];
                    $gtin_exists = true;
                } elseif ($gtin_conf == 2 && !empty($p['upc'])) {
                    $gtin = $p['upc'];
                    $gtin_exists = true;
                } elseif ($gtin_conf == 3 && !empty($p['reference'])) {
                    $gtin = $p['reference'];
                    $gtin_exists = true;
                } elseif ($gtin_conf == 4 && !empty($p['mpn'])) {
                    $gtin = $p['mpn'];
                    $gtin_exists = true;
                } elseif ($gtin_conf == 0) {
                    $gtin_exists = false;
                }

                if ($gtin_exists) {
                    $xml_writer->writeElement('g:gtin', $gtin);
                }


                // Mpn
                $mpn_exists = false;

                if ($mpn_conf == 1 && !empty($p['supplier_reference'])) {
                    $mpn = $mpn_prefix.$p['supplier_reference'];
                    $mpn_exists = true;
                } elseif ($mpn_conf == 2 && !empty($p['reference'])) {
                    $mpn = $mpn_prefix.$p['reference'];
                    $mpn_exists = true;
                } elseif ($mpn_conf == 3) {
                    $mpn = $mpn_prefix.$p['id_product'];
                    $mpn_exists = true;
                } elseif ($mpn_conf == 'disabled') {
                    $mpn_exists = false;
                }

                if ($mpn_exists) {
                    $xml_writer->writeElement('g:mpn', $mpn);
                }

                // If there is no GTIN and MPN and BRAND create element identifier not exist
                if (!$gtin_exists && !$mpn_exists && !$brand_exists) {
                    $xml_writer->writeElement('g:identifier_exists', 'false');
                } else {
                    $xml_writer->writeElement('g:identifier_exists', 'true');
                }


                if ($products_attributes) {
                    $xml_writer->writeElement('g:item_group_id', $p['id_product']);
                }


                // Product type
                $categories_path = $cat_path_arr[$p['id_category_default']]['path'];
                $categories_path = strip_tags($categories_path);
                $xml_writer->writeElement('g:product_type', $categories_path);


                // Google category / taxonomy
                $path_google_taxonomy = $cat_path_arr[$p['id_category_default']]['path_google_taxonomy'];
                $path_google_taxonomy = strip_tags($path_google_taxonomy);
                $xml_writer->writeElement('g:google_product_category', $path_google_taxonomy);

                if ($include_shipping_cost_conf) {
                    foreach ($p['shipping'] as $key => $val) {
                        $xml_writer->startElement('g:shipping');
                        $xml_writer->writeElement('g:country', $key);
                        $xml_writer->writeElement('g:service', $val['name']);
                        $xml_writer->writeElement('g:price', $val['price'].' '.$currency->iso_code);
                        $xml_writer->endElement();
                    }
                }

                if ($p['attr_sizes'] != '') {
                    $xml_writer->startElement('g:size');
                    $xml_writer->text(htmlspecialchars($p['attr_sizes'], ENT_COMPAT, 'UTF-8'));
                    $xml_writer->endElement();
                }

                if ($p['attr_colors'] != '') {
                    $xml_writer->startElement('g:color');
                    $xml_writer->text(htmlspecialchars($p['attr_colors'], ENT_COMPAT, 'UTF-8'));
                    $xml_writer->endElement();
                }

                // features
                if (isset($p['features']) && count($p['features'])) {
                    $xml_writer->startElement('g:product_detail');
                    $xml_writer->writeElement('g:section_name', $this->l('Product details'));
                    foreach ($p['features'] as $f) {
                        $xml_writer->writeElement('g:attribute_name', $f['name']);
                        $xml_writer->writeElement('g:attribute_value', $f['value']);
                    }
                    $xml_writer->endElement();
                }

                // Shipping weight
                if ($p['weight'] != '0') {
                    $xml_writer->writeElement('g:shipping_weight', $p['weight']);
                }

                $xml_writer->endElement(); // Close item

                file_put_contents($generate_file_path.'_temp', $xml_writer->flush(true), FILE_APPEND);
            }
            unset($products);
        }

        $xml_writer->endElement(); // close channel
        $xml_writer->text('</rss>'); // Close rss

        if (file_put_contents($generate_file_path.'_temp', $xml_writer->flush(true), FILE_APPEND)) { // Flush rest of data

            // remove old file
            @unlink($generate_file_path);
            @chmod($generate_file_path.'_temp', 0777);
            // rename just generated _temp to destionation name
            rename($generate_file_path.'_temp', $generate_file_path);
            @chmod($generate_file_path, 0777);
        }

        return true;
    }




    public function assignInGoogleShoppingToProducts($id_shop, $categories_ids, $manufacturers_ids, $suppliers_ids, $action)
    {
        $products_to_assign = $this->getProductsByParams($id_shop, $categories_ids, $manufacturers_ids, $selected_suppliers = false);

        if (count($products_to_assign)) {
            foreach ($products_to_assign as $product) {
                $id_product = $product['id_product'];

                Db::getInstance()->execute('
                    UPDATE `'._DB_PREFIX_.'product`
                    SET `in_google_shopping` = '.(int)$action.'
                    WHERE `id_product` = '.(int)$id_product);

                Db::getInstance()->execute('
                    UPDATE `'._DB_PREFIX_.'product_shop`
                    SET `in_google_shopping` = '.(int)$action.'
                    WHERE `id_product` = '.(int)$id_product);
            }
            return true;
        } else {
            return false;
        }
    }


    public function getProductsByParams($id_shop, $selected_categories = false, $selected_manufacturers = false, $selected_suppliers = false)
    {
        if (!$id_shop) {
            $id_shop = (int)$this->context->shop->id;
        }

        $sql = 'SELECT DISTINCT p.`id_product`
                FROM `'._DB_PREFIX_.'product` p
                INNER JOIN `'._DB_PREFIX_.'product_shop` product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = p.`id_product` AND ps.`id_product_attribute` = 0)'.
                ((count($selected_categories) && $selected_categories != '') ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
                WHERE pl.`id_shop` = '.(int)$id_shop.
                ((count($selected_categories) && $selected_categories != '') ? ' AND c.`id_category` IN ('.implode(',', $selected_categories).')' : '').
                ((count($selected_manufacturers) && $selected_manufacturers != '')  ? ' AND p.`id_manufacturer` IN ('.implode(',', $selected_manufacturers).')' : '').
                ((count($selected_suppliers) && $selected_suppliers != '')  ? ' AND p.`id_supplier` IN ('.implode(',', $selected_suppliers).')' : '');

        //d(Db::getInstance()->executeS($sql));
        return Db::getInstance()->executeS($sql);
    }


    // Count results
    private function getProductsDBLightCount($obj)
    {
        $id_lang = $obj->id_lang;
        $id_shop = $obj->id_shop;

        $only_active = (int)$obj->only_active;
        $available_for_order = (int)$obj->available_for_order;
        $selected_categories = array_filter(explode(',', $obj->selected_categories), 'is_numeric');
        $exclude_manufacturers = array_filter(explode(',', $obj->exclude_manufacturers), 'is_numeric');
        $exclude_suppliers = array_filter(explode(',', $obj->exclude_suppliers), 'is_numeric');
        $exclude_products = array_filter(explode(',', $obj->exclude_products), 'is_numeric');

        $sql = 'SELECT COUNT(DISTINCT p.`id_product`)
                FROM `'._DB_PREFIX_.'product` p
                INNER JOIN `'._DB_PREFIX_.'product_shop` product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = p.`id_product` AND ps.`id_product_attribute` = 0)'.
                (count($selected_categories) ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
                WHERE pl.`id_lang` = '.(int)$id_lang.
                (count($selected_categories) ? ' AND c.`id_category` IN ('.implode(',', $selected_categories).')' : '').
                (count($exclude_manufacturers) ? ' AND p.`id_manufacturer` NOT IN ('.implode(',', $exclude_manufacturers).')' : '').
                (count($exclude_suppliers) ? ' AND p.`id_supplier` NOT IN ('.implode(',', $exclude_suppliers).')' : '').
                (count($exclude_products) ? ' AND p.`id_product`  NOT IN ('.implode(',', $exclude_products).')' : '').
                ($only_active ? ' AND product_shop.`active` = 1' : '').
                ($available_for_order ? ' AND product_shop.`available_for_order` = 1' : '').'
                AND p.`in_google_shopping` = 1
                AND product_shop.`available_for_order` = 1
                ORDER BY p.`id_product`';

        //d(Db::getInstance()->executeS($sql));

        return Db::getInstance()->getValue($sql);
    }


    /**
    * Ligh Function to get all available products for comparision engines
    * For saving memory we get only necesary data
    */

    private function getProductsDBLight($obj, $offset = 0)
    {
        $id_lang = $obj->id_lang;
        $id_shop = $obj->id_shop;

        $only_active = (int)$obj->only_active;
        $available_for_order = (int)$obj->available_for_order;
        $selected_categories = array_filter(explode(',', $obj->selected_categories), 'is_numeric');
        $exclude_manufacturers = array_filter(explode(',', $obj->exclude_manufacturers), 'is_numeric');
        $exclude_suppliers = array_filter(explode(',', $obj->exclude_suppliers), 'is_numeric');
        $exclude_products = array_filter(explode(',', $obj->exclude_products), 'is_numeric');

        $sql = 'SELECT DISTINCT p.`id_product`, p.`weight`, product_shop.`id_category_default`, p.`reference`, p.`mpn`, p.`ean13`, p.`upc`, product_shop.`id_tax_rules_group`,
                p.`supplier_reference`, product_shop.`condition`, product_shop.`available_date`, product_shop.`unit_price_ratio`, product_shop.`unity`, product_shop.`cache_default_attribute`,
                pl.`name`, pl.`product_name_google_shopping`, pl.`description_short`, pl.`description`, pl.`meta_description`, pl.`link_rewrite`,
                pl.`custom_label_0`, pl.`custom_label_1`, pl.`custom_label_2`, pl.`custom_label_3`, pl.`custom_label_4`, pl.`product_short_desc_google_shopping`,
                m.`name` AS manufacturer_name,
                s.`name` AS supplier_name,
                ps.`product_supplier_reference` AS supplier_reference,
                sav.`out_of_stock`
                FROM `'._DB_PREFIX_.'product` p
                INNER JOIN `'._DB_PREFIX_.'product_shop` product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = p.`id_product` AND ps.`id_product_attribute` = 0)'.
                (count($selected_categories) ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
                LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 AND sav.id_shop = '.(int)$id_shop.')
                WHERE pl.`id_lang` = '.(int)$id_lang.
                (count($selected_categories) ? ' AND c.`id_category` IN ('.implode(',', $selected_categories).')' : '').
                (count($exclude_manufacturers) ? ' AND p.`id_manufacturer` NOT IN ('.implode(',', $exclude_manufacturers).')' : '').
                (count($exclude_suppliers) ? ' AND p.`id_supplier` NOT IN ('.implode(',', $exclude_suppliers).')' : '').
                (count($exclude_products) ? ' AND p.`id_product`  NOT IN ('.implode(',', $exclude_products).')' : '').
                ($only_active ? ' AND product_shop.`active` = 1' : '').
                ($available_for_order ? ' AND product_shop.`available_for_order` = 1' : '').'
                AND p.`in_google_shopping` = 1
                AND product_shop.`available_for_order` = 1
                ORDER BY p.`id_product`
                LIMIT '.(int)$offset.','.(int)$this->limit;

        //d(Db::getInstance()->executeS($sql));

        return Db::getInstance()->executeS($sql);
    }


    private function getProducts($obj, $offset)
    {
        $return = array();

        $id_lang = $obj->id_lang;
        $id_shop = $obj->id_shop;
        $id_country = $obj->id_country;
        $id_carrier = $obj->id_carrier;
        $id_currency = $obj->id_currency;

        $only_available = $obj->only_available;

        $unit_pricing_measure = $obj->unit_pricing_measure;
        $min_product_price = $obj->min_product_price;
        $include_shipping_cost = $obj->include_shipping_cost;
        $products_attributes = $obj->products_attributes;
        $features_enabled = (int)$obj->features_enabled;

        $sizes_attribute_group = explode(',', $obj->sizes_attribute_group);
        $colors_attribute_group = (int)$obj->color_attribute_group;
        $ean_validiation = $obj->ean_validiation;


        $products = self::getProductsDBLight($obj, $offset);
        //p('PRODUCTS COUNT DB: '.count($products));

        // initiate context for shop we generating
        $context = Context::getContext()->cloneContext();

        if ($id_shop !== null && $context->shop->id != (int)$id_shop) {
            $context->shop = new Shop((int)$id_shop);
        }
        // END

        if (!Group::isFeatureActive()) {
            $id_group = Configuration::get('PS_CUSTOMER_GROUP');
        } else {
            $id_group = Configuration::get('PS_UNIDENTIFIED_GROUP');
        }

        foreach ($products as $k => $p) {
            if ($products_attributes) {
                $combinations = self::getAttributeCombinations($p['id_product'], $id_lang, $id_shop);
            } else {
                $combinations = array();
            }

            if (!$products_attributes || !count($combinations)) {
                $price = self::getPrice((int)$p['id_product'], (int)$p['cache_default_attribute'], $id_shop, $id_currency, $id_country, $id_group, false, true, $context);
                $price_sale = self::getPrice((int)$p['id_product'], (int)$p['cache_default_attribute'], $id_shop, $id_currency, $id_country, $id_group, true, true, $context);

                $quantity = self::getQuantity((int)$p['id_product'], $id_shop, null, null);
                $p['quantity'] = $quantity;
                // STOCK MANAGMENT AND OUT_OF_STOCK
                // Allow to order the product when out of stock?
                $product_out_of_stock = (int)$p['out_of_stock'];

                $sp = SpecificPrice::getByProductId((int)$p['id_product'], false, false);

                if ($sp) {
                    if ($sp[0]['from'] == '0000-00-00 00:00:00' && $sp[0]['to'] == '0000-00-00 00:00:00') {
                        $p['sale_price_effective_date'] = '';
                    } elseif ($sp[0]['from'] == '0000-00-00 00:00:00') {
                        $p['sale_price_effective_date'] = date("c", time()) .'/'.date("c", strtotime($sp[0]['to']));
                    } elseif ($sp[0]['to'] == '0000-00-00 00:00:00') {
                        $p['sale_price_effective_date'] = date("c", strtotime($sp[0]['from'])) .'/'.date("c", time());
                    } else {
                        $p['sale_price_effective_date'] = date("c", strtotime($sp[0]['from'])) .'/'.date("c", strtotime($sp[0]['to']));
                    }
                }
                // if use only avaiable and stock managment is on then skip that product if is not in stock
                if ($only_available && $this->stock_management) {
                    if ($quantity <= 0) {
                        continue;
                    }
                    // else if only avaliable is off and we use stock managment then we do normal prestashop logick regarding allow to buy or not
                } elseif ($only_available == false && $this->stock_management) {
                    if ($quantity > 0) {
                        $p['quantity'] = $quantity;
                    } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 0) {
                        continue;
                    } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 1) {
                        $p['quantity'] = 99;
                    } elseif ($quantity <= 0 && $this->order_out_of_stock == 0 && $product_out_of_stock == 2) {
                        continue;
                    } elseif ($quantity <= 0 && $this->order_out_of_stock == 1 && $product_out_of_stock == 2) {
                        $p['quantity'] = 99;
                    }
                } elseif ($this->stock_management == false) {
                    $p['quantity'] = 99;
                }
                // END STOCK MANAGMENT

                if (!self::validateEAN13($p['ean13'], $ean_validiation)) {
                    $p['ean13'] = '';
                }

                // unit price
                if ($p['unit_price_ratio'] != 0 && $p['unity'] != '' && $unit_pricing_measure) {
                    $unit_price_ratio = (float)Tools::ps_round((float)$p['unit_price_ratio'], 2);

                    if ($p['unity'] == 'm2' && $unit_price_ratio >= 1) {
                        $p['unit_pricing_measure'] = $unit_price_ratio.' sqm';
                        $p['unit_pricing_base_measure'] = '1 sqm';
                    } else {
                        $p['unit_pricing_measure'] = $unit_price_ratio.' '.$p['unity'];
                        $p['unit_pricing_base_measure'] = (int)$unit_price_ratio.' '.$p['unity'];
                    }
                }

                $p['images'] = $this->getProductImges($id_lang, (int)$p['id_product'], null, $p['link_rewrite']);
                $p['price'] = $price;
                $p['price_sale'] = $price_sale;

                if (isset($p['product_name_google_shopping']) && !empty($p['product_name_google_shopping'])) {
                    $p['product_name'] = $p['product_name_google_shopping'];
                } else {
                    $p['product_name'] = $p['name'];
                }

                //$p['product_name'] = self::my_mb_ucfirst($p['product_name']);


                if (isset($p['product_short_desc_google_shopping']) && !empty($p['product_short_desc_google_shopping'])) {
                    $p['product_description_short'] = $p['product_short_desc_google_shopping'];
                    $p['description'] = $p['product_short_desc_google_shopping'];
                    $p['description_short'] = $p['product_short_desc_google_shopping'];
                } else {
                    $p['product_description_short'] = $p['description_short'];
                }

                if ($include_shipping_cost) {
                    $p['shipping'] = $this->getShippingCostByParams($p['id_product'], $id_shop, $id_country, $id_carrier, $id_currency, $price);
                }

                if ($features_enabled) {
                    $p['features'] = self::getFrontFeaturesStatic($id_lang, $id_shop, (int)$p['id_product']);
                }


                $p['attr_sizes'] = '';
                $p['attr_colors'] = '';

                if (isset($sizes_attribute_group) && count($sizes_attribute_group) == 1) {
                    $sizes_string = $this->getAvailableAttributesValuesByAttributeGroupId($p, (int)$sizes_attribute_group[0], $id_lang, $id_shop, $only_available, '/', 0);
                    if ($sizes_string) {
                        $p['attr_sizes'] = $sizes_string;
                    } else {
                        $p['attr_sizes'] = '';
                    }
                } elseif (isset($sizes_attribute_group) && count($sizes_attribute_group) > 1) {
                    foreach ($sizes_attribute_group as $id_attribute_group) {
                        $sizes_string = $this->getAvailableAttributesValuesByAttributeGroupId($p, (int)$id_attribute_group, $id_lang, $id_shop, $only_available, '/', 0);
                        if ($sizes_string) {
                            $p['attr_sizes'] = $sizes_string;
                            break;
                        }
                    }

                    if (isset($p['attr_sizes']) == false) {
                        $p['attr_sizes'] = '';
                    }
                }

                if (isset($colors_attribute_group) && $colors_attribute_group != 0) {
                    $colors_string = $this->getAvailableAttributesValuesByAttributeGroupId($p, $colors_attribute_group, $id_lang, $id_shop, $only_available, '/', 0);
                    if ($colors_string) {
                        $p['attr_colors'] = $colors_string;
                    } else {
                        $p['attr_colors'] = '';
                    }
                }

                if (isset($min_product_price) && $min_product_price > 0) {
                    if ($price > $min_product_price) {
                        $return[] = $p;
                    }
                } else {
                    $return[] = $p;
                }
            } else {
                foreach ($combinations as $ca => $a) {
                    $price_attr = self::getPrice((int)$p['id_product'], (int)$a['id_product_attribute'], $id_shop, $id_currency, $id_country, $id_group, false, true, $context);
                    $price_attr_sale = self::getPrice((int)$p['id_product'], (int)$a['id_product_attribute'], $id_shop, $id_currency, $id_country, $id_group, true, true, $context);

                    $quantity = self::getQuantity((int)$p['id_product'], $id_shop, (int)$a['id_product_attribute'], null);

                    $sp = SpecificPrice::getByProductId((int)$p['id_product'], false, false);
                    if ($sp) {
                        if ($sp[0]['from'] == '0000-00-00 00:00:00' && $sp[0]['to'] == '0000-00-00 00:00:00') {
                            $p['sale_price_effective_date'] = '';
                        } elseif ($sp[0]['from'] == '0000-00-00 00:00:00') {
                            $p['sale_price_effective_date'] = date("c", time()) .'/'.date("c", strtotime($sp[0]['to']));
                        } elseif ($sp[0]['to'] == '0000-00-00 00:00:00') {
                            $p['sale_price_effective_date'] = date("c", strtotime($sp[0]['from'])) .'/'.date("c", time());
                        } else {
                            $p['sale_price_effective_date'] = date("c", strtotime($sp[0]['from'])) .'/'.date("c", strtotime($sp[0]['to']));
                        }
                    }
                    // STOCK MANAGMENT AND OUT_OF_STOCK
                    // Allow to order the product when out of stock?
                    $product_out_of_stock = (int)$p['out_of_stock'];

                    // if use only avaiable and stock managment is on then skip that product if is not in stock
                    $p['quantity'] = $quantity;
                    if ($only_available && $this->stock_management) {
                        if ($quantity <= 0) {
                            continue;
                        }

                        // else if only avaliable is off and we use stock managment then we do normal prestashop logick regarding allow to buy or not
                    } elseif ($only_available == false && $this->stock_management) {
                        if ($quantity > 0) {
                            $p['quantity'] = $quantity;
                        } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 0) {
                            continue;
                        } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 1) {
                            $p['quantity'] = 99;
                        } elseif ($quantity <= 0 && $this->order_out_of_stock == 0 && $product_out_of_stock == 2) {
                            continue;
                        } elseif ($quantity <= 0 && $this->order_out_of_stock == 1 && $product_out_of_stock == 2) {
                            $p['quantity'] = 99;
                        }
                    } elseif ($this->stock_management == false) {
                        $p['quantity'] = 99;
                    }
                    // END STOCK MANAGMENT


                    $p['images'] = $this->getProductImges($id_lang, (int)$p['id_product'], (int)$a['id_product_attribute'], $p['link_rewrite']);

                    // if images for attributes don't exist get main ones
                    if (!count($p['images'])) {
                        $p['images'] = $this->getProductImges($id_lang, (int)$p['id_product'], null, $p['link_rewrite']);
                    }

                    $p['price'] = $price_attr;
                    $p['price_sale'] = $price_attr_sale;
                    $p['id_product_attribute'] = $a['id_product_attribute'];


                    // unit price
                    if ($p['unit_price_ratio'] != 0 && $p['unity'] != '' && $unit_pricing_measure) {
                        $unit_price_ratio = (float)Tools::ps_round((float)$p['unit_price_ratio'], 2);
                        $p['unit_pricing_measure'] = $unit_price_ratio.' '.$p['unity'];
                    }


                    if (isset($p['product_name_google_shopping']) && !empty($p['product_name_google_shopping'])) {
                        $p['product_name'] = trim($p['product_name_google_shopping']).trim($a['attribute_name']);
                    } else {
                        $p['product_name'] = trim($p['name']).trim($a['attribute_name']);
                    }

                    //$p['product_name'] = self::my_mb_ucfirst($p['product_name']);


                    if (isset($p['product_short_desc_google_shopping']) && !empty($p['product_short_desc_google_shopping'])) {
                        $p['product_description_short'] = $p['product_short_desc_google_shopping'];
                        $p['description'] = $p['product_short_desc_google_shopping'];
                        $p['description_short'] = $p['product_short_desc_google_shopping'];
                    } else {
                        $p['product_description_short'] = $p['description_short'];
                    }


                    $p['reference'] = $a['reference'];
                    $p['supplier_reference'] = $a['supplier_reference'];
                    if ($include_shipping_cost) {
                        $p['shipping'] = $this->getShippingCostByParams($p['id_product'], $id_shop, $id_country, $id_carrier, $id_currency, $price_attr);
                    }

                    $p['ean13'] = $a['ean13'];

                    if (!self::validateEAN13($p['ean13'], $ean_validiation)) {
                        $p['ean13'] = '';
                    }

                    if ($features_enabled) {
                        $p['features'] = self::getFrontFeaturesStatic($id_lang, $id_shop, (int)$p['id_product']);
                    }

                    if ($products_attributes) {
                        $p['attr_colors'] = '';
                        $p['attr_sizes'] = '';

                        if (isset($sizes_attribute_group) && $sizes_attribute_group != 0) {
                            $sizes_string = $this->getAvailableAttributesValueByAttributeGroupIdAndIpa($p, $sizes_attribute_group, $id_lang, $id_shop, $only_available, '/', 0, (int)$a['id_product_attribute']);
                            if ($sizes_string) {
                                $p['attr_sizes'] = $sizes_string;
                            } else {
                                $p['attr_sizes'] = '';
                            }
                        }

                        if (isset($sizes_attribute_group) && count($sizes_attribute_group) == 1) {
                            $sizes_string = $this->getAvailableAttributesValueByAttributeGroupIdAndIpa($p, (int)$sizes_attribute_group[0], $id_lang, $id_shop, $only_available, '/', 0, (int)$a['id_product_attribute']);
                            if ($sizes_string) {
                                $p['attr_sizes'] = $sizes_string;
                            } else {
                                $p['attr_sizes'] = '';
                            }
                        } elseif (isset($sizes_attribute_group) && count($sizes_attribute_group) > 1) {
                            foreach ($sizes_attribute_group as $id_attribute_group) {
                                $sizes_string = $this->getAvailableAttributesValueByAttributeGroupIdAndIpa($p, (int)$id_attribute_group, $id_lang, $id_shop, $only_available, '/', 0, (int)$a['id_product_attribute']);
                                if ($sizes_string) {
                                    $p['attr_sizes'] = $sizes_string;
                                    break;
                                }
                            }

                            if (isset($p['attr_sizes']) == false) {
                                $p['attr_sizes'] = '';
                            }
                        }

                        if (isset($colors_attribute_group) && $colors_attribute_group != 0) {
                            $colors_string = $this->getAvailableAttributesValueByAttributeGroupIdAndIpa($p, $colors_attribute_group, $id_lang, $id_shop, $only_available, '/', 0, (int)$a['id_product_attribute']);
                            if ($colors_string) {
                                $p['attr_colors'] = $colors_string;
                            } else {
                                $p['attr_colors'] = '';
                            }
                        }

                        if (isset($p['attr_colors']) == false) {
                            $p['attr_colors'] = '';
                        }

                        if (isset($min_product_price) && $min_product_price > 0) {
                            if ($price_attr > $min_product_price) {
                                $return[] = $p;
                            }
                        } else {
                            $return[] = $p;
                        }
                    }
                }
            }
        }

        // p('PRODUCTS COUNT: '.count($return));
        ///d($return);
        //unset($products);
        return $return;
    }

    public static function validateEAN13($barcode, $ean_validiation = false)
    {
        if (!$ean_validiation) {
            return true;
        }
        if (!preg_match("/^[0-9]{13}$/", $barcode)) {
            return false;
        }

        $digits = str_split($barcode);
        $evenSum = 0;
        $oddSum = 0;

        for ($i = 0; $i < 12; $i++) {
            if (($i == 0) || (($i % 2) == 0)) {
                $evenSum += $digits[$i];
            } else {
                $oddSum += $digits[$i];
            }
        }

        $total = ($evenSum * 3) + $oddSum;
        $checkDigit = $total % 10;

        if ($checkDigit == $digits[12]) {
            return true;
        } else {
            return false;
        }
    }

    private function getAvailableAttributesValueByAttributeGroupIdAndIpa($product, $id_attribute_group, $id_lang, $id_shop, $only_available, $separator = ';', $return_type = 0, $id_product_attribute = 0)
    {
        $sql = 'SELECT  al.`name` AS attribute_name, pa.`id_product_attribute`
                FROM `'._DB_PREFIX_.'product_attribute` pa
                    INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop = '.(int)$id_shop.')
                    LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                    LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group` AND a.`id_attribute_group` = '.(int)$id_attribute_group.')
                    LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
                    LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = '.(int)$product['id_product'].')
                WHERE pa.`id_product` = '.(int)$product['id_product'].'

                AND  pa.`id_product_attribute` = '.(int)$id_product_attribute.'

                AND ag.`id_attribute_group` = '.(int)$id_attribute_group.'
                ORDER BY al.`name`';

        $results = Db::getInstance()->ExecuteS($sql);
        //p($results);

        if (!count($results)) {
            return false;
        }

        $has_quantity = false;
        $id_product = (int)$product['id_product'];

        $available_attribute_values = array();

        if ($return_type == 0) {
            $vals_string = '';
            foreach ($results as $k => $v) {
                $id_product_attribute = $v['id_product_attribute'];
                $available_attribute_values[] = self::my_mb_ucfirst(trim($v['attribute_name']));
            }
            // Remove duplicates
            $available_attribute_values = array_unique($available_attribute_values);
            // create output string from values
            $vals_string = implode($separator, $available_attribute_values);
            $available_attribute_values = false;
            $vals_string = rtrim($vals_string, $separator);
            //p($vals_string);
            return $vals_string;
        } else {
            $vals_array = array();
            foreach ($results as $k => $v) {
                $id_product_attribute = $v['id_product_attribute'];
                $available_attribute_values[] = self::my_mb_ucfirst(trim($v['attribute_name']));
            }
            return array_unique($available_attribute_values);
        }
    }



    private function getAvailableAttributesValuesByAttributeGroupId($product, $id_attribute_group, $id_lang, $id_shop, $only_available, $separator = ';', $return_type = 0)
    {
        $sql = 'SELECT  al.`name` AS attribute_name, pa.`id_product_attribute`
                FROM `'._DB_PREFIX_.'product_attribute` pa
                    INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop = '.(int)$id_shop.')
                    LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                    LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group` AND a.`id_attribute_group` = '.(int)$id_attribute_group.')
                    LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
                    LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = '.(int)$product['id_product'].')
                WHERE pa.`id_product` = '.(int)$product['id_product'].'
                AND ag.`id_attribute_group` = '.(int)$id_attribute_group.'
                ORDER BY al.`name`';

        $results = Db::getInstance()->ExecuteS($sql);
        //p($results);

        if (!count($results)) {
            return false;
        }

        $has_quantity = false;
        $id_product = (int)$product['id_product'];

        $available_attribute_values = array();

        if ($return_type == 0) {
            $vals_string = '';
            foreach ($results as $k => $v) {
                $id_product_attribute = $v['id_product_attribute'];
                $available_attribute_values[] = self::my_mb_ucfirst(trim($v['attribute_name']));
            }
            // Remove duplicates
            $available_attribute_values = array_unique($available_attribute_values);
            // create output string from values
            $vals_string = implode($separator, $available_attribute_values);
            $available_attribute_values = false;
            $vals_string = rtrim($vals_string, $separator);
            //p($vals_string);
            return $vals_string;
        } else {
            $vals_array = array();
            foreach ($results as $k => $v) {
                $id_product_attribute = $v['id_product_attribute'];
                $available_attribute_values[] = self::my_mb_ucfirst(trim($v['attribute_name']));
            }
            return array_unique($available_attribute_values);
        }
    }

    public static function getQuantity($id_product, $id_shop, $id_product_attribute = null, $cache_is_pack = null)
    {
        if ((int)$cache_is_pack || ($cache_is_pack === null && Pack::isPack((int)$id_product))) {
            if (!Pack::isInStock((int)$id_product)) {
                return 0;
            }
        }

        return (StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, $id_shop));
    }

    public static function getPrice($id_product, $id_product_attribute, $id_shop, $id_currency, $id_country, $id_group, $usereduc, $usetax, $context)
    {
        $id_state = 0;
        $zipcode = 0;
        $decimals = 2;
        $quantity = 1;
        $cart_quantity = 1;
        $only_reduc = false;
        $specific_price_output = null;
        $with_ecotax = true;
        $use_group_reduction = true;
        $use_customer_price = true;
        $id_cart = 0;
        $id_customer = 0;
        $id_cutomization = 0;

        if (version_compare(_PS_VERSION_, '1.6.1.0', '<=')) {
            return self::priceCalculation(
                $id_shop,
                $id_product,
                $id_product_attribute,
                $id_country,
                $id_state,
                $zipcode,
                $id_currency,
                $id_group,
                $quantity,
                $usetax,
                $decimals,
                $only_reduc,
                $usereduc,
                $with_ecotax,
                $specific_price_output,
                $use_group_reduction,
                $id_customer,
                $use_customer_price,
                $id_cart,
                $cart_quantity
            );
        } elseif (version_compare(_PS_VERSION_, '1.7.0.0', '<=')) {
            return self::priceCalculationNew(
                $id_shop,
                $id_product,
                $id_product_attribute,
                $id_country,
                $id_state,
                $zipcode,
                $id_currency,
                $id_group,
                $quantity,
                $usetax,
                $decimals,
                $only_reduc,
                $usereduc,
                $with_ecotax,
                $specific_price_output,
                $use_group_reduction,
                $id_customer,
                $use_customer_price,
                $id_cart,
                $cart_quantity,
                $context
            );
        } else {
            if (Module::isEnabled('groupinc')) {
                return Product::priceCalculation(
                    $id_shop,
                    $id_product,
                    $id_product_attribute,
                    $id_country,
                    $id_state,
                    $zipcode,
                    $id_currency,
                    $id_group,
                    $quantity,
                    $usetax,
                    $decimals,
                    $only_reduc,
                    $usereduc,
                    $with_ecotax,
                    $specific_price_output,
                    $use_group_reduction,
                    $id_customer,
                    $use_customer_price,
                    $id_cart,
                    $cart_quantity,
                    $id_cutomization
                );
            } else {
                return self::priceCalculationNew8(
                    $id_shop,
                    $id_product,
                    $id_product_attribute,
                    $id_country,
                    $id_state,
                    $zipcode,
                    $id_currency,
                    $id_group,
                    $quantity,
                    $usetax,
                    $decimals,
                    $only_reduc,
                    $usereduc,
                    $with_ecotax,
                    $specific_price_output,
                    $use_group_reduction,
                    $id_customer,
                    $use_customer_price,
                    $id_cart,
                    $cart_quantity,
                    $id_cutomization
                );
            }
        }
    }

    public static function convertPrice($price, $currency = null, $to_currency = true, Context $context = null)
    {
        static $default_currency = null;

        if ($default_currency === null) {
            $default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        }

        if ($currency === null) {
            $currency = $context->currency;
        } elseif (is_numeric($currency)) {
            $currency = new Currency($currency, null, $context->shop->id);
        }

        //p($currency->conversion_rate);


        $c_id = (is_array($currency) ? $currency['id_currency'] : $currency->id);
        $c_rate = (is_array($currency) ? $currency['conversion_rate'] : $currency->conversion_rate);

        if ($c_id != $default_currency) {
            if ($to_currency) {
                $price *= $c_rate;
            } else {
                $price /= $c_rate;
            }
        }

        return $price;
    }


    public static function priceCalculation(
        $id_shop,
        $id_product,
        $id_product_attribute,
        $id_country,
        $id_state,
        $zipcode,
        $id_currency,
        $id_group,
        $quantity,
        $use_tax,
        $decimals,
        $only_reduc,
        $use_reduc,
        $with_ecotax,
        &$specific_price,
        $use_group_reduction,
        $id_customer = 0,
        $use_customer_price = true,
        $id_cart = 0,
        $real_quantity = 0
    ) {
        static $address = null;
        static $context = null;

        if ($address === null) {
            $address = new Address();
        }

        if ($context == null) {
            $context = Context::getContext()->cloneContext();
        }

        if ($id_shop !== null && $context->shop->id != (int)$id_shop) {
            $context->shop = new Shop((int)$id_shop);
        }

        if (!$use_customer_price) {
            $id_customer = 0;
        }

        if ($id_product_attribute === null) {
            $id_product_attribute = Product::getDefaultAttribute($id_product);
        }

        $cache_id = $id_product.'-'.$id_shop.'-'.$id_currency.'-'.$id_country.'-'.$id_state.'-'.$zipcode.'-'.$id_group.
            '-'.$quantity.'-'.$id_product_attribute.'-'.($use_tax?'1':'0').'-'.$decimals.'-'.($only_reduc?'1':'0').
            '-'.($use_reduc?'1':'0').'-'.$with_ecotax.'-'.$id_customer.'-'.(int)$use_group_reduction.'-'.(int)$id_cart.'-'.(int)$real_quantity;

        // reference parameter is filled before any returns
        $specific_price = SpecificPrice::getSpecificPrice(
            (int)$id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $quantity,
            $id_product_attribute,
            $id_customer,
            $id_cart,
            $real_quantity
        );
        if (isset(self::$_prices[$cache_id])) {
            return self::$_prices[$cache_id];
        }

        // fetch price & attribute price
        $cache_id_2 = $id_product.'-'.$id_shop;
        if (!isset(self::$_pricesLevel2[$cache_id_2])) {
            $sql = new DbQuery();
            $sql->select('product_shop.`price`, product_shop.`ecotax`');
            $sql->from('product', 'p');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_product=p.id_product AND product_shop.id_shop = '.(int)$id_shop.')');
            $sql->where('p.`id_product` = '.(int)$id_product);
            if (Combination::isFeatureActive()) {
                $sql->select('product_attribute_shop.id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on');
                $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product` = p.`id_product`');
                $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop = '.(int)$id_shop.')');
            } else {
                $sql->select('0 as id_product_attribute');
            }

            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            foreach ($res as $row) {
                $array_tmp = array(
                    'price' => $row['price'],
                    'ecotax' => $row['ecotax'],
                    'attribute_price' => (isset($row['attribute_price']) ? $row['attribute_price'] : null)
                );
                self::$_pricesLevel2[$cache_id_2][(int)$row['id_product_attribute']] = $array_tmp;

                if (isset($row['default_on']) && $row['default_on'] == 1) {
                    self::$_pricesLevel2[$cache_id_2][0] = $array_tmp;
                }
            }
        }
        if (!isset(self::$_pricesLevel2[$cache_id_2][(int)$id_product_attribute])) {
            return;
        }

        $result = self::$_pricesLevel2[$cache_id_2][(int)$id_product_attribute];

        if (!$specific_price || $specific_price['price'] < 0) {
            $price = (float)$result['price'];
        } else {
            $price = (float)$specific_price['price'];
        }
        // convert only if the specific price is in the default currency (id_currency = 0)
        if (!$specific_price || !($specific_price['price'] >= 0 && $specific_price['id_currency'])) {
            $price = self::convertPrice($price, $id_currency, true, $context);
        }

        // Attribute price
        if (is_array($result) && (!$specific_price || !$specific_price['id_product_attribute'] || $specific_price['price'] < 0)) {
            $attribute_price = self::convertPrice($result['attribute_price'] !== null ? (float)$result['attribute_price'] : 0, $id_currency, true, $context);
            // If you want the default combination, please use NULL value instead
            if ($id_product_attribute !== false) {
                $price += $attribute_price;
            }
        }

        // Tax
        $address->id_country = $id_country;
        $address->id_state = $id_state;
        $address->postcode = $zipcode;

        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$id_product, $context));
        $product_tax_calculator = $tax_manager->getTaxCalculator();

        // Add Tax
        if ($use_tax) {
            $price = $product_tax_calculator->addTaxes($price);
        }

        // Reduction
        $specific_price_reduction = 0;
        if (($only_reduc || $use_reduc) && $specific_price) {
            if ($specific_price['reduction_type'] == 'amount') {
                $reduction_amount = $specific_price['reduction'];

                if (!$specific_price['id_currency']) {
                    $reduction_amount = self::convertPrice($reduction_amount, $id_currency, true, $context);
                }
                $specific_price_reduction = !$use_tax ? $product_tax_calculator->removeTaxes($reduction_amount) : $reduction_amount;
            } else {
                $specific_price_reduction = $price * $specific_price['reduction'];
            }
        }

        if ($use_reduc) {
            $price -= $specific_price_reduction;
        }



        // Group reduction
        if ($use_group_reduction) {
            $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
            if ($reduction_from_category !== false) {
                $group_reduction = $price * (float)$reduction_from_category;
            } else { // apply group reduction if there is no group reduction for this category
                $group_reduction = (($reduc = Group::getReductionByIdGroup($id_group)) != 0) ? ($price * $reduc / 100) : 0;
            }
        } else {
            $group_reduction = 0;
        }

        if ($only_reduc) {
            return Tools::ps_round($group_reduction + $specific_price_reduction, $decimals);
        }

        if ($use_reduc) {
            $price -= $group_reduction;
        }

        // Eco Tax
        if (($result['ecotax'] || isset($result['attribute_ecotax'])) && $with_ecotax) {
            $ecotax = $result['ecotax'];
            if (isset($result['attribute_ecotax']) && $result['attribute_ecotax'] > 0) {
                $ecotax = $result['attribute_ecotax'];
            }

            if ($id_currency) {
                $ecotax = self::convertPrice($ecotax, $id_currency, true, $context);
            }
            if ($use_tax) {
                // reinit the tax manager for ecotax handling
                $tax_manager = TaxManagerFactory::getManager(
                    $address,
                    (int)Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID')
                );
                $ecotax_tax_calculator = $tax_manager->getTaxCalculator();
                $price += $ecotax_tax_calculator->addTaxes($ecotax);
            } else {
                $price += $ecotax;
            }
        }
        $price = Tools::ps_round($price, $decimals);
        if ($price < 0) {
            $price = 0;
        }

        self::$_prices[$cache_id] = $price;
        return self::$_prices[$cache_id];
    }


    public static function priceCalculationNew(
        $id_shop,
        $id_product,
        $id_product_attribute,
        $id_country,
        $id_state,
        $zipcode,
        $id_currency,
        $id_group,
        $quantity,
        $use_tax,
        $decimals,
        $only_reduc,
        $use_reduc,
        $with_ecotax,
        &$specific_price,
        $use_group_reduction,
        $id_customer = 0,
        $use_customer_price = true,
        $id_cart = 0,
        $real_quantity = 0,
        $context = null,
        $i_customization = 0
    ) {
        static $address = null;
        static $context = null;

        if ($context == null) {
            $context = Context::getContext()->cloneContext();
        }


        $cur_cart = new Cart();

        if ($id_shop !== null && $context->shop->id != (int)$id_shop) {
            $context->shop = new Shop((int)$id_shop);
            $context->currency = new Currency($id_currency);
        }

        //d($context->shop);

        if (!$use_customer_price) {
            $id_customer = 0;
        }

        if ($id_product_attribute === null) {
            $id_product_attribute = Product::getDefaultAttribute($id_product);
        }

        $cache_id = (int)$id_product.'-'.(int)$id_shop.'-'.(int)$id_currency.'-'.(int)$id_country.'-'.$id_state.'-'.$zipcode.'-'.(int)$id_group.
            '-'.(int)$quantity.'-'.(int)$id_product_attribute.
            '-'.(int)$with_ecotax.'-'.(int)$id_customer.'-'.(int)$use_group_reduction.'-'.(int)$id_cart.'-'.(int)$real_quantity.
            '-'.($only_reduc?'1':'0').'-'.($use_reduc?'1':'0').'-'.($use_tax?'1':'0').'-'.(int)$decimals;

        // reference parameter is filled before any returns
        $specific_price = SpecificPrice::getSpecificPrice(
            (int)$id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $quantity,
            $id_product_attribute,
            $id_customer,
            $id_cart,
            $real_quantity
        );

        if (isset(self::$_prices[$cache_id])) {
            /* Affect reference before returning cache */
            if (isset($specific_price['price']) && $specific_price['price'] > 0) {
                $specific_price['price'] = self::$_prices[$cache_id];
            }
            return self::$_prices[$cache_id];
        }

        // fetch price & attribute price
        $cache_id_2 = $id_product.'-'.$id_shop;
        if (!isset(self::$_pricesLevel2[$cache_id_2])) {
            $sql = new DbQuery();
            $sql->select('product_shop.`price`, product_shop.`ecotax`');
            $sql->from('product', 'p');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_product=p.id_product AND product_shop.id_shop = '.(int)$id_shop.')');
            $sql->where('p.`id_product` = '.(int)$id_product);
            if (Combination::isFeatureActive()) {
                $sql->select('IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on');
                $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.id_product = p.id_product AND product_attribute_shop.id_shop = '.(int)$id_shop.')');
            } else {
                $sql->select('0 as id_product_attribute');
            }

            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            if (is_array($res) && count($res)) {
                foreach ($res as $row) {
                    $array_tmp = array(
                        'price' => $row['price'],
                        'ecotax' => $row['ecotax'],
                        'attribute_price' => (isset($row['attribute_price']) ? $row['attribute_price'] : null)
                    );
                    self::$_pricesLevel2[$cache_id_2][(int)$row['id_product_attribute']] = $array_tmp;

                    if (isset($row['default_on']) && $row['default_on'] == 1) {
                        self::$_pricesLevel2[$cache_id_2][0] = $array_tmp;
                    }
                }
            }
        }

        if (!isset(self::$_pricesLevel2[$cache_id_2][(int)$id_product_attribute])) {
            return;
        }

        $result = self::$_pricesLevel2[$cache_id_2][(int)$id_product_attribute];

        if (!$specific_price || $specific_price['price'] < 0) {
            $price = (float)$result['price'];
        } else {
            $price = (float)$specific_price['price'];
        }

        // convert only if the specific price is in the default currency (id_currency = 0)
        if (!$specific_price || !($specific_price['price'] >= 0 && $specific_price['id_currency'])) {
            $price = self::convertPrice($price, $id_currency, true, $context);
            if (isset($specific_price['price']) && $specific_price['price'] >= 0) {
                $specific_price['price'] = $price;
            }
        }

        if (is_array($result) && (!$specific_price || !$specific_price['id_product_attribute'] || $specific_price['price'] < 0)) {
            $attribute_price = self::convertPrice($result['attribute_price'] !== null ? (float)$result['attribute_price'] : 0, $id_currency, true, $context);
            // If you want the default combination, please use NULL value instead
            if ($id_product_attribute !== false) {
                $price += $attribute_price;
            }
        }

        // Tax
        $address = Address::initialize(null, true);
        $address->id_country = $context->country->id;
        $address->id_state = $id_state;
        $address->postcode = $zipcode;



        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$id_product, $context));
        $product_tax_calculator = $tax_manager->getTaxCalculator();


        if ($use_tax) {
            $price = $product_tax_calculator->addTaxes($price);
        }

        // Eco Tax
        if (($result['ecotax'] || isset($result['attribute_ecotax'])) && $with_ecotax) {
            $ecotax = $result['ecotax'];
            if (isset($result['attribute_ecotax']) && $result['attribute_ecotax'] > 0) {
                $ecotax = $result['attribute_ecotax'];
            }

            if ($id_currency) {
                $ecotax = self::convertPrice($ecotax, $id_currency, true, $context);
            }
            if ($use_tax) {
                // reinit the tax manager for ecotax handling
                $tax_manager = TaxManagerFactory::getManager(
                    $address,
                    (int)Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID')
                );
                $ecotax_tax_calculator = $tax_manager->getTaxCalculator();
                $price += $ecotax_tax_calculator->addTaxes($ecotax);
            } else {
                $price += $ecotax;
            }
        }

        // Reduction
        $specific_price_reduction = 0;
        if (($only_reduc || $use_reduc) && $specific_price) {
            if ($specific_price['reduction_type'] == 'amount') {
                $reduction_amount = $specific_price['reduction'];

                if (!$specific_price['id_currency']) {
                    $reduction_amount = self::convertPrice($reduction_amount, $id_currency, true, $context);
                }

                $specific_price_reduction = $reduction_amount;

                // Adjust taxes if required

                if (!$use_tax && $specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->removeTaxes($specific_price_reduction);
                }
                if ($use_tax && !$specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->addTaxes($specific_price_reduction);
                }
            } else {
                $specific_price_reduction = $price * $specific_price['reduction'];
            }
        }

        if ($use_reduc) {
            $price -= $specific_price_reduction;
        }

        // Group reduction
        if ($use_group_reduction) {
            $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
            if ($reduction_from_category !== false) {
                $group_reduction = $price * (float)$reduction_from_category;
            } else { // apply group reduction if there is no group reduction for this category
                $group_reduction = (($reduc = Group::getReductionByIdGroup($id_group)) != 0) ? ($price * $reduc / 100) : 0;
            }

            $price -= $group_reduction;
        }

        if ($only_reduc) {
            return Tools::ps_round($specific_price_reduction, $decimals);
        }

        //$price = 8.454999582;

        if ($id_product === 1) {
            // dump($price);
            //dump(Tools::ps_round($price, $decimals));
            //dump($specific_price_reduction);
        }
        $price = Tools::ps_round($price, $decimals);

        if ($price < 0) {
            $price = 0;
        }

        self::$_prices[$cache_id] = $price;
        return self::$_prices[$cache_id];
    }


    public static function priceCalculationNew8(
        $id_shop,
        $id_product,
        $id_product_attribute,
        $id_country,
        $id_state,
        $zipcode,
        $id_currency,
        $id_group,
        $quantity,
        $use_tax,
        $decimals,
        $only_reduc,
        $use_reduc,
        $with_ecotax,
        &$specific_price,
        $use_group_reduction,
        $id_customer = 0,
        $use_customer_price = true,
        $id_cart = 0,
        $real_quantity = 0,
        $id_customization = 0
    ) {
        static $address = null;
        static $context = null;

        if ($context == null) {
            $context = Context::getContext()->cloneContext();
        }

        if ($address === null) {
            if (is_object($context->cart) && $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
                $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                $address = new Address($id_address);
            } else {
                $address = new Address();
            }
        }

        if ($id_shop !== null && $context->shop->id != (int) $id_shop) {
            $context->shop = new Shop((int) $id_shop);
        }

        if (!$use_customer_price) {
            $id_customer = 0;
        }

        if ($id_product_attribute === null) {
            $id_product_attribute = Product::getDefaultAttribute($id_product);
        }

        $cache_id = (int) $id_product . '-' . (int) $id_shop . '-' . (int) $id_currency . '-' . (int) $id_country . '-' . $id_state . '-' . $zipcode . '-' . (int) $id_group .
            '-' . (int) $quantity . '-' . (int) $id_product_attribute . '-' . (int) $id_customization .
            '-' . (int) $with_ecotax . '-' . (int) $id_customer . '-' . (int) $use_group_reduction . '-' . (int) $id_cart . '-' . (int) $real_quantity .
            '-' . ($only_reduc ? '1' : '0') . '-' . ($use_reduc ? '1' : '0') . '-' . ($use_tax ? '1' : '0') . '-' . (int) $decimals;

        // reference parameter is filled before any returns
        $specific_price = SpecificPrice::getSpecificPrice(
            (int) $id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $quantity,
            $id_product_attribute,
            $id_customer,
            $id_cart,
            $real_quantity
        );

        if (isset(self::$_prices[$cache_id])) {
            return self::$_prices[$cache_id];
        }

        // fetch price & attribute price
        $cache_id_2 = $id_product . '-' . $id_shop;
        // We need to check the cache for this price AND attribute, if absent the whole product cache needs update
        // This can happen if the cache was filled before the combination was created for example
        if (!isset(self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute])) {
            $sql = new DbQuery();
            $sql->select('product_shop.`price`, product_shop.`ecotax`');
            $sql->from('product', 'p');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_product=p.id_product AND product_shop.id_shop = ' . (int) $id_shop . ')');
            $sql->where('p.`id_product` = ' . (int) $id_product);
            if (Combination::isFeatureActive()) {
                $sql->select('IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on, product_attribute_shop.`ecotax` AS attribute_ecotax');
                $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.id_product = p.id_product AND product_attribute_shop.id_shop = ' . (int) $id_shop . ')');
            } else {
                $sql->select('0 as id_product_attribute');
            }

            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            if (is_array($res) && count($res)) {
                foreach ($res as $row) {
                    $array_tmp = [
                        'price' => $row['price'],
                        'ecotax' => $row['ecotax'],
                        'attribute_price' => isset($row['attribute_price']) ? $row['attribute_price'] : null,
                        'attribute_ecotax' => isset($row['attribute_ecotax']) ? $row['attribute_ecotax'] : null,
                    ];
                    self::$_pricesLevel2[$cache_id_2][(int) $row['id_product_attribute']] = $array_tmp;

                    if (isset($row['default_on']) && $row['default_on'] == 1) {
                        self::$_pricesLevel2[$cache_id_2][0] = $array_tmp;
                    }
                }
            }
        }

        if (!isset(self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute])) {
            return null;
        }

        $result = self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute];

        if (!$specific_price || $specific_price['price'] < 0) {
            $price = (float) $result['price'];
        } else {
            $price = (float) $specific_price['price'];
        }
        // convert only if the specific price is in the default currency (id_currency = 0)
        if (
            !$specific_price ||
            !(
                $specific_price['price'] >= 0 &&
                $specific_price['id_currency'] &&
                $id_currency !== $specific_price['id_currency']
            )
        ) {
            $price = Tools::convertPrice($price, $id_currency);

            if (isset($specific_price['price']) && $specific_price['price'] >= 0) {
                $specific_price['price'] = $price;
            }
        }

        // Attribute price
        if (is_array($result) && (!$specific_price || !$specific_price['id_product_attribute'] || $specific_price['price'] < 0)) {
            $attribute_price = Tools::convertPrice($result['attribute_price'] !== null ? (float) $result['attribute_price'] : 0, $id_currency);
            // If you want the default combination, please use NULL value instead
            if ($id_product_attribute !== false) {
                $price += $attribute_price;
            }
        }

        // Customization price
        if ((int) $id_customization) {
            $price += Tools::convertPrice(Customization::getCustomizationPrice($id_customization), $id_currency);
        }

        // Tax
        $address->id_country = $id_country;
        $address->id_state = $id_state;
        $address->postcode = $zipcode;

        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int) $id_product, $context));
        $product_tax_calculator = $tax_manager->getTaxCalculator();

        // Add Tax
        if ($use_tax) {
            $price = $product_tax_calculator->addTaxes($price);
        }

        // Eco Tax
        if (($result['ecotax'] || isset($result['attribute_ecotax'])) && $with_ecotax) {
            $ecotax = $result['ecotax'];
            if (isset($result['attribute_ecotax']) && $result['attribute_ecotax'] > 0) {
                $ecotax = $result['attribute_ecotax'];
            }

            if ($id_currency) {
                $ecotax = Tools::convertPrice($ecotax, $id_currency);
            }
            if ($use_tax) {
                if (self::$psEcotaxTaxRulesGroupId === null) {
                    self::$psEcotaxTaxRulesGroupId = (int) Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID');
                }
                // reinit the tax manager for ecotax handling
                $tax_manager = TaxManagerFactory::getManager(
                    $address,
                    self::$psEcotaxTaxRulesGroupId
                );
                $ecotax_tax_calculator = $tax_manager->getTaxCalculator();
                $price += $ecotax_tax_calculator->addTaxes($ecotax);
            } else {
                $price += $ecotax;
            }
        }

        // Reduction
        $specific_price_reduction = 0;
        if (($only_reduc || $use_reduc) && $specific_price) {
            if ($specific_price['reduction_type'] == 'amount') {
                $reduction_amount = $specific_price['reduction'];

                if (!$specific_price['id_currency']) {
                    $reduction_amount = Tools::convertPrice($reduction_amount, $id_currency);
                }

                $specific_price_reduction = $reduction_amount;

                // Adjust taxes if required

                if (!$use_tax && $specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->removeTaxes($specific_price_reduction);
                }
                if ($use_tax && !$specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->addTaxes($specific_price_reduction);
                }
            } else {
                $specific_price_reduction = $price * $specific_price['reduction'];
            }
        }

        if ($use_reduc) {
            $price -= $specific_price_reduction;
        }


        // Group reduction
        if ($use_group_reduction) {
            $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
            if ($reduction_from_category !== false) {
                $group_reduction = $price * (float) $reduction_from_category;
            } else { // apply group reduction if there is no group reduction for this category
                $group_reduction = (($reduc = Group::getReductionByIdGroup($id_group)) != 0) ? ($price * $reduc / 100) : 0;
            }

            $price -= $group_reduction;
        }

        if ($only_reduc) {
            return Tools::ps_round($specific_price_reduction, $decimals);
        }

        $price = Tools::ps_round($price, $decimals);

        // if ($id_product === 1) {
        //     dump($decimals);
        //     dump($price);
        // }


        if ($price < 0) {
            $price = 0;
        }

        self::$_prices[$cache_id] = $price;

        return self::$_prices[$cache_id];
    }

    private static function getAttributeCombinations($id_product, $id_lang, $id_shop)
    {
        $sql = 'SELECT pa.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`public_name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`, ps.`product_supplier_reference` AS supplier_reference
                FROM `'._DB_PREFIX_.'product_attribute` pa
                    INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop = '.(int)$id_shop.')
                    LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                    LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                    LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
                    LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = '.(int)$id_product.')
                    LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = '.(int)$id_product.' AND ps.`id_product_attribute` = pa.`id_product_attribute`)
                WHERE pa.`id_product` = '.(int)$id_product.'
                ORDER BY pa.`id_product_attribute`';

        $results = Db::getInstance()->ExecuteS($sql);
        $return = array();

        foreach ($results as $k => $r) {
            if (!isset($return[$r['id_product_attribute']]['attribute_name'])) {
                $return[$r['id_product_attribute']]['attribute_name'] = '';
            }

            $return[$r['id_product_attribute']]['price'] = $r['price'];
            $return[$r['id_product_attribute']]['id_product_attribute'] = $r['id_product_attribute'];
            $return[$r['id_product_attribute']]['attribute_name'] .= ', '.self::my_mb_ucfirst($r['group_name']).' - '.self::my_mb_ucfirst($r['attribute_name']).'';
            $return[$r['id_product_attribute']]['quantity'] = $r['quantity'];
            $return[$r['id_product_attribute']]['reference'] = $r['reference'];
            $return[$r['id_product_attribute']]['supplier_reference'] = $r['supplier_reference'];
            $return[$r['id_product_attribute']]['ean13'] = $r['ean13'];
        }

        unset($results);
        return $return;
    }

    public function getProductImges($id_lang, $id_product, $id_product_attribute)
    {
        $id_shop = Context::getContext()->shop->id;
        $attributeFilter = ($id_product_attribute ? ' AND ai.`id_product_attribute` = ' . (int) $id_product_attribute : '');
        $shopFilter = ($id_shop ? ' AND ims.`id_shop` = ' . (int) $id_shop : '');
        $sql = 'SELECT *
            FROM `' . _DB_PREFIX_ . 'image` i
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image`)';

        if ($id_product_attribute) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` ai ON (i.`id_image` = ai.`id_image`)';
        }

        if ($id_shop) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` ims ON (i.`id_image` = ims.`id_image`)';
        }

        $sql .= ' WHERE i.`id_product` = ' . (int) $id_product . ' AND il.`id_lang` = ' . (int) $id_lang . $attributeFilter . $shopFilter . '
            ORDER BY i.`cover` DESC';

        return Db::getInstance()->executeS($sql);
    }


    private static function getFrontFeaturesStatic($id_lang, $id_shop, $id_product)
    {
        $features =  Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT name, value, pf.id_feature
            FROM '._DB_PREFIX_.'feature_product pf
            LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = '.(int)$id_lang.')
            LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = '.(int)$id_lang.')
            LEFT JOIN '._DB_PREFIX_.'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = '.(int)$id_lang.')
                '.Shop::addSqlAssociation('feature', 'f').'
            WHERE pf.id_product = '.(int)$id_product.'
            AND feature_shop.id_shop = '.(int)$id_shop.'
            ORDER BY f.position ASC'
        );

        if (count($features)) {
            return $features;
        } else {
            return array();
        }
    }


    private static function getImageLink($name, $ids, $id_shop, $rewrite_url, $type = null)
    {
        $not_default = false;
        $shop = new Shop($id_shop);

        // legacy mode or default image
        $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'-'.(int)$shop->id_theme.'.jpg')) ? '-'.$shop->id_theme : '');
        if ((Configuration::get('PS_LEGACY_IMAGES')
            && (file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg')))
            || ($not_default = strpos($ids, 'default') !== false)) {
            if ($rewrite_url == 1 && !$not_default) {
                // $uri_path = $ids.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
                $uri_path = __PS_BASE_URI__.$ids.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg';
            }
        } else {
            // if ids if of the form id_product-id_image, we want to extract the id_image part
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').'-'.(int)$shop->id_theme.'.jpg')) ? '-'.$shop->id_theme : '');
            if ($rewrite_url == 1) {
                // $uri_path = $id_image.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
                $uri_path = __PS_BASE_URI__.$id_image.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').$theme.'.jpg';
            }
        }

        if ($rewrite_url) {
            return self::getShopDomain($id_shop, true).$uri_path;
        } else {
            return self::getShopDomain($id_shop, true).$uri_path;
        }
    }

    private static function getProductLink($id_product, $id_lang, $id_shop, $ipa, $rewrite_url = false, $alias = null)
    {
        if ($rewrite_url) {
            $link = new Link();
            return htmlspecialchars($link->getProductLink($id_product, false, null, null, $id_lang, $id_shop, $ipa, false, false, true), ENT_COMPAT, 'UTF-8', false);
        } else {
            return self::getShopDomain($id_shop).'index.php?controller=product&id_product='.$id_product.'&id_lang='.$id_lang;
        }
    }

    public static function getShopDomain($id_shop, $only_domain = false)
    {
        $shop = new Shop($id_shop);
        $ssl_enable = Configuration::get('PS_SSL_ENABLED', null, null, $id_shop);

        if ($ssl_enable) {
            $domain = $shop->domain_ssl;
        } else {
            $domain = $shop->domain;
        }

        if ($only_domain) {
            return ($ssl_enable ? 'https://' : 'http://').$domain;
        } else {
            return ($ssl_enable ? 'https://' : 'http://').$domain.$shop->physical_uri.$shop->virtual_uri;
        }
    }

    public function splitWords($string, $nb_caracs, $separator)
    {
        return mb_strimwidth($string, 0, $nb_caracs, $separator);
    }

    public static function html2txt($str, $html_desc_cleaner = false)
    {
        if (!$html_desc_cleaner) {
            return $str;
        } else {
            $html = new Html2Text($str);
            return $html->getText();
        }
    }

    private static function my_mb_ucfirst($str)
    {
        $str = mb_strtolower($str, "UTF-8");
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc.mb_substr($str, 1);
    }

    public static function getCountriesByZoneId($id_zone, $id_shop)
    {
        $sql = ' SELECT DISTINCT c.`iso_code`, c.`id_zone`, c.`id_country`
                FROM `'._DB_PREFIX_.'country` c
                LEFT JOIN `'._DB_PREFIX_.'country_shop` country_shop ON (country_shop.id_country = c.id_country AND country_shop.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_country` = c.`id_country`)
                WHERE (c.`id_zone` = '.(int)$id_zone.' OR s.`id_zone` = '.(int)$id_zone.')
                AND c.`active` = 1
                ';

        return Db::getInstance()->executeS($sql);
    }

    public function getShippingCostByParams($id_product, $id_shop, $id_country, $id_carrier, $id_currency, $price)
    {
        $return = array();
        $id_zone = Country::getIdZone($id_country);
        $countries = self::getCountriesByZoneId($id_zone, $id_shop);
        foreach ($countries as $c) {
            $return[$c['iso_code']] = self::getShippingData($id_product, $id_shop, $c['id_country'], $c['id_zone'], $id_carrier, $id_currency, $price);
        }

        //p($return);
        return $return;
    }

    public static function getShippingData($id_product, $id_shop, $id_country, $id_zone, $id_carrier, $id_currency, $price)
    {
        $product = new Product($id_product);
        $carrier = new Carrier((int)$id_carrier);
        $currency = Currency::getCurrencyInstance($id_currency);
        $out = array();

        $out['name'] = trim($carrier->name);
        $out['price'] = self::getProductShippingCost($id_shop, $id_zone, $id_country, $product, $id_currency, $price, $id_carrier, true);

        unset($product);
        unset($carrier);

        //dump($out);
        // die();
        return $out;
    }

    public static function getIdTaxRulesGroupByIdCarrier($id_carrier, $id_shop)
    {
        $key = 'carrier_id_tax_rules_group_'.(int)$id_carrier.'_'.(int)$id_shop;
        if (!Cache::isStored($key)) {
            Cache::store(
                $key,
                Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                    SELECT `id_tax_rules_group`
                    FROM `'._DB_PREFIX_.'carrier_tax_rules_group_shop`
                    WHERE `id_carrier` = '.(int)$id_carrier.' AND id_shop='.(int)$id_shop)
            );
        }

        return Cache::retrieve($key);
    }


    public static function getTaxRateByIdCountry($id_tax_rules_group, $id_country)
    {
        $postcode = 0;
        $id_state = 0;

        $rows = Db::getInstance()->executeS('
                SELECT tr.*
                FROM `'._DB_PREFIX_.'tax_rule` tr
                JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (tr.`id_tax_rules_group` = trg.`id_tax_rules_group`)
                WHERE trg.`active` = 1
                AND tr.`id_country` = '.(int)$id_country.'
                AND tr.`id_tax_rules_group` = '.(int)$id_tax_rules_group.'
                AND tr.`id_state` IN (0, '.(int)$id_state.')
                AND (\''.pSQL($postcode).'\' BETWEEN tr.`zipcode_from` AND tr.`zipcode_to`
                    OR (tr.`zipcode_to` = 0 AND tr.`zipcode_from` IN(0, \''.pSQL($postcode).'\')))
                ORDER BY tr.`zipcode_from` DESC, tr.`zipcode_to` DESC, tr.`id_state` DESC, tr.`id_country` DESC');

        $first_row = true;
        $taxes = array();

        foreach ($rows as $row) {
            $tax = new Tax((int)$row['id_tax']);
            $taxes[] = $tax;

            // the applied behavior correspond to the most specific rules
            if ($first_row) {
                $first_row = false;
            }

            if ($row['behavior'] == 0) {
                break;
            }
        }

        unset($rows);

        if (isset($row) && count($row)) {
            $tax = new Tax($row['id_tax']);
            return $tax->rate;
        } else {
            return 0;
        }
    }


    public static function getProductShippingCost($id_shop, $id_zone, $id_country, $product, $id_currency, $price, $id_carrier = null, $use_tax = true)
    {
        $carrier = new Carrier((int)$id_carrier);

        $order_total = $price;
        $orderTotalwithDiscounts = $price;
        $total_package_without_shipping_tax_inc = $price;

        // Start with shipping cost at 0
        $shipping_cost = 0;

        if (!Validate::isLoadedObject($carrier)) {
            die(Tools::displayError('Fatal error: "no carrier"'));
        }
        if (!$carrier->active) {
            return $shipping_cost;
        }

        // Free fees if free carrier
        if ($carrier->is_free == 1) {
            return 0;
        }

        // Select carrier tax
        if ($use_tax && !Tax::excludeTaxeOption()) {
            $id_tax_rules_group = self::getIdTaxRulesGroupByIdCarrier($id_carrier, $id_shop);
            $carrier_tax = self::getTaxRateByIdCountry($id_tax_rules_group, $id_country);
        }

        $configuration = Configuration::getMultiple(array(
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HANDLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT'
        ), null, null, $id_shop);

        // Free fees
        $free_fees_price = 0;
        if (isset($configuration['PS_SHIPPING_FREE_PRICE'])) {
            $free_fees_price = Tools::convertPrice((float)$configuration['PS_SHIPPING_FREE_PRICE'], Currency::getCurrencyInstance($id_currency));
        }

        if ($orderTotalwithDiscounts >= (float)($free_fees_price) && (float)($free_fees_price) > 0) {
            return $shipping_cost;
        }

        if (isset($configuration['PS_SHIPPING_FREE_WEIGHT']) && $product->weight >= (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] && (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] > 0) {
            return $shipping_cost;
        }

        // Get shipping cost using correct method
        if ($carrier->range_behavior) {
            if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT && !Carrier::checkDeliveryPriceByWeight($carrier->id, $product->weight, $id_zone))
            || (
                $carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE && !Carrier::checkDeliveryPriceByPrice($carrier->id, $total_package_without_shipping_tax_inc, $id_zone, $id_currency)
            )) {
                $shipping_cost += 0;
            } else {
                if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping_cost += $carrier->getDeliveryPriceByWeight($product->weight, $id_zone);
                } else { // by price
                    $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, $id_currency);
                }
            }
        } else {
            if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) {
                $shipping_cost += $carrier->getDeliveryPriceByWeight($product->weight, $id_zone);
            } else {
                $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, $id_currency);
            }
        }
        // Adding handling charges
        if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier->shipping_handling) {
            $shipping_cost += (float)$configuration['PS_SHIPPING_HANDLING'];
        }

        // Additional Shipping Cost per product
        if (!$product->is_virtual) {
            $shipping_cost += $product->additional_shipping_cost;
        }

        $shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance($id_currency));

        // Apply tax
        if ($use_tax && isset($carrier_tax)) {
            $shipping_cost *= 1 + ($carrier_tax / 100);
        }

        $shipping_cost = (float)Tools::ps_round((float)$shipping_cost, 2);
        unset($carrier);
        return $shipping_cost;
    }


    public function checkConfigExist($id_feed)
    {
        $exist = false;
        $exist = Db::getInstance()->executeS('SELECT *
            FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro`
            WHERE `id_pdgooglemerchantcenterpro` = '.(int)$id_feed);

        if (count($exist)) {
            $exist = true;
        }

        return $exist;
    }

    public function hookActionGetProductPropertiesAfterUnitPrice($params)
    {
        self::$_taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);

        if (isset($params['product']['quantity_wanted'])) {
            $quantity = max((int) $params['product']['minimal_quantity'], (int) $params['product']['quantity_wanted']);
        } elseif (isset($params['product']['cart_quantity'])) {
            $quantity = max((int) $params['product']['minimal_quantity'], (int) $params['product']['cart_quantity']);
        } else {
            $quantity = (int) $params['product']['minimal_quantity'];
        }

        if (self::$_taxCalculationMethod == PS_TAX_EXC) {
            $params['product']['price_tax_exc'] = Tools::ps_round($priceTaxExcluded, 6);
            $params['product']['price'] = $priceTaxIncluded = Product::getPriceStatic(
                (int) $params['product']['id_product'],
                true,
                $params['product']['id_product_attribute'],
                2,
                null,
                false,
                true,
                $quantity
            );
            $params['product']['price_without_reduction'] =
                $params['product']['price_without_reduction_without_tax'] = Product::getPriceStatic(
                    (int) $params['product']['id_product'],
                    false,
                    $params['product']['id_product_attribute'],
                    2,
                    null,
                    false,
                    false,
                    $quantity
                );
        } else {
            $priceTaxIncluded = Product::getPriceStatic(
                (int) $params['product']['id_product'],
                true,
                $params['product']['id_product_attribute'],
                2, // HERE IS PROBLEM IF I ADD 2 it solve all issues
                null,
                false,
                true,
                $quantity
            );
            $params['product']['price'] = Tools::ps_round($priceTaxIncluded, 6);

            $params['product']['price_without_reduction'] = Product::getPriceStatic(
                (int) $params['product']['id_product'],
                true,
                $params['product']['id_product_attribute'],
                6,
                null,
                false,
                false,
                $quantity
            );
            $params['product']['price_without_reduction_without_tax'] = Product::getPriceStatic(
                (int) $params['product']['id_product'],
                false,
                $params['product']['id_product_attribute'],
                6,
                null,
                false,
                false,
                $quantity
            );
        }
    }

    /*
    ** Hook update carrier when we change somthing in carrier id is changing to
    ** And reflect that change in db
    */

    public function hookactionCarrierUpdate($params)
    {
        //p($params);
        $old_id_carrier = $params['id_carrier'];
        $new_id_carrier = $params['carrier']->id;

        Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'pdgooglemerchantcenterpro`
            SET `id_carrier` = '.(int)$new_id_carrier.'
            WHERE `id_carrier` = '.(int)$old_id_carrier);
    }

    public function updateMapGoogleCategories2ShopCategories($catsmappingarr, $taxonomy_lang)
    {
        Db::getInstance()->Execute('
            DELETE FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_category`
            WHERE lang = "'.$taxonomy_lang.'"
        ');

        foreach ($catsmappingarr as $shop_category => $taxonomy_category) {
            if ($taxonomy_category && !empty($taxonomy_category)) {
                Db::getInstance()->Execute('
                    INSERT INTO `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_category`
                    VALUES (\''.(int)$shop_category.'\', \''.pSQL($taxonomy_category).'\', \''.pSQL($taxonomy_lang).'\')');
            }
        }
        return true;
    }

    public function getGoogleTaxonomyCategory($id_category, $taxonomy_lang)
    {
        return Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_category`
            WHERE `id_category` = '.(int)($id_category).'
            AND `lang` = \''.pSQL($taxonomy_lang).'\'
        ');
    }

    public function getGoogleTaxonomyCategoryValue($id_category, $taxonomy_lang)
    {
        return Db::getInstance()->getRow('
            SELECT txt_taxonomy
            FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_category`
            WHERE `id_category` = '.(int)($id_category).'
            AND `lang` = \''.pSQL($taxonomy_lang).'\'
        ');
    }



    public function getSelectTaxonomiesOptions()
    {
        return Db::getInstance()->ExecuteS('
            SELECT `id_pdgooglemerchantcenterpro_taxonomy`, taxonomy_lang
            FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy`
        ');
    }



    public function downloadTaxonomyFile($url)
    {
        $content = false;

        // slovakia changes for download taxonomy from en-US
        $url = str_replace('sk-SK', 'en-US', $url);

        // Try with file_get_contents
        if (ini_get('allow_url_fopen')) {
            $content = Tools::file_get_contents($url);
        }

        // If returns false > try with CURL if available
        if ($content === false && function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_VERBOSE => true
            ));

            $content = @curl_exec($ch);
            curl_close($ch);
        }

        return $content;
    }

    public function importTaxonomyData($taxonomy_lang)
    {
        $return = false;
        // Build URL to fetch from Google
        $url = self::GOOGLE_TAXONOMY_DATA_URL.'taxonomy.'.$taxonomy_lang.'.txt';

        // Get and check content is here
        $content = $this->downloadTaxonomyFile($url);

        if (!$content || Tools::strlen($content) == 0) {
            die('0');
        }

        // Convert to array and check all is still OK
        $lines = explode("\n", trim($content));
        if (!$lines || !is_array($lines)) {
            die('0');
        }

        Db::getInstance()->Execute('
            DELETE FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_data`
            WHERE `lang` = \''.pSQL($taxonomy_lang).'\'
        ');

        foreach ($lines as $index => $line) {
            // First skip is a version number
            if ($index > 0) {
                $return = Db::getInstance()->Execute('
                    INSERT INTO `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_data` (`value`, `lang`)
                    VALUES (\''.pSQL($line).'\', \''.pSQL($taxonomy_lang).'\'
                )');
            }
        }
        return $return;
    }

    public static function echoMemoryUsage()
    {
        $mem_usage = memory_get_usage(true);

        if ($mem_usage < 1024) {
            echo $mem_usage.' bytes';
        } elseif ($mem_usage < 1048576) {
            echo round($mem_usage / 1024, 2).' kilobytes';
        } else {
            echo round($mem_usage / 1048576, 2).' megabytes';
        }
    }

    // Wordpress function to remove acents
    private static function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }

    public function hookactionProductAdd($params)
    {
        if (Configuration::get($this->prefix.'ASSIGN_ON_ADD') && $id_product = $params['id_product']) {
            $product = new Product((int)$id_product);
            $product->in_google_shopping = 1;
            $product->update();
        }
    }


    /**
    * Hooks used in module
    */

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJquery();
        $this->context->controller->addCSS(($this->_path).'views/css/admin.css');
        $this->context->controller->addJS(($this->_path).'views/js/init.js');

        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $id_product = (int)Tools::getValue('id_product');
            $product = new Product($id_product);

            Media::addJsDef(array(
                'product_description_short' => $product->description_short,
                'product_meta_dcescription' => $product->meta_description,
                'product_name' => $product->name,
                'product_meta_title' => $product->meta_title,
            ));
        }
    }



    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = Tools::getValue('id_product') ? (int)Tools::getValue('id_product') : (int)$params['id_product'];
        $id_shop = (int)$this->context->shop->id;

        if (Validate::isLoadedObject($product = new Product((int)$id_product))) {
            $product = new Product($id_product);
            $this->context->smarty->assign(array(
                'product' => $product,
                'languages' => $this->context->controller->getLanguages(),
                'default_form_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
            ));

            if ($this->ps_ver_15) {
                return $this->display(__FILE__, 'extraproducttab_15.tpl');
            } elseif ($this->ps_ver_16) {
                return $this->display(__FILE__, 'extraproducttab_16.tpl');
            } elseif ($this->ps_ver_17 && !$this->ps_ver_1770_gt) {
                return $this->display(__FILE__, 'extraproducttab_17.tpl');
            } elseif ($this->ps_ver_17 && $this->ps_ver_1770_gt) {
                return $this->display(__FILE__, 'extraproducttab_1770.tpl');
            } elseif ($this->ps_ver_8) {
                return $this->display(__FILE__, 'extraproducttab_1770.tpl');
            }
        } else {
            return $this->displayError($this->l('You must save this product before save settings'));
        }
    }

    public function hookActionAdminProductsListingFieldsModifier($params)
    {
        if (!$this->ps_ver_17) {
            if (isset($params['fields'])) {
                $params['fields'] = array_merge($params['fields'], array(
                    'in_google_shopping' => array(
                        'title' => $this->l('Google shopping'),
                        'width' => 'auto',
                        'align' => 'text-center',
                        'type' => 'bool',
                        'filter' => false,
                        'orderby' => false,
                        'filter_key' => 'sa!in_google_shopping',
                        'active' => 'in_google_shopping'
                    )
                ));
            }
        } else {
            $params['sql_select']['in_google_shopping'] = [
                'table' => 'sa',
                'field' => 'in_google_shopping',
                'filtering' => '= %s'
            ];

            $in_google_shopping_filter = Tools::getValue('filter_column_in_google_shopping', false);
            if ($in_google_shopping_filter && $in_google_shopping_filter !=  '') {
                $params['sql_where'][] .= " sa.in_google_shopping = ".$in_google_shopping_filter;
            }
        }
    }
}
