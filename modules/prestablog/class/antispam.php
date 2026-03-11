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

class AntiSpamClass extends ObjectModel
{
    public $id;
    public $id_shop = 1;
    public $question;
    public $reply;
    public $checksum;
    public $actif = 1;

    protected $table = 'prestablog_antispam';
    protected $identifier = 'id_prestablog_antispam';

    protected static $table_static = 'prestablog_antispam';
    protected static $identifier_static = 'id_prestablog_antispam';

    public static $definition = [
        'table' => 'prestablog_antispam',
        'primary' => 'id_prestablog_antispam',
        'multilang' => true,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'actif' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'question' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'required' => true,
                'size' => 255,
            ],
            'reply' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'required' => true,
                'size' => 255,
            ],
            'checksum' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 32,
            ],
        ],
    ];

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
                $lg = (int) $language['id_lang'];
                foreach (array_keys($rules['validateLang']) as $field) {
                    if (Tools::getIsset($field . '_' . $lg)) {
                        $object->{$field}[$lg] = Tools::getValue($field . '_' . $lg);
                    }
                }
            }
        }
    }

    public function registerTablesBdd()
    {
        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . bqSQL($this->table) . '` (
        `' . bqSQL($this->identifier) . '` int(10) unsigned NOT NULL auto_increment,
        `id_shop` int(10) unsigned NOT NULL,
        `actif` tinyint(1) NOT NULL DEFAULT \'1\',
        PRIMARY KEY (`' . bqSQL($this->identifier) . '`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . bqSQL($this->table) . '_lang` (
        `' . bqSQL($this->identifier) . '` int(10) unsigned NOT NULL,
        `id_lang` int(10) unsigned NOT NULL,
        `question` varchar(255) NOT NULL,
        `reply` varchar(255) NOT NULL,
        `checksum` varchar(32) NOT NULL,
        PRIMARY KEY (`' . bqSQL($this->identifier) . '`, `id_lang`))
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'prestablog_antispam`
        ADD KEY `id_shop` (`id_shop`),
        ADD KEY `actif` (`actif`)')) {
            return false;
        }

        return true;
    }

    public function deleteTablesBdd()
    {
        if (!Db::getInstance()->Execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . bqSQL($this->table) . '`
            ')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . bqSQL($this->table) . '_lang`
            ')) {
            return false;
        }

        return true;
    }

    public static function getListe($id_lang = null, $only_actif = 0)
    {
        $context = Context::getContext();
        $multiboutique_filtre = 'AND c.`id_shop` = ' . (int) $context->shop->id;

        $actif = '';
        if ($only_actif) {
            $actif = 'AND c.`actif` = 1';
        }

        if (empty($id_lang)) {
            $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }

        $liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT c.*, cl.*
            FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '` c
            JOIN `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_lang` cl
                ON (c.`' . bqSQL(self::$identifier_static) . '` = cl.`' . bqSQL(self::$identifier_static) . '`)
            WHERE cl.id_lang = ' . (int) $id_lang . '
            ' . $multiboutique_filtre . '
            ' . $actif);

        return $liste;
    }

    public static function getAntiSpamByChecksum($checksum)
    {
        $context = Context::getContext();
        $multiboutique_filtre = 'AND c.`id_shop` = ' . (int) $context->shop->id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
            SELECT c.*, cl.*
            FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '` c
            JOIN `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_lang` cl
                ON (c.`' . bqSQL(self::$identifier_static) . '` = cl.`' . bqSQL(self::$identifier_static) . '`)
            WHERE cl.checksum = \'' . pSQL(trim($checksum)) . '\'
            ' . $multiboutique_filtre . ';');
    }

    public function changeEtat($field)
    {
        if (!Db::getInstance()->Execute('
        UPDATE `' . bqSQL(_DB_PREFIX_ . $this->table) . '`
        SET `' . bqSQL($field) . '` = CASE `' . bqSQL($field) . '` WHEN 1 THEN 0 WHEN 0 THEN 1 END
        WHERE `' . bqSQL($this->identifier) . '` = ' . (int) $this->id)) {
            return false;
        }

        return true;
    }

    public function reloadChecksum()
    {
        $liste = Db::getInstance()->ExecuteS('
            SELECT * FROM `' . bqSQL(_DB_PREFIX_ . self::$table_static) . '_lang`
        ');

        foreach ($liste as $antispam) {
            $cs = md5((int) $antispam[$this->identifier] . (int) $antispam['id_lang'] . _COOKIE_KEY_ . $antispam['question']);

            Db::getInstance()->Execute('
                UPDATE `' . bqSQL(_DB_PREFIX_ . $this->table) . '_lang`
                    SET `checksum` = \'' . pSQL($cs) . '\'
                WHERE `' . bqSQL($this->identifier) . '` = ' . (int) $antispam[$this->identifier] . '
                    AND `id_lang` = ' . (int) $antispam['id_lang']);
        }
    }
}
