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
<!-- Pixel Plus: AddToCart NO-AJAX -->

    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            var sent = false;
            fctp_addToCart(10);

            function fctp_addToCart(max_tries) {
                if (typeof jQuery == 'undefined' || typeof fbq != 'function') {
                    setTimeout(function() { fctp_addToCart(max_tries-1) },500);
                } else {
                    var values = '';
                {if $entity == 'index' || $entity == 'search' || $entity == 'category' || $entity == 'prices-drop'}
                    $(document).on('click mousedown', '.ajax_add_to_cart_button', function(e) {
                        pixelCall(getpixelvalueslist($(this)));
                    });
                    $(document).on('click mousedown', 'button.add-to-cart', function(e) {
                        pixelCall(getpixelvalueslist17($(this)));
                    });
                {/if}
                {if $custom_add_to_cart != ''}
                    init_cust_add_to_cart(5);
                {/if}
                {if $entity == 'product'}
                    if ($("#add_to_cart button, #add_to_cart a, #add_to_cart input").length > 0) {
                        $(document).on('mousedown', '#add_to_cart button, #add_to_cart a, #add_to_cart input',
                            function(e) {
                                pixelCall(getpixelvalue($(this)));
                            });
                    } else {
                        if ($('#add-to-cart-or-refresh button.add-to-cart').length != 0) {
                            $(document).on('mousedown', '#add-to-cart-or-refresh button.add-to-cart', function(e) {
                                pixelCall(getpixelvalue($(this)));
                            });
                        } else if ($("button#add_to_cart").length == 1) {
                            $(document).on('mousedown', '#add_to_cart', function() {
                                pixelCall(getpixelvalue($(this)));
                            });
                            $(document).on('mousedown', "button#add_to_cart", function(e) {
                                pixelCall(getpixelvalue($(this)));
                            });
                        } else {
                            /* Last resort */
                            if ($('.ajax_add_to_cart_button').length > 0) {
                                $(document).on('click', '.ajax_add_to_cart_button', function(e) {
                                    pixelCall(getpixelvalueslist($(this)));
                                });
                                $(document).on('mousedown', '.ajax_add_to_cart_button', function(e) {
                                    pixelCall(getpixelvalue($(this)));
                                });

                            } else {
                                /* 1.7 Versions */
                                if ($('button.add-to-cart').length != 0) {
                                    $(document).on('click', 'button.add-to-cart', function(e) {
                                        pixelCall(getpixelvalueslist17($(this)));
                                    });
                                } else {
                                    console.log('AddToCart not found, customizations may be needed');
                                }
                            }
                        }
                    }
                {/if}
                    /* 1.5.X versions */
                    $("#add_to_cart input").click(function() {
                        pixelCall(getpixelvalue($(this)));
                    });

                    function getpixelvalueslist(object) {
                        var iv = id_product_attribute = 0;
                        if (typeof productPrice != 'undefined') {
                            iv = productPrice;
                        } else {
                            iv = getPriceRecursive(6, object);
                            if (iv === false) {
                                iv = null;
                            }
                        }
                        productname = getNameRecursive(6, object);
                        if (typeof productname === 'undefined' || productname === false) {
                            productname = $("#bigpic").attr('title');
                        }
                        if (typeof id_product === 'undefined' || id_product === null) {
                            id_product = object.data('idProduct');
                        }
                        if (typeof id_product === 'undefined' || id_product === null) {
                            id_product = object.closest('article').data('idProduct');
                        }
                        if (typeof id_product === 'undefined' || id_product === null) {
                            id_product = gup('id_product', object.attr('href'));
                        }
                        if (typeof id_product === 'undefined' || id_product === null) {
                            id_product = $('.primary_block').find('input[name="id_product"]').val();
                        }
                        if (typeof id_product === 'undefined' || id_product === null) {
                            id_product = object.closest('form').find('input[name="id_product"]').val();
                        }
                        if (typeof object.data('idProductAttribute') !== 'undefined') {
                            id_product_attribute = object.data('idProductAttribute');
                        }
                        values = {
                            content_name: productname,
                            value: iv,
                            currency: '{$fctp_currency|escape:'htmlall':'UTF-8'}',
                            {if $content_category != ''}content_category: '{$content_category nofilter}',{/if}
                            content_ids : ['{$id_prefix|escape:'htmlall':'UTF-8'}' + id_product + (id_product_attribute > 0 ? '{$combi_prefix|escape:'htmlall':'UTF-8'}' + id_product_attribute : '')],
                            content_type: 'product',
                            {if isset($fpf_id)}product_catalog_id :  '{$fpf_id|escape:'htmlall':'UTF-8'}',{/if}
                        };
                        return values;
                    }

                    function getpixelvalueslist17(object) {
                        let v = {};
                        if (object.parents('.product-miniature').length > 0) {
                            object = object.parents('.product-miniature').first();
                        } else {
                            object = object.parents('.row').first();
                        }
                        let p = $('#product-details').data('product');
                        v.value = id_product_attribute = 0;
                        if (typeof productPrice != 'undefined') {
                            v.value = productPrice;
                        } else {
                            var priceObj = '';
                            if (object.find('.current-price span').length > 0) {
                                priceObj = object.find('.current-price span').first();
                            } else if (object.find('span.product-price').length > 0) {
                                priceObj = object.find('span.product-price').first();
                            } else if (object.find('span.price').length > 0) {
                                priceObj = object.find('span.price').first();
                            }  else if (object.find('#final_price').length > 0) {
                                priceObj = object.find('#final_price');
                            }
                            if (priceObj != '') {
                                v.value = getPriceFromElement(priceObj);
                            } else {
                                console.log('Could not find the product price, contact the module developer for a customization');
                            }
                        }
                        if (object.find('[itemprop="name"]').length > 0) {
                            v.content_name = object.find('[itemprop="name"]').text();
                        } else if (object.find('.product-title').length > 0) {
                            v.content_name = object.find('.product-title').first().text();
                        } else if (object.find('h1').length > 0) {
                            v.content_name = object.find('h1').first().text();
                        }
                        if (typeof v.content_name === 'undefined') {
                            v.content_name = p.name;
                        }
                        id_product = getIdProduct(object, p);

                        if (typeof object.data('idProductAttribute') !== 'undefined') {
                            id_product_attribute = object.data('idProductAttribute');
                        } else if (p && typeof p.id_product_attribute) {
                            id_product_attribute = p.id_product_attribute;
                        }

                        if ($('#quantity-configurator').length > 0) {
                            v.quantity = $('#quantity-configurator').val();
                            v.value *= v.quantity;
                        }
                        v.id_product = id_product;
                        v.id_product_attribute = id_product_attribute;
                        v.currency = '{$fctp_currency|escape:'htmlall':'UTF-8'}';
                        v.content_type = 'product';
                        v.content_ids = ['{$id_prefix|escape:'htmlall':'UTF-8'}' + id_product + (id_product_attribute > 0 ? '{$combi_prefix|escape:'htmlall':'UTF-8'}' + id_product_attribute : '')];
                        {if $content_category != ''}v.content_category = '{$content_category nofilter}';{/if}
                        {if isset($fpf_id)}v.product_catalog_id = '{$fpf_id|escape:'htmlall':'UTF-8'}';{/if}
                        return v;
                    }

                    function getPriceRecursive(tries, object) {
                        var res = '';
                        if (object.parent().find('[itemprop="price"]').length > 0) {
                            res = getPriceFromElement(object.parent().find('[itemprop="price"]'));
                        } else if (object.parent().find('.price:eq(0)').length > 0) {
                            res = getPriceFromElement(object.parent().find('.price:eq(0)'));
                        }
                        if (res >= 0) {
                            return res;
                        }
                        if (tries > 0) {
                            return getPriceRecursive(tries - 1, object.parent());
                        }
                    }

                    function getNameRecursive(tries, object) {
                        var res = '';
                        if (object.parent().find('.product-name, itemprop[name]').length > 0) {
                            res = object.parent().find('.product-name, itemprop[name]').first().text().trim();
                        } else {
                            if (tries > 0) {
                                res = getNameRecursive(tries - 1, object.parent());
                            } else {
                                return false;
                            }
                        }
                        if (res != '') {
                            return res;
                        }
                    }

                    function getIdProduct(object, p) {
                        if (typeof object.data('id_product') !== 'undefined') {
                            return object.data('id_product');
                        } else if (typeof object.find('button').data('idProduct') !== 'undefined') {
                            return object.find('button').data('idProduct');
                        } else if (object.find('[name="id_product"]').length > 0) {
                            return object.find('input[name="id_product"]').first().val();
                        } else if (object.find('input#product_page_product_id').length > 0) {
                            return object.find('input#product_page_product_id').val();
                        } else if (object.closest('form').length > 0 && object.closest('form').find('input[name="id_product"]').length > 0) {
                            return object.closest('form').find('input[name="id_product"]').val();
                        } else if (p) {
                            return p.id_product;
                        } else {
                            console.log(
                                'Could not find the product ID in the products list, contact the developer to ask for further assistance'
                            );
                            return '';
                        }
                    }

                    function getpixelvalue(object) {
                    {if $is_17 && $combi}
                        discoverCombi();
                    {/if}
                        var ipa = 0;
                        var id_product = 0;
                        if ($("#buy_block").find("input[name=id_product]:eq(0)").length > 0) {
                            id_product = $("#buy_block").find("input[name=id_product]:eq(0)").val();
                        } else if ($("#add-to-cart-or-refresh").find("input[name=id_product]:eq(0)").length > 0) {
                            id_product = $("#add-to-cart-or-refresh").find("input[name=id_product]:eq(0)").val();
                        } else {
                            console.log(
                                'Could not locate the Product ID: Contact the module developer for assistenace');
                        }
                        if (typeof combination !== 'undefined') {
                            ipa = combination;
                        } else {
                            if ($("#buy_block").find("input[name=id_product_attribute]:eq(0)").length > 0) {
                                ipa = $("#buy_block").find("input[name=id_product_attribute]:eq(0)").val();
                            } else if ($("#add-to-cart-or-refresh").find("input[name=id_product_attribute]:eq(0)").length > 0) {
                                ipa = $("#add-to-cart-or-refresh").find("input[name=id_product]:eq(0)").val();
                            } else if (typeof $('.product-details').data('idProductAttribute') !== 'undefined') {
                                ipa = $('.product-details').data('idProductAttribute').val();
                            }
                        }
                        let values = {
                            'ipa': parseInt(ipa) || 0,
                            'id_product': id_product,
                            'quantity': $('#quantity_wanted').val()
                        };
                        return values;
                    }

                    function getProductPrice(object) {
                        var main = '';
                        if ($('.col-product-info').length > 0) {
                            main = $('.col-product-info');
                        } else if ($('#main').length > 0) {
                            main = $('#main')
                        } else if ($('#center_column').length > 0) {
                            main = $('#center_column');
                        }
                        if (main != '') {
                            var selectors = ['#our_price_display', '[itemprop=price]', '.product-price',
                                '.pb-right-column', '.product-information'
                            ];
                            var l = selectors.length;
                            for (var i = 0; i < l; i++) {
                                if (main.find(selectors[i]).length > 0) {
                                    return getPriceFromElement($(selectors[i]));
                                }
                            }
                        }
                    }

                    function getPriceFromElement(e) {
                        if (typeof e.attr('content') !== 'undefined') {
                            return getPriceFromContent(e.attr('content'));
                        } else {
                            iv = e.text().replace(/\D/g, '');
                            return formatPrice(iv);
                        }
                    }

                    function getPriceFromContent(e) {
                        /*if (e.indexOf('.') !== -1) {
                        return parseFloat(e).toFixed(pp_price_precision);
                        } else { */
                        return parseFloat(e);
                        //}
                    }

                    function formatPrice(e) {
                        if (typeof pp_price_precision === 'undefined') {
                            var pp_price_precision = 2;
                        }
                        if (e.indexOf('.') === -1) {
                            return parseFloat(e.slice(0, -(pp_price_precision)) + '.' + e.slice((e.slice(0, -(
                                pp_price_precision)).length)));
                        } else {
                            return parseFloat(e);
                        }
                    }

                    function gup(name, url) {
                        if (!url) url = location.href;
                        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
                        var regexS = "[\\?&]" + name + "=([^&#]*)";
                        var regex = new RegExp(regexS);
                        var results = regex.exec(url);
                        return results == null ? null : results[1];
                    }
                    function init_cust_add_to_cart(tries) {
                        if ($('{$custom_add_to_cart|escape:'htmlall':'UTF-8'}').length > 0 || tries == 0) {
                            $(document).on('click mousedown', '{$custom_add_to_cart|escape:'htmlall':'UTF-8'}', function() {
                                console.log('Custom Add To Cart clicked');
                                pixelCall(getpixelvalueslist($(this)));
                            });
                        } else {
                            setTimeout(function() { init_cust_add_to_cart(tries - 1) }, 250);
                        }
                    }
                function pixelCall(values) {
                    if (sent == false) {
                        //console.log(values);
                        jQuery.ajax({
                            url: pp_aurl,
                            type: 'POST',
                            cache: false,
                            data: {
                                event: 'AddToCart',
                                'values': values,
                                rand: Math.floor((Math.random() * 100000) + 1)
                            }
                        })
                        .done(function(data) {
                            if (data.return == 'ok') {
                                ppTrackEvent('AddToCart', data.custom_data, data.event_id);
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('Pixel Plus: Add to Cart event could not be tracked');
                        });
                        sent = true;
                        /* Enable again the addToCart event */
                        setTimeout(function() {
                            sent = false;
                        }, 1000);
                    }
                }
        }
        }
        });
    </script>
<!-- End Pixel Plus: AddToCart NO-AJAX -->