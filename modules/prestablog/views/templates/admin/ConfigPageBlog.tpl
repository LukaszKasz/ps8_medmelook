{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if Tools::getIsset('success')}
    {$prestablog->displayConfirmation("{l s='Settings updated successfully' d='Modules.Prestablog.Prestablog'}")}
{/if}

{$prestablog->get_displayFormOpen('blog.png', "{l s='Blog page configuration' d='Modules.Prestablog.Prestablog'}", $confpath)}

<div class="bootstrap">
    <div class="alert alert-info">
      <strong>{l s='Information' d='prestablog'}</strong><br/>
      <p>{l s='Use this link in your menu configuration:' d='Modules.Prestablog.Prestablog'}</p>
      <ul>
        {if Language::countActiveLanguages() > 1}
            {foreach $languages $language}
                {if (int) Configuration::get('prestablog_rewrite_actif')}
                    {if (int) Configuration::get('PS_REWRITING_SETTINGS')}
                        {if Configuration::get("{$prestablog->name}_urlblog") == false}
                            {assign "urlBlogPage" "{$urlRoot|escape:'html':'UTF-8'}{Language::getIsoById((int) $language['id_lang'])}/blog"}
                        {else}
                            {assign "urlBlogPage" "{$urlRoot|escape:'html':'UTF-8'}{Language::getIsoById((int) $language['id_lang'])}/{Configuration::get("{$prestablog->name}_urlblog")}"}
                        {/if}
                    {else}
                        {assign "urlBlogPage" "{$urlRoot|escape:'html':'UTF-8'}{$prestablog->ctrblog}&id_lang={(int) $language['id_lang']}"}
                    {/if}
                {else}
                    {if (int) Configuration::get('PS_REWRITING_SETTINGS')}
                        {assign "urlBlogPage" "{$urlRoot|escape:'html':'UTF-8'}{Language::getIsoById((int) $language['id_lang'])}"}
                        {assign "urlBlogPage" "{$urlBlogPage|escape:'html':'UTF-8'}/{$prestablog->ctrblog}"}
                    {else}
                        {assign "urlBlogPage" "{$urlRoot|escape:'html':'UTF-8'}{$prestablog->ctrblog}&id_lang={(int) $language['id_lang']}"}
                    {/if}
                {/if}
                <li><img src='../../img/l/{$language['id_lang']|escape:'html':'UTF-8'}.jpg' style='vertical-align:middle;' class='prestablogflag'><a href='{$urlBlogPage|escape:'html':'UTF-8'}' target='_blank'> {$urlBlogPage|escape:'html':'UTF-8'}</a></li>
            {/foreach}
        {else}
            {if ((int) Configuration::get('PS_REWRITING_SETTINGS')) && ((int) Configuration::get('prestablog_rewrite_actif'))}
                {if Configuration::get("{$prestablog->name}_urlblog") == false}
                    {assign "urlBlogPage" "{$urlRoot|escape:'html':'UTF-8'}blog"}
                {else}
                    {assign "urlBlogPage" "{$urlRoot|escape:'html':'UTF-8'}{Configuration::get("{$prestablog->name}_urlblog")}"}
                {/if}
            {else}
                {assign "urlBlogPage" "{$urlRoot|escape:'html':'UTF-8'}{$prestablog->ctrblog}"}
            {/if}
            <li><a href='{$urlBlogPage|escape:'html':'UTF-8'}' target='_blank'>{$urlBlogPage|escape:'html':'UTF-8'}</a></li>
        {/if}
      </ul>
    </div>
