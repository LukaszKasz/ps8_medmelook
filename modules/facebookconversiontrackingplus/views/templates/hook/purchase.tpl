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
<!-- Facebook Register Checkout Pixel -->

    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            var fctp_cookie_control = {$fctp_cookie_control|intval}
            trackPurchase(20);

            function trackPurchase(tries) {
                if (typeof fbq === 'undefined' && typeof jQuery === 'undefined') {
                    if (tries > 0) {
                        setTimeout(function () {
                            trackPurchase(tries - 1)
                        }, 250);
                    } else {
                        console.log('PP: Could not track the Purchase event');
                    }
                    return;
                }
                {if isset($ordervars.aurl) && $ordervars.aurl != ''}
                    jQuery.ajax({
                            url: '{$ordervars.aurl|escape:'htmlall':'UTF-8'}',
                            type: 'POST',
                            cache: false,
                            data: {
                                id_order : '{$ordervars.id_order|intval}',
                                id_customer : '{$ordervars.id_customer|intval}',
                                {if $pp_api}event{else}simple_event{/if}: 'Purchase',
                                {if isset($fb_event_purchase_page)}event_id: '{$fb_event_purchase_page|escape:'htmlall':'UTF-8'}',{/if}
                                fctp_token: '{$purchase_token|escape:'htmlall':'UTF-8'}',
                                rand: Math.floor((Math.random() * 100000) + 1)
                            }
                        })
                        .done(function(data) {
                            if (data.return == 'ok') {
                                firePurchase();
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log(
                                'Conversion could not be sent, contact module developer to check the issue');
                        });
                {else}
                    firePurchase();
                {/if}
            }

            function firePurchase() {
                ppTrackEvent('Purchase', {
                    'value':'{$ordervars.ordervalue|floatval}',
                    {if $ordervars.shipping_value > 0}'shipping': {$ordervars.shipping_value|floatval},{/if}
                    'payment_module' : '{$ordervars.payment_module|escape:'htmlall':'UTF-8'}',
                    'currency':'{$ordervars.currency|escape:'htmlall':'UTF-8'}',
                    'order_id':'{$ordervars.id_order|intval}',
                    'order_reference' : '{$ordervars.order_reference|escape:'htmlall':'UTF-8'}',
                    'num_items':'{$ordervars.product_quantity|intval}',
                    {if isset($pcart_contents)}'contents' : {$pcart_contents nofilter},{/if}
                    'content_type': 'product',
                    'content_ids' : {$ordervars.product_list nofilter},
                    {if isset($fpf_id)}product_catalog_id:  '{$fpf_id|escape:'htmlall':'UTF-8'}'{/if}
                    }{if isset($fb_event_purchase_page)}, '{$fb_event_purchase_page|escape:'htmlall':'UTF-8'}'{/if});
                if (fctp_cookie_control) {
                    setCookie('pp_purchaseSent', {$ordervars.id_order|intval}, 6);
                }
            }

            function setCookie(name, value, hours) {
                var expires = "";
                if (hours) {
                    var date = new Date();
                    date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }
            });
        </script>
    
    <!-- END Facebook Register Checkout Pixel -->