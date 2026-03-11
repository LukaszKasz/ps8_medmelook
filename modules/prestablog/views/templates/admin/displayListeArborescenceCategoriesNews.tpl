{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{assign var="tmp_min" value=""}

{foreach $liste_cat $value}
  {assign var="active" value=false}
  {if ((Tools::getValue('idN')
    && in_array((int) $value['id_prestablog_categorie'], CorrespondancesCategoriesClass::getCategoriesListe((int) Tools::getValue('idN'))))
    || (Tools::getValue('categories') && in_array((int) $value['id_prestablog_categorie'], Tools::getValue('categories')))
  )}
    {assign var="active" value=true}
  {/if}

  <tr class="prestablog_branch{if $decalage > 0 } childs{/if}{if $active } alt_row{/if}"
    rel="{$value['branch']}" id="prestablog_categorie_{(int) $value['id_prestablog_categorie']}"
  >
  <td>
  <input type="checkbox"

  {if ($tmp_min == '' || $tmp_min > $value['id_prestablog_categorie'])}
    {assign var="tmp_min" value=$value['id_prestablog_categorie']}
  {/if}

  {if ((Tools::getIsset('addNews')) && ($value['id_prestablog_categorie']) == $tmp_min && ($value['parent'] == 0))}
      checked
  {/if}

  name="categories[]"
  value="{(int) $value['id_prestablog_categorie']}"
  {if $active }checked=checked{/if}
  />
  </td>
  <td>{$value['id_prestablog_categorie']}</td>

  {if file_exists("{$imgUpPath}/c/adminth_{$value['id_prestablog_categorie']}.jpg")}
      <td class="center">
          <img src="{$imgPathBO}{$getT}/up-img/c/adminth_{$value['id_prestablog_categorie']}.jpg?{$md5}"/>
      </td>
  {else}
      <td class="center">-</td>
  {/if}

  <td>

  {foreach $languages $language}
    {foreach $liste_cat_lang $cat_lang}
      {if ((int) $cat_lang['id_prestablog_categorie'] == (int) $value['id_prestablog_categorie'] && (int) $cat_lang['id_lang'] == (int) $language['id_lang'])}
        <div class="catlang" rel="{(int) $language['id_lang']}">
        {if {$decalage} > 0}
          <span style="padding-left:{$decalage*20}px;background: url(../modules/prestablog/views/img/decalage.png) no-repeat right center;"></span>
        {else}
          <span style=""></span>
        {/if}

        {if (count($value['children']) > 0 && in_array((int) $value['id_prestablog_categorie'], $liste_id_branch_deploy) )}
          <img src="{$imgPathBO}collapse.gif" class="expand-cat" data-action="collapse" data-path="{$imgPathBO}" rel="{$value['branch']}"/>
        {elseif (count($value['children']) > 0 && !in_array((int) $value['id_prestablog_categorie'], $liste_id_branch_deploy) )}
          <img src="{$imgPathBO}expand.gif" class="expand-cat" data-action="expand" data-path="{$imgPathBO}" rel="{$value['branch']}"/>
        {/if}

        {if ($active)}
          <strong>{$cat_lang['title']}</strong>
        {else}
            {$cat_lang['title']}
        {/if}

        {assign var="liste_groupes_categorie" value=CategoriesClass::getGroupsFromCategorie((int) $value['id_prestablog_categorie'])}

        {if (count($liste_groupes_categorie) > 0) }
          <div><small>
          <img src="{$imgPathBO}group.png">&nbsp;
          {foreach $liste_groupes_categorie $groupe}
            {assign var="group" value=$prestablog->create_group($groupe,$language['id_lang'])}
            {$group->name}, 
          {/foreach}
          </small></div>
        {/if}
        </div>
      {/if}
    {/foreach}
  {/foreach}

  </td></tr>

  {if count($value['children']) > 0 }
    {$prestablog->displayListeArborescenceCategoriesNews($value['children'], $decalage + 1, $liste_id_branch_deploy)}
  {/if}

{/foreach}