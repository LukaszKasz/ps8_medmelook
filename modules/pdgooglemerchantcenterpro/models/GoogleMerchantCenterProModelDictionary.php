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

class GoogleMerchantCenterProModelDictionary extends ObjectModel
{
    public $active = 1;
    public $source_word;
    public $destination_word;
    public $date_add = '0000-00-00 00:00:00';
    public $date_upd = '0000-00-00 00:00:00';
                
    public static $definition = array(
        'table' => 'pdgooglemerchantcenterpro_dictionary',
        'primary' => 'id_pdgooglemerchantcenterpro_dictionary',
        'multilang_shop' => false,
        'fields' => array(
            'active' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'source_word' =>        array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'destination_word' =>   array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'date_add' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
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

    public function update($null_values = false)
    {
        if ((int)$this->id === 0) {
            return false;
        }

        return parent::update($null_values);
    }


    /**
    * Creates tables
    */
    public static function createTables()
    {
        return Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdgooglemerchantcenterpro_dictionary` (
                `id_pdgooglemerchantcenterpro_dictionary` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `source_word` text,
                `destination_word` text,
                `active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                `date_add` datetime,
                `date_upd` datetime,
                PRIMARY KEY (`id_pdgooglemerchantcenterpro_dictionary`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');
    }

    public static function dropTables()
    {
        $sql = 'DROP TABLE IF EXISTS
                `'._DB_PREFIX_.'pdgooglemerchantcenterpro_dictionary`';

        return Db::getInstance()->execute($sql);
    }
}
