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

class CheckoutDeliveryStep extends CheckoutDeliveryStepCore
{
    public function handleRequest(array $requestParams = array())
    {
        if (Module::isEnabled("apaczka") && !empty($requestParams['delivery_option'])) {
            $indexApaczka = $requestParams['delivery_option'][array_keys($requestParams['delivery_option'])[0]];
            $indexApaczka = str_replace(",", "", $indexApaczka);
            
            if (!empty($requestParams['apaczka_supplier'][$indexApaczka])) {
                $qryPoint = ",apaczka_point=NULL";
                if (!empty($requestParams['apaczka_delivery_point'][$indexApaczka])) {
                    $qryPoint = ',apaczka_point="'. pSQL($requestParams['apaczka_delivery_point'][$indexApaczka]).'" ';
                }
                
                Db::getInstance()->execute(
                    "UPDATE "._DB_PREFIX_.'cart 
                        SET apaczka_supplier="'. pSQL($requestParams['apaczka_supplier'][$indexApaczka]).'"'
                        .$qryPoint. '
                    WHERE id_cart='.$this->getCheckoutSession()->getCart()->id
                );
            }
        }
        
        parent::handleRequest($requestParams);
    }
}
