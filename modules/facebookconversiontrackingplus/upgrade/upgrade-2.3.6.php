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

function upgrade_module_2_3_6($module)
{
    // Remove old values
    Configuration::deleteByName('FCTP_DYNAMIC_ADS');
    // Create the new ones and set up the default values
    $new_fields = [
        ['name' => 'FCTP_CMS', 'def' => 1],
        ['name' => 'FCTP_CMS_VALUE', 'def' => 1],
        ['name' => 'FCTP_CONTACT_US', 'def' => 1],
        ['name' => 'FCTP_CONTACT_US_VALUE', 'def' => 1],
        ['name' => 'FCTP_NEWSLETTER', 'def' => 1],
        ['name' => 'FCTP_NEWSLETTER_VALUE', 'def' => 1],
        ['name' => 'FCTP_PAGETIME', 'def' => 1],
        ['name' => 'FCTP_PAGETIME_VALUE', 'def' => 1],
        ['name' => 'FCTP_PAGEVIEW_COUNT', 'def' => 1],
        ['name' => 'FCTP_PAGEVIEW_COUNT_VALUE', 'def' => 1],
        ['name' => 'FCTP_PAGEVIEW_COUNT_COOKIE_DAYS', 'def' => 1],
        ['name' => 'FCP_DEFERRED_LOADING', 'def' => 0],
        ['name' => 'FCP_DEFERRED_SECONDS', 'def' => 0],
        ['name' => 'FCP_DEFER_FIRST_TIME', 'def' => 1],
        ['name' => 'FCTP_DISCOUNT', 'def' => 1],
        ['name' => 'FCTP_DISCOUNT_VALUE', 'def' => 1],
        ['name' => 'FCP_EXTERNAL_ID_USAGE', 'def' => 1],
    ];
    foreach ($new_fields as $field) {
        Configuration::updateValue($field['name'], $field['def']);
    }

    return true;
}
