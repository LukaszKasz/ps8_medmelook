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

function upgrade_module_2_4_3($module)
{
    Configuration::updateGlobalValue('FCTP_PURCHASE_TAX', 1);

    // Microdata moved from serialize to JSON, re-generate it
    $module->checkMicroData();
    $base_url = _MODULE_DIR_ . $module->name . '/views/templates/hook/';
    // Remove unused template
    $fn = 'pageview_count_event.tpl';
    if (file_exists($base_url . $fn)) {
        unlink($base_url . $fn);
    }

    return true;
}
