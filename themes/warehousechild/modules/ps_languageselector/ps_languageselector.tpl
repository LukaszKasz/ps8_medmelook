{*<div id="language_selector" class="d-inline-block" style="border: 1px solid #333; border-radius: 10px; text-align: center; padding-left: 5px; margin-left: 5px;">*}
<div id="language_selector" class="d-inline-block" style="padding-left: 5px; margin-left: 5px;">
    <div class="language-selector-wrapper d-inline-block">
        <div class="language-selector dropdown js-dropdown">
            <a class="expand-more" data-toggle="dropdown" data-iso-code="{$language.iso_code}"><img width="16" height="11" src="{$urls.img_lang_url}{$current_language.id_lang}.jpg" alt="{$current_language.name_simple}" class="img-fluid lang-flag" />  {$current_language.name_simple} <i class="fa fa-angle-down fa-fw" aria-hidden="true"></i></a>
            <div class="dropdown-menu">
                <ul>
                    {foreach from=$languages item=language}
                        <li {if $language.id_lang == $current_language.id_lang} class="current" {/if}>
                            {if $language.id_lang == 1}
                                <a href="{url entity='language' id=$language.id_lang}?SubmitCurrency=1&id_currency=1" rel="alternate" hreflang="{$language.iso_code}"
                                   class="dropdown-item"><img width="16" height="11" src="{$urls.img_lang_url}{$language.id_lang}.jpg" alt="{$language.name_simple}" class="img-fluid lang-flag"  data-iso-code="{$language.iso_code}"/>  {$language.name_simple} </a>
                            {/if}
                            {if $language.id_lang == 3}
                                <a href="{url entity='language' id=$language.id_lang}?SubmitCurrency=1&id_currency=2" rel="alternate" hreflang="{$language.iso_code}"
                                   class="dropdown-item"><img width="16" height="11" src="{$urls.img_lang_url}{$language.id_lang}.jpg" alt="{$language.name_simple}" class="img-fluid lang-flag"  data-iso-code="{$language.iso_code}"/>  {$language.name_simple} </a>
                            {/if}
                            {if $language.id_lang == 4}
                                <a href="{url entity='language' id=$language.id_lang}?SubmitCurrency=1&id_currency=2" rel="alternate" hreflang="{$language.iso_code}"
                                   class="dropdown-item"><img width="16" height="11" src="{$urls.img_lang_url}{$language.id_lang}.jpg" alt="{$language.name_simple}" class="img-fluid lang-flag"  data-iso-code="{$language.iso_code}"/>  {$language.name_simple} </a>
                            {/if}
                            {if $language.id_lang == 6}
                                <a href="{url entity='language' id=$language.id_lang}?SubmitCurrency=1&id_currency=3" rel="alternate" hreflang="{$language.iso_code}"
                                   class="dropdown-item"><img width="16" height="11" src="{$urls.img_lang_url}{$language.id_lang}.jpg" alt="{$language.name_simple}" class="img-fluid lang-flag"  data-iso-code="{$language.iso_code}"/>  {$language.name_simple} </a>
                            {/if}
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </div>
</div>