{foreach from=$order->getShipping() item=line}
	<tr>
		<td>{dateFormat date=$line.date_add full=true}</td>
		<td>{$line.type|escape:'htmlall':'UTF-8'}</td>
		<td>{$line.carrier_name|escape:'htmlall':'UTF-8'}</td>
		<td class="weight">{$line.weight|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')}</td>
		<td class="center">
			{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}
				{displayPrice price=$line.shipping_cost_tax_incl currency=$currency->id}
			{else}
				{displayPrice price=$line.shipping_cost_tax_excl currency=$currency->id}
			{/if}
		</td>
		<td>
			<span class="shipping_number_show">{if $line.url && $line.tracking_number}<a class="_blank" href="{$line.url|replace:'@':$line.tracking_number}">{$line.tracking_number|escape:'htmlall':'UTF-8'}</a>{else}{$line.tracking_number|escape:'htmlall':'UTF-8'}{/if}</span>
		</td>
		<td>
			{if $line.can_edit}
				<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
					<span class="shipping_number_edit" style="display:none;">
						<input type="hidden" name="id_order_carrier" value="{$line.id_order_carrier|escape:'htmlall':'UTF-8'}" />
						<input type="text" name="tracking_number" value="{$line.tracking_number|escape:'htmlall':'UTF-8'}" />
						<button type="submit" class="btn btn-default" name="submitShippingNumber">
							<i class="icon-ok"></i>
							{l s='Update' mod='sensbitpaczkawruchu'}
						</button>
					</span>
					<a href="#" class="edit_shipping_number_link btn btn-default">
						<i class="icon-pencil"></i>
						{l s='Edit' mod='sensbitpaczkawruchu'}
					</a>
					<a href="#" class="cancel_shipping_number_link btn btn-default" style="display: none;">
						<i class="icon-remove"></i>
						{l s='Cancel' mod='sensbitpaczkawruchu'}
					</a>
				</form>
			{/if}

		</td>
	</tr>
{/foreach}

{if isset($trackmultiplecarriers) && $trackmultiplecarriers}
	{foreach from=Trackmultiplecarriers::getTrackMultipleCarriersShipping($order->id) item=line}
		<tr id="trackmultiplecarriers_{$line.id_tracknum|intval}">
			<td>{dateFormat date=$line.date_add full=true}</td>
			<td>{$line.type|escape:'htmlall':'UTF-8'}</td>
			<td>{$line.carrier_name|escape:'htmlall':'UTF-8'}</td>
			<td class="weight">-</td>
			<td class="center">-</td>
			<td>
				<span class="shipping_number_show">{if $line.url && $line.tracking_number}<a class="_blank" href="{$line.url|replace:'@':$line.tracking_number|escape:'htmlall':'UTF-8'}">{$line.tracking_number|escape:'htmlall':'UTF-8'}</a>{else}{$line.tracking_number|escape:'htmlall':'UTF-8'}{/if}</span>
			</td>
			<td>
				{if $line.can_edit}
					<a href="{$line.id_tracknum|intval}" class="btn btn-primary addons4presta_trackmultiplecarriers_add_btn" rel="1" data-toggle="modal" data-target="#trackMultipleCarriersModal">{l s='Edit' mod='sensbitpaczkawruchu'}</a>
					<a href="{$line.id_tracknum|intval}" class="btn btn-default tmc-delete" rel="2">{l s='Delete' mod='sensbitpaczkawruchu'}</a>
				{/if}
			</td>
		</tr>
	{/foreach}
{/if}