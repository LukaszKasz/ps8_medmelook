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

function upgrade_module_5_0_3()
{
    Tools::clearCache();
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/views/css/prestablog-back-office.css');

    return true;
}
