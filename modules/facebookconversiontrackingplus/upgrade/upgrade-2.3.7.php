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

function upgrade_module_2_3_7($module)
{
    // Remove old values
    Configuration::deleteByName('FCP_CUST_WISHLIST_MODULE');

    // Update feed settings to global values
    $langs = Language::getLanguages();
    $shops = Shop::getShops();
    $multi = Shop::isFeatureActive();
    foreach ($shops as $shop) {
        $key = 'FCTP_' . $shop['id_shop'];
        if ($module::$feed_v2) {
            $feed_id = Configuration::get($key, null, null, $multi ? $shop['id_shop'] : null);
            Configuration::deleteByName($key);
            Configuration::updateGlobalValue($key, $feed_id);
        } else {
            foreach ($langs as $lang) {
                $key .= '_' . $lang['id_lang'];
                $feed_id = Configuration::get($key, null, null, $multi ? $shop['id_shop'] : null);
                Configuration::deleteByName($key);
                Configuration::updateGlobalValue($key, $feed_id);
            }
        }
    }

    return true;
}
