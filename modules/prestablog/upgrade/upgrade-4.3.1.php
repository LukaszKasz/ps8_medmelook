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

function upgrade_module_4_3_1($object)
{
    if (!Db::getInstance()->execute('
        ALTER TABLE `' . bqSQL(_DB_PREFIX_) . 'prestablog_color`
        ADD COLUMN `ariane_color` varchar(255),
        ADD COLUMN `ariane_color_text` varchar(255),
        ADD COLUMN `ariane_border` varchar(255),
        ADD COLUMN `block_categories_link_btn` varchar(255),
        DROP COLUMN `article_comment_background`')) {
        return false;
    }

    if (!Db::getInstance()->execute('
        ALTER TABLE `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide`
        DROP COLUMN `position`')) {
        return false;
    }
    if (!Db::getInstance()->execute('
        ALTER TABLE `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang`
        ADD COLUMN `position` int(11)')) {
        return false;
    }

    Tools::clearCache();

    return true;
}
