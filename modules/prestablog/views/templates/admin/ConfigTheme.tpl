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
{$prestablog->get_displayFormOpen('theme.png', "{l s='Theme' d='Modules.Prestablog.Prestablog'}", $confpath)}
        

{$prestablog->get_displayFormSelect('col-lg-5', "{l s='Choose your module theme :' d='Modules.Prestablog.Prestablog'}",'theme', $getT,$themes,null,'col-lg-5')}
{literal}
    <script language="javascript" type="text/javascript">
                $(document).ready(function() {
                  $("#theme").change(function() {
                    var src = $(this).val();
                    var issrc = "<img src=\'../modules/prestablog/views/img/" + src + "-preview.jpg\' width='400px'>";
                    $("#imagePreview").hide();
                    $("#imagePreview").html(src ? issrc : "");
                    $("#imagePreview").fadeIn();
                    });
                    });
                    </script>
{/literal}
  
<label>{l s='Preview :' d='Modules.Prestablog.Prestablog'}</label>

                    <div id='imagePreview'>
                    <img src='{$src|escape:'html':'UTF-8'}' width='400px'>
                    </div>
                    <div class='clear'></div>

{$prestablog->get_displayFormSubmit('submitTheme', 'icon-save', "{l s='Update' d='Modules.Prestablog.Prestablog'}")}
{$prestablog->get_displayFormClose()}
{if $layerslider}
    {$prestablog->displayCreativeSlide()}
    {$prestablog->get_displayInfo('col-lg-5', "{l s='The slide will be displayed in your blog\'s welcome page' d='Modules.Prestablog.Prestablog'}}")}
{/if}
</div>
<div class='col-md-6'>
{$prestablog->get_displayConfLayout()}
</div>
</div>
</div>





      














