<style>
    #x13eucookies.x13eucookies__backdrop,
    #x13eucookies .x13eucookies-mask::after {ldelim}
    background: {$x13eucookies_appearance.box.backdrop_color};
    {rdelim}

    {if $x13eucookies_config.block_website && $x13eucookies_appearance.box.backdrop_show}
        body.x13eucookies-block {ldelim}
        overflow: hidden !important;

        {rdelim}
    {else}
        #x13eucookies.x13eucookies__backdrop {
            pointer-events: none;
        }

        #x13eucookies.x13eucookies__backdrop>* {
            pointer-events: auto;
        }

    {/if}

    {if $x13eucookies_config.rounded}
        #x13eucookies .x13eucookies__box:not(.x13eucookies__box--cloud-full),
        #x13eucookies .x13eucookies__btn,
        #x13eucookies .x13eucookies__cloud {ldelim}
        border-radius: 8px !important;
        {rdelim}
    {/if}

    #x13eucookies .x13eucookies__btn--accept-all {ldelim}
    color: {$x13eucookies_appearance.accept_all.color};
    background-color: {$x13eucookies_appearance.accept_all.background};
    border: 1px solid {$x13eucookies_appearance.accept_all.border_color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--accept-all svg * {ldelim}
    fill: {$x13eucookies_appearance.accept_all.color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--accept-all:active,
    #x13eucookies .x13eucookies__btn--accept-all:hover {ldelim}
    color: {$x13eucookies_appearance.accept_all.hover_color};
    background-color: {$x13eucookies_appearance.accept_all.hover_background};
    border: 1px solid {$x13eucookies_appearance.accept_all.hover_border_color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--accept-all:active svg *,
    #x13eucookies .x13eucookies__btn--accept-all:hover svg * {ldelim}
    fill: {$x13eucookies_appearance.accept_all.hover_color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--accept-selected {ldelim}
    color: {$x13eucookies_appearance.accept_selected.color};
    background-color: {$x13eucookies_appearance.accept_selected.background};
    border: 1px solid {$x13eucookies_appearance.accept_selected.border_color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--accept-selected svg * {ldelim}
    fill: {$x13eucookies_appearance.accept_selected.color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--accept-selected:active,
    #x13eucookies .x13eucookies__btn--accept-selected:hover {ldelim}
    color: {$x13eucookies_appearance.accept_selected.hover_color};
    background-color: {$x13eucookies_appearance.accept_selected.hover_background};
    border: 1px solid {$x13eucookies_appearance.accept_selected.hover_border_color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--accept-selected:active svg *,
    #x13eucookies .x13eucookies__btn--accept-selected:hover svg * {ldelim}
    fill: {$x13eucookies_appearance.accept_selected.hover_color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--deny,
    #x13eucookies .x13eucookies__btn--settings {ldelim}
    color: {$x13eucookies_appearance.settings.color};
    background-color: {$x13eucookies_appearance.settings.background};
    border: 1px solid {$x13eucookies_appearance.settings.border_color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--deny svg *,
    #x13eucookies .x13eucookies__btn--settings svg * {ldelim}
    fill: {$x13eucookies_appearance.settings.color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--deny:active,
    #x13eucookies .x13eucookies__btn--settings:active,
    #x13eucookies .x13eucookies__btn--deny:hover,
    #x13eucookies .x13eucookies__btn--settings:hover {ldelim}
    color: {$x13eucookies_appearance.settings.hover_color};
    background-color: {$x13eucookies_appearance.settings.hover_background};
    border: 1px solid {$x13eucookies_appearance.settings.hover_border_color};
    {rdelim}

    #x13eucookies .x13eucookies__btn--deny:active svg *,
    #x13eucookies .x13eucookies__btn--settings:active svg *,
    #x13eucookies .x13eucookies__btn--deny:hover svg *,
    #x13eucookies .x13eucookies__btn--settings:hover svg * {ldelim}
    fill: {$x13eucookies_appearance.settings.hover_color};
    {rdelim}

    #x13eucookies .x13eucookies__description,
    #x13eucookies .x13eucookies__description p,
    #x13eucookies .x13eucookies__description .x13eucookies__link,
    #x13eucookies .x13eucookies__table,
    #x13eucookies .x13eucookies__table * {ldelim}
    line-height: 1.5em;
    font-size: {$x13eucookies_appearance.box.font_size}px !important;
    {rdelim}

    #x13eucookies .x13eucookies__nav-link.active {
        border-bottom-color: {$x13eucookies_appearance.cookies.color_tabs_underlined} !important;
    }

    {if $x13eucookies_appearance.cookies.switch_invert_color}
        #x13eucookies .x13eucookies__toggle-item {ldelim}
        background-color: #fff;
        border: 1px solid {$x13eucookies_appearance.cookies.switch_background_color};
        {rdelim}

        #x13eucookies .x13eucookies__toggle-item .x13eucookies__check {ldelim}
        background-color: {$x13eucookies_appearance.cookies.switch_background_color};
        {rdelim}

        #x13eucookies .x13eucookies__toggle-item .x13eucookies__check svg * {ldelim}
        fill: #fff;
        {rdelim}

        #x13eucookies .x13eucookies__toggle input:checked+.x13eucookies__toggle-item .x13eucookies__check {ldelim}
        background-color: #fff;
        {rdelim}

        #x13eucookies .x13eucookies__toggle input:checked+.x13eucookies__toggle-item .x13eucookies__check svg * {ldelim}
        fill: {$x13eucookies_appearance.cookies.switch_active_background_color};
        {rdelim}

        #x13eucookies .x13eucookies__toggle input:checked+.x13eucookies__toggle-item {ldelim}
        background-color: {$x13eucookies_appearance.cookies.switch_active_background_color};
        border: 1px solid {$x13eucookies_appearance.cookies.switch_active_background_color};
        {rdelim}
    {else}
        #x13eucookies .x13eucookies__toggle-item {ldelim}
        background-color: {$x13eucookies_appearance.cookies.switch_background_color};
        border: 1px solid {$x13eucookies_appearance.cookies.switch_background_color};
        {rdelim}

        #x13eucookies .x13eucookies__toggle-item .x13eucookies__check,
        #x13eucookies .x13eucookies__toggle input:checked+.x13eucookies__toggle-item .x13eucookies__check {ldelim}
        background-color: #fff;
        {rdelim}

        #x13eucookies .x13eucookies__toggle-item .x13eucookies__check svg *,
        #x13eucookies .x13eucookies__toggle input:checked+.x13eucookies__toggle-item .x13eucookies__check svg * {ldelim}
        fill: #000;
        {rdelim}

        #x13eucookies .x13eucookies__toggle input:checked+.x13eucookies__toggle-item {ldelim}
        background-color: {$x13eucookies_appearance.cookies.switch_active_background_color};
        border: 1px solid {$x13eucookies_appearance.cookies.switch_active_background_color};
        {rdelim}
    {/if}

    {if $x13eucookies_config.widget_hide_on_mobile}
        @media (max-width: 767.98px) {ldelim}
        #x13eucookies-icon {ldelim}
        display: none;
        {rdelim}
        {rdelim}
    {/if}

    {$x13eucookies_config.extra_css nofilter}
</style>