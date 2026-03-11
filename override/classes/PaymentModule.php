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
abstract class PaymentModule extends PaymentModuleCore
{
    /**
     * Validate an order in database
     * Function called from a payment module.
     *
     * @param int $id_cart
     * @param int $id_order_state
     * @param float $amount_paid Amount really paid by customer (in the default currency)
     * @param string $payment_method Payment method (eg. 'Credit card')
     * @param string|null $message Message to attach to order
     * @param array $extra_vars
     * @param null $currency_special
     * @param bool $dont_touch_amount
     * @param bool $secure_key
     * @param Shop $shop
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    /*
    * module: apaczka
    * date: 2026-03-11 18:15:58
    * version: 1.4.0
    */
    public function validateOrder(
        $id_cart,
        $id_order_state,
        $amount_paid,
        $payment_method = 'Unknown',
        $message = null,
        $extra_vars = [],
        $currency_special = null,
        $dont_touch_amount = false,
        $secure_key = false,
        Shop $shop = null,
        $order_reference = null
    ) {
        if (Module::isEnabled("apaczka")) {
            $this->apaczkaCarrierValidate($id_cart);
        }
        
        $result = parent::validateOrder(
            $id_cart,
            $id_order_state,
            $amount_paid,
            $payment_method,
            $message,
            $extra_vars,
            $currency_special,
            $dont_touch_amount,
            $secure_key,
            $shop,
            $order_reference
        );
        if (!$result) {
            return false;
        }
        
        if (Module::isEnabled("apaczka")) {
            $this->apaczkaOrderUpdate($id_cart);
        }
    
        return $result;
    }
    /*
    * module: apaczka
    * date: 2026-03-11 18:15:58
    * version: 1.4.0
    */
    protected function apaczkaOrderUpdate($idCart)
    {
        if ($this->currentOrder) {
            $apaczkaCart = Db::getInstance()->getRow(
                "SELECT apaczka_supplier, apaczka_point FROM "._DB_PREFIX_.'cart WHERE id_cart='.(int)$idCart
            );
            if ($apaczkaCart && (!empty($apaczkaCart['apaczka_supplier']) || !empty($apaczkaCart['apaczka_point']))) {
                Db::getInstance()->execute(
                    "UPDATE "._DB_PREFIX_.'orders 
                    SET 
                        apaczka_supplier="'.pSQL($apaczkaCart['apaczka_supplier']).'", 
                        apaczka_point="'.pSQL($apaczkaCart['apaczka_point']).'" 
                        WHERE id_order='.$this->currentOrder
                );
            }
        }
    }
    
    /*
    * module: apaczka
    * date: 2026-03-11 18:15:58
    * version: 1.4.0
    */
    protected function apaczkaCarrierValidate($idCart)
    {
        $apaczkaCarriers = unserialize(Configuration::get('APACZKA_CARRIERS'));
        if (empty($apaczkaCarriers)) {
            return true;
        }
        $apaczkaCart = Db::getInstance()->getRow(
            "SELECT * FROM "._DB_PREFIX_.'cart WHERE id_cart='.(int)$idCart
        );
        $carrier = new Carrier($apaczkaCart['id_carrier']);
        if (!Validate::isLoadedObject($carrier)) {
        }
        $idReference = $carrier->id_reference;
        return true;
    }
}