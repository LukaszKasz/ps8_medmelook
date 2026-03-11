{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if Tools::getValue('success') == 'su'}
	{if !Tools::getIsset('reload')}
	    Tools::redirect("Location:{$confpath|escape:'html':'UTF-8'}&configSlide&success=su&reload")
	{/if}

	<div class="margin-form">
	<div class="module_confirmation conf confirm alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>
		{l s='Settings updated successfully' d='Modules.Prestablog.Prestablog'}
	</div>
	</div>
{/if}

{if Tools::getValue('errorslide') == 'la'}
    {$prestablog->displayError("<p>{l s='This lang is already taken' d='Modules.Prestablog.Prestablog'}</p>")}
{/if}

{$prestablog->displayConfSlide()}

<div class="margin-form">
<div class="blocmodule">
    <div class="col-sm-3" style="float:left; width:25%; margin-top:10px; margin-bottom: 20px;">
    <a class="btn btn-primary" href="{$confpath|escape:'html':'UTF-8'}&addSlide{if Tools::getValue('languesup')}&languesup={Tools::getValue('languesup')|escape:'html':'UTF-8'}{/if}">
        <i class="icon-plus"></i>&nbsp;
        {l s='Add a slide' d='Modules.Prestablog.Prestablog'}
    </a>
    </div>
    <div class="clearfix"></div>

    <div class="form-group">
      <label class="control-label col-lg-1">{l s='Language' d='Modules.Prestablog.Prestablog'}</label>
      <div class="col-lg-7">
        {foreach $languages as $language}
            {assign var="lid" value=$language['id_lang']}
            <input type="radio" name="id_lang" value="{$lid|escape:'html':'UTF-8'}"
                onclick="location.href='{$confpath}&configSlide&languesup={$lid}'"
                {if Tools::getValue('languesup') == $lid}
                    checked="checked"
                {/if}
            />
            <img src="../img/l/{$lid}.jpg" class="pointer indent-right prestablogflag" alt="{$language['name']}" title="{$language['name']}">
        {/foreach}

        {if count($languages) != 1}
            <input type="radio" name="id_lang" value="all"
                onclick="location.href='{$confpath}&configSlide'"
                {if !Tools::getValue('languesup')}
                    checked="checked"
                {/if}
            /> All
        {/if}
      </div>
    </div>

    {if Tools::getValue('languesup')}
        {assign var="slider" value=SliderClass::getListSlider((int) $context->shop->id, Tools::getValue('languesup'))}
    {else}
        {assign var="slider" value=SliderClass::getListSlider((int) $context->shop->id)}
    {/if}
    <div class="clearfix"></div>
    <div id="slides">
        <div class="row">
            {assign var="maxLength" value=80}

            {foreach $slider as $slide}
                <div class="col-md-3">
                    <div id="slides_{$slide.id_slide}">
                        <img class="item" src="{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/slider/{$slide.id_slide|escape:'html':'UTF-8'}.jpg" style="max-width: 100%; height: auto;">
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 20px;">

                            {if Tools::getValue('languesup')}
                                {foreach $languages as $language}
                                    {if Tools::getValue('languesup') == $language.id_lang}
                                        <div class="flags_gauche">
                                            <img src="../img/l/{$language.id_lang|escape:'html':'UTF-8'}.jpg" class="indent-right prestablogflag" alt="{$language.name|escape:'html':'UTF-8'}" title="{$language.name|escape:'html':'UTF-8'}">
                                        </div>
                                        {break}
                                    {/if}
                                {/foreach}
                            {/if}

                            {if !Tools::getValue('languesup')}
                                <div class="flags">
                                    {foreach $languages as $language}
                                        {assign var="isAvailable" value=false}

                                        {foreach $slide.languages as $slideLang}
                                            {if $slideLang.id_lang == $language.id_lang}
                                                {assign var="isAvailable" value=true}
                                                {break}
                                            {/if}
                                        {/foreach}

                                        {if $isAvailable}
                                            <img src="../img/l/{$language.id_lang|escape:'html':'UTF-8'}.jpg" class="indent-right prestablogflag" alt="{$language.name|escape:'html':'UTF-8'}" title="{$language.name|escape:'html':'UTF-8'}">
                                        {else}
                                            <img src="../img/l/{$language.id_lang|escape:'html':'UTF-8'}.jpg" class="indent-right prestablogflag" alt="{$language.name|escape:'html':'UTF-8'}" title="{$language.name|escape:'html':'UTF-8'}" style="opacity: 0.1;">
                                        {/if}
                                    {/foreach}
                                </div>
                            {/if}

                            <h4 style="margin-top: 5px;">
                                  <strong>{l s='Title' d='Modules.Prestablog.Prestablog'} : </strong>{$slide.languages[0].title|escape:'html':'UTF-8'|truncate:$maxLength:"...":true}
                            </h4>

                            <p><strong>{l s='Position' d='Modules.Prestablog.Prestablog'} : </strong>{$slide.position|escape:'html':'UTF-8'}</p>

                            <div class="btn-group-action">
                                <a class="btn btn-default"
                                   href="{$confpath|escape:'html':'UTF-8'}&removeSlide&idS={$slide.id_slide|escape:'html':'UTF-8'}&idlang={$slide.languages[0].id_lang|escape:'html':'UTF-8'}{if Tools::getValue('languesup')}&languesup={Tools::getValue('languesup')|escape:'html':'UTF-8'}{/if}"
                                   onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                                   <i class="icon-trash" style="color: #6c868e;"></i>
                                   {l s='Delete' d='Modules.Prestablog.Prestablog'}
                                </a>

                                {if count($slide.languages) > 1 && !Tools::getValue('languesup')}
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="editLangDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {l s='Edit' d='Modules.Prestablog.Prestablog'} <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="editLangDropdown">
                                            {foreach $languages as $language}
                                                {assign var="isAvailable" value=false}

                                                {foreach $slide.languages as $slideLang}
                                                    {if $slideLang.id_lang == $language.id_lang}
                                                        {assign var="isAvailable" value=true}
                                                        {break}
                                                    {/if}
                                                {/foreach}

                                                {if $isAvailable}
                                                    <li>
                                                        <a href="{$confpath|escape:'html':'UTF-8'}&editSlide&idS={$slide.id_slide|escape:'html':'UTF-8'}&languesup={$language.id_lang|escape:'html':'UTF-8'}">{l s='Edit' d='Modules.Prestablog.Prestablog'} 
                                                            <img src="../img/l/{$language.id_lang|escape:'html':'UTF-8'}.jpg" class="indent-right prestablogflag" alt="{$language.name|escape:'html':'UTF-8'}" title="{$language.name|escape:'html':'UTF-8'}">

                                                        </a>
                                                    </li>
                                                {/if}
                                            {/foreach}
                                        </ul>
                                    </div>
                                {else}
                                    <a class="btn btn-default"
                                       href="{$confpath|escape:'html':'UTF-8'}&editSlide&idS={$slide.id_slide|escape:'html':'UTF-8'}&languesup={$slide.languages[0].id_lang|escape:'html':'UTF-8'}">
                                       <i class="icon-edit" style="color: #6c868e;"></i>
                                       {l s='Edit' d='Modules.Prestablog.Prestablog'}
                                    </a>
                                {/if}

                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>
<script type="text/javascript" src="../modules/prestablog/views/js/numbers.js"></script>
