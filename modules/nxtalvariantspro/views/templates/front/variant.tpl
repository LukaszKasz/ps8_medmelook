{**
 * Product Variants Pro
 *
 * @author    Nxtal <support@nxtal.com>
 * @copyright Nxtal 2023
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @version   1.4.0
 *
 *}

{if $variant_groups}
	<div class="nxtal-variant-box">
		{foreach from=$variant_groups item='variant_group'}
			<div class="nxtal-variant" data-image="{$variant_group.image|escape:'htmlall':'UTF-8'}">
				<div class="nxtal-variant-label">
					<div class="variant-group-left-inline">
						<span class="variant-group-label">
							{$variant_group.name|escape:'htmlall':'UTF-8'}:
						</span>
						<span class="variant-group-value"></span>						
					</div>
					<div class="variant-group-right-inline">
						{if $variant_group.image}
							<span class="variant-group-image">
							</span>
						{/if}
						{* <span class="variant-group-action">
							<i class="material-icons">expand_more</i>
						</span> *}
					</div>
				</div>
				<div class="nxtal-variant-attributes">
					<ul>
					{foreach from=$variant_group.products item='vProduct'}
						<li {if $vProduct.id_product eq $id_product}class="active"{/if} data-label="{$vProduct.label|escape:'htmlall':'UTF-8'}" {if $vProduct.cover}data-cover="{$vProduct.cover|escape:'htmlall':'UTF-8'}"{/if}>
							<a href="{$vProduct.url|escape:'htmlall':'UTF-8'}" title="{$vProduct.label|escape:'htmlall':'UTF-8'}">
								{if $vProduct.image}
									<img src="{$vProduct.image|escape:'htmlall':'UTF-8'}" alt="{$vProduct.label|escape:'htmlall':'UTF-8'}" />
								{/if}
							</a>
						</li>
					{/foreach}
					</ul>	
				</div>
				<div class="xs-toggle-view">{l s='Full view' mod='nxtalvariantspro'}</div>
			</div>
		{/foreach}
	</div>
	<script>
		var textFullView = '{l s='Full view' mod='nxtalvariantspro'}';
		var textMinimalView = '{l s='Minimal view' mod='nxtalvariantspro'}';
	</script>
{/if}
