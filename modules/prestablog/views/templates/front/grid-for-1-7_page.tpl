{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}

<!-- Module Presta Blog START PAGE -->
{extends file=$layout_blog}

{block name='head_seo'}
	<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
	<meta name="description" content="{$meta_description|escape:'htmlall':'UTF-8'}">
	<meta name="keywords" content="{$meta_keywords|escape:'htmlall':'UTF-8'}">
	{if $page.meta.robots !== 'index'}
		<meta name="robots" content="{$page.meta.robots|escape:'htmlall':'UTF-8'}">
	{/if}
    {block name='head_hreflang'}
        {if isset($language_urls_redirect)}
            {foreach from=$language_urls_redirect item=pageUrl key=langCode}
                {if !empty($pageUrl) && !empty($langCode)}
                    <link rel="alternate" href="{$pageUrl|escape:'htmlall':'UTF-8'}" hreflang="{$langCode|escape:'htmlall':'UTF-8'}">
                {/if}
            {/foreach}
        {/if}
    {/block}
	{if isset($urls_redirect)}
		{if isset($language.iso_code) && isset($urls_redirect[$language.iso_code])}
		  {if isset($Pagination.NombreTotalPages) && $Pagination.NombreTotalPages > 1}
			{if $Pagination.PageCourante == 1}
			  <link rel="canonical" href="{$urls_redirect[$language.iso_code]|escape:'htmlall':'UTF-8'}">
			{else}
			  {foreach from=$Pagination.PremieresPages key=key_page item=value_page}
				{if $Pagination.PageCourante == $key_page}
				  <link rel="canonical" href="{PrestaBlogUrl categorie=$prestablog_categorie_link_rewrite|escape:'htmlall':'UTF-8' start=$value_page p=$key_page c=$prestablog_categorie m=$prestablog_month y=$prestablog_year}">
				{/if}
			  {/foreach}
			{/if}
		  {else}
			<link rel="canonical" href="{$urls_redirect[$language.iso_code]|escape:'htmlall':'UTF-8'}">
		  {/if}
		{else}
		  {if isset($Pagination.NombreTotalPages) && $Pagination.NombreTotalPages > 1}
			{if $Pagination.PageCourante == 1}
			  <link rel="canonical" href="{$pageUrl|escape:'htmlall':'UTF-8'}">
			{else}
			  {foreach from=$Pagination.PremieresPages key=key_page item=value_page}
				{if $Pagination.PageCourante == $key_page}
				  <link rel="canonical" href="{PrestaBlogUrl categorie=$prestablog_categorie_link_rewrite|escape:'htmlall':'UTF-8' start=$value_page p=$key_page c=$prestablog_categorie m=$prestablog_month y=$prestablog_year}">
				{/if}
			  {/foreach}
			{/if}
		  {else}
			<link rel="canonical" href="{$pageUrl|escape:'htmlall':'UTF-8'}">
		  {/if}
		{/if}
	  {/if}

	{if $prestashop_version >= '1.7.8'}
		{block name='hook_after_title_tag'}
				{hook h='displayAfterTitleTag'}
		{/block}
	{/if}
	{if isset($Pagination.NombreTotalPages) && $Pagination.NombreTotalPages > 1}
		{foreach from=$Pagination.PremieresPages key=key_page item=value_page}
		  {if $Pagination.PageCourante == ($key_page-1)}
			<link rel="next" href="{PrestaBlogUrl categorie=$prestablog_categorie_link_rewrite|escape:'htmlall':'UTF-8' start=$value_page p=$key_page c=$prestablog_categorie m=$prestablog_month y=$prestablog_year}{$prestablog_search_query|escape:'htmlall':'UTF-8'}">
		  {/if}
		  {if $Pagination.PageCourante == 2 && $key_page == 1}
			<link rel="prev" href="{PrestaBlogUrl}">
		  {else if $Pagination.PageCourante == ($key_page+1)}
			<link rel="prev" href="{PrestaBlogUrl categorie=$prestablog_categorie_link_rewrite|escape:'htmlall':'UTF-8' start=$value_page p=$key_page c=$prestablog_categorie m=$prestablog_month y=$prestablog_year}{$prestablog_search_query|escape:'htmlall':'UTF-8'}">
		  {/if}
		{/foreach}
	  {/if}
{/block}

{block name='content'}

	{if isset($tpl_filtre_cat) && $tpl_filtre_cat}{PrestaBlogContent return=$tpl_filtre_cat}{/if}
	{if isset($tpl_menu_cat) && $tpl_menu_cat}{PrestaBlogContent return=$tpl_menu_cat}{/if}

	{if isset($tpl_title) && $tpl_title}{PrestaBlogContent return=$tpl_title}{/if}
	{if isset($tpl_unique) && $tpl_unique}{PrestaBlogContent return=$tpl_unique}{/if}
	{if isset($tpl_extra) && $tpl_extra}{PrestaBlogContent return=$tpl_extra}{/if}
	{if isset($tpl_comment) && $tpl_comment}{PrestaBlogContent return=$tpl_comment}{/if}
	{if isset($tpl_comment_fb) && $tpl_comment_fb}{PrestaBlogContent return=$tpl_comment_fb}{/if}

	{if isset($tpl_slide) && $tpl_slide}{PrestaBlogContent return=$tpl_slide}{/if}
    {if isset($tpl_cat) && $tpl_cat}{PrestaBlogContent return=$tpl_cat}{/if}
	{if isset($tpl_aut) && $tpl_aut}{PrestaBlogContent return=$tpl_aut}{/if}
	{if isset($tpl_all) && $tpl_all}{PrestaBlogContent return=$tpl_all}{/if}

{/block}



<!-- /Module Presta Blog END PAGE -->
