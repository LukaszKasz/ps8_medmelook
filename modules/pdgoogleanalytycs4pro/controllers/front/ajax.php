<?php
/**
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Analytycs 4.0 Pro 1.6.x and 1.7.x Module © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek @ PrestaDev.pl
* @license   Do not edit, modify or copy this file, if you wish to customize it, contact us at info@prestadev.pl.
* @link      http://prestadev.pl
* @package   PD Google Analytycs 4.0 Pro 1.6.x and 1.7.x Module
* @version   1.0.1
* @date      01-05-2021
*/

class PdGoogleAnalytycs4ProAjaxModuleFrontController extends ModuleFrontController
{
    private $name = '';

    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
        $this->name = 'pdgoogleanalytycs4pro';
    }

    public function displayAjax()
    {
        $module = new PdGoogleAnalytycs4Pro();

        if (Tools::getValue('secure_key') == $module->secure_key) {
            $id_product = (int)Tools::getValue('product_id');
            $id_lang = (int)Context::getContext()->language->id;
            $id_shop = (int)Context::getContext()->shop->id;
            $currency_iso = (string)Context::getContext()->currency->iso_code;
            $action = (string)Tools::getValue('action');
            $cart_rules = $module->getCartRuleWithCoupon();

            $cn = $module::getControlerName();

            if ($action == 'updateCart') {
                $id_product_attribute = Tools::getValue('product_id_product_attribute');

                $product = new Product((int)$id_product, false, $id_lang);
                $variant = '';
                $attribute_combination_resume = false;
                if ($id_product_attribute) {
                    $attribute_combination_resume = $product->getAttributeCombinationsById($id_product_attribute, $id_lang, true);
                    if ($attribute_combination_resume) {
                        foreach ($attribute_combination_resume as $acr) {
                            $variant .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $variant = mb_substr($variant, 0, -3);
                    }
                }
                if (!empty($variant)) {
                    $product_name = addslashes($product->name.' ('.$variant.')');
                } else {
                    $product_name = addslashes($product->name);
                }

                $price = Product::getPriceStatic($id_product, true, $id_product_attribute, 6, null, false, true);
                $price_old = Product::getPriceStatic($id_product, true, $id_product_attribute, 6, null, false, false);

                $discount = 0;
                if ($price_old > $price) {
                    $discount = $price_old - $price;
                }

                $content_ids = $module->getProductIdStringByType($product, $id_product_attribute);
                $content_category = explode('/', $module->getCategoryPath($product->id_category_default));
                $content_category = array_map('trim', $content_category);

                $data = array(
                    'item_list_id' => $cn,
                    'item_list_name' => $cn,
                    'content_ids' => $content_ids,
                    'content_category' => isset($content_category[0]) ? addslashes($content_category[0]) : '',
                    'content_category2' => isset($content_category[1]) ? addslashes($content_category[1]) : '',
                    'content_category3' => isset($content_category[2]) ? addslashes($content_category[2]) : '',
                    'content_category4' => isset($content_category[3]) ? addslashes($content_category[3]) : '',
                    'content_category5' => isset($content_category[4]) ? addslashes($content_category[4]) : '',

                    'content_name' => $product_name,
                    'content_value' => Tools::ps_round($price, 2),
                    'content_discount' => Tools::ps_round($discount, 2),
                    'content_variant' => $variant,
                    'content_manufacturer' => Manufacturer::getNameById($product->id_manufacturer) ? Manufacturer::getNameById($product->id_manufacturer) : '',
                    'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                    'currency' => $currency_iso,
                    'http_referer' => addslashes($module->http_referer),
                );
                die(json_encode($data));
            } elseif ($action == 'productClick') {
                $id_product_attribute = Tools::getValue('product_id_product_attribute');

                $product = new Product((int)$id_product, false, $id_lang);
                $variant = '';
                $attribute_combination_resume = false;
                if ($id_product_attribute) {
                    $attribute_combination_resume = $product->getAttributeCombinationsById($id_product_attribute, $id_lang, true);
                    if ($attribute_combination_resume) {
                        foreach ($attribute_combination_resume as $acr) {
                            $variant .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $variant = mb_substr($variant, 0, -3);
                    }
                }
                if (!empty($variant)) {
                    $product_name = addslashes($product->name.' ('.$variant.')');
                } else {
                    $product_name = addslashes($product->name);
                }

                $price = Product::getPriceStatic($id_product, true, $id_product_attribute, 6, null, false, true);
                $price_old = Product::getPriceStatic($id_product, true, $id_product_attribute, 6, null, false, false);

                $discount = 0;
                if ($price_old > $price) {
                    $discount = $price_old - $price;
                }

                $content_category = explode('/', $module->getCategoryPath($product->id_category_default));
                $content_category = array_map('trim', $content_category);

                $content_ids = $module->getProductIdStringByType($product, $id_product_attribute);
                $data = array(
                    'item_list_id' => $cn,
                    'item_list_name' => $cn,
                    'content_ids' => $content_ids,
                    'content_category' => isset($content_category[0]) ? addslashes($content_category[0]) : '',
                    'content_category2' => isset($content_category[1]) ? addslashes($content_category[1]) : '',
                    'content_category3' => isset($content_category[2]) ? addslashes($content_category[2]) : '',
                    'content_category4' => isset($content_category[3]) ? addslashes($content_category[3]) : '',
                    'content_category5' => isset($content_category[4]) ? addslashes($content_category[4]) : '',
                    'content_name' => $product_name,
                    'content_value' => Tools::ps_round($price, 2),
                    'content_discount' => Tools::ps_round($discount, 2),
                    'content_variant' => $variant,
                    'content_manufacturer' => Manufacturer::getNameById($product->id_manufacturer) ? Manufacturer::getNameById($product->id_manufacturer) : '',
                    'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                    'currency' => $currency_iso,
                    'http_referer' => addslashes($module->http_referer),
                );
                die(json_encode($data));
            } elseif ($action == 'updateProduct') {
                $groups = Tools::getValue('attributes_groups');
                $id_product_attribute = (int)self::getIdProductAttributeByIdAttributes((int)$id_product, $groups);
                $product = new Product((int)$id_product, false, $id_lang);
                $price = Product::getPriceStatic($id_product, true, $id_product_attribute, 2);
                $content_category = explode('/', $module->getCategoryPath($product->id_category_default));
                $content_category = array_map('trim', $content_category);
                $content_ids = $module->getProductIdStringByType($product, $id_product_attribute);

                $variant = '';
                $attribute_combination_resume = false;
                if ($id_product_attribute) {
                    $attribute_combination_resume = $product->getAttributeCombinationsById($id_product_attribute, $id_lang, true);
                    if ($attribute_combination_resume) {
                        foreach ($attribute_combination_resume as $acr) {
                            $variant .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                        }
                        $variant = mb_substr($variant, 0, -3);
                    }
                }


                if (!empty($variant)) {
                    $product_name = addslashes($product->name.' ('.$variant.')');
                } else {
                    $product_name = addslashes($product->name);
                }

                $price = Product::getPriceStatic($id_product, true, $id_product_attribute, 6, null, false, true);
                $price_old = Product::getPriceStatic($id_product, true, $id_product_attribute, 6, null, false, false);

                $discount = 0;
                if ($price_old > $price) {
                    $discount = $price_old - $price;
                }

                $data = array(
                    'item_list_id' => $cn,
                    'item_list_name' => $cn,
                    'content_ids' => $content_ids,
                    'content_category' => isset($content_category[0]) ? addslashes($content_category[0]) : '',
                    'content_category2' => isset($content_category[1]) ? addslashes($content_category[1]) : '',
                    'content_category3' => isset($content_category[2]) ? addslashes($content_category[2]) : '',
                    'content_category4' => isset($content_category[3]) ? addslashes($content_category[3]) : '',
                    'content_category5' => isset($content_category[4]) ? addslashes($content_category[4]) : '',
                    'content_name' => $product_name,
                    'content_value' => Tools::ps_round($price, 2),
                    'content_discount' => Tools::ps_round($discount, 2),
                    'content_variant' => $variant,
                    'content_manufacturer' => Manufacturer::getNameById($product->id_manufacturer) ? Manufacturer::getNameById($product->id_manufacturer) : '',
                    'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                    'http_referer' => addslashes($module->http_referer),
                );

                die(json_encode($data));
            } elseif ($action == 'addDeliveryInfo') {
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $id_carrier = (int)Tools::getValue('id_carrier');

                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }
                    $cart_products = $cart->getProducts();

                    $cn = $module::getControlerName();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $module->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';

                        $cp['content_ids'] = $module->getProductIdStringByType($cp);

                        $cp['content_coupon'] = ($cart_rules && is_array($cart_rules)) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);
                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }
                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);

                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }

                    $carrier_name = '';
                    if ($id_carrier = Tools::getValue('id_carrier')) {
                        $carriers = $module->getCarriersArray();
                        $carrier_name = $carriers[$id_carrier];
                    }

                    $this->context->smarty->assign(array(
                        'content_value' => $value,
                        'content_products' => $cart_products,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'currency' => $currency_iso,
                        'carrier_name' => addslashes($carrier_name),
                        'http_referer' => addslashes($module->http_referer),
                    ));

                    $html = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/addDeliveryInfo.tpl');

                    die(json_encode($html));
                }
            } elseif ($action == 'addPaymentInfo') {
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }
                if (isset($cart->id)) {
                    $id_carrier = (int)Tools::getValue('id_carrier');
                    $value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                    $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                    if ($discount_cart > 0) {
                        $value -= $discount_cart;
                    }

                    $cart_products = $cart->getProducts();

                    foreach ($cart_products as &$cp) {
                        $content_category = explode('/', $module->getCategoryPath($cp['id_category_default']));
                        $content_category = array_map('trim', $content_category);

                        $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                        $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                        $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                        $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                        $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                        $cp['content_ids'] = $module->getProductIdStringByType($cp);
                        $cp['content_coupon'] = ($cart_rules && is_array($cart_rules)) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                        $discount = 0;
                        $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                        if ($discount_cart > 0) {
                            $discount += $discount_cart;
                        }
                        $cp['discount'] = Tools::ps_round($discount, 2);
                        $product = new Product($cp['id_product'], false, $id_lang);
                        $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                        if ($attribute_combination_resume) {
                            $cp['variant'] = '';
                            foreach ($attribute_combination_resume as $acr) {
                                $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                            }
                            $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                        }

                        if (!empty($cp['variant'])) {
                            $cp['name'] = addslashes($product->name.' ('.$cp['variant'].')');
                        } else {
                            $cp['name'] = addslashes($product->name);
                        }
                        $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);

                        $cp['item_list_id'] = $cn;
                        $cp['item_list_name'] = $cn;
                    }

                    $payment_name = '';
                    if ($payment_module = Tools::getValue('payment_module')) {
                        if (($payment_module_instance = Module::getInstanceByName($payment_module))) {
                            $payment_name = $payment_module_instance->displayName;
                        }
                    }

                    $this->context->smarty->assign(array(
                        'content_value' => $value,
                        'content_products' => $cart_products,
                        'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                        'currency' => $currency_iso,
                        'payment_name' => addslashes($payment_name),
                        'http_referer' => addslashes($module->http_referer),
                    ));

                    $html = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/addPaymentInfo.tpl');

                    die(json_encode($html));
                }

            } else if ($action == 'confirmOrder') {
                $cart = Context::getContext()->cart;
                if (!($cart instanceof Cart)) {
                    return;
                }



                if (isset($cart->id)) {
                    if ($module->transaction_send_type == 3) {
                        if (!empty($module->ga4_id)) {
                            $order_value = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
                            $discount_cart = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
                            if ($discount_cart > 0) {
                                $order_value -= $discount_cart;
                            }
                            $total_excluding_tax = $cart->getOrderTotal(false);
                            $total_including_tax = $cart->getOrderTotal(true);
                            $order_tax = $total_including_tax - $total_excluding_tax;
                            $shipping_cost = $cart->getTotalShippingCost(null, true);

                            $id_lang = (int)$this->context->language->id;
                            $id_shop = (int)$this->context->shop->id;
                            $id_currency = $cart->id_currency;
                            $currency = new Currency($id_currency);
                            $currency_iso = $currency->iso_code;

                            $obj = new PdGA4PModelCart($cart->id);
                            if (isset($obj->id_cart) && !$obj->order_send) {

                                $cart_rules = $module->getCartRuleWithCoupon((int)$cart->id);
                                $cart_products = $cart->getProducts();

                                foreach ($cart_products as &$cp) {
                                    $content_category = explode('/', $module->getCategoryPath($cp['id_category_default']));
                                    $content_category = array_map('trim', $content_category);

                                    $cp['content_category'] = isset($content_category[0]) ? addslashes($content_category[0]) : '';
                                    $cp['content_category2'] = isset($content_category[1]) ? addslashes($content_category[1]) : '';
                                    $cp['content_category3'] = isset($content_category[2]) ? addslashes($content_category[2]) : '';
                                    $cp['content_category4'] = isset($content_category[3]) ? addslashes($content_category[3]) : '';
                                    $cp['content_category5'] = isset($content_category[4]) ? addslashes($content_category[4]) : '';
                                    $cp['content_ids'] = $module->getProductIdStringByType($cp);
                                    $cp['content_coupon'] = ($cart_rules && is_array($cart_rules)) ? $cart_rules['name'].' - '.$cart_rules['code'] : '';
                                    $discount = 0;
                                    $discount = $cp['price_without_reduction'] - $cp['price_wt'];
                                    if ($discount_cart > 0) {
                                        $discount += $discount_cart;
                                    }
                                    $cp['discount'] = Tools::ps_round($discount, 2);
                                    $product = new Product($cp['id_product'], false, $id_lang);
                                    $attribute_combination_resume = $product->getAttributeCombinationsById($cp['id_product_attribute'], $id_lang, true);
                                    if ($attribute_combination_resume) {
                                        $cp['variant'] = '';
                                        foreach ($attribute_combination_resume as $acr) {
                                            $cp['variant']  .=  $acr['group_name'].': '.$acr['attribute_name'].' - ';
                                        }
                                        $cp['variant'] = mb_substr($cp['variant'], 0, -3);
                                    }

                                    if (!empty($cp['variant'])) {
                                        $cp['product_name'] = addslashes($product->name.' ('.$cp['variant'].')');
                                    } else {
                                        $cp['product_name'] = addslashes($product->name);
                                    }
                                    $cp['price_with_reduction_round'] = Tools::ps_round($cp['price_wt'], 2);

                                    $cp['price'] = Tools::ps_round($cp['price'], 2);

                                    $cp['item_list_id'] = $cn;

                                    $cp['product_quantity'] = $cp['cart_quantity'];
                                    $cp['item_list_name'] = $cn;
                                    $cp['manufacturer'] =  $product->id_manufacturer ? addslashes(Manufacturer::getNameById($product->id_manufacturer)) : '';
                                }

                                $this->context->smarty->assign(array(
                                    'pd_google_analytics_id_aw' => $module->ga4_id_aw,
                                    'pd_google_analytics_id_aw_label' => $module->ga4_id_aw_label,
                                    'content_transaction_id' =>  $cart->id,
                                    'content_shipping' => Tools::ps_round($shipping_cost, 2),
                                    'content_value' => Tools::ps_round($order_value, 2),
                                    'content_tax' => Tools::ps_round($order_tax, 2),
                                    'content_products' => $cart_products,
                                    'content_coupon' => sizeof($cart_rules) ? $cart_rules['name'].' - '.$cart_rules['code'] : '',
                                    'http_referer' => addslashes($module->http_referer),
                                    'currency' => $currency_iso,
                                ));

                                $obj->order_send = 1;
                                $obj->date_upd = date('Y-m-d H:i:s');
                                $obj->update();

                                $html = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/displayOrderConfirmation.tpl');
                                die(json_encode($html));
                            }
                        }
                    }
                }
            }
        }
    }

    public static function getIdProductAttributeByIdAttributes($idProduct, $idAttributes, $findBest = false)
    {
        $idProduct = (int) $idProduct;

        if (!is_array($idAttributes) && is_numeric($idAttributes)) {
            $idAttributes = [(int) $idAttributes];
        }

        if (!is_array($idAttributes) || empty($idAttributes)) {
            throw new PrestaShopException(sprintf('Invalid parameter $idAttributes with value: "%s"', print_r($idAttributes, true)));
        }

        $idAttributesImploded = implode(',', array_map('intval', $idAttributes));
        $idProductAttribute = Db::getInstance()->getValue(
            '
            SELECT
                pac.`id_product_attribute`
            FROM
                `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
            WHERE
                pa.id_product = ' . $idProduct . '
                AND pac.id_attribute IN (' . $idAttributesImploded . ')
            GROUP BY
                pac.`id_product_attribute`
            HAVING
                COUNT(pa.id_product) = ' . count($idAttributes)
        );

        if ($idProductAttribute === false && $findBest) {
            //find the best possible combination
            //first we order $idAttributes by the group position
            $orderred = [];
            $result = Db::getInstance()->executeS(
                '
                SELECT
                    a.`id_attribute`
                FROM
                    `' . _DB_PREFIX_ . 'attribute` a
                    INNER JOIN `' . _DB_PREFIX_ . 'attribute_group` g ON a.`id_attribute_group` = g.`id_attribute_group`
                WHERE
                    a.`id_attribute` IN (' . $idAttributesImploded . ')
                ORDER BY
                    g.`position` ASC'
            );

            foreach ($result as $row) {
                $orderred[] = $row['id_attribute'];
            }

            while ($idProductAttribute === false && count($orderred) > 1) {
                array_pop($orderred);
                $idProductAttribute = Db::getInstance()->getValue(
                    '
                    SELECT
                        pac.`id_product_attribute`
                    FROM
                        `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                        INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                    WHERE
                        pa.id_product = ' . (int) $idProduct . '
                        AND pac.id_attribute IN (' . implode(',', array_map('intval', $orderred)) . ')
                    GROUP BY
                        pac.id_product_attribute
                    HAVING
                        COUNT(pa.id_product) = ' . count($orderred)
                );
            }
        }

        return $idProductAttribute;
    }
}
