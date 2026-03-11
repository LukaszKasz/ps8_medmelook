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
		$("table#sender_address_list .datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

		$('table#sender_address_list .filter').keypress(function(event){
			formSubmit(event, 'submitFilterButtonSenderAddress');
		});

		$('table#packages_list .filter').keypress(function(e){
			var key = (e.keyCode ? e.keyCode : e.which);
			if (key == 13)
			{
				e.preventDefault();
				formSubmit(event, 'submitFilterButtonSenderAddress');
			}
		});

		$('#submitFilterButtonSenderAddress').click(function() {
			$('#submitFilterSenderAddress').val(1);
			$('#sender_addresses').submit();
		});

	});
</script>

<form id="sender_addresses" class="form-horizontal clearfix" action="{$full_url|escape:'htmlall':'UTF-8'}" method="post">
    <input type="hidden" value="0" name="submitFilterButtonSenderAddress" id="submitFilterSenderAddress">
	<div class="panel col-lg-12">
		<div class="panel-heading">
			{l s='Sender addresses' mod='dpdpoland'}
			<span class="badge">
				{$list_total|escape:'htmlall':'UTF-8'}
			</span>
			<span class="panel-heading-action">
				<a id="desc-sender_address-new" class="list-toolbar-btn" href="{$form_url|escape:'htmlall':'UTF-8'}">
					<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Add new' mod='dpdpoland'}" data-html="true" data-placement="top">
						<i class="process-icon-new"></i>
					</span>
				</a>
			</span>
		</div>
		<div class="table-responsive clearfix">
			<table class="table" id="sender_address_list" name="list_table">
				<thead>
					<tr class="nodrag nodrop">
						<th class="center">
							<span class="title_box{if $order_by == 'id_sender_address'} active{/if}">{l s='Sender address ID' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=id_sender_address&SenderAddressOrderWay=desc"{if $order_by == 'id_sender_address' && $order_way == 'desc'} class="active"{/if}><i class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=id_sender_address&SenderAddressOrderWay=asc"{if $order_by == 'id_sender_address' && $order_way == 'asc'} class="active"{/if}><i class="icon-caret-up"></i></a>
							</span>
						</th>
						<th class="">
							<span class="title_box{if $order_by == 'alias'} active{/if}">{l s='Alias' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=alias&SenderAddressOrderWay=desc"{if $order_by == 'alias' && $order_way == 'desc'} class="active"{/if}><i class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=alias&SenderAddressOrderWay=asc"{if $order_by == 'alias' && $order_way == 'asc'} class="active"{/if}><i class="icon-caret-up"></i></a>
							</span>
						</th>
						<th class="">
							<span class="title_box{if $order_by == 'company'} active{/if}">{l s='Company' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=company&SenderAddressOrderWay=desc"{if $order_by == 'company' && $order_way == 'desc'} class="active"{/if}><i class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=company&SenderAddressOrderWay=asc"{if $order_by == 'company' && $order_way == 'asc'} class="active"{/if}><i class="icon-caret-up"></i></a>
							</span>
						</th>
						<th class="left">
							<span class="title_box{if $order_by == 'name'} active{/if}">{l s='Name' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=name&SenderAddressOrderWay=desc"{if $order_by == 'name' && $order_way == 'desc'} class="active"{/if}><i class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=name&SenderAddressOrderWay=asc"{if $order_by == 'name' && $order_way == 'asc'} class="active"{/if}><i class="icon-caret-up"></i></a>
							</span>
						</th>
						<th class="">
							<span class="title_box{if $order_by == 'city'} active{/if}">{l s='City' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=city&SenderAddressOrderWay=desc"{if $order_by == 'city' && $order_way == 'desc'} class="active"{/if}><i class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=city&SenderAddressOrderWay=asc"{if $order_by == 'city' && $order_way == 'asc'} class="active"{/if}><i class="icon-caret-up"></i></a>
							</span>
						</th>
						<th class="">
							<span class="title_box{if $order_by == 'email'} active{/if}">{l s='Email' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=email&SenderAddressOrderWay=desc"{if $order_by == 'email' && $order_way == 'desc'} class="active"{/if}><i class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&SenderAddressOrderBy=email&SenderAddressOrderWay=asc"{if $order_by == 'email' && $order_way == 'asc'} class="active"{/if}><i class="icon-caret-up"></i></a>
							</span>
						</th>
						<th></th>
					</tr>

					<tr class="nodrag nodrop filter row_hover">
						<th class="center">
							<input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_id_sender_address') && Context::getContext()->cookie->SenderAddressFilter_id_sender_address}{Context::getContext()->cookie->SenderAddressFilter_id_sender_address}{/if}" name="SenderAddressFilter_id_sender_address" class="filter">
						</th>

						<th class="center">
							<input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_alias') && Context::getContext()->cookie->SenderAddressFilter_alias}{Context::getContext()->cookie->SenderAddressFilter_alias}{/if}" name="SenderAddressFilter_alias" class="filter">
						</th>

						<th class="center">
							<input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_company') && Context::getContext()->cookie->SenderAddressFilter_company}{Context::getContext()->cookie->SenderAddressFilter_company}{/if}" name="SenderAddressFilter_company" class="filter">
						</th>

						<th>
							<input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_name') && Context::getContext()->cookie->SenderAddressFilter_name}{Context::getContext()->cookie->SenderAddressFilter_name}{/if}" name="SenderAddressFilter_name" class="filter">
						</th>

						<th>
							<input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_city') && Context::getContext()->cookie->SenderAddressFilter_city}{Context::getContext()->cookie->SenderAddressFilter_city}{/if}" name="SenderAddressFilter_city" class="filter">
						</th>

						<th>
							<input type="text" value="{if Context::getContext()->cookie->__isset('SenderAddressFilter_email') && Context::getContext()->cookie->SenderAddressFilter_email}{Context::getContext()->cookie->SenderAddressFilter_email}{/if}" name="SenderAddressFilter_email" class="filter">
						</th>

						<th class="actions text-center">
							<span class="pull-right">
								<button id="submitFilterButtonSenderAddress" class="btn btn-default" data-list-id="sender_address_list" name="submitFilter" type="submit">
									<i class="icon-search"></i>
									{l s='Search' mod='dpdpoland'}
								</button>
								{if $filters_has_value}
									<button type="submit" name="submitResetSenderAddress" class="btn btn-warning">
										<i class="icon-eraser"></i> {l s='Reset' mod='dpdpoland'}
									</button>
								{/if}
							</span>
						</th>
					</tr>
				</thead>

				<tbody>
				{if isset($table_data) && $table_data}
					{section name=ii loop=$table_data}
					<tr class="odd" id="tr_{$smarty.section.ii.index|escape:'htmlall':'UTF-8' + 1}_{$table_data[ii].id_sender_address|escape:'htmlall':'UTF-8'}_0">
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
						<td class="text-left">
							{if $table_data[ii].city}
								{$table_data[ii].city|escape:'htmlall':'UTF-8'}
							{else}
								--
							{/if}
						</td>
						<td class="text-left">
                            {if $table_data[ii].email}
                                {$table_data[ii].email|escape:'htmlall':'UTF-8'}
                            {else}
								--
                            {/if}
						</td>
						<td class="text-right sender-address-list-buttons">
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
						<td colspan="7" class="list-empty">
							<div class="list-empty-msg">
								<i class="icon-warning-sign list-empty-icon"></i>
								{l s='No records found' mod='dpdpoland'}
							</div>
						</td>
					</tr>
				{/if}
				</tbody>
			</table>
			{include file=$smarty.const._DPDPOLAND_TPL_DIR_|cat:'admin/_pagination_16.tpl' identifier='SenderAddress'}
		</div>
	</div>
</form>