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
<label>{l s='Company name:' mod='dpdpoland'}</label>
<div class="margin-form">{if isset($address.company)}{$address.company|escape:'htmlall':'UTF-8'}{/if}</div>

<label>{l s='Name and surname:' mod='dpdpoland'}</label>
<div class="margin-form">{if isset($address.name)}{$address.name|escape:'htmlall':'UTF-8'}{/if}</div>

<label>{l s='Street and house no.:' mod='dpdpoland'}</label>
<div class="margin-form">{if isset($address.street)}{$address.street|escape:'htmlall':'UTF-8'}{/if}</div>

<label>{l s='Postal code:' mod='dpdpoland'}</label>
<div class="margin-form">{if isset($address.postcode)}{$address.postcode|escape:'htmlall':'UTF-8'}{/if}</div>

<label>{l s='City:' mod='dpdpoland'}</label>
<div class="margin-form">{if isset($address.city)}{$address.city|escape:'htmlall':'UTF-8'}{/if}</div>

<label>{l s='Country:' mod='dpdpoland'}</label>
<div class="margin-form">{if isset($address.country)}{$address.country|escape:'htmlall':'UTF-8'}{/if}</div>

<label>{l s='E-mail:' mod='dpdpoland'}</label>
<div class="margin-form">{if isset($address.email)}{$address.email|escape:'htmlall':'UTF-8'}{/if}</div>

<label>{l s='Tel. No.' mod='dpdpoland'}</label>
<div class="margin-form">{if isset($address.phone)}{$address.phone|escape:'htmlall':'UTF-8'}{/if}</div>