<?php
/**
 * Facebook Products Feed catalogue export for Prestashop
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2016
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Advertising & Marketing
 * Registered Trademark & Property of smart-modules.com
 * ****************************************
 * *        Facebook Products Feed        *
 * *   http://www.smart-modules.com       *
 * ****************************************
 */

require_once dirname(__FILE__) . '/../../../config/config.inc.php';
require_once dirname(__FILE__) . '/../../../init.php';
require_once dirname(__FILE__) . '/../facebookproductsfeed.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

$langCode = Tools::getValue('lang_code');
if ($langCode !== '') {
    // Ensure the SSL base URL is defined
    if (!defined('_PS_BASE_URL_SSL_')) {
        define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
    }

    // Determine the appropriate base URL depending on the current protocol.
    // This example checks the HTTPS server variable; adjust as needed.
    $origin = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? _PS_BASE_URL_SSL_
        : _PS_BASE_URL_;

    header('Access-Control-Allow-Origin: ' . $origin);
    header('Content-Type: application/json');

    // Sanitize the language code
    $iso = preg_replace('/[^-a-zA-Z0-9_]/', '', $langCode);

    if (Module::isEnabled('facebookproductsfeed')) {
        $module = Module::getInstanceByName('facebookproductsfeed');
        echo json_encode($module->prepareGoogleTaxonomies($iso));
    } else {
        // If the module is disabled, send an empty array.
        echo '[{}]';
    }
} else {
    // If no language code is provided, return an empty JSON object as an array with one empty object.
    echo '[{}]';
}
