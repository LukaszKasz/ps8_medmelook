{*
* 2013-2016 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Merchant Center Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2013-2016 Patryk Marek PrestaDev.pl
* @version   Release: 2.1.2
*}




<!-- PD Google Merchant Center Pro -->
<div id="ModulepdgooglemerchantcenterproProductTab" class="panel product-tab">
<h3>{l s='Product options' mod='pdgooglemerchantcenterpro'}</h3>
		
	<input type="hidden" name="submitted_conf[]" value="Modulepdgooglemerchantcenterpro" />
		
	<br>
	<div class="form-group col-lg-12">
			<label class="form-control-label">
				{l s='In Google Shopping' mod='pdgooglemerchantcenterpro'}
				<span class="help-box" data-toggle="popover" data-content="{l s='In Google Shopping' mod='pdgooglemerchantcenterpro'}"></span>
			</label>
			<div class="row">
			<div class="radio_block">
				<div class="input-group col-lg-12">
					<div class="radio">
						<label class="">
								<input  type="radio" name="in_google_shopping" id="in_price_compare_on" value="1" {if $product->in_google_shopping}checked="checked" {/if} />
								{l s='Yes' mod='pdgooglemerchantcenterpro'}
						</label>
					</div>
					<div class="radio">
						<label class="">
								<input  type="radio" name="in_google_shopping" id="in_price_compare_off" value="0" {if !$product->in_google_shopping}checked="checked" {/if} />
								{l s='No' mod='pdgooglemerchantcenterpro'}
						</label>
						
					</div>
				</div>
			</div>
			</div>
	</div>

	<div class="form-group">
		<label class="form-control-label" for="product_name_google_shopping">
			{l s='Alternate product name'  mod='pdgooglemerchantcenterpro'}
			<span class="help-box" data-toggle="popover" data-content="{l s='Alternate product name if provided normal product name will be replaced with this one' mod='pdgooglemerchantcenterpro'}"></span>
		</label>
		<div class="col-lg-9">
			{include file="./input_text_lang.tpl"
				languages=$languages
				input_name='product_name_google_shopping'
				class="product_name_google_shopping"
		
				input_value=$product->product_name_google_shopping}
		</div>
	</div>
	<div class="form-group">
		<label class="form-control-label" for="product_short_desc_google_shopping">
			{l s='Alternate product description'  mod='pdgooglemerchantcenterpro'}
			<span class="help-box" data-toggle="popover" data-content="{l s='Alternate product description if provided normal product short description will be replaced with this one' mod='pdgooglemerchantcenterpro'}"></span>
		</label>
		<div class="col-lg-9">
			{include file="./input_text_lang.tpl"
				languages=$languages
				input_name='product_short_desc_google_shopping'
				class="product_short_desc_google_shopping"
		
				input_value=$product->product_short_desc_google_shopping}
		</div>
	</div>





	<div class="form-group">
		<label class="form-control-label" for="custom_label_0">
			{l s='Custom label 0'  mod='pdgooglemerchantcenterpro'}
			<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
		</label>
		<div class="col-lg-9">

			{include file="./input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_0'
				
				input_value=$product->custom_label_0}
		</div>
	</div>


	<div class="form-group">
		<label class="form-control-label" for="custom_label_1">
			{l s='Custom label 1'  mod='pdgooglemerchantcenterpro'}
			<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
		</label>
		<div class="col-lg-9">

			{include file="./input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_1'
				
				input_value=$product->custom_label_1}
		</div>
	</div>


	<div class="form-group">
		<label class="form-control-label" for="custom_label_2">
			{l s='Custom label 2'  mod='pdgooglemerchantcenterpro'}
			<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
		</label>
		<div class="col-lg-9">

			{include file="./input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_2'
				
				input_value=$product->custom_label_2}
		</div>
	</div>



		<div class="form-group">
		<label class="form-control-label" for="custom_label_3">
			{l s='Custom label 3'  mod='pdgooglemerchantcenterpro'}
			<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
		</label>
		<div class="col-lg-9">

			{include file="./input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_3'
				input_value=$product->custom_label_3}
		</div>
	</div>


		<div class="form-group">
		<label class="form-control-label" for="custom_label_4">
			{l s='Custom label 4'  mod='pdgooglemerchantcenterpro'}
			<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
		</label>
		<div class="col-lg-9">

			{include file="./input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_4'
				
				input_value=$product->custom_label_4}
		</div>
	</div>


</div>


<!-- PD Google Merchant Center Pro -->