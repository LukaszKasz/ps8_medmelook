<style>
	#sandbox_text_enabled {
		color:red;
	}

	#imoje > label, #imoje_data > label {
		margin-top:3px
	}
</style>

<div class="imoje-wrap bootstrap">
    {if $imoje_msg != ''}
		<div class="bootstrap">
			<div class="{if $imoje_msg.type == 'error'}alert alert-danger{else}alert alert-success{/if}">{$imoje_msg.message}</div>
		</div>
    {/if}
	<div class="imoje-header">
        <span class="imoje-intro">
            <img width="85" alt="imoje" src="{$module_dir}logo.svg"/>
        </span>
	</div>

	<div class="imoje-half">
		<form id="imoje-configuration" method="post" name="imoje-configuration">
			<h3>
				<img src="{$module_dir}assets/img/icon-config.gif" alt="config icon"
				     class="config-icon"/>{l s='Configuration' mod='imoje'}
			</h3>
			<p>{l s='The module requires a configuration with your shop in the imoje administration panel.' mod='imoje'} {l s='Go to ' mod='imoje'}
				<strong><a href="https://imoje.ing.pl">{l s='imoje.ing.pl' mod='imoje'}</a></strong>{l s=' and log in to the administration panel. Then go to ' mod='imoje'}
				<strong>{l s='Shops>your shop name>Details>Data for integration ' mod='imoje'}</strong>{l s='and copy ' mod='imoje'}
				<strong>{l s='Merchant ID, Shop ID, Shop key, Authorization token ' mod='imoje'}</strong>{l s='into the fields described below.' mod='imoje'}</p>
			<hr/>
            {if $imoje_sandbox == 1}
				<h3 id="sandbox_text_enabled">{l s='Sandbox mode is enabled' mod='imoje'}!</h3>
				<hr/>
				<br/>
            {/if}
			<div id="imoje">

				<label for="imoje_sandbox">{l s='Sandbox' mod='imoje'}</label>
				<div class="margin-form">
					<select name="imoje_sandbox" class="imoje-sel-en">
						<option value="0" {if $imoje_sandbox == 0}selected="selected"{/if}>{l s='Off' mod='imoje'}</option>
						<option value="1" {if $imoje_sandbox == 1}selected="selected"{/if}>{l s='On' mod='imoje'}</option>
					</select>
					<p>{l s='In order to use sandbox mode, you must create an account in a dedicated ' mod='imoje'}<b><a href="https://sandbox.imoje.ing.pl">{l s='sandbox environment' mod='imoje'}</a></b>
				</div>

				<label for="imoje_create_order_arrangement">{l s='Create order' mod='imoje'}</label>
				<div class="margin-form">
					<select name="imoje_create_order_arrangement" class="imoje-sel-en">
						<option value="0"
                                {if $imoje_create_order_arrangement == 0}selected{/if}>{l s='After checkout' mod='imoje'}</option>
						<option value="1"
                                {if $imoje_create_order_arrangement == 1}selected{/if}>{l s='After IPN' mod='imoje'}</option>
					</select>
				</div>

				<label for="imoje_cancel_order">{l s='Allow order to be cancelled via notification' mod='imoje'}</label>
				<div class="margin-form">
					<select name="imoje_cancel_order" class="imoje-sel-en">
						<option value="0" {if $imoje_cancel_order == 0}selected{/if}>{l s='Off' mod='imoje'}</option>
						<option value="1" {if $imoje_cancel_order == 1}selected{/if}>{l s='On' mod='imoje'}</option>
					</select>
				</div>

				<label for="imoje_ing_ksiegowosc">{l s='ING Księgowość' mod='imoje'}</label>
				<div class="margin-form">
					<select name="imoje_ing_ksiegowosc" class="imoje-sel-en">
						<option value="0" {if $imoje_ing_ksiegowosc == 0}selected{/if}>{l s='Off' mod='imoje'}</option>
						<option value="1" {if $imoje_ing_ksiegowosc == 1}selected{/if}>{l s='On' mod='imoje'}</option>
					</select>
					<p>{l s='If you are entitled to a tax exemption and you want imoje to send the basis for the exemption to ING Księgowość, then create a new tax class with a name starting as ' mod='imoje'}
						<b>ZW_</b>{l s=' and ending with one of the available values of the ' mod='imoje'}<b>basisForVatExemption</b>{l s='object at the following ' mod='imoje'}
						<b><a href="https://imojeapi.docs.apiary.io/#/introduction/ing-ksiegowosc">{l s='link' mod='imoje'}</a></b>.{l s=' Example: ' mod='imoje'}<b>ZW_DENTAL_TECHNICAN_SERVICES</b>
					</p>
				</div>

				<label for="imoje_ing_lease_now">{l s='ING Lease Now' mod='imoje'}</label>
				<div class="margin-form">
					<select name="imoje_ing_lease_now" class="imoje-sel-en">
						<option value="0" {if $imoje_ing_lease_now == 0}selected{/if}>{l s='Off' mod='imoje'}</option>
						<option value="1" {if $imoje_ing_lease_now == 1}selected{/if}>{l s='On' mod='imoje'}</option>
					</select>
				</div>

				<label for="imoje_assign_stock_negative_status">{l s='Assign pending status for orders with negative stock status' mod='imoje'}</label>
				<div class="margin-form">
					<select name="imoje_assign_stock_negative_status" class="imoje-sel-en">
						<option value="0" {if $imoje_assign_stock_negative_status == 0}selected{/if}>{l s='Off' mod='imoje'}</option>
						<option value="1" {if $imoje_assign_stock_negative_status == 1}selected{/if}>{l s='On' mod='imoje'}</option>
					</select>
				</div>

				<div id="imoje_data">

					<label for="imoje_merchant_id">{l s='Merchant ID' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_merchant_id" id="imoje_merchant_id"
						       value="{$imoje_merchant_id|escape:'htmlall':'UTF-8'}" required
						       data-error-msg="{l s='Merchant ID is not a valid key' mod='imoje'}"/>
					</div>

					<label for="imoje_service_id">{l s='Service ID' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_service_id" id="imoje_service_id"
						       value="{$imoje_service_id|escape:'htmlall':'UTF-8'}" required
						       data-error-msg="{l s='Service ID is not a valid key' mod='imoje'}"/>
					</div>

					<label for="imoje_service_key">{l s='Service key' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_service_key" id="imoje_service_key"
						       value="{$imoje_service_key|escape:'htmlall':'UTF-8'}" required
						       data-error-msg="{l s='Service Key is not a valid key' mod='imoje'}"/>
					</div>

					<label for="imoje_token">{l s='Authorization token' mod='imoje'}</label>
					<div class="margin-form">
						<input id="imoje_authorization_token" type="text" class="text" name="imoje_token" id="imoje_token"
						       value="{$imoje_token|escape:'htmlall':'UTF-8'}"/>
					</div>

					<br/>

					<label for="imoje_ga_key">{l s='Google Analytics key' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_ga_key" id="imoje_ga_key"
						       value="{$imoje_ga_key|escape:'htmlall':'UTF-8'}"
						       data-error-msg="{l s='Google Analytics key is not a valid' mod='imoje'}"/>
					</div>

					<br/>
					<hr/>
					<h3>
                        {l s='imoje' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_payment_title" id="imoje_payment_title"
						       placeholder="{$imoje_payment_title_default}"
						       value="{if $imoje_payment_title}{$imoje_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>

					<label for="imoje_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_imoje_button" type="hidden">
						<input id="imoje_button" {if $imoje_imoje_button}checked{/if} value="1" class="checkbox"
						       name="imoje_imoje_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_hide_brand" type="hidden">
						<input id="imoje_hide_brand" {if $imoje_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_hide_brand" type="checkbox">
					</div>

					<label for="imoje_currencies[]">{l s='Available currencies' mod='imoje'}
						({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'}
						)</label>
					<div class="margin-form">
						<select class="imoje_currencies" name="imoje_currencies[]" multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>

					<hr/>
					<h3>
                        {l s='BLIK' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_blik_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_blik_payment_title" id="imoje_blik_payment_title"
						       placeholder="{$imoje_blik_payment_title_default}"
						       value="{if $imoje_blik_payment_title}{$imoje_blik_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>
					<label for="imoje_blik_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_blik_button" type="hidden">
						<input id="imoje_blik_button" {if $imoje_blik_button}checked{/if} value="1" class="checkbox"
						       name="imoje_blik_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_blik_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0" name="imoje_blik_hide_brand" type="hidden">
						<input id="imoje_blik_hide_brand" {if $imoje_blik_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_blik_hide_brand" type="checkbox">
					</div>

					<label for="imoje_blik_code_checkout">{l s='Display code field to pay in store' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_blik_code_checkout" type="hidden">
						<input {if $imoje_blik_code_checkout}checked{/if} value="1" class="checkbox"
						       name="imoje_blik_code_checkout" type="checkbox">
					</div>

                    {*                    <label for="imoje_blik_oneclick">{l s='Activate OneClick' mod='imoje'}</label>*}
                    {*                    <div class="margin-form">*}
                    {*                        <input value="0"*}
                    {*                               name="imoje_blik_oneclick" type="hidden">*}
                    {*                        <input {if $imoje_blik_oneclick}checked{/if} value="1" class="checkbox"*}
                    {*                               name="imoje_blik_oneclick" type="checkbox">*}
                    {*                    </div>*}
					<br/>

					<label for="imoje_blik_currencies[]">{l s='Available currencies' mod='imoje'}({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'})</label>
					<div class="margin-form">
						<select class="imoje_blik_currencies" name="imoje_blik_currencies[]" multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_blik_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>

					<hr/>
					<h3>
                        {l s='PBL' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_pbl_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_pbl_payment_title" id="imoje_pbl_payment_title"
						       placeholder="{$imoje_pbl_payment_title_default}"
						       value="{if $imoje_pbl_payment_title}{$imoje_pbl_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>

					<label for="imoje_pbl_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_pbl_button" type="hidden">
						<input id="imoje_pbl_button" {if $imoje_pbl_button}checked{/if} value="1" class="checkbox"
						       name="imoje_pbl_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_pbl_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0" name="imoje_pbl_hide_brand" type="hidden">
						<input id="imoje_pbl_hide_brand" {if $imoje_pbl_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_pbl_hide_brand" type="checkbox">
					</div>

					<label for="imoje_pbl_currencies[]">{l s='Available currencies' mod='imoje'}
						({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'}
						)</label>
					<div class="margin-form">
						<select class="imoje_pbl_currencies" name="imoje_pbl_currencies[]" multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_pbl_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>

					<hr/>
					<h3>
                        {l s='Cards' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_cards_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_cards_payment_title" id="imoje_cards_payment_title"
						       placeholder="{$imoje_cards_payment_title_default}"
						       value="{if $imoje_cards_payment_title}{$imoje_cards_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>

					<label for="imoje_cards_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_cards_button" type="hidden">
						<input id="imoje_cards_button" {if $imoje_cards_button}checked{/if} value="1" class="checkbox"
						       name="imoje_cards_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_cards_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_cards_hide_brand" type="hidden">
						<input id="imoje_cards_hide_brand" {if $imoje_cards_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_cards_hide_brand" type="checkbox">
					</div>

					<label for="imoje_cards_currencies[]">{l s='Available currencies' mod='imoje'}
						({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'}
						)</label>
					<div class="margin-form">
						<select class="imoje_cards_currencies" name="imoje_cards_currencies[]" multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_cards_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>

					<hr/>
					<h3>
                        {l s='Electronic wallet' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_wallet_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_wallet_payment_title"
						       id="imoje_wallet_payment_title"
						       placeholder="{$imoje_wallet_payment_title_default}"
						       value="{if $imoje_wallet_payment_title}{$imoje_wallet_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>

					<label for="imoje_wallet_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_wallet_button" type="hidden">
						<input id="imoje_wallet_button" {if $imoje_wallet_button}checked{/if} value="1"
						       class="checkbox" name="imoje_wallet_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_wallet_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_wallet_hide_brand" type="hidden">
						<input id="imoje_wallet_hide_brand" {if $imoje_wallet_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_wallet_hide_brand" type="checkbox">
					</div>

					<label for="imoje_wallet_currencies[]">{l s='Available currencies' mod='imoje'}
						({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'}
						)</label>
					<div class="margin-form">
						<select class="imoje_wallet_currencies" name="imoje_wallet_currencies[]"
						        multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_wallet_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>

					<hr/>
					<h3>
                        {l s='Visa Mobile' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_visa_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_visa_payment_title" id="imoje_visa_payment_title"
						       placeholder="{$imoje_visa_payment_title_default}"
						       value="{if $imoje_visa_payment_title}{$imoje_visa_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>

					<label for="imoje_visa_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_visa_button" type="hidden">
						<input id="imoje_visa_button" {if $imoje_cards_button}checked{/if} value="1" class="checkbox"
						       name="imoje_visa_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_visa_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_visa_hide_brand" type="hidden">
						<input id="imoje_visa_hide_brand" {if $imoje_visa_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_visa_hide_brand" type="checkbox">
					</div>

					<label for="imoje_visa_currencies[]">{l s='Available currencies' mod='imoje'}
						({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'}
						)</label>
					<div class="margin-form">
						<select class="imoje_visa_currencies" name="imoje_visa_currencies[]" multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_visa_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>

					<hr/>
					<h3>
                        {l s='imoje pay later' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_paylater_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_paylater_payment_title"
						       id="imoje_paylater_payment_title"
						       placeholder="{$imoje_paylater_payment_title_default}"
						       value="{if $imoje_paylater_payment_title}{$imoje_paylater_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>

					<label for="imoje_paylater_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_paylater_button" type="hidden">
						<input id="imoje_paylater_button" {if $imoje_paylater_button}checked{/if} value="1"
						       class="checkbox" name="imoje_paylater_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_paylater_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_paylater_hide_brand" type="hidden">
						<input id="imoje_paylater_hide_brand" {if $imoje_paylater_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_paylater_hide_brand" type="checkbox">
					</div>

					<label for="imoje_paylater_currencies[]">{l s='Available currencies' mod='imoje'}
						({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'}
						)</label>
					<div class="margin-form">
						<select class="imoje_paylater_currencies" name="imoje_paylater_currencies[]"
						        multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_paylater_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>

					<hr/>
					<h3>
                        {l s='imoje installments' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_installments_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_installments_payment_title"
						       id="imoje_installments_payment_title"
						       placeholder="{$imoje_installments_payment_title_default}"
						       value="{if $imoje_installments_payment_title}{$imoje_installments_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>

					<label for="imoje_installments_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_installments_button" type="hidden">
						<input id="imoje_installments_button" {if $imoje_installments_button}checked{/if} value="1"
						       class="checkbox" name="imoje_installments_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_installments_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_installments_hide_brand" type="hidden">
						<input id="imoje_installments_hide_brand" {if $imoje_installments_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_installments_hide_brand" type="checkbox">
					</div>

					<label for="imoje_installments_currencies[]">{l s='Available currencies' mod='imoje'}
						({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'}
						)</label>
					<div class="margin-form">
						<select class="imoje_installments_currencies" name="imoje_installments_currencies[]"
						        multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_installments_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>

					<hr/>
					<h3>
                        {l s='Wire transfer' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_wt_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_wt_payment_title" id="imoje_wt_payment_title"
						       placeholder="{$imoje_wt_payment_title_default}"
						       value="{if $imoje_wt_payment_title}{$imoje_wt_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>

					<label for="imoje_wt_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_wt_button" type="hidden">
						<input id="imoje_wt_button" {if $imoje_wt_button}checked{/if} value="1" class="checkbox"
						       name="imoje_wt_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_wt_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_wt_hide_brand" type="hidden">
						<input id="imoje_wt_hide_brand" {if $imoje_wt_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_wt_hide_brand" type="checkbox">
					</div>

					<label for="imoje_wt_currencies[]">{l s='Available currencies' mod='imoje'}
						({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'}
						)</label>
					<div class="margin-form">
						<select class="imoje_wt_currencies" name="imoje_wt_currencies[]" multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_wt_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>
					<hr/>
					<h3>
                        {l s='LeaseNow' mod='imoje'}
					</h3>
					<hr/>
					<br/>

					<label for="imoje_leasenow_payment_title">{l s='Payment title' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_leasenow_payment_title" id="imoje_leasenow_payment_title"
						       placeholder="{$imoje_leasenow_payment_title_default}"
						       value="{if $imoje_leasenow_payment_title}{$imoje_leasenow_payment_title|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>

					<label for="imoje_leasenow_button">{l s='Show payment button (checkout)' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_leasenow_button" type="hidden">
						<input id="imoje_leasenow_button" {if $imoje_leasenow_button}checked{/if} value="1" class="checkbox"
						       name="imoje_leasenow_button" type="checkbox">
					</div>

					<br/>

					<label for="imoje_leasenow_hide_brand">{l s='Hide brand' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_leasenow_hide_brand" type="hidden">
						<input id="imoje_leasenow_hide_brand" {if $imoje_leasenow_hide_brand}checked{/if} value="1" class="checkbox"
						       name="imoje_leasenow_hide_brand" type="checkbox">
					</div>

					<label for="imoje_leasenow_currencies[]">{l s='Available currencies' mod='imoje'}
						({l s='if you want pick more currency hold CTRl and press left button mouse on each one' mod='imoje'}
						)</label>
					<div class="margin-form">
						<select class="imoje_leasenow_currencies" name="imoje_leasenow_currencies[]" multiple="multiple">
                            {foreach from=$imoje_available_currencies item=foo}
								<option value="{$foo}"
                                        {if in_array($foo, $imoje_leasenow_currencies)}selected{/if}>{$foo}</option>
                            {/foreach}
						</select>
					</div>

					<label for="imoje_leasenow_display_card">{l s='Display image on product page' mod='imoje'}</label>
					<div class="margin-form">
						<input value="0"
						       name="imoje_leasenow_display_card" type="hidden">
						<input id="imoje_leasenow_display_card" {if $imoje_leasenow_display_card}checked{/if} value="1" class="checkbox"
						       name="imoje_leasenow_display_card" type="checkbox">
					</div>

					<label for="imoje_leasenow_button_width">{l s='Size Lease Now button in percent' mod='imoje'}</label>
					<div class="margin-form">
						<input type="text" class="text" name="imoje_leasenow_button_width" id="imoje_leasenow_button_width"
						       placeholder="{$imoje_leasenow_button_width}"
						       value="{if $imoje_leasenow_button_width}{$imoje_leasenow_button_width|escape:'htmlall':'UTF-8'}{/if}"/>
						<p>{l s='Value in percent, for example 50' mod='imoje'}
						</p>
					</div>
				</div>
				<hr/>
			</div>

			<div class="margin-form">
				<input id="submit-imoje-configuration" type="submit" class="btn btn-primary" name="submitImoje"
				       value="{l s='Save' mod='imoje'}"/>
			</div>
		</form>
	</div>
</div>
