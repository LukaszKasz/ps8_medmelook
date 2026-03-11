{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}

<script type="text/javascript">
    id_language = Number({$dl});
{literal}
    function RetourLangueCheckUp(ArrayCheckedLang, idLangEnCheck, idLangDefaut) {
        if (ArrayCheckedLang.length > 0)
            return ArrayCheckedLang[0];
        else
            return idLangDefaut;
    }

    $(function() {
{/literal}
                {if Tools::getValue('idSB')}
                    {if !Tools::getValue('languesup') && count($lln) == 1}
                         {literal}changeTheLanguage('title', '{/literal}{$div_lang_name} {literal}', {(int) $lln[0]}, '');{/literal}
                    {/if}
                {else}
                    

                {if Tools::getValue('languesup')}
                    {assign "acl" "{Tools::getValue('languesup')}" }
                {/if}

                    {if count($acl) == 1}
                        {literal}changeTheLanguage('title', '{/literal}{$div_lang_name}{literal}', {/literal}{(int) $acl[0]}{literal}, '');{/literal}
                    {else}
                        {literal}changeTheLanguage('title', '{/literal}{$div_lang_name}{literal}', {/literal}{(int) $dl}{literal}, '');{/literal}
                    {/if}
                {/if}
                
            {literal}
                $("input[name='languesup[]']").click(function() {
                    if (this.checked) changeTheLanguage('title', '{/literal}{$div_lang_name}{literal}', this.value, '');
                    else
                    {
                      selectedL = new Array();
                      $("input[name='languesup[]']:checked").each(function() {selectedL.push($(this).val());});
                      changeTheLanguage('title', '{/literal}{$div_lang_name}{literal}',
                      RetourLangueCheckUp(selectedL, this.value, {/literal}{$dl}{literal}), '');
                    }
                });

                $("#submitForm").click(function(event) {
                  test = 0;
                  $("input[name='languesup[]']:checked").each(function() {
                    test += 1;
                  });
                  if (test == 0)
                  {
                    alert("{/literal}{l s='You must choose at least one language !' d='Modules.Prestablog.Prestablog'}");
                                {literal}
                                $("html, body").animate({scrollTop: $("#menu_config_prestablog").offset().top}, 300);
                                $("#check_lang_prestablog").css("background-color", "#FFA300");
                            return false;
                        } else return true;
					});
				});
              function changeTheLanguage(title, divLangName, id_lang, iso) {
                $("#imgCatLang").attr("src", "../img/l/" + id_lang + ".jpg");
                return changeLanguage(title, divLangName, id_lang, iso);
              }
							
            
</script>{/literal}


{$prestablog->get_displayFormOpen('icon-edit', $legend_title, $confpath, 'formWithSelectLang')}

