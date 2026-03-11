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
<fieldset id="ModulepdgooglemerchantcenterproProductTab">
<h4>{l s='Product options' mod='pdgooglemerchantcenterpro'}</h4>
<div class="separation"></div>

		<input type="hidden" name="submitted_conf[]" value="ModulePdPriceComparePro" />
		
		<label>
			{l s='In Google Shopping' mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="margin-form">
				<input type="radio" name="in_google_shopping" id="in_google_shopping_on" value="1" {if $product->in_google_shopping}checked="checked" {/if} />
				<label for="in_google_shopping_on" class="t">
					{l s='Yes' mod='pdgooglemerchantcenterpro'}
				</label>
				<input type="radio" name="in_google_shopping" id="in_google_shopping_off" value="0" {if !$product->in_google_shopping}checked="checked"{/if} />
				<label for="in_google_shopping_off" class="t">
					{l s='No' mod='pdgooglemerchantcenterpro'}
				</label>
				<p class="preference_description">
					{l s='Include this product in Google Shopping feed' mod='pdgooglemerchantcenterpro'}
				</p>
		</div>
		<div class="clear"></div>


		<label>
			{l s='Alternate product name' mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name='product_name_google_shopping'
				class="product_name_google_shopping"
				maxchar=128
				input_value=$product->product_name_google_shopping}
			<p class="preference_description">
				{l s='Alternate product name if provided normal product name will be replaced with this one' mod='pdgooglemerchantcenterpro'}
			</p>
		</div>
		<div class="clear"></div>

		<label for="product_name_google_shopping_copy_from">
				{l s='Copy alternate product name' mod='pdgooglemerchantcenterpro'} 
		</label>
		<div class="margin-form">
			<select name="product_name_google_shopping_copy_from" id="product_name_google_shopping_copy_from">
				<option value="0">{l s='Product meta title' mod='pdgooglemerchantcenterpro'}</option>
				<option value="1">{l s='Product name' mod='pdgooglemerchantcenterpro'}</option>
				<option value="2">{l s='Empty field' mod='pdgooglemerchantcenterpro'}</option>
			</select>
		</div>
		<p class="preference_description">
			{l s='Copy alternate product name from meta title or product name, when selected above field will be updated automatically' mod='pdgooglemerchantcenterpro'}
		</p>
		<div class="clear"></div>


		<label>
			{l s='Alternate product description' mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/textarea_lang.tpl"
				languages=$languages
				class="product_short_desc_google_shopping"
				input_name='product_short_desc_google_shopping'
				input_value=$product->product_short_desc_google_shopping}
			<p class="preference_description">
				{l s='Alternate product description if provided normal product short description will be replaced with this one' mod='pdgooglemerchantcenterpro'}
			</p>
		</div>
		<div class="clear"></div>

		<label for="product_short_description_google_shopping_copy_from">
				{l s='Copy alternate product description' mod='pdgooglemerchantcenterpro'} 
		</label>
		<div class="margin-form">
			<select name="product_short_description_google_shopping_copy_from" id="product_short_description_google_shopping_copy_from">
				<option value="0">{l s='Meta description' mod='pdgooglemerchantcenterpro'}</option>
				<option value="1">{l s='Product short description' mod='pdgooglemerchantcenterpro'}</option>
				<option value="2">{l s='Empty field' mod='pdgooglemerchantcenterpro'}</option>
			</select>
		</div>
		<p class="preference_description">
			{l s='Copy alternate product description from meta dsecription or product short description, when selected above field will be updated automatically' mod='pdgooglemerchantcenterpro'}
		</p>
		<div class="clear"></div>


		<label for="custom_label_0">
			{l s='Custom label 0' mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_0'
                maxchar=100
                input_value=$product->custom_label_0}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</p>
		</div>
		<div class="clear"></div>

		<label for="custom_label_1">
			{l s='Custom label 1' mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_1'
                maxchar=100
                input_value=$product->custom_label_1}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</p>
		</div>
		<div class="clear"></div>


		<label for="custom_label_2">
			{l s='Custom label 2' mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_2'
                maxchar=100
                input_value=$product->custom_label_2}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</p>
		</div>
		<div class="clear"></div>


		<label for="custom_label_3">
			{l s='Custom label 3' mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_3'
                maxchar=100
                input_value=$product->custom_label_3}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</p>
		</div>
		<div class="clear"></div>


		<label for="custom_label_4">
			{l s='Custom label 4' mod='pdgooglemerchantcenterpro'}
		</label>
		<div class="margin-form">
			{include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_name='custom_label_4'
                maxchar=100
                input_value=$product->custom_label_4}
		
			<p class="preference_description">
				{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}
			</p>
		</div>
		<div class="clear"></div>

		
</fieldset>
<!-- PD Google Merchant Center Pro -->