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

function upgrade_module_2_2_1($module)
{
    // Review the microdata to include the updates
    $module->checkMicroData();
    $module->installTabs();
    Configuration::updateValue('FCTP_MICRO_IMG_LIMIT', 0);
    Configuration::updateValue('FCTP_MICRO_IGNORE_COVER', 0);
    if (Configuration::hasKey('FCP_ORDER_CONVERSION') && Configuration::get('FCP_ORDER_CONVERSION') != '') {
        Configuration::updateValue('FCP_ORDER_CONVERSION', json_encode('FCP_ORDER_CONVERSION'));
    }

    return true;
}
