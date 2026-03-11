{**
 * Product Variants Pro
 *
 * @author    Nxtal <support@nxtal.com>
 * @copyright Nxtal 2023
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @version   1.4.0
 *
 *}

{if $element_name eq 'variant_style'}
	<style name="nxtalvariantspro">
		.nxtal-variant-box .nxtal-variant-attributes li a{
			{if $element_params.NXTAL_VARIANTSPRO_BACKGROUND}background:{$element_params.NXTAL_VARIANTSPRO_BACKGROUND|escape:'htmlall':'UTF-8'} !important;{/if}
			{if $element_params.NXTAL_VARIANTSPRO_BORDER}border-color:{$element_params.NXTAL_VARIANTSPRO_BORDER|escape:'htmlall':'UTF-8'} !important;{/if}
		}
		.nxtal-variant-box .nxtal-variant-attributes li a .current-price,
		.nxtal-variant-box .nxtal-variant-attributes li a .variant-attribute-name {
			{if $element_params.NXTAL_VARIANTSPRO_COLOR}color:{$element_params.NXTAL_VARIANTSPRO_COLOR|escape:'htmlall':'UTF-8'} !important;{/if}
		}
		.nxtal-variant-box .nxtal-variant-attributes li.active a{
			{if $element_params.NXTAL_VARIANTSPRO_BACKGROUND_ACTIVE}background:{$element_params.NXTAL_VARIANTSPRO_BACKGROUND_ACTIVE|escape:'htmlall':'UTF-8'} !important;{/if}
			{if $element_params.NXTAL_VARIANTSPRO_BORDER_ACTIVE}border-color:{$element_params.NXTAL_VARIANTSPRO_BORDER_ACTIVE|escape:'htmlall':'UTF-8'} !important;{/if}
		}
		.nxtal-variant-box .nxtal-variant-attributes li.active a .current-price,
		.nxtal-variant-box .nxtal-variant-attributes li.active a .variant-attribute-name {
			{if $element_params.NXTAL_VARIANTSPRO_COLOR_ACTIVE}color:{$element_params.NXTAL_VARIANTSPRO_COLOR_ACTIVE|escape:'htmlall':'UTF-8'} !important;{/if}
		}
		{if $element_params.NXTAL_VARIANTSPRO_CSS}{$element_params.NXTAL_VARIANTSPRO_CSS nofilter}{/if}
	</style>
{/if}

{if $element_name eq 'catalog_label' && $element_params.variants > 1}
	<div class="nxtal-variant-text">
		<div>
			{l s='%s variants' sprintf=[$element_params.variants|escape:'htmlall':'UTF-8'] mod='nxtalvariantspro'}
		</div>
	</div>
{/if}

{if $element_name eq 'product_names' && $element_params.products}
	{foreach from=$element_params.products item='vProduct'}
		<a href="{$vProduct.link|escape:'htmlall':'UTF-8'}" target="_blank">{$vProduct.name|escape:'htmlall':'UTF-8'}</a><br>
	{/foreach}
{/if}

{if $element_name eq 'controller_tabs'}
	<style>
		.nxtalvariantspro-tabs {
			margin-bottom: 15px;
		}
		.nxtalvariantspro-tabs .btn {
			margin-right: 5px;
			border: 1px solid;
			font-size: 15px;
			text-transform: none !important;
			border-radius: 0 !important;
		}
	</style>
	<div class="nxtalvariantspro-tabs">	
		{foreach from=$element_params.tabs item='tab'}
			<a href="{$tab.link|escape:'htmlall':'UTF-8'}" class="btn {if $tab.active}btn-primary{/if}">{$tab.name|escape:'htmlall':'UTF-8'}</a>
		{/foreach}
	</div>
{/if}

{if $element_name eq 'image_type'}
	{l s='Set product detail main image type.' mod='nxtalvariantspro'} 
	<a href="{$element_params.link|escape:'htmlall':'UTF-8'}" target="_blank"> {l s='enabled' mod='nxtalvariantspro'}</a> 
	{l s='for selected Carousel type.' mod='nxtalvariantspro'}
{/if}

{if $element_name eq 'admin_image_input'}

<div id="variant-product-image" class="mb-3">
	<div id="variant-content" class="row">
	  <div class="col-md-12">
		<h2>{l s='Variant product image' mod='nxtalvariantspro'}
			<span class="help-box" data-toggle="popover"
				data-content="{l s='If image is set then this image will appear as variant cover image.' mod='nxtalvariantspro'}">
			</span>
		</h2>
	  </div>
	  <div class="col-lg-12">
		<fieldset class="form-group">			
			<div class="col-12 col-md-6 col-lg-6">
				<div class="variant-image-box">
					<label {if $element_params.image_link}style="background-image: url({$element_params.image_link|escape:'htmlall':'UTF-8'})"{/if}>
						<input type="file" id="variant-image-input" accept=".jpg, .png, .jpeg" >
						<div class="vi-add">
							<span>+</span>
						</div>
						<div class="vi-progress">
							<span class="vi-upload"></span>
					    </div>
					</label>
					<div class="delete-image">
						<span>{l s='Delete' mod='nxtalvariantspro'}</span>
					</div>
				</div>
				<div class="display-response"></div>
			</div>
		</fieldset>
	  </div>
	</div>
	<script type="text/javascript">	
		var variation_image_delete_confirm = '{l s='Are you sure?' mod='nxtalvariantspro'}';
	</script>
</div>
{/if}

{if $element_name eq 'import_export'}
<div>
	<ul>
		<li>{l s='To import %s by a CSV file, the %s must be set in a CSV file in a specific format.' sprintf=[$element_params.label|escape:'htmlall':'UTF-8', $element_params.label|escape:'htmlall':'UTF-8'] mod='nxtalvariantspro'} <a href="{$element_params.sample_link|escape:'htmlall':'UTF-8'}" download onClick="$('.sampleDownload').removeClass('hide');">{l s='Click here' mod='nxtalvariantspro'}</a> {l s='to download sample file.' mod='nxtalvariantspro'}
		<span class="sampleDownload alert-warning hide">{l s='The file is being downloaded..' mod='nxtalvariantspro'}</span>
		</li>
		{if $element_params.label eq 'zipcode'}
		<li>{l s='%s must be valid for that country.' sprintf=[$element_params.label|escape:'htmlall':'UTF-8'] mod='nxtalvariantspro'}
		{/if}
		</li>
		{if $element_params.export_link}
		<li><a href="{$element_params.export_link|escape:'htmlall':'UTF-8'}" download onClick="$('.exportDownload').removeClass('hide');">{l s='Click here' mod='nxtalvariantspro'}</a> {l s='to export %s in CSV file.' sprintf=[$element_params.label|escape:'htmlall':'UTF-8'] mod='nxtalvariantspro'}
		<span class="exportDownload alert-warning hide">{l s='The file is being downloaded..' mod='nxtalvariantspro'}</span>
		</li>
		{/if}
	</ul>
</div>
{/if}
