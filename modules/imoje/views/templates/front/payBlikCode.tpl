<form class="payment-form" data-method="POST">
	<div>
		<div class="card col-xs-12 col-lg-8">
			<div id="text-tip" class="d--none"></div>
			<div id="content_blik_code">
                {if $active_profile}
					<div id="content_blik_oneclick">
						<p>
						<h2 class="h3">{l s='Debit profile' mod='imoje'}</h2>
						<hr/>
						<div data-profile-id="{$active_profile.id}"
						     data-profile-name="{$active_profile.label}"
						     class="div_profile">
                            {foreach $active_profile.aliasList as $alias}
								<div data-blik-key="{$alias.key}"
								     data-blik-name="{$alias.label}" class="blik-alias-div">
									<button class="btn btn-primary blik-alias">
                                        {$alias.label}
									</button>
									<button class="btn btn-danger alias-deactivate">
                                        {l s='Deactivate' mod='imoje'}
									</button>
								</div>
                            {/foreach}
						</div>
						<hr/>
					</div>
                {/if}

				<h2 class="h5 mt-1 mb-2" id="insert_blik_code-text">
                    {l s='Insert BLIK code' mod='imoje'}
				</h2>
				<div class="content_insert_code d--none">
					<input id="blik_code"
					       placeholder="{l s='BLIK code' mod='imoje'}"
					       class="form-control"
					       type="text"
					       name="blik_code"
					       required
					       autocomplete="off"
					       maxlength="6">
					<br/>

                    {if $is_customer_logged && $is_imoje_blik_oneclick}
						<div class="remember_code-content">
							<div class="float-xs-left">
                            <span class="custom-checkbox">
                                <input id="remember_code-checkbox" type="checkbox">
                                <span>
                                    <i class="material-icons checkbox-checked"></i>
                                </span>
                            </span>
							</div>
							<div class="remember_code">
								<label for="remember_code-checkbox">
                                    {l s='I want to remember this shop' mod='imoje'}
								</label>
							</div>
						</div>
                    {/if}

					<button id="blik_submit" class="btn btn-primary btn-submit-code disabled"
					        style="display: inline-block" disabled>
                        {l s='Pay' mod='imoje'}
					</button>
				</div>
				<br/>
			</div>
		</div>

		<div class="modal_blik" id="modal_processing">
			<div class="modal_blik-content" id="modal_processing-content">
				<img class="loading_gif" src="{$loading_gif}" alt="{l s='Loading' mod='imoje'}"/>
				<br/>
				<span id="modal_tip"></span>
			</div>
		</div>

		<div class="modal_blik" id="modal_blik_code">
			<div class="modal_blik-content" id="modal_blik_code-content">
				<p id="proceed_payment_code"></p>
				<label for="blik_code_modal" class="d--none">
				</label>
				<input placeholder="{l s='BLIK code' mod='imoje'}"
				       id="blik_code_modal"
				       class="form-control"
				       type="text"
				       name="blik_code"
				       required autocomplete="off"
				       maxlength="6">
				<br/>
				<button id="blik_submit_modal" class="btn btn-primary btn-submit-code disabled blik_submit" disabled>
                    {l s='Pay' mod='imoje'}
				</button>
				<button class="btn btn-secondary btn-modal-close">
                    {l s='Close' mod='imoje'}
				</button>
			</div>
		</div>

		<div class="modal_blik" id="modal_blik_alias_deactivate-modal">
			<div class="modal_blik-content" id="modal_blik_alias_deactivate-modal-content">
				<h2 class="h3">{l s='Deactivate profile' mod='imoje'}</h2>
				<label for="blik_code" class="">
				</label>
				<p>
                    {l s='Are you sure that you want deactivate profile ' mod='imoje'}
					<span id="deactivate_profile_name-text"></span>?
				</p>
				<button id="deactivate_profile_accept-btn" class="btn btn-primary">
                    {l s='Deactivate' mod='imoje'}
				</button>
				<button class="btn btn-secondary btn-modal-close">
                    {l s='Close' mod='imoje'}
				</button>
			</div>
		</div>
	</div>

	<div class="clearfix">
	</div>
    {include file="module:imoje/views/templates/hook/payment_imoje_terms.tpl"}

</form>

