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
class NewsClass extends ObjectModel
{
    public $id;
    public $id_shop = 1;
    public $title;
    public $langues;
    public $paragraph;
    public $content;
    public $date;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $link_rewrite;
    public $categories = [];
    public $products_liaison = [];
    public $articles_liaison = [];
    public $slide = 0;
    public $actif;
    public $actif_langue = 0;
    public $read = 0;
    public $url_redirect = '';
    public $average_rating;
    public $number_rating;
    public $author_id;

    protected $table = 'prestablog_news';
    protected $identifier = 'id_prestablog_news';

    public static $table_static = 'prestablog_news';
    public static $identifier_static = 'id_prestablog_news';

    public static $definition = [
        'table' => 'prestablog_news',
        'primary' => 'id_prestablog_news',
        'multilang' => true,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'date' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true],
            'langues' => ['type' => self::TYPE_STRING, 'validate' => 'isString',  'required' => true],
            'slide' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'actif' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'url_redirect' => ['type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'],
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255],
            'meta_title' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'size' => 255,
            ],
            'meta_description' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'size' => 255,
            ],
            'meta_keywords' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'size' => 255,
            ],
            'link_rewrite' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isLinkRewrite',
                'size' => 255,
            ],
            'content' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'],
            'paragraph' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'],
        ],
    ];

    // Method for filtering by id_shop in multishop environnement
    public static function getNewsWithShopById($id_prestablog_news, $id_lang, $id_shop)
    {
        $sql = 'SELECT n.*, nl.*
                FROM ' . _DB_PREFIX_ . 'prestablog_news n
                LEFT JOIN ' . _DB_PREFIX_ . 'prestablog_news_lang nl ON n.id_prestablog_news = nl.id_prestablog_news
                WHERE n.id_prestablog_news = ' . (int) $id_prestablog_news . '
                AND nl.id_lang = ' . (int) $id_lang . '
                AND n.id_shop = ' . (int) $id_shop;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if ($result) {
            $news = new NewsClass($id_prestablog_news, $id_lang, $id_shop);
            $news->hydrate($result);

            return $news;
        }

        return false;
    }

    public function copyFromPost()
    {
        $object = $this;
        $table = $this->table;

        foreach ($_POST as $key => $value) {
            if (property_exists($object, $key) && $key != 'id_' . $table) {
                if ($key == 'passwd' && Tools::getValue('id_' . $table) && empty($value)) {
                    continue;
                }
                if ($key == 'passwd' && !empty($value)) {
                    $value = Tools::encrypt($value);
                }
                $object->{$key} = Tools::getValue($key);
            }
        }

        $rules = call_user_func([get_class($object), 'getValidationRules'], get_class($object));
        if (count($rules['validateLang'])) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach (array_keys($rules['validateLang']) as $field) {
                    if (Tools::getIsset($field . '_' . (int) $language['id_lang'])) {
                        $object->{$field}[(int) $language['id_lang']] = Tools::getValue(
                            $field . '_' . (int) $language['id_lang']
                        );
                    }
                }
            }
        }
    }

    public static function getCountListeAllNoLang(
        $only_actif = 0,
        $only_slide = 0,
        $date_debut = null,
        $date_fin = null,
        $categorie = null)
    {
        $context = Context::getContext();
        $multiboutique_filtre = 'AND n.`id_shop` = ' . (int) $context->shop->id;

        $actif = '';
        if ($only_actif) {
            $actif = 'AND n.`actif` = 1';
        }
        $slide = '';
        if ($only_slide) {
            $slide = 'AND n.`slide` = 1';
        }

        $verbose_categorie = '';
        if ($categorie) {
            $verbose_categorie = 'AND cc.`categorie` = ' . (int) $categorie;
        }

        $between_date = '';
        if (!empty($date_debut) && !empty($date_fin)) {
            $between_date = 'AND TIMESTAMP(n.`date`) BETWEEN \'' . pSQL($date_debut) . '\' AND \'' . pSQL($date_fin) . '\'';
        } elseif (empty($date_debut) && !empty($date_fin)) {
            $between_date = 'AND TIMESTAMP(n.`date`) <= \'' . pSQL($date_fin) . '\'';
        } elseif (!empty($date_debut) && empty($date_fin)) {
            $between_date = 'AND TIMESTAMP(n.`date`) >= \'' . pSQL($date_debut) . '\'';
        }

        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
            SELECT count(DISTINCT n.id_prestablog_news) as `count`
            FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '` n
            LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_correspondancecategorie` cc
            ON (n.`' . bqSQL(self::$identifier_static) . '` = cc.`news`)
            WHERE n.`' . bqSQL(self::$identifier_static) . '` > 0
            ' . $multiboutique_filtre . '
            ' . $actif . '
            ' . $slide . '
            ' . $verbose_categorie . '
            ' . $between_date);

        return $value['count'];
    }

    public static function getTitleNews($id, $id_lang)
    {
        if (empty($id_lang)) {
            $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }

        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
            SELECT nl.`title`
            FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '` n
            JOIN `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_lang` nl
            ON (n.`' . bqSQL(self::$identifier_static) . '` = nl.`' . bqSQL(self::$identifier_static) . '`)
            WHERE
            nl.`id_lang` = ' . (int) $id_lang . '
            AND    n.`' . bqSQL(self::$identifier_static) . '` = ' . (int) $id);

        return $value['title'];
    }

    public static function getAuthorID($news)
    {
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
            SELECT n.`author_id`
            FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_lang` n
            WHERE
            n.`id_prestablog_news` = ' . (int) $news);

        return $value['author_id'];
    }

    public static function getProductLinkListe($news, $active = false)
    {
        $activeCondition = $active ? 'AND p.`active` = 1' : '';
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT pnp.`id_product`
            FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_product` pnp
            JOIN `' . _DB_PREFIX_ . 'product` p ON (pnp.`id_product` = p.`id_product`)
            WHERE pnp.`' . bqSQL(self::$identifier_static) . '` = ' . (int) $news . ' 
            ' . $activeCondition);

        $return2 = [];
        foreach ($return1 as $value) {
            $return2[] = $value['id_product'];
        }

        return $return2;
    }

    public static function getArticleLinkListe($news, $active = false)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT    `id_prestablog_newslink`
            FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_newslink`
            WHERE `' . bqSQL(self::$identifier_static) . '` = ' . (int) $news);

        $return2 = [];
        foreach ($return1 as $value) {
            $news = new NewsClass((int) $value['id_prestablog_newslink']);

            if ((int) $news->id) {
                if ($active) {
                    if ($news->actif && (new DateTime($news->date)) <= (new DateTime())) {
                        $return2[] = $value['id_prestablog_newslink'];
                    }
                } else {
                    $return2[] = $value['id_prestablog_newslink'];
                }
            } else {
                NewsClass::removeArticleLinkDeleted((int) $value['id_prestablog_newslink']);
            }
        }

        return $return2;
    }

    public static function getPopupLink($news)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT    `id_prestablog_popup`
            FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_popuplink`
            WHERE `id_prestablog_news` = ' . (int) $news);

        $news = new NewsClass((int) $news);

        if (isset($return1[0])) {
            $return2 = '';
            if ((int) $news->id) {
                $return2 = (int) $return1[0]['id_prestablog_popup'];
            } else {
                NewsClass::removePopupLinkDeleted((int) $return1[0]['id_prestablog_popup']);
            }

            return $return2;
        }
    }

    public static function getColorHome($id_shop)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT    `menu_color`,`read_color`,`hover_color`,`title_color`,`text_color`,`menu_hover`,`menu_link`,`link_read`,`article_title`,`article_text`,`block_categories`,`block_categories_link`,`block_title`,`block_btn`,`categorie_block_background`,`article_background`,`categorie_block_background_hover`,`block_btn_hover`,`ariane_color`,`ariane_color_text`,`ariane_border`,`block_categories_link_btn`, `sharing_icon_color`
        FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_color`
        WHERE `id_shop` =' . $id_shop);

        return $return1;
    }

    public static function getNewsProductLinkListe($product, $active = false)
    {
        $context = Context::getContext();
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT    np.`' . bqSQL(self::$identifier_static) . '`
        FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_product` np
        LEFT JOIN `' . bqSQL(_DB_PREFIX_ . 'prestablog_news') . '` n
        ON (np.`id_prestablog_news` = n.`id_prestablog_news`)
        WHERE np.`id_product` = ' . (int) $product . '
        AND np.`id_shop` = ' . (int) $context->shop->id . '
        ORDER BY n.`date` DESC');

        $return2 = [];
        foreach ($return1 as $value) {
            $news = new NewsClass((int) $value['id_prestablog_news']);

            if ($active) {
                if ($news->actif && (new DateTime($news->date)) <= (new DateTime())) {
                    $return2[] = $value['id_prestablog_news'];
                }
            } else {
                $return2[] = $value['id_prestablog_news'];
            }
        }

        return $return2;
    }

    public static function checkRate($news, $id_session)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT    np.`id_prestablog_news`
        FROM `' . bqSQL(_DB_PREFIX_ . 'prestablog_rate') . '` np
        WHERE np.`id_session` = ' . (int) $id_session . ' AND  np.`id_prestablog_news` = ' . (int) $news);

        if (isset($return1[0]) && $return1[0]['id_prestablog_news'] != null && $return1[0]['id_prestablog_news'] != 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function insertRateId($news, $id_session)
    {
        return Db::getInstance()->Execute('
        INSERT INTO  `' . bqSQL(_DB_PREFIX_ . 'prestablog_rate') . '`
        (`id_prestablog_news`, `id_session`)
        VALUES
        (' . (int) $news . ', ' . (int) $id_session . ')');
    }

    public static function getRate($news)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT    np.`average_rating`, np.`number_rating`
        FROM `' . bqSQL(_DB_PREFIX_ . 'prestablog_news') . '` np
        WHERE np.`id_prestablog_news` = ' . (int) $news);

        return $return1;
    }

    public static function insertRating($news, $rate)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT    np.`average_rating`, np.`number_rating`
        FROM `' . bqSQL(_DB_PREFIX_ . 'prestablog_news') . '` np
        WHERE np.`id_prestablog_news` = ' . (int) $news);

        $average_rating = $return1[0]['average_rating'];
        $number_rating = $return1[0]['number_rating'];
        if ($number_rating == 0 || $number_rating == null) {
            $new_rate = $rate;
            $number_rating = 1;
        } else {
            $full_rate = $average_rating * $number_rating;
            ++$number_rating;
            $new_rate = ($rate + $full_rate) / $number_rating;
        }

        return Db::getInstance()->Execute('
        UPDATE `' . bqSQL(_DB_PREFIX_ . 'prestablog_news') . '`
        SET `average_rating`= ' . $new_rate . ',`number_rating` = ' . $number_rating . '
        WHERE `id_prestablog_news` = ' . (int) $news);
    }

    public static function removeProductLinkDeleted($product)
    {
        $context = Context::getContext();

        return Db::getInstance()->Execute('
        DELETE FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_product`
        WHERE `id_product` = ' . (int) $product . ' AND `id_shop` = ' . (int) $context->shop->id);
    }

    public static function removeArticleLinkDeleted($news)
    {
        return Db::getInstance()->Execute('
        DELETE FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_newslink`
        WHERE `id_prestablog_newslink` = ' . (int) $news);
    }

    public static function removePopupLinkDeleted($news)
    {
        return Db::getInstance()->Execute('
        DELETE FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_popuplink`
        WHERE `id_prestablog_news` = ' . (int) $news);
    }

    public static function updateProductLinkNews($news, $product)
    {
        $context = Context::getContext();

        return Db::getInstance()->Execute('
        INSERT INTO `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_product`
        (`' . bqSQL(self::$identifier_static) . '`, `id_product`, `id_shop`)
        VALUES (' . (int) $news . ', ' . (int) $product . ', ' . (int) $context->shop->id . ')');
    }

    public static function updateArticleLinkNews($news, $newslink)
    {
        return Db::getInstance()->Execute('
        INSERT INTO `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_newslink`
        (`' . bqSQL(self::$identifier_static) . '`, `id_prestablog_newslink`)
        VALUES (' . (int) $news . ', ' . (int) $newslink . ')');
    }

    public static function updatePopupLinkNews($news, $popup)
    {
        return Db::getInstance()->Execute('
        INSERT INTO `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_popuplink`
        (`' . bqSQL(self::$identifier_static) . '`, `id_prestablog_popup`)
        VALUES (' . (int) $news . ', ' . (int) $popup . ')');
    }

    public static function updateAuthorId($news, $author_id)
    {
        $news = (int) $news;
        $author_id = (int) $author_id;

        return Db::getInstance()->Execute(
            'INSERT INTO `' . bqSQL(_DB_PREFIX_ . 'prestablog_news') . '` (`id_prestablog_news`, `author_id`)
            VALUES (' . $news . ', ' . $author_id . ')
            ON DUPLICATE KEY UPDATE `author_id` = VALUES(`author_id`)'
        );
    }

    public static function removeAllProductsLinkNews($news)
    {
        return Db::getInstance()->Execute('
        DELETE FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_product`
        WHERE `' . bqSQL(self::$identifier_static) . '` = ' . (int) $news);
    }

    public static function removeAllArticlesLinkNews($news)
    {
        return Db::getInstance()->Execute('
        DELETE FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_newslink`
        WHERE `' . bqSQL(self::$identifier_static) . '` = ' . (int) $news);
    }

    public static function removeAllPopupLinkNews($news)
    {
        return Db::getInstance()->Execute('
        DELETE FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_popuplink`
        WHERE `' . bqSQL(self::$identifier_static) . '` = ' . (int) $news);
    }

    public static function removeProductLinkNews($news, $product)
    {
        $context = Context::getContext();

        return Db::getInstance()->Execute('
        DELETE FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_product`
        WHERE `' . bqSQL(self::$identifier_static) . '` = ' . (int) $news . ' AND `id_product` = ' . (int) $product . ' AND `id_shop` = ' . (int) $context->shop->id);
    }

    public static function removeArticleLinkNews($news, $newslink)
    {
        return Db::getInstance()->Execute('
        DELETE FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_newslink`
        WHERE `' . bqSQL(self::$identifier_static) . '` = ' . (int) $news . '
        AND `id_prestablog_newslink` = ' . (int) $newslink);
    }

    public static function removePopupLinkNews($news, $popuplink)
    {
        return Db::getInstance()->Execute('
        DELETE FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_popuplink`
        WHERE `' . bqSQL(self::$identifier_static) . '` = ' . (int) $news . '
        AND `id_prestablog_popup` = ' . (int) $popuplink);
    }

    public static function getCountListeAll(
        $id_lang = null,
        $only_actif = 0,
        $only_slide = 0,
        $date_debut = null,
        $date_fin = null,
        $categorie = null,
        $actif_langue = 0,
        $search = '')
    {
        $context = Context::getContext();
        $multiboutique_filtre = ' AND n.`id_shop` = ' . (int) $context->shop->id;

        $actif = '';
        if ($only_actif) {
            $actif = ' AND n.`actif` = 1';
        }
        $actif_lang = '';
        if ($actif_langue) {
            $actif_lang = ' AND nl.`actif_langue` = 1';
        }
        $slide = '';
        if ($only_slide) {
            $slide = ' AND n.`slide` = 1';
        }

        $verbose_categorie = '';
        if ($categorie != null) {
            if (is_array($categorie)) {
                $verbose_categorie = ' AND (';
                foreach ($categorie as $value) {
                    $verbose_categorie .= ' cc.`categorie` = ' . (int) $value . ' OR';
                }
                $verbose_categorie = rtrim($verbose_categorie, 'OR');
                $verbose_categorie .= ')';
            } elseif (is_int($categorie)) {
                $verbose_categorie = ' AND cc.`categorie` = ' . (int) $categorie;
            }
        }

        $between_date = '';
        if (!empty($date_debut) && !empty($date_fin)) {
            $between_date = ' AND TIMESTAMP(n.`date`) BETWEEN \'' . pSQL($date_debut) . '\' AND \'' . pSQL($date_fin) . '\'';
        } elseif (empty($date_debut) && !empty($date_fin)) {
            $between_date = ' AND TIMESTAMP(n.`date`) <= \'' . pSQL($date_fin) . '\'';
        } elseif (!empty($date_debut) && empty($date_fin)) {
            $between_date = ' AND TIMESTAMP(n.`date`) >= \'' . pSQL($date_debut) . '\'';
        }

        $lang = '';
        if (empty($id_lang)) {
            $lang = ' AND nl.`id_lang` = ' . (int) Configuration::get('PS_LANG_DEFAULT');
        } elseif (is_array($id_lang)) {
            if (count($id_lang) > 0) {
                foreach ($id_lang as $lang_id) {
                    $lang = ' AND nl.`id_lang` = ' . (int) $lang_id . ' ';
                }
            }
        } else {
            if ((int) $id_lang == 0) {
                $lang = '';
            } else {
                $lang = ' AND nl.`id_lang` = ' . (int) $id_lang;
            }
        }

        $filtre_groupes = PrestaBlog::getFiltreGroupes('cc.`categorie`', 'categorie');

        $search_sql = '';
        if ($search != '') {
            $from_fields = [
                'nl.`title`',
                'nl.`content`',
                'nl.`paragraph`',
            ];
            $search_sql = PrestaBlog::createSqlFilterSearch($from_fields, $search, 3);
        }

        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
        SELECT count(DISTINCT nl.id_prestablog_news) as `count`
        FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_lang` nl
        LEFT JOIN `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '` n
        ON (n.`' . bqSQL(self::$identifier_static) . '` = nl.`' . bqSQL(self::$identifier_static) . '`)
        LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_correspondancecategorie` cc
        ON (n.`' . bqSQL(self::$identifier_static) . '` = cc.`news`)
        WHERE 1=1
        ' . $filtre_groupes . '
        ' . $search_sql . '
        ' . $multiboutique_filtre . '
        ' . $lang . '
        ' . $actif . '
        ' . $actif_lang . '
        ' . $slide . '
        ' . $verbose_categorie . '
        ' . $between_date);

        return $value['count'];
    }

    public static function getListeTable(
        $id_lang = null,
        $only_actif = 0,
        $only_slide = 0,
        $limit_start = 0,
        $limit_stop = null,
        $tri_champ = 'n.`date`',
        $tri_ordre = 'desc',
        $date_debut = null,
        $date_fin = null,
        $categorie = null,
        $actif_langue = 0,
        $title_length = 150,
        $intro_length = 150,
        $search = '')
    {
        $context = Context::getContext();
        $multiboutique_filtre = ' AND n.`id_shop` = ' . (int) $context->shop->id;

        $actif = $only_actif ? ' AND n.`actif` = 1' : '';
        $actif_lang = $actif_langue ? ' AND nl.`actif_langue` = 1' : '';
        $slide = $only_slide ? ' AND n.`slide` = 1' : '';

        $cat = '';
        if (!empty($categorie)) {
            $cat = is_array($categorie) ?
                   ' AND cc.`categorie` IN (' . implode(',', array_map('intval', $categorie)) . ')' :
                   ' AND cc.`categorie` = ' . (int) $categorie;
        }

        $between_date = '';
        if ($date_debut && $date_fin) {
            $between_date = ' AND TIMESTAMP(n.`date`) BETWEEN \'' . pSQL($date_debut) . '\' AND \'' . pSQL($date_fin) . '\'';
        } elseif ($date_fin) {
            $between_date = ' AND TIMESTAMP(n.`date`) <= \'' . pSQL($date_fin) . '\'';
        } elseif ($date_debut) {
            $between_date = ' AND TIMESTAMP(n.`date`) >= \'' . pSQL($date_debut) . '\'';
        }

        $lang = $id_lang ?
                (is_array($id_lang) ? ' AND nl.`id_lang` IN (' . implode(',', array_map('intval', $id_lang)) . ')' : ' AND nl.`id_lang` = ' . (int) $id_lang) :
                ' AND nl.`id_lang` = ' . (int) Configuration::get('PS_LANG_DEFAULT');

        $filtre_groupes = PrestaBlog::getFiltreGroupes('cc.`categorie`', 'categorie');
        $search_sql = $search ? PrestaBlog::createSqlFilterSearch(['nl.`title`', 'nl.`content`', 'nl.`paragraph`'], $search, 3) : '';

        $limit = !empty($limit_stop) ? ' LIMIT ' . (int) $limit_start . ', ' . (int) $limit_stop : '';

        $sql = 'SELECT DISTINCT(nl.`id_prestablog_news`), n.*, nl.*,
            LEFT(nl.`title`, ' . (int) $title_length . ') as title,
            (
                SELECT count(cn.`id_prestablog_commentnews`)
                FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_commentnews` cn
                WHERE cn.`news` = n.`id_prestablog_news`
                AND cn.`actif` = 1
            ) as count_comments,
            n.`' . bqSQL(self::$identifier_static) . '` as `id`
            FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_lang` nl
            LEFT JOIN `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '` n ON (n.`' . bqSQL(self::$identifier_static) . '` = nl.`' . bqSQL(self::$identifier_static) . '`)
            LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_correspondancecategorie` cc ON (n.`' . bqSQL(self::$identifier_static) . '` = cc.`news`)
            WHERE 1=1
            ' . $filtre_groupes . '
            ' . $search_sql . '
            ' . $multiboutique_filtre . '
            ' . $lang . '
            ' . $actif . '
            ' . $actif_lang . '
            ' . $slide . '
            ' . $cat . '
            ' . $between_date . '
            ORDER BY ' . pSQL($tri_champ) . ' ' . pSQL($tri_ordre) . $limit;

        $liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

        if (count($liste) > 0) {
            foreach ($liste as $key => $value) {
                $liste[$key]['categories'] = CorrespondancesCategoriesClass::getCategoriesListeName(
                    (int) $value['id_prestablog_news'],
                    (int) $context->language->id,
                    1
                );
                $liste[$key]['authors'] = AuthorClass::getAuthorName((int) $value['id_prestablog_news']);

                $liste[$key]['paragraph'] = $value['paragraph'];
                $liste[$key]['paragraph_crop'] = $value['paragraph'];

                if ((Tools::strlen(trim($value['paragraph'])) == 0)
                && (Tools::strlen(trim(strip_tags($value['content']))) >= 1)) {
                    $liste[$key]['paragraph_crop'] = trim(strip_tags(html_entity_decode($value['content'])));
                }

                if (Tools::strlen(trim($liste[$key]['paragraph_crop'])) > (int) $intro_length) {
                    $liste[$key]['paragraph_crop'] = PrestaBlog::cleanCut(
                        $liste[$key]['paragraph_crop'],
                        (int) $intro_length,
                        ' [...]'
                    );
                }
                if (file_exists(PrestaBlog::imgUpPath() . '/' . $value[self::$identifier_static] . '.jpg')) {
                    $liste[$key]['image_presente'] = 1;
                }
                if (Tools::strlen(trim($value['content'])) >= 1) {
                    $liste[$key]['link_for_unique'] = 1;
                }
            }
        }

        return $liste;
    }

    public static function getListe(
        $id_lang = null,
        $only_actif = 0,
        $only_slide = 0,
        $limit_start = 0,
        $limit_stop = null,
        $tri_champ = 'n.`date`',
        $tri_ordre = 'desc',
        $date_debut = null,
        $date_fin = null,
        $categorie = null,
        $actif_langue = 0,
        $title_length = 150,
        $intro_length = 150,
        $search = '')
    {
        $context = Context::getContext();
        $multiboutique_filtre = ' AND n.`id_shop` = ' . (int) $context->shop->id;

        $liste = [];

        $actif = '';
        if ($only_actif) {
            $actif = ' AND n.`actif` = 1';
        }
        $actif_lang = '';
        if ($actif_langue) {
            $actif_lang = ' AND nl.`actif_langue` = 1';
        }
        $slide = '';
        if ($only_slide) {
            $slide = ' AND n.`slide` = 1';
        }

        $cat = '';
        if ($categorie != null) {
            if (is_array($categorie)) {
                $cat = ' AND (';
                foreach ($categorie as $value) {
                    $cat .= ' cc.`categorie` = ' . (int) $value . ' OR';
                }
                $cat = rtrim($cat, 'OR');
                $cat .= ')';
            } elseif (is_int($categorie)) {
                $cat = ' AND cc.`categorie` = ' . (int) $categorie;
            }
        }

        $between_date = '';
        if (!empty($date_debut) && !empty($date_fin)) {
            $between_date = ' AND TIMESTAMP(n.`date`) BETWEEN \'' . pSQL($date_debut) . '\' AND \'' . pSQL($date_fin) . '\'';
        } elseif (empty($date_debut) && !empty($date_fin)) {
            $between_date = ' AND TIMESTAMP(n.`date`) <= \'' . pSQL($date_fin) . '\'';
        } elseif (!empty($date_debut) && empty($date_fin)) {
            $between_date = ' AND TIMESTAMP(n.`date`) >= \'' . pSQL($date_debut) . '\'';
        }

        $limit = '';
        if (!empty($limit_stop)) {
            $limit = ' LIMIT ' . (int) $limit_start . ', ' . (int) $limit_stop;
        }

        $lang = '';
        if (empty($id_lang)) {
            $lang = ' AND nl.`id_lang` = ' . (int) Configuration::get('PS_LANG_DEFAULT');
        } elseif (is_array($id_lang)) {
            if (count($id_lang) > 0) {
                foreach ($id_lang as $lang_id) {
                    $lang = ' AND nl.`id_lang` = ' . (int) $lang_id . ' ';
                }
            }
        } else {
            if ((int) $id_lang == 0) {
                $lang = '';
            } else {
                $lang = ' AND nl.`id_lang` = ' . (int) $id_lang;
            }
        }

        $filtre_groupes = PrestaBlog::getFiltreGroupes('cc.`categorie`', 'categorie');

        $search_sql = '';
        if ($search != '') {
            $from_fields = [
                'nl.`title`',
                'nl.`content`',
                'nl.`paragraph`',
            ];
            $search_sql = PrestaBlog::createSqlFilterSearch($from_fields, $search, 3);
        }

        $sql = 'SELECT DISTINCT(nl.`id_prestablog_news`), n.*, nl.*,
    LEFT(nl.`title`, ' . (int) $title_length . ') as title,
    (
    SELECT count(cn.`id_prestablog_commentnews`)
    FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_commentnews` cn
    WHERE cn.`news` = n.`id_prestablog_news`
    AND cn.`actif` = 1
    ) as count_comments,
    n.`' . bqSQL(self::$identifier_static) . '` as `id`
    FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_lang` nl
    LEFT JOIN `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '` n
    ON (n.`' . bqSQL(self::$identifier_static) . '` = nl.`' . bqSQL(self::$identifier_static) . '`)
    LEFT JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_correspondancecategorie` cc
    ON (n.`' . bqSQL(self::$identifier_static) . '` = cc.`news`)
    WHERE 1=1
    ' . $filtre_groupes . '
    ' . $search_sql . '
    ' . $multiboutique_filtre . '
    ' . $lang . '
    ' . $actif . '
    ' . $actif_lang . '
    ' . $slide . '
    ' . $cat . '
    ' . $between_date . '
    ORDER BY ' . pSQL($tri_champ) . ' ' . pSQL($tri_ordre) . '
    ' . $limit;

        $liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

        if (count($liste) > 0) {
            foreach ($liste as $key => $value) {
                $liste[$key]['categories'] = CorrespondancesCategoriesClass::getCategoriesListeName(
                    (int) $value['id_prestablog_news'],
                    (int) $context->language->id,
                    1
                );
                $liste[$key]['authors'] = AuthorClass::getAuthorName((int) $value['id_prestablog_news']);

                $liste[$key]['paragraph'] = $value['paragraph'];
                $liste[$key]['paragraph_crop'] = $value['paragraph'];

                if ((Tools::strlen(trim($value['paragraph'])) == 0)
                && (Tools::strlen(trim(strip_tags($value['content']))) >= 1)) {
                    $liste[$key]['paragraph_crop'] = trim(strip_tags(html_entity_decode($value['content'])));
                }

                if (Tools::strlen(trim($liste[$key]['paragraph_crop'])) > (int) $intro_length) {
                    $liste[$key]['paragraph_crop'] = PrestaBlog::cleanCut(
                        $liste[$key]['paragraph_crop'],
                        (int) $intro_length,
                        ' [...]'
                    );
                }
                // check jpg exist in categories
                if (file_exists(PrestaBlog::imgUpPath() . '/' . $value[self::$identifier_static] . '.jpg')) {
                    $liste[$key]['image_presente'] = 1;
                } else {
                    $liste[$key]['image_presente'] = 0;
                }

                // check webp thumb exist in categories
                $prefixe = 'thumb_';
                if (file_exists(PrestaBlog::imgUpPath() . '/' . $prefixe . $value[self::$identifier_static] . '.webp')) {
                    $liste[$key]['webp_present'] = 1;
                } else {
                    $liste[$key]['webp_present'] = 0;
                }
                if (file_exists(PrestaBlog::imgUpPath() . '/adminth_' . $value[self::$identifier_static] . '.webp')) {
                    $liste[$key]['adminth_webp_present'] = 1;
                } else {
                    $liste[$key]['adminth_webp_present'] = 0;
                }
                if (Tools::strlen(trim($value['content'])) >= 1) {
                    $liste[$key]['link_for_unique'] = 1;
                }
            }
        }

        return $liste;
    }

    public function registerTablesBdd()
    {
        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . bqSQL(_DB_PREFIX_ . $this->table) . '` (
        `' . bqSQL($this->identifier) . '` int(10) unsigned NOT null auto_increment,
        `id_shop` int(10) unsigned NOT null,
        `date` datetime NOT null,
        `date_modification` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `langues` text NOT null,
        `actif` tinyint(1) NOT null DEFAULT \'1\',
        `slide` tinyint(1) NOT null DEFAULT \'0\',
        `url_redirect` text NOT null,
        `average_rating` decimal(10,1),
        `number_rating` int(10),
        `author_id` int(10),
        PRIMARY KEY (`' . bqSQL($this->identifier) . '`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . bqSQL(_DB_PREFIX_ . $this->table) . '_lang` (
        `' . bqSQL($this->identifier) . '` int(10) unsigned NOT null,
        `id_lang` int(10) unsigned NOT null,
        `title` varchar(255) NOT null,
        `paragraph` text NOT null,
        `content` mediumtext NOT null,
        `meta_description` text NOT null,
        `meta_keywords` text NOT null,
        `meta_title` text NOT null,
        `link_rewrite` text NOT null,
        `actif_langue` tinyint(1) NOT null DEFAULT \'1\',
        `read` int(10) unsigned NOT null DEFAULT \'0\',
        PRIMARY KEY (`' . bqSQL($this->identifier) . '`, `id_lang`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . bqSQL(_DB_PREFIX_ . $this->table) . '_product` (
        `' . bqSQL($this->identifier) . '_product` int(10) unsigned NOT null auto_increment,
        `' . bqSQL($this->identifier) . '` int(10) unsigned NOT null,
        `id_product` int(10) unsigned NOT null,
        `id_shop` INT NOT NULL,
        PRIMARY KEY (`' . bqSQL($this->identifier) . '_product`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . bqSQL(_DB_PREFIX_) . 'prestablog_color` (
        `id` int(10) unsigned NOT null auto_increment,
        `menu_color` varchar(33) NOT null DEFAULT \'0\',
        `menu_hover` varchar(33) NOT null DEFAULT \'0\',
        `read_color` varchar(33) NOT null DEFAULT \'0\',
        `hover_color` varchar(33) NOT null DEFAULT \'0\',
        `title_color` varchar(33) NOT null DEFAULT \'0\',
        `text_color` varchar(33) NOT null DEFAULT \'0\',
        `menu_link` varchar(33) NOT null DEFAULT \'0\',
        `link_read` varchar(33) NOT null DEFAULT \'0\',
        `article_title` varchar(33) NOT null DEFAULT \'0\',
        `article_text` varchar(33) NOT null DEFAULT \'0\',
        `block_categories` varchar(33) NOT null DEFAULT \'0\',
        `block_categories_link` varchar(33) NOT null DEFAULT \'0\',
        `block_title` varchar(33) NOT null DEFAULT \'0\',
        `block_btn` varchar(33) NOT null DEFAULT \'0\',
        `categorie_block_background` varchar(33) NOT null DEFAULT \'0\',
        `article_background` varchar(33) NOT null DEFAULT \'0\',
        `categorie_block_background_hover` varchar(33) NOT null DEFAULT \'0\',
        `block_btn_hover` varchar(33) NOT null DEFAULT \'0\',
        `id_shop` int(10) NOT null DEFAULT \'1\',
        `ariane_color` varchar(33) NOT null DEFAULT \'0\',
        `ariane_color_text` varchar(33) NOT null DEFAULT \'0\',
        `ariane_border` varchar(33) NOT null DEFAULT \'0\',
        `block_categories_link_btn` varchar(33) NOT null DEFAULT \'0\',
        `sharing_icon_color` varchar(33) NOT null DEFAULT \'0\',
        PRIMARY KEY (`id`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . bqSQL(_DB_PREFIX_) . 'prestablog_news_newslink` (
        `id_prestablog_news_newslink` int(10) unsigned NOT null auto_increment,
        `id_prestablog_news` int(10) unsigned NOT null,
        `id_prestablog_newslink` int(10) unsigned NOT null,
        PRIMARY KEY (`id_prestablog_news_newslink`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . bqSQL(_DB_PREFIX_) . 'prestablog_rate` (
        `id` int(10) unsigned NOT null auto_increment,
        `id_prestablog_news` int(10) unsigned NOT null,
        `id_session` int(10) unsigned NOT null,
        PRIMARY KEY (`id`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . bqSQL(_DB_PREFIX_) . 'prestablog_news_popuplink` (
        `id_prestablog_news_popuplink` int(10) unsigned NOT null auto_increment,
        `id_prestablog_news` int(10) unsigned NOT null,
        `id_prestablog_popup` int(10) unsigned NOT null,
        PRIMARY KEY (`id_prestablog_news_popuplink`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
        ALTER TABLE `' . bqSQL(_DB_PREFIX_) . 'prestablog_news`
        ADD KEY `id_shop` (`id_shop`),
        ADD KEY `actif` (`actif`)')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
        ALTER TABLE `' . bqSQL(_DB_PREFIX_) . 'prestablog_news_newslink`
        ADD KEY `id_prestablog_news` (`id_prestablog_news`),
        ADD KEY `id_prestablog_newslink` (`id_prestablog_newslink`)')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
        ALTER TABLE `' . bqSQL(_DB_PREFIX_) . 'prestablog_news_product`
        ADD KEY `id_prestablog_news` (`id_prestablog_news`),
        ADD KEY `id_product` (`id_product`),
        ADD KEY `id_shop` (`id_shop`)')) {
            return false;
        }

        $langues = Language::getLanguages(true);
        if (count($langues) > 0) {
            $langue_use = [];
            foreach ($langues as $value) {
                $langue_use[] = (int) $value['id_lang'];
            }

            if (!Db::getInstance()->Execute('
            INSERT INTO `' . bqSQL(_DB_PREFIX_ . $this->table) . '`
            (`' . bqSQL($this->identifier) . '`, `id_shop`, `date` , `langues` , `actif`, `slide`)
            VALUES
            (1, 1, DATE_ADD(NOW(), INTERVAL -3 DAY), \'' . json_encode($langue_use) . '\', 1, 1)')) {
                return false;
            }

            $title = [
                1 => 'This is a demo title',
            ];

            $paragraph = [
                1 => 'Praesent fringilla adipiscing leo. Vestibulum eget venenatis risus. Aliquam tristique erat acodio suscipit tempus. Nullam faucibus libero tortor, eget volutpat lacus molestie non',
            ];

            $content = [
                1 => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eget pretium lectus, sed bibendum
            augue. In sollicitudin convallis blandit.
            
            Curabitur venenatis ut elit quis tempus.
            
            Sed eget sem pretium, consequat ante sit amet, accumsan nunc. Vestibulum adipiscing dapibus tortor,
            eget lacinia neque dapibus auctor. Integer a dui in tellus dignissim dictum eu eu orci.
            Integer venenatis libero a justo rutrum, eu facilisis libero aliquam. Praesent sit amet elit nunc.
            Vestibulum aliquam turpis tellus, sed sagittis velit suscipit molestie. Nullam eleifend convallis
            sodales. Aenean est magna, molestie quis viverra vitae, hendrerit nec dui.',
            ];

            $meta_description = [
                1 => 'Praesent fringilla adipiscing leo. Vestibulum eget venenatis risus.',
            ];

            $meta_keywords = [
                1 => 'Curabitur, venenatis, ut elit, quis tempus, sed eget, sem pretium',
            ];

            $meta_title = [
                1 => 'Curabitur venenatis ut elit quis tempus, sed eget sem pretium',
            ];

            $link_rewrite = [
                1 => 'curabitur-venenatis-ut-elit-quis-tempus-sed-eget-sem-pretium',
            ];

            $sql_values = 'VALUES ';
            for ($i = 1; $i <= 1; ++$i) {
                foreach ($langues as $value) {
                    $sql_values .= '
        (
        ' . (int) $i . ',
        ' . (int) $value['id_lang'] . ',
        \'' . $title[$i] . '\',
        \'' . pSQL($paragraph[$i]) . '\',
        \'' . $content[$i] . '\',
        \'' . pSQL($meta_description[$i]) . '\',
        \'' . pSQL($meta_keywords[$i]) . '\',
        \'' . pSQL($meta_title[$i]) . '\',
        \'' . pSQL($link_rewrite[$i]) . '\',
        1
    ),';
                }
            }

            $sql_values = rtrim($sql_values, ',');
            if (!Db::getInstance()->Execute('
    INSERT INTO `' . bqSQL(_DB_PREFIX_ . $this->table) . '_lang`
    (
    `' . bqSQL($this->identifier) . '`,
    `id_lang`,
    `title`,
    `paragraph`,
    `content`,
    `meta_description`,
    `meta_keywords`,
    `meta_title`,
    `link_rewrite`,
    `actif_langue`
    )
    ' . $sql_values)) {
                return false;
            }
        }

        return true;
    }

    public function deleteTablesBdd()
    {
        if (!Db::getInstance()->Execute('
        DROP TABLE IF EXISTS `' . bqSQL(_DB_PREFIX_ . $this->table) . '`
        ')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        DROP TABLE IF EXISTS `' . bqSQL(_DB_PREFIX_ . $this->table) . '_lang`
        ')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        DROP TABLE IF EXISTS `' . bqSQL(_DB_PREFIX_ . $this->table) . '_product`
        ')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        DROP TABLE IF EXISTS `' . bqSQL(_DB_PREFIX_ . $this->table) . '_newslink`
        ')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        DROP TABLE IF EXISTS `' . bqSQL(_DB_PREFIX_ . $this->table) . '_popuplink`
        ')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        DROP TABLE IF EXISTS `' . bqSQL(_DB_PREFIX_) . 'prestablog_rate`
        ')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
        DROP TABLE IF EXISTS `' . bqSQL(_DB_PREFIX_) . 'prestablog_color`
        ')) {
            return false;
        }

        return true;
    }

    public function changeEtat($field)
    {
        if (!Db::getInstance()->Execute('
        UPDATE `' . bqSQL(_DB_PREFIX_ . $this->table) . '`
        SET `' . pSQL($field) . '`=CASE `' . pSQL($field) . '` WHEN 1 THEN 0 WHEN 0 THEN 1 END
        WHERE `' . bqSQL($this->identifier) . '`=' . (int) $this->id)) {
            return false;
        }

        return true;
    }

    public function razEtatLangue($id_news)
    {
        if (!Db::getInstance()->Execute('
        UPDATE `' . bqSQL(_DB_PREFIX_ . $this->table) . '_lang` SET `actif_langue` = 0
        WHERE `' . bqSQL($this->identifier) . '`= ' . (int) $id_news)) {
            return false;
        }

        return true;
    }

    public function changeActiveLangue($id_news, $id_lang)
    {
        if (!Db::getInstance()->Execute('
        UPDATE `' . bqSQL(_DB_PREFIX_ . $this->table) . '_lang` SET `actif_langue` = 1
        WHERE `' . bqSQL($this->identifier) . '`= ' . (int) $id_news . '
        AND `id_lang` = ' . (int) $id_lang)) {
            return false;
        }

        return true;
    }

    public function incrementRead($id_news, $id_lang)
    {
        if (!Db::getInstance()->Execute('
        UPDATE `' . bqSQL(_DB_PREFIX_ . $this->table) . '_lang` SET `read` = (`read` + 1)
        WHERE `' . bqSQL($this->identifier) . '`= ' . (int) $id_news . '
        AND `id_lang` = ' . (int) $id_lang)) {
            return false;
        }

        return true;
    }

    public static function getRead($id_news, $id_lang)
    {
        $row = Db::getInstance()->getRow('
        SELECT `read`
        FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_lang`
        WHERE `' . bqSQL(self::$identifier_static) . '`= ' . (int) $id_news . '
        AND `id_lang` = ' . (int) $id_lang);

        if (isset($row['read'])) {
            return (int) $row['read'];
        }

        return false;
    }
}
