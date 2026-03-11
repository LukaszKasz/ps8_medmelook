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
include_once _PS_MODULE_DIR_ . 'prestablog/prestablog.php';
include_once _PS_MODULE_DIR_ . 'prestablog/class/news.php';
include_once _PS_MODULE_DIR_ . 'prestablog/class/categories.php';

class PrestaBlogSitemapModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    // public $display_column_left = true;
    // public $display_column_right = true;
    public $display_header = false;
    public $display_footer = false;

    public function init()
    {
        parent::init();
        if (!Configuration::get('prestablog_sitemap_actif')) {
            Tools::redirect('index.php?controller=404');
        }

        if (Tools::getValue('token') != Configuration::get('prestablog_sitemap_token')) {
            Tools::redirect('index.php?controller=404');
        }
    }

    public function display()
    {
        $module = new PrestaBlog();

        $module->createTheShopSitemap();

        return true;
    }
}
