{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{$prestablog->displayHtml("
{if $editPermissionAccess}    
    <div class='blocmodule'>
        <fieldset class='row'>

            <div class='col-sm-3'>
                <a class='btn btn-primary' href='{$confpath}&addCat'>
                    <i class='icon-plus'></i>&nbsp;
					{l s='Add a category' d='Modules.Prestablog.Prestablog'}
                </a>
             </div>
             <div class='col-sm-3'>
                <a class='btn btn-primary' href='{$confpath}&orderCat'>
                    <i class='icon-sort-numeric-asc'></i>&nbsp;
                        {l s='Order of categories' d='Modules.Prestablog.Prestablog'}
                </a>
            </div>
        </fieldset>
    </div>
{/if}
    <div class='blocmodule'>
        <fieldset>
            <legend style='margin-bottom:10px;'>{l s='Categories' d='Modules.Prestablog.Prestablog'}</legend>
            <table class='table_news' cellpadding='0' cellspacing='0' style='margin:auto;width:100%;'>
            <thead class='center'>
                <tr>
                    <th><p>Id</p></th>
                    <th><p>{l s='Image' d='Modules.Prestablog.Prestablog'}</p></th>
                    <th><p>{l s='Title' d='Modules.Prestablog.Prestablog'}</p></th>
                    <th><p>{l s='Title Meta' d='Modules.Prestablog.Prestablog'}</p></th>
                    <th><p><img src='{$imgPathBO}group.png'> {l s='Groups permissions' d='Modules.Prestablog.Prestablog'}
                          </p></th>
")}              
            {if !$prestaboost}
                {$prestablog->displayHtml("<th><p>{l s='Popup' d='Modules.Prestablog.Prestablog'}</p></th>")}
            {/if}
                    {$prestablog->displayHtml("
                    <th><p>{l s='Use in articles' d='Modules.Prestablog.Prestablog'}</p></th>
                    <th class='center'><p>{l s='Activate' d='Modules.Prestablog.Prestablog'}</p></th>
                    <th class='center'><p>{l s='Actions' d='Modules.Prestablog.Prestablog'}</p></th>
                </tr>
            </thead>
                ")}

        {if (count($liste) > 0)}
            {$prestablog->displayHtml({$prestablog->displayListeArborescenceCategories($liste)})}
        
        {else}                    
            {$prestablog->displayHtml("
                <tr><td colspan='5' class='center'>{l s='No content registered' d='Modules.Prestablog.Prestablog'}</td></tr>
             ")}
        {/if}
{$prestablog->displayHtml("
           </table>
        </fieldset>
    </div>
")}