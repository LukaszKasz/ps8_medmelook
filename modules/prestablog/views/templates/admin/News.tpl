{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if $is_multishop_active && $no_shop_selected}
    <div id="suggestion_banner" class="suggestion_banner col-sm-4 blocmodule">
        <p class="title">Multistore</p>
        <p>{l s='If your multistore functionnality is on, please select one shop to configure your blog.' d='Modules.Prestablog.Prestablog'}</p>
    </div>
{else}

    <div class="blocmodule">
        <form method="post" action="{$confpath|escape:'html':'UTF-8'}&newsListe" enctype="multipart/form-data">
            <fieldset>
                <input type="hidden" name="submitFiltreNews" value="1" />
                <div class="col-sm-2">
                    <a class="btn btn-primary" href="{$confpath|escape:'html':'UTF-8'}&addNews">
                        <i class="icon-plus"></i>&nbsp;
                        {l s='Add a news' d='Modules.Prestablog.Prestablog'}
                    </a>
                </div>
                <div class="col-sm-2">
                    <input type="text" id="search_article" name="search_article"
                        placeholder="{l s='Search by articles' d='Modules.Prestablog.Prestablog'}" onkeyup="filter()"
                        style="margin-bottom: 5px;" />
                </div>
                <div class="col-sm-2">
                    <input type="text" id="search_author" name="search_author"
                        placeholder="{l s='Search by author' d='Modules.Prestablog.Prestablog'}" onkeyup="filter()"
                        style="margin-bottom: .4rem;" />
                </div>
                <script type="text/javascript">
                    filter();

                    function filter() {
                        var search_article = $("#search_article").val();
                        var search_author = $("#search_author").val();

                        var filter_search_author, filter_search_article, table, tr, td, i, td_search_article,
                            td_search_author;
                        filter_search_article = search_article.toLowerCase();
                        filter_search_author = search_author.toLowerCase();
                        table = document.getElementById("table_article");
                        tr = table.getElementsByTagName("tr");

                        for (i = 0; i < tr.length; i++) {
                            td = tr[i].getElementsByTagName("td")[0];
                            td_search_article = tr[i].getElementsByTagName("td")[4];
                            td_search_author = tr[i].getElementsByTagName("td")[2];

                            if (td) {
                                if ((td_search_article.innerHTML.toUpperCase().indexOf(filter_search_article
                                        .toUpperCase()) > -1) && (td_search_author.innerHTML.toUpperCase().indexOf(
                                        filter_search_author.toUpperCase()) > -1)) {
                                    tr[i].style.display = "";
                                } else {
                                    tr[i].style.display = "none";
                                }
                            }
                        }
                    }
                </script>

                {if count($categories) > 0}
                    <div class="col-sm-2">
                        {$categoriesClass->displaySelectArboCategories($categories, 0, 0, "{l s="Categories"  d='Modules.Prestablog.Prestablog'}",
                        'c',
                        'form.submit();', $toolsValueCategories)}
                    </div>
                {/if}
                <div class="col-sm-1">
                    <input type="checkbox" name="activeNews" {if $checkActive == 1}checked{/if} onchange="form.submit();">
                    {l s="active"  d='Modules.Prestablog.Prestablog'}
                </div>
            </fieldset>
        </form>

        {if count($languages) != 1}
            {foreach from=$languages item="language"}
                <input type="radio" name="id_lang" value="{$language['id_lang']|escape:'html':'UTF-8'}" onclick="location.href='{$confpath|escape:'html':'UTF-8'}&newsListe&languesup={$language['id_lang']|escape:'html':'UTF-8'}'"
                    {if $toolsLanguageSup and $language['id_lang'] == $toolsLanguageSup}checked=checked{/if} />
                <img src="../img/l/{$language['id_lang']|escape:'html':'UTF-8'}.jpg" class="pointer indent-right prestablogflag" alt="{$language['name']|escape:'html':'UTF-8'}" title="{$language['name']|escape:'html':'UTF-8'}">
            {/foreach}

            <input type="radio" name="all" value="all" onclick="location.href='{$confpath|escape:'html':'UTF-8'}&newsListe'"
            {if not $toolsLanguageSup} checked=checked {/if}/> {l s="All"  d='Modules.Prestablog.Prestablog'}
        {/if}
        </div>

    <div class="blocmodule">
        <fieldset>
            <legend style="margin-bottom:10px;"> {l s='News' d='Modules.Prestablog.Prestablog'} :
                {if $listCategorieValue}
                    {sprintf("{l s='%1$s currents items on %2$s' d='Modules.Prestablog.Prestablog'}", $countList, $categoriesName)|escape:'html':'UTF-8'}
                {else}
                    {sprintf("{l s='%1$s currents items' d='Modules.Prestablog.Prestablog'}", $countList)|escape:'html':'UTF-8'}
                {/if}
                <span style="color: green;">
                </span>
            </legend>

            <table class="table_news" id="table_article" cellpadding="0" cellspacing="0" style="margin:auto;width:100%;">
                <thead>
                    <tr>
                        <th style="text-align:center;">Id</th>
                        <th style="text-align:center;">{l s='Date' d='Modules.Prestablog.Prestablog'}</th>
                        <th style="text-align:center;">{l s='Author' d='Modules.Prestablog.Prestablog'}</th>
                        <th style="text-align:center;">{l s='Image' d='Modules.Prestablog.Prestablog'}</th>
                        <th width="400px" style="text-align:center;">{l s='Title' d='Modules.Prestablog.Prestablog'}</th>
                        <th width="70px" style="text-align:center;">{l s='Read' d='Modules.Prestablog.Prestablog'}</th>
                        <th width="70px" style="text-align:center;">{l s='Rate/5' d='Modules.Prestablog.Prestablog'}</th>
                        <th style="text-align:center;">{l s='Comments' d='Modules.Prestablog.Prestablog'}</th>
                        {if not $prestaboost}
                            <th style="text-align:center;">{l s='Popup' d='Modules.Prestablog.Prestablog'}</th>
                        {/if}
                        <th style="text-align:center;">{l s='Products linked' d='Modules.Prestablog.Prestablog'}</th>
                        <th style="text-align:center;">{l s='Activate' d='Modules.Prestablog.Prestablog'}</th>
                        <th style="text-align:center;">{l s='Actions' d='Modules.Prestablog.Prestablog'}</th>
                    </tr>
                </thead>

                {if count($newsList) > 0}
                    {foreach $newsList "value"}
                        {assign "langListNews" json_decode($value['langues'], true)}
                        {assign "langue" "false"}
                        {if in_array($toolsLanguageSup, $langListNews)}
                            {assign "langue" "true"}
                        {else}
                            {assign "langue" "false"}
                        {/if}

                        {if $langue == "true"}
                            <tr>
                                <td class="center">
                                    {$value['id_prestablog_news']|escape:'html':'UTF-8'}
                                    {if not empty($value['url_redirect']) and Validate::isAbsoluteUrl($value['url_redirect'])}
                                        <a href="{$value['url_redirect']|escape:'html':'UTF-8'}" target="_blank"
                                            title="{l s='Permanent redirect url' d='Modules.Prestablog.Prestablog'}">
                                            <i class="material-icons" style="font-size: 12px;">open_in_new</i>
                                        </a>
                                    {/if}
                                </td>
                                <td class="center">
                                    {if Language::getIsoById($configLangDefault == 'FR')}
                                        {ToolsCore::displayDate($value['date'], 1, true)}
                                    {else}
                                        {assign "orderDate" explode('-', $value['date'])}
                                        {assign "orderDate2" explode(' ', $orderDate[2])}
                                        {$orderDate[1]|escape:'html':'UTF-8'}/{$orderDate2[0]|escape:'html':'UTF-8'}/{$orderDate[0]|escape:'html':'UTF-8'} {$orderDate2[1]|escape:'html':'UTF-8'}
                                    {/if}
                                </td>
                                <td class="center">
                                    {if isset($value['author_id'])}
                                        {assign "authorData" AuthorClass::getAuthorData($value['author_id'])}
                                    {/if}
                                    {if isset($authorData['lastname'])}
                                        {$authorData['firstname']|escape:'html':'UTF-8'} {$authorData['lastname']|escape:'html':'UTF-8'}
                                    {else}
                                        -
                                    {/if}
                                </td>
                                <td class="center">
                                    {if file_exists("{$imgUpPath|escape:'html':'UTF-8'}/adminth_{$value['id_prestablog_news']}.jpg")}
                                        <img src="{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/up-img/adminth_{$value['id_prestablog_news']|escape:'html':'UTF-8'}.jpg?{$md5|escape:'html':'UTF-8'}" />
                                    {else}                               
                                        -
                                    {/if}
                                </td>
                                <td style="text-align: left;">
                                    {foreach $langListNews "valLangue"}
                                        {if count($languages) >= 1 && array_key_exists((int) $valLangue, $languagesShop) && $toolsLanguageSup == (int) $valLangue}
                                            {assign "newsTempo" $prestablog->createNewsClass((int)$value['id_prestablog_news'])}
                                            <img src="../img/l/{$valLangue|escape:'html':'UTF-8'}.jpg" class="prestablogflag">
                                            <a target="_blank"
                                                href="{PrestaBlog::prestablogUrl([
                                                                                           'id' => $newsTempo->id,
                                                                                           'seo' => $newsTempo->link_rewrite[$valLangue],
                                                                                           'titre' => $newsTempo->title[$valLangue],
                                                                                           'id_lang' => $valLangue
                                                                                       ])}{$accurl} preview={$prestablog->generateToken($newsTempo->id)|escape:'url':'UTF-8'}">
                                                <i class="material-icons" style="color: #6c868e; vertical-align: middle;">remove_red_eye</i>
                                            </a>
                                            {if Configuration::get("{$prestablog->name}_author_edit_actif") == 0}
                                                {if $permissionEdit}
                                                    <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}" class="hrefComment">
                                                        {NewsClass::getTitleNews($value['id_prestablog_news'], $valLangue)}
                                                    </a><br />
                                                {else}
                                                    {NewsClass::getTitleNews($value['id_prestablog_news'], $valLangue)}
                                                    <br />
                                                {/if}
                                            {else}
                                                {if $context->employee->id_profile == 1 or $context->employee->id == $value['author_id']}
                                                    {if $permissionEdit}
                                                        <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}" class="hrefComment">
                                                            {NewsClass::getTitleNews($value['id_prestablog_news'], $valLangue)|escape:'html':'UTF-8'}
                                                        </a><br />
                                                    {else}
                                                        {NewsClass::getTitleNews($value['id_prestablog_news'], $valLangue)|escape:'html':'UTF-8'}
                                                        <br />
                                                    {/if}
                                                {else}
                                                    {NewsClass::getTitleNews($value['id_prestablog_news'], $valLangue)|escape:'html':'UTF-8'}
                                                    <br />
                                                {/if}
                                            {/if}
                                        {/if}
                                    {/foreach}
                                </td>
                                <td>
                                    {foreach $langListNews "valLangue"}
                                        {if count($languages) >= 1 && array_key_exists((int) $valLangue, $languagesShop) && $toolsLanguageSup == (int) $valLangue}
                                            <img src="../img/l/{(int) $valLangue|escape:'html':'UTF-8'}.jpg" class="prestablogflag">
                                            {NewsClass::GetRead((int) $value['id_prestablog_news'], (int) $valLangue)|escape:'html':'UTF-8'}<br />
                                        {/if}
                                    {/foreach}
                                </td>
                                <td class="center">
                                    {assign "rate" NewsClass::getRate((int) $value['id_prestablog_news'])}
                                    {if $rate[0]['number_rating'] == ''}
                                        -
                                    {else}
                                        {$rate[0]['average_rating']} ({$rate[0]['number_rating']|escape:'html':'UTF-8'})
                                    {/if}
                                </td>
                                <td class="center">
                                    {assign "actifComments" CommentNewsClass::getListe(1, (int) $value['id_prestablog_news']) }
                                    {assign "allComments" CommentNewsClass::getListe(-2, (int) $value['id_prestablog_news']) }
                                    {if count($allComments) > 0}
                                        {count($actifComments)|escape:'html':'UTF-8'} {l s="of"  d='Modules.Prestablog.Prestablog'}
                                        {count($allComments)|escape:'html':'UTF-8'} {l s="active"  d='Modules.Prestablog.Prestablog'}
                                    {else}
                                        -
                                    {/if}
                                </td>
                                {if not $prestaboost}                            
                                <td class="center">

                                        {assign "popupLink" {NewsClass::getPopupLink($value['id_prestablog_news'])}|escape:'html':'UTF-8'}

                                        {if NewsClass::getPopupLink($value['id_prestablog_news']) == 1}
                                            {l s="Yes"  d='Modules.Prestablog.Prestablog'}
                                        {else}
                                            {l s="No"  d='Modules.Prestablog.Prestablog'}
                                        {/if}
                                </td>
                                {/if}
                                <td class="center">
                                    {assign "productsLink" NewsClass::getProductLinkListe((int) $value['id_prestablog_news'])}
                                    {if count($productsLink) > 0}
                                        {count($productsLink)|escape:'html':'UTF-8'}
                                    {else}
                                        -
                                    {/if}
                                </td>
                                <td class="center">
                                    {if $permissionActivate}
                                    <a href="{$confpath|escape:'html':'UTF-8'}&etatNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}">
                                        {if $value['actif']}
                                            <i class="material-icons action-enabled" style="color: #78d07d;">check</i>
                                        {else}
                                            <i class="material-icons action-disabled" style="color: #c05c67;">clear</i>
                                        {/if}
                                    </a>
                                    {/if}
                                </td>
                                <td class="center">
                                {if (int) Configuration::get("{$prestablog->name}_author_edit_actif") == 0}
                                    {if $permissionEdit}
                                        <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}"
                                            title="{l s='Edit' d='Modules.Prestablog.Prestablog'}">
                                            <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                                        </a>
                                    {/if}
                                    {if $permissionDelete}
                                        <a href="{$confpath|escape:'html':'UTF-8'}&deleteNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}"
                                           onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                                            <i class="material-icons" style="color: #c05c67;">delete</i>
                                        </a>
                                    {/if}
                                {else}
                                    {if $context->employee->id_profile == 1 || $context->employee->id == $value['author_id']}
                                        {if $permissionEdit}
                                            <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}"
                                                title="{l s='Edit' d='Modules.Prestablog.Prestablog'}">
                                                <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                                            </a>
                                        {/if}
                                        {if $permissionDelete}
                                            <a href="{$confpath|escape:'html':'UTF-8'}&deleteNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}"
                                               onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                                                <i class="material-icons" style="color: #c05c67;">delete</i>
                                            </a>
                                        {/if}
                                    {/if}
                                {/if}
                            </td>
                        </tr>
                        {elseif Tools::getValue('languesup') && $langue == "false"}
                        {else}
                        <tr>
                            <td class="center">
                                {$value['id_prestablog_news']|escape:'html':'UTF-8'}
                                {if not empty($value['url_redirect']) and Validate::isAbsoluteUrl($value['url_redirect'])}
                                    <a href="{$value['url_redirect']|escape:'html':'UTF-8'}" target="_blank"
                                        title="{l s="Permanent redirect url"  d='Modules.Prestablog.Prestablog'}">
                                        <i class="material-icons" style="font-size: 12px;">open_in_new</i>
                                    </a>
                                {/if}
                            </td>
                            <td class="center">
                                {if Language::getIsoById($configLangDefault == 'FR')}
                                    {ToolsCore::displayDate($value['date'], 1, true)|escape:'html':'UTF-8'}
                                {else}
                                    {assign "orderDate" explode('-', $value['date'])}
                                    {assign "orderDate2" explode(' ', $orderDate[2])}
                                    {$orderDate[1]|escape:'html':'UTF-8'}/{$orderDate2[0]|escape:'html':'UTF-8'}/{$orderDate[0]|escape:'html':'UTF-8'} {$orderDate2[1]|escape:'html':'UTF-8'}
                                {/if}
                            </td>
                            <td class="center">
                                {if isset($value['author_id'])}
                                    {assign "authorData" AuthorClass::getAuthorData($value['author_id'])}
                                {/if}
                                {if isset($authorData['lastname'])}
                                    {$authorData['firstname']|escape:'html':'UTF-8'} {$authorData['lastname']|escape:'html':'UTF-8'}
                                {else}
                                    -
                                {/if}
                            </td>
                            <td class="center">
                                {if file_exists("{$imgUpPath|escape:'html':'UTF-8'}/adminth_{$value['id_prestablog_news']|escape:'html':'UTF-8'}.jpg")}
                                    <img src="{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/up-img/adminth_{$value['id_prestablog_news']|escape:'html':'UTF-8'}.jpg?{$md5|escape:'html':'UTF-8'}" />
                                {else}
                                -
                                {/if}
                            </td>
                            <td style="text-align: left;">
                                {foreach $langListNews "valLangue"}
                                    {if count($languages) >= 1 && array_key_exists((int) $valLangue, $languagesShop)}
                                        {assign "newsTempo" $prestablog->createNewsClass((int)$value['id_prestablog_news'])}
                                        <img src="../img/l/{$valLangue|escape:'html':'UTF-8'}.jpg" class="prestablogflag">
                                        <a target="_blank"
                                            href="{PrestaBlog::prestablogUrl([
                                                                                       'id' => $newsTempo->id,
                                                                                       'seo' => $newsTempo->link_rewrite[$valLangue],
                                                                                       'titre' => $newsTempo->title[$valLangue],
                                                                                       'id_lang' => $valLangue
                                                                                   ])}{$accurl} preview={$prestablog->generateToken($newsTempo->id)|escape:'url':'UTF-8'}">
                                            <i class="material-icons" style="color: #6c868e; vertical-align: middle;">remove_red_eye</i>
                                        </a>
                                        {if $permissionEdit}
                                            <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}" class="hrefComment">
                                                {NewsClass::getTitleNews($value['id_prestablog_news'], $valLangue)|escape:'html':'UTF-8'}
                                            </a><br />
                                        {else}
                                            {NewsClass::getTitleNews($value['id_prestablog_news'], $valLangue)|escape:'html':'UTF-8'}<br />
                                        {/if}
                                    {/if}
                                {/foreach}
                            </td>
                            <td>
                                {foreach $langListNews "valLangue"}
                                    {if count($languages) >= 1 && array_key_exists((int) $valLangue, $languagesShop)}
                                        <img src="../img/l/{(int) $valLangue|escape:'html':'UTF-8'}.jpg" class="prestablogflag">
                                        {NewsClass::GetRead((int) $value['id_prestablog_news'], (int) $valLangue)}<br />
                                    {/if}
                                {/foreach}
                            </td>
                            <td class="center">
                                {assign "rate" NewsClass::getRate((int) $value['id_prestablog_news'])}
                                {if $rate[0]['number_rating'] == ''}
                                    -
                                {else}
                                    {$rate[0]['average_rating']|escape:'html':'UTF-8'}({$rate[0]['number_rating']|escape:'html':'UTF-8'})
                                {/if}
                            </td>
                            <td class="center">
                                {assign "actifComments" CommentNewsClass::getListe(1, (int) $value['id_prestablog_news']) }
                                {assign "allComments" CommentNewsClass::getListe(-2, (int) $value['id_prestablog_news']) }
                                {if count($allComments) > 0}
                                    {count($actifComments)|escape:'html':'UTF-8'} {l s="of"  d='Modules.Prestablog.Prestablog'}
                                    {count($allComments)|escape:'html':'UTF-8'} {l s="active"  d='Modules.Prestablog.Prestablog'}
                                {else}
                                    -
                                {/if}
                            </td>
                            {if not $prestaboost}
                            <td class="center">
                                    {assign "popupLink" NewsClass::getPopupLink($value['id_prestablog_news'])}
                                    {if isset($popuplink)}
                                        {l s="Yes"  d='Modules.Prestablog.Prestablog'}
                                    {else}
                                        {l s="No"  d='Modules.Prestablog.Prestablog'}
                                    {/if}
                            </td>
                            {/if}
                            <td class="center">
                                {assign "productsLink" NewsClass::getProductLinkListe((int) $value['id_prestablog_news'])}
                                {if count($productsLink) > 0}
                                    {count($productsLink)|escape:'html':'UTF-8'}
                                {else}
                                    -
                                {/if}
                            </td>
                            <td class="center">
                                {if $permissionActivate} 
                                <a href="{$confpath|escape:'html':'UTF-8'}&etatNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}">
                                    {if $value['actif']}
                                        <i class="material-icons action-enabled" style="color: #78d07d;">check</i>
                                    {else}
                                        <i class="material-icons action-disabled" style="color: #c05c67;">clear</i>
                                    {/if}
                                </a>
                                {/if} 
                            </td>
                            <td class="center">
                                {if (int) Configuration::get("{$prestablog->name}_author_edit_actif") == 0}
                                    {if $permissionEdit}
                                    <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}"
                                        title="{l s="Edit"  d='Modules.Prestablog.Prestablog'}">
                                        <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                                    </a>
                                    {/if}
                                    {if $permissionDelete}                                            
                                    <a href="{$confpath|escape:'html':'UTF-8'}&deleteNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}"
                                        onclick="return confirm('{l s="Are you sure?"  d='Modules.Prestablog.Prestablog'}');">
                                        <i class="material-icons" style="color: #c05c67;">delete</i>
                                    </a>
                                    {/if}
                                {else}
                                    {if $context->employee->id_profile == 1 || $context->employee->id == $value['author_id']}
                                        {if $permissionEdit}
                                            <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}"
                                                title="{l s="Edit"  d='Modules.Prestablog.Prestablog'}">
                                                <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                                            </a>
                                        {/if}
                                        {if $permissionDelete}                                            
                                            <a href="{$confpath|escape:'html':'UTF-8'}&deleteNews&idN={$value['id_prestablog_news']|escape:'html':'UTF-8'}"
                                                onclick="return confirm('{l s="Are you sure?"  d='Modules.Prestablog.Prestablog'}');">
                                                <i class="material-icons" style="color: #c05c67;">delete</i>
                                            </a>
                                        {/if}
                                    {/if}
                                {/if}
                            </td>
                        </tr>
                        {/if}
                    {/foreach}

                    {assign "pageType" "newsListe"}
                    {if $pagination['NombreTotalPages'] > 1}
                        <tfooter>
                            <tr>
                                <td colspan="12">
                                    <div class="prestablog_pagination">
                                    {if $pagination['PageCourante'] > 1}
                                        <a href="{$confpath|escape:'html':'UTF-8'}&{$pageType|escape:'html':'UTF-8'}&start={$pagination['StartPrecedent']|escape:'html':'UTF-8'}&p={$pagination['PagePrecedente']|escape:'html':'UTF-8'}&languesup={$toolsLanguageSup|escape:'html':'UTF-8'}">&lt;&lt;</a>
                                    {else}
                                            <span class="disabled">&lt;&lt;</span>
                                        {/if}
                                        {if $pagination['PremieresPages']}
                                            {foreach $pagination['PremieresPages'] item="value_page" key="key_page"}
                                                {if ($toolsValuePagination == $key_page or ($toolsValuePagination == '' and $key_page == 1))}
                                                    <span class="current">{$key_page|escape:'html':'UTF-8'}</span>
                                                {else}
                                                    {if key_page == 1}
                                                        <a href="{$confpath|escape:'html':'UTF-8'}&{$pageType|escape:'html':'UTF-8'}"> {$key_page|escape:'html':'UTF-8'}</a>
                                                        {else}
                                                            <a href="{$confpath|escape:'html':'UTF-8'}&{$pageType|escape:'html':'UTF-8'}&start={$value_page|escape:'html':'UTF-8'}&p={$key_page|escape:'html':'UTF-8'}&languesup={$toolsLanguageSup|escape:'html':'UTF-8'}">{$key_page|escape:'html':'UTF-8'}</a>
                                                        {/if}
                                                    {/if}
                                                {/foreach}
                                            {/if}
                                            {if isset($pagination['Pages']) and $pagination['Pages']}
                                                <span class="more">...</span>
                                                {foreach $pagination['Pages'] item="value_page" key="key_page"}
                                                    {if not in_array($value_page, $pagination['PremieresPages'])}
                                                        {if (int) $toolsValuePagination == $key_page or ($toolsValuePagination == '' && $key_page == 1)}
                                                            <span class="current">{$key_page|escape:'html':'UTF-8'}</span>
                                                        {else}
                                                            <a href="{$confpath|escape:'html':'UTF-8'}&{$pageType|escape:'html':'UTF-8'}&start={$value_page|escape:'html':'UTF-8'}&p={$key_page|escape:'html':'UTF-8'}&languesup={$toolsLanguageSup|escape:'html':'UTF-8'}">{$key_page|escape:'html':'UTF-8'}</a>
                                                        {/if}
                                                    {/if}
                                                {/foreach}
                                            {/if}

                                            {if $pagination['PageCourante'] < $pagination['NombreTotalPages']}
                                                <a href="{$confpath|escape:'html':'UTF-8'}&{$pageType|escape:'html':'UTF-8'}&start={$pagination['StartSuivant']|escape:'html':'UTF-8'}&p={$pagination['PageSuivante']|escape:'html':'UTF-8'}&languesup={$toolsLanguageSup|escape:'html':'UTF-8'}">&gt;&gt;</a>
                                            {else}
                                                <span class="disabled">&gt;&gt;</span>
                                            {/if}                                
                                    </div>
                                </td>
                            </tr>
                        </tfooter>
                    {/if}
                {else}
                    <tr>
                        <td colspan="8" class="center">{l s="No content registered" d= "pretablog"}</td>
                    </tr>
                {/if}
            </table>
        </fieldset>
    </div>
{/if}
<div class="clearfix"></div>