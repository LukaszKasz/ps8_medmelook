<div class="x13eucookies__infobar x13eucookies-hidden">
    <div class="x13eucookies__infobar-inner">
        <div class="x13eucookies__infobar-text x13eucookies__description x13eucookies__description--nomargin">
            {$x13eucookies_appearance.box.text nofilter}

            <p>
                <a href="#" data-action="show-box">
                    {$x13eucookies_appearance.settings.text}
                </a>
            </p>
        </div>
        <div class="x13eucookies__infobar-buttons">
            {if $x13eucookies_appearance.other.deny_button}
            <button class="btn x13eucookies__btn x13eucookies__btn--deny"
                data-action="deny">{$x13eucookies_appearance.other.deny_text}</button>
            {/if}
            <button class="btn x13eucookies__btn x13eucookies__btn--accept-all"
                data-action="accept-all">{$x13eucookies_appearance.accept_all.text}</button>
        </div>
    </div>
</div>