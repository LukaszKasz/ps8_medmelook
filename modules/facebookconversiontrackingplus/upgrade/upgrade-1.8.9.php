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

function upgrade_module_1_8_9($object)
{
    /* Update procedure for version 2.1.2 adding new features exclusively for out of stock products */
    if (!$object->isRegisteredInHook('displayBeforeBodyClosingTag')) {
        $object->registerHook('displayBeforeBodyClosingTag');
    }

    // Upgrade is successfull
    return true;
}
