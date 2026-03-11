{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if isset($errors) && $errors|@count > 0}
    {foreach from=$errors item=error}
        <div class="alert alert-danger">{$error}</div>
    {/foreach}
{/if}
{$prestablog->get_displayFormOpen('icon-reply', "{l s='Reply to comment' d='Modules.Prestablog.Admin'}", $confpath)}

<input type="hidden" name="id_parent" value="{$id_parent}" />

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Replying to' d='Modules.Prestablog.Admin'}</label>
    <div class="col-lg-7">
        <blockquote>
            <p>{$parent_comment->comment|escape:'html':'UTF-8'}</p>
            <footer>{$parent_comment->name|escape:'html':'UTF-8'}, {$parent_comment->date|date_format:"%d/%m/%Y %H:%M"}</footer>
        </blockquote>
    </div>
</div>

<div class="form-group">
    <label class="col-lg-2 control-label">{l s='Name' d='Modules.Prestablog.Admin'}</label>
    <div class="col-lg-4">
        <input type="text" name="name" value="{$author_name|escape:'html':'UTF-8'}" maxlength="50" required />
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">{l s='Reply' d='Modules.Prestablog.Admin'}</label>
    <div class="col-lg-7">
        <textarea id='comment' name='comment' required></textarea>
    </div>
</div>

{$prestablog->get_displayFormDate('col-lg-2', "{l s='Date' d='Modules.Prestablog.Admin'}", 'date', date('Y-m-d H:i:s'), true)}

<div class="margin-form">
    <div class="col-lg-2">
        <button class="btn btn-primary" name="submitReplyComment" type="submit">
            <i class="icon-reply"></i>&nbsp;{l s='Post Reply' d='Modules.Prestablog.Admin'}
        </button>
    </div>
</div>

{$prestablog->get_displayFormClose()}