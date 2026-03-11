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
<!-- Pixel Plus JS Vars -->
<script>
    {foreach from=$js_vars key=key item=var}
    var {$key|escape:'htmlall':'UTF-8'} = '{$var nofilter}'; {* Can't be escaped as it may contain string literals or HTML *}
    {/foreach}
</script>