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
 * *                     V 2.3.4                     *
 * **************************************************
 *
 *}
<!-- Facebook ViewCategory event tracking -->
<script type="text/javascript">
        let cms_event_id = {if isset($cms_event_id)}"{$cms_event_id|escape:'htmlall':'UTF-8'}"{else}ppGetCookie('pp_pixel_event_id_view'){/if};
        fctp_cmsView(10);
        function fctp_cmsView(max_tries) {
            if (typeof jQuery == 'undefined' || typeof fbq != 'function') {
                setTimeout(function() { fctp_cmsView(max_tries-1) },500);
            } else {
                jQuery(document).ready(function() {
                    var edata = {
                        page_name : '{$entityname nofilter}',
                    };
                    edata.value = {$cms_value|floatval};
                    edata.currency = '{$fctp_currency|escape:'htmlall':'UTF-8'}';
                    edata.page_id = {$content_ids|floatval};
                    ppTrackEvent('ViewCMS', edata, cms_event_id);
                });
            }
        }
</script>
<!-- END Facebook ViewCategory event tracking -->