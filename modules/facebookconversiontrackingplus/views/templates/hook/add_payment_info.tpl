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
    <!-- Start Add Payment Info -->
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        if (document.readyState == "interactive") {
            fctp_startPayment(25);
        }
    });
    var paymentAdded = false;
    function fctp_startPayment(max_tries) {
        if ((typeof jQuery == 'undefined' || typeof fbq != 'function')) {
            if (max_tries > 0) {
                setTimeout(function () {
                    fctp_startPayment(max_tries - 1)
                }, 250);
            }
            return;
        } else {
            var sel = [
                    ['#supercheckout_confirm_order', '#supercheckout_confirm_order'],
                    ['.payment-option label, .payment-option input', '.payment-option input, .payment-option label'],
                    ['.payment_module a', '.payment_module a'],
                    ['#HOOK_PAYMENT', '#HOOK_PAYMENT, #HOOK_PAYMENT input'],
                    ['#checkout-payment-step', '#checkout-payment-step input'],
                    ['#opc_payment_methods', '#opc_payment_methods input'],
                    ['#payment_method_container', '#payment_method_container input, .module_payment_container'],
                    ['#module-steasycheckout-default', '.payment-options label, .payment-options input'],
                    ['.payment-options label, .payment-options input', '.payment-options label, .payment-options input'],
                ];
            var i = 0, len = sel.length;
            while (i < len) {
                //console.log('Step ' + i + ' of ' + len + ' Found: '+ $(sel[i][0]).length + 'Is added? ' + paymentAdded);
                if ($(sel[i][0]).length > 0) {
                    $(document).on('mousedown, mouseup', sel[i][1], function() {
                        if (paymentAdded === false) {
                            paymentAdded = true;
                            {if $pp_api}
                            jQuery.ajax({
                                url: pp_aurl,
                                type: 'POST',
                                cache: false,
                                data: {
                                    event: 'AddPaymentInfo',
                                    value: '{$initiate_payment_value|floatval}',
                                    //payment_selected: $(this).closest('.payment_option').find('label').text(),
                                    rand: Math.floor((Math.random() * 100000) + 1)
                                }
                            })
                            .done(function(data) {
                                //console.log(data);
                                if (data.return == 'ok') {
                                    trackAddPaymentInfo(data.event_id);
                                }
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                console.log(
                                    'Conversion could not be sent, contact module developer to check the issue');

                            });
                            {else}
                            trackAddPaymentInfo();
                            {/if}
                        }
                    });
                    if (paymentAdded) {
                        break;
                    }
                    break;
                }
                i++;
            }
            if ($("#payment_method_container").length == 1) {
                setTimeout(function () {
                    onePageCheckoutTracking(10);
                }, 1000);
            }
        }
    }

    // For onepagecheckout compatibilty
    function onePageCheckoutTracking(tries) {
        if ($("#btn_place_order").length == 1) {
            $("#btn_place_order").click(function() {
                if ($("#payment_method_container .selected").length == 1 && paymentAdded === false) {
                    paymentAdded = true;
                    jQuery.ajax({
                        url: pp_aurl,
                        type: 'POST',
                        cache: false,
                        data: {
                            event: 'AddPaymentInfo',
                            value: '{$initiate_payment_value|floatval}',
                            rand: Math.floor((Math.random() * 100000) + 1)
                        }
                    })
                        .done(function(data) {
                            if (data.return == 'ok') {
                                trackAddPaymentInfo(data.event_id);
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log(
                                'Conversion could not be sent, contact module developer to check the issue');
                        });
                }
            });
        } else {
            retry(tries-1);
        }
    }
    function retry(tries) {
        if (tries > 0) {
            console.log('-+-');
            setTimeout(function() { onePageCheckoutTracking(); }, 500);
        }
    }
    function trackAddPaymentInfo(event_id = '')
    {
        if (!event_id) {
            event_id = ppGetCookie('pp_event_start_payment');
            deleteCookie('pp_event_start_payment');
        }
        paymentAdded = true;
        ppTrackEvent('AddPaymentInfo', {
            value: {$initiate_payment_value|floatval},
            currency: '{$fctp_currency|escape:'htmlall':'UTF-8'}',
        }, event_id);
    }
    </script>
    <!-- End Add Payment Info Call -->
