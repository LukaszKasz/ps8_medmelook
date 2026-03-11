{extends file=$layout}

{block name='content'}

    {capture name=path}{l s='Payment' mod='imoje'}{/capture}
    <div class="text-center">{l s='Your order has been processed. You will be informed via e-mail about it end status.' mod='imoje'}</div><br>

    {if isset($ga_key) && $ga_key}{include file="module:imoje/views/templates/front/_ga.tpl" ga_key=$ga_key ga_conversion=$ga_conversion}{/if}
{/block}
