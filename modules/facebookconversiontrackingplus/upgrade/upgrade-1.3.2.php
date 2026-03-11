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
function upgrade_module_1_3_2($module)
{
    // Remove the Keypage searches as now it tracks all searches
    $sql = [];
    $sql[] = 'DELETE FROM `' . _DB_PREFIX_ . 'facebookpixels` WHERE pixel_extras_type = 4;';
    $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'facebookpixels` SET pixel_extras_type = 4 WHERE pixel_extras_type = 5';
    $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'facebookpixels` SET pixel_extras_type = 5 WHERE pixel_extras_type = 6';
    foreach ($sql as $query) {
        Db::getInstance()->execute(pSQL($query));
    }

    // All done if we get here the upgrade is successfull
    return $module;
}
