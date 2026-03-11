{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
 <script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
 <script type="text/javascript" src="{__PS_BASE_URI__|escape:'html':'UTF-8'}modules/prestablog/views/js/jquery.mjs.nestedSortable.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('div#configuration_blog .treeordercat ol.sortable').nestedSortable({
        forcePlaceholderSize: true,
        handle: 'div',
        helper:  'clone',
        items: 'li',
        opacity: .6,
        placeholder: 'placeholder',
        revert: 250,
        tabSize: 25,
        tolerance: 'pointer',
        toleranceElement: '> div',
        maxLevels: 10,
        isTree: true,
        expandOnHover: 700,
        startCollapsed: true
    });

    $('div#configuration_blog .treeordercat .disclose').on('click', function() {
      $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
    })

    $('form[name=formOrderCat]').submit(function() {
        dataprestabloged = $('div#configuration_blog .treeordercat ol.sortable').nestedSortable('dataprestablog');
        $('input[name=newOrderCat]').val(dataprestabloged);
    })
});
</script>

<div class="blocmodule">
<fieldset class="row">
<div class="col-sm-3">
<a class="btn btn-primary" href="{$confpath|escape:'html':'UTF-8'}&catListe">
<i class="icon-list"></i>&nbsp;
{l s='Return to list of categories' d='Modules.Prestablog.Prestablog'}
</a>
</div>
</fieldset>
</div>

{$prestablog->get_displayFormOpen('filter.png',"{l s='Order categories' d='Modules.Prestablog.Prestablog'}",{$confpath|escape:'html':'UTF-8'},'formOrderCat')}
<input type="hidden" name="newOrderCat" value="">
{$prestablog->get_displayInfo({l s='Change the order of categories with a simple drag and drop.' d='Modules.Prestablog.Prestablog'})}
<div class="form-group">
    <label class="control-label col-lg-2"></label>
    <div class="col-lg-7 treeordercat">
    {if $count == 0}
        <ol class="sortable">
    {else}
        <ol>
    {/if}
    
    {foreach $liste $value}
        {$count = $count + 1}
        <li id="list_{$value['id_prestablog_categorie']|escape:'html':'UTF-8'}">
        <div>
        <span class="disclose">
        <span></span>
        </span>
    
        {if file_exists("{$imgUpPath|escape:'html':'UTF-8'}/c/adminth_{$value['id_prestablog_categorie']|escape:'html':'UTF-8'}.jpg")}
            <img class="thumb" src="{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/up-img/c/adminth_{$value['id_prestablog_categorie']|escape:'html':'UTF-8'}.jpg?{$md5|escape:'html':'UTF-8'}" style="float:none;"/>
        {/if}
        {$value['title']}
        </div>
    
         {if count($value.children) > 0}
            {$prestablog->displayOrderTreeCategories($value.children, $count)}
         {/if}
        </li>
    {/foreach}
    </ol>

    </div>
</div>
{$prestablog->get_displayFormSubmit('submitOrderCat', 'icon-save', {l s='Update' d='Modules.Prestablog.Prestablog'})}
{$prestablog->get_displayFormClose()}