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
<div class="prestablog_slide">
  <div class="sliders_prestablog">
    {if is_array($ListeBlogNews) && count($ListeBlogNews) > 0}
      {foreach from=$ListeBlogNews item=slide name=slides}
        {if isset($slide.id_slide)}
        
          {if isset($slide.url_associate) && $slide.url_associate != ""}
            <a href="{$slide.url_associate|escape:'htmlall':'UTF-8'}">
              {if $slide.webp_exists}
                <img src="{$prestablog_theme_slide_upimg|escape:'html':'UTF-8'}{$slide.id_slide|intval}.webp?{$md5pic|escape:'htmlall':'UTF-8'}" class="visu"
              {else}
                <img src="{$prestablog_theme_slide_upimg|escape:'html':'UTF-8'}{$slide.id_slide|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" class="visu"
              {/if}

              {if Configuration::get('prestablog_show_slide_title')}
                alt="{$slide.title|escape:'htmlall':'UTF-8'}" title="{$slide.title|escape:'htmlall':'UTF-8'}"
              {else}
                alt="{$slide.title|escape:'htmlall':'UTF-8'}"
              {/if}
              />
            </a>
          {else}
            <div>
              {if $slide.webp_exists}
                <img src="{$prestablog_theme_slide_upimg|escape:'html':'UTF-8'}{$slide.id_slide|intval}.webp?{$md5pic|escape:'htmlall':'UTF-8'}" class="visu"
              {else}
                <img src="{$prestablog_theme_slide_upimg|escape:'html':'UTF-8'}{$slide.id_slide|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" class="visu"
              {/if}

              {if Configuration::get('prestablog_show_slide_title')}
                alt="{$slide.title|escape:'htmlall':'UTF-8'}" title="{$slide.title|escape:'htmlall':'UTF-8'}"
              {else}
                alt="{$slide.title|escape:'htmlall':'UTF-8'}"
              {/if}
              />
            </div>
          {/if}
          
        {/if}
      {/foreach}
    {/if}
  </div>
</div>
<div class="clearfix"></div>
<!-- /Module Presta Blog -->

