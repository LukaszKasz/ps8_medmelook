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
    {$prestablog->get_displayFormOpen('blocs.png', "{l s='Block last news' d='Modules.Prestablog.Prestablog'}",
        $confpath)}

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_lastnews_actif")}
    
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show introduction text' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_lastnews_showintro","{l s='This option may penalize your SEO.' d='Modules.Prestablog.Prestablog'}")}

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show thumb' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_lastnews_showthumb")}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Title length' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_lastnews_title_length", Configuration::get("{$prestablog->name}_lastnews_title_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Introduction length' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_lastnews_intro_length", Configuration::get("{$prestablog->name}_lastnews_intro_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}

    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Number of news to display' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_lastnews_limit", Configuration::get("{$prestablog->name}_lastnews_limit"), 10, 'col-lg-4','','','','PrestablogUintTextBox')}

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Link "show all"' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_lastnews_showall")}

    {$prestablog->get_displayFormSubmit('submitConfBlocLastNews','icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}

    {$prestablog->get_displayFormClose()}


    {$prestablog->get_displayFormOpen('blocs.png', "{l s='Block date news' d='Modules.Prestablog.Prestablog'}",
    $confpath)}

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_datenews_actif")}
    
    {$prestablog->get_displayFormSelect('col-lg-5', "{l s='Order news' d='Modules.Prestablog.Prestablog'}","{$prestablog->name}_datenews_order",
        Configuration::get("{$prestablog->name}_datenews_order"),['desc' => {l s='Desc' d='Modules.Prestablog.Prestablog'}, 'asc' => {l s='Asc' d='Modules.Prestablog.Prestablog'}],null,
            'col-lg-5')}

    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Link "show all"' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_datenews_showall")}

    {$prestablog->get_displayFormSubmit('submitConfBlocDateNews','icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}

    {$prestablog->get_displayFormOpen('search.png', "{l s='Block search news' d='Modules.Prestablog.Prestablog'}",
        $confpath)}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_blocsearch_actif")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Advance search in the top of results, with filter of categories' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_search_filtrecat")}
    {$prestablog->get_displayFormSubmit('submitConfBlocSearch','icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}

    {$prestablog->get_displayFormOpen('rss.png', "{l s='Block Rss all news' d='Modules.Prestablog.Prestablog'}",
        $confpath)}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_allnews_rss")}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Title length' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_rss_title_length", Configuration::get("{$prestablog->name}_rss_title_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Introduction length' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_rss_intro_length", Configuration::get("{$prestablog->name}_rss_intro_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}

    {$prestablog->get_displayFormSubmit('submitConfBlocRss','icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}", 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}
</div>

<div class='col-md-6'>
    {$prestablog->get_displayFormOpen('order.png', "{l s='Order of the blocks on columns' d='Modules.Prestablog.Prestablog'}",
        $confpath)}
    {$prestablog->get_displayOrderBlocs($sbl, $sbr)}
<script src="{$srcscript1|escape:'html':'UTF-8'}"></script>
<script src="{$srcscript2|escape:'html':'UTF-8'}"></script>

                      <script type="text/javascript">
                      $(function() {
                        $("#sortblocLeft, #sortblocRight").sortable({
                          placeholder: "ui-state-highlight",
                          connectWith: ".connectedSortable",
                          items: "li:not(.ui-state-disabled)",
                          update: function(event, ui) {
                            $.ajax({
                              url: '{$context->link->getAdminLink('AdminPrestaBlogAjax')}',
                              type: "GET",
                              data: {
                                ajax: true,
                                action: 'prestablogrun',
                                do: 'sortBlocs',
                                sortblocLeft: $("#sortblocLeft").sortable("toArray", { attribute: "rel" }),
                                sortblocRight: $("#sortblocRight").sortable("toArray", { attribute: "rel" }),
                                id_shop: '{$context->shop->id}'
                                },
                                success:function(data){}
                                });
                              }
                              }).disableSelection();
                              });
                              </script>
    {$prestablog->get_displayFormClose()}


    {$prestablog->get_displayFormOpen('blocs.png', "{l s='Footer last news' d='Modules.Prestablog.Prestablog'}",
        $confpath)}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Activate' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_footlastnews_actif")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show introduction text' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_footlastnews_intro","{l s='This option may penalize your SEO.' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Number of news to display' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_footlastnews_limit", Configuration::get("{$prestablog->name}_footlastnews_limit"), 10, 'col-lg-4','','','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Title length' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_footer_title_length", Configuration::get("{$prestablog->name}_footer_title_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormInput('col-lg-5', "{l s='Introduction length' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_footer_intro_length", Configuration::get("{$prestablog->name}_footer_intro_length"), 10, 'col-lg-4',"{l s='caracters' d='Modules.Prestablog.Prestablog'}",'','','PrestablogUintTextBox')}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Link "show all"' d='Modules.Prestablog.Prestablog'}",
        "{$prestablog->name}_footlastnews_showall")}
    {$prestablog->get_displayFormEnableItemConfiguration('col-lg-5', "{l s='Show thumb' d='Modules.Prestablog.Prestablog'}",
    "{$prestablog->name}_footlastnews_showthumb")}
    {$prestablog->get_displayFormSubmit('submitConfFooterLastNews','icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}

    {$prestablog->get_displayFormClose()}
</div>
</div>
<script src="{$srcscript3|escape:'html':'UTF-8'}"></script>
</div>
</div>