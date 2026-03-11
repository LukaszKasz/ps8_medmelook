{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{assign "htmlLibre" ""}
{if $demo_mode}
    {$prestablog->displayWarning("{l s='Feature disabled on the demo mode' d='Modules.Prestablog.Prestablog'}")}
{/if}

{$prestablog->get_displayFormOpen('icon-upload', "{l s='Import from Wordpress XML file' d='Modules.Prestablog.Prestablog'}",
    $confpath)}

{$prestablog->displayWarning("{l s='Be carefull ! Select only Articles exportation on your WordPress.' d='Modules.Prestablog.Prestablog'}")}

{$prestablog->get_displayFormFile('col-lg-2',"{l s='Upload file' d='Modules.Prestablog.Prestablog'}", "{$prestablog->name|escape:'html':'UTF-8'}_import_xml",
'col-lg-5',"{l s='Format' d='Modules.Prestablog.Prestablog'}*.xml")}

{$prestablog->get_displayFormSubmit('submitImportXml','icon-cloud-upload', "{l s='Send file' d='Modules.Prestablog.Prestablog'}")}
{$prestablog->get_displayFormClose()}

{if Configuration::get("{$prestablog->name|escape:'html':'UTF-8'}_import_xml")} 
    {if !$file}
        <br/>
        {$prestablog->get_displayError("{l s='The XML file in the configuration is not locate in the ./download directory' d='Modules.Prestablog.Prestablog'} <br/> {l s='You must upload a new import XML file.' d='Modules.Prestablog.Prestablog'}")}
    {else}
        {if strpos($file_content, '<?xml') === false} 
        <br/>
        {$prestablog->displayError("{l s='The file is not an XML content' d='Modules.Prestablog.Prestablog'} <br/> {l s='You must upload a new import XML file.' d='Modules.Prestablog.Prestablog'}")}
    {else}
    {$prestablog->get_displayFormOpen('icon-gear', "{l s='Chose the language where you want to import this xml' d='Modules.Prestablog.Prestablog'}", $confpath)}
            {l s='Current XML import file in configuration :' d='Modules.Prestablog.Prestablog'}
            {Configuration::get("{$prestablog->name|escape:'html':'UTF-8'}_import_xml")}
    
            <div class='col-lg-2'>{l s='Select language' d='Modules.Prestablog.Prestablog'}</div>
            <div class='col-lg-7'>
            {foreach $languages $language}
                <input type='radio' name='import_xml_langue' value='{$language['id_lang']|escape:'html':'UTF-8'}' {if $prestablog->langue_default_store == {$language['id_lang']|escape:'html':'UTF-8'}}checked {/if}> <img src='../img/l/{$language['id_lang']|escape:'html':'UTF-8'}.jpg' class='prestablogflag'>
            {/foreach}
            </div>
    
    {$prestablog->get_displayFormSubmit('submitParseXml','icon-gears', "{l s='Import the current file' d='Modules.Prestablog.Prestablog'}")}
    {$prestablog->get_displayFormClose()}

        {/if}
    {/if}
{/if}



