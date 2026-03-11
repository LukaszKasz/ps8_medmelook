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

{foreach from=$missing_micro key=type item=data}
    <h4 class="text-uppercase"><strong>{$type|escape:'htmlall':'UTF-8'}</strong></h4>
    <ul>
    {foreach from=$data key=field item=value}
        <li>{$field|escape:'htmlall':'UTF-8'}</li>
    {/foreach}
    </ul>
{/foreach}