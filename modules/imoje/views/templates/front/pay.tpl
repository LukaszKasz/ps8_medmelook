{extends file=$layout}

{block name='content'}
    <style>
        #submit-payment-form {
            display: none;
        }
    </style>
    {capture name=path}{l s='Payment' mod='imoje'}{/capture}
    <div class="text-center" style="text-align:center">
		<span id="text-tip">
			{if isset($blik_msg) && $blik_msg}
                {l s='Please wait, you will be redirected to BLIK payment page.' mod='imoje'}
            {elseif isset($pbl_msg) && $pbl_msg}
                {l s='Please wait, in the next step you will be redirected to the bank.' mod='imoje'}
            {else}
                {l s='Please wait, in the next step you will choose a payment method.' mod='imoje'}
            {/if}
		</span>
    </div>
    <br>
    <div id="imoje-payment-form">
        {$form nofilter}
    </div>
    <br>
    {if isset($ga_key) && $ga_key}{include file="module:imoje/views/templates/front/_ga.tpl" ga_key=$ga_key}{/if}
    <script type="text/javascript">
        if (window.location.hash === '#processed') {
            window.location.replace("{$checkout_link}");
            document.getElementById('text-tip').innerHTML = '{$text_return_to_checkout}';
        } else {
            window.location.hash = '#processed';
            document.getElementById("submit-payment-form").click();
        }
    </script>
{/block}
