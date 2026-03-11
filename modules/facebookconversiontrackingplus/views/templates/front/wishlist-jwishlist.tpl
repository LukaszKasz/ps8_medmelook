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
<!-- Wishlist Pixel Call -->
<script type="text/javascript">
    //console.clear();
    document.addEventListener('DOMContentLoaded', function() {
    console.log('init');
        var fctp_wishlist = {$fctp_wishlist nofilter}; {* Can't escape, it's a JSON object *}
        fctp_addToWishlist(20);
        function fctp_addToWishlist(max_tries) {
            if (typeof jQuery == 'undefined' || typeof fbq != 'function') {
                setTimeout(function() { fctp_addToWishlist(max_tries - 1); }, 250);
            } else {
                if ($(fctp_wishlist.selector).length > 0) {
                    $(document).ajaxComplete(function (request, jqXHR, settings) {
                        if (settings.url.indexOf(fctp_wishlist.name) >= 0) {
                            // Successful AddToWishlist
                            const params = new Proxy(new URLSearchParams(settings.data), {
                                get: (searchParams, prop) => searchParams.get(prop),
                            });
                            jQuery.ajax({
                                url: pp_aurl,
                                type: 'POST',
                                cache: false,
                                data: {
                                    id_product : params.id_product,
                                    id_product_attribute : params.id_product_attribute,
                                    quantity : params.quantity,
                                    event: 'AddToWishlist',
                                    rand: Math.floor((Math.random() * 100000) + 1)
                                }
                            })
                                .done(function(data) {
                                    //console.log(data);
                                    if (data.return != 'error') {
                                        ppTrackEvent('AddToWishlist', data.custom_data, data.event_id);
                                        window.fctp_wishlist_act = true;
                                        setTimeout(function() { window.fctp_wishlist_act = false; }, 500);
                                    }
                                })
                                .fail(function(jqXHR, textStatus, errorThrown) {
                                    console.log(
                                        'Pixel Plus: Failed to track the AddToWishlist Event');
                                });
                        }
                    });
                }
            }
        }
    });
</script>
<!-- End Wishlist Pixel Call -->