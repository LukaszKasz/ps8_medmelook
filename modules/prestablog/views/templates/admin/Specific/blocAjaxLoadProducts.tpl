{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<tr class="noInlisted_{$product_search->id|escape:'html':'UTF-8'}">
    <td class="{if $product_search->active}center{else} noactif{/if}">{$product_search->id|escape:'html':'UTF-8'}</td>
    <td class="{if $product_search->active}center{else} noactif{/if}"><img src="{$image_thumb_url|escape:'html':'UTF-8'}"></td>
    <td {if $product_search->active} {else}class="noactif"{/if}>{$product_search->name|escape:'html':'UTF-8'}</td>
    <td class="{if $product_search->active}center{else} noactif{/if}">
        <img src="../modules/prestablog/views/img/disabled.gif" rel="{$product_search->id|escape:'html':'UTF-8'}" class="delinked" />
    </td>
</tr>
