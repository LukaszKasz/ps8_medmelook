{*
 * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 *
*}
<!-- Pixel Plus Product Vars -->
<script type="text/javascript">
{if isset($id_product_attribute)}
    var fb_pixel_event_id_view = '{$refresh_pixel_id|escape:'htmlall':'UTF-8'}';
    if (typeof combination === 'undefined' || (combination != {$id_product_attribute|intval})) {
    setTimeout(function() {
        if ((typeof discoverCombi !== 'undefined') && discoverCombi() === false) {
            combination = {$id_product_attribute|intval};
            if ($('[itemprop=price]').length > 0) {
                pvalue = parseFloat($('[itemprop=price]').attr('content'));
            }
        }
        if (typeof trackViewContent !== 'undefined') {
            trackViewContent();
        }
        //console.log(combi_change);
    }, 1200);
    }
{/if}
</script>