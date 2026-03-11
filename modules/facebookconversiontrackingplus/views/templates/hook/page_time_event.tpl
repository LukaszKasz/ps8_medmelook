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
    document.addEventListener("DOMContentLoaded", function() {
        init_time_event(10);
        var time = 0;

        function init_time_event(tries) {
            //Main script start
            if (typeof jQuery === 'undefined' || typeof fbq === 'undefined') {
                if (tries > 0) {
                    setTimeout(function () {
                        init_time_event(tries - 1)
                    }, 500);
                } else {
                    console.log('PP: Could not Initiate the Page Time event');
                }
                return;
            }
            var counter = 0;
            var timer = new IntervalTimer(function () {
                if (counter < 4) {
                    time = time + 30;
                    if (time == 120) {
                        time = '+120';
                    }
                    fctp_pagetime(tries - 1, time);
                    counter++;
                }
            }, 30000); // WAS 30000

            document.addEventListener("visibilitychange", function () {
                if (document.visibilityState === 'visible') {
                    timer.resume();
                } else {
                    timer.pause();
                }
            });
        }
        function IntervalTimer(callback, interval) {
            var timerId, timeoutId, startTime, remaining = 0;
            var state = 0; //  0 = idle, 1 = running, 2 = paused, 3= resumed
            this.interval = interval;
            this.pause = function () {
                if (state != 1 && state != 3) return;
                remaining = this.interval - (new Date() - startTime);
                if (state == 1) window.clearInterval(timerId);
                if (state == 3) window.clearTimeout(timeoutId);
                state = 2;
            };

            this.resume = function () {
                if (state != 2) return;
                state = 3;
                timeoutId = window.setTimeout(this.timeoutCallback, remaining);
            };

            this.timeoutCallback = function () {
                if (state != 3) return;
                callback();
                startTime = new Date();
                timerId = window.setInterval(callback, interval);
                state = 1;
            };

            startTime = new Date();
            timerId = window.setInterval(callback, interval);
            state = 1;
        }

        function fctp_pagetime(tries)
        {
            {if isset($fctp_ajaxurl) && $fctp_ajaxurl != ''}
            jQuery.ajax({
                url: pp_aurl,
                type: 'POST',
                cache: false,
                data: {
                    event: 'Pagetime',
                    source_url: location.href,
                    time: time,
                    rand: Math.floor((Math.random() * 100000) + 1)
                }
            })
            .done(function(data) {
                if (data.return == 'ok') {
                    trackPageTime(data);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.log('Conversion could not be sent, contact module developer to check the issue');
            });
            {else}
                trackPageTime({literal}{event_id:ppGetCookie('pp_pixel_event_id'){/literal});
            {/if}
        }
        function trackPageTime(data)
        {
            if (!validateTime(time)) {
                return false;
            }
            var eventName = time == 0 ? 'Pagetime' : 'Time'+time+'s';
            ppTrackEvent(eventName, {
                'content_name' : '{l s='PageTime' mod='facebookconversiontrackingplus'}',
                value: {$FCTP_PAGETIME_VALUE|floatval},
                currency : '{$fctp_currency|escape:'htmlall':'UTF-8'}',
                status: true,
                time : time+'s',
            },  data.event_id);
        }
        function validateTime(time) {
            let tmpTime = time.toString().replace('+', ''); // Remove the plus symbol
            return !isNaN(tmpTime) && parseInt(tmpTime) > 0; // Check if it's a positive number
        }
    });
</script>
<!-- End Contact Pixel Call -->