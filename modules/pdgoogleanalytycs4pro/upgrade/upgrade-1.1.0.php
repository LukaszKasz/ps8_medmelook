<?php
/**
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Analytycs 4 Pro 1.6.x and 1.7.x Module © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek @ PrestaDev.pl
* @license   Do not edit, modify or copy this file, if you wish to customize it, contact us at info@prestadev.pl.
* @link      http://prestadev.pl
* @package   PD Google Analytycs 4 Pro 1.6.x and 1.7.x Module
* @version   1.0.2
* @date      01-05-2021
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'../../models/PdGA4PModel.php');
require_once(dirname(__FILE__).'../../models/PdGA4PRegistrationModel.php');

function upgrade_module_1_1_0($module)
{
    $module->registerHook('actionProductSearchAfter');
    $module->registerHook('actionOrderStatusPostUpdate');
    $module->registerHook('actionObjectOrderAddAfter');
    $module->registerHook('actionCustomerAccountAdd');
    PdGA4PModel::installDB();
    PdGA4PRegistrationModel::installDB();

    return true;
}