<script type="text/javascript">

	document.addEventListener("DOMContentLoaded", function (event) {

		(function () {

			// region vars
			var $blikCode = $("#blik_code"),
				$blikSubmit = $('#blik_submit'),
				$contentBlikCode = $('#content_blik_code'),
				$contentBlikOneclick = $('#content_blik_oneclick'),
				$modalProcessing = $('#modal_processing'),
				isSubmitted = false,
				$rememberCodeCheckbox = $('#remember_code-checkbox'),
				$aliasDeactivateButton = $('.alias-deactivate'),
				$blikProfile = $('.blik-alias'),
				$modalBlikAliasDeactivateModal = $('#modal_blik_alias_deactivate-modal'),
				$deactivateProfileNameText = $('#deactivate_profile_name-text'),
				$modalTip = $('#modal_tip'),
				$deactivateProfileAcceptButton = $('#deactivate_profile_accept-btn'),
				$textTip = $('#text-tip'),
				$modalBlik = $('.modal_blik'),
				dontProcessing = false,
				dontProcessingTimeout = null,
				$insertBlikCodeText = $('#insert_blik_code-text'),
				$modalBlikCode = $('#modal_blik_code'),
				$btnModalClose = $('.btn-modal-close'),
				$blikSubmitModal = $('#blik_submit_modal'),
				$blikCodeModal = $('#blik_code_modal'),
				$proceedPaymentCodeText = $('#proceed_payment_code'),
				timeoutAppendResultText = null,
				tempKey = null,
				profileId = null,
				tempThis = null,
				tempNewParamAlias = null,
				msAppendResultText = 30000,
				msBlikSubmit = 90000,
				$terms = $('#conditions_to_approve\\[terms-and-conditions\\], #cgv');
			// endregion

            {if !$active_profile}
			toggleContentInsertCode();
            {/if}

            {if $profile_id}
			profileId = "{$profile_id}";
            {/if}

			$insertBlikCodeText.on('click', function () {
				toggleContentInsertCode();
			});

			if (!isSubmitted) {

				$blikCode.on('input', function () {
					if ($blikCode.val().length === 6) {
						$blikSubmit.removeClass('disabled');
						$blikSubmit.removeAttr("disabled");
					} else {
						disableBlikSubmit();
					}
				});

				$blikCodeModal.on('input', function () {
					if ($blikCodeModal.val().length === 6) {
						$blikSubmitModal.removeClass('disabled').removeAttr("disabled");
					} else {
						disableBlikSubmit();
					}
				});
			}

			function toggleContentInsertCode() {
				$insertBlikCodeText.toggleClass('is-active').next(".content_insert_code").stop().slideToggle(500);
			}

			function appendResultText(text, color) {

				clearTimeout(timeoutAppendResultText);

				$textTip.show();
				$textTip.html(text).css('color', color);
				timeoutAppendResultText = setTimeout(function () {
					$textTip.hide();
				}, msAppendResultText)
				return;
			}

			function blikSubmit(data) {
				disableBlikSubmit();
				isSubmitted = true;
				$modalBlik.hide();
				clearDontProcessingTimeout()

				dontProcessingTimeout = setTimeout(
					function () {
						dontProcessing = true
					}, msBlikSubmit);

				processForm(data);
			}

			$blikSubmit.on('click', function () {

				if ($terms.length
					&& !$terms.is(':checked')) {
					appendResultText("{l s='You must accept terms and conditions' mod='imoje'}", 'red');
					return;
				}

				var data = {
					blikCode: $blikCode.val(),
				}

				if ($rememberCodeCheckbox.is(":checked")) {
					data.rememberBlikCode = 1;
					data.profileId = profileId;
				}

				blikSubmit(data);
			});

			$blikSubmitModal.on('click', function () {

				var data = {
					blikCode: $blikCodeModal.val(),
				}

				if ($('.blik-alias').length > 1) {
					data.profileId = profileId
				}

				if (tempNewParamAlias) {
					data.rememberBlikCode = 1
				}

				blikSubmit(data);
			});

			function disableBlikSubmit() {
				$blikSubmit.addClass('disabled').attr("disabled", true);
				$blikSubmitModal.addClass('disabled').attr("disabled", true);
			}

			// region deactivate profile
			$aliasDeactivateButton.on('click', function () {

				$deactivateProfileNameText.html($(this).parent().data("blik-name"))

				tempThis = $(this);
				$modalBlikAliasDeactivateModal.show();
			});

			$deactivateProfileAcceptButton.on('click', function () {

				$modalBlikAliasDeactivateModal.hide();

				if (!tempThis) {
					return;
				}

				var blikKey = tempThis.parent().data('blik-key');

				processForm({
						profileId:  profileId,
						blikKey:    blikKey,
						deactivate: 1
					},
					'{l s='Please wait, deactivation is in progress' mod='imoje'}',
					function (response) {

						$modalProcessing.hide();

						if (response.status) {

							$('div[data-blik-key=' + blikKey + ']').remove();

							appendResultText("{l s='Successfully deactivated profile' mod='imoje'}" + " " + tempThis.parent().data('blik-name'), 'green');

							if ($('.blik-alias-div').length === 0) {
								toggleContentInsertCode();
								$('div[data-profile-id=' + profileId + ']').remove();
								$contentBlikOneclick.remove();
								return;
							}
							return;
						}

						appendResultText('{l s='Could not deactivate profile. Try again later or contact with shop staff.' mod='imoje'}', 'red')
					})
			})

			// endregion

			function clearDontProcessingTimeout() {
				clearTimeout(dontProcessingTimeout);
				dontProcessing = null;
			}

			function checkPayment(transactionId) {

				$.ajax({
					data:   {
						transactionId:    transactionId,
						checkTransaction: true,
						cartId:           "{$cart_id}"
					},
					method: "POST",
					url:    "{$payment_blik_url nofilter}"
				})
					.then(function (data) { // done
						if (dontProcessing) {
							$modalProcessing.hide();
							appendResultText('{l s='Response timed out. Try again.' mod='imoje'}', 'red');
							clearDontProcessingTimeout();
							return;
						}

						if (!data.status) {

							$modalProcessing.hide();

							appendResultText('{l s='Something went wrong. Please contact with shop staff' mod='imoje'}', 'red');
							clearDontProcessingTimeout();
							return;
						}

						if (data.body && data.body.error) {

							$modalProcessing.hide();

							clearDontProcessingTimeout();

							if (data.body.code) {

								$proceedPaymentCodeText.html(data.body.error);

								if (data.body.newParamAlias) {
									tempNewParamAlias = true;
								}

								$modalBlikCode.show();
								return;
							}

							appendResultText(data.body.error, 'red');
							return;
						}

						if (typeof data.body.urlRedirect !== 'undefined') {
							location.href = data.body.urlRedirect;
							return;
						}

						if (data.body.transaction.status === 'pending') { // state inny niz pending

							if (dontProcessing) {
								$modalProcessing.hide();
								appendResultText('{l s='Response timed out. Try again.' mod='imoje'}', 'red');
								clearDontProcessingTimeout();
								return;
							}

							setTimeout(function () {
								checkPayment(transactionId)
							}, 1000);

							return;
						}

						clearDontProcessingTimeout();

						$modalProcessing.hide();

						$contentBlikCode.html('{$failure_tip1} <br/><br/> {$failure_tip2}');

					}, function () { // fail
					});
			}

			// region debit profile
			$blikProfile.on("click", function () {

				dontProcessingTimeout = setTimeout(
					function () {
						dontProcessing = true
					}, msBlikSubmit);

				var parent = $(this).parent(),
					key = parent.data("blik-key")
						? parent.data("blik-key")
						: null;

				tempKey = key;

				processForm({
						profileId: profileId,
						blikKey:   key,
					},
					'',
					function (response) {

						if (response.status && response.body.transaction.id) {
							checkPayment(response.body.transaction.id);
							return;
						}

						$modalProcessing.hide();
						appendResultText('{l s='Could not debit profile. Try again later or contact with shop staff.' mod='imoje'}', 'red');
					}
				);

			});
			// endregion

			$btnModalClose.on('click', function () {
					$modalBlik.hide();
				}
			)

			// region show modal processing
			function showModalProcessing(text) {
				$modalTip.html(text);
				$modalProcessing.show();
			}

			// endregion

			function clearInputs() {
				$blikCode.val('');
				$blikCodeModal.val('');
			}

			// region process form
			function processForm(data, modalTipText = '', funcAtDone = null, funcAtAlways = null) {

				document.activeElement.blur();

				$.ajax({
					method:     "POST",
					url:        "{$payment_blik_url nofilter}",
					data:       data,
					beforeSend: function () {

						$textTip.hide();
						clearInputs();
						showModalProcessing(modalTipText
							? modalTipText
							: '{l s='Now accept payment in your bank application' mod='imoje'}');
					}
				})
					.done(function (data) {

						if (typeof data !== "object") {
							$modalProcessing.hide();
							appendResultText('{l s='Something went wrong. Please contact with shop staff' mod='imoje'}', 'red');
							return;
						}

						if (!data.status) {
							$modalProcessing.hide();
							appendResultText('{l s='Something went wrong. Please contact with shop staff' mod='imoje'}', 'red');
							return;
						}

						if (data.body && data.body.error) {

							$modalProcessing.hide();

							clearDontProcessingTimeout();

							if (data.body.code) {

								$('#proceed_payment_code').html(data.body.error);

								if (data.body.newParamAlias) {
									tempNewParamAlias = true;
								}

								$modalBlikCode.show();
								return;
							}

							appendResultText(data.body.error, 'red');
							return;
						}

						if (funcAtDone) {
							funcAtDone(data);
							return;
						}

						if (typeof data.body.urlRedirect !== 'undefined') {
							location.href = data.body.urlRedirect;
							return;
						}

						if (data.body.transaction.status === 'rejected') {
							$modalProcessing.hide();
							$contentBlikCode.html('{$failure_tip1} <br/><br/> {$failure_tip2}');
							return;
						}

						if (data.body.transaction.id) {
							checkPayment(data.body.transaction.id);
							return;
						}

						$modalProcessing.hide();
						appendResultText('{l s='Something went wrong. Please contact with shop staff' mod='imoje'}', 'red');
					})
					.fail(function () {
						$modalProcessing.hide();
						appendResultText('{l s='Something went wrong. Please contact with shop staff' mod='imoje'}', 'red');
					})
					.always(function (response) {
						if (funcAtAlways) {
							funcAtAlways(response);
						}
					});
			}

			// endregion
		})()
	});
</script>
