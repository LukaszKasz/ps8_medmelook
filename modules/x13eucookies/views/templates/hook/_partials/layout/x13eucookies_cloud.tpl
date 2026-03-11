{$colClass = 'sm'}
{if $x13eucookies_ps16}
    {$colClass = 'xs'}
{/if}

<div id="x13eucookies-box"
    class="x13eucookies__box {if $x13eucookies_config.layout == 'cloud_full_height'} x13eucookies__box--cloud-full {else}x13eucookies__box--cloud  {/if} x13eucookies-hidden">
    <div class="x13eucookies__box-inner">
        <div
            class="x13eucookies__box-header {if !$x13eucookies_appearance.box.show_title && $x13eucookies_appearance.box.center_logo} x13eucookies__box-header--logo-center{/if}">
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
        <div
            class="x13eucookies__box-footer {if $x13eucookies_appearance.box.style == 'list'}{if $x13eucookies_appearance.other.deny_button}x13eucookies__box-footer--cloud-list {/if}{else} x13eucookies__box-footer--cloud-tabs{/if}">
            <div class="row">
                {if $x13eucookies_appearance.box.style == 'list'}
                    {if $x13eucookies_appearance.other.deny_button}
                        <div class="col col-{$colClass}-6">
                            <button class="btn btn-block x13eucookies__btn x13eucookies__btn--deny"
                                data-action="deny">{$x13eucookies_appearance.other.deny_text}</button>
                        </div>
                        <div class="col col-{$colClass}-6">
                            <button class="btn btn-block x13eucookies__btn x13eucookies__btn--accept-selected"
                                data-action="accept-selected">{$x13eucookies_appearance.accept_selected.text}</button>
                        </div>
                        <div class="col col-{$colClass}-12">
                            <button class="btn btn-block x13eucookies__btn x13eucookies__btn--accept-all"
                                data-action="accept-all">{$x13eucookies_appearance.accept_all.text}</button>
                        </div>
                    {else}
                        <div class="col col-{$colClass}-6">
                            <button class="btn btn-block x13eucookies__btn x13eucookies__btn--accept-selected"
                                data-action="accept-selected">{$x13eucookies_appearance.accept_selected.text}</button>
                        </div>
                        <div class="col col-{$colClass}-6">
                            <button class="btn btn-block x13eucookies__btn x13eucookies__btn--accept-all"
                                data-action="accept-all">{$x13eucookies_appearance.accept_all.text}</button>
                        </div>
                    {/if}
                {else}
                    {if $x13eucookies_appearance.other.deny_button}
                        <div class="col col-{$colClass}-4">

                            <button class="x13eucookies__btn x13eucookies__btn--unstyle"
                                data-action="deny">{$x13eucookies_appearance.other.deny_text}</button>

                            <button class="x13eucookies__btn x13eucookies__btn--unstyle  x13eucookies-hidden"
                                data-action="back">{$x13eucookies_appearance.other.close_text}</button>
                        </div>
                        <div class="col col-{$colClass}-4">
                            <button class="x13eucookies__btn x13eucookies__btn--unstyle"
                                data-action="choose">{$x13eucookies_appearance.settings.text}
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="1 1 20 20">
                                    <path d="M9.4 17.65 8.35 16.6l4.6-4.6-4.6-4.6L9.4 6.35 15.05 12Z" />
                                </svg></button>

                            <button class="x13eucookies__btn x13eucookies__btn--unstyle  x13eucookies-hidden"
                                data-action="accept-selected">{$x13eucookies_appearance.accept_selected.text}</button>
                        </div>
                        <div class="col col-{$colClass}-4">
                            <button class="x13eucookies__btn x13eucookies__btn--unstyle"
                                data-action="accept-all">{$x13eucookies_appearance.accept_all.text}</button>
                        </div>
                    {else}
                        <div class="col col-{$colClass}-8">
                            <div class="row">
                                <div class="col col-{$colClass}-6">

                                    <button class="x13eucookies__btn x13eucookies__btn--unstyle x13eucookies-hidden"
                                        data-action="back">{$x13eucookies_appearance.other.close_text}</button>
                                </div>
                                <div class="col col-{$colClass}-6">
                                    <button class="x13eucookies__btn x13eucookies__btn--unstyle  x13eucookies-hidden"
                                        data-action="accept-selected">{$x13eucookies_appearance.accept_selected.text}</button>

                                </div>
                                <div class="col col-{$colClass}-12">
                                <button class="x13eucookies__btn x13eucookies__btn--unstyle"
                                    data-action="choose">{$x13eucookies_appearance.settings.text}
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="1 1 20 20">
                                        <path d="M9.4 17.65 8.35 16.6l4.6-4.6-4.6-4.6L9.4 6.35 15.05 12Z" />
                                    </svg></button>
                            </div>
                            </div>
                        </div>


                        <div class="col col-{$colClass}-4">
                            <button class="x13eucookies__btn x13eucookies__btn--unstyle"
                                data-action="accept-all">{$x13eucookies_appearance.accept_all.text}</button>
                        </div>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
</div>

