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

function upgrade_module_2_4_4($module)
{
    if (!$module->isRegisteredInHook('actionOrderStatusPostUpdate')) {
        $module->registerHook('actionOrderStatusPostUpdate');
    }

    Configuration::updateGlobalValue('FCTP_PURCHASE_VALID_ONLY', 0);
    Configuration::updateValue('FCTP_ORDER_DELAYED_LIST', json_encode([]));

    return true;
}
