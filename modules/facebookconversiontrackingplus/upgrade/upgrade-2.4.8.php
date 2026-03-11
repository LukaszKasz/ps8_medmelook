<?php
/**
 * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_4_8($module)
{
    $configs_to_update = [
        [
            'old' => 'FCTP_ORDER_CUSTOMER_GROUP_EXCLUDE',
            'new' => 'FCTP_ORDER_CUSTOMER_GROUP_EXC',
        ],
    ];
    $end = false;
    $c = 1;
    while (!$end) {
        $key = 'FCTP_CONVERSION_API_ACCESS_TOKEN_' . $c;
        $new_key = 'FCTP_CAPI_TOKEN_' . $c;
        if (Shop::isFeatureActive()) {
            $shops = Shop::getShops(true, null, true);
        } else {
            $shops = [1];
        }
        $shop = array_unshift($shops, 0);
        $end = true;
        foreach ($shops as $shop) {
            if (Configuration::hasKey($key, null, null, $shop)) {
                $end = false;
                $tmp_val = Configuration::get($key, null, null, $shop);
                Configuration::updateValue($new_key, $tmp_val, false, null, $shop);
            }
        }
        Configuration::deleteByName($key);
        ++$c;
    }

    return true;
}
