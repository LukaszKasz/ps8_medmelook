{**
* 2016-2017 Sensbit
*
* MODUŁ ZOSTAŁ UDOSTĘPNIONY NA PODSTAWIE LICENCJI NA JEDNO STANOWISKO/DOMENĘ
* NIE MASZ PRAWA DO JEGO KOPIOWANIA, EDYTOWANIA I SPRZEDAWANIA
* W PRZYPADKU PYTAŃ LUB BŁĘDÓW SKONTAKTUJ SIĘ Z AUTOREM
*
* ENGLISH:
* MODULE IS LICENCED FOR ONE-SITE / DOMAIM
* YOU ARE NOT ALLOWED TO COPY, EDIT OR SALE
* IN CASE OF ANY QUESTIONS CONTACT AUTHOR
*
* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
*
* PL: ODWIEDŹ NASZ SKLEP PO WIĘCEJ PROFESJONALNYCH MODUŁÓW PRESTASHOP
* EN: VISIT OUR ONLINE SHOP FOR MORE PROFESSIONAL PRESTASHOP MODULES
* HTTPS://SKLEP.SENSBIT.PL
*
* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
*
* @author    Tomasz Dacka (kontakt@sensbit.pl)
* @copyright 2016-2017 sensbit.pl
* @license   One-site license (jednostanowiskowa, bez możliwości kopiowania i udostępniania innym)
*}


<div id='sensbitpaczkawruchu-wrapper' class='row'>
	<div id='sensbitpaczkawruchu' class='sensbitpaczkawruchu'>
		<img  style='height:auto;' class='sensbitpaczkawruchu-img' src="{$sensbitpaczkawruchu_options.module_dir}views/img/services/odbior-w-punkcie.png"/>
		<div class="sensbitpaczkawruchu-search">
			<select class='sensbitpaczkawruchu-point-select'>
				<option>
					{if !empty($sensbitpaczkawruchu_options.point)}
						{$sensbitpaczkawruchu_options.point_label}
					{else}
						{l s='Choose your point' mod='sensbitpaczkawruchu'}
					{/if}
				</option>
			</select>
			<a class='sensbitpaczkawruchu-map-btn' onclick='sensbitpaczkawruchu.openMap(".sensbitpaczkawruchu-point-select", "{$sensbitpaczkawruchu_options.customer_place}");
					return false;' href='#'>{l s='Choose from map' mod='sensbitpaczkawruchu'}</a>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		if (typeof window.jQuery === 'undefined') {
			var sensbitpaczkawruchujquery = document.createElement('script');
			sensbitpaczkawruchujquery.type = 'text/javascript';
			sensbitpaczkawruchujquery.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js';
			document.documentElement.childNodes[0].appendChild(sensbitpaczkawruchujquery);
		}
	}, false);
</script>

<script>
	{strip}
		{literal}
			var sensbitpaczkawruchuloader = setInterval(function () {
				if (typeof sensbitpaczkawruchu !== 'undefined' && typeof sensbitpaczkawruchu.init === 'function') {
					sensbitpaczkawruchu.init({/literal}{$sensbitpaczkawruchu_options|json_encode nofilter}{literal});
					clearInterval(sensbitpaczkawruchuloader);
				}
			}, 300);
		{/literal}
	{/strip}
</script>
