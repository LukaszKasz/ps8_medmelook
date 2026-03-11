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
    {$prestablog->get_displayFormOpen('comments.png', {l s='Color of blog\'s element' d='Modules.Prestablog.Prestablog'}, $confpath)}
           
    {$prestablog->get_displayFormOpen('', "{l s='Menu' d='Modules.Prestablog.Prestablog'}", $confpath)}
<div class='col-md-8'>
    {$prestablog->get_displayFormInputColor('col-lg-5',
        '1',
        "{l s='Background' d='Modules.Prestablog.Prestablog'}",
        'menu_color',
        $prestablog->verifConditionSmarty($menu_color),
        'mColorPicker')}
    {$prestablog->get_displayFormInputColor('col-lg-5',
        '2',
        "{l s='Background hover' d='Modules.Prestablog.Prestablog'}",
        'menu_hover',
        $prestablog->verifConditionSmarty($menu_hover),
        'mColorPicker')}
    {$prestablog->get_displayFormInputColor('col-lg-5',
        '3',
        "{l s='Links' d='Modules.Prestablog.Prestablog'}",
        'menu_link',
        $prestablog->verifConditionSmarty($menu_link),
        'mColorPicker')}
</div>
<div class='col-md-4'>
    <img src='{$prestablog->imgPathBO()}colorpicker/color-menu.png' style='max-width: 100%; height: auto;'>
</div></fieldset></div>

<div class='blocmodule'><fieldset><legend> {l s='Pagination' d='Modules.Prestablog.Prestablog'}</legend>
    <div class='col-md-8'>

        {$prestablog->get_displayFormInputColor('col-md-5',
            '1',
            "{l s='Current-background' d='Modules.Prestablog.Prestablog'}",
            'ariane_color',
            $prestablog->verifConditionSmarty($ariane_color),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '2',
            "{l s='Current-text' d='Modules.Prestablog.Prestablog'}",
            'ariane_color_text',
            $prestablog->verifConditionSmarty($ariane_color_text),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '3',
            "{l s='Current-border' d='Modules.Prestablog.Prestablog'}",
            'ariane_border',
            $prestablog->verifConditionSmarty($ariane_border),
            'mColorPicker')}
    </div>
    <div class='col-md-4'>
        <img src='{$prestablog->imgPathBO()}colorpicker/color-pagination.png' style='max-width: 100%; height: auto;'>
    </div>
    </fieldset>
</div>

<div class='blocmodule'>
        <fieldset><legend> {l s='Categories listing' d='Modules.Prestablog.Prestablog'}</legend>
    <div class='col-md-8'>

        {$prestablog->get_displayFormInputColor('col-lg-5',
            '1',
            "{l s='Title' d='Modules.Prestablog.Prestablog'}",
            'title_color',
            $prestablog->verifConditionSmarty($title_color),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '2',
            "{l s='Text' d='Modules.Prestablog.Prestablog'}",
            'text_color',
            $prestablog->verifConditionSmarty($text_color),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '3',
            "{l s='Articles background' d='Modules.Prestablog.Prestablog'}",
            'categorie_block_background',
            $prestablog->verifConditionSmarty($categorie_block_background),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '4',
            "{l s='Articles background hover' d='Modules.Prestablog.Prestablog'}",
            'categorie_block_background_hover',
            $prestablog->verifConditionSmarty($categorie_block_background_hover),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '5',
            "{l s='Read more link' d='Modules.Prestablog.Prestablog'}",
            'link_read',
            $prestablog->verifConditionSmarty($link_read),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '6',
            "{l s='Read more background' d='Modules.Prestablog.Prestablog'}",
            'read_color',
            $prestablog->verifConditionSmarty($read_color),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '7',
            "{l s='Read more hover' d='Modules.Prestablog.Prestablog'}",
            'hover_color',
            $prestablog->verifConditionSmarty($hover_color),
            'mColorPicker')}
    </div>
    <div class='col-md-4'>
        <img src='{$prestablog->imgPathBO()}colorpicker/color-listing.png' style='max-width: 100%; height: auto;'>
    </div>
    </fieldset>
</div>

