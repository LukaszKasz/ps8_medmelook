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

function upgrade_module_5_2_7()
{
    Tools::clearCache();
    if (is_dir(_PS_MODULE_DIR_ . 'prestablog/override')) {
        Tools::deleteDirectory(_PS_MODULE_DIR_ . 'prestablog/override');
    }

    if (is_dir(_PS_MODULE_DIR_ . 'prestablog/override_before_1531')) {
        Tools::deleteDirectory(_PS_MODULE_DIR_ . 'prestablog/override_before_1531');
    }
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/views/js/tinymce.inc.js');

    return true;
}
