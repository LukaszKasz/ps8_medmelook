<?php
/**
 * (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *
 * @version    5.2.9
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

include_once dirname(__FILE__) . '/class/news.php';
include_once dirname(__FILE__) . '/class/categories.php';
include_once dirname(__FILE__) . '/class/correspondancescategories.php';
include_once dirname(__FILE__) . '/class/commentnews.php';
include_once dirname(__FILE__) . '/class/antispam.php';
include_once dirname(__FILE__) . '/class/subblocks.php';
include_once dirname(__FILE__) . '/class/displayslider.php';
include_once dirname(__FILE__) . '/class/popup.php';
include_once dirname(__FILE__) . '/class/author.php';
include_once dirname(__FILE__) . '/class/slider.php';
$layerslider = Module::getInstanceByName('layerslider');
if ($layerslider) {
    include_once dirname(__FILE__) . '/../layerslider/layerslider.php';
}

class PrestaBlog extends Module implements WidgetInterface
{
    /******* DEMO MODE **********/

    /*
    * true or false,  false, all upload files
    * and critical hoster informations are disable
    * feature @PrestaBlog demo reserved
    */
    protected $demo_mode = false;

    public $html_out = '';
    public $module_path = '';
    public $mois_langue = [];
    public $rss_langue = [];

    private $checksum = '';

    protected $check_slide;
    protected $check_active;

    protected $check_comment_state = -2;

    protected $normal_image_size_width = 1024;
    protected $normal_image_size_height = 1024;

    protected $admin_crop_image_size_width = 400;
    protected $admin_crop_image_size_height = 400;

    protected $lb_crop_image_size_width = 700;
    protected $lb_crop_image_size_height = 700;

    protected $admin_thumb_image_size_width = 40;
    protected $admin_thumb_image_size_height = 40;

    protected $max_image_size = 25510464;
    protected $default_theme = 'grid-and-slides';

    protected $confpath;

    public $layout_blog = [
        0 => 'layouts/layout-full-width.tpl',
        1 => 'layouts/layout-both-columns.tpl',
        2 => 'layouts/layout-left-column.tpl',
        3 => 'layouts/layout-right-column.tpl',
        4 => 'layouts/layout-content-only.tpl',
    ];

    public static function httpS()
    {
        return Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http';
    }

    public static function urlSRoot()
    {
        $module_shop_domain = self::getContextShopDomain(true) . __PS_BASE_URI__;
        if (Configuration::get('PS_SSL_ENABLED')) {
            $module_shop_domain = self::getContextShopDomainSsl(true) . __PS_BASE_URI__;
        }

        $virtual_uri = Context::getContext()->shop->virtual_uri;

        if (!empty($virtual_uri) && strpos($module_shop_domain, $virtual_uri) === false) {
            $module_shop_domain .= $virtual_uri;
        }

        return $module_shop_domain;
    }

    public static function accurl()
    {
        if ((int) Configuration::get('PS_REWRITING_SETTINGS')
        && (int) Configuration::get('prestablog_rewrite_actif')) {
            return '?';
        } else {
            return '&';
        }
    }

    public static function getT()
    {
        return Configuration::get('prestablog_theme');
    }

    public static function getP()
    {
        return Configuration::get('prestablog_popup');
    }

    public static function imgPath()
    {
        return dirname(__FILE__) . '/views/img/';
    }

    public static function imgPathFO()
    {
        return _MODULE_DIR_ . 'prestablog/views/img/';
    }

    public static function imgPathBO()
    {
        return _MODULE_DIR_ . 'prestablog/views/img/';
    }

    public static function imgUpPath()
    {
        return self::imgPath() . self::getT() . '/up-img';
    }

    public static function imgAuthorUpPath()
    {
        return self::imgPath() . self::getT() . '/author_th';
    }

    public static function getPathRootForExternalLink()
    {
        return Tools::getShopDomainSsl(true) . __PS_BASE_URI__;
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /************** WIDGET 1.7 **************/

    public function renderWidget($hookname = null, array $configuration = [])
    {
        $template_file = 'module:prestablog/views/templates/hook/' . self::getT() . '_' . $hookname . '.tpl';

        $this->smarty->assign($this->getWidgetVariables($hookname, $configuration));

        return $this->fetch($template_file, $this->getCacheId());
    }

    public function getWidgetVariables($hookname = null, array $configuration = [])
    {
        $hookname = $hookname;
        $configuration = $configuration;

        return null;
    }

    /************ / WIDGET 1.7 **************/

    public function renderWidgetPopup($hookname = null, array $configuration = [])
    {
        $template_file_popup = 'module:prestablog/views/templates/hook/' . $hookname . '.tpl';

        $this->smarty->assign($this->getWidgetVariables($hookname, $configuration));

        return $this->fetch($template_file_popup, $this->getCacheId());
    }

    public function getWidgetVariablesPopup($hookname = null, array $configuration = [])
    {
        $hookname = $hookname;
        $configuration = $configuration;

        return null;
    }

    /************ / WIDGET 1.7 **************/

    public static function isPSVersion($compare, $version)
    {
        return version_compare(_PS_VERSION_, $version, $compare);
    }

    public static function getModuleDataBaseVersion()
    {
        $module = Db::getInstance()->getRow('
      SELECT `version` FROM `' . bqSQL(_DB_PREFIX_) . 'module`
      WHERE `name` = \'prestablog\'');

        return $module['version'];
    }

    public function loadJsForTiny()
    {
        $this->context->controller->addJs([
            _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_ . 'admin/tinymce.inc.js',
            _PS_JS_DIR_ . 'admin/tinymce_loader.js',
        ]);
    }

    public function __construct()
    {
        $this->name = 'prestablog';
        $this->tab = 'front_office_features';
        $this->version = '5.2.9';
        $this->author = 'Prestablog';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '7aafe030447c17f08629e0319107b62b';
        parent::__construct();

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];

        $this->displayName = $this->trans('PrestaBlog', [], 'Modules.Prestablog.Prestablog');
        $this->description = $this->trans('A module to add a blog on your web store.', [], 'Modules.Prestablog.Prestablog');

        $this->confirmUninstall = $this->trans('Are you sure you want to delete this module ?', [], 'Modules.Prestablog.Prestablog');

        $this->confpath = 'index.php?tab=AdminModules&configure=prestablog&token=' . Tools::getValue('token');
        $this->ctrblog = '?fc=module&module=prestablog&controller=blog';
        $this->cc = 'configure=prestablog';
        $this->pp_conf = $this->confpath;
        $this->langue_default_store = (int) Configuration::get('PS_LANG_DEFAULT');

        $path = dirname(__FILE__);
        if (strpos(__FILE__, 'Module.php') !== false) {
            $path .= '/../modules/' . $this->name;
        }
        $this->configurations = [
            $this->name . '_token' => md5($this->genererMDP(32) . _COOKIE_KEY_),
        ];

        $this->mois_langue = [
            1 => $this->trans('January', [], 'Modules.Prestablog.Prestablog'),
            2 => $this->trans('February', [], 'Modules.Prestablog.Prestablog'),
            3 => $this->trans('March', [], 'Modules.Prestablog.Prestablog'),
            4 => $this->trans('April', [], 'Modules.Prestablog.Prestablog'),
            5 => $this->trans('May', [], 'Modules.Prestablog.Prestablog'),
            6 => $this->trans('June', [], 'Modules.Prestablog.Prestablog'),
            7 => $this->trans('July', [], 'Modules.Prestablog.Prestablog'),
            8 => $this->trans('August', [], 'Modules.Prestablog.Prestablog'),
            9 => $this->trans('September', [], 'Modules.Prestablog.Prestablog'),
            10 => $this->trans('October', [], 'Modules.Prestablog.Prestablog'),
            11 => $this->trans('November', [], 'Modules.Prestablog.Prestablog'),
            12 => $this->trans('December', [], 'Modules.Prestablog.Prestablog'),
        ];

        $this->module_path = $path;
        if (Configuration::get('prestablog_urlblog') == false) {
            $this->message_call_back = [
                'blog' => 'blog',
                'no_result_found' => $this->trans('No result found', [], 'Modules.Prestablog.Prestablog'),
                'no_result_listed' => $this->trans('No result listed', [], 'Modules.Prestablog.Prestablog'),
                'total_results' => $this->trans('Total results', [], 'Modules.Prestablog.Prestablog'),
                'next_results' => $this->trans('Next', [], 'Modules.Prestablog.Prestablog'),
                'prev_results' => $this->trans('Previous', [], 'Modules.Prestablog.Prestablog'),
                'blocRss' => $this->trans('Block Rss all news', [], 'Modules.Prestablog.Prestablog'),
                'blocDateListe' => $this->trans('Block date news', [], 'Modules.Prestablog.Prestablog'),
                'blocLastListe' => $this->trans('Block last news', [], 'Modules.Prestablog.Prestablog'),
                'blocCatListe' => $this->trans('Block categories news', [], 'Modules.Prestablog.Prestablog'),
                'blocSearch' => $this->trans('Block search news', [], 'Modules.Prestablog.Prestablog'),
                'Yu3Tr9r7' => $this->trans('The import XML was successfull', [], 'Modules.Prestablog.Prestablog'),
                '2yt6wEK7' => $this->trans('No import selected', [], 'Modules.Prestablog.Prestablog'),
            ];
        } else {
            $this->message_call_back = [
                Configuration::get($this->name . '_urlblog') => Configuration::get($this->name . '_urlblog'),
                'no_result_found' => $this->trans('No result found', [], 'Modules.Prestablog.Prestablog'),
                'no_result_listed' => $this->trans('No result listed', [], 'Modules.Prestablog.Prestablog'),
                'total_results' => $this->trans('Total results', [], 'Modules.Prestablog.Prestablog'),
                'next_results' => $this->trans('Next', [], 'Modules.Prestablog.Prestablog'),
                'prev_results' => $this->trans('Previous', [], 'Modules.Prestablog.Prestablog'),
                'blocRss' => $this->trans('Block Rss all news', [], 'Modules.Prestablog.Prestablog'),
                'blocDateListe' => $this->trans('Block date news', [], 'Modules.Prestablog.Prestablog'),
                'blocLastListe' => $this->trans('Block last news', [], 'Modules.Prestablog.Prestablog'),
                'blocCatListe' => $this->trans('Block categories news', [], 'Modules.Prestablog.Prestablog'),
                'blocSearch' => $this->trans('Block search news', [], 'Modules.Prestablog.Prestablog'),
                'Yu3Tr9r7' => $this->trans('The import XML was successfull', [], 'Modules.Prestablog.Prestablog'),
                '2yt6wEK7' => $this->trans('No import selected', [], 'Modules.Prestablog.Prestablog'),
            ];
        }

        $this->default_theme = 'grid-and-slides';

        $this->configurations = [
            /* Thèmes et slide ************************* */
            /* URL /blog */
            $this->name . '_urlblog' => 'blog',
            /**Popup*/
            $this->name . '_popuphome_actif' => 0,
            $this->name . '_popup_general' => 0,
            // Thème
            $this->name . '_theme' => $this->default_theme,
            // layouts/layout-left-column.tpl
            $this->name . '_layout_blog' => 2,
            // displayRating
            $this->name . '_rating_actif' => 1,
            // number read front
            $this->name . '_read_actif' => 0,
            // Slideshow
            $this->name . '_homenews_actif' => 0,
            $this->name . '_pageslide_actif' => 0,
            $this->name . '_homenews_limit' => 5,
            $this->name . '_slide_picture_width' => 960,
            $this->name . '_slide_picture_height' => 350,
            $this->name . '_show_slide_title' => 1,
            $this->name . '_slide_title_length' => 80,
            $this->name . '_slide_intro_length' => 160,
            /* /Thèmes et slide ************************ */

            /* Blocs *********************************** */
            // Bloc derniers articles
            $this->name . '_lastnews_limit' => 5,
            $this->name . '_lastnews_showall' => 1,
            $this->name . '_lastnews_actif' => 1,
            $this->name . '_lastnews_showintro' => 0,
            $this->name . '_lastnews_showthumb' => 1,
            $this->name . '_lastnews_title_length' => 80,
            $this->name . '_lastnews_intro_length' => 120,
            // Bloc d'articles par date
            $this->name . '_datenews_order' => 'desc',
            $this->name . '_datenews_showall' => 0,
            $this->name . '_datenews_actif' => 0,
            // Bloc Rss pour tous les articles
            $this->name . '_allnews_rss' => 0,
            $this->name . '_rss_title_length' => 80,
            $this->name . '_rss_intro_length' => 200,
            // Bloc Search
            $this->name . '_blocsearch_actif' => 1,
            $this->name . '_search_filtrecat' => 1,
            // Dernières actualités en footer
            $this->name . '_footlastnews_actif' => 0,
            $this->name . '_footlastnews_limit' => 3,
            $this->name . '_footlastnews_showall' => 1,
            $this->name . '_footlastnews_intro' => 0,
            $this->name . '_footer_title_length' => 80,
            $this->name . '_footer_intro_length' => 120,
            $this->name . '_footlastnews_showthumb' => 1,
            // Ordre des blocs dans les colonnes
            $this->name . '_sbr' => json_encode(
                [
                    0 => '',
                ]
            ),
            $this->name . '_sbl' => json_encode(
                [
                    0 => 'blocSearch',
                    1 => 'blocLastListe',
                    2 => 'blocCatListe',
                    3 => 'blocDateListe',
                    4 => 'blocRss',
                ]
            ),
            /* /Blocs ********************************** */

            /* SubBlocs ******************************** */
            $this->name . '_subblocks_actif' => 1,
            /* /SubBlocs ******************************* */

            /* Commentaires ***********************D***** */
            $this->name . '_comment_actif' => 1,
            $this->name . '_comment_only_login' => 0,
            $this->name . '_comment_auto_actif' => 0,
            $this->name . '_comment_alert_admin' => 0,
            $this->name . '_comment_admin_mail' => Configuration::get('PS_SHOP_EMAIL'),
            $this->name . '_captcha_actif' => 0,
            $this->name . '_captcha_public_key' => '',
            $this->name . '_captcha_private_key' => '',
            $this->name . '_comment_subscription' => 1,
            /* /Commentaires *************************** */

            /* Commentaires Facebook ******************* */
            $this->name . '_commentfb_actif' => 0,
            $this->name . '_commentfb_nombre' => 5,
            $this->name . '_commentfb_apiId' => '',
            $this->name . '_commentfb_modosId' => '',
            /* /Commentaires Facebook ****************** */

            /* Categorie ******************************* */
            // Menu catégories dans la page du blog
            $this->name . '_menu_cat_blog_index' => 1,
            $this->name . '_menu_cat_blog_list' => 1,
            $this->name . '_menu_cat_blog_article' => 1,
            $this->name . '_menu_cat_blog_empty' => 0,
            $this->name . '_menu_cat_home_link' => 1,
            $this->name . '_menu_cat_home_img' => 1,
            // $this->name.'_menu_cat_blog_rss' => 0,
            $this->name . '_menu_cat_blog_nbnews' => 0,
            // Bloc de catégories d'article
            $this->name . '_catnews_showall' => 0,
            $this->name . '_catnews_rss' => 0,
            $this->name . '_catnews_actif' => 0,
            $this->name . '_catnews_empty' => 0,
            $this->name . '_catnews_tree' => 1,
            // page liste d'articles
            $this->name . '_catnews_shownbnews' => 1,
            $this->name . '_catnews_showthumb' => 1,
            $this->name . '_catnews_showintro' => 1,
            // liste des categories
            $this->name . '_thumb_cat_width' => 150,
            $this->name . '_thumb_cat_height' => 150,
            $this->name . '_full_cat_width' => 535,
            $this->name . '_full_cat_height' => 236,
            $this->name . '_cat_title_length' => 80,
            $this->name . '_cat_intro_length' => 120,
            /* /Categorie ****************************** */

            /* Globales ******************************** */
            // Configuration du rewrite
            $this->name . '_rewrite_actif' => (int) Configuration::get('PS_REWRITING_SETTINGS'),
            // Configuration du display author
            $this->name . '_author_actif' => 0,
            $this->name . '_author_edit_actif' => 0,
            $this->name . '_author_cate_actif' => 0,
            $this->name . '_author_news_actif' => 0,
            $this->name . '_author_about_actif' => 0,
            $this->name . '_author_news_number' => 6,
            $this->name . '_author_intro_length' => 150,
            $this->name . '_author_pic_width' => 200,
            $this->name . '_author_pic_height' => 200,
            $this->name . '_enable_permissions' => 0,
            // Configuration générale du front-office
            $this->name . '_nb_liste_page' => 8,
            $this->name . '_article_page' => 2,
            $this->name . '_producttab_actif' => 1,
            $this->name . '_material_icons' => 0,
            $this->name . '_socials_actif' => 1,
            $this->name . '_s_facebook' => 1,
            $this->name . '_s_twitter' => 1,
            $this->name . '_s_linkedin' => 1,
            $this->name . '_s_email' => 1,
            $this->name . '_s_pinterest' => 1,
            $this->name . '_s_pocket' => 0,
            $this->name . '_s_tumblr' => 0,
            $this->name . '_s_reddit' => 0,
            $this->name . '_s_hackernews' => 0,
            $this->name . '_uniqnews_rss' => 0,
            $this->name . '_view_cat_desc' => 1,
            $this->name . '_view_cat_thumb' => 0,
            $this->name . '_view_cat_img' => 1,
            $this->name . '_view_news_img' => 0,
            $this->name . '_show_breadcrumb' => 1,
            $this->name . '_lb_title_length' => 80,
            $this->name . '_lb_intro_length' => 120,
            // liste des produits liés
            $this->name . '_thumb_linkprod_width' => 100,
            // liste d'articles
            $this->name . '_thumb_picture_width' => 129,
            $this->name . '_thumb_picture_height' => 129,
            $this->name . '_news_title_length' => 80,
            $this->name . '_news_intro_length' => 200,
            // Configuration globale de l'administration
            $this->name . '_nb_car_min_linkprod' => 2,
            $this->name . '_nb_list_linkprod' => 5,
            $this->name . '_nb_car_min_linknews' => 2,
            $this->name . '_nb_list_linknews' => 5,
            $this->name . '_nb_car_min_linklb' => 2,
            $this->name . '_nb_list_linklb' => 5,
            $this->name . '_nb_news_pl' => 20,
            $this->name . '_nb_comments_pl' => 20,
            $this->name . '_comment_div_visible' => 0,
            /* /Globales ******************************* */

            /* Outils ********************************** */
            // Anitspam
            $this->name . '_antispam_actif' => 0,
            // Sitemap
            $this->name . '_sitemap_actif' => 0,
            $this->name . '_sitemap_articles' => 1,
            $this->name . '_sitemap_categories' => 1,
            $this->name . '_sitemap_limit' => 5000,
            $this->name . '_sitemap_older' => 99,
            $this->name . '_sitemap_token' => $this->genererMDP(8),
            // Importation depuis un XML de WordPress
            $this->name . '_import_xml' => '',
            // ChatGPT
            $this->name . '_chatgpt_api_key' => '',
            $this->name . '_chatgpt_model' => '',
            $this->name . '_chatgpt_models' => '',
            /* /Outils ********************************* */
            $this->name . '_token' => md5($this->genererMDP(32) . _COOKIE_KEY_),
        ];

        $this->context->smarty->assign(
            [
                'prestablog_config' => Configuration::getMultiple(array_keys($this->configurations)),
                'md5pic' => md5(time()),
                'prestablog_theme_dir' => _MODULE_DIR_ . $this->name . '/views/',
                'prestablog_theme_upimg' => _MODULE_DIR_ . $this->name . '/views/img/' . self::getT() . '/up-img/',
            ]
        );
        // Create configuration files for all themes
        $this->createConfigFilesForAllThemes();
    }

    private function registerHookPosition($hook_name, $position)
    {
        if ($this->registerHook($hook_name)) {
            $this->updatePosition((int) Hook::getIdByName($hook_name), 0, (int) $position);
        } else {
            return false;
        }

        return true;
    }

    private function registerMetaAndColumnForEachThemes()
    {
        // insertion du meta pour prestablog
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
    INSERT INTO `' . bqSQL(_DB_PREFIX_) . 'meta`
    (`page`, `configurable`)
    VALUES
    (\'module-prestablog-blog\', 1)')) {
            return false;
        }

        $id_meta = (int) Db::getInstance()->Insert_ID();

        if (!Configuration::get('prestablog_id_meta')) {
            Configuration::updateValue('prestablog_id_meta', (int) $id_meta);
        }

        // insertion des meta_lang
        foreach (array_keys(Shop::getShops()) as $id_shop) {
            foreach (Language::getLanguages() as $lang) {
                if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
        INSERT INTO `' . bqSQL(_DB_PREFIX_) . 'meta_lang`
        (`id_meta`, `id_shop`, `id_lang`, `title`, `description`, `url_rewrite`)
        VALUES
        (
        ' . (int) $id_meta . ',
        ' . (int) $id_shop . ',
        ' . (int) $lang['id_lang'] . ',
        \'PrestaBlog\',
        \'Blog\',
        \'module-blog\')')) {
                    return false;
                }
            }
        }

        return true;
    }

    private function deleteMetaAndColumnForEachThemes($id_meta)
    {
        if ((int) $id_meta > 0) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
      DELETE FROM `' . bqSQL(_DB_PREFIX_) . 'meta`
      WHERE `id_meta` = ' . (int) $id_meta)) {
                return false;
            }

            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
      DELETE FROM `' . bqSQL(_DB_PREFIX_) . 'meta_lang`
      WHERE `id_meta` = ' . (int) $id_meta)) {
                return false;
            }

            if (!Configuration::deleteByName('prestablog_id_meta')) {
                return false;
            }
        }

        return true;
    }

    public function initLangueModule($id_lang)
    {
        $this->rss_langue['id_lang'] = $id_lang;
        $this->rss_langue['channel_title'] = Configuration::get('PS_SHOP_NAME') . ' ' . $this->trans('news feed', [], 'Modules.Prestablog.Prestablog');
    }

    public function registerAdminAjaxTab()
    {
        // Prepare tab AdminPrestaBlogAjaxController
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminPrestaBlogAjax';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'PrestaBlogAjax';
        }

        $tab->id_parent = (int) Tab::getCurrentTabId();
        $tab->module = $this->name;

        return $tab->add();
    }

    public function deleteAdminAjaxTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminPrestaBlogAjax');
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
        }
    }

    public function registerAdminChatGptTab()
    {
        $tabId = Tab::getIdFromClassName('AdminPrestaBlogChatGPT');
        if ($tabId) {
            return true; // tab is allready existing
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminPrestaBlogChatGPT';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'PrestaBlog ChatGPT';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentModules');
        $tab->module = $this->name;

        return $tab->add();
    }

    public function deleteAdminChatGptTab()
    {
        $tabId = Tab::getIdFromClassName('AdminPrestaBlogChatGPT');
        if ($tabId) {
            $tab = new Tab($tabId);

            return $tab->delete();
        }

        return true;
    }

    public function registerContentTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('Management');

        if (!$id_tab) {
            $tab = new Tab();
            $tab->class_name = 'Management';
            $tab->id_parent = 0;
            $tab->position = 6;
            $tab->module = '';
            $tab->icon = '';

            foreach (Language::getLanguages(false) as $lang) {
                $tab->name[$lang['id_lang']] = 'PrestaBlog';
            }

            return $tab->save();
        }

        return true;
    }

    public function deleteContentTab()
    {
        foreach (['Management'] as $tab_name) {
            $id_tab = (int) Tab::getIdFromClassName($tab_name);
            if ($id_tab) {
                $tab = new Tab($id_tab);
                $tab->delete();
            }
        }

        return true;
    }

    public function registerAdminTab()
    {
        $id_tab_prestablog = (int) Tab::getIdFromClassName('AdminPrestaBlog');
        if ($id_tab_prestablog) {
            $tab = new Tab($id_tab_prestablog);
        } else {
            $tab = new Tab();
        }

        $tab->class_name = 'AdminPrestaBlog';
        $tab->module = $this->name;
        $tab->id_parent = (int) Tab::getIdFromClassName('Management');
        $tab->position = 1;
        $tab->icon = 'library_books';
        $tab->active = 1;

        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = 'PrestaBlog';
        }

        $tab->save();

        $tabs = [
            [
                'class_name' => 'AdminPrestaBlogNewsList',
                'names' => [
                    'cs' => 'Seznam článků',
                    'da' => 'Artikeloversigt',
                    'de' => 'Artikelliste',
                    'en' => 'List of Articles',
                    'es' => 'Lista de artículos',
                    'fr' => 'Liste des articles',
                    'it' => 'Elenco degli articoli',
                    'pl' => 'Lista artykułów',
                    'pt' => 'Lista de artigos',
                    'ro' => 'Lista de articole',
                    'sv' => 'Lista över artiklar',
                ],
                'position' => 2,
                'icon' => 'list',
            ],
            [
                'class_name' => 'AdminPrestaBlogAddArticle',
                'names' => [
                    'cs' => 'Vytvořit článek',
                    'da' => 'Opret artikel',
                    'de' => 'Artikel erstellen',
                    'en' => 'Create an Article',
                    'es' => 'Crear un artículo',
                    'fr' => 'Créer un article',
                    'it' => 'Crea un articolo',
                    'pl' => 'Utwórz artykuł',
                    'pt' => 'Criar um artigo',
                    'ro' => 'Creează un articol',
                    'sv' => 'Skapa en artikel',
                ],
                'position' => 3,
                'icon' => 'add_circle',
            ],
            [
                'class_name' => 'AdminPrestaBlogCategories',
                'names' => [
                    'cs' => 'Přidat kategorii',
                    'da' => 'Tilføj en kategori',
                    'de' => 'Kategorie hinzufügen',
                    'en' => 'Add a Category',
                    'es' => 'Añadir una categoría',
                    'fr' => 'Ajouter une catégorie',
                    'it' => 'Aggiungi una categoria',
                    'pl' => 'Dodaj kategorię',
                    'pt' => 'Adicionar uma categoria',
                    'ro' => 'Adaugă o categorie',
                    'sv' => 'Lägg till en kategori',
                ],
                'position' => 4,
                'icon' => 'category',
            ],
            [
                'class_name' => 'AdminPrestaBlogComments',
                'names' => [
                    'cs' => 'Správa komentářů',
                    'da' => 'Administrer kommentarer',
                    'de' => 'Kommentare verwalten',
                    'en' => 'Manage Comments',
                    'es' => 'Gestionar comentarios',
                    'fr' => 'Gestion des commentaires',
                    'it' => 'Gestisci i commenti',
                    'pl' => 'Zarządzaj komentarzami',
                    'pt' => 'Gerenciar comentários',
                    'ro' => 'Gestionare comentarii',
                    'sv' => 'Hantera kommentarer',
                ],
                'position' => 5,
                'icon' => 'comment',
            ],
        ];

        foreach ($tabs as $tabInfo) {
            $id_subtab = (int) Tab::getIdFromClassName($tabInfo['class_name']);
            if ($id_subtab) {
                $subTab = new Tab($id_subtab);
            } else {
                $subTab = new Tab();
            }

            $subTab->class_name = $tabInfo['class_name'];
            $subTab->module = $this->name;
            $subTab->id_parent = (int) Tab::getIdFromClassName('AdminPrestaBlog');
            $subTab->position = $tabInfo['position'];
            $subTab->icon = $tabInfo['icon'];
            $subTab->active = 1;

            foreach (Language::getLanguages(true) as $lang) {
                $iso_code = $lang['iso_code'];
                if (isset($tabInfo['names'][$iso_code])) {
                    $subTab->name[$lang['id_lang']] = $tabInfo['names'][$iso_code];
                } else {
                    $subTab->name[$lang['id_lang']] = $tabInfo['names']['en'];
                }
            }

            $subTab->save();
        }

        return true;
    }

    public function deleteAdminTab()
    {
        $tabs = [
            'AdminPrestaBlog',
            'AdminPrestaBlogNewsList',
            'AdminPrestaBlogAddArticle',
            'AdminPrestaBlogCategories',
            'AdminPrestaBlogComments',
        ];

        foreach ($tabs as $tab_name) {
            $id_tab = (int) Tab::getIdFromClassName($tab_name);
            if ($id_tab) {
                $tab = new Tab($id_tab);
                $tab->delete();
            }
        }

        return true;
    }

    public function hookDisplayBeforeBodyClosingTag()
    {
        $prestaboost = Module::getInstanceByName('prestaboost');
        if (!$prestaboost) {
            $this->news = new NewsClass((int) Tools::getValue('id'), (int) $this->context->cookie->id_lang);
            if ($id_prestablog_popup = $this->isOkDisplay($this->news->id)) {
                $id_prestablog_popup = (int) PopupClass::getIdFrontPopupNewsPreFiltered($this->news->id);

                return $this->displayPopup((int) $this->context->language->id, (int) $id_prestablog_popup);
            }
            if ($id_prestablog_popup = $this->isOkDisplayCate($this->categories->id)) {
                $id_prestablog_popup = (int) PopupClass::getIdFrontPopupCatePreFiltered($this->categories->id);

                return $this->displayPopup((int) $this->context->language->id, (int) $id_prestablog_popup);
            }
            if ($id_prestablog_popup = $this->isOkDisplayHome()) {
                $popuplink = PopupClass::getPopupActifHome();
                $id_prestablog_popup = $popuplink[0]['id_prestablog_popup'];

                return $this->displayPopup((int) $this->context->language->id, (int) $id_prestablog_popup);
            }
        }
    }

    private static function unlinkFile($file)
    {
        if (file_exists($file)) {
            return unlink($file);
        }
    }

    private static function readDirectory($directory)
    {
        return readdir($directory);
    }

    private static function makeDirectory($directory)
    {
        return mkdir($directory);
    }

    public function install()
    {
        /*
        * si multiboutique, alors activer le contexte pour installe le module
        * sur toutes les boutiques
        */
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $news = new NewsClass();
        $categories = new CategoriesClass();
        $correspondances_categories = new CorrespondancesCategoriesClass();
        $comment_news = new CommentNewsClass();
        $antispam = new AntiSpamClass();
        $sub_blocks = new SubBlocksClass(null, null, null, $this->getTranslator());
        $popup = new PopupClass(null, null, null, $this->getTranslator());
        $slider = new SliderClass();
        $author = new AuthorClass();

        $this->installQuickAccess();

        if (!parent::install()
            // ACCROCHES TEMPLATE
          || !$this->registerHookPosition('displayHeader', 1)
          || !$this->registerHookPosition('displayHome', 1)
          || !$this->registerHook('displayTop')
          || !$this->registerHook('displaySlider')
          || !$this->registerHook('displayRating')
          || !$this->registerHookPosition('displayRightColumn', 1)
          || !$this->registerHookPosition('displayLeftColumn', 1)
          || !$this->registerHook('displayFooter')
          || !$this->registerHook('ModuleRoutes')
          || !$this->registerHook('displayPrestaBlogList')
          || !$this->registerHook('displayBeforeBodyClosingTag')

            // ACCROCHES TEMPLATE PRESTASHOP 1.7
          || !$this->installHookPS17()
            // CONFIGURATION & INTEGRATION BASE DE DONNEES
          || !$this->updateConfiguration('add')
          || !$this->metaTitlePageBlog('add')
            // STRUCTURE BASE DE DONNEES
          || !$news->registerTablesBdd()
          || !$categories->registerTablesBdd()
          || !$correspondances_categories->registerTablesBdd()
          || !$comment_news->registerTablesBdd()
          || !$antispam->registerTablesBdd()
          || !$sub_blocks->registerTablesBdd()
          || !$popup->createTables()
          || !$slider->registerTablesBdd()
          || !$author->registerTablesBdd()

            // ADMIN CONTROLLERS
          || !$this->registerContentTab()
          || !$this->registerAdminTab()
          || !$this->registerAdminAjaxTab()
          || !$this->registerAdminChatGptTab()

            // META LANG & THEME
          || !$this->registerMetaAndColumnForEachThemes()

            // || !$this->registerHook('displayTop')
            // || !$this->registerHook('displayBackOfficeHeader')
        ) {
            return false;
        }

        Tools::clearCache();

        return true;
    }

    public static function createDynInstance($class, $params = [])
    {
        $reflection_class = new ReflectionClass($class);

        return $reflection_class->newInstanceArgs($params);
    }

    public function displayContent()
    {
        if (Tools::getValue('class') && in_array(Tools::getValue('class'), $this->class_used['table'])) {
            $current_class = Tools::getValue('class');
            if (!class_exists($current_class)) {
                $this->html_out .= $this->displayError($this->trans('This class doesn\'t exists: ', [], 'Modules.Prestablog.Prestablog') . $current_class);
            } else {
                $object_model = self::createDynInstance($current_class, []);

                if (is_object($object_model)) {
                    $definition_lang = $object_model->definitionLang();
                    if (!Tools::isSubmit('add')
                && !Tools::isSubmit('edit')
                && !Tools::getIsset('add' . $definition_lang['tableName'])
                && !Tools::getIsset('update' . $definition_lang['tableName'])
                    ) {
                        $this->html_out .= $object_model->displayList();
                    }
                    if (Tools::getIsset('add' . $definition_lang['tableName']) || Tools::isSubmit('add')) {
                        $this->html_out .= $object_model->displayForm('add');
                    }
                    if (Tools::getIsset('update' . $definition_lang['tableName']) || Tools::isSubmit('edit')) {
                        $this->html_out .= $object_model->displayForm('edit');
                    }
                }
            }
        }
    }

    public function isOkDisplay($news)
    {
        $prestaboost = Module::getInstanceByName('prestaboost');
        if (!$prestaboost) {
            $id_prestablog_popup = (int) PopupClass::getIdFrontPopupNewsPreFiltered($news);

            if ((int) $id_prestablog_popup > 0) {
                $popup = new PopupClass((int) $id_prestablog_popup, (int) $this->context->language->id, null, $this->getTranslator());

                $popup_hash = md5(
                    $popup->id
            . $popup->date_start
            . $popup->date_stop
            . $popup->delay
            . $popup->expire
            . $popup->expire_ratio
            . $popup->theme
            . $popup->restriction_rules
            . $popup->restriction_pages
            . $popup->actif
            . $popup->footer
            . $popup->title
            . $popup->content
            . $popup->pop_colorpicker_content
            . $popup->pop_colorpicker_modal
            . $popup->pop_colorpicker_btn
            . $popup->pop_colorpicker_btn_border
            . $popup->pop_opacity_content
            . $popup->pop_opacity_modal
            . $popup->pop_opacity_btn
                );

                if (!isset($_COOKIE['PopupCookie' . $popup_hash]) && $popup->actif == 1) {
                    setcookie(
                        'PopupCookie' . $popup_hash,
                        '1',
                        time() + ((int) $popup->expire_ratio * (int) $popup->expire)
                    );
                    parent::_clearCache('header.tpl');

                    return (int) $id_prestablog_popup;
                }
            }
        }
    }

    public function isOkDisplayCate($categorie)
    {
        $prestaboost = Module::getInstanceByName('prestaboost');
        if (!$prestaboost) {
            $id_prestablog_popup = (int) PopupClass::getIdFrontPopupCatePreFiltered($categorie);

            if ((int) $id_prestablog_popup > 0) {
                $popup = new PopupClass((int) $id_prestablog_popup, (int) $this->context->language->id, null, $this->getTranslator());

                $popup_hash = md5(
                    $popup->id
            . $popup->date_start
            . $popup->date_stop
            . $popup->delay
            . $popup->expire
            . $popup->expire_ratio
            . $popup->theme
            . $popup->restriction_rules
            . $popup->restriction_pages
            . $popup->actif
            . $popup->footer
            . $popup->title
            . $popup->content
            . $popup->pop_colorpicker_content
            . $popup->pop_colorpicker_modal
            . $popup->pop_colorpicker_btn
            . $popup->pop_colorpicker_btn_border
            . $popup->pop_opacity_content
            . $popup->pop_opacity_modal
            . $popup->pop_opacity_btn
                );

                if (!isset($_COOKIE['PopupCookie' . $popup_hash]) && $popup->actif == 1) {
                    setcookie(
                        'PopupCookie' . $popup_hash,
                        '1',
                        time() + ((int) $popup->expire_ratio * (int) $popup->expire)
                    );
                    parent::_clearCache('header.tpl');

                    return (int) $id_prestablog_popup;
                }
            }
        }
    }

    public function isOkDisplayHome()
    {
        $prestaboost = Module::getInstanceByName('prestaboost');
        if (!$prestaboost) {
            $popuplink = PopupClass::getPopupActifHome();

            if (isset($popuplink[0])) {
                $id_prestablog_popup = $popuplink[0]['id_prestablog_popup'];

                if ((int) $id_prestablog_popup > 0) {
                    $popup = new PopupClass((int) $id_prestablog_popup, (int) $this->context->language->id, null, $this->getTranslator());

                    $popup_hash = md5(
                        $popup->id
              . $popup->date_start
              . $popup->date_stop
              . $popup->delay
              . $popup->expire
              . $popup->expire_ratio
              . $popup->theme
              . $popup->restriction_rules
              . $popup->restriction_pages
              . $popup->actif
              . $popup->footer
              . $popup->title
              . $popup->content
              . $popup->pop_colorpicker_content
              . $popup->pop_colorpicker_modal
              . $popup->pop_colorpicker_btn
              . $popup->pop_colorpicker_btn_border
              . $popup->pop_opacity_content
              . $popup->pop_opacity_modal
              . $popup->pop_opacity_btn
                    );

                    if (!isset($_COOKIE['PopupCookie' . $popup_hash]) && $popup->actif == 1) {
                        setcookie(
                            'PopupCookie' . $popup_hash,
                            '1',
                            time() + ((int) $popup->expire_ratio * (int) $popup->expire)
                        );
                        parent::_clearCache('header.tpl');

                        return (int) $id_prestablog_popup;
                    }
                }
            }
        }
    }

    public static function isNotRestrictionHome()
    {
        $url_current = Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url_split = array_slice(explode('/', $url_current), -1)[0];
        $return = null;

        if (Configuration::get('prestablog_urlblog') == false) {
            if ($url_split == 'blog') {
                $return = true;
            } else {
                $return = false;
            }
        } else {
            if ($url_split == Configuration::get('prestablog_urlblog')) {
                $return = true;
            } else {
                $return = false;
            }
        }

        return $return;
    }

    public static function isNotRestrictionCate($cate)
    {
        $url_current = Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url_split = explode('-', $url_current);
        $cate_id = (int) str_replace('c', '', $url_split[count($url_split) - 1]);

        $return = null;

        if ($cate_id == $cate) {
            $return = true;
        } else {
            $return = false;
        }

        return $return;
    }

    public static function isNotRestrictionNews($news)
    {
        $url_current = Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url_split = explode('-', $url_current);
        $id = str_replace('n', '', $url_split[count($url_split) - 1]);

        $return = null;

        if ($id == $news) {
            $return = true;
        } else {
            $return = false;
        }

        return $return;
    }

    public static function isNotRestrictionPage($restriction_rules, $restriction_pages)
    {
        $urls = preg_split("/\r/", $restriction_pages);
        $urls_ok = [];
        $restriction_rules = (int) $restriction_rules;

        $url_current = Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        foreach ($urls as $url) {
            $url = trim($url);
            if (preg_match('/^(http|https):\/\/*/i', $url)) {
                $urls_ok[] = $url;
            }
        }

        $return = null;

        switch ($restriction_rules) {
            case 0: // on all pages
                $return = true;
                break;

            case 1: // on all pages, except urls_ok
                if (in_array($url_current, $urls_ok)) {
                    $return = false;
                } else {
                    $return = true;
                }
                break;

            case 2: // only on pages in urls_ok
                if (in_array($url_current, $urls_ok)) {
                    $return = true;
                } else {
                    $return = false;
                }
                break;
        }

        return $return;
    }

    public static function popupContent($params)
    {
        if (isset($params['adminPreview']) && $params['adminPreview']) {
            return html_entity_decode($params['return'], ENT_QUOTES, 'UTF-8');
        }

        return $params['return'];
    }

    public $actions_form = ['addpopupsubmit', 'editpopupsubmit'];

    public $class_used = [
        'table' => [
            'PopupClass',
        ],
        'dashboard' => [],
        'config' => [],
    ];

    public function displayPopup($id_lang, $id_prestablog_popup = 0)
    {
        $prestaboost = Module::getInstanceByName('prestaboost');
        if (!$prestaboost) {
            $popup = new PopupClass((int) $id_prestablog_popup, (int) $id_lang, null, $this->getTranslator());

            $this->context->controller->addCSS($this->_path . 'views/css/theme-' . $popup->theme . '.css', 'all');

            $isAdmin = $this->context->controller instanceof AdminController;
            $this->smarty->assign([
                'adminPreview' => $isAdmin,
                'id_lang' => $id_lang,
                'Popup' => $popup,
            ]);

            if (!isset($this->context->smarty->registered_plugins['function']['PopupContent'])) {
                smartyRegisterFunction(
                    $this->context->smarty,
                    'function',
                    'PopupContent',
                    ['PopupClass', 'popupContent']
                );
            }

            parent::_clearCache($popup->theme . '.tpl');

            return $this->display(__FILE__, $popup->theme . '.tpl');
        }
    }

    public static function scanThemeTpl()
    {
        $return = [];

        foreach (glob(dirname(__FILE__) . '/views/templates/hook/*.{tpl}', GLOB_BRACE) as $file) {
            if (!is_dir($file)) {
                if (($file == dirname(__FILE__) . '/views/templates/hook/colorpicker.tpl') || ($file == dirname(__FILE__) . '/views/templates/hook/lite-popup.tpl')) {
                    $return[] = [
                        'id' => basename($file, '.tpl'),
                        'name' => basename($file, '.tpl'),
                    ];
                }
            }
        }

        return $return;
    }

    public function installHookPS17()
    {
        if (!$this->registerHook('displayNav')
                    || !$this->registerHook('displayNav2')
                    || !$this->registerHook('displayFooterProduct')
                    || !$this->registerHook('displayBackOfficeHeader')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        $news = new NewsClass();
        $categories = new CategoriesClass();
        $correspondances_categories = new CorrespondancesCategoriesClass();
        $comment_news = new CommentNewsClass();
        $antispam = new AntiSpamClass();
        $sub_blocks = new SubBlocksClass(null, null, null, $this->getTranslator());
        $popup = new PopupClass(null, null, null, $this->getTranslator());
        $author = new AuthorClass();
        $slider = new SliderClass();

        $this->uninstallQuickAccess();

        if (!parent::uninstall()
            // META LANG & THEME
                  || !$this->deleteMetaAndColumnForEachThemes((int) Configuration::get('prestablog_id_meta'))
            // CONFIGURATION & INTEGRATION BASE DE DONNEES
                  || !$this->updateConfiguration('del')
                  || !$this->metaTitlePageBlog('del')
            // STRUCTURE BASE DE DONNEES
                  || !$news->deleteTablesBdd()
                  || !$categories->deleteTablesBdd()
                  || !$correspondances_categories->deleteTablesBdd()
                  || !$comment_news->deleteTablesBdd()
                  || !$antispam->deleteTablesBdd()
                  || !$sub_blocks->deleteTablesBdd()
                  || !$popup->dropTables()
                  || !$author->deleteTablesBdd()
                  || !$slider->deleteTablesBdd()
            // ADMIN CONTROLLERS
                  || !$this->deleteContentTab()
                  || !$this->deleteAdminTab()
                  || !$this->deleteAdminAjaxTab()
                  || !$this->deleteAdminChatGptTab()
            // SITEMAPS
                  || !$this->deleteAllSitemap()
        ) {
            return false;
        }

        Tools::clearCache();

        return true;
    }

    public function installQuickAccess()
    {
        $qa = new QuickAccess();
        foreach (Language::getLanguages(true) as $language) {
            $qa->name[(int) $language['id_lang']] = $this->displayName;
        }
        $qa->link = 'index.php?controller=AdminModules&configure=prestablog&module_name=' . $this->name;
        $qa->new_window = 0;
        $qa->Add();
        Configuration::updateValue($this->name . '_QuickAccess', $qa->id);

        return true;
    }

    public function uninstallQuickAccess()
    {
        $qa = new QuickAccess((int) Configuration::get($this->name . '_QuickAccess'));
        $qa->delete();
        Configuration::deleteByName($this->name . '_QuickAccess');

        return true;
    }

    private function deleteAllSitemap()
    {
        $shops = Shop::getShops();
        foreach (array_keys($shops) as $key_shop) {
            $this->deleteSitemapFromShop((int) $key_shop);
        }

        return true;
    }

    private function updateConfiguration($action)
    {
        switch ($action) {
            case 'add':
                $shops = Shop::getShops();
                foreach (array_keys($shops) as $key_shop) {
                    foreach ($this->configurations as $configuration_key => $configuration_value) {
                        Configuration::updateValue($configuration_key, $configuration_value, false, null, $key_shop);
                    }
                }
                foreach ($this->configurations as $configuration_key => $configuration_value) {
                    Configuration::updateValue($configuration_key, $configuration_value);
                }
                break;

            case 'del':
                foreach ($this->configurations as $configuration_key => $configuration_value) {
                    Configuration::deleteByName($configuration_key);
                }
                break;
        }

        return true;
    }

    private function checkConfiguration()
    {
        foreach ($this->configurations as $configuration_key => $configuration_value) {
            if (!Configuration::getIdByName($configuration_key, null, (int) $this->context->shop->id)) {
                Configuration::updateValue(
                    $configuration_key,
                    $configuration_value,
                    false,
                    null,
                    (int) $this->context->shop->id
                );
            }
            if (!Configuration::getIdByName($configuration_key)) {
                Configuration::updateValue($configuration_key, $configuration_value);
            }
        }
    }

    private function metaTitlePageBlog($action)
    {
        $languages = Language::getLanguages(true);

        switch ($action) {
            case 'add':
                $languages = Language::getLanguages(true);

                $meta_title_config_lang = [];
                $meta_description_config_lang = [];
                $titre_h1_config_lang = [];

                foreach ($languages as $language) {
                    if (Configuration::get('prestablog_urlblog') == false) {
                        $meta_title_config_lang[(int) $language['id_lang']] = 'blog';
                    } else {
                        $meta_title_config_lang[(int) $language['id_lang']] = Configuration::get($this->name . '_urlblog');
                    }

                    $meta_description_config_lang[(int) $language['id_lang']] = '';
                    $titre_h1_config_lang[(int) $language['id_lang']] = '';
                }

                Configuration::updateValue($this->name . '_titlepageblog', $meta_title_config_lang);
                Configuration::updateValue($this->name . '_descpageblog', $meta_description_config_lang);
                Configuration::updateValue($this->name . '_h1pageblog', $titre_h1_config_lang);

                break;

            case 'del':
                $languages = Language::getLanguages();
                foreach ($languages as $language) {
                    Configuration::deleteByName($this->name . '_titlepageblog');
                    Configuration::deleteByName($this->name . '_descpageblog');
                    Configuration::deleteByName($this->name . '_h1pageblog');
                }
                break;
        }

        return true;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDisplaysliderModule';
        $helper->currentIndex = $this->confpath;
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create structure of creative slider form.
     */
    protected function getConfigForm()
    {
        $table = _DB_PREFIX_ . 'layerslider';
        $rows = Db::getInstance()->executeS("SELECT CONCAT('#', id, ' ', name) AS name, id FROM $table WHERE flag_deleted = 0");
        $options = [];
        for ($i = 0; isset($rows[$i]); ++$i) {
            $options[0] = ['name' => '- None -', 'id' => '0'];
            $options[$i + 1] = $rows[$i];
        }

        return [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings of Creative slider\'s slides', [], 'Modules.Prestablog.Prestablog'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->trans('Select a slider', [], 'Modules.Prestablog.Prestablog'),
                        'desc' => $this->trans('If you select a slider from Creative slider, please switch off the Prestablog\'s slideshow on the right side.', [], 'Modules.Prestablog.Prestablog'),
                        'name' => 'DISPLAYSLIDER_ID',
                        'options' => [
                            'name' => 'name',
                            'id' => 'id',
                            'query' => $options,
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Prestablog.Prestablog'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return ['DISPLAYSLIDER_ID' => Configuration::get('DISPLAYSLIDER_ID', 0)];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    private function postForm()
    {
        $errors = [];
        $post_en_cours = false;
        $config_theme = $this->getConfigXmlTheme(self::getT());
        $languages = Language::getLanguages();

        $this->check_slide = 0;
        $this->check_active = 0;

        if (Tools::getValue('submitFiltreNews')) {
            if (Tools::getValue('slide')) {
                $this->check_slide = 1;
            } else {
                $this->check_slide = 0;
            }
            if (Tools::getValue('activeNews')) {
                $this->check_active = 1;
            } else {
                $this->check_active = 0;
            }
        } else {
            if (Tools::getValue('slideget') == 1) {
                $this->check_slide = 1;
            } else {
                $this->check_slide = 0;
            }
            if (Tools::getValue('activeget') == 1) {
                $this->check_active = 1;
            } else {
                $this->check_active = 0;
            }
        }

        if (Tools::getValue('submitFiltreComment')) {
            $this->check_comment_state = Tools::getValue('activeComment');
        } else {
            if (Tools::getValue('activeCommentget')) {
                $this->check_comment_state = Tools::getValue('activeCommentget');
            } else {
                $this->check_comment_state = -2;
            }
        }

        $this->confpath .= '&activeget=' . $this->check_active;
        $this->confpath .= '&slideget=' . $this->check_slide;
        $this->confpath .= '&activeCommentget=' . $this->check_comment_state;
        if (Tools::isSubmit('deleteAuthor') && Tools::getValue('idA')) {
            AuthorClass::delAuthor((int) Tools::getValue('idA'));
            Tools::redirectAdmin($this->confpath . '&authorList');
        }
        if (Tools::isSubmit('deleteNews') && Tools::getValue('idN')) {
            // Permissions system
            $rulesAuthor = 'can_delete_article';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $news = new NewsClass((int) Tools::getValue('idN'));
            if (!$news->delete()) {
                $errors[] = $this->trans('An error occurred while delete news.', [], 'Modules.Prestablog.Prestablog');
            } else {
                $this->deleteAllImagesThemes((int) $news->id);
                CorrespondancesCategoriesClass::delAllCategoriesNews((int) $news->id);
                Tools::redirectAdmin($this->confpath . '&newsListe');
            }
        } elseif (Tools::isSubmit('deleteCat') && Tools::getValue('idC')) {
            // Permissions system
            $rulesAuthor = 'can_delete_category';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            $post_en_cours = true;
            $categorie = new CategoriesClass((int) Tools::getValue('idC'));
            if (!$categorie->delete()) {
                $errors[] = $this->trans('An error occurred while delete categorie.', [], 'Modules.Prestablog.Prestablog');
            } else {
                $this->deleteAllImagesThemesCat((int) $categorie->id);
                CorrespondancesCategoriesClass::delAllCorrespondanceNewsAfterDelCat((int) $categorie->id);
                SubBlocksClass::delAllCorrespondanceAfterDelCat((int) $categorie->id);
                Tools::redirectAdmin($this->confpath . '&catListe');
            }
        } elseif (Tools::isSubmit('deleteAntiSpam') && Tools::getValue('idAS')) {
            $post_en_cours = true;
            $antispam = new AntiSpamClass((int) Tools::getValue('idAS'));
            if (!$antispam->delete()) {
                $errors[] = $this->trans('An error occurred while delete antispam question.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&configAntiSpam');
            }
        } elseif (Tools::isSubmit('removeSlide') && Tools::getValue('idS') && Tools::getValue('idlang')) {
            // Permissions system
            $rulesAuthor = 'can_manage_slide';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            $post_en_cours = true;

            $img_jpg = _PS_MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/slider/' . Tools::getValue('idS') . '.jpg';
            $img_webp = _PS_MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/slider/' . Tools::getValue('idS') . '.webp';

            SliderClass::removeLang(Tools::getValue('idS'), Tools::getValue('idlang'));
            SliderClass::remove(Tools::getValue('idS'));

            if (!SliderClass::slideGetLang(Tools::getValue('idS'))) {
                if (file_exists($img_jpg)) {
                    unlink($img_jpg);
                }
                if (file_exists($img_webp)) {
                    unlink($img_webp);
                }
            }

            $languesup = Tools::getValue('languesup') ? '&languesup=' . (int) Tools::getValue('languesup') : '';
            Tools::redirectAdmin($this->confpath . '&configSlide&success' . $languesup);
        } elseif (Tools::isSubmit('etatNews') && Tools::getValue('idN')) {
            $post_en_cours = true;
            $news = new NewsClass((int) Tools::getValue('idN'));
            if (!$news->changeEtat('actif')) {
                $errors[] = $this->trans('An error occurred while change status of news.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&newsListe');
            }
        } elseif (Tools::isSubmit('slideNews') && Tools::getValue('idN')) {
            $post_en_cours = true;
            $news = new NewsClass((int) Tools::getValue('idN'));
            if (!$news->changeEtat('slide')) {
                $errors[] = $this->trans('An error occurred while change status of slide.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&newsListe');
            }
        } elseif (Tools::isSubmit('etatCat') && Tools::getValue('idC')) {
            $post_en_cours = true;
            $categories = new CategoriesClass((int) Tools::getValue('idC'));
            if (!$categories->changeEtat('actif')) {
                $errors[] = Tools::displayError('An error occurred while change status object.');
            } else {
                Tools::redirectAdmin($this->confpath . '&catListe');
            }
        } elseif (Tools::isSubmit('etatAntiSpam') && Tools::getValue('idAS')) {
            $post_en_cours = true;
            $antispam = new AntiSpamClass((int) Tools::getValue('idAS'));
            if (!$antispam->changeEtat('actif')) {
                $errors[] = $this->trans('An error occurred while change status of antispam question.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&configAntiSpam');
            }
        } if (Tools::isSubmit('submitAddAuthor')) {
            $employeeData = Tools::getValue('employees');
            $explode = explode('-', $employeeData);

            $id_employee = (int) $explode[0];

            if (filter_var($id_employee, FILTER_VALIDATE_INT) !== false) {
                $nameParts = explode(' ', $explode[1]);
                $firstname = pSQL($nameParts[0]);

                $lastnameAndEmail = explode('/', $nameParts[1]);
                $lastname = pSQL($lastnameAndEmail[0]);
                $email = pSQL($lastnameAndEmail[1]);

                if (AuthorClass::addAuthor($id_employee, $firstname, $lastname, $email)) {
                    $this->context->controller->confirmations[] = $this->l('Author added successfully.');
                } else {
                    $this->context->controller->errors[] = $this->l('Failed to add author.');
                }
            } else {
                $this->context->controller->errors[] = $this->l('Invalid employee selection.');
            }

            Tools::redirectAdmin($this->confpath . '&authorList');
        } elseif (Tools::isSubmit('submitAuthorPermissions')) {
            $authorId = (int) Tools::getValue('id_author');

            $permissions = [
                'can_add_article' => (int) Tools::getValue('can_add_article', 0),
                'can_edit_article' => (int) Tools::getValue('can_edit_article', 0),
                'can_delete_article' => (int) Tools::getValue('can_delete_article', 0),
                'can_activate_article' => (int) Tools::getValue('can_activate_article', 0),
                'can_create_category' => (int) Tools::getValue('can_create_category', 0),
                'can_delete_category' => (int) Tools::getValue('can_delete_category', 0),
                'can_manage_comments' => (int) Tools::getValue('can_manage_comments', 0),
                'can_manage_popup' => (int) Tools::getValue('can_manage_popup', 0),
                'can_manage_slide' => (int) Tools::getValue('can_manage_slide', 0),
                'can_manage_personalised_list' => (int) Tools::getValue('can_manage_personalised_list', 0),
                'can_configure_module' => (int) Tools::getValue('can_configure_module', 0),
                'can_use_tool' => (int) Tools::getValue('can_use_tool', 0),
            ];

            if (AuthorClass::checkAuthor($authorId)) {
                $author = new AuthorClass($authorId);
                $author->permissions = json_encode($permissions);

                if ($author->update()) {
                    $this->context->controller->confirmations[] = $this->trans('Settings updated successfully', [], 'Modules.Prestablog.Prestablog');
                } else {
                    $this->context->controller->errors[] = $this->trans('Failed to update permissions', [], 'Modules.Prestablog.Prestablog');
                }
            } else {
                $this->context->controller->errors[] = $this->trans('Author not found', [], 'Modules.Prestablog.Prestablog');
            }

            Tools::redirectAdmin($this->confpath . '&authorPermissions&id_author=' . $authorId . '&success=1');
        } elseif (Tools::isSubmit('submitAddSlide')) {
            // Permissions system
            $rulesAuthor = 'can_manage_slide';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            $post_en_cours = true;
            $title = Tools::getValue('title');
            $url_associate = Tools::getValue('url_associate');
            $lang = Tools::getValue('id_lang');

            if (empty($title)) {
                Tools::redirectAdmin($this->confpath . '&addSlide&languesup=' . (int) $lang . '&error=title');

                return;
            }

            $uploadOk = 1;

            if (isset($_FILES['load_img_slide']) && $_FILES['load_img_slide']['name'] != '') {
                $check = getimagesize($_FILES['load_img_slide']['tmp_name']);
                $mime_type = $check['mime']; // Get the MIME type of the image

                $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                $file_extension = pathinfo($_FILES['load_img_slide']['name'], PATHINFO_EXTENSION);

                if ($check[0] > (int) Configuration::get('prestablog_slide_picture_width') || $check[1] > (int) Configuration::get('prestablog_slide_picture_height')) {
                    $uploadOk = 0;
                    Tools::redirectAdmin($this->confpath . '&addSlide&languesup=' . (int) $lang . '&error=size');

                    return;
                }

                if (!in_array($mime_type, $allowed_mime_types)) {
                    $uploadOk = 0;
                    Tools::redirectAdmin($this->confpath . '&addSlide&languesup=' . (int) $lang . '&error=image');

                    return;
                }

                if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                    $uploadOk = 0;
                    Tools::redirectAdmin($this->confpath . '&addSlide&languesup=' . (int) $lang . '&error=image');

                    return;
                }

                if ($uploadOk) {
                    $slider = new SliderClass();
                    $slider->id_shop = (int) $this->context->shop->id;
                    $slider->langues = json_encode(Tools::getValue('languesup'));

                    if ($slider->addTableSlide($slider->id_shop)) {
                        $id = SliderClass::getIdLastSlide();

                        if ($id) {
                            if ($lang != 'all') {
                                $slider->addTableSlideLang($title, $url_associate, $lang);
                            } else {
                                $languages = Language::getLanguages(true);
                                foreach ($languages as $language) {
                                    $slider->addTableSlideLang($title, $url_associate, $language['id_lang']);
                                }
                            }

                            if (isset($_FILES['load_img_slide']) && $_FILES['load_img_slide']['name'] != '') {
                                $target_dir = _MODULE_DIR_ . $this->name . '/views/img/' . PrestaBlog::getT() . '/slider/';
                                $target_file = $target_dir . 'slider_' . $id;

                                $this->uploadImageSlide($_FILES['load_img_slide'], $id, $check[0], $check[1]);
                            }

                            $languesup = Tools::getValue('languesup') ? '&languesup=' . (int) Tools::getValue('languesup') : '';
                            Tools::redirectAdmin($this->confpath . '&configSlide&success' . $languesup);

                            return;
                        } else {
                            Tools::redirectAdmin($this->confpath . '&addSlide&error=add');

                            return;
                        }
                    } else {
                        Tools::redirectAdmin($this->confpath . '&addSlide&error=add');

                        return;
                    }
                }
            }
        } elseif (Tools::isSubmit('submitEditSlide')) {
            // Permissions system
            $rulesAuthor = 'can_manage_slide';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $lang = Tools::getValue('id_lang');
            $title = Tools::getValue('title');
            $position = Tools::getValue('position');
            $url_associate = Tools::getValue('url_associate');
            $id_slide = Tools::getValue('id_slide');
            $old_lang = Tools::getValue('old_lang');

            if (empty($title)) {
                Tools::redirectAdmin($this->confpath . '&editSlide&idS=' . (int) $id_slide . '&languesup=' . (int) $lang . '&error=title');

                return;
            }

            $uploadOk = 1;

            if (isset($_FILES['load_img_slide']) && $_FILES['load_img_slide']['name'] != '') {
                $check = getimagesize($_FILES['load_img_slide']['tmp_name']);
                $mime_type = $check['mime'];

                $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                $file_extension = pathinfo($_FILES['load_img_slide']['name'], PATHINFO_EXTENSION);

                if ($check[0] > (int) Configuration::get('prestablog_slide_picture_width') || $check[1] > (int) Configuration::get('prestablog_slide_picture_height')) {
                    $uploadOk = 0;
                    Tools::redirectAdmin($this->confpath . '&editSlide&idS=' . (int) $id_slide . '&languesup=' . (int) $lang . '&error=size');

                    return;
                }

                if (!in_array($mime_type, $allowed_mime_types)) {
                    $uploadOk = 0;
                    Tools::redirectAdmin($this->confpath . '&editSlide&idS=' . (int) $id_slide . '&languesup=' . (int) $lang . '&error=image');

                    return;
                }

                if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                    $uploadOk = 0;
                    Tools::redirectAdmin($this->confpath . '&editSlide&idS=' . (int) $id_slide . '&languesup=' . (int) $lang . '&error=image');

                    return;
                }

                if ($uploadOk) {
                    @unlink(_MODULE_DIR_ . $this->name . '/views/img/' . PrestaBlog::getT() . '/slider/slider_' . $id_slide . '.jpg');
                    @unlink(_MODULE_DIR_ . $this->name . '/views/img/' . PrestaBlog::getT() . '/slider/slider_' . $id_slide . '.png');
                    @unlink(_MODULE_DIR_ . $this->name . '/views/img/' . PrestaBlog::getT() . '/slider/slider_' . $id_slide . '.jpeg');
                    @unlink(_MODULE_DIR_ . $this->name . '/views/img/' . PrestaBlog::getT() . '/slider/slider_' . $id_slide . '.gif');
                    @unlink(_MODULE_DIR_ . $this->name . '/views/img/' . PrestaBlog::getT() . '/slider/slider_' . $id_slide . '.webp');

                    $target_dir = _MODULE_DIR_ . $this->name . '/views/img/' . PrestaBlog::getT() . '/slider/';
                    $this->uploadImageSlide($_FILES['load_img_slide'], $id_slide, $check[0], $check[1]);
                }
            } else {
                // Check if WebP exists and create it if missing
                $this->checkAndCreateWebP($id_slide);
            }

            SliderClass::updateDatas($id_slide, $lang, $old_lang, $title, $url_associate, $position);

            Tools::redirectAdmin($this->confpath . '&configSlide&languesup=' . (int) $lang . '&success');
        } elseif (Tools::isSubmit('submitEditAuthor')) {
            $author_id = Tools::getValue('author_id');
            $pseudo = Tools::getValue('pseudo');
            $bio = Tools::getValue('biography');
            $email = Tools::getValue('email');
            $meta_title = Tools::getValue('meta_title');
            $meta_description = Tools::getValue('meta_description');

            if (isset($_FILES['load_img']) && $_FILES['load_img']['name'] != '') {
                $target_dir = _MODULE_DIR_ . $this->name . '/views/img/author_th/';
                $target_file = $target_dir . 'author_' . $author_id;
                $uploadOk = 1;
                $imageFileType = $_FILES['load_img']['type'];
                // Check if image file is a actual image or fake image
                if (Tools::isSubmit('submitEditAuthor')) {
                    $check = getimagesize($_FILES['load_img']['tmp_name']);

                    if ($check !== false) {
                        $uploadOk = 1;
                    } else {
                        echo 'File is not an image.';
                        $uploadOk = 0;
                    }
                }
                // Check if file already exists
                if (file_exists($target_file)) {
                    echo 'Sorry, file already exists.';
                    $uploadOk = 0;
                }

                // Allow certain file formats
                if ($imageFileType != 'image/jpg' && $imageFileType != 'image/png' && $imageFileType != 'image/jpeg'
      && $imageFileType != 'image/gif') {
                    $uploadOk = 0;
                } elseif ($check[0] > (int) Configuration::get('prestablog_author_pic_width') || $check[1] > (int) Configuration::get('prestablog_author_pic_height')) {
                    $uploadOk = 0;
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    $info = $this->trans('Sorry, your file was not uploaded. Your image needs to be less than ', [], 'Modules.Prestablog.Prestablog');
                    $info .= (int) Configuration::get('prestablog_author_pic_width');
                    $info .= $this->trans(' px width and ', [], 'Modules.Prestablog.Prestablog');
                    $info .= (int) Configuration::get('prestablog_author_pic_height');
                    $info .= $this->trans(' px height', [], 'Modules.Prestablog.Prestablog');

                    $errors[] = $info;
                } else {
                    $this->uploadImageAdmin(
                        $_FILES['load_img'],
                        $author_id,
                        $check[0],
                        $check[1]
                    );

                    foreach (self::scanListeThemes() as $theme) {
                        $config_theme = $this->getConfigXmlTheme($theme);
                        $this->imageResize(
                            self::imgPath() . $theme . '/author_th/' . $author_id . '.jpg',
                            self::imgPath() . $theme . '/author_th/author_img_' . $author_id . '.jpg',
                            (int) $this->admin_crop_image_size_width,
                            (int) $this->admin_crop_image_size_height
                        );
                        $this->autocropImage(
                            $author_id . '.jpg',
                            self::imgPath() . $theme . '/author_th/',
                            self::imgPath() . $theme . '/author_th/',
                            (int) $this->admin_thumb_image_size_width,
                            (int) $this->admin_thumb_image_size_height,
                            'authorth_',
                            null
                        );
                    }
                }
            }

            if (!count($errors)) {
                AuthorClass::editAuthor($author_id, $pseudo, $bio, $email, $meta_title, $meta_description);
                Tools::redirectAdmin($this->confpath . '&authorList&success=au');
            } else {
                AuthorClass::editAuthor($author_id, $pseudo, $bio, $email, $meta_title, $meta_description);
                Tools::redirectAdmin($this->confpath . '&accountGest&error');
            }
        } elseif (Tools::isSubmit('submitAddNews')) {
            $post_en_cours = true;
            $rulesAuthor = 'can_add_article';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');
                return;
            }

            $rulesActivate = 'can_activate_article';
            $resultActivate = $this->loadAuthorAndCheckPermissions($rulesActivate);

            if (Configuration::get($this->name . '_enable_permissions') != 1) {
                $permissionActivate = true;
            } else {
                $permissionActivate = ($resultActivate && isset($resultActivate['permissions'][$rulesActivate]) && $resultActivate['permissions'][$rulesActivate] == 1);
            }

            if (!count(Tools::getValue('languesup'))) {
                $errors[] = $this->trans('You must activate at least one language', [], 'Modules.Prestablog.Prestablog');
            } elseif (!Tools::getValue('categories')) {
                $errors[] = $this->trans('You must choose at least one categorie', [], 'Modules.Prestablog.Prestablog');
            } else {
                foreach ($languages as $language) {
                    if (!Tools::getValue('title_' . $language['id_lang'])
        && in_array($language['id_lang'], Tools::getValue('languesup'))
                    ) {
                        $errors[] = $this->trans('The title must be specified', [], 'Modules.Prestablog.Prestablog') . " ($language[name])";
                    }
                    if (!Tools::getValue('link_rewrite_' . $language['id_lang'])
      && in_array($language['id_lang'], Tools::getValue('languesup'))
                    ) {
                        $errors[] = $this->trans('The url rewrite must be specified', [], 'Modules.Prestablog.Prestablog') . " ($language[name])";
                    }

                    $summary = Tools::getValue('paragraph_' . $language['id_lang']);
                    $content = Tools::getValue('content_' . $language['id_lang']);

                    if (!$summary
                    && !$content
                    && in_array($language['id_lang'], Tools::getValue('languesup'))
                    ) {
                        $errors[] = $this->trans('The content or introduction must be specified', [], 'Modules.Prestablog.Prestablog') . " ($language[name])";
                    }
                }
            }

            if (!count($errors)) {
                $news = new NewsClass();
                $news->id_shop = (int) $this->context->shop->id;
                $news->copyFromPost();
                if (!$permissionActivate) {
                    $news->actif = 0;
                }
                $news->langues = json_encode(Tools::getValue('languesup'));
                if (!$news->add()) {
                    $errors[] = $this->trans('An error occurred while add object.', [], 'Modules.Prestablog.Prestablog');
                }

                NewsClass::removeAllProductsLinkNews((int) $news->id);
                if (Tools::getValue('productsLink')) {
                    foreach (Tools::getValue('productsLink') as $product_link) {
                        NewsClass::updateProductLinkNews((int) $news->id, (int) $product_link);
                    }
                }

                NewsClass::removeAllArticlesLinkNews((int) $news->id);
                if (Tools::getValue('articlesLink')) {
                    foreach (Tools::getValue('articlesLink') as $article_link) {
                        NewsClass::updateArticleLinkNews((int) $news->id, (int) $article_link);
                    }
                }
                NewsClass::removeAllPopupLinkNews((int) $news->id);

                if (Tools::getValue('popupLink')) {
                    NewsClass::updatePopupLinkNews((int) $news->id, Tools::getValue('popupLink'));
                }

                $news->razEtatLangue((int) $news->id);
                foreach ($languages as $language) {
                    if (in_array($language['id_lang'], Tools::getValue('languesup'))) {
                        $news->changeActiveLangue((int) $news->id, (int) $language['id_lang']);
                    }
                }
                if (!$this->demo_mode) {
                    if ($_FILES['homepage_logo']['name']) {
                        if (!$this->uploadImage(
                            $_FILES['homepage_logo'],
                            $news->id,
                            $this->normal_image_size_width,
                            $this->normal_image_size_height
                        )) {
                            $errors[] = $this->trans('An error occurred while upload image.', [], 'Modules.Prestablog.Prestablog');
                        } else {
                            foreach (self::scanListeThemes() as $theme) {
                                $config_theme = $this->getConfigXmlTheme($theme);
                                $this->imageResize(
                                    self::imgPath() . $theme . '/up-img/' . $news->id . '.jpg',
                                    self::imgPath() . $theme . '/up-img/admincrop_' . $news->id . '.jpg',
                                    (int) $this->admin_crop_image_size_width,
                                    (int) $this->admin_crop_image_size_height
                                );

                                $this->autocropImage(
                                    $news->id . '.jpg',
                                    self::imgPath() . $theme . '/up-img/',
                                    self::imgPath() . $theme . '/up-img/',
                                    (int) $this->admin_thumb_image_size_width,
                                    (int) $this->admin_thumb_image_size_height,
                                    'adminth_',
                                    null
                                );

                                $config_theme_array = PrestaBlog::objectToArray($config_theme);
                                foreach ($config_theme_array['images'] as $key_theme_array => $value_theme_array) {
                                    $this->autocropImage(
                                        $news->id . '.jpg',
                                        self::imgPath() . $theme . '/up-img/',
                                        self::imgPath() . $theme . '/up-img/',
                                        (int) $value_theme_array['width'],
                                        (int) $value_theme_array['height'],
                                        $key_theme_array . '_',
                                        null
                                    );
                                }
                            }
                        }
                    }
                }

                if (Tools::getValue('author_id') && AuthorClass::verifyAuthorSet(Tools::getValue('author_id')) == true) {
                    NewsClass::updateAuthorId((int) $news->id, Tools::getValue('author_id'));
                }
                if (!count($errors)) {
                    if (!Tools::getValue('categories')) {
                        CorrespondancesCategoriesClass::delAllCategoriesNews($news->id);
                    } else {
                        CorrespondancesCategoriesClass::delAllCategoriesNews($news->id);
                        CorrespondancesCategoriesClass::updateCategoriesNews(Tools::getValue('categories'), $news->id);
                    }

                    Tools::redirectAdmin($this->confpath . '&newsListe');
                }
            }
        } elseif (Tools::isSubmit('submitAddCat')) {
            $post_en_cours = true;

            if (!Tools::getValue('title_' . $this->langue_default_store)) {
                $errors[] = $this->trans('The title must be specified', [], 'Modules.Prestablog.Prestablog');
            }

            $categories = new CategoriesClass();
            $categories->id_shop = (int) $this->context->shop->id;
            $categories->copyFromPost();
            $categories->position = (int) $categories->getLastPosition();

            if (!count($errors)) {
                if (!$categories->add()) {
                    $errors[] = $this->trans('An error occurred while add object.', [], 'Modules.Prestablog.Prestablog');
                } else {
                    if (!CategoriesClass::injectGroupsInCategorie(Tools::getValue('groupBox'), (int) $categories->id)) {
                        $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog') . ' - ' . $this->trans('Groups', [], 'Modules.Prestablog.Prestablog');
                    }

                    if (!$this->demo_mode) {
                        if ($_FILES['imageCategory']['name']) {
                            if (!$this->uploadImage(
                                $_FILES['imageCategory'],
                                $categories->id,
                                $this->normal_image_size_width,
                                $this->normal_image_size_height,
                                'c'
                            )) {
                                $errors[] = $this->trans('An error occurred while upload image.', [], 'Modules.Prestablog.Prestablog');
                            } else {
                                foreach (self::scanListeThemes() as $theme) {
                                    $this->imageResize(
                                        self::imgPath() . $theme . '/up-img/c/' . $categories->id . '.jpg',
                                        self::imgPath() . $theme . '/up-img/c/admincrop_' . $categories->id . '.jpg',
                                        (int) $this->admin_crop_image_size_width,
                                        (int) $this->admin_crop_image_size_height
                                    );

                                    $this->autocropImage(
                                        $categories->id . '.jpg',
                                        self::imgPath() . $theme . '/up-img/c/',
                                        self::imgPath() . $theme . '/up-img/c/',
                                        (int) $this->admin_thumb_image_size_width,
                                        (int) $this->admin_thumb_image_size_height,
                                        'adminth_',
                                        null
                                    );

                                    $config_theme_array = PrestaBlog::objectToArray($config_theme);
                                    foreach ($config_theme_array['categories'] as $kta => $vta) {
                                        $this->autocropImage(
                                            $categories->id . '.jpg',
                                            self::imgPath() . $theme . '/up-img/c/',
                                            self::imgPath() . $theme . '/up-img/c/',
                                            (int) $vta['width'],
                                            (int) $vta['height'],
                                            $kta . '_',
                                            null
                                        );
                                    }
                                }
                            }
                        }
                    }
                    CategoriesClass::removeAllPopupLinkCategorie((int) $categories->id);
                    if (Tools::getValue('popupLinkCate')) {
                        CategoriesClass::updatePopupLinkCategorie((int) $categories->id, (int) Tools::getValue('popupLinkCate'));
                    }
                }
            }

            if (!count($errors)) {
                Tools::redirectAdmin($this->confpath . '&catListe');
            }
        } elseif (Tools::isSubmit('submitAddAntiSpam')) {
            $post_en_cours = true;

            if (!Tools::getValue('question_' . $this->langue_default_store)) {
                $errors[] = $this->trans('The question must be specified', [], 'Modules.Prestablog.Prestablog');
            }
            if (!Tools::getValue('reply_' . $this->langue_default_store)) {
                $errors[] = $this->trans('The reply must be specified', [], 'Modules.Prestablog.Prestablog');
            }

            if (!count($errors)) {
                $antispam = new AntiSpamClass();
                $antispam->id_shop = (int) $this->context->shop->id;
                $antispam->copyFromPost();

                if (!$antispam->add()) {
                    $errors[] = $this->trans('An error occurred while add object.', [], 'Modules.Prestablog.Prestablog');
                } else {
                    $antispam->reloadChecksum();
                    Tools::redirectAdmin($this->confpath . '&configAntiSpam');
                }
            }
        } elseif (Tools::isSubmit('etatSubBlock') && Tools::getValue('idSB')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSB'), null, null, $this->getTranslator());
            if (!$sub_blocks->changeEtat('actif')) {
                $errors[] = $this->trans('An error occurred while change status of custom articles list.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
            }
        } elseif (Tools::isSubmit('randSubBlock') && Tools::getValue('idSB')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSB'), null, null, $this->getTranslator());
            if (!$sub_blocks->changeEtat('random')) {
                $errors[] = $this->trans('An error occurred while change random status of custom articles list.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
            }
        } elseif (Tools::isSubmit('blog_linkSubBlock') && Tools::getValue('idSB')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            $post_en_cours = true;
            $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSB'), null, null, $this->getTranslator());
            if (!$sub_blocks->changeEtat('blog_link')) {
                $errors[] = $this->trans('An error occurred while change random status of custom articles list.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
            }
        } elseif (Tools::isSubmit('etatSubBlockFront') && Tools::getValue('idSBF')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSBF'), null, null, $this->getTranslator());
            if (!$sub_blocks->changeEtat('actif')) {
                $errors[] = $this->trans('An error occurred while change status of custom articles list.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
            }
        } elseif (Tools::isSubmit('randSubBlockFront') && Tools::getValue('idSBF')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            $post_en_cours = true;
            $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSBF'), null, null, $this->getTranslator());
            if (!$sub_blocks->changeEtat('random')) {
                $errors[] = $this->trans('An error occurred while change random status of custom articles list.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
            }
        } elseif (Tools::isSubmit('blog_linkSubBlockFront') && Tools::getValue('idSBF')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            $post_en_cours = true;
            $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSBF'), null, null, $this->getTranslator());
            if (!$sub_blocks->changeEtat('blog_link')) {
                $errors[] = $this->trans('An error occurred while change random status of custom articles list.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
            }
        } elseif (Tools::isSubmit('submitAddSubBlock')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;

            if (!count(Tools::getValue('languesup'))) {
                $errors[] = $this->trans('You must activate at least one language', [], 'Modules.Prestablog.Prestablog');
            } else {
                foreach ($languages as $language) {
                    if (!Tools::getValue('title_' . $language['id_lang'])
                    && in_array($language['id_lang'], Tools::getValue('languesup'))
                    ) {
                        $errors[] = $this->trans('The title must be specified', [], 'Modules.Prestablog.Prestablog') . " ($language[name])";
                    }
                }
            }

            if (!count($errors)) {
                $sub_blocks = new SubBlocksClass(null, null, null, $this->getTranslator());
                $sub_blocks->id_shop = (int) $this->context->shop->id;
                $sub_blocks->copyFromPost();
                $sub_blocks->langues = json_encode(Tools::getValue('languesup'));

                $sub_blocks->position = (int) $sub_blocks->getLastPosition();

                if (!$sub_blocks->add()) {
                    $errors[] = $this->trans('An error occurred while add object.', [], 'Modules.Prestablog.Prestablog');
                } else {
                    if (!Tools::getValue('categories')) {
                        SubBlocksClass::delAllCategories($sub_blocks->id);
                    } else {
                        SubBlocksClass::delAllCategories($sub_blocks->id);
                        SubBlocksClass::updateCategories(Tools::getValue('categories'), $sub_blocks->id);
                    }
                    Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
                }
            }
        } elseif (Tools::isSubmit('submitAddSubBlockFront')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;

            if (!count(Tools::getValue('languesup'))) {
                $errors[] = $this->trans('You must activate at least one language', [], 'Modules.Prestablog.Prestablog');
            } else {
                foreach ($languages as $language) {
                    if (!Tools::getValue('title_' . $language['id_lang'])
                  && in_array($language['id_lang'], Tools::getValue('languesup'))
                    ) {
                        $errors[] = $this->trans('The title must be specified', [], 'Modules.Prestablog.Prestablog') . " ($language[name])";
                    }
                }
            }
            if (!count($errors)) {
                $sub_blocks = new SubBlocksClass(null, null, null, $this->getTranslator());
                $sub_blocks->id_shop = (int) $this->context->shop->id;
                $sub_blocks->copyFromPost();
                $sub_blocks->langues = json_encode(Tools::getValue('languesup'));
                $sub_blocks->position = (int) $sub_blocks->getLastPosition();

                if (!$sub_blocks->add()) {
                    $errors[] = $this->trans('An error occurred while add object.', [], 'Modules.Prestablog.Prestablog');
                } else {
                    if (!Tools::getValue('categories')) {
                        SubBlocksClass::updateSubBlock($sub_blocks->id);
                        SubBlocksClass::delAllCategories($sub_blocks->id);
                    } else {
                        SubBlocksClass::updateSubBlock($sub_blocks->id);
                        SubBlocksClass::delAllCategories($sub_blocks->id);
                        SubBlocksClass::updateCategories(Tools::getValue('categories'), $sub_blocks->id);
                    }
                    Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
                }
            }
        } elseif (Tools::isSubmit('submitUpdateSubBlock') && Tools::getValue('idSB')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;

            if (!count(Tools::getValue('languesup'))) {
                $errors[] = $this->trans('You must activate at least one language', [], 'Modules.Prestablog.Prestablog');
            } else {
                foreach ($languages as $language) {
                    if (!Tools::getValue('title_' . $language['id_lang'])
                && in_array($language['id_lang'], Tools::getValue('languesup'))
                    ) {
                        $errors[] = $this->trans('The title must be specified', [], 'Modules.Prestablog.Prestablog') . " ($language[name])";
                    }
                }
            }

            if (!count($errors)) {
                $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSB'), null, null, $this->getTranslator());
                $sub_blocks->copyFromPost();
                $sub_blocks->langues = json_encode(Tools::getValue('languesup'));

                if (!$sub_blocks->update()) {
                    $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog');
                }

                if (!count($errors)) {
                    if (!Tools::getValue('categories')) {
                        SubBlocksClass::delAllCategories($sub_blocks->id);
                    } else {
                        SubBlocksClass::delAllCategories($sub_blocks->id);
                        SubBlocksClass::updateCategories(Tools::getValue('categories'), $sub_blocks->id);
                    }
                }
            }
        } elseif (Tools::isSubmit('submitUpdateSubBlockFront') && Tools::getValue('idSBF')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;

            if (!count(Tools::getValue('languesup'))) {
                $errors[] = $this->trans('You must activate at least one language', [], 'Modules.Prestablog.Prestablog');
            } else {
                foreach ($languages as $language) {
                    if (!Tools::getValue('title_' . $language['id_lang'])
              && in_array($language['id_lang'], Tools::getValue('languesup'))
                    ) {
                        $errors[] = $this->trans('The title must be specified', [], 'Modules.Prestablog.Prestablog') . " ($language[name])";
                    }
                }
            }

            if (!count($errors)) {
                $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSBF'), null, null, $this->getTranslator());
                $sub_blocks->copyFromPost();
                $sub_blocks->langues = json_encode(Tools::getValue('languesup'));

                if (!$sub_blocks->update()) {
                    $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog');
                }

                if (!count($errors)) {
                    if (!Tools::getValue('categories')) {
                        SubBlocksClass::delAllCategories($sub_blocks->id);
                    } else {
                        SubBlocksClass::delAllCategories($sub_blocks->id);
                        SubBlocksClass::updateCategories(Tools::getValue('categories'), $sub_blocks->id);
                    }
                }
            }
        } elseif (Tools::isSubmit('deleteSubBlock') && Tools::getValue('idSB')) {
            // Permissions system
            $rulesAuthor = 'can_manage_personalised_list';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSB'), null, null, $this->getTranslator());
            if (!$sub_blocks->delete()) {
                $errors[] = $this->trans('An error occurred while delete object.', [], 'Modules.Prestablog.Prestablog');
            } else {
                SubBlocksClass::delAllCategories((int) $sub_blocks->id);
                Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
            }
        } elseif (Tools::isSubmit('addProductLink') && Tools::getValue('idN') && Tools::getValue('idP')) {
            $post_en_cours = true;

            NewsClass::updateProductLinkNews((int) Tools::getValue('idN'), (int) Tools::getValue('idP'));

            if (!count($errors)) {
                Tools::redirectAdmin($this->confpath . '&editNews&idN=' . Tools::getValue('idN') . '#productLinkTable');
            }
        } elseif (Tools::isSubmit('removeProductLink') && Tools::getValue('idN') && Tools::getValue('idP')) {
            $post_en_cours = true;

            NewsClass::removeProductLinkNews((int) Tools::getValue('idN'), (int) Tools::getValue('idP'));

            if (!count($errors)) {
                Tools::redirectAdmin($this->confpath . '&editNews&idN=' . Tools::getValue('idN') . '#productLinkTable');
            }
        } elseif (Tools::isSubmit('addPopupLink') && Tools::getValue('idN') && Tools::getValue('idP')) {
            $post_en_cours = true;

            NewsClass::updatePopupLinkNews((int) Tools::getValue('idN'), (int) Tools::getValue('idP'));

            if (!count($errors)) {
                Tools::redirectAdmin($this->confpath . '&editNews&idN=' . Tools::getValue('idN') . '#popupLinkTable');
            }
        } elseif (Tools::isSubmit('removePopupLink') && Tools::getValue('idN') && Tools::getValue('idP')) {
            $post_en_cours = true;

            NewsClass::removePopupLinkNews((int) Tools::getValue('idN'), (int) Tools::getValue('idP'));

            if (!count($errors)) {
                Tools::redirectAdmin($this->confpath . '&editNews&idN=' . Tools::getValue('idN') . '#popupLinkTable');
            }
        } elseif (Tools::isSubmit('submitUpdateNews') && Tools::getValue('idN')) {
            // Permissions system
            $rulesAuthor = 'can_edit_article';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');
                return;
            }
            // Permissions system

            $post_en_cours = true;

            if (!count(Tools::getValue('languesup'))) {
                $errors[] = $this->trans('You must activate at least one language', [], 'Modules.Prestablog.Prestablog');
            } elseif (!Tools::getValue('categories')) {
                $errors[] = $this->trans('You must choose at least one categorie', [], 'Modules.Prestablog.Prestablog');
            } else {
                foreach ($languages as $language) {
                    if (!Tools::getValue('title_' . $language['id_lang'])
                        && in_array($language['id_lang'], Tools::getValue('languesup'))
                    ) {
                        $errors[] = $this->trans('The title must be specified', [], 'Modules.Prestablog.Prestablog') . " ($language[name])";
                    }
                    if (Tools::getIsset('link_rewrite_' . $language['id_lang'])
                        && !Tools::getValue('link_rewrite_' . $language['id_lang'])
                        && in_array($language['id_lang'], Tools::getValue('languesup'))
                    ) {
                        $errors[] = $this->trans('The url rewrite must be specified', [], 'Modules.Prestablog.Prestablog') . " ($language[name]) "
                        . Tools::getValue('link_rewrite_' . $language['id_lang']);
                    }

                    $summary = Tools::getValue('paragraph_' . $language['id_lang']);
                    $content = Tools::getValue('content_' . $language['id_lang']);

                    if (!$summary && !$content && in_array($language['id_lang'], Tools::getValue('languesup'))) {
                        $errors[] = $this->trans('The content or introduction must be specified', [], 'Modules.Prestablog.Prestablog');
                    }
                }
            }

            if (!Validate::isAbsoluteUrl(Tools::getValue('url_redirect'))) {
                $errors[] = sprintf(
                    $this->trans('The field %1$s is not a correct.', [], 'Modules.Prestablog.Prestablog'),
                    '' . $this->trans('Permanent redirect url', [], 'Modules.Prestablog.Prestablog') . ''
                );
            }

            if (!count($errors)) {
                $news = new NewsClass((int) Tools::getValue('idN'));
                $news->id_shop = (int) $this->context->shop->id;
                $originalActif = $news->actif;
                $news->copyFromPost();
                if (!$permissionActivate && $news->actif != $originalActif) {
                    $news->actif = $originalActif;
                }
                $news->langues = json_encode(Tools::getValue('languesup'));
                if (!$news->update()) {
                    $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog');
                }

                NewsClass::removeAllProductsLinkNews((int) $news->id);
                if (Tools::getValue('productsLink')) {
                    foreach (Tools::getValue('productsLink') as $product_link) {
                        NewsClass::updateProductLinkNews((int) $news->id, (int) $product_link);
                    }
                }

                NewsClass::removeAllArticlesLinkNews((int) $news->id);
                if (Tools::getValue('articlesLink')) {
                    foreach (Tools::getValue('articlesLink') as $article_link) {
                        NewsClass::updateArticleLinkNews((int) $news->id, (int) $article_link);
                    }
                }
                NewsClass::removeAllPopupLinkNews((int) $news->id);
                if (Tools::getValue('popupLink')) {
                    NewsClass::updatePopupLinkNews((int) $news->id, (int) Tools::getValue('popupLink'));
                }
                $news->razEtatLangue((int) $news->id);
                foreach ($languages as $language) {
                    if (in_array($language['id_lang'], Tools::getValue('languesup'))) {
                        $news->changeActiveLangue((int) $news->id, (int) $language['id_lang']);
                    }
                }

                if (!$this->demo_mode) {
                    if ($_FILES['homepage_logo']['name']) {
                        if (!$this->uploadImage(
                            $_FILES['homepage_logo'],
                            Tools::getValue('idN'),
                            $this->normal_image_size_width,
                            $this->normal_image_size_height
                        )) {
                            $errors[] = $this->trans('An error occurred while upload image.', [], 'Modules.Prestablog.Prestablog');
                        } else {
                            foreach (self::scanListeThemes() as $value_theme) {
                                $config_theme = $this->getConfigXmlTheme($value_theme);
                                $this->imageResize(
                                    self::imgPath() . $value_theme . '/up-img/' . Tools::getValue('idN') . '.jpg',
                                    self::imgPath() . $value_theme . '/up-img/admincrop_' . Tools::getValue('idN') . '.jpg',
                                    (int) $this->admin_crop_image_size_width,
                                    (int) $this->admin_crop_image_size_height
                                );

                                $this->autocropImage(
                                    Tools::getValue('idN') . '.jpg',
                                    self::imgPath() . $value_theme . '/up-img/',
                                    self::imgPath() . $value_theme . '/up-img/',
                                    (int) $this->admin_thumb_image_size_width,
                                    (int) $this->admin_thumb_image_size_height,
                                    'adminth_',
                                    null
                                );

                                $config_theme_array = PrestaBlog::objectToArray($config_theme);
                                foreach ($config_theme_array['images'] as $kta => $vta) {
                                    $this->autocropImage(
                                        Tools::getValue('idN') . '.jpg',
                                        self::imgPath() . $value_theme . '/up-img/',
                                        self::imgPath() . $value_theme . '/up-img/',
                                        (int) $vta['width'],
                                        (int) $vta['height'],
                                        $kta . '_',
                                        null
                                    );
                                }
                            }
                        }
                    }
                }

                if (!count($errors)) {
                    if (!Tools::getValue('categories')) {
                        CorrespondancesCategoriesClass::delAllCategoriesNews((int) Tools::getValue('idN'));
                    } else {
                        CorrespondancesCategoriesClass::delAllCategoriesNews((int) Tools::getValue('idN'));
                        CorrespondancesCategoriesClass::updateCategoriesNews(
                            Tools::getValue('categories'),
                            (int) Tools::getValue('idN')
                        );
                    }
                    if (Tools::getValue('authors')) {
                        $explode = explode('-', Tools::getValue('authors'));
                        $id = $explode[0];
                        NewsClass::updateAuthorId((int) $news->id, $id);
                    }
                    Tools::redirectAdmin($this->confpath . '&editNews&idN=' . Tools::getValue('idN') . '&success=1');
                }
            }
        } elseif (Tools::isSubmit('submitUpdateCat') && Tools::getValue('idC')) {
            $post_en_cours = true;

            $categories = new CategoriesClass((int) Tools::getValue('idC'));
            $categories->id_shop = (int) $this->context->shop->id;
            $categories->copyFromPost();

            if (!CategoriesClass::injectGroupsInCategorie(Tools::getValue('groupBox'), (int) $categories->id)) {
                $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog') . ' - ' . $this->trans('Groups', [], 'Modules.Prestablog.Prestablog');
            }

            if (!$categories->update()) {
                $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog');
            }

            if (!$this->demo_mode) {
                if ($_FILES['imageCategory']['name']) {
                    if (!$this->uploadImage(
                        $_FILES['imageCategory'],
                        $categories->id,
                        $this->normal_image_size_width,
                        $this->normal_image_size_height,
                        'c'
                    )) {
                        $errors[] = $this->trans('An error occurred while upload image.', [], 'Modules.Prestablog.Prestablog');
                    } else {
                        foreach (self::scanListeThemes() as $value_theme) {
                            $this->imageResize(
                                self::imgPath() . $value_theme . '/up-img/c/' . $categories->id . '.jpg',
                                self::imgPath() . $value_theme . '/up-img/c/admincrop_' . $categories->id . '.jpg',
                                (int) $this->admin_crop_image_size_width,
                                (int) $this->admin_crop_image_size_height
                            );

                            $this->autocropImage(
                                $categories->id . '.jpg',
                                self::imgPath() . $value_theme . '/up-img/c/',
                                self::imgPath() . $value_theme . '/up-img/c/',
                                (int) $this->admin_thumb_image_size_width,
                                (int) $this->admin_thumb_image_size_height,
                                'adminth_',
                                null
                            );

                            $config_theme_array = PrestaBlog::objectToArray($config_theme);
                            foreach ($config_theme_array['categories'] as $key_theme_array => $value_theme_array) {
                                $this->autocropImage(
                                    $categories->id . '.jpg',
                                    self::imgPath() . $value_theme . '/up-img/c/',
                                    self::imgPath() . $value_theme . '/up-img/c/',
                                    (int) $value_theme_array['width'],
                                    (int) $value_theme_array['height'],
                                    $key_theme_array . '_',
                                    null
                                );
                            }
                        }
                    }
                }
            }
            CategoriesClass::removeAllPopupLinkCategorie((int) $categories->id);
            if (Tools::getValue('popupLinkCate')) {
                CategoriesClass::updatePopupLinkCategorie((int) $categories->id, (int) Tools::getValue('popupLinkCate'));
            }
            if (!count($errors)) {
                Tools::redirectAdmin($this->confpath . '&catListe');
            }
        } elseif (Tools::isSubmit('submitUpdateAntiSpam') && Tools::getValue('idAS')) {
            $post_en_cours = true;

            if (!count($errors)) {
                $antispam = new AntiSpamClass((int) Tools::getValue('idAS'));
                $antispam->id_shop = (int) $this->context->shop->id;
                $antispam->copyFromPost();

                if (!$antispam->update()) {
                    $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog');
                } else {
                    $antispam->reloadChecksum();
                    Tools::redirectAdmin($this->confpath . '&configAntiSpam');
                }
            }
        } elseif (Tools::isSubmit('submitUpdateComment') && Tools::getValue('idC')) {
            // Permissions system
            $rulesAuthor = 'can_manage_comments';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            if (!Tools::getValue('name')) {
                $errors[] = $this->trans('The name must be specified', [], 'Modules.Prestablog.Prestablog');
            }

            if (!count($errors)) {
                $comment = new CommentNewsClass((int) Tools::getValue('idC'));
                $comment->copyFromPost();

                if (!$comment->update()) {
                    $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog');
                }
            }
        } elseif (Tools::isSubmit('deleteComment') && Tools::getValue('idC')) {
            // Permissions system
            $rulesAuthor = 'can_manage_comments';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $comment_news = new CommentNewsClass((int) Tools::getValue('idC'));
            if (!$comment_news->delete()) {
                $errors[] = $this->trans('An error occurred while delete object.', [], 'Modules.Prestablog.Prestablog');
            } else {
                if (Tools::getValue('idN')) {
                    Tools::redirectAdmin($this->confpath . '&editNews&idN=' . Tools::getValue('idN') . '&showComments');
                } else {
                    Tools::redirectAdmin($this->confpath . '&commentListe&success');
                }
            }
        } elseif (Tools::isSubmit('deleteAllComment')) {
            // Permissions system
            $rulesAuthor = 'can_manage_comments';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            foreach (Tools::getValue('AllidCToDelete') as $valeur) {
                $post_en_cours = true;
                $comment_news = new CommentNewsClass($valeur);

                if (!$comment_news->delete()) {
                    $errors[] = $this->trans('An error occurred while delete object.', [], 'Modules.Prestablog.Prestablog');
                } else {
                    if (Tools::getValue('idN')) {
                        // Tools::redirectAdmin($this->confpath.'&editNews&idN='.Tools::getValue('idN').'&showComments');
                    } else {
                        //   Tools::redirectAdmin($this->confpath.'&commentListe');
                    }
                }
            }
            Tools::redirectAdmin($this->confpath . '&commentListe&success');
        } elseif (Tools::isSubmit('enabledComment') && Tools::getValue('idC')) {
            // Permissions system
            $rulesAuthor = 'can_manage_comments';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $comment_news = new CommentNewsClass((int) Tools::getValue('idC'));
            if (!$comment_news->changeEtat('actif', 1)) {
                $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog');
            } else {
                $liste_abo = CommentNewsClass::listeCommentMailAbo((int) $comment_news->news);

                if (Configuration::get($this->name . '_comment_subscription') && count($liste_abo)) {
                    $news = new NewsClass((int) $comment_news->news, $this->langue_default_store);

                    foreach ($liste_abo as $value_abo) {
                        $pre_url = Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . $this->ctrblog;

                        Mail::Send(
                            $this->langue_default_store,
                            'feedback-subscribe',
                            $this->trans('New comment', [], 'Modules.Prestablog.Prestablog') . ' / ' . $news->title,
                            [
                                '{news}' => (int) $news->id_prestablog_news,
                                '{title_news}' => $news->title,
                                '{url_news}' => $pre_url . '&id=' . (int) $comment_news->news,
                                '{url_desabonnement}' => $pre_url . '&d=' . (int) $comment_news->news,
                            ],
                            $value_abo,
                            null,
                            Configuration::get('PS_SHOP_EMAIL'),
                            Configuration::get('PS_SHOP_NAME'),
                            null,
                            null,
                            dirname(__FILE__) . '/mails/'
                        );
                    }
                }

                if (Tools::getValue('idN')) {
                    Tools::redirectAdmin($this->confpath . '&editNews&idN=' . (int) Tools::getValue('idN') . '&showComments');
                } else {
                    Tools::redirectAdmin($this->confpath . (Tools::isSubmit('commentListe') ? '&commentListe' : ''));
                }
            }
        } elseif (Tools::isSubmit('pendingComment') && Tools::getValue('idC')) {
            // Permissions system
            $rulesAuthor = 'can_manage_comments';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $comment_news = new CommentNewsClass((int) Tools::getValue('idC'));
            if (!$comment_news->changeEtat('actif', -1)) {
                $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog');
            } else {
                Tools::redirectAdmin($this->confpath . (Tools::isSubmit('commentListe') ? '&commentListe' : ''));
            }
        } elseif (Tools::isSubmit('disabledComment') && Tools::getValue('idC')) {
            // Permissions system
            $rulesAuthor = 'can_manage_comments';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $comment_news = new CommentNewsClass((int) Tools::getValue('idC'));
            if (!$comment_news->changeEtat('actif', 0)) {
                $errors[] = $this->trans('An error occurred while update object.', [], 'Modules.Prestablog.Prestablog');
            } else {
                if (Tools::getValue('idN')) {
                    Tools::redirectAdmin($this->confpath . '&editNews&idN=' . Tools::getValue('idN') . '&showComments');
                } else {
                    Tools::redirectAdmin($this->confpath . (Tools::isSubmit('commentListe') ? '&commentListe' : ''));
                }
            }
        } elseif (Tools::isSubmit('deleteImageBlog') && Tools::getValue('idN')) {
            $post_en_cours = true;
            if (!file_exists(self::imgUpPath() . '/' . Tools::getValue('idN') . '.jpg')) {
                $errors[] = $this->trans('This action cannot be taken.', [], 'Modules.Prestablog.Prestablog');
            } else {
                $this->deleteAllImagesThemes(Tools::getValue('idN'));
            }
            if (!count($errors)) {
                Tools::redirectAdmin($this->confpath . '&editNews&idN=' . Tools::getValue('idN'));
            }
        } elseif (Tools::isSubmit('deleteImageBlog') && Tools::getValue('idC')) {
            $post_en_cours = true;
            if (!file_exists(self::imgUpPath() . '/c/' . Tools::getValue('idC') . '.jpg')) {
                $errors[] = $this->trans('This action cannot be taken.', [], 'Modules.Prestablog.Prestablog');
            } else {
                $this->deleteAllImagesThemesCat((int) Tools::getValue('idC'));
            }
            if (!count($errors)) {
                Tools::redirectAdmin($this->confpath . '&editCat&idC=' . Tools::getValue('idC'));
            }
        } elseif (Tools::isSubmit('submitCrop') && Tools::getValue('idN')) {
            $post_en_cours = true;
            if (!file_exists(self::imgUpPath() . '/admincrop_' . Tools::getValue('idN') . '.jpg')) {
                $errors[] = $this->trans('This action cannot be taken.', [], 'Modules.Prestablog.Prestablog');
            } else {
                $config_theme = $this->getConfigXmlTheme(self::getT());
                $config_theme_array = PrestaBlog::objectToArray($config_theme);

                list($w_image_base, $h_image_base) = getimagesize(
                    self::imgUpPath() . '/admincrop_' . Tools::getValue('idN') . '.jpg'
                );

                $this->cropImage(
                    Tools::getValue('idN') . '.jpg',
                    self::imgUpPath() . '/',
                    self::imgUpPath() . '/',
                    (int) $w_image_base,
                    (int) $h_image_base,
                    (int) $config_theme_array['images'][Tools::getValue('pfx')]['width'],
                    (int) $config_theme_array['images'][Tools::getValue('pfx')]['height'],
                    (int) Tools::getValue('x'),
                    (int) Tools::getValue('y'),
                    (int) Tools::getValue('w'),
                    (int) Tools::getValue('h'),
                    Tools::getValue('pfx') . '_',
                    null
                );

                if (Tools::getValue('pfx') == 'thumb') {
                    $this->autocropImage(
                        Tools::getValue('idN') . '.jpg',
                        self::imgUpPath() . '/',
                        self::imgUpPath() . '/',
                        (int) $this->admin_thumb_image_size_width,
                        (int) $this->admin_thumb_image_size_height,
                        'adminth_',
                        null
                    );
                }
            }
            if (!count($errors)) {
                $url_redir = $this->confpath . '&editNews';
                $url_redir .= '&idN=' . Tools::getValue('idN');
                $url_redir .= '&pfx=' . Tools::getValue('pfx');
                Tools::redirectAdmin($url_redir);
            }
        } elseif (Tools::isSubmit('submitCrop') && Tools::getValue('idC')) {
            $post_en_cours = true;
            if (!file_exists(self::imgUpPath() . '/c/admincrop_' . Tools::getValue('idC') . '.jpg')) {
                $errors[] = $this->trans('This action cannot be taken.', [], 'Modules.Prestablog.Prestablog');
            } else {
                $config_theme = $this->getConfigXmlTheme(self::getT());
                $config_theme_array = PrestaBlog::objectToArray($config_theme);

                $img_crop = self::imgUpPath() . '/c/admincrop_' . Tools::getValue('idC') . '.jpg';

                list($w_image_base, $h_image_base) = getimagesize($img_crop);

                $this->cropImage(
                    Tools::getValue('idC') . '.jpg',
                    self::imgUpPath() . '/c/',
                    self::imgUpPath() . '/c/',
                    (int) $w_image_base,
                    (int) $h_image_base,
                    (int) $config_theme_array['categories'][Tools::getValue('pfx')]['width'],
                    (int) $config_theme_array['categories'][Tools::getValue('pfx')]['height'],
                    (int) Tools::getValue('x'),
                    (int) Tools::getValue('y'),
                    (int) Tools::getValue('w'),
                    (int) Tools::getValue('h'),
                    Tools::getValue('pfx') . '_',
                    null
                );

                if (Tools::getValue('pfx') == 'thumb') {
                    $this->autocropImage(
                        Tools::getValue('idC') . '.jpg',
                        self::imgUpPath() . '/c/',
                        self::imgUpPath() . '/c/',
                        (int) $this->admin_thumb_image_size_width,
                        (int) $this->admin_thumb_image_size_height,
                        'adminth_',
                        null
                    );
                }
            }
            if (!count($errors)) {
                $url_redir = $this->confpath . '&editCat';
                $url_redir .= '&idC=' . Tools::getValue('idC');
                $url_redir .= '&pfx=' . Tools::getValue('pfx');
                Tools::redirectAdmin($url_redir);
            }
        } elseif (Tools::isSubmit('submitAntiSpamConfig')) {
            // Permissions system
            $rulesAuthor = 'can_use_tool';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_antispam_actif'))) {
                Configuration::updateValue(
                    $this->name . '_antispam_actif',
                    (int) Tools::getValue($this->name . '_antispam_actif')
                );
            }
            Tools::redirectAdmin($this->confpath . '&configAntiSpam');
        } elseif (Tools::isSubmit('submitSitemapConfig')) {
            // Permissions system
            $rulesAuthor = 'can_use_tool';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_sitemap_actif'))) {
                Configuration::updateValue(
                    $this->name . '_sitemap_actif',
                    (int) Tools::getValue($this->name . '_sitemap_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_sitemap_articles'))) {
                Configuration::updateValue(
                    $this->name . '_sitemap_articles',
                    (int) Tools::getValue($this->name . '_sitemap_articles')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_sitemap_categories'))) {
                Configuration::updateValue(
                    $this->name . '_sitemap_categories',
                    (int) Tools::getValue($this->name . '_sitemap_categories')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_sitemap_limit'))) {
                Configuration::updateValue(
                    $this->name . '_sitemap_limit',
                    (int) Tools::getValue($this->name . '_sitemap_limit')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_sitemap_older'))) {
                Configuration::updateValue(
                    $this->name . '_sitemap_older',
                    (int) Tools::getValue($this->name . '_sitemap_older')
                );
            }
            Tools::redirectAdmin($this->confpath . '&sitemap');
        } elseif (Tools::isSubmit('submitSitemapGenerate')) {
            // Permissions system
            $rulesAuthor = 'can_use_tool';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $this->createTheShopSitemap();
            Tools::redirectAdmin($this->confpath . '&sitemap');
        } elseif (Tools::isSubmit('deleteSitemap')) {
            // Permissions system
            $rulesAuthor = 'can_use_tool';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $this->deleteSitemapFromShop((int) $this->context->shop->id);
            Tools::redirectAdmin($this->confpath . '&sitemap');
        } elseif (Tools::isSubmit('submitSubBlocksConfig')) {
            if (is_numeric(Tools::getValue($this->name . '_subblocks_actif'))) {
                Configuration::updateValue(
                    $this->name . '_subblocks_actif',
                    (int) Tools::getValue($this->name . '_subblocks_actif')
                );
            }
            Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
        } elseif (Tools::isSubmit('submitTheme')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            Configuration::updateValue($this->name . '_theme', Tools::getValue('theme'));
            Tools::redirectAdmin($this->confpath . '&configTheme&success');
        } elseif (Tools::isSubmit('selectLayout')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            Configuration::updateValue(
                $this->name . '_layout_blog',
                (int) Tools::getValue($this->name . '_layout_blog')
            );
            Tools::redirectAdmin($this->confpath . '&configTheme');
        } elseif (Tools::isSubmit('submitWizard')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            Tools::redirectAdmin($this->confpath . '&configWizard');
        } elseif (Tools::isSubmit('submitUrl')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $filesToKeep = [
                _PS_MODULE_DIR_ . 'prestablog/controllers/front/blog.php',
                _PS_MODULE_DIR_ . 'prestablog/controllers/front/index.php',
                _PS_MODULE_DIR_ . 'prestablog/controllers/front/rss.php',
                _PS_MODULE_DIR_ . 'prestablog/controllers/front/sitemap.php',
            ];

            $dirList = glob(_PS_MODULE_DIR_ . 'prestablog/controllers/front/*', GLOB_BRACE);
            foreach ($dirList as $fileDel) {
                if (!in_array($fileDel, $filesToKeep)) {
                    if (is_dir($fileDel)) {
                        rmdir($fileDel);
                    } else {
                        unlink($fileDel);
                    }
                }
            }
            $urlBlog = str_replace(' ', '_', Tools::getValue($this->name . '_urlblog'));
            $urlBlog = str_replace('-', '_', Tools::getValue($this->name . '_urlblog'));
            $urlBlog = preg_replace('/[^A-Za-z0-9\-]/', '', $urlBlog);
            Configuration::updateValue($this->name . '_urlblog', $urlBlog);
            $file = Tools::file_get_contents(_PS_MODULE_DIR_ . 'prestablog/controllers/front/blog.php');

            $fh = fopen(_PS_MODULE_DIR_ . 'prestablog/controllers/front/' . $urlBlog . '.php', 'w+');
            $bob = 'PrestaBlog' . Tools::ucfirst($urlBlog) . 'ModuleFrontController';
            $bob2 = "['" . $urlBlog . "'];";
            fwrite($fh, str_replace('PrestaBlogBlogModuleFrontController', $bob, $file));
            fclose($fh);

            $prestablog = new PrestaBlog();
            $file = Tools::file_get_contents(_PS_MODULE_DIR_ . 'prestablog/controllers/front/' . $urlBlog . '.php');
            $fh = fopen(_PS_MODULE_DIR_ . 'prestablog/controllers/front/' . $urlBlog . '.php', 'w+');
            fwrite($fh, str_replace("$prestablog->message_call_back['Blog'];", $bob2, $file));
            fclose($fh);

            Tools::redirectAdmin($this->confpath . '&pageBlog&success');
        } elseif (Tools::isSubmit('submitPageBlog')) {
            if (is_numeric(Tools::getValue($this->name . '_pageslide_actif'))) {
                Configuration::updateValue(
                    $this->name . '_pageslide_actif',
                    (int) Tools::getValue($this->name . '_pageslide_actif')
                );
            }

            $languages = Language::getLanguages(true);

            $title_cfg_lang = [];
            $desc_cfg_lang = [];
            $titre_h1_cfg_lang = [];

            foreach ($languages as $language) {
                $title_cfg_lang[(int) $language['id_lang']] = Tools::getValue('meta_title_' . $language['id_lang']);
                $desc_cfg_lang[(int) $language['id_lang']] = Tools::getValue('meta_description_' . $language['id_lang']);
                $titre_h1_cfg_lang[(int) $language['id_lang']] = Tools::getValue('titre_h1_' . $language['id_lang']);
            }

            Configuration::updateValue($this->name . '_titlepageblog', $title_cfg_lang);
            Configuration::updateValue($this->name . '_descpageblog', $desc_cfg_lang);
            Configuration::updateValue($this->name . '_h1pageblog', $titre_h1_cfg_lang);

            Tools::redirectAdmin($this->confpath . '&pageBlog&success');
        } elseif (Tools::isSubmit('submitPopupHome')) {
            PopupClass::updateValuePopuphome(
                Tools::getValue('popupLink')
            );
            PopupClass::DeleteAllValue(
                Tools::getValue('popupLink')
            );
            if (is_numeric(Tools::getValue($this->name . '_popuphome_actif'))) {
                Configuration::updateValue(
                    $this->name . '_popuphome_actif',
                    (int) Tools::getValue($this->name . '_popuphome_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_popup_general'))) {
                Configuration::updateValue(
                    $this->name . '_popup_general',
                    (int) Tools::getValue($this->name . '_popup_general')
                );
            }
            Tools::redirectAdmin($this->confpath . '&pageBlog&success');
        } elseif (Tools::isSubmit('submitConfSlideNews')) {
            // Permissions system
            $rulesAuthor = 'can_manage_slide';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            if (is_numeric(Tools::getValue($this->name . '_homenews_limit'))) {
                Configuration::updateValue(
                    $this->name . '_homenews_limit',
                    (int) Tools::getValue($this->name . '_homenews_limit')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_homenews_actif'))) {
                Configuration::updateValue(
                    $this->name . '_homenews_actif',
                    (int) Tools::getValue($this->name . '_homenews_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_pageslide_actif'))) {
                Configuration::updateValue(
                    $this->name . '_pageslide_actif',
                    (int) Tools::getValue($this->name . '_pageslide_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_slide_title_length'))) {
                Configuration::updateValue(
                    $this->name . '_slide_title_length',
                    (int) Tools::getValue($this->name . '_slide_title_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_slide_intro_length'))) {
                Configuration::updateValue(
                    $this->name . '_slide_intro_length',
                    (int) Tools::getValue($this->name . '_slide_intro_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_slide_picture_width'))) {
                Configuration::updateValue(
                    $this->name . '_slide_picture_width',
                    (int) Tools::getValue($this->name . '_slide_picture_width')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_slide_picture_height'))) {
                Configuration::updateValue(
                    $this->name . '_slide_picture_height',
                    (int) Tools::getValue($this->name . '_slide_picture_height')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_show_slide_title'))) {
                Configuration::updateValue(
                    $this->name . '_show_slide_title',
                    (int) Tools::getValue($this->name . '_show_slide_title')
                );
            }

            $xml = Tools::file_get_contents(_PS_MODULE_DIR_ . 'prestablog/views/config/' . self::getT() . '.xml');
            $config_theme = $this->getConfigXmlTheme(self::getT());
            $config_theme_array = PrestaBlog::objectToArray($config_theme);

            $remplacement = '
			  <thumb> <!--Image prevue pour les miniatures dans les listes -->
			  <width>' . (int) $config_theme_array['images']['thumb']['width'] . '</width>
			  <height>' . (int) $config_theme_array['images']['thumb']['height'] . '</height>
			  </thumb>
			  <slide> <!--Image prevue pour les slides -->
			  <width>' . Tools::getValue($this->name . '_slide_picture_width') . '</width>
			  <height>' . Tools::getValue($this->name . '_slide_picture_height') . '</height>
			  </slide>';

            $xml = preg_replace('#<images[^>]*>.*?</images>#si', '<images>' . $remplacement . '</images>', $xml);

            if (is_writable(_PS_MODULE_DIR_ . $this->name . '/views/config/')) {
                file_put_contents(_PS_MODULE_DIR_ . 'prestablog/views/config/' . self::getT() . '.xml', $xml);
                Tools::redirectAdmin($this->confpath . '&configSlide&success');
            }
        } elseif (Tools::isSubmit('submitConfListeArticles')) {
            if (is_numeric(Tools::getValue($this->name . '_news_title_length'))) {
                Configuration::updateValue(
                    $this->name . '_news_title_length',
                    (int) Tools::getValue($this->name . '_news_title_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_news_intro_length'))) {
                Configuration::updateValue(
                    $this->name . '_news_intro_length',
                    (int) Tools::getValue($this->name . '_news_intro_length')
                );
            }

            $xml = Tools::file_get_contents(_PS_MODULE_DIR_ . 'prestablog/views/config/' . self::getT() . '.xml');
            $config_theme = $this->getConfigXmlTheme(self::getT());
            $config_theme_array = PrestaBlog::objectToArray($config_theme);

            $remplacement = '
			  <thumb> <!--Image prevue pour les miniatures dans les listes -->
			  <width>' . Tools::getValue('thumb_picture_width') . '</width>
			  <height>' . Tools::getValue('thumb_picture_height') . '</height>
			  </thumb>
			  <slide> <!--Image prevue pour les slides -->
			  <width>' . (int) $config_theme_array['images']['slide']['width'] . '</width>
			  <height>' . (int) $config_theme_array['images']['slide']['height'] . '</height>
			  </slide>';

            $xml = preg_replace('#<images[^>]*>.*?</images>#si', '<images>' . $remplacement . '</images>', $xml);

            if (is_writable(_PS_MODULE_DIR_ . $this->name . '/views/config/')) {
                file_put_contents(_PS_MODULE_DIR_ . $this->name . '/views/config/' . self::getT() . '.xml', $xml);
                Tools::redirectAdmin($this->confpath . '&configSubBlocks&success');
            }
        } elseif (Tools::isSubmit('submitConfBlocSearch')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_blocsearch_actif'))) {
                Configuration::updateValue(
                    $this->name . '_blocsearch_actif',
                    (int) Tools::getValue($this->name . '_blocsearch_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_search_filtrecat'))) {
                Configuration::updateValue(
                    $this->name . '_search_filtrecat',
                    (int) Tools::getValue($this->name . '_search_filtrecat')
                );
            }

            Tools::redirectAdmin($this->confpath . '&configBlocs&success');
        } elseif (Tools::isSubmit('submitConfBlocRss')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_allnews_rss'))) {
                Configuration::updateValue(
                    $this->name . '_allnews_rss',
                    (int) Tools::getValue($this->name . '_allnews_rss')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_rss_title_length'))) {
                Configuration::updateValue(
                    $this->name . '_rss_title_length',
                    (int) Tools::getValue($this->name . '_rss_title_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_rss_intro_length'))) {
                Configuration::updateValue(
                    $this->name . '_rss_intro_length',
                    (int) Tools::getValue($this->name . '_rss_intro_length')
                );
            }

            Tools::redirectAdmin($this->confpath . '&configBlocs&success');
        } elseif (Tools::isSubmit('submitConfBlocLastNews')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_lastnews_limit'))) {
                Configuration::updateValue(
                    $this->name . '_lastnews_limit',
                    (int) Tools::getValue($this->name . '_lastnews_limit')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_lastnews_limit'))) {
                Configuration::updateValue(
                    $this->name . '_lastnews_actif',
                    (int) Tools::getValue($this->name . '_lastnews_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_lastnews_showintro'))) {
                Configuration::updateValue(
                    $this->name . '_lastnews_showintro',
                    (int) Tools::getValue($this->name . '_lastnews_showintro')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_lastnews_showthumb'))) {
                Configuration::updateValue(
                    $this->name . '_lastnews_showthumb',
                    (int) Tools::getValue($this->name . '_lastnews_showthumb')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_lastnews_showall'))) {
                Configuration::updateValue(
                    $this->name . '_lastnews_showall',
                    (int) Tools::getValue($this->name . '_lastnews_showall')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_lastnews_title_length'))) {
                Configuration::updateValue(
                    $this->name . '_lastnews_title_length',
                    (int) Tools::getValue($this->name . '_lastnews_title_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_lastnews_intro_length'))) {
                Configuration::updateValue(
                    $this->name . '_lastnews_intro_length',
                    (int) Tools::getValue($this->name . '_lastnews_intro_length')
                );
            }

            Tools::redirectAdmin($this->confpath . '&configBlocs&success');
        } elseif (Tools::isSubmit('submitConfFooterLastNews')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            if (is_numeric(Tools::getValue($this->name . '_footlastnews_limit'))) {
                Configuration::updateValue(
                    $this->name . '_footlastnews_limit',
                    (int) Tools::getValue($this->name . '_footlastnews_limit')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_footlastnews_actif'))) {
                Configuration::updateValue(
                    $this->name . '_footlastnews_actif',
                    (int) Tools::getValue($this->name . '_footlastnews_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_footlastnews_showall'))) {
                Configuration::updateValue(
                    $this->name . '_footlastnews_showall',
                    (int) Tools::getValue($this->name . '_footlastnews_showall')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_footlastnews_intro'))) {
                Configuration::updateValue(
                    $this->name . '_footlastnews_intro',
                    (int) Tools::getValue($this->name . '_footlastnews_intro')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_footer_title_length'))) {
                Configuration::updateValue(
                    $this->name . '_footer_title_length',
                    (int) Tools::getValue($this->name . '_footer_title_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_footer_intro_length'))) {
                Configuration::updateValue(
                    $this->name . '_footer_intro_length',
                    (int) Tools::getValue($this->name . '_footer_intro_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_footlastnews_showthumb'))) {
                Configuration::updateValue(
                    $this->name . '_footlastnews_showthumb',
                    (int) Tools::getValue($this->name . '_footlastnews_showthumb')
                );
            }

            Tools::redirectAdmin($this->confpath . '&configBlocs&success');
        } elseif (Tools::isSubmit('submitConfBlocDateNews')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            if (is_numeric(Tools::getValue($this->name . '_datenews_actif'))) {
                Configuration::updateValue(
                    $this->name . '_datenews_actif',
                    (int) Tools::getValue($this->name . '_datenews_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_datenews_showall'))) {
                Configuration::updateValue(
                    $this->name . '_datenews_showall',
                    (int) Tools::getValue($this->name . '_datenews_showall')
                );
            }
            Configuration::updateValue($this->name . '_datenews_order', Tools::getValue($this->name . '_datenews_order'));

            Tools::redirectAdmin($this->confpath . '&configBlocs&success');
        } elseif (Tools::isSubmit('submitConfListCat')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            if (is_numeric(Tools::getValue($this->name . '_nb_liste_page'))) {
                Configuration::updateValue(
                    $this->name . '_nb_liste_page',
                    (int) Tools::getValue($this->name . '_nb_liste_page')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_news_title_length'))) {
                Configuration::updateValue(
                    $this->name . '_news_title_length',
                    (int) Tools::getValue($this->name . '_news_title_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_news_intro_length'))) {
                Configuration::updateValue(
                    $this->name . '_news_intro_length',
                    (int) Tools::getValue($this->name . '_news_intro_length')
                );
            }

            $xml = Tools::file_get_contents(_PS_MODULE_DIR_ . 'prestablog/views/config/' . self::getT() . '.xml');
            $config_theme = $this->getConfigXmlTheme(self::getT());
            $config_theme_array = PrestaBlog::objectToArray($config_theme);

            $remplacement = '
			  <thumb> <!--Image prevue pour les miniatures dans les listes -->
			  <width>' . Tools::getValue('thumb_picture_width') . '</width>
			  <height>' . Tools::getValue('thumb_picture_height') . '</height>
			  </thumb>
			  <slide> <!--Image prevue pour les slides -->
			  <width>' . (int) $config_theme_array['images']['slide']['width'] . '</width>
			  <height>' . (int) $config_theme_array['images']['slide']['height'] . '</height>
			  </slide>';

            $xml = preg_replace('#<images[^>]*>.*?</images>#si', '<images>' . $remplacement . '</images>', $xml);

            if (is_numeric(Tools::getValue($this->name . '_rating_actif'))) {
                Configuration::updateValue(
                    $this->name . '_rating_actif',
                    (int) Tools::getValue($this->name . '_rating_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_read_actif'))) {
                Configuration::updateValue(
                    $this->name . '_read_actif',
                    (int) Tools::getValue($this->name . '_read_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_article_page'))) {
                Configuration::updateValue(
                    $this->name . '_article_page',
                    (int) Tools::getValue($this->name . '_article_page')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_catnews_shownbnews'))) {
                Configuration::updateValue(
                    $this->name . '_catnews_shownbnews',
                    (int) Tools::getValue($this->name . '_catnews_shownbnews')
                );
            }
            if (is_writable(_PS_MODULE_DIR_ . $this->name . '/views/config/')) {
                file_put_contents(_PS_MODULE_DIR_ . $this->name . '/views/config/' . self::getT() . '.xml', $xml);
            }
            Tools::redirectAdmin($this->confpath . '&configCategories&success');
        } elseif (Tools::isSubmit('submitConfBlocCatNews')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system
            if (is_numeric(Tools::getValue($this->name . '_catnews_actif'))) {
                Configuration::updateValue(
                    $this->name . '_catnews_actif',
                    (int) Tools::getValue($this->name . '_catnews_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_catnews_showall'))) {
                Configuration::updateValue(
                    $this->name . '_catnews_showall',
                    (int) Tools::getValue($this->name . '_catnews_showall')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_catnews_empty'))) {
                Configuration::updateValue(
                    $this->name . '_catnews_empty',
                    (int) Tools::getValue($this->name . '_catnews_empty')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_catnews_tree'))) {
                Configuration::updateValue(
                    $this->name . '_catnews_tree',
                    (int) Tools::getValue($this->name . '_catnews_tree')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_catnews_showthumb'))) {
                Configuration::updateValue(
                    $this->name . '_catnews_showthumb',
                    (int) Tools::getValue($this->name . '_catnews_showthumb')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_catnews_showintro'))) {
                Configuration::updateValue(
                    $this->name . '_catnews_showintro',
                    (int) Tools::getValue($this->name . '_catnews_showintro')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_cat_title_length'))) {
                Configuration::updateValue(
                    $this->name . '_cat_title_length',
                    (int) Tools::getValue($this->name . '_cat_title_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_cat_intro_length'))) {
                Configuration::updateValue(
                    $this->name . '_cat_intro_length',
                    (int) Tools::getValue($this->name . '_cat_intro_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_catnews_rss'))) {
                Configuration::updateValue(
                    $this->name . '_catnews_rss',
                    (int) Tools::getValue($this->name . '_catnews_rss')
                );
            }

            Tools::redirectAdmin($this->confpath . '&configCategories&success');
        } elseif (Tools::isSubmit('submitConfRewrite')) {
            if (is_numeric(Tools::getValue($this->name . '_rewrite_actif'))) {
                Configuration::updateValue(
                    $this->name . '_rewrite_actif',
                    (int) Tools::getValue($this->name . '_rewrite_actif')
                );
            }

            Tools::redirectAdmin($this->confpath . '&configModule&success');
        } elseif (Tools::isSubmit('submitAiConfig')) {
            // Permissions system
            $rulesAuthor = 'can_use_tool';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $post_en_cours = true;
            $errors = [];

            $apiKey = Tools::getValue('prestablog_chatgpt_api_key');
            $modelGpt = Tools::getValue('prestablog_chatgpt_model');

            // Check if an API key is already stored in the configuration
            $existingApiKey = Configuration::get('prestablog_chatgpt_api_key');

            if (empty($apiKey) && empty($existingApiKey)) {
                $errors[] = $this->trans('The ChatGPT API key is mandatory.', [], 'Modules.Prestablog.Ai');
            }

            if (empty($modelGpt)) {
                $errors[] = $this->trans('The GPT model is mandatory.', [], 'Modules.Prestablog.Ai');
            }

            if (!count($errors)) {
                // Update the API key only if a new one is provided
                if (!empty($apiKey)) {
                    Configuration::updateValue('prestablog_chatgpt_api_key', $apiKey);
                }
                Configuration::updateValue('prestablog_chatgpt_model', $modelGpt);
            }
        } elseif (Tools::isSubmit('submitAuthorDisplay')) {
            if (is_numeric(Tools::getValue($this->name . '_enable_permissions'))) {
                Configuration::updateValue(
                    $this->name . '_enable_permissions',
                    (int) Tools::getValue($this->name . '_enable_permissions')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_author_edit_actif'))) {
                Configuration::updateValue(
                    $this->name . '_author_edit_actif',
                    (int) Tools::getValue($this->name . '_author_edit_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_author_actif'))) {
                Configuration::updateValue(
                    $this->name . '_author_actif',
                    (int) Tools::getValue($this->name . '_author_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_author_cate_actif'))) {
                Configuration::updateValue(
                    $this->name . '_author_cate_actif',
                    (int) Tools::getValue($this->name . '_author_cate_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_author_news_actif'))) {
                Configuration::updateValue(
                    $this->name . '_author_news_actif',
                    (int) Tools::getValue($this->name . '_author_news_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_author_about_actif'))) {
                Configuration::updateValue(
                    $this->name . '_author_about_actif',
                    (int) Tools::getValue($this->name . '_author_about_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_author_news_number'))) {
                Configuration::updateValue(
                    $this->name . '_author_news_number',
                    (int) Tools::getValue($this->name . '_author_news_number')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_author_intro_length'))) {
                Configuration::updateValue(
                    $this->name . '_author_intro_length',
                    (int) Tools::getValue($this->name . '_author_intro_length')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_author_pic_width'))) {
                Configuration::updateValue(
                    $this->name . '_author_pic_width',
                    (int) Tools::getValue($this->name . '_author_pic_width')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_author_pic_height'))) {
                Configuration::updateValue(
                    $this->name . '_author_pic_height',
                    (int) Tools::getValue($this->name . '_author_pic_height')
                );
            }

            Tools::redirectAdmin($this->confpath . '&configAuthor&success');
        } elseif (Tools::isSubmit('submitConfGobalFront')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_producttab_actif'))) {
                Configuration::updateValue(
                    $this->name . '_producttab_actif',
                    (int) Tools::getValue($this->name . '_producttab_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_thumb_linkprod_width'))) {
                Configuration::updateValue(
                    $this->name . '_thumb_linkprod_width',
                    (int) Tools::getValue($this->name . '_thumb_linkprod_width')
                );
            }

            if (is_numeric(Tools::getValue($this->name . '_material_icons'))) {
                Configuration::updateValue(
                    $this->name . '_material_icons',
                    (int) Tools::getValue($this->name . '_material_icons')
                );
            }

            if (is_numeric(Tools::getValue($this->name . '_socials_actif'))) {
                Configuration::updateValue(
                    $this->name . '_socials_actif',
                    (int) Tools::getValue($this->name . '_socials_actif')
                );
            }

            if (is_numeric(Tools::getValue($this->name . '_s_facebook'))) {
                Configuration::updateValue(
                    $this->name . '_s_facebook',
                    (int) Tools::getValue($this->name . '_s_facebook')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_s_twitter'))) {
                Configuration::updateValue(
                    $this->name . '_s_twitter',
                    (int) Tools::getValue($this->name . '_s_twitter')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_s_linkedin'))) {
                Configuration::updateValue(
                    $this->name . '_s_linkedin',
                    (int) Tools::getValue($this->name . '_s_linkedin')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_s_email'))) {
                Configuration::updateValue(
                    $this->name . '_s_email',
                    (int) Tools::getValue($this->name . '_s_email')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_s_pinterest'))) {
                Configuration::updateValue(
                    $this->name . '_s_pinterest',
                    (int) Tools::getValue($this->name . '_s_pinterest')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_s_pocket'))) {
                Configuration::updateValue(
                    $this->name . '_s_pocket',
                    (int) Tools::getValue($this->name . '_s_pocket')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_s_tumblr'))) {
                Configuration::updateValue(
                    $this->name . '_s_tumblr',
                    (int) Tools::getValue($this->name . '_s_tumblr')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_s_reddit'))) {
                Configuration::updateValue(
                    $this->name . '_s_reddit',
                    (int) Tools::getValue($this->name . '_s_reddit')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_s_hackernews'))) {
                Configuration::updateValue(
                    $this->name . '_s_hackernews',
                    (int) Tools::getValue($this->name . '_s_hackernews')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_uniqnews_rss'))) {
                Configuration::updateValue(
                    $this->name . '_uniqnews_rss',
                    (int) Tools::getValue($this->name . '_uniqnews_rss')
                );
            }

            if (is_numeric(Tools::getValue($this->name . '_view_news_img'))) {
                Configuration::updateValue(
                    $this->name . '_view_news_img',
                    (int) Tools::getValue($this->name . '_view_news_img')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_show_breadcrumb'))) {
                Configuration::updateValue(
                    $this->name . '_show_breadcrumb',
                    (int) Tools::getValue($this->name . '_show_breadcrumb')
                );
            }
            Tools::redirectAdmin($this->confpath . '&configModule&success');
        } elseif (Tools::isSubmit('submitConfCategory')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_view_cat_desc'))) {
                Configuration::updateValue(
                    $this->name . '_view_cat_desc',
                    (int) Tools::getValue($this->name . '_view_cat_desc')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_view_cat_thumb'))) {
                Configuration::updateValue(
                    $this->name . '_view_cat_thumb',
                    (int) Tools::getValue($this->name . '_view_cat_thumb')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_view_cat_img'))) {
                Configuration::updateValue(
                    $this->name . '_view_cat_img',
                    (int) Tools::getValue($this->name . '_view_cat_img')
                );
            }

            $xml = Tools::file_get_contents(_PS_MODULE_DIR_ . 'prestablog/views/config/' . self::getT() . '.xml');

            $remplacement = '
			  <thumb> <!--Image prevue pour les miniatures dans les listes -->
			  <width>' . Tools::getValue('thumb_cat_width') . '</width>
			  <height>' . Tools::getValue('thumb_cat_height') . '</height>
			  </thumb>
			  <full> <!--Image prevue pour la description de la categorie en liste 1ere page -->
			  <width>' . Tools::getValue('full_cat_width') . '</width>
			  <height>' . Tools::getValue('full_cat_height') . '</height>
			  </full>';

            $xml = preg_replace(
                '#<categories[^>]*>.*?</categories>#si',
                '<categories>' . $remplacement . '</categories>',
                $xml
            );

            if (is_writable(_PS_MODULE_DIR_ . $this->name . '/views/config/')) {
                file_put_contents(_PS_MODULE_DIR_ . 'prestablog/views/config/' . self::getT() . '.xml', $xml);
                Tools::redirectAdmin($this->confpath . '&configCategories&success');
            }
        } elseif (Tools::isSubmit('submitConfGobalAdmin')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_nb_car_min_linkprod'))) {
                Configuration::updateValue(
                    $this->name . '_nb_car_min_linkprod',
                    (int) Tools::getValue($this->name . '_nb_car_min_linkprod')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_nb_list_linkprod'))) {
                Configuration::updateValue(
                    $this->name . '_nb_list_linkprod',
                    (int) Tools::getValue($this->name . '_nb_list_linkprod')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_nb_car_min_linknews'))) {
                Configuration::updateValue(
                    $this->name . '_nb_car_min_linknews',
                    (int) Tools::getValue($this->name . '_nb_car_min_linknews')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_nb_list_linknews'))) {
                Configuration::updateValue(
                    $this->name . '_nb_list_linknews',
                    (int) Tools::getValue($this->name . '_nb_list_linknews')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_nb_car_min_linklb'))) {
                Configuration::updateValue(
                    $this->name . '_nb_car_min_linklb',
                    (int) Tools::getValue($this->name . '_nb_car_min_linklb')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_nb_list_linklb'))) {
                Configuration::updateValue(
                    $this->name . '_nb_list_linklb',
                    (int) Tools::getValue($this->name . '_nb_list_linklb')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_nb_news_pl'))) {
                Configuration::updateValue(
                    $this->name . '_nb_news_pl',
                    (int) Tools::getValue($this->name . '_nb_news_pl')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_nb_comments_pl'))) {
                Configuration::updateValue(
                    $this->name . '_nb_comments_pl',
                    (int) Tools::getValue($this->name . '_nb_comments_pl')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_comment_div_visible'))) {
                Configuration::updateValue(
                    $this->name . '_comment_div_visible',
                    (int) Tools::getValue($this->name . '_comment_div_visible')
                );
            }

            Tools::redirectAdmin($this->confpath . '&configModule&success');
        } elseif (Tools::isSubmit('submitConfMenuCatBlog')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_menu_cat_blog_index'))) {
                Configuration::updateValue(
                    $this->name . '_menu_cat_blog_index',
                    (int) Tools::getValue($this->name . '_menu_cat_blog_index')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_menu_cat_blog_list'))) {
                Configuration::updateValue(
                    $this->name . '_menu_cat_blog_list',
                    (int) Tools::getValue($this->name . '_menu_cat_blog_list')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_menu_cat_blog_article'))) {
                Configuration::updateValue(
                    $this->name . '_menu_cat_blog_article',
                    (int) Tools::getValue($this->name . '_menu_cat_blog_article')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_menu_cat_blog_empty'))) {
                Configuration::updateValue(
                    $this->name . '_menu_cat_blog_empty',
                    (int) Tools::getValue($this->name . '_menu_cat_blog_empty')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_menu_cat_home_link'))) {
                Configuration::updateValue(
                    $this->name . '_menu_cat_home_link',
                    (int) Tools::getValue($this->name . '_menu_cat_home_link')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_menu_cat_home_img'))) {
                Configuration::updateValue(
                    $this->name . '_menu_cat_home_img',
                    (int) Tools::getValue($this->name . '_menu_cat_home_img')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_menu_cat_blog_nbnews'))) {
                Configuration::updateValue(
                    $this->name . '_menu_cat_blog_nbnews',
                    (int) Tools::getValue($this->name . '_menu_cat_blog_nbnews')
                );
            }

            Tools::redirectAdmin($this->confpath . '&configCategories&success');
        } elseif (Tools::isSubmit('submitColorBlog')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $menu_color = Tools::getValue('menu_color');
            $menu_hover = Tools::getValue('menu_hover');
            $read_color = Tools::getValue('read_color');
            $hover_color = Tools::getValue('hover_color');
            $title_color = Tools::getValue('title_color');
            $text_color = Tools::getValue('text_color');
            $menu_link = Tools::getValue('menu_link');
            $link_read = Tools::getValue('link_read');
            $article_title = Tools::getValue('article_title');
            $article_text = Tools::getValue('article_text');
            $block_categories = Tools::getValue('block_categories');
            $block_categories_link = Tools::getValue('block_categories_link');
            $block_title = Tools::getValue('block_title');
            $block_btn = Tools::getValue('block_btn');
            $block_btn_hover = Tools::getValue('block_btn_hover');
            $categorie_block_background = Tools::getValue('categorie_block_background');
            $categorie_block_background_hover = Tools::getValue('categorie_block_background_hover');
            $article_background = Tools::getValue('article_background');
            $ariane_color = Tools::getValue('ariane_color');
            $ariane_color_text = Tools::getValue('ariane_color_text');
            $ariane_border = Tools::getValue('ariane_border');
            $block_categories_link_btn = Tools::getValue('block_categories_link_btn');
            $sharing_icon_color = Tools::getValue('sharing_icon_color');
            $liste_colors = NewsClass::getColorHome((int) $this->context->shop->id);

            if (!isset($liste_colors[0]) || $liste_colors[0] == '' || $liste_colors[0] == null) {
                if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
					INSERT INTO `' . bqSQL(_DB_PREFIX_) . 'prestablog_color`
					(`menu_color`,`read_color`,`hover_color`,`title_color`,`text_color`,`menu_hover`,`menu_link`,`link_read`,`article_title`,`article_text`,`block_categories`,`block_categories_link`,`block_title`,`block_btn`,`categorie_block_background`,`article_background`,`categorie_block_background_hover`,`block_btn_hover`,`id_shop`,`ariane_color`,`ariane_color_text`,`ariane_border`,`block_categories_link_btn`,`sharing_icon_color`)
					VALUES
					("' . $menu_color . '","' . $read_color . '","' . $hover_color . '","' . $title_color . '","' . $text_color . '","' . $menu_hover . '","' . $menu_link . '","' . $link_read . '","' . $article_title . '","' . $article_text . '","' . $block_categories . '","' . $block_categories_link . '","' . $block_title . '","' . $block_btn . '","' . $categorie_block_background . '","' . $article_background . '","' . $categorie_block_background_hover . '","' . $block_btn_hover . '","' . (int) $this->context->shop->id . '","' . $ariane_color . '","' . $ariane_color_text . '","' . $ariane_border . '","' . $block_categories_link_btn . '","' . $sharing_icon_color . '")')) {
                    return false;
                }
            } else {
                if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
					  UPDATE `' . bqSQL(_DB_PREFIX_) . 'prestablog_color`
					  SET menu_color="' . $menu_color . '", read_color="' . $read_color . '", hover_color="' . $hover_color . '", title_color="' . $title_color . '", text_color="' . $text_color . '", menu_hover="' . $menu_hover . '", menu_link="' . $menu_link . '", link_read="' . $link_read . '", article_title="' . $article_title . '", article_text="' . $article_text . '", block_categories="' . $block_categories . '", block_categories_link="' . $block_categories_link . '", block_title="' . $block_title . '", block_btn="' . $block_btn . '", categorie_block_background="' . $categorie_block_background . '", article_background="' . $article_background . '", categorie_block_background_hover="' . $categorie_block_background_hover . '", block_btn_hover="' . $block_btn_hover . '", ariane_color="' . $ariane_color . '", ariane_color_text="' . $ariane_color_text . '", ariane_border="' . $ariane_border . '", block_categories_link_btn="' . $block_categories_link_btn . '", sharing_icon_color="' . $sharing_icon_color . '"
					  WHERE id_shop=' . (int) $this->context->shop->id)) {
                    return false;
                }
            }

            $liste_colors = NewsClass::getColorHome((int) $this->context->shop->id);
            $menu_color_db = $liste_colors[0]['menu_color'];
            $menu_hover_db = $liste_colors[0]['menu_hover'];
            $read_color_db = $liste_colors[0]['read_color'];
            $hover_color_db = $liste_colors[0]['hover_color'];
            $title_color_db = $liste_colors[0]['title_color'];
            $text_color_db = $liste_colors[0]['text_color'];
            $menu_link_db = $liste_colors[0]['menu_link'];
            $link_read_db = $liste_colors[0]['link_read'];
            $article_title_db = $liste_colors[0]['article_title'];
            $article_text_db = $liste_colors[0]['article_text'];
            $block_categories_db = $liste_colors[0]['block_categories'];
            $block_categories_link_db = $liste_colors[0]['block_categories_link'];
            $block_title_db = $liste_colors[0]['block_title'];
            $block_btn_db = $liste_colors[0]['block_btn'];
            $block_btn_hover_db = $liste_colors[0]['block_btn_hover'];
            $categorie_block_background_db = $liste_colors[0]['categorie_block_background'];
            $categorie_block_background_hover_db = $liste_colors[0]['categorie_block_background_hover'];
            $article_background_db = $liste_colors[0]['article_background'];
            $ariane_color_db = $liste_colors[0]['ariane_color'];
            $ariane_color_text_db = $liste_colors[0]['ariane_color_text'];
            $ariane_border_db = $liste_colors[0]['ariane_border'];
            $block_categories_link_btn_db = $liste_colors[0]['block_categories_link_btn'];
            $sharing_icon_color = $liste_colors[0]['sharing_icon_color'];

            $presta = '/**
            * (c) Prestablog
            *
            * MODULE PrestaBlog
            *
            * @author    Prestablog
            * @copyright Copyright (c) permanent, Prestablog
            * @license   Commercial
            */
           ';
            $color_menu = '#prestablog_menu_cat nav ul, img.logo_home, #menu-mobile {
             list-style: none;
             background-color: ' . $menu_color . '!important;
           }';
            $hover_menu = '#prestablog_menu_cat nav ul li:hover {
             background: ' . $menu_hover . '!important;
           }';
            $color_read = '.prestablog_more {
             display: block;
             background-color: ' . $read_color . '!important;
           }';
            // .prestablog_more a: couleur texte
            $color_hover = '.prestablog_more a.blog_link:hover, .prestablog_more .comments:hover, .prestablog_more a.blog_link:hover::before, .prestablog_more .comments:hover::before {
             background-color: ' . $hover_color . '!important;
             color: #fff;
           }';

            // ajouter prestablogfronth2 blog_list h3 ...
            $color_title = '#blog_list_1-7 .block_bas h3 a, #blog_list_1-7 .block_bas .h3title a, .prestablog .block_bas h3 a, .prestablog .block_bas .h3title a {
            color:' . $title_color . '!important;
           }';

            $color_text = '#blog_list_1-7 p, .date_blog-cat {
             margin: 12px 0px;
             color: ' . $text_color . '!important;
           }';
            $link_menu = '#prestablog_menu_cat nav ul li a, #prestablog_menu_cat nav ul li i, #prestablog_menu_cat span, #menu-mobile {
             color: ' . $menu_link . '!important;
           }';
            $read_link = '#blog_list_1-7 a.blog_link, #blog_list_1-7 a.comments, .prestablog_more, .prestablog_more a {
             color: ' . $link_read . '!important;
           }';
            $title_article = '#prestablogfont h1, #prestablogfont h2, #prestablogfont h3, #prestablogfont h4, #prestablogfont h5, #prestablogfont h6, #prestablog_article{
             color:' . $article_title . '!important;
           }';
            $text_article = '#prestablogfont p, #prestablogfont ul, #prestablogfont li {
             color: ' . $article_text . '!important;
           }';
            $categories_block = '.block-categories.prestablog {
             background: ' . $block_categories . '!important;
           }';
            $categories_block_link = '.block-categories.prestablog a.link_block, #prestablog_catliste a {
             color: ' . $block_categories_link . '!important;
           }';
            $categories_block_link_btn = '.block-categories.prestablog a.btn_link {
             color: ' . $block_categories_link_btn . '!important;
           }';
            $icon_sharing_color = '.blogsoc-icon {
             background-color: ' . $sharing_icon_color . '!important;
           }';
            $title_block = '.title_block {
             color: ' . $block_title . '!important;
           }';
            $btn_block = '#prestablog_lastliste a.btn-primary, #prestablog_catliste a.btn-primary, #prestablog_dateliste a.btn-primary, #prestablog_block_rss a, #prestablog_bloc_search .btn.button-search {
             background-color: ' . $block_btn . '!important;
           }';
            $btn_block_hover = '#prestablog_lastliste a.btn-primary:hover, #prestablog_catliste a.btn-primary:hover, #prestablog_dateliste a.btn-primary:hover, #prestablog_block_rss a:hover, #prestablog_bloc_search .btn.button-search:hover {
             background-color: ' . $block_btn_hover . '!important;
           }';
            $background_article = '#prestablogfront, .prestablogExtra, #prestablog-fb-comments, #prestablog-comments, #prestablog-rating, #prestablogauthor, time.date span, .info_blog span {
             background-color: ' . $article_background . '!important;
           }';
            $background_categorie_block = '#blog_list_1-7 .block_cont, .prestablog .block_cont {
             background-color: ' . $categorie_block_background . '!important;
           }';
            $background_categorie_block_hover = '#blog_list_1-7 li:hover .block_cont, .prestablog:hover .block_cont {
             background-color: ' . $categorie_block_background_hover . '!important;
           }';
            $ariane_block_color = 'div.prestablog_pagination span.current {
             background-color: ' . $ariane_color . '!important;
           }';
            $ariane_block_color_text = 'div.prestablog_pagination span.current {
             color: ' . $ariane_color_text . '!important;
           }';
            $ariane_border_color = 'div.prestablog_pagination span.current {
             border: 1px solid ' . $ariane_border . '!important;
			}';
            $fp = fopen(_PS_MODULE_DIR_ . 'prestablog/views/css/blog' . (int) $this->context->shop->id . '.css', 'w');
            fwrite($fp, $presta);
            if ($menu_color_db != '0') {
                fwrite($fp, $color_menu);
            }
            if ($menu_hover_db != '0') {
                fwrite($fp, $hover_menu);
            }
            if ($read_color_db != '0') {
                fwrite($fp, $color_read);
            }
            if ($hover_color_db != '0') {
                fwrite($fp, $color_hover);
            }
            if ($title_color_db != '0') {
                fwrite($fp, $color_title);
            }
            if ($text_color_db != '0') {
                fwrite($fp, $color_text);
            }
            if ($menu_link_db != '0') {
                fwrite($fp, $link_menu);
            }
            if ($link_read_db != '0') {
                fwrite($fp, $read_link);
            }
            if ($article_title_db != '0') {
                fwrite($fp, $title_article);
            }
            if ($article_text_db != '0') {
                fwrite($fp, $text_article);
            }
            if ($block_categories_db != '0') {
                fwrite($fp, $categories_block);
            }
            if ($block_categories_link_db != '0') {
                fwrite($fp, $categories_block_link);
            }
            if ($block_categories_link_btn_db != '0') {
                fwrite($fp, $categories_block_link_btn);
            }
            if ($sharing_icon_color != '0') {
                fwrite($fp, $icon_sharing_color);
            }
            if ($block_title_db != '0') {
                fwrite($fp, $title_block);
            }
            if ($block_btn_db != '0') {
                fwrite($fp, $btn_block);
            }
            if ($article_background_db != '0') {
                fwrite($fp, $background_article);
            }
            if ($categorie_block_background_db != '0') {
                fwrite($fp, $background_categorie_block);
            }
            if ($categorie_block_background_hover_db != '0') {
                fwrite($fp, $background_categorie_block_hover);
            }
            if ($block_btn_hover_db != '0') {
                fwrite($fp, $btn_block_hover);
            }
            if ($ariane_color_db != '0') {
                fwrite($fp, $ariane_block_color);
            }
            if ($ariane_color_text_db != '0') {
                fwrite($fp, $ariane_block_color_text);
            }
            if ($ariane_border_db != '0') {
                fwrite($fp, $ariane_border_color);
            }
            fclose($fp);
            Tools::redirectAdmin($this->confpath . '&colorBlog&success');
        } elseif (Tools::isSubmit('submitConfComment')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_comment_actif'))) {
                Configuration::updateValue(
                    $this->name . '_comment_actif',
                    (int) Tools::getValue($this->name . '_comment_actif')
                );
                if ((int) Tools::getValue($this->name . '_comment_actif') == 1) {
                    Configuration::updateValue($this->name . '_commentfb_actif', 0);
                }
            }
            if (is_numeric(Tools::getValue($this->name . '_captcha_actif'))) {
                Configuration::updateValue(
                    $this->name . '_captcha_actif',
                    (int) Tools::getValue($this->name . '_captcha_actif')
                );
            }
            if (Tools::getValue($this->name . '_captcha_public_key')) {
                Configuration::updateValue(
                    $this->name . '_captcha_public_key',
                    Tools::getValue($this->name . '_captcha_public_key')
                );
            }
            if (Tools::getValue($this->name . '_captcha_private_key')) {
                Configuration::updateValue(
                    $this->name . '_captcha_private_key',
                    Tools::getValue($this->name . '_captcha_private_key')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_comment_only_login'))) {
                Configuration::updateValue(
                    $this->name . '_comment_only_login',
                    (int) Tools::getValue($this->name . '_comment_only_login')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_comment_auto_actif'))) {
                Configuration::updateValue(
                    $this->name . '_comment_auto_actif',
                    (int) Tools::getValue($this->name . '_comment_auto_actif')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_comment_alert_admin'))) {
                Configuration::updateValue(
                    $this->name . '_comment_alert_admin',
                    (int) Tools::getValue($this->name . '_comment_alert_admin')
                );
            }
            if (is_numeric(Tools::getValue($this->name . '_comment_subscription'))) {
                Configuration::updateValue(
                    $this->name . '_comment_subscription',
                    (int) Tools::getValue($this->name . '_comment_subscription')
                );
            }

            Configuration::updateValue(
                $this->name . '_comment_admin_mail',
                Tools::getValue($this->name . '_comment_admin_mail')
            );

            Tools::redirectAdmin($this->confpath . '&configComments&success');
        } elseif (Tools::isSubmit('submitConfCss')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            $css = Tools::getValue('content_css');
            $fp = fopen(_PS_MODULE_DIR_ . 'prestablog/views/css/custom' . (int) $this->context->shop->id . '.css', 'w');
            $presta = '/**
			 * (c) Prestablog
			 *
			 * MODULE PrestaBlog
			 *
			 * @author    Prestablog
			 * @copyright Copyright (c) permanent, Prestablog
			 * @license   Commercial
			 */
			 ';
            fwrite($fp, $presta);
            fclose($fp);
            file_put_contents(_PS_MODULE_DIR_ . 'prestablog/views/css/custom' . (int) $this->context->shop->id . '.css', $css);
            Tools::redirectAdmin($this->confpath . '&colorBlog&success');
        } elseif (Tools::isSubmit('submitConfCommentFB')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue($this->name . '_commentfb_actif'))) {
                Configuration::updateValue(
                    $this->name . '_commentfb_actif',
                    (int) Tools::getValue($this->name . '_commentfb_actif')
                );
                if ((int) Tools::getValue($this->name . '_commentfb_actif') == 1) {
                    Configuration::updateValue($this->name . '_comment_actif', 0);
                }
            }
            if (is_numeric(Tools::getValue($this->name . '_commentfb_nombre'))) {
                Configuration::updateValue(
                    $this->name . '_commentfb_nombre',
                    (int) Tools::getValue($this->name . '_commentfb_nombre')
                );
            }

            if (is_numeric(Tools::getValue($this->name . '_commentfb_apiId'))) {
                Configuration::updateValue(
                    $this->name . '_commentfb_apiId',
                    Tools::getValue($this->name . '_commentfb_apiId')
                );
            }

            if (is_numeric(Tools::getValue($this->name . '_commentfb_modosId'))) {
                $list_fb_moderators = json_decode(Configuration::get($this->name . '_commentfb_modosId'), true);
                $list_fb_moderators[] = Tools::getValue($this->name . '_commentfb_modosId');
                $list_fb_moderators = array_unique($list_fb_moderators);
                Configuration::updateValue($this->name . '_commentfb_modosId', json_encode($list_fb_moderators));
            }

            Tools::redirectAdmin($this->confpath . '&configComments&success');
        } elseif (Tools::isSubmit('deleteFacebookModerator')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (is_numeric(Tools::getValue('fb_moderator_id'))) {
                $list_fb_moderators = json_decode(Configuration::get($this->name . '_commentfb_modosId'), true);
                $list_fb_moderators = array_diff($list_fb_moderators, [Tools::getValue('fb_moderator_id')]);
                $list_fb_moderators = array_unique($list_fb_moderators);
                Configuration::updateValue($this->name . '_commentfb_modosId', json_encode($list_fb_moderators));
            }

            Tools::redirectAdmin($this->confpath . '&configComments&success');
        } elseif (Tools::isSubmit('submitParseXml')) {
            // Permissions system
            $rulesAuthor = 'can_use_tool';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            include_once $this->module_path . '/class/Xml.php';
            $xml_string = trim(Tools::file_get_contents(_PS_UPLOAD_DIR_ . Configuration::get($this->name . '_import_xml')));
            $xml_array = Xml::toArray(Xml::build($xml_string));
            if (count($xml_array['rss']['channel']['item']) > 0) {
                $liste_items = [];

                if (isset($xml_array['rss']['channel']['item']['title'])) {
                    $liste_items[0] = $xml_array['rss']['channel']['item'];
                } else {
                    $liste_items = $xml_array['rss']['channel']['item'];
                }

                foreach ($liste_items as $v_item) {
                    if ($v_item['wp:post_type'] == 'post') {
                        $post = new NewsClass();
                        $post->id_shop = (int) $this->context->shop->id;
                        $post->date = $v_item['wp:post_date'];
                        $post->langues = json_encode(
                            [
                                0 => (int) Tools::getValue('import_xml_langue'),
                            ]
                        );

                        if ($v_item['wp:status'] == 'publish') {
                            $post->actif = 1;
                        } else {
                            $post->actif = 0;
                        }

                        $post->title[(int) Tools::getValue('import_xml_langue')] = $v_item['title'];
                        $post->paragraph[(int) Tools::getValue('import_xml_langue')] = $v_item['excerpt:encoded'];
                        $post->content[(int) Tools::getValue('import_xml_langue')] = $v_item['content:encoded'];
                        $post->meta_title[(int) Tools::getValue('import_xml_langue')] = $v_item['title'];

                        if (trim($v_item['wp:post_name']) == '') {
                            $v_item['wp:post_name'] = PrestaBlog::prestablogFilter(Tools::link_rewrite($v_item['title']));
                        } else {
                            $v_item['wp:post_name'] = PrestaBlog::prestablogFilter(Tools::link_rewrite($v_item['wp:post_name']));
                        }

                        $post->link_rewrite[(int) Tools::getValue('import_xml_langue')] = $v_item['wp:post_name'];

                        /* gestion des catégories et tags */
                        if (isset($v_item['category']) && count($v_item['category']) > 0) {
                            $import_categories = [];
                            $import_categories_id = [];
                            if (isset($v_item['category']['@domain'])) {
                                /* gestion des catégories */
                                if ($v_item['category']['@domain'] == 'category') {
                                    $import_categories[] = $v_item['category']['@'];
                                }

                                /* gestion des tags > keywords */
                                if ($v_item['category']['@domain'] == 'post_tag') {
                                    $key_words = $v_item['category']['@'];
                                }
                            } else {
                                /* gestion des catégories */
                                if (count($v_item['category']) > 0) {
                                    foreach ($v_item['category'] as $v_category) {
                                        if ($v_category['@domain'] == 'category') {
                                            $import_categories[] = $v_category['@'];
                                        }
                                    }
                                    $import_categories = array_unique($import_categories);
                                }

                                /** gestion des tags > keywords */
                                $import_tags = [];
                                if (count($v_item['category']) > 0) {
                                    foreach ($v_item['category'] as $v_tag) {
                                        if ($v_tag['@domain'] == 'post_tag') {
                                            $import_tags[] = $v_tag['@'];
                                        }
                                    }
                                    $import_tags = array_unique($import_tags);
                                }
                                $key_words = '';
                                if (count($import_tags) > 0) {
                                    foreach ($import_tags as $v_import_tag) {
                                        $key_words .= $v_import_tag . ', ';
                                    }
                                }
                                $key_words = rtrim($key_words, ', ');
                            }

                            if (count($import_categories) > 0) {
                                foreach ($import_categories as $v_import_categorie) {
                                    if ($id_import_category = CategoriesClass::isCategoriesExist(
                                        (int) Tools::getValue('import_xml_langue'),
                                        $v_import_categorie
                                    )) {
                                        $import_categories_id[] = $id_import_category;
                                    } else {
                                        $categorie = new CategoriesClass();
                                        $categorie->id_shop = (int) $this->context->shop->id;
                                        $categorie->title[(int) Tools::getValue('import_xml_langue')] = $v_import_categorie;
                                        $categorie->link_rewrite[(int) Tools::getValue('import_xml_langue')] =
                      PrestaBlog::prestablogFilter(Tools::link_rewrite($v_import_categorie));
                                        $categorie->add();
                                        $import_categories_id[] = $categorie->id;
                                    }
                                }
                            }

                            $post->meta_keywords[(int) Tools::getValue('import_xml_langue')] = Tools::substr($key_words, 0, 254);
                        }

                        $post->add();
                        if ($post->id) {
                            $post->razEtatLangue((int) $post->id);
                            $post->changeActiveLangue((int) $post->id, (int) Tools::getValue('import_xml_langue'));

                            /* gestion des commentaires */
                            if (isset($v_item['wp:comment']) && count($v_item['wp:comment']) > 0) {
                                $comment = new CommentNewsClass();
                                if (isset($v_item['wp:comment']['wp:comment_author'])) {
                                    $comment->news = $post->id;

                                    $v_item['wp:comment']['wp:comment_author'] = Tools::substr($v_item['wp:comment']['wp:comment_author'], 0, 254);
                                    $comment->name = (trim($v_item['wp:comment']['wp:comment_author']) == '' ?
                      $this->trans('Nobody', [], 'Modules.Prestablog.Prestablog') : $v_item['wp:comment']['wp:comment_author']);
                                    if (Validate::isUrlOrEmpty($v_item['wp:comment']['wp:comment_author_url'])) {
                                        $comment->url = $v_item['wp:comment']['wp:comment_author_url'];
                                    }

                                    $comment->comment = $v_item['wp:comment']['wp:comment_content'];
                                    $comment->date = $v_item['wp:comment']['wp:comment_date'];

                                    if ((int) $v_item['wp:comment']['wp:comment_approved'] == 1) {
                                        $comment->actif = 1;
                                    } else {
                                        $comment->actif = 0;
                                    }

                                    $comment->add();
                                } else {
                                    foreach ($v_item['wp:comment'] as $v_comment) {
                                        $comment = new CommentNewsClass();

                                        $comment->news = $post->id;

                                        $v_comment['wp:comment_author'] = Tools::substr($v_comment['wp:comment_author'], 0, 254);
                                        $comment->name = (trim($v_comment['wp:comment_author']) == '' ?
                        $this->trans('Nobody', [], 'Modules.Prestablog.Prestablog') : $v_comment['wp:comment_author']);
                                        if (Validate::isUrlOrEmpty($v_comment['wp:comment_author_url'])) {
                                            $comment->url = $v_comment['wp:comment_author_url'];
                                        }

                                        $comment->comment = $v_comment['wp:comment_content'];
                                        $comment->date = $v_comment['wp:comment_date'];

                                        if ((int) $v_comment['wp:comment_approved'] == 1) {
                                            $comment->actif = 1;
                                        } else {
                                            $comment->actif = 0;
                                        }

                                        $comment->add();
                                    }
                                }
                            }
                            /* liaison des catégories aux articles */
                            if (count($import_categories_id) > 0) {
                                CorrespondancesCategoriesClass::updateCategoriesNews($import_categories_id, $post->id);
                            }
                        }
                    }
                }
            } else {
                $errors[] = $this->trans('No items to import', [], 'Modules.Prestablog.Prestablog');
            }

            if (!count($errors)) {
                self::unlinkFile(_PS_UPLOAD_DIR_ . Configuration::get($this->name . '_import_xml'));
                Configuration::updateValue($this->name . '_import_xml', null);
                Tools::redirectAdmin($this->confpath . '&import&feedback=Yu3Tr9r7');
            }
        } elseif (Tools::isSubmit('submitImportXml')) {
            // Permissions system
            $rulesAuthor = 'can_configure_module';
            $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

            if (!$result) {
                Tools::redirectAdmin($this->confpath . '&permission_error=1');

                return;
            }
            // Permissions system

            if (!$this->demo_mode) {
                if (isset($_FILES[$this->name . '_import_xml']) && is_uploaded_file($_FILES[$this->name . '_import_xml']['tmp_name'])) {
                    if ($_FILES[$this->name . '_import_xml']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
                        $errors[] = sprintf(
                            $this->trans('The file is too large. Maximum size allowed is: %1$d kB. The file you\'re trying to upload is: %2$d kB.', [], 'Modules.Prestablog.Prestablog'),
                            Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024,
                            number_format($_FILES[$this->name . '_import_xml']['size'] / 1024, 2, '.', '')
                        );
                    } else {
                        do {
                            $uniqid = sha1(microtime());
                        } while (file_exists(_PS_UPLOAD_DIR_ . $uniqid));
                        if (!self::copy($_FILES[$this->name . '_import_xml']['tmp_name'], _PS_UPLOAD_DIR_ . $uniqid)) {
                            $errors[] = $this->trans('File copy failed', [], 'Modules.Prestablog.Prestablog');
                        }

                        self::unlinkFile($_FILES[$this->name . '_import_xml']['tmp_name']);
                        self::unlinkFile(_PS_UPLOAD_DIR_ . Configuration::get($this->name . '_import_xml'));
                        Configuration::updateValue($this->name . '_import_xml', $uniqid);
                    }
                    Tools::redirectAdmin($this->confpath . '&import');
                } else {
                    Tools::redirectAdmin($this->confpath . '&import&feedback=2yt6wEK7');
                }
            }

            if (!count($errors)) {
                Tools::redirectAdmin($this->confpath . '&import');
            }
        }

        if ($post_en_cours) {
            if (count($errors) > 0) {
                $this->html_out .= $this->displayError(implode('<br />', $errors));
            } else {
                $this->html_out .= $this->displayConfirmation($this->trans('Settings updated successfully', [], 'Modules.Prestablog.Prestablog'));
            }
        }
    }

    public function displayWarning($warn)
    {
        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'warn' => $warn,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayWarning.tpl');
    }

    public function get_displayInfo($info)
    {
        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'info' => $info,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayInfo.tpl');
    }

    public function displayInfo($info)
    {
        $this->html_out .= $this->get_displayInfo($info);
    }

    public function moduleDatepicker($class, $time)
    {
        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'time' => $time,
            'class' => $class,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/Datepicker.tpl');
    }

    public function checkPresenceFoldersCritiques()
    {
        $errors = [];
        $success = [];

        if (!is_dir(_PS_MODULE_DIR_ . $this->name . '/mails/en')) {
            $errors[] = $this->trans('No existing the module\'s default "en" mails folder.', [], 'Modules.Prestablog.Prestablog');
            $is_extract = Tools::ZipExtract(
                _PS_MODULE_DIR_ . $this->name . '/lost/mails/en.zip',
                _PS_MODULE_DIR_ . $this->name . '/mails/'
            );
            if (!$is_extract) {
                $errors[] = $this->trans('Error extract the module\'s default "en" mails folder.', [], 'Modules.Prestablog.Prestablog');
            } else {
                $success[] = $this->trans('Restore the module\'s default "en" mails folder successfull.', [], 'Modules.Prestablog.Prestablog');
            }
        }

        if (Configuration::get($this->name . '_sitemap_actif')) {
            if (!is_dir(_PS_MODULE_DIR_ . $this->name . '/sitemap/' . (int) $this->context->shop->id)) {
                $errors[] = sprintf(
                    $this->trans('No existing the sitemap folder for %1$s', [], 'Modules.Prestablog.Prestablog'),
                    $this->context->shop->name
                );
                if (!self::makeDirectory(_PS_MODULE_DIR_ . $this->name . '/sitemap/' . (int) $this->context->shop->id)) {
                    $errors[] = sprintf(
                        $this->trans('Error creating the sitemap folder for %1$s.', [], 'Modules.Prestablog.Prestablog'),
                        $this->context->shop->name
                    );
                } else {
                    $success[] = sprintf(
                        $this->trans('Creating sitemap folder for %1$s successfull', [], 'Modules.Prestablog.Prestablog'),
                        $this->context->shop->name
                    );
                }
            }
            if (!is_writable(_PS_MODULE_DIR_ . $this->name . '/sitemap/' . (int) $this->context->shop->id)) {
                $errors[] = sprintf(
                    $this->trans('The folder %1$s not have the write permissions.', [], 'Modules.Prestablog.Prestablog'),
                    '' . _PS_MODULE_DIR_ . $this->name . '/sitemap/' . (int) $this->context->shop->id . ''
                );
            }

            if (count($errors) > 0) {
                $this->html_out = $this->displayError(implode('<br />', $errors));
                if (count($success) > 0) {
                    $this->html_out .= $this->displayConfirmation(implode('<br />', $success));
                }

                return $this->html_out;
            }
        }
    }

    private function displayNavConfiguration()
    {
        // Permissions system
        $permissions_enabled = Configuration::get($this->name . '_enable_permissions') == 1;
        $can_manage_comments = !$permissions_enabled || $this->loadAuthorAndCheckPermissions('can_manage_comments');
        $can_manage_popup = !$permissions_enabled || $this->loadAuthorAndCheckPermissions('can_manage_popup');
        $can_manage_slide = !$permissions_enabled || $this->loadAuthorAndCheckPermissions('can_manage_slide');
        $can_manage_personalised_list = !$permissions_enabled || $this->loadAuthorAndCheckPermissions('can_manage_personalised_list');
        $can_configure_module = !$permissions_enabled || $this->loadAuthorAndCheckPermissions('can_configure_module');
        $can_use_tool = !$permissions_enabled || $this->loadAuthorAndCheckPermissions('can_use_tool');

        $languages = Language::getLanguages(true);

        $this->context->smarty->assign(['homepath' => $this->confpath,
            'newspath' => $this->confpath . '&newsListe&languesup=' . (int) $this->context->language->id,
            'commentspath' => $this->confpath . '&commentListe',
            'categoriespath' => $this->confpath . '&catListe',
            'customizenewspath' => $this->confpath . '&configSubBlocks',
            'prestaboost' => Module::getInstanceByName('prestaboost'),
            'popuppath' => $this->confpath . '&class=PopupClass&displayContent',
            'languagecount' => count($languages),
            'multilangslidepath' => $this->confpath . '&configSlide&languesup=' . $languages[0]['id_lang'],
            'slidepath' => $this->confpath . '&configSlide&languesup=' . (int) $this->context->language->id,
            'antispampath' => $this->confpath . '&configAntiSpam',
            'importpath' => $this->confpath . '&import',
            'sitemappath' => $this->confpath . '&sitemap',
            'configaipath' => $this->confpath . '&configAi',
            'configthemepath' => $this->confpath . '&configTheme',
            'configblogpath' => $this->confpath . '&pageBlog',
            'configcategoriespath' => $this->confpath . '&configCategories',
            'configblocspath' => $this->confpath . '&configBlocs',
            'configcommentspath' => $this->confpath . '&configComments',
            'configmodulepath' => $this->confpath . '&configModule',
            'configcolorpath' => $this->confpath . '&colorBlog',
            'configimagepath' => $this->confpath . '&configImage',
            'authorpath' => $this->confpath . '&authorList',
            'hasaccount' => AuthorClass::checkAuthor($this->context->employee->id) != '' && AuthorClass::checkAuthor($this->context->employee->id) != null,
            'myprofilepath' => $this->confpath . '&accountGest',
            'configauthorpath' => $this->confpath . '&configAuthor',
            'documentationpath' => $this->confpath . '&documentation',
            'informationspath' => $this->confpath . '&informations',
            'contactpath' => $this->confpath . '&contact',
            'isdemomode' => $this->demo_mode,
            'version' => $this->version,
            'can_manage_comments' => $can_manage_comments,
            'can_manage_popup' => $can_manage_popup,
            'can_manage_slide' => $can_manage_slide,
            'can_manage_personalised_list' => $can_manage_personalised_list,
            'can_configure_module' => $can_configure_module,
            'can_use_tool' => $can_use_tool,
            'blogpageurl' => self::prestablogUrl([]),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/NavigationConfiguration.tpl');
    }

    public function getContent()
    {
        $this->backoffice_content = true;

        if (Tools::version_compare($this->version, self::getModuleDataBaseVersion(), '>')) {
            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/config.xml')) {
                self::unlinkFile(_PS_MODULE_DIR_ . $this->name . '/config.xml');
            }

            foreach (glob(_PS_MODULE_DIR_ . $this->name . '/config_[a-z][a-z].{xml}', GLOB_BRACE) as $config_module_file) {
                if (!is_dir($config_module_file)) {
                    self::unlinkFile($config_module_file);
                }
            }

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules'));
        }

        if ($error_critique = $this->checkPresenceFoldersCritiques()) {
            return $error_critique;
        }

        $this->postForm();

        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->context->controller->addCSS($this->_path . 'views/css/jquery-ui.css');
        $this->html_out .= $this->displayNavConfiguration();

        if (Tools::isSubmit('addNews')
          || Tools::isSubmit('editNews')
          || Tools::isSubmit('submitAddNews')
          || (Tools::isSubmit('submitUpdateNews') && Tools::getValue('idN'))
        ) {
            $this->displayFormNews();
        } elseif (Tools::isSubmit('addSlide')) {
            $this->displayAddSlide();
        } elseif (Tools::isSubmit('editSlide') && Tools::getValue('idS')) {
            $this->displayEditSlide();
        } elseif (Tools::isSubmit('addCat')
        || Tools::isSubmit('editCat')
        || Tools::isSubmit('submitAddCat')
        || (Tools::isSubmit('submitUpdateCat') && Tools::getValue('idC'))
        ) {
            $this->displayFormCategories();
        } elseif (Tools::isSubmit('orderCat')) {
            $this->displayOrderCategories();
        } elseif (Tools::isSubmit('submitOrderCat')) {
            if (Tools::getValue('newOrderCat')) {
                $new_order_cat = [];
                foreach (preg_split('/\&/', Tools::getValue('newOrderCat')) as $key => $value) {
                    $current_order_list = preg_split('/\=/', $value);

                    if (preg_match('/\d+/', $current_order_list[0], $match_id)) {
                        $id_prestablog_categorie = (int) $match_id[0];
                    } else {
                        $id_prestablog_categorie = 0;
                    }

                    $parent = (int) $current_order_list[1];

                    $new_order_cat[] = [
                        'id_prestablog_categorie' => (int) $id_prestablog_categorie,
                        'parent' => (int) $parent,
                        'position' => (int) $key,
                    ];
                }

                foreach ($new_order_cat as $value) {
                    CategoriesClass::updatePosition(
                        (int) $value['id_prestablog_categorie'],
                        (int) $value['parent'],
                        (int) $value['position']
                    );
                }
            }
            $this->displayOrderCategories();
        } elseif (Tools::isSubmit('addAntiSpam')
        || Tools::isSubmit('editAntiSpam')
        || Tools::isSubmit('submitAddAntiSpam')
        || (Tools::isSubmit('submitUpdateAntiSpam') && Tools::getValue('idAS'))
        ) {
            $this->displayFormAntiSpam();
        } elseif (Tools::isSubmit('submitReplyComment')) {
            $errors = [];

            $id_parent = (int) Tools::getValue('id_parent');
            $parent_comment = new CommentNewsClass($id_parent);

            if (!Validate::isLoadedObject($parent_comment) || $parent_comment->actif != 1) {
                $errors[] = $this->trans('You cannot reply to a comment that does not exist or is not approved.', [], 'Modules.Prestablog.Prestablog');
            }

            $name = Tools::getValue('name');
            $date = Tools::getValue('date');
            $comment_text = Tools::getValue('comment');

            if (empty($name)) {
                $errors[] = $this->trans('The Name field is required.', [], 'Modules.Prestablog.Prestablog');
            }

            if (empty($date)) {
                $errors[] = $this->trans('The Date field is required.', [], 'Modules.Prestablog.Prestablog');
            }

            if (empty($comment_text)) {
                $errors[] = $this->trans('The Comment field is required.', [], 'Modules.Prestablog.Prestablog');
            }

            if (!count($errors)) {
                $news_id = $parent_comment->news;
                $actif = 1;

                $reply_comment = new CommentNewsClass();
                $reply_comment->news = $news_id;
                $reply_comment->date = $date;
                $reply_comment->name = $name;
                $reply_comment->comment = $comment_text;
                $reply_comment->actif = $actif;
                $reply_comment->id_parent = $id_parent;
                $reply_comment->is_admin = 1;

                if ($reply_comment->save()) {
                    Tools::redirectAdmin($this->confpath . '&commentListe&conf=4');
                } else {
                    $errors[] = $this->trans('An error occurred while saving the reply.', [], 'Modules.Prestablog.Prestablog');
                }
            }

            if (count($errors)) {
                $this->context->smarty->assign('errors', $errors);
                $this->displayReplyCommentForm($id_parent);

                return $this->html_out;
            }
        } elseif (Tools::isSubmit('replyComment')) {
            $id_parent = (int) Tools::getValue('idC');
            $this->displayReplyCommentForm($id_parent);
        } elseif (Tools::isSubmit('editComment')
        || (Tools::isSubmit('submitUpdateComment') && Tools::getValue('idC'))
        ) {
            $this->displayFormComments();
        } elseif (Tools::isSubmit('addSubBlock')
        || Tools::isSubmit('editSubBlock')
        || Tools::isSubmit('submitAddSubBlock')
        || (Tools::isSubmit('submitUpdateSubBlock') && Tools::getValue('idSB'))
        ) {
            $this->displayFormSubBlocks();
        } elseif (Tools::isSubmit('submitAddSubBlockFront')
        || (Tools::isSubmit('submitUpdateSubBlockFront') && Tools::getValue('idSBF'))
        ) {
            $this->displayPageBlog();
        } elseif (Tools::isSubmit('addpopup')
        || Tools::isSubmit('editpopup')
        || Tools::isSubmit('updatepopup')
        || Tools::isSubmit('addpopupsubmit')
        || Tools::isSubmit('editpopupsubmit')
        || (Tools::isSubmit('updatepopup') && Tools::getValue('idN'))
        || Tools::isSubmit('deletepopup')
        || Tools::isSubmit('statuspopup')
        ) {
            $this->getContentPopup();
        } elseif (Tools::isSubmit('pageBlog')) {
            $this->displayPageBlog();
        } elseif (Tools::isSubmit('configAntiSpam')) {
            $this->displayConfigAntiSpam();
        } elseif (Tools::isSubmit('sitemap')) {
            $this->displaySitemap();
        } elseif (Tools::isSubmit('configAi') || Tools::isSubmit('submitAiConfig')) {
            $this->displayConfigAi();
        } elseif (Tools::isSubmit('configModule')) {
            $this->displayConf();
        } elseif (Tools::isSubmit('configTheme')) {
            $this->displayConfTheme();
        } elseif (Tools::isSubmit('configWizard')) {
            $this->displayConfWizard();
        } elseif (Tools::isSubmit('configSubBlocks')) {
            $this->displayListeSubBlocks();
        } elseif (Tools::isSubmit('displayContent')) {
            $this->getContentPopup();
        } elseif (Tools::isSubmit('configCategories')) {
            $this->displayConfCategories();
        } elseif (Tools::isSubmit('configBlocs')) {
            $this->displayConfBlocs();
        } elseif (Tools::isSubmit('configProductTab')) {
            $this->displayConfProductTab();
        } elseif (Tools::isSubmit('configComments')) {
            $this->displayConfComments();
        } elseif (Tools::isSubmit('colorBlog')) {
            $this->displayColorBlog();
        } elseif (Tools::isSubmit('authorList')) {
            $this->displayAuthorList();
        } elseif (Tools::isSubmit('accountGest')) {
            $this->displayAccountGestion();
        } elseif (Tools::isSubmit('configAuthor')) {
            $this->displayConfigAuthor();
        } elseif (Tools::isSubmit('addAuthor')) {
            $this->displayAddAuthor();
        } elseif (Tools::isSubmit('authorPermissions')) {
            $this->displayAuthorPermissions();
        } elseif (Tools::isSubmit('configSlide')) {
            $this->displaySlideSystem();
        } elseif (Tools::isSubmit('debug')) {
            $this->displayDebug();
        } elseif (Tools::isSubmit('documentation')) {
            $this->displayDocumentation();
        } elseif (Tools::isSubmit('informations')) {
            $this->displayInformations();
        } elseif (Tools::isSubmit('contact')) {
            $this->displayContact();
        } elseif (Tools::isSubmit('import')) {
            $this->displayImport();
        } elseif (Tools::isSubmit('catListe')) {
            $this->displayListeCategories();
        } elseif (Tools::isSubmit('newsListe')) {
            $this->displayListeNews();
        } elseif (Tools::isSubmit('commentListe')) {
            $this->displayListeComments();
        } elseif (Tools::isSubmit('submitDisplaysliderModule')) {
            $this->postProcess();
            Tools::redirectAdmin($this->confpath . '&configTheme');
        } elseif (Tools::isSubmit('configImage')) {
            $this->displayConfPictures();
        } else {
            $this->displayHome();
        }

        return $this->html_out;
    }

    public function getContentPopup()
    {
        $pathM = AdminController::$currentIndex . '&configure=prestablog&token=' . Tools::getAdminTokenLite('AdminModules') . '&displayContent';

        if (!Tools::getValue('class')) {
            Tools::redirectAdmin($pathM . '&class=PopupClass');
        }

        $update_process = false;
        $errors = [];
        $warnings = [];

        if ((int) Tools::getIsset('success')) {
            $this->html_out .= $this->displayConfirmation($this->trans('Settings updated successfully', [], 'Modules.Prestablog.Prestablog'));
        }
        if ((int) Tools::getIsset('undo') || (int) Tools::getIsset('error')) {
            $this->html_out .= $this->html_out .= $this->displayError('The current action was canceled');
        }
        foreach ($this->class_used['table'] as $object_model) {
            $pp = $this->pp_conf . '&class=' . $object_model;
            if (Tools::getValue('class') == $object_model) {
                $definition = ObjectModel::getDefinition($object_model);
                if (Tools::isSubmit('statuspopup')) {
                    $process_model = self::createDynInstance($object_model);
                    if (!$process_model->changeState((int) Tools::getValue($definition['primary']))) {
                        $errors[] = $this->trans('Could not change status.', [], 'Modules.Prestablog.Prestablog');
                    } else {
                        Tools::redirectAdmin($pp . '&displayContent&success');
                    }
                    $update_process = true;
                }
                if (Tools::isSubmit('deletepopup')) {
                    $process_model = self::createDynInstance(
                        $object_model,
                        [(int) Tools::getValue($definition['primary'])]
                    );
                    if (!$process_model->deletepopup()) {
                        $errors[] = $this->trans('Could not delete.', [], 'Modules.Prestablog.Prestablog');
                    } else {
                        Tools::redirectAdmin($pp . '&displayContent&success');
                    }
                    $update_process = true;
                }
                if (Tools::isSubmit('submitBulkdeletepopup')) {
                    if (Tools::getValue($definition['table'] . 'Box')) {
                        foreach (Tools::getValue($definition['table'] . 'Box') as $id) {
                            $process_model = self::createDynInstance($object_model, [(int) $id]);
                            if (!$process_model->deletepopup()) {
                                $errors[] = sprintf($this->trans('Could not delete %1$s', [], 'Modules.Prestablog.Prestablog'), $id);
                            }
                        }
                        if (!count($errors) > 0) {
                            Tools::redirectAdmin($pp . '&displayContent&success');
                        }
                    }
                    $update_process = true;
                }
                if (Tools::isSubmit('addpopupsubmit')) {
                    // Permissions system
                    $rulesAuthor = 'can_manage_popup';
                    $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

                    if (!$result) {
                        Tools::redirectAdmin($this->confpath . '&permission_error=1');

                        return;
                    }
                    // Permissions system

                    if (Tools::getValue('date_start') == '') {
                        $errorpopup = 'error1';
                        $update_process = false;
                    } elseif (Tools::getValue('date_stop') == '') {
                        $errorpopup = 'error2';
                        $update_process = false;
                    } elseif (Tools::getValue('title_' . (int) $this->context->language->id) == '') {
                        $errorpopup = 'error3';
                        $update_process = false;
                    } elseif (Tools::getValue('content_' . (int) $this->context->language->id) == '') {
                        $errorpopup = 'error4';
                        $update_process = false;
                    } else {
                        $process_model = self::createDynInstance($object_model);
                        $process_model->copyFromPost();
                        if ($process_model->add()) {
                            Tools::redirectAdmin($pp . '&displayContent&success');
                            $update_process = true;
                        }
                    }
                } elseif (Tools::isSubmit('editpopupsubmit')) {
                    // Permissions system
                    $rulesAuthor = 'can_manage_popup';
                    $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

                    if (!$result) {
                        Tools::redirectAdmin($this->confpath . '&permission_error=1');

                        return;
                    }
                    // Permissions system

                    $process_model = self::createDynInstance(
                        $object_model,
                        [(int) Tools::getValue($definition['primary'])]
                    );
                    $process_model->copyFromPost();
                    if ($process_model->update()) {
                        Tools::redirectAdmin(
                            $pp . '&' . $definition['primary'] . '=' . (int) Tools::getValue(
                                $definition['primary']
                            ) . '&displayContent&success'
                        );
                    }
                    $update_process = true;
                }
            }
        }

        if (Tools::getValue('class') && in_array(Tools::getValue('class'), $this->class_used['table'])) {
            $current_class = Tools::getValue('class');
            if (!class_exists($current_class)) {
                $this->html_out .= $this->displayError($this->trans('This class doesn\'t exists: ', [], 'Modules.Prestablog.Prestablog') . $current_class);
            } else {
                $object_model = self::createDynInstance($current_class, [null, null, null, $this->getTranslator()]);

                if (is_object($object_model)) {
                    $definition_lang = $object_model->definitionLang();

                    if (!Tools::isSubmit('add')
              && !Tools::isSubmit('edit')
              && !Tools::getIsset('add' . $definition_lang['tableName'])
              && !Tools::getIsset('update' . $definition_lang['tableName'])
                    ) {
                        $this->html_out .= $object_model->displayList();
                    }

                    if ((Tools::getIsset('add' . $definition_lang['tableName']) || Tools::isSubmit('addpopup')) && Tools::getIsset('error1')) {
                        $this->html_out .= $this->displayError($this->trans('Please choose a starting date', [], 'Modules.Prestablog.Prestablog'));
                        $this->html_out .= $object_model->displayForm('add');
                    }
                    if ((Tools::getIsset('add' . $definition_lang['tableName']) || Tools::isSubmit('addpopup')) && Tools::getIsset('error2')) {
                        $this->html_out .= $this->displayError($this->trans('Please choose a stopping date', [], 'Modules.Prestablog.Prestablog'));
                        $this->html_out .= $object_model->displayForm('add');
                    }
                    if ((Tools::getIsset('add' . $definition_lang['tableName']) || Tools::isSubmit('addpopup')) && Tools::getIsset('error3')) {
                        $this->html_out .= $this->displayError($this->trans('The title must be specified', [], 'Modules.Prestablog.Prestablog'));
                        $this->html_out .= $object_model->displayForm('add');
                    }
                    if ((Tools::getIsset('add' . $definition_lang['tableName']) || Tools::isSubmit('addpopup')) && Tools::getIsset('error4')) {
                        $this->html_out .= $this->displayError($this->trans('The content or introduction must be specified', [], 'Modules.Prestablog.Prestablog'));
                        $this->html_out .= $object_model->displayForm('add');
                    }
                    if ((Tools::getIsset('add' . $definition_lang['tableName']) || Tools::isSubmit('addpopup')) && !Tools::getIsset('error2') && !Tools::getIsset('error1') && !Tools::getIsset('error3') && !Tools::getIsset('error4')) {
                        $this->html_out .= $object_model->displayForm('add');
                    }
                    if (Tools::getIsset('update' . $definition_lang['tableName']) || Tools::isSubmit('updatepopup')) {
                        $this->html_out .= $object_model->displayForm('edit');
                    }
                }
            }
        }

        if (isset($errorpopup)) {
            $datas = '';
            if (Tools::getValue('pop_colorpicker_content') != '') {
                $datas .= '&8=' . Tools::getValue('pop_colorpicker_content');
            }
            if (Tools::getValue('pop_colorpicker_modal') != '') {
                $datas .= '&9=' . Tools::getValue('pop_colorpicker_modal');
            }
            if (Tools::getValue('pop_colorpicker_btn') != '') {
                $datas .= '&10=' . Tools::getValue('pop_colorpicker_btn');
            }
            if (Tools::getValue('pop_colorpicker_btn_border') != '') {
                $datas .= '&11=' . Tools::getValue('pop_colorpicker_btn_border');
            }
            if (Tools::getValue('date_start') != '') {
                $datas .= '&1=' . Tools::getValue('date_start');
            }
            if (Tools::getValue('date_stop') != '') {
                $datas .= '&2=' . Tools::getValue('date_stop');
            }
            if (Tools::getValue('height') != '') {
                $datas .= '&3=' . Tools::getValue('height');
            }
            if (Tools::getValue('width') != '') {
                $datas .= '&4=' . Tools::getValue('width');
            }
            if (Tools::getValue('delay') != '') {
                $datas .= '&5=' . Tools::getValue('delay');
            }
            if (Tools::getValue('expire') != '') {
                $datas .= '&6=' . Tools::getValue('expire');
            }

            Tools::redirectAdmin(AdminController::$currentIndex . '&configure=prestablog&addpopup&token=' . Tools::getAdminTokenLite('AdminModules') . '&class=PopupClass&' . $errorpopup . '' . $datas);
        }
        if (count($errors) > 0) {
            $this->html_out .= $this->displayError($errors);
        }
        if (count($warnings) > 0) {
            $this->html_out .= $this->displayWarning($warnings);
        }
        if ($this->html_out != '') {
            return $this->html_out;
        }
        if ($update_process) {
            return $this->displayConfirmation($this->trans('Settings updated successfully', [], 'Modules.Prestablog.Prestablog'));
        } else {
            return null;
        }
    }

    private function displayHome()
    {
        // Permissions system
        $rulesDelete = 'can_delete_article';
        $rulesEdit = 'can_edit_article';
        $rulesActivate = 'can_activate_article';

        $resultDelete = $this->loadAuthorAndCheckPermissions($rulesDelete);
        if (Configuration::get($this->name . '_enable_permissions') != 1) {
            $permissionDelete = true;
        } else {
            $permissionDelete = ($resultDelete && isset($resultDelete['permissions'][$rulesDelete]) && $resultDelete['permissions'][$rulesDelete] == 1);
        }

        $resultEdit = $this->loadAuthorAndCheckPermissions($rulesEdit);
        if (Configuration::get($this->name . '_enable_permissions') != 1) {
            $permissionEdit = true;
        } else {
            $permissionEdit = ($resultEdit && isset($resultEdit['permissions'][$rulesEdit]) && $resultEdit['permissions'][$rulesEdit] == 1);
        }

        $resultActivate = $this->loadAuthorAndCheckPermissions($rulesActivate);
        if (Configuration::get($this->name . '_enable_permissions') != 1) {
            $permissionActivate = true;
        } else {
            $permissionActivate = ($resultActivate && isset($resultActivate['permissions'][$rulesActivate]) && $resultActivate['permissions'][$rulesActivate] == 1);
        }
        // Permissions system

        $liste_news = NewsClass::getListe(
            (int) $this->context->language->id,
            1,
            0,
            0,
            (int) Configuration::get($this->name . '_lastnews_limit'),
            'n.`date`',
            'desc',
            null,
            null,
            null,
            0,
            (int) Configuration::get('prestablog_news_title_length'),
            (int) Configuration::get('prestablog_news_intro_length')
        );

        // Check if a shop in multishop is selected
        $isMultishopActive = Shop::isFeatureActive();
        $shopContext = Shop::getContext();
        $noShopSelected = ($shopContext !== Shop::CONTEXT_SHOP);
        $this->context->smarty->assign([
            'imgPathFO' => self::imgPathFO(),
            'imgPathBO' => self::imgPathBO(),
            'lastNewsLimit' => (int) Configuration::get($this->name . '_lastnews_limit'),
            'languageIso' => Language::getIsoById((int) $this->context->language->id),
            'languageId' => $this->context->language->id,
            'newsListCount' => count($liste_news),
            'newsList' => $liste_news,
            'authors' => AuthorClass::getListeAuthor(),
            'imgUpPath' => self::imgUpPath(),
            'md5' => md5(time()),
            'getT' => self::getT(),
            'authorEditActive' => (int) Configuration::get($this->name . '_author_edit_actif'),
            'confpath' => $this->confpath,
            'newsClass' => new NewsClass(),
            'commentNewsClass' => new CommentNewsclass(),
            'commentFbActive' => Configuration::get('prestablog_commentfb_actif'),
            'featureActive' => Shop::isFeatureActive(),
            'no_shop_selected' => $noShopSelected,
            'is_multishop_active' => $isMultishopActive,
            'context' => $this->context,
            'prestablog' => $this,
            'permissionDelete' => $permissionDelete,
            'permissionEdit' => $permissionEdit,
            'permissionActivate' => $permissionActivate,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/Home.tpl');
    }

    private function displayAuthorList()
    {
        $liste = AuthorClass::getListeAuthor();
        $id_emp = $this->context->employee->id_profile;
        $author = new AuthorClass();

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'liste' => $liste,
            'id_emp' => $id_emp,
            'author' => $author,
            'imgAuthorUpPath' => self::imgAuthorUpPath(),
            'imgPathBO' => self::imgPathBO(),
            'getT' => self::getT(),
        ]);
        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayAuthorList.tpl');
    }

    private function displayAddAuthor()
    {
        $legend_title = $this->trans('Add an author', [], 'Modules.Prestablog.Prestablog');
        $employees = [];
        $employees[0] = $this->trans('Select an author', [], 'Modules.Prestablog.Prestablog');
        foreach (AuthorClass::getListeEmployee() as $employee) {
            $returnDB = AuthorClass::checkAuthor($employee['id_employee']);

            if ($returnDB == '' || $returnDB == null) {
                $employees[$employee['id_employee']] = $employee['id_employee'] . '-' . $employee['firstname'] . ' ' . $employee['lastname'] . '/' . $employee['email'];
            }
        }
        $addauthor_text = $this->trans('Add an author :', [], 'Modules.Prestablog.Prestablog');
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'employees' => $employees,
            'legend_title' => $legend_title,
            'addauthor_text' => $addauthor_text,
        ]);
        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayAddAuthor.tpl');
    }

    private function displayAuthorPermissions()
    {
        if ($this->context->employee->id_profile != 1) {
            $permissionErrors[] = $this->trans('You do not have permission to access this page.', [], 'Modules.Prestablog.Prestablog');
            Tools::redirectAdmin($this->confpath . '&authorList&permission_error=1');

            return;
        }

        $authorId = (int) Tools::getValue('id_author');
        $author = new AuthorClass($authorId);
        $permissions = $this->loadAuthorPermissions($authorId);

        $this->context->smarty->assign([
            'author' => $author,
            'permissions' => $permissions,
            'confpath' => $this->confpath,
            'success' => Tools::getValue('success'),
            'permissionErrors' => isset($permissionErrors) ? $permissionErrors : [],
            'permissions_system_enabled' => Configuration::get('prestablog_enable_permissions'),
        ]);

        $this->html_out .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/displayAuthorPermissions.tpl');
    }

    private function loadAuthorPermissions($authorId)
    {
        $author = new AuthorClass($authorId);
        $defaultPermissions = $this->getDefaultPermissions();

        if (isset($author->permissions) && !empty($author->permissions)) {
            $authorPermissions = json_decode($author->permissions, true);
            if (is_array($authorPermissions)) {
                return array_merge($defaultPermissions, $authorPermissions);
            }
        }

        return $defaultPermissions;
    }

    private function loadAuthorAndCheckPermissions($permissionType)
    {
        $authorId = $this->context->employee->id;

        if (Configuration::get($this->name . '_enable_permissions') != 1) {
            return [
                'author' => new AuthorClass($authorId),
                'permissions' => $this->getDefaultPermissions(),
            ];
        }

        $author = new AuthorClass($authorId);

        if (!isset($author->permissions) || empty($author->permissions)) {
            return false;
        }

        $permissions = json_decode($author->permissions, true);

        if (isset($permissions[$permissionType]) && $permissions[$permissionType] == 1) {
            return [
                'author' => $author,
                'permissions' => $permissions,
            ];
        }

        return false;
    }

    private function getDefaultPermissions()
    {
        return [
            'can_add_article' => 0,
            'can_edit_article' => 0,
            'can_delete_article' => 0,
            'can_activate_article' => 0,
            'can_create_category' => 0,
            'can_delete_category' => 0,
            'can_manage_comments' => 0,
            'can_manage_popup' => 0,
            'can_manage_slide' => 0,
            'can_manage_personalised_list' => 0,
            'can_configure_module' => 0,
            'can_use_tool' => 0,
            // Add here all permissions (check also submitAuthorPermissions and displayAuthorPermissions.tpl)
        ];
    }

    public static function checkAuthorPermissionsStatic($authorId, $permissionType, $redirect = true)
    {
        $prestaBlog = Module::getInstanceByName('prestablog');
        $result = $prestaBlog->loadAuthorAndCheckPermissions($permissionType);

        if (!$result && $redirect) {
            Tools::redirectAdmin($prestaBlog->confpath . '&permission_error=1');

            return false;
        }

        return $result;
    }

    private function displayAccountGestion()
    {
        $html_libre = '';
        $id = $this->context->employee->id;
        $pseudo = AuthorClass::getPseudo($id);
        $biography = AuthorClass::getBio($id);
        $email = AuthorClass::getEmail($id);
        $meta_title = AuthorClass::getMetaTitle($id);
        $Meta_Description = AuthorClass::getMetaDescription($id);

        $this->loadJsForTiny();
        if (Tools::getIsset('error')) {
            $info = $this->trans('Sorry, your file was not uploaded. Your image needs to be less than ', [], 'Modules.Prestablog.Prestablog');
            $info .= (int) Configuration::get('prestablog_author_pic_width');
            $info .= $this->trans(' px width and ', [], 'Modules.Prestablog.Prestablog');
            $info .= (int) Configuration::get('prestablog_author_pic_height');
            $info .= $this->trans(' px height', [], 'Modules.Prestablog.Prestablog');

            $this->html_out .= $this->displayError($info);
        }
        $img_author['is_true'] = false;
        if (file_exists(self::imgAuthorUpPath() . '/' . $id . '.jpg')) {
            $img_author['src_value'] = self::imgPathBO() . self::getT() . '/author_th/' . $id . '.jpg';
            $img_author['is_true'] = true;
        } else {
            $img_author['src_value'] = self::imgPathBO() . self::getT() . '/author_th/default.jpg';
        }
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'img_author' => $img_author,
            'id' => $id,
            'pseudo' => $pseudo,
            'biography' => $biography,
            'email' => $email,
            'meta_title' => $meta_title,
            'Meta_Description' => $Meta_Description,
        ]);
        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayAccountGestion.tpl');
    }

    public function displayAddSlide()
    {
        $legend_title = $this->trans('Add a slide', [], 'Modules.Prestablog.Prestablog');
        $info = $this->trans('The actual configuration of your sizes are settle to : ', [], 'Modules.Prestablog.Prestablog');
        $info .= (int) Configuration::get('prestablog_slide_picture_width');
        $info .= $this->trans(' px width and ', [], 'Modules.Prestablog.Prestablog');
        $info .= (int) Configuration::get('prestablog_slide_picture_height');
        $info .= $this->trans(' px height', [], 'Modules.Prestablog.Prestablog');
        $languages = Language::getLanguages(true);

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'legend_title' => $legend_title,
            'info' => $info,
            'languages' => $languages,
            'laguageId' => $this->context->language->id,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayAddSlide.tpl');
    }

    private function displayEditSlide()
    {
        $info = '';
        $html_libre = '';
        $languages = Language::getLanguages(true);
        $id_slide = Tools::getValue('idS');

        $id_lang = (int) Tools::getValue('languesup');
        $title = SliderClass::getTitle($id_slide, $id_lang, (int) $this->context->shop->id);
        $url_associate = SliderClass::getURL($id_slide, $id_lang, (int) $this->context->shop->id);
        $position = SliderClass::getPosition($id_slide, $id_lang, (int) $this->context->shop->id);

        $info = $this->trans('The actual configuration of your sizes are settle to : ', [], 'Modules.Prestablog.Prestablog');
        $info .= (int) Configuration::get('prestablog_slide_picture_width');
        $info .= $this->trans(' px width and ', [], 'Modules.Prestablog.Prestablog');
        $info .= (int) Configuration::get('prestablog_slide_picture_height');
        $info .= $this->trans(' px height', [], 'Modules.Prestablog.Prestablog');

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'id_slide' => $id_slide,
            'id_lang' => $id_lang,
            'title' => $title,
            'url_associate' => $url_associate,
            'position' => $position,
            'info' => $info,
            'imgPathBO' => self::imgPathBO(),
            'getT' => self::getT(),
            'languages' => $languages,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayEditSlide.tpl');
    }

    private function displaySlideSystem()
    {
        $languages = Language::getLanguages(true);

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'name' => $this->name,
            'languages' => $languages,
            'imgPathBO' => self::imgPathBO(),
            'getT' => self::getT(),
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displaySlideSystem.tpl');
    }

    private function displayListeNews()
    {
        // Permissions system
        $rulesDelete = 'can_delete_article';
        $rulesEdit = 'can_edit_article';
        $rulesActivate = 'can_activate_article';

        $resultDelete = $this->loadAuthorAndCheckPermissions($rulesDelete);
        if (Configuration::get($this->name . '_enable_permissions') != 1) {
            $permissionDelete = true;
        } else {
            $permissionDelete = ($resultDelete && isset($resultDelete['permissions'][$rulesDelete]) && $resultDelete['permissions'][$rulesDelete] == 1);
        }

        $resultEdit = $this->loadAuthorAndCheckPermissions($rulesEdit);
        if (Configuration::get($this->name . '_enable_permissions') != 1) {
            $permissionEdit = true;
        } else {
            $permissionEdit = ($resultEdit && isset($resultEdit['permissions'][$rulesEdit]) && $resultEdit['permissions'][$rulesEdit] == 1);
        }

        $resultActivate = $this->loadAuthorAndCheckPermissions($rulesActivate);
        if (Configuration::get($this->name . '_enable_permissions') != 1) {
            $permissionActivate = true;
        } else {
            $permissionActivate = ($resultActivate && isset($resultActivate['permissions'][$rulesActivate]) && $resultActivate['permissions'][$rulesActivate] == 1);
        }
        // Permissions system

        $languages_shop = [];
        foreach (Language::getLanguages() as $value) {
            $languages_shop[$value['id_lang']] = $value['iso_code'];
        }

        $nb_par_page = (int) Configuration::get($this->name . '_nb_news_pl');

        $tri_champ = 'n.`date`';
        $tri_ordre = 'desc';
        $languages = Language::getLanguages(true);

        if (Tools::getValue('c') && (int) Tools::getValue('c') > 0) {
            $categorie = (int) Tools::getValue('c');
            $this->confpath .= $this->confpath . '&c=' . $categorie;
        } else {
            $categorie = null;
        }

        $count_liste = NewsClass::getCountListeAll(
            0,
            (int) $this->check_active,
            (int) $this->check_slide,
            null,
            null,
            $categorie,
            0
        );

        $pagination = self::getPagination(
            $count_liste,
            null,
            $nb_par_page,
            (int) Tools::getValue('start'),
            (int) Tools::getValue('p')
        );
        $liste = NewsClass::getListe(
            0,
            (int) $this->check_active,
            (int) $this->check_slide,
            (int) Tools::getValue('start'),
            $nb_par_page,
            $tri_champ,
            $tri_ordre,
            null,
            null,
            $categorie,
            0,
            (int) Configuration::get('prestablog_news_title_length'),
            (int) Configuration::get('prestablog_news_intro_length')
        );

        // Check if a shop in multishop is selected
        $isMultishopActive = Shop::isFeatureActive();
        $shopContext = Shop::getContext();
        $noShopSelected = ($shopContext !== Shop::CONTEXT_SHOP);

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'context' => $this->context,
            'imgPathFO' => self::imgPathFO(),
            'imgPathBO' => self::imgPathBO(),
            'categories' => CategoriesClass::getListe((int) $this->context->language->id, 0),
            'categoriesClass' => new CategoriesClass(),
            'toolsValueCategories' => Tools::getValue('c'),
            'checkActive' => $this->check_active,
            'languages' => Language::getLanguages(true),
            'toolsLanguageSup' => Tools::getValue('languesup'),
            'listCategorieValue' => $categorie,
            'countList' => $count_liste,
            'categoriesName' => CategoriesClass::getCategoriesName((int) $this->context->language->id, (int) $categorie),
            'prestaboost' => Module::getInstanceByName('prestaboost'),
            'newsList' => $liste,
            'configLangDefault' => Configuration::get('PS_LANG_DEFAULT'),
            'imgUpPath' => self::imgUpPath(),
            'md5' => md5(time()),
            'getT' => self::getT(),
            'languagesShop' => $languages_shop,
            'accurl' => self::accurl(),
            'prestablog' => $this,
            'pagination' => $pagination,
            'toolsValuePagination' => (int) Tools::getValue('p'),
            'featureActive' => Shop::isFeatureActive(),
            'no_shop_selected' => $noShopSelected,
            'is_multishop_active' => $isMultishopActive,
            'permissionDelete' => $permissionDelete,
            'permissionEdit' => $permissionEdit,
            'permissionActivate' => $permissionActivate,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/News.tpl');
    }

    public function createNewsClass($id)
    {
        return new NewsClass((int) $id);
    }

    private function displayListeComments()
    {
        // Permissions system
        $rulesAuthor = 'can_manage_comments';
        $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

        if (!$result) {
            Tools::redirectAdmin($this->confpath . '&permission_error=1');

            return;
        }
        // Permissions system

        $nb_per_page = (int) Configuration::get($this->name . '_nb_comments_pl');

        if (Tools::getValue('n') && (int) Tools::getValue('n') > 0) {
            $news = (int) Tools::getValue('n');
            $this->confpath .= '&n=' . $news;
        } else {
            $news = null;
        }

        $only_parents = true;

        $count_liste = CommentNewsClass::getCountListeParentsOnly($this->check_comment_state, $news);

        $liste = CommentNewsClass::getListeNavigate(
            $this->check_comment_state,
            (int) Tools::getValue('start', 0),
            $nb_per_page,
            $only_parents,
            $news
        );

        foreach ($liste as &$comment) {
            $comment['replies'] = CommentNewsClass::getReplies($comment['id_prestablog_commentnews'], false);
        }

        $pagination = self::getPagination(
            $count_liste,
            null,
            $nb_per_page,
            (int) Tools::getValue('start', 0),
            (int) Tools::getValue('p', 1)
        );

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'nb_per_page' => $nb_per_page,
            'news' => $news,
            'count_liste' => $count_liste,
            'liste' => $liste,
            'pagination' => $pagination,
            'imgPathFO' => self::imgPathFO(),
            'check_comment_state' => $this->check_comment_state,
            'context' => $this->context,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/Comments.tpl');
    }

    private function displayReplyCommentForm($id_parent)
    {
        $parent_comment = new CommentNewsClass($id_parent);

        if (!Validate::isLoadedObject($parent_comment) || $parent_comment->actif != 1) {
            $this->html_out .= $this->displayError($this->trans('You cannot reply to a comment that does not exist or is not approved.', [], 'Modules.Prestablog.Prestablog'));

            return;
        }

        $employee = $this->context->employee;

        $author_id = (int) $employee->id;

        if (AuthorClass::verifyAuthorSet($author_id)) {
            $authorData = AuthorClass::getAuthorData($author_id);

            if (!empty($authorData['pseudo'])) {
                $author_name = $authorData['pseudo'];
            } else {
                $author_name = trim($authorData['firstname'] . ' ' . $authorData['lastname']);
            }
        } else {
            $author_name = trim($employee->firstname . ' ' . $employee->lastname);
        }

        $name = Tools::getValue('name', $author_name);
        $comment_text = Tools::getValue('comment', '');
        $date_value = Tools::getValue('date', date('Y-m-d H:i:s'));

        $this->context->smarty->assign([
            'id_parent' => $id_parent,
            'parent_comment' => $parent_comment,
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'author_name' => $name,
            'comment_text' => $comment_text,
            'date_value' => $date_value,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ReplyComment.tpl');
    }

    public function verbose_blog_categories($value)
    {
        $cat_verbose = '';

        if (is_array($value['blog_categories']) && count($value['blog_categories']) > 1) {
            foreach ($value['blog_categories'] as $id_category) {
                $category = new CategoriesClass(
                    (int) $id_category,
                    (int) $this->context->cookie->id_lang
                );
                $cat_verbose .= $category->title . ', ';
            }
        } elseif (is_int($value['blog_categories'])) {
            $category = new CategoriesClass(
                (int) $value['blog_categories'],
                (int) $this->context->cookie->id_lang
            );
            $cat_verbose .= $category->title;
        } else {
            $cat_verbose = '-';
        }

        $cat_verbose = rtrim(trim($cat_verbose), ',');

        return $cat_verbose;
    }

    private function displayListeSubBlocks()
    {
        $javascript1 = self::httpS() . '://code.jquery.com/ui/1.10.3/jquery-ui.js';
        $javascript2 = __PS_BASE_URI__ . 'modules/prestablog/views/js/jquery.mjs.nestedSortable.js';

        $languages = Language::getLanguages(true);

        $languages_shop = [];
        foreach (Language::getLanguages() as $value) {
            $languages_shop[$value['id_lang']] = $value['iso_code'];
        }
        $config_theme = $this->getConfigXmlTheme(self::getT());

        $sub_blocks = new SubBlocksClass(null, null, null, $this->getTranslator());
        $liste_hook = SubBlocksClass::getHookListe((int) $this->context->language->id, 0);

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'context' => $this->context,
            'prestablog' => $this,
            'imgPathFO' => self::imgPathFO(),
            'javascript1' => $javascript1,
            'javascript2' => $javascript2,
            'languages' => $languages,
            'languages_shop' => $languages_shop,
            'liste_hook' => $liste_hook,
            'select_type' => $sub_blocks->getListeSelectType(),
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayListeSubBlocks.tpl');
    }

    private function displayListeCategories()
    {
        // Permissions system
        $editRulesAuthor = 'can_create_category'; // Droit pour ajouter/éditer une catégorie
        $deleteRulesAuthor = 'can_delete_category'; // Droit pour supprimer une catégorie

        // Vérifier le droit d'ajouter/éditer une catégorie
        $editResult = $this->loadAuthorAndCheckPermissions($editRulesAuthor);

        // Vérifier le droit de supprimer une catégorie
        $deleteResult = $this->loadAuthorAndCheckPermissions($deleteRulesAuthor);

        // Si les permissions sont désactivées, l'utilisateur a tous les droits par défaut
        if (Configuration::get($this->name . '_enable_permissions') != 1) {
            $editPermissionAccess = true;
            $deletePermissionAccess = true;
        } else {
            // Vérification des droits d'édition/ajout
            $editPermissionAccess = ($editResult && isset($editResult['permissions'][$editRulesAuthor]) && $editResult['permissions'][$editRulesAuthor] == 1);

            // Vérification des droits de suppression
            $deletePermissionAccess = ($deleteResult && isset($deleteResult['permissions'][$deleteRulesAuthor]) && $deleteResult['permissions'][$deleteRulesAuthor] == 1);
        }
        // Permissions system

        $liste = CategoriesClass::getListe((int) $this->context->language->id, 0);

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'prestaboost' => Module::getInstanceByName('prestaboost'),
            'imgPathBO' => self::imgPathBO(),
            'liste' => $liste,
            'editPermissionAccess' => $editPermissionAccess,
            'deletePermissionAccess' => $deletePermissionAccess,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayListeCategories.tpl');
    }

    public function displayOrderCategories($liste = null, &$count = 0)
    {
        // If no list is provided, get the default list
        if ($liste === null) {
            $liste = CategoriesClass::getListe((int) $this->context->language->id, 0);
        }

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'imgUpPath' => self::imgUpPath(),
            'imgPathBO' => self::imgPathBO(),
            'getT' => self::getT(),
            'liste' => $liste,
            'count' => $count,
            'md5' => md5(time()),
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayOrderCategories.tpl');
    }

    public function displayOrderTreeCategories($liste, &$count = 0)
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'imgUpPath' => self::imgUpPath(),
            'imgPathBO' => self::imgPathBO(),
            'getT' => self::getT(),
            'liste' => $liste,
            'count' => $count,
            'md5' => md5(time()),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/displayOrderTreeCategories.tpl');
    }

    public function displayHtml($html)
    {
        $this->html_out .= $html;
    }

    public function create_group($groupe, $language_id_lang)
    {
        return new Group((int) $groupe, (int) $language_id_lang);
    }

    public function displayListeArborescenceCategoriesNews($liste_cat, $decalage = 0, $liste_id_branch_deploy = [])
    {
        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'context' => $this->context,
            'imgUpPath' => self::imgUpPath(),
            'imgPathBO' => self::imgPathBO(),
            'getT' => self::getT(),
            'liste_cat' => $liste_cat,
            'liste_cat_lang' => CategoriesClass::getListeNoArbo(),
            'languages' => Language::getLanguages(true),
            'decalage' => $decalage,
            'liste_id_branch_deploy' => $liste_id_branch_deploy,
            'md5' => md5(time()),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/displayListeArborescenceCategoriesNews.tpl');
    }

    public function get_displayListeArborescenceCategoriesSubBlocks(
        $liste_cat,
        $decalage = 0,
        $liste_id_branch_deploy = [])
    {
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $html_out = '';
        foreach ($liste_cat as $value) {
            if (file_exists(self::imgUpPath() . '/c/adminth_' . $value['id_prestablog_categorie'] . '.jpg')) {
                $value['imgthidc'] = self::imgPathBO() . self::getT() . '/up-img/c/';
                $value['imgthidc'] .= 'adminth_' . $value['id_prestablog_categorie'] . '.jpg';
            }
        }
        $md5 = md5(time());
        $liste_cat_lang = CategoriesClass::getListeNoArbo();
        $languages = Language::getLanguages(true);

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'listecat' => $liste_cat,
            'value' => $value,
            'id_lang' => $id_lang,
            'decalage' => $decalage,
            'md5' => $md5,
            'languages' => $languages,
            'liste_cat_lang' => $liste_cat_lang,
            'listeidbranchdeploy' => $liste_id_branch_deploy,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayListeArborescenceCategoriesSubBlocks.tpl');
    }

    public function displayListeArborescenceCategoriesSubBlocks(
        $liste_cat,
        $decalage = 0,
        $liste_id_branch_deploy = [])
    {
        $this->html_out .= get_displayListeArborescenceCategoriesSubBlocks($liste_cat, $decalage, $liste_id_branch_deploy);
    }

    public function get_string_group_list($liste_groupes_categorie, $language_id)
    {
        $group_loop = '';

        foreach ($liste_groupes_categorie as $groupe) {
            $group = new Group((int) $groupe, (int) $language_id);
            $group_loop .= $group->name . ', ';
        }
        $group_loop = rtrim(trim($group_loop), ',');

        return $group_loop;
    }

    public function displayListeArborescenceCategories($liste, $decalage = 0)
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'imgUpPath' => self::imgUpPath(),
            'imgPathBO' => self::imgPathBO(),
            'getT' => self::getT(),
            'md5' => md5(time()),
            'liste' => $liste,
            'decalage' => $decalage,
            'prestaboost_active' => (Module::getInstanceByName('prestaboost') !== false),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/displayListeArborescenceCategories.tpl');
    }

    private function displayDebug()
    {
        $this->context->smarty->assign([
            'prestablog' => $this,
            'imgPathFO' => self::imgPathFO(),
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayDebug.tpl');
    }

    private function displayDocumentation()
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayDocumentation.tpl');
    }

    private function displayContact()
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
        ]);
        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayContact.tpl');
    }

    private function displayImport()
    {
        $languages = Language::getLanguages(true);
        $this->context->smarty->assign([
            'languages' => $languages,
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'demo_mode' => $this->demo_mode,
            'file' => file_exists(_PS_UPLOAD_DIR_ . Configuration::get($this->name . '_import_xml')),
            'file_content' => $file_content = Tools::file_get_contents(_PS_UPLOAD_DIR_ . Configuration::get($this->name . '_import_xml')),
        ]);
        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/Import.tpl');
    }

    private function displayConfigAntiSpam()
    {
        $liste = AntiSpamClass::getListe((int) $this->context->language->id, 0);
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'liste' => $liste,
        ]);
        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ConfigAntiSpam.tpl');
    }

    public function deleteSitemapFromShop($id_shop)
    {
        $directory_site_map = _PS_MODULE_DIR_ . $this->name . '/sitemap/' . (int) $id_shop;

        foreach (glob($directory_site_map . '/*.{xml}', GLOB_BRACE) as $file) {
            if (!is_dir($file)) {
                self::unlinkFile($directory_site_map . '/' . basename($file));
            }
        }
    }

    public static function getRobotsContent()
    {
        $tab = [];

        // Special allow directives
        $tab['Allow'] = [
            '*/modules/*.css',
            '*/modules/*.js',
            '*/modules/*.png',
            '*/modules/*.jpg',
            '*/themes/*/assets/cache/*.js',
            '*/themes/*/assets/cache/*.css',
            '*/themes/*/assets/css/*',
        ];

        // Directories
        $tab['Directories'] = ['cache/', 'classes/', 'config/', 'controllers/',
            'css/', 'download/', 'js/', 'localization/', 'log/', 'mails/', 'modules/', 'override/',
            'pdf/', 'src/', 'tools/', 'translations/', 'upload/', 'vendor/', 'web/', 'webservice/', ];

        // Files
        $disallow_controllers = [
            'addresses', 'address', 'authentication', 'cart', 'discount', 'footer',
            'get-file', 'header', 'history', 'identity', 'images.inc', 'init', 'my-account', 'order',
            'order-slip', 'order-detail', 'order-follow', 'order-return', 'order-confirmation', 'pagination', 'password',
            'pdf-invoice', 'pdf-order-return', 'pdf-order-slip', 'product-sort', 'search', 'statistics', 'attachment', 'guest-tracking',
        ];

        // Rewrite files
        $tab['Files'] = [];
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $sql = 'SELECT DISTINCT ml.url_rewrite, l.iso_code
                  FROM ' . _DB_PREFIX_ . 'meta m
                  INNER JOIN ' . _DB_PREFIX_ . 'meta_lang ml ON ml.id_meta = m.id_meta
                  INNER JOIN ' . _DB_PREFIX_ . 'lang l ON l.id_lang = ml.id_lang
                  WHERE l.active = 1 AND m.page IN (\'' . implode('\', \'', $disallow_controllers) . '\')';
            if ($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                foreach ($results as $row) {
                    $tab['Files'][$row['iso_code']][] = $row['url_rewrite'];
                }
            }
        }

        $tab['GB'] = [
            '?order=', '?tag=', '?id_currency=', '?search_query=', '?back=', '?n=',
            '&order=', '&tag=', '&id_currency=', '&search_query=', '&back=', '&n=',
        ];

        foreach ($disallow_controllers as $controller) {
            $tab['GB'][] = 'controller=' . $controller;
        }

        return $tab;
    }

    public function createTheShopSitemap()
    {
        $this->deleteSitemapFromShop((int) $this->context->shop->id);

        $languages = Language::getLanguages(true, (int) $this->context->shop->id);

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>';
        $xml_feed = new SimpleXMLElement($xml);

        $list_sitemap = [];
        if (Configuration::get($this->name . '_sitemap_articles')) {
            $list_sitemap[] = 'articles';
        }
        if (Configuration::get($this->name . '_sitemap_categories')) {
            $list_sitemap[] = 'categories';
        }

        $all_urls_form_language = [];

        foreach ($languages as $lang) {
            foreach ($list_sitemap as $type_list) {
                switch ($type_list) {
                    case 'articles':
                        $all_urls_form_language = NewsClass::getListe(
                            (int) $lang['id_lang'],
                            1,
                            0,
                            0,
                            null,
                            'n.`date`',
                            'desc',
                            date(
                                'Y-m-d H:i:s',
                                strtotime('-' . (int) Configuration::get($this->name . '_sitemap_older') . ' months')
                            ),
                            null,
                            null,
                            1,
                            (int) Configuration::get('prestablog_news_title_length'),
                            (int) Configuration::get('prestablog_news_intro_length')
                        );
                        break;

                    case 'categories':
                        $all_urls_form_language = CategoriesClass::getListeNoArbo(1, (int) $lang['id_lang']);
                        break;

                    default:
                        $all_urls_form_language = [];
                        break;
                }

                $xmls_urls = array_chunk(
                    $all_urls_form_language,
                    (int) Configuration::get($this->name . '_sitemap_limit')
                );
                foreach ($xmls_urls as $xml_key => $xml_urls) {
                    $location_file = $this->name . '/sitemap/';
                    $location_file .= (int) $this->context->shop->id . '/';
                    $location_file .= $type_list . '_' . $lang['iso_code'] . '_' . (int) $xml_key . '.xml';

                    $this->createSplitSitemap($location_file, $xml_urls, $type_list);

                    $sitemap = $xml_feed->addChild('sitemap');
                    $sitemap->addAttribute('lang', $lang['iso_code']);
                    $sitemap->addAttribute('type', 'text/html');
                    $sitemap->addAttribute('charset', 'UTF-8');

                    $sitemap->addChild('loc', self::urlSRoot() . 'modules/' . $location_file);
                    $sitemap->addChild('lastmod', date('c'));
                }
            }
        }
        file_put_contents(
            _PS_MODULE_DIR_ . $this->name . '/sitemap/' . (int) $this->context->shop->id . '/master.xml',
            $xml_feed->asXML()
        );

        return true;
    }

    public function createSplitSitemap($location_file, $urls, $type_list = '')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
        $xml .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
        $xml .= '</urlset>';

        $xml_feed = new SimpleXMLElement($xml);

        foreach ($urls as $child) {
            switch ($type_list) {
                case 'articles':
                    $sitemap = $xml_feed->addChild('url');
                    $sitemap->addChild('priority', '0.9');
                    $sitemap->addChild('loc', self::prestablogUrl(
                        [
                            'id' => (int) $child['id_prestablog_news'],
                            'seo' => $child['link_rewrite'],
                            'titre' => $child['title'],
                            'id_lang' => (int) $child['id_lang'],
                        ]
                    ));

                    $sitemap->addChild('lastmod', date('c', strtotime($child['date_modification'])));
                    $sitemap->addChild('changefreq', 'weekly');

                    if ($child['image_presente']) {
                        $imagechild = $sitemap->addChild(
                            'image:image',
                            null,
                            'http://www.google.com/schemas/sitemap-image/1.1'
                        );
                        $imgchild = self::urlSRoot() . 'modules/prestablog/views/img/';
                        $imgchild .= self::getT() . '/up-img/' . $child['id_prestablog_news'] . '.jpg';
                        $imagechild->addChild(
                            'image:loc',
                            $imgchild,
                            'http://www.google.com/schemas/sitemap-image/1.1'
                        );
                        $imagechild->addChild(
                            'image:title',
                            $child['title'],
                            'http://www.google.com/schemas/sitemap-image/1.1'
                        );
                    }
                    break;

                case 'categories':
                    if ($child['title'] != '') {
                        $sitemap = $xml_feed->addChild('url');
                        $sitemap->addChild('priority', '0.5');
                        $sitemap->addChild(
                            'loc',
                            self::prestablogUrl([
                                'c' => (int) $child['id_prestablog_categorie'],
                                'titre' => ($child['link_rewrite'] != '' ? $child['link_rewrite'] : $child['title']),
                                'id_lang' => (int) $child['id_lang'],
                            ])
                        );

                        $sitemap->addChild('changefreq', 'yearly');

                        if ($child['image_presente']) {
                            $imagechild = $sitemap->addChild(
                                'image:image',
                                null,
                                'http://www.google.com/schemas/sitemap-image/1.1'
                            );
                            $imgchild = self::urlSRoot() . 'modules/prestablog/views/img/';
                            $imgchild .= self::getT() . '/up-img/c/' . $child['id_prestablog_categorie'] . '.jpg';
                            $imagechild->addChild(
                                'image:loc',
                                $imgchild,
                                'http://www.google.com/schemas/sitemap-image/1.1'
                            );
                            $imagechild->addChild(
                                'image:title',
                                $child['title'],
                                'http://www.google.com/schemas/sitemap-image/1.1'
                            );
                        }
                    }
                    break;
            }
        }

        file_put_contents(_PS_MODULE_DIR_ . $location_file, $xml_feed->asXML());

        return true;
    }

    private function checkCurrentSitemap()
    {
        $directory_site_map = _PS_MODULE_DIR_ . $this->name . '/sitemap/' . (int) $this->context->shop->id;

        $liste_sitemap = glob($directory_site_map . '/master.{xml}', GLOB_BRACE);
        if (count($liste_sitemap) > 0) {
            foreach ($liste_sitemap as $idx => $file) {
                if (!is_dir($file)) {
                    $liste_sitemap[$idx] = basename($liste_sitemap[$idx]);
                } else {
                    unset($liste_sitemap[$idx]);
                }
            }
        }

        $liste_sitemap_wildcard = glob($directory_site_map . '/*[^master]*.{xml}', GLOB_BRACE);
        if (count($liste_sitemap_wildcard) > 0) {
            foreach ($liste_sitemap_wildcard as $idx => $file) {
                if (!is_dir($file)) {
                    $liste_sitemap_wildcard[$idx] = basename($liste_sitemap_wildcard[$idx]);
                } else {
                    unset($liste_sitemap_wildcard[$idx]);
                }
            }
        }

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'context' => $this->context,
            'urlSRoot' => self::urlSRoot(),
            'liste_sitemap' => $liste_sitemap,
            'liste_sitemap_wildcard' => $liste_sitemap_wildcard,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/checkCurrentSitemap.tpl');
    }

    private function displaySitemap()
    {
        $older_date = date('d/m/Y', strtotime('-' . (int) Configuration::get($this->name . '_sitemap_older') . ' months'));

        $urlcron = self::urlSRoot() . 'index.php?fc=module&module=prestablog';
        $urlcron .= '&controller=sitemap&id_shop=' . (int) $this->context->shop->id;
        $urlcron .= '&token=' . Configuration::get($this->name . '_sitemap_token');

        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'context' => $this->context,
            'older_date' => $older_date,
            'urlcron' => $urlcron,
            'checkCurrentSitemap' => $this->checkCurrentSitemap(),
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displaySitemap.tpl');
    }

    private function verifyApiConnection()
    {
        $apiKey = Configuration::get('prestablog_chatgpt_api_key');
        $defaultModels = ['gpt-3.5-turbo', 'gpt-4'];

        if (empty($apiKey)) {
            // add default models
            Configuration::updateValue('prestablog_chatgpt_models', json_encode($defaultModels));

            return ['success' => false, 'message' => $this->trans('API key is missing, setting default models.', [], 'Modules.Prestablog.Ai')];
        }

        // check api and models
        $apiCheckUrl = 'https://api.openai.com/v1/models';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiCheckUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        $apiResult = curl_exec($ch);
        $apiHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_message = curl_error($ch);
            curl_close($ch);

            return ['success' => false, 'message' => $this->trans('Error connecting to API: ', [], 'Modules.Prestablog.Ai') . $error_message];
        }

        curl_close($ch);

        if ($apiHttpCode != 200) {
            if ($apiHttpCode == 401) {
                return ['success' => false, 'message' => $this->trans('Unauthorized: Invalid API key.', [], 'Modules.Prestablog.Ai')];
            } else {
                return ['success' => false, 'message' => $this->trans('Failed to connect to API. HTTP Status Code: ', [], 'Modules.Prestablog.Ai') . $apiHttpCode];
            }
        }

        $responseData = json_decode($apiResult, true);

        if (isset($responseData['data'])) {
            $models = array_column($responseData['data'], 'id');
            $filteredModels = array_filter($models, function ($model) {
                return in_array($model, ['gpt-3.5-turbo', 'gpt-4']);
            });
            $modelsJson = json_encode($filteredModels);
            Configuration::updateValue('prestablog_chatgpt_models', $modelsJson);

            return ['success' => true, 'message' => $this->trans('API connection successful.', [], 'Modules.Prestablog.Ai')];
        } else {
            // if models is not configured, add default model
            Configuration::updateValue('prestablog_chatgpt_models', json_encode($defaultModels));

            return ['success' => false, 'message' => $this->trans('Failed to retrieve models, setting default models.', [], 'Modules.Prestablog.Ai')];
        }
    }

    private function displayConfigAi()
    {
        $confpath = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&confpath=configAi';

        $apiVerification = $this->verifyApiConnection();

        $modelsJson = Configuration::get('prestablog_chatgpt_models');
        $models = json_decode($modelsJson, true);

        $this->context->smarty->assign([
            'confpath' => $confpath,
            'prestablog' => $this,
            'chatgpt_model' => Configuration::get('prestablog_chatgpt_model'),
            'chatgpt_api_key' => Configuration::get('prestablog_chatgpt_api_key'),
            'api_verification' => $apiVerification,
            'chatgpt_models' => $models,
            'demo_mode' => $this->demo_mode,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ConfigAi.tpl');
    }

    public function sendMessageToGpt($message, $prompt = 'free_discussion', $theme = '')
    {
        $api_key = Configuration::get('prestablog_chatgpt_api_key');
        $model = Configuration::get('prestablog_chatgpt_model');
        $api_url = 'https://api.openai.com/v1/chat/completions';

        if (!is_string($message) || empty($message)) {
            return ['success' => false, 'message' => 'Invalid message type. Expected a non-empty string.'];
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => (string) $message],
                ],
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key,
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return ['success' => false, 'message' => 'cURL Error #:' . $err];
        } else {
            $responseData = json_decode($response, true);

            if (isset($responseData['choices'][0]['message']['content'])) {
                return [
                    'success' => true,
                    'response' => $responseData['choices'][0]['message']['content'],
                ];
            } elseif (isset($responseData['choices'][0]['text'])) {
                return [
                    'success' => true,
                    'response' => $responseData['choices'][0]['text'],
                ];
            } elseif (isset($responseData['error'])) {
                return [
                    'success' => false,
                    'message' => 'API Error: ' . $responseData['error']['message'],
                    'api_response' => $responseData,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid API response structure',
                    'api_response' => $responseData,
                ];
            }
        }
    }

    public function translateMessageWithGpt($message, $language)
    {
        $apiKey = Configuration::get('prestablog_chatgpt_api_key');
        $model = Configuration::get('prestablog_chatgpt_model');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that translates text.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Translate the following text to {$language}: {$message}",
                    ],
                ],
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ['success' => false, 'message' => 'cURL Error #:' . $err];
        }

        $responseData = json_decode($response, true);

        if (isset($responseData['choices'][0]['message']['content'])) {
            return [
                'success' => true,
                'translation' => trim($responseData['choices'][0]['message']['content']),
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid API response structure',
            ];
        }
    }

    public function displayChatGPT()
    {
        $id_tab = Tab::getIdFromClassName('AdminPrestaBlogChatGPT');
        $id_employee = (int) $this->context->employee->id;
        $token = Tools::getAdminToken('AdminPrestaBlogChatGPT' . $id_tab . $id_employee);
        $languages = Language::getLanguages(true);

        $this->context->smarty->assign([
            'prestablog' => $this,
            'chatgpt_api_key' => Configuration::get('prestablog_chatgpt_api_key'),
            'chatgpt_model' => Configuration::get('prestablog_chatgpt_model'),
            'admin_token' => $token,
            'languages' => $languages,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/displayChatGPT.tpl');
    }

    private function displayPageBlog()
    {
        $legend_title = $this->trans('Add your articles on home\'s frontpage', [], 'Modules.Prestablog.Prestablog');
        $lln = [];
        if (isset($id_front)) {
            $sub_blocks = new SubBlocksClass((int) $id_front, null, null, $this->getTranslator());
            $lln = json_decode($sub_blocks->langues, true);
            if (!is_array($lln)) {
                $lln = [];
            }
            $legend_title = $this->trans('Update the list', [], 'Modules.Prestablog.Prestablog');
        } else {
            $sub_blocks = new SubBlocksClass(null, null, null, $this->getTranslator());
        }
        if (Tools::isSubmit('submitUpdateSubBlockFront') || Tools::isSubmit('submitAddSubBlockFront')) {
            $sub_blocks->id_shop = (int) $this->context->shop->id;
            $sub_blocks->copyFromPost();
        }

        $popups_link = [];
        $popups_link[0] = '';
        foreach (PopupClass::getListePopup((int) $this->context->language->id, (int) $this->context->shop->id) as $popup_link) {
            $popups_link[$popup_link['id_prestablog_popup']] = $popup_link['title'];
        }

        $dl = $this->langue_default_store;

        $news = new NewsClass((int) Tools::getValue('idN'));
        if ($news->langues != '') {
            $lang_liste_news = json_decode($news->langues, true);
        } else {
            $lang_liste_news = [];
        }

        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'context' => $this->context,
            'dl' => $this->langue_default_store,
            'lang_liste_news' => $lang_liste_news,
            'languages' => Language::getLanguages(true),
        ]);

        $html_language = $this->display(__FILE__, 'views/templates/admin/displayPageBlog_language.tpl');

        $liste_cat = CategoriesClass::getListe((int) $this->context->language->id, 0);
        $liste_cat_no_arbre = CategoriesClass::getListeNoArbo();
        $liste_cat_branches_actives = [];

        foreach (SubBlocksClass::getCategories($sub_blocks->id, 0) as $value) {
            $liste_cat_branches_actives = array_unique(
                array_merge(
                    $liste_cat_branches_actives,
                    preg_split('/\./', CategoriesClass::getBranche((int) $value))
                )
            );
        }

        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'context' => $this->context,
            'dl' => $this->langue_default_store,
            'languages' => Language::getLanguages(true),
            'liste_cat_lang' => CategoriesClass::getListeNoArbo(),
            'liste_cat_branches_actives' => $liste_cat_branches_actives,
            'liste_cat_no_arbre' => $liste_cat_no_arbre,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayPageBlog.tpl');

        /* FIN CATEGORIES */

        $this->context->smarty->assign([
            'html_language' => $html_language,
            'confpath' => $this->confpath,
            'divLangName' => 'meta_title¤meta_description¤titre_h1',
            'prestablog' => $this,
            'languages' => Language::getLanguages(true),
            'urlRoot' => self::urlSRoot(),
            'prestaboost' => Module::getInstanceByName('prestaboost'),
            'imgPathFO' => self::imgPathFO(),
            'popup' => new PopupClass(null, null, null, $this->getTranslator()),
            'popups_link' => $popups_link,
            'getP' => self::getP(),
            'id_lang' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'defaultLang' => $this->langue_default_store,
            'lln' => $lln,
            'sub_blocks' => $sub_blocks,
            'acl' => [],
            'getT' => self::getT(),
            'liste_cat' => $liste_cat,
            'liste_cat_no_arbre' => $liste_cat_no_arbre,
            'liste_cat_branches_actives' => $liste_cat_branches_actives,
            'context' => $this->context,
            'dl' => $dl,
            'legend_title' => $legend_title,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ConfigPageBlog.tpl');
    }

    private function displayFormSubBlocks()
    {
        $dl = $this->langue_default_store;
        $languages = Language::getLanguages(true);
        $div_lang_name = 'title';
        $lln = [];

        $legend_title = $this->trans('Create a list', [], 'Modules.Prestablog.Prestablog');
        if (Tools::getValue('idSB')) {
            $sub_blocks = new SubBlocksClass((int) Tools::getValue('idSB'), null, null, $this->getTranslator());
            $lln = json_decode($sub_blocks->langues, true);
            if (!is_array($lln)) {
                $lln = [];
            }
            $legend_title = $this->trans('Update the list', [], 'Modules.Prestablog.Prestablog');
        } else {
            $sub_blocks = new SubBlocksClass(null, null, null, $this->getTranslator());
        }

        if (Tools::isSubmit('submitUpdateSubBlock') || Tools::isSubmit('submitAddSubBlock')) {
            $sub_blocks->id_shop = (int) $this->context->shop->id;
            $sub_blocks->copyFromPost();
        }

        // langues start
        $array_check_lang = [];
        if (Tools::getValue('languesup')) {
            $array_check_lang = Tools::getValue('languesup');
        }

        $retl = 'RetourLangueCheckUp(selectedL, this.value, ' . $dl . ')';
        $selectLang = 'changeTheLanguage(\'title\', \'' . $div_lang_name . '\', this.value, \'\');';
        $selectedL = 'selectedL = new Array();
        $("input[name=\'languesup[]\']:checked").each(function() {selectedL.push($(this).val());});
        changeTheLanguage(\'title\', \'' . $div_lang_name . '\', ' . $retl . ', \'\');';

        $valuedate = date('Y-m-d H:i:s');

        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $liste_cat = CategoriesClass::getListe((int) $this->context->language->id, 0);
        foreach ($liste_cat as $value) {
            if (file_exists(self::imgUpPath() . '/c/adminth_' . $value['id_prestablog_categorie'] . '.jpg')) {
                $value['imgthidc'] = self::imgPathBO() . self::getT() . '/up-img/c/';
                $value['imgthidc'] .= 'adminth_' . $value['id_prestablog_categorie'] . '.jpg';
            }
        }
        $liste_cat_branches_actives = [];
        foreach (SubBlocksClass::getCategories($sub_blocks->id, 0) as $value) {
            $liste_cat_branches_actives = array_unique(
                array_merge(
                    $liste_cat_branches_actives,
                    preg_split('/\./', CategoriesClass::getBranche((int) $value))
                )
            );
        }
        $md5 = md5(time());

        $liste_cat_lang = CategoriesClass::getListeNoArbo();
        $languages = Language::getLanguages(true);

        $this->context->smarty->assign([
            'selectedL' => $selectedL,
            'selectLang' => $selectLang,
            'valuedate' => $valuedate,
            'default_language' => $dl,
            'array_check_lang' => $array_check_lang,
            'lln' => $lln,
            'acl' => [],
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'context' => $this->context,
            'dl' => $dl,
            'sub_blocks' => $sub_blocks,
            'date_start' => $sub_blocks->date_start,
            'date_stop' => $sub_blocks->date_stop,
            'legend_title' => $legend_title,
            'languages' => $languages,
            'div_lang_name' => $div_lang_name,
            'toolsLanguageSup' => Tools::getValue('languesup'),
            'getT' => self::getT(),
            'listecat' => $liste_cat,
            'liste_cat_no_arbre' => CategoriesClass::getListeNoArbo(),
            'liste_cat_branches_actives' => $liste_cat_branches_actives,
            'SubBlocksClass_getCategories' => SubBlocksClass::getCategories($sub_blocks->id, 0),
            'id_lang' => $id_lang,
            'md5' => $md5,
            'liste_cat_lang' => $liste_cat_lang,
            'decalage' => 0,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayFormSubBlocks.tpl');
    }

    private function displayConfWizard()
    {
        $this->html_out .= $this->displayFormOpen('wizard.png', $this->trans('Wizard templating', [], 'Modules.Prestablog.Prestablog'), $this->confpath);
        $this->html_out .= $this->displayFormSubmit('submitWizard', 'icon-save', $this->trans('Update', [], 'Modules.Prestablog.Prestablog'));
        $this->html_out .= $this->displayFormClose();
    }

    public static function scanListeThemes()
    {
        $liste = [];
        foreach (glob(_PS_MODULE_DIR_ . 'prestablog/views/config/*.{xml}', GLOB_BRACE) as $file) {
            if (!is_dir($file)) {
                $themeName = rtrim(basename($file), '.xml');
                if ($themeName !== 'default') {
                    $liste[] = $themeName;
                }
            }
        }

        return $liste;
    }

    public static function scanListePopups()
    {
        $liste = [];
        foreach (glob(_PS_MODULE_DIR_ . 'prestablog/views/config/*.{xml}', GLOB_BRACE) as $file) {
            if (!is_dir($file)) {
                $liste[] = rtrim(basename($file), '.xml');
            }
        }

        return $liste;
    }

    public static function scanLayoutFolder()
    {
        $liste = [];
        foreach (glob(_PS_MODULE_DIR_ . 'prestablog/views/img/layout/*.{png}', GLOB_BRACE) as $file) {
            if (!is_dir($file)) {
                $liste[rtrim(basename($file), '.png')] = basename($file);
            }
        }

        return $liste;
    }

    private function displayConfTheme()
    {
        $themes = [];
        foreach (self::scanListeThemes() as $value_theme) {
            $themes[$value_theme] = basename($value_theme);
        }
        $src = self::imgPathFO() . self::getT() . '-preview.jpg';
        $layerslider = Module::getInstanceByName('layerslider');

        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'imgPathFO' => self::imgPathFO(),
            'scanListThemes' => self::scanListeThemes(),
            'getT' => self::getT(),
            'scanLayoutFolder' => [],
            'themes' => $themes,
            'src' => $src,
            'layerslider' => $layerslider,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ConfigTheme.tpl');
    }

    public function get_displayConfLayout()
    {
        $scanLayoutFolder = self::scanLayoutFolder();
        $_layout_blog = (int) Configuration::get($this->name . '_layout_blog');
        $img = self::imgPathFO();
        $imgLayout = self::imgPathFO() . 'layout/';
        $imgCheck = self::imgPathFO() . 'check.png';
        $prestablog_layout_blog = $this->confpath . '&selectLayout&prestablog_layout_blog=';
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'languages' => Language::getLanguages(true),
            'scanLayoutFolder' => $scanLayoutFolder,
            'img' => $img,
            '_layout_blog' => $_layout_blog,
            'imgLayout' => $imgLayout,
            'imgCheck' => $imgCheck,
            'prestablog_layout_blog' => $prestablog_layout_blog,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayConfLayout.tpl');
    }

    public function displayConfLayout()
    {
        $this->html_out .= $this->get_displayConfLayout();
    }

    public function displayCreativeSlide()
    {
        $this->html_out .= $this->get_displayCreativeSlide();
    }

    public function get_displayCreativeSlide()
    {
        $config_theme = $this->getConfigXmlTheme(self::getT());

        return $this->renderForm();
    }

    public function displayConfSlide()
    {
        if (Tools::getIsset('success')) {
            $this->html_out .= $this->displayConfirmation($this->trans('Settings updated successfully', [], 'Modules.Prestablog.Prestablog'));
        }

        $config_theme = $this->getConfigXmlTheme(self::getT());

        $this->html_out .= $this->displayFormOpen('slide.png', $this->trans('Slideshow Prestablog', [], 'Modules.Prestablog.Prestablog'), $this->confpath);

        $this->html_out .= $this->displayFormEnableItemConfiguration(
            'col-lg-5',
            $this->trans('Slide on homepage', [], 'Modules.Prestablog.Prestablog'),
            $this->name . '_homenews_actif',
            $this->trans('The slide will be displayed in the center column of the home page of your shop.', [], 'Modules.Prestablog.Prestablog')
        );
        $this->html_out .= $this->displayFormEnableItemConfiguration(
            'col-lg-5',
            $this->trans('Slide on blogpage', [], 'Modules.Prestablog.Prestablog'),
            $this->name . '_pageslide_actif',
            $this->trans('The slide will be displayed in the top of first page articles list of blog.', [], 'Modules.Prestablog.Prestablog')
        );
        $this->html_out .= $this->displayFormInput(
            'col-lg-5',
            $this->trans('Number of slide to display', [], 'Modules.Prestablog.Prestablog'),
            $this->name . '_homenews_limit',
            Configuration::get($this->name . '_homenews_limit'),
            10,
            'col-lg-2', '', '', '', 'PrestablogUintTextBox'
        );

        $this->html_out .= $this->displayFormInput(
            'col-lg-5',
            $this->trans('Slide picture width', [], 'Modules.Prestablog.Prestablog'),
            $this->name . '_slide_picture_width',
            Configuration::get($this->name . '_slide_picture_width'),
            10,
            'col-lg-2',
            $this->trans('px', [], 'Modules.Prestablog.Prestablog'), '', '', 'PrestablogUintTextBox'
        );
        $this->html_out .= $this->displayFormInput(
            'col-lg-5',
            $this->trans('Slide picture height', [], 'Modules.Prestablog.Prestablog'),
            $this->name . '_slide_picture_height',
            Configuration::get($this->name . '_slide_picture_height'),
            10,
            'col-lg-2',
            $this->trans('px', [], 'Modules.Prestablog.Prestablog'), '', '', 'PrestablogUintTextBox'
        );
        $this->html_out .= $this->displayFormEnableItemConfiguration(
            'col-lg-5',
            $this->trans('Show slide title on front', [], 'Modules.Prestablog.Prestablog'),
            $this->name . '_show_slide_title',
            $this->trans('If enabled, the title of the slide will be displayed on the front.', [], 'Modules.Prestablog.Prestablog')
        );
        $this->html_out .= $this->displayFormSubmit('submitConfSlideNews', 'icon-save', $this->trans('Update', [], 'Modules.Prestablog.Prestablog'));
        $this->html_out .= $this->displayFormClose();
    }

    private function displayConfBlocs()
    {
        $srcscript1 = self::httpS() . '://code.jquery.com/ui/1.10.3/jquery-ui.js';
        $srcscript2 = __PS_BASE_URI__ . 'modules/prestablog/views/js/jquery.mjs.nestedSortable.js';
        $srcscript3 = __PS_BASE_URI__ . 'modules/prestablog/views/js/numbers.js';
        $sbl = json_decode(Configuration::get($this->name . '_sbl'), true);
        $sbr = json_decode(Configuration::get($this->name . '_sbr'), true);
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'sbl' => $sbl,
            'sbr' => $sbr,
            'srcscript1' => $srcscript1,
            'srcscript2' => $srcscript2,
            'srcscript3' => $srcscript3,
            'context' => $this->context,
        ]);
        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ConfigBlocs.tpl');
    }

    private function displayConfCategories()
    {
        $config_theme = $this->getConfigXmlTheme(self::getT());
        $imgsrc = self::imgPathFO();

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'config_theme' => $config_theme,
            'imgsrc' => $imgsrc,
            'module_dir' => _MODULE_DIR_,
            'config' => Configuration::get($this->name . '_urlblog'),
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ConfigCategories.tpl');
    }

    private function displayConfigAuthor()
    {
        $this->context->smarty->assign([
            'prestablog' => $this,
            'context' => $this->context,
            'confpath' => $this->confpath,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayConfigAuthor.tpl');
    }

    private function displayConf()
    {
        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'imgPathFO' => self::imgPathFO(),
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ConfigGlobal.tpl');
    }

    private function displayColorPicker()
    {
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fields_form = [];
        $fields_form[0]['form'] = ['input' => [
            [
                'type' => 'color',
                'label' => $this->trans('Color', [], 'Modules.Prestablog.Prestablog'),
                'name' => 'maincolor',
                'lang' => false,
                'id' => 'color_0',
                'data-hex' => true,
                'class' => 'mColorPicker',
            ],
            [
                'type' => 'date',
                'label' => $this->trans('Date', [], 'Modules.Prestablog.Prestablog'),
                'name' => 'date_test',
                'maxlength' => 10,
            ]]];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;

        return $helper->generateForm($fields_form);
    }

    public function verifConditionSmarty($variable)
    {
        if ($variable != '0' && $variable != null) {
            return $variable;
        } else {
            return '';
        }
    }

    private function displayColorBlog()
    {
        $liste_colors = NewsClass::getColorHome((int) $this->context->shop->id);
        if (!isset($liste_colors[0]) || $liste_colors[0] == null) {
            $menu_color = 0;
            $menu_hover = 0;
            $read_color = 0;
            $hover_color = 0;
            $title_color = 0;
            $text_color = 0;
            $menu_link = 0;
            $link_read = 0;
            $article_title = 0;
            $article_text = 0;
            $block_categories = 0;
            $block_categories_link = 0;
            $block_title = 0;
            $block_btn = 0;
            $block_btn_hover = 0;
            $categorie_block_background = 0;
            $categorie_block_background_hover = 0;
            $article_background = 0;
            $ariane_color = 0;
            $ariane_color_text = 0;
            $ariane_border = 0;
            $block_categories_link_btn = 0;
            $sharing_icon_color = 0;
        } else {
            $menu_color = $liste_colors[0]['menu_color'];
            $menu_hover = $liste_colors[0]['menu_hover'];
            $read_color = $liste_colors[0]['read_color'];
            $hover_color = $liste_colors[0]['hover_color'];
            $title_color = $liste_colors[0]['title_color'];
            $text_color = $liste_colors[0]['text_color'];
            $menu_link = $liste_colors[0]['menu_link'];
            $link_read = $liste_colors[0]['link_read'];
            $article_title = $liste_colors[0]['article_title'];
            $article_text = $liste_colors[0]['article_text'];
            $block_categories = $liste_colors[0]['block_categories'];
            $block_categories_link = $liste_colors[0]['block_categories_link'];
            $block_title = $liste_colors[0]['block_title'];
            $block_btn = $liste_colors[0]['block_btn'];
            $block_btn_hover = $liste_colors[0]['block_btn_hover'];
            $categorie_block_background = $liste_colors[0]['categorie_block_background'];
            $categorie_block_background_hover = $liste_colors[0]['categorie_block_background_hover'];
            $article_background = $liste_colors[0]['article_background'];
            $ariane_color = $liste_colors[0]['ariane_color'];
            $ariane_color_text = $liste_colors[0]['ariane_color_text'];
            $ariane_border = $liste_colors[0]['ariane_border'];
            $block_categories_link_btn = $liste_colors[0]['block_categories_link_btn'];
            $sharing_icon_color = $liste_colors[0]['sharing_icon_color'];
        }

        $css = Tools::file_get_contents(_PS_MODULE_DIR_ . 'prestablog/views/css/custom' . (int) $this->context->shop->id . '.css');
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'menu_color' => $menu_color,
            'menu_hover' => $menu_hover,
            'menu_link' => $menu_link,
            'ariane_color' => $ariane_color,
            'ariane_color_text' => $ariane_color_text,
            'ariane_border' => $ariane_border,
            'title_color' => $title_color,
            'text_color' => $text_color,
            'categorie_block_background' => $categorie_block_background,
            'categorie_block_background_hover' => $categorie_block_background_hover,
            'link_read' => $link_read,
            'read_color' => $read_color,
            'hover_color' => $hover_color,
            'article_title' => $article_title,
            'article_text' => $article_text,
            'article_background' => $article_background,
            'block_title' => $block_title,
            'block_categories' => $block_categories,
            'block_categories_link' => $block_categories_link,
            'block_categories_link_btn' => $block_categories_link_btn,
            'sharing_icon_color' => $sharing_icon_color,
            'block_btn' => $block_btn,
            'block_btn_hover' => $block_btn_hover,
            'css' => $css,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ConfigDesign.tpl');
    }

    private function displayConfComments()
    {
        $infocom = $this->trans('The classic comment system (on the left of your page) and its options cannot be applied to facebook comments (on the right of your page).', [], 'Modules.Prestablog.Prestablog');
        $list_fb_moderators = json_decode(Configuration::get($this->name . '_commentfb_modosId'), true);

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'infocom' => $infocom,
            'list_fb_moderators' => $list_fb_moderators,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/ConfigComment.tpl');
    }

    public function get_displayFormOpen(
        $icon_legend = 'cog.gif',
        $label_legend = 'New Form',
        $action = '',
        $name = 'formblog')
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'icon_legend' => $icon_legend,
            'label_legend' => $label_legend,
            'action' => $action,
            'name' => $name,
            'imgPathFO' => self::imgPathFO(),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormOpen.tpl');
    }

    public function displayFormOpen(
        $icon_legend = 'cog.gif',
        $label_legend = 'New Form',
        $action = '',
        $name = 'formblog')
    {
        $this->html_out .= $this->get_displayFormOpen($icon_legend, $label_legend, $action, $name);
    }

    public function get_displayFormClose()
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormClose.tpl');
    }

    public function displayFormClose()
    {
        $this->html_out .= $this->get_displayFormClose();
    }

    public function get_displayFormSelect(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = '',
        $options = [],
        $sizecar = 20,
        $size_bootstrap = 'col-lg-5',
        $info_span = null,
        $help = null,
        $info_span_before = null)
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'label_bootstrap' => $label_bootstrap,
            'label_text' => $label_text,
            'name_item' => $name_item,
            'value' => $value,
            'options' => $options,
            'sizecar' => $sizecar,
            'size_bootstrap' => $size_bootstrap,
            'info_span' => $info_span,
            'help' => $help,
            'info_span_before' => $info_span_before,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormSelect.tpl');
    }

    public function displayFormSelect(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = '',
        $options = [],
        $sizecar = 20,
        $size_bootstrap = 'col-lg-5',
        $info_span = null,
        $help = null,
        $info_span_before = null)
    {
        $this->html_out .= $this->get_displayFormSelect($label_bootstrap, $label_text, $name_item, $value, $options, $sizecar, $size_bootstrap, $info_span, $help, $info_span_before);
    }

    public function get_displayFormSelectAuthor(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = '',
        $options = [],
        $sizecar = 20,
        $size_bootstrap = 'col-lg-5',
        $info_span = null,
        $help = null,
        $info_span_before = null)
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'label_bootstrap' => $label_bootstrap,
            'name_item' => $name_item,
            'label_text' => $label_text,
            'size_bootstrap' => $size_bootstrap,
            'info_span_before' => $info_span_before,
            'sizecar' => $sizecar,
            'options' => $options,
            'value' => $value,
            'info_span' => $info_span,
            'help' => $help,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormSelectAuthor.tpl');
    }

    public function displayFormSelectAuthor(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = '',
        $options = [],
        $sizecar = 20,
        $size_bootstrap = 'col-lg-5',
        $info_span = null,
        $help = null,
        $info_span_before = null)
    {
        $this->html_out .= get_displayFormSelectAuthor($label_bootstrap, $label_text, $name_item, $value, $options, $sizecar, $size_bootstrap, $info_span, $help, $info_span_before);
    }

    public function get_displayFormSubmit($submit_name, $icon, $label)
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'submit_name' => $submit_name,
            'icon' => $icon,
            'label' => $label,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormSubmit.tpl');
    }

    public function displayFormSubmit($submit_name, $icon, $label)
    {
        $this->html_out .= $this->get_displayFormSubmit($submit_name, $icon, $label);
    }

    public function get_displayOrderBlocs($sbl, $sbr)
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'sbl' => $sbl,
            'sbr' => $sbr,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayOrderBlocs.tpl');
    }

    public function get_displayFormFile(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $size_bootstrap = 'col-lg-5',
        $help = null)
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'label_bootstrap' => $label_bootstrap,
            'name_item' => $name_item,
            'label_text' => $label_text,
            'size_bootstrap' => $size_bootstrap,
            'help' => $help,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormFile.tpl');
    }

    public function displayFormFile(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $size_bootstrap = 'col-lg-5',
        $help = null)
    {
        $this->html_out .= displayFormFile($label_bootstrap, $label_text, $name_item, $size_bootstrap, $help);
    }

    public function displayFormFileNoLabel($name_item = '', $size_bootstrap = 'col-lg-5', $help = null)
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'name_item' => $name_item,
            'size_bootstrap' => $size_bootstrap,
            'help' => $help,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/displayFormFileNoLabel.tpl');
    }

    public function get_displayFormInputColor(
        $label_bootstrap = 'col-lg-2',
        $number = '',
        $label_text = '',
        $name_item = '',
        $value = '',
        $class = '',
        $size_bootstrap = '')
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'label_bootstrap' => $label_bootstrap,
            'number' => $number,
            'label_text' => $label_text,
            'name_item' => $name_item,
            'value' => $value,
            'size_bootstrap' => $size_bootstrap,
            'class' => $class,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormInputColor.tpl');
    }

    public function displayFormInputColor(
        $label_bootstrap = 'col-lg-2',
        $number = '',
        $label_text = '',
        $name_item = '',
        $value = '',
        $class = '',
        $size_bootstrap = '')
    {
        $this->html_out .= get_displayFormInputColor($label_bootstrap, $number, $label_text, $name_item, $value, $class, $size_bootstrap);
    }

    public function get_displayFormInput(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = '',
        $sizecar = 20,
        $size_bootstrap = 'col-lg-5',
        $info_span = null,
        $help = null,
        $info_span_before = null,
        $class = '')
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'label_bootstrap' => $label_bootstrap,
            'label_text' => $label_text,
            'name_item' => $name_item,
            'value' => $value,
            'sizecar' => $sizecar,
            'size_bootstrap' => $size_bootstrap,
            'info_span' => $info_span,
            'help' => $help,
            'info_span_before' => $info_span_before,
            'class' => $class,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormInput.tpl');
    }

    public function displayFormInput(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = '',
        $sizecar = 20,
        $size_bootstrap = 'col-lg-5',
        $info_span = null,
        $help = null,
        $info_span_before = null,
        $class = '')
    {
        $this->html_out .= $this->get_displayFormInput($label_bootstrap, $label_text, $name_item, $value, $sizecar, $size_bootstrap, $info_span, $help, $info_span_before, $class);
    }

    public function get_displayFormDate(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = '',
        $time = true)
    {
        if (!$value) {
            if ($time) {
                $value = date('Y-m-d H:i:s');
            } else {
                $value = date('Y-m-d');
            }
        }
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'label_bootstrap' => $label_bootstrap,
            'label_text' => $label_text,
            'name_item' => $name_item,
            'value' => $value,
            'time' => $time,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormDate.tpl');
    }

    public function displayFormDate(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = '',
        $time = true)
    {
        $this->html_out .= $this->get_displayFormDate($label_bootstrap, $label_text, $name_item, $value, $time);
    }

    public function displayFormDateWithActivation(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = '',
        $time = true,
        $name_item_activation = '',
        $value_activation = null)
    {
        if (!$value) {
            if ($time) {
                $value = date('Y-m-d H:i:s');
            } else {
                $value = date('Y-m-d');
            }
        }

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'label_bootstrap' => $label_bootstrap,
            'label_text' => $label_text,
            'name_item' => $name_item,
            'value' => $value,
            'time' => $time,
            'name_item_activation' => $name_item_activation,
            'value_activation' => $value_activation,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormDateWithActivation.tpl');
    }

    public function get_displayFormEnableItemConfiguration(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $help = null)
    {
        $ni = $name_item;
        $isOn = false;
        if (Tools::getValue($ni, Configuration::get($ni))) {
            $isOn = true;
        }
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'label_bootstrap' => $label_bootstrap,
            'label_text' => $label_text,
            'name_item' => $name_item,
            'help' => $help,
            'ni' => $ni,
            'isOn' => $isOn,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormEnableItemConfiguration.tpl');
    }

    public function displayFormEnableItemConfiguration(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $help = null)
    {
        $this->html_out .= $this->get_displayFormEnableItemConfiguration($label_bootstrap, $label_text, $name_item, $help);
    }

    public function get_displayFormEnableItem(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = null,
        $help = null)
    {
        $ni = $name_item;

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'label_bootstrap' => $label_bootstrap,
            'label_text' => $label_text,
            'name_item' => $name_item,
            'value' => $value,
            'help' => $help,
            'ni' => $ni,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormEnableItem.tpl');
    }

    public function displayFormEnableItem(
        $label_bootstrap = 'col-lg-5',
        $label_text = '',
        $name_item = '',
        $value = null,
        $help = null)
    {
        $this->html_out .= $this->get_displayFormEnableItem($label_bootstrap, $label_text, $name_item, $value, $help);
    }

    public function displayFlagsFor($item, $div_lang_name)
    {
        $languages = Language::getLanguages(true);

        return $this->displayFlags($languages, $this->langue_default_store, $div_lang_name, $item, true);
    }

    public function displayFormNews()
    {
        // Permissions system
        $rulesAuthor = 'can_add_article';
        $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

        if (!$result) {
            Tools::redirectAdmin($this->confpath . '&permission_error=1');

            return;
        }

        $rulesActivate = 'can_activate_article';
        $resultActivate = $this->loadAuthorAndCheckPermissions($rulesActivate);

        if (Configuration::get($this->name . '_enable_permissions') != 1) {
            $permissionActivate = true; // Permet l'activation si les permissions sont désactivées
        } else {
            $permissionActivate = ($resultActivate && isset($resultActivate['permissions'][$rulesActivate]) && $resultActivate['permissions'][$rulesActivate] == 1);
        }
        // Permissions system

        $legend_title = $this->trans('Add news', [], 'Modules.Prestablog.Prestablog');
        $lang_liste_news = null;
        if (Tools::getValue('idN')) {
            $news = new NewsClass((int) Tools::getValue('idN'));
            $lang_liste_news = json_decode($news->langues, true);
            $legend_title = $this->trans('Edit news', [], 'Modules.Prestablog.Prestablog') . ' #' . $news->id;
        } else {
            $news = new NewsClass();
        }

        if (Tools::isSubmit('submitUpdateNews') || Tools::isSubmit('submitAddNews')) {
            $news->id_shop = (int) $this->context->shop->id;
            $news->copyFromPost();
        }
        $iso = $this->context->language->id;

        $resultprodjs = $this->trans('You must search before', [], 'Modules.Prestablog.Prestablog');
        $resultprodjs .= ' (' . (int) Configuration::get($this->name . '_nb_car_min_linkprod');
        $resultprodjs .= ' ' . $this->trans('caract. minimum', [], 'Modules.Prestablog.Prestablog') . ')';

        $resultarticlejs = $this->trans('You must search before', [], 'Modules.Prestablog.Prestablog');
        $resultarticlejs .= ' (' . (int) Configuration::get($this->name . '_nb_car_min_linknews');
        $resultarticlejs .= ' ' . $this->trans('caract. minimum', [], 'Modules.Prestablog.Prestablog') . ')';

        $isPSVersionValid = $this->isPSVersion('>=', '1.6');

        $popups_link = [];
        $popups_link[0] = '';
        foreach (PopupClass::getListePopup((int) $this->context->language->id, (int) $this->context->shop->id) as $popup_link) {
            $popups_link[$popup_link['id_prestablog_popup']] = $popup_link['title'];
        }
        $authors = [];
        $stringAuthors = '';
        if (Tools::getIsset('editNews')) {
            if ($this->context->employee->id_profile == 1 && AuthorClass::getListeAuthor() != '' && AuthorClass::getListeAuthor() != null) {
                $authors[0] = '0 - ' . $this->trans('No author', [], 'Modules.Prestablog.Prestablog');
                foreach (AuthorClass::getListeAuthor() as $author) {
                    $authors[$author['id_author']] = $author['id_author'] . ' - ' . $author['firstname'] . ' ' . $author['lastname'];
                }
                $name = AuthorClass::getAuthorName(Tools::getValue('idN'));
                $id_auth = AuthorClass::getAuthorID(Tools::getValue('idN'));
                if (isset($id_auth[0]['author_id'], $name['firstname'])) {
                    $stringAuthors = $id_auth[0]['author_id'] . ' - ' . $name['firstname'] . ' ' . $name['lastname'];
                } else {
                    $stringAuthors = 'null';
                }
            }
        }

        $prestablogurl = [];
        if ($lang_liste_news) {
            foreach ($lang_liste_news as $val_langue) {
                if (isset($news->title[(int) $val_langue])) {
                    $prestablogurl[$val_langue] = PrestaBlog::prestablogUrl([
                        'id' => (int) $news->id,
                        'seo' => $news->link_rewrite[(int) $val_langue],
                        'titre' => $news->title[(int) $val_langue],
                        'id_lang' => (int) $val_langue, ]);
                }
            }
        }

        $comments_actif = null;
        $comments_all = null;
        $comments_non_lu = null;
        $comments_disabled = null;
        $datesCommentUnread = [];
        $datesActifComment = [];
        $datesDisabledComment = [];
        if (Tools::getValue('idN')) {
            $comments_actif = CommentNewsClass::getListe(1, $news->id);
            $comments_all = CommentNewsClass::getListe(-2, $news->id);
            $comments_non_lu = CommentNewsClass::getListe(-1, $news->id);
            $comments_disabled = CommentNewsClass::getListe(0, $news->id);

            foreach ($comments_non_lu as $value_c) {
                $datesCommentUnread[$value_c['id_prestablog_commentnews']] = ToolsCore::displayDate($value_c['date'], null, true);
            }
            foreach ($comments_actif as $value_c) {
                $datesActifComment[$value_c['id_prestablog_commentnews']] = ToolsCore::displayDate($value_c['date'], null, true);
            }
            foreach ($comments_disabled as $value_c) {
                $datesDisabledComment[$value_c['id_prestablog_commentnews']] = ToolsCore::displayDate($value_c['date'], null, true);
            }
        }

        $liste_cat_branches_actives = [];
        foreach (CorrespondancesCategoriesClass::getCategoriesListe((int) $news->id) as $value) {
            $liste_cat_branches_actives = array_unique(array_merge(
                $liste_cat_branches_actives,
                preg_split('/\./', CategoriesClass::getBranche((int) $value))
            ));
        }

        $products_link = [];
        if (Tools::getValue('idN')) {
            $products_link = NewsClass::getProductLinkListe((int) Tools::getValue('idN'));
        }

        $languages_shop = [];
        foreach (Language::getLanguages() as $value) {
            $languages_shop[$value['id_lang']] = $value['iso_code'];
        }

        $name = null;
        $id_auth = null;
        if (Tools::getIsset('editNews')) {
            $name = AuthorClass::getAuthorName(Tools::getValue('idN'));
            $id_auth = AuthorClass::getAuthorID(Tools::getValue('idN'));
        }

        $dl = $this->langue_default_store;
        $languages = Language::getLanguages(true);
        $linkRewriteTab = [];

        foreach ($languages as $language) {
            $lid = (int) $language['id_lang'];

            $this->context->smarty->assign([
                'prestablog' => $this,
                'confpath' => $this->confpath,
                'context' => $this->context,
                'lid' => $lid,
                'dl' => $dl,
                'news' => $news,
            ]);

            $linkRewriteTab[$lid] = $this->display(__FILE__, 'views/templates/admin/EditNews_linkRewriteTab.tpl');
        }

        $titlesTab = [];
        foreach ($languages as $language) {
            $lid = (int) $language['id_lang'];

            $this->context->smarty->assign([
                'prestablog' => $this,
                'confpath' => $this->confpath,
                'context' => $this->context,
                'lid' => $lid,
                'dl' => $dl,
            ]);

            $titlesTab[$lid] = $this->display(__FILE__, 'views/templates/admin/EditNews_titlesTab.tpl');
        }

        $div_lang_name = 'title¤link_rewrite¤meta_title¤meta_description¤meta_keywords¤cpara1¤cpara2';
        $this->context->smarty->assign([
            'prestablog' => $this,
            'confpath' => $this->confpath,
            'context' => $this->context,
            'dl' => $dl,
            'lid' => $lid,
            'languages' => $languages,
            'lang_liste_news' => $lang_liste_news,
        ]);

        $html_language = $this->display(__FILE__, 'views/templates/admin/EditNews_Languages.tpl');

        $array_check_lang = [];
        if (Tools::getValue('languesup')) {
            $array_check_lang = Tools::getValue('languesup');
        }

        $permanentUrlRedirect = $this->trans('Completed url with http://', [], 'Modules.Prestablog.Prestablog') . '<br/>' . sprintf(
            $this->trans('This feature will redirect %1$s to this url with a redirect 301', [], 'Modules.Prestablog.Prestablog'),
            '' . (isset($news->title[$lid]) ? $news->title[$lid] : '') . ''
        );

        $this->context->smarty->assign([
            'permanentUrlRedirect' => $permanentUrlRedirect,
            'html_language' => $html_language,
            'titlesTab' => $titlesTab,
            'linkRewriteTab' => $linkRewriteTab,
            'array_check_lang' => $array_check_lang,
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'iso' => $iso,
            'html_libre' => '',
            'context' => $this->context,
            'config_theme' => $this->getConfigXmlTheme(self::getT()),
            'default_language' => $dl,
            'languages' => $languages,
            'div_lang_name' => $div_lang_name,
            'legend_title' => $legend_title,
            'iso_tiny_mce' => $iso,
            'resultprodjs' => $resultprodjs,
            'resultarticlejs' => $resultarticlejs,
            'ad' => dirname($_SERVER['PHP_SELF']),
            'languages_shop' => $languages_shop,
            'html_libre' => '',
            'prestablogurl' => $prestablogurl,
            'imgPathFO' => self::imgPathFO(),
            'accurl' => self::accurl(),
            'imgUpPath' => self::imgUpPath(),
            'imgPathBO' => self::imgPathBO(),
            'getP' => self::getP(),
            'getT' => self::getT(),
            'md5' => md5(time()),
            'imgPath' => self::imgPath(),
            'liste_cat' => CategoriesClass::getListe((int) $this->context->language->id, 0),
            'liste_cat_no_arbre' => CategoriesClass::getListeNoArbo(),
            'liste_cat_branches_actives' => $liste_cat_branches_actives,
            'demo_mode' => $this->demo_mode,
            'isPSVersionValid' => $isPSVersionValid,
            'news' => $news,
            'prestaboost' => Module::getInstanceByName('prestaboost'),
            'popups_link' => $popups_link,
            'employee' => $this->context->employee,
            'name' => $name,
            'id_auth' => $id_auth,
            'authors' => $authors,
            'stringAuthors' => $stringAuthors,
            'datesCommentUnread' => $datesCommentUnread,
            'datesActifComment' => $datesActifComment,
            'datesDisabledComment' => $datesDisabledComment,
            'lang_liste_news' => $lang_liste_news,
            'comments_actif' => $comments_actif,
            'comments_all' => $comments_all,
            'comments_non_lu' => $comments_non_lu,
            'comments_disabled' => $comments_disabled,
            'products_link' => $products_link,
            'layerslider' => Module::getInstanceByName('layerslider'),
            'commentFbActive' => Configuration::get('prestablog_commentfb_actif'),
            'chatgpt_api_key' => Configuration::get('prestablog_chatgpt_api_key'),
            'permissionActivate' => $permissionActivate,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/EditNews.tpl');
    }

    private function displayFormCategories()
    {
        // Permissions system
        $rulesAuthor = 'can_create_category';
        $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

        if (!$result) {
            Tools::redirectAdmin($this->confpath . '&permission_error=1');

            return;
        }
        // Permissions system

        $config_theme = $this->getConfigXmlTheme(self::getT());

        $dl = $this->langue_default_store;
        $languages = Language::getLanguages(true);
        $iso = Language::getIsoById((int) $this->context->language->id);
        $div_lang_name = 'title¤link_rewrite¤meta_title¤meta_description¤meta_keywords¤cpara1';

        $legend_title = $this->trans('Add a category', [], 'Modules.Prestablog.Prestablog');
        if (Tools::getValue('idC')) {
            $categories = new CategoriesClass((int) Tools::getValue('idC'));
            $legend_title = $this->trans('Update the category', [], 'Modules.Prestablog.Prestablog') . ' #' . $categories->id;
        } else {
            $categories = new CategoriesClass();
        }

        if (Tools::isSubmit('submitUpdateCat') || Tools::isSubmit('submitAddCat')) {
            $categories->id_shop = (int) $this->context->shop->id;
            $categories->copyFromPost();
        }

        $this->loadJsForTiny();

        $iso_tiny_mce = (file_exists(_PS_ROOT_DIR_ . '/js/tinymce/jscripts/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en');
        $ad = dirname($_SERVER['PHP_SELF']);

        $allow_accents_js = 'var PS_ALLOW_ACCENTED_CHARS_URL = 0;';
        if (Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')) {
            $allow_accents_js = 'var PS_ALLOW_ACCENTED_CHARS_URL = 1;';
        }

        $popuplink = null;
        $popups_link = [];

        $prestaboost = Module::getInstanceByName('prestaboost');
        if (!$prestaboost) {
            $popups_link[0] = '';
            foreach (PopupClass::getListePopup((int) $this->context->language->id, (int) $this->context->shop->id) as $popup_link) {
                $popups_link[$popup_link['id_prestablog_popup']] = $popup_link['title'];
            }
            $popuplink = CategoriesClass::getPopupLink($categories->id);
        }

        $this->context->smarty->assign([
            'THEME_CSS_DIR' => _THEME_CSS_DIR_,
            'PS_BASE_URI' => __PS_BASE_URI__,
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'imgPathFO' => self::imgPathFO(),
            'imgPathBO' => self::imgPathBO(),
            'imgUpPath' => self::imgUpPath(),
            'getT' => self::getT(),
            'getP' => self::getP(),
            'iso' => $iso,
            'prestaboost' => $prestaboost,
            'popuplink' => $popuplink,
            'popups_link' => $popups_link,
            'legend_title' => $legend_title,
            'languages' => $languages,
            'categories' => $categories,
            'context' => $this->context,
            'demo_mode' => $this->demo_mode,
            'path' => $this->_path,
            'allow_accents_js' => $allow_accents_js,
            'iso_tiny_mce' => $iso_tiny_mce,
            'ad' => $ad,
            'dl' => $dl,
            'div_lang_name' => $div_lang_name,
            'config_theme' => $config_theme,
            'md5' => md5(time()),
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayFormCategories.tpl');
    }

    public function displayFormGroups($active_group)
    {
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'groups' => Group::getGroups((int) $this->context->language->id),
            'active_group' => $active_group,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/Component/displayFormGroups.tpl');
    }

    private function displayFormAntiSpam()
    {
        $languages = Language::getLanguages(true);
        $div_lang_name = 'question¤reply';
        $legend_title = $this->trans('Add an AntiSpam question', [], 'Modules.Prestablog.Prestablog');
        if (Tools::getValue('idAS')) {
            $antispam = new AntiSpamClass((int) Tools::getValue('idAS'));
            $legend_title = $this->trans('Update the AntiSpam question', [], 'Modules.Prestablog.Prestablog');
        } else {
            $antispam = new AntiSpamClass();
        }

        if (Tools::isSubmit('submitUpdateAntiSpam') || Tools::isSubmit('submitAddAntiSpam')) {
            $antispam->id_shop = (int) $this->context->shop->id;
            $antispam->copyFromPost();
        }
        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'legend_title' => $legend_title,
            'languages' => $languages,
            'antispam' => $antispam,
            'div_lang_name' => $div_lang_name,
            'dl' => $this->context->language->id,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/displayFormAntiSpam.tpl');
    }

    private function displayFormComments()
    {
        // Permissions system
        $rulesAuthor = 'can_manage_comments';
        $result = $this->loadAuthorAndCheckPermissions($rulesAuthor);

        if (!$result) {
            Tools::redirectAdmin($this->confpath . '&permission_error=1');

            return;
        }
        // Permissions system

        $legend_title = $this->trans('Add a comment', [], 'Modules.Prestablog.Prestablog');
        if (Tools::getValue('idC')) {
            $legend_title = $this->trans('Update the comment', [], 'Modules.Prestablog.Prestablog');
            $comment = new CommentNewsClass((int) Tools::getValue('idC'));
        } else {
            $comment = new CommentNewsClass();
            $comment->copyFromPost();
        }

        $array = ['-1' => $this->trans('Pending', [], 'Modules.Prestablog.Prestablog'),
            '1' => $this->trans('Enabled', [], 'Modules.Prestablog.Prestablog'),
            '0' => $this->trans('Disabled', [], 'Modules.Prestablog.Prestablog'), ];

        $this->context->smarty->assign([
            'confpath' => $this->confpath,
            'prestablog' => $this,
            'comment' => $comment,
            'legend_title' => $legend_title,
            'array' => $array,
            'laguageId' => $this->context->language->id,
        ]);

        $this->html_out .= $this->display(__FILE__, 'views/templates/admin/EditComments.tpl');
    }

    private function deleteAllImagesThemes($id)
    {
        foreach (self::scanListeThemes() as $value_theme) {
            $pathdel = _PS_MODULE_DIR_ . $this->name . '/views/img/' . $value_theme . '/up-img/';
            $config_theme = $this->getConfigXmlTheme($value_theme);
            $config_theme_array = PrestaBlog::objectToArray($config_theme);
            foreach (array_keys($config_theme_array['images']) as $key_theme_array) {
                self::unlinkFile($pathdel . $key_theme_array . '_' . $id . '.jpg');
                self::unlinkFile($pathdel . $key_theme_array . '_' . $id . '.webp');
            }
            self::unlinkFile($pathdel . $id . '.jpg');
            self::unlinkFile($pathdel . 'admincrop_' . $id . '.jpg');
            self::unlinkFile($pathdel . 'adminth_' . $id . '.jpg');
            self::unlinkFile($pathdel . 'adminth_' . $id . '.webp');
        }

        return true;
    }

    public function deleteAllImagesThemesCat($id)
    {
        foreach (self::scanListeThemes() as $value_theme) {
            $pathdel = _PS_MODULE_DIR_ . $this->name . '/views/img/' . $value_theme . '/up-img/';
            $config_theme = $this->getConfigXmlTheme($value_theme);
            $config_theme_array = PrestaBlog::objectToArray($config_theme);
            foreach (array_keys($config_theme_array['categories']) as $key_theme_array) {
                self::unlinkFile($pathdel . '/c/' . $key_theme_array . '_' . $id . '.jpg');
            }
            self::unlinkFile($pathdel . 'c/' . $id . '.jpg');
            self::unlinkFile($pathdel . 'c/admincrop_' . $id . '.jpg');
            self::unlinkFile($pathdel . 'c/adminth_' . $id . '.jpg');
        }

        return true;
    }

    private function uploadImage($file_image, $id, $w, $h, $folder = null)
    {
        if (isset($file_image, $file_image['tmp_name']) && !empty($file_image['tmp_name'])) {
            $tmpname = false;
            Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
            if (ImageManager::validateUpload($file_image, $this->max_image_size)) {
                return false;
            } elseif (!$tmpname = tempnam(_PS_TMP_IMG_DIR_, 'PS')) {
                return false;
            } elseif (!move_uploaded_file($file_image['tmp_name'], $tmpname)) {
                return false;
            } else {
                list($image_width, $image_height) = getimagesize($tmpname);
                foreach (self::scanListeThemes() as $theme) {
                    $config_theme = $this->getConfigXmlTheme($theme);
                    $config_theme_array = PrestaBlog::objectToArray($config_theme);
                    $thumbDimensionsCorrect = false;

                    foreach ($config_theme_array['images'] as $key_theme_array => $value_theme_array) {
                        if ($key_theme_array == 'thumb' && $value_theme_array['width'] == $image_width && $value_theme_array['height'] == $image_height) {
                            $thumbDimensionsCorrect = true;
                            break;
                        }
                    }

                    if ($thumbDimensionsCorrect) {
                        if (!copy(
                            $tmpname,
                            self::imgPath() . $theme . '/up-img/' . ($folder ? $folder . '/' : '') . $id . '.jpg'
                        )) {
                            return false;
                        }
                    } else {
                        if (!$this->imageResize(
                            $tmpname,
                            self::imgPath() . $theme . '/up-img/' . ($folder ? $folder . '/' : '') . $id . '.jpg',
                            $w,
                            $h
                        )) {
                            return false;
                        }
                    }
                }
            }
            if (isset($tmpname)) {
                unlink($tmpname);
            }
        }

        return true;
    }

    private function uploadImageAdmin($file_image, $id, $w, $h, $folder = null)
    {
        if (isset($file_image, $file_image['tmp_name']) && !empty($file_image['tmp_name'])) {
            $tmpname = false;
            Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
            if (ImageManager::validateUpload($file_image, $this->max_image_size)) {
                return false;
            } elseif (!$tmpname = tempnam(_PS_TMP_IMG_DIR_, 'PS')) {
                return false;
            } elseif (!move_uploaded_file($file_image['tmp_name'], $tmpname)) {
                return false;
            } else {
                foreach (self::scanListeThemes() as $value_theme) {
                    if (!$this->imageResize(
                        $tmpname,
                        self::imgPath() . $value_theme . '/author_th/' . ($folder ? $folder . '/' : '') . $id . '.jpg',
                        $w,
                        $h
                    )) {
                        return false;
                    }
                }
            }

            if (isset($tmpname)) {
                unlink($tmpname);
            }
        }

        return true;
    }

    private function uploadImageSlide($file_image, $id, $w, $h, $folder = null)
    {
        if (isset($file_image, $file_image['tmp_name']) && !empty($file_image['tmp_name'])) {
            $tmpname = false;
            Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);

            // Validate the uploaded image
            if (ImageManager::validateUpload($file_image, $this->max_image_size)) {
                return false;
            } elseif (!$tmpname = tempnam(_PS_TMP_IMG_DIR_, 'PS')) {
                return false;
            } elseif (!move_uploaded_file($file_image['tmp_name'], $tmpname)) {
                return false;
            } else {
                foreach (self::scanListeThemes() as $value_theme) {
                    $jpeg_target = self::imgPath() . $value_theme . '/slider/' . ($folder ? $folder . '/' : '') . $id . '.jpg';

                    // Resize and save the JPEG image
                    if (!$this->imageResize($tmpname, $jpeg_target, $w, $h)) {
                        return false;
                    }

                    // Generate WebP format if supported
                    if (function_exists('imagewebp')) {
                        $image = imagecreatefromjpeg($jpeg_target);
                        if ($image) {
                            $webp_target = self::imgPath() . $value_theme . '/slider/' . ($folder ? $folder . '/' : '') . $id . '.webp';
                            imagewebp($image, $webp_target);
                            imagedestroy($image); // Free the memory
                        }
                    }
                }
            }

            if (isset($tmpname)) {
                unlink($tmpname);
            }
        }

        return true;
    }

    private function checkAndCreateWebP($id_slide)
    {
        $target_dir = _PS_ROOT_DIR_ . '/modules/' . $this->name . '/views/img/' . PrestaBlog::getT() . '/slider/';

        $jpg_file = $target_dir . $id_slide . '.jpg';
        $webp_file = $target_dir . $id_slide . '.webp';

        if (!file_exists($webp_file)) {
            if (function_exists('imagewebp')) {
                if (file_exists($jpg_file)) {
                    $image = imagecreatefromjpeg($jpg_file);

                    if (imagewebp($image, $webp_file)) {
                        imagedestroy($image);
                    }
                }
            }
        }
    }

    private function imageResize($fichier_avant, $fichier_apres, $dest_width, $dest_height)
    {
        list($image_width, $image_height, $type) = getimagesize($fichier_avant);
        $source_image = ImageManager::create($type, $fichier_avant);

        if ($image_width > $dest_width || $image_height > $dest_height) {
            $proportion = $dest_width / $image_width;
            $dest_height = $image_height * $proportion;
            $dest_width = $dest_width;
        } else {
            $dest_height = $image_height;
            $dest_width = $image_width;
        }
        $dest_image = imagecreatetruecolor($dest_width, $dest_height);
        imagecopyresampled(
            $dest_image,
            $source_image,
            0,
            0,
            0,
            0,
            $dest_width + 1,
            $dest_height + 1,
            $image_width,
            $image_height
        );

        // compatibility with other module working on cache.
        $ret = ImageManager::write('jpg', $dest_image, $fichier_apres);
        Hook::exec('actionOnImageResizeAfter', ['dst_file' => $fichier_apres, 'file_type' => 'jpg']);

        return $ret;
    }

    public function scanDirectory($directory)
    {
        $output = [];

        if (is_dir($directory)) {
            $my_directory = opendir($directory);

            while ($entry = self::readDirectory($my_directory)) {
                if ($entry != '.' && $entry != '..') {
                    if (is_dir($directory . '/' . $entry)) {
                        $output[] = $entry;
                    }
                }
            }
            closedir($my_directory);
        }

        return $output;
    }

    public function scanFilesDirectory($directory, $expections = null)
    {
        $output = [];
        if (!is_dir($directory)) {
            return [];
        }

        $my_directory = opendir($directory);

        while ($entry = self::readDirectory($my_directory)) {
            if ($entry != '.' && $entry != '..') {
                if (count($expections) > 0) {
                    if (is_file($directory . '/' . $entry) && !in_array($entry, $expections)) {
                        $output[] = $entry;
                    }
                } elseif (is_file($directory . '/' . $entry)) {
                    $output[] = $entry;
                }
            }
        }
        closedir($my_directory);

        return $output;
    }

    public static function copyRecursive($source, $dest)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return self::copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            self::copyRecursive($source . '/' . $entry, $dest . '/' . $entry);
        }

        // Clean up
        $dir->close();

        return true;
    }

    public function createConfigFilesForAllThemes()
    {
        // Define the list of available themes
        $themes = ['grid-and-slides', 'grid-for-1-7'/* other theme names... */];
        $default_config_file = _PS_MODULE_DIR_ . 'prestablog/views/config/default.xml';

        // Check if the default configuration file exists
        if (!file_exists($default_config_file)) {
            throw new Exception('Default configuration file does not exist');
        }

        // Loop through each theme
        foreach ($themes as $theme) {
            $config_file = _PS_MODULE_DIR_ . 'prestablog/views/config/' . $theme . '.xml';

            // If a configuration file for this theme does not exist, create it
            if (!file_exists($config_file)) {
                $copy_success = copy($default_config_file, $config_file);
                if (!$copy_success) {
                    throw new Exception("Failed to copy default configuration file to $config_file");
                }
            }
        }
    }

    public function getConfigXmlTheme($theme)
    {
        $config_file = _PS_MODULE_DIR_ . 'prestablog/views/config/' . $theme . '.xml';

        // Check if the configuration file for this theme exists
        if (!file_exists($config_file)) {
            throw new Exception("Configuration file for theme $theme does not exist");
        }

        // Load and return the configuration from the file
        return simplexml_load_file($config_file);
    }

    private function retourneTexteBalise($text, $debut, $fin)
    {
        $debut = strpos($text, $debut) + Tools::strlen($debut);
        $fin = strpos($text, $fin);

        return Tools::substr($text, $debut, $fin - $debut);
    }

    private function cleanMetaKeywords($keywords)
    {
        if (!empty($keywords) && $keywords != '') {
            $out = [];
            $words = explode(',', $keywords);
            foreach ($words as $word_item) {
                $word_item = trim($word_item);
                if (!empty($word_item) && $word_item != '') {
                    $out[] = $word_item;
                }
            }

            return (count($out) > 0) ? implode(',', $out) : '';
        } else {
            return '';
        }
    }

    public static function prestablogContent($params)
    {
        return $params['return'];
    }

    public static function prestablogUrl($params)
    {
        if (Configuration::get('prestablog_urlblog') == false) {
            $base_url_blog = 'blog';
        } else {
            $base_url_blog = Configuration::get('prestablog_urlblog');
        }
        // $base_url_blog = 'articles';

        $param = null;
        $ok_rewrite = '';
        $ok_rewrite_id = '';
        $ok_rewrite_do = '';
        $ok_rewrite_cat = '';
        $ok_rewrite_categorie = '';
        $ok_rewrite_au = '';
        $ok_rewrite_page = '';
        $ok_rewrite_titre = '';
        $ok_rewrite_seo = '';
        $ok_rewrite_year = '';
        $ok_rewrite_month = '';

        $ko_rewrite = '';
        $ko_rewrite_id = '';
        $ko_rewrite_do = '';
        $ko_rewrite_cat = '';
        $ko_rewrite_au = '';
        $ko_rewrite_page = '';
        $ko_rewrite_year = '';
        $ko_rewrite_month = '';

        if (isset($params['do']) && $params['do'] != '') {
            $ko_rewrite_do = 'do=' . $params['do'];
            $ok_rewrite_do = $params['do'];
            ++$param;
        }
        if (isset($params['au']) && $params['au'] != '') {
            $ko_rewrite_au = '&au=' . $params['au'];
            $ok_rewrite_au = '-au' . $params['au'];
            ++$param;
        }
        if (isset($params['id']) && $params['id'] != '') {
            $ko_rewrite_id = '&id=' . $params['id'];
            $ok_rewrite_id = '-n' . $params['id'];
            ++$param;
        }
        if (isset($params['c']) && $params['c'] != '') {
            $ko_rewrite_cat = '&c=' . $params['c'];
            $ok_rewrite_cat = '-c' . $params['c'];
            ++$param;
        }

        if (isset($params['start'], $params['p']) && $params['start'] != '' && $params['p'] != '') {
            $ko_rewrite_page = '&start=' . $params['start'] . '&p=' . $params['p'];
            $ok_rewrite_page = $params['start'] . 'p' . $params['p'];
            ++$param;
        }
        if (isset($params['titre']) && $params['titre'] != '') {
            $ok_rewrite_titre = PrestaBlog::prestablogFilter(Tools::link_rewrite($params['titre']));
            ++$param;
        }
        if (isset($params['categorie']) && $params['categorie'] != '') {
            $ok_rewrite_categorie = PrestaBlog::prestablogFilter(Tools::link_rewrite($params['categorie']));
            if (isset($params['start'], $params['p']) && $params['start'] != '' && $params['p'] != '') {
                $ok_rewrite_categorie .= '-';
            } else {
                $ok_rewrite_categorie .= '';
            }
            ++$param;
        }
        if (isset($params['seo']) && $params['seo'] != '') {
            $ok_rewrite_titre = PrestaBlog::prestablogFilter(Tools::link_rewrite($params['seo']));
            ++$param;
        }
        if (isset($params['y']) && $params['y'] != '') {
            $ko_rewrite_year = '&y=' . $params['y'];
            $ok_rewrite_year = 'y' . $params['y'];
            ++$param;
        }
        if (isset($params['m']) && $params['m'] != '') {
            $ko_rewrite_month = '&m=' . $params['m'];
            $ok_rewrite_month = '-m' . $params['m'];
            ++$param;
        }
        if (isset($params['seo']) && $params['seo'] != '') {
            $ok_rewrite_seo = $params['seo'];
            $ok_rewrite_titre = '';
            ++$param;
        }
        if (isset($params) && count($params) > 0 && !isset($params['rss'])) {
            $ok_rewrite = $base_url_blog . '/' . $ok_rewrite_do . $ok_rewrite_categorie . $ok_rewrite_page;
            $ok_rewrite .= $ok_rewrite_year . $ok_rewrite_month . $ok_rewrite_titre . $ok_rewrite_seo;
            $ok_rewrite .= $ok_rewrite_cat . $ok_rewrite_id;
            $ok_rewrite .= $ok_rewrite_au;

            $ko_rewrite = '?fc=module&module=prestablog&controller=blog&' . ltrim(
                $ko_rewrite_do . $ko_rewrite_id . $ko_rewrite_cat . $ko_rewrite_au . $ko_rewrite_page . $ko_rewrite_year . $ko_rewrite_month,
                '&'
            );
        } elseif (isset($params['rss'])) {
            if ($params['rss'] == 'all') {
                $ok_rewrite = 'rss';
                $ko_rewrite = '?fc=module&module=prestablog&controller=rss';
            } else {
                $ok_rewrite = 'rss/' . $params['rss'];
                $ko_rewrite = '?fc=module&module=prestablog&controller=rss&rss=' . $params['rss'];
            }
        } else {
            $ok_rewrite = $base_url_blog;
            $ko_rewrite = '?fc=module&module=prestablog&controller=blog';
        }

        if (!isset($params['id_lang'])) {
            (int) $params['id_lang'] = null;
        }

        if ((int) Configuration::get('PS_REWRITING_SETTINGS') && (int) Configuration::get('prestablog_rewrite_actif')) {
            return self::getBaseUrlFront((int) $params['id_lang']) . $ok_rewrite;
        } else {
            return self::getBaseUrlFront((int) $params['id_lang']) . $ko_rewrite;
        }
    }

    public static function getBaseUrlFront($id_lang = null)
    {
        return self::urlSRoot() . self::getLangLink($id_lang);
    }

    public static function getLangLink($id_lang = null)
    {
        $context = Context::getContext();

        if (!Configuration::get('PS_REWRITING_SETTINGS')) {
            return '';
        }
        if (Language::countActiveLanguages() <= 1) {
            return '';
        }
        if (!$id_lang) {
            $id_lang = $context->language->id;
        }

        return Language::getIsoById((int) $id_lang) . '/';
    }

    public static function prestablogFilter($retourne)
    {
        $search = ['/--+/'];
        $replace = ['-'];

        $retourne = Tools::strtolower(preg_replace($search, $replace, $retourne));

        $url_replace = [
            '/А/' => 'A', '/а/' => 'a',
            '/Б/' => 'B', '/б/' => 'b',
            '/В/' => 'V', '/в/' => 'v',
            '/Г/' => 'G', '/г/' => 'g',
            '/Д/' => 'D', '/д/' => 'd',
            '/Е/' => 'E', '/е/' => 'e',
            '/Ж/' => 'J', '/ж/' => 'j',
            '/З/' => 'Z', '/з/' => 'z',
            '/И/' => 'I', '/и/' => 'i',
            '/Й/' => 'Y', '/й/' => 'y',
            '/К/' => 'K', '/к/' => 'k',
            '/Л/' => 'L', '/л/' => 'l',
            '/М/' => 'M', '/м/' => 'm',
            '/Н/' => 'N', '/н/' => 'n',
            '/О/' => 'O', '/о/' => 'o',
            '/П/' => 'P', '/п/' => 'p',
            '/Р/' => 'R', '/р/' => 'r',
            '/С/' => 'S', '/с/' => 's',
            '/Т/' => 'T', '/т/' => 't',
            '/У/' => 'U', '/у/' => 'u',
            '/Ф/' => 'F', '/ф/' => 'f',
            '/Х/' => 'H', '/х/' => 'h',
            '/Ц/' => 'C', '/ц/' => 'c',
            '/Ч/' => 'CH', '/ч/' => 'ch',
            '/Ш/' => 'SH', '/ш/' => 'sh',
            '/Щ/' => 'SHT', '/щ/' => 'sht',
            '/Ъ/' => 'A', '/ъ/' => 'a',
            '/Ь/' => 'X', '/ь/' => 'x',
            '/Ю/' => 'YU', '/ю/' => 'yu',
            '/Я/' => 'YA', '/я/' => 'ya',
        ];

        $cyrillic_find = array_keys($url_replace);
        $cyrillic_replace = array_values($url_replace);

        $retourne = Tools::strtolower(preg_replace($cyrillic_find, $cyrillic_replace, $retourne));

        return $retourne;
    }

    public static function getPrestaBlogMetaTagsNewsOnly($id_lang, $id = null)
    {
        if ($id) {
            $row = [];

            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                                                                SELECT `title`, `meta_title`, `meta_description`, `meta_keywords`
                                                                FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_news_lang`
                                                                WHERE id_lang = ' . (int) $id_lang . ' AND id_prestablog_news = ' . (int) $id);
        }
        if ($row) {
            return self::completeMetaTags($row);
        }
    }

    public static function getPrestaBlogMetaTagsNewsCat($id_lang, $id = null)
    {
        if ($id) {
            $row = [];

            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                                                                SELECT `title`, `meta_title`, `meta_description`, `meta_keywords`
                                                                FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_categorie_lang`
                                                                WHERE id_lang = ' . (int) $id_lang . ' AND id_prestablog_categorie = ' . (int) $id);
        }
        if ($row) {
            return self::completeMetaTags($row);
        }
    }

    public static function getPrestaBlogMetaTagsPage($id_lang)
    {
        $row = [
            'title' => Configuration::get('prestablog_titlepageblog', (int) $id_lang),
            'meta_title' => Configuration::get('prestablog_titlepageblog', (int) $id_lang),
            'meta_description' => Configuration::get('prestablog_descpageblog', (int) $id_lang),
        ];

        return self::completeMetaTags($row);
    }

    public static function getPrestaBlogMetaTagsNewsDate()
    {
        return self::completeMetaTags(null);
    }

    public static function completeMetaTags($meta_tags)
    {
        $context = Context::getContext();

        $prestablog = new PrestaBlog();

        if (empty($meta_tags['meta_title'])) {
            $meta_tags['meta_title'] = $meta_tags['title'];
        }
        if (empty($meta_tags['meta_description'])) {
            $meta_tags['meta_description'] = '';
        }
        if (empty($meta_tags['meta_keywords'])) {
            $meta_tags['meta_keywords'] = '';
            if (Configuration::get('PS_META_KEYWORDS', (int) $context->language->id)) {
                $meta_tags['meta_keywords'] = Configuration::get('PS_META_KEYWORDS', (int) $context->language->id);
            }
        }

        $metatile = '';

        $metatile .= (Tools::getValue('p') ? ' - ' . $prestablog->l('page') . ' ' . Tools::getValue('p') : '');
        $metatile .= (Tools::getValue('y') ? ' - ' . Tools::getValue('y') : '');
        $metatile .= (Tools::getValue('m') ? ' - ' . $prestablog->mois_langue[Tools::getValue('m')] : '');

        $meta_tags['meta_title'] .= $metatile;

        $metadesc = '';
        $metadesc .= (Tools::getValue('p') ? ' - ' . $prestablog->l('page') . ' ' . Tools::getValue('p') : '');
        $metadesc .= (Tools::getValue('y') ? ' - ' . Tools::getValue('y') : '');
        $metadesc .= (Tools::getValue('m') ? ' - ' . $prestablog->mois_langue[Tools::getValue('m')] : '');

        $meta_tags['meta_description'] .= $metadesc;

        $meta_tags['meta_title'] = ltrim($meta_tags['meta_title'], ' - ');
        $meta_tags['meta_description'] = ltrim($meta_tags['meta_description'], ' - ');

        return $meta_tags;
    }

    public static function getPagination($count_liste, $entites_en_moins = 0, $end = 10, $start = 0, $p = 1)
    {
        $pagination = [];

        $pagination['NombreTotalEntites'] = ($count_liste - $entites_en_moins);

        $pagination['NombreTotalPages'] = ceil((int) $pagination['NombreTotalEntites'] / (int) $end);

        if ($pagination['NombreTotalEntites'] > 0) {
            if ($p) {
                $pagination['PageCourante'] = (int) $p;
                $pagination['PagePrecedente'] = (int) $p - 1;
                $pagination['PageSuivante'] = (int) $p + 1;
            } else {
                $pagination['PageCourante'] = 1;
                $pagination['PagePrecedente'] = 0;
                $pagination['PageSuivante'] = 2;
            }

            if ($start) {
                $pagination['StartCourant'] = (int) $start;
                $pagination['StartPrecedent'] = (int) $start - (int) $end;
                $pagination['StartSuivant'] = (int) $start + (int) $end;
            } else {
                $pagination['StartCourant'] = 0;
                $pagination['StartPrecedent'] = 0;
                $pagination['StartSuivant'] = (int) $end;
            }
            for ($icount = 1; $icount <= (int) $pagination['NombreTotalPages']; ++$icount) {
                $pagination['Pages'][$icount] = ($icount - 1) * (int) $end;
            }

            if (count($pagination['Pages']) <= 5) {
                $pagination['PremieresPages'] = array_slice($pagination['Pages'], 0, 5, true);
                unset($pagination['Pages']);
            } else {
                $pagination['PremieresPages'] = array_slice($pagination['Pages'], 0, 1, true);
                if ($pagination['PageCourante'] == 1) {
                    $pagination['Pages'] = array_slice(
                        $pagination['Pages'],
                        $pagination['PageCourante'] - 1,
                        6,
                        true
                    );
                } else {
                    if ($pagination['PageCourante'] + 4 >= $pagination['NombreTotalPages']) {
                        $pagination['Pages'] = array_slice(
                            $pagination['Pages'],
                            $pagination['NombreTotalPages'] - 5,
                            5,
                            true
                        );
                    } else {
                        $pagination['Pages'] = array_slice(
                            $pagination['Pages'],
                            $pagination['PageCourante'] - 1,
                            5,
                            true
                        );
                    }
                }
            }
        }

        return $pagination;
    }

    /* Auto cropping picture when posting news */
    public function autocropImage($image_source, $rep_source, $rep_dest, $tl, $th, $prefixe, $change_nom)
    {
        // Cast dimensions to integers
        $tl = (int) $tl;
        $th = (int) $th;

        // Construct the full path to the image
        $full_path = $rep_source . $image_source;
        // Use pathinfo to get the file extension
        $extensionSource = strtolower(pathinfo($image_source, PATHINFO_EXTENSION));

        // Load the image based on its extension
        switch ($extensionSource) {
            case 'png':
                $imageSource = imagecreatefrompng($full_path);
                break;
            case 'jpg':
            case 'jpeg':
                $imageSource = imagecreatefromjpeg($full_path);
                break;
            default:
                // Handle unsupported extensions
                return false;
        }

        // Get source image dimensions
        $sl = (int) imagesx($imageSource);
        $sh = (int) imagesy($imageSource);

        // If the image already matches the target dimensions, copy instead of crop
        if ($sl === $tl && $sh === $th) {
            // Determine the base image name
            $base_image_name = $change_nom ?: preg_replace('/\.(jpg|jpeg|png)$/i', '', $image_source);
            // Define the paths for the copied and WebP images
            $jpegImagePath = $rep_dest . $prefixe . $base_image_name . '.jpg';
            $webpImagePath = $rep_dest . $prefixe . $base_image_name . '.webp';

            // Copy the image directly for JPEG
            copy($full_path, $jpegImagePath);

            // Create a WebP version if supported
            if (function_exists('imagewebp')) {
                $imageForWebP = imagecreatefromjpeg($jpegImagePath); // Create an image resource from JPEG for conversion
                imagewebp($imageForWebP, $webpImagePath, 90); // Convert to WebP
                imagedestroy($imageForWebP); // Free the image resource
            }
        } else {
            $sourceWidth = imagesx($imageSource);
            $sourceHeight = imagesy($imageSource);

            $ratioSource = $sourceWidth / $sourceHeight;
            $ratioTarget = $tl / $th;

            // Calculating dimensions for resizing before cropping
            if ($ratioSource > $ratioTarget) {
                $newHeight = $th;
                $newWidth = (int) ($th * $ratioSource);
            } else {
                $newWidth = $tl;
                $newHeight = (int) ($tl / $ratioSource);
            }

            // Preparation of the destination image
            $imageBeforeCrop = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($imageBeforeCrop, $imageSource, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

            // Define coordonate of the crop
            $x = ($newWidth - $tl) / 2;
            $y = ($newHeight - $th) / 2;

            $finalImage = imagecreatetruecolor($tl, $th);
            imagecopyresampled($finalImage, $imageBeforeCrop, 0, 0, $x, $y, $tl, $th, $tl, $th);

            // Construction of the base name of the image
            $baseImageName = $change_nom ?: preg_replace('/\.(jpg|jpeg|png)$/i', '', $image_source);

            // Saving WebP format //
            if (function_exists('imagewebp')) {
                $webpImagePath = $rep_dest . $prefixe . $baseImageName . '.webp';
                imagewebp($finalImage, $webpImagePath, 100);
                Hook::exec('actionOnImageResizeAfter', ['dst_file' => $webpImagePath, 'file_type' => 'webp']);
            }

            // Saving Jpeg format //
            $jpegImagePath = $rep_dest . $prefixe . $baseImageName . '.jpg';
            imagejpeg($finalImage, $jpegImagePath, 100);
            Hook::exec('actionOnImageResizeAfter', ['dst_file' => $jpegImagePath, 'file_type' => 'jpeg']);
        }
        imagedestroy($imageSource);

        return true;
    }

    /* update a picture with crop in news */
    public function cropImage(
        $image_source,
        $rep_source,
        $rep_dest,
        $w_image_base,
        $h_image_base,
        $w_image_dest,
        $h_image_dest,
        $x_crop_base,
        $y_crop_base,
        $w_crop_base,
        $h_crop_base,
        $prefixe,
        $change_nom)
    {
        $full_path = $rep_source . $image_source;
        $ext = strtolower(pathinfo($image_source, PATHINFO_EXTENSION));
        $dst_r = imagecreatetruecolor($w_image_dest, $h_image_dest);

        list($w_image_source, $h_image_source) = getimagesize($full_path);

        $w_ratio = $w_image_source / $w_image_base;
        $h_ratio = $h_image_source / $h_image_base;

        $x_crop_base *= $w_ratio;
        $y_crop_base *= $h_ratio;
        $w_crop_base *= $w_ratio;
        $h_crop_base *= $h_ratio;

        switch ($ext) {
            case 'png':
                $image = imagecreatefrompng($full_path);
                break;

            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($full_path);
                break;

            default:
                break;
        }

        imagecopyresampled(
            $dst_r,
            $image,
            0,
            0,
            $x_crop_base,
            $y_crop_base,
            $w_image_dest,
            $h_image_dest,
            $w_crop_base,
            $h_crop_base
        );

        // Define the base name for saving
        $base_name = $change_nom ?: preg_replace('/\.(jpg|jpeg|png)$/i', '', $image_source);

        // Paths for the JPEG and WebP images
        $jpeg_path = $rep_dest . $prefixe . $base_name . '.jpg';
        $webp_path = $rep_dest . $prefixe . $base_name . '.webp';

        // Save JPEG version
        if ($ext === 'png' || $ext === 'jpg' || $ext === 'jpeg') {
            imagejpeg($dst_r, $jpeg_path, 90);
            // Hook for compatibility with module Page Cache Ultimate and WEBP for JPEG
            Hook::exec('actionOnImageResizeAfter', ['dst_file' => $jpeg_path, 'file_type' => 'jpeg']);
        }

        // Save WebP version if possible
        if (function_exists('imagewebp')) {
            imagewebp($dst_r, $webp_path, 90);
            // Hook for compatibility with module Page Cache Ultimate and WEBP for WebP
            Hook::exec('actionOnImageResizeAfter', ['dst_file' => $webp_path, 'file_type' => 'webp']);
        }

        imagedestroy($dst_r);
    }

    public function gestAntiSpam()
    {
        if ($this->checksum != '') {
            return AntiSpamClass::getAntiSpamByChecksum($this->checksum);
        } else {
            $liste = AntiSpamClass::getListe((int) $this->context->language->id, 1);
            if (count($liste) > 0) {
                return $liste[array_rand($liste, 1)];
            } else {
                return false;
            }
        }
    }

    public static function newsRatingID($id_news, $id_session)
    {
        if (isset($id_session)) {
            NewsClass::insertRateId($id_news, $id_session);

            return true;
        } else {
            return false;
        }
    }

    public static function newsRating($id_news, $rate)
    {
        if (isset($rate)) {
            NewsClass::insertRating($id_news, $rate);

            return true;
        } else {
            return false;
        }
    }

    public function gestComment($id_news)
    {
        if (!Configuration::get($this->name . '_comment_actif')) {
            return false;
        }

        $errors = [];
        $is_submit = true;
        $content_form = [
            'news' => (int) $id_news,
            'name' => trim(Tools::getValue('name')),
            'url' => trim(Tools::getValue('url')),
            'comment' => trim(Tools::getValue('comment')),
            'date' => date('Y-m-d H:i:s'),
            'actif' => (Configuration::get($this->name . '_comment_auto_actif') ? 1 : 0 - 1),
            'antispam_checksum' => '',
            'id_parent' => (int) Tools::getValue('id_parent'),
        ];

        if (Tools::getValue('submitComment')) {
            if (Configuration::get('prestablog_antispam_actif')) {
                $liste_as = AntiSpamClass::getListe((int) $this->context->language->id, 1);
                if (count($liste_as) > 0) {
                    foreach ($liste_as as $value_as) {
                        if (Tools::getIsset($value_as['checksum'])) {
                            $content_form['antispam_checksum'] = Tools::getValue($value_as['checksum']);
                            $this->checksum = $value_as['checksum'];
                            if (Tools::getValue($value_as['checksum']) != $value_as['reply']) {
                                $errors[$value_as['checksum']] = $this->trans('Your antispam reply is not correct.', [], 'Modules.Prestablog.Prestablog');
                            }
                        }
                    }
                }
            }

            $ereg_url = '#^\b(((http|https)\:\/\/)[^\s()]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))$#';
            if (Tools::strlen($content_form['name']) < 3) {
                $errors['name'] = $this->trans('Your name cannot be empty or inferior at 3 characters.', [], 'Modules.Prestablog.Prestablog');
            }
            if (Tools::strlen($content_form['comment']) < 5) {
                $errors['comment'] = $this->trans('Your comment cannot be empty or inferior at 5 characters.', [], 'Modules.Prestablog.Prestablog');
            }

            if (Configuration::get('prestablog_captcha_actif')) {
                if (!($gcaptcha = (int) Tools::getValue('g-recaptcha-response'))) {
                    $errors['url'] = $this->trans('Make sure to validate the captcha.', [], 'Modules.Prestablog.Prestablog');
                }
            }

            if (count($errors) > 0) {
                $is_submit = false;
            } else {
                CommentNewsClass::insertComment(
                    $content_form['news'],
                    $content_form['date'],
                    $content_form['name'],
                    $content_form['url'],
                    $content_form['comment'],
                    $content_form['actif'],
                    $content_form['id_parent']
                );

                if (Configuration::get($this->name . '_comment_alert_admin')) {
                    $news = new NewsClass((int) $content_form['news'], $this->langue_default_store);
                    $content_form['title_news'] = $news->title;

                    $urlnews = Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . $this->ctrblog;
                    $urlnews .= '&id=' . $content_form['news'];

                    Mail::Send(
                        $this->langue_default_store,
                        'feedback-admin',
                        $this->trans('New comment', [], 'Modules.Prestablog.Prestablog') . ' / ' . $content_form['title_news'],
                        [
                            '{news}' => $content_form['news'],
                            '{title_news}' => $content_form['title_news'],
                            '{date}' => ToolsCore::displayDate($content_form['date'], null, true),
                            '{name}' => $content_form['name'],
                            '{url}' => $content_form['url'],
                            '{comment}' => $content_form['comment'],
                            '{url_news}' => $urlnews,
                            '{actif}' => $content_form['actif'],
                        ],
                        Configuration::get($this->name . '_comment_admin_mail'),
                        null,
                        Configuration::get('PS_SHOP_EMAIL'),
                        Configuration::get('PS_SHOP_NAME'),
                        null,
                        null,
                        dirname(__FILE__) . '/mails/'
                    );
                }

                $liste_abo = CommentNewsClass::listeCommentMailAbo($content_form['news']);

                if (Configuration::get($this->name . '_comment_subscription')
                                                              && count($liste_abo) > 0
                                                              && Configuration::get($this->name . '_comment_auto_actif')
                ) {
                    $news = new NewsClass((int) $content_form['news'], $this->langue_default_store);
                    $content_form['title_news'] = $news->title;

                    $urlnews = Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . $this->ctrblog;
                    $urlnews .= '&id=' . $content_form['news'];
                    $urldesabo = $urlnews . '&d=' . $content_form['news'];

                    foreach ($liste_abo as $value_abo) {
                        Mail::Send(
                            $this->langue_default_store,
                            'feedback-subscribe',
                            $this->trans('New comment', [], 'Modules.Prestablog.Prestablog') . ' / ' . $content_form['title_news'],
                            [
                                '{news}' => $content_form['news'],
                                '{title_news}' => $content_form['title_news'],
                                '{url_news}' => $urlnews,
                                '{url_desabonnement}' => $urldesabo,
                            ],
                            $value_abo,
                            null,
                            Configuration::get('PS_SHOP_EMAIL'),
                            Configuration::get('PS_SHOP_NAME'),
                            null,
                            null,
                            dirname(__FILE__) . '/mails/'
                        );
                    }
                }

                $is_submit = true;
            }
        } else {
            $is_submit = false;
        }
        $comments = CommentNewsClass::getListe(1, $id_news);

        foreach ($comments as &$comment) {
            $comment['replies'] = CommentNewsClass::getReplies($comment['id_prestablog_commentnews'], true);
        }

        $this->context->smarty->assign([
            'isSubmit' => $is_submit,
            'errors' => $errors,
            'content_form' => $content_form,
            'comments' => $comments,
        ]);

        return true;
    }

    public function blocDateListe()
    {
        $actif_filtre = 'n.`actif` = 1';
        $multiboutique_filtre = ' AND n.`id_shop` = ' . (int) $this->context->shop->id;
        $langue_filtre = ' AND nl.`id_lang` = ' . (int) $this->context->language->id;
        $actif_langue_filtre = ' AND nl.`actif_langue` = 1';

        $filtre_groupes = PrestaBlog::getFiltreGroupes('cc.`categorie`', 'categorie');

        $all_filtres = $actif_filtre . $multiboutique_filtre . $langue_filtre . $actif_langue_filtre . $filtre_groupes;

        if (Configuration::get($this->name . '_datenews_actif')) {
            $result_date_liste = [];

            $fin_reelle = 'TIMESTAMP(n.`date`) <= \'' . date('Y/m/d H:i:s') . '\'';

            $result_annee = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
                                                          SELECT    DISTINCT YEAR(n.`date`) AS `annee`
                                                          FROM `' . bqSQL(_DB_PREFIX_) . NewsClass::$table_static . '_lang` as nl
                                                          LEFT JOIN `' . bqSQL(_DB_PREFIX_) . NewsClass::$table_static . '` as n
                                                          ON (n.`id_prestablog_news` = nl.`id_prestablog_news`)
                                                          LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_correspondancecategorie` cc
                                                          ON (n.`id_prestablog_news` = cc.`news`)
                                                          WHERE ' . $all_filtres . '
                                                          AND ' . $fin_reelle . '
                                                          ORDER BY annee ' . pSQL(Configuration::get($this->name . '_datenews_order')));

            if (count($result_annee) > 0) {
                foreach ($result_annee as $value_annee) {
                    $result_count_annee = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
                                                              SELECT COUNT(DISTINCT nl.`id_prestablog_news`) AS `value`
                                                              FROM `' . bqSQL(_DB_PREFIX_) . NewsClass::$table_static . '_lang` as nl
                                                              LEFT JOIN `' . bqSQL(_DB_PREFIX_) . NewsClass::$table_static . '` as n
                                                              ON (n.`id_prestablog_news` = nl.`id_prestablog_news`)
                                                              LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_correspondancecategorie` cc
                                                              ON (n.`id_prestablog_news` = cc.`news`)
                                                              WHERE ' . $all_filtres . '
                                                              AND ' . $fin_reelle . '
                                                              AND YEAR(n.`date`) = \'' . pSQL($value_annee['annee']) . '\'');

                    $result_date_liste[$value_annee['annee']]['nombre_news'] = $result_count_annee['value'];

                    $result_mois = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
                                                              SELECT    DISTINCT MONTH(n.`date`) AS `mois`
                                                              FROM `' . bqSQL(_DB_PREFIX_) . NewsClass::$table_static . '_lang` as nl
                                                              LEFT JOIN `' . bqSQL(_DB_PREFIX_) . NewsClass::$table_static . '` as n
                                                              ON (n.`id_prestablog_news` = nl.`id_prestablog_news`)
                                                              LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_correspondancecategorie` cc
                                                              ON (n.`id_prestablog_news` = cc.`news`)
                                                              WHERE ' . $all_filtres . '
                                                              AND YEAR(n.`date`) = ' . pSQL($value_annee['annee']) . '
                                                              AND ' . $fin_reelle . '
                                                              ORDER BY mois ' . pSQL(Configuration::get($this->name . '_datenews_order')));

                    if (count($result_mois) > 0) {
                        foreach ($result_mois as $value_mois) {
                            $result_count_mois = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
                                                                  SELECT COUNT(DISTINCT n.`id_prestablog_news`) AS `value`
                                                                  FROM `' . bqSQL(_DB_PREFIX_) . NewsClass::$table_static . '_lang` as nl
                                                                  LEFT JOIN `' . bqSQL(_DB_PREFIX_) . NewsClass::$table_static . '` as n
                                                                  ON (n.`id_prestablog_news` = nl.`id_prestablog_news`)
                                                                  LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_correspondancecategorie` cc
                                                                  ON (n.`id_prestablog_news` = cc.`news`)
                                                                  WHERE ' . $all_filtres . '
                                                                  AND ' . $fin_reelle . '
                                                                  AND YEAR(n.`date`) = ' . pSQL($value_annee['annee']) . '
                                                                  AND MONTH(n.`date`) = ' . pSQL($value_mois['mois']));

                            $e1 = $result_count_mois['value'];
                            $e2 = $this->mois_langue[$value_mois['mois']];

                            $result_date_liste[$value_annee['annee']]['mois'][$value_mois['mois']]['nombre_news'] = $e1;
                            $result_date_liste[$value_annee['annee']]['mois'][$value_mois['mois']]['mois_value'] = $e2;
                        }
                    }
                }
            }

            $this->context->smarty->assign([
                'ResultDateListe' => $result_date_liste,
                'prestablog_annee' => Tools::getValue('prestablog_annee'),
            ]);

            $this->context->controller->registerJavascript(
                'modules-prestablog-dateliste',
                'modules/prestablog/views/js/dateliste.js',
                ['position' => 'bottom', 'priority' => 200]
            );

            return $this->display(__FILE__, self::getT() . '_bloc-dateliste.tpl');
        }
    }

    public function blocLastListe()
    {
        if (Configuration::get($this->name . '_lastnews_actif')) {
            $tri_champ = 'n.`date`';
            $tri_ordre = 'desc';
            $liste = NewsClass::getListe(
                (int) $this->context->language->id,
                1,
                0,
                0,
                (int) Configuration::get($this->name . '_lastnews_limit'),
                $tri_champ,
                $tri_ordre,
                null,
                date('Y/m/d H:i:s'),
                null,
                1,
                (int) Configuration::get('prestablog_lastnews_title_length'),
                (int) Configuration::get('prestablog_lastnews_intro_length')
            );

            $this->context->smarty->assign(['ListeBlocLastNews' => $liste]);

            return $this->display(__FILE__, self::getT() . '_bloc-lastliste.tpl');
        }
    }

    public function blocCatListe()
    {
        $tplcatlist = _PS_MODULE_DIR_ . 'prestablog/views/templates/hook/';
        $tplcatlist .= self::getT() . '_bloc-catliste-tree-branch.tpl';

        if (Configuration::get($this->name . '_catnews_actif')) {
            $categorie_courante_id = (int) Tools::getValue('c');
            $categorie_courante = null;
            $categorie_parente = null;

            if ($categorie_courante_id) {
                $categorie_courante = new CategoriesClass(
                    $categorie_courante_id,
                    (int) $this->context->cookie->id_lang
                );
                if ($categorie_courante->parent) {
                    $categorie_parente = new CategoriesClass(
                        (int) $categorie_courante->parent,
                        (int) $this->context->cookie->id_lang
                    );
                }
            }

            $categories_parents = CategoriesClass::getListe((int) $this->context->language->id, 1, 0);

            $sousCategories = [];
            if ($categorie_courante) {
                $sousCategories = CategoriesClass::getListe((int) $this->context->language->id, 1, (int) $categorie_courante->id);

                if (count($sousCategories) > 0) {
                    foreach ($sousCategories as $key => $value) {
                        if (!Configuration::get($this->name . '_catnews_empty') && (int) $value['nombre_news_recursif'] == 0) {
                            unset($sousCategories[$key]);
                        } else {
                            $sousCategories[$key]['nombre_news'] = (int) $value['nombre_news'];
                        }
                    }
                }
            }

            $this->context->smarty->assign([
                'prestablog_categorie_courante' => $categorie_courante,
                'prestablog_categorie_parent' => $categorie_parente,
                'ListeBlocCatNews' => $categories_parents,
                'SousCategories' => $sousCategories,
                'tree_branch_path' => $tplcatlist,
            ]);

            return $this->display(__FILE__, self::getT() . '_bloc-catliste.tpl');
        }
    }

    public function hookDisplayNav()
    {
        return $this->display(__FILE__, self::getT() . '_nav-top.tpl', $this->getCacheId());
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS(
            $this->_path . 'views/css/admin.css',
            'all'
        );
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS(
            $this->_path . 'views/css/' . self::getT() . '-module.css',
            'all'
        );
        $this->context->controller->addCSS(
            $this->_path . 'views/css/' . self::getT() . '-module-widget.css',
            'all'
        );
        $this->context->controller->addCSS(
            $this->_path . 'views/css/blog' . (int) $this->context->shop->id . '.css',
            'all'
        );
        $this->context->controller->addCSS(
            $this->_path . 'views/css/custom' . (int) $this->context->shop->id . '.css',
            'all'
        );
        if (Configuration::get($this->name . '_material_icons')) {
            $this->context->controller->registerStylesheet(
                'material-icons',
                'https://fonts.googleapis.com/icon?family=Material+Icons',
                ['server' => 'remote']
            );
        }
        $this->news = new NewsClass((int) Tools::getValue('id'), (int) $this->context->cookie->id_lang);
        $this->categories = new CategoriesClass((int) Tools::getValue('c'));
        // Inclusion du JS pour les différents slides.
        $currentTheme = PrestaBlog::getT();
        if ($currentTheme == 'grid-and-slides') {
            $this->context->controller->registerJavascript(
                'modules-prestablog-swip-min',
                'modules/prestablog/views/js/prestablog-swip.min.js',
                ['position' => 'top', 'priority' => 1]
            );
        }
        if (Configuration::get($this->name . '_popup_general') == 1) {
            $id_prestablog_popup_news = PopupClass::getIdFrontPopupNewsPreFiltered($this->news->id);
            $id_prestablog_popup_cate = PopupClass::getIdFrontPopupCatePreFiltered($this->categories->id);
            $popuplink = PopupClass::getPopupActifHome();
            if (isset($popuplink[0]['id_prestablog_popup'])) {
                $id_prestablog_popup_home = $popuplink[0]['id_prestablog_popup'];
            }

            if (isset($id_prestablog_popup_news) && $id_prestablog_popup_news == $this->isOkDisplay($this->news->id)) {
                $popup = new PopupClass((int) $id_prestablog_popup_news, (int) $this->context->language->id, null, $this->getTranslator());
                $this->context->controller->addCss($this->_path . 'views/css/bootstrap-modal.css');
                $this->context->controller->addCSS($this->_path . 'views/css/theme-' . $popup->theme . '.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/popup.js');
            }
            if (isset($id_prestablog_popup_cate) && $id_prestablog_popup_cate == $this->isOkDisplayCate($this->categories->id)) {
                $popup = new PopupClass((int) $id_prestablog_popup_cate, (int) $this->context->language->id, null, $this->getTranslator());
                $this->context->controller->addCss($this->_path . 'views/css/bootstrap-modal.css');
                $this->context->controller->addCSS($this->_path . 'views/css/theme-' . $popup->theme . '.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/popup.js');
            }
            if (isset($id_prestablog_popup_home) && $id_prestablog_popup_home == $this->isOkDisplayHome()) {
                $popup = new PopupClass((int) $id_prestablog_popup_home, (int) $this->context->language->id, null, $this->getTranslator());
                $this->context->controller->addCss($this->_path . 'views/css/bootstrap-modal.css');
                $this->context->controller->addCSS($this->_path . 'views/css/theme-' . $popup->theme . '.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/popup.js');
            }
        }
        $this->context->controller->addJS($this->_path . 'views/js/collaps.js');
        $this->context->controller->addJS('https://www.google.com/recaptcha/api.js');
        /* This is a fix to solve the bug of other modules that duplicate the displayheader process */
        if (!isset($this->context->smarty->registered_plugins['function']['PrestaBlogUrl'])) {
            smartyRegisterFunction(
                $this->context->smarty,
                'function',
                'PrestaBlogUrl',
                ['PrestaBlog', 'prestablogUrl']
            );
        }

        /* Echap html content / js */
        if (!isset($this->context->smarty->registered_plugins['function']['PrestaBlogContent'])) {
            smartyRegisterFunction(
                $this->context->smarty,
                'function',
                'PrestaBlogContent',
                ['PrestaBlog', 'prestablogContent']
            );
        }

        /* facebook share only on news */

        if (isset($this->context->controller->module->name)
                                                  && $this->context->controller->module->name == $this->name
                                                  && Tools::getValue('id')
        ) {
            $this->news = new NewsClass((int) Tools::getValue('id'), (int) $this->context->cookie->id_lang);
            if (file_exists(_PS_MODULE_DIR_ . 'prestablog/views/img/' . self::getT() . '/up-img/' . $this->news->id . '.jpg')) {
                $news_image_url = self::urlSRoot() . 'modules/prestablog/views/img/';
                $news_image_url .= self::getT() . '/up-img/' . $this->news->id . '.jpg';
            } else {
                $news_image_url = self::urlSRoot() . 'img/logo.jpg';
            }

            /* moderateurs commentaires facebook */
            $list_fb_moderators = [];
            if (Configuration::get($this->name . '_commentfb_actif')) {
                $list_fb_moderators = json_decode(Configuration::get($this->name . '_commentfb_modosId'), true);
            }
            /* /moderateurs commentaires facebook */

            $this->context->smarty->assign([
                'prestablog_news_meta' => $this->news,
                'prestablog_news_meta_img' => $news_image_url,
                'prestablog_news_meta_url' => PrestaBlog::prestablogUrl([
                    'id' => $this->news->id,
                    'seo' => $this->news->link_rewrite,
                    'titre' => $this->news->title,
                ]),
                'prestablog_fb_admins' => $list_fb_moderators,
                'prestablog_fb_appid' => Configuration::get('prestablog_commentfb_apiId'),
            ]);

            return $this->display(__FILE__, self::getT() . '_header-meta-og.tpl');
        }
    }

    public function hookDisplayRating()
    {
        $html_out = '';
        if (Configuration::get($this->name . '_rating_actif')) {
            $html_out .= $this->showRating();
        }

        return $html_out;
    }

    public function hookDisplaySlider()
    {
        $html = '';
        $config = $this->getConfigFormValues();
        $layerslider = Module::getInstanceByName('layerslider');

        if ($layerslider && !empty($config['DISPLAYSLIDER_ID'])) {
            require_once _PS_MODULE_DIR_ . 'layerslider/helper.php';
            require_once _PS_MODULE_DIR_ . 'layerslider/base/layerslider.php';
            $html = $layerslider->generateSlider($config['DISPLAYSLIDER_ID']);
        }

        return $html;
    }

    public function hookDisplayHome()
    {
        $html_out = '';
        if (Configuration::get($this->name . '_homenews_actif')) {
            $html_out .= $this->showSlide();
        }

        if (Configuration::get($this->name . '_subblocks_actif')) {
            $html_out .= $this->showSubBlocks('displayHome');
        }

        return $html_out;
    }

    public function hookDisplayPrestaBlogList($params)
    {
        $html_out = '';
        $liste_subblocks = SubBlocksClass::getListe((int) $this->context->language->id, 1, 'displayCustomHook');

        if (count($liste_subblocks) > 0) {
            foreach ($liste_subblocks as $value) {
                if ((int) $value['id_prestablog_subblock'] == (int) $params['id']) {
                    $news_liste = self::returnUniversalNewsListSubBlocks($value, (int) $this->context->language->id);

                    if (count($news_liste) > 0) {
                        if ($value['random']) {
                            shuffle($news_liste);
                        }

                        $this->context->smarty->assign([
                            'subblocks' => $value,
                            'news' => $news_liste,
                        ]);

                        $template = self::getT() . '_page-subblock.tpl';
                        if ($value['template'] != '') {
                            $template = $value['template'];
                        }

                        $html_out .= $this->display(__FILE__, $template);
                    }
                }
            }
        }

        return $html_out;
    }

    public static function returnUniversalNewsListSubBlocks($value, $id_lang)
    {
        $date_fin = date('Y-m-d H:i:s');
        $liste = [];
        switch ((int) $value['select_type']) {
            case 1:
                $liste = NewsClass::getListe(
                    $id_lang,
                    1,
                    0,
                    0,
                    (int) $value['nb_list'],
                    'n.`date`',
                    'desc',
                    $value['use_date_start'] ? $value['date_start'] : null,
                    $value['use_date_stop'] ? $value['date_stop'] : $date_fin,
                    $value['blog_categories'],
                    1,
                    (int) $value['title_length'],
                    (int) $value['intro_length']
                );
                break;

            case 2:
                $liste = NewsClass::getListe(
                    $id_lang,
                    1,
                    0,
                    0,
                    (int) $value['nb_list'],
                    'n.`date`',
                    'asc',
                    $value['use_date_start'] ? $value['date_start'] : null,
                    $value['use_date_stop'] ? $value['date_stop'] : $date_fin,
                    $value['blog_categories'],
                    1,
                    (int) $value['title_length'],
                    (int) $value['intro_length']
                );
                break;

            case 3:
                $liste = NewsClass::getListe(
                    $id_lang,
                    1,
                    0,
                    0,
                    (int) $value['nb_list'],
                    '`count_comments`',
                    'desc',
                    $value['use_date_start'] ? $value['date_start'] : null,
                    $value['use_date_stop'] ? $value['date_stop'] : $date_fin,
                    $value['blog_categories'],
                    1,
                    (int) $value['title_length'],
                    (int) $value['intro_length']
                );
                break;

            case 4:
                $liste = NewsClass::getListe(
                    $id_lang,
                    1,
                    0,
                    0,
                    (int) $value['nb_list'],
                    '`count_comments`',
                    'asc',
                    $value['use_date_start'] ? $value['date_start'] : null,
                    $value['use_date_stop'] ? $value['date_stop'] : $date_fin,
                    $value['blog_categories'],
                    1,
                    (int) $value['title_length'],
                    (int) $value['intro_length']
                );
                break;

            case 5:
                $liste = NewsClass::getListe(
                    $id_lang,
                    1,
                    0,
                    0,
                    (int) $value['nb_list'],
                    'nl.`read`',
                    'desc',
                    $value['use_date_start'] ? $value['date_start'] : null,
                    $value['use_date_stop'] ? $value['date_stop'] : $date_fin,
                    $value['blog_categories'],
                    1,
                    (int) $value['title_length'],
                    (int) $value['intro_length']
                );
                break;

            case 6:
                $liste = NewsClass::getListe(
                    $id_lang,
                    1,
                    0,
                    0,
                    (int) $value['nb_list'],
                    'nl.`read`',
                    'asc',
                    $value['use_date_start'] ? $value['date_start'] : null,
                    $value['use_date_stop'] ? $value['date_stop'] : $date_fin,
                    $value['blog_categories'],
                    1,
                    (int) $value['title_length'],
                    (int) $value['intro_length']
                );
                break;

            default:
                $liste = [];
                break;
        }

        return $liste;
    }

    public function showSubBlocks($hook_name)
    {
        $html_out = '';
        $liste_subblocks = SubBlocksClass::getListe((int) $this->context->language->id, 1, $hook_name);

        if (count($liste_subblocks) > 0) {
            if (Configuration::get('prestablog_commentfb_actif')) {
                $this->context->controller->registerJavascript(
                    'modules-prestablog-facebook-count',
                    'modules/prestablog/views/js/facebook-count.js',
                    ['position' => 'bottom', 'priority' => 200]
                );
            }

            foreach ($liste_subblocks as $value) {
                $news_liste = self::returnUniversalNewsListSubBlocks($value, (int) $this->context->language->id);

                if (count($news_liste) > 0) {
                    if ($value['random']) {
                        shuffle($news_liste);
                    }

                    $this->context->smarty->assign([
                        'subblocks' => $value,
                        'news' => $news_liste,
                    ]);

                    $template = self::getT() . '_page-subblock.tpl';
                    if ($value['template'] != '') {
                        $template = $value['template'];
                    }

                    $html_out .= $this->display(__FILE__, $template);
                }
            }
        }

        return $html_out;
    }

    public function showdisplaySlider()
    {
        $layerslider = Module::getInstanceByName('layerslider');
        if ($layerslider) {
            return $this->display(__FILE__, self::getT() . '_displayTop.tpl');
        }
    }

    public function showRating()
    {
        return $this->display(__FILE__, self::getT() . '_displayRating.tpl');
    }

    public function showSlide()
    {
        if ($this->slideDatas()) {
            return $this->display(__FILE__, self::getT() . '_slide.tpl');
        }
    }

    public function slideDatas()
    {
        $liste = SliderClass::getAllSlider(
            (int) $this->context->shop->id,
            (int) $this->context->language->id
        );

        $slideImagePath = _PS_MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/slider/';
        $baseUri = __PS_BASE_URI__;

        foreach ($liste as &$slide) {
            $slide['webp_exists'] = file_exists($slideImagePath . $slide['id_slide'] . '.webp');
        }

        if (count($liste) > 0) {
            $this->context->smarty->assign([
                'ListeBlogNews' => $liste,
                'prestablog_theme_slide_upimg' => $baseUri . 'modules/prestablog/views/img/' . PrestaBlog::getT() . '/slider/',
            ]);

            $this->context->controller->registerJavascript(
                'modules-prestablog-slide-hook',
                $baseUri . 'modules/prestablog/views/js/slide.js',
                ['position' => 'bottom', 'priority' => 201]
            );

            return true;
        } else {
            return false;
        }
    }

    public function blocRss()
    {
        if (Configuration::get('prestablog_allnews_rss')) {
            return $this->display(__FILE__, self::getT() . '_bloc-rss.tpl');
        }
    }

    public function blocSearch()
    {
        if (Configuration::get('prestablog_blocsearch_actif')) {
            $this->context->smarty->assign([
                'prestablog_search_query' => trim(Tools::getValue('prestablog_search')),
            ]);

            return $this->display(__FILE__, self::getT() . '_bloc-search.tpl');
        }
    }

    public function hookDisplayLeftColumn()
    {
        $result = null;

        $sbl = json_decode(Configuration::get($this->name . '_sbl'), true);
        if (count($sbl) > 0) {
            foreach ($sbl as $vs) {
                if ($vs != '') {
                    $result .= $this->$vs();
                }
            }
        }

        return $result;
    }

    public function hookDisplayRightColumn()
    {
        $result = null;

        $sbr = json_decode(Configuration::get($this->name . '_sbr'), true);
        if (count($sbr) > 0) {
            foreach ($sbr as $vs) {
                if ($vs != '') {
                    $result .= $this->$vs();
                }
            }
        }

        return $result;
    }

    public function hookDisplayFooterProduct()
    {
        $liste_news_linked = NewsClass::getNewsProductLinkListe((int) Tools::getValue('id_product'), true);
        if (Configuration::get($this->name . '_producttab_actif') && count($liste_news_linked) > 0) {
            $returnliste = [];
            foreach ($liste_news_linked as $vnews) {
                $lang = (int) $this->context->language->id;
                $news = new NewsClass((int) $vnews);
                $lang_liste_news = json_decode($news->langues, true);

                if (in_array($lang, $lang_liste_news)) {
                    $paragraph = $paragraph_crop = $news->paragraph[$lang];

                    if ((Tools::strlen(trim($paragraph)) == 0)
                                                    && (Tools::strlen(trim(strip_tags($news->content[$lang]))) >= 1)) {
                        $paragraph_crop = trim(strip_tags($news->content[$lang]));
                    }

                    $imgexist = file_exists($this->module_path . '/views/img/' . self::getT() . '/up-img/' . $news->id . '.jpg');
                    $returnliste[(int) $vnews] = [
                        'id' => $news->id,
                        'url' => PrestaBlog::prestablogUrl([
                            'id' => $news->id,
                            'seo' => $news->link_rewrite[$lang],
                            'titre' => $news->title[$lang],
                        ]),
                        'title' => $news->title[$lang],
                        'paragraph_crop' => PrestaBlog::cleanCut(
                            $paragraph_crop,
                            (int) Configuration::get('prestablog_news_intro_length'),
                            ' [...]'
                        ),
                        'image_presente' => $imgexist,
                    ];
                }
            }
            if (count($returnliste) > 0) {
                $this->context->smarty->assign([
                    'listeNewsLinked' => $returnliste,
                ]);

                return $this->display(__FILE__, self::getT() . '_product-footer.tpl');
            }
        }
    }

    public function hookDisplayFooter()
    {
        if (Configuration::get($this->name . '_footlastnews_actif')) {
            $tri_champ = 'n.`date`';
            $tri_ordre = 'desc';
            $liste = NewsClass::getListe(
                (int) $this->context->language->id,
                1,
                0,
                0,
                (int) Configuration::get($this->name . '_footlastnews_limit'),
                $tri_champ,
                $tri_ordre,
                null,
                date('Y/m/d H:i:s'),
                null,
                1,
                (int) Configuration::get('prestablog_footer_title_length'),
                (int) Configuration::get('prestablog_footer_intro_length')
            );
            $showThumb = (int) Configuration::get($this->name . '_footlastnews_showthumb');

            $this->context->smarty->assign([
                'ListeBlocLastNews' => $liste,
                'footlastnews_showthumb' => $showThumb,
            ]);

            return $this->display(__FILE__, self::getT() . '_footer-lastliste.tpl');
        }
    }

    public function generateToken($add_text = null)
    {
        return md5($add_text . $this->module_key . _COOKIE_KEY_);
    }

    public function isPreviewMode($id_prestablog_news)
    {
        if (Tools::getValue('preview') == $this->generateToken($id_prestablog_news)) {
            return true;
        }

        return false;
    }

    public function hookModuleRoutes()
    {
        if (Configuration::get('prestablog_urlblog') == false) {
            $base_url_blog = 'blog';
        } else {
            $base_url_blog = Configuration::get('prestablog_urlblog');
        }

        $module_routes = [
            'prestablog-blog-root' => [
                'controller' => null,
                'rule' => '{controller}',
                'keywords' => [
                    'controller' => ['regexp' => $base_url_blog, 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
            'prestablog-blog-news' => [
                'controller' => null,
                'rule' => '{controller}/{urlnews}-n{n}',
                'keywords' => [
                    'urlnews' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'n' => ['regexp' => '[0-9]+', 'param' => 'id'],
                    'controller' => ['regexp' => $base_url_blog, 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
            'prestablog-blog-author' => [
                'controller' => null,
                'rule' => '{controller}/{urlnews}-au{au}',
                'keywords' => [
                    'urlnews' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'au' => ['regexp' => '[0-9]+', 'param' => 'au'],
                    'controller' => ['regexp' => $base_url_blog, 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
            'prestablog-blog-date' => [
                'controller' => null,
                'rule' => '{controller}/y{y}-m{m}',
                'keywords' => [
                    'y' => ['regexp' => '[0-9]{4}', 'param' => 'y'],
                    'm' => ['regexp' => '[0-9]+', 'param' => 'm'],
                    'controller' => ['regexp' => $base_url_blog, 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
            'prestablog-blog-date-pagignation' => [
                'controller' => null,
                'rule' => '{controller}/{start}p{p}y{y}-m{m}',
                'keywords' => [
                    'y' => ['regexp' => '[0-9]{4}', 'param' => 'y'],
                    'm' => ['regexp' => '[0-9]+', 'param' => 'm'],
                    'start' => ['regexp' => '[0-9]+', 'param' => 'start'],
                    'p' => ['regexp' => '[0-9]+', 'param' => 'p'],
                    'controller' => ['regexp' => $base_url_blog, 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
            'prestablog-blog-pagignation' => [
                'controller' => null,
                'rule' => '{controller}/{start}p{p}',
                'keywords' => [
                    'start' => ['regexp' => '[0-9]+', 'param' => 'start'],
                    'p' => ['regexp' => '[0-9]+', 'param' => 'p'],
                    'controller' => ['regexp' => $base_url_blog, 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
            'prestablog-blog-catpagination' => [
                'controller' => null,
                'rule' => '{controller}/{urlcat}-{start}p{p}-c{c}',
                'keywords' => [
                    'c' => ['regexp' => '[0-9]+', 'param' => 'c'],
                    'urlcat' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'start' => ['regexp' => '[0-9]+', 'param' => 'start'],
                    'p' => ['regexp' => '[0-9]+', 'param' => 'p'],
                    'controller' => ['regexp' => $base_url_blog, 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
            'prestablog-blog-cat' => [
                'controller' => null,
                'rule' => '{controller}/{urlcat}-c{c}',
                'keywords' => [
                    'c' => ['regexp' => '[0-9]+', 'param' => 'c'],
                    'urlcat' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'controller' => ['regexp' => $base_url_blog, 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
            'prestablog-rss-root' => [
                'controller' => null,
                'rule' => '{controller}',
                'keywords' => [
                    'controller' => ['regexp' => 'rss', 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
            'prestablog-blog-rss' => [
                'controller' => null,
                'rule' => '{controller}/{rss}',
                'keywords' => [
                    'rss' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'rss'],
                    'controller' => ['regexp' => 'rss', 'param' => 'controller'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'prestablog',
                ],
            ],
        ];

        return $module_routes;
    }

    public static function cleanCut($string, $length, $cut_string = '...')
    {
        $string = strip_tags($string);

        if (Tools::strlen($string) <= $length) {
            return $string;
        }

        if ($length <= Tools::strlen($cut_string)) {
            return Tools::substr($string, 0, $length);
        }

        $str = Tools::substr($string, 0, $length - Tools::strlen($cut_string) + 1);

        return Tools::substr($str, 0, strrpos($str, ' ')) . $cut_string;
    }

    public static function arrayDeleteValue($array, $search)
    {
        $temp = [];
        foreach ($array as $key => $value) {
            if ($value != $search) {
                $temp[$key] = $value;
            }
        }

        return $temp;
    }

    private function dirSize($directory)
    {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    private function rrmdir($directory)
    {
        foreach (glob($directory . '/*', GLOB_BRACE) as $file) {
            if (is_dir($file)) {
                $this->rrmdir($file);
            }
        }
    }

    private function rcopy($src, $dst)
    {
        $dir = opendir($src);
        self::makeDirectory($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->rcopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    self::copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public static function copy($source, $destination, $stream_context = null)
    {
        return Tools::copy($source, $destination, $stream_context);
    }

    public function genererMDP($longueur = 16)
    {
        $mdp = '';
        $possible = 'abcdfghijklmnopqrstuvwxyz012346789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueur_max = Tools::strlen($possible);
        if ($longueur > $longueur_max) {
            $longueur = $longueur_max;
        }
        $i = 0;
        while ($i < $longueur) {
            $mdp .= Tools::substr($possible, mt_rand(0, $longueur_max - 1), 1);
            ++$i;
        }

        return $mdp;
    }

    public static function objectToArray($object)
    {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }

        if (is_object($object)) {
            $object = get_object_vars($object);
        }

        return array_map(['PrestaBlog', 'objectToArray'], $object);
    }

    public static function createSqlFilterSearch($from_fields = [], $search = '', $nb_car_per_item = 3)
    {
        if ($search != '') {
            $filtre_sujet2 = '';
            $filtre_cumul = '%';

            $filtre_search = 'AND (' . "\n";

            foreach (preg_split('/ /', $search) as $value_keywords) {
                if (Tools::strlen($value_keywords) >= (int) $nb_car_per_item) {
                    $filtre_cumul .= $value_keywords . '%';
                    foreach ($from_fields as $field) {
                        $filtre_sujet2 .= 'OR ' . $field . ' LIKE \'%' . pSQL($value_keywords) . '%\'' . "\n";
                    }
                }
            }
            if (($filtre_cumul != '%') && (strpos($filtre_sujet2, $filtre_cumul) === false)) {
                foreach ($from_fields as $field) {
                    $filtre_sujet2 .= 'OR ' . $field . ' LIKE \'' . pSQL($filtre_cumul) . '\'' . "\n";
                }
            }
            $filtre_sujet2 = trim(ltrim($filtre_sujet2, 'OR'));

            if ($filtre_sujet2) {
                $filtre_search .= $filtre_sujet2 . ')' . "\n";
            }

            $filtre_search = trim(rtrim($filtre_search, 'AND (' . "\n"));
            // $filtre_search = trim(ltrim($filtre_search, 'AND'));
            $filtre_search = trim(ltrim($filtre_search, 'OR'));

            return $filtre_search;
        }

        return '';
    }

    public static function getContextShopDomain($http = false, $entities = false)
    {
        if (!$domain = ShopUrl::getMainShopDomain((int) Context::getContext()->shop->id)) {
            $domain = Tools::getHttpHost();
        }
        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $domain = 'http://' . $domain;
        }

        return $domain;
    }

    public static function getContextShopDomainSsl($http = false, $entities = false)
    {
        if (!$domain = ShopUrl::getMainShopDomainSSL((int) Context::getContext()->shop->id)) {
            $domain = Tools::getHttpHost();
        }
        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $domain = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $domain;
        }

        return $domain;
    }

    public static function getFiltreGroupes(
        $parent_field_join = 'c.`id_prestablog_categorie`',
        $parent_class = 'categorie')
    {
        $context = Context::getContext();

        /* Only on fron office */
        if (isset($context->employee->id) && (int) $context->employee->id > 0) {
            return '';
        } else {
            // Attempt to retrieve customer groups
            $groups = FrontController::getCurrentCustomerGroups();

            // If no customer groups found, use the default visitor group
            if (empty($groups)) {
                $groups = [(int) Configuration::get('PS_UNIDENTIFIED_GROUP')];
            }

            $filter = '
            AND EXISTS (
                SELECT 1
                FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_' . pSQL($parent_class) . '` pc
                JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_' . pSQL($parent_class) . '_group` pcg
                ON (
                    pc.id_prestablog_' . pSQL($parent_class) . ' = pcg.id_prestablog_' . pSQL($parent_class) . '
                    AND pcg.`id_group` IN (' . implode(',', $groups) . ')
                )
                WHERE ' . pSQL($parent_field_join) . ' = pc.`id_prestablog_categorie`
            )';

            return $filter;
        }
    }
}
