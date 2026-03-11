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

function upgrade_module_5_0_0()
{
    Tools::clearCache();
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/antispam.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/author.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/categories.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/commentnews.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/correspondancescategories.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/displayslider.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/lookbook.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/news.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/popup.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/slider.class.php');
    Tools::deleteFile(_PS_MODULE_DIR_ . '/prestablog/class/subblocks.class.php');

    return true;
}
