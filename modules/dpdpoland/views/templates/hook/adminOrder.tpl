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
<br/>
{if $displayBlock}
    <script>
        var dpdpoland_ajax_uri = '{$dpdpoland_ajax_uri|escape:'htmlall':'UTF-8'}';
        var dpdpoland_pdf_uri = '{$smarty.const._DPDPOLAND_PDF_URI_|escape:'htmlall':'UTF-8'}';
        var dpdpoland_token = '{$dpdpoland_token|escape:'htmlall':'UTF-8'}';
        var dpdpoland_id_shop = '{$dpdpoland_id_shop|escape:'htmlall':'UTF-8'}';
        var dpdpoland_id_lang = '{$dpdpoland_id_lang|escape:'htmlall':'UTF-8'}';
        var _DPDPOLAND_DIMENTION_WEIGHT_DIVISOR_ = '{$smarty.const._DPDPOLAND_DIMENTION_WEIGHT_DIVISOR_|escape:'htmlall':'UTF-8'}';
        var id_package_ws = '{$package->id_package_ws|escape:'htmlall':'UTF-8'}';
        var _PS_ADMIN_DIR_ = '{$smarty.const._PS_ADMIN_DIR_|regex_replace:"/\\\/":"\\\\\\"}';
        var dpdpoland_parcels_error_message = "{l s='All products should be assigned to a particular parcel!' mod='dpdpoland'}";
        var modified_field_message = "{l s='Modified field' mod='dpdpoland'}";
        var redirect_and_open = '{$redirect_and_open|escape:'htmlall':'UTF-8'}';
        var printout_format = '{$printout_format|escape:'htmlall':'UTF-8'}';
        var current_order_uri = '{$dpdpoland_order_uri|escape:'htmlall':'UTF-8'}';

        {if isset($ps14) && $ps14}
        var id_order = '{Tools::getValue('id_order')|escape:'htmlall':'UTF-8'}';
        {/if}

        $(document).ready(function () {
            $('#dpdpoland_current_status_accordion').accordion({
                collapsible: true,
                autoHeight: true,
                heightStyle: "content"
            });

            {if isset($smarty.get.scrollToShipment)}
            $.scrollTo('#dpdpoland', 400, {offset: {top: -100}});
            toggleShipmentCreationDisplay();
            {/if}
        });
    </script>
    <fieldset id="dpdpoland" class="dpdpoland-ps14 dpdpoland-ps15">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_MODULE_URI_|escape:'htmlall':'UTF-8'}logo.gif" width="16"
                 height="16"> {l s='DPD Polska Sp. z o.o. shipping' mod='dpdpoland'}
            <a href="javascript:toggleShipmentCreationDisplay()"
               rel="[ {l s='collapse' mod='dpdpoland'} ]">[ {l s='expand' mod='dpdpoland'} ]</a>
        </legend>

        {if $smarty.const._DPDPOLAND_DEBUG_MODE_}
            <div class="warning warn">
                {l s='Module is in DEBUG mode' mod='dpdpoland'}
                {if DpdPoland::getConfig(DpdPolandWS::DEBUG_FILENAME)}
                    <br/>
                    <a target="_blank"
                       href="{$smarty.const._DPDPOLAND_MODULE_URI_|escape:'htmlall':'UTF-8'}{DpdPoland::getConfig(DpdPolandWS::DEBUG_FILENAME)}">
                        {l s='View debug file' mod='dpdpoland'}
                    </a>
                {/if}
            </div>
        {/if}
        <div id="dpdpoland_shipment_creation"{if isset($smarty.get.scrollToShipment)} class="displayed-element"{/if}>
            <div id="dpdpoland_msg_container">{if isset($errors) && $errors}{include file=$smarty.const._PS_MODULE_DIR_|cat:'dpdpoland/views/templates/admin/errors.tpl'}{/if}</div>
            {if isset($compatibility_warning_message) && $compatibility_warning_message}
                <div class="warning warn">
                    {$compatibility_warning_message|escape:'htmlall':'UTF-8'}
                </div>
            {/if}
            {if isset($address_warning_message) && $address_warning_message}
                <div class="warning warn">
                    {$address_warning_message|escape:'htmlall':'UTF-8'}
                </div>
            {/if}
            <label>{l s='Shipment mode:' mod='dpdpoland'}</label>
            <div class="margin-form">
                <select name="dpdpoland_SessionType" autocomplete="off">
                    <option value="domestic"{if (!empty($package->sessionType) && $package->sessionType == 'domestic') || (empty($package->sessionType) && $selected_id_method == $smarty.const._DPDPOLAND_STANDARD_ID_)} selected="selected"{/if}>{l s='DPD domestic shipment - Standard' mod='dpdpoland'}</option>
                    <option value="domestic_with_cod"{if (!empty($package->sessionType) && $package->sessionType == 'domestic_with_cod') || (empty($package->sessionType) && $selected_id_method == $smarty.const._DPDPOLAND_STANDARD_COD_ID_)} selected="selected"{/if}>{l s='DPD domestic shipment - Standard with COD' mod='dpdpoland'}</option>
                    <option value="international"{if (!empty($package->sessionType) && $package->sessionType == 'international') || (empty($package->sessionType) && $selected_id_method == $smarty.const._DPDPOLAND_CLASSIC_ID_)} selected="selected"{/if}>{l s='DPD international shipment (DPD Classic)' mod='dpdpoland'}</option>
                    <option value="pudo"{if (!empty($package->sessionType) && $package->sessionType == 'pudo') || (empty($package->sessionType) && $selected_id_method == $smarty.const._DPDPOLAND_PUDO_ID_)} selected="selected"{/if}>{l s='DPD Poland Reception Point Pickup' mod='dpdpoland'}</option>
                    <option value="pudo_cod"{if (!empty($package->sessionType) && $package->sessionType == 'pudo_cod') || (empty($package->sessionType) && $selected_id_method == $smarty.const._DPDPOLAND_PUDO_COD_ID_)} selected="selected"{/if}>{l s='DPD Poland Reception Point Pickup with COD' mod='dpdpoland'}</option>
                </select>
            </div>
            <div class="clear"></div>

            <label>{l s='DPD client number (Payer):' mod='dpdpoland'}</label>
            <div class="margin-form">
                <select class="client_number_select" name="dpdpoland_PayerNumber" autocomplete="off">
                    {foreach from=$payerNumbers item=payerNumber}
                        <option value="{$payerNumber.payer_number|escape:'htmlall':'UTF-8'}"{if $selectedPayerNumber == $payerNumber.payer_number} selected="selected"{/if}>{$payerNumber.payer_number|escape:'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <div class="clear"></div>

            <div class="separation"></div>

            <div id="dpdpoland_sender_address_container">
                <label><h3>{l s='Sender:' mod='dpdpoland'}</h3></label>
                <div class="clear"></div>
                <br/>
                <div id="dpdpoland_sender_address_selection_container">
                    <label>{l s='Recipient address:' mod='dpdpoland'}</label>
                    <div class="margin-form">
                        <select class="col-lg-6 col-sm-6 col-xs-12" id="sender_address_selection"
                                name="sender_address_selection" autocomplete="off">
                            {foreach from=$sender_addresses item=address key=id_sender_address}
                                <option value="{$id_sender_address}"{if $id_sender_address == $package->id_sender_address} selected="selected"{/if}>{$address}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="dpdpoland_address">
                    {include file=$smarty.const._DPDPOLAND_TPL_DIR_|cat:'admin/address.tpl'}
                </div>

                <div class="info hint clear visible-element relative">
                    {l s='Sender address can be changed in module settings page.' mod='dpdpoland'}
                </div>
            </div>

            <div id="dpdpoland_recipient_address_container">
                <label><h3>{l s='Recipient:' mod='dpdpoland'}</h3></label>
                <div class="clear"></div>

                <div id="dpdpoland_recipient_address_selection_container">
                    <label>{l s='Recipient address:' mod='dpdpoland'}</label>
                    <div class="margin-form">
                        <select id="dpdpoland_recipient_address_selection" name="dpdpoland_id_address_delivery"
                                autocomplete="off">
                            {foreach from=$recipientAddresses item=address}
                                {capture assign=address_title|escape:'htmlall':'UTF-8'}{$address['alias']|escape:'UTF-8'} - {$address['address1']|escape:'UTF-8'} {$address['postcode']|escape:'UTF-8'} {$address['city']|escape:'UTF-8'}{if !empty($address['state'])} {$address['state']|escape:'UTF-8'}{/if}, {$address['country']|escape:'UTF-8'}{/capture}
                                <option value="{$address['id_address']|escape:'htmlall':'UTF-8'}"{if $address['id_address'] == $selectedRecipientIdAddress} selected="selected"{/if}>{$address_title|truncate:45:'...'|escape:'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="dpdpoland_address">
                    {include file=$smarty.const._DPDPOLAND_TPL_DIR_|cat:'admin/address.tpl' address=$recipientAddress}
                </div>

            </div>

            <div class="clear"></div>

            <div class="separation"></div>

            <input type="hidden" id="dpdpoland_pudo_code_input">
            <div class="pudo-map-container">
                <script id="dpd-widget" type="text/javascript">
                    function pointSelected(pointID) {
                        $('#dpdpoland_pudo_code_input').val(pointID);
                    }
                </script>

                <script type="text/javascript"
                        src="//pudofinder.dpd.com.pl/source/dpd_widget.js?key=1ae3418e27627ab52bebdcc1a958fa04"></script>

                <hr/>
            </div>

            <div>
                <div id="dpdpoland_cod_amount_container"{if ($package->sessionType && $package->sessionType != 'domestic_with_cod') || (!$package->sessionType && $selected_id_method != $smarty.const._DPDPOLAND_STANDARD_COD_ID_)} class="hidden-element"{/if}>
                    <label>{l s='COD:' mod='dpdpoland'}</label>
                    <div class="margin-form">
                        <input type="text" name="dpdpoland_COD_amount" autocomplete="off"
                               onchange="this.value = this.value.replace(/,/g, '.');"
                               value="{if $package->cod_amount}{$package->cod_amount|escape:'htmlall':'UTF-8'}{else}{if isset($ps14) && $ps14}{Tools::convertPrice($order->total_paid_real, $currency_from, $currency_to)}{else}{Tools::convertPriceFull($order->total_paid_tax_incl, $currency_from, $currency_to)}{/if}{/if}"
                               maxlength="14" size="11"> <span>{$smarty.const._DPDPOLAND_CURRENCY_ISO_}</span>
                        <p>{l s='Enter the amount of COD' mod='dpdpoland'}</p>
                    </div>
                </div>

                {if $settings->declared_value}
                    <div id="dpdpoland_declared_value_amount_container">
                        <label>{l s='Valuable parcel:' mod='dpdpoland'}</label>
                        <div class="margin-form">
                            <input type="text" name="dpdpoland_DeclaredValue_amount" autocomplete="off"
                                   onchange="this.value = this.value.replace(/,/g, '.');"
                                   value="{if $package->declaredValue_amount}{$package->declaredValue_amount}{else}{$order_price_pl}{/if}"
                                   maxlength="14" size="11"> <span>{$smarty.const._DPDPOLAND_CURRENCY_ISO_}</span>
                            <p>{l s='Leave blank if service is not needed' mod='dpdpoland'}</p>
                        </div>
                    </div>
                {/if}
                <div class="clear"></div>

                {if $settings->cud}
                    <div id="dpdpoland_cud_container">
                        <label>{l s='Return parcel:' mod='dpdpoland'}</label>
                        <div class="margin-form">
                            <input type="checkbox" name="cud" value="1"{if $package->cud} checked="checked"{/if} />
                        </div>
                    </div>
                {/if}

                {if $settings->rod}
                    <div id="dpdpoland_rod_container">
                        <label>{l s='Return documents:' mod='dpdpoland'}</label>
                        <div class="margin-form">
                            <input type="checkbox" name="rod" value="1"{if $package->rod} checked="checked"{/if} />
                        </div>
                    </div>
                {/if}

                {if $settings->dpde}
                    <div id="dpdpoland_dpde_container">
                        <label for="dpdpoland_dpde" class="control-label col-lg-5">
                            {l s='DPD Express:' mod='dpdpoland'}
                        </label>
                        <div class="input-group col-lg-5">
                            <input type="checkbox" name="dpde" value="1"{if $package->dpde} checked="checked"{/if}>
                        </div>
                    </div>
                {/if}

                {if $settings->dpdnd}
                    <div id="dpdpoland_dpdnd_container">
                        <label for="dpdpoland_dpde" class="control-label col-lg-5">
                            {l s='DPD Next day:' mod='dpdpoland'}
                        </label>
                        <div class="input-group col-lg-5">
                            <input type="checkbox" name="dpdnd" value="1"{if $package->dpdnd} checked="checked"{/if}>
                        </div>
                    </div>
                {/if}

                {if $settings->dpdtoday}
                    <div id="dpdpoland_dpdtoday_container">
                        <label for="dpdpoland_dpdtoday" class="control-label col-lg-5">
                            {l s='DPD Today:' mod='dpdpoland'}
                        </label>
                        <div class="input-group col-lg-5">
                            <input type="checkbox" name="dpdtoday"
                                   value="1"{if $package->dpdtoday} checked="checked"{/if}>
                        </div>
                    </div>
                {/if}

                {if $settings->dpdsaturday}
                    <div id="dpdpoland_dpdsaturday_container">
                        <label for="dpdpoland_dpdsaturday" class="control-label col-lg-5">
                            {l s='DPD Saturday:' mod='dpdpoland'}
                        </label>
                        <div class="input-group col-lg-5">
                            <input type="checkbox" name="dpdsaturday"
                                   value="1"{if $package->dpdsaturday} checked="checked"{/if}>
                        </div>
                    </div>
                {/if}

                {if $settings->dpdfood}
                    <div id="dpdpoland_dpdfood_container">
                        <label for="dpdpoland_dpdfood" class="control-label col-lg-5">
                            {l s='DPD Food:' mod='dpdpoland'}
                        </label>
                        <div class="input-group col-lg-5">
                            <input type="checkbox" name="dpdfood"
                                   value="1"{if $package->dpdfood} checked="checked"{/if}>
                        </div>
                    </div>
                {/if}

                {if $settings->dpdlq}
                    <div id="dpdpoland_dpdlq_container">
                        <label for="dpdpoland_dpdlq" class="control-label col-lg-5">
                            {l s='DPD ADR:' mod='dpdpoland'}
                        </label>
                        <div class="input-group col-lg-5">
                            <input type="checkbox" name="dpdlq"
                                   value="1"{if $package->dpdlq} checked="checked"{/if}>
                        </div>
                    </div>
                {/if}
            </div>
            <div class="clear"></div>

            <div class="separation"></div>

            <div>
                <div id="dpdpoland_additional_info_container">
                    <label>{l s='Additional shipment information:' mod='dpdpoland'}</label>
                    <div class="margin-form">
                        <textarea name="additional_info" autocomplete="off" rows="4"
                                  cols="32">{if $package->additional_info}{$package->additional_info|escape:'htmlall':'UTF-8'}{elseif !$loaded_object}{$default_customer_data_1|escape:'htmlall':'UTF-8'}{/if}</textarea>
                    </div>
                </div>

                <div id="dpdpoland_shipment_references_container">
                    <label>{l s='Order ID:' mod='dpdpoland'}</label>
                    <div class="margin-form">
                        <input type="text" name="dpdpoland_ref1" autocomplete="off"
                               value="{if $package->ref1}{$package->ref1|escape:'htmlall':'UTF-8'}{elseif !$loaded_object}{$default_ref1|escape:'htmlall':'UTF-8'}{/if}"/>
                        <p>{l s='Reference number 1' mod='dpdpoland'}</p>
                    </div>
                    <div class="clear"></div>

                    <label>{l s='Invoice number:' mod='dpdpoland'}</label>
                    <div class="margin-form">
                        <input type="text" name="dpdpoland_ref2" autocomplete="off"
                               value="{if $package->ref2}{$package->ref2|escape:'UTF-8'}{elseif !$loaded_object}{$default_ref2|escape:'htmlall':'UTF-8'}{/if}"/>
                        <p>{l s='Reference number 2' mod='dpdpoland'}</p>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>

            <div class="separation"></div>

            <input type="hidden"
                   name="dpdpoland_parcel_content_type"
                   class="form-control" value="{$parcel_content_source|escape:'htmlall':'UTF-8'}">

            <b>{l s='Group the products in your shipment into parcels' mod='dpdpoland'}</b><br/>
            {l s='This module lets you organize your products into parcels using the table below. Select parcel number.' mod='dpdpoland'}
            <br/><br/>
            <table width="100%" cellspacing="0" cellpadding="0" class="table" id="dpdpoland_shipment_products">
                <colgroup>
                    <col width="10%">
                    <col width="">
                    <col width="10%">
                    <col width="20%">
                    <col width="5%">
                </colgroup>
                <thead>
                <tr>
                    <th>{l s='ID' mod='dpdpoland'}</th>
                    <th>{l s='Product' mod='dpdpoland'}</th>
                    <th>{l s='Weight' mod='dpdpoland'}</th>
                    <th>{l s='Parcel' mod='dpdpoland'}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$products item=product name=products}
                    <tr>
                        <td class="parcel_reference">
                            <input type="hidden"
                                   name="dpdpoland_products[{$smarty.foreach.products.index|escape:'htmlall':'UTF-8'}][id_product]"
                                   value="{$product.id_product|escape:'htmlall':'UTF-8'}">
                            <input type="hidden"
                                   name="dpdpoland_products[{$smarty.foreach.products.index|escape:'htmlall':'UTF-8'}][id_product_attribute]"
                                   value="{$product.id_product_attribute|escape:'htmlall':'UTF-8'}">
                            <input type="hidden"
                                   name="dpdpoland_products[{$smarty.foreach.products.index|escape:'htmlall':'UTF-8'}][reference]"
                                   class="form-control"
                                   value="{$product.reference|escape:'htmlall':'UTF-8'}">
                            <input type="hidden"
                                   name="dpdpoland_products[{$smarty.foreach.products.index|escape:'htmlall':'UTF-8'}][name]"
                                   class="form-control"
                                   value="{$product.name|escape:'htmlall':'UTF-8'}">
                            {$product.id_product|escape:'htmlall':'UTF-8'}
                            _{$product.id_product_attribute|escape:'htmlall':'UTF-8'}
                        </td>
                        <td class="product_name">{$product.name|escape:'htmlall':'UTF-8'}</td>
                        <td class="parcel_weight">
                            <input type="hidden" name="parcel_weight"
                                   value="{if $product.weight > 0}{$product.weight|escape:'htmlall':'UTF-8'}{else}{DpdPoland::getConfig(DpdPolandConfiguration::DEFAULT_WEIGHT)|escape:'htmlall':'UTF-8'}{/if}"/>
                            {$product.weight|string_format:"%.3f"} {$smarty.const._DPDPOLAND_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
                        </td>
                        <td>
                            <select class="parcel_selection"
                                    name="dpdpoland_products[{$smarty.foreach.products.index|escape:'htmlall':'UTF-8'}][parcel]"
                                    autocomplete="off">
                                <option value="0">--</option>
                                {foreach from=$parcels item=parcel}
                                    {if isset($parcel.id_parcel) && isset($product.id_parcel)}
                                        {assign var="selected_parcel" value=$parcel.id_parcel == $product.id_parcel}
                                    {else}
                                        {assign var="selected_parcel" value=$parcel.number == 1}
                                    {/if}
                                    <option value="{$parcel.number|escape:'htmlall':'UTF-8'}"{if $selected_parcel} selected="selected"{/if}>{$parcel.number|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td>
                            <input id="product_width" type="hidden"
                                   value="{$parcels[0].width|escape:'htmlall':'UTF-8'}"/>
                            <input id="product_height" type="hidden"
                                   value="{$parcels[0].height|escape:'htmlall':'UTF-8'}"/>
                            <input id="product_length" type="hidden"
                                   value="{$parcels[0].length|escape:'htmlall':'UTF-8'}"/>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            <br/>

            <div id="dpdpoland_add_product_container">
                <input type="text" id="dpdpoland_select_product" size="50" autocomplete="off"/>
                <input type="hidden" id="dpdpoland_selected_product_id_product" value="0"/>
                <input type="hidden" id="dpdpoland_selected_product_id_product_attribute" value="0"/>
                <input type="hidden" id="dpdpoland_selected_product_weight_numeric" value="0"/>
                <input type="hidden" id="dpdpoland_selected_product_weight" value="0"/>
                <input type="hidden" id="dpdpoland_selected_product_name" value="0"/>
                <input type="hidden" id="dpdpoland_selected_product_parcel_content" value="0"/>
                <input type="button" class="button" id="dpdpoland_add_product"
                       value="{l s='Add product' mod='dpdpoland'}"/>
                <p>
                    {l s='Begin typing the first letters of the product name, then select the product from the drop-down list.' mod='dpdpoland'}
                </p>
            </div>

            <div class="separation"></div>

            <b>{l s='Manage parcels' mod='dpdpoland'}</b><br/>
            {l s='Here you can change parcel parameters, create new parcels' mod='dpdpoland'}
            <br/><br/>
            <table width="100%" cellspacing="0" cellpadding="0" class="table" id="dpdpoland_shipment_parcels">
                <colgroup>
                    <col width="5%">
                    <col width="">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="5%">
                </colgroup>
                <thead>
                <tr>
                    <th class="center">{l s='Parcel' mod='dpdpoland'}</th>
                    <th>{l s='Content of parcel' mod='dpdpoland'}</th>
                    <th>{l s='Weight (kg)' mod='dpdpoland'}</th>
                    <th>{l s='Height (cm)' mod='dpdpoland'}</th>
                    <th>{l s='Length (cm)' mod='dpdpoland'}</th>
                    <th>{l s='Width (cm)' mod='dpdpoland'}</th>
                    <th>{l s='Dimension weight' mod='dpdpoland'}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$parcels item=parcel}
                    <tr>
                        <td class="center">
                            {$parcel.number|escape:'htmlall':'UTF-8'}
                            <input type="hidden" name="parcels[{$parcel.number|escape:'htmlall':'UTF-8'}][number]"
                                   value="{$parcel.number|escape:'htmlall':'UTF-8'}"/>
                        </td>
                        <td>
                            <input type="hidden" name="parcels[{$parcel.number|escape:'htmlall':'UTF-8'}][content]"
                                   autocomplete="off" value="{$parcel.content|escape:'htmlall':'UTF-8'}"/>
                            <input type="text" name="parcels[{$parcel.number|escape:'htmlall':'UTF-8'}][content]"
                                   size="46" autocomplete="off" value="{$parcel.content|escape:'htmlall':'UTF-8'}"/>
                            <p class="preference_description clear">{l s='Modified field' mod='dpdpoland'}</p>
                        </td>
                        <td>
                            <input type="text" name="parcels[{$parcel.number|escape:'htmlall':'UTF-8'}][weight]"
                                   size="10" autocomplete="off" value="{$parcel.weight|escape:'htmlall':'UTF-8'}"/>
                            <p class="preference_description clear">{l s='Modified field' mod='dpdpoland'}</p>
                        </td>
                        <td>
                            <input type="text" name="parcels[{$parcel.number|escape:'htmlall':'UTF-8'}][height]"
                                   size="10" autocomplete="off" value="{$parcel.height|escape:'htmlall':'UTF-8'}"/>
                            <p class="preference_description clear">{l s='Modified field' mod='dpdpoland'}</p>
                        </td>
                        <td>
                            <input type="text" name="parcels[{$parcel.number|escape:'htmlall':'UTF-8'}][length]"
                                   size="10" autocomplete="off" value="{$parcel.length|escape:'htmlall':'UTF-8'}"/>
                            <p class="preference_description clear">{l s='Modified field' mod='dpdpoland'}</p>
                        </td>
                        <td>
                            <input type="text" name="parcels[{$parcel.number|escape:'htmlall':'UTF-8'}][width]"
                                   size="10" autocomplete="off" value="{$parcel.width|escape:'htmlall':'UTF-8'}"/>
                            <p class="preference_description clear">{l s='Modified field' mod='dpdpoland'}</p>
                        </td>
                        <td class="parcel_dimension_weight">{sprintf('%.3f', $parcel.length|escape:'htmlall':'UTF-8'*$parcel.width|escape:'htmlall':'UTF-8'*$parcel.height|escape:'htmlall':'UTF-8'/$smarty.const._DPDPOLAND_DIMENTION_WEIGHT_DIVISOR_|escape:'htmlall':'UTF-8')}</td>
                        <td>
                            {if $parcel@index >0}
                                <img src="../img/admin/delete.gif" class="delete_parcel">
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>

            <div id="parcel_addition_container">
                <br/>
                <div class="infoContainer first">
                    <div class="info hint clear visible-element relative">
                        {l s='When adding new parcel: Additional fee will be charged by DPD PL depending on your DPD PL contract. Price for shipment that was shown to your customer always includes only one parcel per order.' mod='dpdpoland'}
                    </div>
                </div>
                <input type="button" id="add_parcel" class="button" value="{l s='Add parcel' mod='dpdpoland'}"/>
                <div class="clear"></div>
                <div class="separation"></div>

                <div class="infoContainer">
                    <div class="info hint clear visible-element relative">
                        {l s='It will not be possible to edit shipment after printintig labels.' mod='dpdpoland'}
                    </div>
                </div>
            </div>

            <div id="dpdgeopost_actions">
                <input type="button" id="save_and_print_labels" class="button"
                       value="{l s='Save and print labels' mod='dpdpoland'}"/>
                <input type="button" id="save_labels" class="button" value="{l s='Save' mod='dpdpoland'}"/>
                <input type="button" id="print_labels" class="button{if !$package->id_package_ws} hidden-element{/if}"
                       value="{l s='Print labels' mod='dpdpoland'}"/>
                <div id="printout_format_container">
                    <input id="printout_format_a4" type="radio" name="dpdpoland_printout_format"
                           {if DpdPoland::getConfig(DpdPolandConfiguration::DEFAULT_PRINTER_TYPE) == DpdPolandConfiguration::PRINTOUT_FORMAT_A4}checked="checked"{/if}
                           value="{DpdPolandConfiguration::PRINTOUT_FORMAT_A4|escape:'htmlall':'UTF-8'}"/>
                    <label class="t" for="printout_format_a4">
                        {l s='A4' mod='dpdpoland'}
                    </label>
                    <br/>
                    <input id="printout_format_label" type="radio" name="dpdpoland_printout_format"
                           {if DpdPoland::getConfig(DpdPolandConfiguration::DEFAULT_PRINTER_TYPE) == DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL}checked="checked"{/if}
                           value="{DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL|escape:'htmlall':'UTF-8'}"/>
                    <label class="t" for="printout_format_label">
                        {l s='Label Printer' mod='dpdpoland'}
                    </label>
                </div>
                <label class="printout_format_label">{l s='Printout format:' mod='dpdpoland'}</label>
                <div class="clear"></div>
            </div>
            <div class="separation"></div>
        </div>
        <div id="dpdpoland_current_status_accordion">
            <h3>
                <a href="#">
                    {l s='Current status' mod='dpdpoland'}
                </a>
            </h3>
            <div>
                <table cellspacing="0" cellpadding="10" class="table">
                    <thead>
                    <tr>
                        <th width="200">{l s='Action' mod='dpdpoland'}</th>
                        <th width="50">{l s='Status' mod='dpdpoland'}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{l s='Labels printed' mod='dpdpoland'}</td>
                        <td>{if $package->labels_printed}{l s='Yes' mod='dpdpoland'}{else}{l s='No' mod='dpdpoland'}{/if}</td>
                    </tr>
                    <tr>
                        <td>{l s='Manifest printed' mod='dpdpoland'}</td>
                        <td>{if $package->isManifestPrinted()}{l s='Yes' mod='dpdpoland'}{else}{l s='No' mod='dpdpoland'}{/if}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
{else}
    <fieldset id="dpdpoland">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_MODULE_URI_|escape:'htmlall':'UTF-8'}logo.gif" width="16"
                 height="16"> {l s='DPD Polska Sp. z o.o. shipping' mod='dpdpoland'}
        </legend>
        <p class="warn warning">
            {l s='Module is not configured yet. Please check required settings' mod='dpdpoland'} <a
                    href="{$moduleSettingsLink|escape:'htmlall':'UTF-8'}">{l s='here' mod='dpdpoland'}</a>
        </p>
    </fieldset>
{/if}