{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div id="title_{$lid|escape:'html':'UTF-8'}" style="display: 
{if $lid == $dl }
    block
{else}
    none
{/if}
;">
<input id="title_{$lid|escape:'html':'UTF-8'}" type="text" name="title_{$lid|escape:'html':'UTF-8'}" maxlength="{(int) Configuration::get('prestablog_news_title_length')|escape:'html':'UTF-8'}"
{if isset($news->title[$lid]) }
    value="{$news->title[$lid]|escape:'html':'UTF-8'}"
{else}
    value=""
{/if}
onkeyup="if (isArrowKey(event)) return; copy2friendlyURLPrestaBlog();" onchange="copy2friendlyURLPrestaBlog();">
</div>