<form id="imoje-installments-form" class="pl-2 mt-2 mb-2" method="POST" action="{$payment_link}">
	<div class="imoje-installments__wrapper" id="imoje-installments__wrapper"
	     data-installments-amount="{$imoje_installments_data.amount}"
	     data-installments-currency="{$imoje_installments_data.currency}"
	     data-installments-service-id="{$imoje_installments_data.serviceId}"
	     data-installments-merchant-id="{$imoje_installments_data.merchantId}"
	     data-installments-signature="{$imoje_installments_data.signature}"
	     data-installments-url="{$imoje_installments_data.url}"
	>
	</div>
	<input name="imoje-selected-channel" id="imoje-selected-channel" hidden>
	<input name="imoje-installments-period" id="imoje-installments-period" value="12" hidden>
</form>

{include file='module:imoje/views/templates/hook/payment-imoje-terms.tpl'}

<script type="text/javascript">

	(function () {

		document.addEventListener("DOMContentLoaded", function (event) {

			var $imojePmForm = $('#pay-with-payment-option-' + $("#imoje-installments-form").parent().attr('id').split('-')[2] + '-form'),
				imojeIsPassedInstallments = false;

			// catch an installment message
			window.addEventListener('message', function (data) {
				if (data.data?.channel && data.data.period) {
					imojeIsPassedInstallments = true;

					$('#imoje-selected-channel').val(data.data.channel)
					$('#imoje-installments-period').val(data.data.period)
				}
			}, false);

			$imojePmForm.on('submit', function (e) {

				if (!imojeIsPassedInstallments) {
					$('#conditions_to_approve\\[terms-and-conditions\\], #cgv').prop("checked", false)
					e.preventDefault();

					alert("{l s='This payment method is unavailable, choose another' mod='imoje'}")
					return false;
				}
			})

			function show_installments_widget() {

				var script = document.getElementById('imoje-installments__script'),
					$wraper = $('#imoje-installments__wrapper');

				if (script == null) {
					script = document.createElement('script');
					script.id = 'imoje-installments__script';
					script.src = $wraper.data('installmentsUrl');
					script.onload = () => {
						show_installments_widget();
					};
					document.body.append(script);

					return;
				}

				var installmentsData = $wraper.data();

				document.getElementById('imoje-installments__wrapper').imojeInstallments({
						amount:     installmentsData.installmentsAmount,
						currency:   installmentsData.installmentsCurrency,
						serviceId:  installmentsData.installmentsServiceId,
						merchantId: installmentsData.installmentsMerchantId,
						signature:  installmentsData.installmentsSignature
					}
				)

			}

			show_installments_widget()

		});
	})();

</script>


