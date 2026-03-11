<?php
/**
* 2012-2015 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Merchant Center Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2015 Patryk Marek - PrestaDev.pl
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @link      http://prestadev.pl
* @package   PD Google Merchant Center Pro - PrestaShop 1.5.x and 1.6.x Module
* @version   2.2.0
* @date      04-03-2016
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

// ini_set('display_startup_errors',1);
// ini_set('display_errors',1);
// error_reporting(-1);

$module = Module::getInstanceByName('pdgooglemerchantcenterpro');

if (!$module->active || !Module::isInstalled('pdgooglemerchantcenterpro')) {
    die($module->l('Module is not instaled or not active.'));
}

$secure_key = Tools::getValue('secure_key');
if ($secure_key != $module->secure_key) {
    die($module->l('Wrong security key !!!'));
}

$generate_all = Tools::getValue('generate_all');
$id_configuration = (int)Tools::getValue('id_configuration');

if (isset($id_configuration) && !is_numeric($id_configuration) && empty($generate_all)) {
    die($module->l('Id configuration need to be number (numeric value).'));
}

if (is_numeric($generate_all) && $generate_all == 1) {
    if ($module->generateFeedFromConfig(true, false)) {
        die($module->l('All XML feeds from configurations was generated corectly.'));
    }
}

if (!$module->checkConfigExist($id_configuration) && empty($generate_all)) {
    die($module->l('Id configuration not exist.'));
}

if ((isset($id_configuration) && is_numeric($id_configuration)) && empty($generate_all)) {
    if ($module->generateFeedFromConfig(false, $id_configuration)) {
        die($module->l('XML feed was generated.'));
    }
}
