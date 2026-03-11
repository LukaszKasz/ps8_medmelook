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
          <span class="input-group-addon">{$info_span_before}</span>
        {/if}
        <select name="{$name_item}" id="{$name_item}" {if $sizecar}size="{$sizecar}"{/if}>
        {foreach $options $val}
          <option value="{$val}" {if $value == $val}selected{/if}>{$val}</option>
        {/foreach}
        </select>
        {if $info_span}
          <span class="input-group-addon">{$info_span}</span>
        {/if}
      </div>
      {if $help}
        <p class="help-block">{$help}</p>
      {/if}
    </div>
</div>


