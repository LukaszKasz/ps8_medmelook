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
{if Tools::getValue('permission_error') == '1'}
    <div class="alert alert-danger">
        {l s='You do not have permission to access this page' d='Modules.Prestablog.Prestablog'}
    </div>
{/if}

    <p style="display: flex;">
        <i class="material-icons" style="color: #949494;">article</i>
        {$lastNewsLimit|escape:'html':'UTF-8'} {l s='latest news in' d='Modules.Prestablog.Prestablog'} {$languageIso|escape:'html':'UTF-8'}
    </p>
    <table class="table_news">
        <thead>
            <tr>
                <th style="text-align:center;">
                    {l s='ID' d='Modules.Prestablog.Prestablog'}
                </th>
                <th style="text-align:center;">
                    {l s='Date' d='Modules.Prestablog.Prestablog'}
                </th>
                <th style="text-align:center;">
                    {l s='Author' d='Modules.Prestablog.Prestablog'}
                </th>
                <th style="text-align:center;">
                    {l s='Picture' d='Modules.Prestablog.Prestablog'}
                </th>
                <th style="text-align:center;">
                    {l s='Title' d='Modules.Prestablog.Prestablog'}
                </th>
                <th style="text-align:center;">{l s='Read' d='Modules.Prestablog.Prestablog'}</th>
                <th width="80px" style="text-align:center;">{l s='Rate/5' d='Modules.Prestablog.Prestablog'}</th>
                <th style="text-align:center;">{l s='Comments' d='Modules.Prestablog.Prestablog'}</th>
                {if not $prestaboost}
                    <th style="text-align:center;">{l s='Popup' d='Modules.Prestablog.Prestablog'}</th>
                {/if}
                <th style="text-align:center;">{l s='Products linked' d='Modules.Prestablog.Prestablog'}</th>
                <th style="text-align:center;">{l s='Activate' d='Modules.Prestablog.Prestablog'}</th>
                <th style="text-align:center;">{l s='Actions' d='Modules.Prestablog.Prestablog'}</th>
            </tr>
        </thead>
        <tbody>

            {assign "iterator" "0"}
            {assign "langue" "false"}
            {if $newsListCount > 0}
                {while isset($newsList[$iterator])}
                    {if $iterator <= $lastNewsLimit+1}
                        {assign "langListNews[]" $newsList[$iterator]['langues']}
                        {assign "j" "0"}
                        {assign "newsId" $newsList[$iterator]['id_prestablog_news']}
                        {while isset($langListNews[$j])}
                            {if in_array($languageId, $langListNews)}
                                {assign "langue" "true"}
                            {else}
                                {assign "langue" "false"}
                            {/if}
                            {assign "j" $j+1}
                        {/while}
                        {if isset($languageId) and isset($langue) and $langue==true}
                            <tr>
                                <td>
                                    {$newsId|escape:'html':'UTF-8'}
                                </td>
                                <td>
                                    {$newsList[$iterator]['date']|escape:'html':'UTF-8'}
                                </td>
                                <td>

                                        {if isset($newsList[$iterator]['author_id'])}
                                            {foreach from=$authors item='item' key='key'}
                                                {if $item['id_author'] == $newsList[$iterator]['author_id']|escape:'html':'UTF-8'}
                                                    {$item['firstname']|escape:'html':'UTF-8'} {$item['lastname']|escape:'html':'UTF-8'}
                                                {/if}
                                            {/foreach}
                                        {else}
                                            -
                                        {/if}

                                </td>
                                <td style="text-align:center;">
                                    {if file_exists("{$imgUpPath|escape:'html':'UTF-8'}/adminth_{$newsId|escape:'html':'UTF-8'}.jpg")}
                                        {assign "imgIdReload" "adminth_{$newsId|escape:'html':'UTF-8'}.jpg?{$md5|escape:'html':'UTF-8'}"}
                                        <img src="{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/up-img/{$imgIdReload|escape:'html':'UTF-8'}" />
                                    {else}
                                        {l s='No picture' d='Modules.Prestablog.Prestablog'}
                                    {/if}
                                </td>
                                 <td>
                                {if Configuration::get("{$prestablog->name}_author_edit_actif") == 0}
                                    {if $permissionEdit}
                                        <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$newsId|escape:'html':'UTF-8'}" class="hrefComment">
                                            {$newsList[$iterator]['title']|escape:'html':'UTF-8'}
                                        </a>
                                    {else}
                                        {$newsList[$iterator]['title']|escape:'html':'UTF-8'}
                                    {/if}
                                {else}
                                    {if $context->employee->id_profile == 1 or $context->employee->id == $newsList[$iterator]['author_id']}
                                        {if $permissionEdit}
                                            <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$newsId|escape:'html':'UTF-8'}" class="hrefComment">
                                                {$newsList[$iterator]['title']|escape:'html':'UTF-8'}
                                            </a>
                                        {else}
                                            {$newsList[$iterator]['title']|escape:'html':'UTF-8'}
                                        {/if}
                                    {else}
                                        {$newsList[$iterator]['title']|escape:'html':'UTF-8'}
                                    {/if}
                                {/if}
                                </td>
                                <td>
                                    {$newsClass->getRead($newsId, $newsList[$iterator]['langues'])}
                                </td>
                                <td class="center">
                                    {assign "rate" $newsClass->getRate($newsId)}
                                    {if $rate[0]['number_rating'] == ''}
                                        -
                                    {else}
                                        {$rate[0]['average_rating']} ({$rate[0]['number_rating']})
                                    {/if}
                                </td>
                                <td class="center">
                                    {assign "activeComments" $commentNewsClass->getListe(1, $newsId)}
                                    {assign "allComments" $commentNewsClass->getListe(-2, $newsId)}
                                    {if count($allComments) > 0}
                                        {count($activeComments)} {l s='of' d='Modules.Prestablog.Prestablog'} {count($allComments)} {l s='active' d='Modules.Prestablog.Prestablog'}
                                    {else}
                                        -
                                    {/if}
                                </td>
                                {if not $prestaboost}
                                    <td class="center">
                                        {if isset($newsClass->getPopupLink($newsId))}
                                            {l s='Yes' d='Modules.Prestablog.Prestablog'}
                                        {else}
                                            {l s='No' d='Modules.Prestablog.Prestablog'}
                                        {/if}
                                    </td>
                                {/if}
                                <td class="center">
                                    {assign "productsLink" $newsClass->getProductLinkListe($newsId)}
                                    {if count($productsLink) > 0} {count($productsLink)} {else} - {/if}
                                </td>
                                <td class="center">
                                    {if $permissionActivate}
                                    <a href="{$confpath|escape:'html':'UTF-8'}&etatNews&idN={$newsId|escape:'html':'UTF-8'}">
                                        {if $newsList[$iterator]['actif']}
                                            <i class="material-icons action-enabled" style="color: #78d07d;">check</i>
                                        {else}
                                            <i class="material-icons action-disabled" style="color: #c05c67;">clear</i>
                                        {/if}
                                    </a>
                                    {/if}
                                </td>
                                <td class="center">
                                     {if Configuration::get("{$prestablog->name}_author_edit_actif") == 0}
                                        {if $permissionEdit}
                                            <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$newsId|escape:'html':'UTF-8'}" title={l s='Edit' d='Modules.Prestablog.Prestablog'}>
                                                <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                                            </a>
                                        {/if}
                                    {if $permissionDelete}
                                    <a href="{$confpath|escape:'html':'UTF-8'}&deleteNews&idN={$newsId|escape:'html':'UTF-8'}" title={l s='Delete' d='Modules.Prestablog.Prestablog'}
                                        onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                                        <i class="material-icons" style="color: #c05c67;">delete</i>
                                    </a>
                                    {/if}
                                {else}
                                    {if $context->employee->id_profile == 1 or $context->employee->id == $newsList[$iterator]['author_id']}
                                        {if $permissionEdit}
                                            <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$newsId|escape:'html':'UTF-8'}" title="{l s='Edit' d='Modules.Prestablog.Prestablog'}">
                                                <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                                            </a>
                                        {/if}
                                        {if $permissionDelete}
                                            <a href="{$confpath|escape:'html':'UTF-8'}&deleteNews&idN={$newsId|escape:'html':'UTF-8'}" title="{l s='Delete' d='Modules.Prestablog.Prestablog'}"
                                               onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                                                <i class="material-icons" style="color: #c05c67;">delete</i>
                                            </a>
                                        {/if}
                                    {/if}
                                {/if}
                                </td>
                            </tr>
                        {/if}
                    {/if}
                    {assign "iterator" $iterator+1}
                {/while}
            {/if}
        </tbody>
    </table>

    {if $commentFbActive}
        <div class="col-sm-3">
            <p style="display: flex;">
            <i class="material-icons" style="color: #3b52bb;">facebook</i> {l s="Facebook comments" d='Modules.Prestablog.Prestablog'}
            </p>
            <div class="bootstrap">
                <div class="alert alert-info">
                    <strong>{l s='Information' d='Modules.Prestablog.Prestablog'}</strong><br />
                    <p>
                        {l s='To moderate comments, go on the front office at bottom of each posts.' d='Modules.Prestablog.Prestablog'}
                    </p>
                </div>
            </div>
        </div>
    {else}
        {assign "commentUnread" $commentNewsClass->getListeNonLu()}
        <div class="col-sm-3">
            <p style="display: flex;">
            <i class="material-icons" style="color: #008cff;">help</i> 
                {count($commentUnread)|escape:'html':'UTF-8'}&nbsp;{l s="comment" d='Modules.Prestablog.Prestablog'}{if count($commentUnread) > 1}s{/if} {l s="pending" d='Modules.Prestablog.Prestablog'}
            </p>
        </div>
    {/if}
{/if}
<div class="clearfix"></div>