<div id="x13eucookies-accordion">
  {foreach from=$x13eucookies_consents_groups key='consentKey' item='consent'}
    <script data-keepinline="true">
    window.x13eucookies_consent_{$consent.id_xeucookies_cookie_category} = {
      with_consent: () => {
        {$consent.js_with_consent nofilter}
      },
      without_consent: () => {
        {$consent.js_without_consent nofilter}
      }
    }
    </script>
    <div class="x13eucookies__card">

      <div class="x13eucookies__card-header">
        <button class="x13eucookies__btn x13eucookies__btn--unstyle" id="x13eucookies__heading-{$consentKey}"
          data-toggle="x13eucookies-collapse" data-target="#x13eucookies__consent-{$consent.id_xeucookies_cookie_category}" aria-expanded="false"
          aria-controls="x13eucookies__consent-{$consent.id_xeucookies_cookie_category}">
          <span class="accordion-toggler">
            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="2 3 20 20">
              <path d="M12 15.05 6.35 9.375l1.05-1.05 4.6 4.6 4.6-4.6 1.05 1.05Z" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="2 3 20 20">
              <path d="m7.4 15.05-1.05-1.075L12 8.325l5.65 5.65-1.05 1.075-4.6-4.6Z" />
            </svg>
          </span>
          {$consent.name}{if $x13eucookies_appearance.cookies.show_counter} <span
            class="x13eucookies__count">{$consent.cookies|count}</span>{/if}
        </button>
        <div class="x13eucookies__toggle{if $consent.required} required{/if}">
          <input id="x13eucookies-consent-{$consent.id_xeucookies_cookie_category}" type="checkbox" {if $x13eucookies_enable_third_party_cookies || $consent.required} checked="checked"{/if}
            {if $consent.required}disabled{/if} {if $x13eucookies_ps16} class="not_uniform" {/if}
            {if $x13eucookies_config.send_gtm_consents} {if !empty($consent.type)}data-gtm-event="x13eucookies_consent_accepted_{$consent.type}"{/if} data-gtm-consent="{$consent.gtm_consent_type|replace:' ':''}"{/if}
{if $x13eucookies_config.send_microsoft_consents} data-microsoft-consent="{$consent.microsoft_consent_type}"{/if} />
          <label class="x13eucookies__toggle-item" for="x13eucookies-consent-{$consent.id_xeucookies_cookie_category}">
            <span class="x13eucookies__check">
              <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24">
                <path
                  d="M6.4 18.65 5.35 17.6l5.6-5.6-5.6-5.6L6.4 5.35l5.6 5.6 5.6-5.6 1.05 1.05-5.6 5.6 5.6 5.6-1.05 1.05-5.6-5.6Z" />
              </svg>
              <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24">
                <path d="m9.55 17.65-5.325-5.325 1.05-1.075 4.275 4.275 9.175-9.175 1.05 1.075Z" />
              </svg>
            </span>
          </label>
        </div>
      </div>

      <div id="x13eucookies__consent-{$consent.id_xeucookies_cookie_category}" class="x13eucookie-collapse" aria-labelledby="x13eucookies__heading-{$consent.id_xeucookies_cookie_category}"
        data-parent="#x13eucookies-accordion">
        <div class="x13eucookies__card-body">
            <div class="x13eucookies__description {if (empty($consent.cookies) && empty($x13eucookies_appearance.cookies.empty_cookies)) || $x13eucookies_appearance.cookies.display_style != 'groups_details'} x13eucookies__description--nomargin-last{/if}">{$consent.details nofilter}</div>

          {if $x13eucookies_appearance.cookies.display_style == 'groups_details'}
            {if !empty($consent.cookies)}
              <table class="x13eucookies__table">
                <thead>
                  <tr>
                    <th class="col">{l s='Cookies' mod='x13eucookies'}</th>
                    <th class="col">{l s='Provider' mod='x13eucookies'}</th>
                    <th class="col">{l s='Purpose' mod='x13eucookies'}</th>
                    <th class="col">{l s='Validity term' mod='x13eucookies'}</th>
                  </tr>
                </thead>
                <tbody>
                  {foreach from=$consent.cookies key='cookieKey' item='cookie'}
                    <tr data-key="{$cookieKey}">
                      <td data-label="{l s='Cookies' mod='x13eucookies'}"><span>{$cookie.name}</span></td>
                      <td data-label="{l s='Provider' mod='x13eucookies'}"><span>{$cookie.provider}</span></td>
                      <td data-label="{l s='Purpose' mod='x13eucookies'}"><span>{$cookie.details nofilter}</span></td>
                      <td data-label="{l s='Validity term' mod='x13eucookies'}"><span>{$cookie.expiration}</span></td>
                    </tr>
                  {/foreach}
                </tbody>
              </table>
            {elseif !empty($x13eucookies_appearance.cookies.empty_cookies)}
              <div class="x13eucookies__alert">{l s='There are no cookies to display' mod='x13eucookies'}</div>
            {/if}
          {/if}
        </div>
      </div>
    </div>
  {/foreach}
</div>