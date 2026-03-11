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
{if $prestablog_categorie_obj->image_presente && $prestablog_config.prestablog_view_cat_img}
    <picture class="prestablog_cat_img">
        {if isset($prestablog_categorie_obj->webp_present) and $prestablog_categorie_obj->webp_present}
        <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}c/full_{$prestablog_categorie_obj->id|intval}.webp?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/webp">
        {/if}
        <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}c/full_{$prestablog_categorie_obj->id|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/jpeg">
        <img class="prestablog_cat_img" src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}c/full_{$prestablog_categorie_obj->id|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$prestablog_categorie_obj->title|escape:'htmlall':'UTF-8'}">
    </picture>
{/if}
{if $prestablog_categorie_obj->image_presente && $prestablog_config.prestablog_view_cat_thumb}
    <picture class="prestablog_thumb_cat">
        {if isset($prestablog_categorie_obj->webp_present) and $prestablog_categorie_obj->webp_present}
        <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}c/thumb_{$prestablog_categorie_obj->id|intval}.webp?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/webp">
        {/if}
        <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}c/thumb_{$prestablog_categorie_obj->id|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/jpeg">
        <img class="prestablog_thumb_cat" src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}c/thumb_{$prestablog_categorie_obj->id|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$prestablog_categorie_obj->title|escape:'htmlall':'UTF-8'}">
    </picture>
{/if}
{if isset($prestablog_categorie_obj->description) && $prestablog_config.prestablog_view_cat_desc}
<div class="cat_desc_blog" itemprop="description" >{PrestaBlogContent return=$prestablog_categorie_obj->descri}</div>
{/if}
<div class="clearfix"></div>
<!-- /Module Presta Blog -->
