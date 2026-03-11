<?php
/**
* 2012-2016 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Dynamic Ads Feed Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2016 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Facebook Dynamic Ads Feed Pro - PrestaShop 1.5.x and 1.6.x Module
* @version   1.0.2
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      27-11-2016
*/
class AdminProductsController extends AdminProductsControllerCore
{
    /*
    * module: pdgooglemerchantcenterpro
    * date: 2024-03-14 16:27:40
    * version: 2.6.3
    */
    public function initProcess()
    {
        if (Tools::isSubmit('in_google_shoppingproduct')) {
            $id_product = (int)Tools::getValue('id_product');
            $context = Context::getContext();
            $id_shop = $context->language->id;
            $id_lang = $context->shop->id;
            if (is_numeric($id_product)) {
                $obj = new Product($id_product, false, $id_lang, $id_shop);
                $obj->in_google_shopping = $obj->in_google_shopping ? 0 : 1;
                $obj->update();
            }
        }
    
        parent::initProcess();
    }
}