{if Tools::getValue('idSB')}
<input type="hidden" name="idSB" value="{(int) Tools::getValue('idSB')}" />
<input type="hidden" name="position" value="{(int) $sub_blocks->position}" />
{/if}
<div class="form-group">
    <label class="control-label col-lg-2">{l s='Language' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        <span id='check_lang_prestablog'>
            {if count($languages) != 1}
                <input type='checkbox' name='checkmelang' class='noborder' onclick="checkDelBoxes(this.form, 'languesup[]', this.checked)">
                {l s='All' d='Modules.Prestablog.Prestablog'} |&nbsp;
            {/if}
            {foreach $languages as $language}
                {assign "lid" "{(int) $language['id_lang']}"}
                <input type='checkbox' name='languesup[]' value='{$lid}'
                {if (Tools::getValue('idSB') && in_array((int) $lid, $lln)) 
                || (Tools::getValue('languesup') && in_array((int) $lid, Tools::getValue('languesup')))}
                    checked=checked
                {/if}
                {if count($languages) == 1}style='display:none' checked=checked {/if} />
                <img src='../img/l/{(int)$lid|escape:'html':'UTF-8'}.jpg' class='pointer indent-right prestablogflag' alt='{$language['name']|escape:'html':'UTF-8'}' title='{$language['name']|escape:'html':'UTF-8'}'
                    onclick="changeTheLanguage('title', '{$div_lang_name|escape:'html':'UTF-8'}', {$lid|escape:'html':'UTF-8'}, '{$language['iso_code']|escape:'html':'UTF-8'}');">
            {/foreach}
        </span>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Title' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" "{(int)$language['id_lang']}"}
            <div id='title_{$lid|escape:'html':'UTF-8'}' style='display:{if $lid == $dl}block{else}none{/if};'>
                <input type='text' name='title_{$lid|escape:'html':'UTF-8'}' id='title_{$lid|escape:'html':'UTF-8'}' value='{if isset($sub_blocks->title[$lid])}{$sub_blocks->title[$lid]|escape:'html':'UTF-8'}{/if}' />
            </div>
        {/foreach}
    </div>
    <div class="col-lg-2">
        {$prestablog->displayFlagsFor('title', $div_lang_name)}
    </div>
</div>

{$prestablog->get_displayFormSelect('col-lg-2', "{l s='List' d='Modules.Prestablog.Prestablog'}", 'select_type', $sub_blocks->select_type, $sub_blocks->getListeSelectType(), null, 'col-lg-5')}

{if Tools::getValue('preselecthook')}
    {$prestablog->get_displayFormSelect('col-lg-2', "{l s='Hook' d='Modules.Prestablog.Prestablog'}", 'hook_name', Tools::getValue('preselecthook'), $sub_blocks->getListeHook(), null, 'col-lg-5')}
{else}
    {$prestablog->get_displayFormSelect('col-lg-2', "{l s='Hook' d='Modules.Prestablog.Prestablog'}", 'hook_name',$sub_blocks->hook_name, $sub_blocks->getListeHook(), null, 'col-lg-5')}
{/if}


{$prestablog->get_displayFormInput('col-lg-2', "{l s='Template' d='Modules.Prestablog.Prestablog'}", 'template', $sub_blocks->template,
60, 'col-lg-6', null, sprintf(
"{l s='Leave blank to use the default template %1$s' d='Modules.Prestablog.Prestablog'}",
"<strong>{$getT}_page-subblock.tpl</strong>"
))
}
{$prestablog->get_displayFormInput('col-lg-2', "{l s='Number of news to display' d='Modules.Prestablog.Prestablog'}", 'nb_list',
$sub_blocks->nb_list,
10, 'col-lg-4','','','','PrestablogUintTextBox')
}
{$prestablog->get_displayFormInput('col-lg-2', "{l s='Title length' d='Modules.Prestablog.Prestablog'}", 'title_length',
$sub_blocks->title_length,
10, 'col-lg-4', "{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
{$prestablog->get_displayFormInput('col-lg-2', "{l s='Description length' d='Modules.Prestablog.Prestablog'}", 'intro_length',
$sub_blocks->intro_length, 10, 'col-lg-4', "{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Use a period' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-6">
        <div class="blocmodule">
            {$prestablog->displayFormDateWithActivation('col-lg-1', {l s='From' d='Modules.Prestablog.Prestablog'}, 'date_start', $sub_blocks->date_start, true, 'use_date_start', $sub_blocks->use_date_stop)}
            {$prestablog->displayFormDateWithActivation('col-lg-1', {l s='To' d='Modules.Prestablog.Prestablog'}, 'date_stop', $sub_blocks->date_stop, true, 'use_date_stop', $sub_blocks->use_date_stop)}
        </div>
    </div>
</div>

{$prestablog->get_displayFormEnableItem('col-lg-2',"{l s='Random list' d='Modules.Prestablog.Prestablog'}" ,
    'random', $sub_blocks->random, "{l s='This option will randomize your list.' d='Modules.Prestablog.Prestablog'}")}
 

<div class='form-group'>
<label class='control-label col-lg-2'>{l s='Categories' d='Modules.Prestablog.Prestablog'} </label>
<div class='col-lg-5'>
                  
    <div class='blocmodule'>
        <table cellspacing='0' cellpadding='0' class='table'>
            <thead>
            <tr>
                <th style='width:20px;'>
                <input type='checkbox' name='checkme' class='noborder' onclick="{literal}checkDelBoxes(this.form, 'categories[]', this.checked){/literal}" />
                </th>
                <th style='width:20px;'>{l s='ID' d='Modules.Prestablog.Prestablog'}</th>
                <th style='width:60px;'>{l s='Image' d='Modules.Prestablog.Prestablog'}</th>
                <th>{l s='Name' d='Modules.Prestablog.Prestablog'}&nbsp;<img id='imgCatLang' src='../img/l/{$dl|escape:'html':'UTF-8'}.jpg' style='vertical-align:middle;' class="prestablogflag"></th>
            </tr>
            </thead>
                        
        {include file="{$smarty.current_dir|escape:'html':'UTF-8'}/Component/displayListeArborescenceCategoriesSubBlocks.tpl" listecat=$listecat decalage=0 listeidbranchdeploy=$liste_cat_branches_actives}                              
        </table>
    </div>
</div>
   {literal}
                  <script language="javascript" type="text/javascript">
                  $(document).ready(function() {
                  $(".catlang").hide();
                  $(".catlang[rel="+id_language+"]").show();
          
                  $("div.language_flags img, #check_lang_prestablog img").click(function() {
                      $(".catlang").hide();
                      $(".catlang[rel="+id_language+"]").show();
                      $("#imgCatLang").attr("src", "../img/l/" + id_language + ".jpg");
                      });
    {/literal}
                  {foreach $liste_cat_branches_actives $value}
                      $('tr#prestablog_categorie_{$value}').show();
                  {/foreach}
          
                  {foreach $liste_cat_no_arbre $value}
                  {if (in_array((int) $value['parent'], $liste_cat_branches_actives))}
                      $('tr#prestablog_categorie_{$value['id_prestablog_categorie']}').show();
                  {/if}
                  {/foreach}
              
                 
    {literal}
                  $('img.expand-cat').click(function()
                  {
                  BranchClick = $(this).attr('rel');
                  BranchClickSplit = BranchClick.split('.');
                  fixBranchClickSplit = "0,"+BranchClickSplit.toString();
                  action = $(this).data(
                  'action');
                  path = $(this).data('path');
          
                  switch (action) {
                  case 'expand':
                  $('tr.prestablog_branch').each(function()
                  {
                  BranchParent = $(this).attr('rel');
                  BranchParentSplit = BranchParent.split('.');
                  fixBranchParentSplit = "0,"+BranchParentSplit.toString();
          
                  if
                  ($.isSubstring(fixBranchParentSplit,fixBranchClickSplit) && BranchClick != BranchParent 
                    && BranchClickSplit.length + 1 == BranchParentSplit.length)
                  {
                  $(this).show();
                  }
                  }); $(this).attr('src',
                  path.concat('collapse.gif'));
                  $(this).data(
                  'action', 'collapse');
                  break;
          
                  case 'collapse':
                  $('tr.prestablog_branch').each(function()
                  {
                  BranchParent = $(this).attr('rel');
                  BranchParentSplit = BranchParent.split('.');
                  fixBranchParentSplit = "0,"+BranchParentSplit.toString();
          
                  if
                  ($.isSubstring(fixBranchParentSplit,
                  fixBranchClickSplit) &&
                  BranchClick != BranchParent) {
                  $(this).hide();
                  $(this).find('img.expand-cat').each(function()
                  {
                  $(this).attr('src', path.concat(
                  'expand.gif'));
                  $(this).data('action', 'expand');
                  });
                  }
                  }); $(this).attr('src',
                  path.concat('expand.gif'));
                  $(this).data(
                  'action', 'expand');
                  break;
                  }
                  });
                  });
                  jQuery.isSubstring =
                  function(haystack, needle) {
                  return haystack.indexOf(needle) !==
                  -1;
                  };
          
              </script>
            {/literal}
    </div>

    <div class="john" style="display:none;">
        {$prestablog->get_displayFormEnableItem(
        'col-lg-2',"{l s='Blog link' d='Modules.Prestablog.Prestablog'}",
        'blog_link', $sub_blocks->blog_link,
        "{l s='Show link to the blog' d='Modules.Prestablog.Prestablog'}")}
    </div>

    {$prestablog->get_displayFormEnableItem(
    'col-lg-2',"{l s='Activate' d='Modules.Prestablog.Prestablog'}",
    'actif',
    $sub_blocks->actif)}

    <div class="margin-form">

    {if Tools::getValue('idSB')}
        <button class="btn btn-primary" id="submitForm" name="submitUpdateSubBlock">
            <i class="icon-save"></i>&nbsp;{l s='Update' d='Modules.Prestablog.Prestablog'}
        </button>
    {else}
        <button class="btn btn-primary"id="submitForm" name="submitAddSubBlockFront">
            <i class="icon-plus"></i>&nbsp;{l s='Add' d='Modules.Prestablog.Prestablog'}
        </button>
    {/if}

    </div>
{$prestablog->get_displayFormClose()}
<script type="text/javascript" src="../modules/prestablog/views/js/numbers.js"></script>