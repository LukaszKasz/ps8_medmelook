<?php
/**
* 2012-2015 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Merchant Center Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2015 Patryk Marek - PrestaDev.pl
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @link      http://prestadev.pl
* @package   PD Google Merchant Center Pro - PrestaShop 1.5.x and 1.6.x Module
* @version   2.2.0
* @date      04-03-2016
*/

class GoogleMerchantCenterProModelTaxonomy extends ObjectModel
{
    public $imported = 0;
    public $import = 0;

    public $taxonomy_lang;
    public $languages;
    public $countries;
    public $currencies;
    
    public $date_add = '0000-00-00 00:00:00';

                
    public static $definition = array(
        'table' => 'pdgooglemerchantcenterpro_taxonomy',
        'primary' => 'id_pdgooglemerchantcenterpro_taxonomy',
        'multilang_shop' => false,
        'fields' => array(
            'taxonomy_lang' =>     array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'languages' =>         array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'currencies' =>        array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'countries' =>         array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'import' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'imported' =>          array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'date_add' =>          array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            
        ),
     );

    public function add($autodate = false, $null_values = false)
    {
        return parent::add($autodate, $null_values);
    }

    public function delete()
    {
        if ((int)$this->id === 0) {
            return false;
        }

        return parent::delete();
    }

    public function update($autodate = false, $null_values = false)
    {
        if ((int)$this->id === 0) {
            return false;
        }

        return parent::update($autodate, $null_values);
    }


    /**
    * Creates tables
    */
    public static function createTables()
    {
        return Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy` (
                `id_pdgooglemerchantcenterpro_taxonomy` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `taxonomy_lang` varchar(5) NOT NULL,
                `languages` text NOT NULL,
                `currencies` text NOT NULL,
                `countries` text NOT NULL,
                `imported` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `import` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `date_add` datetime,
                PRIMARY KEY (`id_pdgooglemerchantcenterpro_taxonomy`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');
    }

    public static function dropTables()
    {
        $sql = 'DROP TABLE IF EXISTS
                `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy`';

        return Db::getInstance()->execute($sql);
    }

    public static function createTablesTaxonomyData()
    {
        return Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_data` (
                `id_taxonomy_data` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `value` text NOT NULL,
                `lang` varchar(5) NOT NULL,
                PRIMARY KEY (`id_taxonomy_data`), KEY `lang` (`lang`), FULLTEXT KEY `fulltext_index` (`value`)
            ) ENGINE=MyISAM DEFAULT CHARSET=UTF8;
        ');
    }

    public static function dropTablesTaxonomyData()
    {
        $sql = 'DROP TABLE IF EXISTS
                `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_data`';

        return Db::getInstance()->execute($sql);
    }

    public static function addTaxonomyCorelations()
    {
        $return = false;
        $module = Module::getInstanceByName('pdgooglemerchantcenterpro');
        foreach ($module->googleTaxonomiesCorelations as $taxonomy_lang => $v) {
            $data = '(\''.pSQL($taxonomy_lang).'\',\''.pSQL($v['languages']).'\',\''.pSQL($v['currencies']).'\',\''.pSQL($v['countries']).'\',0,0,\''.'0000-00-00 00:00:00'.'\')';
           
            $return = Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy` (`taxonomy_lang`, `languages`, `currencies`, `countries`, `imported`, `import`, `date_add`)
            VALUES '.$data);
        }
        return $return;
    }


    /**
    * Creates tables
    */
    public static function createTablesTaxonomyCategory()
    {
        return Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_category` (
                `id_category` int(11) NOT NULL,
                `txt_taxonomy` text NOT NULL,
                `lang` varchar(5) NOT NULL,
                KEY `id_category` (`id_category`,`lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');
    }

    public static function dropTablesTaxonomyCategory()
    {
        $sql = 'DROP TABLE IF EXISTS
                `'._DB_PREFIX_.'pdgooglemerchantcenterpro_taxonomy_category`';

        return Db::getInstance()->execute($sql);
    }
}
