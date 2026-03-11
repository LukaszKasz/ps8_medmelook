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

function upgrade_module_4_0_5($object)
{
    /*if (!Db::getInstance()->execute('
        ALTER TABLE `'.bqSQL(_DB_PREFIX_).'prestablog_news_lang`
        MODIFY `content` MEDIUMTEXT'));
    {
        return false;
    }*/

    Tools::clearCache();

    return true;
}
