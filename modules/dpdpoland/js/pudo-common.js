/**
 * 2024 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 *  @author    DPD Polska Sp. z o.o.
 *  @copyright 2024 DPD Polska Sp. z o.o.
 *  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska Sp. z o.o.
 */

$(document).ready(function () {
    handleDpdPudo();
    $(document).on('click', '.delivery_option_radio', handleDpdPudo);
    $(document).on('click', 'input[name^="delivery_option"]', handleDpdPudo);

    $('.dpdpoland-pudo-container').parent().css('border-right', '0');
    $('.dpdpoland-pudo-cod-container').parent().css('border-right', '0');

    $(document).on('click', '.dpdpoland-pudo-open-map-btn', (e) => showModal(e, '#dpdpolandPudoModal'));
    $(document).on('click', '.dpdpoland-pudo-change-map-btn', (e) => showModal(e, '#dpdpolandPudoModal'));

    $(document).on('click', '.dpdpoland-pudo-cod-open-map-btn', (e) => showModal(e, '#dpdpolandPudoCodModal'));
    $(document).on('click', '.dpdpoland-pudo-cod-change-map-btn', (e) => showModal(e, '#dpdpolandPudoCodModal'));

});

function showModal(event, modalDiv) {
    event.preventDefault();
    event.stopPropagation();
    $(modalDiv).modal({
        backdrop: 'static',
        keyboard: false
    })
    handleDpdPudo();
}

function handleDpdPudo() {

    $('.container_dpdpoland_pudo_cod_error').css("display", "none");
    $('.container_dpdpoland_pudo_cod_warning').css("display", "none");

    if (getSelectedCarrier() === getIdPudoCarrier()) {
        $('.dpdpoland-pudo-new-point').css("display", "block");
        $('.dpdpoland-pudo-selected-point').css("display", "none");

        $('.dpdpoland-selected-point').text("");
        const dpdWidgetPudoIframe = $("#dpd-widget-pudo-iframe")
        dpdWidgetPudoIframe.attr("src", dpdWidgetPudoIframe.attr("src"));

        disableOrderProcessBtn();
    } else if (getSelectedCarrier() === getIdPudoCodCarrier()) {
        $('.dpdpoland-pudo-cod-selected-point').css("display", "none");
        $('.dpdpoland-pudo-cod-new-point').css("display", "block");

        $('.dpdpoland-selected-point-cod').text("");
        const dpdWidgetPudoCodIframe = $("#dpd-widget-pudo-cod-iframe")
        dpdWidgetPudoCodIframe.attr("src", dpdWidgetPudoCodIframe.attr("src"));

        disableOrderProcessBtn();
    } else {
        enableOrderProcessBtn();
    }
}

function getIdPudoCarrier() {
    return Number(dpdpoland_id_pudo_carrier);
}

function getIdPudoCodCarrier() {
    return Number(dpdpoland_id_pudo_cod_carrier);
}

function getSelectedCarrier() {
    let idSelectedCarrier = $('input[name^="delivery_option"]:checked').val();

    if (typeof idSelectedCarrier == 'undefined')
        return null;

    idSelectedCarrier = idSelectedCarrier.replace(',', '');
    if (typeof idSelectedCarrier == 'undefined' || idSelectedCarrier === 0)
        return null;

    return Number(idSelectedCarrier);
}