{if $x13eucookies_config.layout == 'cloud' && $x13eucookies_config.display_icon_under_cloud}
    <div class="x13eucookies__icon-mockup">
        <svg id="Warstwa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 0 99.21 96.84">
            <defs>
                {literal}
                    <style>
                        .cls-1{fill:url(#Gradient_bez_nazwy_3);}.cls-1,.cls-2,.cls-3,.cls-4{fill-rule:evenodd;}.cls-2{fill:url(#Gradient_bez_nazwy_5);}.cls-3{fill:url(#Gradient_bez_nazwy_4);}.cls-4{fill:url(#Gradient_bez_nazwy_6);}
                    </style>
                {/literal}
                <linearGradient id="Gradient_bez_nazwy_3" x1="40.41" y1="49.61" x2="88.67" y2="49.61"
                    gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#c27423" />
                    <stop offset="1" stop-color="#995316" />
                </linearGradient>
                <linearGradient id="Gradient_bez_nazwy_4" x1="20.42" y1="47.23" x2="87.29" y2="47.23"
                    gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#fab757" />
                    <stop offset="1" stop-color="#dc800b" />
                </linearGradient>
                <linearGradient id="Gradient_bez_nazwy_5" x1="31.87" y1="38.92" x2="77.65" y2="38.92"
                    gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#fbc578" />
                    <stop offset="1" stop-color="#f69f28" />
                </linearGradient>
                <linearGradient id="Gradient_bez_nazwy_6" x1="-18.24" y1="96.1" x2="-14.16" y2="96.1"
                    gradientTransform="translate(-1368.57 286.66) rotate(90) scale(14.77 -14.77)"
                    gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#b9732c" />
                    <stop offset="1" stop-color="#9e5716" />
                </linearGradient>
            </defs>
            <path class="cls-1"
                d="m87.92,36c-1.11.21-2.25.34-3.42.34-10.27,0-18.59-8.32-18.59-18.59,0-1.13.14-2.22.33-3.3-4.84-1.35-8.43-5.68-8.62-10.91-3.35-.75-6.81-1.18-10.39-1.18C21.15,2.37,0,23.52,0,49.61s21.15,47.23,47.23,47.23,47.23-21.15,47.23-47.23c0-2.21-.2-4.38-.49-6.51-2.88-1.43-5.1-3.99-6.06-7.1Z" />
            <path class="cls-3"
                d="m87.96,36c-1.13.21-2.28.35-3.47.35-10.27,0-18.59-8.32-18.59-18.59,0-1.13.14-2.22.33-3.29-4.99-1.39-8.67-5.91-8.67-11.34,0-.94.14-1.84.34-2.71-1.95-.25-3.92-.41-5.94-.41C25.89,0,4.75,21.15,4.75,47.23s21.15,47.23,47.23,47.23,47.23-21.15,47.23-47.23c0-.97-.12-1.92-.18-2.88-5.24-.08-9.61-3.58-11.07-8.36Z" />
            <path class="cls-2"
                d="m84.5,36.35c-10.27,0-18.59-8.32-18.59-18.59,0-1.12.14-2.21.33-3.28-3.73-1.05-6.7-3.86-7.97-7.51-1.57-.23-3.16-.39-4.8-.39-17.86,0-32.34,14.48-32.34,32.34s14.48,32.34,32.34,32.34,32.34-14.48,32.34-32.34c0-.91-.08-1.8-.15-2.69-.39.02-.77.12-1.16.12Z" />
            <path class="cls-4"
                d="m56.61,76.06c1.79,0,3.24,1.45,3.24,3.24s-1.45,3.24-3.24,3.24-3.24-1.45-3.24-3.24,1.45-3.24,3.24-3.24Zm-21.63-15.24c4.21,0,7.62,3.42,7.62,7.62s-3.42,7.62-7.62,7.62-7.62-3.42-7.62-7.62,3.42-7.62,7.62-7.62Zm42.37,0c2.13,0,3.86,1.73,3.86,3.86s-1.73,3.86-3.86,3.86-3.86-1.73-3.86-3.86,1.73-3.86,3.86-3.86Zm-19.07-7.44c2.65,0,4.81,2.15,4.81,4.81s-2.15,4.81-4.81,4.81-4.81-2.15-4.81-4.81,2.15-4.81,4.81-4.81Zm18.36-15.24c4.21,0,7.62,3.42,7.62,7.62s-3.42,7.62-7.62,7.62-7.62-3.42-7.62-7.62,3.42-7.62,7.62-7.62Zm-55.8.79c2.13,0,3.86,1.73,3.86,3.86s-1.73,3.86-3.86,3.86-3.86-1.73-3.86-3.86,1.73-3.86,3.86-3.86Zm33.93-3.58c1.54,0,2.8,1.25,2.8,2.8s-1.25,2.8-2.8,2.8-2.8-1.25-2.8-2.8,1.25-2.8,2.8-2.8Zm-19.79-9.26c1.79,0,3.24,1.45,3.24,3.24s-1.45,3.24-3.24,3.24-3.24-1.45-3.24-3.24,1.45-3.24,3.24-3.24Zm16.25-14.1c3.89,0,7.05,3.16,7.05,7.05s-3.16,7.05-7.05,7.05-7.05-3.16-7.05-7.05,3.16-7.05,7.05-7.05Z" />
        </svg>
    </div>
{/if}