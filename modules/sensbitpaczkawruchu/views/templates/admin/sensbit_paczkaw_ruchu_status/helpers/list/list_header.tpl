
{extends file="helpers/list/list_header.tpl"}
{block name=override_header}
	<div class="sensbitpaczkawruchu sensbitpaczkawruchu-parent">
		<div class="alert alert-info">

			<h4>Jak działa automatyczne sprawdzanie statusów przesyłek?</h4>
			<p>Dla przesyłek w bazie sklepu o statusie, który ma poniżej włączoną opcję <strong>automatycznego sprawdzania statusu</strong> nasz moduł sprawdzi czy status w Orlen Paczka się zmienił.</p>
			<p>Jeśli tak i nowy status będzie miał ustawioną automatyczną zmianę statusu zamówienia, to Twoje zamówienia będą oznaczane wybranym statusem.</p>
			<p>Sprawdzanie statusów odbywa się na 4 różne sposoby.</p>
			<p>
			<ol>
				<li>Ręcznie klikając na status przesyłki w sklepie.</li>
				<li>Automatycznie w tle wyświetlając szczegóły danego zamówienia.</li>
				<li>Automatycznie w tle wyświetlając listę przesyłek Orlen Paczka > Przesyłki.</li>
				<li>Automatycznie za pomocą CRONa.</li>
			</ol>
			</p>
			<br/>
			<h4>Jak ustawić automatyczne sprawdzanie statusów przesyłek?</h4>
			<p>Zaznacz przy każdym pośrednim statusie przesyłki opcję <strong>automatycznego sprawdzania statusu</strong>.</p>
			<p>Nie zaznaczaj tej opcji przy statusach "doręczono", bo nie ma sensu sprawdzać czy status przesyłki doręczonej uległ zmianie.</p>
			<p>Możesz skorzystać z naszych ustawień automatycznego sprawdzania statusów klikając przycisk poniżej.<br/>
				Spowoduje to ustawienie automatycznego sprawdzania statusów dla wszystkich pośrednich statusów przesyłek. Ustawienia statusów zamówień nie ulegną zmianie.</p>
			<p><input class="btn btn-default btn-xs" type="submit" name="setDefaultStatusesData" value="Ustaw domyślne wartości konfiguracji automatycznego sprawdzania statusów"/></p>
		</div>
	</div>
	<script>
		$(function () {
		{if SensbitPaczkawRuchuTools::isBootstrap()}
			$(".sensbitpaczkawruchu-parent").insertBefore("#form-sensbitpaczkawruchu_status table.table");
		{else}
			$(".sensbitpaczkawruchu-parent").insertBefore("table[name=list_table]");
		{/if}
		});
	</script>
{/block} 
