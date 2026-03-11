{extends file=$layout}

{block name='content'}
    <div class="alert alert-danger" role="alert">
        {foreach from=$imojeErrors key=key item=error}
            <p class="text-center">{l s=$error mod='imoje'}</p>
        {/foreach}
    </div>
{/block}