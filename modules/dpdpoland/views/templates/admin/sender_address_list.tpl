{** 2019 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 * @author    DPD Polska Sp. z o.o.
 * @copyright 2019 DPD Polska Sp. z o.o.
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of DPD Polska Sp. z o.o.
 *}
<script>
	$(document).ready(function(){
		$('table#sender_address_list .filter').keypress(function(event){
			formSubmit(event, 'submitFilterButtonSenderAddress');
		})
	});
</script>

<form class="form" action="{$full_url|escape:'htmlall':'UTF-8'}" method="post">
    <input type="hidden" value="0" name="submitFilterSenderAddress" id="submitFilterSenderAddress">
    <table id="sender_address_list" name="list_table" class="table_grid">
        <tbody>
            <tr>
                <td class="bottom">
                    <span class="float-left">
                        {if $page > 1}
                            <input type="image" src="../img/admin/list-prev2.gif" onclick="getE('submitFilterSenderAddress').value=1"/>&nbsp;
                            <input type="image" src="../img/admin/list-prev.gif" onclick="getE('submitFilterSenderAddress').value=;{$page|escape:'htmlall':'UTF-8' - 1}"/>
                        {/if}
                        {l s='Page' mod='dpdpoland'} <b>{$page|escape:'htmlall':'UTF-8'}</b> / {$total_pages|escape:'htmlall':'UTF-8'}
                        {if $page < $total_pages}
                            <input type="image" src="../img/admin/list-next.gif" onclick="getE('submitFilterSenderAddress').value=;{$page|escape:'htmlall':'UTF-8' + 1}"/>&nbsp;
                            <input type="image" src="../img/admin/list-next2.gif" onclick="getE('submitFilterSenderAddress').value=;{$total_pages|escape:'htmlall':'UTF-8'}"/>
                        {/if}
                        | {l s='Display' mod='dpdpoland'}
                        <select name="pagination" onchange="submit()">
                            {foreach from=$pagination item=value}
                                <option value="{$value|intval|escape:'htmlall':'UTF-8'}"{if $selected_pagination == $value} selected="selected" {elseif $selected_pagination == NULL && $value == $pagination[1]} selected="selected2"{/if}>{$value|intval|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                        / {$list_total|escape:'htmlall':'UTF-8'} {l s='result(s)' mod='dpdpoland'}
                    </span>
                    <span class="float-right">
                        <input type="submit" class="button" value="{l s='Filter' mod='dpdpoland'}" name="submitFilterButtonSenderAddress" id="submitFilterButtonSenderAddress">
                        <input type="submit" class="button" value="{l s='Reset' mod='dpdpoland'}" name="submitResetSenderAddress">
                    </span>
                    <span class="clear"></span>
                </td>
            </tr>
            <tr>
                <td>
                    <table cellspacing="0" cellpadding="0" class="table sender_address_list">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
							<col width="30px">
                        </colgroup>
                        <thead>
                            <tr class="nodrag nodrop titles-row">
                                <th class="center">
                                    <span class="title_box">
                                        {l s='Sender address ID' mod='dpdpoland'}
                                    </span>
                                    <br>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=id_sender_address&SenderAddressOrderWay=desc">
                                        {if $order_by == 'id_sender_address' && $order_way == 'desc'}
                                            <img border="0" src="../img/admin/down_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/down.gif">
                                        {/if}
                                    </a>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=id_sender_address&SenderAddressOrderWay=asc">
                                        {if $order_by == 'id_sender_address' && $order_way == 'asc'}
                                            <img border="0" src="../img/admin/up_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/up.gif">
                                        {/if}
                                    </a>
                                </th>
                                <th class="center">
									<span class="title_box">
										{l s='Alias' mod='dpdpoland'}
									</span>
                                    <br>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=alias&SenderAddressOrderWay=desc">
                                        {if $order_by == 'alias' && $order_way == 'desc'}
                                            <img border="0" src="../img/admin/down_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/down.gif">
                                        {/if}
                                    </a>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=alias&SenderAddressOrderWay=asc">
                                        {if $order_by == 'alias' && $order_way == 'asc'}
                                            <img border="0" src="../img/admin/up_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/up.gif">
                                        {/if}
                                    </a>
                                </th>
                                <th class="center">
									<span class="title_box">
										{l s='Company' mod='dpdpoland'}
									</span>
                                    <br>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=company&SenderAddressOrderWay=desc">
                                        {if $order_by == 'company' && $order_way == 'desc'}
                                            <img border="0" src="../img/admin/down_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/down.gif">
                                        {/if}
                                    </a>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=company&SenderAddressOrderWay=asc">
                                        {if $order_by == 'company' && $order_way == 'asc'}
                                            <img border="0" src="../img/admin/up_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/up.gif">
                                        {/if}
                                    </a>
                                </th>
                                <th class="center">
									<span class="title_box">
										{l s='Name' mod='dpdpoland'}
									</span>
                                    <br>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=name&SenderAddressOrderWay=desc">
                                        {if $order_by == 'name' && $order_way == 'desc'}
                                            <img border="0" src="../img/admin/down_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/down.gif">
                                        {/if}
                                    </a>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=name&SenderAddressOrderWay=asc">
                                        {if $order_by == 'name' && $order_way == 'asc'}
                                            <img border="0" src="../img/admin/up_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/up.gif">
                                        {/if}
                                    </a>
                                </th>
                                <th class="center">
									<span class="title_box">
										{l s='City' mod='dpdpoland'}
									</span>
                                    <br>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=city&SenderAddressOrderWay=desc">
                                        {if $order_by == 'city' && $order_way == 'desc'}
                                            <img border="0" src="../img/admin/down_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/down.gif">
                                        {/if}
                                    </a>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=city&SenderAddressOrderWay=asc">
                                        {if $order_by == 'city' && $order_way == 'asc'}
                                            <img border="0" src="../img/admin/up_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/up.gif">
                                        {/if}
                                    </a>
                                </th>
                                <th class="center">
									<span class="title_box">
										{l s='Email' mod='dpdpoland'}
									</span>
                                    <br>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=email&SenderAddressOrderWay=desc">
                                        {if $order_by == 'email' && $order_way == 'desc'}
                                            <img border="0" src="../img/admin/down_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/down.gif">
                                        {/if}
                                    </a>
                                    <a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=email&SenderAddressOrderWay=asc">
                                        {if $order_by == 'email' && $order_way == 'asc'}
                                            <img border="0" src="../img/admin/up_d.gif">
                                        {else}
                                            <img border="0" src="../img/admin/up.gif">
                                        {/if}
                                    </a>
                                </th>
								<th class="center">
                                    <span class="title_box">
                                        {l s='Actions' mod='dpdpoland'}<br>&nbsp;
                                    </span>
                                    <br>
                                </th>
                            </tr>
                            <tr class="nodrag nodrop filter row_hover filter-row">
								<td class="center">
                                    <input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_id_sender_address') && Context::getContext()->cookie->SenderAddressFilter_id_sender_address}{Context::getContext()->cookie->SenderAddressFilter_id_sender_address}{/if}" name="SenderAddressFilter_id_sender_address" class="filter">
                                </td>
                                <td class="center">
                                    <input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_alias') && Context::getContext()->cookie->SenderAddressFilter_alias}{Context::getContext()->cookie->SenderAddressFilter_alias}{/if}" name="SenderAddressFilter_alias" class="filter">
                                </td>
                                <td class="center">
                                    <input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_company') && Context::getContext()->cookie->SenderAddressFilter_company}{Context::getContext()->cookie->SenderAddressFilter_company}{/if}" name="SenderAddressFilter_company" class="filter">
                                </td>
                                <td class="center">
                                    <input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_name') && Context::getContext()->cookie->SenderAddressFilter_name}{Context::getContext()->cookie->SenderAddressFilter_name}{/if}" name="SenderAddressFilter_name" class="filter">
                                </td>
                                <td class="center">
                                    <input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_city') && Context::getContext()->cookie->SenderAddressFilter_city}{Context::getContext()->cookie->SenderAddressFilter_city}{/if}" name="SenderAddressFilter_city" class="filter">
                                </td>
                                <td class="center">
                                    <input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_email') && Context::getContext()->cookie->SenderAddressFilter_email}{Context::getContext()->cookie->SenderAddressFilter_email}{/if}" name="SenderAddressFilter_email" class="filter">
                                </td>
								<td class="center">
                                    --
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            {if isset($table_data) && $table_data}
                                {section name=ii loop=$table_data}
                                    <tr class="row_hover" id="tr_{$smarty.section.ii.index|escape:'htmlall':'UTF-8' + 1}_{$table_data[ii].id_sender_address|escape:'htmlall':'UTF-8'}_0">
                                        <td class="center">
                                            {if $table_data[ii].id_sender_address}
                                                {$table_data[ii].id_sender_address|escape:'htmlall':'UTF-8'}
                                            {else}
                                                --
                                            {/if}
                                        </td>
                                        <td class="center">
                                            {if $table_data[ii].alias}
                                                {$table_data[ii].alias|escape:'htmlall':'UTF-8'}
                                            {else}
                                                --
                                            {/if}
                                        </td>
                                        <td class="center">
                                            {if $table_data[ii].company}
                                                {$table_data[ii].company|escape:'htmlall':'UTF-8'}
                                            {else}
                                                --
                                            {/if}
                                        </td>
                                        <td class="center">
                                            {if $table_data[ii].name}
                                                {$table_data[ii].name|escape:'htmlall':'UTF-8'}
                                            {else}
                                                --
                                            {/if}
                                        </td>
                                        <td class="center">
                                            {if $table_data[ii].city}
                                                {$table_data[ii].city|escape:'htmlall':'UTF-8'}
                                            {else}
                                                --
                                            {/if}
                                        </td>
                                        <td class="center">
                                            {if $table_data[ii].email}
                                                {$table_data[ii].email|escape:'htmlall':'UTF-8'}
                                            {else}
                                                --
                                            {/if}
                                        </td>
										<td class="center sender_address-list-buttons">
                                            <a title="{l s='Edit' mod='dpdpoland'}" href="{$form_url|escape:'htmlall':'UTF-8'}&editSenderAddress&id_sender_address={$table_data[ii].id_sender_address|escape:'htmlall':'UTF-8'}">
                                                <img alt="{l s='Edit sender address' mod='dpdpoland'}" src="../img/admin/edit.gif">
                                            </a>
                                            <a title="{l s='Delete' mod='dpdpoland'}" href="{$full_url|escape:'htmlall':'UTF-8'}&deleteSenderAddress&id_sender_address={$table_data[ii].id_sender_address|escape:'htmlall':'UTF-8'}">
                                                <img alt="{l s='Delete sender address' mod='dpdpoland'}" src="../img/admin/delete.gif">
                                            </a>
                                        </td>
                                    </tr>
                                {/section}
                            {else}
                                <tr>
                                    <td colspan="7" class="center">
                                        {l s='There are no addresses yet' mod='dpdpoland'}
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</form>