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

<script>
    fctp_pageviewcount(20);
    function fctp_pageviewcount(tries) {
        if (typeof jQuery === 'undefined' || typeof fbq != 'function') {
            if (tries > 0) {
                setTimeout(function () {
                    fctp_pageviewcount(tries - 1)
                }, 350);
            }
        } else {
            if (consentStatus) {
                jQuery.ajax({
                    url: pp_aurl,
                    type: 'POST',
                    cache: false,
                    data: {
                        event: 'Pageviewcount',
                        source_url: location.href,
                        token: '{$cookie_token|escape:'htmlall':'UTF-8'}',
                    }
                })
                    .done(function (data) {
                        if (data !== null && data.return == 'ok' && typeof data.current_page !== 'undefined') {
                            var page = data.current_page == 20 ? 'PagesViewedMore' + data.current_page : 'PagesViewed' + data.current_page;
                            ppTrackEvent(page, {
                                'currency': '{$fctp_currency|escape:'htmlall':'UTF-8'}',
                                'value': '{$pageviewcountvalue|floatval}0000'
                            }, pageview_event_id);
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        //console.log('Pixel Plus: Cookie consent could not be validated');
                    });
            }
        }
    }
</script>
