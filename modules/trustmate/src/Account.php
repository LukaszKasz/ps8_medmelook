<?php

/**
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2021 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  @version  Release: $Revision$
 *  International Registered Trademark & Property of PrestaShop SA
 */

class TrustmateAccount
{
    public static function reset()
    {
        Configuration::deleteByName('TRUSTMATE_DOMAIN');
        Configuration::deleteByName('TRUSTMATE_UUID');
        Configuration::deleteByName('TRUSTMATE_INVITATIONS');
        Configuration::deleteByName('TRUSTMATE_DISPATCH_TRIGGERED_BY');
        Configuration::deleteByName('TRUSTMATE_BEE');
        Configuration::deleteByName('TRUSTMATE_CHUPACABRA');
        Configuration::deleteByName('TRUSTMATE_FERRET');
        Configuration::deleteByName('TRUSTMATE_FERRET2');
        Configuration::deleteByName('TRUSTMATE_GORILLA');
        Configuration::deleteByName('TRUSTMATE_MUSKRAT');
        Configuration::deleteByName('TRUSTMATE_MUSKRAT2');
        Configuration::deleteByName('TRUSTMATE_HORNET');
        Configuration::deleteByName('TRUSTMATE_HYDRA');
        Configuration::deleteByName('TRUSTMATE_BADGER2');
        Configuration::deleteByName('TRUSTMATE_HORNET_POSTITION');
        Configuration::deleteByName('TRUSTMATE_MULTIHORNET');
        Configuration::deleteByName('TRUSTMATE_MULTIHORNET_PAGES');

        return true;
    }

    public static function getRegistrationData()
    {
        $context = Context::getContext();
        $address = Configuration::get('PS_SHOP_ADDR1') . ' ' . Configuration::get('PS_SHOP_ADDR2');

        return array(
            'url' => $context->shop->domain,
            'name' => Configuration::get('PS_SHOP_NAME'),
            'email' => Configuration::get('PS_SHOP_EMAIL'),
            'street' => $address,
            'city' => Configuration::get('PS_SHOP_CITY'),
            'zip_code' => Configuration::get('PS_SHOP_CODE'),
            'country' => Country::getIsoById(Configuration::get('PS_SHOP_COUNTRY_ID')),
            'nip' => Configuration::get('PS_SHOP_DETAILS'),
        );
    }
}
