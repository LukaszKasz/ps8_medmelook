{*
 * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 *
*}

    <!-- Pixel Plus: Add missing OG microdata -->
    {foreach from=$og_data key=key item=value}
        {if $key == 'og:retailer_item_id'}
            {if $combi_enabled && $product_combi > 0}
                <meta property="og:product:item_group_id" content="{$prefix|escape:'htmlall':'UTF-8'}{$value|escape:'htmlall':'UTF-8'}" />
                <meta property="og:retailer_item_id" content="{$prefix|escape:'htmlall':'UTF-8'}{$value|escape:'htmlall':'UTF-8'}{$combi_prefix|escape:'htmlall':'UTF-8'}{$combi|escape:'htmlall':'UTF-8'}" />
            {else}
                <meta property="{$key|escape:'htmlall':'UTF-8'}" content="{$prefix|escape:'htmlall':'UTF-8'}{$value|escape:'htmlall':'UTF-8'}" />
            {/if}
        {elseif $key == 'og:image'}
            {foreach from=$og_data.$key item=image}
                <meta property="og:image" content="{$image|escape:'htmlall':'UTF-8'}"/>
            {/foreach}
        {else}
            <meta property="{$key|escape:'htmlall':'UTF-8'}" content="{$value|escape:'htmlall':'UTF-8'}"/>
        {/if}
    {/foreach}
    <!-- {* <meta property="product:custom_label_0" content="{$ip} || {$localization_info}" /> *} -->
    <!-- End Pixel Plus: Add missing OG microdata -->
