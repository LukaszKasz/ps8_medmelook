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
<form id="sender_address_form" class="defaultForm" action="{$saveAction|escape:'htmlall':'UTF-8'}&menu=sender_address_form" method="post" enctype="multipart/form-data">
    <fieldset id="credentials">
        <legend>
            <img src="{$smarty.const._DPDPOLAND_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Sender address' mod='dpdpoland'}" />
            {l s='Sender address' mod='dpdpoland'}
        </legend>

        <label>
            {l s='Alias:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="alias_input" type="text" name="alias" value="{$object->alias}" />
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Company:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="company_input" type="text" name="company" value="{$object->company}" />
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Name:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="name_input" type="text" name="name" value="{$object->name}" />
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Phone:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="phone_input" type="text" name="phone" value="{$object->phone}" />
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Address:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="address_input" type="text" name="address" value="{$object->address}" />
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='City:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="city_input" type="text" name="city" value="{$object->city}" />
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Email:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="email_input" type="text" name="email" value="{$object->email}" />
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <label>
            {l s='Postcode:' mod='dpdpoland'}
        </label>
        <div class="margin-form">
            <input id="postcode_input" type="text" name="postcode" value="{$object->postcode}" />
            <sup>*</sup>
        </div>
        <div class="clear"></div>

        <input type="hidden" name="id_sender_address" value="{$object->id_sender_address|intval}" />

        <div class="margin-form">
            <input type="submit" class="button" name="saveSenderAddress" value="{l s='Save' mod='dpdpoland'}" />
        </div>

        <div class="small">
            <sup>*</sup> {l s='Required field' mod='dpdpoland'}
        </div>
    </fieldset>
</form>