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
<!-- START > PD Google Analytycs 4.0 Module -->
<script async data-keepinline="true" src="https://www.googletagmanager.com/gtag/js?id={$pd_google_analytics_id nofilter}"></script>

{literal}
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', '{/literal}{$pd_google_analytics_id nofilter}{literal}', {
		'send_page_view': {/literal}{$pd_google_analytics_spv nofilter}{if isset($pd_google_analytics_aec) && $pd_google_analytics_aec},
		{literal}'allow_enhanced_conversions': {/literal}{$pd_google_analytics_aec nofilter}{/if}{literal}});
	{/literal}{if isset($pd_google_analytics_id2) && !empty($pd_google_analytics_id2)}{literal}gtag('config', '{/literal}{$pd_google_analytics_id2 nofilter}{literal}', {
		'send_page_view': {/literal}{$pd_google_analytics_spv nofilter}{if isset($pd_google_analytics_aec) && $pd_google_analytics_aec},
		{literal}'allow_enhanced_conversions': {/literal}{$pd_google_analytics_aec nofilter}{/if}{literal}});
 	{/literal}{/if}{literal}gtag('set', {'currency': {/literal}"{$pd_google_analytics_currency_iso nofilter}"{literal}});
	gtag('set', {'country': {/literal}"{$pd_google_analytics_country_iso nofilter}"{literal}});
	{/literal}{if !empty($pd_google_analytics_id_aw) && !empty($pd_google_analytics_id_aw_label)}{literal}gtag('config', '{/literal}{$pd_google_analytics_id_aw nofilter}{literal}');
 	{/literal}{/if}

 	{if isset($pd_google_analytics_aec) && $pd_google_analytics_aec != 'false' && !empty($pd_google_analytics_city)}
	gtag('set', 'user_data', {
		{if !empty($pd_google_analytics_email)}{literal}"email": "{/literal}{$pd_google_analytics_email nofilter}",{/if}
		{if !empty($pd_google_analytics_phone)}{literal}"phone_number": "{/literal}{$pd_google_analytics_phone nofilter}",{/if}
		{literal}"address": {{/literal}
			{if !empty($pd_google_analytics_firstname)}{literal}"first_name": "{/literal}{$pd_google_analytics_firstname nofilter}",{/if}
			{if !empty($pd_google_analytics_lastname)}{literal}"last_name": "{/literal}{$pd_google_analytics_lastname nofilter}",{/if}
			{if !empty($pd_google_analytics_street)}{literal}"street": "{/literal}{$pd_google_analytics_street nofilter}",{/if}
			{if !empty($pd_google_analytics_city)}{literal}"city": "{/literal}{$pd_google_analytics_city nofilter}",{/if}
			{if !empty($pd_google_analytics_postcode)}{literal}"postal_code": "{/literal}{$pd_google_analytics_postcode nofilter}",{/if}
			{if !empty($pd_google_analytics_country)}{literal}"country": "{/literal}{$pd_google_analytics_country nofilter}"{/if}
		}
	});
	{/if}
</script>
<!-- END > PD Google Analytycs 4.0 Module -->
{/if}
