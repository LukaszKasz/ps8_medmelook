{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="application/javascript">
    var apaczkaCarriers = {$apaczka_carriers_json|cleanHtml nofilter};
    var apaczkaCarriersNamesPoints = []; 
    for (let idCarrier in apaczkaCarriers) {
        if(apaczkaCarriers[idCarrier].points == 1) {
            apaczkaCarriersNamesPoints.push(apaczkaCarriers[idCarrier].apaczkaName);
        }
    }

    {foreach $apaczka_carriersConfig as $carrier_reference => $config}
        var apaczkaMap{$config['id_carrier']} = new ApaczkaMap({
            app_id: "{$apaczka_apiKey|escape:'htmlall':'UTF-8'}",
            countryCode: "{$countryCode}",
            hideServicesCod: true,
            criteria: {
                field: "services_receiver",
                operator: "eq",
                value: true
            },
        {if $config['cod']}
                criteria: {
                    field: "services_cod", 
                    operator: "eq", 
                    value: true
                },
            {/if}
            
            onChange : function(record) {
                var idCarrierSelected = {$config['id_carrier']|escape:'htmlall':'UTF-8'};
                var idCarrierReference =  {$carrier_reference|escape:'htmlall':'UTF-8'};
                var cod = parseInt({$config['cod']|escape:'htmlall':'UTF-8'});

                if (!!record) {
                    if(cod && !(cod && record.services_cod)) {
                        alert("{l s='Chosen service point does not support cash on delivery.' mod='apaczka'}");
                        return;
                    }

                    if (apaczkaCarriers[idCarrierReference].apaczkaName != record.supplier) {
                        var selected = false; 
                        for (let idCarrier in apaczkaCarriers) {
                            if (apaczkaCarriers[idCarrier].apaczkaName == record.supplier && apaczkaCarriers[idCarrier].points) {
                                idCarrierSelected = apaczkaCarriers[idCarrier].id_carrier;
                                selected=true;
                                break;
                            }
                        }

                        if (!selected) {
                            alert("{l s='Chosen carrier has not been configured in the shop. Choose different carrier.' mod='apaczka'}");
                            return;
                        }
                    } 

                    var apaczkaDeliveryPoint = document.getElementById("apaczka_delivery_point_"+idCarrierSelected);
                    var apaczkaDeliveryLabel = document.getElementById("apaczka_delivery_point_label_"+idCarrierSelected); 
                    var apaczkaDeliveryOption = document.getElementById("delivery_option_"+idCarrierSelected); 
                    
                    if (apaczkaDeliveryPoint != null) {
                        apaczkaDeliveryPoint.value=record.foreign_access_point_id;
                    }
                    
                    if (apaczkaDeliveryLabel != null) {
                        apaczkaDeliveryLabel.innerHTML= record.name + ", " + record.street + ", " + record.city + " (" + record.foreign_access_point_id + ")";
                    }
                    
                    var confirmButtons = document.querySelectorAll("button[name=confirmDeliveryOption]"); 
                    
                    for (let button in confirmButtons) {
                        button.disabled=false;
                    }
                    
                    if (apaczkaDeliveryOption != null) {
                        apaczkaDeliveryOption.checked=true;
                        apaczkaDeliveryOption.dispatchEvent(new Event("change"));
                    }
                }
            }
        });
    
        apaczkaMap{$config['id_carrier']|escape:'htmlall':'UTF-8'}.setFilterSupplierAllowed(
            apaczkaCarriersNamesPoints, 
            ["{$config['apaczkaName']|escape:'htmlall':'UTF-8'}"]
        );
        apaczkaMap{$config['id_carrier']|escape:'htmlall':'UTF-8'}.setSupplier(
            "{$config['apaczkaName']|escape:'htmlall':'UTF-8'}"
        );
    {/foreach}

    function apaczkaLoadCarrier(idMap, supplier, showMap) {
        var radioCarrierEl = document.getElementById("delivery_option_"+idMap);

        if (radioCarrierEl != null) {
            var parentEl = radioCarrierEl;

            for(var i=0; i<20; i++) {
                parentEl = parentEl.parentElement;

                if (parentEl == null) {
                    break;
                }

                if (parentEl.className != null && parentEl.className.indexOf("delivery-option") !== -1) {    
                    var pointValue = "";
                    
                    if (supplier == "{$apaczka_cartRow['apaczka_supplier']|escape:'htmlall':'UTF-8'}" && "{$apaczka_cart->id_carrier|escape:'htmlall':'UTF-8'}" == idMap) {
                        pointValue = "{$apaczka_cartRow['apaczka_point']|escape:'htmlall':'UTF-8'}";
                    }

                    {literal}
                        parentEl.insertAdjacentHTML(
                            "beforeend", 
                            `<input 
                                type="hidden" 
                                id="apaczka_delivery_point_${idMap}" 
                                name="apaczka_delivery_point[${idMap}]" 
                                value="${pointValue}"
                            >`
                        );

                        parentEl.insertAdjacentHTML(
                            "beforeend", 
                            `<input 
                                type="hidden" 
                                id="apaczka_supplier_${idMap}"
                                name="apaczka_supplier[${idMap}]" 
                                value="${supplier}">`
                        );
                    {/literal}

                    if (showMap) {
                        let nopoint = '';
                        if (({$apaczka_cart->id_carrier|escape:'htmlall':'UTF-8'} == idMap) && ("{$apaczka_cartRow['apaczka_point']|escape:'htmlall':'UTF-8'}" != "")) {
                            nopoint = "{$apaczka_cartRow['apaczka_point']|escape:'htmlall':'UTF-8'}";
                        } else {
                            nopoint = `<div class="apaczka-no-point w-100">{l s='Choose service point' mod='apaczka'}</div>`
                        }

                        let addressObjTxt = "{$apaczka_addressObjTxt|cleanHtml|default:'' nofilter}";

                        {literal}
                            parentEl.insertAdjacentHTML(
                                "beforeend", 
                                `<div 
                                    data-id-carrier="${idMap}" 
                                    style="display: none;"
                                    class="apaczka-additional-div w-100 p-1 font-weight-bold"
                                >
                                    <div id="apaczka_delivery_point_label_${idMap}">
                                        ${nopoint}
                                    </div>
                                    <button 
                                        type="button" 
                                        class="btn btn-primary apaczka-open-map" 
                                        onclick="javascript:apaczkaMap${idMap}.show(${addressObjTxt});"
                                    >
                                        Otwórz mapę
                                    </button>
                                </div>`
                            );
                        {/literal}
                    }
                    break;
                }
            }
        }
    }

    window.onload = function() {
        {foreach $apaczka_carriersConfig as $carrier_reference => $config}
            apaczkaLoadCarrier({$config['id_carrier']|escape:'htmlall':'UTF-8'}, "{$config['apaczkaName']}", {if $config['points']}1{else}0{/if});
        {/foreach}

        {if $apaczka_carriersPoints}
            //show map section (start value):
            var selectedCarrierRadios = document.querySelectorAll('[type=\"radio\"][name^=\"delivery_option\"]:checked')                    
            if (selectedCarrierRadios.length > 0) {
                
                var val = selectedCarrierRadios[0].value.split(',')[0].trim();
                var additionalDivsToShow = document.querySelectorAll('div.apaczka-additional-div[data-id-carrier=\"'+val+'\"]');
                
                if (additionalDivsToShow.length > 0) {
                    additionalDivsToShow[0].style.display='block';
                }
            }
            
            var idsCarriersPoints = {$apaczka_carriersPoints_json|cleanHtml nofilter};
            var radios = document.querySelectorAll('[type=\"radio\"][name^=\"delivery_option\"]');
            
            if (radios == null) {
                return; 
            }

            for(let i=0; i<radios.length; i++) {
                radios[i].addEventListener('change', function(event) {
                    //hide all maps sections
                    var additionalDivs = document.querySelectorAll('div.apaczka-additional-div');

                    for(k=0; k<additionalDivs.length; k++) {
                        additionalDivs[k].style.display='none';
                    }
                    
                    //show single map section 
                    var val = event.target.value.split(',')[0].trim();
                    
                    var additionalDivsToShow = document
                        .querySelectorAll('div.apaczka-additional-div[data-id-carrier=\"'+val+'\"]');
                    
                    if (additionalDivsToShow.length > 0) {
                        additionalDivsToShow[0].style.display='block';
                    }
                    
                    if (idsCarriersPoints.includes( event.target.value.split(',')[0].trim() ) ) {
                        var selectedPoint = document
                            .getElementById('apaczka_delivery_point_'+event.target.value.split(',')[0].trim());

                        if (selectedPoint == null || (selectedPoint != null && selectedPoint.value=='') ) {
                            document.querySelectorAll('button[name=confirmDeliveryOption]')[0].disabled=true;
                        } else {
                            document.querySelectorAll('button[name=confirmDeliveryOption]')[0].disabled=false;
                        }
                    } else {
                        document.querySelectorAll('button[name=confirmDeliveryOption]')[0].disabled=false;
                    }

                }); 
            }
            
            //sprawdzenie czy wybrano pkt
            var buttons = document.querySelectorAll('button[type=\"submit\"][name^=\"confirmDeliveryOption\"]');
            
            if (buttons == null) {
                return; 
            }

            for(let i=0; i<buttons.length; i++) {
                buttons[i].addEventListener('click', function(event) {
                    var selectedRadios = document.querySelectorAll('[type=\"radio\"][name^=\"delivery_option\"]:checked');
                    
                    if (selectedRadios.length == 0) {
                        return; 
                    }
                    
                    var selectedPointInput = document.getElementById('apaczka_delivery_point_'+selectedRadios[0].value.split(',')[0].trim());
                    var selectedPoint = '';
                    
                    if (selectedPointInput != null) {
                        selectedPoint = selectedPointInput.value.trim();
                    }

                    if (idsCarriersPoints.includes(selectedRadios[0].value.split(',')[0].trim()) &&  selectedPoint=='' ) {
                        alert('{l s='Service point not chosen!' mod="apaczka"}');
                        event.stopPropagation();
                        event.preventDefault();
                        return;
                    }
                });
                
                var selectedRadios = document.querySelectorAll('[type=\"radio\"][name^=\"delivery_option\"]:checked');

                if (selectedRadios.length > 0) {
                    selectedRadios[0].dispatchEvent(new Event('change'));
                }
            }
        {/if}
    };
</script>