<div class='sensbitpaczkawruchu'>
	{if $data.first_message}
		<div class="alert alert-warning">
			<strong>Uwaga! Zamówienie ma przypisaną wiadomość.</strong>{if $data.first_message_lines>3} <a href="#order_{$data.id_order}-first-message" class="sensbitpaczkawruchu-slide-toggle">Pokaż</a>{/if}
			<div id="order_{$data.id_order}-first-message"{if $data.first_message_lines>3} style="display:none;"{/if}>
				{$data.first_message}
			</div>
		</div>
	{/if}
	<table class='table messages-container sensbitpaczkawruchu-order-form' id='sensbitpaczkawruchu-order-form-{$data.id_order}'>
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
				<th>{l s='Options' mod='sensbitpaczkawruchu'}</th>
				<th></th>
			</tr>
		</thead>

		<tbody class="package-container">
			{include file="./new_package_form.tpl"}
		</tbody>
	</table>
</div>