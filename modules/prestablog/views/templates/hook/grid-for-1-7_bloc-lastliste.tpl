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
<div class="block-categories prestablog" id="prestablog_list">
	<h4 class="title_block">{l s='Last blog articles' d='Modules.Prestablog.Grid'}</h4>
	<div class="block_content" id="prestablog_lastliste">
		{if $ListeBlocLastNews}
			{foreach from=$ListeBlocLastNews item=Item name=myLoop}
				<p>
					{if isset($Item.link_for_unique)}<a href="{PrestaBlogUrl id=$Item.id_prestablog_news seo=$Item.link_rewrite titre=$Item.title}" class="link_block">{/if}
						{if isset($Item.image_presente) && $prestablog_config.prestablog_lastnews_showthumb}
                            <picture>
                                {if isset($Item.webp_present) and $Item.webp_present}
                                <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id_prestablog_news|intval}.webp?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/webp">
                                {/if}
                                <source srcset="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" type="image/jpeg">
                                <img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$Item.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$Item.title|escape:'htmlall':'UTF-8'}" class="lastlisteimg">
                            </picture>
                        {/if}

						{$Item.title|escape:'htmlall':'UTF-8'}
						{if $prestablog_config.prestablog_lastnews_showintro}<br /><span>{$Item.paragraph_crop|escape:'htmlall':'UTF-8'}</span>{/if}
					{if isset($Item.link_for_unique)}</a>{/if}
				</p>
				{if !$smarty.foreach.myLoop.last}{/if}
			{/foreach}
		{else}
			<p>{l s='No news' d='Modules.Prestablog.Grid'}</p>
		{/if}

		{if $prestablog_config.prestablog_lastnews_showall}<div class="clearblog"></div><a href="{PrestaBlogUrl}" class="btn-primary btn_link">{l s='See all' d='Modules.Prestablog.Grid'}</a>{/if}
	</div>
</div>
<!-- /Module Presta Blog -->
