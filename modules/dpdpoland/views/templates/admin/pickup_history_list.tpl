<script>
    var dpdpoland_packages_ids = "{Context::getContext()->cookie->dpdpoland_packages_ids|escape:'htmlall':'UTF-8'}";

    $(document).ready(function () {
        $("table .datepicker").datepicker({
            prevText: '',
            nextText: '',
            dateFormat: 'yy-mm-dd'
        });

        $('table#packages_list .filter').keypress(function (event) {
            formSubmit(event, 'submitFilterButtonPackages');
        });

        if (dpdpoland_packages_ids) {
            window.location = window.location + '&printManifest';
        }
        $('table#sender_address_list .filter').keypress(function (event) {
            formSubmit(event, 'submitFilterButtonPickupHistory');
        })
    });
</script>


<form class="form" action="{$full_url|escape:'htmlall':'UTF-8'}" method="post">
    <input type="hidden" value="0" name="submitFilterPickupHistory" id="submitFilterPickupHistory">
    <table id="sender_address_list" name="list_table" class="table_grid">
        <tbody>
        <tr>
            <td class="bottom">
                    <span class="float-left">
                        {if $page > 1}
                            <input type="image" src="../img/admin/list-prev2.gif"
                                   onclick="getE('submitFilterPickupHistory').value=1"/>

&nbsp;


                            <input type="image" src="../img/admin/list-prev.gif"
                                   onclick="getE('submitFilterPickupHistory').value=;{$page|escape:'htmlall':'UTF-8' - 1}"/>
                        {/if}
                        {l s='Page' mod='dpdpoland'} <b>{$page|escape:'htmlall':'UTF-8'}</b> / {$total_pages|escape:'htmlall':'UTF-8'}
                        {if $page < $total_pages}
                            <input type="image" src="../img/admin/list-next.gif"
                                   onclick="getE('submitFilterPickupHistory').value=;{$page|escape:'htmlall':'UTF-8' + 1}"/>

