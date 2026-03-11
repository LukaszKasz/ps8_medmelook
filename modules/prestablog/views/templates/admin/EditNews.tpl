{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if Tools::getIsset('success') && Tools::getIsset('editNews')} 
    {$prestablog->displayConfirmation("{l s='Settings updated successfully' d='Modules.Prestablog.Prestablog'}")}
{/if}

{assign "path_langs_iso_tinymce" "{_PS_ROOT_DIR_}/js/tiny_mce/langs/{$iso|escape:'html':'UTF-8'}.js"}
{$prestablog->loadJsForTiny()}

{if file_exists($path_langs_iso_tinymce)}
    {assign "iso_tiny_mce" $iso}
{else}
    {assign "iso_tiny_mce" "en"}
{/if}

{assign "allow_accents_js" "var PS_ALLOW_ACCENTED_CHARS_URL = 0;"}
{if Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')}
    {assign "allow_accents_js" "var PS_ALLOW_ACCENTED_CHARS_URL = 1;"}
{/if}


    <script type="text/javascript">

        {$allow_accents_js}

        var iso = '{$iso_tiny_mce|escape:'html':'UTF-8'}';
        var pathCSS = '{_THEME_CSS_DIR_|escape:'html':'UTF-8'}';
        var ad = '{$ad|escape:'html':'UTF-8'}';

        id_language = Number({$default_language|escape:'html':'UTF-8'});

    </script>
    {literal}
    <script type="text/javascript">
        function copy2friendlyURLPrestaBlog() {
            if ($('#slink_rewrite_'+id_language).is(':disabled') == false) {
                $('#slink_rewrite_'+id_language).val(str2url($('input#title_'+id_language).val().replace(/^[0-9]+\./, ''), 'UTF-8'));
            }
        }
    
        function updateFriendlyURLPrestaBlog() {
            $('#slink_rewrite_'+id_language).val(str2url($('#slink_rewrite_'+id_language).val().replace(/^[0-9]+\./, ''), 'UTF-8'));
        }

        function RetourLangueCheckUp(ArrayCheckedLang, idLangEnCheck, idLangDefaut) {
            if (ArrayCheckedLang.length > 0)
                return ArrayCheckedLang[0];
            else
                return idLangDefaut;
        }

        $(function() {
        {/literal}
                    {if Tools::getValue('idN')}
                        {if !Tools::getValue('languesup') && count($lang_liste_news) == 1}
                            changeTheLanguage('title', '{$div_lang_name}', {(int) $lang_liste_news[0]}, '');
                        {/if}
                    {else}
                        {if count($array_check_lang) == 1}
                            changeTheLanguage('title', '{$div_lang_name}', {(int) $array_check_lang[0]}, '');
                        {else}
                            changeTheLanguage('title', '{$div_lang_name}', {(int) $default_language}, '');
                        {/if}
                    {/if}

                    {literal}
                    $(".catlang").hide();
                    $(".catlang[rel="+id_language+"]").show();

                    $("div.language_flags img, #check_lang_prestablog img").click(function() {
                        $(".catlang").hide();
                        $(".catlang[rel="+id_language+"]").show();
                        $("#imgCatLang").attr("src", "../img/l/" + id_language + ".jpg");
                    });

                    $("input[name='languesup[]']").click(function() {
                        if (this.checked)
                            changeTheLanguage('title', '{/literal}{$div_lang_name|escape:'html':'UTF-8'}{literal}', this.value, '');
                        else {
                            var selectedL = new Array();
                            $("input[name='languesup[]']:checked").each(function() { selectedL.push($(this).val()); });
                            var retl = RetourLangueCheckUp(selectedL, this.value, '{/literal}{$dl|escape:'html':'UTF-8'}{literal}');
                            changeTheLanguage('title', '{/literal}{$div_lang_name|escape:'html':'UTF-8'}{literal}', retl, '');
                        }
                    });
                    
                    $("form[name=formWithSelectLang]").submit(function() {
                    {/literal}

                        {foreach $languages $language}
                        $('#slink_rewrite_{$language['id_lang']|escape:'html':'UTF-8'}').removeAttr("disabled");

                        {/foreach}

                        {literal}
                        selectedLangues = new Array();
                            $("input[name='languesup[]']:checked").each(function() {selectedLangues.push($(this).val());});


                            if (selectedLangues.length == 0) {
                        {/literal}
                                alert("{l s='You must choose at least one language !' d='Modules.Prestablog.Prestablog'}");
                                {literal}
                                $("html, body").animate({scrollTop: $("#menu_config_prestablog").offset().top}, 300);
                                $("#check_lang_prestablog").css("background-color", "#FFA300");
                            return false;
                        } else return true;
                    });


                    $("#prestablog_control").click(function() {
                        if ($('#slink_rewrite_'+id_language).is(':disabled') == true) {
                            $('#slink_rewrite_'+id_language).removeAttr("disabled");
                            $('#slink_rewrite_'+id_language).css("background-color", "#fff");
                            $('#slink_rewrite_'+id_language).css("color", "#000");
                            {/literal}
                            $(this).html("{l s='Disable this rewrite' d='Modules.Prestablog.Prestablog'}");
                            {literal}
                        }
                        else {
                            $('#slink_rewrite_'+id_language).attr("disabled", true);
                            $('#slink_rewrite_'+id_language).css("background-color", "#e0e0e0");
                            $('#slink_rewrite_'+id_language).css("color", "#7F7F7F");
                            {/literal}
                            $(this).html("{l s='Enable this rewrite' d='Modules.Prestablog.Prestablog'}");
                            {literal}
                        }
                    });
                    {/literal}
                    {foreach $languages $language}
                    {assign "lid" $language['id_lang']}
                    if ($("#slink_rewrite_{$lid|escape:'html':'UTF-8'}").val() == '') {
                        $("#slink_rewrite_{$lid|escape:'html':'UTF-8'}").removeAttr("disabled");
                        $("#slink_rewrite_{$lid|escape:'html':'UTF-8'}").css("background-color", "#fff");
                        $("#slink_rewrite_{$lid|escape:'html':'UTF-8'}").css("color", "#000");
                        $("#prestablog_control").html("{l s='Disable this rewrite' d='Modules.Prestablog.Prestablog'}");
                    }

                    $("#paragraph_{$lid|escape:'html':'UTF-8'}").keyup(function(){
                        var limit = parseInt($(this).attr("maxlength"));
                        var text = $(this).val();
                        var chars = text.length;
                        if (chars > limit) {
                            var new_text = text.substr(0, limit);
                            $(this).val(new_text);
                        }
                        $("#compteur-texte-{$lid|escape:'html':'UTF-8'}").html(chars+" / "+limit);
                    });
                    {/foreach}
                    {literal}
                        $("#productLinkSearch").bind("keyup", function() {
                            ReloadLinkedSearchProducts();
                            }); 
                        $(document).ready(function() {
                            ReloadLinkedProducts();
                        }); 
                        $("#articleLinkSearch").bind("keyup click focusin", function() {
                            ReloadLinkedSearchArticles();
                        }); 
                        $(document).ready(function() {
                            ReloadLinkedArticles();
                        });
                });

                function ReloadLinkedSearchProducts(start) {
                    var listLinkedProducts = '';
                    $("input[name^=productsLink]").each(function() {
                    listLinkedProducts += $(this).val() + ";";
                    });

                if ($("#productLinkSearch").val() != '' && $("#productLinkSearch").val().length >=
                {/literal}
                    {(int) Configuration::get("{$prestablog->name}_nb_car_min_linkprod")|escape:'html':'UTF-8'}
                {literal}
                    ) {
                        $.ajax({
                            {/literal}
                                url: '{$context->link->getAdminLink('AdminPrestaBlogAjax')}',
                             {literal}
                                type: "GET",
                                data: {
                                    ajax: true,
                                    action: 'prestablogrun',
                                    do :'searchProducts',
                                    listLinkedProducts: listLinkedProducts,
                                    start: start,
                                    req: $("#productLinkSearch").val(),
                            {/literal}
                                    id_shop: '{$context->shop->id}'
                                    },
                            {literal}
                                success: function(data) {
                                    $("#productLinkResult").empty();
                                    $("#productLinkResult").append(data);
                                }
                            });
                        } else {
                                $("#productLinkResult").empty();
                            {/literal}
                                $("#productLinkResult").append('<tr><td colspan="4" class="center">{$resultprodjs}</td></tr>');
                {literal}
                                }
                }

                function ReloadLinkedSearchArticles(start) {
                    var listLinkedArticles = '';
                    $("input[name^=articlesLink]").each(function() {
                    listLinkedArticles += $(this).val() + ";";
                    });

                    if ($("#articleLinkSearch").val() != '' && $("#articleLinkSearch").val().length >=
                {/literal}
                    {(int) Configuration::get("{$prestablog->name}_nb_car_min_linknews")}) {
                {literal}
                    $.ajax({
                {/literal}
                        url: '{$context->link->getAdminLink('AdminPrestaBlogAjax')}',
                        {literal}
                        type: "GET",
                        data: {
                            ajax: true,
                            action: 'prestablogrun',
                            do :'searchArticles',
                            listLinkedArticles: listLinkedArticles,
                            start: start,
                            req: $("#articleLinkSearch").val(),
                        {/literal}
                            id_shop: '{$context->shop->id}'
                        },
                        {literal}
                        success: function(data) {
                            $("#articleLinkResult").empty();
                            $("#articleLinkResult").append(data);
                        }
                    });
                }
                else {
                    $("#articleLinkResult").empty();
                {/literal}
                        $("#articleLinkResult").append('<tr><td colspan="4" class="center">{$resultarticlejs|escape:'html':'UTF-8'}</td></tr>');
            {literal}
                    }
                }

            function ReloadLinkedProducts() {
                var req = '';
                $("input[name^=productsLink]").each(function() {
                    req += $(this).val() + ";";
                });
                $.ajax({
            {/literal}
                    url: '{$context->link->getAdminLink('AdminPrestaBlogAjax')}',
                    {literal}
                    type: "GET",
                    data: {
                        ajax: true,
                        action: 'prestablogrun',
                        do :'loadProductsLink',
                        req: req,
{/literal}
                        id_shop: '{$context->shop->id}'
                    },
                    {literal}
                    success: function(data) {
                        $("#productLinked").empty();
                        $("#productLinked").append(data);
                    }
                });
            }

            function ReloadLinkedArticles() {
                var req = '';
                $("input[name^=articlesLink]").each(function() {
                    req += $(this).val() + ";";
                });
                $.ajax({
            {/literal}
                    url: '{$context->link->getAdminLink('AdminPrestaBlogAjax')}',
                    {literal}
                    type: "GET",
                    data: {
                        ajax: true,
                        action: 'prestablogrun',
                        do :'loadArticlesLink',
                        req: req,
                        {/literal}
                        id_shop: '{$context->shop->id}'
                    },
                    {literal}
                    success: function(data) {
                        $("#articleLinked").empty();
                        $("#articleLinked").append(data);
                    }
                });
            }

            function changeTheLanguage(title,divLangName,id_lang,iso) {
                $("#imgCatLang").attr("src", "../img/l/" + id_lang + ".jpg");
                return changeLanguage(title,divLangName,id_lang,iso);
            }
    </script>
    {/literal}

{$prestablog->get_displayFormOpen('icon-edit', $legend_title, $confpath, 'formWithSelectLang')}

{if Tools::getValue('idN')}
    <input type="hidden" name="idN" value="{Tools::getValue('idN')|escape:'html':'UTF-8'}" />

    <div class="form-group">
        <label class="control-label col-lg-2">{l s='Preview' d='Modules.Prestablog.Prestablog'}</label>
        <div class="col-lg-7">
            {foreach $lang_liste_news $val_langue}
                {if count($languages) >= 1 && array_key_exists((int) $val_langue, $languages_shop)}
                    <a target='_blank' href='{$prestablogurl[$val_langue]|escape:'html':'UTF-8'}{$accurl|escape:'html':'UTF-8'}preview={$prestablog->generateToken((int) $news->id)|escape:'html':'UTF-8'}'
                    class='indent-right'>
                    <img src='../img/l/{(int) $val_langue|escape:'html':'UTF-8'}.jpg' class='prestablogflag'>
                    <img src='{$imgPathFO|escape:'html':'UTF-8'}preview.gif' />
                    </a>
                {/if}
            {/foreach}
        </div>
    </div>
{/if}

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Language' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {$html_language}
    </div>
</div>
    {if $chatgpt_api_key}
    {$prestablog->displayChatGPT()}
    {/if}

<div class="form-group">
    <label class="control-label col-lg-2">Main title</label>
    <div class="col-lg-7">
        {foreach $languages $language}
            {assign "lid" "{(int) $language['id_lang']}"}
            <div id="title_{$lid}" style="display: {if $lid == 1}block{else}none{/if};">
                <input id="title_{$lid}" type="text" name="title_{$lid}" maxlength="{(int) Configuration::get('prestablog_news_title_length')|escape:'html':'UTF-8'}"
                value="{if isset($news->title[$lid])}{$news->title[$lid]|escape:'htmlall':'UTF-8'}{/if}"
                onkeyup="if (isArrowKey(event)) return; copy2friendlyURLPrestaBlog();" onchange="copy2friendlyURLPrestaBlog();">
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1">
        {$prestablog->displayFlagsFor('title', $div_lang_name)}
    </div>
</div>
{if !$permissionActivate}<div style="display: none">{/if}
    {$prestablog->get_displayFormEnableItem('col-lg-2', {l s='Activate' d='Modules.Prestablog.Prestablog'}, 'actif', $news->actif)}
{if !$permissionActivate}</div>{/if}

{if $layerslider}
    {$prestablog->get_displayInfo("{l s='In order to display the slides from Creative Slider anywhere you want in the article, please paste the shortcode of the slide you want from the Creative Slider module in your content.' d='Modules.Prestablog.Prestablog'}")}
{/if}

{if !isset($commentFbActive) or !$commentFbActive}
    {if Tools::getValue('idN')}
        <div class="form-group">
            <label class="control-label col-lg-2">{l s='Comments' d='Modules.Prestablog.Prestablog'}</label>
            <div class="col-lg-10" style="line-height: 35px;">
                {if count($comments_all) > 0}
                    <div id="labelComments">
                        <strong>{count($comments_actif)|escape:'html':'UTF-8'}</strong>
                        {l s='approuved' d='Modules.Prestablog.Prestablog'} {l s='of' d='Modules.Prestablog.Prestablog'}
                        <strong>{count($comments_all)|escape:'html':'UTF-8'}</strong>
    
                        {if count($comments_non_lu) > 0}
                            &nbsp;&mdash;-&nbsp;
                            <span style='color:green;font-weight:bold;'>{count($comments_non_lu)|escape:'html':'UTF-8'}{l s='Comments pending' d='Modules.Prestablog.Prestablog'}</span>
                        {/if}
    
                        <span onclick="$('#comments').slideToggle();" style="cursor: pointer" class="link">
                            {l s='Click here to manage comments' d='Modules.Prestablog.Prestablog'}
                        </span>
                    </div>
                {else}
                    <div id="labelComments">
                        {l s='No comment' d='Modules.Prestablog.Prestablog'}
    
                        {if count($comments_non_lu) > 0}
                            &nbsp;&mdash;-&nbsp;
                            <span style='color:green;font-weight:bold;'>{count($comments_non_lu)|escape:'html':'UTF-8'}{l s='Comments pending' d='Modules.Prestablog.Prestablog'}</span>
                        {/if}
                    </div>
                {/if}
            </div>
        </div>

        {if count($comments_all) > 0}        
        <div class="form-group">
        <label class="control-label col-lg-2"></label>
        <div class="col-lg-10">


            {if Tools::isSubmit('showComments')}
                <div id='comments'>
                <script type='text/javascript'>
                        $(document).ready(function() $('html, body').animate(scrollTop: $('#labelComments').offset().top, 750););
                    </script>
                {else}
                <div id='comments'
                {if Configuration::get("{$prestablog->name}_comment_div_visible")}
                    style=''
                {else}
                    style='display: none;'
                {/if}
                </div>
                {/if}
                    <div class='blocs col-sm-4'>
                    <h3>
                        <i class='material-icons roundcomment' style='color: #e77000;'>help_outline</i>   
                        {count($comments_non_lu)|escape:'html':'UTF-8'}&nbsp;{l s='Comments pending' d='Modules.Prestablog.Prestablog'}
                    </h3>
                    {if count($comments_non_lu) > 0}
                        <div class='wrap'>
                        {foreach $comments_non_lu $value_c}
                            {assign "ur" "&idN={Tools::getValue('idN')}&idC={$value_c['id_prestablog_commentnews']}"}
                            <h4>
                            {$datesCommentUnread[$value_c['id_prestablog_commentnews']]|escape:'html':'UTF-8'}<br />{l s='by' d='Modules.Prestablog.Prestablog'}
                            <strong>{$value_c['name']|escape:'html':'UTF-8'}</strong>
                            </h4>
                            <p>{$value_c['comment']}</p>
                            <p class='center' style='margin-top:10px;'>
                            <a href='{$confpath|escape:'html':'UTF-8'}&editComment&idC={$value_c['id_prestablog_commentnews']|escape:'html':'UTF-8'}'
                            class='hrefComment' title='{l s='Edit' d='Modules.Prestablog.Prestablog'}'>
                            <i class='material-icons' style='color: #6c868e;'>mode_edit</i>
                            </a>
                            <a href='{$confpath|escape:'html':'UTF-8'}&deleteComment{$ur|escape:'html':'UTF-8'}' class='hrefComment' 
                            onclick=\"return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');\" title='{l s='Delete' d='Modules.Prestablog.Prestablog'}'>
                                <i class='material-icons' style='color: #c05c67;'>delete</i>
                            </a>
                            <a href='{$confpath|escape:'html':'UTF-8'}&enabledComment{$ur|escape:'html':'UTF-8'}' class='hrefComment' title='{l s='Approuved' d='Modules.Prestablog.Prestablog'}'>
                            <i class='material-icons' style='color: #37bd54;'>check</i>
                            </a>
                            <a href='{$confpath|escape:'html':'UTF-8'}&disabledComment{$ur|escape:'html':'UTF-8'}' class='hrefComment' title='{l s='Disabled' d='Modules.Prestablog.Prestablog'}'>
                            <i class='material-icons' style='color: #b33b3b;'>close</i>
                            </a>
                            </p>
                        {/foreach}
                        </div>
                    {/if}
                    </div>
                    <div class='blocs col-sm-4'>
                    <h3>
                        <i class='material-icons roundcomment' style='color: #37bd54;'>check</i>                  
                        {count($comments_actif)|escape:'html':'UTF-8'}&nbsp;{l s='Comments approuved' d='Modules.Prestablog.Prestablog'}</h3>
                    {if isset($comments_all) && $comments_all != null && count($comments_all) > 0}
                        <div class='wrap'>
                        {foreach $comments_actif as $value_c}
                            {assign "ur" "&idN={Tools::getValue('idN')}&idC={$value_c['id_prestablog_commentnews']}"}
                            <h4>
                            {l s='by' d='Modules.Prestablog.Prestablog'}
                            <strong>{$value_c['name']|escape:'html':'UTF-8'}</strong>
                            </h4>
                            <p>{$value_c['comment']}test</p>
                            <p class='center' style='margin-top:10px;'>
                            <a href='{$confpath|escape:'html':'UTF-8'}&editComment&idC={$value_c['id_prestablog_commentnews']|escape:'html':'UTF-8'}'
                            class='hrefComment' title='{l s='Edit' d='Modules.Prestablog.Prestablog'}'>
                            <i class='material-icons' style='color: #6c868e;'>mode_edit</i>
                            </a>
                            <a href='{$confpath|escape:'html':'UTF-8'}&deleteComment{$ur|escape:'html':'UTF-8'}' class='hrefComment' 
                            onclick=\"return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');\" title='{l s='Delete' d='Modules.Prestablog.Prestablog'}'>
                                <i class='material-icons' style='color: #c05c67;'>delete</i>
                            </a>
                            <a href='{$confpath|escape:'html':'UTF-8'}&disabledComment{$ur|escape:'html':'UTF-8'}' class='hrefComment' title='{l s='Disabled' d='Modules.Prestablog.Prestablog'}'>
                            <i class='material-icons' style='color: #b33b3b;'>close</i>
                            </a>
                            </p>
                        {/foreach}
                        </div>
                    {/if}
                    </div>
                    <div class='blocs col-sm-3'>
                    <h3>
                        <i class='material-icons roundcomment' style='color: #b33b3b;'>close</i>
                        {count($comments_disabled)|escape:'html':'UTF-8'}&nbsp;{l s='Comments disabled' d='Modules.Prestablog.Prestablog'}
                    </h3>
                    {if count($comments_disabled) > 0}
                        <div class='wrap'>
                        {foreach $comments_disabled $value_c}
                            {assign "ur" "&idN={Tools::getValue('idN')}&idC={$value_c['id_prestablog_commentnews']}"}
                            <h4>
                            {l s='by' d='Modules.Prestablog.Prestablog'}
                            <strong>{$value_c['name']|escape:'html':'UTF-8'}</strong>
                            </h4>
                            <p>{$value_c['comment']}test</p>
                            <p class='center' style='margin-top:10px;'>
                            <a href='{$confpath|escape:'html':'UTF-8'}&editComment&idC={$value_c['id_prestablog_commentnews']|escape:'html':'UTF-8'}'
                            class='hrefComment' title='{l s='Edit' d='Modules.Prestablog.Prestablog'}'>
                            <i class='material-icons' style='color: #6c868e;'>mode_edit</i>
                            </a>
                            <a href='{$confpath|escape:'html':'UTF-8'}&deleteComment{$ur|escape:'html':'UTF-8'}' class='hrefComment' 
                            onclick=\"return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');\" title='{l s='Delete' d='Modules.Prestablog.Prestablog'}'>
                                <i class='material-icons' style='color: #c05c67;'>delete</i>
                            </a>
                            <a href='{$confpath|escape:'html':'UTF-8'}&enabledComment{$ur|escape:'html':'UTF-8'}' class='hrefComment' title='{l s='Approuved' d='Modules.Prestablog.Prestablog'}'>
                            <i class='material-icons' style='color: #37bd54;'>check</i>
                            </a>
                            </p>
                        {/foreach}
                        </div>
                    {/if}
                    </div>
                </div>
                <div class='clear'></div>

            </div>
        </div>
            {/if}       
        {/if}
    {/if}
<div class="form-group">
  <label class="control-label col-lg-2">{l s='SEO' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-7" style="line-height: 35px;">
    <span onclick="$('#seo').slideToggle();" style="cursor: pointer; display: flex;" class="link">
    <i class="material-icons" style="line-height: 35px;">settings</i>
    {l s='Click here to improve SEO' d='Modules.Prestablog.Prestablog'}
    </span>
  </div>
</div>
<div id='seo' style='display: none;'>

<div class="form-group">
    <label class="control-label col-lg-2">
        {l s='Url Rewrite' d='Modules.Prestablog.Prestablog'}
        <br />
        <a href='#' id='prestablog_control'>
            {if isset($news->id)}
                {l s='Enable this rewrite' d='Modules.Prestablog.Prestablog'}
            {else}
                {l s='Disable this rewrite' d='Modules.Prestablog.Prestablog'}
            {/if}
        </a>
    </label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            {$linkRewriteTab[$lid]}
        {/foreach}
    </div>
    <div class="col-lg-1">
        {$prestablog->displayFlagsFor('link_rewrite', $div_lang_name)}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Meta Title' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            <div id='meta_title_{$lid|escape:'html':'UTF-8'}' style='display: {if $lid == $default_language} block {else} none{/if};'>
                <input type='text' name='meta_title_{$lid|escape:'html':'UTF-8'}' id='meta_title_{$lid|escape:'html':'UTF-8'}' value="{if isset($news->meta_title[$lid])}{$news->meta_title[$lid]|escape:'html':'UTF-8'}{/if}" maxlength="250">
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1">
        {$prestablog->displayFlagsFor('meta_title', $div_lang_name)}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Meta Description' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            <div id='meta_description_{$lid|escape:'html':'UTF-8'}' style='display: {if $lid == $default_language} block {else} none{/if};'>
                <input type='text' name='meta_description_{$lid|escape:'html':'UTF-8'}' id='meta_description_{$lid|escape:'html':'UTF-8'}' value="{if isset($news->meta_description[$lid])}{$news->meta_description[$lid]|escape:'html':'UTF-8'}{/if}" maxlength="250">
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1">
        {$prestablog->displayFlagsFor('meta_description', $div_lang_name)}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Meta Keywords' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            <div id='meta_keywords_{$lid|escape:'html':'UTF-8'}' style='display: {if $lid == $default_language} block {else} none{/if};'>
                <input type='text' name='meta_keywords_{$lid|escape:'html':'UTF-8'}' id='meta_keywords_{$lid|escape:'html':'UTF-8'}' value="{if isset($news->meta_keywords[$lid])}{$news->meta_keywords[$lid]|escape:'html':'UTF-8'}{/if}" maxlength="250">
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1">
        {$prestablog->displayFlagsFor('meta_keywords', $div_lang_name)}
    </div>
</div>

    {$prestablog->get_displayFormInput('col-lg-2', "{l s='Permanent redirect url' d='Modules.Prestablog.Prestablog'}",
    'url_redirect', $news->url_redirect, null, 'col-lg-7',
    "{l s='Advanced user only' d='Modules.Prestablog.Prestablog'}", "{$permanentUrlRedirect}", '<i class="icon-external-link"></i>')}
</div>

{if $demo_mode}
    <div class='bootstrap'>
        <div class='alert alert-warning'>
            <button class='close' data-dismiss='alert' type='button'>×</button>
            <strong>{l s='Warning' d='Modules.Prestablog.Prestablog'}</strong><br/>
            {l s='Feature disabled on the demo mode' d='Modules.Prestablog.Prestablog'}
        </div>
    </div>
{/if}

{if Tools::getValue('idN') && file_exists("{$imgUpPath|escape:'html':'UTF-8'}/admincrop_{Tools::getValue('idN')}.jpg")}
    <span id='labelPicture'></span>
    {assign "config_theme_array" PrestaBlog::objectToArray($config_theme)}
    {if Tools::getValue('pfx')}
        {literal}
            <script type="text/javascript">
                $(document).ready(function() {
                    $('html, body').animate({scrollTop: $('#labelPicture').offset().top}, 750);
                });
            </script>
        {/literal}
    {/if}

<script src='{__PS_BASE_URI__|escape:'html':'UTF-8'}modules/prestablog/views/js/jquery.Jcrop.prestablog.js'></script>
<link rel='stylesheet' href='{__PS_BASE_URI__|escape:'html':'UTF-8'}modules/prestablog/views/css/jquery.Jcrop.css' type='text/css' />

{literal}
    <script language='Javascript'>
        var ratioValue = new Array();
    {/literal}
    
    {foreach $config_theme_array['images'] as $key_theme_array => $value_theme_array}
    {literal}ratioValue['{/literal}{$key_theme_array}{literal}'] = {/literal}{(int) $value_theme_array['width'] / (int) $value_theme_array['height']}{literal};{/literal}
    {/foreach}    
    
    {literal}
   
        var monRatio;
        var monImage;
        $(function() {
            $('div.togglePreview').hide();
    {/literal}
    
    {if Tools::getValue('pfx')}
        {literal}
            $('input[name$=\'imageChoix\']').filter('[value=\'{/literal}{Tools::getValue('pfx')}{literal}\']').attr('checked', true);
            $('input[name$=\'imageChoix\']').filter('[value=\'{/literal}{Tools::getValue('pfx')}{literal}\']').parent().next(1).slideDown();
            $('#pfx').val('{/literal}{Tools::getValue('pfx')}{literal}');
            $('#ratio').val(ratioValue['{/literal}{Tools::getValue('pfx')}{literal}']);
            monRatio = ratioValue['{/literal}{Tools::getValue('pfx')}{literal}'];
            $('#cropbox').Jcrop({ aspectRatio: monRatio,onSelect: updateCoords });
            nomImage = '{/literal}{l s='Resize' d='Modules.Prestablog.Prestablog'} {Tools::getValue('pfx')}{literal}';
        {/literal}
        {if $isPSVersionValid} 
            {literal}$('#resizeText').html(nomImage);{/literal} 
        {else} 
            {literal}$('#resizeBouton').val(nomImage);{/literal} 
        {/if}
    {/if}
    
    {literal}
            $('input[name$=\'imageChoix\']').change(function () {
                $('div.togglePreview').slideUp();
                $(this).parent().next().slideDown();
                $('#pfx').val($(this).val());
                $('#ratio').val(ratioValue[$(this).val()]);
                monRatio = ratioValue[$(this).val()];
                $('#cropbox').Jcrop({ aspectRatio: monRatio,onSelect: updateCoords });
                nomImage = '{/literal}{l s='Resize' d='Modules.Prestablog.Prestablog'} {literal}'+$('#pfx').val();
    {/literal}
    {if $isPSVersionValid} 
        {literal}$('#resizeText').html(nomImage);{/literal} 
    {else} 
        {literal}$('#resizeBouton').val(nomImage);{/literal} 
    {/if}
    {literal}
            });
        });
    
        function updateCoords(c) {
            $('#x').val(c.x);
            $('#y').val(c.y);
            $('#w').val(c.w);
            $('#h').val(c.h);
        };
    
        function checkCoords() {
    
            if (!$('input[name=\'imageChoix\']:checked').val()) {
                alert('{/literal}{l s='Please select a picture to crop.' d='Modules.Prestablog.Prestablog'}{literal}');
                return false;
            }
            else {
                if (parseInt($('#w').val()))
                    return true;
                alert('{/literal}{l s='Please select a crop region then press submit.' d='Modules.Prestablog.Prestablog'}{literal}');
                return false;
            }
        };
    </script>
    {/literal}

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Main picture' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-10">
        <div id='image' class='col-md-7'>
            <div class='blocmodule'>
                <img id='cropbox' src='{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/up-img/admincrop_{Tools::getValue('idN')|escape:'html':'UTF-8'}.jpg?{$md5|escape:'html':'UTF-8'}' />
                <p align='center'>
                    {l s='Filesize' d='Modules.Prestablog.Prestablog'}
                    {filesize("{$imgPath|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/up-img/{Tools::getValue('idN')|escape:'html':'UTF-8'}.jpg") / 1000}kb
                </p>
                <p>
                    <a href='{$confpath|escape:'html':'UTF-8'}&deleteImageBlog&idN={Tools::getValue('idN')|escape:'html':'UTF-8'}' 
                    onclick=\"return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');\">
                        <img src='{$imgPathFO|escape:'html':'UTF-8'}delete.gif' alt='{l s='Delete' d='Modules.Prestablog.Prestablog'}' />
                        {l s='Delete' d='Modules.Prestablog.Prestablog'}
                    </a>
                </p>
                <p>{$prestablog->displayFormFileNoLabel('homepage_logo', 'col-lg-10', "{l s='Format:' d='Modules.Prestablog.Prestablog'}.jpg.png")}</p>
            </div>
        </div>
        <div class='col-md-5'>
            {foreach $config_theme_array['images'] as $key_theme_array => $value_theme_array}
                {assign "label_pic" $key_theme_array}
                {if $key_theme_array == 'thumb'}
                    {assign "label_pic" "{l s='thumb for articles list' d='Modules.Prestablog.Prestablog'}"}
                {elseif $key_theme_array == 'slide'}
                    {assign "label_pic" "{l s='slide picture (home / blog page)' d='Modules.Prestablog.Prestablog'}"}
                {/if}
                {if $key_theme_array != 'slide'}
                    <div class='blocmodule'>
                        <p><input type='radio' name='imageChoix' value='{$key_theme_array|escape:'html':'UTF-8'}' />&nbsp;{$label_pic|escape:'html':'UTF-8'}<span style='font-size: 80%;'> ({$value_theme_array['width']|escape:'html':'UTF-8'} * {$value_theme_array['height']|escape:'html':'UTF-8'})</span></p>
                        <div class='togglePreview' style='text-align:center;'>
                            <img style='border:1px solid #4D4D4D;padding:0px;max-width:100%' src='{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/up-img/{$key_theme_array|escape:'html':'UTF-8'}_{Tools::getValue('idN')|escape:'html':'UTF-8'}.jpg?{$md5|escape:'html':'UTF-8'}'>
                        </div>
                    </div>
                {/if}
            {/foreach}
            <div class="blocmodule">
                <a class="btn btn-default" onclick="if (checkCoords()) { formCrop.submit(); }">
                <i class="icon-crop"></i>&nbsp;<span id="resizeText">{l s='Resize' d='Modules.Prestablog.Prestablog'}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{else}
    <div class="form-group">
        <label class="control-label col-lg-2">{l s='Main picture' d='Modules.Prestablog.Prestablog'}</label>
        <div class="col-lg-10">
            {$prestablog->displayFormFileNoLabel('homepage_logo', 'col-lg-5', "{l s='Format:' d='Modules.Prestablog.Prestablog'}.jpg.png")}
        </div>
    </div>
{/if}

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Introduction' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            <div id='cpara1_{$lid}' style='{if $lid == $default_language}display: block;{else}display: none;{/if}'>
                <textarea maxlength='{(int) Configuration::get('prestablog_news_intro_length')}' id='paragraph_{$lid}'
                    name='paragraph_{$lid}'>{if isset($news->paragraph[$lid])}{$news->paragraph[$lid]}{/if}</textarea>
                <p>
                    {l s='Caracters remaining' d='Modules.Prestablog.Prestablog'} :
                    <span id='compteur-texte-{$lid}' style='color:red;'>
                        {if isset($news->paragraph[$lid])}{Tools::strlen($news->paragraph[$lid])}{else}0{/if} /
                        {(int) Configuration::get('prestablog_news_intro_length')}
                    </span>
                    <br />
                    {l s='Configure the max length in the general configuration of the module theme.' d='Modules.Prestablog.Prestablog'}
                </p>
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1"> 
        {$prestablog->displayFlagsFor('cpara1', $div_lang_name)}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Content' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            <div id='cpara2_{$lid}' style='{if $lid == $default_language}display: block;{else}display: none;{/if}'>
                <textarea class='rte autoload_rte' id='content_{$lid}' name='content_{$lid}'>{if isset($news->content[$lid])}{$news->content[$lid]}{/if}</textarea>
            </div>
        {/foreach}
    </div>
    <div class="col-lg-1"> 
        {$prestablog->displayFlagsFor('cpara2', $div_lang_name)}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Categories' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-5">
        <div class="blocmodule">
            <table cellspacing='0' cellpadding='0' class='table'>
                <thead>
                    <tr>
                        <th style='width:20px;'>
                            <input type='checkbox' name='checkme' class='noborder' onclick="checkDelBoxes(this.form, 'categories[]', this.checked)">
                        </th>
                        <th style='width:20px;'>{l s='ID' d='Modules.Prestablog.Prestablog'}</th>
                        <th style='width:60px;'>{l s='Image' d='Modules.Prestablog.Prestablog'}</th>
                        <th>
                            {l s='Name' d='Modules.Prestablog.Prestablog'}
                            <img id='imgCatLang' src='../img/l/{$default_language|escape:'html':'UTF-8'}.jpg' style='vertical-align:middle;' class='prestablogflag'>
                        </th>
                    </tr>
                </thead>
                {$prestablog->displayListeArborescenceCategoriesNews($liste_cat, 0, $liste_cat_branches_actives)}
            </table>
        </div>
        <script language='javascript' type='text/javascript'>$(document).ready(function() {
            $(document).ready(function() {
                    {foreach $liste_cat_branches_actives as $value}
                        $('tr#prestablog_categorie_{$value}').show();
                    {/foreach}

                    {foreach $liste_cat_no_arbre as $value}
                        {if in_array((int) $value['parent'], $liste_cat_branches_actives)}
                            $('tr#prestablog_categorie_{$value['id_prestablog_categorie']}').show();
                        {/if}
                    {/foreach}     
                    });
         
         {literal}
        $('img.expand-cat').click(function() {

            BranchClick=$(this).attr('rel');
            BranchClickSplit = BranchClick.split('.');
            fixBranchClickSplit = '0,'+BranchClickSplit.toString();
            action = $(this).data('action');
            path = $(this).data('path');

            switch (action) {
            case 'expand':
            $('tr.prestablog_branch').each(function() {
            BranchParent = $(this).attr('rel');
            BranchParentSplit = BranchParent.split('.');
            fixBranchParentSplit = '0,'+BranchParentSplit.toString();

            if ($.isSubstring(fixBranchParentSplit, fixBranchClickSplit)
            && BranchClick != BranchParent
            && BranchClickSplit.length+1 == BranchParentSplit.length
            ) {
            $(this).show();
            }
            });
            $(this).attr('src', path.concat('collapse.gif'));
            $(this).data('action', 'collapse');
            break;

            case 'collapse':
            $('tr.prestablog_branch').each(function() {
            BranchParent = $(this).attr('rel');
            BranchParentSplit = BranchParent.split('.');
            fixBranchParentSplit = '0,'+BranchParentSplit.toString();

            if ($.isSubstring(fixBranchParentSplit, fixBranchClickSplit)
            && BranchClick != BranchParent
            ) {
            $(this).hide();
            $(this).find('img.expand-cat').each(function() {
                $(this).attr('src', path.concat('expand.gif'));
                $(this).data('action', 'expand');
                });
            }
            });
            $(this).attr('src', path.concat('expand.gif'));
            $(this).data('action', 'expand');
            break;
            }
            });
            });
            jQuery.isSubstring = function(haystack, needle) {
            return haystack.indexOf(needle) !== -1;
        };
        {/literal}	
        </script>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Related products' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-10">
        <div id='currentProductLink' style='display:none;'>
            {if count($products_link) > 0}
                {foreach $products_link as $product_link}
                    <input type='text' name='productsLink[]' value='{(int) $product_link}' class='linked_{(int) $product_link|escape:'html':'UTF-8'}' />
                {/foreach}
            {/if}

            {if Tools::getValue('productsLink') && !Tools::getValue('idN')}
                {foreach Tools::getValue('productsLink') as $product_link}
                    <input type='text' name='productsLink[]' value='{(int) $product_link['id_product']|escape:'html':'UTF-8'}' class='linked_{(int) $product_link['id_product']}'>
                {/foreach}
            {/if}
        </div>
    </div>
    <div class='blocmodule col-sm-4'>
        <table cellspacing='0' cellpadding='0' class='table' style='width:100%'>
            <thead>
                <tr>
                    <th class='center' style='width:30px;'>{l s='ID' d='Modules.Prestablog.Prestablog'}</th>
                    <th class='center' style='width:50px;'>{l s='Image' d='Modules.Prestablog.Prestablog'}</th>
                    <th class='center'>{l s='Name' d='Modules.Prestablog.Prestablog'}</th>
                    <th class='center' style='width:40px;'>{l s='Unlink' d='Modules.Prestablog.Prestablog'}</th>
                </tr>
            </thead>
            <tbody id='productLinked'>
                <tr>
                    <td colspan='4' class='center'>{l s='No product linked' d='Modules.Prestablog.Prestablog'}</td>
                </tr>
            </tbody>
        </table>
        </div>
        <div class='col-sm-1'></div>
        <div class='blocmodule col-sm-5'>
        <p class='center'>
            {l s='Search' d='Modules.Prestablog.Prestablog'} :
            <input type='text' size='20' id='productLinkSearch' name='productLinkSearch'
                placeholder='{sprintf("{l s='Keywords from %1$s or #id' d='Modules.Prestablog.Prestablog'}", "{l s='Name' d='Modules.Prestablog.Prestablog'}")}' />
        </p>
        <table cellspacing='0' cellpadding='0' class='table' style='width:100%'>
            <thead>
                <tr>
                    <th class='center' style='width:40px;'>{l s='Link' d='Modules.Prestablog.Prestablog'}</th>
                    <th class='center' style='width:30px;'>{l s='ID' d='Modules.Prestablog.Prestablog'}</th>
                    <th class='center' style='width:50px;'>{l s='Image' d='Modules.Prestablog.Prestablog'}</th>
                    <th class='center'>{l s='Name' d='Modules.Prestablog.Prestablog'}</th>
                </tr>
            </thead>
            <tbody id='productLinkResult'>
                <tr>
                    <td colspan='4' class='center'>
                        {l s='You must search before' d='Modules.Prestablog.Prestablog'} ({l s='caract. minimum' d='Modules.Prestablog.Prestablog'}
                        {(int) Configuration::get("{$prestablog->name}_nb_car_min_linkprod")})
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2 ">{l s='Related Posts' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-10">
        <div id='currentArticleLink' style='display:none;'>
            {if Tools::getValue('idN')}
                {assign "articles_link" NewsClass::getArticleLinkListe((int) Tools::getValue('idN'))}
                {if count($articles_link) > 0}
                    {foreach $articles_link $article_link}
                        <input type='text' name='articlesLink[]' value='{(int) $article_link}'
                            class='linked_{(int) $article_link}' />
                    {/foreach}
                {/if}
            {/if}
            {if Tools::getValue('articlesLink') && !Tools::getValue('idN')}
                {foreach Tools::getValue('articlesLink') $article_link}
                    <input type='text' name='articlesLink[]'
                        value='{(int) $article_link['id_prestablog_news']}'
                        class='linked_{(int) $article_link['id_prestablog_news']}' />
                {/foreach}
            {/if}
        </div>
        <div class='blocmodule col-sm-4'>
            <table cellspacing='0' cellpadding='0' class='table' style='width:100%'>
                <thead>
                    <tr>
                        <th class='center' style='width:30px;'>{l s='ID' d='Modules.Prestablog.Prestablog'}</th>
                        <th class='center' style='width:50px;'>{l s='Image' d='Modules.Prestablog.Prestablog'}</th>
                        <th class='center'>{l s='Title' d='Modules.Prestablog.Prestablog'}</th>
                        <th class='center' style='width:40px;'>{l s='Unlink' d='Modules.Prestablog.Prestablog'}</th>
                    </tr>
                </thead>
                <tbody id='articleLinked'>
                    <tr>
                        <td colspan='4' class='center'>{l s='No article linked' d='Modules.Prestablog.Prestablog'}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class='col-sm-1'></div>
        <div class='blocmodule col-sm-5'>
            <p class='center'>
                    {l s='Search' d='Modules.Prestablog.Prestablog'} :
                    <input type='text' size='20' id='articleLinkSearch' name='articleLinkSearch'
                        placeholder='{sprintf("{l s='Keywords from %1$s or #id' d='Modules.Prestablog.Prestablog'}", "{l s='Title' d='Modules.Prestablog.Prestablog'}")}'>
            </p>
            <table cellspacing='0' cellpadding='0' class='table' style='width:100%'>
                <thead>
                    <tr>
                        <th class='center' style='width:40px;'>{l s='Link' d='Modules.Prestablog.Prestablog'}</th>
                        <th class='center' style='width:30px;'>{l s='ID' d='Modules.Prestablog.Prestablog'}</th>
                        <th class='center' style='width:50px;'>{l s='Image' d='Modules.Prestablog.Prestablog'}</th>
                        <th class='center'>{l s='Title' d='Modules.Prestablog.Prestablog'}</th>
                    </tr>
                </thead>
                <tbody id='articleLinkResult'>
                    <tr>
                        <td colspan='4' class='center'>
                            {l s='You must search before' d='Modules.Prestablog.Prestablog'} ({l s='caract. minimum' d='Modules.Prestablog.Prestablog'}
                            {(int) Configuration::get("{$prestablog->name}_nb_car_min_linknews")})
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
            <div id='display_author' style='display: none;'>
                <input type='text' name='author_id' id='author_id' value='{$employee->id|escape:'html':'UTF-8'}' />
            </div>
    </div>
</div>

{$prestablog->get_displayFormDate('col-lg-2', "{l s='Date' d='Modules.Prestablog.Prestablog'}", 'date', $news->date, true)}

{if !$prestaboost}
    {assign "popuplink" "{NewsClass::getPopupLink($news->id)}"}
    {if isset($popuplink)}
        {$prestablog->get_displayFormSelect('col-lg-2', "{l s='Add a popup to your article :' d='Modules.Prestablog.Prestablog'}", 'popupLink',
        $popuplink, $popups_link, null, 'col-lg-5')}
    {else}
        {$prestablog->get_displayFormSelect('col-lg-2', "{l s='Add a popup to your article :' d='Modules.Prestablog.Prestablog'}", 'popupLink',
        $getP, $popups_link, null, 'col-lg-5')}
    {/if}
{/if}

{if Tools::getIsset('editNews')}
    {if $employee->id_profile == 1 && AuthorClass::getListeAuthor() != '' && AuthorClass::getListeAuthor() != null}
        {if $id_auth != '' && $name != ''}
            {$prestablog->get_displayFormSelectAuthor('col-lg-2', "{l s='Select an author' d='Modules.Prestablog.Prestablog'}", 'authors',
            $stringAuthors, $authors,
            null, 'col-lg-5')}
        {else}
            {$prestablog->get_displayFormSelectAuthor('col-lg-2', "{l s='Select an author' d='Modules.Prestablog.Prestablog'}", 'authors',
            '', $authors, null, 'col-lg-5')}
        {/if}
    {/if}
{/if}
<div class='margin-form'>
    {if Tools::getValue('idN')}
        <button class='btn btn-primary' id='submitForm' name='submitUpdateNews' type='submit'>
            <i class='icon-save'></i>&nbsp;
            {l s='Update' d='Modules.Prestablog.Prestablog'}
        </button>
    {else}
        <button class='btn btn-primary' id='submitForm' name='submitAddNews' type='submit'>
            <i class='icon-plus'></i>
            {l s='Add content' d='Modules.Prestablog.Prestablog'}
        </button>
    {/if}
</div>

{$prestablog->get_displayFormClose()}

<form name="formCrop" id="formCrop" action="{$confpath}" method="post" onsubmit="return checkCoords();">
    <input type="hidden" name="idN" value="{Tools::getValue('idN')}" />
    <input type="hidden" id="pfx" name="pfx" value="{Tools::getValue('pfx')}" />
    <input type="hidden" id="x" name="x" />
    <input type="hidden" id="y" name="y" />
    <input type="hidden" id="w" name="w" />
    <input type="hidden" id="h" name="h" />
    <input type="hidden" id="ratio" name="ratio" />
    <input type="hidden" name="submitCrop" value="submitCrop" />
</form>
