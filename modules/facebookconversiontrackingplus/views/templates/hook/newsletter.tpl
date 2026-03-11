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
 * *                     V 2.3.4                     *
 * **************************************************
 *
*}

<!-- Contact Pixel Call -->
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {
        init_newsletter(10);
        var fb_pixel_newsletter_event_id = '';
        var FCTP_NEWSLETTER_VALUE = '{$FCTP_NEWSLETTER_VALUE|floatval}';
        var max_tries = 0;


        function init_newsletter(tries)
        {
            if (typeof jQuery === 'undefined' || typeof ppGetCookie === 'undefined') {
                if (tries > 0) {
                    setTimeout(function() { init_newsletter(tries-1); }, 350);
                } else {
                    console.log('PP: Could not Initiate the Newsletter Event Tracking');
                }
                return;
            }
            fb_pixel_newsletter_event_id = ppGetCookie('pp_pixel_newsletter_event_id');
            {if isset($register_newsletter) && $register_newsletter == 1}
            fctp_newsletter(10);
            {else}
            $('input[name="submitNewsletter"]').click(function (event) {
                if ($('input[name="email"]').val() != '') {
                    setTimeout(function () {
                        if (jQuery('.block_newsletter').find(".alert-danger").length == 1) {
                            console.log(
                                'Conversion could not be sent, contact module developer to check the issue');
                        } else {
                            fctp_newsletter(10);
                        }
                    }, 1000);
                }
            });
            {/if}
            function fctp_newsletter(max_tries, email = "") {
                if (typeof fbq != 'undefined' && typeof jQuery != 'undefined') {
                    {if isset($fctp_ajaxurl) && $fctp_ajaxurl != ''}
                    jQuery.ajax({
                        url: pp_aurl,
                        type: 'POST',
                        cache: false,
                        data: {
                            event: 'Newsletter',
                            rand: Math.floor((Math.random() * 100000) + 1),
                            source_url: window.location.href
                        }
                    })
                        .done(function (data) {
                            if (data.return == 'ok') {
                                trackNewsletter(email);
                            }
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            console.log('Conversion could not be sent, as the email is already registered');
                        });
                    {else}
                    trackNewsletter();
                    {/if}
                } else {
                    if (tries > 0) {
                        setTimeout(function () {
                            fctp_newsletter(tries - 1)
                        }, 500);
                    }
                }
            }

            function trackNewsletter() {
                ppTrackEvent('Newsletter', {
                    'content_name': '{l s='Newsletter' mod='facebookconversiontrackingplus' js=1}',
                    value: FCTP_NEWSLETTER_VALUE,
                    currency: '{$fctp_currency|escape:'htmlall':'UTF-8'}',
                }, fb_pixel_newsletter_event_id);
            }
        }
    });
</script>
<!-- End Contact Pixel Call -->