{if isset($imoje_can_refund) && $imoje_can_refund}
    {if new_layout_refund}
        {capture assign=refund_fieldset}
			<div class="well">

				<div class="card" id="imojeRefund">
					<div class="card-header">
						<h3 class="card-header-title">
                            {l s='Make a refund' mod='imoje'}
						</h3>
					</div>

					<div class="card-body">
                        {if isset($imoje_refund_error) && $imoje_refund_error}
							<div class="alert alert-danger">
								<p class="error">{$imoje_refund_error|escape:'htmlall':'UTF-8'}</p>
							</div>
                        {/if}
                        {if isset($imoje_refund_message) && $imoje_refund_message}
							<div style="margin-top:15px" class="alert alert-success">
								<p>{$imoje_refund_message}</p>
							</div>
                        {/if}
                        {if isset($imoje_amount_refundable)
                        && $imoje_amount_refundable
                        && ($imoje_amount_refundable > 0)}
							<div class="form-group row type-text_with_length_counter js-text-with-length-counter">
								<label for="imoje_refund_amount" class="form-control-label label-on-top col-12">
                                    {l s='Insert amount to refund' mod='imoje'}
								</label>

								<div class="col-12">
									<div class="input-group js-text-with-length-counter">
										<input type="text" class="form-control" id="imoje_refund_amount"
										       name="imoje_refund_amount" value=""
										       placeholder="{$imoje_amount_refundable}">
									</div>
								</div>
							</div>
							<div class="form-group row type-checkbox ">
								<div class="col-sm">
									<div class="checkbox">
										<div class="md-checkbox md-checkbox-inline">
											<label><input type="checkbox" name="imoje_change_status" class=""
											              value="1">
												<i class="md-checkbox-control"></i>{l s='Change status to refund' mod='imoje'}
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="text-right">
								<button name="imoje_submit_refund" disabled type="submit" class="btn btn-primary"
								        id="imoje_submit_refund" value="1">
                                    {l s='Perform a refund' mod='imoje'}
								</button>
							</div>
                        {/if}
					</div>
				</div>
			</div>
        {/capture}
		<script>
			$(document).ready(function () {
				$("{$refund_fieldset|escape:'javascript':'UTF-8'}").insertAfter($('#orderProductsOriginalPosition').first());

				$('#imoje_refund_amount').on('keyup change', function () {

					$('#imoje_submit_refund').prop('disabled', isNaN($(this).val()) || $(this).val() <= 0 || $(this).val() > {$imoje_amount_refundable});
				});

				$('#imoje_submit_refund').on('click', function () {

					var confirmResult = confirm('{l s='Do you really want to submit the refund request?' mod='imoje'}');

					if (confirmResult) {

						$.ajax({
							url:     window.location.href,
							type:    'POST',
							data:    {
								imoje_refund_amount: $('#imoje_refund_amount').val(),
								imoje_submit_refund: 1,
								imoje_change_status: $('#imoje_change_status').val()
							},
							success: function (response) {
								if (!response) {
									alert('{l s='Refund has been created, and awaiting for confirmation.' mod='imoje'}');
									return;
								}
								alert(response)
							},
							error:   function (response) {
								if (!response) {
									alert('{l s='Refund error.' mod='imoje'}');
									return;
								}

								alert(response)

							}
						});

					}
				})

			});
		</script>
    {else}
        {capture assign=refund_fieldset}
			<div class="well">
				<h3>{l s='Make a refund' mod='imoje'}</h3>

                {if isset($imoje_refund_error) && $imoje_refund_error}
					<br/>
					<div style="margin-top:15px" class="alert alert-danger">
						<p class="error">{$imoje_refund_error|escape:'htmlall':'UTF-8'}</p>
					</div>
                {/if}
                {if isset($imoje_refund_message) && $imoje_refund_message}
					<div style="margin-top:15px" class="alert alert-success">
						<p>{$imoje_refund_message}</p>
					</div>
                {/if}

				<div class="row">
                    {if isset($imoje_amount_refundable)
                    && $imoje_amount_refundable
                    && ($imoje_amount_refundable > 0)}
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-lg-3">
                                    {l s='Insert amount to refund' mod='imoje'}
								</label>
								<div class="col-lg-9">
									<input type="text" class="form-control" id="imoje_refund_amount"
									       name="imoje_refund_amount" value=""
									       placeholder="{$imoje_amount_refundable}">
								</div>
							</div>

							<div class="form-group">
								<div class="checkbox pull-right">
									<div class="md-checkbox md-checkbox-inline">
										<label>
											<input type="checkbox" name="imoje_change_status" id="imoje_change_status" class=""
											       value="1">
											<i class="md-checkbox-control">

											</i>{l s='Change status to refund' mod='imoje'}
										</label>
									</div>
								</div>
							</div>
						</div>
						<button disabled type="submit" id="imoje_submit_refund" class="btn btn-primary pull-right"
						        name="imoje_submit_refund" value="1">
                            {l s='Perform a refund' mod='imoje'}
						</button>
                    {/if}
				</div>
			</div>
        {/capture}
		<script>
			$(document).ready(function () {
				$("{$refund_fieldset|escape:'javascript':'UTF-8'}").insertAfter($('.panel-heading').first());
				$('#imoje_refund_amount').on('keyup change', function () {

					$('#imoje_submit_refund').prop('disabled', isNaN($(this).val()) || $(this).val() <= 0 || $(this).val() > {$imoje_amount_refundable});
				});

				$('#imoje_submit_refund').on('click', function () {

					var confirmResult = confirm('{l s='Do you really want to submit the refund request?' mod='imoje'}');

					if (confirmResult) {

						$.ajax({
							url:     window.location.href,
							type:    'POST',
							data:    {
								imoje_refund_amount: $('#imoje_refund_amount').val(),
								imoje_submit_refund: 1,
								imoje_change_status: $('#imoje_change_status').val()
							},
							success: function (response) {
								alert(response)
							},
							error:   function (response) {
								alert(response)
							}
						});

					}
				})
			});
		</script>
    {/if}
{/if}
