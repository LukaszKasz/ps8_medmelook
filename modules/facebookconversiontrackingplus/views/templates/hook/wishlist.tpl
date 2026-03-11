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
<!-- Registration Pixel Call -->
<script type="text/javascript">
    var fctp_wishlist_act = true;
    document.addEventListener('DOMContentLoaded', function () {
        fctp_addToWishlist(10);
    });

    function fctp_addToWishlist(max_tries) {
        if (typeof jQuery == 'undefined' || typeof fbq != 'function') {
            if (max_tries > 0) {
                setTimeout(function () {
                    fctp_addToWishlist(max_tries - 1);
                }, 250);
            } else {
                console.log('PP: Could not initiate the AddToWishlist event');
            }
            return;
        }
        var wishlist_custom_button = '.btn-iqitwishlist-add';
        if ($(wishlist_custom_button).length > 0) {
            $(wishlist_custom_button).click(function(e) {
                window.fctp_wishlist_act = false;
                var id_product_wish = $(this).attr('data-id-product');
                var id_product_attribute_wish = $(this).attr('data-id-product-attribute');
                var id_combination = '';
                {if $combi_enabled}
                if (id_product_attribute_wish > 0) {
                    id_combination = '{$combi_prefix|escape:'htmlall':'UTF-8'}' + id_product_attribute_wish;
                }
                {/if}
                var pid = '{$id_prefix|escape:'htmlall':'UTF-8'}' + id_product_wish + id_combination;
                trackWishlist(pid);
            });

            function trackWishlist(pid_wish) {
                if (window.fctp_wishlist_act == false) {
                    window.fb_pixel_wishlist_event_id = window.generateEventId(12);
                    $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                        // When friednly url not enabled fc=module&module=iqitwishlist&controller=actions
                        // When friednly url is enabled module/iqitwishlist/actions
                        var wishlistUrl = originalOptions.url;
                        console.log(originalOptions);
                        if (typeof wishlistUrl != 'undefined') {
                            var checkURLSEO = wishlistUrl.search('module/iqitwishlist/actions');
                            var checkURLnonseo = wishlistUrl.search(
                                'fc=module&module=iqitwishlist&controller=actions');
                            if (typeof originalOptions.data !== 'undefined' && (checkURLSEO > - 1 || checkURLnonseo > -1)) {
                                console.log("Found wishlist url");
                                if (options.data.indexOf('&fb_pixel_wishlist_event_id') === - 1) {
                                    options.data += '&fb_pixel_wishlist_event_id=' +
                                        fb_pixel_wishlist_event_id;
                                }
                            }
                        }

                    });
                    ppTrackEvent('AddToWishlist', {
                        value: {$wishlist_value|floatval},
                        currency: '{$fctp_currency|escape:'htmlall':'UTF-8'}',
                        content_type: 'product',
                        content_ids: [pid_wish]
                    }, window.fb_pixel_wishlist_event_id);
                    /* Prevent duplicates */
                    window.fctp_wishlist_act = true;
                    setTimeout(function() { window.fctp_wishlist_act = false; }, 500);
                }
            }
        }
    }
</script>
<!-- End Registration Pixel Call -->
