<?php
/**
 * 2008 - 2018 (c) Prestablog
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

class AuthorClass extends ObjectModel
{
    public $id_author;
    public $lastname;
    public $firstname;
    public $pseudo;
    public $date;
    public $bio;
    public $email;
    public $meta_title;
    public $meta_description;
    public $permissions;

    protected $table = 'prestablog_author';
    protected $identifier = 'id_author';

    public static $table_static = 'prestablog_author';
    public static $identifier_static = 'id_author';

    public static $definition = [
        'table' => 'prestablog_author',
        'primary' => 'id_author',
        'fields' => [
            'lastname' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'firstname' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'pseudo' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'date' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true],
            'bio' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255],
            'meta_title' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'meta_description' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'permissions' => ['type' => self::TYPE_STRING, 'validate' => 'isJson'],
        ],
    ];

    public static function isTableInstalled()
    {
        $table = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SHOW TABLES LIKE \'' . bqSQL(_DB_PREFIX_ . self::$table_static) . '%\'
            ');

        if (count($table) > 0) {
            return true;
        }

        return false;
    }

    public static function checkAuthor($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `id_author`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_author`
            WHERE `id_author`= ' . (int) $id);

        return $return1;
    }

    public static function getPseudo($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `pseudo`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_author`
            WHERE `id_author`= ' . (int) $id);
        if (isset($return1[0]['pseudo'])) {
            return $return1[0]['pseudo'];
        }
    }

    public static function getBio($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `bio`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_author`
            WHERE `id_author`= ' . (int) $id);
        if (isset($return1[0]['bio'])) {
            return $return1[0]['bio'];
        }
    }

    public static function getEmail($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `email`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_author`
            WHERE `id_author`= ' . (int) $id);

        if (isset($return1[0]['email'])) {
            return $return1[0]['email'];
        }
    }

    public static function getMetaTitle($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `meta_title`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_author`
            WHERE `id_author`= ' . (int) $id);

        if (isset($return1[0]['meta_title'])) {
            return $return1[0]['meta_title'];
        }
    }

    public static function getMetaDescription($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `meta_description`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_author`
            WHERE `id_author`= ' . (int) $id);

        if (isset($return1[0]['meta_description'])) {
            return $return1[0]['meta_description'];
        }
    }

    public static function getListeEmployee()
    {
        return Db::getInstance()->ExecuteS('
            SELECT p.`id_employee`, p.`lastname`, p.`firstname`, p.`email`
            FROM `' . bqSQL(_DB_PREFIX_) . 'employee` p');
    }

    public static function addAuthor($id_employee, $firstname, $lastname, $email)
    {
        $defaultPermissions = json_encode([
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
        ]);

        return Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'prestablog_author`
            (`id_author`, `firstname`, `lastname`, `date`, `email`, `permissions`)
            VALUES
            (' . (int) $id_employee . ', \'' . pSQL($firstname) . '\', \'' . pSQL($lastname) . '\', \'' . date('Y-m-d') . '\' , \'' . pSQL($email) . '\', \'' . pSQL($defaultPermissions) . '\')'
        );
    }

    public static function editAuthor($author_id, $pseudo, $bio, $email, $meta_title, $meta_description)
    {
        return Db::getInstance()->update('prestablog_author', [
            'pseudo' => pSQL($pseudo),
            'bio' => pSQL($bio, true),
            'email' => pSQL($email),
            'meta_title' => pSQL($meta_title),
            'meta_description' => pSQL($meta_description),
        ], 'id_author = ' . (int) $author_id);
    }

    public static function getListeAuthor()
    {
        $authors = Db::getInstance()->ExecuteS('
            SELECT p.`id_author`, p.`firstname`, p.`lastname`, p.`date`, p.`email`, p.`pseudo`, p.`permissions`
            FROM `' . _DB_PREFIX_ . bqSQL('prestablog_author') . '` p');

        foreach ($authors as &$author) {
            $author['permissions'] = json_decode($author['permissions'], true);

            if (empty($author['permissions'])) {
                $author['permissions'] = [
                    'can_add_article' => 0,
                    'can_edit_article' => 0,
                    'can_delete_article' => 0,
                ];
            }
        }

        return $authors;
    }

    public static function delAuthor($id)
    {
        return Db::getInstance()->Execute('
                DELETE FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_author`
                WHERE `id_author` = ' . (int) $id);
    }

    public static function getCountArticleCreated($author_id)
    {
        // Count author article number
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
            SELECT count(DISTINCT n.id_prestablog_news) as `count`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_news` n
            WHERE n.`author_id` =  ' . (int) $author_id);

        return $value['count'];
    }

    public static function getMostRedArticle($author_id)
    {
        // Retrieve the most read article created by the author
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT nl.`read`, nl.`id_prestablog_news`, nl.`title`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_news_lang` nl
            JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_news` n ON nl.`id_prestablog_news` = n.`id_prestablog_news`
            WHERE n.`author_id` = ' . (int) $author_id . '
            ORDER BY nl.`read` DESC
            LIMIT 1');

        if (!empty($value)) {
            return $value[0]['title'];
        } else {
            return '';
        }
    }

    public static function getAuthorData($author_id)
    {
        // Retrieving author data based on author_id
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
                    SELECT n.`id_author`, n.`firstname`, n.`lastname`, n.`pseudo`, n.`email`, n.`bio`
                    FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_author` n
                    WHERE n.`id_author` = ' . (int) $author_id);

        return $value;
    }

    public static function getArticleListe($author, $active = false, $limit_start = 0, $limit_stop = null)
    {
        $context = Context::getContext();
        $id_shop = (int) $context->shop->id;

        $limit = '';
        if (!empty($limit_stop)) {
            $limit = ' LIMIT ' . (int) $limit_start . ', ' . (int) $limit_stop;
        }

        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `id_prestablog_news`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_news`
            WHERE `author_id` = ' . (int) $author . '
            AND `id_shop` = ' . $id_shop . '
            ' . ($active ? 'AND `actif` = 1' : '') . '
            ' . $limit);

        $return2 = [];
        foreach ($return1 as $value) {
            $news = new NewsClass((int) $value['id_prestablog_news']);

            if ((int) $news->id) {
                $return2[] = $value['id_prestablog_news'];
            }
        }

        return $return2;
    }

    public static function getAuthorID($news_id)
    {
        // Retrieving the author
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT n.`author_id`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_news` n
            WHERE n.`id_prestablog_news` =  ' . (int) $news_id);

        if (isset($value[0]['author_id']) && $value[0]['author_id'] != null) {
            return $value;
        } else {
            return '';
        }
    }

    public static function getAuthorName($news_id)
    {
        // Retrieving the author's name
        $authorData = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
            SELECT a.`firstname`, a.`lastname`, a.`pseudo`, a.`bio`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_news` n
            JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_author` a ON a.`id_author` = n.`author_id`
            WHERE n.`id_prestablog_news` =  ' . (int) $news_id);

        if (!empty($authorData)) {
            return $authorData;
        } else {
            return '';
        }
    }

    public static function getAuthorPseudo($news_id)
    {
        // Retrieving the author's pseudo
        $pseudo = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
            SELECT a.`pseudo`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_news` n
            JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_author` a ON a.`id_author` = n.`author_id`
            WHERE n.`id_prestablog_news` =  ' . (int) $news_id);

        if (!empty($pseudo)) {
            return $pseudo['pseudo']; // Returning just the pseudo, not the entire array
        } else {
            return '';
        }
    }

    public static function verifyAuthorSet($author_id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `firstname`
            FROM `' . _DB_PREFIX_ . bqSQL('prestablog_author') . '`
            WHERE `id_author` = ' . (int) $author_id);

        if (!empty($return1)) {
            return true;
        } else {
            return false;
        }
    }

    public function registerTablesBdd()
    {
        $sql = '
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_author` (
                `' . bqSQL($this->identifier) . '` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `lastname` varchar(255) NOT NULL,
                `firstname` varchar(255) NOT NULL,
                `pseudo` varchar(255) NOT NULL,
                `date` datetime NOT NULL,
                `bio` mediumtext,
                `meta_title` varchar(60),
                `meta_description` varchar(160),
                `email` varchar(255) NOT NULL,
                `permissions` JSON,  -- Ajout de la colonne JSON sans valeur par défaut
                PRIMARY KEY (`' . bqSQL($this->identifier) . '`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        $sql_init_permissions = 'UPDATE `' . _DB_PREFIX_ . 'prestablog_author` SET `permissions` = \'{}\' WHERE `permissions` IS NULL';
        if (!Db::getInstance()->execute($sql_init_permissions)) {
            return false;
        }

        return true;
    }

    public function deleteTablesBdd()
    {
        if (!Db::getInstance()->Execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'prestablog_author`
            ')) {
            return false;
        }

        return true;
    }

    public static function hasPermission($author_id, $permission)
    {
        $author = new self($author_id);
        $permissions = json_decode($author->permissions, true);

        return isset($permissions[$permission]) && $permissions[$permission] == 1;
    }
}
