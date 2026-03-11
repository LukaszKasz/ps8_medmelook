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
<div class="block-categories prestablog" id="prestablog_categorieslist">
    <h4 class="title_block">
        {if isset($prestablog_categorie_courante) && $prestablog_categorie_courante->id}
            {if isset($prestablog_categorie_parent) && $prestablog_categorie_parent->id == 0 && $prestablog_categorie_courante->id != 0}
                <a href="{PrestaBlogUrl}" class="link_block">{l s='Blog' d='Modules.Prestablog.Grid'}</a>&nbsp;>
            {elseif isset($prestablog_categorie_parent) && $prestablog_categorie_parent->id > 0}
                <a href="{PrestaBlogUrl c=$prestablog_categorie_parent->id titre=$prestablog_categorie_parent->link_rewrite}" class="link_block">
                    {$prestablog_categorie_parent->title|escape:'htmlall':'UTF-8'}
                </a>&nbsp;>
            {/if}
            {$prestablog_categorie_courante->title|escape:'htmlall':'UTF-8'}
        {else}
            {l s='Blog categories' d='Modules.Prestablog.Grid'}
        {/if}
    </h4>

    <div class="block_content" id="prestablog_catliste">
        {if isset($ListeBlocCatNews) && $ListeBlocCatNews|@count > 0}
            {if $prestablog_config.prestablog_catnews_tree}
                <ul class="prestablogtree category-sub-menu">
                    {foreach from=$ListeBlocCatNews item=Item name=myLoop}
                        <li data-depth="0">
                            <div class="contcatblockblog">
                                <a href="{PrestaBlogUrl c=$Item.id_prestablog_categorie titre=$Item.link_rewrite}" class="link_block">
                                    {if isset($Item.image_presente) && $prestablog_config.prestablog_catnews_showthumb}
                                        <img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}c/adminth_{$Item.id_prestablog_categorie|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$Item.link_rewrite|escape:'htmlall':'UTF-8'}" class="catblog_img catlisteimg" />
                                    {/if}
                                    <strong class="catblog_title">{$Item.title|escape:'htmlall':'UTF-8'}</strong>
                                    {if $prestablog_config.prestablog_catnews_shownbnews && $Item.nombre_news_recursif > 0}
                                        &nbsp;<span class="catblog_nb_news">({$Item.nombre_news_recursif|intval})</span>
                                    {/if}
                                </a>

                                {if $prestablog_config.prestablog_catnews_rss}
                                    <a target="_blank" href="{PrestaBlogUrl rss=$Item.id_prestablog_categorie}" data-depth="1" class="link_block">
                                        <img src="{$prestablog_theme_dir|escape:'html':'UTF-8'}/img/rss.png" alt="Rss feed" align="absmiddle" />
                                    </a>
                                {/if}

                                {if $prestablog_config.prestablog_catnews_showintro}
                                    <a class="catblog_desc" href="{PrestaBlogUrl c=$Item.id_prestablog_categorie titre=$Item.link_rewrite}" class="link_block">
                                        <span>{$Item.description_crop|escape:'htmlall':'UTF-8'}</span>
                                    </a>
                                {/if}
                            </div>

                            {if isset($Item.children) && $Item.children|@count > 0}
                                <div class="blog-collapse-icons{if !$prestablog_config.prestablog_catnews_showthumb} no-image{/if}">
                                    <i class="material-icons add">add</i><i class="material-icons remove">remove</i>
                                </div>
                                <div class="blogitems">
                                    {include file="$tree_branch_path" node=$Item.children}
                                </div>
                            {/if}
                        </li>
                    {/foreach}
                </ul>

            {else}
                {foreach from=$ListeBlocCatNews item=Item name=myLoop}
                    <div class="prestablogcatnotreecont">
                        <a href="{PrestaBlogUrl c=$Item.id_prestablog_categorie titre=$Item.link_rewrite}" class="link_block">
                            {if isset($Item.image_presente) && $prestablog_config.prestablog_catnews_showthumb}
                                <img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}c/adminth_{$Item.id_prestablog_categorie|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$Item.link_rewrite|escape:'htmlall':'UTF-8'}" class="catlisteimg" />
                            {/if}
                            <strong>{$Item.title|escape:'htmlall':'UTF-8'}</strong>
                            {if $prestablog_config.prestablog_catnews_shownbnews && $Item.nombre_news_recursif > 0}
                                &nbsp;<span>({$Item.nombre_news_recursif|intval})</span>
                            {/if}
                        </a>

                        {if $prestablog_config.prestablog_catnews_showintro}
                            <a class="catblog_desc" href="{PrestaBlogUrl c=$Item.id_prestablog_categorie titre=$Item.link_rewrite}" class="link_block">
                                <span>{$Item.description_crop|escape:'htmlall':'UTF-8'}</span>
                            </a>
                        {/if}

                        {if isset($prestablog_categorie_courante) && $prestablog_categorie_courante->id == $Item.id_prestablog_categorie}
                            {if isset($Item.children) && $Item.children|@count > 0}
                                <ul class="subcategory-list">
                                    {foreach from=$Item.children item=SubItem name=subLoop}
                                        <li data-depth="1">
                                            <a href="{PrestaBlogUrl c=$SubItem.id_prestablog_categorie titre=$SubItem.link_rewrite}" class="link_block">
                                                {if isset($SubItem.image_presente) && $prestablog_config.prestablog_catnews_showthumb}
                                                    <img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}c/adminth_{$SubItem.id_prestablog_categorie|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$SubItem.link_rewrite|escape:'htmlall':'UTF-8'}" class="catblog_img catlisteimg" />
                                                {/if}
                                                <strong class="catblog_title">{$SubItem.title|escape:'htmlall':'UTF-8'}</strong>
                                                {if $prestablog_config.prestablog_catnews_shownbnews && $SubItem.nombre_news_recursif > 0}
                                                    &nbsp;<span class="catblog_nb_news">({$SubItem.nombre_news_recursif|intval})</span>
                                                {/if}
                                            </a>

                                            {if $prestablog_config.prestablog_catnews_showintro}
                                                <a class="catblog_desc" href="{PrestaBlogUrl c=$SubItem.id_prestablog_categorie titre=$SubItem.link_rewrite}" class="link_block">
                                                    <span>{$SubItem.description_crop|escape:'htmlall':'UTF-8'}</span>
                                                </a>
                                            {/if}
                                        </li>
                                    {/foreach}
                                </ul>
                            {/if}
                        {/if}
                    </div>
                {/foreach}
            {/if}
        {/if}

        {if $prestablog_config.prestablog_catnews_showall}
            <a href="{PrestaBlogUrl}" class="btn-primary btn_link">{l s='See all' d='Modules.Prestablog.Grid'}</a>
        {/if}
    </div>
</div>
<!-- /Module Presta Blog -->

