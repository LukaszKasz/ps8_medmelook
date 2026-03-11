{*<pre>
{$data|print_r}
</pre>*}
<tr id='package_{$uniq}' class='package' data-id='{$uniq}'>
	<td>
		<div class="message">Trwa przygotowywanie przesyłki.</div>
		<input type="checkbox" value="{$uniq}" name="checked" checked="checked"/>
	</td>
	<td>
		<input type="hidden" value="{$data.id_order}" name="id_order" class='param'/>
		<input type="hidden" value="{$uniq}" name="uniq" class='param'/>
		<input type="hidden" value="{$data.service}" name="service" class='param'/>
		<input type="hidden" value="{$data.is_locker|intval}" name="is_locker" class='param'/>
		<input type="hidden" value="0" name="id_shipment" class='param'/>
		{$data.template}
	</td>
	<td>
		<div class='tip' title='{$data.service_name}'>{$data.service_logo}</div>
	</td>

	<td>
		<div class='row'>
			<div class='col-sm-6'>
				<input type='text' class='param' value="{$data.firstname}" name='firstname' placeholder='{l s='Firstname' mod='sensbitpaczkawruchu'}'/>
			</div>
			<div class='col-sm-6'>
				<input type='text' class='param' value="{$data.lastname}"  name='lastname' placeholder='{l s='Lastname' mod='sensbitpaczkawruchu'}'/>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-6'>
				<input class='param form-control' name="email" type="email" value="{$data.email}" placeholder="{l s='Email' mod='sensbitpaczkawruchu'}" />
			</div>
			<div class='col-sm-6'>
				<input class='param form-control' name="phone" type="text" value="{if empty($data.phone_mobile)}{$data.phone}{else}{$data.phone_mobile}{/if}" placeholder="{l s='Phone' mod='sensbitpaczkawruchu'}"/>
			</div>
		</div>

	</td>
	<td>
		{if $data.is_locker}
			<div class="input-group">
				<input type='text' name='target_point' value="{$data.point}" class='param package_{$uniq}_target_point' placeholder="{l s='Target point' mod='sensbitpaczkawruchu'}"/>
				<span class="input-group-addon"><button title="{l s='Select from map' mod='sensbitpaczkawruchu'}" class="btn btn-xs btn-warning tip" onclick="sensbitpaczkawruchu.openMap('.package_{$uniq}_target_point', '{$data.city}, {$data.address1}');return false;">{l s='Map' mod='sensbitpaczkawruchu'}</button></span>
			</div>

		{else}
			<div class='address'>
				<div style="display:inline">
					{if $data.company}
						{$data.company} <br/>
					{/if}
					{$data.firstname} {$data.lastname}
					<br/>
					{$data.address1} {$data.address2}<br/>
					{$data.postcode} {$data.city}
				</div>
				<button class="btn btn-default btn-xs edit-address tip" title="{l s='Edit address' mod='sensbitpaczkawruchu'}"><i class="icon-edit"></i></button>
			</div>
			<div class='address-edit' style="display:none">
				<input type='text' class='param' value="{$data.company}" name='company' placeholder='{l s='Company' mod='sensbitpaczkawruchu'}'/>
				<div class='row'>
					<div class='col-sm-6'>
						<input type='text' class='param' value="{$data.firstname}" name='firstname' placeholder='{l s='Firstname' mod='sensbitpaczkawruchu'}'/>
					</div>
					<div class='col-sm-6'>
						<input type='text' class='param' value="{$data.lastname}"  name='lastname' placeholder='{l s='Lastname' mod='sensbitpaczkawruchu'}'/>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-6'>
						<input type='text' class='param' value="{$data.address1}"  name='street' placeholder='{l s='Street' mod='sensbitpaczkawruchu'}'/>
					</div>
					<div class='col-sm-6'>
						<input type='text' class='param' value="{$data.address2}"  name='building_number' placeholder='{l s='Building number' mod='sensbitpaczkawruchu'}'/>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-6'>
						<input type='text' class='param' value="{$data.postcode}"  name='postcode' placeholder='{l s='Postcode' mod='sensbitpaczkawruchu'}'/>
					</div>
					<div class='col-sm-6'>
						<input type='text' class='param' value="{$data.city}"  name='city' placeholder='{l s='City' mod='sensbitpaczkawruchu'}'/>
					</div>
				</div>
				<button class="btn btn-success btn-xs save-address tip" title="{l s='Save address' mod='sensbitpaczkawruchu'}"><i class="icon-check"></i></button>

			</div>
		{/if}
	</td>
	<td>
		{if isset($data.options.is_cod)}
			<div class="input-group">
				<span class="input-group-addon"><input class='param' placeholder="{l s='Is COD' mod='sensbitpaczkawruchu'}" name="is_cod" type="checkbox"{if $data.options.is_cod} checked='checked'{/if}/></span>
				<input class='param' placeholder="{l s='COD value' mod='sensbitpaczkawruchu'}" name="cod_value" type="text" value="{$data.total_paid_tax_incl|round:2}"/>
			</div>
			<input class='param form-control' name="transfer_description" type="text" value="{$data.options.transfer_description}" placeholder="{l s='Transfer description' mod='sensbitpaczkawruchu'}"/>
		{else}
			-
		{/if}
	</td>
	<td>
		{if isset($data.options.is_insurance)}
			<div class="input-group">
				<span class="input-group-addon"><input class='param' placeholder="{l s='Is Insurance' mod='sensbitpaczkawruchu'}" name="is_insurance" type="checkbox"{if $data.options.is_insurance} checked='checked'{/if}/></span>
				<input class='param' placeholder="{l s='Insurance value' mod='sensbitpaczkawruchu'}" name="insurance_value" type="text" value="{$data.total_paid_tax_incl|round:2}"/>
			</div>
		{else}
			-
		{/if}
	</td>
	<td>
		<input class='param' name="reference" type="text" value="{$data.custom_reference}" placeholder="{l s='Reference' mod='sensbitpaczkawruchu'}"/>
	</td>
	<td>
		<select name="size" class='param' title="{l s='Size' mod='sensbitpaczkawruchu'}" style="width: 70px;">
			{foreach $sizes as $size}
				<option value="{$size.id}"{if (isset($data.options.is_mini) && $data.options.is_mini && $size.id=='MINI') || (isset($data.options.size) && $data.options.size == $size.id)} selected='selected'{/if}>{$size.label}</option>
			{/foreach}
		</select>
	</td>
	<td>
		<button class="btn btn-xs btn-danger remove-package"><i class="icon-remove"></i></button>
	</td>
</tr>