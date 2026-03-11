{$colClass = 'sm'}
{if $x13eucookies_ps16}
    {$colClass = 'xs'}
{/if}

<div id="x13eucookies-box" class="x13eucookies__box  x13eucookies-hidden">
    <div class="x13eucookies__box-header {if !$x13eucookies_appearance.box.show_title && $x13eucookies_appearance.box.center_logo} x13eucookies__box-header--logo-center{/if}{if $x13eucookies_appearance.box.close_button} x13eucookies__box-header--close{/if}">
        {if $x13eucookies_appearance.box.close_button}
            <button type="button" class="x13eucookies__btn x13eucookies__btn--close" data-action="restore">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="1 1 20 20">
                    <path
                        d="M6.4 18.65 5.35 17.6l5.6-5.6-5.6-5.6L6.4 5.35l5.6 5.6 5.6-5.6 1.05 1.05-5.6 5.6 5.6 5.6-1.05 1.05-5.6-5.6Z" />
                </svg>
            </button>
        {/if}
        <p class="x13eucookies__box-title">
            {if $x13eucookies_appearance.box.show_logo}
                {if $x13eucookies_ps16}
                    <img class="x13eucookies__logo" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}">
                {else}
                    <img class="x13eucookies__logo" src="{$shop.logo}" alt="{$shop.name}">
                {/if}
            {/if}
            {if $x13eucookies_appearance.box.show_title}
                {l s='Cookies' mod='x13eucookies'}
            {/if}
        </p>
    </div>
    <div class="x13eucookies__box-body">
        {if $x13eucookies_appearance.box.style == 'list'}
            {if $x13eucookies_ps16}
                {include file='../x13eucookies_list.tpl'}
            {else}
                {include file='module:x13eucookies/views/templates/hook/_partials/x13eucookies_list.tpl'}
            {/if}
        {elseif $x13eucookies_appearance.box.style == 'tabs'}
            {if $x13eucookies_ps16}
                {include file='../x13eucookies_tabs.tpl'}
            {else}
                {include file='module:x13eucookies/views/templates/hook/_partials/x13eucookies_tabs.tpl'}
            {/if}
        {/if}
    </div>
    <div class="x13eucookies__box-footer">
        <div class="row">
            {if $x13eucookies_appearance.other.deny_button}
            <div class="col col-{$colClass}-4">
                <button class="btn btn-block x13eucookies__btn x13eucookies__btn--deny"
                    data-action="deny">{$x13eucookies_appearance.other.deny_text}</button>
            </div>
            {/if}
            <div class="col col-{$colClass}-4">
                {if $x13eucookies_appearance.box.style != 'list'}
                    <button class=" btn btn-block x13eucookies__btn x13eucookies__btn--settings" data-action="choose">
                        {$x13eucookies_appearance.settings.text}
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="1 1 20 20">
                            <path d="M9.4 17.65 8.35 16.6l4.6-4.6-4.6-4.6L9.4 6.35 15.05 12Z" />
                        </svg></button>
                {/if}

                <button class="btn btn-block x13eucookies__btn x13eucookies__btn--accept-selected {if $x13eucookies_appearance.box.style != 'list'} x13eucookies-hidden
                        {/if}" data-action="accept-selected">{$x13eucookies_appearance.accept_selected.text}</button>
            </div>
            <div class="col col-{$colClass}-4">
                <button class="btn btn-block x13eucookies__btn x13eucookies__btn--accept-all"
                    data-action="accept-all">{$x13eucookies_appearance.accept_all.text}</button>
            </div>
        </div>
    </div>
</div>