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
	<div class="panel-heading tab">
		<i class="icon-link"></i> <h2>{l s='Product options' mod='pdgooglemerchantcenterpro'}</h2>
	</div>

	<div class="clearfix container-fluid">
		<input type="hidden" name="submitted_conf[]" value="Modulepdgooglemerchantcenterpro" />


	<div class="form-group col-lg-12">
			<label class="form-control-label">
				{l s='In Google Shopping' mod='pdgooglemerchantcenterpro'}
				<span class="help-box" data-toggle="popover" data-content="{l s='In Google Shopping' mod='pdgooglemerchantcenterpro'}"></span>
			</label>
			<div class="row">
			<div class="radio_block">
				<div class="col-lg-12">
					<div class="widget-radio-inline">
					<div class="radio">
						<label class="">
							<input  type="radio" name="in_google_shopping" id="in_price_compare_on" value="1" {if $product->in_google_shopping}checked="checked" {/if} />
							{l s='Yes' mod='pdgooglemerchantcenterpro'}
						</label>
					</div>
					</div>
					<div class="widget-radio-inline">
					<div class="radio">
						<label class="">
							<input  type="radio" name="in_google_shopping" id="in_price_compare_off" value="0" {if !$product->in_google_shopping}checked="checked"{/if} />
							{l s='No' mod='pdgooglemerchantcenterpro'}
						</label>
					</div>
					</div>
				</div>
			</div>
			</div>
	</div>



	<fieldset class="form-group">
	    <label class="form-control-label">
	    	{l s='Alternate product name'  mod='pdgooglemerchantcenterpro'}
	    	<span class="help-box" data-toggle="popover" data-content="{l s='Alternate product name if provided normal product name will be replaced with this one' mod='pdgooglemerchantcenterpro'}"></span>
	    </label>
	    <div class="translations tabbable">

	        <div class="translationsFields tab-content ">
	            {foreach from=$languages item=language}
	                {if $language.active}
	                <div data-locale="{$language.iso_code}" class="translationsFields-product_name_google_shopping{$language.id_lang} tab-pane{if $id_language == $language.id_lang} show active{/if} translation-field  translation-label-{$language.iso_code}">
	                    <input 
	                    	type="text" 
	                        name="product_name_google_shopping_{$language.id_lang}"
	                        class="form-control"
	                        value="{$product->product_name_google_shopping[$language.id_lang]}">
	                </div>
	                {/if}
	            {/foreach}
	        </div>
	    </div>
	</fieldset>




	<fieldset class="form-group">
	    <label class="form-control-label">
	    	{l s='Alternate product short description'  mod='pdgooglemerchantcenterpro'}
	    	<span class="help-box" data-toggle="popover" data-content="{l s='Alternate product short description if provided normal product short description will be replaced with this one' mod='pdgooglemerchantcenterpro'}"></span>
	    </label>
	    <div class="translations tabbable">

	        <div class="translationsFields tab-content ">
	            {foreach from=$languages item=language}
	                {if $language.active}
	                <div data-locale="{$language.iso_code}" class="translationsFields-product_short_desc_google_shopping{$language.id_lang} tab-pane{if $id_language == $language.id_lang} show active{/if} translation-field  translation-label-{$language.iso_code}">
	                    <textarea 
	                    	type="text" 
	                        name="product_short_desc_google_shopping_{$language.id_lang}"
	                        class="form-control"
	                        >{$product->product_short_desc_google_shopping[$language.id_lang]}</textarea>
	                </div>
	                {/if}
	            {/foreach}
	        </div>
	    </div>
	</fieldset>



	<fieldset class="form-group">
	    <label class="form-control-label">
	    	{l s='Custom label 0'  mod='pdgooglemerchantcenterpro'}
	    	<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
	    </label>
	    <div class="translations tabbable">

	        <div class="translationsFields tab-content ">
	            {foreach from=$languages item=language}
	                {if $language.active}
	                <div data-locale="{$language.iso_code}" class="translationsFields-custom_label_0{$language.id_lang} tab-pane{if $id_language == $language.id_lang} show active{/if} translation-field  translation-label-{$language.iso_code}">
	                    <textarea 
	                    	type="text" 
	                        name="custom_label_0_{$language.id_lang}"
	                        class="form-control"
	                        >{$product->custom_label_0[$language.id_lang]}</textarea>
	                </div>
	                {/if}
	            {/foreach}
	        </div>
	    </div>
	</fieldset>


	<fieldset class="form-group">
	    <label class="form-control-label">
	    	{l s='Custom label 1'  mod='pdgooglemerchantcenterpro'}
	    	<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
	    </label>
	    <div class="translations tabbable">

	        <div class="translationsFields tab-content ">
	            {foreach from=$languages item=language}
	                {if $language.active}
	                <div data-locale="{$language.iso_code}" class="translationsFields-custom_label_1{$language.id_lang} tab-pane{if $id_language == $language.id_lang} show active{/if} translation-field  translation-label-{$language.iso_code}">
	                    <textarea 
	                    	type="text" 
	                        name="custom_label_1_{$language.id_lang}"
	                        class="form-control"
	                        >{$product->custom_label_1[$language.id_lang]}</textarea>
	                </div>
	                {/if}
	            {/foreach}
	        </div>
	    </div>
	</fieldset>



	<fieldset class="form-group">
	    <label class="form-control-label">
	    	{l s='Custom label 2'  mod='pdgooglemerchantcenterpro'}
	    	<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
	    </label>
	    <div class="translations tabbable">

	        <div class="translationsFields tab-content ">
	            {foreach from=$languages item=language}
	                {if $language.active}
	                <div data-locale="{$language.iso_code}" class="translationsFields-custom_label_2{$language.id_lang} tab-pane{if $id_language == $language.id_lang} show active{/if} translation-field  translation-label-{$language.iso_code}">
	                    <textarea 
	                    	type="text" 
	                        name="custom_label_2_{$language.id_lang}"
	                        class="form-control"
	                        >{$product->custom_label_2[$language.id_lang]}</textarea>
	                </div>
	                {/if}
	            {/foreach}
	        </div>
	    </div>
	</fieldset>



	<fieldset class="form-group">
	    <label class="form-control-label">
	    	{l s='Custom label 3'  mod='pdgooglemerchantcenterpro'}
	    	<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
	    </label>
	    <div class="translations tabbable">

	        <div class="translationsFields tab-content ">
	            {foreach from=$languages item=language}
	                {if $language.active}
	                <div data-locale="{$language.iso_code}" class="translationsFields-custom_label_3{$language.id_lang} tab-pane{if $id_language == $language.id_lang} show active{/if} translation-field  translation-label-{$language.iso_code}">
	                    <textarea 
	                    	type="text" 
	                        name="custom_label_3_{$language.id_lang}"
	                        class="form-control"
	                        >{$product->custom_label_3[$language.id_lang]}</textarea>
	                </div>
	                {/if}
	            {/foreach}
	        </div>
	    </div>
	</fieldset>
	

	<fieldset class="form-group">
	    <label class="form-control-label">
	    	{l s='Custom label 4'  mod='pdgooglemerchantcenterpro'}
	    	<span class="help-box" data-toggle="popover" data-content="{l s='Can contain additional information about the item' mod='pdgooglemerchantcenterpro'}"></span>
	    </label>
	    <div class="translations tabbable">

	        <div class="translationsFields tab-content ">
	            {foreach from=$languages item=language}
	                {if $language.active}
	                <div data-locale="{$language.iso_code}" class="translationsFields-custom_label_4{$language.id_lang} tab-pane{if $id_language == $language.id_lang} show active{/if} translation-field  translation-label-{$language.iso_code}">
	                    <textarea 
	                    	type="text" 
	                        name="custom_label_4_{$language.id_lang}"
	                        class="form-control"
	                        >{$product->custom_label_4[$language.id_lang]}</textarea>
	                </div>
	                {/if}
	            {/foreach}
	        </div>
	    </div>
	</fieldset>
	


	</div>
</div>


<!-- PD Google Merchant Center Pro -->