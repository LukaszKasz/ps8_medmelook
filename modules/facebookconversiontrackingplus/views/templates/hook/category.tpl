{*
 * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @version
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 *
 * **************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * **************************************************
 *
 *}
<!-- Facebook ViewCategory event tracking -->
<script type="text/javascript">
    if (typeof window.pp_vc === 'undefined') {
        var pp_vc = false;
    }
    var combination = '';
    {* {if isset($top_sell_ids) && $top_sell_ids} *}
    var content_ids_list = {$content_ids nofilter}; {* Can't escape, JSON encoded *}

    fctp_categoryView(10);

    function fctp_categoryView(max_tries) {
        if (typeof jQuery == 'undefined' || typeof fbq != 'function') {
            setTimeout(function() {
                fctp_categoryView(max_tries-1)
            },500);
        } else {
            var edata = {
                content_name: '{$entityname nofilter}',
            };
            edata.value = {$category_value|floatval};
            edata.currency = '{$fctp_currency|escape:'htmlall':'UTF-8'}';
            edata.content_type = 'product';
            edata.content_category = '{$entityname nofilter}';
            edata.content_ids = content_ids_list;
            {if isset($fpf_id)}edata.product_catalog_id = '{$fpf_id|escape:'htmlall':'UTF-8'}';{/if}
            //console.log(edata);
            ppTrackEvent('ViewCategory', edata, ppGetCookie('pp_pixel_viewcategory_event_id'));
            pp_vc = true;
            //console.log(ppGetCookie('pp_pixel_viewcategory_event_id'));
            deleteCookie('pp_pixel_viewcategory_event_id');
        }
    }
</script>
<!-- END Facebook ViewCategory event tracking -->