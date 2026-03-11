{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div class="input-group">
    {foreach from=$languages item=language}
        <a class="btn btn-default"
           href="#"
           data-toggle="modal"
           data-target="#popup-content-{(int)$language.id_lang}">
            <i class="icon-eye"></i> {$language.iso_code}
        </a>
        {$popup->displayPopup((int)$language.id_lang, (int) $helper->id)}
    {/foreach}
</div>
