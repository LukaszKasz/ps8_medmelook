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

<div class="blocmodule">
       <fieldset>
              <div class="col-sm-3">
                     <a class="btn btn-primary" href="{$confpath|escape:'htmlall':'UTF-8'}&addSubBlock">
                     <i class="icon-plus"></i>&nbsp;
				{l s='Create a list' d='Modules.Prestablog.Prestablog'}
                     </a>
              </div>
       </fieldset>
</div>

{if count($liste_hook) > 0}
       
       <script src="{$javascript1|escape:'url':'UTF-8'}"></script>
       <script type="text/javascript" src="{$javascript2|escape:'url':'UTF-8'}"></script>

       {foreach $liste_hook $hook_name}
              <div class="blocmodule">
                     {assign "liste" SubBlocksClass::getListe(null, 0, $hook_name)}
                     <fieldset>
                     <legend style="margin-bottom:10px;">{l s='Articles list for ' d='Modules.Prestablog.Prestablog'}
                     <span class="label label-success">{$hook_name|escape:'htmlall':'UTF-8'}</span>
                     </legend>
                     <table class="table" cellpadding="0" cellspacing="0" style="margin:auto;width:100%;">
                     <thead class="center">
                     <tr>
                     <th>Id</th>
                     {if $hook_name == 'displayCustomHook'}
                            <th>{l s='Add this shortcode directly in your tpl' d='Modules.Prestablog.Prestablog'}</th>
                     {else}
                            <th>{l s='Positions' d='Modules.Prestablog.Prestablog'}</th>
                     {/if}
                     <th style="text-align:center">{l s='Type' d='Modules.Prestablog.Prestablog'}</th>
                     <th style="text-align:center">{l s='Languages' d='Modules.Prestablog.Prestablog'}</th>
                     <th style="text-align:center">{l s='Categories' d='Modules.Prestablog.Prestablog'}</th>
                     <th style="text-align:center">{l s='Limit list' d='Modules.Prestablog.Prestablog'}</th>
                     <th style="text-align:center">{l s='Custom template' d='Modules.Prestablog.Prestablog'}</th>
                     <th style="text-align:center">{l s='Random' d='Modules.Prestablog.Prestablog'}</th>
                     <th style="text-align:center">{l s='Blog link' d='Modules.Prestablog.Prestablog'}</th>
                     <th style="text-align:center">{l s='Activate' d='Modules.Prestablog.Prestablog'}</th>
                     <th style="text-align:center">{l s='Actions' d='Modules.Prestablog.Prestablog'}</th>
                     </tr>
                     </thead>
                     {if count($liste) > 0}
                            {if $hook_name == 'displayCustomHook'}
                                <tbody>
                            {else}
                                <tbody id="subblocks_positions_{$liste[0]['hook_name']}">
                            {/if}
                            {foreach $liste $value}
                                   {if $hook_name == 'displayCustomHook'}
                                          <tr>
                                   {else}
                                          <tr class="odd" order-id="{(int) $value['id_prestablog_subblock']}">
                                   {/if}

                                   <td style="text-align:center">{(int) $value['id_prestablog_subblock']}</td>
                                   {if $hook_name == 'displayCustomHook'}
                                          <td style="text-align:center"><strong>
                                          {assign "idsb" $value['id_prestablog_subblock']}
                                          {literal}{hook h='displayPrestaBlogList' id='{/literal}{$idsb|escape:'htmlall':'UTF-8'}{literal}' mod='prestablog'}{/literal}
                                          </strong></td>
                                   {else}
                                          <td class="pointer" style="text-align:center;">
                                          <img src="{$imgPathFO|escape:'htmlall':'UTF-8'}move.png" />
                                          </td>
                                   {/if}
                                   <td style="text-align:center">{$select_type[(int) $value['select_type']]|escape:'htmlall':'UTF-8'}</td>
                                   <td>

                                   {assign "lang_liste_news" json_decode($value['langues'], true)}
                                   {if is_array($lang_liste_news) && count($lang_liste_news) > 0}
                                          {foreach $lang_liste_news $val_langue}
                                                 {if count($languages) >= 1 && array_key_exists((int) $val_langue, $languages_shop)}
                                                        {assign "gettsb" SubBlocksClass::getTitleSubBlock((int) $value['id_prestablog_subblock'], (int) $val_langue)}
                                                        <img src="../img/l/{(int)$val_langue}.jpg"/> {$gettsb}<br/>
                                                 {/if}                                                 
                                          {/foreach}
                                   {else}
                                          -
                                   {/if}
                                   </td>
                                   <td>
                                   {$prestablog->verbose_blog_categories($value)}
                                   </td>
                                   <td style="text-align:center">{(int) $value['nb_list']}</td>
                                   <td style="text-align:center">
                                   {if $value['template'] != ''}
                                          {$value['template']|escape:'htmlall':'UTF-8'}
                                   {else}
                                          -
                                   {/if}
                                   </td>
                                   <td style="text-align:center">
                                   <a href="{$confpath|escape:'htmlall':'UTF-8'}&randSubBlock&idSB={$value['id_prestablog_subblock']}">
                                   {if $value['random']}
                                          <i class="material-icons action-enabled" style="color: #78d07d;">check</i>
                                   {else}
                                          <i class="material-icons action-disabled" style="color: #c05c67;">clear</i>
                                   {/if}
                                   </a>
                                   </td>

                                   <td style="text-align:center">
                                   <a href="{$confpath|escape:'htmlall':'UTF-8'}&blog_linkSubBlock&idSB={$value['id_prestablog_subblock']}">
                                   {if $value['blog_link']}
                                          <i class="material-icons action-enabled" style="color: #78d07d;">check</i>
                                   {else}
                                          <i class="material-icons action-disabled" style="color: #c05c67;">clear</i>
                                   {/if}
                                   </a>
                                   </td>

                                   <td style="text-align:center">
                                   <a href="{$confpath|escape:'htmlall':'UTF-8'}&etatSubBlock&idSB={$value['id_prestablog_subblock']}">
                                   {if $value['actif']}
                                          <i class="material-icons action-enabled" style="color: #78d07d;">check</i>
                                   {else}
                                          <i class="material-icons action-disabled" style="color: #c05c67;">clear</i>
                                   {/if}
                                   </a>
                                   </td>

                                   <td style="text-align:center">
                                   <a href="{$confpath|escape:'htmlall':'UTF-8'}&editSubBlock&idSB={$value['id_prestablog_subblock']}" title="{l s='Edit' d='Modules.Prestablog.Prestablog'}">
                                   <i class="material-icons" style="color: #6c868e;">mode_edit</i>
                                   </a>
                                   
                                   <a href="{$confpath|escape:'htmlall':'UTF-8'}&deleteSubBlock&idSB={$value['id_prestablog_subblock']}"
                                   onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
                                   <i class="material-icons" style="color: #c05c67;">delete</i>
                                   </a>
                                   </td>
                                   </tr>                                   
                            {/foreach}
                            </tbody>
<script type="text/javascript">
$(function() {
  $("#subblocks_positions_{$value['hook_name']}").sortable({
    axis: 'y',
    placeholder: "ui-state-highlight",
    update: function(event, ui) {
      $.ajax({
        url: '{$context->link->getAdminLink('AdminPrestaBlogAjax')}',
        type: "GET",
        data: {
          action: 'prestablogrun',
          items: $(this).sortable('toArray', { attribute: 'order-id' }),
          ajax: true,
          do: 'sortSubBlocks',
          id_shop: '{$context->shop->id}',
          hook_name: '{$value['hook_name']}'
        },
        success:function(data){}
      });
    }
  }).disableSelection();
});
</script>
                     {else}
                            <tr><td colspan="5" class="center">{l s='No content registered' d='Modules.Prestablog.Prestablog'}</td></tr>
                     {/if}
                     </table>
                     </fieldset>
              </div>
       {/foreach}
{/if}