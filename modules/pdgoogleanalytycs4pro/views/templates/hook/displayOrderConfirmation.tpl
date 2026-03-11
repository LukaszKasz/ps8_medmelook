{*
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
* @version   1.0.2
* @date      01-05-2021
*}

<!-- PD Google Analytycs 4 Pro - EVENT > purchase -->
<script type="text/javascript" data-keepinline="true">
    console.log('Fired up event GA4: purchase');
    gtag('event', 'purchase', {
        affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
        coupon: '{$content_coupon|escape:'htmlall':'UTF-8'}',
        currency: '{$currency|escape:'htmlall':'UTF-8'}',
        items: [
            {foreach from=$content_products item=product name=products}
            {
                item_id: '{$product.content_ids|escape:'htmlall':'UTF-8'}',
                item_name: '{$product.product_name|escape:'htmlall':'UTF-8'}',
                coupon: '{$product.content_coupon|escape:'htmlall':'UTF-8'}',
                discount: {$product.discount|escape:'htmlall':'UTF-8'},
                index: {$smarty.foreach.products.index},
                affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
                item_brand: '{$product.manufacturer|escape:'htmlall':'UTF-8'}',
                item_category: '{$product.content_category|escape:'htmlall':'UTF-8'}',
                {if !empty($product.content_category2)}item_category2: '{$product.content_category2|escape:'htmlall':'UTF-8'}',{/if}
                {if !empty($product.content_category3)}item_category3: '{$product.content_category3|escape:'htmlall':'UTF-8'}',{/if}
                {if !empty($product.content_category4)}item_category4: '{$product.content_category4|escape:'htmlall':'UTF-8'}',{/if}
                {if !empty($product.content_category5)}item_category5: '{$product.content_category5|escape:'htmlall':'UTF-8'}',{/if}
                item_variant: '{if isset($product.variant)}{$product.variant}{/if}',
                price: {$product.price|escape:'htmlall':'UTF-8'},
                currency: '{$currency|escape:'htmlall':'UTF-8'}',
                quantity: '{$product.product_quantity|escape:'htmlall':'UTF-8'}'
            },
            {/foreach}
        ],
        transaction_id: '{$content_transaction_id|escape:'htmlall':'UTF-8'}',
        shipping: {$content_shipping|escape:'htmlall':'UTF-8'},
        value: {$content_value|escape:'htmlall':'UTF-8'},
        tax: {$content_tax|escape:'htmlall':'UTF-8'}
    });


{if !empty($pd_google_analytics_id_aw) && !empty($pd_google_analytics_id_aw_label)}
    console.log('Fired up event GTAG: ADS / Adwords conversion');
    gtag('event', 'conversion', {
        'send_to': '{$pd_google_analytics_id_aw|escape:'htmlall':'UTF-8'}/{$pd_google_analytics_id_aw_label|escape:'htmlall':'UTF-8'}',
        'value': '{$content_value|escape:'htmlall':'UTF-8'}',
        'currency': '{$currency|escape:'htmlall':'UTF-8'}',
        'transaction_id': '{$content_transaction_id|escape:'htmlall':'UTF-8'}'
    });
{/if}
</script>
<!-- PD Google Analytycs 4 Pro - EVENT > purchase -->

