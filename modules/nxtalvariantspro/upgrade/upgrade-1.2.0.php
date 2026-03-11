<?php
/**
 * Product Variants Pro
 *
 * @author    Nxtal <support@nxtal.com>
 * @copyright Nxtal 2023
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @version   1.4.0
 *
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($module)
{
    return Db::getInstance()->execute('
		ALTER TABLE `'._DB_PREFIX_.'nxtal_variant_product` ADD COLUMN `id_feature` INT(11) AFTER `type`;
	');
}
