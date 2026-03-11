<tr id='shipment_{$shipment->id}'>
	<td>
		<div class="message"></div>
		<input type="checkbox" value="{$shipment->id}" checked="checked" class="completed-packs"/>
	</td>
	<td>
		<div class='sensbitpaczkawruchu-tip' title='{$service->getName()}'>{$service->getLogo()}</div>
	</td>
	<td>
		{$shipment->tracking_number}
	</td>
	<td>
		{$status = $shipment->getStatus()}
		<a href='#' onclick="sensbitpaczkawruchu.getPackStatus({$shipment->id});return false" class='sensbitpaczkawruchu-tip sensbitpaczkawruchu-status-checker sensbitpaczkawruchu-pack-status' data-id-shipment='{$shipment->id}' data-autocheck="{$status.autocheck}" title='{l s='Kliknij aby sprawdzić aktualny status przesyłki' mod='sensbitpaczkawruchu'}'>{$status.title}</a>
	</td>
	<td>
		{displayPrice price=$shipment->price}
	</td>
	<td>
		{$shipment->date_add}
	</td>
	<td>
		{$shipment->getEmployeeName()}
	</td>
	<td>
		<button onclick="sensbitpaczkawruchu.printLabels({$shipment->id});return false" class="btn btn-xs btn-success sensbitpaczkawruchu-tip" title="{l s='Print label' mod='sensbitpaczkawruchu'}"><i class="icon-print"></i></button>
			{if Configuration::get(SensbitPaczkawRuchu::CFG_SIMPLE_PRINTNODE_ENABLED)}
			<button onclick="sensbitpaczkawruchu.printNode({$shipment->id});return false" class="btn btn-xs btn-warning sensbitpaczkawruchu-tip" title="{l s='Print label on PrintNode' mod='sensbitpaczkawruchu'}"><i class="icon-print"></i> <i class="icon-cloud"></i></button>
			{/if}	

		{if true || $status == 'created' || $status == 'offers_prepared'}
			<button onclick="sensbitpaczkawruchu.deleteShipments({$shipment->id});return false" class="btn btn-xs btn-danger sensbitpaczkawruchu-tip" title="{l s='Delete shipment' mod='sensbitpaczkawruchu'}"><i class="icon-remove"></i></button>
			{/if}

	</td>
</tr>