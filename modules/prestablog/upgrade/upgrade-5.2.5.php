<?php
/**
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_2_5($module)
{
    $result = true;

    $sql_check_id_parent = 'SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'prestablog_commentnews` LIKE "id_parent"';
    $column_id_parent_exists = Db::getInstance()->executeS($sql_check_id_parent);

    if (!$column_id_parent_exists) {
        // Ajouter le champ id_parent
        $sql_add_id_parent = 'ALTER TABLE `' . _DB_PREFIX_ . 'prestablog_commentnews` 
            ADD `id_parent` INT(10) UNSIGNED NOT NULL DEFAULT "0" AFTER `actif`';
        if (!Db::getInstance()->execute($sql_add_id_parent)) {
            $result = false;
        }
    }

    $sql_check_is_admin = 'SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'prestablog_commentnews` LIKE "is_admin"';
    $column_is_admin_exists = Db::getInstance()->executeS($sql_check_is_admin);

    if (!$column_is_admin_exists) {
        $sql_add_is_admin = 'ALTER TABLE `' . _DB_PREFIX_ . 'prestablog_commentnews` 
            ADD `is_admin` TINYINT(1) NOT NULL DEFAULT "0" AFTER `id_parent`';
        if (!Db::getInstance()->execute($sql_add_is_admin)) {
            $result = false;
        }
    }

    Tools::clearCache();

    return $result;
}
