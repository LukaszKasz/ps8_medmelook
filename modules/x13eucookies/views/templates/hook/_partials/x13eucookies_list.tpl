<p class="x13eucookies__title x13eucookies__title--list">{l s='Cookie information' mod='x13eucookies'}</p>
<div class="x13eucookies__description">{$x13eucookies_appearance.box.text nofilter}</div>
<p class="x13eucookies__title x13eucookies__title--list">{l s='Cookie management' mod='x13eucookies'}</p>

{if $x13eucookies_ps16}
    {include file='./x13eucookies_select-all.tpl'}
    {include file='./x13eucookies_accordion.tpl'}
{else}
    {include file='module:x13eucookies/views/templates/hook/_partials/x13eucookies_select-all.tpl'}
    {include file='module:x13eucookies/views/templates/hook/_partials/x13eucookies_accordion.tpl'}
{/if}

{if $x13eucookies_appearance.box.display_about_cookies}
    <p class="x13eucookies__title x13eucookies__title--list">{l s='About Cookies' mod='x13eucookies'}</p>
    <div class="x13eucookies__description x13eucookies__description--nomargin-last">{$x13eucookies_appearance.box.about_cookies_text nofilter}</div>
{/if}