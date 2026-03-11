{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="product-variants js-product-variants">

    {foreach from=$groups key=id_attribute_group item=group}
        {if !empty($group.attributes)}
        <div class="clearfix product-variants-item product-variants-item-{$id_attribute_group}">
            <span class="form-control-label">{$group.name}</span>
            {if $group.group_type == 'select'}
                <div class="custom-select2">
                <select
                        id="group_{$id_attribute_group}"
                        aria-label="{$group.name}"
                        data-product-attribute="{$id_attribute_group}"
                        name="group[{$id_attribute_group}]"
                        class="form-control form-control-select">
                    {foreach from=$group.attributes key=id_attribute item=group_attribute}
                        <option value="{$id_attribute}"
                                title="{$group_attribute.name}"{if $group_attribute.selected} selected="selected"{/if} {if $group.attributes_quantity.$id_attribute <= 0} class="attribute-not-in-stock"{/if}>{$group_attribute.name}

                      </option>
                    {/foreach}
                </select>
                </div>
            {elseif $group.group_type == 'color'}
                <ul id="group_{$id_attribute_group}">
                    {foreach from=$group.attributes key=id_attribute item=group_attribute}
                        <li class="float-left input-container {if $group.attributes_quantity.$id_attribute <= 0} attribute-not-in-stock{/if}" data-toggle="tooltip" data-animation="false" data-placement="top"  data-container= ".product-variants" title="{$group_attribute.name}">
                            <input class="input-color" type="radio" data-product-attribute="{$id_attribute_group}"
                                   name="group[{$id_attribute_group}]"
                                   value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if}>
                            <span
                                    {if $group_attribute.texture}
                                        class="color texture" style="background-image: url({$group_attribute.texture}){if $group.attributes_quantity.$id_attribute <= 0} , url('/medmelook/img/brak.png'){/if}""
                                    {elseif $group_attribute.html_color_code}
                                        class="color" style="background-color: {$group_attribute.html_color_code}"
                                    {/if}
                            ><span class="attribute-name sr-only">{$group_attribute.name}</span></span>
                        </li>
                    {/foreach}
                </ul>
            {elseif $group.group_type == 'radio'}
                <ul id="group_{$id_attribute_group}">
                    {foreach from=$group.attributes key=id_attribute item=group_attribute}
                        <li class="input-container float-left {if $group.attributes_quantity.$id_attribute <= 0} attribute-not-in-stock{/if}">
                            <input class="input-radio" type="radio" data-product-attribute="{$id_attribute_group}"
                                   name="group[{$id_attribute_group}]"
                                   title="{$group_attribute.name}"
                                   value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if}>
                            <span class="radio-label">{$group_attribute.name}</span>
                        </li>
                    {/foreach}
                </ul>
            {/if}
        </div>
        {/if}
    {/foreach}

    {if !$configuration.is_catalog}
        {block name='product_availability'}
            {if $product.show_availability && $product.availability_message}
                <span id="product-availability"
                      class="js-product-availability badge {if $product.availability == 'available'} {if $product.quantity <= 0  && !$product.allow_oosp} badge-danger product-unavailable  {elseif $product.quantity <= 0  && $product.allow_oosp}badge-warning product-unavailable-allow-oosp {else}badge-success product-available{/if}{elseif $product.availability == 'last_remaining_items'}badge-warning product-last-items{else}badge-danger product-unavailable{/if}">
                  {if $product.availability == 'available'}
                      <i class="fa fa-check rtl-no-flip" aria-hidden="true"></i>
                                                     {$product.availability_message}
                  {elseif $product.availability == 'last_remaining_items'}
                      <i class="fa fa-exclamation" aria-hidden="true"></i>
                                                     {$product.availability_message}
                  {else}
                      <i class="fa fa-ban" aria-hidden="true"></i>
                              {$product.availability_message}
                      {if isset($product.available_date) && $product.available_date != '0000-00-00'}

                      {if $product.available_date|date_format:"%y%m%d" > $smarty.now|date_format:"%y%m%d"}<span
                              class="available-date"> - {l s='Availability date:' d='Shop.Theme.Catalog'} {$product.available_date}</span>{/if}
                  {/if}
                  {/if}
                </span>
            {/if}
        {/block}
    {/if}
</div>




