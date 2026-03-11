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

<div class="blocmodule">
    <form method="post" action="{$confpath|escape:'html':'UTF-8'}&commentListe" enctype="multipart/form-data">
        <fieldset>
            <input type="hidden" name="submitFiltreComment" value="1" />
            <div class="col-sm-2">
                <i class="material-icons">filter_list</i>
                {l s='Filter list' d='Modules.Prestablog.Prestablog'} :
            </div>
            <div class="col-sm-2">
                <input type="radio" name="activeComment" {if $check_comment_state == -2} checked {/if}
                    onchange="form.submit();" value="-2">
                <i class="material-icons">refresh</i> {l s='All' d='Modules.Prestablog.Prestablog'}
            </div>
            <div class="col-sm-2">
                <input type="radio" name="activeComment" {if $check_comment_state == -1} checked {/if}
                    onchange="form.submit();" value="-1">
                <i class="material-icons" style="color: #FF7800;">help_outline</i> {l s='Pending' d='Modules.Prestablog.Prestablog'}
            </div>
            <div class="col-sm-2">
                <input type="radio" name="activeComment" {if $check_comment_state == 1} checked {/if}
                    onchange="form.submit();" value="1">
                <i class="material-icons" style="color: #78d07d;">check_circle</i> {l s='Enabled' d='Modules.Prestablog.Prestablog'}
            </div>
            <div class="col-sm-2">
                <input type="radio" name="activeComment"
                    {if is_numeric($check_comment_state) && $check_comment_state == 0} checked {/if}
                    onchange="form.submit();" value="0">
                <i class="material-icons" style="color: #c05c67;">cancel</i> {l s='Disabled' d='Modules.Prestablog.Prestablog'}
            </div>
            <div class="col-sm-2">
                <div>
                    <input type="text" id="search_news" name="search_news"
                        placeholder="{l s='Search by articles' d='Modules.Prestablog.Prestablog'}" onkeyup="filter()" />
                </div>
                <div>
                    <input type="text" id="search_comment" name="search_comment"
                        placeholder="{l s='Search by comments' d='Modules.Prestablog.Prestablog'}" onkeyup="filter()" />
                </div>
            </div>
        </fieldset>
    </form>
</div>

