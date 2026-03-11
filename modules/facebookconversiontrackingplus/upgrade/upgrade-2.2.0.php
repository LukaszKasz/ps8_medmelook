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

function upgrade_module_2_2_0()
{
    Configuration::updateValue('FCTP_BLOCK_SCRIPT', 0);
    Configuration::updateValue('FCTP_COOKIE_NAME', '');
    Configuration::updateValue('FCTP_COOKIE_VALUE', '');
    Configuration::updateValue('FCTP_COOKIE_EXTERNAL', 0);
    Configuration::updateValue('FCTP_COOKIE_RELOAD', 1);
    Configuration::updateValue('FCTP_COOKIE_BUTTON', '');

    return true;
}
