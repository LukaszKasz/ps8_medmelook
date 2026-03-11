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

function upgrade_module_2_5_9($module)
{
    // Old hooks unregistration
    $hooks_to_remove = [
        'displayProductButtons',
        'hookHeader',
        'createAccount',
        'paymentReturn',
        'orderConfirmation',
    ];
    foreach ($hooks_to_remove as $hook) {
        if ($module->isRegisteredInHook($hook)) {
            $module->unregisterHook($hook);
        }
    }

    return true;
}
