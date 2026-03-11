<?php
/**
* 2012-2017 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Price Compare Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2017 Patryk Marek - PrestaDev.pl
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @link      http://prestadev.pl
* @package   PD Price Compare Pro - PrestaShop 1.5.x and 1.6.x Module
* @version   1.5.0
* @date      10-06-2015
*/

require_once dirname(__FILE__).'/../../models/GoogleMerchantCenterProModelDictionary.php';

class AdminGoogleMerchantCenterProDictionaryController extends ModuleAdminController
{
    public $module = null;
    public $module_name = 'pdgooglemerchantcenterpro';
    
    
    public function __construct()
    {
        $this->table = 'pdgooglemerchantcenterpro_dictionary';
        $this->className = 'GoogleMerchantCenterProModelDictionary';
        $this->lang = false;
        $this->bootstrap = true;

        if (Module::isInstalled($this->module_name)) {
            $this->module = Module::getInstanceByName($this->module_name);
        }

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->id_lang = Configuration::get('PS_LANG_DEFAULT');

        $this->ps_ver_16 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')) ? true : false;
        $this->ps_ver_15 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.5', '=')) ? true : false;

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            ),
            'enableSelection' => array('text' => $this->l('Enable selection')),
            'disableSelection' => array('text' => $this->l('Disable selection'))
        );

        

        $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'confirm' => $this->l('Delete selected items?')
                )
        );
        
        $this->fields_list = array(
            'id_pdgooglemerchantcenterpro_dictionary' => array(
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
            'source_word' => array(
                'title' => $this->l('Source word / phrase'),
                'width' => 100
            ),
            'destination_word' => array(
                'title' => $this->l('Destination word / phrase'),
                'width' => 100
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
        );
    }
          
    /**
     * Function used to render the list to display for this controller
     */
    public function renderList()
    {
        $this->displayInformation('&nbsp;<b>'.$this->l('How do I use dictionary and what it is for?').'</b>
            <br />
            <ul>
                <li>'.$this->l('Some quick brief: Google don\'t like some words in product names or product description and you can replace them on the fly for empty string or some other word.').'<br /></li>
                <li>'.$this->l('To add new entry click in top right corrner button "Add new dictionary entry" and type source word or phrase and destination word or phrase and click save.').'<br /></li>
            </ul>');

        // init and render the first list
        return parent::renderList();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_configuration'] = array(
                'href' => self::$currentIndex.'&addpdgooglemerchantcenterpro_dictionary&token='.$this->token,
                'desc' => $this->l('Add new dictionary entry', null, null, false),
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

        // Switch or radio for ps 1.5 compatibility
        $switch = version_compare(_PS_VERSION_, '1.6.0', '>=') ? 'switch' : 'radio';

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Dictionary add / edit'),
                'icon' => 'icon-user'
            ),
            'input' => array(
                array(
                    'type' => $switch,
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Set if replacement should be active'),
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
                    'type'  => 'text',
                    'label' => $this->l('Source word / phrase'),
                    'name'  => 'source_word',
                ),
                array(
                    'type'  => 'text',
                    'label' => $this->l('Destination word / phrase'),
                    'name'  => 'destination_word',
                ),
            )
        );
    
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
            'icon' => 'process-icon-save',
            'class' => 'btn btn-default pull-right'
        );
        
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        
        return parent::renderForm();
    }


    public function postProcess()
    {
        //Tools::clearSmartyCache();
        return parent::postProcess();
    }


    public function processAdd()
    {
        if (Tools::isSubmit('submitAddpdgooglemerchantcenterpro_dictionary')) {
            if (!Tools::getValue('source_word') || Tools::getValue('source_word') == '') {
                $this->errors[] = $this->l('You need to specify source word / phrase.');
            }

            if (!Tools::getValue('destination_word') || Tools::getValue('destination_word') == '') {
                $this->errors[] = $this->l('You need to specify destination word / phrase for which source word / phrase will be replaced.');
            }

            $object = new $this->className();
            $object->active = Tools::getValue('active');
            $object->source_word = Tools::getValue('source_word');
            $object->destination_word = Tools::getValue('destination_word');
            $object->date_add = date('Y-m-d H:i:s');
            $object->date_upd = '0000-00-00 00:00:00';
                

            if (!$object->add()) {
                $this->errors[] = Tools::displayError('An error occurred while creating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            }

            $this->errors = array_unique($this->errors);
            
            if (!empty($this->errors)) {
                // if we have errors, we stay on the form instead of going back to the list
                $this->display = 'edit';
                return false;
            }
        }
    }

    public function processUpdate()
    {
        if (Tools::isSubmit('submitAddpdgooglemerchantcenterpro_dictionary') && Tools::isSubmit('id_pdgooglemerchantcenterpro_dictionary')) {
            if (!Tools::getValue('source_word') || Tools::getValue('source_word') == '') {
                $this->errors[] = $this->l('You need to specify source word / phrase.');
            }

            if (!Tools::getValue('destination_word') || Tools::getValue('destination_word') == '') {
                $this->errors[] = $this->l('You need to specify destination word / phrase for which source word / phrase will be replaced.');
            }

            $id_pdgooglemerchantcenterpro_dictionary = (int)Tools::getValue('id_pdgooglemerchantcenterpro_dictionary');
            $object = new $this->className($id_pdgooglemerchantcenterpro_dictionary);

            $object->active = Tools::getValue('active');
            $object->source_word = Tools::getValue('source_word');
            $object->destination_word = Tools::getValue('destination_word');
            $object->date_upd = date('Y-m-d H:i:s');

            if (!$object->update()) {
                $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            }
        }
    
        $this->errors = array_unique($this->errors);
        
        if (!empty($this->errors)) {
            // if we have errors, we stay on the form instead of going back to the list
            $this->display = 'edit';
            return false;
        }
    }
}
