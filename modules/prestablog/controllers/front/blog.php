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
header('Content-type: text/html; charset=UTF-8');

include_once _PS_MODULE_DIR_ . 'prestablog/prestablog.php';
include_once _PS_MODULE_DIR_ . 'prestablog/class/news.php';
include_once _PS_MODULE_DIR_ . 'prestablog/class/categories.php';
include_once _PS_MODULE_DIR_ . 'prestablog/class/correspondancescategories.php';
include_once _PS_MODULE_DIR_ . 'prestablog/class/commentnews.php';
include_once _PS_MODULE_DIR_ . 'prestablog/class/antispam.php';
include_once _PS_MODULE_DIR_ . 'prestablog/class/displayslider.php';
include_once _PS_MODULE_DIR_ . 'prestablog/class/popup.php';

class PrestaBlogBlogModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    private $assign_page = 0;
    private $prestablog;

    private $news = [];
    private $news_count_all;
    private $path;
    private $pagination = [];
    private $config_theme;
    private $breadcrumb_links_perso = [];

    public function getTemplatePathFix($template)
    {
        return 'module:prestablog/views/templates/front/' . $template;
    }

    public function setMedia()
    {
        parent::setMedia();

        /* Help loading pictures for correct display */
        $this->addjqueryPlugin('imagesloaded.pkgd');
        $this->context->controller->registerJavascript(
            'modules-prestablog-imagesloaded.pkgd',
            'modules/prestablog/views/js/imagesloaded.pkgd.min.js',
            ['position' => 'bottom', 'priority' => 200]
        );

        /* Cascading grid layout library */
        $this->addjqueryPlugin('masonry-pkgd');
        $this->context->controller->registerJavascript(
            'modules-prestablog-masonry-pkgd',
            'modules/prestablog/views/js/masonry.pkgd.min.js',
            ['position' => 'bottom', 'priority' => 200]
        );

        /* Adding fancy link on article pictures */
        $this->addjqueryPlugin('fancybox');
        $this->context->controller->registerJavascript(
            'modules-prestablog-fancybox',
            'modules/prestablog/views/js/fancybox.js',
            ['position' => 'bottom', 'priority' => 200]
        );
    }

    public function canonicalRedirectionCustomController($canonical_url = '')
    {
        $match_url = '';
        if (Configuration::get('PS_SSL_ENABLED') && ($this->ssl || Configuration::get('PS_SSL_ENABLED_EVERYWHERE'))) {
            $match_url .= 'https://';
        } else {
            $match_url .= 'http://';
        }

        $match_url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $match_url = rawurldecode($match_url);

        if (!preg_match('/^' . Tools::pRegexp(rawurldecode($canonical_url), '/') . '([&?].*)?$/', $match_url)) {
            $redirect_type = '301';

            $redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';

            header('HTTP/1.0 ' . $redirect_type . ' Moved');
            header('Cache-Control: no-cache');
            Tools::redirectLink($canonical_url);
        }
    }

    public function getBreadcrumbLinks()
    {
        if (Configuration::get('prestablog_show_breadcrumb')) {
            $breadcrumb_links = parent::getBreadcrumbLinks();
            foreach ($this->breadcrumb_links_perso as $bclp) {
                $breadcrumb_links['links'][] = [
                    'title' => $bclp['title'],
                    'url' => $bclp['url'],
                ];
            }

            return $breadcrumb_links;
        } else {
            $breadcrumb_links['links'][] = [
                'title' => '',
                'url' => '',
            ];

            return $breadcrumb_links;
        }
    }

    public function init()
    {
        // ajout du lien blog en tout début de breadcrumb
        if (Configuration::get('prestablog_show_breadcrumb')) {
            $this->breadcrumb_links_perso[] = [
                'title' => $this->trans('Blog', [], 'Modules.Prestablog.Prestablog'),
                'url' => PrestaBlog::prestablogUrl([]),
            ];
        }
        $id_prestablog_news = null;
        parent::init();

        $this->prestablog = new PrestaBlog();

        /* assignPage (1 = 1 news page, 2 = news listes, 0 = rien, 3 = author) */

        $this->context->smarty->assign([
            'isLogged' => Context::getContext()->customer->isLogged(),
            'bloprestag_config' => Configuration::getMultiple(array_keys($this->prestablog->configurations)),
            'prestablog_popup' => PrestaBlog::getP(),
            'prestablog_theme' => PrestaBlog::getT(),
            'prestablog_theme_dir' => _MODULE_DIR_ . 'prestablog/views/',
            'prestablog_theme_dir_img' => _MODULE_DIR_ . 'prestablog/views/img/',
            'prestablog_root_url_path' => PrestaBlog::getPathRootForExternalLink(),
            'prestablog_theme_upimgnoslash' => 'modules/prestablog/views/img/' . PrestaBlog::getT() . '/up-img/',
            'md5pic' => md5(time()),
        ]);

        $this->context->smarty->assign([
            'prestablog_color' => NewsClass::getColorHome((int) $this->context->shop->id),
        ]);

        if (Tools::getValue('submitRating')) {
            $id_session = Context::getContext()->customer->id;
            $id_news = (int) Tools::getValue('id');
            $rate = (int) Tools::getValue('rate');
            echo "Prestablog::newsRatingID($id_news, $id_session);";
            Prestablog::newsRatingID($id_news, $id_session);
            Prestablog::newsRating($id_news, $rate);
        }
        if (Tools::getValue('au') && $author_id = (int) Tools::getValue('au')) {
            $this->assign_page = 3;
            $author = AuthorClass::getAuthorData($author_id);
            $author_name = isset($author['pseudo']) && $author['pseudo'] != ''
                           ? $author['pseudo']
                           : $author['firstname'] . ' ' . $author['lastname'];

            // add author name to breadcrumb
            if (Configuration::get('prestablog_show_breadcrumb')) {
                $this->breadcrumb_links_perso[] = [
                    'title' => $author_name,
                    'url' => PrestaBlog::prestablogUrl(['au' => $author_id, 'titre' => $author_name]),
                ];
            }
        } elseif (Tools::getValue('id') && $id_prestablog_news = (int) Tools::getValue('id')) {
            $this->assign_page = 1;
            $id_shop = (int) $this->context->shop->id;
            if (!$id_shop) {
                $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
            }
            $id_lang = (int) $this->context->cookie->id_lang;
            $id_prestablog_news = (int) Tools::getValue('id');

            $this->news = NewsClass::getNewsWithShopById($id_prestablog_news, $id_lang, $id_shop);

            if (!$this->news) {
                Tools::redirect('404.php');
            }

            $news_object = $this->news;

            $news_object->categories = CorrespondancesCategoriesClass::getCategoriesListeName(
                $news_object->id,
                $id_lang,
                1
            );

            if (!$this->prestablog->isPreviewMode($news_object->id)) {
                if (!$news_object->actif) {
                    Tools::redirect('404.php');
                }

                $listecat = CorrespondancesCategoriesClass::getCategoriesListe($news_object->id);
                if (!CategoriesClass::isCustomerPermissionGroups($listecat)) {
                    Tools::redirect('404.php');
                }

                if (!empty($news_object->url_redirect) && Validate::isAbsoluteUrl($news_object->url_redirect)) {
                    Tools::redirect($news_object->url_redirect);
                }

                if (!in_array($id_lang, json_decode($news_object->langues, true))) {
                    Tools::redirect(PrestaBlog::prestablogUrl([]));
                }
            }

            $branche_lapluslongue = '';
            if (count($news_object->categories)) {
                $categories_branches = [];
                foreach ($news_object->categories as $categorie_id) {
                    $categories_branches[] = CategoriesClass::getBranche($categorie_id['id_prestablog_categorie']);
                }
                asort($categories_branches);
                $branche_lapluslongue = $categories_branches[0];
                $branche_count = 0;
                foreach ($categories_branches as $branche) {
                    $branche_list = preg_split('/\./', $branche);
                    if (count($branche_list) > $branche_count) {
                        $branche_lapluslongue = $branche;
                        $branche_count = count($branche_list);
                    }
                }
            }

            foreach (CategoriesClass::getBreadcrumb($branche_lapluslongue) as $cat_branche) {
                $this->breadcrumb_links_perso[] = $cat_branche;
            }

            if (Configuration::get('prestablog_show_breadcrumb')) {
                $this->breadcrumb_links_perso[] = [
                    'title' => $news_object->title,
                    'url' => PrestaBlog::prestablogUrl([
                        'id' => $news_object->id,
                        'seo' => $news_object->link_rewrite,
                        'titre' => $news_object->title,
                    ]),
                ];
            }
        } elseif (Tools::getValue('a') && Configuration::get('prestablog_comment_subscription')) {
            if (!Context::getContext()->customer->isLogged()) {
                $urlauth = urlencode('index.php?fc=module&module=prestablog&controller=blog&a=' . Tools::getValue('a'));
                Tools::redirect('index.php?controller=authentication&back=' . $urlauth);
            }

            $this->news = new NewsClass((int) Tools::getValue('a'), (int) $this->context->cookie->id_lang);

            if ($this->news->actif) {
                CommentNewsClass::insertCommentAbo((int) $this->news->id, (int) $this->context->cookie->id_customer);
            }

            Tools::redirect(PrestaBlog::prestablogUrl([
                'id' => $this->news->id,
                'seo' => $this->news->link_rewrite,
                'titre' => $this->news->title,
            ]));
        } elseif (Tools::getValue('d') && Configuration::get('prestablog_comment_subscription')) {
            if (Context::getContext()->customer->isLogged()) {
                $this->news = new NewsClass((int) Tools::getValue('d'), (int) $this->context->cookie->id_lang);
                if ($this->news->actif) {
                    CommentNewsClass::deleteCommentAbo((int) $this->news->id, (int) $this->context->cookie->id_customer);
                }
            }

            Tools::redirect(PrestaBlog::prestablogUrl([
                'id' => $this->news->id,
                'seo' => $this->news->link_rewrite,
                'titre' => $this->news->title,
            ]));
        } else {
            $this->assign_page = 2;
            $categorie = null;
            $year = null;
            $month = null;

            if (Tools::getValue('c')) {
                if (!CategoriesClass::isCustomerPermissionGroups([(int) Tools::getValue('c')])) {
                    Tools::redirect('404.php');
                }

                $categorie = new CategoriesClass((int) Tools::getValue('c'), (int) $this->context->cookie->id_lang);

                $breadcrumbcat = CategoriesClass::getBreadcrumb(CategoriesClass::getBranche((int) $categorie->id));

                foreach ($breadcrumbcat as $cat_branche) {
                    $this->breadcrumb_links_perso[] = $cat_branche;
                }

                $lrw = ($categorie->link_rewrite != '' ? $categorie->link_rewrite : $categorie->title);
                $this->context->smarty->assign([
                    'prestablog_categorie' => $categorie->id,
                    'prestablog_categorie_name' => $categorie->title,
                    'prestablog_categorie_link_rewrite' => $lrw,
                ]);
            } else {
                $this->context->smarty->assign([
                    'prestablog_categorie' => null,
                    'prestablog_categorie_name' => null,
                    'prestablog_categorie_link_rewrite' => null,
                ]);
            }

            if (Configuration::get('prestablog_show_breadcrumb')) {
                if (trim(Tools::getValue('prestablog_search'))) {
                    $this->breadcrumb_links_perso[] = [
                        'title' => sprintf(
                            $this->trans('Search %1$s in the blog', [], 'Modules.Prestablog.Blog'),
                            '"' . trim(Tools::getValue('prestablog_search')) . '"'
                        ),
                        'url' => '#',
                    ];
                }
            }

            if (Tools::getValue('y')) {
                $year = Tools::getValue('y');
                if (Configuration::get('prestablog_show_breadcrumb')) {
                    $this->breadcrumb_links_perso[] = [
                        'title' => $year,
                        'url' => '#',
                    ];
                }
            }

            if (Tools::getValue('m')) {
                $month = Tools::getValue('m');
                if (Configuration::get('prestablog_show_breadcrumb')) {
                    $this->breadcrumb_links_perso[] = [
                        'title' => $this->prestablog->mois_langue[$month],
                        'url' => PrestaBlog::prestablogUrl([
                            'y' => $year,
                            'm' => $month,
                        ]),
                    ];
                }
            }

            if (Tools::getValue('p')) {
                if (Configuration::get('prestablog_show_breadcrumb')) {
                    $this->breadcrumb_links_perso[] = [
                        'title' => $this->trans('Page', [], 'Modules.Prestablog.Blog') . ' ' . Tools::getValue('p'),
                        'url' => '#',
                    ];
                }
            }

            $this->context->smarty->assign([
                'prestablog_month' => $month,
                'prestablog_year' => $year,
            ]);

            if (Tools::getValue('m') && Tools::getValue('y')) {
                $date_debut = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, +1, $year));
                $date_fin = date('Y-m-d H:i:s', mktime(0, 0, 0, $month + 1, +1, $year));
                if ($date_fin > date('Y-m-d H:i:s')) {
                    $date_fin = date('Y-m-d H:i:s');
                }
            } else {
                $date_debut = null;
                $date_fin = date('Y-m-d H:i:s');
            }

            $categories_filtre = null;

            if (isset($categorie->id)) {
                $categories_filtre = (int) $categorie->id;
            } elseif (Tools::getValue('prestablog_search_array_cat')) {
                $categories_filtre = Tools::getValue('prestablog_search_array_cat');
            }

            $this->news_count_all = NewsClass::getCountListeAll(
                (int) $this->context->cookie->id_lang,
                1,
                0,
                $date_debut,
                $date_fin,
                $categories_filtre,
                1,
                Tools::getValue('prestablog_search')
            );

            $this->news = NewsClass::getListe(
                (int) $this->context->cookie->id_lang,
                1,
                0,
                (int) Tools::getValue('start'),
                (int) Configuration::get('prestablog_nb_liste_page'),
                'n.`date`',
                'desc',
                $date_debut,
                $date_fin,
                $categories_filtre,
                1,
                (int) Configuration::get('prestablog_news_title_length'),
                (int) Configuration::get('prestablog_news_intro_length'),
                Tools::getValue('prestablog_search')
            );

            /*
            * fix for redirect if news haven't got any news for the
            * current language and current start page on category list
            */

            if ((int) $this->news_count_all > 0 && count($this->news) == 0) {
                Tools::redirect(PrestaBlog::prestablogUrl([
                    'c' => (int) $categorie->id,
                    'titre' => $categorie->title,
                ]));
            }
            if ((int) $this->news_count_all == 0 && Tools::getValue('p')) {
                Tools::redirect(PrestaBlog::prestablogUrl([]));
            }
        }

        if ($this->assign_page == 1) {
            $this->context->smarty->assign(PrestaBlog::getPrestaBlogMetaTagsNewsOnly(
                (int) $this->context->cookie->id_lang,
                (int) Tools::getValue('id')
            ));
        } elseif ($this->assign_page == 2 && Tools::getValue('c')) {
            $this->context->smarty->assign(PrestaBlog::getPrestaBlogMetaTagsNewsCat(
                (int) $this->context->cookie->id_lang,
                (int) Tools::getValue('c')
            ));
        } elseif ($this->assign_page == 2 && (Tools::getValue('y') || Tools::getValue('m'))) {
            $this->context->smarty->assign(PrestaBlog::getPrestaBlogMetaTagsNewsDate());
        } elseif ($this->assign_page == 3 && Tools::getValue('au')) {
            $pseudo = AuthorClass::getPseudo(Tools::getValue('au'));
            $bio = AuthorClass::getBio(Tools::getValue('au'));
            $meta_title = AuthorClass::getMetaTitle(Tools::getValue('au'));
            $meta_description = AuthorClass::getMetaDescription(Tools::getValue('au'));

            $this->context->smarty->assign([
                'title' => $this->trans('Author', [], 'Modules.Prestablog.Blog') . ' : ' . $pseudo,
                'meta_title' => $meta_title,
                'meta_description' => html_entity_decode($meta_description),
                'meta_keywords' => '',
            ]);
        } else {
            $this->context->smarty->assign(PrestaBlog::getPrestaBlogMetaTagsPage(
                (int) $this->context->cookie->id_lang
            ));
        }

        if (!$this->prestablog->isPreviewMode((int) $id_prestablog_news)) {
            $this->gestionRedirectionCanonical((int) $this->assign_page);
        }
        /* URLS SEO */

        /* Cas 1 : article/news */
        if ($this->assign_page == 1) {
            $this->context->smarty->assign(['prestablog_page_type' => 'article']);
            $news = new NewsClass((int) Tools::getValue('id'));
            $langues_seo = json_decode($news->langues, true);
            $urls_redirect = [];
            $language_urls_redirect = []; // Nouvelle variable pour stocker les codes de langue

            foreach ($langues_seo as $langue => $value) {
                if (!empty($value)) {
                    $language = new Language($value);
                    $iso_code = $language->iso_code;
                    $language_code = isset($language->language_code) ? $language->language_code : $iso_code; // Utiliser le code ISO si le code de langue n'existe pas
                    $url = PrestaBlog::prestablogURL([
                        'id' => $news->id,
                        'seo' => $news->link_rewrite[$value],
                        'titre' => $news->title[$value],
                        'id_lang' => $value,
                    ]);
                    if (!empty($url)) {
                        $urls_redirect[$iso_code] = $url;
                        $language_urls_redirect[$language_code] = $url; // Ajout des codes de langue
                    }
                }
            }
            $this->context->smarty->assign(['urls_redirect' => $urls_redirect, 'language_urls_redirect' => $language_urls_redirect]);
        }
        /* Cas 2 : Catégorie d'articles */
        if ($this->assign_page == 2) {
            $this->context->smarty->assign(['prestablog_page_type' => 'categorie']);
            $categories = new CategoriesClass((int) Tools::getValue('c'));
            $langues_seo = $categories->link_rewrite;
            $urls_redirect = [];
            $language_urls_redirect = []; // Nouvelle variable pour stocker les codes de langue

            if ($langues_seo && (is_array($langues_seo) || is_object($langues_seo))) {
                foreach ($langues_seo as $langue => $value) {
                    if (!empty($value)) {
                        $language = new Language($langue);
                        $iso_code = $language->iso_code;
                        $language_code = isset($language->language_code) ? $language->language_code : $iso_code; // Utiliser le code ISO si le code de langue n'existe pas
                        $url = PrestaBlog::prestablogURL([
                            'c' => $categories->id,
                            'titre' => $value,
                            'id_lang' => $langue,
                        ]);
                        if (!empty($url)) {
                            $urls_redirect[$iso_code] = $url;
                            $language_urls_redirect[$language_code] = $url; // Ajout des codes de langue
                        }
                    }
                }
            } else {
                foreach (Language::getLanguages(true) as $langue) {
                    $iso_code = $langue['iso_code'];
                    $language_code = isset($langue['language_code']) ? $langue['language_code'] : $iso_code; // Utiliser le code ISO si le code de langue n'existe pas
                    $url = PrestaBlog::getBaseUrlFront($langue['id_lang'])
                        . (Configuration::get('prestablog_urlblog') == false ? 'blog' : Configuration::get('prestablog_urlblog'));
                    if (!empty($url)) {
                        $urls_redirect[$iso_code] = $url;
                        $language_urls_redirect[$language_code] = $url; // Ajout des codes de langue
                    }
                }
            }
            $this->context->smarty->assign(['urls_redirect' => $urls_redirect, 'language_urls_redirect' => $language_urls_redirect]);
        }
    }

    private function getLanguageFromId($id)
    {
        $languages = Language::getLanguages(true);
        foreach ($languages as $value) {
            if ($id == $value['id_lang']) {
                return $value;
            }
        }
    }

    private function gestionRedirectionCanonical($assign_page)
    {
        switch ($assign_page) {
            case 1:
                $news = new NewsClass((int) Tools::getValue('id'), (int) $this->context->cookie->id_lang);
                if (!Tools::getValue('submitComment')) {
                    $this->canonicalRedirectionCustomController(PrestaBlog::prestablogUrl([
                        'id' => $news->id,
                        'seo' => $news->link_rewrite,
                        'titre' => $news->title,
                    ]));
                }

                break;

            case 2:
                if (Tools::getValue('start')
                    && Tools::getValue('p')
                    && !Tools::getValue('c')
                    && !Tools::getValue('m')
                    && !Tools::getValue('y')
                ) {
                    $this->canonicalRedirectionCustomController(PrestaBlog::prestablogUrl([
                        'start' => (int) Tools::getValue('start'),
                        'p' => (int) Tools::getValue('p'),
                    ]));
                }
                if (Tools::getValue('c') && !Tools::getValue('start') && !Tools::getValue('p')) {
                    $categorie = new CategoriesClass((int) Tools::getValue('c'), (int) $this->context->cookie->id_lang);
                    $cat_link_rewrite = $categorie->link_rewrite;
                    if ($categorie->link_rewrite == '') {
                        $cat_link_rewrite = CategoriesClass::getCategoriesName(
                            (int) $this->context->cookie->id_lang,
                            (int) Tools::getValue('c')
                        );
                    }
                    $this->canonicalRedirectionCustomController(PrestaBlog::prestablogUrl([
                        'c' => $categorie->id,
                        'categorie' => $cat_link_rewrite,
                    ]));
                }
                if (Tools::getValue('c') && Tools::getValue('start') && Tools::getValue('p')) {
                    $categorie = new CategoriesClass((int) Tools::getValue('c'), (int) $this->context->cookie->id_lang);
                    $cat_link_rewrite = $categorie->link_rewrite;
                    if ($categorie->link_rewrite == '') {
                        $cat_link_rewrite = CategoriesClass::getCategoriesName(
                            (int) $this->context->cookie->id_lang,
                            (int) Tools::getValue('c')
                        );
                    }
                    $this->canonicalRedirectionCustomController(PrestaBlog::prestablogUrl([
                        'c' => $categorie->id,
                        'start' => (int) Tools::getValue('start'),
                        'p' => (int) Tools::getValue('p'),
                        'categorie' => $cat_link_rewrite,
                    ]));
                }
                if (Tools::getValue('m')
                    && Tools::getValue('y')
                    && !Tools::getValue('start')
                    && !Tools::getValue('p')
                ) {
                    $this->canonicalRedirectionCustomController(PrestaBlog::prestablogUrl([
                        'y' => (int) Tools::getValue('y'),
                        'm' => (int) Tools::getValue('m'),
                    ]));
                }
                if (Tools::getValue('m') && Tools::getValue('y') && Tools::getValue('start') && Tools::getValue('p')) {
                    $this->canonicalRedirectionCustomController(PrestaBlog::prestablogUrl([
                        'y' => (int) Tools::getValue('y'),
                        'm' => (int) Tools::getValue('m'),
                        'start' => (int) Tools::getValue('start'),
                        'p' => (int) Tools::getValue('p'),
                    ]));
                }
                if (!Tools::getValue('m')
                    && !Tools::getValue('y')
                    && !Tools::getValue('c')
                    && !Tools::getValue('start')
                    && !Tools::getValue('p')
                ) {
                    $title_h1_index = trim(Configuration::get(
                        'prestablog_h1pageblog',
                        (int) $this->context->cookie->id_lang
                    ));
                    if ($title_h1_index != '') {
                        $this->context->smarty->assign('prestablog_title_h1', $title_h1_index);
                    }

                    $this->canonicalRedirectionCustomController(PrestaBlog::prestablogUrl([]));
                }
                break;
        }
    }

    public function initContent()
    {
        parent::initContent();

        // / Menu cat
        if ($this->assign_page == 1 && Configuration::get('prestablog_menu_cat_blog_article')) {
            $this->voirListeCatMenu();
        }
        // ne pas afficher le menu cat sur la page search
        if ($this->assign_page == 2 && !trim(Tools::getValue('prestablog_search'))) {
            if (Configuration::get('prestablog_menu_cat_blog_index')
                && !Tools::getValue('c')
                && !Tools::getValue('y')
                && !Tools::getValue('m')
                && !Tools::getValue('p')
            ) {
                $this->voirListeCatMenu();
            } elseif (Configuration::get('prestablog_menu_cat_blog_list')
                && (Tools::getValue('c')
                    || Tools::getValue('y')
                    || Tools::getValue('m')
                    || Tools::getValue('p')
                )
            ) {
                $this->voirListeCatMenu();
            }
        }

        // Menu cat //

        // Search
        if ($this->assign_page == 2
            && trim(Tools::getValue('prestablog_search'))
            && Configuration::get('prestablog_search_filtrecat')
        ) {
            $this->voirFiltrageSearch();
        }
        // Search

        // Author
        if (Tools::getValue('au') && $this->assign_page == 3) {
            // Afficher le menu si la configuration est activée
            if (Configuration::get('prestablog_menu_cat_blog_article')) {
                $this->voirListeCatMenu();
            }

            $articles_author = AuthorClass::getArticleListe((int) Tools::getValue('au'), true, 0, (int) Configuration::get('prestablog_author_news_number'));

            if (count($articles_author) > 0) {
                foreach ($articles_author as $article_author) {
                    $get_cat_liste = CorrespondancesCategoriesClass::getCategoriesListe((int) $article_author);
                    $article = new NewsClass((int) $article_author, (int) $this->context->cookie->id_lang);
                    if (file_exists(
                        _PS_MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/up-img/' . $article->id . '.jpg'
                    )) {
                        $article->image_presente = true;
                    } else {
                        $article->image_presente = false;
                    }

                    $this->articles_author[$article_author] = [
                        'title' => $article->title,
                        'date' => $article->date,
                        'image_presente' => $article->image_presente,
                        'link' => PrestaBlog::prestablogUrl([
                            'id' => $article->id,
                            'seo' => $article->link_rewrite,
                            'titre' => $article->title,
                        ]),
                    ];
                }
            }
            $author = AuthorClass::getAuthorData((int) Tools::getValue('au'));
            $author_image_path = _PS_ROOT_DIR_ . '/modules/prestablog/views/img/' . PrestaBlog::getT() . '/author_th/' . (int) Tools::getValue('au') . '.jpg';
            $author_image_exists = file_exists($author_image_path);

            $author_name = isset($author['pseudo']) && $author['pseudo'] != ''
                           ? $author['pseudo']
                           : trim($author['firstname'] . ' ' . $author['lastname']);

            $author['paragraph_author_crop'] = $author['bio'];

            if (Tools::strlen(trim(strip_tags($author['bio']))) >= 1) {
                $author['paragraph_author_crop'] = html_entity_decode($author['bio']);
                $author['bio'] = html_entity_decode($author['bio']);
            }

            if (Tools::strlen(trim($author['paragraph_author_crop'])) > (int) Configuration::get('prestablog_news_intro_length')) {
                $author['paragraph_author_crop'] = PrestaBlog::cleanCut(
                    $author['paragraph_author_crop'],
                    (int) Configuration::get('prestablog_news_intro_length'),
                    ' [...]'
                );
            }

            $this->context->smarty->assign([
                'author_id' => (int) Tools::getValue('au'),
                'author_image_exists' => $author_image_exists,
                'firstname' => $author['firstname'],
                'lastname' => $author['lastname'],
                'pseudo' => $author['pseudo'],
                'email' => $author['email'],
                'biography' => $author['bio'],
                'bio_crop' => $author['paragraph_author_crop'],
                'prestablog_author_upimg' => _MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/author_th/',
                'md5pic' => md5(time()),
                'articles_author' => $this->articles_author,
            ]);

            $this->context->smarty->assign([
                'tpl_aut' => $this->context->smarty->fetch(
                    $this->getTemplatePathFix(PrestaBlog::getT() . '_page-unique-author.tpl')
                ),
            ]);
        }
        // Author //

        if ($this->assign_page == 1) {
            // liaison produits
            $products_liaison = NewsClass::getProductLinkListe((int) $this->news->id, true);

            if (count($products_liaison) > 0) {
                foreach ($products_liaison as $product_link) {
                    $product = new Product((int) $product_link, false, (int) $this->context->cookie->id_lang);
                    $product_cover = Image::getCover($product->id);
                    $image_product = new Image((int) $product_cover['id_image']);
                    $image_thumb_path = ImageManager::thumbnail(
                        _PS_IMG_DIR_ . 'p/' . $image_product->getExistingImgPath() . '.jpg',
                        'product_blog_mini_2_' . (int) $product_cover['id_image'] . '.jpg',
                        (int) Configuration::get('prestablog_thumb_linkprod_width'),
                        'jpg',
                        true,
                        true
                    );

                    if ($image_thumb_path == '') {
                        $image_presente = false;
                    } else {
                        $image_presente = true;
                    }
                    $product_price = $product->getPrice(true, null, 2); // Price with rediction if applicable
                    $product_regular_price = $product->getPriceWithoutReduct(false, null); // Prix without reduction

                    $this->news->products_liaison[$product_link] = [
                        'name' => $product->name,
                        'description_short' => $product->description_short,
                        'thumb' => $image_thumb_path,
                        'img_empty' => _PS_MODULE_DIR_ . 'prestablog/views/img/product_link_white.jpg',
                        'image_presente' => $image_presente,
                        'link' => $product->getLink($this->context),
                        'price' => Tools::displayPrice($product_price),
                        'regular_price' => Tools::displayPrice($product_regular_price),
                        'on_sale' => ($product_price < $product_regular_price),
                    ];
                }
            }
            // /liaison produits

            // liaison articles
            $articles_liaison = NewsClass::getArticleLinkListe((int) $this->news->id, true);
            $prefixe = 'thumb_';

            if (count($articles_liaison) > 0) {
                foreach ($articles_liaison as $article_liaison) {
                    $get_cat_liste = CorrespondancesCategoriesClass::getCategoriesListe((int) $article_liaison);
                    if (CategoriesClass::isCustomerPermissionGroups($get_cat_liste)) {
                        $article = new NewsClass((int) $article_liaison, (int) $this->context->cookie->id_lang);
                        $prefixe = 'thumb_';
                        $jpegPath = PrestaBlog::imgUpPath() . '/' . $prefixe . $article->id . '.jpg';
                        $webpPath = PrestaBlog::imgUpPath() . '/' . $prefixe . $article->id . '.webp';

                        $article->image_presente = file_exists($jpegPath) ? true : false;
                        $article->webp_present = file_exists($webpPath) ? true : false;

                        $this->news->articles_liaison[$article_liaison] = [
                            'title' => $article->title,
                            'date' => $article->date,
                            'image_presente' => $article->image_presente,
                            'webp_present' => $article->webp_present,
                            'link' => PrestaBlog::prestablogUrl([
                                'id' => $article->id,
                                'seo' => $article->link_rewrite,
                                'titre' => $article->title,
                            ]),
                        ];
                    }
                }
            }
            $id_session = Context::getContext()->customer->id;
            $check = NewsClass::checkrate((int) Tools::getValue('id'), $id_session);

            $validate = 'true';
            $notvalidate = 'false';
            if ($check == true) {
                $this->context->smarty->assign([
                    'validate' => $validate,
                ]);
            } else {
                $this->context->smarty->assign([
                    'validate' => $notvalidate,
                ]);
            }

            // /liaison articles
            $popup_liaison = NewsClass::getPopupLink((int) $this->news->id);
            $id_cookie = (int) $this->context->cookie->id_lang;

            if (isset($popup_liaison) && $popup_liaison != 0 && $this->news->actif_popup = '1') {
                $popup = new PopupClass($popup_liaison, $id_cookie);

                $this->prestablog->displayPopup($id_cookie, $popup_liaison);
            }

            $prestablog_current_url = PrestaBlog::prestablogUrl([
                'id' => $this->news->id,
                'seo' => $this->news->link_rewrite,
                'titre' => $this->news->title,
            ]);
            /* Introducte WebP Condition */
            // Define the prefix for the image files
            $prefixe = 'thumb_';

            // Check if the JPEG image exists with the prefix
            $jpegPath = PrestaBlog::imgUpPath() . '/' . $prefixe . $this->news->id . '.jpg';
            $jpegExists = file_exists($jpegPath);

            // Check if the WebP image exists with the same prefix
            $webpPath = PrestaBlog::imgUpPath() . '/' . $prefixe . $this->news->id . '.webp';
            $webpExists = file_exists($webpPath);

            // If the JPEG image exists, pass its path and the WebP path (if available) to Smarty
            if ($jpegExists) {
                $this->context->smarty->assign('news_Image', [
                    'jpeg' => 'modules/prestablog/views/img/' . PrestaBlog::getT() . '/up-img/' . $prefixe . $this->news->id . '.jpg',
                    'webp' => $webpExists ? 'modules/prestablog/views/img/' . PrestaBlog::getT() . '/up-img/' . $prefixe . $this->news->id . '.webp' : null,
                    'webp_present' => $webpExists ? 1 : 0, // Indicates if the WebP version is available
                ]);
            }

            if (file_exists(PrestaBlog::imgAuthorUpPath() . '/' . $this->news->author_id . '.jpg')) {
                $this->context->smarty->assign(
                    'author_Avatar',
                    'modules/prestablog/views/img/' . PrestaBlog::getT() . '/author_th/' . $this->news->author_id . '.jpg'
                );
            }
            $this->context->smarty->assign([
                'LinkReal' => PrestaBlog::getBaseUrlFront() . '?fc=module&module=prestablog&controller=blog',
                'news' => $this->news,
                'prestablog_current_url' => $prestablog_current_url,
            ]);

            // INCREMENT NEWS READ
            if (!$this->context->cookie->__isset('prestablog_news_read_' . (int) $this->context->cookie->id_lang)) {
                $this->news->incrementRead((int) $this->news->id, (int) $this->context->cookie->id_lang);
                $this->context->cookie->__set(
                    'prestablog_news_read_' . (int) $this->context->cookie->id_lang,
                    json_encode([(int) $this->news->id])
                );
            } else {
                $cookie_read_lang = 'prestablog_news_read_' . (int) $this->context->cookie->id_lang;
                $array_news_readed = json_decode($this->context->cookie->__get($cookie_read_lang), true);
                if (!in_array((int) $this->news->id, $array_news_readed)) {
                    $array_news_readed[] = (int) $this->news->id;
                    $this->news->incrementRead((int) $this->news->id, (int) $this->context->cookie->id_lang);
                    $this->context->cookie->__set(
                        'prestablog_news_read_' . (int) $this->context->cookie->id_lang,
                        json_encode($array_news_readed)
                    );
                }
            }
            // /INCREMENT NEWS READ
            $author = AuthorClass::getAuthorName((int) $this->news->id);

            if (isset($author) && $author != '') {
                $author['paragraph_author_crop'] = $author['bio'];
            }
            if (isset($author['bio'])) {
                if (Tools::strlen(trim(strip_tags($author['bio']))) >= 1) {
                    $author['paragraph_author_crop'] = html_entity_decode($author['bio']);
                }

                if (Tools::strlen(trim($author['paragraph_author_crop'])) > (int) Configuration::get('prestablog_author_intro_length')) {
                    $author['paragraph_author_crop'] = PrestaBlog::cleanCut(
                        $author['paragraph_author_crop'],
                        (int) Configuration::get('prestablog_author_intro_length'),
                        ' [...]'
                    );
                }
            }
            if ($author == '') {
                $this->context->smarty->assign([
                    'author_firstname' => '',
                    'author_lastname' => '',
                    'author_pseudo' => '',
                    'author_bio' => '',
                    'author_bio_crop' => '',
                    'prestablog_author_upimg' => _MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/author_th/',
                    'id_author' => '',
                ]);
            } else {
                $author_image_path = _PS_ROOT_DIR_ . '/modules/prestablog/views/img/' . PrestaBlog::getT() . '/author_th/' . (int) $this->news->author_id . '.jpg';
                $author_image_exists = file_exists($author_image_path);

                $this->context->smarty->assign([
                    'author_firstname' => $author['firstname'],
                    'author_lastname' => $author['lastname'],
                    'author_pseudo' => $author['pseudo'],
                    'author_bio' => $author['bio'],
                    'author_bio_crop' => $author['paragraph_author_crop'],
                    'prestablog_author_upimg' => _MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/author_th/',
                    'id_author' => (int) $this->news->author_id,
                    'author_image_exists' => $author_image_exists,
                ]);
            }
            $this->context->smarty->assign([
                'tpl_page-title' => $this->context->smarty->fetch(
                    $this->getTemplatePathFix(PrestaBlog::getT() . '_page-title.tpl')
                ),
            ]);
            $this->context->smarty->assign([
                'tpl_title' => $this->context->smarty->fetch(
                    $this->getTemplatePathFix(PrestaBlog::getT() . '_page-title.tpl')
                ),
            ]);
            $this->context->smarty->assign([
                'tpl_unique' => $this->context->smarty->fetch(
                    $this->getTemplatePathFix(PrestaBlog::getT() . '_page-unique.tpl')
                ),
            ]);
            $this->context->smarty->assign([
                'tpl_extra' => $this->context->smarty->fetch(
                    $this->getTemplatePathFix(PrestaBlog::getT() . '_page-extra.tpl')
                ),
            ]);

            if ($this->prestablog->gestComment($this->news->id)) {
                if (Configuration::get('prestablog_antispam_actif')) {
                    $anti_spam_load = $this->prestablog->gestAntiSpam();

                    if ($anti_spam_load != false) {
                        $this->context->smarty->assign([
                            'AntiSpam' => $anti_spam_load,
                        ]);
                    }
                }
                $this->context->smarty->assign([
                    'Is_Subscribe' => in_array(
                        $this->context->cookie->id_customer,
                        CommentNewsClass::listeCommentAbo($this->news->id)
                    ),
                ]);

                $this->context->controller->registerJavascript(
                    'modules-prestablog-comments',
                    'modules/prestablog/views/js/comments.js',
                    ['position' => 'bottom', 'priority' => 200]
                );

                $this->context->smarty->assign([
                    'tpl_comment' => $this->context->smarty->fetch(
                        $this->getTemplatePathFix(PrestaBlog::getT() . '_page-comment.tpl')
                    ),
                ]);
            }
            if (Configuration::get('prestablog_commentfb_actif')) {
                $iso_code = $this->context->language->iso_code;
                $this->context->smarty->assign([
                    'fb_comments_url' => $prestablog_current_url,
                    'fb_comments_nombre' => (int) Configuration::get('prestablog_commentfb_nombre'),
                    'fb_comments_apiId' => Configuration::get('prestablog_commentfb_apiId'),
                    'fb_comments_iso' => Tools::strtolower($iso_code) . '_' . Tools::strtoupper($iso_code),
                ]);

                $this->context->controller->registerJavascript(
                    'modules-prestablog-facebook',
                    'modules/prestablog/views/js/facebook.js',
                    ['position' => 'bottom', 'priority' => 200]
                );

                $this->context->smarty->assign([
                    'tpl_comment_fb' => $this->context->smarty->fetch(
                        $this->getTemplatePathFix(PrestaBlog::getT() . '_page-comment-fb.tpl')
                    ),
                ]);
            }
        } elseif ($this->assign_page == 2 && !trim(Tools::getValue('prestablog_search'))) {
            if (Configuration::get('prestablog_pageslide_actif')
                && !Tools::getValue('c')
                && !Tools::getValue('y')
                && !Tools::getValue('m')
                && !Tools::getValue('p')
            ) {
                if ($this->prestablog->slideDatas()) {
                    $this->context->smarty->assign([
                        'tpl_slide' => $this->context->smarty->fetch(
                            $this->getTemplatePathFix(PrestaBlog::getT() . '_slide.tpl')
                        ),
                    ]);
                }
            }
            if ((
                Configuration::get('prestablog_view_cat_desc')
                || Configuration::get('prestablog_view_cat_thumb')
                || Configuration::get('prestablog_view_cat_img')
            )
            && Tools::getValue('c')
            && !Tools::getValue('y')
            && !Tools::getValue('m')
            && !Tools::getValue('p')
            ) {
                $obj_categorie = new CategoriesClass((int) Tools::getValue('c'), (int) $this->context->cookie->id_lang);

                if (file_exists(_PS_MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/up-img/c/' . $obj_categorie->id . '.jpg')) {
                    $obj_categorie->image_presente = true;
                } else {
                    $obj_categorie->image_presente = false;
                }

                $thumb_config = Configuration::get('prestablog_view_cat_thumb');
                $img_config = Configuration::get('prestablog_view_cat_img');

                if ($img_config == 1) {
                    if (file_exists(_PS_MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/up-img/c/full_' . $obj_categorie->id . '.webp')) {
                        $obj_categorie->webp_present = true;
                    } else {
                        $obj_categorie->webp_present = false;
                    }
                } elseif ($thumb_config == 1) {
                    if (file_exists(_PS_MODULE_DIR_ . 'prestablog/views/img/' . PrestaBlog::getT() . '/up-img/c/thumb_' . $obj_categorie->id . '.webp')) {
                        $obj_categorie->webp_present = true;
                    } else {
                        $obj_categorie->webp_present = false;
                    }
                }

                $this->context->smarty->assign([
                    'prestablog_categorie_obj' => $obj_categorie,
                ]);

                $obj_categorie->descri = $obj_categorie->description;
                $this->context->smarty->assign([
                    'prestablog_categorie_obj_nop' => $obj_categorie->descri,
                ]);
                $this->context->smarty->assign([
                    'tpl_cat' => $this->context->smarty->fetch(
                        $this->getTemplatePathFix(PrestaBlog::getT() . '_category.tpl')
                    ),
                ]);
            }
        }
        if ($this->assign_page == 2) {
            $this->pagination = PrestaBlog::getPagination(
                $this->news_count_all,
                null,
                (int) Configuration::get('prestablog_nb_liste_page'),
                (int) Tools::getValue('start'),
                (int) Tools::getValue('p')
            );

            $prestablog_search_query = '';
            if (trim(Tools::getValue('prestablog_search'))) {
                if ((int) Configuration::get('PS_REWRITING_SETTINGS')
                    && (int) Configuration::get('prestablog_rewrite_actif')
                ) {
                    $prestablog_search_query = '?prestablog_search=' . trim(Tools::getValue('prestablog_search'));
                } else {
                    $prestablog_search_query = '&prestablog_search=' . trim(Tools::getValue('prestablog_search'));
                }
            }

            $this->context->smarty->assign([
                'prestablog_search_query' => $prestablog_search_query,
                'prestablog_pagination' => $this->getTemplatePathFix(
                    PrestaBlog::getT() . '_page-pagination.tpl'
                ),
                'Pagination' => $this->pagination,
                'news' => $this->news,
                'NbNews' => $this->news_count_all,
            ]);

            if (Configuration::get('prestablog_commentfb_actif')) {
                $this->context->controller->registerJavascript(
                    'modules-prestablog-facebook-count',
                    'modules/prestablog/views/js/facebook-count.js',
                    ['position' => 'bottom', 'priority' => 200]
                );
            }

            $this->context->smarty->assign([
                'tpl_all' => $this->context->smarty->fetch(
                    $this->getTemplatePathFix(PrestaBlog::getT() . '_page-all.tpl')
                ),
            ]);
        }

        $this->context->smarty->assign([
            'layout_blog' => $this->prestablog->layout_blog[(int) Configuration::get('prestablog_layout_blog')],
        ]);

        $this->context->smarty->assign('prestashop_version', _PS_VERSION_);
        $this->setTemplate($this->getTemplatePathFix(PrestaBlog::getT() . '_page.tpl'));
        // check id_session
    }

    private function voirListeCatMenu()
    {
        $liste_cat = CategoriesClass::getListe((int) $this->context->cookie->id_lang, 1);

        if (count($liste_cat) > 0) {
            $this->context->smarty->assign([
                'MenuCatNews' => $this->displayMenuCategories($liste_cat),
            ]);

            $this->context->controller->registerJavascript(
                'modules-prestablog-menucat',
                'modules/prestablog/views/js/menucat.js',
                ['position' => 'bottom', 'priority' => 202]
            );

            $this->context->smarty->assign([
                'tpl_menu_cat' => $this->context->smarty->fetch(
                    $this->getTemplatePathFix(PrestaBlog::getT() . '_page-menucat.tpl')
                ),
            ]);
        }
    }

    private function voirFiltrageSearch()
    {
        $liste_cat = CategoriesClass::getListe((int) $this->context->cookie->id_lang, 1);

        if (count($liste_cat) > 0) {
            $html_out = '';
            $categories = new CategoriesClass();
            $liste_categories = CategoriesClass::getListe((int) $this->context->language->id, 0);
            $categorie_filtre = [];

            if (Tools::getValue('prestablog_search_array_cat')
                && count(Tools::getValue('prestablog_search_array_cat')) > 0
            ) {
                foreach (Tools::getValue('prestablog_search_array_cat') as $cat_id) {
                    $categorie_filtre[$cat_id] = new CategoriesClass((int) $cat_id, (int) $this->context->cookie->id_lang);
                }
            }

            $displaySelectArboCategories = $categories->displaySelectArboCategories(
                $liste_categories,
                0,
                0,
                $this->trans('Select a category', [], 'Modules.Prestablog.Blog'),
                'SelectCat',
                '',
                0);

            $search_array_cat = null;
            if (Tools::getValue('prestablog_search_array_cat')) {
                $search_array_cat = Tools::getValue('prestablog_search_array_cat');
            }
            $this->context->smarty->assign([
                'prestablog_search_query' => trim(Tools::getValue('prestablog_search')),
                'prestablog_search_array_cat' => $search_array_cat,
                'liste_categories' => $liste_categories,
                'categories' => $categories,
                'categorie_filtre' => $categorie_filtre,
                'displaySelectArboCategories' => $displaySelectArboCategories,
            ]);

            $this->context->controller->registerJavascript(
                'modules-prestablog-filtrecat',
                'modules/prestablog/views/js/filtrecat.js',
                ['position' => 'bottom', 'priority' => 202]
            );

            $this->context->smarty->assign([
                'tpl_filtre_cat' => $this->context->smarty->fetch(
                    $this->getTemplatePathFix(PrestaBlog::getT() . '_page-filtrecat.tpl')
                ),
            ]);
        }
    }

    public function displayMenuCategories($liste, $first = true, $child = false, $num = 0)
    {
        $prestablog = new PrestaBlog();

        $this->context->smarty->assign([
            'blog' => $this,
            'prestablog' => $prestablog,
            'liste' => $liste,
            'first' => $first,
            'child' => $child,
            'num' => $num,
        ]);

        return $this->context->smarty->fetch($this->getTemplatePathFix(PrestaBlog::getT() . '_page-menusubcat.tpl'));
    }
}
