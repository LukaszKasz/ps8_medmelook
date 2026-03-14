<?php
/**
* 2007-2021 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Apaczka extends Module
{
    protected $config_form = true;
    protected $carriers = [];
    protected $apaczkaCarriers = ['DHL_PARCEL', 'DPD', 'INPOST', 'POCZTA', 'UPS', 'PWR'];
    
    public function __construct()
    {
        $this->name = 'apaczka';
        $this->tab = 'shipping_logistics';
        $this->version = '1.4.0';
        $this->author = 'Apaczka.pl';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '20ddb7b092d62f20a3f92442f1a8f270';
        parent::__construct();
        $this->displayName = $this->l('Shipping with Apaczka');
        $this->description = $this->l('Simple map for shipping with Apaczka.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete this module? ');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    public function install()
    {
        $sql = 'ALTER TABLE '._DB_PREFIX_.'cart 
        ADD COLUMN apaczka_supplier VARCHAR(50) DEFAULT "",
        ADD COLUMN apaczka_point VARCHAR(50) DEFAULT ""; 
        ';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        $sql = 'ALTER TABLE '._DB_PREFIX_.'orders 
        ADD COLUMN apaczka_supplier VARCHAR(50) DEFAULT "",
        ADD COLUMN apaczka_point VARCHAR(50) DEFAULT ""; 
        ';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayBeforeCarrier') &&
            $this->registerHook('displayAdminOrder') && 
            Configuration::updateValue('APACZKA_MAPS_API_KEY', '') &&
            Configuration::updateValue('APACZKA_CARRIERS', '');
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    public function uninstall()
    {
        $sql = 'ALTER TABLE '._DB_PREFIX_.'cart 
        DROP COLUMN apaczka_supplier,
        DROP COLUMN apaczka_point; 
        ';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        $sql = 'ALTER TABLE '._DB_PREFIX_.'orders 
        DROP COLUMN apaczka_supplier,
        DROP COLUMN apaczka_point; 
        ';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        Configuration::deleteByName('APACZKA_MAPS_API_KEY');
        Configuration::deleteByName('APACZKA_CARRIERS');
       
        return parent::uninstall();
    }

    public function hookDisplayAdminOrder($params)
    {

        $db = Db::getInstance();
        $query = 'SELECT apaczka_supplier, apaczka_point FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $params['id_order'] . ';';

        $apaczka_order = $db->getRow($query);

        $this->context->smarty->assign([
            'apaczka_order' => $apaczka_order
        ]);

        return $this->display(__FILE__, 'apaczka.tpl');
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';
        if (((bool)Tools::isSubmit('submitApaczkaModule')) == true) {
            $this->postProcessUpdateCarriers();
            $output .= $this->displayConfirmation($this->l('Carrier configuration successfully saved'));
        }

        $this->carriers = Carrier::getCarriers($this->context->language->id);
        
        $this->context->smarty->assign($this->getConfigFormValues());
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('carriers', $this->carriers);
        $this->context->smarty->assign('apaczkaCarriers', $this->apaczkaCarriers);

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }
    
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $apaczkaCarriersConfig = Configuration::get('APACZKA_CARRIERS');
        
        if (empty($apaczkaCarriersConfig)) {
            $apaczkaCarriersConfig = $this->getDefaultCarriersConfig();
            $apaczkaCarriersConfigFinal = $apaczkaCarriersConfig;
        } else {
            $apaczkaCarriersConfig = unserialize($apaczkaCarriersConfig);
            $apaczkaCarriersConfigFinal = [];
            
            foreach ($this->carriers as $carrier) {
                $added = false;

                foreach ($apaczkaCarriersConfig as $reference => $apaczkaCarrier) {
                    if ($reference == $carrier['id_reference']) {
                        $apaczkaCarriersConfigFinal[$reference] = $apaczkaCarrier;
                        $added = true;
                        break;
                    }
                }
                
                if (!$added) {
                    $apaczkaCarriersConfigFinal[$carrier['id_reference']] = [
                        'apaczkaName' => '-1',
                        'cod' => 0,
                        'points' => 0
                    ];
                }
            }
        }

        return array(
            'APACZKA_MAPS_API_KEY' => Configuration::get('APACZKA_MAPS_API_KEY'),
            'APACZKA_CARRIERS' => $apaczkaCarriersConfigFinal,
        );
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Save form data.
     */
    protected function postProcessUpdateCarriers()
    {
        $apaczkaConfig = [];
        // // Fill empty data with default values 
        foreach (Tools::getValue('APACZKA_CARRIERS') as $reference => $carrier) {
            $apaczkaConfig[$reference] = [
                'apaczkaName' => array_key_exists('apaczkaName', $carrier) ? $carrier['apaczkaName'] : 0,
                'cod' => array_key_exists('cod', $carrier) ? $carrier['cod'] : 0,
                'points' => array_key_exists('points', $carrier) ? $carrier['points'] : 0
            ];
        }

        Configuration::updateGlobalValue('APACZKA_MAPS_API_KEY', Tools::getValue('APACZKA_MAPS_API_KEY'));
        Configuration::updateGlobalValue('APACZKA_CARRIERS', serialize($apaczkaConfig));
    }
    
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS('/modules/'.$this->name.'/views/css/back.css');
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if (strpos(_PS_VERSION_, '1.7') === 0) {
            $this->context->controller->registerJavascript(
                '1.7',
                'https://mapa.apaczka.pl/client/apaczka.map.js', // JS path
                array('server' => 'remote', 'position' => 'head', 'priority' => 1) // Arguments
            );
        } elseif (strpos(_PS_VERSION_, '1.6') === 0) {
            $this->context->controller->addJS('https://mapa.apaczka.pl/client/apaczka.map.js');
        } else {
            $this->context->controller->registerJavascript(
                '1.7',
                'https://mapa.apaczka.pl/client/apaczka.map.js', // JS path
                array('server' => 'remote', 'position' => 'head', 'priority' => 1) // Arguments
            );
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    protected function getApaczkaCarriersConfig()
    {
        $apaczkaCarriersConfig = unserialize(Configuration::get('APACZKA_CARRIERS'));
        $carriers = Carrier::getCarriers($this->context->language->id);
        
        foreach ($apaczkaCarriersConfig as $key => &$config) {
            if (!($config['apaczkaName'] != '-1' && in_array($config['apaczkaName'], $this->apaczkaCarriers))) {
                unset($apaczkaCarriersConfig[$key]);
                continue;
            }
            
            
            foreach ($carriers as $carrier) {
                if ($carrier['id_reference'] == $key) {
                    $config['id_carrier'] = $carrier['id_carrier'];
                    break;
                }
            }
            
            if (empty($config['id_carrier'])) {
                unset($apaczkaCarriersConfig[$key]);
                continue;
            }
        }
        
        return $apaczkaCarriersConfig;
    }
    
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    public function hookDisplayBeforeCarrier()
    {
        $city = "";
        $street = "";
        $addressObjTxt = null;
        $countryCode = "PL";
        
        if (!empty($this->context->cart->id_address_delivery)) {
            $address = new Address($this->context->cart->id_address_delivery);
            if ($address->id) {
                $city = $address->city;
                $street = $address->address1;
                $countryCode = Country::getIsoById($address->id_country);
            }
        }
        
        if (!empty($city) && !empty($street)) {
            $addressObjTxt = "{address: {city: '".$city."', street: '".$street."'}}";
        }
        //echo $addressObjTxt; exit;
        $apiKey = Configuration::get('APACZKA_MAPS_API_KEY', '');
        
        if (empty($apiKey)) {
            return "";
        }
        
        $apaczkaCarriersConfig = $this->getApaczkaCarriersConfig();
        
        if (empty($apaczkaCarriersConfig)) {
            return "";
        }
     
        $cartRow = Db::getInstance()->getRow(
            "SELECT apaczka_supplier, apaczka_point 
            from "._DB_PREFIX_."cart WHERE id_cart=".(int)$this->context->cart->id
        );
        
        $smarty = Context::getContext()->smarty;
        $smarty->assign([
            'apaczka_carriers_json' => json_encode($apaczkaCarriersConfig),
            'apaczka_apiKey' => $apiKey,
            'apaczka_carriersConfig' => $apaczkaCarriersConfig,
            'apaczka_cartRow' => $cartRow,
            'apaczka_cart' => $this->context->cart,
            'apaczka_addressObjTxt' => $addressObjTxt,
            'countryCode' => $countryCode,
            'apaczka_ajax_url' => $this->context->link->getModuleLink($this->name, 'ajax'),
        ]);

        $idsCarriersPoints = [];
        foreach ($apaczkaCarriersConfig as $config) {
            if (!empty($config['points'])) {
                $idsCarriersPoints[] = $config['id_carrier'];
            }
        }

        $smarty->assign('apaczka_carriersPoints_json', json_encode($idsCarriersPoints));
        $smarty->assign('apaczka_carriersPoints', $idsCarriersPoints);

        return $this->display(__FILE__, '/views/templates/front/before_carrier.tpl');
    }
    
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    protected function getDefaultCarriersConfig()
    {
        $carriers = Carrier::getCarriers($this->context->language->id);
        $apaczkaCarriers = [];
        
        foreach ($carriers as $carrier) {
            $apaczkaCarriers[$carrier['id_reference']] = [
                'apaczkaName' => '-1',
                'cod' => 0,
                'points' => 0
            ];
        }
        
        return $apaczkaCarriers;
    }
}
