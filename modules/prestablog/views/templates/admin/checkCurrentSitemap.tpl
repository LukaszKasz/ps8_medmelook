{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if count($liste_sitemap) > 0}
    <p>
        <a onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');" class="btn btn-primary" href="{$confpath|escape:'html':'UTF-8'}&deleteSitemap">
            <i class="icon-trash-o"></i>&nbsp;{l s='Delete all sitemap xml for this shop' d='Modules.Prestablog.Prestablog'}
        </a>
    </p>
{/if}

<p>
    {l s='Use master sitemap to regroup all news :' d='Modules.Prestablog.Prestablog'}
</p>

{if count($liste_sitemap) > 0}
    <ul>
    {foreach $liste_sitemap  $file}
        <li><a href="{$urlSRoot|escape:'html':'UTF-8'}modules/prestablog/sitemap/{l s={$context->shop->id} d='Modules.Prestablog.Prestablog'}/{$file|escape:'html':'UTF-8'}" target="_blank">{$urlSRoot|escape:'html':'UTF-8'}modules/prestablog/sitemap/{l s={$context->shop->id} d='Modules.Prestablog.Prestablog'}/{$file|escape:'html':'UTF-8'}</a></li>
    {/foreach}
    </ul>
{else}
    <p>{l s='no xml file available' d='Modules.Prestablog.Prestablog'}</p>
{/if}

<hr/>

<p>{sprintf("{l s='All sitemaps can be crawled individually for %1$s store :' d='Modules.Prestablog.Prestablog'}", "<strong>{l s={$context->shop->name} d='Modules.Prestablog.Prestablog'}</strong>")}</p>

{if count($liste_sitemap_wildcard) > 0}
    <ul>
    {foreach $liste_sitemap_wildcard  $file}
        <li><a href="{$urlSRoot|escape:'html':'UTF-8'}modules/prestablog/sitemap/{l s={$context->shop->id} d='Modules.Prestablog.Prestablog'}/{$file|escape:'html':'UTF-8'}" target="_blank">{$urlSRoot|escape:'html':'UTF-8'}modules/prestablog/sitemap/{l s={$context->shop->id} d='Modules.Prestablog.Prestablog'}/{$file|escape:'html':'UTF-8'}</a></li>
    {/foreach}
    </ul>
{else}
    <p>{l s='no xml file available' d='Modules.Prestablog.Prestablog'}</p>
{/if}
