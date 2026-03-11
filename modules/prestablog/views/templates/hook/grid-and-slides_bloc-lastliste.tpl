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
<div class="block-categories prestablogcat prestablogswip" id="prestablog_lastliste">
	<h4 class="title_block">{l s='Last blog articles' d='Modules.Prestablog.Grid'}</h4>
		{if $ListeBlocLastNews}
			{if $prestablog_config.prestablog_lastnews_showthumb && $prestablog_config.prestablog_lastnews_showintro}
				<div class="swiper-container prestabloglastnewswipintro">
					<div class="swiper-wrapper">
					{foreach from=$ListeBlocLastNews item=Item name=myLoop}
                        <div class="swiper-slide">
							{if isset($Item.link_for_unique)}<a href="{PrestaBlogUrl id=$Item.id_prestablog_news seo=$Item.link_rewrite titre=$Item.title}" class="link_block">{/if}
								{if isset($Item.image_presente) and $Item.image_presente}
                                    <picture>
                                        {if isset($Item.webp_present) and $Item.webp_present}
                                        <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id_prestablog_news|intval}.webp?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/webp">
                                        {/if}
                                        <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/jpeg">
                                        <img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$Item.title|escape:'htmlall':'UTF-8'}" class="lastlisteimg">
                                    </picture>
                                {/if}
								<p class="prestabloglastnewstitle">{$Item.title|escape:'htmlall':'UTF-8'}</p>
								<span class="swiper-slide-resume">{$Item.paragraph_crop|escape:'htmlall':'UTF-8'}</span>
							{if isset($Item.link_for_unique)}</a>{/if}
						</div>
						{if !$smarty.foreach.myLoop.last}{/if}
					{/foreach}
					</div>
					<div class="swiper-pagination-lastnewsblog"></div>
				</div>
			{elseif $prestablog_config.prestablog_lastnews_showthumb}
				<div class="swiper-container prestabloglastnewswip">
					<div class="swiper-wrapper">
					{foreach from=$ListeBlocLastNews item=Item name=myLoop}
					<div class="swiper-slide">
							{if isset($Item.link_for_unique)}<a href="{PrestaBlogUrl id=$Item.id_prestablog_news seo=$Item.link_rewrite titre=$Item.title}" class="link_block">{/if}
								{if isset($Item.image_presente) and $Item.image_presente && $prestablog_config.prestablog_lastnews_showthumb}
                                    <picture>
                                        {if isset($Item.webp_present) and $Item.webp_present}
                                        <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id_prestablog_news|intval}.webp?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/webp">
                                        {/if}
                                        <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/jpeg">
                                        <img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$Item.title|escape:'htmlall':'UTF-8'}" class="lastlisteimg">
                                    </picture>
                                {/if}
								<p class="prestabloglastnewstitle">{$Item.title|escape:'htmlall':'UTF-8'}</p>
							</a>
						</div>
						{if !$smarty.foreach.myLoop.last}{/if}
					{/foreach}
					</div>
					<div class="swiper-pagination-lastnewsblog"></div>
				</div>


			{elseif $prestablog_config.prestablog_lastnews_showintro}
				<div class="swiper-container prestabloglastnewswip">
					<div class="swiper-wrapper">
					{foreach from=$ListeBlocLastNews item=Item name=myLoop}
						<div class="swiper-slide">
							{if isset($Item.link_for_unique)}<a href="{PrestaBlogUrl id=$Item.id_prestablog_news seo=$Item.link_rewrite titre=$Item.title}" class="link_block">{/if}
								<p class="prestabloglastnewstitle-nopic">{$Item.title|escape:'htmlall':'UTF-8'}</p>
								<span class="swiper-slide-resume">{$Item.paragraph_crop|escape:'htmlall':'UTF-8'}</span>
							{if isset($Item.link_for_unique)}</a>{/if}
						</div>
						{if !$smarty.foreach.myLoop.last}{/if}
					{/foreach}
					</div>
					<div class="swiper-pagination-lastnewsblog"></div>
				</div>
			{else}
 				{foreach from=$ListeBlocLastNews item=Item name=myLoop}
						{if isset($Item.link_for_unique)}<a href="{PrestaBlogUrl id=$Item.id_prestablog_news seo=$Item.link_rewrite titre=$Item.title}" class="link_block">{/if}					
							<div class="prestabloglastnewstitle-nopic-nointro">
							{$Item.title|escape:'htmlall':'UTF-8'}
							</div>
						{if isset($Item.link_for_unique)}</a>{/if}
					{if !$smarty.foreach.myLoop.last}{/if}
				{/foreach}
            {/if}
					

		{else}
			<p>{l s='No news' d='Modules.Prestablog.Grid'}</p>
		{/if}
		{if $prestablog_config.prestablog_lastnews_showall}<div class="clearblog"></div><a href="{PrestaBlogUrl}" class="btn-primary btn_link">{l s='See all' d='Modules.Prestablog.Grid'}</a>{/if}
</div>
<!-- /Module Presta Blog -->
