<?php
/**
 * 2008 - 2020 (c) Prestablog
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

function upgrade_module_4_4_8()
{
    Tools::clearCache();
    Tools::deleteFile(_PS_MODULE_DIR_ . 'prestablog/slider_position.php');

    return true;
}
