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

<div>
    <h5 class="my-0">{l s='Company name:' mod='dpdpoland'}</h5>
    <p>{if isset($address.company) && $address.company}{$address.company|escape:'htmlall':'UTF-8'}{/if}</p>
</div>

<div>
    <h5 class="my-0">{l s='Name and surname:' mod='dpdpoland'}</h5>
    <p>{if isset($address.name) && $address.name}{$address.name|escape:'htmlall':'UTF-8'}{/if}</p>
</div>

<div>
    <h5 class="my-0">{l s='Street and house no.:' mod='dpdpoland'}</h5>
    <p>{if isset($address.street) && $address.street}{$address.street|escape:'htmlall':'UTF-8'}{/if}</p>
</div>

<div>
    <h5 class="my-0">{l s='Postal code:' mod='dpdpoland'}</h5>
    <p>{if isset($address.postcode) && $address.postcode}{$address.postcode|escape:'htmlall':'UTF-8'}{/if}</p>
</div>

<div>
    <h5 class="my-0">{l s='City:' mod='dpdpoland'}</h5>
    <p>{if isset($address.city) && $address.city}{$address.city|escape:'htmlall':'UTF-8'}{/if}</p>
</div>

<div>
    <h5 class="my-0">{l s='Country:' mod='dpdpoland'}</h5>
    <p>{if isset($address.country) && $address.country}{$address.country|escape:'htmlall':'UTF-8'}{/if}</p>
</div>

<div>
    <h5 class="my-0">{l s='E-mail:' mod='dpdpoland'}</h5>
    <p>{if isset($address.email) && $address.email}{$address.email|escape:'htmlall':'UTF-8'}{/if}</p>
</div>

<div>
    <h5 class="my-0">{l s='Tel. No.' mod='dpdpoland'}</h5>
    <p>{if isset($address.phone) && $address.phone}{$address.phone|escape:'htmlall':'UTF-8'}{/if}</p>
</div>