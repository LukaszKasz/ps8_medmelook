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
function upgrade_module_5_0_9()
{
    Tools::clearCache();
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/views/js/jquery.rwdImageMaps.min.js');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/views/js/jquery.canvasAreaDraw.blog.js');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/views/js/treeCategories.js');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/views/js/rrssb.min.js');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/views/css/rrssb.css');

    if (!Db::getInstance()->executeS('SHOW COLUMNS FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_color` LIKE "sharing_icon_color"')) {
        if (!Db::getInstance()->execute('
            ALTER TABLE `' . bqSQL(_DB_PREFIX_) . 'prestablog_color`
            ADD COLUMN `sharing_icon_color` varchar(30)')) {
            return false;
        }
    }

    return true;
}
