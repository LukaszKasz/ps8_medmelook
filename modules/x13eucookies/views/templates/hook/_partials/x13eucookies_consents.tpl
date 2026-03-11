<script data-keepinline="true">
    let x13eucookies_consents = {$x13eucookies_consents|json_encode nofilter};
</script>
<script data-keepinline="true">
    const X13EUCOOKIES_RELOAD_PAGE = {$x13eucookies_reload_page};
    const X13EUCOOKIES_PROPERTY_NAME = "{$x13eucookies_cookie_name}";
    const X13EUCOOKIES_AJAX_URL = "{$x13eucookies_ajax_url}";
    const X13EUCOOKIES_DAYS_EXPIRES = {$x13eucookies_config.days_expires};
    const X13EUCOOKIES_LAYOUT = "{$x13eucookies_config.layout}";
    const X13EUCOOKIES_SHOW_EFFECT = '{$x13eucookies_show_effect}';
    const X13EUCOOKIES_BLOCK_IFRAMES = false;
    const X13EUCOOKIES_CONSENTS_GROUPS = {$x13eucookies_consents_simplified_groups|json_encode nofilter};
    const X13EUCOOKIES_MARKETING_COOKIES_ID = {$x13eucookies_marketing_cookies_id};
    const X13EUCOOKIES_MOVE_MODAL_BEFORE_BODY = {$x13eucookies_troubleshooting.move_modal_before_body|default:0};
</script>

{if $x13eucookies_config.send_gtm_consents|default:false}
    <script data-keepinline="true">
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            window.dataLayer.push(arguments);
        }
    {if $x13eucookies_synchronous_consents eq false}
    {*
    ad_storage = Google Ads
    analytics_storage = Google Analytics
    functionality_storage = Functional Cookies
    personalization_storage = Personalization
    security_storage = Security
    ad_personalization = Google Ads Personalization
    ad_user_data = Google Ads User Data *
    *}
        gtag('consent', 'default', {
            'ad_storage': 'denied',
            'analytics_storage': 'denied',
            'functionality_storage': 'granted',
            'personalization_storage': 'denied',
            'security_storage': 'granted',
            'ad_personalization': 'denied',
            'ad_user_data': 'denied',
            'wait_for_update': 1000
        });
        gtag('set', 'url_passthrough', {if $x13eucookies_config.gtm_consents_url_passthrough|default:false eq true}true{else}false{/if});
        gtag('set', 'ads_data_redaction', {if $x13eucookies_config.gtm_consents_ads_data_redaction|default:true eq true}true{else}false{/if});

        // Get consents asynchronously
        {literal}
        const cookies = document.cookie
            .split(";")
            .map((cookie) => cookie.split("="))
            .reduce((acc, [key, value]) => ({ ...acc, [key.trim()]: value }), {});
        const cookie = cookies[X13EUCOOKIES_PROPERTY_NAME] ?? "{}";

        if (cookie != "{}") {
            const cookieJson = JSON.parse(cookie);
            let consents = {};
            let events = [];

            for ([key, value] of Object.entries(X13EUCOOKIES_CONSENTS_GROUPS.gtm.consents)) {
                const tempConsents = value.split(",").map((consent) => {
                    const access = cookieJson[key] ? "granted" : "denied";

                    if (cookieJson[key]) {
                        events = [...events, X13EUCOOKIES_CONSENTS_GROUPS.gtm.events[key]];
                    }

                    consents = {...consents, [consent.trim()]: access};
                });
            }

            const uniqueEvents = [...new Set(events)];

            if (window.gtag) {
                gtag("consent", "update", consents);
                dataLayer.push({ event: "x13eucookies_consent_update" });

                uniqueEvents.forEach((eventName) => {
                    dataLayer.push({ event: eventName });
                })
            }
        }
        {/literal}
    {else}
        gtag('consent', 'default', {
            'ad_storage': {if $x13eucookies_gtm_consents.ad_storage eq true}'granted'{else}'denied'{/if},
            'analytics_storage': {if $x13eucookies_gtm_consents.analytics_storage eq true}'granted'{else}'denied'{/if},
            'functionality_storage': 'granted',
            'personalization_storage': {if $x13eucookies_gtm_consents.personalization_storage eq true}'granted'{else}'denied'{/if},
            'security_storage': 'granted',
            'ad_personalization': {if $x13eucookies_gtm_consents.ad_personalization eq true}'granted'{else}'denied'{/if},
            'ad_user_data': {if $x13eucookies_gtm_consents.ad_user_data eq true}'granted'{else}'denied'{/if}
        });
        gtag('set', 'url_passthrough', {if $x13eucookies_config.gtm_consents_url_passthrough|default:false eq true}true{else}false{/if});
        gtag('set', 'ads_data_redaction', {if $x13eucookies_config.gtm_consents_ads_data_redaction|default:true eq true}true{else}false{/if});
    {/if}
    </script>
{/if}

{if $x13eucookies_config.send_microsoft_consents}
    <script data-keepinline="true">
        window.uetq = window.uetq || [];
        window.uetq.push('consent', 'default', {
            'ad_storage': {if !empty($x13eucookies_microsoft_consents['ad_storage'])}'granted'{else}'denied'{/if}
        });
    </script>
{/if}