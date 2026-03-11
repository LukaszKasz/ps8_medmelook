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
 * **************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * **************************************************
 *
*}
<!-- Search Pixel Call -->
<script type="text/javascript">
    function getContentIdsList() {
        {if isset($content_ids_list) && $content_ids_list != '[]'}
        return {$content_ids_list nofilter}; {* It's a JSON object *}
        {elseif isset($products) && (count($products) > 0)}
        return [{foreach from=$products item=product name=sproducts}'{$id_prefix|escape:'htmlall':'UTF-8'}{$product.id_product|intval}{if $combi && $product.id_product_attribute > 0}{$combi_prefix|escape:'htmlall':'UTF-8'}{$product.id_product_attribute|intval}{/if}'{if !$smarty.foreach.sproducts.last},{/if}
            {/foreach}];
        {else}
        {assign var="manual_mode" value=true}
        var listSelector = getListSelector();
        if (typeof listSelector !== 'undefined') {
            let content_ids_list = getContentIds(listSelector, '{$id_prefix|escape:'htmlall':'UTF-8'}', {if $combi}'{$combi_prefix|escape:'htmlall':'UTF-8'}'{else}false{/if});
            if (content_ids_list.length > 0) {
                return content_ids_list;
            } else {
                console.log('Could not locate the product IDs');
            }
        }
        return [];
        {/if}
    }

    document.addEventListener('DOMContentLoaded', function() {
        fctp_search(10);
    });
    var fb_pixel_event_id_search = ppGetCookie('pp_pixel_event_id_search');
    var content_ids_list = [];
    var search_string = '{if isset($search_query) && $search_query != ''}{$search_query|cleanHtml}{elseif isset($search_string) && $search_string != ''}{$search_string|cleanHtml}{else}{$search_keywords|cleanHtml}{/if}'; {* Can't escape search strings as in foreing languages will cause a character encoding, rendering them illegible *}


    function fctp_search(max_tries) {
        if (typeof jQuery == 'undefined' || typeof fbq != 'function' || typeof getContentIdsList === 'undefined') {
            if (max_tries > 0) {
                setTimeout(function () {
                    fctp_search(max_tries - 1)
                }, 500);
            } else {
                console.log('PP: Could not initiate the Search event');
            }
            return;
        }
        content_ids_list = getContentIdsList();
        if (content_ids_list.length > 0) {
            launchSearchEvent();
        }
        function launchSearchEvent() {
            {if !(isset($content_ids_list) && $content_ids_list != '[]') && $capi}
            $.ajax({
                url: pp_aurl,
                type: 'POST',
                cache: false,
                data: {
                    customAjax: true,
                    content_ids_list : content_ids_list,
                    search_query: search_string, {* can't escape as it may alter the search string *}
                    quantity: qty,
                    event: 'Search',
                    rand: Math.floor((Math.random() * 100000) + 1),
                    token: '{$static_token|escape:'htmlall':'UTF-8'}',
                }
            })
                .done(function(data) {
                    if (typeof data.custom_data !== 'undefined') {
                        ppTrackEvent('Search', data.custom_data, data.event_id);
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    // Something went wrong
                });
            {else}
            let data = {
                'search_string': search_string,
                value: {$search_value|floatval},
                currency: '{$fctp_currency|escape:'htmlall':'UTF-8'}',
                content_ids: content_ids_list,
                content_type: 'product',
                {if isset($fpf_id)}product_catalog_id: '{$fpf_id|escape:'htmlall':'UTF-8'}',{/if}
            };
            ppTrackEvent('Search', data, fb_pixel_event_id_search);
            {/if}
        }
    }

    {* Only if the list couldn't be populated from the TPL variables *}
    {if isset($manual_mode)}
    function getListSelector() {
        if ($('#product_list').length > 0) {
            return $('#product_list').children();
        } else if ($('.products article').length > 0) {
            return $('.products article');
        } else if ($('.product_list').length > 0) {
            return $('.product_list').children();
        } else if ($('.ajax_block_product').length > 0) {
            return $('.ajax_block_product');
        }
    }

    function getContentIds(selector, prefix, combi_prefix) {
        var tmp = [];
        var id = '';
        selector.each(function() {
            if (tmp.length < 5) {
                let e = false;
                if ($(this).data('idProduct') > 0) {
                    e = $(this);
                } else if ($(this).find('[data-id-product]').length > 0) {
                    e = $(this).find('[data-id-product]').first();
                } else {
                    $(this).find('a').each(function() {
                        let param = $(this).attr('href').match(/\/([0-9]*)-([0-9]*)[\-]?/);
                        if (typeof param[1] !== 'undefined') {
                            id = param[1]
                        } else if (combi_prefix !== false && typeof param[2] !== 'undefined') {
                            id += combi_prefix+param[2];
                        }
                    });
                }
                if (e !== false) {
                    id = e.data('idProduct');
                    if (combi_prefix !== false && e.data('idProductAttribute')) {
                        id += combi_prefix+e.data('idProductAttribute');
                    }
                }
                if (id !== '') {
                    tmp.push(id);
                }
            } else {
                return false;
            }
        });
        return tmp.length > 0 ? tmp : false;
    }
    {/if}
</script>
<!-- End Search Pixel Call -->
