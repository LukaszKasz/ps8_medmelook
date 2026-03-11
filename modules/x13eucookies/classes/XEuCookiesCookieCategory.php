<?php

/**
 * @author    x13.pl <x13@x13.pl>
 * @copyright Copyright (c) 2018-2024 - www.x13.pl
 * @license   Commercial license, only to use on restricted domains
 */
class XEuCookiesCookieCategory extends ObjectModel
{
    public $id;
    public $id_xeucookies_cookie_category;
    public $type = 'custom';
    public $blocked_modules;
    public $position;
    public $required;
    public $active = 1;
    public $name;
    public $details;
    public $js_with_consent;
    public $js_without_consent;
    // public $smarty_with_consent;
    // public $smarty_without_consent;
    public $gtm_consent_type;
    public $microsoft_consent_type;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'x13eucookies_cookie_category',
        'primary' => 'id_xeucookies_cookie_category',
        'multilang' => true,
        'multishop' => true,
        'fields' => [
            'id_xeucookies_cookie_category' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'type' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'lang' => false,
            ],
            'gtm_consent_type' => [
                'type' => self::TYPE_STRING,
                'required' => false,
                'lang' => false,
            ],
            'microsoft_consent_type' => [
                'type' => self::TYPE_STRING,
                'required' => false,
                'lang' => false,
            ],
            'name' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'lang' => true,
            ],
            'blocked_modules' => [
                'type' => self::TYPE_STRING,
                'required' => false,
                'lang' => false,
            ],
            'details' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
            ],
            'js_with_consent' => [
                'type' => self::TYPE_STRING,
            ],
            'js_without_consent' => [
                'type' => self::TYPE_STRING,
            ],
            // 'smarty_with_consent' => [
            //     'type' => self::TYPE_STRING,
            // ],
            // 'smarty_without_consent' => [
            //     'type' => self::TYPE_STRING,
            // ],
            'required' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'position' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'active' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        if ($this->id) {
            // n/a
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->position = $this->getHighestPosition();
        $ret = parent::add($autodate, $null_values);

        return $ret;
    }

    public function update($null_values = false)
    {
        if ($this->active == 0 && $this->required == 1) {
            throw new PrestaShopException(Translate::getModuleTranslation('x13eucookies', 'You cannot disable required cookie category', 'x13eucookies'));
        }

        $ret = parent::update($null_values);

        return $ret;
    }
    public function delete()
    {
        if ((int) $this->id === 0) {
            return false;
        }

        return parent::delete() && $this->cleanPositions();
    }

    public static function getHighestPosition()
    {
        return Db::getInstance()->getValue('
            SELECT IFNULL(MAX(position),0)+1
            FROM `' . _DB_PREFIX_ . static::$definition['table'] . '`');
    }

    public function cleanPositions()
    {
        $result = true;
        $result &= Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . static::$definition['table'] . '` a 
            JOIN (SELECT @rownum := 0) b 
            SET a.position = @rownum := @rownum + 1 
            ORDER BY a.position;
        ');

        return (bool) $result;
    }

    public static function getDefaultCategories()
    {
        $query = new DbQuery();
        $query->select('id_xeucookies_cookie_category');
        $query->from(static::$definition['table']);
        $query->where('type IN ("nessesary", "statistical", "marketing", "preference")');

        return array_column(Db::getInstance()->executeS($query), 'id_xeucookies_cookie_category');
    }

    public static function getAll($idLang = null, $withCookies = false, $activeOnly = true)
    {
        $idLang = (int) $idLang ?: Context::getContext()->language->id;
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table'], 'a');
        $query->leftJoin(static::$definition['table'] . '_lang', 'l', 'l.' . static::$definition['primary'] . ' = a.' . static::$definition['primary'] . ' AND l.id_lang = ' . $idLang);
        $query->orderBy('position ASC');

        if ($activeOnly) {
            $query->where('a.active = 1');
        }

        $categories = Db::getInstance()->executeS($query);

        if (!empty($categories) && $withCookies) {
            foreach ($categories as &$category) {
                $category['cookies'] = XEuCookiesCookie::getByCategoryId($category['id_xeucookies_cookie_category'], $idLang);
            }
        }

        return $categories;
    }

    public static function getNameById($id)
    {
        $query = new DbQuery();
        $query->select('name');
        $query->from(static::$definition['table'] . '_lang');
        $query->where('id_lang = ' . Context::getContext()->language->id);
        $query->where(static::$definition['primary'] . ' = ' . (int) $id);

        return Db::getInstance()->getValue($query);
    }

    public static function getIdByType($type)
    {
        $query = new DbQuery();
        $query->select(static::$definition['primary']);
        $query->from(static::$definition['table']);
        $query->where('type = "' . pSQL($type) . '"');

        return Db::getInstance()->getValue($query);
    }

    public static function getBlockedModules($allowedCategories)
    {
        $query = new DbQuery();
        $query->select('a.blocked_modules, a.id_xeucookies_cookie_category');
        $query->from(static::$definition['table'], 'a');
        $query->leftJoin(static::$definition['table'] . '_shop', 's', 's.' . static::$definition['primary'] . ' = a.' . static::$definition['primary'] . ' AND s.id_shop = ' . (int) Context::getContext()->shop->id);
        $query->where('a.active = 1');
        $query->where('a.required = 0');

        if (!empty($allowedCategories)) {
            $query->where('a.id_xeucookies_cookie_category NOT IN (' . implode(',', array_map('intval', $allowedCategories)) . ')');
        }

        $result = Db::getInstance()->executeS($query);
        if (empty($result)) {
            return [];
        }

        $output = [];
        foreach ($result as $modules) {
            $output[$modules['id_xeucookies_cookie_category']] = [
                'id_xeucookies_cookie_category' => $modules['id_xeucookies_cookie_category'],
                'blocked_modules' => !empty($modules['blocked_modules']) ? json_decode($modules['blocked_modules'], true) : [],
            ];
        }

        return $output;
    }

    public static function getRequiredCategories()
    {
        $query = new DbQuery();
        $query->select('a.id_xeucookies_cookie_category');
        $query->from(static::$definition['table'], 'a');
        $query->leftJoin(static::$definition['table'] . '_shop', 's', 's.' . static::$definition['primary'] . ' = a.' . static::$definition['primary'] . ' AND s.id_shop = ' . (int) Context::getContext()->shop->id);
        $query->where('a.active = 1');
        $query->where('a.required = 1');

        $result = Db::getInstance()->executeS($query);
        if (empty($result)) {
            return [];
        }

        return array_column($result, 'id_xeucookies_cookie_category');
    }
}
