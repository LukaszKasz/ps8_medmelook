{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div class="form-group">
    <label class="control-label {$label_bootstrap}" for="{$name_item}">{$label_text}</label>
     {if $time}
        <div class="col-lg-4">
    {else}
        <div class="col-lg-3">
    {/if}

            <div class="input-group">
            <span class="input-group-addon"><i class="icon-calendar"></i></span>
            {if $time}
                <input id="{$name_item}" size="20" type="text"
                value="{$value}" name="{$name_item}">
                {else}
                    <input id="{$name_item}" size="10" type="text"
                value="{$value}" name="{$name_item}">
            {/if} 
            </div>
            <p class="help-block">
            {l s='Format: YYYY-MM-DD' d='prestablog'}    
                {if $time}
                    {l s='HH:MM:SS' d='prestablog'}
                {/if} 
            </p>
            </div>
            </div>
{$prestablog->moduleDatepicker($name_item, true)}