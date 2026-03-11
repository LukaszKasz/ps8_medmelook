<script>
    $(document).ready(function () {
        $("table#pickup_history_list .datepicker").datepicker({
            prevText: '',
            nextText: '',
            dateFormat: 'yy-mm-dd'
        });

        $('table#pickup_history_list .filter').keypress(function (event) {
            formSubmit(event, 'submitFilterButtonPickupHistory');
        });

        $('table#packages_list .filter').keypress(function (e) {
            var key = (e.keyCode ? e.keyCode : e.which);
            if (key == 13) {
                e.preventDefault();
                formSubmit(event, 'submitFilterButtonPickupHistory');
            }
        });

        $('#submitFilterButtonPickupHistory').click(function () {
            $('#submitFilterPickupHistory').val(1);
            $('#pickup_historyes').submit();
        });

    });
</script>

<form id="pickup_historyes" class="form-horizontal clearfix" action="{$full_url|escape:'htmlall':'UTF-8'}"
      method="post">
    <input type="hidden" value="0" name="submitFilterButtonPickupHistory" id="submitFilterPickupHistory">
    <div class="panel col-lg-12">
        <div class="panel-heading">
            {l s='Pickup history' mod='dpdpoland'}
            <span class="badge">
				{$list_total|escape:'htmlall':'UTF-8'}
			</span>
        </div>
        <div class="table-responsive clearfix">
            <table class="table" id="pickup_history_list" name="list_table">
                <thead>
                <tr class="nodrag nodrop">

                    <th class="">
							<span class="title_box{if $order_by == 'order_number'} active{/if}">{l s='Pickup number' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=order_number&PickupHistoryOrderWay=desc"{if $order_by == 'order_number' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=order_number&PickupHistoryOrderWay=asc"{if $order_by == 'order_number' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="">
							<span class="title_box{if $order_by == 'sender_address'} active{/if}">{l s='Address' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_address&PickupHistoryOrderWay=desc"{if $order_by == 'sender_address' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_address&PickupHistoryOrderWay=asc"{if $order_by == 'sender_address' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="left">
							<span class="title_box{if $order_by == 'sender_company'} active{/if}">{l s='Company' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_company&PickupHistoryOrderWay=desc"{if $order_by == 'sender_company' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_company&PickupHistoryOrderWay=asc"{if $order_by == 'sender_company' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="">
							<span class="title_box{if $order_by == 'sender_name'} active{/if}">{l s='Name' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_name&PickupHistoryOrderWay=desc"{if $order_by == 'sender_name' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_name&PickupHistoryOrderWay=asc"{if $order_by == 'sender_name' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="">
							<span class="title_box{if $order_by == 'sender_phone'} active{/if}">{l s='Phone' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_phone&PickupHistoryOrderWay=desc"{if $order_by == 'sender_phone' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_phone&PickupHistoryOrderWay=asc"{if $order_by == 'sender_phone' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="">
							<span class="title_box{if $order_by == 'type'} active{/if}">{l s='Type' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=type&PickupHistoryOrderWay=desc"{if $order_by == 'type' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=type&PickupHistoryOrderWay=asc"{if $order_by == 'type' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="">
							<span class="title_box{if $order_by == 'envelope'} active{/if}">{l s='Envelope' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=envelope&PickupHistoryOrderWay=desc"{if $order_by == 'envelope' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=envelope&PickupHistoryOrderWay=asc"{if $order_by == 'envelope' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="">
							<span class="title_box{if $order_by == 'package'} active{/if}">{l s='Package' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=package&PickupHistoryOrderWay=desc"{if $order_by == 'package' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=package&PickupHistoryOrderWay=asc"{if $order_by == 'package' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="">
							<span class="title_box{if $order_by == 'pallet'} active{/if}">{l s='Pallet' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pallet&PickupHistoryOrderWay=desc"{if $order_by == 'pallet' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pallet&PickupHistoryOrderWay=asc"{if $order_by == 'pallet' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="">
							<span class="title_box{if $order_by == 'pickup_time'} active{/if}">{l s='Pickup time' mod='dpdpoland'}
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pickup_time&PickupHistoryOrderWay=desc"{if $order_by == 'pickup_time' && $order_way == 'desc'} class="active"{/if}><i
                                            class="icon-caret-down"></i></a>
								<a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pickup_time&PickupHistoryOrderWay=asc"{if $order_by == 'pickup_time' && $order_way == 'asc'} class="active"{/if}><i
                                            class="icon-caret-up"></i></a>
							</span>
                    </th>
                    <th class="">
                        <span class="title_box{if $order_by == 'pickup_date'} active{/if}">
								{l s='Pickup date' mod='dpdpoland'}
								<a{if $order_by == 'pickup_date' && $order_way == 'desc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pickup_date&PickupHistoryOrderWay=desc">
									<i class="icon-caret-down"></i>
								</a>
								<a{if $order_by == 'pickup_date' && $order_way == 'asc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pickup_date&PickupHistoryOrderWay=asc">
									<i class="icon-caret-up"></i>
								</a>
							</span>
                    </th>
                    <th></th>
                </tr>

                <tr class="nodrag nodrop filter row_hover">

                    <th class="center">
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_order_number') && Context::getContext()->cookie->PickupHistoryFilter_order_number}{Context::getContext()->cookie->PickupHistoryFilter_order_number}{/if}"
                               name="PickupHistoryFilter_order_number" class="filter">
                    </th>

                    <th class="center">
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_sender_address') && Context::getContext()->cookie->PickupHistoryFilter_sender_address}{Context::getContext()->cookie->PickupHistoryFilter_sender_address}{/if}"
                               name="PickupHistoryFilter_sender_address" class="filter">
                    </th>

                    <th>
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_sender_company') && Context::getContext()->cookie->PickupHistoryFilter_sender_company}{Context::getContext()->cookie->PickupHistoryFilter_sender_company}{/if}"
                               name="PickupHistoryFilter_sender_company" class="filter">
                    </th>

                    <th>
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_sender_name') && Context::getContext()->cookie->PickupHistoryFilter_sender_name}{Context::getContext()->cookie->PickupHistoryFilter_sender_name}{/if}"
                               name="PickupHistoryFilter_sender_name" class="filter">
                    </th>

                    <th>
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_sender_phone') && Context::getContext()->cookie->PickupHistoryFilter_sender_phone}{Context::getContext()->cookie->PickupHistoryFilter_sender_phone}{/if}"
                               name="PickupHistoryFilter_sender_phone" class="filter">
                    </th>
                    <th>
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_type') && Context::getContext()->cookie->PickupHistoryFilter_type}{Context::getContext()->cookie->PickupHistoryFilter_type}{/if}"
                               name="PickupHistoryFilter_type" class="filter">
                    </th>
                    <th>
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_envelope') && Context::getContext()->cookie->PickupHistoryFilter_envelope}{Context::getContext()->cookie->PickupHistoryFilter_envelope}{/if}"
                               name="PickupHistoryFilter_envelope" class="filter">
                    </th>
                    <th>
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_package') && Context::getContext()->cookie->PickupHistoryFilter_package}{Context::getContext()->cookie->PickupHistoryFilter_package}{/if}"
                               name="PickupHistoryFilter_package" class="filter">
                    </th>
                    <th>
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_pallet') && Context::getContext()->cookie->PickupHistoryFilter_pallet}{Context::getContext()->cookie->PickupHistoryFilter_pallet}{/if}"
                               name="PickupHistoryFilter_pallet" class="filter">
                    </th>
                    <th>
                        <input type="text"
                               value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_pickup_time') && Context::getContext()->cookie->PickupHistoryFilter_pickup_time}{Context::getContext()->cookie->PickupHistoryFilter_pickup_time}{/if}"
                               name="PickupHistoryFilter_pickup_time" class="filter">
                    </th>
                    <th class="text-right">
                        <div class="date_range row">
                            <div class="input-group fixed-width-md">
                                <input type="text" placeholder="{l s='From' mod='dpdpoland'}"
                                       name="PickupHistoryFilter_pickup_date[0]" id="PickupHistoryFilter_pickup_date_0"
                                       class="filter datepicker">
                                <span class="input-group-addon">
										<i class="icon-calendar"></i>
									</span>
                            </div>
                            <div class="input-group fixed-width-md">
                                <input type="text" placeholder="{l s='To' mod='dpdpoland'}"
                                       name="PickupHistoryFilter_pickup_date[1]" id="PickupHistoryFilter_pickup_date_1"
                                       class="filter datepicker">
                                <span class="input-group-addon">
										<i class="icon-calendar"></i>
									</span>
                            </div>
                        </div>
                    </th>


                    <th class="actions text-center">
							<span class="pull-right">
								<button id="submitFilterButtonPickupHistory" class="btn btn-default"
                                        data-list-id="pickup_history_list" name="submitFilter" type="submit">
									<i class="icon-search"></i>
									{l s='Search' mod='dpdpoland'}
								</button>
								{if $filters_has_value}
                                    <button type="submit" name="submitResetPickupHistory" class="btn btn-warning">
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
                        <tr class="odd"
                            id="tr_{$smarty.section.ii.index|escape:'htmlall':'UTF-8' + 1}_{$table_data[ii].id_pickup_history|escape:'htmlall':'UTF-8'}_0">

                            <td class="center">
                                {if $table_data[ii].order_number}
                                    {$table_data[ii].order_number|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="text-left">
                                {if $table_data[ii].sender_address}
                                    {$table_data[ii].sender_address|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="text-left">
                                {if $table_data[ii].sender_company}
                                    {$table_data[ii].sender_company|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="text-left">
                                {if $table_data[ii].sender_name}
                                    {$table_data[ii].sender_name|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="center">
                                {if $table_data[ii].sender_phone}
                                    {$table_data[ii].sender_phone|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="center">
                                {if $table_data[ii].type}
                                    {if $table_data[ii].type == 'DOMESTIC'}
                                        {l s='DOMESTIC' mod='dpdpoland'}
                                    {elseif $table_data[ii].type  == 'INTERNATIONAL'}
                                        {l s='INTERNATIONAL' mod='dpdpoland'}
                                    {/if}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="center">
                                {if $table_data[ii].envelope}
                                    {$table_data[ii].envelope|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="center">
                                {if $table_data[ii].package}
                                    {$table_data[ii].package|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="center">
                                {if $table_data[ii].pallet}
                                    {$table_data[ii].pallet|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="center">
                                {if $table_data[ii].pickup_time}
                                    {$table_data[ii].pickup_time|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="center">
                                {if $table_data[ii].pickup_date}
                                    {$table_data[ii].pickup_date|date_format:'%Y-%m-%d'|escape:'htmlall':'UTF-8'}
                                {else}
                                    --
                                {/if}
                            </td>
                            <td class="text-right sender-address-list-buttons"></td>
                        </tr>
                    {/section}
                {else}
                    <tr>
                        <td colspan="12" class="list-empty">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No records found' mod='dpdpoland'}
                            </div>
                        </td>
                    </tr>
                {/if}
                </tbody>
            </table>
            {include file=$smarty.const._DPDPOLAND_TPL_DIR_|cat:'admin/_pagination_16.tpl' identifier='PickupHistory'}
        </div>
    </div>
</form>