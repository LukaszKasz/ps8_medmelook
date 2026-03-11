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

class Product extends ProductCore
{
    public $in_google_shopping;
    public $product_name_google_shopping;
    public $product_short_desc_google_shopping;

    public $custom_label_0;
    public $custom_label_1;
    public $custom_label_2;
    public $custom_label_3;
    public $custom_label_4;


    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {
        if (Configuration::get('PD_GMCP_ASSIGN_ON_ADD')) {
            $this->in_google_shopping = 1;
        }
        
        self::$definition['fields']['in_google_shopping'] = array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool');
        self::$definition['fields']['product_name_google_shopping'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 128);
        self::$definition['fields']['product_short_desc_google_shopping'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => false);

        self::$definition['fields']['custom_label_0'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 128);
        self::$definition['fields']['custom_label_1'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100);
        self::$definition['fields']['custom_label_2'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100);
        self::$definition['fields']['custom_label_3'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100);
        self::$definition['fields']['custom_label_4'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100);

        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}
