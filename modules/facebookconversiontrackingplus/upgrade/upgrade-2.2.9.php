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

function upgrade_module_2_2_9($module)
{
    // Update the name of the templates
    $old_tpls = ['start_order', 'start_payment', 'checkoutpixel'];
    $base_url = _MODULE_DIR_ . $module->name . '/views/templates/hook/';

    foreach ($old_tpls as $tpl) {
        if (file_exists($base_url . $tpl)) {
            unlink($base_url . $tpl);
        }
    }

    return true;
}
