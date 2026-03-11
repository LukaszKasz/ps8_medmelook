<form id="imoje_pm_form" class="pl-2 mt-2 mb-2" method="POST" action="{$payment_link}">
	<div class="box-pm row">
        {foreach from=$payment_method_list key=id item=payment_method}
			<input required {if !$payment_method.isAvailable} disabled {/if} class="input-hidden" type="radio" id="{$payment_method.paymentMethod}:{$payment_method.paymentMethodCode}" name="imoje-pm" value="{$payment_method.paymentMethod}:{$payment_method.paymentMethodCode}"/>
			<label for="{$payment_method.paymentMethod}:{$payment_method.paymentMethodCode}" class="pm pm-content">

				<img {if !$payment_method.isAvailable} class="pm-logo pbl_is_not_online" {else} class="pm-logo" {/if} src="{$payment_method.logo}" alt="{$payment_method.description}">

				<p class="pm-description">
                    {$payment_method.description}
				</p>
			</label>
        {/foreach}
	</div>

</form>
{include file='module:imoje/views/templates/hook/payment_imoje_terms.tpl'}

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function (event) {

		var $imojePmForm = $('#imoje_pm_form');

		$('label[for]').filter(function () {
			var $input = $(window[this.htmlFor]);
			// Collect the labels whos inputs are offscreen and don't align to them
			return ($input.offset().left < 0) && ($input.offset().top != $(this).offset().top);
		}).on('mousedown.init', function () {
			// Move the input so that it is aligned with the label, to prevent scrolling
			$(window[this.htmlFor]).css({
				'position': 'fixed',
				'top':      $(this).offset().top
			});
		});

		$imojePmForm.on('submit', function (e) {

				if (!$("input[name='imoje-pm']").is(':checked')) {
					$('#conditions_to_approve\\[terms-and-conditions\\], #cgv').prop("checked", false)
					e.preventDefault();

					alert("{l s='You must select payment method channel' mod='imoje'}")
					return false;
				}

				$imojePmForm[0].submit()
			}
		)

	})

</script>


