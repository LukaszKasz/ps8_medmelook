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
 * *     Facebook Conversion Tracking Pixel Plus     *
 * *          http://www.smart-modules.com           *
 * **************************************************
 *
*}

<!-- Facebook View Content Track -->
<script type="text/javascript">
    if (typeof vc_last_id === 'undefined') {
        var vc_last_id = 0;
        var vc_last_ipa = 0;
    }
    var pp_vc_event_id = '';
    var combination = {if isset($product)}{if $product|is_array && isset($product.id_product_attribute)}{$product.id_product_attribute|intval}{elseif isset($product->id_product_attribute)}{$product->id_product_attribute|intval}{else}0{/if}{else}{if isset($id_product_attribute)}{$id_product_attribute|intval}{else}0{/if}{/if};
    var combi_change = false;
    var u = document.URL;
    var pvalue = {if isset($entityprice)}{$entityprice|floatval}{else}productPrice{/if};

    document.addEventListener('DOMContentLoaded', function () {
        fctp_viewContent(10);
    });

    function fctp_viewContent(max_tries) {
        // Check if jQuery or Facebook's fbq is available
        if (typeof jQuery == 'undefined' || typeof fbq != 'function') {
            if (max_tries > 0) {
                setTimeout(function () {
                    fctp_viewContent(max_tries - 1);
                }, 500);
            } else {
                console.log('PP: Could not initiate the ViewContent event');
            }
            return;
        }
        pp_vc_event_id = generateEventId(12);

        // Handle custom modules (dynamicproduct waits for ajaxComplete)
        {if $custom_vc_module == 'dynamicproduct'}
        $(document).ajaxComplete(function (event, request, settings) {
            if (settings.data.indexOf('action=calculate_result') !== -1) {
                try {
                    // Parse the JSON response to get final_prices
                    var response = JSON.parse(request.responseText);

                    // Check if final_prices exists and fetch price_ht or price_ttc
                    if (response.final_prices) {
                        pvalue = {if !$use_tax}response.final_prices.price_ht{else}response.final_prices.price_ttc{/if};

                        // Trigger the ViewContent tracking event
                        trackViewContent();
                    } else {
                        console.log('PP: final_prices not found in the response');
                    }
                } catch (e) {
                    console.log('PP: Could not parse AJAX response or missing final_prices');
                }
            }
        });
        {else}
        {if !$is_17}
        // Handle older PrestaShop versions (1.6 or below)
        $(document).ready(function() {
            if ($("#idCombination").length == 1) {
                if (combination == 0) {
                    combination = $("#idCombination").val();
                }
                MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

                var observer = new MutationObserver(function(mutations, observer) {
                    var combi = $("#idCombination").val();
                    if (combination != combi) {
                        combination = combi;
                        trackViewContent();
                    }
                });

                observer.observe(document.getElementById("idCombination"), {
                    subtree: true,
                    attributes: true
                });
                // Trigger by default when page loaded
                trackViewContent();
            } else {
                // Trigger when there is no combination loaded on page load
                trackViewContent();
            }
        });
        {else}
        // For PrestaShop 1.7+
        trackViewContent();
        {/if}
        {/if}
    }

    function trackViewContent() {
        let ipa = {$product_combi|intval};
        if (typeof combination !== 'undefined' && combination > 0) {
            ipa = combination;
        }
        let id = '{$id_prefix|escape:'htmlall':'UTF-8'}{$product_id|intval}';

        {if $hascombi && $combi_enabled}
        if (ipa > 0) {
            id += '{$combi_prefix|escape:'htmlall':'UTF-8'}' + ipa;
        }
        {/if}

        {if $capi}
        $.ajax({
            url: pp_aurl,
            type: 'POST',
            cache: false,
            data: {
                customAjax: true,
                id_product : {$product_id|intval},
                id_product_attribute : ipa,
                event: 'ViewContent',
                rand: Math.floor((Math.random() * 100000) + 1),
                token: '{$static_token|escape:'htmlall':'UTF-8'}',
                event_id: pp_vc_event_id,
                source_url: window.location.href
            }
        })
            .done(function(data) {
                if (data.return == 'ok') {
                    pp_vc_event_id = data.event_id;
                    sendTrackViewContent(id, ipa);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.log('Error: Could not track ViewContent event');
            });
        {else}
        if (vc_last_id != id || vc_last_ipa != ipa) {
            sendTrackViewContent(id, ipa);
        }
        {/if}
    }

    function sendTrackViewContent(id, ipa) {
        ppTrackEvent('ViewContent', {
            content_name: '{$entityname nofilter}',
            {if $content_category != ''}
            content_category: '{$content_category nofilter}',
            {/if}
            value: pvalue,
            currency: '{$fctp_currency|escape:'htmlall':'UTF-8'}',
            {if isset($product_id) && $product_id != ''}
            content_type: 'product',
            content_ids: [id],
            {/if}
            {if isset($fpf_id)}
            product_catalog_id: '{$fpf_id|escape:'htmlall':'UTF-8'}'
            {/if}
        }, typeof pp_vc_event_id !== 'undefined' ? pp_vc_event_id : generateEventId(12));

        vc_last_id = id;
        vc_last_ipa = ipa;
    }

    function discoverCombi() {
        if (combi_change === true) {
            combi_change = false;
            return true;
        }
        if ($('#product-details').length > 0) {
            if (typeof $('#product-details').data('product') !== 'undefined') {
                combination = $('#product-details').data('product').id_product_attribute;
                pvalue = $('#product-details').data('product').price_amount;
                return true;
            }
        }
        return false;
    }
</script>

<!-- END Facebook View Content Track -->
