<div style="clear:both"></div>
{$data=$sensbitpaczkawruchu}

{capture name='content'}
	<form class='packages-ready-form messages-container'{if empty($data.shipments)} style="display:none"{/if}>
		<h4>{l s='Packages created for order' mod='sensbitpaczkawruchu'} {$data.order->reference}</h4>
		<table class='table'>
			<thead>
				<tr>
					<th></th>
					<th>{l s='Service' mod='sensbitpaczkawruchu'}</th>
					<th>{l s='Tracking number' mod='sensbitpaczkawruchu'}</th>
					<th>{l s='Status przesyłki' mod='sensbitpaczkawruchu'}</th>
					<th>Cena przesyłki</th>
					<th>{l s='Create date' mod='sensbitpaczkawruchu'}</th>
					<th>{l s='Created by' mod='sensbitpaczkawruchu'}</th>
					<th></th>
				</tr>
			</thead>
			<tbody class='packages-ready-container'>
				{foreach from=$data.shipments item=shipment}
					{$shipment->getCompletedRowHtml()}
				{/foreach}
			</tbody>
		</table>
		<div class="packages-completed-actions">
			<button class="btn btn-default print-labels">{l s='Print labels' mod='sensbitpaczkawruchu'}</button>
			<button class="btn btn-default print-protocol">{l s='Print protocol' mod='sensbitpaczkawruchu'}</button>

			<button class="btn btn-default delete-shipments">{l s='Cancel shipments' mod='sensbitpaczkawruchu'}</button>

		</div>
	</form>


	{if empty($data.templates) && empty($data.global_templates)}
		<div class='alert alert-warning'>Nie posiadasz skonfigurowanych szablonów przesyłek.</div>
	{else}
		{if !empty($data.templates)}
			<div class="sensbitpaczkawruchu-connected-templates">
				<h4>Szablony powiązane z tym zamówieniem</h4>
				{foreach $data.templates as $template}
					<a href='' class='button btn btn-default btn-xs sensbitpaczkawruchu-service' data-id='{$template.id_template}'>{$template.name} {SensbitPaczkawRuchuService::getServiceLogo($template.service)|unescape}</a>

				{/foreach}
			</div>
		{else}
			<div class='alert alert-warning'>To zamówienie nie posiada żadnych przypisanych szablonów.</div>
		{/if}
		{if !empty($data.global_templates)}
			<h4{if !empty($data.templates)} style="margin-top:15px"{/if}>Wszystkie pozostałe szablony niepowiązane z tym zamówieniem. <a href="#" class="btn btn-xs btn-success switch_global_templates s">Pokaż</a><a href="#" class="btn btn-xs btn-warning switch_global_templates h">Ukryj</a></h4>
			<div class="global_templates">
				{foreach $data.global_templates as $template}
					<a href='' class='button btn btn-default btn-xs sensbitpaczkawruchu-service' data-id='{$template.id_template}'>{$template.name} {SensbitPaczkawRuchuService::getServiceLogo($template.service)|unescape}</a>
				{/foreach}
			</div>
		{/if}

		<form class='packages-form messages-container' style="display:none">
			{*<h3>{l s='Create new package for order ' mod='sensbitpaczkawruchu'} {$data.order->reference}</h3>*}
			<table class='table'>
				<thead>
					<tr>	
						<th></th>
						<th>{l s='Template' mod='sensbitpaczkawruchu'}</th>
						<th>{l s='Service' mod='sensbitpaczkawruchu'}</th>
						<th>{l s='Receiver contact data' mod='sensbitpaczkawruchu'}</th>
						<th>{l s='Destination' mod='sensbitpaczkawruchu'}</th>
						<th>{l s='Cash on delivery' mod='sensbitpaczkawruchu'}</th>
						<th>{l s='Insurance' mod='sensbitpaczkawruchu'}</th>
						<th>{l s='Reference' mod='sensbitpaczkawruchu'}</th>
						<th>{l s='Size' mod='sensbitpaczkawruchu'}</th>
						<th></th>
					</tr>
				</thead>

				<tbody class="package-container">

				</tbody>

			</table>
			<div style="margin-top:20px">
				<button class="btn btn-warning prepare-packs">{l s='Create' mod='sensbitpaczkawruchu'}</button>
			</div>
		</form>

		<script>
			sensbitpaczkawruchu.setOptions({
				id_order: {$data.order->id|intval},
				ajax_url_packages: '{$link->getAdminLink('AdminSensbitPaczkawRuchuPackage')}'
			});
		</script>

	{/if}
{/capture}

<div class="sensbitpaczkawruchu{if $data.hide_global_templates} hide_global_templates{/if}{if empty($data.templates) && $data.hide_panel_if_no_templates} hide_no_templates{/if}">
	{if $data.bootstrap}
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-truck"></i> Wysyłka z OrlenPaczka.pl <a href="{$data.module_link}"><i class="icon-cogs"></i></a> <a href="#" class="switch_no_templates s">Pokaż</a><a href="#" class="switch_no_templates h">Ukryj</a>
			</div>
			<div class="panel-body panel_container">
				{if $data.customer_point}
					<div class="alert alert-info">
						Wybrany punkt odbioru przez klienta: <strong>{$data.customer_point}</strong>{if isset($data.customer_point_data.address)}  <em>{$data.customer_point_data.address}</em>{else} <strong style="color:#c00">Uwaga. Nie można pobrać danych tego punktu. Może już nie istnieć! Podczas tworzenia przesyłki PWR może zmienić automatycznie punkt na najbliższy wględem wskazanego!</strong>{/if}
					</div>
				{else}
					<div class="alert alert-{if $data.default_point}warning{else}danger{/if}">
						{if $data.default_point}
							Klient nie wybrał punktu w tym zamówieniu ale znamy jego ostatnio wybrany punkt: <strong>{$data.default_point}</strong>{if isset($data.default_point_data.address)} <em>{$data.default_point_data.address}</em>{else} <strong style="color:#c00">Uwaga. Nie można pobrać danych tego punktu. Może już nie istnieć!</strong>{/if}
						{else}
							Klient nie wybrał punktu w tym zamówieniu.
						{/if}
					</div>
				{/if}
				{$smarty.capture.content}
			</div>
		</div>
	{else}
		<fieldset>
			<legend><img src="../img/admin/delivery.gif"> Wysyłka z OrlenPaczka.pl<a href="{$data.module_link}"><i class="icon-cogs"></i></a> <a href="#" class="btn btn-xs btn-success switch_no_templates s">Pokaż</a><a href="#" class="btn btn-xs btn-warning switch_no_templates h">Ukryj</a></legend>
			<div class="panel_container">
				{$smarty.capture.content}
			</div>
		</fieldset>
	{/if}
</div>

