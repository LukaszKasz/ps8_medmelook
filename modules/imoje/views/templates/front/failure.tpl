{extends file=$layout}

{block name='content'}

    {capture name=path}{l s='Payment' mod='imoje'}{/capture}
    <div class="text-center">{l s='Unable to complete your payment request. Try again later or contact with shop staff.' mod='imoje'}</div>
    <br>
    {if isset($ga_key) && $ga_key}{include file="module:imoje/views/templates/front/_ga.tpl" ga_key=$ga_key}{/if}
{/block}
