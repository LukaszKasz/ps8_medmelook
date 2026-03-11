{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{$prestablog->get_displayFormOpen('icon-edit', $legend_title, $confpath)}

{if Tools::getValue('idC')}
    <input type="hidden" name="idC" value="{Tools::getValue('idC')}" />
{/if}

{assign "title_news" NewsClass::getTitleNews((int) $comment->news, (int) $laguageId)}

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Parent news' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-5" style="line-height: 35px;">
        <a href='{$confpath|escape:'html':'UTF-8'}&editNews&idN={$comment->news|escape:'html':'UTF-8'}' 
        onclick="return confirm('{l s='You will leave this page. Are you sure ?' d='Modules.Prestablog.Prestablog'}');">
            {$title_news|escape:'html':'UTF-8'}
        </a>
    </div>
</div>

{$prestablog->get_displayFormInput('col-lg-2', "{l s='Name' d='Modules.Prestablog.Prestablog'}", 'name', $comment->name, 50, 'col-lg-4')}

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Comment' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        <textarea id='comment' name='comment'>{$comment->comment|escape:'html':'UTF-8'}</textarea>
    </div>
</div>

{$prestablog->get_displayFormDate('col-lg-2', "{l s='Date' d='Modules.Prestablog.Prestablog'}", 'date', $comment->date, true)}
{$prestablog->get_displayFormSelect('col-lg-2', "{l s='Status' d='Modules.Prestablog.Prestablog'}", 'actif', $comment->actif,
    $array, null, 'col-lg-3', null, null, '<i class="icon-eye"></i>')}

<div class="margin-form">


    {if Tools::getValue('idC')}
        <div class="col-lg-3">
            <button class="btn btn-primary" name="submitUpdateComment" type="submit">
                <i class="icon-save"></i>&nbsp;{l s='Update the comment' d='Modules.Prestablog.Prestablog'}
            </button>
        </div>
        <div class="col-lg-2">
            <a class="btn btn-default" href="{$confpath|escape:'html':'UTF-8'}&deleteComment&idC={$comment->id|escape:'html':'UTF-8'}"
                onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                <i class="icon-trash-o"></i>&nbsp;{l s='Delete the comment' d='Modules.Prestablog.Prestablog'}
            </a>
        </div>
        {else}
        <div class="col-lg-2">
            <button class="btn btn-primary" name="submitAddComment" type="submit">
                <i class="icon-plus"></i>&nbsp;{l s='Add the comment' d='Modules.Prestablog.Prestablog'}
            </button>
        </div>
    {/if}

</div>

{$prestablog->get_displayFormClose()}