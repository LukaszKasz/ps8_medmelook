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
{if isset($subblocks.title) && $subblocks.title != ''}
    <section class="clearfix prestablogswip">
        <h2 class="title">{$subblocks.title|escape:'htmlall':'UTF-8'}</h2>
        {$i = 0}
        {if sizeof($news)}
            <div id="blog_list_1-7" class="swiper-container bloglistswip">
				<div class="swiper-wrapper">
                {foreach from=$news item=news_item name=NewsName}
                    {if $i <= $subblocks.nb_list}
                        <div class="blog-grid swiper-slide">
							<div style="display:none;">
								{$i++|intval}
							</div>
                            <div class="block_cont">
                                <div class="block_top">
                                     {if isset($news_item.image_presente)}
                                        <a href="{PrestaBlogUrl id=$news_item.id_prestablog_news seo=$news_item.link_rewrite titre=$news_item.title}" title="{$news_item.title|escape:'htmlall':'UTF-8'}">
                                            <picture>
                                                {if isset($news_item.webp_present) and $news_item.webp_present}
                                                <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$news_item.id_prestablog_news|intval}.webp?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/webp">
                                                {/if}
                                                <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$news_item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/jpeg">
                                                <img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$news_item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$news_item.title|escape:'htmlall':'UTF-8'}">
                                            </picture>
                                        </a>
                                    {/if}
                                </div>
                                <div class="block_bas">
                                    <h3>
                                        <a href="{PrestaBlogUrl id=$news_item.id_prestablog_news seo=$news_item.link_rewrite titre=$news_item.title}"
                                            title="{$news_item.title|escape:'htmlall':'UTF-8'}">{$news_item.title|escape:'htmlall':'UTF-8'}</a>
                                        <br /><span class="date_blog-cat">{l s='Published :' d='Modules.Prestablog.Grid'}
                                            {dateFormat date=$news_item.date full=0}
                                            {if $prestablog_config.prestablog_author_actif}
                                                {if $prestablog_config.prestablog_author_cate_actif}
                                                   {assign var=hasPseudo value=false}
                                                    {foreach from=$news_item.authors item=author key=key name=current}
                                                        {if $key == "pseudo" && isset($author) && $author != ''}
                                                            {assign var=hasPseudo value=true}
                                                            {$pseudo = $author}
                                                            {l s='By' d='Modules.Prestablog.Grid'}
                                                            <a href="{PrestaBlogUrl au=$news_item.author_id titre={$pseudo|escape:'htmlall':'UTF-8'}}">{$pseudo|escape:'htmlall':'UTF-8'}</a>
                                                            {break}
                                                        {/if}
                                                    {/foreach}
                                                    {if !$hasPseudo}
                                                        {foreach from=$news_item.authors item=author key=key name=current}
                                                            {if $key == "firstname"}
                                                                {$firstname = $author}
                                                                {l s='By' d='Modules.Prestablog.Grid'}
                                                                <a href="{PrestaBlogUrl au=$news_item.author_id titre={$firstname|escape:'htmlall':'UTF-8'}}">{$firstname|escape:'htmlall':'UTF-8'}</a>
                                                                {break}
                                                            {/if}
                                                        {/foreach}
                                                    {/if}
                                                {/if}
                                            {/if}
                                            {if sizeof($news_item.categories)} | {l s='Categories :' d='Modules.Prestablog.Grid'}
                                                {foreach from=$news_item.categories item=categorie key=key name=current}
                                                    <a href="{PrestaBlogUrl c=$key titre=$categorie.link_rewrite}"
                                                        class="categorie_blog">{$categorie.title|escape:'htmlall':'UTF-8'}</a>
                                                    {if !$smarty.foreach.current.last},{/if}
                                                {/foreach}
                                            {/if}</span>

                                        {if $prestablog_config.prestablog_rating_actif}
                                            <div class="star_content">
                                                {section name="i" start=0 loop=5 step=1}
                                                    {if $smarty.section.i.index lt $news_item.average_rating}
                                                        <div class="material-icons checked">star</div>
                                                    {elseif $news_item.average_rating == 5}
                                                        <div class="material-icons checked">star</div>
                                                    {else}
                                                        <div class="material-icons">star</div>
                                                    {/if}
                                                {/section}
                                            </div>
                                        {/if}
                                    </h3>
                                    {if $news_item.paragraph_crop!=''}
                                        <p class="blog_desc">
                                                {$news_item.paragraph_crop|escape:'htmlall':'UTF-8'}
                                        </p>
                                    {/if}                                
                                </div>
                                <div class="prestablog_more">
                                    <a href="{PrestaBlogUrl id=$news_item.id_prestablog_news seo=$news_item.link_rewrite titre=$news_item.title}"
                                        class="blog_link"><i class="material-icons">search</i> {l s='Read more' d='Modules.Prestablog.Grid'}</a>
                                    {if $prestablog_config.prestablog_comment_actif==1}
                                        <a href="{PrestaBlogUrl id=$news_item.id_prestablog_news seo=$news_item.link_rewrite titre=$news_item.title}#comment"
                                            class="comments"><i class="material-icons">comment</i> {$news_item.count_comments|intval}</a>
                                    {/if}
                                    {if $prestablog_config.prestablog_read_actif}
                                        <span><i class="material-icons">remove_red_eye</i> {$news_item.read|escape:'htmlall':'UTF-8'}</span>
                                    {/if}
                                    {if $prestablog_config.prestablog_commentfb_actif==1}
                                        <a href="{PrestaBlogUrl id=$news_item.id_prestablog_news seo=$news_item.link_rewrite titre=$news_item.title}#comment"
                                            id="showcomments{$news_item.id_prestablog_news|intval}" class="comments"
                                            data-commentsurl="{PrestaBlogUrl id=$news_item.id_prestablog_news seo=$news_item.link_rewrite titre=$news_item.title}"
                                            data-commentsidnews="{$news_item.id_prestablog_news|intval}"><i
                                                class="material-icons">comment</i> {$news_item.count_comments|intval}
                                        </a>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
            <div class="nivo-directionNav">
                <div class="swiper-button-next">
                  <a class="nivo-nextNav"></a>
                </div>
                <div class="swiper-button-prev">
                  <a class="nivo-prevNav"></a>
                </div>
            </div>
            <div class="swiper-pagination-bloglist"></div>
        </div>
        {/if}
    </section>
{/if}
<!-- /Module Presta Blog -->