</div>
{$prestablog->get_displayFormEnableItemConfiguration('col-lg-2', "{l s='Slide on blogpage' d='Modules.Prestablog.Prestablog'}",
"{$prestablog->name}_pageslide_actif")}

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Title Meta' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            {assign "titleName" "{$prestablog->name}_titlepageblog"}
            {assign "titlePage" "{Configuration::get($titleName , (int)$lid)}"}
            <div id='meta_title_{$lid|escape:'html':'UTF-8'}'
                style='display: {if $lid == $prestablog->langue_default_store}block{else}none{/if};'>
                <input type='text' id='meta_title_{$lid|escape:'html':'UTF-8'}' name='meta_title_{$lid|escape:'html':'UTF-8'}' value='{$titlePage|escape:'html':'UTF-8'}' />
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1">
        {$prestablog->displayFlagsFor('meta_title', $divLangName)}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Description Meta' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            {assign "titleName" "{$prestablog->name}_descpageblog"}
            {assign "titlePage" "{Configuration::get($titleName , $lid)}"}
            <div id='meta_description_{$lid|escape:'html':'UTF-8'}'
                style='display: {if $lid == $prestablog->langue_default_store}block{else}none{/if};'>
                <input type='text' id='meta_description_{$lid|escape:'html':'UTF-8'}' name='meta_description_{$lid|escape:'html':'UTF-8'}' value='{$titlePage|escape:'html':'UTF-8'}' />
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1">
        {$prestablog->displayFlagsFor('meta_description', $divLangName)}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Title page H1' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            {assign "titleName" "{$prestablog->name}_h1pageblog"}
            {assign "titlePage" "{Configuration::get($titleName , $lid)}"}
            <div id='titre_h1_{$lid|escape:'html':'UTF-8'}'
                style='display: {if $lid == $prestablog->langue_default_store}block{else}none{/if};'>
                <input type='text' id='titre_h1_{$lid|escape:'html':'UTF-8'}' name='titre_h1_{$lid|escape:'html':'UTF-8'}' value='{$titlePage|escape:'html':'UTF-8'}' />
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1">
        {$prestablog->displayFlagsFor('titre_h1', $divLangName)}
    </div>
