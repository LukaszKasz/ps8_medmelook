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
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 *
*}


{if isset($fcp_product_custom_selector)}
    <!-- Pixel Plus Product Customization  -->
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
                        console.log('PP: Could not initiate the Add To Cart Event Tracking');
                    }
                    return;
                }

                function setCustomProductCookie(name, value, hours) {
                    var expires = "";
                    if (hours) {
                        var date = new Date();
                        date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                        expires = "; expires=" + date.toUTCString();
                    }
                    document.cookie = name + "=" + (value || "") + expires + "; path=/";
                }

                function getCustomProductCookie(name) {
                    var nameEQ = name + "=";
                    var ca = document.cookie.split(';');
                    for (var i = 0; i < ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                    }
                    return null;
                }

                $(".{$fcp_product_custom_selector}").on("click", function () {

                    var purchase_event_id = generateEventId(12);
                    var cP = [];
                    cP['content_name'] = '{$entityname|escape:'htmlall':'UTF-8'}';
                    cP['value'] = pvalue;
                    cP['content_ids'] = {$product_id|intval};
                    cP['content_type'] = 'product';
                    var eI = [];
                    eI['eventID'] = purchase_event_id;

                    ppTrackEvent('CustomizeProduct', cP, eI);
                    //console.log("Event triggered customize")

                    var sc = [];
                    sc[purchase_event_id] = cP;
                    var coG = getCustomProductCookie('pp_custom_product_sent');
                    if (coG == "") {
                        coG = [];
                        var poc = JSON.parse(coG);
                    } else {
                        var poc = [];
                    }
                    poc[purchase_event_id] = cP;
                    setCustomProductCookie('pp_custom_product_sent', JSON.stringify(poc), 1);
                })
            }
        });
    </script>
    <!-- END Pixel Plus Product Vars -->

{/if}