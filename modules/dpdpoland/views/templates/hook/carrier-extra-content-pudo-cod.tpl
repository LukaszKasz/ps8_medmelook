<div class="col-xs-12 dpdpoland-pudo-cod-container">

    <div class="form-group container_dpdpoland_pudo_cod_warning" style="display:none">
        <p class="alert alert-danger">{l s='Selected point does not provide the cod service' mod='dpdpoland'}</p>
    </div>

    <div class="form-group container_dpdpoland_pudo_cod_error" style="display:none">
        <p class="alert alert-danger">{l s='Error occured. Please try again' mod='dpdpoland'}</p>
    </div>

    <div class="dpdpoland-pudo-cod-new-point">
        <span> {l s='DPD pickup point:' mod='dpdpoland'}</span>
        <div class="dpdpoland-pudo-cod-open-map-btn btn btn-secondary">
            {l s='Select from map' mod='dpdpoland'}
        </div>
    </div>

    <div class="dpdpoland-pudo-cod-selected-point" style="display: none">
        <p> {l s='Selected DPD pickup point:' mod='dpdpoland'} <span
                    class="dpdpoland-selected-point-cod font-weight-bold"></span></p>
        <div class="dpdpoland-pudo-cod-change-map-btn btn btn-secondary float-xs-right">
            {l s='Change' mod='dpdpoland'}
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="dpdpolandPudoCodModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dpd-xl" role="document">
        <div class="modal-content modal-dpd-content">
            <div class="modal-body modal-dpd-body">

                <script id="dpd-widget-pudo-cod" type="text/javascript">
                    function pointSelectedCod(pudoCodCode) {

                        $('.container_dpdpoland_pudo_cod_warning').css("display", "none");
                        $('.container_dpdpoland_pudo_cod_error').css("display", "none");

                        $.ajax(dpdpoland_ajax_uri, {
                            data: {
                                'pudo_code': pudoCodCode,
                                'save_pudo_id': 1,
                                'token': dpdpoland_token,
                                'id_cart': dpdpoland_cart,
                                'session': dpdpoland_session
                            },
                            success: function (data) {
                                if (Number(data) === 1) {
                                    $('.dpdpoland-pudo-cod-new-point').css("display", "none");
                                    $('.dpdpoland-pudo-cod-selected-point').css("display", "block");

                                    if ($('.container_dpdpoland_pudo_cod_warning').css('display') === 'none')
                                        enableOrderProcessBtn();
                                } else {
                                    $('.container_dpdpoland_pudo_cod_error').css("display", "block");
                                    disableOrderProcessBtn();
                                }

                                setTimeout(() => {
                                    $('#dpdpolandPudoCodModal').modal('toggle');
                                    $(".modal-backdrop").hide();
                                }, 500);

                            },
                            error: function () {
                                $('.container_dpdpoland_pudo_cod_error').css("display", "block");
                                disableOrderProcessBtn();
                                $('#dpdpolandPudoCodModal').modal('toggle');
                                $(".modal-backdrop").hide();
                            }
                        });

                        $.ajax(dpdpoland_ajax_uri, {
                            data: {
                                'pudo_code': pudoCodCode,
                                'call_has_pudo_cod': 1,
                                'token': dpdpoland_token,
                                'id_cart': dpdpoland_cart
                            },
                            success: function (data) {
                                if (Number(data) === 0) {
                                    $('.container_dpdpoland_pudo_cod_warning').css("display", "block");
                                    disableOrderProcessBtn();
                                } else
                                    $('.container_dpdpoland_pudo_cod_warning').css("display", "none");
                            }
                        });

                        $.ajax(dpdpoland_ajax_uri, {
                            data: {
                                'pudo_code': pudoCodCode,
                                'call_pudo_address': 1,
                                'token': dpdpoland_token,
                                'id_cart': dpdpoland_cart
                            },
                            success: function (data) {
                                $('.dpdpoland-selected-point-cod').text(data);
                            },
                            error: function () {
                            }
                        });
                    }
                </script>

                <!--suppress ES6ConvertVarToLetConst, JSUnresolvedReference -->
                <script type="text/javascript">

                    if (typeof eventCreatedCod == 'undefined') {
                        var eventCreatedCod = false;
                    }

                    var dpdPoland_iframeCod = document.createElement("iframe");
                    dpdPoland_iframeCod.setAttribute("id", "dpd-widget-pudo-cod-iframe");
                    dpdPoland_iframeCod.setAttribute("allow", "geolocation");
                    dpdPoland_iframeCod.src = '//pudofinder.dpd.com.pl/widget?key=1ae3418e27627ab52bebdcc1a958fa04&direct_delivery_cod=1';
                    dpdPoland_iframeCod.style.width = "100%";
                    dpdPoland_iframeCod.style.border = "none";
                    dpdPoland_iframeCod.style.minHeight = "400px";
                    dpdPoland_iframeCod.style.height = "768px";

                    var dpdPoland_scriptCod = document.getElementById("dpd-widget-pudo-cod");
                    if (dpdPoland_scriptCod)
                        dpdPoland_scriptCod.parentNode.insertBefore(dpdPoland_iframeCod, dpdPoland_scriptCod);

                    if (!eventCreatedCod) {
                        var dpdPoland_eventListenerCod = window[window.addEventListener ? "addEventListener" : "attachEvent"];
                        var dpdPoland_messageEventCod = ("attachEvent" === dpdPoland_eventListenerCod) ? "onmessage" : "message";
                        dpdPoland_eventListenerCod(dpdPoland_messageEventCod, function (a) {
                            if (getSelectedCarrier() === getIdPudoCodCarrier()) {
                                if (a.data.height && !isNaN(a.data.height)) {
                                    dpdPoland_iframeCod.style.height = a.data.height + "px"
                                } else if (a.data.point_id)
                                    pointSelectedCod(a.data.point_id);
                            }
                        }, !1);
                        eventCreatedCod = true
                    }


                </script>
            </div>
            <div class="modal-footer modal-dpd-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{l s='Close' mod='dpdpoland'}</button>
            </div>
        </div>
    </div>
</div>