</div>
{$prestablog->get_displayFormSubmit('submitPageBlog', 'icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
{$prestablog->get_displayFormClose()}
{$prestablog->get_displayFormOpen('blog.png', "{l s='Blog URL configuration' d='Modules.Prestablog.Prestablog'}", $confpath)}

{assign "info" "{l s='Be careful, if you change the URL after being referenced, you\'ll lost all your work. Please use with caution' d='Modules.Prestablog.Prestablog'}
"}
{$prestablog->displayWarning($info)}

{if Configuration::get("{$prestablog->name}_urlblog") == false}
    {$prestablog->get_displayFormInput('col-lg-2', "{l s='Url of your blog' d='Modules.Prestablog.Prestablog'}", "{$prestablog->name}_urlblog",
    'blog', 10, 'col-lg-4' )}
{else}
    {$prestablog->get_displayFormInput('col-lg-2', "{l s='Url of your blog' d='Modules.Prestablog.Prestablog'}", "{$prestablog->name}_urlblog",
    Configuration::get("{$prestablog->name}_urlblog"), 10, 'col-lg-4' )}
{/if}

{$prestablog->get_displayFormSubmit('submitUrl', 'icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
{$prestablog->get_displayFormClose()}

{if !$prestaboost}

    <div class="col-md-12">
        {$prestablog->get_displayFormOpen('slide.png', "{l s='Popup prestablog' d='Modules.Prestablog.Prestablog'}", $confpath)}
        <a href="{$confpath|escape:'html':'UTF-8'}&class=PopupClass&displayContent"style="display: flex;">
        <span class="material-icons">add_box</span> {l s='Create a popup' d='Modules.Prestablog.Prestablog'}</a>

        {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='General activation of the popup' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_popup_general")}

        {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Popup on homepage' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_popuphome_actif",
        "{l s='The popup will be displayed in the blog\'s home page. For displaying the popup in articles or categories, please go directly to the creation of articles and categories' d='Modules.Prestablog.Prestablog'}")}

        {foreach PopupClass::getListePopup((int) $context->language->id, (int) $context->shop->id) $popup_link}
            {assign "popups_link[{$popup_link['id_prestablog_popup']}]" value=$popup_link['title']}
        {/foreach}

        {assign "popupLink" PopupClass::getIdPopupActifHome()}

        {if isset($popupLink[0])}
            {assign "popupLink" $popupLink[0]['id_prestablog_popup']}

            {$prestablog->get_displayFormSelect('col-lg-5', "{l s='Choose the popup to display :' d='Modules.Prestablog.Prestablog'}", 'popupLink',
            $popupLink, $popups_link, null, 'col-lg-5')}
        {else}
            {$prestablog->get_displayFormSelect('col-lg-5', "{l s='Choose the popup to display :' d='Modules.Prestablog.Prestablog'}", 'popupLink',
            $getP, $popups_link, null, 'col-lg-5')}
        {/if}

        {$prestablog->get_displayFormSubmit('submitPopupHome', 'icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
        {$prestablog->get_displayFormClose()}
    </div>
{/if}

<div class="col-md-12">
    {if not isset($id_lang)}
        {assign "id_lang" Configuration::get('PS_LANG_DEFAULT')}
    {/if}
    {if SubBlocksClass::getIdSbHome($id_lang) != 0}
        {assign "id_front" SubBlocksClass::getIdSbHome($id_lang)}
    {/if}
    {assign "div_lang_name" 'title'}

        <script type="text/javascript">
            id_language = Number({$dl|escape:'html':'UTF-8'});
{literal}
            function RetourLangueCheckUp(ArrayCheckedLang, idLangEnCheck, idLangDefaut) {
                if (ArrayCheckedLang.length > 0)
                    return ArrayCheckedLang[0];
                else
                    return idLangDefaut;
            }

            $(function() {
{/literal}
                {if isset($id_front)}
                    {if not Tools::getValue('languesup') && count($lln) == 1}
                        changeTheLanguage('title', '{$div_lang_name|escape:'html':'UTF-8'}', {(int) $lln[0]}, '');
                    {/if}
                {else}
                    {if Tools::getValue('languesup')}
                        {assign "acl" Tools::getValue('languesup')}
                    {/if}
                    {if count($acl) == 1}
                        changeTheLanguage('title', '{$div_lang_name|escape:'html':'UTF-8'}', {(int) $acl[0]}, '');
                    {else}
                        changeTheLanguage('title', '{$div_lang_name|escape:'html':'UTF-8'}', {(int) $dl|escape:'html':'UTF-8'}, '');
                    {/if}
                {/if}
{literal}
                $("input[name='languesup[]']").click(function() {
                    if (this.checked)
{/literal}
                        changeTheLanguage('title', '{$div_lang_name|escape:'html':'UTF-8'}', this.value, '');
{literal}
                    else
                    {
                        selectedL = new Array();
                        $("input[name='languesup[]']:checked").each(
                            function() {
                                selectedL.push($(this).val());
                            });
{/literal}
                        changeTheLanguage('title', '{$div_lang_name|escape:'html':'UTF-8'}', RetourLangueCheckUp(selectedL, this.value, {$dl|escape:'html':'UTF-8'}), '');
{literal}
                    }
                });

                $("#submitForm").click(function(event) {
                    test = 0;
                    $("input[name='languesup[]']:checked").each(function() {
                        test += 1;
                    });
                    if (test == 0)
                    {
                        $("input[name='languesup[]'][value={/literal}{$dl|escape:'html':'UTF-8'}{literal}]").prop("checked","true");
                    }
                });
            });

            function changeTheLanguage(title, divLangName, id_lang, iso) {
                $("#imgCatLang").attr("src", "../img/l/" + id_lang + ".jpg");
                return changeLanguage(title, divLangName, id_lang, iso);
            }
        </script>
{/literal}

{$prestablog->get_displayFormOpen('icon-edit', $legend_title, $confpath, 'formWithSelectLang')}
{if isset($id_front)}
    <input type="hidden" name="idSBF" value="{(int) $id_front|escape:'html':'UTF-8'}" />
    <input type="hidden" name="position" value="{(int) $sub_blocks->position|escape:'html':'UTF-8'}" />
{/if}
<div class="form-group">
    <label class="control-label col-lg-2">{l s='Language' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        <span id='check_lang_prestablog'>
        {if count($languages) != 1}
            <input type='checkbox' name='checkmelang' class='noborder' onclick="checkDelBoxes(this.form, 'languesup[]', this.checked)"> {l s='All' d='Modules.Prestablog.Prestablog'} | 
        {/if}
        {foreach $languages as $language}
            {assign "lid" "{(int)$language['id_lang']}"}
            <input type='checkbox' name='languesup[]' value='{$lid|escape:'html':'UTF-8'}'
            {if (SubBlocksClass::getIdSbHome($id_lang) && in_array((int) $lid, $lln)) || (Tools::getValue('languesup') && in_array((int) $lid, Tools::getValue('languesup')))}
                checked=checked
            {/if}
            {if count($languages) == 1}style='display:none' checked=checked {/if}>
            <img src='../img/l/{(int)$lid|escape:'html':'UTF-8'}.jpg' class='pointer indent-right prestablogflag' alt='{$language['name']|escape:'html':'UTF-8'}'
                title='{$language['name']|escape:'html':'UTF-8'}' onclick="changeTheLanguage('title', '{$div_lang_name|escape:'html':'UTF-8'}', {$lid|escape:'html':'UTF-8'}, '{$language['iso_code']|escape:'html':'UTF-8'}');">
        {/foreach}
        </span>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Title' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" "{(int)$language['id_lang']}"}
            <div id='title_{$lid}' style='display:{if $lid == $dl}block{else}none{/if};'>
                <input type='text' name='title_{$lid|escape:'html':'UTF-8'}' id='title_{$lid|escape:'html':'UTF-8'}'
                    value='{if isset($sub_blocks->title[$lid])}{$sub_blocks->title[$lid]|escape:'html':'UTF-8'}{/if}'>
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1">
        {$prestablog->displayFlagsFor('title', $div_lang_name)}
    </div>
</div>

    {$prestablog->get_displayFormSelect('col-lg-2', "{l s='List' d='Modules.Prestablog.Prestablog'}", 'select_type', $sub_blocks->select_type,
    $sub_blocks->getListeSelectType(), null, 'col-lg-5')}

    <div style="display: none;">

        {$prestablog->get_displayFormSelect('col-lg-2', "{l s='Hook' d='Modules.Prestablog.Prestablog'}", 'hook_name',
        "{if Tools::getValue('preselecthook')}{Tools::getValue('preselecthook')}{else}{$sub_blocks->hook_name}{/if}",
        $sub_blocks->getListeHook(), null, 'col-lg-5')}

    </div>

    {$prestablog->get_displayFormInput('col-lg-2', "{l s='Number of news to display' d='Modules.Prestablog.Prestablog'}", 'nb_list',
    $sub_blocks->nb_list, 10, 'col-lg-4','','','','PrestablogUintTextBox')}

    {$prestablog->get_displayFormInput('col-lg-2', "{l s='Title length' d='Modules.Prestablog.Prestablog'}", 'title_length',
    $sub_blocks->title_length, 10, 'col-lg-4', "{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}

    {$prestablog->get_displayFormInput('col-lg-2', "{l s='Description length' d='Modules.Prestablog.Prestablog'}", 'intro_length',
    $sub_blocks->intro_length, 10, 'col-lg-4', "{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}

{assign "randomTranslate" "{l s='Random list' d='Modules.Prestablog.Prestablog'}"}
{assign "optionTranslate" "{l s='This option will randomize your list.' d='Modules.Prestablog.Prestablog'}"}
{$prestablog->get_displayFormEnableItem('col-lg-2', $randomTranslate, 'random', $sub_blocks->random, $optionTranslate)}

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Categories' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-5">
        <div class='blocmodule'>
            <table cellspacing='0' cellpadding='0' class='table'>
                <thead>
                    <tr>
                        <th style='width:20px;'><input type='checkbox' name='checkme' class='noborder'
                                onclick="checkDelBoxes(this.form, 'categories[]', this.checked)"></th>
                        <th style='width:20px;'>{l s='ID' d='Modules.Prestablog.Prestablog'}</th>
                        <th style='width:60px;'>{l s='Image' d='Modules.Prestablog.Prestablog'}</th>
                        <th>{l s='Name' d='Modules.Prestablog.Prestablog'} &nbsp;<img id='imgCatLang' src='../img/l/{$dl|escape:'html':'UTF-8'}.jpg'
                                style='vertical-align:middle;' class='prestablogflag'></th>
                    </tr>
                </thead>
                {$prestablog->get_displayListeArborescenceCategoriesSubBlocks($liste_cat, 0, $liste_cat_branches_actives)}
            </table>
        </div>
        <script language='javascript' type='text/javascript'>
            $(document).ready(function() {
                $('.catlang').hide();
                $('.catlang[rel=' + id_language + ']').show();
                $('div.language_flags img, #check_lang_prestablog img').click(function() {
                    $('.catlang').hide();
                    $('.catlang[rel=' + id_language + ']').show();
                    $('#imgCatLang').attr('src', '../img/l/' + id_language + '.jpg');
                });

                {foreach $liste_cat_branches_actives as $value}
                    $('tr#prestablog_categorie_{$value}').show();
                {/foreach}

                {foreach $liste_cat_no_arbre as $value}
                    {if (in_array((int) $value['parent'], $liste_cat_branches_actives))}
                        $('tr#prestablog_categorie_{$value['id_prestablog_categorie']}').show();
                    {/if}
                {/foreach}

                $('img.expand-cat').click(function() {
                    BranchClick = $(this).attr('rel');
                    BranchClickSplit = BranchClick.split('.');
                        fixBranchClickSplit = '0,'
                        ' + BranchClickSplit.toString(); action = $(this).data(
                        'action');
                    path = $(this).data('path');

                    switch (action) {
                        case 'expand':
                            $('tr.prestablog_branch').each(function() {
                                    BranchParent = $(this).attr('rel');
                                    BranchParentSplit = BranchParent.split('.');
                                        fixBranchParentSplit = '0,'
                                        ' + BranchParentSplit.toString();

                                        if ($.isSubstring(fixBranchParentSplit, fixBranchClickSplit) &&
                                            BranchClick != BranchParent &&
                                            BranchClickSplit.length + 1 == BranchParentSplit.length) {
                                            $(this).show();
                                        }
                                    }); $(this).attr('src', path.concat('collapse.gif')); $(this).data(
                                    'action', 'collapse');
                                break;

                                case 'collapse':
                                $('tr.prestablog_branch').each(function() {
                                        BranchParent = $(this).attr('rel');
                                        BranchParentSplit = BranchParent.split('.');
                                            fixBranchParentSplit = '0,'
                                            ' + BranchParentSplit.toString();

                                            if ($.isSubstring(fixBranchParentSplit,
                                                    fixBranchClickSplit) &&
                                                BranchClick != BranchParent) {
                                                $(this).hide();
                                                $(this).find('img.expand-cat').each(function() {
                                                    $(this).attr('src', path.concat(
                                                        'expand.gif'));
                                                    $(this).data('action', 'expand');
                                                });
                                            }
                                        }); $(this).attr('src', path.concat('expand.gif')); $(this).data(
                                        'action', 'expand');
                                    break;
                                }
                            });
                });
                jQuery.isSubstring = function(haystack, needle) {
                    return haystack.indexOf(needle) !== -1;
                };
        </script>
    </div>
</div>


{$prestablog->get_displayFormEnableItem(
    'col-lg-2',
    "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
'actif',
$sub_blocks->actif
)}

<div class="margin-form">

    {if isset($id_front)}
        <button class="btn btn-primary" id="submitForm" name="submitUpdateSubBlockFront">
            <i class="icon-save"></i>&nbsp;{l s='Update' d='Modules.Prestablog.Prestablog'}
        </button>
    {else}
        <button class="btn btn-primary" id="submitForm" name="submitAddSubBlockFront">
            <i class="icon-plus"></i>&nbsp;{l s='Add' d='Modules.Prestablog.Prestablog'}
        </button>
    {/if}

</div>

{$prestablog->get_displayFormClose()}
</div>
<script type="text/javascript" src="../modules/prestablog/views/js/numbers.js"></script>
</div>
</div>