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


<script type="text/javascript">
{literal}
$(document).ready(function(){

	$('select#product_name_google_shopping_copy_from').on('change', function() {
		change_name_lang_values_per_selection(parseInt($(this).val()));
	});

	function change_name_lang_values_per_selection(option_selected) {
		if (option_selected == 1) {
			$.each(product_meta_title, function(key, value) {
				$('textarea#product_name_google_shopping_'+key).val(convertHtmlToText(value));
			});
		} else if (option_selected == 2) {
			$.each(product_name, function(key, value) {
				$('textarea#product_name_google_shopping_'+key).val(convertHtmlToText(value));
			});
		} else if (option_selected == 3) {
			$.each(product_name, function(key, value) {
				$('textarea#product_name_google_shopping_'+key).val('');
			});
		}
	}

	$('select#product_short_description_google_shopping_copy_from').on('change', function() {
		change_desc_lang_values_per_selection(parseInt($(this).val()));
	});

	function change_desc_lang_values_per_selection(option_selected) {
		if (option_selected == 1) {
			$.each(product_meta_dcescription, function(key, value) {
				$('textarea#product_short_desc_google_shopping_'+key).val(convertHtmlToText(value));
			});
		} else if (option_selected == 2) {
			$.each(product_description_short, function(key, value) {
				$('textarea#product_short_desc_google_shopping_'+key).val(convertHtmlToText(value));
			});
		} else if (option_selected == 3) {
			$.each(product_name, function(key, value) {
				$('textarea#product_short_desc_google_shopping_'+key).val('');
			});
		}
	}
});

{/literal}
</script>



<!-- PD Google Merchant Center Pro -->
<div id="ModulepdgooglemerchantcenterproProductTab" class="panel product-tab">
<h3>{l s='Product options' mod='pdgooglemerchantcenterpro'}</h3>
		
	<div class="form-group">
		<input type="hidden" name="submitted_conf[]" value="Modulepdgooglemerchantcenterpro" />
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="in_google_shopping" type="radio" onclick=""}</span></div>
		<label class="control-label col-lg-2">
			{l s='In Google Shopping' mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="col-lg-9">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="in_google_shopping" id="in_google_shopping_on" value="1" {if $product->in_google_shopping}checked="checked" {/if} />
				<label for="in_google_shopping_on" class="radioCheck">
					{l s='Yes' mod='pdgooglemerchantcenterpro'}
				</label>
				<input type="radio" name="in_google_shopping" id="in_google_shopping_off" value="0" {if !$product->in_google_shopping}checked="checked"{/if} />
				<label for="in_google_shopping_off" class="radioCheck">
					{l s='No' mod='pdgooglemerchantcenterpro'}
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Include this product in Google Shopping feed' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="product_name_google_shopping">
			{l s='Alternate product name'  mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="col-lg-9">
			{include file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name='product_name_google_shopping'
				class="product_name_google_shopping"
				maxchar=128
				input_value=$product->product_name_google_shopping}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Alternate product name if provided normal product name will be replaced with this one' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">
		</span></div>
		<label class="control-label col-lg-2" for="product_name_google_shopping_copy_from">
			{l s='Copy alternate product name' mod='pdgooglemerchantcenterpro'} 
		</label>
		<div class="col-lg-3">
			<select name="product_name_google_shopping_copy_from" id="product_name_google_shopping_copy_from">
				<option value="0">{l s='-- please select --' mod='pdgooglemerchantcenterpro'}</option>
				<option value="1">{l s='Product meta title' mod='pdgooglemerchantcenterpro'}</option>
				<option value="2">{l s='Product name' mod='pdgooglemerchantcenterpro'}</option>
				<option value="3">{l s='Empty field' mod='pdgooglemerchantcenterpro'}</option>
			</select>
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Copy alternate product name from meta title or product name, when selected above field will be updated automatically' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>


	<div class="form-group">
		<label class="control-label col-lg-3" for="product_short_desc_google_shopping">
			{l s='Alternate product description'  mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="col-lg-9">
			{include file="controllers/products/textarea_lang.tpl"
				languages=$languages
				maxchar=5000
				class="product_short_desc_google_shopping"
				input_name='product_short_desc_google_shopping'
				input_value=$product->product_short_desc_google_shopping}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Alternate product description if provided normal product short description will be replaced with this one' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">
		</span></div>
		<label class="control-label col-lg-2" for="product_short_description_google_shopping_copy_from">
			{l s='Copy alternate product description' mod='pdgooglemerchantcenterpro'} 
		</label>
		<div class="col-lg-3">
			<select name="product_short_description_google_shopping_copy_from" id="product_short_description_google_shopping_copy_from">
				<option value="0">{l s='-- please select --' mod='pdgooglemerchantcenterpro'}</option>
				<option value="1">{l s='Meta description' mod='pdgooglemerchantcenterpro'}</option>
				<option value="2">{l s='Product short description' mod='pdgooglemerchantcenterpro'}</option>
				<option value="3">{l s='Empty field' mod='pdgooglemerchantcenterpro'}</option>
			</select>
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Copy alternate product description from meta dsecription or product short description, when selected above field will be updated automatically' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>


	<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_0">
			{l s='Custom label 0'  mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="col-lg-9">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_0'
				maxchar=100
				input_value=$product->custom_label_0}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>


	<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_1">
			{l s='Custom label 1'  mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="col-lg-9">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_1'
				maxchar=100
				input_value=$product->custom_label_1}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>


	<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_2">
			{l s='Custom label 2'  mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="col-lg-9">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_2'
				maxchar=100
				input_value=$product->custom_label_2}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>



		<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_3">
			{l s='Custom label 3'  mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="col-lg-9">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_3'
				maxchar=100
				input_value=$product->custom_label_3}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>


		<div class="form-group">
		<label class="control-label col-lg-3" for="custom_label_4">
			{l s='Custom label 4'  mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="col-lg-9">

			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='custom_label_4'
				maxchar=100
				input_value=$product->custom_label_4}
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">
			{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</div>
		</div>
	</div>



	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'  mod='pdgooglemerchantcenterpro'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='pdgooglemerchantcenterpro'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='pdgooglemerchantcenterpro'}</button>
	</div>

</div>

<script type="text/javascript">
	if (tabs_manager.allow_hide_other_languages) {
		hideOtherLanguage({$default_form_language|escape:'htmlall':'UTF-8'});
	}
</script>

<!-- PD Google Merchant Center Pro -->