<div class="blocmodule">
    <fieldset>
        <legend style="margin-bottom:10px;">{l s='Comments' d='Modules.Prestablog.Prestablog'} :</legend>
        <table id="table_comment" cellpadding="0" cellspacing="0" style="margin:auto;width:100%;">
            <thead class="center">
                <tr>
                    <th>Id</th>
                    <th>{l s='Date' d='Modules.Prestablog.Prestablog'}</th>
                    <th>{l s='News' d='Modules.Prestablog.Prestablog'}</th>
                    <th>{l s='Name' d='Modules.Prestablog.Prestablog'}</th>
                    <th>{l s='Comment' d='Modules.Prestablog.Prestablog'}</th>
                    <th class="center" style="width:100px;">{l s='Status' d='Modules.Prestablog.Prestablog'}</th>
                    <th class="center" style="text-align: right;">
                        Actions
                        <input type="checkbox" id="selectAllComments" onclick="toggleSelectAll(this)" style="margin-left: 10px; margin-right: 10px;">
                    </th>

                </tr>
            </thead>

            {if count($liste) > 0}
                {foreach $liste  $value}
                    <tr>
                        <td class="center">{$value['id_prestablog_commentnews']|escape:'html':'UTF-8'}</td>
                        {if Language::getIsoById((int) (Configuration::get('PS_LANG_DEFAULT'))) == 'FR'}
                            <td class="center">{ToolsCore::displayDate($value['date'], 1, true)}</td>
                        {else}
                            {assign "orderdate" explode('-', $value['date'])}
                            {assign "orderdate2" explode(' ', $orderdate[2])}
                            <td class="center">{$orderdate[1]|escape:'html':'UTF-8'}/{$orderdate2[0]|escape:'html':'UTF-8'}/{$orderdate[0]|escape:'html':'UTF-8'} {$orderdate2[1]|escape:'html':'UTF-8'}</td>
                        {/if}

                        {assign "title_news" NewsClass::getTitleNews((int) $value['news'], (int) $context->language->id)}

                        <td>
                        <a href="{$confpath|escape:'html':'UTF-8'}&editNews&idN={$value['news']|escape:'html':'UTF-8'}">
                                {$prestablog::cleanCut($title_news, 40, '...')|escape:'html':'UTF-8'}
                            </a>
                        </td>
                        <td>{$value['name']|escape:'html':'UTF-8'}</td>
                        <td>{$prestablog::cleanCut($value['comment'], 120, '...')|escape:'html':'UTF-8'}</td>
                        <td class="status">
                            <a class="enabled" href="{$confpath|escape:'html':'UTF-8'}&commentListe&idC={$value['id_prestablog_commentnews']|escape:'html':'UTF-8'}&enabledComment" {if (int) $value['actif'] != 1}
                                style="display:none;" {else} rel="1"
                                {/if}>
                                <i class="material-icons" title="{l s='Approved' d='Modules.Prestablog.Prestablog'}" style="color: #4CAF50;">check_circle</i>
                            </a>
                            <a class="disabled" href="{$confpath|escape:'html':'UTF-8'}&commentListe&idC={$value['id_prestablog_commentnews']|escape:'html':'UTF-8'}&disabledComment" {if (int) $value['actif'] != 0}
                                style="display:none;" {else} rel="1"
                                {/if}>
                                <i class="material-icons" title="{l s='Disabled' d='Modules.Prestablog.Prestablog'}" style="color: #F44336;">cancel</i>
                            </a>
                            <a class="question" href="{$confpath|escape:'html':'UTF-8'}&commentListe&idC={$value['id_prestablog_commentnews']|escape:'html':'UTF-8'}&pendingComment" {if (int) $value['actif'] != -1 }
                                style="display:none;" {else} rel="1"
                                {/if}>
                                <i class="material-icons" title="{l s='Pending' d='Modules.Prestablog.Prestablog'}" style="color: #FF7800;">help_outline</i>
                            </a>
                        </td>
{literal}
                        <script language="javascript" type="text/javascript">
                           $(document).ready(function() {
                                $("td.status").mouseenter(function() {
                                    $(this).find("a").fadeIn();
                                }).mouseleave(function() {
                                    $(this).find("a").each(function() {
                                        if ($(this).attr('rel') != 1) {
                                            $(this).fadeOut();
                                        }
                                    });
                                });
                            });
                        </script>
{/literal}
                        <form method="post" action="{$confpath|escape:'html':'UTF-8'}&deleteAllComment&idC={$value['id_prestablog_commentnews']|escape:'html':'UTF-8'}:'html'}">
                            <td style="text-align:right">
                                {if $value.actif == 1}
                                    <a href="{$confpath|escape:'html':'UTF-8'}&replyComment&idC={$value.id_prestablog_commentnews|escape:'html':'UTF-8'}"
                                       title="{l s='Reply' d='Modules.Prestablog.Prestablog'}">
                                        <i class="material-icons" style="color: #6c868e;">reply</i>
                                    </a>
                                {/if}
                                <a href="{$confpath|escape:'html':'UTF-8'}&editComment&idC={$value['id_prestablog_commentnews']|escape:'html':'UTF-8'}:'html'}"
                                    title="{l s='Edit' d='Modules.Prestablog.Prestablog'}">
                                    <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                                </a>
                                <a href="{$confpath|escape:'html':'UTF-8'}&deleteComment&idC={$value['id_prestablog_commentnews']|escape:'html':'UTF-8'}:'html'}"
                                    onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                                    <i class="material-icons" style="color: #c05c67;">delete</i>
                                </a>
                                <input type="checkbox" name="AllidCToDelete[]" value="{$value['id_prestablog_commentnews']|escape:'html':'UTF-8'}"
                                    style="vertical-align: top;margin-left: 2px;" />


                            </td>
                    </tr>
                    {if isset($value['replies']) && count($value['replies']) > 0}
                        {foreach $value['replies'] as $reply}
                            <tr class="reply">
                                <td class="center">{$reply.id_prestablog_commentnews|escape:'html':'UTF-8'}</td>
                                {if Language::getIsoById((int) (Configuration::get('PS_LANG_DEFAULT'))) == 'FR'}
                                    <td class="center">{ToolsCore::displayDate($reply.date, 1, true)}</td>
                                {else}
                                    {assign var="orderdate" value=explode('-', $reply.date)}
                                    {assign var="orderdate2" value=explode(' ', $orderdate[2])}
                                    <td class="center">{$orderdate[1]|escape:'html':'UTF-8'}/{$orderdate2[0]|escape:'html':'UTF-8'}/{$orderdate[0]|escape:'html':'UTF-8'} {$orderdate2[1]|escape:'html':'UTF-8'}</td>
                                {/if}

                                <td>
                                </td>
                                <td style="padding-left: 20px;">↳ {$reply.name|escape:'html':'UTF-8'}</td>
                                <td><small>{$prestablog::cleanCut($reply.comment, 120, '...')|escape:'html':'UTF-8'}</small></td>
                               <td class="status">
                                    <a class="enabled" href="{$confpath|escape:'html':'UTF-8'}&commentListe&idC={$reply['id_prestablog_commentnews']|escape:'html':'UTF-8'}&enabledComment" {if (int) $reply['actif'] != 1}
                                        style="display:none;" {else} rel="1"
                                        {/if}>
                                        <i class="material-icons" title="{l s='Approved' d='Modules.Prestablog.Prestablog'}" style="color: #4CAF50;">check_circle</i>
                                    </a>
                                    <a class="disabled" href="{$confpath|escape:'html':'UTF-8'}&commentListe&idC={$reply['id_prestablog_commentnews']|escape:'html':'UTF-8'}&disabledComment" {if (int) $reply['actif'] != 0}
                                        style="display:none;" {else} rel="1"
                                        {/if}>
                                        <i class="material-icons" title="{l s='Disabled' d='Modules.Prestablog.Prestablog'}" style="color: #F44336;">cancel</i>
                                    </a>
                                    <a class="question" href="{$confpath|escape:'html':'UTF-8'}&commentListe&idC={$reply['id_prestablog_commentnews']|escape:'html':'UTF-8'}&pendingComment" {if (int) $reply['actif'] != -1}
                                        style="display:none;" {else} rel="1"
                                        {/if}>
                                        <i class="material-icons" title="{l s='Pending' d='Modules.Prestablog.Prestablog'}" style="color: #FF7800;">help_outline</i>
                                    </a>
                                </td>
                                <td style="text-align:right">
                                    <a href="{$confpath|escape:'html':'UTF-8'}&editComment&idC={$reply.id_prestablog_commentnews|escape:'html':'UTF-8'}" title="{l s='Edit' d='Modules.Prestablog.Prestablog'}">
                                        <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                                    </a>
                                    <a href="{$confpath|escape:'html':'UTF-8'}&deleteComment&idC={$reply.id_prestablog_commentnews|escape:'html':'UTF-8'}" onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                                        <i class="material-icons" style="color: #c05c67;">delete</i>
                                    </a>
                                    <input type="checkbox" name="AllidCToDelete[]" value="{$reply.id_prestablog_commentnews|escape:'html':'UTF-8'}" style="vertical-align: top;margin-left: 2px;"  onclick="toggleSelect(this)" />
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                {/foreach}
            {/if}
            <div style="float:right; margin-bottom: 10px">
                <input type="submit" id="deleteAllComment" name="deleteAllComment" class="btn btn-default"
                    value="{l s='Delete checked comments' d='Modules.Prestablog.Prestablog'}"
                    onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');" style="float:right;" />
            </div>

            </form>
        </tbody>
            {if (int) $pagination['NombreTotalPages'] > 1}
                <tfooter>
                    <tr>
                        <td colspan="7">
                            <div class="prestablog_pagination">
                                {if (int) $pagination['PageCourante'] > 1}
                                    <a href="{$confpath|escape:'html':'UTF-8'}&commentListe&start={$pagination['StartPrecedent']|escape:'html':'UTF-8'}:'html'}&p={$pagination['PagePrecedente']|escape:'html':'UTF-8'}]}">&lt;&lt;</a>
                                {else}
                                    <span class="disabled">&lt;&lt;</span>
                                {/if}
                                {if $pagination['PremieresPages']}
                                    {foreach $pagination['PremieresPages'] $value_page $key_page}
                                        {if (int) Tools::getValue('p') == $key_page || (Tools::getValue('p') == '' && $key_page == 1)}
                                            <span class="current">{$key_page|escape:'html':'UTF-8'}</span>
                                        {else}
                                            {if $key_page == 1}
                                                <a href="{$confpath|escape:'html':'UTF-8'}&commentListe">{$key_page|escape:'html':'UTF-8'}</a>
                                            {else}
                                                <a href="{$confpath|escape:'html':'UTF-8'}&commentListe&start={$value_page|escape:'html':'UTF-8'}&p={$key_page|escape:'html':'UTF-8'}">{$key_page|escape:'html':'UTF-8'}</a>
                                            {/if}
                                        {/if}
                                    {/foreach}
                                {/if}
                                {if isset($pagination['Pages']) && $pagination['Pages']}
                                    <span class="more">...</span>
                                    {foreach $pagination['Pages'] $value_page $key_page}
                                        {if !in_array($value_page, $pagination['PremieresPages'])}
                                            {if (int) Tools::getValue('p') == $key_page || Tools::getValue('p') == ''}
                                                <span class="current">{$key_page}</span>
                                            {else}
                                                <a href="{$confpath|escape:'html':'UTF-8'}&commentListe&start={$value_page|escape:'html':'UTF-8'}&p={$key_page|escape:'html':'UTF-8'}">
                                                {$key_page|escape:'html':'UTF-8'}</a>
                                            {/if}
                                        {/if}
                                    {/foreach}
                                {/if}
                                {if $pagination['PageCourante'] < $pagination['NombreTotalPages']}
                                    <a href="{$confpath|escape:'html':'UTF-8'}&start={$pagination['StartSuivant']|escape:'html':'UTF-8'}&p={$pagination['PageSuivante']|escape:'html':'UTF-8'}">
                                    &gt;&gt;</a>
                                {else}
                                    <span class="disabled">&gt;&gt;</span>
                                {/if}
                            </div>
                        </td>
                    </tr>
                </tfooter>
            {/if}
        </table>
    </fieldset>
