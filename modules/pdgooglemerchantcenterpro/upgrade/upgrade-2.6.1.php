<?php
/**
* 2012-2016 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Dynamic Ads Feed Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2016 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Facebook Dynamic Ads Feed Pro - PrestaShop 1.5.x and 1.6.x Module
* @version   1.0.2
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      27-11-2016
*/

if (!defined('_PS_VERSION_')) {
    exit;
}


function upgrade_module_2_6_1($module)
{
    $module->registerHook('actionGetProductPropertiesAfterUnitPrice');

    return true;
}
