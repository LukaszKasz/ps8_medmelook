{*
* Product Variants Pro
*
* @author    Nxtal <support@nxtal.com>
* @copyright Nxtal 2023
* @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
* @version   1.4.0
*
*}

<div class="panel filter-panel" data-multioption="{$isMultiple|escape:'htmlall':'UTF-8'}" data-type="{$type|escape:'htmlall':'UTF-8'}">
	{if $isMultiple}
	<div class="tree-panel-heading-controls clearfix">
		<div class="input-group">
		    <input type="text" name="search" placeholder="{l s='search..' mod='nxtalvariantspro'}" autocomplete="off" spellcheck="false" dir="auto" />
			<div class="input-group-addon">
				<i class="icon-search"></i>
			</div>
	    </div>
		<div class="ajax_list"></div>
	</div>
	<div class="panel-body">
	    <div class="list-group">{foreach from=$elements item="element"}<div data-id="{$element['id_'|cat:$type|escape:'htmlall':'UTF-8']}" class="list-group-item">
				{if isset($element.image)}
				<div class="col-lg-2">
					<img src="{$element.image|escape:'htmlall':'UTF-8'}" alt="{$element.name|escape:'htmlall':'UTF-8'}" />
				</div>				
				<div class="col-lg-10">
					<h4>{$element.name|escape:'htmlall':'UTF-8'}</h4>
					<em>{l s='ID:' mod='nxtalvariantspro'} #{$element['id_'|cat:$type|escape:'htmlall':'UTF-8']|escape:'htmlall':'UTF-8'}</em>					
				</div>
				{else}
					{$element.name|escape:'htmlall':'UTF-8'}
					#{$element['id_'|cat:$type|escape:'htmlall':'UTF-8']|escape:'htmlall':'UTF-8'}
				{/if}
				<span class="clear pull-right">x</span>
				<input type="hidden" name="{$type|escape:'htmlall':'UTF-8'}s[]" value="{$element['id_'|cat:$type|escape:'htmlall':'UTF-8']|escape:'htmlall':'UTF-8'}" />
			</div>{/foreach}</div>
		<h4 class="no-data">{l s='There is no %s selected yet, you can choose specific %s by using search box.' sprintf=[$label, $label] mod='nxtalvariantspro'}</h4>
	</div>	
	{else}
		<div class="input-group">
		    <input type="text" name="search" placeholder="{l s='search..' mod='nxtalvariantspro'}" value="{if isset($elements[0].name)}{$elements[0].name|escape:'htmlall':'UTF-8'}{/if}" autocomplete="off" spellcheck="false" dir="auto" />
			<div class="input-group-addon">
				<i class="icon-search"></i>
			</div>
			<input type="hidden" name="id_{$type|escape:'htmlall':'UTF-8'}" value="{if isset($elements[0]['id_'|cat:$type])}{$elements[0]['id_'|cat:$type|escape:'htmlall':'UTF-8']|escape:'htmlall':'UTF-8'}{/if}" />
	    </div>
		<div class="ajax_list"></div>
	{/if}
</div>
<script>
var idText = "{l s='ID:' mod='nxtalvariantspro'}";
</script>