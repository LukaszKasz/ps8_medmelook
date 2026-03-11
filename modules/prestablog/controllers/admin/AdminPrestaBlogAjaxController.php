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
class AdminPrestaBlogAjaxController extends ModuleAdminController
{
    public function ajaxProcessPrestaBlogRun()
    {
        $current_lang = (int) $this->context->language->id;
        $html = '';
        switch (Tools::getValue('do')) {
            case 'sortSubBlocks':
                if (Tools::getValue('items') && Tools::getValue('hook_name')) {
                    SubBlocksClass::updatePositions(Tools::getValue('items'), Tools::getValue('hook_name'));
                }

                break;

            case 'sortBlocs':
                if (Tools::getValue('sortblocLeft')) {
                    $sort_bloc_left = json_encode(Tools::getValue('sortblocLeft'));
                } else {
                    $sort_bloc_left = json_encode([0 => '']);
                }

                if (Tools::getValue('sortblocRight')) {
                    $sort_bloc_right = json_encode(Tools::getValue('sortblocRight'));
                } else {
                    $sort_bloc_right = json_encode([0 => '']);
                }

                Configuration::updateValue(
                    'prestablog_sbl',
                    $sort_bloc_left,
                    false,
                    null,
                    (int) Tools::getValue('id_shop')
                );
                Configuration::updateValue('prestablog_sbl', $sort_bloc_left);
                Configuration::updateValue(
                    'prestablog_sbr',
                    $sort_bloc_right,
                    false,
                    null,
                    (int) Tools::getValue('id_shop')
                );
                Configuration::updateValue('prestablog_sbr', $sort_bloc_right);

                break;

            case 'loadProductsLink':
                $prestablog = new PrestaBlog();

                if (Tools::getValue('req')) {
                    $list_product_linked = [];
                    $list_product_linked = preg_split('/;/', rtrim(Tools::getValue('req'), ';'));
                    if (count($list_product_linked) > 0) {
                        foreach ($list_product_linked as $product_link) {
                            $product_search = new Product((int) $product_link, false, $current_lang);
                            $product_cover = Image::getCover($product_search->id);
                            $image_product = new Image((int) $product_cover['id_image']);

                            $image_thumb_html = ImageManager::thumbnail(
                                _PS_IMG_DIR_ . 'p/' . $image_product->getExistingImgPath() . '.jpg',
                                'product_mini_' . (int) $product_cover['id_image'] . '.jpg',
                                45,
                                'jpg'
                            );
                            preg_match('/src="([^"]*)"/i', $image_thumb_html, $matches);
                            $image_thumb_url = $matches[1];
                            $this->context->smarty->assign([
                                'product_search' => $product_search,
                                'image_thumb_url' => $image_thumb_url,
                            ]);

                            $html .= $this->context->smarty->fetch($this->getTemplateSpecPath('blocAjaxLoadProducts.tpl'));
                        }
                        $html .= $this->context->smarty->fetch($this->getTemplateSpecPath('blocAjaxLoadProductsjs.tpl'));
                    } else {
                        $this->context->smarty->assign([
                            'prestablog' => $prestablog,
                        ]);
                        $html = $this->context->smarty->fetch($this->getTemplateSpecPath('noResultWarningClass.tpl'));
                    }
                } else {
                    $this->context->smarty->assign([
                        'prestablog' => $prestablog,
                    ]);
                    $html = $this->context->smarty->fetch($this->getTemplateSpecPath('noResultWarningClass.tpl'));
                }
                echo $html;
                break;

            case 'loadArticlesLink':
                $prestablog = new PrestaBlog();
                if (Tools::getValue('req')) {
                    $list_article_linked = [];
                    $list_article_linked = preg_split('/;/', rtrim(Tools::getValue('req'), ';'));

                    if (count($list_article_linked) > 0) {
                        foreach ($list_article_linked as $article_link) {
                            $article_search = new NewsClass((int) $article_link, $current_lang);

                            if (file_exists(PrestaBlog::imgUpPath() . '/adminth_' . $article_search->id . '.jpg')) {
                                $thumbnail = PrestaBlog::imgPathFO() . PrestaBlog::getT() . '/up-img/adminth_' . $article_search->id . '.jpg?' . md5(time());
                            } else {
                                $thumbnail = '-';
                            }
                            $this->context->smarty->assign([
                                'article_search' => $article_search,
                                'thumbnail' => $thumbnail,
                            ]);
                            $html .= $this->context->smarty->fetch($this->getTemplateSpecPath('blocAjaxLoadArticles.tpl'));
                        }
                        $html .= $this->context->smarty->fetch($this->getTemplateSpecPath('blocAjaxLoadArticlesjs.tpl'));
                    } else {
                        $this->context->smarty->assign([
                            'prestablog' => $prestablog,
                        ]);
                        $html = $this->context->smarty->fetch($this->getTemplateSpecPath('noResultWarningClass.tpl'));
                    }
                } else {
                    $this->context->smarty->assign([
                        'prestablog' => $prestablog,
                    ]);
                    $html = $this->context->smarty->fetch($this->getTemplateSpecPath('noResultWarningClass.tpl'));
                }
                echo $html;
                break;

            case 'searchProducts':
                if (Tools::getValue('req') != '') {
                    if (Tools::strlen(Tools::getValue('req'))
                        >= (int) Configuration::get('prestablog_nb_car_min_linkprod')) {
                        $start = 0;
                        $pas = (int) Configuration::get('prestablog_nb_list_linkprod');
                        if (!$pas || $pas == 0) {
                            $pas = 5;
                        }

                        if (Tools::getValue('start')) {
                            $start = (int) Tools::getValue('start');
                        }

                        $end = (int) $pas + (int) $start;

                        $list_product_linked = [];

                        if (Tools::getValue('listLinkedProducts') != '') {
                            $list_product_linked = preg_split(
                                '/;/',
                                rtrim(Tools::getValue('listLinkedProducts'), ';')
                            );
                        }

                        $result_search = [];
                        $prestablog = new PrestaBlog();
                        $rsql_search = '';
                        $rsql_lang = '';

                        $query = Tools::strtoupper(pSQL(trim(Tools::getValue('req'))));
                        $rsql_search .= ' UPPER(pl.`name`) LIKE \'%' . pSQL($query) . '%\' OR';

                        $querys = array_filter(explode(' ', $query));

                        // 'description', 'description_short', 'link_rewrite',
                        // 'meta_title', 'meta_description', 'meta_keywords'
                        $list_champs_product_lang = ['name'];

                        foreach ($querys as $value) {
                            // test si #id_product pour aller chercher directement le produit
                            if (preg_match('/^(#[0-9]*)$/', $value)) {
                                $rsql_search .= ' pl.`id_product` = ' . (int) ltrim($value, '#') . ' OR';
                            }

                            foreach ($list_champs_product_lang as $value_c) {
                                $rsql_search .= ' UPPER(pl.`' . pSQL($value_c) . '`) LIKE \'%' . pSQL($value) . '%\' OR';
                            }
                        }

                        if (Tools::getValue('lang') != '') {
                            $current_lang = (int) Tools::getValue('lang');
                        }

                        $rsql_lang = 'AND pl.`id_lang` = ' . (int) $current_lang;
                        $rsql_shop = 'AND ps.`id_shop` = ' . (int) Tools::getValue('id_shop');

                        $rsql_search = ' WHERE (' . rtrim($rsql_search, 'OR') . ') ' . $rsql_lang . ' ' . $rsql_shop;

                        $rsql_plink = '';

                        foreach ($list_product_linked as $product_link) {
                            $rsql_plink .= ' AND pl.`id_product` <> ' . (int) $product_link;
                        }

                        $rsql_search .= $rsql_plink;

                        $count_search = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
                            SELECT COUNT(DISTINCT pl.`id_product`) AS `value`
                            FROM  `' . bqSQL(_DB_PREFIX_) . 'product_lang` AS pl
                            LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'product_shop` AS ps
                                ON (ps.`id_product` = pl.`id_product`)
                            ' . $rsql_search . ';');

                        $rsql = 'SELECT DISTINCT(pl.`id_product`)
                            FROM  `' . bqSQL(_DB_PREFIX_) . 'product_lang` AS pl
                            LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'product_shop` AS ps
                                ON (ps.`id_product` = pl.`id_product`)
                            ' . $rsql_search . '
                            ORDER BY pl.`name`
                            LIMIT ' . (int) $start . ', ' . (int) $pas . ' ;';

                        $result_search = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($rsql);
                        if (count($result_search) > 0) {
                            foreach ($result_search as $value) {
                                $product_search = new Product((int) $value['id_product'], false, $current_lang);
                                $product_cover = Image::getCover($product_search->id);
                                $image_product = new Image((int) $product_cover['id_image']);
                                $image_thumb_path = ImageManager::thumbnail(
                                    _PS_IMG_DIR_ . 'p/' . $image_product->getExistingImgPath() . '.jpg',
                                    'product_mini_' . (int) $product_cover['id_image'] . '.jpg',
                                    45,
                                    'jpg'
                                );

                                $this->context->smarty->assign([
                                    'product_search' => $product_search,
                                    'image_thumb_path' => $image_thumb_path,
                                ]);
                                $html .= $this->context->smarty->fetch($this->getTemplateSpecPath('blocAjaxSearchProducts.tpl'));
                            }

                            $this->context->smarty->assign([
                                'count_search' => $count_search,
                                'prestablog' => $prestablog,
                                'start' => $start,
                                'pas' => $pas,
                                'end' => $end,
                            ]);
                            $html .= $this->context->smarty->fetch($this->getTemplateSpecPath('blocSearchProductsMore.tpl'));
                        } else {
                            $prestablog = new PrestaBlog();
                            $this->context->smarty->assign([
                                'prestablog' => $prestablog,
                            ]);

                            // Display the Smarty template
                            $html = $this->context->smarty->fetch($this->getTemplateSpecPath('warningClass.tpl'));
                        }
                    } else {
                        $prestablog = new PrestaBlog();
                        $this->context->smarty->assign([
                            'prestablog' => $prestablog,
                        ]);

                        // Display the Smarty template
                        $html = $this->context->smarty->fetch($this->getTemplateSpecPath('warningClass.tpl'));
                    }
                } else {
                    $prestablog = new PrestaBlog();
                    $this->context->smarty->assign([
                        'prestablog' => $prestablog,
                    ]);

                    // Display the Smarty template
                    $html = $this->context->smarty->fetch($this->getTemplateSpecPath('warningClass.tpl'));
                }
                echo $html;
                break;

            case 'searchArticles':
                if (Tools::getValue('req') != '') {
                    if (Tools::strlen(Tools::getValue('req'))
                        >= (int) Configuration::get('prestablog_nb_car_min_linknews')) {
                        $start = 0;
                        $pas = (int) Configuration::get('prestablog_nb_list_linknews');
                        if (!$pas || $pas == 0) {
                            $pas = 5;
                        }

                        if (Tools::getValue('start')) {
                            $start = (int) Tools::getValue('start');
                        }

                        $end = (int) $pas + (int) $start;

                        $list_article_linked = [];

                        if (Tools::getValue('listLinkedArticles') != '') {
                            $list_article_linked = preg_split('/;/', rtrim(Tools::getValue('listLinkedArticles'), ';'));
                        }

                        $result_search = [];
                        $prestablog = new PrestaBlog();
                        $rsql_search = '';
                        $rsql_lang = '';

                        $query = Tools::strtoupper(pSQL(trim(Tools::getValue('req'))));
                        $querys = array_filter(explode(' ', $query));

                        $list_champs_article_lang = [
                            // 'paragraph',
                            // 'content',
                            // 'link_rewrite',
                            'title',
                            // 'meta_title',
                            // 'meta_description',
                            // 'meta_keywords'
                        ];

                        foreach ($querys as $value) {
                            // test si #id_product pour aller chercher directement le produit
                            if (preg_match('/^(#[0-9]*)$/', $value)) {
                                $rsql_search .= ' nl.`id_prestablog_news` = ' . (int) ltrim($value, '#') . ' OR';
                            }

                            foreach ($list_champs_article_lang as $value_c) {
                                $rsql_search .= ' UPPER(nl.`' . pSQL($value_c) . '`) LIKE \'%' . pSQL($value) . '%\' OR';
                            }
                        }

                        if (Tools::getValue('lang') != '') {
                            $current_lang = (int) Tools::getValue('lang');
                        }

                        $rsql_lang = 'AND nl.`id_lang` = ' . (int) $current_lang;
                        $rsql_shop = 'AND n.`id_shop` = ' . (int) Tools::getValue('id_shop');

                        $rsql_search = ' WHERE (' . rtrim($rsql_search, 'OR') . ') ' . $rsql_lang . ' ' . $rsql_shop;

                        $rsql_plink = '';

                        foreach ($list_article_linked as $article_link) {
                            $rsql_plink .= ' AND nl.`id_prestablog_news` <> ' . (int) $article_link;
                        }

                        $rsql_search .= $rsql_plink;

                        $count_search = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
                            SELECT COUNT(DISTINCT nl.`id_prestablog_news`) AS `value`
                            FROM  `' . bqSQL(_DB_PREFIX_) . 'prestablog_news_lang` AS nl
                            LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_news` AS n
                                ON (n.`id_prestablog_news` = nl.`id_prestablog_news`)
                            ' . $rsql_search . ';');

                        $rsql = 'SELECT DISTINCT(nl.`id_prestablog_news`)
                            FROM  `' . bqSQL(_DB_PREFIX_) . 'prestablog_news_lang` AS nl
                            LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_news` AS n
                                ON (n.`id_prestablog_news` = nl.`id_prestablog_news`)
                            ' . $rsql_search . '
                            ORDER BY nl.`title`
                            LIMIT ' . (int) $start . ', ' . (int) $pas . ' ;';

                        $result_search = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($rsql);
                        $prestablog = new PrestaBlog();

                        if (count($result_search) > 0) {
                            foreach ($result_search as $value) {
                                $article_search = new NewsClass((int) $value['id_prestablog_news'], $current_lang);

                                $thumbnail = '-';
                                if (file_exists(PrestaBlog::imgUpPath() . '/adminth_' . $article_search->id . '.jpg')) {
                                    $thumbnail = PrestaBlog::imgPathFO() . PrestaBlog::getT() . '/up-img/adminth_' . $article_search->id . '.jpg?' . md5(time());
                                }
                                $this->context->smarty->assign([
                                    'article_search' => $article_search,
                                    'thumbnail' => $thumbnail,
                                ]);

                                // Display the Smarty template
                                $html .= $this->context->smarty->fetch($this->getTemplateSpecPath('blocAjaxSearchArticles.tpl'));
                            }
                            $this->context->smarty->assign([
                                'count_search' => $count_search,
                                'prestablog' => $prestablog,
                                'start' => $start,
                                'pas' => $pas,
                                'end' => $end,
                            ]);
                            $html .= $this->context->smarty->fetch($this->getTemplateSpecPath('blocSearchArticlesMore.tpl'));
                        } else {
                            $prestablog = new PrestaBlog();
                            $this->context->smarty->assign([
                                'prestablog' => $prestablog,
                            ]);

                            // Display the Smarty template
                            $html = $this->context->smarty->fetch($this->getTemplateSpecPath('/warningClass.tpl'));
                        }
                    } else {
                        $prestablog = new PrestaBlog();
                        $this->context->smarty->assign([
                            'prestablog' => $prestablog,
                        ]);

                        // Display the Smarty template
                        $html = $this->context->smarty->fetch($this->getTemplateSpecPath('/warningClass.tpl'));
                    }
                }

                echo $html;
                break;

            case 'search':
                break;

            default:
                break;
        }
    }

    private function getTemplateSpecPath($template)
    {
        return $this->getModulePath() . 'views/templates/admin/Specific/' . $template;
    }

    private function getModulePath()
    {
        return _PS_MODULE_DIR_ . 'prestablog/';
    }
}
