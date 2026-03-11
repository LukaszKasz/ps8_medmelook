{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div class="col-md-5">
    {$prestablog->get_displayFormOpen('icon-sitemap', "{l s='Sitemap configuration' d='Modules.Prestablog.Prestablog'}",$confpath)}
    {$prestablog->get_displayInfo("{l s='Sitemap allow webmaster to inform crawlers search engine.' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-4', "{l s='Sitemap activation' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_sitemap_actif")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-4', "{l s='Articles' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_sitemap_articles")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-4', "{l s='Categories' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_sitemap_categories")}

    {$prestablog->get_displayFormInput(
        'col-lg-4', 
        "{l s='Limit url number per xml file' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_sitemap_limit",
        Configuration::get("{$prestablog->name}_sitemap_limit"), 
        10,
        'col-lg-4',
        "{l s='Urls/xml' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormInput(
        'col-lg-4',
        "{l s='Date since' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_sitemap_older",
        Configuration::get("{$prestablog->name}_sitemap_older"),
        10,
        'col-lg-3',
        "{l s='Month(s)' d='Modules.Prestablog.Prestablog'}",
        "{l s='Since : ' d='Modules.Prestablog.Prestablog'}{$older_date|escape:'html':'UTF-8'}")}
    {$prestablog->get_displayFormInput(
        'col-lg-4',
        "{l s='Token security for cron' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_sitemap_token",
        Configuration::get("{$prestablog->name}_sitemap_token"),
        10,
        'col-lg-6',
        null,
        "{l s='Locked' d='Modules.Prestablog.Prestablog'}")}

    {$prestablog->get_displayFormSubmit('submitSitemapConfig','icon-save', "{l s='Update the configuration' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}
</div>
<div class="col-md-7">
    {$prestablog->get_displayFormOpen('icon-cogs', "{l s='Sitemap manual' d='Modules.Prestablog.Prestablog'}",$confpath)}
    {$prestablog->get_displayFormSubmit('submitSitemapGenerate','icon-cog', "{l s='Generate sitemap' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}
    {$prestablog->get_displayFormOpen('icon-cogs', "{l s='Sitemap automatic with cron url' d='Modules.Prestablog.Prestablog'}",$confpath)}
    <p>
        {l s='Add a "Cron task" to reload this url:' d='Modules.Prestablog.Prestablog'}
    </p>
    <p><strong>{$urlcron|escape:'html':'UTF-8'}</strong></p>
    <p>{l s='It will automatically generate your XML Sitemaps.' d='Modules.Prestablog.Prestablog'}</p>
    {$prestablog->get_displayFormClose()}
    {$prestablog->displayWarning(sprintf("{l s='This action will erase all current sitemaps for the shop %1$s' d='Modules.Prestablog.Prestablog'}", "<strong>{l s={$context->shop->name} d='Modules.Prestablog.Prestablog'}</strong>"))}
    <div class="bootstrap">
        <div class="alert alert-info">
          <strong>{l s='Information' d='prestablog'}</strong><br/>
          {$checkCurrentSitemap}
        </div>
    </div>
</div>
</div>
</div>