<div class='blocmodule'>
    <fieldset><legend> {l s='Articles content' d='Modules.Prestablog.Prestablog'}</legend>
        <div class='col-md-8'>
            {$prestablog->get_displayFormInputColor('col-lg-5',
                '1',
                "{l s='Title' d='Modules.Prestablog.Prestablog'}",
                'article_title',
                $prestablog->verifConditionSmarty($article_title),
                'mColorPicker')}
            {$prestablog->get_displayFormInputColor('col-lg-5',
               '2',
               "{l s='Text' d='Modules.Prestablog.Prestablog'}",
               'article_text',
               $prestablog->verifConditionSmarty($article_text),
               'mColorPicker')}
           {$prestablog->get_displayFormInputColor('col-lg-5',
               '3',
               "{l s='Background' d='Modules.Prestablog.Prestablog'}",
               'article_background',
               $prestablog->verifConditionSmarty($article_background),
               'mColorPicker')}
            {$prestablog->get_displayFormInputColor('col-lg-5',
               '3',
               "{l s='Sharing Icons' d='Modules.Prestablog.Prestablog'}",
               'sharing_icon_color',
               $prestablog->verifConditionSmarty($sharing_icon_color),
               'mColorPicker')}
        </div>
    </fieldset>
</div>

<div class='blocmodule'>
    <fieldset><legend> {l s='Blocks content' d='Modules.Prestablog.Prestablog'}</legend>
    <div class='col-md-8'>
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '1',
            "{l s='Titles' d='Modules.Prestablog.Prestablog'}",
            'block_title',
            $prestablog->verifConditionSmarty($block_title),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '2',
            "{l s='Background' d='Modules.Prestablog.Prestablog'}",
            'block_categories',
            $prestablog->verifConditionSmarty($block_categories),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '3',
            "{l s='Links' d='Modules.Prestablog.Prestablog'}",
            'block_categories_link',
            $prestablog->verifConditionSmarty($block_categories_link),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '4',
            "{l s='Links button text' d='Modules.Prestablog.Prestablog'}",
            'block_categories_link_btn',
            $prestablog->verifConditionSmarty($block_categories_link_btn),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '5',
            "{l s='Buttons' d='Modules.Prestablog.Prestablog'}",
            'block_btn',
            $prestablog->verifConditionSmarty($block_btn),
            'mColorPicker')}
        {$prestablog->get_displayFormInputColor('col-lg-5',
            '6',
            "{l s='Buttons hover' d='Modules.Prestablog.Prestablog'}",
            'block_btn_hover',
            $prestablog->verifConditionSmarty($block_btn_hover),
            'mColorPicker')}

    </div>
    <div class='col-md-4'>
        <img src='{$prestablog->imgPathBO()}colorpicker/color-blocks.png' style='max-width: 100%; height: auto;'>
    </div>
    </fieldset>
</div>

    {$prestablog->get_displayFormSubmit('submitColorBlog','icon-save', "{l s='Save' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}
</div>
<div class='col-md-6'>
    {$prestablog->get_displayFormOpen('comments.png', "{l s='Custom css' d='Modules.Prestablog.Prestablog'}",$confpath)}

<div class='form-group'>
    <label class='control-label col-md-2'>{l s='Custom css' d='Modules.Prestablog.Prestablog'}</label>
    <div class='col-md-10'>
        {if $css == ''}
            <div style='display: block;'>
                <textarea class='rte autoload_rte' id='content_css' name='content_css' style='height: 350px;'>/**
                    * 2008 - 2024 (c) Prestablog
                    *
                    * MODULE PrestaBlog
                    *
                    * @author    Prestablog
                    * @copyright Copyright (c) permanent, Prestablog
                    * @license   Commercial
                    */
                </textarea>
            </div>
        {else}
            <div style='display: block;'>
                <textarea class='rte autoload_rte'
                    id='content_css'
                    name='content_css'
                    style='height: 350px;'>{$css}</textarea>
            </div>
        {/if}
    <br>   
        {$prestablog->get_displayFormSubmit('submitConfCss','icon-save', "{l s='Save' d='Modules.Prestablog.Prestablog'}")}
    </div>

    {$prestablog->get_displayFormClose()}
    </div>
    <script type="text/javascript" src="{__PS_BASE_URI__}js/jquery/plugins/jquery.colorpicker.js"></script>
    <script type="text/javascript">$.fn.mColorPicker.init.replace = ".mColorPicker"</script>
</div>
</div>