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
    // console.log('PP ATC');
    var pp_cart_adding = false;
    var qty = 1;
    var button_clicked = false;
    const reg = new RegExp('^(?:[a-z]+:)?//', 'i');
    document.addEventListener("DOMContentLoaded", function(event) {
        init(20);
        function init(tries) {
            if (typeof jQuery === 'undefined') {
                if (tries > 0) {
                    setTimeout(() => { init(tries-1) }, 250);
                } else {
                    console.log('PP: Could not initiate the Add To Cart Event Tracking');
                }
                return;
            }
            var selector = '.add-to-cart, .ajax_add_to_cart_button, .btn-addtocart, #add_to_cart button, #add_to_cart input[type="submit"], #add_to_cart';
            if (typeof pp_custom_add_to_cart !== 'undefined' && pp_custom_add_to_cart != '') {
                selector += ',' + pp_custom_add_to_cart;
            }
            $(document).on('click mousedown', selector, function () {
                if (button_clicked !== false) {
                    clearTimeout(button_clicked);
                }
                button_clicked = setTimeout(function () {
                    button_clicked = false;
                }, 1500);
            });
            setTimeout(function () {
                init_add_to_cart();
            }, 200);

            function unique(array) {
                return $.grep(array, function (el, index) {
                    return index === $.inArray(el, array);
                });
            }

            window.ajaxsetupcalled = false;

            function formatedNumberToFloat(price, ci, cp) {
                price = price.replace(ci, '').replace(cp, '');
                switch (parseInt(currencyFormat)) {
                    case 1:
                        price = price.replace(',', '').replace(' ', '');
                    case 2:
                    case 3:
                        price = price.replace('.', '').replace(' ', '').replace(',', '.');
                    case 4:
                        price = price.replace(',', '').replace(' ', '');
                }
                return parseFloat(price);
            }

            function init_add_to_cart() {
                {if isset($pp_custom_add_to_cart) && $pp_custom_add_to_cart}
                $(document).on('mousedown', pp_custom_add_to_cart, function () {
                    qty = $('#quantity_wanted').val();
                    customAjaxCall(id_product, combination);
                });
                {else}
                $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
                    // console.log('Ajax Prefilter');
                    var urlData = originalOptions.data;
                    if (typeof urlData !== 'undefined' && typeof urlData !== 'object') {
                        var checkData = urlData.search('controller=cart');
                        if (typeof originalOptions.data !== 'undefined' && checkData > -1) {
                            delete window.content_ids_data;
                            delete window.content_ids_product;
                            delete window.total_products_value;
                            window.pp_atc_event_id = generateEventId(12);
                            if (options.data.indexOf('&pp_atc_event_id') === -1) {
                                options.data += '&pp_atc_event_id=' + window.pp_atc_event_id;
                            }
                        }
                    }

                });

                $(document).ajaxComplete(function (request, jqXHR, settings) {
                    //1.5 && 1.6 code
                    if (!button_clicked) {
                        return;
                    }
                    let r = '';
                    if (typeof jqXHR.responseJSON !== 'undefined') {
                        r = jqXHR.responseJSON;
                    } else if (typeof jqXHR.responseText !== 'undefined') {
                        r = JSON.parse(jqXHR.responseText);
                    } else {
                        console.log('can\'t process the response');
                        return;
                    }
                    // console.log(r, settings);
                    if (r !== undefined && r !== null && (typeof r.products === 'object') && r.products.length > 0) {
                        // console.log('PP: Tracking Add To Cart');
                        let url_str = settings.url + '&' + settings.data;
                        let url = '';
                        if (reg.test(url_str)) {
                            url = new URL(url_str);
                        } else {
                            url = new URL(url_str, location.protocol + '//' + location.host);
                        }
                        let search_params = url.searchParams;
                        let ignore_combi_check = {if isset($module_ignore_combi)}{$module_ignore_combi|intval}{else}0{/if};
                        var sel_pid = 0;
                        var ipa = 0;
                        //console.log(search_params.get('id_product'));
                        if (search_params.get('id_product') !== null) {
                            sel_pid = search_params.get('id_product');
                            ipa = search_params.get('ipa');
                        } else {
                            sel_pid = parseInt($('#product_page_product_id, #id_product').first().val()) || 0;
                            ipa = parseInt($('#idCombination, #id_product_attribute').first().val()) || 0;
                        }
                        var is_delete = search_params.get('delete');
                        if (is_delete == 1 || is_delete == 'true') {
                            console.log("Removing a product from the cart, no event is needed");
                            return;
                        }
                        if (sel_pid > 0) {
                            window.content_name = '';
                            window.content_category = '{$content_category nofilter}';
                            //cart value should never be 0 or empty, so assigning miniumm value as 1
                            window.content_value = 1;
                            window.content_ids_data = [];
                            window.content_ids_product = [];
                            $.each(r.products, function (key, value) {
                                var id_combination = '';
                                {if $combi_enabled}
                                if ((value.idCombination > 0 && value.idCombination == ipa) || value.idCombination > 0 && ignore_combi_check) {
                                    id_combination = '{$combi_prefix|escape:'htmlall':'UTF-8'}' + value.idCombination;
                                }
                                {/if}

                                if ((sel_pid == value.id && value.idCombination == 0) || (sel_pid == value.id && value.idCombination > 0 && value.idCombination == ipa) || (sel_pid == value.id && ignore_combi_check)) {
                                    content_name = value.name;
                                    //console.log('Price:');
                                    //console.log(value.price);
                                    //send only one item price, but ps 1.6 returns multiple of the total
                                    content_value = formatedNumberToFloat(value.price, window.currencyISO, window.currencySign) / value.quantity;
                                    var pid = '{$id_prefix|escape:'htmlall':'UTF-8'}' + value.id + id_combination;
                                    var this_product = {
                                        'id': pid,
                                        'quantity': value.quantity,
                                        'item_price': formatedNumberToFloat(value.price, window
                                            .currencyIso, window.currencySign) / value.quantity
                                    }
                                    content_ids_data.push(this_product);
                                    content_ids_product.push(pid);
                                }

                            });
                            window.total_products_value = formatedNumberToFloat(r.total, window.currencyISO, window.currencySign);
                            var cartValues = {
                                'content_name': window.content_name,
                                'content_ids': window.content_ids_product,
                                'contents': window.content_ids_data,
                                'content_type': 'product',
                                'value': content_value,
                                'currency': '{$currency_format_add_to_cart|escape:'htmlall':'UTF-8'}'
                            };

                            {if isset($fpf_id)}
                            cartValues['product_catalog_id'] = '{$fpf_id|escape:'htmlall':'UTF-8'}';
                            {/if}
                            if (window.content_category != '') {
                                cartValues['content_category'] = window.content_category;
                            }
                            if (cartValues.content_type != '' && cartValues.contents != '' && cartValues.content_ids != '' && cartValues.value != '' && cartValues.currency != '') {
                                trackAddToCart(cartValues, window.pp_atc_event_id);
                            } else {
                                // Is not an AddToCart event
                            }
                        } else {
                            //console.log('Pixel Plus: Could not locate the Product ID, aborting AddToCart');
                        }
                    }
                    button_clicked = false;
                });
                {/if}
            }

            function customAjaxCall(id_product, id_product_attribute) {
                $.ajax({
                    url: pp_aurl,
                    type: 'POST',
                    cache: false,
                    data: {
                        customAjax: true,
                        id_product: id_product,
                        id_product_attribute: id_product_attribute,
                        quantity: qty,
                        event: 'AddToCart',
                        rand: Math.floor((Math.random() * 100000) + 1),
                        token: '{$static_token|escape:'htmlall':'UTF-8'}',
                    }
                })
                    .done(function (data) {
                        // console.log(data);
                        if (data.return == 'ok') {
                            trackAddToCart(data.custom_data, data.event_id);
                        }
                        setTimeout(function () {
                            pp_cart_adding = false;
                        }, 2000);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        // Something went wrong
                    });
            }

            function trackAddToCart(data, event_id) {
                if (!pp_cart_adding) {
                    pp_cart_adding = true;
                    ppTrackEvent('AddToCart', data{if $capi}, event_id{/if});
                    deleteCookie('pp_pixel_event_id');
                }
            }
        }
    });
</script>
<!-- End Add to cart pixel call -->