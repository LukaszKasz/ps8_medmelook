<ul class="x13eucookies__nav {if $x13eucookies_appearance.box.display_about_cookies} x13eucookies__nav--with-cookies{/if} row" id="x13eucookiesTab" role="tablist">
  <li class="x13eucookies__nav-item">
    <a class="x13eucookies__nav-link active" id="x13eucookies-consents-tab" data-toggle="x13eucookies-tab" href="#x13eucookiesConsents" role="tab"
      aria-controls="x13eucookiesConsents" aria-selected="true">{l s='Consents' mod='x13eucookies'}</a>
  </li>
  <li class="x13eucookies__nav-item">
    <a class="x13eucookies__nav-link" id="x13eucookies-details-tab" data-toggle="x13eucookies-tab" href="#x13eucookiesDetails" role="tab"
      aria-controls="x13eucookiesDetails" aria-selected="false">{l s='Details' mod='x13eucookies'}</a>
  </li>

  {if $x13eucookies_appearance.box.display_about_cookies}
    <li class="x13eucookies__nav-item">
      <a class="x13eucookies__nav-link" id="x13eucookies-cookies-tab" data-toggle="x13eucookies-tab" href="#x13eucookiesCookies" role="tab"
        aria-controls="x13eucookiesCookies" aria-selected="false">{l s='About Cookies' mod='x13eucookies'}</a>
    </li>
  {/if}
</ul>
<div class="x13eucookies__tab-content" id="x13eucookiesTabContent">
  <div class="x13eucookies__tab-pane active" id="x13eucookiesConsents" role="tabpanel" aria-labelledby="x13eucookies-consents-tab">
    <p class="x13eucookies__title">{l s='Cookie information' mod='x13eucookies'}</p>
    <div class="x13eucookies__description x13eucookies__description--nomargin-last">{$x13eucookies_appearance.box.text nofilter}</div>
  </div>
  <div class="x13eucookies__tab-pane" id="x13eucookiesDetails" role="tabpanel" aria-labelledby="x13eucookies-details-tab">
    <p class="x13eucookies__title">{l s='Cookie management' mod='x13eucookies'}</p>
    {if $x13eucookies_ps16}
      {include file='./x13eucookies_select-all.tpl'}
      {include file='./x13eucookies_accordion.tpl'}
    {else}
      {include file='module:x13eucookies/views/templates/hook/_partials/x13eucookies_select-all.tpl'}
      {include file='module:x13eucookies/views/templates/hook/_partials/x13eucookies_accordion.tpl'}
    {/if}
  </div>
  {if $x13eucookies_appearance.box.display_about_cookies}
    <div class="x13eucookies__tab-pane" id="x13eucookiesCookies" role="tabpanel" aria-labelledby="x13eucookies-cookies-tab">
      <p class="x13eucookies__title">{l s='About Cookies' mod='x13eucookies'}</p>
      <div class="x13eucookies__description x13eucookies__description--nomargin-last">{$x13eucookies_appearance.box.about_cookies_text nofilter}</div>
    </div>
  {/if}
</div>