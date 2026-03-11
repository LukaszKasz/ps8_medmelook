{if isset($ga_key) && $ga_key && isset($ga_conversion) && $ga_conversion}

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id={$ga_key}"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}

		gtag('js', new Date());

		gtag('config', '{$ga_key}');

		gtag("event", "purchase", {$ga_conversion|cleanHtml nofilter});

	</script>
{/if}
