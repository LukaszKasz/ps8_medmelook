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
        <div class="col-lg-10">
    {else}
        <div class="col-lg-6">
    {/if}
        <div class="input-group">
            <span class="switch prestashop-switch fixed-width-lg" style="margin-right:5px;">
            {if $value_activation}
                <input name="{$name_item_activation}" id="{$name_item_activation}_on"
                value="1" checked="checked"  type="radio">
                <label for="{$name_item_activation}_on">{l s='Yes' d='prestablog'}</label>

                <input name="{$name_item_activation}" id="{$name_item_activation}_off"
                    value="0" type="radio">
            {else}
                <input name="{$name_item_activation}" id="{$name_item_activation}_on"
                value="1" type="radio">
                <label for="{$name_item_activation}_on">{l s='Yes' d='prestablog'}</label>

                <input name="{$name_item_activation}" id="{$name_item_activation}_off"
                    value="0" checked="checked" type="radio">
            {/if}
               
               

                    <label for="{$name_item_activation}_off">{l s='No' d='prestablog'}</label>
                <a class="slide-button btn"></a>
            </span>
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
</div>{$prestablog->moduleDatepicker($name_item, true)}