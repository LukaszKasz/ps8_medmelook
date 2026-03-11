{if $x13eucookies_appearance.cookies.display_select_all}
    <div class="x13eucookies__select-all">
        <button class="x13eucookies__btn x13eucookies__btn--unstyle"
            data-action="select-all">{l s='Select all' mod='x13eucookies'}</button>
        <div class="x13eucookies__toggle">
            <input id="x13eucookies-select-all" type="checkbox"{if $x13eucookies_enable_third_party_cookies} checked="checked" {/if} {if $x13eucookies_ps16} class="not_uniform"{/if} />
            <label class="x13eucookies__toggle-item" for="x13eucookies-select-all">
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
{/if}