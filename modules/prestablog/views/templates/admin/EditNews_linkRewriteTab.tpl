{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div id="link_rewrite_{$lid|escape:'html':'UTF-8'}" style="display: {if $lid == $dl}block{else}none{/if};">
<input type="text" name="link_rewrite_{$lid|escape:'html':'UTF-8'}" id="slink_rewrite_{$lid|escape:'html':'UTF-8'}"
value="{if isset($news->link_rewrite[$lid])}{$news->link_rewrite[$lid]}{/if}"
onkeyup="if (isArrowKey(event)) return ;updateFriendlyURLPrestaBlog();"
onchange="updateFriendlyURLPrestaBlog();"
{if isset($news->id) }
      style="color:#7F7F7F;background-color:#e0e0e0;" disabled="true"
{/if}
/>
</div>