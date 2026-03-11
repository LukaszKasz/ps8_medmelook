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

{if isset($ps_15) && $ps_15}
	<a href="{$href|escape:'html':'UTF-8'}" title="{$action|escape:'html':'UTF-8'}" >
		<img src="{$smarty.const._DPDPOLAND_IMG_URI_}download.gif" alt="{$action|escape:'html':'UTF-8'}" />
	</a>
{else}
	<a href="{$href|escape:'html':'UTF-8'}" title="{$action|escape:'html':'UTF-8'}" >
		<i class="{$icon|escape:'html':'UTF-8'}"></i> {$action|escape:'html':'UTF-8'}
	</a>
{/if}
