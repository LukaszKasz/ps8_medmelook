{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<script type='text/javascript'>id_language = Number({$dl});</script>
{$prestablog->get_displayFormOpen('icon-shield', "$legend_title",$confpath)}

        {if (Tools::getValue('idAS')) }
            <input type='hidden' name='idAS' value="{Tools::getValue(idAS)|escape:'html':'UTF-8'}">
        {/if}

<div class="form-group">
    <label class="control-label col-lg-2">
        {l s='Question' d='Modules.Prestablog.Prestablog'}
    </label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign var="lid" value=$language.id_lang}
            <div id="question_{$lid}" style="display: {if $lid == $dl} block {else} none{/if};">
                <input type="text" name="question_{$lid|escape:'html':'UTF-8'}" id="question_{$lid|escape:'html':'UTF-8'}"
                    value="{if isset($antispam->question[$lid])}{$antispam->question[$lid]|escape:'html':'UTF-8'}{/if}">
            </div>
        {/foreach}
    </div>

    {if $prestablog->displayFlagsFor('question', $div_lang_name)}
        <div class="col-lg-1">
            {$prestablog->displayFlagsFor('question', $div_lang_name)}
        </div>
    {/if}
</div>

<div class="form-group">
    <label class="control-label col-lg-2">
        {l s='Expected reply' d='Modules.Prestablog.Prestablog'}
    </label>
    <div class="col-lg-7">
        {foreach $languages as $language}
            {assign var="lid" value=$language.id_lang}
            <div id="reply_{$lid|escape:'html':'UTF-8'}" style="display: {if $lid == $dl} block {else} none {/if};">
                <input type="text" name="reply_{$lid|escape:'html':'UTF-8'}" id="reply_{$lid|escape:'html':'UTF-8'}"
                    value="{if isset($antispam->reply[$lid])}{$antispam->reply[$lid]|escape:'html':'UTF-8'}{/if}">
            </div>
        {/foreach}
    </div>

    {if $prestablog->displayFlagsFor('reply', $div_lang_name)}
        <div class="col-lg-1">
            {$prestablog->displayFlagsFor('reply', $div_lang_name)}
        </div>
    {/if}
</div>

 {$prestablog->get_displayFormEnableItem(
    'col-lg-2',
    "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
    'actif',
    $antispam->actif
    )}
<div class='margin-form'>
    {if (Tools::getValue('idAS'))}
            <button class='btn btn-primary' id='submitForm' name='submitUpdateAntiSpam'>
                <i class='icon-save'></i>&nbsp;{l s='Update the AntiSpam question' d='Modules.Prestablog.Prestablog'}
            </button>
    {else}
            <button class='btn btn-primary' id='submitForm' name='submitAddAntiSpam'>
                <i class='icon-plus'></i>&nbsp;{l s='Add the AntiSpam question' d='Modules.Prestablog.Prestablog'}
            </button>
    {/if}
</div>
{$prestablog->displayFormClose()}