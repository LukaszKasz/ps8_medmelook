{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{assign "ret" "" }     
{if strpos($icon_legend, 'icon-') !== false}
    {assign "ret" "<i class='{$icon_legend}'></i>" }                 
{else}
    {if $icon_legend != ''}
        {assign "ret" "<img src='{$imgPathFO}{$icon_legend}' />" }  
    {else}
        {assign "ret" "<i class='{$icon_legend}'></i>" }                 
    {/if}
{/if}


<div class="blocmodule">
    <fieldset>
        <legend> {$ret}&nbsp;{$label_legend}</legend>
            <form method="post" class="form-horizontal" action="{$action}" name="{$name}" enctype="multipart/form-data">