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

<!-- Pixel Plus: Add missing microdata -->
{if isset($schema.product) && $schema.product}
    <div itemscope itemtype="http://schema.org/Product">
{/if}
    {foreach from=$micro_data.product key=key item=value}
        {if $key == 'image'}
            {foreach from=$value item=$image}
                {if $image != ''}<link itemprop="image" href="{$image|escape:'htmlall':'UTF-8'}">{/if}
            {/foreach}
        {elseif $key == 'brand'}
            <div itemprop="brand" itemtype="https://schema.org/Brand" itemscope>
                <meta itemprop="name" content="{$value|escape:'htmlall':'UTF-8'}" />
            </div>
        {else}
            <meta itemprop="{$key|escape:'htmlall':'UTF-8'}" content="{if $key == 'productID'}{$prefix|escape:'htmlall':'UTF-8'}{/if}{$value|escape:'htmlall':'UTF-8'}">
        {/if}
    {/foreach}
        {if isset($schema.offers) && $schema.offers}
            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
        {/if}

            {if isset($micro_data.offers)}
            {foreach from=$micro_data.offers key=key item=value}
                {if $key == 'condition'}
                    <link itemprop="itemCondition" href="http://schema.org/{if $value == 'new'}NewCondition{elseif $value == 'used'}UsedCondition{else}RefubrishedCondition{/if}">
                {elseif $key == 'availability'}
                    <link itemprop="itemAvailability" href="http://schema.org/{if $value == 'in stock'}InStock{else}OutOfStock{/if}">
                {else}
                    <meta itemprop="{$key|escape:'htmlall':'UTF-8'}" content="{$value|escape:'htmlall':'UTF-8'}">
                {/if}
            {/foreach}
            {/if}
        {if isset($schema.offers) && $schema.offers}
            </div>
        {/if}
{if isset($schema.product) && $schema.product}
    </div>
{/if}
<!-- End Pixel Plus: Add missing microdata -->
