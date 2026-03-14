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
* If you did not receive a copy of this file and are unable to obtain it
* through the world-wide-web, please send an email to license@prestashop.com
* so we can send you a copy immediately.
*/

class ApaczkaAjaxModuleFrontController extends ModuleFrontController
{
    public $ajax = true;

    public function displayAjax()
    {
        $cart = $this->context->cart;

        if (!$cart || !$cart->id) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'message' => 'Cart not found',
            ]));

            return;
        }

        $supplier = trim((string) Tools::getValue('supplier', ''));
        $point = trim((string) Tools::getValue('point', ''));

        Db::getInstance()->update(
            'cart',
            [
                'apaczka_supplier' => pSQL($supplier),
                'apaczka_point' => pSQL($point),
            ],
            'id_cart = ' . (int) $cart->id
        );

        $this->ajaxRender(json_encode([
            'success' => true,
            'supplier' => $supplier,
            'point' => $point,
            'id_cart' => (int) $cart->id,
        ]));
    }
}
