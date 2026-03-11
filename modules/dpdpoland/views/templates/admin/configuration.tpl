{** 2019 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 * @author    DPD Polska Sp. z o.o.
 * @copyright 2019 DPD Polska Sp. z o.o.
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of DPD Polska Sp. z o.o.
 *}
<form id="configuration_form" class="defaultForm" action="{$saveAction|escape:'htmlall':'UTF-8'}&menu=configuration"
      method="post" enctype="multipart/form-data">
    <fieldset id="credentials">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='DPD credentials' mod='dpdpoland'}
        </legend>

        <label>
            {l s='Login:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="login_input" type="text" name="{DpdPolandConfiguration::LOGIN|escape:'htmlall':'UTF-8'}"
                   value="{DpdPoland::getInputValue(DpdPolandConfiguration::LOGIN, $settings->login)|escape:'htmlall':'UTF-8'}"/>
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Password:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="password" type="password" name="{DpdPolandConfiguration::PASSWORD|escape:'htmlall':'UTF-8'}" value=""/>
            <sup>*</sup>
        </div>
        <div class="clear"></div>
        <label>
            {l s='Customer FID:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input type="text" name="{DpdPolandConfiguration::CUSTOMER_FID|escape:'htmlall':'UTF-8'}"
                   value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_FID, $settings->customer_fid)|escape:'htmlall':'UTF-8'}"/>
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <div class="separation"></div>

        <div id="error_message" class="error hidden-element"></div>
        <div id="success_message" class="conf hidden-element"></div>

        <div class="float-left">
            <label>
                {l s='DPD client number:' mod='dpdpoland'}
            </label>
            <div class="margin-form">
                <input id="client_number" type="text" name="" value=""/>
            </div>
        </div>

        <div class="float-left">
            <label>
                {l s='Client name:' mod='dpdpoland'}
            </label>
            <div class="margin-form">
                <input id="client_name" type="text" name="" value=""/>
            </div>
        </div>

        <div class="add-client-number-button-container">
            <input id="addClientNumber" type="button" class="button" value="{l s='Add' mod='dpdpoland'}"/>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Master FID:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <div id="client_numbers_table_container">
                {include file=$smarty.const._DPDPOLAND_TPL_DIR_|cat:'admin/payer_numbers_table.tpl'}
            </div>
        </div>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>

        <div class="small">
            <sup>*</sup> {l s='Required field' mod='dpdpoland'}
        </div>
    </fieldset>

    <br/>

    <fieldset id="customer_data">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='General WS parameters' mod='dpdpoland'}
        </legend>

        <label>
            {l s='Customer company name:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input type="text" name="{DpdPolandConfiguration::CUSTOMER_COMPANY|escape:'htmlall':'UTF-8'}"
                   value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_COMPANY, $settings->customer_company)|escape:'htmlall':'UTF-8'}"/>
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Customer name and surname:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input type="text" name="{DpdPolandConfiguration::CUSTOMER_NAME|escape:'htmlall':'UTF-8'}"
                   value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_NAME, $settings->customer_name)|escape:'htmlall':'UTF-8'}"/>
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Customer tel. No.:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input type="text" name="{DpdPolandConfiguration::CUSTOMER_PHONE|escape:'htmlall':'UTF-8'}"
                   value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_PHONE, $settings->customer_phone)|escape:'htmlall':'UTF-8'}"/>
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>

        <div class="small">
            <sup>*</sup> {l s='Required field' mod='dpdpoland'}
        </div>
    </fieldset>

    <br/>

    <fieldset id="shipping">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='Active shiping services' mod='dpdpoland'}
        </legend>

        <p class="clear list info hint visible-element relative">
            {l s='Please note that after module installation carriers are not created.' mod='dpdpoland'}
        </p>

        <p class="clear hint list info visible-element relative">
            {l s='In the Payment preferences, please select the carriers for which the given payment module will be active.' mod='dpdpoland'}
        </p>
        <label for="dpd_standard">
            {l s='DPD domestic shipment - Standard:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpd_standard" type="checkbox"
                   name="{DpdPolandConfiguration::CARRIER_STANDARD|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::CARRIER_STANDARD, $settings->carrier_standard)}checked="checked"{/if}
                   value="1"/>
            <p class="preference_description">
                {l s='DPD domestic shipment - Standard:' mod='dpdpoland'}
            </p>
        </div>
        <div class="clear"></div>

        <label for="dpd_standard_cod">
            {l s='DPD domestic shipment - Standard with COD:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpd_standard_cod"
                   {if !DPDpoland::CODMethodIsAvailable()}onclick="alert(no_COD_methods_message); return false;"{/if}
                   type="checkbox"
                   name="{DpdPolandConfiguration::CARRIER_STANDARD_COD}" {if DpdPoland::getInputValue(DpdPolandConfiguration::CARRIER_STANDARD_COD, $settings->carrier_standard_cod) && DPDpoland::CODMethodIsAvailable()}checked="checked"{/if}
                   value="1"/>
            <p class="preference_description">
                {l s='DPD domestic shipment - Standard with COD' mod='dpdpoland'}
            </p>
        </div>
        <div class="clear"></div>

        <label for="dpd_classic">
            {l s='DPD international shipment (DPD Classic):' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpd_classic" type="checkbox"
                   name="{DpdPolandConfiguration::CARRIER_CLASSIC|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::CARRIER_CLASSIC, $settings->carrier_classic)}checked="checked"{/if}
                   value="1"/>
            <p class="preference_description">
                {l s='DPD international shipment (DPD Classic)' mod='dpdpoland'}
            </p>
        </div>
        <div class="clear"></div>

        <label for="dpd_pudo">
            {l s='DPD Poland Reception Point Pickup' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpd_pudo" type="checkbox" name="{DpdPolandConfiguration::CARRIER_PUDO|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::CARRIER_PUDO, $settings->carrier_pudo)}checked="checked"{/if}
                   value="1"/>
            <p class="preference_description">
                {l s='DPD Poland Reception Point Pickup' mod='dpdpoland'}
            </p>
        </div>
        <div class="clear"></div>

        <label for="dpd_pudo_cod">
            {l s='DPD Poland Reception Point Pickup with COD' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpd_pudo_cod" type="checkbox"
                   name="{DpdPolandConfiguration::CARRIER_PUDO_COD|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::CARRIER_PUDO_COD, $settings->carrier_pudo_cod)}checked="checked"{/if}
                   value="1"/>
            <p class="preference_description">
                {l s='DPD Poland Reception Point Pickup with COD' mod='dpdpoland'}
            </p>
        </div>
        <div class="clear"></div>

        {if DPDpoland::CODMethodIsAvailable()}
            <label for="cod_payment_methods">
                {l s='COD payment methods' mod='dpdpoland'}
            </label>
            <div class="margin-form">
                <div class="payment_modules_container">
                    <table>
                        {section name=iii loop=$payment_modules}
                            <tr>
                                <td align="right">
                                    <label>{$payment_modules[iii].displayName|escape:'htmlall':'UTF-8'}</label>
                                </td>
                                <td>
                                    <input type="checkbox"
                                           name="{DpdPolandConfiguration::COD_MODULE_PREFIX|escape:'htmlall':'UTF-8'}{$payment_modules[iii].name|escape:'htmlall':'UTF-8'}"
                                           value="1"
                                           {if DpdPoland::getConfig(DpdPolandConfiguration::COD_MODULE_PREFIX|escape:'htmlall':'UTF-8'|cat:$payment_modules[iii].name|escape:'htmlall':'UTF-8')}checked="checked"{/if} />
                                </td>
                            </tr>
                        {/section}
                    </table>
                </div>
            </div>
        {/if}
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>
    </fieldset>

    <br/>

    <fieldset id="active_zones">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='Active zones' mod='dpdpoland'}
        </legend>

        <div class="margin-form">
            <table cellspacing="0" cellpadding="5" id="zones_table">
                <thead>
                <th>

                </th>
                <th>
                    {l s='DPD domestic shipment - Standard' mod='dpdpoland'}
                </th>
                <th>

                </th>
                <th>
                    {l s='DPD domestic shipment - Standard with COD' mod='dpdpoland'}
                </th>
                <th>

                </th>
                <th>
                    {l s='DPD international shipment (DPD Classic)' mod='dpdpoland'}
                </th>
                <th>
                    {l s='DPD Poland Reception Point Pickup' mod='dpdpoland'}
                </th>
                <th>
                    {l s='DPD Poland Reception Point Pickup with COD' mod='dpdpoland'}
                </th>
                </thead>
                <tbody>
                {section name=ii loop=$zones}
                    <tr class="fees{if $smarty.section.ii.index %2 == 0} alt_row{/if}">
                        <td class="border_top border_bottom border_bold">
                            {$zones[ii].name|escape:'htmlall':'UTF-8'}
                        </td>
                        <td class="center">
                            <input type="checkbox"{if in_array($zones[ii].id_zone, $carrier_zones['standard'])} checked="checked"{/if}
                                   name="standard_{$zones[ii].id_zone|intval}" class="form-control domestic_zone"
                                   value="1"/>
                        </td>
                        <td>

                        </td>
                        <td class="center">
                            <input type="checkbox"{if in_array($zones[ii].id_zone, $carrier_zones['standard_cod'])} checked="checked"{/if}
                                   name="standard_cod_{$zones[ii].id_zone|intval}"
                                   class="form-control domestic_cod_zone" value="1"/>
                        </td>
                        <td>

                        </td>
                        <td class="center">
                            <input type="checkbox"{if in_array($zones[ii].id_zone, $carrier_zones['classic'])} checked="checked"{/if}
                                   name="classic_{$zones[ii].id_zone|intval}" class="form-control classic_zone"
                                   value="1">
                        </td>
                        <td>

                        </td>
                        <td class="center">
                            <input type="checkbox"{if in_array($zones[ii].id_zone, $carrier_zones['pudo'])} checked="checked"{/if}
                                   name="pudo_{$zones[ii].id_zone|intval}" class="form-control pudo_zone" value="1">
                        </td>
                        <td class="center">
                            <input type="checkbox"{if in_array($zones[ii].id_zone, $carrier_zones['pudo_cod'])} checked="checked"{/if}
                                   name="pudo_cod_{$zones[ii].id_zone|intval}" class="form-control pudo_cod_zone"
                                   value="1">
                        </td>
                    </tr>
                {/section}
                </tbody>
            </table>
        </div>
        <div class="clear"></div>

        <p class="clear list info hint visible-element relative">
            {l s='Please define price ranges for each carrier in carrier configuration page or import CSV file with price ranges.' mod='dpdpoland'}
        </p>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>
    </fieldset>

    <br/>

    <fieldset id="price_calculation">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='Price calculation' mod='dpdpoland'}
        </legend>

        <label>
            {l s='Shipping price calculation method:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="price_calculation_csv" type="radio"
                   name="{DpdPolandConfiguration::PRICE_CALCULATION_TYPE|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::PRICE_CALCULATION_TYPE, $settings->price_calculation_type) == DpdPolandConfiguration::PRICE_CALCULATION_CSV}checked="checked"{/if}
                   value="{DpdPolandConfiguration::PRICE_CALCULATION_CSV}"/>
            <label class="t" for="price_calculation_csv">
                {l s='CSV rules' mod='dpdpoland'}
            </label>
            <input id="price_calculation_prestashop" type="radio"
                   name="{DpdPolandConfiguration::PRICE_CALCULATION_TYPE|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::PRICE_CALCULATION_TYPE, $settings->price_calculation_type) == DpdPolandConfiguration::PRICE_CALCULATION_PRESTASHOP}checked="checked"{/if}
                   value="{DpdPolandConfiguration::PRICE_CALCULATION_PRESTASHOP}"/>
            <label class="t" for="price_calculation_prestashop">
                {l s='PrestaShop shipping locations rules' mod='dpdpoland'}
            </label>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>
    </fieldset>

    <br/>

    <fieldset id="weight_measurement">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='Weight measurement units conversation' mod='dpdpoland'}
        </legend>

        <label>
            {l s='System default weight units:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            {DpdPoland::getConfig('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}
        </div>
        <div class="clear"></div>

        <label>
            {l s='DPD weight units:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            {$smarty.const._DPDPOLAND_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
        </div>
        <div class="clear"></div>

        <label>
            {l s='Conversation rate:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="weight_conversion_input" type="text"
                   name="{DpdPolandConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8'}"
                   value="{DpdPoland::getInputValue(DpdPolandConfiguration::WEIGHT_CONVERSATION_RATE, $settings->weight_conversation_rate)|escape:'htmlall':'UTF-8'}"/>
            <sup>*</sup>
            1 {DpdPoland::getConfig('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'} = <span
                    id="dpd_weight_unit">{DpdPoland::getInputValue(DpdPolandConfiguration::WEIGHT_CONVERSATION_RATE, $settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}</span> {$smarty.const._DPDPOLAND_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
            <p class="preference_description">
                {l s='Conversation rate from system to DPD weight units. If your system uses the same units as DPD please fill 1.' mod='dpdpoland'}
            </p>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>

        <div class="small">
            <sup>*</sup> {l s='Required field' mod='dpdpoland'}
        </div>
    </fieldset>

    <br/>

    <fieldset id="dimension_measurement">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='Dimension measurement units conversation' mod='dpdpoland'}
        </legend>

        <label>
            {l s='System default dimension units:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            {DpdPoland::getConfig('PS_DIMENSION_UNIT')|escape:'htmlall':'UTF-8'}
        </div>
        <div class="clear"></div>

        <label>
            {l s='DPD dimension units:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            {$smarty.const._DPDPOLAND_DEFAULT_DIMENSION_UNIT_|escape:'htmlall':'UTF-8'}
        </div>
        <div class="clear"></div>

        <label>
            {l s='Conversation rate:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="weight_conversion_input" type="text"
                   name="{DpdPolandConfiguration::DIMENSION_CONVERSATION_RATE|escape:'htmlall':'UTF-8'}"
                   value="{DpdPoland::getInputValue(DpdPolandConfiguration::DIMENSION_CONVERSATION_RATE, $settings->dimension_conversation_rate)|escape:'htmlall':'UTF-8'}"/>
            <sup>*</sup>
            1 {DpdPoland::getConfig('PS_DIMENSION_UNIT')|escape:'htmlall':'UTF-8'} = <span
                    id="dpd_weight_unit">{DpdPoland::getInputValue(DpdPolandConfiguration::DIMENSION_CONVERSATION_RATE, $settings->dimension_conversation_rate)|escape:'htmlall':'UTF-8'}</span> {$smarty.const._DPDPOLAND_DEFAULT_DIMENSION_UNIT_|escape:'htmlall':'UTF-8'}
            <p class="preference_description">
                {l s='Conversation rate from system to DPD dimension units. If your system uses the same units as DPD please fill 1.' mod='dpdpoland'}
            </p>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>

        <div class="small">
            <sup>*</sup> {l s='Required field' mod='dpdpoland'}
        </div>
    </fieldset>

    <br/>

    <fieldset id="ws_url">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='Web Services URL' mod='dpdpoland'}
        </legend>

        <label>
            {l s='Web Services URL:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <select id="ws_url_input" name="{DpdPolandConfiguration::WS_URL|escape:'htmlall':'UTF-8'}">
                <option value="{DpdPolandConfiguration::WSDL_URL_LIVE|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::WS_URL, $settings->ws_url) == DpdPolandConfiguration::WSDL_URL_LIVE} selected="selected"{/if}>{l s='Access to the production system' mod='dpdpoland'}</option>
                <option value="{DpdPolandConfiguration::WSDL_URL_DEMO|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::WS_URL, $settings->ws_url) == DpdPolandConfiguration::WSDL_URL_DEMO} selected="selected"{/if}>{l s='Access to the demonstration system' mod='dpdpoland'}</option>
            </select>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>

        <div class="small">
            <sup>*</sup> {l s='Required field' mod='dpdpoland'}
        </div>
    </fieldset>

    <br/>

    <fieldset id="additional_settings">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='Additional settings' mod='dpdpoland'}
        </legend>

        <label for="declared_value">
            {l s='Declared value:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="declared_value" type="checkbox"
                   name="{DpdPolandConfiguration::DECLARED_VALUE|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::DECLARED_VALUE, $settings->declared_value)}checked="checked"{/if}
                   value="1"/>
        </div>
        <div class="clear"></div>

        <label for="cud">
            {l s='Return parcel:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="cud" type="checkbox" name="{DpdPolandConfiguration::CUD|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::CUD, $settings->cud)}checked="checked"{/if}
                   value="1"/>
        </div>
        <div class="clear"></div>

        <label for="rod">
            {l s='Return documents:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="rod" type="checkbox" name="{DpdPolandConfiguration::ROD|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::ROD, $settings->rod)}checked="checked"{/if}
                   value="1"/>
        </div>
        <div class="clear"></div>

        <label for="dpde">
            {l s='DPD Express:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpde" type="checkbox" name="{DpdPolandConfiguration::DPDE|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::DPDE, $settings->dpde)}checked="checked"{/if}
                   value="1"/>
        </div>
        <div class="clear"></div>

        <label for="dpdnd">
            {l s='DPD Next day:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpdnd" type="checkbox" name="{DpdPolandConfiguration::DPDND|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::DPDND, $settings->dpdnd)}checked="checked"{/if}
                   value="1"/>
        </div>
        <div class="clear"></div>

        <label for="dpdtoday">
            {l s='DPD Today:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpdtoday" type="checkbox" name="{DpdPolandConfiguration::DPDTODAY|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::DPDTODAY, $settings->dpdtoday)}checked="checked"{/if}
                   value="1"/>
        </div>
        <div class="clear"></div>

        <label for="dpdsaturday">
            {l s='DPD Saturday:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpdsaturday" type="checkbox"
                   name="{DpdPolandConfiguration::DPDSATURDAY|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::DPDSATURDAY, $settings->dpdsaturday)}checked="checked"{/if}
                   value="1"/>
        </div>
        <div class="clear"></div>

        <label for="dpdfood">
            {l s='DPD Food:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpdfood" type="checkbox" name="{DpdPolandConfiguration::DPDFOOD|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::DPDFOOD, $settings->dpdfood)}checked="checked"{/if}
                   value="1"/>
        </div>
        <div class="clear"></div>

        <label for="dpdfood">
            {l s='DPD ADR:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="dpdfood" type="checkbox" name="{DpdPolandConfiguration::DPDLQ|escape:'htmlall':'UTF-8'}"
                   {if DpdPoland::getInputValue(DpdPolandConfiguration::DPDLQ, $settings->dpdlq)}checked="checked"{/if}
                   value="1"/>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Ref1' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <span class="radio">
                <input id="additional_ref1_none" type="radio"
                       name="{DpdPolandConfiguration::ADDITIONAL_REF1|escape:'htmlall':'UTF-8'}"
                       {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF1, $settings->ref1) == DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}"/>
                <label class="t" for="additional_ref1_none">
                    {l s='None' mod='dpdpoland'}
                </label>
            </span>
            <span class="radio">
                <input id="additional_ref1_dynamic" type="radio"
                       name="{DpdPolandConfiguration::ADDITIONAL_REF1|escape:'htmlall':'UTF-8'}"
                       {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF1, $settings->ref1) == DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}"/>
                <label class="t" for="additional_ref1_dynamic">
                    {l s='Dynamic' mod='dpdpoland'}
                </label>
                <span class="hidable">
                    <select name="{DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC|escape:'htmlall':'UTF-8'}">
                        <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_ID} selected="selected"{/if}>{l s='Order ID' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE} selected="selected"{/if}>{l s='Order reference' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_INVOICE_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_INVOICE_ID} selected="selected"{/if}>{l s='Invoice ID' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS} selected="selected"{/if}>{l s='Other field in shipping address' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_PRODUCT_NAME|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_PRODUCT_NAME} selected="selected"{/if}>{l s='Product name' mod='dpdpoland'}</option>
                    </select>
                </span>
            </span>
            <span class="radio">
                <input id="additional_ref1_static" type="radio"
                       name="{DpdPolandConfiguration::ADDITIONAL_REF1|escape:'htmlall':'UTF-8'}"
                       {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF1, $settings->ref1) == DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}"/>
                <label class="t" for="additional_ref1_static">
                    {l s='Static' mod='dpdpoland'}
                </label>
                <span class="hidable">
                    <input type="text" name="{DpdPolandConfiguration::CUSTOMER_DATA_STATIC|escape:'htmlall':'UTF-8'}"
                           value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_STATIC, $settings->customer_data_static)|escape:'htmlall':'UTF-8'}"/>
                </span>
            </span>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Ref2' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <span class="radio">
                <input id="additional_ref2_none" type="radio"
                       name="{DpdPolandConfiguration::ADDITIONAL_REF2|escape:'htmlall':'UTF-8'}"
                       {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF2, $settings->ref2) == DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}"/>
                <label class="t" for="additional_ref2_none">
                    {l s='None' mod='dpdpoland'}
                </label>
            </span>
            <span class="radio">
                <input id="additional_ref2_dynamic" type="radio"
                       name="{DpdPolandConfiguration::ADDITIONAL_REF2|escape:'htmlall':'UTF-8'}"
                       {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF2, $settings->ref2) == DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}"/>
                <label class="t" for="additional_ref2_dynamic">
                    {l s='Dynamic' mod='dpdpoland'}
                </label>
                <span class="hidable">
                    <select name="{DpdPolandConfiguration::REF2_DYNAMIC|escape:'htmlall':'UTF-8'}">
                        <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF2_DYNAMIC, $settings->ref2_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_ID} selected="selected"{/if}>{l s='Order ID' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF2_DYNAMIC, $settings->ref2_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE} selected="selected"{/if}>{l s='Order reference' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_INVOICE_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF2_DYNAMIC, $settings->ref2_dynamic) == DpdPolandConfiguration::DYNAMIC_INVOICE_ID} selected="selected"{/if}>{l s='Invoice ID' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF2_DYNAMIC, $settings->ref2_dynamic) == DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS} selected="selected"{/if}>{l s='Other field in shipping address' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_PRODUCT_NAME|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::REF2_DYNAMIC, $settings->ref2_dynamic) == DpdPolandConfiguration::DYNAMIC_PRODUCT_NAME} selected="selected"{/if}>{l s='Product name' mod='dpdpoland'}</option>
                    </select>
                </span>
            </span>
            <span class="radio">
                <input id="additional_ref2_static" type="radio"
                       name="{DpdPolandConfiguration::ADDITIONAL_REF2|escape:'htmlall':'UTF-8'}"
                       {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_REF2, $settings->ref2) == DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}"/>
                <label class="t" for="additional_ref2_static">
                    {l s='Static' mod='dpdpoland'}
                </label>
                <span class="hidable">
                    <input type="text" name="{DpdPolandConfiguration::REF2_STATIC|escape:'htmlall':'UTF-8'}"
                           value="{DpdPoland::getInputValue(DpdPolandConfiguration::REF2_STATIC, $settings->ref2_static)|escape:'htmlall':'UTF-8'}"/>
                </span>
            </span>
        </div>
        <div class="clear"></div>

        <label>
            {l s='CustomerData1' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <span class="radio">
                <input id="additional_customer_data_1_none" type="radio"
                       name="{DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1|escape:'htmlall':'UTF-8'}"
                       {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1, $settings->customer_data_1) == DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_NONE}"/>
                <label class="t" for="additional_customer_data_1_none">
                    {l s='None' mod='dpdpoland'}
                </label>
            </span>
            <span class="radio">
                <input id="additional_customer_data_1_dynamic" type="radio"
                       name="{DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1|escape:'htmlall':'UTF-8'}"
                       {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1, $settings->customer_data_1) == DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC}"/>
                <label class="t" for="additional_customer_data_1_dynamic">
                    {l s='Dynamic' mod='dpdpoland'}
                </label>
                <span class="hidable">
                    <select name="{DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC|escape:'htmlall':'UTF-8'}">
                        <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_ID} selected="selected"{/if}>{l s='Order ID' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE} selected="selected"{/if}>{l s='Order reference' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_INVOICE_ID|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_INVOICE_ID} selected="selected"{/if}>{l s='Invoice ID' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS} selected="selected"{/if}>{l s='Other field in shipping address' mod='dpdpoland'}</option>
                        <option value="{DpdPolandConfiguration::DYNAMIC_PRODUCT_NAME|escape:'htmlall':'UTF-8'}"{if DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_DYNAMIC, $settings->customer_data_dynamic) == DpdPolandConfiguration::DYNAMIC_PRODUCT_NAME} selected="selected"{/if}>{l s='Product name' mod='dpdpoland'}</option>

                    </select>
                </span>
            </span>
            <span class="radio">
                <input id="additional_customer_data_1_static" type="radio"
                       name="{DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1|escape:'htmlall':'UTF-8'}"
                       {if DpdPoland::getInputValue(DpdPolandConfiguration::ADDITIONAL_CUSTOMER_DATA_1, $settings->customer_data_1) == DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}checked="checked"{/if} value="{DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC}"/>
                <label class="t" for="additional_customer_data_1_static">
                    {l s='Static' mod='dpdpoland'}
                </label>
                <span class="hidable">
                    <input type="text" name="{DpdPolandConfiguration::CUSTOMER_DATA_STATIC|escape:'htmlall':'UTF-8'}"
                           value="{DpdPoland::getInputValue(DpdPolandConfiguration::CUSTOMER_DATA_STATIC, $settings->customer_data_static)|escape:'htmlall':'UTF-8'}"/>
                </span>
            </span>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>
    </fieldset>

    <fieldset id="default_values">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Settings' mod='dpdpoland'}"/>
            {l s='Default values' mod='dpdpoland'}
        </legend>
        <label>
            {l s='Default weight:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="default_weight" type="number"
                   name="{DpdPolandConfiguration::DEFAULT_WEIGHT|escape:'htmlall':'UTF-8'}"
                   value="{DpdPoland::getInputValue(DpdPolandConfiguration::DEFAULT_WEIGHT, $settings->default_weight)|escape:'htmlall':'UTF-8'}"/>
        </div>
        <div class="clear"></div>
        <label>
            {l s='Default printer type:' mod='dpdpoland'}
        </label>

        <div class="margin-form">
            <select id="default_printer_input"
                    name="{DpdPolandConfiguration::DEFAULT_PRINTER_TYPE|escape:'htmlall':'UTF-8'}">
                <option value="{DpdPolandConfiguration::PRINTOUT_FORMAT_A4|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::DEFAULT_PRINTER_TYPE, $settings->default_printer_type) == DpdPolandConfiguration::PRINTOUT_FORMAT_A4|escape:'htmlall':'UTF-8'} selected="selected"{/if}>{l s='A4' mod='dpdpoland'}</option>
                <option value="{DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::DEFAULT_PRINTER_TYPE, $settings->default_printer_type) == DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL|escape:'htmlall':'UTF-8'} selected="selected"{/if}>{l s='Label' mod='dpdpoland'}</option>
            </select>
        </div>
        <div class="clear"></div>
        <label>
            {l s='Parcel content:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <select id="parcel_content_source"
                    name="{DpdPolandConfiguration::PARCEL_CONTENT_SOURCE|escape:'htmlall':'UTF-8'}">
                <option value="{DpdPolandConfiguration::PARCEL_CONTENT_SOURCE_SKU|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::PARCEL_CONTENT_SOURCE, $settings->parcel_content_source) == DpdPolandConfiguration::PARCEL_CONTENT_SOURCE_SKU|escape:'htmlall':'UTF-8'} selected="selected"{/if}>{l s='Product SKU' mod='dpdpoland'}</option>
                <option value="{DpdPolandConfiguration::PARCEL_CONTENT_SOURCE_PRODUCT_ID|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::PARCEL_CONTENT_SOURCE, $settings->parcel_content_source) == DpdPolandConfiguration::PARCEL_CONTENT_SOURCE_PRODUCT_ID|escape:'htmlall':'UTF-8'} selected="selected"{/if}>{l s='Product ID' mod='dpdpoland'}</option>
                <option value="{DpdPolandConfiguration::PARCEL_CONTENT_SOURCE_PRODUCT_NAME|escape:'htmlall':'UTF-8'}" {if DpdPoland::getInputValue(DpdPolandConfiguration::PARCEL_CONTENT_SOURCE, $settings->parcel_content_source) == DpdPolandConfiguration::PARCEL_CONTENT_SOURCE_PRODUCT_NAME|escape:'htmlall':'UTF-8'} selected="selected"{/if}>{l s='Product NAME' mod='dpdpoland'}</option>
            </select>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button"
                   name="{DpdPolandConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
                   value="{l s='Save' mod='dpdpoland'}"/>
        </div>
    </fieldset>

    <br/>
</form>