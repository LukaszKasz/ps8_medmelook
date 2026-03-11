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

function upgrade_module_4_4_4($object)
{
    if (!Db::getInstance()->execute('
        ALTER TABLE `' . bqSQL(_DB_PREFIX_) . 'prestablog_news_product`
        ADD `id_shop` INT NOT NULL')) {
        return false;
    }

    Tools::clearCache();

    return true;
}