</div>
<script type="text/javascript">
    filter();

    function filter() {
        var search_news = $("#search_news").val();
        var search_comment = $("#search_comment").val();
        var filter_search_news, filter_search_comment, table, tr, td, i, td_search_news,
            td_search_comment;
        filter_search_news = search_news.toLowerCase();
        filter_search_comment = search_comment.toLowerCase();
        table = document.getElementById("table_comment");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td_search_news = tr[i].getElementsByTagName("td")[2];
            td_search_comment = tr[i].getElementsByTagName("td")[4];
            if (td) {
                if ((td_search_news.innerHTML.toLowerCase().indexOf(filter_search_news.toLowerCase()) >
                        -1) && (td_search_comment.innerHTML.toLowerCase().indexOf(filter_search_comment
                        .toLowerCase()) > -1)) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    function toggleSelectAll(selectAll) {
        const commentCheckboxes = document.querySelectorAll('input[name="AllidCToDelete[]"]');
        commentCheckboxes.forEach(function(checkbox) {
            checkbox.checked = selectAll.checked;
        });
    }

    function toggleSelect(singleCheckbox) {
        const selectAllCheckbox = document.getElementById('selectAllComments');
        const commentCheckboxes = document.querySelectorAll('input[name="AllidCToDelete[]"]');

        if (!singleCheckbox.checked) {
            selectAllCheckbox.checked = false;
        }

        if (Array.from(commentCheckboxes).every(input => input.checked)) {
            selectAllCheckbox.checked = true;
        }
    }

</script>