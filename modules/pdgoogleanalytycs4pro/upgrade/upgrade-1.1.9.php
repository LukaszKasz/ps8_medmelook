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

function upgrade_module_1_1_9($module)
{
    Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'pdgoogleanalytycs4pro` ADD `client_id` varchar(64) NOT NULL');

    Configuration::updateValue('PD_GA4P_OS_SEND_ORDER', '5,11,2');
    Configuration::updateValue('PD_GA4P_TRANSACTION_SEND_TYPE', 1);
    Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_API_SECRET', '');
    Configuration::updateValue('PD_GA4P_GOOGLE_ANAL_AEC', 1);

    return true;
}
