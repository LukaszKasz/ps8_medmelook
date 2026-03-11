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

<label class="col-lg-5 col-sm-5 col-xs-12">{l s='Company name:' mod='dpdpoland'}</label>
<div class="col-lg-7 col-sm-7 col-xs-12">
    <input type="text" name="dpdpoland_address_company" autocomplete="off" value="{if isset($address.company) && $address.company}{$address.company|escape:'htmlall':'UTF-8'}{/if}">
</div>

<div class="clearfix"></div>
<label class="col-lg-5 col-sm-5 col-xs-12">{l s='Name:' mod='dpdpoland'}</label>
<div class="col-lg-7 col-sm-7 col-xs-12">
    <input type="text" name="dpdpoland_address_firstname" autocomplete="off" value="{if isset($address.firstname) && $address.firstname}{$address.firstname|escape:'htmlall':'UTF-8'}{/if}">
</div>

<div class="clearfix"></div>
<label class="col-lg-5 col-sm-5 col-xs-12">{l s='Surname:' mod='dpdpoland'}</label>
<div class="col-lg-7 col-sm-7 col-xs-12">
    <input type="text" name="dpdpoland_address_lastname" autocomplete="off" value="{if isset($address.lastname) && $address.lastname}{$address.lastname|escape:'htmlall':'UTF-8'}{/if}">
</div>

<div class="clearfix"></div>
<label class="col-lg-5 col-sm-5 col-xs-12">{l s='Street and house no.:' mod='dpdpoland'}</label>
<div class="col-lg-7 col-sm-7 col-xs-12">
    <input type="text" name="dpdpoland_address_street" autocomplete="off" value="{if isset($address.street) && $address.street}{$address.street|escape:'htmlall':'UTF-8'}{/if}">
</div>

<div class="clearfix"></div>
<label class="col-lg-5 col-sm-5 col-xs-12">{l s='Postal code:' mod='dpdpoland'}</label>
<div class="col-lg-7 col-sm-7 col-xs-12">
    <input type="text" name="dpdpoland_address_postcode" autocomplete="off" value="{if isset($address.postcode) && $address.postcode}{$address.postcode|escape:'htmlall':'UTF-8'}{/if}">
</div>

<div class="clearfix"></div>
<label class="col-lg-5 col-sm-5 col-xs-12">{l s='City:' mod='dpdpoland'}</label>
<div class="col-lg-7 col-sm-7 col-xs-12">
    <input type="text" name="dpdpoland_address_city" autocomplete="off" value="{if isset($address.city) && $address.city}{$address.city|escape:'htmlall':'UTF-8'}{/if}">
</div>

<div class="clearfix"></div>
<label class="col-lg-5 col-sm-5 col-xs-12">{l s='Country:' mod='dpdpoland'}</label>
<div class="col-lg-7 col-sm-7 col-xs-12">
    <input type="text" name="dpdpoland_address_country" autocomplete="off" value="{if isset($address.country) && $address.country}{$address.country|escape:'htmlall':'UTF-8'}{/if}">
</div>

<div class="clearfix"></div>
<label class="col-lg-5 col-sm-5 col-xs-12">{l s='E-mail:' mod='dpdpoland'}</label>
<div class="col-lg-7 col-sm-7 col-xs-12">
    <input type="text" name="dpdpoland_address_email" autocomplete="off" value="{if isset($address.email) && $address.email}{$address.email|escape:'htmlall':'UTF-8'}{/if}">
</div>

<div class="clearfix"></div>
<label class="col-lg-5 col-sm-5 col-xs-12">{l s='Tel. No.' mod='dpdpoland'}</label>
<div class="col-lg-7 col-sm-7 col-xs-12">
    <input type="text" name="dpdpoland_address_phone" autocomplete="off" value="{if isset($address.phone) && $address.phone}{$address.phone|escape:'htmlall':'UTF-8'}{/if}">
</div>