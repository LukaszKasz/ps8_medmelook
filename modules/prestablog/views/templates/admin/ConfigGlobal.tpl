{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if Tools::getIsset('success')}
    {$prestablog->displayConfirmation("{l s='Settings updated successfully' d='Modules.Prestablog.Prestablog'}")}
{/if}

<div class='col-md-6'>
    {$prestablog->get_displayFormOpen('rewrite.png', "{l s='Rewrite configuration' d='Modules.Prestablog.Prestablog'}", $confpath)}

    {if not Configuration::get('PS_REWRITING_SETTINGS') && Configuration::get('prestablog_rewrite_actif')}
        {$prestablog->displayError("{l s='The general rewrite option (Friendly URL) of your PrestaShop is not activate.' d='Modules.Prestablog.Prestablog'}
        <br />
        {l s='You must enable this general option to it works.' d='Modules.Prestablog.Prestablog'}")}
    {/if}
    {$prestablog->get_displayFormEnableItemConfiguration(
    'col-lg-5',
    "{l s='Enable rewrite (Friendly URL)' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_rewrite_actif",
    "{l s='Enable only if your server allows URL rewriting (recommended)' d='Modules.Prestablog.Prestablog'}"
    )}
    {$prestablog->get_displayFormSubmit('submitConfRewrite', 'icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}

    {$prestablog->get_displayFormOpen('frontoffice.png',"{l s='Global front configuration' d='Modules.Prestablog.Prestablog'}",$confpath)}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show breadcrumb in all blog pages' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_show_breadcrumb")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show thumbnail image in article page' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_view_news_img")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s="Rss link for categories news" d="prestablog"}",
    "{$prestablog->name}_uniqnews_rss", "{l s='Rss link for categories in the news page.' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Add a new tab with associated blog posts directly in your product page' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_producttab_actif")}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Width of thumbnail in the product list linked' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_thumb_linkprod_width",
    Configuration::get("{$prestablog->name}_thumb_linkprod_width"), 10, 'col-lg-4', "{l s='px' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Include Material Icons in header' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_material_icons")}

    <legend><i class="material-icons">share</i> {l s='Social share buttons' d='Modules.Prestablog.Prestablog'}</legend>


    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Socials buttons share' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_socials_actif")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', 'Facebook', "{$prestablog->name}_s_facebook")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', 'Twitter', "{$prestablog->name}_s_twitter")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', 'Linkedin', "{$prestablog->name}_s_linkedin")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', 'Email', "{$prestablog->name}_s_email")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', 'Pinterest', "{$prestablog->name}_s_pinterest")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', 'Pocket', "{$prestablog->name}_s_pocket")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', 'Tumblr', "{$prestablog->name}_s_tumblr")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', 'Reddit', "{$prestablog->name}_s_reddit")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', 'Hackernews', "{$prestablog->name}_s_hackernews")}
    {$prestablog->get_displayFormSubmit('submitConfGobalFront', 'icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}
</div>
<div class='col-md-6'>

    {$prestablog->get_displayFormOpen('backoffice.png', "{l s='Global admin configuration' d='Modules.Prestablog.Prestablog'}", $confpath)}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Number of characters to search on related products for article edited' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_nb_car_min_linkprod", Configuration::get("{$prestablog->name}_nb_car_min_linkprod"), 10,
    'col-lg-4', "{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Number of results in search off related products for article edited' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_nb_list_linkprod", Configuration::get("{$prestablog->name}_nb_list_linkprod"), 10, 'col-lg-4','','','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Number of characters to search on related articles for article edited' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_nb_car_min_linknews", Configuration::get("{$prestablog->name}_nb_car_min_linknews"), 10,
    'col-lg-4',
    "{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Number of results in search off related articles for article edited' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_nb_list_linknews", Configuration::get("{$prestablog->name}_nb_list_linknews"), 10, 'col-lg-4','','','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='items/page on admin list news' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_nb_news_pl", Configuration::get("{$prestablog->name}_nb_news_pl"), 10, 'col-lg-4','','','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='items/page on admin list comments' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_nb_comments_pl", Configuration::get("{$prestablog->name}_nb_comments_pl"), 10, 'col-lg-4','','','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Always show comments in article edition' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_comment_div_visible")}
    {$prestablog->get_displayFormSubmit('submitConfGobalAdmin', 'icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}
</div>
<script type="text/javascript" src="../modules/prestablog/views/js/numbers.js"></script>
</div>
</div>