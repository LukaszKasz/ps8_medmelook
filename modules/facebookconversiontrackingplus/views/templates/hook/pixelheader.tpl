{*
 * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 *
 * **************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * **************************************************
 *
*}
<!-- Enable Facebook Pixels -->
<script>
    // doNotConsentToPixel = false;
    //console.log(ppGetCookie('pp_pageview_event_id'));
    //var external_id = ppGetCookie('pp_external_id');
    var pageview_event_id = '';
    var pp_price_precision = {if isset($price_precision)}{$price_precision|intval}{else}2{/if};
    var deferred_loading = {$deferred_loading|intval};
    var deferred_seconds = {if isset($deferred_seconds) || $deferred_seconds == 0}{$deferred_seconds|escape:'htmlall':'UTF-8'}{else}4000{/if};
    var event_time = {$event_time|escape:'htmlall':'UTF-8'};
    var local_time = new Date().getTime();
    var consentStatus = true;
    var pp_aurl = '{$ajax_events_url|escape:'htmlall':'UTF-8'|replace:'&amp;':'&'}'.replace(/&amp;/g, "&");

    // Check if pixel is already initialized
    function facebookpixelinit(tries) {
        let ud = {if $pp_advanced_match && isset($user_data)}{$user_data nofilter}{else}{literal}{}{/literal}{/if};
        if (typeof fbq == 'undefined') {
            // Pixel is not initialized, load the script
            initFbqPixels(ud);
        } else {
            console.log('Facebook Pixel Already loaded');
        }

        // Proceed with consent and initialize Pixels
        handleConsentAndInitPixels(ud);

        // Send the PageView event
        sendPageViewEvent()
    }

    function initFbqPixels() {
        {literal}
        !function(f,b,e,v,n,t,s){if (f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if (!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
        {/literal}
    }

    function handleConsentAndInitPixels(ud) {
        {if !$pixel_consent}
        consentStatus = false;
        {else}
        if (typeof window.doNotConsentToPixel !== 'undefined' && doNotConsentToPixel) {
            consentStatus = false;
        }
        {/if}
        pixelConsent(consentStatus);

        {foreach from=$fctpid item="pixel_id" name="pixelforeach"}
        fbq('init', '{strip}{$pixel_id|escape:'htmlall':'UTF-8'}{/strip}', ud);
        {/foreach}
    }
    function sendPageViewEvent() {
        /* Code to avoid multiple pixels call */
        /* Used to make it compatible with onepagecheckout */
        if (typeof window.fbq_pageview == 'undefined') {
            pageview_event_id = generateEventId(12);
            ppTrackEvent('PageView', {literal}{}{/literal}, pageview_event_id);
            if (consentStatus) {
                return jQuery.ajax({
                    url: pp_aurl,
                    type: 'POST',
                    cache: false,
                    data: {
                        event: 'PageView',
                        pageview_event_id: pageview_event_id,
                        source_url: window.location.href
                    }
                });
            }
            window.fbq_pageview = 1;  // Mark pageview as processed
        }
    }

    // Consent and localStorage checks, unchanged
    {if isset($consent_check) && $consent_check && !$pixel_consent && !$consent_mode_check_cookies && $pp_local_storage_data.values != ''}
        function checkLocalStorage() {
            let lc = [];
            try {
                lc = JSON.parse(localStorage.getItem('{$pp_local_storage_data.var|escape:'htmlall':'UTF-8'}'));
            } catch (e) {
                console.log('PP: Could not locate and process the local storage data, review the settings');
                console.log('PP: Current value to be searched: {$pp_local_storage_data.var|escape:'htmlall':'UTF-8'}');
            }
            console.log(lc);
            let values = {$pp_local_storage_data.values nofilter}; {* It's a JSON so can't be escaped *}
            console.log(values);
            if (lc !== null) {
                for (let i = 0; i < values.length; i++) {
                    if (typeof lc[values[i]] !== 'undefined') {
                        lc = lc[values[i]];
                        if (i == values.length - 1) {
                            let r = '{$pp_local_storage_data.last_value|escape:'htmlall':'UTF-8'}';
                            if (lc === r || lc === parseInt(r)) {
                                setTimeout(function () {
                                    if (!consentStatus) {
                                        jQuery.ajax({
                                            url: pp_aurl,
                                            type: 'POST',
                                            cache: false,
                                            dataType: "json",
                                            data: {
                                                localStorageConsent: true,
                                                token: '{$cookie_token|escape:'htmlall':'UTF-8'}',
                                            }
                                        })
                                            .done(function (data) {
                                                consentStatus = true;
                                                console.log(data);
                                                pixelConsent(data.return == 'ok');
                                            })
                                            .fail(function (jqXHR, textStatus, errorThrown) {
                                                console.log('Pixel Plus: Local Storage consent could not be validated');
                                            });
                                    }
                                }, 1500);
                            }
                        }
                    }
                }
            }
        }
        checkLocalStorage();
    {/if}
    {if isset($consent_check) && $consent_check && !$pixel_consent && !$cookie_reload}
    let checking_consent = false;
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click mousedown mouseup', '{$cookie_check_button|escape:'htmlall':'UTF-8'}', function() {
                console.log('Checking consent...');
                if (!checking_consent) {
                    checking_consent = true;
                    {if $consent_mode_check_cookies}
                    setTimeout(function() {
                        jQuery.ajax({
                            url: pp_aurl,
                            type: 'POST',
                            cache: false,
                            dataType: "json",
                            data: {
                                cookieConsent: true,
                                token : '{$cookie_token|escape:'htmlall':'UTF-8'}',
                            }
                        })
                        .done(function(data) {
                            consentStatus = true;
                            pixelConsent(data.return == 'ok');
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('Pixel Plus: Cookie consent could not be validated');
                        });
                    }, 1500);
                    {else}
                    //console.log('localStorageCheck');
                    // Local Storage checks
                    {if isset($pp_local_storage_data)}
                    //console.log('check Local Storage');
                    checkLocalStorage();
                    {else}
                    console.log('PP: Validation from Local Storage can\'t proceed. The value to search has not been set');
                    {/if}
                    {/if}
                }
                setTimeout(() => { checking_consent = false}, 500);
            });
        });
    {/if}
</script>
<!-- End Enable Facebook Pixels -->
