{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<tr class="Outlisted noOutlisted_{$article_search->id}">
    <td class="{if $article_search->actif} {else}noactif{/if} center">
        <img src="../modules/prestablog/views/img/linked.png" rel="{$article_search->id}" class="linked" />
    </td>
    <td class="{if $article_search->actif} {else}noactif{/if} center">{$article_search->id}</td>
    <td class="{if $article_search->actif} {else}noactif{/if} center" style="width:50px;"><img src="{$thumbnail}" alt="{$article_search->title}"></td>
    <td class="{if $article_search->actif} {else}noactif{/if}">{$article_search->title}</td>
</tr>
