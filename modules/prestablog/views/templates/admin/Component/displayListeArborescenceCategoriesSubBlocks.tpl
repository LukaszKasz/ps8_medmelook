{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{foreach $listecat $value}
    {assign var="active" value=false}
    {if (Tools::getValue('idSB')&& in_array( (int) $value['id_prestablog_categorie'],
    SubBlocksClass::getCategories((int) Tools::getValue('idSB'), 0))) || (Tools::getValue('categories')
    && in_array((int) $value['id_prestablog_categorie'], Tools::getValue('categories')))}
    {assign var="active" value=true}
       
    {/if}
    {if (SubBlocksClass::getIdSbHome($id_lang) && in_array( (int) $value['id_prestablog_categorie'],
    SubBlocksClass::getCategories((int) SubBlocksClass::getIdSbHome($id_lang), 0)
    )) || (Tools::getValue('categories')
    && in_array((int) $value['id_prestablog_categorie'], Tools::getValue('categories')))}
    {assign var="active" value=true}
       
    {/if}
    

    <tr class="prestablog_branch{if $decalage > 0} childs{/if}{if $active} alt_row{/if}" rel="{$value['branch']}" id="prestablog_categorie_{(int) $value['id_prestablog_categorie']}" >
        <td>
            <input type="checkbox" name="categories[]" value="{(int) $value['id_prestablog_categorie']}" {if $active} checked=checked{/if} />
        </td>
        <td>{$value['id_prestablog_categorie']}</td>
    {if isset($value['imgthidc'])}
        <td class="center">
            <img src="{$imgthidc}?{$md5}" />
        </td>
    {else}
        <td class="center">-</td>
    {/if}
        <td>
        {foreach $languages $language}
            {foreach $liste_cat_lang $cat_lang}
                {if (int) $cat_lang['id_prestablog_categorie'] == (int) $value['id_prestablog_categorie']
                && (int) $cat_lang['id_lang'] == (int) $language['id_lang']}
                {assign var="ouidecalage" ""}
                    {if $decalage > 0}
                        {assign "ouidecalage" "padding-left:{$decalage * 20} px"}
                        {assign "ouidecalage" "background: url(../modules/prestablog/views/img/decalage.png) "}
                        {assign "ouidecalage" "no-repeat right center;"}
                    {/if}
                    <div class="catlang" rel="{(int) $language['id_lang']}">
                        <span style="{$ouidecalage}"></span>
                        {if count($value['children']) > 0
                        && in_array((int) $value['id_prestablog_categorie'], $listeidbranchdeploy)}
                            <img
                                src="{$prestablog->imgPathBO()}collapse.gif"
                                class="expand-cat"
                                data-action="collapse"
                                data-path="{$prestablog->imgPathBO()}"
                                rel="{$value['branch']}"
                                />
                        {elseif count($value['children']) > 0
                        && !in_array((int) $value['id_prestablog_categorie'], $listeidbranchdeploy)}
                            <img
                            src="{$prestablog->imgPathBO()}expand.gif"
                            class="expand-cat"
                            data-action="expand"
                            data-path="{$prestablog->imgPathBO()}"
                            rel="{$value['branch']}"
                            />    
                        {/if}

                        {if $active}
                            <strong>{$cat_lang['title']}</strong>
                        {else}
                            {$cat_lang['title']}
                        {/if}
                    </div>
                {/if}
            {/foreach}
        {/foreach}
        </td>
    </tr>


    {if count($value['children']) > 0}
        {$prestablog->get_displayListeArborescenceCategoriesSubBlocks($value['children'], $decalage + 1, $listeidbranchdeploy)}
    {/if}

{/foreach}