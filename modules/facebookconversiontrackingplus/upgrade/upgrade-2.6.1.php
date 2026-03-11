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

function upgrade_module_2_6_1($module)
{
    $dir = _PS_MODULE_DIR_ . $module->name . '/views/templates/admin/_configure/helpers/form';
    if (file_exists($dir)) {
        array_map('unlink', glob("$dir/*.*"));
        rmdir($dir);
    }

    // Always return
    return true;
}
