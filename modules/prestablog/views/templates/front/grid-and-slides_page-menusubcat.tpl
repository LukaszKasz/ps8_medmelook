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

{assign "num" 0}
{if $child == true}
	<ul class="sub-menu hidden">
{else}
	<ul>
{/if}

{if $first && Configuration::get('prestablog_menu_cat_home_link')}
	{if Configuration::get('prestablog_urlblog') == false}
		{assign "menu_cat_home_img" $prestablog->message_call_back['blog']}
	{else}
		{assign "menu_cat_home_img" $prestablog->message_call_back[Configuration::get('prestablog_urlblog')]}
	{/if}
	{if Configuration::get('prestablog_menu_cat_home_img')}
		<li><a href="{PrestaBlog::prestablogUrl(array())|escape:'htmlall':'UTF-8'}"><i class="material-icons idi">home</i></a></li>
	{else}
		<li><a href="{PrestaBlog::prestablogUrl(array())|escape:'htmlall':'UTF-8'}">{$menu_cat_home_img|cleanHtml nofilter}</a></li>
	{/if}
	{assign "first" 0}
{/if}

{foreach $liste $value}
	{if !Configuration::get('prestablog_menu_cat_blog_empty') && (int)$value['nombre_news_recursif'] == 0}
	{else}
		{assign "nombre_news_recursif" ''}
		<li>
			{if $value['link_rewrite'] != ''}
				{assign "titre" $value['link_rewrite']}
			{else}
				{assign "titre" $value['title']}
			{/if}


			<a href="{PrestaBlog::prestablogUrl([
				'c' => (int)$value['id_prestablog_categorie'],
				'titre' => $titre|escape:'htmlall':'UTF-8'
			])}" {if count($value['children']) > 0}class="mparent"{/if}>
				{$value['title']|escape:'htmlall':'UTF-8'}{$nombre_news_recursif|cleanHtml nofilter}
			</a>

				{if count($value['children']) > 0}
					{if $value['parent'] > 0}
						<i class="material-icons idi2">keyboard_arrow_right</i>
					{else}
						<i class="material-icons idi">keyboard_arrow_down</i>
					{/if}
				{/if}
			{if count($value['children']) > 0}
				{assign "num" $num+1}
				{assign "child" true}
				{PrestaBlogContent return=$blog->displayMenuCategories($value['children'], $first, $child, $num)}
			{/if}
		</li>
	{/if}
{/foreach}
</ul>
<!-- Module Presta Blog -->
