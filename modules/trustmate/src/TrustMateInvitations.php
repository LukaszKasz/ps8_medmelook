<?php
/**
* 2007-2020 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2021 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  @version  Release: $Revision$
*  International Registered Trademark & Property of PrestaShop SA
*/

class TrustMateInvitations
{
    const STATUS_DISABLED = 0;
    const STATUS_FROM_MODULE_ONLY_COMPANY = 1; // deprecated
    const STATUS_FROM_MODULE = 2;
    const STATUS_FROM_API = 3;

    public static function isDispatchEnabled()
    {
        return (Configuration::get('TRUSTMATE_INVITATIONS') == self::STATUS_FROM_MODULE_ONLY_COMPANY)
            || (Configuration::get('TRUSTMATE_INVITATIONS') == self::STATUS_FROM_MODULE);
    }

    public static function isDispatchTriggeredBy($status)
    {
        return $status->id == Configuration::get('TRUSTMATE_DISPATCH_TRIGGERED_BY');
    }

    public static function setFormData(array $formData)
    {
        Configuration::updateValue('TRUSTMATE_INVITATIONS', $formData['invitations']);
        Configuration::updateValue('TRUSTMATE_DISPATCH_TRIGGERED_BY', $formData['trigger']);
    }

    public static function getDispatchData($order)
    {
        $customer = $order->getCustomer();

        $data = array(
            'name' => $customer->firstname,
            'email' => $customer->email,
            'orderNumber' => $order->reference,
            'uuid' => Configuration::get('TRUSTMATE_UUID'),
            'sourceType' => 'presta',
            'products' => self::getProductsDispatchData($order),
        );

        $data['signature'] = self::signData($data);

        return $data;
    }

    public static function getProductsDispatchData($order)
    {
        $context = Context::getContext();

        $result = array();
        foreach ($order->getProducts() as $orderProduct) {
            $product = new Product($orderProduct['id_product']);

            $gtin = null;
            if (count($product->getAttributeCombinations())) {
                $gtins = array();
                foreach ($product->getAttributeCombinations() as $combination) {
                    if ($combination['ean13']) {
                        $gtins[] = trim($combination['ean13']);
                    } elseif ($combination['upc']) {
                        $gtins[] = trim($combination['upc']);
                    }
                }

                $gtin = implode(';', array_unique($gtins));
            } else {
                if (!empty($orderProduct['product_ean13'])) {
                    $gtin = $product->ean13;
                }

                if (!$gtin && !empty($orderProduct['product_upc'])) {
                    $gtin = $product->upc;
                }
            }

            /**
             * Currently we do not support product variants (variant id available in $orderProduct['product_attribute_id']).
             * Product id works like group id here so we copy it for possibly use in future. Variant purchases overwrite
             * same product again and again, but all GTINs are preserved (see above).
             */
            $result[] = array(
                'id' => $product->id,
                'group_id' => $product->id,
                'name' => $product->name[$context->language->id],
                'product_url' => $context->link->getProductLink($product),
                'image_url' => self::getProductImageUrl($product, $context),
                'image_thumb_url' => self::getProductImageUrl($product, $context, 'small_default'),
                'priority' => $product->price,
                'sku' => (isset($product->reference) && $product->reference) ? $product->reference : null,
                'gtin' => $gtin,
                'category' => self::getFullCategoryPath($product->id_category_default),
                'source_type' => 'presta',
            );
        }

        return $result;
    }

    public static function getProductImageUrl($product, $context, $type = null)
    {
        if ($cover = Product::getCover($product->id)) {
            $name = $product->name[$context->language->id];

            return $context->link->getImageLink($name, $cover['id_image'], $type);
        }

        return null;
    }

    public static function signData(array $data)
    {
        return md5($data['email'] . $data['uuid']);
    }

    public static function getFullCategoryPath($product_category_id)
    {
        $full_path = '';
        $category = new Category($product_category_id);
        if ($category) {
            $segments = array();
            foreach ($category->getParentsCategories() as $parentCategory) {
                array_unshift($segments, $parentCategory['name']);
            }

            $full_path = implode(' / ', $segments);
        }

        return $full_path;
    }
}
