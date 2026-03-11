{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<ul id="sortblocLeft" class="connectedSortable">
    <li class="ui-state-default ui-state-disabled">
        {l s='Left' d='prestablog'}
    </li>
    {if count($sbl) > 0}
        {foreach $sbl "vs"}
            {if $vs != ''}
                <li rel="{$vs|escape:'html':'UTF-8'}" class="ui-state-default ui-move">
                    {$prestablog->message_call_back[$vs]|escape:'html':'UTF-8'}
                </li>
            {/if}
        {/foreach}
    {/if}
</ul>
<ul id="sortblocRight" class="connectedSortable">
    <li class="ui-state-default ui-state-disabled">
        {l s='Right' d='prestablog'}
    </li>
    {if count($sbr) > 0}
        {foreach $sbr "vs"}
            {if $vs != ''}
                <li rel="{$vs|escape:'html':'UTF-8'}" class="ui-state-default ui-move">
                    {$prestablog->message_call_back[$vs]|escape:'html':'UTF-8'}
                </li>
            {/if}
        {/foreach}
    {/if}
</ul>