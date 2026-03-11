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

{literal}
    <!-- Registration Pixel Call -->
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) { 
        var registered = false;
        {/literal}
        {if $entity == 'supercheckout'}
        if ($('#email').is(':visible')) {
            $('#supercheckout_confirm_order').click(function() {
                if (isEmail($('#email').val()) && registered == false) {
                    fctp_opc_registration(10);
                    registered = true;
                }
            });
        }
        {/if}
        {literal}
        function fctp_opc_registration(tries) {
            if (typeof jQuery == 'undefined' || typeof fbq != 'function' || typeof ppGetCookie === 'undefined') {
                if (tries > 0) {
                    setTimeout(function() {fctp_opc_registration(tries-1)}, 350);
                }
                return;
            } else {
                if (ppGetCookie('pp_register')) { // Only if the registration cookie is present, sent the event.
                    ppTrackEvent('CompleteRegistration', {
                        'content_name' : '{/literal}{l s='Registered Customer' mod='facebookconversiontrackingplus'}{literal}',
                        email : $('#email').val(),
                    }, generateEventId('Purchase'));
                }
            }
        }
        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }
    });
    </script>
    <!-- End Registration Pixel Call -->
{/literal}
