{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{assign "addInfo_span_before" "" }    
{if $info_span_before}
    {assign "addInfo_span_before" "<span class='input-group-addon'> {$info_span_before}</span> " }                

{/if}
{assign "addSizecar" "" }    
{if $sizecar}
    {assign "addSizecar" "<span class='input-group-addon'>" } {$info_span_before}</span>                

{/if}
{assign "select" "" }    
 <div class='form-group'>
<label class='control-label {$label_bootstrap}' for='{$name_item}'>{$label_text}</label>
<div class='{$size_bootstrap}'>
<div class='input-group'>
{$addInfo_span_before}

<select name='{$name_item}' id='{$name_item}' {$addSizecar} >


{if count($options) > 0}
    {foreach $options key=k item=v}

        {if $k == $value}
            <option value='{$k}' selected="selected" > {$v}</option>
        {else}
            <option value='{$k}'> {$v}</option>
        {/if}
          

    {/foreach}

{/if}

{assign "addInfo_span" "" } 
{if $info_span}
    {assign "addInfo_span" "<span class='input-group-addon'>{$info_span}</span>" } 
    
{/if}

</select>
{$addInfo_span}
</div>

{if $help}
   <p class='help-block'>
   {$help}
   </p>
    

{/if}

</div>
</div> 