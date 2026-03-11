<div class="col-xs-12 dpdpoland-pudo-container">

    <div class="form-group container_dpdpoland_pudo_error" style="display:none">
        <p class="alert alert-danger">{l s='Error occured. Please try again' mod='dpdpoland'}</p>
    </div>

    <div class="dpdpoland-pudo-new-point">
        <span> {l s='DPD pickup point:' mod='dpdpoland'}</span>
        <div class="dpdpoland-pudo-open-map-btn btn btn-secondary">
            {l s='Select from map' mod='dpdpoland'}
        </div>
    </div>

    <div class="dpdpoland-pudo-selected-point" style="display: none">
        <p> {l s='Selected DPD pickup point:' mod='dpdpoland'} <span
                    class="dpdpoland-selected-point font-weight-bold"></span></p>
        <div class="dpdpoland-pudo-change-map-btn btn btn-secondary float-xs-right">
            {l s='Change' mod='dpdpoland'}
        </div>
    </div>
</div>

<!-- Modal -->

<div class="modal fade" id="dpdpolandPudoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dpd-xl" role="document">
        <div class="modal-content modal-dpd-content">
            <div class="modal-body modal-dpd-body">

                <script id="dpd-widget-pudo" type="text/javascript">

                    function pointSelected(pudoCode) {
                        $('.container_dpdpoland_pudo_error').css("display", "none");
                        $.ajax(dpdpoland_ajax_uri, {
                            data: {
                                'pudo_code': pudoCode,
                                'save_pudo_id': 1,
                                'token': dpdpoland_token,
                                'id_cart': dpdpoland_cart,
                                'session': dpdpoland_session
                            },
                            success: function (data) {
                                if (Number(data) === 1) {
                                    $('.dpdpoland-pudo-new-point').css("display", "none");
                                    $('.dpdpoland-pudo-selected-point').css("display", "block");
                                    enableOrderProcessBtn();
                                } else {
                                    $('.container_dpdpoland_pudo_error').css("display", "block");
                                    disableOrderProcessBtn();
                                }
                                setTimeout(() => {
                                    $('#dpdpolandPudoModal').modal('toggle');
                                    $(".modal-backdrop").hide();
                                }, 500);

                            },
                            error: function () {
                                $('.container_dpdpoland_pudo_error').css("display", "block");
                                disableOrderProcessBtn();
                                $('#dpdpolandPudoModal').modal('toggle');
                                $(".modal-backdrop").hide();
                            }
                        });

                        $.ajax(dpdpoland_ajax_uri, {
                            data: {
                                'pudo_code': pudoCode,
                                'call_pudo_address': 1,
                                'token': dpdpoland_token,
                                'id_cart': dpdpoland_cart
                            },
                            success: function (data) {
                                $('.dpdpoland-selected-point').text(data);
                            },
                            error: function () {
                            }
                        });
                    }
                </script>
                <!--suppress ES6ConvertVarToLetConst, JSUnresolvedReference -->
                <script type="text/javascript">

                    if (typeof eventCreated == 'undefined') {
                        var eventCreated = false;
                    }

                    var dpdPoland_iframe = document.createElement("iframe");
                    dpdPoland_iframe.setAttribute("id", "dpd-widget-pudo-iframe");
                    dpdPoland_iframe.setAttribute("allow", "geolocation");
                    dpdPoland_iframe.src = '//pudofinder.dpd.com.pl/widget?key=1ae3418e27627ab52bebdcc1a958fa04';
                    dpdPoland_iframe.style.width = "100%";
                    dpdPoland_iframe.style.border = "none";
                    dpdPoland_iframe.style.minHeight = "400px";
                    dpdPoland_iframe.style.height = "768px";

                    var dpdPoland_script = document.getElementById("dpd-widget-pudo");
                    if (dpdPoland_script)
                        dpdPoland_script.parentNode.insertBefore(dpdPoland_iframe, dpdPoland_script);

                    if (!eventCreated) {
                        var dpdPoland_eventListener = window[window.addEventListener ? "addEventListener" : "attachEvent"];
                        var dpdPoland_messageEvent = ("attachEvent" === dpdPoland_eventListener) ? "onmessage" : "message";
                        dpdPoland_eventListener(dpdPoland_messageEvent, function (a) {
                            if (getSelectedCarrier() === getIdPudoCarrier()) {
                                if (a.data.height && !isNaN(a.data.height)) {
                                    dpdPoland_iframe.style.height = a.data.height + "px"
                                } else if (a.data.point_id)
                                    pointSelected(a.data.point_id);
                            }
                        }, !1);
                        eventCreated = true
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