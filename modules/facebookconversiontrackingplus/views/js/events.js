/**
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
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 */

function ppTrackEvent(e, d, e_id) {
    const debug = typeof pp_event_debug !== 'undefined' && pp_event_debug;
    let t = '';
    let ev = ['PageView', 'ViewContent', 'AddToCart', 'Purchase', 'InitiateCheckout', 'CompleteRegistration', 'AddPaymentInfo', 'Search', 'AddToWishlist', 'Contact'];
    let cev = ['ViewCategory', 'CustomizeProduct', 'GuestRegistration', 'ViewCMS', 'Discount', 'Newletter', 'Pagetime', 'Time30s', 'Time60s', 'Time90s', 'Time+120s', 'PagesViewed5', 'PagesViewed10', 'PagesViewed15', 'PagesViewedMore20'];
    if (typeof d === 'object' && (Object.keys(d).length > 0 || e === 'PageView') && e_id != '') {
        if (ev.includes(e)) {
            t = 'track';
        }
        if (cev.includes(e)) {
            t = 'trackCustom';
        }
        // Add a module identifier in the event for better tracability
        d.event_trigger = 'Pixel Plus';
        if (t !== '') {
            if (typeof single_event_tracking !== 'undefined' && single_event_tracking) {
                for (let pid of pixel_ids.split(',')) {
                    fbq(t.replace('track', 'trackSingle'), pid.trim(), e, d, {eventID: e_id});
                }
            } else {
                if (debug) {
                    console.log(t, e, d, {eventID: e_id})
                }
                fbq(t, e, d, {eventID: e_id});
            }
        } else {
            if (debug) {
                console.log('Trying to track a non registered event ' + e);
            }
        }
    }
}

function pixelConsent(valid) {
    /* Function to delete all cookies starting with ... */
    function deleteAllCookies(name) {
        var cookies = document.cookie.split(';');
        cookies.forEach(function(cookie) {
            var cookieName = cookie.split('=')[0].trim();
            let paths = ['/', window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'))];
            let domains = [window.location.host, '.' + window.location.host];

            var now = new Date();
            var expirationTime = new Date(now.getTime() + 3600 * 1000); // 1 hour in milliseconds
            //var expirationTime = 'Thu, 01 Jan 1970 00:00:01 GMT';
            if (cookieName.indexOf(name) === 0) {
                for (let path of paths) {
                    for (let domain of domains) {
                        document.cookie = cookieName + '=; Path=' + path + '; Domain=' + domain + '; Expires= '+ expirationTime +';';
                        console.log(cookieName + '=; Path=' + path + '; Domain=' + domain + '; Expires= '+ expirationTime +';');
                    }
                }
            }
        });
    }

    setTimeout(() => {
        updateConsent();
        // Update the consent state
    }, 250);
    function updateConsent()
    {
        $.ajax({
            url: pp_aurl,
            type: 'POST',
            data: {
                action: 'updateConsent',
                ajax: 1,
                consent: +valid, // Added plus to cast to int
            },
        });
    }
    // console.log((valid ? 'Grant' : 'Revoke') + ' Consent');
    fbq('consent', valid ? 'grant' : 'revoke');
}

// Get cookie by name
function ppGetCookie(cName) {
    const name = cName + "=";
    const cDecoded = decodeURIComponent(document.cookie); //to be careful
    const cArr = cDecoded.split('; ');
    let res;
    cArr.forEach(val => {
        if (val.indexOf(name) === 0) res = val.substring(name.length);
    })
    return res
}
function deleteCookie(name) {
    document.cookie = name + '=; Path=/; Domain=' + window.location.host + '; Expires=Thu, 01 Jan 1970 00:00:01 GMT; Max-age=0';
}

function generateEventId(length) {
    var randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var result = '';
    for (var i = 0; i < length; i++) {
        result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
    }
    return result;
}


function getIPV6() {
    return jQuery.ajax({
        url: 'https://ipv6.smart-modules.com',
        type: 'GET',
        cache: false,
        timeout: 500 // sets timeout to 0.5 seconds
    });
}
function isValidV6(ip) {
    const regexExp = /(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/gi;
    return regexExp.test(ip);
    }

function initializePixel() {
    /*Deferred Loading - Condition*/
    if (typeof deferred_loading !== 'undefined' && deferred_loading) {
        document.addEventListener('readystatechange', (event) => {
            if (document.readyState == "complete") {
                setTimeout(function () {
                    facebookpixelinit(20);
                }, deferred_seconds);
            }
        });
    } else {
        facebookpixelinit(20);
    }
}

// Check if the document is already loaded
if (document.readyState === 'loading') {
    // The document is still loading, wait for the DOMContentLoaded event
    document.addEventListener('DOMContentLoaded', initializePixel);
} else {
    // The document is already loaded, execute immediately
    initializePixel();
}