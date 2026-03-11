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
    <!-- Initiate Checkout Pixel Call -->
    <script type="text/javascript">
    {if isset($pcart) && !empty($pcart)}
        var pp_items = {$pcart nofilter};
    {/if}
    document.addEventListener('DOMContentLoaded', function() {
        fctp_initiateCheckout(10);
    });

    function fctp_initiateCheckout(max_tries) {
        if (typeof jQuery == 'undefined' || typeof fbq != 'function') {
            if (max_tries > 0) {
                setTimeout(function () {
                    fctp_initiateCheckout(max_tries - 1);
                }, 500);
            } else {
                console.log('PP: Could not start the InitiateCheckout event Tracking');
            }
        } else {
            {if $fbp_custom_checkout == 1}
                trackInitiateCheckout(pp_items, pp_items.length);
            {elseif $entity == 'order'}
                {if $is_17}
                    {if $ic_mode == 1}
                        trackInitiateCheckout(pp_items, '{if isset($cart_qties)}{$cart_qties|intval}{else}pp_items.length{/if}');
                    {/if}
                {else}
                        if ($(".cart_navigation a.standard-checkout").length > 0) {
                            // Was .cart_navigation a.standard-checkout
                            $(document).on('mousedown', ".cart_navigation a.standard-checkout", function(e) {
                            trackInitiateCheckout(pp_items, '{if isset($cart_qties)}{$cart_qties|intval}{else}pp_items.length{/if}');
                            });
                        } else if ($(".cart_navigation a").length > 0) {
                            // Can't find .standard-checkout class try to catch the event
                            $(document).on('mousedown', ".cart_navigation a", function(e) {
                            trackInitiateCheckout(pp_items, '{if isset($cart_qties)}{$cart_qties|intval}{else}pp_items.length{/if}');
                            });
                        }

                {/if}
            {elseif $entity == 'cart' && $ic_mode == 2}

                    if ($('.checkout a').length > 0) {
                        $('.checkout a').click(function(e) {
                            trackInitiateCheckout(pp_items, pp_items.length);
                        });
                    }
            {elseif $entity == 'order-opc'}

                if ($('.step-num').length > 0) {
                    trackInitiateCheckout(pp_items, pp_items.length);
                } else if ($('.checkoutstep.step2').length > 0) {
                    $(document).on('mousedown', '.checkoutstep.step2', function() {
                        trackInitiateCheckout(pp_items, pp_items.length);
                    });
                } else {
                    trackInitiateCheckout(pp_items, pp_items.length);
                }
            {/if}
        }
    }

    function trackInitiateCheckout(items, num_items) {
        if (isNaN(parseInt(num_items))) {
            num_items = items.length;
        }

        let event_data = {
            {if $pp_api}event{else}simple_event{/if}: 'InitiateCheckout',
            id_cart: {if isset($cart->id)}{$cart->id|intval}{else}{$id_cart|intval}{/if},
            rand: Math.floor((Math.random() * 100000) + 1),
            source_url: location.protocol+'//'+
                location.hostname+
                (location.port?":"+location.port:"")+
                location.pathname+
                (location.search?location.search:""),
        }
        jQuery.ajax({
            url: pp_aurl,
            type: 'POST',
            cache: false,
            data: event_data
        })
        .done(function(data) {
            // console.log(data);
            if (typeof data.return === 'undefined') {
                ppTrackEvent('InitiateCheckout', data.custom_data, data.event_id);
            } else {
                {if !$pp_api}
                fireInitiateCheckout(pp_items, pp_items.length);
                {else}
                console.log('Initiate Checkout event could not be sent, contact module developer to check the issue');
                {/if}
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.log('Initiate Checkout event could not be sent, contact module developer to check the issue');
        });
    }
    {if !$pp_api}
    /* Update, only use then API is not active */
    function fireInitiateCheckout(items, num_items) {
        ppTrackEvent('InitiateCheckout', {
            content_ids: items,
            content_type: 'product',
            num_items : num_items,
            content_category: 'Checkout',
            value: {if $initiate_checkout_value > 0}{$initiate_checkout_value|floatval}{else}{if isset($pcart_value)}{$pcart_value|floatval}{else}1{/if}{/if},
            {if isset($pcart_value)}currency: '{$pcart_currency|escape:'htmlall':'UTF-8'}'{/if},
            {if isset($pcart_contents)}contents : {$pcart_contents nofilter},{/if}
            {if isset($fpf_id)}product_catalog_id :  '{$fpf_id|escape:'htmlall':'UTF-8'}',{/if}
        }, generateEventId('InitiateCheckout'));
    }
    {/if}
    </script>
<!-- End Initiate Checkout Pixel Call -->
