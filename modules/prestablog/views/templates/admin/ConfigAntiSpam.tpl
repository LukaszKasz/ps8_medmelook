{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{$prestablog->get_displayFormOpen('icon-shield', "{l s='Antispam questions' d='Modules.Prestablog.Prestablog'}",$confpath)}
<div class="bootstrap">
    <div class="alert alert-info">
        <strong>
            {l s='Information' d='Modules.Prestablog.Prestablog'}.
        </strong><br />
        <p>
            {l s='This Antispam option can protect you to comments of spammers robots.' d='Modules.Prestablog.Prestablog'}
        </p>
        <p>
            {l s='Will random in the comment form.' d='Modules.Prestablog.Prestablog'}
        </p>
    </div>
</div>
{$prestablog->get_displayFormEnableItemConfiguration('col-lg-2', "{l s='Antispam activation' d='Modules.Prestablog.Prestablog'}",
"{$prestablog->name}_antispam_actif")}
{$prestablog->get_displayFormSubmit('submitAntiSpamConfig','icon-save', "{l s='Update the configuration' d='Modules.Prestablog.Prestablog'}")}
{$prestablog->get_displayFormClose()}

<div class="blocmodule">
    <fieldset>
        <div class="col-sm-3">
            <a class="btn btn-primary" href="{$confpath|escape:'html':'UTF-8'}&addAntiSpam">
                <i class="icon-plus"></i>&nbsp;
                {l s='Add an antispam question' d='Modules.Prestablog.Prestablog'}
            </a>
        </div>
    </fieldset>
</div>

<div class="blocmodule">
    <table class="table" cellpadding="0" cellspacing="0" style="width:100%;margin:auto;">
        <thead class="center">
            <tr>
                <th></th>
                <th>{l s='Question' d='Modules.Prestablog.Prestablog'}</th>
                <th>{l s='Expected reply' d='Modules.Prestablog.Prestablog'}</th>
                <th class="center">{l s='Activate' d='Modules.Prestablog.Prestablog'}</th>
                <th class="center">{l s='Actions' d='Modules.Prestablog.Prestablog'}</th>
            </tr>
        </thead>
        {if count($liste)}
            {foreach $liste "value"}
                <tr>
                    <td class="center">{$value['id_prestablog_antispam']|escape:'html':'UTF-8'}</td>
                    <td>{$value['question']|escape:'html':'UTF-8'}</td>
                    <td>{$value['reply']|escape:'html':'UTF-8'}</td>
                    <td class="center">
                        <a href="{$confpath}&etatAntiSpam&idAS={$value['id_prestablog_antispam']|escape:'html':'UTF-8'}">
                            {if $value['actif']}
                                <i class="material-icons action-enabled" style="color: #78d07d;">check</i>
                            {else}
                                <i class="material-icons action-disabled" style="color: #c05c67;">clear</i>
                            {/if}
                        </a>
                    </td>
                    <td class="center">
                        <a href="{$confpath|escape:'html':'UTF-8'}&editAntiSpam&idAS={$value['id_prestablog_antispam']|escape:'html':'UTF-8'}:'html'}"
                            title="{l s='Edit' d='Modules.Prestablog.Prestablog'}">
                            <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                        </a>
                        <a href="{$confpath|escape:'html':'UTF-8'}&deleteAntiSpam&idAS={$value['id_prestablog_antispam']|escape:'html':'UTF-8'}:'html'}"
                            onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                            <i class="material-icons" style="color: #c05c67;">delete</i>
                        </a>
                    </td>
                </tr>

            {/foreach}
        {else}
            <tr>
                <td colspan="5" class="center">{l s='No content registered' d='Modules.Prestablog.Prestablog'}</td>
            </tr>
        {/if}
    </table>
</div>