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
    document.addEventListener("DOMContentLoaded", function(event) {
    if (document.readyState == "interactive") {
        setTimeout(function() {
            init_registrations(10);
        }, 6000);

        function init_registrations(tries)
        {
            if (typeof jQuery === 'undefined' || typeof fbq === 'undefined') {
                if (tries > 0) {
                    setTimeout(function () {
                        init_registrations(tries - 1);
                    }, 350);
                } else {
                    console.log('PP: Could not initiate the Registration event');
                }
                return;
            }
            {if isset($registeron) && $registeron == 1}
            fctp_registration(10);
            {else}
            {* Guest Tracking converting to customer *}
            $('button[name="submitTransformGuestToCustomer"]').click(function() {
                if ($('input[name="password"]').val() != '') {
                    fctp_registration(10);
                }
            });
            {/if}
            function fctp_registration(tries)
            {
                if (typeof fbq != 'undefined' && typeof jQuery != 'undefined') {
                    {if isset($fctp_ajaxurl) && $fctp_ajaxurl != ''}
                    $.ajax('{$fctp_ajaxurl|escape:'htmlall':'UTF-8'}?trackRegister=1')
                    .done(function (data) {
                        // Conversion tracked
                        if (data.return == 'ok') {
                            trackRegistration();
                        }
                    })
                    .fail(function() {
                        console.log('Conversion could not be sent, contact module developer to check the issue');
                    });
                    {else}
                    trackRegistration();
                    {/if}
                } else {
                    if (tries > 0) {
                        setTimeout(function() { fctp_registration(tries-1) }, 500);
                    }
                }
            }
            if (typeof jQuery == 'undefined' || typeof fbq != 'function') {
                setTimeout(function() { fctp_registration(tries-1)},500);
            }
            function trackRegistration()
            {
                var event_id = ppGetCookie('pp_register');
                {if isset($is_guest) && $is_guest}
                    ppTrackEvent('GuestRegistration', {
                        'content_name' : '{l s='Registered Guest' mod='facebookconversiontrackingplus'}',
                        value: {$complete_registration_value|floatval},
                        currency : '{$fctp_currency|escape:'htmlall':'UTF-8'}',
                        status: true,
                    }, event_id);
                {else}
                    ppTrackEvent('CompleteRegistration', {
                        'content_name' : '{l s='Registered Customer' mod='facebookconversiontrackingplus'}',
                        value: {$complete_registration_value|floatval},
                        currency : '{$fctp_currency|escape:'htmlall':'UTF-8'}',
                        status: true,
                    }, event_id);
                {/if}
                deleteCookie('pp_register');
            }
        }
      }
    });
</script>
<!-- End Registration Pixel Call -->
