{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div class="blocmodule">
    <fieldset>
        <legend> <i class="icon-edit"></i> {l s='Edit Permissions for' d='Modules.Prestablog.Prestablog'} {$author->firstname|escape:'html':'UTF-8'} {$author->lastname|escape:'html':'UTF-8'}</legend>

        <div class="form-group">
            <button type="button" class="btn btn-success" onclick="toggleAllPermissions(true)">
                {l s='Enable All' d='Modules.Prestablog.Prestablog'}
            </button>
            <button type="button" class="btn btn-danger" onclick="toggleAllPermissions(false)">
                {l s='Disable All' d='Modules.Prestablog.Prestablog'}
            </button>
        </div>

        {if isset($success) && $success == '1'}
            <div class="margin-form">
                <div class="module_confirmation conf confirm alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {l s='Permissions updated successfully' d='Modules.Prestablog.Prestablog'}
                </div>
            </div>
        {/if}

        {if isset($errors) && $errors|@count > 0}
            <div class="margin-form">
                <div class="module_confirmation conf error alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {foreach $errors as $error}
                        <p>{$error}</p>
                    {/foreach}
                </div>
            </div>
        {/if}
        {if Configuration::get('prestablog_enable_permissions') != 1}
            <div class="alert alert-warning">
                {l s='The author permission system is not enabled. The permissions you are configuring will not be applied until the system is activated in author > configuration.' d='Modules.Prestablog.Prestablog'}
            </div>
        {/if}

<form method="post" action="{$confpath|escape:'html':'UTF-8'}&submitAuthorPermissions&id_author={$author->id_author|escape:'html':'UTF-8'}" enctype="multipart/form-data">

    <div class="row">
        <div class="col-lg-5">
            <fieldset>
                <legend>{l s='Article, Category, and Comment Permissions' d='Modules.Prestablog.Prestablog'}</legend>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>{l s='Add or Edit Article' d='Modules.Prestablog.Prestablog'}</label>
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="can_add_article" id="can_add_article_on" value="1" {if $permissions.can_add_article == 1}checked{/if}>
                            <label for="can_add_article_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                            <input type="radio" name="can_add_article" id="can_add_article_off" value="0" {if $permissions.can_add_article == 0}checked{/if}>
                            <label for="can_add_article_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>{l s='Edit Article' d='Modules.Prestablog.Prestablog'}</label>
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="can_edit_article" id="can_edit_article_on" value="1" {if $permissions.can_edit_article == 1}checked{/if}>
                            <label for="can_edit_article_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                            <input type="radio" name="can_edit_article" id="can_edit_article_off" value="0" {if $permissions.can_edit_article == 0}checked{/if}>
                            <label for="can_edit_article_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>{l s='Delete Article' d='Modules.Prestablog.Prestablog'}</label>
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="can_delete_article" id="can_delete_article_on" value="1" {if $permissions.can_delete_article == 1}checked{/if}>
                            <label for="can_delete_article_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                            <input type="radio" name="can_delete_article" id="can_delete_article_off" value="0" {if $permissions.can_delete_article == 0}checked{/if}>
                            <label for="can_delete_article_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>{l s='Activate or deactivate article' d='Modules.Prestablog.Prestablog'}</label>
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="can_activate_article" id="can_activate_article_on" value="1" {if $permissions.can_activate_article == 1}checked{/if}>
                            <label for="can_activate_article_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                            <input type="radio" name="can_activate_article" id="can_activate_article_off" value="0" {if $permissions.can_activate_article == 0}checked{/if}>
                            <label for="can_activate_article_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>

                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>{l s='Create or Edit Category' d='Modules.Prestablog.Prestablog'}</label>
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="can_create_category" id="can_create_category_on" value="1" {if $permissions.can_create_category == 1}checked{/if}>
                            <label for="can_create_category_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                            <input type="radio" name="can_create_category" id="can_create_category_off" value="0" {if $permissions.can_create_category == 0}checked{/if}>
                            <label for="can_create_category_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>{l s='Delete Category' d='Modules.Prestablog.Prestablog'}</label>
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="can_delete_category" id="can_delete_category_on" value="1" {if $permissions.can_delete_category == 1}checked{/if}>
                            <label for="can_delete_category_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                            <input type="radio" name="can_delete_category" id="can_delete_category_off" value="0" {if $permissions.can_delete_category == 0}checked{/if}>
                            <label for="can_delete_category_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>{l s='Manage Comments' d='Modules.Prestablog.Prestablog'}</label>
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="can_manage_comments" id="can_manage_comments_on" value="1" {if $permissions.can_manage_comments == 1}checked{/if}>
                            <label for="can_manage_comments_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                            <input type="radio" name="can_manage_comments" id="can_manage_comments_off" value="0" {if $permissions.can_manage_comments == 0}checked{/if}>
                            <label for="can_manage_comments_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="col-lg-4">
            <fieldset>
                <legend>{l s='Manage Contents Permissions' d='Modules.Prestablog.Prestablog'}</legend>

                <div class="form-group">
                    <label>{l s='Manage Popup' d='Modules.Prestablog.Prestablog'}</label>
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="can_manage_popup" id="can_manage_popup_on" value="1" {if $permissions.can_manage_popup == 1}checked{/if}>
                        <label for="can_manage_popup_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                        <input type="radio" name="can_manage_popup" id="can_manage_popup_off" value="0" {if $permissions.can_manage_popup == 0}checked{/if}>
                        <label for="can_manage_popup_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                        <a class="slide-button btn"></a>
                    </span>
                </div>

                <div class="form-group">
                    <label>{l s='Manage Slide' d='Modules.Prestablog.Prestablog'}</label>
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="can_manage_slide" id="can_manage_slide_on" value="1" {if $permissions.can_manage_slide == 1}checked{/if}>
                        <label for="can_manage_slide_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                        <input type="radio" name="can_manage_slide" id="can_manage_slide_off" value="0" {if $permissions.can_manage_slide == 0}checked{/if}>
                        <label for="can_manage_slide_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                        <a class="slide-button btn"></a>
                    </span>
                </div>

                <div class="form-group">
                    <label>{l s='Manage Personalised List' d='Modules.Prestablog.Prestablog'}</label>
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="can_manage_personalised_list" id="can_manage_personalised_list_on" value="1" {if $permissions.can_manage_personalised_list == 1}checked{/if}>
                        <label for="can_manage_personalised_list_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                        <input type="radio" name="can_manage_personalised_list" id="can_manage_personalised_list_off" value="0" {if $permissions.can_manage_personalised_list == 0}checked{/if}>
                        <label for="can_manage_personalised_list_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </fieldset>
        </div>
        <div class="col-lg-3">
            <fieldset>
                <legend>{l s='Configuration Permissions' d='Modules.Prestablog.Prestablog'}</legend>

                <div class="form-group">
                    <label>{l s='Configure Module' d='Modules.Prestablog.Prestablog'}</label>
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="can_configure_module" id="can_configure_module_on" value="1" {if $permissions.can_configure_module == 1}checked{/if}>
                        <label for="can_configure_module_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                        <input type="radio" name="can_configure_module" id="can_configure_module_off" value="0" {if $permissions.can_configure_module == 0}checked{/if}>
                        <label for="can_configure_module_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                        <a class="slide-button btn"></a>
                    </span>
                </div>

                <div class="form-group">
                    <label>{l s='Use Tool' d='Modules.Prestablog.Prestablog'}</label>
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="can_use_tool" id="can_use_tool_on" value="1" {if $permissions.can_use_tool == 1}checked{/if}>
                        <label for="can_use_tool_on">{l s='Yes' d='Modules.Prestablog.Prestablog'}</label>

                        <input type="radio" name="can_use_tool" id="can_use_tool_off" value="0" {if $permissions.can_use_tool == 0}checked{/if}>
                        <label for="can_use_tool_off">{l s='No' d='Modules.Prestablog.Prestablog'}</label>

                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="margin-form">
        <button type="submit" name="submitAuthorPermissions" class="btn btn-primary">
            {l s='Save' d='Modules.Prestablog.Prestablog'}
        </button>
    </div>
</form>
    </fieldset>
</div>
<script type="text/javascript">
    function toggleAllPermissions(enable) {
        var radios = document.querySelectorAll('input[type="radio"][name^="can_"]');
        radios.forEach(function(radio) {
            if (radio.value == (enable ? "1" : "0")) {
                radio.checked = true;
            }
        });
    }
</script>