&nbsp;


                            <input type="image" src="../img/admin/list-next2.gif"
                                   onclick="getE('submitFilterPickupHistory').value=;{$total_pages|escape:'htmlall':'UTF-8'}"/>
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
                        <input type="submit" class="button" value="{l s='Filter' mod='dpdpoland'}"
                               name="submitFilterButtonPickupHistory" id="submitFilterButtonPickupHistory">
                        <input type="submit" class="button" value="{l s='Reset' mod='dpdpoland'}"
                               name="submitResetPickupHistory">
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
										{l s='Order number' mod='dpdpoland'}
									</span>
                            <br>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=order_number&PickupHistoryOrderWay=desc">
                                {if $order_by == 'order_number' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=order_number&PickupHistoryOrderWay=asc">
                                {if $order_by == 'order_number' && $order_way == 'asc'}
                                    <img border="0" src="../img/admin/up_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/up.gif">
                                {/if}
                            </a>
                        </th>
                        <th class="center">
									<span class="title_box">
										{l s='Address' mod='dpdpoland'}
									</span>
                            <br>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_address&PickupHistoryOrderWay=desc">
                                {if $order_by == 'sender_address' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_address&PickupHistoryOrderWay=asc">
                                {if $order_by == 'sender_address' && $order_way == 'asc'}
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
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_company&PickupHistoryOrderWay=desc">
                                {if $order_by == 'sender_company' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_company&PickupHistoryOrderWay=asc">
                                {if $order_by == 'sender_company' && $order_way == 'asc'}
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
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_name&PickupHistoryOrderWay=desc">
                                {if $order_by == 'sender_name' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_name&PickupHistoryOrderWay=asc">
                                {if $order_by == 'sender_name' && $order_way == 'asc'}
                                    <img border="0" src="../img/admin/up_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/up.gif">
                                {/if}
                            </a>
                        </th>
                        <th class="center">
									<span class="title_box">
										{l s='Phone' mod='dpdpoland'}
									</span>
                            <br>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_phone&PickupHistoryOrderWay=desc">
                                {if $order_by == 'sender_phone' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=sender_phone&PickupHistoryOrderWay=asc">
                                {if $order_by == 'sender_phone' && $order_way == 'asc'}
                                    <img border="0" src="../img/admin/up_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/up.gif">
                                {/if}
                            </a>
                        </th>
                        <th class="center">
									<span class="title_box">
										{l s='Type' mod='dpdpoland'}
									</span>
                            <br>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=type&PickupHistoryOrderWay=desc">
                                {if $order_by == 'type' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=type&PickupHistoryOrderWay=asc">
                                {if $order_by == 'type' && $order_way == 'asc'}
                                    <img border="0" src="../img/admin/up_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/up.gif">
                                {/if}
                            </a>
                        </th>
                        <th class="center">
									<span class="title_box">
										{l s='Envelope' mod='dpdpoland'}
									</span>
                            <br>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=envelope&PickupHistoryOrderWay=desc">
                                {if $order_by == 'envelope' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=envelope&PickupHistoryOrderWay=asc">
                                {if $order_by == 'envelope' && $order_way == 'asc'}
                                    <img border="0" src="../img/admin/up_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/up.gif">
                                {/if}
                            </a>
                        </th>
                        <th class="center">
									<span class="title_box">
										{l s='Package' mod='dpdpoland'}
									</span>
                            <br>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=package&PickupHistoryOrderWay=desc">
                                {if $order_by == 'package' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=package&PickupHistoryOrderWay=asc">
                                {if $order_by == 'package' && $order_way == 'asc'}
                                    <img border="0" src="../img/admin/up_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/up.gif">
                                {/if}
                            </a>
                        </th>
                        <th class="center">
									<span class="title_box">
										{l s='Pallet' mod='dpdpoland'}
									</span>
                            <br>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pallet&PickupHistoryOrderWay=desc">
                                {if $order_by == 'pallet' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pallet&PickupHistoryOrderWay=asc">
                                {if $order_by == 'pallet' && $order_way == 'asc'}
                                    <img border="0" src="../img/admin/up_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/up.gif">
                                {/if}
                            </a>
                        </th>
                        <th class="center">
									<span class="title_box">
										{l s='Pickup time' mod='dpdpoland'}
									</span>
                            <br>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pickup_time&PickupHistoryOrderWay=desc">
                                {if $order_by == 'pickup_time' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pickup_time&PickupHistoryOrderWay=asc">
                                {if $order_by == 'pickup_time' && $order_way == 'asc'}
                                    <img border="0" src="../img/admin/up_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/up.gif">
                                {/if}
                            </a>
                        </th>
                        <th class="center">
									<span class="title_box">
										{l s='Pickup date' mod='dpdpoland'}
									</span>
                            <br>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pickup_date&PickupHistoryOrderWay=desc">
                                {if $order_by == 'pickup_date' && $order_way == 'desc'}
                                    <img border="0" src="../img/admin/down_d.gif">
                                {else}
                                    <img border="0" src="../img/admin/down.gif">
                                {/if}
                            </a>
                            <a href="{$full_url|escape:'htmlall':'UTF-8'}&PickupHistoryOrderBy=pickup_date&PickupHistoryOrderWay=asc">
                                {if $order_by == 'pickup_date' && $order_way == 'asc'}
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
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_order_number') && Context::getContext()->cookie->PickupHistoryFilter_order_number}{Context::getContext()->cookie->PickupHistoryFilter_order_number}{/if}"
                                   name="PickupHistoryFilter_order_number" class="filter">
                        </td>

                        <td class="center">
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_sender_address') && Context::getContext()->cookie->PickupHistoryFilter_sender_address}{Context::getContext()->cookie->PickupHistoryFilter_sender_address}{/if}"
                                   name="PickupHistoryFilter_sender_address" class="filter">
                        </td>

                        <td>
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_sender_company') && Context::getContext()->cookie->PickupHistoryFilter_sender_company}{Context::getContext()->cookie->PickupHistoryFilter_sender_company}{/if}"
                                   name="PickupHistoryFilter_sender_company" class="filter">
                        </td>

                        <td>
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_sender_name') && Context::getContext()->cookie->PickupHistoryFilter_sender_name}{Context::getContext()->cookie->PickupHistoryFilter_sender_name}{/if}"
                                   name="PickupHistoryFilter_sender_name" class="filter">
                        </td>

                        <td>
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_sender_phone') && Context::getContext()->cookie->PickupHistoryFilter_sender_phone}{Context::getContext()->cookie->PickupHistoryFilter_sender_phone}{/if}"
                                   name="PickupHistoryFilter_sender_phone" class="filter">
                        </td>
                        <td>
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_type') && Context::getContext()->cookie->PickupHistoryFilter_type}{Context::getContext()->cookie->PickupHistoryFilter_type}{/if}"
                                   name="PickupHistoryFilter_type" class="filter">
                        </td>
                        <td>
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_envelope') && Context::getContext()->cookie->PickupHistoryFilter_envelope}{Context::getContext()->cookie->PickupHistoryFilter_envelope}{/if}"
                                   name="PickupHistoryFilter_envelope" class="filter">
                        </td>
                        <td>
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_package') && Context::getContext()->cookie->PickupHistoryFilter_package}{Context::getContext()->cookie->PickupHistoryFilter_package}{/if}"
                                   name="PickupHistoryFilter_package" class="filter">
                        </td>
                        <td>
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_pallet') && Context::getContext()->cookie->PickupHistoryFilter_pallet}{Context::getContext()->cookie->PickupHistoryFilter_pallet}{/if}"
                                   name="PickupHistoryFilter_pallet" class="filter">
                        </td>
                        <td>
                            <input type="text"
                                   value="{if Context::getContext()->cookie->__isset('PickupHistoryFilter_pickup_time') && Context::getContext()->cookie->PickupHistoryFilter_pickup_time}{Context::getContext()->cookie->PickupHistoryFilter_pickup_time}{/if}"
                                   name="PickupHistoryFilter_pickup_time" class="filter">
                        </td>
                        <td class="right">
                            {l s='From' mod='dpdpoland'} <input type="text" value=""
                                                                name="PickupHistoryFilter_pickup_date[0]"
                                                                id="PackagesFilter_pickup_date_0"
                                                                class="filter datepicker">
                            <br>
                            {l s='To' mod='dpdpoland'} <input type="text" value=""
                                                              name="PickupHistoryFilter_pickup_date[1]"
                                                              id="PackagesFilter_pickup_date_1"
                                                              class="filter datepicker">
                        </td>
                        <td>
                            --
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    {if isset($table_data) && $table_data}
                        {section name=ii loop=$table_data}
                            <tr class="row_hover"
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
                                <td class="center sender_address-list-buttons"></td>
                            </tr>
                        {/section}
                    {else}
                        <tr>
                            <td colspan="12" class="center">
                                {l s='No records found' mod='dpdpoland'}
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