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
    <div class="{$size_bootstrap}">
        <div class="input-group">
        {if $info_span_before}
            <span class="input-group-addon">
                {$info_span_before}
            </span>

        {/if}
        {if $sizecar}
            <input id="{$name_item}" size="{$sizecar}" type="text"
            value="{$value}" name="{$name_item}" class="{$class}">
        {else}
            <input id="{$name_item}"  type="text"
            value="{$value}" name="{$name_item}" class="{$class}">
        {/if}
        
        {if $info_span}
            <span class="input-group-addon">
                {$info_span}
            </span>
        {/if}

        </div>

        {if $help}
            <p class="help-block">
                {$help}
                </p>
        {/if}

    </div>
</div>