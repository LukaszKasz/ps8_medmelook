{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}

<!-- Module Presta Blog -->
<div id="prestablog-comments">
<h3>{l s='Add a comment' d='Modules.Prestablog.Grid'}</h3>
{if ($prestablog_config.prestablog_comment_only_login && $isLogged) || !$prestablog_config.prestablog_comment_only_login}
	{if !$isSubmit}
		<form action="{$LinkReal|escape:'html':'UTF-8'}&id={$news->id|intval}" method="post" class="std">
			<fieldset id="prestablog-comment">
				{if sizeof($errors)}
				<p id="errors">{foreach from=$errors item=Ierror name=errors}{$Ierror|escape:'htmlall':'UTF-8'}<br />{/foreach}</p>
				{/if}
				<p class="text">
					<input type="text" class="text{if sizeof($errors) && array_key_exists('name', $errors)} errors{/if}" name="name" id="name" value="{$content_form.name|escape:'htmlall':'UTF-8'}" placeholder="{l s='Name' d='Modules.Prestablog.Grid'}:" />
				</p>
				<p class="textarea">
					<textarea name="comment" id="comment" cols="26" rows="2" {if sizeof($errors) && array_key_exists('comment', $errors)}class="errors"{/if} placeholder="{l s='Comment' d='Modules.Prestablog.Grid'}:">{$content_form.comment|escape:'htmlall':'UTF-8'}</textarea>
				</p>
				{if isset($AntiSpam)}
					<p class="text">
						<label for="{$AntiSpam.checksum|escape:'htmlall':'UTF-8'}">{l s='Antispam protection' d='Modules.Prestablog.Grid'} : <strong>{$AntiSpam.question|escape:'htmlall':'UTF-8'}</strong></label>
						<input type="text" class="text{if sizeof($errors) && array_key_exists($AntiSpam.checksum, $errors)} errors{/if}" name="{$AntiSpam.checksum|escape:'htmlall':'UTF-8'}" id="{$AntiSpam.checksum|escape:'htmlall':'UTF-8'}" value="{$content_form.antispam_checksum|escape:'htmlall':'UTF-8'}" />
					</p>
				{/if}
				 {if $prestablog_config.prestablog_captcha_actif==1}
				 <div class="g-recaptcha" data-sitekey="{$prestablog_config.prestablog_captcha_public_key|escape:'htmlall':'UTF-8'}"></div>
				{/if}
				<p class="submit">
					<input type="submit" class="btn-primary" name="submitComment" id="submitComment" value="{l s='Submit comment' d='Modules.Prestablog.Grid'}" />
				</p>
			</fieldset>
		</form>
	{else}
		<form id="submitOk" class="std">
			<fieldset>
				<h3>{l s='Your comment has been successfully sent' d='Modules.Prestablog.Grid'}</h3>
				{if $prestablog_config.prestablog_comment_auto_actif}
				<p>{l s='This comment is automatically published.' d='Modules.Prestablog.Grid'}</p>
				{else}
				<p>{l s='Before published, your comment must be approve by an administrator.' d='Modules.Prestablog.Grid'}</p>
				{/if}
			</fieldset>
		</form>
	{/if}
{else}
	<form class="std">
		<fieldset id="prestablog-comment-register">
			<p style="text-align:center;">
				<a href="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}">{l s='You must be register' d='Modules.Prestablog.Grid'}<br />{l s='Clic here to registered' d='Modules.Prestablog.Grid'}</a>
			</p>
		</fieldset>
	</form>
{/if}
{if sizeof($comments)}

    {assign var="total_comments" value=0}
    
    {foreach from=$comments item=comment}
        {assign var="total_comments" value=$total_comments+1}
        
        {if isset($comment.replies) && $comment.replies|@count > 0}
            {assign var="total_comments" value=$total_comments+$comment.replies|@count}
        {/if}
    {/foreach}
    
    {$total_comments|intval} {l s='comments' d='Modules.Prestablog.Grid'}

    {if $prestablog_config.prestablog_comment_subscription}
        <div id="abo">
            {if $Is_Subscribe}
                <a href="{$LinkReal|escape:'html':'UTF-8'}&d={$news->id|intval}">{l s='Stop my subscription to comments' d='Modules.Prestablog.Grid'}</a>
            {else}
                <a href="{$LinkReal|escape:'html':'UTF-8'}&a={$news->id|intval}">{l s='Subscribe to comments' d='Modules.Prestablog.Grid'}</a>
            {/if}
        </div>
    {/if}
    
    <div id="comments">
        {foreach from=$comments item=comment}
            <div class="comment" id="comment-{$comment.id_prestablog_commentnews}">
                <p class="title_comment">
                    {l s='By' d='Modules.Prestablog.Grid'} {$comment.name|escape:'htmlall':'UTF-8'} <span class="comment_date">{dateFormat date=$comment.date full=1}</span>
                </p>
                <p>{$comment.comment|escape:'htmlall':'UTF-8'}</p>
                
                <p class="reply">
                    
                    {if isset($comment.replies) && $comment.replies|@count > 0}
                        <a href="javascript:void(0)" class="toggle-replies" data-target="replies-{$comment.id_prestablog_commentnews}">
                            {l s='View replies' d='Modules.Prestablog.Grid'}
                        </a>  ({$comment.replies|@count|escape:'htmlall':'UTF-8'}) - 
                    {/if}
                        <a href="javascript:void(0)" class="reply-link" data-target="reply-container-{$comment.id_prestablog_commentnews}" data-logged="{$isLogged|intval}">
                            {l s='Reply' d='Modules.Prestablog.Grid'}
                        </a>
                </p>
                <div class="reply-container" id="reply-container-{$comment.id_prestablog_commentnews}">
                    {if $isLogged || !$prestablog_config.prestablog_comment_only_login}
                        <form action="{$LinkReal|escape:'html':'UTF-8'}&id={$news->id|intval}" method="post" class="std">
                            <fieldset id="prestablog-reply">
                                {if sizeof($errors)}
                                    <p id="errors">{foreach from=$errors item=Ierror name=errors}{$Ierror|escape:'htmlall':'UTF-8'}<br />{/foreach}</p>
                                {/if}
                                <p class="text">
                                    <input type="text" class="text{if sizeof($errors) && array_key_exists('name', $errors)} errors{/if}" name="name" id="name" value="{$content_form.name|escape:'htmlall':'UTF-8'}" placeholder="{l s='Name' d='Modules.Prestablog.Grid'}:" />
                                </p>
                                <p class="textarea">
                                    <textarea name="comment" id="comment" cols="26" rows="2" {if sizeof($errors) && array_key_exists('comment', $errors)}class="errors"{/if} placeholder="{l s='Reply' d='Modules.Prestablog.Grid'}:">{$content_form.comment|escape:'htmlall':'UTF-8'}</textarea>
                                </p>

                                <input type="hidden" name="id_parent" value="{$comment.id_prestablog_commentnews}" />

                                {if isset($AntiSpam)}
                                    <p class="text">
                                        <label for="{$AntiSpam.checksum|escape:'htmlall':'UTF-8'}">{l s='Antispam protection' d='Modules.Prestablog.Grid'} : <strong>{$AntiSpam.question|escape:'htmlall':'UTF-8'}</strong></label>
                                        <input type="text" class="text{if sizeof($errors) && array_key_exists($AntiSpam.checksum, $errors)} errors{/if}" name="{$AntiSpam.checksum|escape:'htmlall':'UTF-8'}" id="{$AntiSpam.checksum|escape:'htmlall':'UTF-8'}" value="{$content_form.antispam_checksum|escape:'htmlall':'UTF-8'}" />
                                    </p>
                                {/if}
                                {if $prestablog_config.prestablog_captcha_actif == 1}
                                    <div class="g-recaptcha" data-sitekey="{$prestablog_config.prestablog_captcha_public_key|escape:'htmlall':'UTF-8'}"></div>
                                {/if}
                                <p class="submit">
                                    <input type="submit" class="btn-primary" name="submitComment" id="submitComment" value="{l s='Submit comment' d='Modules.Prestablog.Grid'}" />
                                </p>
                            </fieldset>
                        </form>
                    {else}
                        <p class="login-message">
                            {l s='You must be register' d='Modules.Prestablog.Grid'}
                            <a href="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}">{l s='Clic here to registered' d='Modules.Prestablog.Grid'}</a>
                        </p>
                    {/if}
                </div>

                {if isset($comment.replies) && $comment.replies|@count > 0}
                    <div class="replies" id="replies-{$comment.id_prestablog_commentnews}">
                        {foreach from=$comment.replies item=reply}
                            {if $reply.actif == 1}
                            <div class="comment-reply">
                                <p class="title_comment">
                                    {l s='By' d='Modules.Prestablog.Grid'} {$reply.name|escape:'htmlall':'UTF-8'} 
                                    {if $reply.is_admin}
                                        <span class="author-indicator">(<i class="material-icons">account_circle</i> {l s='Blog Author' d='Modules.Prestablog.Grid'})</span>
                                    {/if}
                                    <span class="comment_date">{dateFormat date=$reply.date full=1}</span>
                                </p>
                                <p>{$reply.comment|escape:'htmlall':'UTF-8'}</p>
                            </div>
                            {/if}
                        {/foreach}
                    </div>
                {/if}
            </div>
        {/foreach}
    </div>
{/if}
</div>
<!-- /Module Presta Blog -->
