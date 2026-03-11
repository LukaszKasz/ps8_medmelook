{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<span id="check_lang_prestablog">
{if isset($languages[1]) }
    <input type="checkbox" name="checkmelang" class="noborder" onclick="checkDelBoxes({literal}this.form, 'languesup[]', this.checked){/literal}"/>
    {l s='All' d='Modules.Prestablog.Prestablog'}
{/if}

{foreach $languages $language}
    <input type="checkbox" name="languesup[]" value="{(int)$language['id_lang']}"
    {if ((Tools::getValue('idN') && in_array((int) $language['id_lang'], $lang_liste_news))
        || (Tools::getValue('languesup')
        && in_array((int) $language['id_lang'], Tools::getValue('languesup')))
        || ((!Tools::getValue('idN') &&
        !Tools::getValue('languesup'))
        && ({(int) $language['id_lang']} == {(int) $dl})))}
        checked=checked
    {/if}

    {if count($languages) == 1 } style="display:none;"{/if}
    />
    <img src="../img/l/{(int) $language['id_lang']}.jpg" class="pointer indent-right prestablogflag" alt="{$language['name']|escape:'html':'UTF-8'}" title="{$language['name']|escape:'html':'UTF-8'}"
    onclick="changeTheLanguage('title', 'title¤link_rewrite¤meta_title¤meta_description¤meta_keywords¤cpara1¤cpara2', {(int)$language['id_lang']|escape:'html':'UTF-8'}, '{$language['iso_code']|escape:'html':'UTF-8'}');"/>
{/foreach}
</span>