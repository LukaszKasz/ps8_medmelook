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
    {$prestablog->get_displayFormOpen('categories.png', "{l s='Category listing' d='Modules.Prestablog.Prestablog'}",
        $confpath)}
        
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Number of news per page' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_nb_liste_page", Configuration::get("{$prestablog->name}_nb_liste_page"), 10, 'col-lg-4','','','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Thumb picture width for news' d='Modules.Prestablog.Prestablog'}",
    "thumb_picture_width","{$config_theme->images->thumb->width}", 10, 'col-lg-4',"{l s='px' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Thumb picture height for news' d='Modules.Prestablog.Prestablog'}",
    "thumb_picture_height", "{$config_theme->images->thumb->height}", 10, 'col-lg-4',"{l s='px' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Title length' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_news_title_length", Configuration::get("{$prestablog->name}_news_title_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Description length' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_news_intro_length", Configuration::get("{$prestablog->name}_news_intro_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}

    {$prestablog->get_displayFormSelect('col-lg-5', "{l s='Article\'s display' d='Modules.Prestablog.Prestablog'}","{$prestablog->name}_article_page",
        Configuration::get("{$prestablog->name}_article_page"),[
                '1' => "{l s='One column' d='Modules.Prestablog.Prestablog'}",
                '2' => "{l s='Two columns' d='Modules.Prestablog.Prestablog'}",
                '3' => "{l s='Three columns' d='Modules.Prestablog.Prestablog'}",
                '4' => "{l s='Full picture' d='Modules.Prestablog.Prestablog'}"],null,
            'col-lg-5')}
	{$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Ratings on news' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_rating_actif", "{l s='Activate rating.' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Display the "read" number' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_read_actif", "{l s='Activate' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show news count by category' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_catnews_shownbnews", "{l s='Does not display zero values.' d='Modules.Prestablog.Prestablog'}")}

    {$prestablog->get_displayFormSubmit('submitConfListCat','icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}


    {$prestablog->get_displayFormOpen('categories.png', "{l s='Block categories news' d='Modules.Prestablog.Prestablog'}",
        $confpath)}
        
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_catnews_actif")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='View empty categories' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_catnews_empty", "{l s='Supports the count of items in the categories recursive children.' d='Modules.Prestablog.Prestablog'}")}


    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Tree view' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_catnews_tree")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show thumb' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_catnews_showthumb")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show crop description' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_catnews_showintro")}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Title length' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_cat_title_length", Configuration::get("{$prestablog->name}_cat_title_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Description length' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_cat_intro_length", Configuration::get("{$prestablog->name}_cat_intro_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Link "show all"' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_catnews_showall")}

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "<img src='{$imgsrc|escape:'html':'UTF-8'}rss.png' align='absmiddle'
        />{l s='Rss feed' d='Modules.Prestablog.Prestablog'}","{$prestablog->name}_catnews_rss", "{l s='List only for selected category' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormSubmit('submitConfBlocCatNews','icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}


 </div><div class='col-md-6'>

    {$prestablog->get_displayFormOpen('top-category.png', "{l s='Top of first page of category' d='Modules.Prestablog.Prestablog'}",
        $confpath)}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show description' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_view_cat_desc")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show thumbnail' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_view_cat_thumb")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show picture' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_view_cat_img")}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Thumb picture width for categories' d='Modules.Prestablog.Prestablog'}",
    "thumb_cat_width","{$config_theme->categories->thumb->width}", 10, 'col-lg-4',"{l s='px' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Thumb picture height for categories' d='Modules.Prestablog.Prestablog'}",
    "thumb_cat_height","{$config_theme->categories->thumb->height}", 10, 'col-lg-4',"{l s='px' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Full size picture width for category' d='Modules.Prestablog.Prestablog'}",
    "full_cat_width","{$config_theme->categories->full->width}", 10, 'col-lg-4',"{l s='px' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Full size picture height for category' d='Modules.Prestablog.Prestablog'}",
    "full_cat_height","{$config_theme->categories->full->height}", 10, 'col-lg-4',"{l s='px' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}

    {$prestablog->get_displayFormSubmit('submitConfCategory','icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}

    {$prestablog->get_displayFormOpen('categories.png', "{l s='Category menu in blog pages' d='Modules.Prestablog.Prestablog'}",
        $confpath)}

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate menu on blog index' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_menu_cat_blog_index")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate menu on blog list' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_menu_cat_blog_list")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate menu on article' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_menu_cat_blog_article")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show blog link' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_menu_cat_home_link")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show blog image link' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_menu_cat_home_img", "{l s='Only if blog link is activated' d='Modules.Prestablog.Prestablog'}<br/>
		{sprintf(
                {l s='Show %1$s instead %2$s' d='Modules.Prestablog.Prestablog'},
                "<img style='vertical-align:top;background-color:#383838;padding:4px;' src='{$imgsrc|escape:'html':'UTF-8'}home.gif'/>",
                "<strong>Blog</strong>"
            )}")}
           

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='View empty categories' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_menu_cat_blog_empty", "{l s='Supports the count of items in the categories recursive children.' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show news count by category'}",
        "{$prestablog->name}_menu_cat_blog_nbnews", "{l s='Does not display zero values.' d='Modules.Prestablog.Prestablog'}")}

    {$prestablog->get_displayFormSubmit('submitConfMenuCatBlog','icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}
</div>
</div>
</div>
<script type="text/javascript" src="../modules/prestablog/views/js/numbers.js"></script>