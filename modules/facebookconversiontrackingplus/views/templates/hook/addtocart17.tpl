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
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 *
*}

<!-- Add To cart Pixel Call -->
<script type="text/javascript">
    (function() {
        // Global flags and variables
        var isProcessingAddToCart = false; // Prevents duplicate processing
        var qty = false;                   // Holds the current quantity value
        var lastXhrResponse = null;        // For XHR deduplication

        // Reset the processing flag after a delay.
        function resetAddToCartFlag() {
            setTimeout(function() {
                isProcessingAddToCart = false;
            }, 500);
        }

        function sendAddToCartFromResponse(r, parsed, delCookie, source) {
            // If already processing, do nothing.
            if (isProcessingAddToCart) return;

            if (!parsed) {
                try {
                    r = JSON.parse(r);
                } catch (e) {
                    return; // Abort if parsing fails
                }
            }
            // console.log(new Date().toISOString(), "sendAddToCartFromResponse from", source, "with response:", r);

            if (r && r.cart && r.cart.products) {
                // Set initial global values (from server-side template variables)
                window.content_name = '{$entityname nofilter}';
                window.content_category = '{$content_category nofilter}';
                window.content_value = 1; // Default minimal value
                window.content_ids_data = [];
                window.content_ids_product = [];

                // Determine the selected product and attribute from the response.
                var selected_product_id = r.id_product || r.idProduct;
                var ipa = r.id_product_attribute || r.idProductAttribute;

                // Process the cart products and build the payload.
                $.each(r.cart.products, function(key, value) {
                    if (
                        (selected_product_id == value.id_product && value.id_product_attribute == 0) ||
                        (selected_product_id == value.id_product && value.id_product_attribute > 0 && value.id_product_attribute == ipa)
                    ) {
                        var pprice = 0;
                        {if $use_tax}
                        if (typeof value.price_with_reduction !== 'undefined') {
                            pprice = value.price_with_reduction;
                        } else if (typeof value.price_without_reduction !== 'undefined') {
                            pprice = value.price_without_reduction;
                        }
                        {else}
                        if (typeof value.price_with_reduction_without_tax !== 'undefined') {
                            pprice = value.price_with_reduction_without_tax;
                        } else if (typeof value.price_wt !== 'undefined') {
                            pprice = value.price_wt;
                        }
                        {/if}
                        if (pprice === 0) {
                            pprice = formatedNumberToFloat(value.price);
                        }
                        if (typeof value.name !== 'undefined') {
                            window.content_name = value.name;
                        }
                        window.content_value = pprice.toFixed(pp_price_precision);

                        // Build product identifier, with combination if enabled.
                        var id_combination = '';
                        {if $combi_enabled}
                        if (value.id_product_attribute > 0 && value.id_product_attribute == ipa) {
                            id_combination = '{$combi_prefix|escape:'htmlall':'UTF-8'}' + value.id_product_attribute;
                        }
                        {/if}
                        var pid = '{if isset($id_prefix)}{$id_prefix|escape:'htmlall':'UTF-8'}{/if}' + value.id_product + id_combination;
                        var productData = {
                            'id': pid,
                            'quantity': (qty !== false ? qty : value.quantity),
                            'item_price': (qty !== false ? qty * pprice : value.quantity * pprice)
                        };
                        if (value.category) {
                            productData.category = value.category;
                        }
                        window.content_ids_data.push(productData);
                        window.content_ids_product.push(pid);
                    }
                });

                // Build the final cartValues object.
                var cartValues = {
                    'content_name': window.content_name,
                    'content_ids': unique(window.content_ids_product),
                    'contents': unique(window.content_ids_data),
                    'content_type': 'product',
                    'value': window.content_value,
                    'currency': '{$currency_format_add_to_cart|escape:'htmlall':'UTF-8'}'
                };
                {if isset($fpf_id)}
                cartValues['product_catalog_id'] = '{$fpf_id|escape:'htmlall':'UTF-8'}';
                {/if}
                if (window.content_category) {
                    cartValues['content_category'] = window.content_category;
                }

                // Depending on the mode, call the appropriate final function.
                {if $capi}
                // When CAPI is enabled, delegate to atcAjaxCall.
                // (Do not set isProcessingAddToCart here so that atcAjaxCall can handle it.)
                atcAjaxCall(selected_product_id, ipa, source);
                {else}
                // When CAPI is disabled, set the flag and immediately fire the event.
                isProcessingAddToCart = true;
                var event_id = ppGetCookie('pp_pixel_event_id') || generateEventId(12);
                trackAddToCart(cartValues, event_id, source);
                {/if}

                if (delCookie) {
                    deleteCookie('pp_pixel_event_id');
                }
            }
        }

        function trackAddToCart(values, event_id, source) {
            values.source = source;
            // console.log(new Date().toISOString(), "Tracking AddToCart event from", source, "with data:", values);
            ppTrackEvent('AddToCart', values, {if $capi}event_id{/if});
            resetAddToCartFlag();
        }

        function atcAjaxCall(id_product, id_product_attribute, source) {
            if (isProcessingAddToCart) return false;
            isProcessingAddToCart = true;
            setTimeout(function() {
                $.ajax({
                    url: pp_aurl,
                    type: 'POST',
                    cache: false,
                    data: {
                        customAjax: true,
                        id_product: id_product,
                        id_product_attribute: id_product_attribute,
                        quantity: qty || 1,
                        event: 'AddToCart',
                        rand: Math.floor((Math.random() * 100000) + 1),
                        token: '{$static_token|escape:'htmlall':'UTF-8'}'
                    }
                })
                    .done(function(data) {
                        if (data.return === 'ok') {
                            trackAddToCart(data.custom_data, data.event_id, source || 'customAjax');
                        } else if (data.return === 'error') {
                            console.error('Error in add-to-cart AJAX call');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX error in add-to-cart call:', textStatus);
                    });
            }, 500);
        }

        // Helper: Converts a formatted price string to a float.
        function formatedNumberToFloat(price) {
            price = price.replace(prestashop.currency.sign, '')
                .replace(prestashop.currency.iso_code, '');
            switch (parseInt(prestashop.currency.format, 10)) {
                case 1:
                    return parseFloat(price.replace(',', '').replace(' ', ''));
                case 2:
                    return parseFloat(price.replace(' ', '').replace(',', '.'));
                case 3:
                    return parseFloat(price.replace('.', '').replace(' ', '').replace(',', '.'));
                case 4:
                    return parseFloat(price.replace(',', '').replace(' ', ''));
                default:
                    return parseFloat(price);
            }
        }

        // Helper: Returns only unique elements from an array.
        function unique(array) {
            return $.grep(array, function(el, index) {
                return index === $.inArray(el, array);
            });
        }

        // Attach event handlers for add-to-cart tracking.
        function initAddToCart() {
            {if isset($custom_add_to_cart) && !empty($custom_add_to_cart)}
            // Custom Add-to-Cart button handler.
            $(document).on('mousedown', '{$custom_add_to_cart|escape:'htmlall':'UTF-8'}', function() {
                if (isProcessingAddToCart) return false;
                if ($(this).data('idProduct') !== undefined) {
                    var ipa = $(this).data('idProductAttribute') || 0;
                    return atcAjaxCall($(this).data('idProduct'), ipa, 'customButton');
                }
                if ($(this).closest('form').length > 0) {
                    var f = $(this).closest('form');
                    var id = f.find('input[name="id_product"]').val() || 0;
                    var ipa = f.find('input[name="id_product_attribute"]').val() || 0;
                    if (id) {
                        return atcAjaxCall(id, ipa, 'customButton');
                    }
                }
            });
            {/if}

            // For AttributewizardPro or AWP modules.
            if (
                    {if isset($attributewizardpro) && $attributewizardpro == 1}true{else}false{/if} ||
                $('#awp_wizard').length === 1
            ) {
                var id_product = $('#product_page_product_id').length ? $('#product_page_product_id').val() : $('.product_page_product_id').val();
                var id_product_attribute = 0;
                $('.exclusive').on('click', function() {
                    if (!isProcessingAddToCart) {
                        atcAjaxCall(id_product, id_product_attribute, 'AWP');
                    }
                });
            }

            // Attach the Prestashop "updateCart" listener.
            if (typeof prestashop === 'object' && typeof prestashop.on !== 'undefined') {
                prestashop.on('updateCart', function(event) {
                    if (isProcessingAddToCart) return;
                    window.pp_atc_event_id = ppGetCookie('pp_pixel_event_id');
                    if (event && event.reason) {
                        if (event.reason.idProduct !== undefined) {
                            var ipa = event.reason.idProductAttribute || 0;
                            {if $capi}
                            atcAjaxCall(event.reason.idProduct, ipa, 'prestashop.on');
                            {else}
                            sendAddToCartFromResponse(event.reason, true, true, 'prestashop.on');
                            {/if}
                        } else if (event.reason.cart !== undefined) {
                            sendAddToCartFromResponse(event.reason, true, true, 'prestashop.on');
                        }
                    }
                });
            }

            // Override XMLHttpRequest as a fallback.
            (function(open) {
                XMLHttpRequest.prototype.open = function(method, url, async) {
                    var fpCartEndpoint = '/{$fp_cart_endpoint|escape:'htmlall':'UTF-8'}';
                    var checkURL = url.search(fpCartEndpoint);
                    if (checkURL > -1 && !isProcessingAddToCart) {
                        // Clear globals before processing the add-to-cart request.
                        delete window.content_ids_data;
                        delete window.content_ids_product;
                        delete window.total_products_value;
                        window.pp_atc_event_id = generateEventId(12);
                        url += (url.search('\\?') > -1)
                            ? '&pp_atc_event_id=' + window.pp_atc_event_id
                            : '?pp_atc_event_id=' + window.pp_atc_event_id;
                    }
                    this.addEventListener('load', function() {
                        if (this.response) {
                            // Check for duplicate XHR response.
                            if (lastXhrResponse === this.response) {
                                // console.log(new Date().toISOString(), "Duplicate XHR response. Skipping.");
                                return;
                            }
                            lastXhrResponse = this.response;
                            try {
                                sendAddToCartFromResponse(this.response, false, false, 'XHR');
                            } catch (e) {
                                console.error("Error processing XHR response:", e);
                            }
                        }
                    });
                    this.addEventListener('error', function() {
                        console.error('Request failed with error');
                    });
                    this.addEventListener('abort', function() {
                        console.error('Request was aborted');
                    });
                    open.apply(this, arguments);
                };
            })(XMLHttpRequest.prototype.open);
        }

        // Wait for DOM and jQuery to be ready.
        document.addEventListener("DOMContentLoaded", function() {
            var tries = 20;
            function waitForjQuery(tries) {
                if (typeof jQuery === 'undefined') {
                    if (tries > 0) {
                        setTimeout(function() { waitForjQuery(tries - 1); }, 250);
                    } else {
                        console.error('PP: Could not initiate the Add To Cart Event Tracking');
                    }
                    return;
                }
                initAddToCart();
            }
            waitForjQuery(tries);

            // Listen for changes in quantity.
            $(document).on('change', '#quantity_wanted', function() {
                qty = $(this).val();
            });
        });
    })();
</script>
<!-- End Add to cart pixel call -->

