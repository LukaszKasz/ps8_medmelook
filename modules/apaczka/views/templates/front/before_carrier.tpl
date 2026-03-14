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

<style>
    .shipping-radio {
        width: 30px !important;
        max-width: 30px !important;
        flex: 0 0 30px !important;
    }

    img[src="https://mapa.apaczka.pl/img/map/logo.png"],
    img[src*="mapa.apaczka.pl/img/map/logo.png"][alt="Apaczka"] {
        display: none !important;
    }

    .apaczka-additional-div {
        box-sizing: border-box;
        width: 250px;
        max-width: 100%;
        margin-top: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    .delivery-option-row.apaczka-pickup-carrier .delivery-option-label.has-logo ~ .apaczka-additional-div {
        width: 200px;
        max-width: 100%;
    }

    .delivery-option-row.apaczka-selected-delivery {
        background: #f4f8ff !important;
        border-radius: 8px;
        box-shadow: inset 0 0 0 1px rgba(48, 93, 217, 0.18);
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }

    .delivery-option-logo,
    .delivery-option-logo img {
        width: 40px !important;
        height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        object-fit: contain;
    }

    .delivery-option-row.apaczka-pickup-carrier.apaczka-hide-logo .delivery-option-logo {
        display: none !important;
    }

    .delivery-option-row.apaczka-pickup-carrier.apaczka-hide-logo .delivery-option-name {
        width: 100% !important;
        max-width: 100% !important;
        flex: 1 1 100% !important;
    }

    .delivery-option-row.apaczka-pickup-carrier.apaczka-hide-logo .delivery-option-label.has-logo .delivery-option-name,
    .delivery-option-row.apaczka-pickup-carrier.apaczka-hide-logo .delivery-option-label.has-logo .delivery-option-delay {
        margin-right: 0 !important;
    }

    @media (max-width: 767px) {
        .apaczka-additional-div {
            display: block;
            width: 155px !important;
            max-width: 155px !important;
            min-width: 0 !important;
            margin-left: auto !important;
            margin-right: auto !important;
            overflow: hidden;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .apaczka-additional-div > div,
        .apaczka-additional-div [id^="apaczka_delivery_point_label_"] {
            display: block;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .apaczka-open-map {
            width: 100%;
        }

        .delivery-option-row.apaczka-pickup-carrier .delivery-option-label.has-logo ~ .apaczka-additional-div {
            width: 155px !important;
            max-width: 155px !important;
        }
    }
</style>

<script type="application/javascript">
    var apaczkaCarriers = {$apaczka_carriers_json|cleanHtml nofilter};
    var apaczkaAjaxUrl = "{$apaczka_ajax_url|escape:'javascript':'UTF-8'}";
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

                    for (let i = 0; i < confirmButtons.length; i++) {
                        confirmButtons[i].disabled = false;
                    }
                    
                    if (apaczkaDeliveryOption != null) {
                        apaczkaDeliveryOption.checked=true;
                    }

                    apaczkaSaveSelectionToCart(record.supplier, record.foreign_access_point_id);
                    apaczkaPersistDeliverySelection();
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

    function apaczkaFindDeliveryOptionContainer(radioCarrierEl) {
        if (!radioCarrierEl) {
            return null;
        }

        if (typeof radioCarrierEl.closest === "function") {
            var closestDeliveryOption = radioCarrierEl.closest(".delivery-option");

            if (closestDeliveryOption) {
                return closestDeliveryOption;
            }
        }

        var parentEl = radioCarrierEl;

        for (var i = 0; i < 20; i++) {
            parentEl = parentEl.parentElement;

            if (parentEl == null) {
                return null;
            }

            if (parentEl.className != null && parentEl.className.indexOf("delivery-option") !== -1) {
                return parentEl;
            }
        }

        return null;
    }

    function apaczkaLoadCarrier(idMap, supplier, showMap) {
        var radioCarrierEl = document.getElementById("delivery_option_"+idMap);

        if (radioCarrierEl != null) {
            var parentEl = apaczkaFindDeliveryOptionContainer(radioCarrierEl);

            if (parentEl == null) {
                return;
            }

            var pointValue = "";

            if (supplier == "{$apaczka_cartRow['apaczka_supplier']|escape:'htmlall':'UTF-8'}" && "{$apaczka_cart->id_carrier|escape:'htmlall':'UTF-8'}" == idMap) {
                pointValue = "{$apaczka_cartRow['apaczka_point']|escape:'htmlall':'UTF-8'}";
            }

            if (document.getElementById("apaczka_delivery_point_" + idMap) == null) {
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
                {/literal}
            }

            if (document.getElementById("apaczka_supplier_" + idMap) == null) {
                {literal}
                    parentEl.insertAdjacentHTML(
                        "beforeend",
                        `<input
                            type="hidden"
                            id="apaczka_supplier_${idMap}"
                            name="apaczka_supplier[${idMap}]"
                            value="${supplier}">`
                    );
                {/literal}
            }

            if (showMap && parentEl.querySelector('.apaczka-additional-div[data-id-carrier="' + idMap + '"]') == null) {
                parentEl.classList.add('apaczka-pickup-carrier');
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
                            class="apaczka-additional-div font-weight-bold"
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
        }
    }

    function apaczkaFindCarrierConfigByCarrierId(idCarrier) {
        for (var idReference in apaczkaCarriers) {
            if (String(apaczkaCarriers[idReference].id_carrier) === String(idCarrier)) {
                return apaczkaCarriers[idReference];
            }
        }

        return null;
    }

    function apaczkaSaveSelectionToCart(supplier, point) {
        if (!apaczkaAjaxUrl || typeof window.fetch !== 'function') {
            return;
        }

        var formData = new FormData();
        formData.append('ajax', '1');
        formData.append('supplier', supplier || '');
        formData.append('point', point || '');

        window.fetch(apaczkaAjaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        }).catch(function() {
            // Silent fail: checkout should still work even if persistence fails.
        });
    }

    function apaczkaPersistDeliverySelection() {
        if (typeof window.selectDeliveryOption === 'function' && typeof window.jQuery === 'function') {
            window.selectDeliveryOption(window.jQuery('#js-delivery'));
            return;
        }

        if (typeof window.jQuery === 'function') {
            var $selectedDeliveryOption = window.jQuery('#js-delivery input[name^="delivery_option"]:checked');

            if ($selectedDeliveryOption.length) {
                $selectedDeliveryOption.trigger('change');
                return;
            }
        }

        var selectedDeliveryOption = document.querySelector('#js-delivery input[name^="delivery_option"]:checked');

        if (selectedDeliveryOption) {
            selectedDeliveryOption.dispatchEvent(new Event("change", { bubbles: true }));
        }
    }

    function apaczkaGetSelectedCarrierId() {
        var selectedCarrierRadio = document.querySelector('[type="radio"][name^="delivery_option"]:checked');

        if (!selectedCarrierRadio || !selectedCarrierRadio.value) {
            return null;
        }

        return selectedCarrierRadio.value.split(',')[0].trim();
    }

    function apaczkaSetConfirmButtonsDisabled(disabled) {
        var buttons = document.querySelectorAll('button[type="submit"][name^="confirmDeliveryOption"]');

        for (var i = 0; i < buttons.length; i++) {
            buttons[i].disabled = disabled;
        }
    }

    function apaczkaHighlightSelectedCarrier() {
        var selectedCarrierId = apaczkaGetSelectedCarrierId();
        var deliveryRows = document.querySelectorAll('.delivery-option-row');

        for (var i = 0; i < deliveryRows.length; i++) {
            deliveryRows[i].classList.remove('apaczka-selected-delivery');
        }

        if (!selectedCarrierId) {
            return;
        }

        var selectedCarrierRadio = document.getElementById('delivery_option_' + selectedCarrierId);
        var selectedCarrierRow = selectedCarrierRadio ? apaczkaFindDeliveryOptionContainer(selectedCarrierRadio) : null;

        if (selectedCarrierRow) {
            selectedCarrierRow.classList.add('apaczka-selected-delivery');
        }
    }

    function apaczkaSyncSelectedCarrierUI() {
        apaczkaHighlightSelectedCarrier();

        {if $apaczka_carriersPoints}
            var selectedCarrierId = apaczkaGetSelectedCarrierId();
            var additionalDivs = document.querySelectorAll('div.apaczka-additional-div');
            var pickupCarrierRows = document.querySelectorAll('.delivery-option-row.apaczka-pickup-carrier');

            for (var i = 0; i < additionalDivs.length; i++) {
                additionalDivs[i].style.display = 'none';
            }

            for (var j = 0; j < pickupCarrierRows.length; j++) {
                pickupCarrierRows[j].classList.remove('apaczka-hide-logo');
            }

            if (!selectedCarrierId) {
                apaczkaSetConfirmButtonsDisabled(false);
                return;
            }

            var selectedCarrierSection = document.querySelector('div.apaczka-additional-div[data-id-carrier="' + selectedCarrierId + '"]');

            if (selectedCarrierSection) {
                selectedCarrierSection.style.display = 'block';

                var selectedCarrierRow = selectedCarrierSection.closest('.delivery-option-row');

                if (selectedCarrierRow && selectedCarrierRow.classList.contains('apaczka-pickup-carrier')) {
                    selectedCarrierRow.classList.add('apaczka-hide-logo');
                }
            }

            var idsCarriersPoints = {$apaczka_carriersPoints_json|cleanHtml nofilter};

            if (idsCarriersPoints.includes(selectedCarrierId)) {
                var selectedPoint = document.getElementById('apaczka_delivery_point_' + selectedCarrierId);
                apaczkaSetConfirmButtonsDisabled(!selectedPoint || selectedPoint.value === '');
            } else {
                apaczkaSetConfirmButtonsDisabled(false);
            }
        {/if}
    }

    function apaczkaInitializeDeliveryOptions() {
        {foreach $apaczka_carriersConfig as $carrier_reference => $config}
            apaczkaLoadCarrier({$config['id_carrier']|escape:'htmlall':'UTF-8'}, "{$config['apaczkaName']}", {if $config['points']}1{else}0{/if});
        {/foreach}

        {if $apaczka_carriersPoints}
            apaczkaSyncSelectedCarrierUI();
        {/if}

        var selectedCarrierId = apaczkaGetSelectedCarrierId();
        var carrierConfig = selectedCarrierId ? apaczkaFindCarrierConfigByCarrierId(selectedCarrierId) : null;

        if (carrierConfig) {
            var selectedPointInput = document.getElementById('apaczka_delivery_point_' + selectedCarrierId);
            var selectedPoint = selectedPointInput ? selectedPointInput.value.trim() : '';
            apaczkaSaveSelectionToCart(carrierConfig.apaczkaName, selectedPoint);
        }
    }

    function apaczkaHandleDocumentChange(event) {
        if (event.target && event.target.matches('[type="radio"][name^="delivery_option"]')) {
            var selectedCarrierId = event.target.value.split(',')[0].trim();
            var carrierConfig = apaczkaFindCarrierConfigByCarrierId(selectedCarrierId);
            var selectedPointInput = document.getElementById('apaczka_delivery_point_' + selectedCarrierId);
            var selectedPoint = selectedPointInput ? selectedPointInput.value.trim() : '';

            if (carrierConfig) {
                apaczkaSaveSelectionToCart(carrierConfig.apaczkaName, selectedPoint);
            } else {
                apaczkaSaveSelectionToCart('', '');
            }

            apaczkaSyncSelectedCarrierUI();
        }
    }

    function apaczkaHandleDocumentClick(event) {
        var target = event.target;

        if (!target) {
            return;
        }

        var confirmButton = target.closest('button[type="submit"][name^="confirmDeliveryOption"]');

        if (!confirmButton) {
            return;
        }

        var selectedCarrierId = apaczkaGetSelectedCarrierId();

        if (!selectedCarrierId) {
            return;
        }

        var idsCarriersPoints = {$apaczka_carriersPoints_json|cleanHtml nofilter};
        var selectedPointInput = document.getElementById('apaczka_delivery_point_' + selectedCarrierId);
        var selectedPoint = selectedPointInput ? selectedPointInput.value.trim() : '';

        if (idsCarriersPoints.includes(selectedCarrierId) && selectedPoint === '') {
            alert('{l s='Service point not chosen!' mod="apaczka"}');
            event.stopPropagation();
            event.preventDefault();
        }
    }

    function apaczkaScheduleInitialize() {
        if (window.apaczkaScheduleInitializeTimeout) {
            window.clearTimeout(window.apaczkaScheduleInitializeTimeout);
        }

        window.apaczkaScheduleInitializeTimeout = window.setTimeout(function() {
            apaczkaInitializeDeliveryOptions();
        }, 50);
    }

    function apaczkaBindEvents() {
        if (!window.apaczkaEventsBound) {
            window.apaczkaEventsBound = true;

            document.addEventListener('change', apaczkaHandleDocumentChange);
            document.addEventListener('click', apaczkaHandleDocumentClick);

            if (typeof prestashop !== 'undefined' && prestashop && typeof prestashop.on === 'function') {
                prestashop.on('updatedDeliveryForm', apaczkaScheduleInitialize);
                prestashop.on('changedCheckoutStep', apaczkaScheduleInitialize);
                prestashop.on('updateDeliveryForm', apaczkaScheduleInitialize);
            }
        }

        var deliveryContainer = document.getElementById('checkout-delivery-step') || document.getElementById('js-delivery') || document.getElementById('hook-display-before-carrier');

        if (deliveryContainer && typeof MutationObserver !== 'undefined') {
            if (window.apaczkaDeliveryObserver) {
                window.apaczkaDeliveryObserver.disconnect();
            }

            var observer = new MutationObserver(function() {
                apaczkaScheduleInitialize();
            });

            observer.observe(deliveryContainer, { childList: true, subtree: true });
            window.apaczkaDeliveryObserver = observer;
        }
    }

    function apaczkaBoot() {
        apaczkaBindEvents();
        apaczkaInitializeDeliveryOptions();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', apaczkaBoot);
    } else {
        apaczkaBoot();
    }
</script>
