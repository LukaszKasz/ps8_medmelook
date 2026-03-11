{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="apaczka-config" class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i>
        {l s='Settings' mod='apaczka'}
    </div>
    <div class="moduleconfig-content">
        <form method="post" enctype="multipart/form-data" action="">
            <div class="row form-group">
                <div class="col-xs-12">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input 
                            type="text" 
                            id="APACZKA_MAPS_API_KEY" 
                            name="APACZKA_MAPS_API_KEY" 
                            class="custom-control-input" 
                            value="{$APACZKA_MAPS_API_KEY|escape:'htmlall':'UTF-8'}" 
                            size="100"
                            required="required"
                        >
                        <label class="custom-control-label required" for="APACZKA_MAPS_API_KEY">
                            {l s='Map API key (you will find it on your account on apaczka.pl)' mod='apaczka'}
                        </label>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-xs-12">
                    <h3 class="section-title">
                        {l s='Carriers service points configuration' mod='apaczka'}
                    </h3>
                </div>
            </div>      
            <table id="apaczka-carriers" width="100%"> 
                <thead>
                    <tr>
                        <th>{l s='Carrier' mod='apaczka'}</th>
                        <th class="col-apaczka-name">{l s='Apaczka service' mod='apaczka'}</th>
                        <th class="col-cod">{l s='Cash on delivery' mod='apaczka'}</th>
                        <th class="col-points">{l s='Self-pickup' mod='apaczka'}</th>
                    </tr>
                </thead>
                <tbody>          
                    {foreach from=$carriers item='carrier' name='carriers'}   
                       <tr>
                            <td class="col-name">{$carrier.name|escape:'htmlall':'UTF-8'}</td>
                            <td class="col-apaczka-name">   
                                <select name="APACZKA_CARRIERS[{$carrier.id_reference|escape:'htmlall':'UTF-8'}][apaczkaName]">
                                    <option value='-1'>Brak</option>
                                    {foreach from=$apaczkaCarriers item='apaczkaCarrier'}
                                        <option value="{$apaczkaCarrier|escape:'htmlall':'UTF-8'}" 
                                            {if $APACZKA_CARRIERS.{$carrier.id_reference|escape:'htmlall':'UTF-8'}.apaczkaName==$apaczkaCarrier} 
                                                selected
                                            {/if}>{$apaczkaCarrier|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td class="col-cod">
                                <input 
                                    type="checkbox" 
                                    class="custom-control-input" 
                                    name="APACZKA_CARRIERS[{$carrier.id_reference|escape:'htmlall':'UTF-8'}][cod]" 
                                    value="1" 
                                    {if $APACZKA_CARRIERS[$carrier.id_reference]['cod']==1} checked{/if}
                                >
                            </td>
                            <td class="col-points">
                                <input 
                                    type="checkbox" 
                                    class="custom-control-input" 
                                    name="APACZKA_CARRIERS[{$carrier.id_reference|escape:'htmlall':'UTF-8'}][points]" 
                                    value="1" 
                                    {if $APACZKA_CARRIERS[$carrier.id_reference]['points']==1} checked{/if}
                                >
                            </td>
                        </tr>
                    {/foreach} 
                </tbody>
            </table>
            <div class="panel-footer">
                <button 
                    type="submit" 
                    value="1" 
                    id="module_form_submit_btn" 
                    name="submitApaczkaModule" 
                    class="btn btn-default pull-right"
                >
                    <i class="process-icon-save"></i>{l s='Save' mod='apaczka'}
                </button>
            </div>
        </form>
    </div>
</div>
