{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<script type="text/javascript">{$allow_accents_js}
	var iso = '{$iso_tiny_mce}';
	var pathCSS = '{$THEME_CSS_DIR}';
	var ad = '{$ad}';
	id_language = Number({$dl});
</script>

{literal}
<script type="text/javascript">
	function copy2friendlyURLPrestaBlog() {

	  if (!$('#slink_rewrite_'+id_language).attr('disabled')) {
		$('#slink_rewrite_'+id_language).val(
		str2url($('input#title_'+id_language).val().replace(/^[0-9]+\./, ''),
		'UTF-8')
		);

	  }
	}
	function updateFriendlyURLPrestaBlog() {

	  $('#slink_rewrite_'+id_language).val(
	  str2url($('#slink_rewrite_'+id_language).val().replace(/^[0-9]+\./, ''),
	  'UTF-8')
	  );
	}

$(function() {

  $("#submitForm").click(function() {
	  {/literal}
	   	{foreach $languages $language}
      	$('#slink_rewrite_{$language['id_lang']}').removeAttr("disabled");
    	{/foreach}
	  {literal}
  });

  $("#prestablog_control").click(function() {
    if ($('#slink_rewrite_'+id_language).is(':disabled') == true) {
        $('#slink_rewrite_'+id_language).removeAttr("disabled");
        $('#slink_rewrite_'+id_language).css("background-color", "#fff");
        $('#slink_rewrite_'+id_language).css("color", "#000");
		{/literal}
        $(this).html("{l s='Disable this rewrite' d='Modules.Prestablog.Prestablog'}");
		{literal}
    } else {
        $('#slink_rewrite_'+id_language).attr("disabled", true);
        $('#slink_rewrite_'+id_language).css("background-color", "#e0e0e0");
        $('#slink_rewrite_'+id_language).css("color", "#7F7F7F");
		{/literal}
          $(this).html("{l s='Enable this rewrite' d='Modules.Prestablog.Prestablog'}");
		{literal}
    }
  });
{/literal}

  {foreach $languages $language}

    {assign var="lid" value="{(int)$language['id_lang']}"}

    if ($("#slink_rewrite_{$lid}").val() == '') {
      $("#slink_rewrite_{$lid}").removeAttr("disabled");
      $("#slink_rewrite_{$lid}").css("background-color", "#fff");
      $("#slink_rewrite_{$lid}").css("color", "#000");
      $("#prestablog_control").html("{l s='Disable this rewrite' d='Modules.Prestablog.Prestablog'}");
    }

  {/foreach}

});
</script>

{$prestablog->get_displayFormOpen('icon-edit', $legend_title, $confpath)}
{if Tools::getValue('idC')}
  <input type="hidden" name="idC" value="{Tools::getValue('idC')}" />
{/if}

{$prestablog->get_displayFormEnableItem('col-lg-2', {l s='Activate' d='Modules.Prestablog.Prestablog'}, 'actif', {$categories->actif})}

<div class="form-group">
  <label class="control-label col-lg-2">{l s='Title' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-7">
    {foreach $languages $language}
      {assign var="lid" value="{(int)$language['id_lang']}"}
      <div id="title_{$lid}"
      {if $lid == $dl}
        style="display: block;"
      {else}
        style="display: none;"
      {/if}
      >
      <input type="text" name="title_{$lid}" id="title_{$lid}" maxlength="{(int) Configuration::get('prestablog_news_title_length')}"
      {if isset($categories->title[$lid])}
        value="{$categories->title[$lid]|escape:'html':'UTF-8'}"
      {else}
        value=""
      {/if}
   		onkeyup="if (isArrowKey(event)) return; copy2friendlyURLPrestaBlog();" onchange="copy2friendlyURLPrestaBlog();"
      >
      </div>
    {/foreach}
  </div>
  <div class="col-lg-1"> 
      {$prestablog->displayFlagsFor('title', {$div_lang_name})}
  </div>  
</div>

<div class="form-group">
  <label class="control-label col-lg-2 ">{l s='Parent category' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-7">
    {$categories->displaySelectArboCategories(
                CategoriesClass::getListe((int) $context->language->id, 0),
                (int) $categories->parent,
                0,
                {l s='Top level' d='Modules.Prestablog.Prestablog'},
                'parent'
            )}
  </div>
</div>

<div class="form-group">
  <label class="control-label col-lg-2">{l s='SEO' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-7" style="line-height: 35px;">
    <span onclick="$('#seo').slideToggle();" style="cursor: pointer; display: flex;" class="link">
    <i class="material-icons" style="line-height: 35px;">settings</i>
    {l s='Click here to improve SEO' d='Modules.Prestablog.Prestablog'}
    </span>
  </div>
</div>

<div id="seo" style="display: none;">
<div class="form-group">
  <label class="control-label col-lg-2">
    {l s='Url Rewrite' d='Modules.Prestablog.Prestablog'}
    <br/><a href="#" id="prestablog_control" />
    {if isset($categories->id)}
      {l s='Enable this rewrite' d='Modules.Prestablog.Prestablog'}
    {else}
      {l s='Disable this rewrite' d='Modules.Prestablog.Prestablog'}
    {/if}
    </a>
  </label>
  <div class="col-lg-7">
    {foreach $languages $language}
      {assign var="lid" value="{(int)$language['id_lang']}"}
      <div id="link_rewrite_{$lid|escape:'html':'UTF-8'}"
      {if $lid == $dl}
        style="display: block;"
      {else}
        style="display: none;"
      {/if}
      >
      <input type="text" name="link_rewrite_{$lid|escape:'html':'UTF-8'}" id="slink_rewrite_{$lid|escape:'html':'UTF-8'}"
      {if isset($categories->link_rewrite[$lid])}
        value="{$categories->link_rewrite[$lid]|escape:'html':'UTF-8'}"
      {else}
        value=""
      {/if}
      onkeyup="if (isArrowKey(event)) return; updateFriendlyURLPrestaBlog();" onchange="updateFriendlyURLPrestaBlog();"
      {if isset($categories->id)}
        style="color:#7F7F7F;background-color:#e0e0e0;" disabled="true"
      {/if}
      />
      </div>
    {/foreach}
  </div>
  <div class="col-lg-1"> 
      {$prestablog->displayFlagsFor('link_rewrite', {$div_lang_name})}
  </div>  
</div>

<div class="form-group">
  <label class="control-label col-lg-2">{l s='Meta Title' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-7">
    {foreach $languages $language}
      {assign var="lid" value="{(int)$language['id_lang']|escape:'html':'UTF-8'}"}
      
      <div id="meta_title_{$lid|escape:'html':'UTF-8'}"
      {if $lid == $dl}
        style="display: block;"
      {else}
        style="display: none;"
      {/if}
      >
      <input type="text" name="meta_title_{$lid|escape:'html':'UTF-8'}" id="meta_title_{$lid|escape:'html':'UTF-8'}"
      {if isset($categories->meta_title[$lid])}
        value="{$categories->meta_title[$lid]|escape:'html':'UTF-8'}"
      {else}
        value=""
      {/if}
      />
      </div>
    {/foreach}
  </div>
  <div class="col-lg-1"> 
      {$prestablog->displayFlagsFor('meta_title', {$div_lang_name})}
  </div>  
</div>

<div class="form-group">
  <label class="control-label col-lg-2">{l s='Meta Description' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-7">
    {foreach $languages $language}
      {assign var="lid" value="{(int)$language['id_lang']|escape:'html':'UTF-8'}"}
      <div id="meta_description_{$lid|escape:'html':'UTF-8'}"
      {if $lid == $dl}
        style="display: block;"
      {else}
        style="display: none;"
      {/if}
      >
      <input type="text" name="meta_description_{$lid|escape:'html':'UTF-8'}" id="meta_description_{$lid|escape:'html':'UTF-8'}"
      {if isset($categories->meta_description[$lid]) }
        value="{$categories->meta_description[$lid]|escape:'html':'UTF-8'}"
      {else}
        value=""
      {/if}
      />
      </div>
    {/foreach}
  </div>
  <div class="col-lg-1"> 
      {$prestablog->displayFlagsFor('meta_description', {$div_lang_name})}
  </div>  
</div>

<div class="form-group">
  <label class="control-label col-lg-2">{l s='Meta Keywords' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-7">
    {foreach $languages $language}
      {assign var="lid" value="{(int)$language['id_lang']}"}
      <div id="meta_keywords_{$lid|escape:'html':'UTF-8'}"
      {if $lid == $dl}
        style="display: block;"
      {else}
        style="display: none;"
      {/if}
      >
      <input type="text" name="meta_keywords_{$lid|escape:'html':'UTF-8'}" id="meta_keywords_{$lid|escape:'html':'UTF-8'}"
      {if isset($categories->meta_keywords[$lid]) }
        value="{$categories->meta_keywords[$lid]|escape:'html':'UTF-8'}"
      {else}
        value=""
      {/if}
      />
      </div>
    {/foreach}
  </div>
  <div class="col-lg-1"> 
      {$prestablog->displayFlagsFor('meta_keywords', {$div_lang_name})}
  </div>
</div>
</div>

<div class="form-group">
  <label class="control-label col-lg-2">{l s='Picture' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-10">

  {if $demo_mode}
    {$prestablog->displayWarning({l s='Feature disabled on the demo mode' d='Modules.Prestablog.Prestablog'})}
  {/if}

  {if Tools::getValue('idC') && file_exists("{$imgUpPath|escape:'html':'UTF-8'}/c/admincrop_{Tools::getValue('idC')}.jpg")}
    <span id="labelPicture"></span>
    {assign "config_theme_array" PrestaBlog::objectToArray($config_theme)}
    {if Tools::getValue('pfx')}
{literal}
      <script type="text/javascript">
        $(document).ready(function() { $("html, body").animate({scrollTop: $("#labelPicture").offset().top}, 750); });
      </script>
{/literal}
    {/if}
    <script src="{$PS_BASE_URI|escape:'html':'UTF-8'}modules/prestablog/views/js/jquery.Jcrop.prestablog.js"></script>
    <link rel="stylesheet" href="{$PS_BASE_URI|escape:'html':'UTF-8'}modules/prestablog/views/css/jquery.Jcrop.css" type="text/css"/>
    <script language="Javascript">
      var monRatio;
      var monImage;
      var ratioValue = new Array();
      {foreach from=$config_theme_array['categories'] item=$value_theme_array key=$key_theme_array}
        ratioValue['{$key_theme_array|escape:'html':'UTF-8'}'] = {(int) $value_theme_array['width'] / (int) $value_theme_array['height']};
      {/foreach}

      $(function(){
        $("div.togglePreview").hide();
        {if Tools::getValue('pfx')}
          {assign "pfx" Tools::getValue('pfx')}
          $('input[name$="imageChoix"]').filter('[value="{$pfx}"]').attr('checked', true);
          $('input[name$="imageChoix"]').filter('[value="{$pfx}"]').parent().next(1).slideDown();
          $("#pfx").val('{$pfx|escape:'html':'UTF-8'}');
          $("#ratio").val(ratioValue['{$pfx|escape:'html':'UTF-8'}']);
          monRatio = ratioValue['{$pfx|escape:'html':'UTF-8'}'];
          $('#cropbox').Jcrop({
            'aspectRatio' : monRatio,
            'onSelect' : updateCoords
          });
          nomImage = '{l s='Resize' d='Modules.Prestablog.Prestablog'} {$pfx|escape:'html':'UTF-8'}';
          $("#resizeText").html(nomImage);
        {/if}

        $('input[name$="imageChoix"]').change(function () {
          $("div.togglePreview").slideUp();
          $(this).parent().next().slideDown();
          $("#pfx").val($(this).val());
          $("#ratio").val(ratioValue[$(this).val()]);
          monRatio = ratioValue[$(this).val()];
          $('#cropbox').Jcrop({
            'aspectRatio' : monRatio,
            'onSelect' : updateCoords
            });
            nomImage = '{l s='Resize' d='Modules.Prestablog.Prestablog'} '+$("#pfx").val();
            $("#resizeText").html(nomImage);
        });
      });

      function updateCoords(c) {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
      };
      function checkCoords() {
        if (!$('input[name="imageChoix"]:checked').val()) {
          alert('{l s='Please select a picture to crop.' d='Modules.Prestablog.Prestablog'}');
          return false;
        } else {
          if (parseInt($('#w').val()))
            return true;
          alert('{l s='Please select a crop region then press submit.' d='Modules.Prestablog.Prestablog'}');
          return false;
        }
      };
    </script>

    <div id="image" class="col-md-7">
    <div class="blocmodule">
    <img id="cropbox" src="{$imgPathBO|escape:'html':'UTF-8'}{$getT|escape:'html':'UTF-8'}/up-img/c/admincrop_{Tools::getValue('idC')}.jpg?{$md5|escape:'html':'UTF-8'}"/>
    <p align="center">{l s='Filesize' d='Modules.Prestablog.Prestablog'}
    {filesize("{$imgUpPath|escape:'html':'UTF-8'}/c/{Tools::getValue('idC')}.jpg") / 1000}kb
    </p>
    <p>
    <a href="{$confpath|escape:'html':'UTF-8'}&deleteImageBlog&idC={Tools::getValue('idC')}" onclick="return confirm('{l s='Are you sure?' d='Modules.Prestablog.Prestablog'}');">
    <i class="material-icons" style="color: #c05c67;">delete</i>
    {l s='Delete' d='Modules.Prestablog.Prestablog'}
    </a>
    </p>
    <p>

    {$prestablog->displayFormFileNoLabel('imageCategory', 'col-lg-10', "{l s='Format:' d='Modules.Prestablog.Prestablog'} .jpg .png")}
    </p>
    </div>
    </div>
    <div class="col-md-5">
      {foreach from=$config_theme_array['categories'] item=$value_theme_array key=$key_theme_array}
        {if file_exists("{$imgUpPath|escape:'html':'UTF-8'}/c/{$key_theme_array|escape:'html':'UTF-8'}_{Tools::getValue('idC')|escape:'html':'UTF-8'}.jpg")}
          {assign var="attrib_image" value=getimagesize("{$imgUpPath|escape:'html':'UTF-8'}/c/{$key_theme_array|escape:'html':'UTF-8'}_{Tools::getValue('idC')|escape:'html':'UTF-8'}.jpg")}
          {if ((int) $attrib_image[0] > 200)}
            <div class="blocmodule">
            <p>
            <input type="radio" name="imageChoix" value="{$key_theme_array|escape:'html':'UTF-8'}"/>
            {if $key_theme_array == "thumb"}
              {l s='thumb for category list' d='Modules.Prestablog.Prestablog'}
            {elseif $key_theme_array == "full"}
              {l s='full picture for description category list' d='Modules.Prestablog.Prestablog'}
            {else}
              {$key_theme_array|escape:'html':'UTF-8'}
            {/if}
            <span style="font-size: 80%;">(
            {l s='Real size : ' d='Modules.Prestablog.Prestablog'}
            {(int) $value_theme_array['width']|escape:'html':'UTF-8'} * {(int) $value_theme_array['height']|escape:'html':'UTF-8'})
            </span>
            </p>
            <div class="togglePreview" style="text-align:center;">
            <img
            style="border:1px solid #4D4D4D;padding:0px;"
            src="{$path|escape:'html':'UTF-8'}views/img/{$getT|escape:'html':'UTF-8'}/up-img/c/{$key_theme_array|escape:'html':'UTF-8'}_{Tools::getValue('idC')|escape:'html':'UTF-8'}.jpg?{$md5|escape:'html':'UTF-8'}"
            width="200"
            />
            </div>
            </div>
          {/if}
        {/if}
      {/foreach}
    <div class="blocmodule">
{literal}
    <a class="btn btn-default" onclick="if (checkCoords()) {formCrop.submit();}"  >
{/literal}
    <i class="icon-crop"></i>&nbsp;<span id="resizeText">{l s='Resize' d='Modules.Prestablog.Prestablog'}</span>
    </a>
    </div>
    </div>
  {else}
    {$prestablog->displayFormFileNoLabel('imageCategory', 'col-lg-5', "{l s='Format:' d='Modules.Prestablog.Prestablog'} .jpg .png")}
  {/if}

  </div>
</div>

<div class="form-group">
  <label class="control-label col-lg-2">{l s='Description' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-7">
    {foreach $languages $language}
      {assign var="lid" value="{(int)$language['id_lang']|escape:'html':'UTF-8'}"}
      <div id="cpara1_{$lid|escape:'html':'UTF-8'}"
      {if $lid == $dl}
        style="display: block;"
      {else}
        style="display: none;"
      {/if}
      >
      <textarea class="rte autoload_rte" id="description_{$lid|escape:'html':'UTF-8'}" name="description_{$lid|escape:'html':'UTF-8'}">
      {if isset($categories->description[$lid])}
        {$categories->description[$lid]|escape:'html':'UTF-8'}
      {/if}
      </textarea>
      </div>
    {/foreach}
  </div>
  <div class="col-lg-1"> 
      {$prestablog->displayFlagsFor('cpara1', {$div_lang_name})}
  </div>
</div>

{assign "active_group" []}
{if Tools::getValue('idC')}
  {assign "active_group" CategoriesClass::getGroupsFromCategorie((int) Tools::getValue('idC'))}
{/if}
<div class="form-group">
  <label class="control-label col-lg-2">{l s='Groups permissions' d='Modules.Prestablog.Prestablog'}</label>
  <div class="col-lg-7">
    {$prestablog->displayFormGroups($active_group)}
  </div>
</div>

{if !$prestaboost}
  {if isset($popuplink)}
    {$prestablog->get_displayFormSelect('col-lg-2', {l s='Add a popup to your categorie :' d='Modules.Prestablog.Prestablog'}, 'popupLinkCate', $popuplink, $popups_link, null, 'col-lg-5')}
  {else}
    {$prestablog->get_displayFormSelect('col-lg-2', {l s='Add a popup to your categorie :' d='Modules.Prestablog.Prestablog'}, 'popupLinkCate', $getP, $popups_link, null, 'col-lg-5')}
  {/if}
{/if}

<div class="margin-form">

{if Tools::getValue('idC')}
  <button class="btn btn-primary" name="submitUpdateCat" type="submit">
  <i class="icon-save"></i>&nbsp;{l s='Update the category' d='Modules.Prestablog.Prestablog'}
  </button>
{else}
  <button class="btn btn-primary" name="submitAddCat" type="submit">
  <i class="icon-plus"></i>&nbsp;{l s='Add the category' d='Modules.Prestablog.Prestablog'}
  </button>
{/if}

</div>

{$prestablog->get_displayFormClose()}

<form name="formCrop" id="formCrop" action="{$confpath|escape:'html':'UTF-8'}" method="post" onsubmit="return checkCoords();">
<input type="hidden" name="idC" value="{Tools::getValue('idC')|escape:'html':'UTF-8'}"/>
<input type="hidden" id="pfx" name="pfx" value="{Tools::getValue('pfx')|escape:'html':'UTF-8'}"/>
<input type="hidden" id="x" name="x" />
<input type="hidden" id="y" name="y" />
<input type="hidden" id="w" name="w" />
<input type="hidden" id="h" name="h" />
<input type="hidden" id="ratio" name="ratio" />
<input type="hidden" name="submitCrop" value="submitCrop" />
</form>