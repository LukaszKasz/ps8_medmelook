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
 * *                                                 *
 * **************************************************
 *
*}

<!-- Contact Pixel Call -->
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {
        init(20);
        function init(tries) {
            if (typeof jQuery === 'undefined') {
                if (tries > 0) {
                    setTimeout(() => {
                        init(tries - 1)
                    }, 250);
                } else {
                    console.log('PP: Could not initiate the Discount Event Tracking');
                }
                return;
            }
            var fb_pixel_discount_event_id = ppGetCookie('pp_pixel_discount_event_id');
            var fctp_discount_value = '{$fctp_discount_value|floatval}';
            var coupon_name;
            var discount_percentage;
            var discount_amount;
            var free_shipping;
            var limited;
            var discount_code;

            init_discount();

            function init_discount()
            {
                if ($('input[name="discount_name"]').length == 1) {
                    $('button[type="submit"]').on('click', function(event) {
                        discount_code = $('input[name="discount_name"]').val();
                        setTimeout(function() {
                            fctp_discount();
                        }, 1000);
                    });
                }
                {literal}
                function fctp_discount()
                {
                    {/literal}
                    {if isset($fctp_ajaxurl) && $fctp_ajaxurl != ''}
                    {literal}
                    var discount_coupon = $('.js-cart-voucher').find('ul').find('li').find('.label').last().text();
                    jQuery.ajax({
                        url: pp_aurl,
                        type: 'POST',
                        cache: false,
                        data: {
                            event: 'Discount',
                            discount_coupon: discount_code,
                            rand: Math.floor((Math.random() * 100000) + 1),
                            source_url: window.location.href
                        }
                    })
                        .done(function(data) {

                            if (data.return == 'ok') {
                                coupon_name = data.params.coupon_name;
                                free_shipping = data.params.free_shipping;
                                limited = data.params.limited;
                                if (data.params.discount_percentage > 0)
                                {
                                    discount_percentage = data.params.discount_percentage;
                                } else {
                                    discount_amount = data.params.discount_amount;
                                }
                                trackDiscount();
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('Conversion could not be sent, as the email is already registered');
                        });
                    {/literal}
                    {else}
                        trackDiscount();
                    {/if}
                    {literal}
                }

                function trackDiscount() {
                    let data = {
                        'content_name' : '{/literal}{l s='Discount' mod='facebookconversiontrackingplus'}',
                        'coupon_name' : coupon_name,
                        'free_shipping' : free_shipping,
                        'limited' : limited,
                        'value' : fctp_discount_value,
                        'currency' : '{$fctp_currency|escape:'htmlall':'UTF-8'}'
                    };
                    if (discount_percentage > 0) {
                        data.discount_percentage = discount_percentage;
                    } else {
                        data.discount_amount = discount_amount;
                    }
                    ppTrackEvent('Discount', data, fb_pixel_discount_event_id);
                }
            }
        }
    });
</script>
<!-- End Contact Pixel Call -->