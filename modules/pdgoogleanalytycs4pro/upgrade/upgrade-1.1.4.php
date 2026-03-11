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

function upgrade_module_1_1_4($module)
{
    Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_NO_DELIVERY', 0);
    return true;
}
