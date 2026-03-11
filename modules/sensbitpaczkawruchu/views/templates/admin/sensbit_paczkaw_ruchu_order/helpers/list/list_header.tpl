{extends file="helpers/list/list_header.tpl"}
{block name=preTable}
	<div class="sensbitpaczkawruchu sensbitpaczkawruchu-parent">
		<div class="sensbitpaczkawruchu sensbitpaczkawruchu-orders-selected-container">
			<p>Wybranych <span class="n">15</span> przesyłek.</p>
			<a href="#" class="prepare-packs">
				Dodaj wybrane
			</a>

			<a href="#" class="next-error" data-next="0">
				Popraw błędne przesyłki
			</a>

		</div>

		<div class="sensbitpaczkawruchu-orders-filters">
			<div class="row">
				<div class="col-sm-8">
					<h4>Super filtry</h4>
					<em class="text-info"><i class="icon-question-circle"></i> Zapisujemy Twoje wybrane filtry w konfiguracji. Nie stracisz ich po odświeżeniu strony.<br/>Oprócz super filtrów możesz używać filtrów w kolumnach danych np. filtrując po nr przesyłki czy kupionych produktach.</em>

					<div class="filters-container">
						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary">
									Data zamówienia
								</div>
								<div style='width:170px;display:inline-block'>
									<div class="input-group">
										<span class="input-group-addon">Od</span>
										<input type='text' class='sensbitpaczkawruchu-datetime' value="{if isset($filters.date_add)}{$filters.date_add.from}{/if}"  name='filters[date_add][from]' placeholder='Od'/>
									</div>
								</div>
								<div  style='width:170px;display:inline-block'>
									<div class="input-group">
										<span class="input-group-addon">Do</span>
										<input type='text' class='sensbitpaczkawruchu-datetime' value="{if isset($filters.date_add)}{$filters.date_add.to}{/if}"  name='filters[date_add][to]' placeholder='Do'/>
									</div>
								</div>
							</div>
						</div>

						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary">
									Przewoźnicy
								</div>
								{foreach $carriers as $carrier}
									{$active=isset($filters.carrier) && in_array($carrier.id_reference, $filters.carrier)}
									<label class="btn btn-default{if $active} active{/if}">
										<input type="checkbox" name="filters[carrier][]" value="{$carrier.id_reference}"{if $active} checked='checked'{/if}/>
										{$carrier.name}
									</label>
								{/foreach}
							</div>
						</div>

						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary">
									Płatności
								</div>
								{foreach $payments as $payment}
									{$active=isset($filters.payment) && in_array($payment.name, $filters.payment)}
									<label class="btn btn-default{if $active} active{/if}">
										<input type="checkbox" name="filters[payment][]" value="{$payment.name}"{if $active} checked='checked'{/if}/>
										{$payment.displayName}
									</label>
								{/foreach}
							</div>
						</div>

						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary">
									Aktywne statusy zamówień
								</div>
								{foreach $statuses as $status}
									{$active=isset($filters.status) && in_array($status.id_order_state, $filters.status)}
									<label class="btn btn-default{if $active} active{/if}">
										<input type="checkbox" name="filters[status][]" value="{$status.id_order_state}"{if $active} checked='checked'{/if}/>
										{$status.name}
										{if $status.color}
											<span style="display:inline-block; margin-left:5px;width:15px;height:15px;background: {$status.color};vertical-align:middle;"></span>
										{/if}
									</label>
								{/foreach}
							</div>
						</div>

						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary">
									Przesyłki
								</div>
								{$active=isset($filters.orders) && in_array('noshipments', $filters.orders)}
								<label class="btn btn-default{if $active} active{/if}">
									<input type="checkbox" name="filters[orders][]" value="noshipments"{if $active} checked='checked'{/if}/>
									Bez przesyłek
								</label>
								{$active=isset($filters.orders) && in_array('withshipments', $filters.orders)}
								<label class="btn btn-default{if $active} active{/if}">
									<input type="checkbox" name="filters[orders][]" value="withshipments"{if $active} checked='checked'{/if}/>
									Z przesyłkami
								</label>
							</div>
						</div>

						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary">
									Status opłaconych zamówień
								</div>
								{$active=isset($filters.state) && in_array('valid', $filters.state)}
								<label class="btn btn-default{if $active} active{/if}">
									<input type="checkbox" name="filters[state][]" value="valid"{if $active} checked='checked'{/if}/>
									Opłacone
								</label>
								{$active=isset($filters.state) && in_array('notvalid', $filters.state)}
								<label class="btn btn-default{if $active} active{/if}">
									<input type="checkbox" name="filters[state][]" value="notvalid"{if $active} checked='checked'{/if}/>
									Nieopłacone
								</label>
							</div>
						</div>

						<div class="filter-group">
							<div class="btn-group"  data-toggle="buttons">
								<div class="btn btn-primary">
									Wyświetlane dane
								</div>
								{foreach $fields_to_filter as $name => $vars}
									{if !in_array($name, array('templates'))}
										{$active=isset($filters.fields) && in_array($name,$filters.fields)}
										<label class="btn btn-default{if $active} active{/if}">
											<input type="checkbox" name="filters[fields][]" value="{$name}"{if $active} checked='checked'{/if}/>
											{$vars.title}
										</label>
									{/if}
								{/foreach}
							</div>
						</div>
						<div class="filter-group">
							<input class="btn btn-success" type="submit" name="set_filters" value="Filtruj"/>
						</div>
					</div>
					<button class="sensbitpaczkawruchu-orders-filters-open btn btn-primary btn-xs" style="margin-top:10px;">Rozwiń ⇊</button>
				</div>
				<div class="col-sm-4">
					<h4>Masowe działania</h4>
					<em class="text-info"><i class="icon-question-circle"></i> Chodzą słuchy, że ktoś jeszcze dodaje przesyłki kilkając pojedynczo. :)<br/>Używaj poniższych opcji z rozsądkiem.</em>
					<div class="filter-group">
						<button class="btn btn-primary sensbitpaczkawruchu-tip sensbitpaczkawruchu-mass-open" title="Na podstawie wybranych szablonów otwiera edycję przesyłek przy wszystkich widocznych zamówieniach poniżej.">
							Edytuj przesyłki do wszystkich zamówień
						</button>
					</div>
					<div class="filter-group">
						<button class="btn btn-primary sensbitpaczkawruchu-tip sensbitpaczkawruchu-mass-add" title="Na podstawie wybranych szablonów otwiera edycję przesyłek po czym od razu je dodaje na podstawie domyślnych ustawień przy wszystkich widocznych zamówieniach poniżej.">
							Dodaj szybko przesyłki do wszystkich zamówień
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(function () {
			var target = '';
		{if SensbitPaczkawRuchuTools::isBootstrap()}
			target = "#form-orders table.table";
		{else}
			target = "table[name=list_table]";
		{/if}
			$(".sensbitpaczkawruchu-parent").insertBefore(target);
			var br_target = $(target).find('tbody > tr').not(':last');
			var colspan = br_target.eq(0).find('td').length;
			$("<tr><td colspan=" + colspan + " style='background: #777 !important;height:3px;'></td></tr>").insertAfter(br_target);
			$(target).addClass('sensbitpaczkawruchu');
		});
	</script>
{/block}
