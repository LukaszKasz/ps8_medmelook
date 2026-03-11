<div class="header-top">
    <div id="desktop-header-container" class="container">
        <div class="row align-items-center">
            {if $iqitTheme.h_logo_position == 'left'}
                <div class="col col-auto col-header-left">
                    <div id="desktop_logo">
                        {renderLogo}
                    </div>
                    {hook h='displayHeaderLeft'}
                </div>
                <div class="col col-header-center">
                    {if isset($iqitTheme.h_txt) && $iqitTheme.h_txt}
                        <div class="header-custom-html">
                            {$iqitTheme.h_txt nofilter}
                        </div>
                    {/if}
                    {hook h='displayHeaderCenter'}
                </div>
            {else}
                <div class="col col-header-left">
                    {if isset($iqitTheme.h_txt) && $iqitTheme.h_txt}
                        <div class="header-custom-html">
                            {$iqitTheme.h_txt nofilter}
                        </div>
                    {/if}
                    {hook h='displayHeaderLeft'}
                </div>
                <div class="col col-header-center text-center">
                    <div id="desktop_logo">
                        {renderLogo}
                    </div>
                    {hook h='displayHeaderCenter'}
                </div>
            {/if}
            <div class="col {if $iqitTheme.h_logo_position == 'left'}col-auto{/if} col-header-right">
                <div class="row no-gutters justify-content-end">
                    {widget_block name="iqitsearch"}
                        {include 'module:iqitsearch/views/templates/hook/iqitsearch-btn.tpl'}
                    {/widget_block}

                    {block name='displayWishlistHook'}
                        <div class="displayWishlistHook" >
                            {hook h='displayWishlistHook'}
                        </div>
                    {/block}

                    {hook h="litespeedEsiBegin" m="ps_customersignin" field="widget_block" tpl="module:ps_customersignin/ps_customersignin-btn.tpl"}
                    {widget_block name="ps_customersignin"}
                        {include 'module:ps_customersignin/ps_customersignin-btn.tpl'}
                    {/widget_block}
                    {hook h="litespeedEsiEnd"}

                    {hook h='displayHeaderButtons'}

                    {if !$configuration.is_catalog}
                        {hook h="litespeedEsiBegin" m="ps_shoppingcart" field="widget_block" tpl="module:ps_shoppingcart/ps_shoppingcart-btn.tpl"}
                        {widget_block name="ps_shoppingcart"}
                            {include 'module:ps_shoppingcart/ps_shoppingcart-btn.tpl'}
                        {/widget_block}
                        {hook h="litespeedEsiEnd"}
                    {/if}
                </div>
                {hook h='displayHeaderRight'}
            </div>
            <div class="col-12">
                <div class="row">
                    {hook h='displayTop'}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container iqit-megamenu-container">{hook h='displayMainMenu'}</div>
{hook h='displayNavFullWidth'}
