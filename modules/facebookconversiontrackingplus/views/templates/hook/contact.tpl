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
    document.addEventListener("DOMContentLoaded", function (event) {
        init(20);
        function init(tries) {
            if (typeof jQuery === 'undefined') {
                if (tries > 0) {
                    setTimeout(() => {
                        init(tries - 1)
                    }, 250);
                } else {
                    console.log('PP: Could not initiate the Contact Event Tracking');
                }
                return;
            }
            init_contact(10);
            var fb_event_contact_page = '{$fb_event_contact_page|escape:'htmlall':'UTF-8'}';
            var contact_value = '{$contact_value|floatval}';
            var subjectSelected = '';

            function init_contact(tries) {
                $(document).ready(function () {
                    $('input[name="submitMessage"]').click(function (event) {
                        if ($('input[name="from"]').val() != '') {
                            subjectSelected = $('#id_contact').find(":selected").text();
                            fctp_contact(10);
                        }
                    });

                    function fctp_contact(max_tries) {
                        {literal}
                        if (typeof fbq != 'undefined' && typeof jQuery != 'undefined') {
                            {/literal}
                            {if isset($fctp_ajaxurl) && $fctp_ajaxurl != ''}
                            {literal}
                            jQuery.ajax({
                                url: pp_aurl,
                                type: 'POST',
                                cache: false,
                                data: {
                                    event: 'Contact',
                                    subject: subjectSelected,
                                    rand: Math.floor((Math.random() * 100000) + 1)
                                }
                            })
                                .done(function (data) {
                                    //console.log(data);
                                    if (data.return == 'ok') {
                                        console.log("here");
                                        // TODO Add return data such as eventID
                                        trackContact();
                                    }
                                })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    console.log(
                                        'Conversion could not be sent, contact module developer to check the issue');

                                });
                            {/literal}
                            {else}
                            trackContact();
                            {/if}
                            {literal}
                        } else {
                            if (tries > 0) {
                                setTimeout(function () {
                                    fctp_contact(tries - 1)
                                }, 500);
                            }
                        }
                    }

                    if (typeof jQuery == 'undefined' || typeof fbq != 'function') {
                        setTimeout(function () {
                            fctp_contact(max_tries - 1)
                        }, 500);
                    }
                });

                function trackContact() {
                    ppTrackEvent('Contact', {
                        'content_name': '{/literal}{l s='Contact' mod='facebookconversiontrackingplus'}',
                        value: contact_value,
                        currency: '{$fctp_currency|escape:'htmlall':'UTF-8'}',{literal}
                        status: true,
                        subject: subjectSelected,
                    }, generateEventId('Contact'));
                }
                {/literal}
            }
        }
    });
</script>
<!-- End Contact Pixel Call -->