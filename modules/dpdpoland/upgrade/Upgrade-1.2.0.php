<?php
/**
 * 2019 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 *  @author    DPD Polska Sp. z o.o.
 *  @copyright 2019 DPD Polska Sp. z o.o.
 *  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska Sp. z o.o.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param Module $module
 *
 * @return bool
 */
function upgrade_module_1_2_0($module)
{
    $hasRegistered =
        $module->registerHook('displayBeforeCarrier') &&
        $module->registerHook('header') &&
        $module->registerHook('actionValidateOrder');

    $hasInstalled = Db::getInstance()->execute(
        'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'dpdpoland_pudo_cart` (
              `pudo_code` VARCHAR(255) NOT NULL,
              `id_cart` int(11) NOT NULL,
              PRIMARY KEY (`id_cart`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
    );

    return $hasInstalled && $hasRegistered;
}
