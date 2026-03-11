{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<tr class="noInlisted_{$article_search->id|escape:'html':'UTF-8'}">
    <td class="{if $article_search->actif}center{else} noactif{/if}">{$article_search->id|escape:'html':'UTF-8'}</td>
    <td class="{if $article_search->actif}center{else} noactif{/if}"><img src="{$thumbnail|escape:'html':'UTF-8'}" alt="{$article_search->title|escape:'html':'UTF-8'}"></td>
    <td {if $article_search->actif} {else}class="noactif"{/if}>{$article_search->title|escape:'html':'UTF-8'}</td>
    <td class="{if $article_search->actif}center{else} noactif{/if}">
        <img src="../modules/prestablog/views/img/disabled.gif" rel="{$article_search->id|escape:'html':'UTF-8'}" class="delinked">
    </td>
</tr>
