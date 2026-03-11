{if $x13eucookies_config.change_after && ($x13eucookies_config.widget_style == 'navbar' || $x13eucookies_config.widget_style == 'icon_navbar')}
    {if $x13eucookies_ps16}
        <button
            class="x13eucookies__navbar x13eucookies__navbar--ps16 x13eucookies__navbar--{$x13eucookies_hookName} x13eucookies__btn x13eucookies__btn--unstyle">
            {l s='Cookies' mod='x13eucookies'}
        </button>
    {else}
        <button
            class="x13eucookies__navbar x13eucookies__navbar--ps17 x13eucookies__navbar--{$x13eucookies_hookName} x13eucookies__btn x13eucookies__btn--unstyle">
            {l s='Cookies' mod='x13eucookies'}
        </button>
    {/if}
{/if}