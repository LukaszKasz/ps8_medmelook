<form class="imoje-pm-form pl-2 mt-2 mb-2" method="POST" action="{$payment_link}">
	<div class="box-pm row">
        {foreach from=$payment_method_list key=id item=payment_method}
			<input required {if !$payment_method.isAvailable} disabled {/if} class="input-hidden" type="radio" id="{$payment_method.paymentMethod}:{$payment_method.paymentMethodCode}" name="imoje-pm" value="{$payment_method.paymentMethod}:{$payment_method.paymentMethodCode}"/>
			<label for="{$payment_method.paymentMethod}:{$payment_method.paymentMethodCode}" class="pm pm-content {if !$payment_method.isAvailable} pbl-offline{/if}">

				<img class="pm-logo" src="{$payment_method.logo}" alt="{$payment_method.description}">

			</label>
        {/foreach}
	</div>

</form>
{include file='module:imoje/views/templates/hook/payment-imoje-terms.tpl'}
<div class="imoje_ipp_regulation d--none">
    {include file='module:imoje/views/templates/hook/payment-imoje-twisto-terms.tpl'}
</div>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function (event) {
		var $imojePmForm = $('.imoje-pm-form'),
			imojeTwisto = 'imoje_paylater:imoje_twisto',
			inputName = 'imoje-pm',
			$imojeTwistoTerms = $('.imoje_ipp_regulation');

		$('label[for]').filter(function () {
			var $input = $(window[this.htmlFor]);
			return ($input.offset().left < 0) && ($input.offset().top !== $(this).offset().top);
		}).on('mousedown.init', function () {
			$(window[this.htmlFor]).css({
				'position': 'fixed',
				'top':      $(this).offset().top
			});
		});

		$('input[type=radio][name=' + inputName + ']').change(function () {
			if (this.value === imojeTwisto) {
				$imojeTwistoTerms.show()
			} else {
				$imojeTwistoTerms.hide();
			}
		})

		$imojePmForm.off('submit').on('submit', function (e) {

				if (!$("input[name='imoje-pm']").is(':checked')) {
					$('#conditions_to_approve\\[terms-and-conditions\\], #cgv').prop("checked", false)
					e.preventDefault();

					alert("{l s='You must select payment method channel' mod='imoje'}")
					return false;
				}

				$imojePmForm[0].submit()
			}
		)
	});

</script>
