{**
 * 2008 - 2017 (c) Prestablog
 *
 * MODULE Prestablog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}

<!-- Module Prestablog -->
<div class="modal fade popup-content" id="popup-content-{$id_lang|intval}" data-delay="{$Popup->delay|intval|escape:'htmlall':'UTF-8'}">
  <div style="max-width:{$Popup->width|intval|escape:'htmlall':'UTF-8'}px;">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      {PopupContent return=$Popup->content|escape:'html':'UTF-8' adminPreview=$adminPreview}

  </div>
</div>
<!-- /Module Prestablog -->
