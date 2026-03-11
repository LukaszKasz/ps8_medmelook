{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{assign var="div_lang_name" value="title¤link_rewrite¤meta_title¤meta_description¤meta_keywords¤cpara1¤cpara2"}

<span id="check_lang_prestablog">

{if isset($languages[1]) }
    <input type="checkbox" name="checkmelang" class="noborder" onclick="{literal}checkDelBoxes(this.form, 'languesup[]', this.checked){/literal}"/>{l s='All' d='Modules.Prestablog.Prestablog'} |
{/if}

{foreach $languages $language }
    {assign var="lid" value=(int) $language['id_lang']}
    <input type="checkbox" name="languesup[]" value="{$lid}"
    {if ((Tools::getValue('idN') && in_array((int) $lid, $lang_liste_news))
        || (Tools::getValue('languesup')
        && in_array((int) $lid, Tools::getValue('languesup')))
        || ((!Tools::getValue('idN') &&
        !Tools::getValue('languesup'))
        && ((int) $lid == (int) $dl))) }
         checked=checked
    {/if}

    {if count($languages) == 1 }
        style="display:none;"
    {/if}
     />
    <img src="../img/l/{$lid}.jpg" class="pointer indent-right prestablogflag" alt="{$language['name']}" title="{$language['name']}"
        onclick="changeTheLanguage('title', '{$div_lang_name}', {$lid}, '{$language['iso_code']}');">
{/foreach}

</span>
{* langues stop *}
