{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{assign "html_slide" ""}
{assign "html_lang" ""}
{assign "html_title" ""}
{assign "html_url_associate" ""}

{if Tools::getValue('error') == 'size'}
    {assign "error_msg" {l s='Sorry, your file was not uploaded. Your image needs to be less than ' d='Modules.Prestablog.Prestablog'}}
    {assign "error_msg" "{$error_msg|escape:'html':'UTF-8'}{(int) Configuration::get('prestablog_slide_picture_width')}"}
    {assign "error_msg" "{$error_msg|escape:'html':'UTF-8'}{l s=' px width and ' d='Modules.Prestablog.Prestablog'}"}
    {assign "error_msg" "{$error_msg|escape:'html':'UTF-8'}{(int) Configuration::get('prestablog_slide_picture_height')}"}
    {assign "error_msg" "{$error_msg|escape:'html':'UTF-8'}{l s=' px height' d='Modules.Prestablog.Prestablog'}"}
    {$prestablog->displayError({$error_msg|escape:'html':'UTF-8'})}
{elseif Tools::getValue('errorslide') == 'la'}
    {$prestablog->displayError("<p>{l s='This lang is already taken' d='Modules.Prestablog.Prestablog'}</p>")}
{elseif Tools::getValue('error') == 'title'}
    {$prestablog->displayError({l s='Title must be field' d='Modules.Prestablog.Prestablog'})}
{elseif Tools::getValue('error') == 'image'}
    {$prestablog->displayError({l s='Image must be upload' d='Modules.Prestablog.Prestablog'})}
{/if}
{$prestablog->get_displayFormOpen('icon-edit', $legend_title, $confpath)}
{$prestablog->get_displayInfo($info)}

<div class='margin-form'>

    <div class="form-group">
        <label class="control-label col-lg-2">
            {l s='Language' d='Modules.Prestablog.Prestablog'}
        </label>
        <div class="col-lg-5">
            <span id='check_lang_prestablog'>
                {foreach $languages as $language}
                    {assign var="lid" value=$language.id_lang}
                    {assign var="lname" value=$language.name}

                    <input type='radio' name='id_lang' value='{$lid|escape:'html':'UTF-8'}'
                        {if Tools::getValue('languesup') == $lid} checked="checked" {/if}>
                    <img src='../img/l/{$lid|escape:'html':'UTF-8'}.jpg' class='pointer indent-right prestablogflag' alt='{$lname|escape:'html':'UTF-8'}' title='{$lname|escape:'html':'UTF-8'}'>			
                {/foreach}

                {if count($languages) != 1}
                    <input type='radio' name='id_lang' value='all'
                        {if !Tools::getValue('languesup') || Tools::getValue('languesup') == 'all'} checked="checked" {/if}>
                    {l s='All' d='Modules.Prestablog.Prestablog'}
                {/if}
            </span>
        </div>
    </div>	

    <div class="form-group">
        <label class="control-label col-lg-2">
            {l s='Your slide' d='Modules.Prestablog.Prestablog'}*
        </label>
        <div class="col-lg-7">
            <div id='image'>
                <input type='file' name='load_img_slide' id='load_img_slide' value='' required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">
            {l s='Your title' d='Modules.Prestablog.Prestablog'}*
        </label>
        <div class="col-lg-7">
            <div id='title'>
                <input type='text' name='title' id='title' value='' required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">
            {l s='URL' d='Modules.Prestablog.Prestablog'}
        </label>
        <div class="col-lg-7">
            <div id='url_associate'>
                <input type='text' name='url_associate' id='url_associate' value=''>
            </div>
        </div>
    </div>
    <input type="hidden" name="languesup" value="{Tools::getValue('languesup')|escape:'html':'UTF-8'}">
    <button class='btn btn-primary' name='submitAddSlide' type='submit'>
        <i class='icon-plus'></i>&nbsp;{l s='Add a slide' d='Modules.Prestablog.Prestablog'}
    </button>

</div>
 

{$prestablog->displayFormClose()}
