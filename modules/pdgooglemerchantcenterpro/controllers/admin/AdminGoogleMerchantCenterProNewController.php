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

require_once dirname(__FILE__).'/../../models/GoogleMerchantCenterProModel.php';

class AdminGoogleMerchantCenterProNewController extends ModuleAdminController
{
    public $module = null;
    public $module_name = 'pdgooglemerchantcenterpro';

    public function __construct()
    {
        $this->table = 'pdgooglemerchantcenterpro';
        $this->className = 'GoogleMerchantCenterProModel';
        $this->lang = false;
        $this->bootstrap = true;

        if (Module::isInstalled($this->module_name)) {
            $this->module = Module::getInstanceByName($this->module_name);
        }

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = false;

        $this->ps_ver_8 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '8.0', '>=')) ? true : false;
        $this->ps_ver_17 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '=')) ? true : false;
        $this->ps_ver_16 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')) ? true : false;
        $this->ps_ver_15 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.5', '=')) ? true : false;
        $this->ps_ver_1753_gt = (version_compare(Tools::substr(_PS_VERSION_, 0, 7), '1.7.5.2', '>')) ? true : false;

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;

        parent::__construct();


        if (($this->ps_ver_17 && $this->ps_ver_1753_gt) || $this->ps_ver_8) {
            $this->_select = 's.name shop_name, l.name lang_name, cl.name as currency_name, ca.name as carrier_name, taxonomy_lang';
            $this->_join = 'LEFT JOIN '._DB_PREFIX_.'shop s ON (s.id_shop = a.id_shop)
                        LEFT JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = a.id_lang)
                        LEFT JOIN '._DB_PREFIX_.'currency c ON (c.id_currency = a.id_currency)
                        LEFT JOIN '._DB_PREFIX_.'currency_lang cl ON (cl.id_currency = a.id_currency AND cl.id_lang = '.(int)$this->context->language->id.')
                        LEFT JOIN '._DB_PREFIX_.'carrier ca ON (ca.`id_carrier` = a.`id_carrier`)
                        LEFT JOIN '._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy pdgmcpt ON (pdgmcpt.`id_pdgooglemerchantcenterpro_taxonomy` = a.`id_pdgooglemerchantcenterpro_taxonomy`)';
        } else {
            $this->_select = 's.name shop_name, l.name lang_name, c.name as currency_name, ca.name as carrier_name, taxonomy_lang';
            $this->_join = 'LEFT JOIN '._DB_PREFIX_.'shop s ON (s.id_shop = a.id_shop)
                        LEFT JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = a.id_lang)
                        LEFT JOIN '._DB_PREFIX_.'currency c ON (c.id_currency = a.id_currency)
                        LEFT JOIN '._DB_PREFIX_.'carrier ca ON (ca.`id_carrier` = a.`id_carrier`)
                        LEFT JOIN '._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy pdgmcpt ON (pdgmcpt.`id_pdgooglemerchantcenterpro_taxonomy` = a.`id_pdgooglemerchantcenterpro_taxonomy`)';
        }

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            ),
            'enableSelection' => array('text' => $this->l('Enable selection')),
            'disableSelection' => array('text' => $this->l('Disable selection'))
        );

        $this->fields_list = array(
            'id_pdgooglemerchantcenterpro' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'filter' => false,
                'width' => 25
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'filter' => false,
                'orderby' => false,
                'width' => 25
            ),
            'lang_name' => array(
                'title' => $this->l('Language'),
                'width' => 80,
                'filter_key' => 'l!name'
            ),
            'currency_name' => array(
                'title' => $this->l('Currency'),
                'width' => 80,
                'filter_key' => 'c!name'
            ),
            'shop_name' => array(
                'title' => $this->l('Shop'),
                'width' => 80,
                'filter_key' => 's!name'
            ),
            'taxonomy_lang' => array(
                'title' => $this->l('Taxonomy ISO'),
                'width' => 80,
                'filter_key' => 'pdgmcp!taxonomy_lang'
            ),
            'carrier_name' => array(
                'title' => $this->l('Carrier'),
                'width' => 80,
                'filter_key' => 'c!name'
            ),
            'generating' => array(
                'title' => $this->l('Generate'),
                'align' => 'text-center',
                'callback' => 'printGenerateIcon',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true
            ),
            'only_available' => array(
                'title' => $this->l('Only available'),
                'width' => 'auto',
                'type' => 'bool',
                'active' => 'only_available',
                'filter' => false,
            ),
            'only_active' => array(
                'title' => $this->l('Only active'),
                'width' => 'auto',
                'type' => 'bool',
                'active' => 'only_active',
                'filter' => false,
            ),

            'image_limit' => array(
                'title' => $this->l('Image limit'),
                'width' => 80,
                'filter_key' => 'a!image_limit'
            ),
            // 'rewrite_url' => array(
            //     'title' => $this->l('Url rewriting'),
            //     'width' => 'auto',
            //     'type' => 'bool',
            //     'active' => 'rewrite_url',
            //     'filter' => false,
            // ),
            'selected_categories' => array(
                'title' => $this->l('Sel. categories'),
                'width' => 'auto',
                'filter' => false,
            ),
            'products_attributes' => array(
                'title' => $this->l('Combinations'),
                'width' => 'auto',
                'type' => 'bool',
                'filter' => false,
                'active' => 'products_attributes',
            ),
            'date_add' => array(
                'title' => $this->l('Date add'),
                'align' => 'right',
                'width' => 'auto',
                'filter' => false,
                'type' => 'datetime',
            ),
            'date_upd' => array(
                'title' => $this->l('Date updated'),
                'align' => 'right',
                'width' => 'auto',
                'filter' => false,
                'type' => 'datetime'
            ),
            'date_gen' => array(
                'title' => $this->l('Last generated'),
                'align' => 'right',
                'width' => 'auto',
                'filter' => false,
                'type' => 'datetime'
            )

        );
    }


    public function printGenerateIcon($id_pdgooglemerchantcenterpro, $tr)
    {
        $id = $tr['id_pdgooglemerchantcenterpro'];
        $link = $this->context->link->getAdminLink('AdminGoogleMerchantCenterProNew').'&id_pdgooglemerchantcenterpro='.$id.'&generate_filepdgooglemerchantcenterpro';
        
        if ($this->ps_ver_16 || $this->ps_ver_17 || $this->ps_ver_8) {
            $button = '<a class="btn btn-default" href="'.$link.'"><i class="icon-refresh"></i></a>';
        } else {
            $button = '<a href="'.$link.'"><img src="../img/admin/manufacturers.gif" /></a>';
        }
        
        return $button;
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        // count categories selected
        $nb_items = count($this->_list);
        for ($i = 0; $i < $nb_items; ++$i) {
            $item = &$this->_list[$i];

            if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `selected_categories` FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro` WHERE `id_pdgooglemerchantcenterpro` = '.(int)$item['id_pdgooglemerchantcenterpro'].'') == '') {
                $item['selected_categories'] = '0 '.$this->l('categories');
            } else {
                $query = new DbQuery();
                $query->select('SUM(CHAR_LENGTH(selected_categories) - CHAR_LENGTH(REPLACE(selected_categories, ",", "")) + 1) as count_selected_categories');
                $query->from('pdgooglemerchantcenterpro', 'a');
                $query->where('a.id_pdgooglemerchantcenterpro ='.(int)$item['id_pdgooglemerchantcenterpro']);
                $query->orderBy('count_selected_categories DESC');
                $number_categories = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                //$item['selected_categories'] = $number_categories == 1 ?  $number_categories.' '.$this->l('category') : $number_categories.' '.$this->l('categories');
                $item['selected_categories'] = $number_categories;
                unset($query);
            }
        }
        // count manufacturers excluded
        for ($i = 0; $i < $nb_items; ++$i) {
            $item = &$this->_list[$i];

            if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `exclude_manufacturers` FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro` WHERE `id_pdgooglemerchantcenterpro` = '.(int)$item['id_pdgooglemerchantcenterpro'].'') == '') {
                $item['exclude_manufacturers'] = '0 '.$this->l('manufacturers');
            } else {
                $query = new DbQuery();
                $query->select('SUM(CHAR_LENGTH(exclude_manufacturers) - CHAR_LENGTH(REPLACE(exclude_manufacturers, ",", "")) + 1) as count_exclude_manufacturers');
                $query->from('pdgooglemerchantcenterpro', 'a');
                $query->where('a.id_pdgooglemerchantcenterpro ='.(int)$item['id_pdgooglemerchantcenterpro']);
                $query->orderBy('count_exclude_manufacturers DESC');
                $number_manufacturers = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                $item['exclude_manufacturers'] = $number_manufacturers == 1 ?  $number_manufacturers.' '.$this->l('manufacturer') : $number_manufacturers.' '.$this->l('manufacturers');
                unset($query);
            }
        }
        // count supliers excluded
        for ($i = 0; $i < $nb_items; ++$i) {
            $item = &$this->_list[$i];

            if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `exclude_suppliers` FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro` WHERE `id_pdgooglemerchantcenterpro` = '.(int)$item['id_pdgooglemerchantcenterpro'].'') == '') {
                $item['exclude_suppliers'] = '0 '.$this->l('suppliers');
            } else {
                $query = new DbQuery();
                $query->select('SUM(CHAR_LENGTH(exclude_suppliers) - CHAR_LENGTH(REPLACE(exclude_suppliers, ",", "")) + 1) as count_exclude_suppliers');
                $query->from('pdgooglemerchantcenterpro', 'a');
                $query->where('a.id_pdgooglemerchantcenterpro ='.(int)$item['id_pdgooglemerchantcenterpro']);
                $query->orderBy('count_exclude_suppliers DESC');
                $number_suppliers = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                $item['exclude_suppliers'] = $number_suppliers == 1 ?  $number_suppliers.' '.$this->l('supplier') : $number_suppliers.' '.$this->l('suppliers');
                unset($query);
            }
        }

        // count products excluded
        for ($i = 0; $i < $nb_items; ++$i) {
            $item = &$this->_list[$i];

            if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `exclude_products` FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro` WHERE `id_pdgooglemerchantcenterpro` = '.(int)$item['id_pdgooglemerchantcenterpro'].'') == '') {
                $item['exclude_products'] = '0 '.$this->l('products');
            } else {
                $query = new DbQuery();
                $query->select('LENGTH(TRIM(BOTH "," FROM exclude_products)) - LENGTH(REPLACE(TRIM(BOTH "," FROM exclude_products), ",", "")) + 1 as count_exclude_products');
                $query->from('pdgooglemerchantcenterpro', 'a');
                $query->where('a.id_pdgooglemerchantcenterpro ='.(int)$item['id_pdgooglemerchantcenterpro']);
                $query->orderBy('count_exclude_products DESC');
                $number_products = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                $item['exclude_products'] = $number_products == 1 ?  $number_products.' '.$this->l('product') : $number_products.' '.$this->l('products');
                unset($query);
            }
        }
    }


    public function displayConfirmation($string)
    {
        $output = '
		<div class="bootstrap">
		<div class="module_confirmation conf confirm alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			'.$string.'
		</div>
		</div>';
        return $this->content .= $output;
    }


    /**
     * Function used to render the list to display for this controller
     */
    public function renderList()
    {
        $this->displayInformation('&nbsp;<b>'.$this->l('How do I create new Google Merchant XML feed?').'</b>
			<br />
			<ul>
				<li>'.$this->l('Click "Add new feed" button.').'<br /></li>
				<li>'.$this->l('Configure feed to Your needs and products configuration and click "Save."').'<br /></li>
				<li>'.$this->l('Next add cron  task for generating xml files located in doc root of Your site').'<br /></li>
                <li>'.$this->l('Add new XML feed link in Your Google Merchant Center account').'<br /></li>
			</ul>');

        // init and render the first list
        $lists = parent::renderList();

        parent::initToolbar();
        
        $lists .= $this->displayCronLinks();
        $lists .= $this->displayXmlLinks();

        $lists .= '<br />'.$this->displayFormAssignProducts();

        return $lists;
    }
    

    public function displayFormAssignProducts()
    {
        // Categories form
        $root_category = Category::getRootCategory();
        $root_category = array('id_category' => $root_category->id, 'name' => $root_category->name);

        if (Tools::getValue('assign_ids_categories')) {
            $selected_categories = Tools::getValue('assign_ids_categories');
        } else {
            $selected_categories = array();
        }

        $manufacturers = Manufacturer::getManufacturers();
        $manufacturers_array = array();
        $manufacturers_array[0] =  array(
            'id_manufacturer' => 0,
            'name' => $this->l('-- Please select --')
        );

        $c = 0;
        foreach ($manufacturers as $m) {
            $c++;
            $manufacturers_array[$c] = array('id_manufacturer' => $m['id_manufacturer'], 'name' => $m['name']);
        }

        $suppliers = Supplier::getSuppliers();
        $suppliers_array = array();
        $suppliers_array[0] =  array(
            'id_supplier' => 0,
            'name' => $this->l('-- Please select --')
        );

        $c = 0;
        foreach ($suppliers as $m) {
            $c++;
            $suppliers_array[$c] = array('id_supplier' => $m['id_supplier'], 'name' => $m['name']);
        }


        $fields_form_2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Mass products assign / unassign for inlcusion in Google Shoping feed by shop, manufacturer, category (you can chose multiple criterions or one)'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Action Assign / unassign'),
                        'desc' => $this->l('Choose if You want to assign or unassign products inclusion in Google Shopping feed'),
                        'name' => 'assign_action',
                        'options' => array(
                            'query' => array(
                                array(
                                'action' => 1,
                                'name' => $this->l('Assign')
                                ),
                                array(
                                'action' => 0,
                                'name' => $this->l('Unassign')
                                ),
                            ),
                            'id' => 'action',
                            'name' => 'name'
                            )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Shop'),
                        'name' => 'assign_id_shop',
                        'desc' => $this->l('Select shop for products inclusion in Google Shopping feed'),
                        'required' => true,
                        'options' => array(
                            'query' => Shop::getShops(true),
                            'id' => 'id_shop',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select manufacturer / manufacturers'),
                        'name' => 'assign_ids_manufacturer[]',
                        'desc' => $this->l('Optional: select products by manufacturers for products inclusion in Google Shopping feed (multiple selection with holding CTRL key and clicking right mouse click)'),
                        'multiple' => true,
                        'options' => array(
                            'query' => $manufacturers_array,
                            'id' => 'id_manufacturer',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select supplier / suppliers'),
                        'name' => 'assign_id_suppliers[]',
                        'desc' => $this->l('Optional: select products by suppliers for products inclusion in Google Shopping feed (multiple selection with holding CTRL key and clicking right mouse click)'),
                        'multiple' => true,
                        'options' => array(
                            'query' => $suppliers_array,
                            'id' => 'id_supplier',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'categories',
                        'label' => $this->l('Select category / categories'),
                        'name' => 'assign_ids_categories',
                        'desc' => $this->l('Select products by categories for products inclusion in Google Shopping feed'),
                        'required' => true,
                        'tree' => array(
                            'use_search' => false,
                            'id' => 'assign_ids_categories',
                            'use_checkbox' => true,
                            'selected_categories' => $selected_categories,
                        ),
                        //retro compat 1.5 for category tree
                        'values' => array(
                            'trads' => array(
                                'Root' => $root_category,
                                'selected' => $this->l('Selected'),
                                'Collapse All' => $this->l('Collapse All'),
                                'Expand All' => $this->l('Expand All'),
                                'Check All' => $this->l('Check All'),
                                'Uncheck All' => $this->l('Uncheck All')
                            ),
                            'selected_cat' => $selected_categories,
                            'input_name' => 'assign_ids_categories[]',
                            'use_radio' => false,
                            'use_search' => false,
                            'disabled_categories' => array(),
                            'top_category' => Category::getTopCategory(),
                            'use_context' => true,
                        )
                    )
                ),
                'submit' => array(
                    'name' => 'submitAssignSelected',
                    'title' => $this->l('Assign / unassign products'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValuesAssign(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form_2));
    }

    public function getConfigFieldsValuesAssign()
    {
        $return = array();
        $return['assign_ids_manufacturer[]'] = Tools::getValue('assign_ids_manufacturer') ? Tools::getValue('assign_ids_manufacturer') : array();
        $return['assign_id_suppliers[]'] = Tools::getValue('assign_id_suppliers') ? Tools::getValue('assign_id_suppliers') : array();
        $return['assign_ids_categories'] = Tools::getValue('assign_ids_categories') ? Tools::getValue('assign_ids_categories') : array();
        $return['assign_action'] = Tools::isSubmit('assign_action') ? Tools::getValue('assign_action') : 1;
        $return['assign_id_shop'] = Tools::getValue('assign_id_shop');

        return $return;
    }



    

    public function displayCronLinks()
    {
        $html = '';
        $configs = $this->module->getServicesList();
        $secure_key = $this->module->secure_key;

        if ($this->ps_ver_16 || $this->ps_ver_17 || $this->ps_ver_8) {
            $html .= '<div class="form-horizontal clearfix">
                        <fieldset class="panel">
                        <div class="panel-heading">
                        <i class="icon-cogs"></i>
                        '.$this->l('Cron links for automatic XML generation').':
                        </div>
                        <div class="form-wrapper">';

            if (count($configs)) {
                foreach ($configs as $c) {
                    $id_shop_config = $c['id_shop'];
                    $shop = new Shop($id_shop_config);
                    $shop_url = $shop->getBaseURL(true);
                    $file_path = $shop_url.'modules/'.$this->module_name;
                    $html .= '
                                    <div class="form-group">
                                    <label class="control-label col-lg-3">'.$this->l('XML Configuration:').' ID '.$c['id_pdgooglemerchantcenterpro'].'</label>
                                        <div class="col-lg-9">
                                            <input type="text" value="'.$file_path.'/get.php?id_configuration='.$c['id_pdgooglemerchantcenterpro'].'&secure_key='.$secure_key.'" />
                                            <p class="help-block">

                                            </p>
                                        </div>
                                    </div>';
                }
                $html .= '
                                <div class="form-group">
                                <label class="control-label col-lg-3">'.$this->l('Generating all XML configurations:').'</label>
                                    <div class="col-lg-9">
                                        <input type="text" value="'.$file_path.'/get.php?generate_all=1&secure_key='.$secure_key.'" />
                                    </div>
                                </div>
                        ';
            } else {
                $html .= '<p>'.$this->l('No configurations found please create some.').'</p>';
            }
            $html .= '</div></fieldset></div>';
        } else {
            $html .= '<br /><fieldset>
                        <legend>'.$this->l('Cron links for automatic XML generation').':</legend>
                        ';

            if (count($configs)) {
                foreach ($configs as $c) {
                    $id_shop_config = $c['id_shop'];
                    $shop = new Shop($id_shop_config);
                    $shop_url = $shop->getBaseURL(true);
                    $file_path = $shop_url.'modules/'.$this->module_name;
                    $html .= '

                                    <label>'.$this->l('XML Configuration:').' ID '.$c['id_pdgooglemerchantcenterpro'].'</label>
                                    <div class="margin-form">
                                        <input type="text" style="width:85%" value="'.$file_path.'/get.php?id_configuration='.$c['id_pdgooglemerchantcenterpro'].'&secure_key='.$secure_key.'" />
                                    </div>
                                    <div class="clear"></div>
                                    ';
                }
                $html .= '
                                <label>'.$this->l('Generating all XML configurations:').'</label>
                                    <div class="margin-form">
                                        <input type="text" style="width:85%" value="'.$file_path.'/get.php?generate_all=1&secure_key='.$secure_key.'" />
                                    </div>';
            } else {
                $html .= '<p>'.$this->l('No configurations found please create some.').'</p>';
            }
            $html .= '</fieldset>';
        }

        return $html;
    }

    public function displayXmlLinks()
    {
        $html = '';
        $configs = $this->module->getServicesList();

        $shop_domain = $this->module->getShopDomain($this->context->shop->id, true);
        $file_path = $shop_domain.__PS_BASE_URI__.'google-merchant_id-';


        if ($this->ps_ver_16 || $this->ps_ver_17 || $this->ps_ver_8) {
            $html .= '
                    <div class="form-horizontal clearfix">
                        <fieldset class="panel">
                        <div class="panel-heading">
                        <i class="icon-cogs"></i>
                        '.$this->l('Links to generated XML files').'
                        </div>
                        <div class="form-wrapper">';

            if (count($configs)) {
                foreach ($configs as $c) {
                    $html .= '
                                    <div class="form-group">
                                    <label class="control-label col-lg-3">'.$this->l('XML file for configuration').' ID: '.$c['id_pdgooglemerchantcenterpro'].'</label>
                                        <div class="col-lg-9">
                                            <input type="text" value="'.$file_path.$c['id_pdgooglemerchantcenterpro'].'.xml" />
                                        </div>
                                    </div>';
                }
            } else {
                $html .= '<p>'.$this->l('No configurations found please create some.').'</p>';
            }
            $html .= '
                        </div>
                        </fieldset>
                    </div>';
        } else {
            $html .= '<br /><fieldset>
                        <legend>'.$this->l('Links to generated XML files').'</legend>';
            if (count($configs)) {
                foreach ($configs as $c) {
                    $html .= '
                                    <label>'.$this->l('XML file for configuration').' ID: '.$c['id_pdgooglemerchantcenterpro'].'</label>
                                    <div class="margin-form">
                                        <input type="text" style="width:85%" value="'.$file_path.$c['id_pdgooglemerchantcenterpro'].'.xml" />
                                    </div>
                                    <div class="clear"></div>
                                        ';
                }
            } else {
                $html .= '<p>'.$this->l('No configurations found please create some.').'</p>';
            }
            $html .= '</fieldset>';
        }
        return $html;
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_customer'] = array(
                'href' => self::$currentIndex.'&addpdgooglemerchantcenterpro&token='.$this->token,
                'desc' => $this->l('Add new XML feed', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $currency_unit = new Currency((int)(Configuration::get('PS_CURRENCY_DEFAULT')));

        // Switch or radio for ps 1.5 compatibility
        $switch = version_compare(_PS_VERSION_, '1.6.0', '>=') ? 'switch' : 'radio';

        // Categories form
        $root_category = Category::getRootCategory();
        $root_category = array('id_category' => $root_category->id, 'name' => $root_category->name);

        if (Tools::getValue('categoryBox')) {
            $selected_categories = Tools::getValue('categoryBox');
        } else {
            $selected_categories = isset($obj->selected_categories) ? explode(',', $obj->selected_categories) : array();
        }
        
        // Return object values to form on update configuration
        $this->fields_value['exclude_manufacturers[]'] = isset($obj->exclude_manufacturers) ? explode(',', $obj->exclude_manufacturers) : 0;
        $this->fields_value['exclude_suppliers[]'] = isset($obj->exclude_suppliers) ? explode(',', $obj->exclude_suppliers) : 0;


        $this->fields_value['sizes_attribute_group[]'] = isset($obj->sizes_attribute_group) ? explode(',', $obj->sizes_attribute_group) : 0;

        $attributes_groups = AttributeGroup::getAttributesGroups((int)$this->context->language->id);
        $default_item = array(
            'name' => $this->l('-- please select --'),
            'id_attribute_group' => 0,
            'is_color_group' => 0,
            'group_type' => '',
            'position' => 0,
            'id_lang' => 1,
            'public_name' => $this->l('-- please select --')
        );
        array_unshift($attributes_groups, $default_item);


        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('New feed configuration'),
                'icon' => 'icon-user'
            ),
            'tabs' => array(
                    'configMain' => $this->l('General feed configuration'),
                    'configProducts' => $this->l('Products source configuration'),
                    'configProductsParams' => $this->l('Products params configuration'),
            ),
            'input' => array(
                    array(
                        'type' => $switch,
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'tab' => 'configMain',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Set if configuration should be active'),
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
                     array(
                        'type' => 'select',
                        'label' => $this->l('Select language'),
                        'name' => 'id_lang',
                        'tab' => 'configMain',
                        'desc' => $this->l('Select language for products in generated XML'),
                        'options' => array(
                            'query' => Language::getLanguages(true),
                            'id' => 'id_lang',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Country'),
                        'name' => 'id_country',
                        'tab' => 'configMain',
                        'desc' => $this->l('Select country for products in generated XML (used for delivery cost calcualation)'),
                        'options' => array(
                            'query' => Country::getCountries((int)$this->context->language->id, true, false, false),
                            'id' => 'id_country',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Shop'),
                        'name' => 'id_shop',
                        'tab' => 'configMain',
                        'desc' => $this->l('Select shop for products in  generated XML (products data come form selected shop)'),
                        'options' => array(
                            'query' => Shop::getShops(true),
                            'id' => 'id_shop',
                            'name' => 'name'
                        )
                    ),
                   
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Currency'),
                        'name' => 'id_currency',
                        'tab' => 'configMain',
                        'desc' => $this->l('Select currency for products in generated XML'),
                        'options' => array(
                            'query' => Currency::getCurrenciesByIdShop((int)$this->context->shop->id),
                            'id' => 'id_currency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Googole category ISO'),
                        'name' => 'id_pdgooglemerchantcenterpro_taxonomy',
                        'tab' => 'configMain',
                        'desc' => $this->l('Select Google category accordingly to selected language, country, currency, there is table in module menu "Map category / import" which shows which Googole taxonomy ISO You should select for choosen language, country and currency'),
                        'options' => array(
                            'query' => $this->module->getSelectTaxonomiesOptions(),
                            'id' => 'id_pdgooglemerchantcenterpro_taxonomy',
                            'name' => 'taxonomy_lang'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Carrier'),
                        'name' => 'id_carrier',
                        'tab' => 'configMain',
                        'desc' => $this->l('Select carrier for products in generated XML (used for XML delivery attribute) IMPORTANT: Don\'t use methods like pickup in store Goolge feed specification don\'t allows it.'),
                        'options' => array(
                            'query' => Carrier::getCarriers((int)$this->context->language->id, true, false, false, null, Carrier::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE),
                            'id' => 'id_carrier',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Include shipping cost'),
                        'desc' => $this->l('Add shipping cost to products in generated XML Feed'),
                        'name' => 'include_shipping_cost',
                        'tab' => 'configMain',
                        'class' => 't',
                        'is_bool' => true, //retro-compat
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
                        'type' => $switch,
                        'label' => $this->l('Only available'),
                        'name' => 'only_available',
                        'is_bool' => true,
                        'tab' => 'configProducts',
                        'class' => 't',
                        'desc' => $this->l('This option, if enabled, will exclude products with 0 stock (only if we have enabled warehouse management or advanced warehouse management), and if it is turned off, and we have enabled warehouse management or advanced warehouse management then module will use the settings from the product editing page regarding product availability and purchase possibility when out of stock.'),
                        'values' => array(
                            array(
                                'id' => 'only_available_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'only_available_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Only available for order'),
                        'name' => 'available_for_order',
                        'is_bool' => true,
                        'tab' => 'configProducts',
                        'class' => 't',
                        'desc' => $this->l('This option, if enabled, will exclude products which have option "Available for order" disabled'),
                        'values' => array(
                            array(
                                'id' => 'available_for_order_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'available_for_order_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Only active'),
                        'name' => 'only_active',
                        'is_bool' => true,
                        'tab' => 'configProducts',
                        'class' => 't',
                        'desc' => $this->l('Include only active products in generated XML'),
                        'values' => array(
                            array(
                                'id' => 'only_active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'only_active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Create products from combinations'),
                        'desc' => $this->l('This option will create products from product attributes combinations (becarefull this option can slow down generation proces because if You have 2000 products and every product have 20 combinations then 40 000 products will be generated)'),
                        'name' => 'products_attributes',
                        'is_bool' => true,
                        'tab' => 'configProducts',
                        'class' => 't',
                        'values' => array(
                            array(
                                'id' => 'products_attributes_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'products_attributes_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select sizes atribute group / groups'),
                        'name' => 'sizes_attribute_group[]',
                        'multiple' => true,
                        'tab' => 'configProducts',
                        'desc' => $this->l('Select sizes attibute group or groups to list sizes for product'),
                        'options' => array(
                            'query' => $attributes_groups,
                            'id' => 'id_attribute_group',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select color atribute group'),
                        'name' => 'color_attribute_group',
                        'tab' => 'configProducts',
                        'desc' => $this->l('Select color attibute group to list colors for product'),
                        'options' => array(
                            'query' => $attributes_groups,
                            'id' => 'id_attribute_group',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'categories',
                        'label' => $this->l('Select category / categories'),
                        'name' => 'categoryBox',
                        'tab' => 'configProducts',
                        'desc' => $this->l('Mark the boxes of categories which will be used in generated XML.'),
                        'required' => true,
                        'tree' => array(
                            'use_search' => false,
                            'id' => 'categoryBox',
                            'use_checkbox' => true,
                            'selected_categories' => $selected_categories,
                        ),
                        //retro compat 1.5 for category tree
                        'values' => array(
                            'trads' => array(
                                'Root' => $root_category,
                                'selected' => $this->l('Selected'),
                                'Collapse All' => $this->l('Collapse All'),
                                'Expand All' => $this->l('Expand All'),
                                'Check All' => $this->l('Check All'),
                                'Uncheck All' => $this->l('Uncheck All')
                            ),
                            'selected_cat' => $selected_categories,
                            'input_name' => 'categoryBox[]',
                            'use_radio' => false,
                            'use_search' => false,
                            'disabled_categories' => array(),
                            'top_category' => Category::getTopCategory(),
                            'use_context' => true,
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Exclude manufacturers'),
                        'name' => 'exclude_manufacturers[]',
                        'tab' => 'configProducts',
                        'desc' => $this->l('Exclude selected manufacturers form generated XML (multiple selection with holding CTRL key and clicking right mouse click)'),
                        'multiple' => true,
                        'options' => array(
                            'query' => Manufacturer::getManufacturers(),
                            'id' => 'id_manufacturer',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Exclude suppliers'),
                        'name' => 'exclude_suppliers[]',
                        'tab' => 'configProducts',
                        'desc' => $this->l('Exclude selected suppliers form generated XML (multiple selection with holding CTRL key and clicking right mouse click)'),
                        'multiple' => true,
                        'options' => array(
                            'query' => Supplier::getSuppliers(),
                            'id' => 'id_supplier',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Excluded products'),
                        'tab' => 'configProducts',
                        'desc' => $this->l('Option allows to exclude Id of particular product, please provide them as coma separated values, example (1,2,3,4,5)'),
                        'lang' => false,
                        'suffix' => 'ID',
                        'name' => 'exclude_products'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Excluded products with price lower than'),
                        'desc' => $this->l('Option allows to exclude particular products which price is lover than value set here'),
                        'name' => 'min_product_price',
                        'tab' => 'configProducts',
                        'suffix' => $currency_unit->sign
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Description type'),
                        'desc' => $this->l('Choose description type for products'),
                        'name' => 'description',
                        'tab' => 'configProductsParams',
                        'options' => array(
                            'query' => array(
                                array(
                                'id' => 1,
                                'name' => $this->l('Short description')
                                ),
                                array(
                                'id' => 2,
                                'name' => $this->l('Long description')
                                ),
                                array(
                                'id' => 3,
                                'name' => $this->l('Meta description')
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name'
                            )
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Clean product descriptions'),
                        'desc' => $this->l('Option allows to clean to plain text product descriptions, in generated XML Feed (helps when have issues in GMC panel in products descriptions)'),
                        'name' => 'html_desc_cleaner',
                        'tab' => 'configProductsParams',
                        'class' => 't',
                        'is_bool' => true, //retro-compat
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
                        'label' => $this->l('Unique Product Identifier (GTIN)'),
                        'desc' => $this->l('Unique product identifiers are product codes or other identifying values associated with an individual product, please select from drop down which option You want to use'),
                        'name' => 'gtin',
                        'tab' => 'configProductsParams',
                        'class' => 'fixed-width-md',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 1,
                                    'name' => $this->l('EAN-13 / JAN')
                                ),
                                array(
                                    'id' => 2,
                                    'name' => $this->l('UPC')
                                ),
                                array(
                                    'id' => 3,
                                    'name' => $this->l('Reference (Index)')
                                ),
                                array(
                                    'id' => 4,
                                    'name' => $this->l('MPN')
                                ),
                                array(
                                    'id' => 0,
                                    'name' => $this->l('Disabled')
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Manufacturer Part Number (MPN)'),
                        'desc' => $this->l('Manufacturer Part Number is used to reference and identify a product using a manufacturer specific naming other than GTIN.'),
                        'name' => 'mpn',
                        'tab' => 'configProductsParams',
                        'class' => 'fixed-width-md',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 1,
                                    'name' => $this->l('Supplier reference')
                                ),
                                array(
                                    'id' => 2,
                                    'name' => $this->l('Reference (Index)')
                                ),
                                array(
                                    'id' => 3,
                                    'name' => $this->l('Id Product')
                                ),
                                array(
                                    'id' => 0,
                                    'name' => $this->l('Disabled')
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Product features'),
                        'desc' => $this->l('Option allows to add product features to generated XML Feed '),
                        'name' => 'features_enabled',
                        'tab' => 'configProductsParams',
                        'class' => 't',
                        'is_bool' => true, //retro-compat
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
                        'type' => $switch,
                        'label' => $this->l('Validate Ean13'),
                        'desc' => $this->l('Option allows to validate product EAN13 codes before we add them to generated XML Feed (option helps when we have issues in GMC panel regarding not valid EAN13 codes)'),
                        'name' => 'ean_validiation',
                        'tab' => 'configProductsParams',
                        'class' => 't',
                        'is_bool' => true, //retro-compat
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
                        'label' => $this->l('MPN number prefix'),
                        'name' => 'mpn_prefix',
                        'tab' => 'configProductsParams',
                        'desc' => $this->l('Add extra prefix to MPN number'),
                        'size' => 20,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Id number prefix'),
                        'name' => 'gid_prefix',
                        'tab' => 'configProductsParams',
                        'desc' => $this->l('Add extra prefix to each product g:id tag in XML'),
                        'size' => 20,
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Manufacturer name'),
                        'desc' => $this->l('Option allows to add manufacturer name to generated XML Feed'),
                        'name' => 'manu_name',
                        'tab' => 'configProductsParams',
                        'class' => 't',
                        'is_bool' => true, //retro-compat
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
                        'type' => $switch,
                        'label' => $this->l('Unit price measure'),
                        'desc' => $this->l('Option allows to enable or disable optional parameter unit price measure'),
                        'name' => 'unit_pricing_measure',
                        'class' => 't',
                        'tab' => 'configProductsParams',
                        'is_bool' => true, //retro-compat
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
                        'type' => $switch,
                        'label' => $this->l('Adults only products'),
                        'desc' => $this->l('Option allows to add attribute adult with value true to generated XML Feed (when you sell porducts for adults please enable this option)'),
                        'name' => 'adults',
                        'tab' => 'configProductsParams',
                        'class' => 't',
                        'is_bool' => true, //retro-compat
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
                        'label' => $this->l('Product images size / type'),
                        'name' => 'id_image_type',
                        'tab' => 'configProductsParams',
                        'desc' => $this->l('Option allows to select size of product images in generated XMl feed.'),
                        'options' => array(
                            'query' => ImageType::getImagesTypes('products', true),
                            'id' => 'id_image_type',
                            'name' => 'name'
                        )
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Images limit'),
                        'name' => 'image_limit',
                        'tab' => 'configProductsParams',
                        'desc' => $this->l('Option allows to limit amount of product images in generated XML file'),
                        'size' => 20,
                    ),
                    array(
                        'type' => $switch,
                        'label' => $this->l('Url rewriting'),
                        'desc' => $this->l('Disable url rewriting if is enabled for product url and product image url in generated XML Feed'),
                        'tab' => 'configProductsParams',
                        'name' => 'rewrite_url',
                        'class' => 't',
                        'is_bool' => true, //retro-compat
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
                   
                )
        );

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
            'icon' => 'process-icon-save',
            'class' => 'btn btn-default pull-right'
        );


        $this->fields_form['buttons'] = array(
            'save-and-stay' => array(
                'title' => $this->l('Save and stay'),
                'name' => 'submitAdd'.$this->table.'AndStay',
                'type' => 'submit',
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save'
            )
        );


        if (!($obj = $this->loadObject(true))) {
            return;
        }

        return parent::renderForm();
    }

    public function postProcess()
    {

         // Assign in_google_shopping to products by params
        if (Tools::isSubmit('submitAssignSelected')) {
            $assign_id_shop = Tools::getValue('assign_id_shop');
            $assign_action = Tools::getValue('assign_action');
            $manufacturers_ids = Tools::getValue('assign_ids_manufacturer');
            $suppliers_ids = Tools::getValue('assign_ids_suppliers');
            $categories_ids = Tools::getValue('assign_ids_categories');

            $error = $this->module->assignInGoogleShoppingToProducts($assign_id_shop, $categories_ids, $manufacturers_ids, $suppliers_ids, $assign_action);
            if ($error == false) {
                $this->errors[] = $this->l('There is no products matching criteria selected or You don\'t select any criteria');
            } else {
                $this->displayConfirmation($this->l('Include / exclude option for products inclusion in Google Shopping feed  was assigned sucesfully'));
            }
        }



        return parent::postProcess();
    }

    public function processAdd()
    {
        if (Tools::isSubmit('submitAddpdgooglemerchantcenterpro') || Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            if (!Tools::getValue('categoryBox') || count(Tools::getValue('categoryBox')) == 0) {
                $this->errors[] = $this->l('You need to select at least one category.');
            }

            if (!count($this->errors)) {
                $object = new $this->className();
                $object->id_shop = Tools::getValue('id_shop');
                $object->id_lang = Tools::getValue('id_lang');
                $object->id_country = Tools::getValue('id_country');
                $object->id_currency = Tools::getValue('id_currency');
                $object->id_carrier = Tools::getValue('id_carrier');
                $object->id_pdgooglemerchantcenterpro_taxonomy = Tools::getValue('id_pdgooglemerchantcenterpro_taxonomy');
                
                $object->active = Tools::getValue('active');
                $object->available_for_order = Tools::getValue('available_for_order');
                $object->only_available = Tools::getValue('only_available');
                $object->only_active = Tools::getValue('only_active');
                $object->selected_categories = implode(',', Tools::getValue('categoryBox'));
                $object->exclude_products = Tools::getValue('exclude_products');
                $object->id_image_type = Tools::getValue('id_image_type');
                if (Tools::getValue('exclude_manufacturers')) {
                    $exclude_manufacturers = Tools::getValue('exclude_manufacturers');
                } else {
                    $exclude_manufacturers = array();
                }

                if (Tools::getValue('exclude_suppliers')) {
                    $exclude_suppliers = Tools::getValue('exclude_suppliers');
                } else {
                    $exclude_suppliers = array();
                }

                $object->exclude_manufacturers = count($exclude_manufacturers) ? implode(',', $exclude_manufacturers) : '';
                $object->exclude_suppliers = count($exclude_suppliers) ? implode(',', $exclude_suppliers) : '';
                $object->products_attributes = Tools::getValue('products_attributes');

                $object->include_shipping_cost = Tools::getValue('include_shipping_cost');
                $object->description = Tools::getValue('description');
                $object->rewrite_url = Tools::getValue('rewrite_url');

                $object->unit_pricing_measure = Tools::getValue('unit_pricing_measure');
                $object->image_limit = Tools::getValue('image_limit');

                $object->min_product_price = Tools::getValue('min_product_price');
                $object->gtin = Tools::getValue('gtin');
                $object->mpn = Tools::getValue('mpn');
                $object->adults = Tools::getValue('adults');
                $object->manu_name = Tools::getValue('manu_name');
                $object->mpn_prefix = Tools::getValue('mpn_prefix');
                $object->gid_prefix = Tools::getValue('gid_prefix');

                $object->features_enabled = Tools::getValue('features_enabled');

                $object->ean_validiation = Tools::getValue('ean_validiation');
                $object->html_desc_cleaner = Tools::getValue('html_desc_cleaner');

                $object->sizes_attribute_group = Tools::getValue('sizes_attribute_group') ? implode(',', Tools::getValue('sizes_attribute_group')) : '';
                $object->color_attribute_group = Tools::getValue('color_attribute_group');


                $object->date_add = date('Y-m-d H:i:s');
                $object->date_upd = '0000-00-00 00:00:00';
                

                if (!$object->add()) {
                    $this->errors[] = Tools::displayError('An error occurred while creating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                }
            }

            $this->errors = array_unique($this->errors);
            
            if (!empty($this->errors)) {
                // if we have errors, we stay on the form instead of going back to the list
                $this->display = 'add';
                return false;
            }

            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && empty($this->errors)) {
                if ($this->display == 'add') {
                    $this->redirect_after = self::$currentIndex.'&'.$this->identifier.'=&conf=3&update'.$this->table.'&token='.$this->token;
                }
            }
        }
    }

    public function processUpdate()
    {
        if (Tools::isSubmit('submitAddpdgooglemerchantcenterpro') || Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
            if (!Tools::getValue('categoryBox') || count(Tools::getValue('categoryBox')) == 0) {
                $this->errors[] = $this->l('You need to select at least one category.');
            }

            $id_pdgooglemerchantcenterpro = (int)Tools::getValue('id_pdgooglemerchantcenterpro');
            $object = new $this->className($id_pdgooglemerchantcenterpro);
            $object->id_shop = Tools::getValue('id_shop');
            $object->id_lang = Tools::getValue('id_lang');
            $object->id_country = Tools::getValue('id_country');
            $object->id_currency = Tools::getValue('id_currency');
            $object->id_carrier = Tools::getValue('id_carrier');
            $object->id_pdgooglemerchantcenterpro_taxonomy = Tools::getValue('id_pdgooglemerchantcenterpro_taxonomy');

            $object->available_for_order = Tools::getValue('available_for_order');
            $object->active = Tools::getValue('active');
            $object->ean_validiation = Tools::getValue('ean_validiation');
            $object->html_desc_cleaner = Tools::getValue('html_desc_cleaner');
            $object->only_available = Tools::getValue('only_available');
            $object->only_active = Tools::getValue('only_active');
            $object->selected_categories = implode(',', Tools::getValue('categoryBox'));
            $object->exclude_products = Tools::getValue('exclude_products');
            $object->id_image_type = Tools::getValue('id_image_type');

            $object->image_limit = Tools::getValue('image_limit');

            if (Tools::getValue('exclude_manufacturers')) {
                $exclude_manufacturers = Tools::getValue('exclude_manufacturers');
            } else {
                $exclude_manufacturers = array();
            }

            if (Tools::getValue('exclude_suppliers')) {
                $exclude_suppliers = Tools::getValue('exclude_suppliers');
            } else {
                $exclude_suppliers = array();
            }

            $object->exclude_manufacturers = count($exclude_manufacturers) ? implode(',', $exclude_manufacturers) : '';
            $object->exclude_suppliers = count($exclude_suppliers) ? implode(',', $exclude_suppliers) : '';

            $object->products_attributes = Tools::getValue('products_attributes');

            $object->include_shipping_cost = Tools::getValue('include_shipping_cost');
            $object->description = Tools::getValue('description');
            $object->rewrite_url = Tools::getValue('rewrite_url');

            $object->unit_pricing_measure = Tools::getValue('unit_pricing_measure');

            $object->min_product_price = Tools::getValue('min_product_price');
            $object->gtin = Tools::getValue('gtin');
            $object->mpn = Tools::getValue('mpn');
            $object->adults = Tools::getValue('adults');
            $object->manu_name = Tools::getValue('manu_name');
            $object->mpn_prefix = Tools::getValue('mpn_prefix');
            $object->gid_prefix = Tools::getValue('gid_prefix');

            $object->features_enabled = Tools::getValue('features_enabled');

            $object->sizes_attribute_group = Tools::getValue('sizes_attribute_group') ? implode(',', Tools::getValue('sizes_attribute_group')) : '';
            $object->color_attribute_group = Tools::getValue('color_attribute_group');

            $object->date_upd = date('Y-m-d H:i:s');

            if (!$object->update()) {
                $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            }
        }
    
        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            // if we have errors, we stay on the form instead of going back to the list
            $this->display = 'add';
            return false;
        }

        if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && empty($this->errors)) {
            if ($this->display == 'edit' || $this->display == 'add') {
                $this->redirect_after = self::$currentIndex.'&updatepdgooglemerchantcenterpro&id_pdgooglemerchantcenterpro='.(int)Tools::getValue('id_pdgooglemerchantcenterpro').'&token='.$this->token;
            } else {
                $this->redirect_after = self::$currentIndex.'&'.$this->identifier.'=&conf=3&update'.$this->table.'&token='.$this->token;
            }
        }
    }

    public function initProcess()
    {
        parent::initProcess();

        if (Tools::isSubmit('only_availablepdgooglemerchantcenterpro')) {
            $id_pdgooglemerchantcenterpro = (int)Tools::getValue('id_pdgooglemerchantcenterpro');
            $object = new GoogleMerchantCenterProModel($id_pdgooglemerchantcenterpro);
            $object->only_available = $object->only_available ? 0 : 1;
            $object->update();
        } elseif (Tools::isSubmit('only_activepdgooglemerchantcenterpro')) {
            $id_pdgooglemerchantcenterpro = (int)Tools::getValue('id_pdgooglemerchantcenterpro');
            $object = new GoogleMerchantCenterProModel($id_pdgooglemerchantcenterpro);
            $object->only_active = $object->only_active ? 0 : 1;
            $object->update();
        } elseif (Tools::isSubmit('rewrite_urlpdgooglemerchantcenterpro')) {
            $id_pdgooglemerchantcenterpro = (int)Tools::getValue('id_pdgooglemerchantcenterpro');
            $object = new GoogleMerchantCenterProModel($id_pdgooglemerchantcenterpro);
            $object->rewrite_url = $object->rewrite_url ? 0 : 1;
            $object->update();
        } elseif (Tools::isSubmit('products_attributespdgooglemerchantcenterpro')) {
            $id_pdgooglemerchantcenterpro = (int)Tools::getValue('id_pdgooglemerchantcenterpro');
            $object = new GoogleMerchantCenterProModel($id_pdgooglemerchantcenterpro);
            $object->products_attributes = $object->products_attributes ? 0 : 1;
            $object->update();
        } elseif (Tools::isSubmit('generate_filepdgooglemerchantcenterpro')) {
            $id_pdgooglemerchantcenterpro = (int)Tools::getValue('id_pdgooglemerchantcenterpro');
            $this->module->generateFeedFromConfig(false, $id_pdgooglemerchantcenterpro);
        }
    }
}
