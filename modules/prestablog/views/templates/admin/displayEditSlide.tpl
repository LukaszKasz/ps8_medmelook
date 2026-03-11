{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div class="margin-form">
{if Tools::getValue('error') == 'pos'}
    {$prestablog->displayError("<p>{l s='This position is already taken for this language' d='Modules.Prestablog.Prestablog'}</p>")}
{elseif Tools::getValue('error') == 'size'}
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


{$prestablog->get_displayFormOpen('icon-edit', "{l s='Edit your slide' d='Modules.Prestablog.Prestablog'}", $confpath)}

{$prestablog->get_displayInfo($info|escape:'html':'UTF-8')}

<div class="form-group">
    <label class="control-label col-lg-2 ">{l s='Language' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        <span id="check_lang_prestablog">
        {foreach $languages as $language}
            {assign "lid" $language['id_lang']}
            <input type="radio" name="id_lang" value="{$lid|escape:'html':'UTF-8'}"
            {if (Tools::getValue('idS') && Tools::getValue('languesup') && $lid == Tools::getValue('languesup'))}
                checked=checked
            {/if}
            />
            <img src="../img/l/{(int) $lid}.jpg" class="pointer indent-right prestablogflag" alt="{$language['name']|escape:'html':'UTF-8'}" title="{$language['name']|escape:'html':'UTF-8'}">
        {/foreach}
        <input type="radio" name="old_lang" value="{Tools::getValue('languesup')|escape:'html':'UTF-8'}" style="display:none;"  checked=checked>
        </span>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2 ">{l s='Your slide' d='Modules.Prestablog.Prestablog'}*</label>
    <div class="col-lg-7">
        <div id="image"><input type="file" name="load_img_slide" id="load_img_slide" value=""></div>
    </div>
</div>

<div class="form-group">
<div class="col-lg-2"></div>
<div class="col-lg-7">
<img class="item" src="{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/slider/{$id_slide|escape:'html':'UTF-8'}.jpg">
</div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2 ">{l s='Your title' d='Modules.Prestablog.Prestablog'}*</label>
    <div class="col-lg-7">
        <div id="title"><input type="text" name="title" id="title" value="{$title|escape:'html':'UTF-8'}"></div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2 ">{l s='Position' d='Modules.Prestablog.Prestablog'}*</label>
    <div class="col-lg-7">
        <div id="position"><input type="text" name="position" id="position" value="{$position|escape:'html':'UTF-8'}"></div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2 ">{l s='URL ' d='Modules.Prestablog.Prestablog'}</label>
    <div class="col-lg-7">
        <div id="url_associate"><input type="text" name="url_associate" id="url_associate" value="{$url_associate|escape:'html':'UTF-8'}"></div>
    </div>
</div>

<input type="hidden" name="id_slide" id="id_slide" value="{$id_slide|escape:'html':'UTF-8'}">
<input type="hidden" name="languesup" value="{Tools::getValue('languesup')|escape:'html':'UTF-8'}">
    
<button class="btn btn-primary" name="submitEditSlide" type="submit">
	<i class="icon-plus"></i>&nbsp;{l s='Edit' d='Modules.Prestablog.Prestablog'}
</button>

{$prestablog->get_displayFormClose()}
