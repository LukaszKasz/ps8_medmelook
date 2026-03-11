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
    <label for="{$ni}" class="control-label {$label_bootstrap}">
        <span>{$label_text}</span>
    </label>
    <div class="col-lg-7">
        <span class="switch prestashop-switch fixed-width-lg">
            <input name="{$ni}" id="{$ni}_on"
                value="1" {if $isOn == true}
                    checked="checked"
                {/if}
                type="radio">
            <label for="{$ni}_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>
            <input name="{$ni}" id="{$ni}_off"
                value="0" {if $isOn == false}
                    checked="checked"
                {/if}
                type="radio">
            <label for="{$ni}_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>
            <a class="slide-button btn"></a>
        </span>
        {if $help}
            <p class="help-block">{$help}</p>
        {/if}
       
    </div>
</div>