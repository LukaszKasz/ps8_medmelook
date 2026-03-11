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

{if $tagType != 'none'}
<!-- PD Google Analytycs 4 Pro - EVENTS CODE FOOTER -->
<script type="text/javascript" >

	{if ($tagType === 'product')}
	
		console.log('Fired up event GA4: view_item > Product page');
		gtag('event', 'view_item', {
			items: [{
			item_id: '{$content_ids|escape:'htmlall':'UTF-8'}',
			item_name: '{$content_name|escape:'htmlall':'UTF-8'}',
			coupon: '{$content_coupon|escape:'htmlall':'UTF-8'}',
			affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
			discount: {$content_discount|escape:'htmlall':'UTF-8'},
			item_brand: '{$content_manufacturer|escape:'htmlall':'UTF-8'}',
			item_category: '{$content_category|escape:'htmlall':'UTF-8'}',
			item_list_name: '{$item_list_name|escape:'htmlall':'UTF-8'}',
			item_list_id: '{$item_list_id|escape:'htmlall':'UTF-8'}',
			{if !empty($content_category2)}item_category2: '{$content_category2|escape:'htmlall':'UTF-8'}',{/if}
			{if !empty($content_category3)}item_category3: '{$content_category3|escape:'htmlall':'UTF-8'}',{/if}
			{if !empty($content_category4)}item_category4: '{$content_category3|escape:'htmlall':'UTF-8'}',{/if}
			{if !empty($content_category5)}item_category5: '{$content_category3|escape:'htmlall':'UTF-8'}',{/if}
			item_variant: '{$content_variant|escape:'htmlall':'UTF-8'}',
			item_list_name: '{$item_list_name|escape:'htmlall':'UTF-8'}',
			item_list_id: '{$item_list_name|escape:'htmlall':'UTF-8'}',
			list_position: 1,
			price: {$content_value|escape:'htmlall':'UTF-8'},
			currency: '{$currency|escape:'htmlall':'UTF-8'}',
			quantity: 1,
			}],
			value: {$content_value|escape:'htmlall':'UTF-8'}
		});

	{else if ($tagType === 'cart')}
		
		console.log('Fired up event GA4: view_cart');
		gtag('event', 'view_cart', {
			currency: '{$currency|escape:'htmlall':'UTF-8'}',
			items: [
				{foreach from=$content_products item=product name=products}
				{
					item_id: '{$product.content_ids|escape:'htmlall':'UTF-8'}',
					item_name: '{$product.name|escape:'htmlall':'UTF-8'}',
					coupon: '{$product.content_coupon|escape:'htmlall':'UTF-8'}',
					discount: {$product.discount|escape:'htmlall':'UTF-8'},
					index: {$smarty.foreach.products.index},
					item_list_name: '{$product.item_list_name|escape:'htmlall':'UTF-8'}',
					item_list_id: '{$product.item_list_id|escape:'htmlall':'UTF-8'}',
					affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
					item_brand: '{$product.manufacturer_name|escape:'htmlall':'UTF-8'}',
					item_category: '{$product.content_category|escape:'htmlall':'UTF-8'}',
					{if !empty($product.content_category2)}item_category2: '{$product.content_category2|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category3)}item_category3: '{$product.content_category3|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category4)}item_category4: '{$product.content_category4|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category5)}item_category5: '{$product.content_category5|escape:'htmlall':'UTF-8'}',{/if}
					item_variant: '{if isset($product.variant)}{$product.variant}{/if}',
					price: {$product.price_with_reduction_round|escape:'htmlall':'UTF-8'},
					currency: '{$currency|escape:'htmlall':'UTF-8'}',
					quantity: '{$product.cart_quantity|escape:'htmlall':'UTF-8'}'
				},
				{/foreach}
			],
			value: {$content_value|escape:'htmlall':'UTF-8'}
		});

	{else if ($tagType === 'order' || $tagType === 'order-opc')}
		
		console.log('Fired up event GA4: begin_checkout');
		gtag('event', 'begin_checkout', {
			coupon: '{$content_coupon|escape:'htmlall':'UTF-8'}',
			currency: '{$currency|escape:'htmlall':'UTF-8'}',
			items: [
				{foreach from=$content_products item=product name=products}
				{
					item_id: '{$product.content_ids|escape:'htmlall':'UTF-8'}',
					item_name: '{$product.name|escape:'htmlall':'UTF-8'}',
					coupon: '{$content_coupon|escape:'htmlall':'UTF-8'}',
					discount: {$product.discount|escape:'htmlall':'UTF-8'},
					index: {$smarty.foreach.products.index},
					item_list_name: '{$product.item_list_name|escape:'htmlall':'UTF-8'}',
					item_list_id: '{$product.item_list_id|escape:'htmlall':'UTF-8'}',
					affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
					item_brand: '{$product.manufacturer_name|escape:'htmlall':'UTF-8'}',
					item_category: '{$product.content_category|escape:'htmlall':'UTF-8'}',
					{if !empty($product.content_category2)}item_category2: '{$product.content_category2|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category3)}item_category3: '{$product.content_category3|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category4)}item_category4: '{$product.content_category4|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category5)}item_category5: '{$product.content_category5|escape:'htmlall':'UTF-8'}',{/if}
					item_variant: '{if isset($product.variant)}{$product.variant}{/if}',
					price: {$product.price_with_reduction_round|escape:'htmlall':'UTF-8'},
					currency: '{$currency|escape:'htmlall':'UTF-8'}',
					quantity: '{$product.cart_quantity|escape:'htmlall':'UTF-8'}'
				},
				{/foreach}
			],
			value: {$content_value|escape:'htmlall':'UTF-8'}
		});

	{else if ($tagType === 'category')}

		console.log('Fired up event GA4: view_item_list > Category products list page');
		gtag('event', 'view_item_list', {
			items: [
				{foreach from=$content_products item=product name=products}
				{
					item_id: '{$product.content_ids|escape:'htmlall':'UTF-8'}',
					item_name: '{$product.product_name|escape:'htmlall':'UTF-8'}',
					coupon: '{$product.content_coupon|escape:'htmlall':'UTF-8'}',
					discount: {$product.discount|escape:'htmlall':'UTF-8'},
					index: {$smarty.foreach.products.index},
					item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
					item_list_id: '{$page|escape:'htmlall':'UTF-8'}',
					affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
					item_brand: '{$product.manufacturer|escape:'htmlall':'UTF-8'}',
					item_category: '{$content_category|escape:'htmlall':'UTF-8'}',
					{if !empty($content_category2)}item_category2: '{$content_category2|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($content_category3)}item_category3: '{$content_category3|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($content_category4)}item_category4: '{$content_category4|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($content_category5)}item_category5: '{$content_category5|escape:'htmlall':'UTF-8'}',{/if}
					item_variant: '{if isset($product.variant)}{$product.variant}{/if}',
					price: {$product.price_with_reduction_round|escape:'htmlall':'UTF-8'},
					currency: '{$currency|escape:'htmlall':'UTF-8'}',
					quantity: 1
				},
				{/foreach}
			],
			item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
			item_list_id: '{$page|escape:'htmlall':'UTF-8'}'
		});

	
	{else if ($tagType === 'prices-drop')}

		console.log('Fired up event GA4: view_promotion > Products list prices drop page');
		gtag('event', 'view_promotion', {
			items: [
				{foreach from=$content_products item=product name=products}
				{
					item_id: '{$product.content_ids|escape:'htmlall':'UTF-8'}',
					item_name: '{$product.product_name|escape:'htmlall':'UTF-8'}',
					coupon: '{$product.content_coupon|escape:'htmlall':'UTF-8'}',
					discount: {$product.discount|escape:'htmlall':'UTF-8'},
					index: {$smarty.foreach.products.index},
					item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
					item_list_id: '{$page|escape:'htmlall':'UTF-8'}',
					affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
					item_brand: '{$product.manufacturer|escape:'htmlall':'UTF-8'}',
					item_category: '{$product.content_category|escape:'htmlall':'UTF-8'}',
					{if !empty($product.content_category2)}item_category2: '{$product.content_category2|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category3)}item_category3: '{$product.content_category3|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category4)}item_category4: '{$product.content_category4|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category5)}item_category5: '{$product.content_category5|escape:'htmlall':'UTF-8'}',{/if}
					item_variant: '{if isset($product.variant)}{$product.variant}{/if}',
					price: {$product.price_with_reduction_round|escape:'htmlall':'UTF-8'},
					currency: '{$currency|escape:'htmlall':'UTF-8'}',
					quantity: 1
				},
				{/foreach}
			],
			item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
			item_list_id: '{$page|escape:'htmlall':'UTF-8'}'
		});

		console.log('Fired up event GA4: view_item_list > Products list prices drop page');
		gtag('event', 'view_item_list', {
			items: [
				{foreach from=$content_products item=product name=products}
				{
					item_id: '{$product.content_ids|escape:'htmlall':'UTF-8'}',
					item_name: '{$product.product_name|escape:'htmlall':'UTF-8'}',
					coupon: '{$product.content_coupon|escape:'htmlall':'UTF-8'}',
					discount: {$product.discount|escape:'htmlall':'UTF-8'},
					index: {$smarty.foreach.products.index},
					item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
					item_list_id: '{$page|escape:'htmlall':'UTF-8'}',
					affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
					item_brand: '{$product.manufacturer|escape:'htmlall':'UTF-8'}',
					item_category: '{$product.content_category|escape:'htmlall':'UTF-8'}',
					{if !empty($product.content_category2)}item_category2: '{$product.content_category2|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category3)}item_category3: '{$product.content_category3|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category4)}item_category4: '{$product.content_category4|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category5)}item_category5: '{$product.content_category5|escape:'htmlall':'UTF-8'}',{/if}
					item_variant: '{if isset($product.variant)}{$product.variant}{/if}',
					price: {$product.price_with_reduction_round|escape:'htmlall':'UTF-8'},
					currency: '{$currency|escape:'htmlall':'UTF-8'}',
					quantity: 1
				},
				{/foreach}
			],
			item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
			item_list_id: '{$page|escape:'htmlall':'UTF-8'}'
		});

	{else if ($tagType === 'best-sales')}

		console.log('Fired up event GA4: view_item_list > Products best-sales list page');
		gtag('event', 'view_item_list', {
			items: [
				{foreach from=$content_products item=product name=products}
				{
					item_id: '{$product.content_ids|escape:'htmlall':'UTF-8'}',
					item_name: '{$product.product_name|escape:'htmlall':'UTF-8'}',
					coupon: '{$product.content_coupon|escape:'htmlall':'UTF-8'}',
					discount: {$product.discount|escape:'htmlall':'UTF-8'},
					index: {$smarty.foreach.products.index},
					item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
					item_list_id: '{$page|escape:'htmlall':'UTF-8'}',
					affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
					item_brand: '{$product.manufacturer|escape:'htmlall':'UTF-8'}',
					item_category: '{$product.content_category|escape:'htmlall':'UTF-8'}',
					{if !empty($product.content_category2)}item_category2: '{$product.content_category2|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category3)}item_category3: '{$product.content_category3|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category4)}item_category4: '{$product.content_category4|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category5)}item_category5: '{$product.content_category5|escape:'htmlall':'UTF-8'}',{/if}
					item_variant: '{if isset($product.variant)}{$product.variant}{/if}',
					price: {$product.price_with_reduction_round|escape:'htmlall':'UTF-8'},
					currency: '{$currency|escape:'htmlall':'UTF-8'}',
					quantity: 1
				},
				{/foreach}
			],
			item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
			item_list_id: '{$page|escape:'htmlall':'UTF-8'}'
		});

	{else if ($tagType === 'new-products')}

		console.log('Fired up event GA4: view_item_list > New products list page');
		gtag('event', 'view_item_list', {
			items: [
				{foreach from=$content_products item=product name=products}
				{
					item_id: '{$product.content_ids|escape:'htmlall':'UTF-8'}',
					item_name: '{$product.product_name|escape:'htmlall':'UTF-8'}',
					coupon: '{$product.content_coupon|escape:'htmlall':'UTF-8'}',
					discount: {$product.discount|escape:'htmlall':'UTF-8'},
					index: {$smarty.foreach.products.index},
					item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
					item_list_id: '{$page|escape:'htmlall':'UTF-8'}',
					affiliation: '{$http_referer|escape:'htmlall':'UTF-8'}',
					item_brand: '{$product.manufacturer|escape:'htmlall':'UTF-8'}',
					item_category: '{$product.content_category|escape:'htmlall':'UTF-8'}',
					{if !empty($product.content_category2)}item_category2: '{$product.content_category2|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category3)}item_category3: '{$product.content_category3|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category4)}item_category4: '{$product.content_category4|escape:'htmlall':'UTF-8'}',{/if}
					{if !empty($product.content_category5)}item_category5: '{$product.content_category5|escape:'htmlall':'UTF-8'}',{/if}
					item_variant: '{if isset($product.variant)}{$product.variant}{/if}',
					price: {$product.price_with_reduction_round|escape:'htmlall':'UTF-8'},
					currency: '{$currency|escape:'htmlall':'UTF-8'}',
					quantity: 1
				},
				{/foreach}
			],
			item_list_name: '{$content_name|escape:'htmlall':'UTF-8'}',
			item_list_id: '{$page|escape:'htmlall':'UTF-8'}'
		});

	{else if ($tagType === 'search')}

		console.log('Fired up event GA4: search');
		gtag('event', 'search', {
			search_term: '{$search_string|escape:'htmlall':'UTF-8'}',
		});

	{else if ($tagType === 'cms')}

		console.log('Fired up event: view_item');
		gtag('event', 'view_item', {
		  item_name: 'Cms page: {$content_name|escape:'htmlall':'UTF-8'}',
		  item_id: {$content_ids|escape:'htmlall':'UTF-8'}
		});

	{else if ($tagType === 'other')}

		console.log('Fired up event: view_item');
		gtag('event', 'view_item', {
		  item_name: '{$content_name|escape:'htmlall':'UTF-8'}',
		  item_id: '{$content_ids|escape:'htmlall':'UTF-8'}'
		});

	{/if}

	{if isset($account_created) && $account_created}
		console.log('Fired up event GA4: sign_up');
		gtag('event', 'sign_up', {
		  method: '{$registration_content_name|escape:'htmlall':'UTF-8'}'
		});
	{/if}

</script>
<!-- PD Google Analytycs 4 Pro - EVENTS CODE FOOTER -->
{/if}