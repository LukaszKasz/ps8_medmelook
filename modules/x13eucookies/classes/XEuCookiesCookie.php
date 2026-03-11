<?php

use XEuCookiesCookie as GlobalXEuCookiesCookie;

/**
 * @author    x13.pl <x13@x13.pl>
 * @copyright Copyright (c) 2018-2024 - www.x13.pl
 * @license   Commercial license, only to use on restricted domains
 */
class XEuCookiesCookie extends ObjectModel
{
    public $id;
    public $id_xeucookies_cookie;
    public $id_xeucookies_cookie_category;
    public $position;
    public $deletable = 1;
    public $active = 1;
    public $name;
    public $details;
    public $expiration;
    public $provider;
    public $provider_url;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'x13eucookies_cookie',
        'primary' => 'id_xeucookies_cookie',
        'multilang' => true,
        'multishop' => true,
        'fields' => [
            'id_xeucookies_cookie' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_xeucookies_cookie_category' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'name' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'lang' => false,
            ],
            'provider' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
            ],
            'provider_url' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
            ],
            'details' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
            ],
            'expiration' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
            ],
            'position' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'deletable' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
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

    public static function getByCategoryId($categoryId, $idLang = null)
    {
        if ($idLang === null) {
            $idLang = Context::getContext()->language->id;
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(static::$definition['table'], 'c');
        $sql->leftJoin(static::$definition['table'] . '_lang', 'cl', 'c.' . static::$definition['primary'] . ' = cl.' . static::$definition['primary'] . '');
        $sql->where('c.' . static::$definition['primary'] . '_category = ' . (int) $categoryId);
        $sql->where('cl.id_lang = ' . (int) $idLang);
        $sql->where('c.active = 1');
        $sql->orderBy('c.position ASC');

        return Db::getInstance()->executeS($sql);
    }

    public static function getDisallowedCookies($acceptedCategories)
    {
        $sql = new DbQuery();
        $sql->select('c.id_xeucookies_cookie, c.id_xeucookies_cookie_category, c.name');
        $sql->from(static::$definition['table'], 'c');

        if (!empty($acceptedCategories)) {
            $sql->where('c.id_xeucookies_cookie_category NOT IN (' . implode(',', array_map('intval', $acceptedCategories)) . ')');
        }

        $requiredCookiesIds = XEuCookiesCookie::getRequiredCookiesIds();

        if (!empty($requiredCookiesIds)) {
            $sql->where('c.id_xeucookies_cookie NOT IN (' . implode(',', array_map('intval', $requiredCookiesIds)) . ')');
        }

        $sql->where('c.deletable = 1');
        $sql->where('c.active = 1');

        return Db::getInstance()->executeS($sql);
    }

    public static function getRequiredCookiesIds()
    {
        $requiredCategories = XEuCookiesCookieCategory::getRequiredCategories();
        $sql = new DbQuery();
        $sql->select('c.id_xeucookies_cookie');
        $sql->from(static::$definition['table'], 'c');
        $sql->where('c.id_xeucookies_cookie_category IN (' . implode(',', array_map('intval', $requiredCategories)) . ')');
        $sql->where('c.active = 1');
        $sql->orderBy('c.position ASC');

        $result = Db::getInstance()->executeS($sql);

        if (empty($result)) {
            return [];
        }

        return array_column($result, 'id_xeucookies_cookie');
    }
}
