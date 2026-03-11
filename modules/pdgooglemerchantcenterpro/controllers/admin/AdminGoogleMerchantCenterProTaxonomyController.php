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

require_once dirname(__FILE__).'/../../models/GoogleMerchantCenterProModelTaxonomy.php';

class AdminGoogleMerchantCenterProTaxonomyController extends ModuleAdminController
{
    public $module = null;
    public $module_name = 'pdgooglemerchantcenterpro';
    
    
    public function __construct()
    {
        $this->table = 'pdgooglemerchantcenterpro_taxonomy';
        $this->className = 'GoogleMerchantCenterProModelTaxonomy';
        $this->lang = false;
        $this->bootstrap = true;
        $this->context = Context::getContext();

        $this->list_simple_header = true;
        $this->list_no_link = true;

        if (Module::isInstalled($this->module_name)) {
            $this->module = Module::getInstanceByName($this->module_name);
        }

        $this->ps_ver_8 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '8.0', '=')) ? true : false;
        $this->ps_ver_17 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '=')) ? true : false;
        $this->ps_ver_16 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')) ? true : false;
        $this->ps_ver_15 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.5', '=')) ? true : false;

        parent::__construct();

        $this->fields_list = array(
            'id_pdgooglemerchantcenterpro_taxonomy' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                   'filter' => false,
                'width' => 25
            ),
            'taxonomy_lang' => array(
                'title' => $this->l('Google iso code'),
                'width' => 60
            ),
            'languages' => array(
                'title' => $this->l('Concerned languages'),
                'width' => 140
            ),
            'currencies' => array(
                'title' => $this->l('Concerned currencies'),
                'width' => 140
            ),
            'countries' => array(
                'title' => $this->l('Concerned countries'),
                'width' => 140
            ),
            'imported' => array(
                'title' => $this->l('Imported'),
                'align' => 'center',
                'callback' => 'printImportedIcon',
                'type' => 'bool',
                'filter' => false,
                'orderby' => false,
                'width' => 25
            ),
            'import' => array(
                'title' => $this->l('Import / update'),
                'align' => 'text-center',
                'callback' => 'printImportIcon',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
                'width' => 25
            ),
            'date_add' => array(
                'title' => $this->l('Date import / update'),
                'align' => 'right',
                'width' => 'auto',
                'filter' => false,
                'type' => 'datetime',
            ),
        );
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $this->addJqueryPlugin(array('autocomplete'));
    }


    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        $languages_array = $this->module->languagesIsoTranslation;
        $countries_array = $this->module->countriesIsoTranslation;

        foreach ($this->_list as $k => $list) {
            if (count(explode(',', $this->_list[$k]['languages'])) > 1) {
                $languages_row_arr = explode(',', $this->_list[$k]['languages']);
                $string = '';
                foreach ($languages_row_arr as &$l) {
                    $l = trim($l);
                    $string .= $languages_array[$l].', ';
                }

                $this->_list[$k]['languages'] = rtrim($string, ', ');
                $string = '';
            } else {
                $this->_list[$k]['languages'] = $languages_array[$list['languages']];
            }

            if (count(explode(',', $this->_list[$k]['countries'])) > 1) {
                $countries_row_arr = explode(',', $this->_list[$k]['countries']);
                $string = '';
                foreach ($countries_row_arr as &$c) {
                    $c = trim($c);
                    $string .= $countries_array[$c].', ';
                }
         
                $this->_list[$k]['countries'] = rtrim($string, ', ');
                $string = '';
            } else {
                $this->_list[$k]['countries'] = $countries_array[$list['countries']];
            }
        }

        //dump($this->_list);
        //die();
    }

    /**
     * Function used to render the list to display for this controller
     */
    public function renderList()
    {
        $this->addRowAction('Mapcategories');

        $this->displayInformation('&nbsp;<b>'.$this->l('How do I import Google taxonomy / product categories and map them to shop categories?').'</b>
            <br />
            <ul>
                <li>'.$this->l('Some quick brief: Each country has its own taxonomy / product categories in Google Merchant Center').'<br /></li>
                <li>'.$this->l('If You want to use shop category products to Google products category mapping first You need to download corect taxonomy data').'<br /></li>
                <li>'.$this->l('Please click "Import / update" button to download Google taxonomy / product category for your country / language / currency, or import all of them').'<br /></li>
                <li>'.$this->l('Final step is to map shop categories to Gogole categories by clicking "Map categories" button and next to each shop category start typing category name in input field, autocomplete function will bring up results which you can assign to shop category').'<br /></li>
            </ul>');

        // init and render the first list
        return parent::renderList();
    }
    

    public function displayMapCategoriesLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
        $tpl->assign(array(
                'href' => self::$currentIndex.'&id_pdgooglemerchantcenterpro_taxonomy='.$id.'&map_categories'.$this->table.'&token='.($token != null ? $token : $this->token),
                'action' => $this->l('Map categories'),
                'id' => $id
        ));
    
        return $tpl->fetch();
    }
    
        
    public function printImportIcon($id_pdgooglemerchantcenterpro_taxonomy, $tr)
    {
        $id = $tr['id_pdgooglemerchantcenterpro_taxonomy'];
        $link = $this->context->link->getAdminLink('AdminGoogleMerchantCenterProTaxonomy').'&id_pdgooglemerchantcenterpro_taxonomy='.$id.'&download_google_taxonomy';
        
        if ($this->ps_ver_16 || $this->ps_ver_17 || $this->ps_ver_8) {
            $button = '<a class="btn btn-default" href="'.$link.'"><i class="icon-circle-arrow-up"></i></a>';
        } else {
            $button = '<a href="'.$link.'"><img src="../img/admin/manufacturers.gif" /></a>';
        }
        
        return $button;
    }

    public function printImportedIcon($id_pdgooglemerchantcenterpro_taxonomy, $tr)
    {
        $imported = $tr['imported'];
        if ($this->ps_ver_16 || $this->ps_ver_17 || $this->ps_ver_8) {
            if ($imported) {
                $button = '<span title="'.$this->l('Enabled').'" class="list-action-enable action-enabled"><i class="icon-check"></i></span>';
            } else {
                $button = '<span title="'.$this->l('Disabled').'" class="list-action-enable action-disabled"><i class="icon-remove"></i></span>';
            }
        } else {
            if ($imported) {
                $button = '<span><img src="../img/admin/enabled.gif" /></span>';
            } else {
                $button = '<span><img src="../img/admin/disabled.gif" /></span>';
            }
        }
        return $button;
    }

    /**
     * List actions
     */
    public function initProcess()
    {
        parent::initProcess();

        if (Tools::isSubmit('download_google_taxonomy')) {
            $id_pdgooglemerchantcenterpro_taxonomy = (int)Tools::getValue('id_pdgooglemerchantcenterpro_taxonomy');

            $object = new $this->className($id_pdgooglemerchantcenterpro_taxonomy);
            $imported = $this->module->importTaxonomyData($object->taxonomy_lang);

            if ($imported) {
                $object->imported = 1;
                $object->date_add = date('Y-m-d H:i:s');
                $object->update();
            }
        }
    }

    public function postProcess()
    {
        if (Tools::getIsset('map_categoriespdgooglemerchantcenterpro_taxonomy')) {
            $this->renderForm();
        }

        if (Tools::isSubmit('searchTaxonomyCategory')) {
            $query = Tools::getValue('q', false);
            $id_pdgooglemerchantcenterpro_taxonomy = (int)Tools::getValue('id_pdgooglemerchantcenterpro_taxonomy');

            $object = new $this->className($id_pdgooglemerchantcenterpro_taxonomy);
            $taxonomy_lang = $object->taxonomy_lang;

            if (ob_get_level() && ob_get_length() > 0) {
                ob_end_clean();
            }
            echo $this->autoCompleteSearch($query, $taxonomy_lang);
            die();
        }

        if (Tools::isSubmit('submitSaveCategoriesMapping')) {
            $id_pdgooglemerchantcenterpro_taxonomy = (int)Tools::getValue('id_pdgooglemerchantcenterpro_taxonomy');
            $object = new $this->className($id_pdgooglemerchantcenterpro_taxonomy);
            $taxonomy_lang = $object->taxonomy_lang;
            $catsmappingarr = Tools::getValue('catsmappingarr');

            if (!count($catsmappingarr)) {
                $this->errors[] = $this->l('Please map categories first before save.');
            }

            $this->module->updateMapGoogleCategories2ShopCategories($catsmappingarr, $taxonomy_lang);
            $this->displayConfirmation($this->l('Shop categories to Google categories mappings was saved sucesfully.'));
        }

        //parent::postProcess();
    }


    public function autoCompleteSearch($query, $taxonomy_lang)
    {
        if (!$query || $query == '' || Tools::strlen($query) < 1) {
            die();
        }
        
        $words = explode(' ', $query);
        $output = '';
        
        $sql = 'SELECT `value`
                FROM `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_data`
                WHERE lang = "'.pSQL($taxonomy_lang).'"';
       
        foreach ($words as $w) {
            $sql .= ' AND value LIKE \'%'.pSQL($w).'%\'';
        }
            
        $items = Db::getInstance()->ExecuteS($sql);

        if ($items) {
            foreach ($items as $item) {
                $output .= trim($item['value'])."\n";
            }
        }
        return trim($output);
    }



    public function renderForm()
    {
        $id_pdgooglemerchantcenterpro_taxonomy = (int)Tools::getValue('id_pdgooglemerchantcenterpro_taxonomy');
        $object = new $this->className($id_pdgooglemerchantcenterpro_taxonomy);
        $taxonomy_lang = $object->taxonomy_lang;

        $categories = $this->module->generateCategoryPath($this->context->language->id, $this->context->shop->id);
        foreach ($categories as &$c) {
            $gct = $this->module->getGoogleTaxonomyCategory((int)$c['id_category'], $taxonomy_lang);

            if ($gct && is_array($gct) && sizeof($gct) && isset($gct['txt_taxonomy'])) {
                $c['txt_taxonomy'] = $gct['txt_taxonomy'];
            } else {
                $c['txt_taxonomy'] = '';
            }
        }

        $this->context->smarty->assign(array(
            'ps_ver_16' => $this->ps_ver_16,
            'ps_ver_15' => $this->ps_ver_15,
            'categories' => $categories,
            'taxonomy_lang' => $taxonomy_lang,
            'id_pdgooglemerchantcenterpro_taxonomy' => $id_pdgooglemerchantcenterpro_taxonomy,
            'post_url' => self::$currentIndex.'&saveAsociations&token='.$this->token,
            'ajax_url' => $this->context->link->getAdminLink('AdminGoogleMerchantCenterProTaxonomy', true),
            'token' => $this->token,
        ));

        $this->content .= $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/categories.tpl');
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
}
