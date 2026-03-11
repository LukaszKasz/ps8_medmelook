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
<section class="footer-block col-xs-12 col-sm-6">
   <h4 class="prestablog-footer-title">{l s='Last blog articles' d='Modules.Prestablog.Grid'}</h4>
   <ul class="prestablog-footer">
      {if $ListeBlocLastNews}
         {foreach from=$ListeBlocLastNews item=Item name=myLoop}
            <li>
               {if isset($Item.link_for_unique)}<a href="{PrestaBlogUrl id=$Item.id_prestablog_news seo=$Item.link_rewrite titre=$Item.title}">{/if}
               
                {if $Item.image_presente && $footlastnews_showthumb}
                    <picture>
                        {if $Item.adminth_webp_present}
                            <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}adminth_{$Item.id_prestablog_news|intval}.webp?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/webp">
                        {/if}
                        <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}adminth_{$Item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/jpeg">
                        <img loading="lazy" src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}adminth_{$Item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$Item.title|escape:'htmlall':'UTF-8'}" class="prestablog-footer-img" />
                    </picture>
                {/if}               
                <strong>{$Item.title|escape:'htmlall':'UTF-8'}</strong>
               
               {if $prestablog_config.prestablog_footlastnews_intro}
                  <br /><span>{$Item.paragraph_crop|escape:'htmlall':'UTF-8'}</span>
               {/if}
               
               {if isset($Item.link_for_unique)}</a>{/if}
               <div style="clear: both;"></div>
            </li>
         {/foreach}
      {else}
         <li>{l s='No news' d='Modules.Prestablog.Grid'}</li>
      {/if}
      {if $prestablog_config.prestablog_footlastnews_showall}
         <li>
            <a href="{PrestaBlogUrl}" class="btn btn-primary">{l s='See all' d='Modules.Prestablog.Grid'}</a>
         </li>
      {/if}
   </ul>
</section>
<!-- /Module Presta Blog -->
