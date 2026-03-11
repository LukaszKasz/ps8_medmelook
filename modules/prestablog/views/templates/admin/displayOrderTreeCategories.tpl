{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if $count == 0}
    <ol class="sortable">
{else}
    <ol>
{/if}

{foreach $liste $value}
    {$count = $count + 1}
    <li id="list_{$value['id_prestablog_categorie']}">
    <div>
    <span class="disclose">
    <span></span>
    </span>

    {if file_exists("{$imgUpPath}/c/adminth_{$value['id_prestablog_categorie']}.jpg")}
        <img class="thumb" src="{$imgPathBO}{$getT}/up-img/c/adminth_{$value['id_prestablog_categorie']}.jpg?{$md5}" style="float:none;"/>
    {/if}
    {$value['title']}
    </div>

    {if count($value['children']) > 0}
        {$prestablog->displayOrderTreeCategories($value['children'], $count)}
    {/if}
    </li>
{/foreach}
</ol>