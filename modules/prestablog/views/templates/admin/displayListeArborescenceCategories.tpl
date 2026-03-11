{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{foreach $liste $value}
    <tr>
    <td class="center">{$value['id_prestablog_categorie']}</td>
    {if file_exists("{$imgUpPath}/c/adminth_{$value['id_prestablog_categorie']}.jpg")}
        <td class="center">
            <img src="{$imgPathBO}{$getT}/up-img/c/adminth_{$value['id_prestablog_categorie']}.jpg?{$md5}"/>
        </td>
    {else}
        <td class="center">-</td>
    {/if}

    <td>
        {if {$decalage} > 0}
            <span style="padding-left:{$decalage*20}px;background: url(../modules/prestablog/views/img/decalage.png) no-repeat right center;"></span>
        {else}
            <span style=""></span>
        {/if}
        {$value['title']}
    </td>

    {if $value['meta_title']}
        <td style="font-size:90%;">{$value['meta_title']}</td>
    {else}
        <td style="text-align:center;">-</td>
    {/if}

    <td style="text-align:center;">

    {assign "liste_groupes_categorie" CategoriesClass::getGroupsFromCategorie((int) {$value['id_prestablog_categorie']})}
    {if count($liste_groupes_categorie) > 0}
        <div><small>
            {$prestablog->get_string_group_list($liste_groupes_categorie, $context->language->id)}
        </small></div>
    {else}
        -
    {/if}
    </td>

    {if !$prestaboost_active}
        <td class="center">
            {if isset(CategoriesClass::getPopupLink($value['id_prestablog_categorie']))}
                Oui
            {else}
                Non
            {/if}
        </td>
    {/if}
    </td>
    
    <td style="text-align:center;">
    {CategoriesClass::getNombreNewsDansCat((int) {$value['id_prestablog_categorie']})}
    </td>

    <td class="center">
        {if $editPermissionAccess}
        <a href="{$confpath}&etatCat&idC={$value['id_prestablog_categorie']}">
        {if $value['actif']}
            <i class="material-icons action-enabled" style="color: #78d07d;">check</i>
        {else}
            <i class="material-icons action-disabled" style="color: #c05c67;">clear</i>
        {/if}
        </a>
        {/if}
    </td>

    <td class="center">
        {if $editPermissionAccess}
        <a href="{$confpath}&editCat&idC={$value['id_prestablog_categorie']}" title="{l s='Edit' d='Modules.Prestablog.Prestablog'}">
        <i class="material-icons" style="color: #6c868e;">mode_edit</i>
        </a>
        {/if}

        {if $deletePermissionAccess}
            {if !count($value['children'])}
                {if (count($liste) > 1 && $decalage == 0) || $decalage > 0}
                    <a href="{$confpath}&deleteCat&idC={$value['id_prestablog_categorie']}" onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                    <i class="material-icons" style="color: #c05c67;">delete</i>
                    </a>
                {else}
                    <a href="#" onclick="return confirm('{l s='You must add a new category before deleting this one !' d='Modules.Prestablog.Prestablog'}');">
                    <i class="material-icons" style="color: #c05c67;">delete</i>
                    </a>
                {/if}
            {else}
                <a href="#" onclick="return alert('{l s='For delete parent category, you should delete all child before !' d='Modules.Prestablog.Prestablog'}');">
                <i class="material-icons" style="color: #c05c67;">delete</i>
                </a>
            {/if}
        {/if}
        
    </td>
    </tr>

    {if count($value['children']) > 0}
        {$prestablog->displayListeArborescenceCategories($value['children'], $decalage + 1)}
    {/if}
{/foreach}