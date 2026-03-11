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

function upgrade_module_4_3_0($object)
{
    if (!Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_author` (
            `id_author` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `lastname` varchar(255),
            `firstname` varchar(255),
            `pseudo` varchar(255),
            `date` datetime,
            `bio` mediumtext,
            `email` varchar(255),
            PRIMARY KEY (`id_author`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
        return false;
    }

    if (!Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_slide` (
            `id_slide` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shop` varchar(255),
            `position` varchar(255),
            PRIMARY KEY (`id_slide`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
        return false;
    }

    if (!Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_slide_lang` (
            `id_slide` INT,
            `title` varchar(255),
            `url_associate` varchar(255),
            `id_lang` varchar(255)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
        return false;
    }

    if (!Db::getInstance()->execute('
        ALTER TABLE `' . bqSQL(_DB_PREFIX_) . 'prestablog_news`
        ADD COLUMN `author_id` int(10) AFTER `number_rating`')) {
        return false;
    }

    Tools::clearCache();

    return true;
}
