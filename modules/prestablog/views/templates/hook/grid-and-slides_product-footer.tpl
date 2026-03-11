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
<section class="page-product-box">
   <div id="blog_article_linked">
   <h3 class="page-product-heading">{l s='Related articles on blog' d='Modules.Prestablog.Grid'}</h3>
   {if $listeNewsLinked}
      <ul id="blog_list_1-7">
      {foreach from=$listeNewsLinked item=Item name=myLoop}
            <li class="blog-grid">
               <div class="block_cont">
                <div class="block_top">
               <a href="{$Item.url|escape:'html':'UTF-8'}">
                  {if $Item.image_presente|intval == 1}<img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$Item.title|escape:'htmlall':'UTF-8'}" />{/if}
               </a>
            </div>
            <div class="block_bas">
                <p class="h3title">
            <a href="{$Item.url|escape:'html':'UTF-8'}">

                  {$Item.title|escape:'htmlall':'UTF-8'}

               </a>
            </p>
            </div>
            {if isset($listeNewsLinked)}
                <div class="prestablog_more">
                             <a href="{$Item.url|escape:'htmlall':'UTF-8'}" class="blog_link"><i class="material-icons">search</i> {l s='Read more' d='Modules.Prestablog.Grid'}</a>
                </div>{/if}
         </div>
            </li>
         {if !$smarty.foreach.myLoop.last}{/if}
      {/foreach}
      </ul>
   {else}
      <p>{l s='No related articles on blog' d='Modules.Prestablog.Grid'}</p>
   {/if}
</div>
</section>
<!-- /Module Presta Blog -->
