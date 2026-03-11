{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<tr class="Outlisted noOutlisted_{$product_search->id}">
    <td class="{if $product_search->active} {else}noactif{/if} center">
        <img src="../modules/prestablog/views/img/linked.png" rel="{$product_search->id}" class="linked" />
    </td>
    <td class="{if $product_search->active} {else}noactif{/if} center">{$product_search->id}</td>
    <td class="{if $product_search->active} {else}noactif{/if} center" style="width:50px;">{$image_thumb_path}</td>
    <td class="{if $product_search->active} {else}noactif{/if}">{$product_search->name}</td>
</tr>
