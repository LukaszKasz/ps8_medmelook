{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if Tools::getIsset('success')}
    {$prestablog->displayConfirmation("{l s='Settings updated successfully' d='Modules.Prestablog.Prestablog'}")}
{/if}

{$prestablog->get_displayInfo($infocom)}
<div class='col-md-6'>
    {$prestablog->get_displayFormOpen('comments.png',"{l s='Comments' d='Modules.Prestablog.Prestablog'}",$confpath)}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_comment_actif")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Only registered users can publish a comment' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_comment_only_login")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Auto approve comments' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_comment_auto_actif")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Inform admin by email for a new comment' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_comment_alert_admin")}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Admin Mail' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_comment_admin_mail",
    Configuration::get("{$prestablog->name}_comment_admin_mail"), 10, 'col-lg-4', NULL,NULL,'<i
        class="icon-envelope-o"></i>')}

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Captcha' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_captcha_actif")}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Clé publique captcha google' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_captcha_public_key",
    Configuration::get("{$prestablog->name}_captcha_public_key"), 10, 'col-lg-4', NULL,NULL)}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Clé privée captcha google' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_captcha_private_key",
    Configuration::get("{$prestablog->name}_captcha_private_key"), 10, 'col-lg-4', NULL,NULL)}

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Mail user subscription' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_comment_subscription","{l s='Only registered users can subscribe' d='Modules.Prestablog.Prestablog'}")}

    {$prestablog->get_displayFormSubmit('submitConfComment', 'icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}
    
</div><div class='col-md-6'>
    {$prestablog->get_displayFormOpen('facebook.png',"{l s='Facebook comments' d='Modules.Prestablog.Prestablog'}",$confpath)}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_commentfb_actif")}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Number of comments visible' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_commentfb_nombre",
    Configuration::get("{$prestablog->name}_commentfb_nombre"), 10, 'col-lg-4')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='API Facebook Id' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_commentfb_apiId",
    Configuration::get("{$prestablog->name}_commentfb_apiId"), 20, 'col-lg-7','',"{l s='Optional' d='Modules.Prestablog.Prestablog'} -
    {l s='You can manage comments directly on facebook application.' d='Modules.Prestablog.Prestablog'}")}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Add global moderator' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_commentfb_modosId","",
    20, 'col-lg-7', "","{l s='Add a facebook accounts ID for beeing comments moderators.' d='Modules.Prestablog.Prestablog'}<br>{l s='ID Can be found on' d='Modules.Prestablog.Prestablog'} <a href='http://findmyfbid.com' target='_blank'>http://findmyfbid.com</a>")}

    {$prestablog->get_displayFormSubmit('submitConfCommentFB', 'icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}

    {$prestablog->get_displayFormOpen('facebook.png',"{l s='Facebook moderators' d='Modules.Prestablog.Prestablog'}",$confpath)}
    {if is_array($list_fb_moderators) && count($list_fb_moderators) > 0}
        {foreach $list_fb_moderators "value_list_fb_moderators"}
                <div class='col-md-6'>
                    <div class='blocmodule'>
                        <i class='icon-facebook'></i> {$value_list_fb_moderators|escape:'html':'UTF-8'}
                        <a style="float:right" onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}')"
                        href="{$confpath|escape:'html':'UTF-8'}&deleteFacebookModerator&fb_moderator_id={$value_list_fb_moderators|escape:'html':'UTF-8'}">
                            <i class='icon-trash'></i> {l s='Delete' d='Modules.Prestablog.Prestablog'}
                        </a>
                    </div>
                </div>
        {/foreach}

    {else}
        {l s='No moderators configured' d='Modules.Prestablog.Prestablog'}
    {/if}

    {$prestablog->get_displayFormClose()}
</div>
</div>
</div>