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
 * @author    DPD Polska Sp. z o.o.
 * @copyright 2019 DPD Polska Sp. z o.o.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska Sp. z o.o.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @return bool
 */
function upgrade_module_1_6_5()
{
    $configuration = DB::getInstance()->executeS('
			SELECT `id_configuration`, `name`, `value`
			FROM `' . _DB_PREFIX_ . 'configuration`
			WHERE `name` = "' . DpdPolandConfiguration::PASSWORD . '"
		');

    if (!$configuration || empty($configuration))
        return true;

    $phpEncryption = null;
    if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
        $phpEncryption = new Rijndael('_RIJNDAEL_KEY_', '_RIJNDAEL_IV_');
    else
        $phpEncryption = new PhpEncryption(_NEW_COOKIE_KEY_);

    foreach ($configuration as $item) {
        $encryptedPassword = $phpEncryption->encrypt($item['value']);
        if (!isset($encryptedPassword) || $encryptedPassword == null || $encryptedPassword == "")
            return false;
        $updateSql = 'UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value` = "' . $encryptedPassword . '" WHERE `' . _DB_PREFIX_ . 'configuration`.`id_configuration` = ' . $item['id_configuration'] . ';';
        $newResult = DB::getInstance()->execute($updateSql);
        if ($newResult !== true)
            return false;
    }

    return true;
}

