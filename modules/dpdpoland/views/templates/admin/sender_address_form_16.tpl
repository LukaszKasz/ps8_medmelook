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

<script>
	var dpdpoland_16 = true;
</script>

<form id="sender_address_form" class="form-horizontal" action="{$saveAction|escape:'htmlall':'UTF-8'}&menu=sender_address_form" method="post" enctype="multipart/form-data">
    <div id="credentials" class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i>
            {l s='Sender address' mod='dpdpoland'}
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="company_input">
                {l s='Alias:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="alias_input" type="text" name="alias" value="{$object->alias}" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="company_input">
                {l s='Company:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="company_input" type="text" name="company" value="{$object->company}" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="name_input">
                {l s='Name:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="name_input" type="text" name="name" value="{$object->name}" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="phone_input">
                {l s='Phone:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="phone_input" type="text" name="phone" value="{$object->phone}" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="address_input">
                {l s='Address:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="address_input" type="text" name="address" value="{$object->address}" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="city_input">
                {l s='City:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="city_input" type="text" name="city" value="{$object->city}" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="email_input">
                {l s='Email:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="email_input" type="text" name="email" value="{$object->email}" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required" for="postcode_input">
                {l s='Postcode:' mod='dpdpoland'}
            </label>
            <div class="col-lg-9">
                <input id="postcode_input" type="text" name="postcode" value="{$object->postcode}" />
            </div>
        </div>

        <input type="hidden" name="id_sender_address" value="{$object->id_sender_address|intval}" />

        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="saveSenderAddress" type="submit">
                <i class="process-icon-save"></i>
                {l s='Save' mod='dpdpoland'}
            </button>
        </div>
    </div>
</form>