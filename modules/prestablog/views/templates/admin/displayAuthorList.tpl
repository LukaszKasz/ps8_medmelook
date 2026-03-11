{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if Tools::getValue('success') && Tools::getValue('success') == 'au'}
    <div class="margin-form">
        <div class="module_confirmation conf confirm alert alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {l s='Settings updated successfully' d='Modules.Prestablog.Prestablog'}
        </div>
    </div>
{/if}
{if Tools::getValue('permission_error') == '1'}
    <div class="alert alert-danger">
        {l s='You do not have permission to access this page' d='Modules.Prestablog.Prestablog'}
    </div>
{/if}
<div class="blocmodule">
    
    {if $id_emp == 1}
            <form method="post" action="{$confpath|escape:'html':'UTF-8'}&authorListe" enctype="multipart/form-data">
                <div class="col-sm-3" style="margin-bottom: 10px;">
                    <a class="btn btn-primary" href="{$confpath|escape:'html':'UTF-8'}&addAuthor">
                        <i class="icon-plus"></i>&nbsp;
                        {l s='Add an author' d='Modules.Prestablog.Prestablog'}
                    </a>
                </div>
            </form>
    {/if}

    <table class="table_news" id="table_author" cellpadding="0" cellspacing="0" style="margin:auto;width:100%;">
        <thead class="center">
            <tr>
                <th style="text-align:center;">Id</th>
                <th style="text-align:center;">{l s='Avatar' d='Modules.Prestablog.Prestablog'}</th>
                <th style="text-align:center;">{l s='Author' d='Modules.Prestablog.Prestablog'}</th>
                <th style="text-align:center;">{l s='Pseudo' d='Modules.Prestablog.Prestablog'}</th>
                <th style="text-align:center;">{l s='Date of creation' d='Modules.Prestablog.Prestablog'}</th>
                <th style="text-align:center;">{l s='Email' d='Modules.Prestablog.Prestablog'}</th>
                <th style="text-align:center;">{l s='Number of articles' d='Modules.Prestablog.Prestablog'}</th>
                <th style="text-align:center;">{l s='Most red article' d='Modules.Prestablog.Prestablog'}</th>
                {if $id_emp == 1}
                    <th style="text-align:center;">{l s='Action' d='Modules.Prestablog.Prestablog'}</th>
                {/if}
            </tr>
        </thead>

        {* {assign var=counter value=0} *}
        {foreach from=$liste key=counter item=value}
        <tr>
            <td class="center">{$value['id_author']|escape:'html':'UTF-8'}</td>
            <td class="center">
                {if file_exists("{$imgAuthorUpPath|escape:'html':'UTF-8'}/authorth_{$value['id_author']|escape:'html':'UTF-8'}.jpg")}
                    <img class="item" src="{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/author_th/authorth_{$value['id_author']|escape:'html':'UTF-8'}.jpg"/>
                {else}
                    <img class="item" src="{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/author_th/default.jpg"/>
                {/if}
            </td>
            <td class="center">{$value['firstname']|escape:'html':'UTF-8'} {$value['lastname']|escape:'html':'UTF-8'}</td>
            <td class="center">{$value['pseudo']|escape:'html':'UTF-8'}</td>
            <td class="center">{$value['date']|escape:'html':'UTF-8'}</td>
            <td class="center">{$value['email']|escape:'html':'UTF-8'}</td>
            <td class="center">{(int) ($author->getCountArticleCreated($value['id_author']))}</td>
            <td class="center">{$author->getMostRedArticle($value['id_author'])|escape:'html':'UTF-8'}</td>
            {if $id_emp == 1}
                <td class="center">
                    <a href="{$confpath|escape:'html':'UTF-8'}&authorPermissions&id_author={$value['id_author']|escape:'html':'UTF-8'}" title="{l s='Edit Permissions' d='Modules.Prestablog.Prestablog'}">
                        <i class="material-icons">lock</i>
                    </a>            
                    &nbsp;                    
                    <a href="{$confpath|escape:'html':'UTF-8'}&deleteAuthor&idA={$value['id_author']|escape:'html':'UTF-8'}" onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');" title="{l s='Delete' d='Modules.Prestablog.Prestablog'}">
                        <i class="material-icons" style="color: #c05c67;">delete</i>
                    </a>

            </td>
            {/if}
        </tr>
        {/foreach}
    </table>
</div>
