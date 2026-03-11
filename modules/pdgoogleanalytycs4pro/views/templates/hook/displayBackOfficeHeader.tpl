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
* @version   1.0.1
* @date      01-05-2021
*}

{if isset($pd_google_analytics_id)}
<!-- START > PD Google Analytycs 4.0 1.6.x and 1.7.x Module -->
<script async data-keepinline="true" src="https://www.googletagmanager.com/gtag/js?id={$pd_google_analytics_id|escape:'htmlall':'UTF-8'}"></script>

{literal}
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', '{/literal}{$pd_google_analytics_id|escape:'htmlall':'UTF-8'}{literal}', {'send_page_view': {/literal}{$pd_google_analytics_spv|escape:'htmlall':'UTF-8'}{literal}});
	{/literal}{if isset($pd_google_analytics_id2) && !empty($pd_google_analytics_id2)}{literal}gtag('config', '{/literal}{$pd_google_analytics_id2|escape:'htmlall':'UTF-8'}{literal}', {'send_page_view': {/literal}{$pd_google_analytics_spv|escape:'htmlall':'UTF-8'}{literal}});
 	{/literal}{/if}{literal}gtag('set', {currency: '{/literal}{$pd_google_analytics_currency|escape:'htmlall':'UTF-8'}{literal}'});
	gtag('set', {country: '{/literal}{$pd_google_analytics_country|escape:'htmlall':'UTF-8'}{literal}'});
	{/literal}{if isset($pd_google_analytics_id_aw) && !empty($pd_google_analytics_id_aw)}{literal}gtag('config', '{/literal}{$pd_google_analytics_id_aw|escape:'htmlall':'UTF-8'}{literal}');
 	{/literal}{/if}{literal}
</script>
{/literal}

<!-- END > PD Google Analytycs 4.0 1.6.x and 1.7.x Module -->
{/if}
