{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{$prestablog->get_displayFormOpen('slide.png',"{l s='Layout of your blog template' d='Modules.Prestablog.Prestablog'}",$confpath)}
<div class="layouts_preview row">
    {foreach from=$scanLayoutFolder item=layout key=key}
        {if $_layout_blog == $key}
            <div class='layout_preview' data-layoutref='{$key|escape:'html':'UTF-8'}'>
                <img class='layout_select' src='{$imgCheck|escape:'html':'UTF-8'}'>
                <img src='{$imgLayout|escape:'html':'UTF-8'}{$layout|escape:'html':'UTF-8'}'>
            </div>
        {else}
            <div class='layout_preview' data-layoutref='{$key}'>
                <a href='{$prestablog_layout_blog|escape:'html':'UTF-8'}{$key|escape:'html':'UTF-8'}'>
                    <img src='{$imgLayout|escape:'html':'UTF-8'}{$layout|escape:'html':'UTF-8'}'>
                </a>
            </div>
        {/if}
    {/foreach}
</div>
{l s='If the changes are not taken into account, please go to the management of your template to appearance > theme and logo > choose the layout. You should find the blog module at the bottom of the table and will be able to choose how to display the blog. ' d='Modules.Prestablog.Prestablog'}
{$prestablog->get_displayFormClose()}
