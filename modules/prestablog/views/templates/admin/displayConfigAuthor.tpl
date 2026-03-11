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

<div class="col-md-6">
{$prestablog->get_displayFormOpen('rewrite.png', {l s='Author configuration' d='Modules.Prestablog.Prestablog'}, $confpath)}

{if $context->employee->id_profile == 1}
    {$prestablog->get_displayFormEnableItemConfiguration(
        'col-lg-5',
        {l s='Enable author permissions' d='Modules.Prestablog.Prestablog'},
        "{$prestablog->name}_enable_permissions",
        {l s='Activate the permission system for authors.' d='Modules.Prestablog.Prestablog'}
    )}
    
  {$prestablog->get_displayFormEnableItemConfiguration(
        'col-lg-5',
        {l s='Only allow the author and super admin to edit their own news' d='Modules.Prestablog.Prestablog'},
        "{$prestablog->name}_author_edit_actif"
  )}
{/if}

{$prestablog->get_displayFormEnableItemConfiguration(
    'col-lg-5',
    {l s='Enable author display' d='Modules.Prestablog.Prestablog'},
    "{$prestablog->name}_author_actif",
    {l s='Activate the display of author and author\'s link page on the news.' d='Modules.Prestablog.Prestablog'}
)}

{$prestablog->get_displayFormEnableItemConfiguration(
    'col-lg-5',
    {l s='Display author on categories page' d='Modules.Prestablog.Prestablog'},
    "{$prestablog->name}_author_cate_actif"
)}

{$prestablog->get_displayFormEnableItemConfiguration(
    'col-lg-5',
    {l s='Display the author link on the news page' d='Modules.Prestablog.Prestablog'},
    "{$prestablog->name}_author_news_actif"
)}

{$prestablog->get_displayFormEnableItemConfiguration(
    'col-lg-5',
    {l s='Display the "About" block at the bottom of the news' d='Modules.Prestablog.Prestablog'},
    "{$prestablog->name}_author_about_actif"
)}

{$prestablog->get_displayFormInput(
    'col-lg-5',
    {l s='Number of news per page' d='Modules.Prestablog.Prestablog'},
    "{$prestablog->name}_author_news_number",
    Configuration::get("{$prestablog->name}_author_news_number"),
    10,
    'col-lg-4'
	,'','','','PrestablogUintTextBox'
)}

{$prestablog->get_displayFormInput(
    'col-lg-5',
    {l s='Description length' d='Modules.Prestablog.Prestablog'},
    "{$prestablog->name}_author_intro_length",
    Configuration::get("{$prestablog->name}_author_intro_length"),
    10,
    'col-lg-4',
    {l s='caracters' d='Modules.Prestablog.Prestablog'}
	,'','','PrestablogUintTextBox'
)}

{$prestablog->get_displayFormInput(
    'col-lg-5',
    {l s='Avatar width' d='Modules.Prestablog.Prestablog'},
    "{$prestablog->name}_author_pic_width",
    Configuration::get("{$prestablog->name}_author_pic_width"),
    10,
    'col-lg-4'
	,'','','','PrestablogUintTextBox'
)}

{$prestablog->get_displayFormInput(
    'col-lg-5',
    {l s='Avatar height' d='Modules.Prestablog.Prestablog'},
    "{$prestablog->name}_author_pic_height",
    Configuration::get("{$prestablog->name}_author_pic_height"),
    10,
    'col-lg-4'
	,'','','','PrestablogUintTextBox'
)}

{$prestablog->get_displayFormSubmit('submitAuthorDisplay', 'icon-save', {l s='Update' d='Modules.Prestablog.Prestablog'})}
{$prestablog->get_displayFormClose()}
</div>
<script type="text/javascript" src="../modules/prestablog/views/js/numbers.js"></script>
</div>
<div class="clearfix"></div>