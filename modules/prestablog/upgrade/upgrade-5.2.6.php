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

function upgrade_module_5_2_6($module)
{
    Tools::clearCache();

    // Check if the old "PrestaBlog" tab exists
    $id_tab_prestablog = (int) Tab::getIdFromClassName('PrestaBlog');

    if ($id_tab_prestablog) {
        // If "PrestaBlog" tab exists, delete it
        $tab = new Tab($id_tab_prestablog);
        $tab->delete();
    }

    // Check if the "Management" tab exists and rename it to "PrestaBlog"
    $id_tab_management = (int) Tab::getIdFromClassName('Management');

    if ($id_tab_management) {
        $tab = new Tab($id_tab_management);

        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = 'PrestaBlog';
        }

        $tab->save();
    }

    $module->registerAdminTab();

    $sql = 'SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'prestablog_author` LIKE "permissions"';
    $result = Db::getInstance()->executeS($sql);

    if (empty($result)) {
        $sql_add_column = 'ALTER TABLE `' . _DB_PREFIX_ . 'prestablog_author` ADD `permissions` JSON';
        if (!Db::getInstance()->execute($sql_add_column)) {
            return false;
        }

        $sql_init_permissions = 'UPDATE `' . _DB_PREFIX_ . 'prestablog_author` SET `permissions` = \'{}\' WHERE `permissions` IS NULL';
        if (!Db::getInstance()->execute($sql_init_permissions)) {
            return false;
        }
    }

    return true;
}
