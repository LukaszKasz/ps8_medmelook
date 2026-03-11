{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<!-- Module Presta Blog -->
<div id="categoriesFiltrage">
	{PrestaBlogContent return=$displaySelectArboCategories}
	<form action="{PrestaBlogUrl}" method="get">
		<div id="prestablog_input_filtre_cat">
			{foreach from=$prestablog_search_array_cat item=cat_filtre}
				<input type="hidden" name="prestablog_search_array_cat[]" value="{$cat_filtre|escape:'htmlall':'UTF-8'}" />
			{/foreach}
		</div>
        <div id="categoriesForFilter">
            {if Tools::getValue('prestablog_search_array_cat') && count(Tools::getValue('prestablog_search_array_cat')) > 0}
                {foreach Tools::getValue('prestablog_search_array_cat') $cat_id}
                    <div class="filtrecat" rel="{$cat_id|intval}">{$categorie_filtre[$cat_id]->title|escape:'htmlall':'UTF-8'}
                        <div class="deleteCat" rel="{$cat_id|intval}">X</div>
                    </div>
                {/foreach}
            {/if}
        </div>
		<input class="search_query form-control ac_input" type="text" value="{$prestablog_search_query|escape:'htmlall':'UTF-8'}" placeholder="{l s='Search again on blog' d='Modules.Prestablog.Grid'}" name="prestablog_search" autocomplete="off">
		<button class="btn btn-default button-search" type="submit">
			<span>{l s='Search again on blog' d='Modules.Prestablog.Grid'}</span>
		</button>
		<div class="clear"></div>
	</form>
</div>
<!-- Module Presta Blog -->
