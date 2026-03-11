<?php
/**
* 2012-2015 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Pixel Tracking © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2015 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Facebook Pixel Tracking PrestaShop 1.5.x and 1.6.x Module
* @version   1.1.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      24-05-2016
*/

class PdGA4PRegistrationModel extends ObjectModel
{
    public $id_customer;
    public $registered;
    public $registered_send;
    public $date_add = '0000-00-00 00:00:00';
    public $date_upd = '0000-00-00 00:00:00';

    public static $definition = array(
        'table' => 'pdgoogleanalytycs4pro_registration',
        'primary' => 'id_customer',
        'multilang' => false,
        'fields' => array(
            'id_customer' =>     array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'registered' =>      array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'registered_send' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false)
        )
    );

    public function __construct($id_customer)
    {
        parent::__construct($id_customer);
    }

    public function add($autodate = true, $null_values = false)
    {
        return parent::add($autodate, $null_values);
    }

    public function delete()
    {
        if ((int)$this->id_customer === 0) {
            return false;
        }
        return parent::delete();
    }

    public function update($null_values = false)
    {
        if ((int)$this->id_customer === 0) {
            return false;
        }
        return parent::update($null_values);
    }

    public static function getRegisteredValueByIdCustomer($id_customer)
    {
        return Db::getInstance()->getValue('
            SELECT `registered` FROM `'._DB_PREFIX_.'pdgoogleanalytycs4pro_registration` 
            WHERE `id_customer` = '.(int)$id_customer.'
            AND `registered_send` = 0
        ');
    }

    public static function installDB()
    {
        return Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdgoogleanalytycs4pro_registration` (
                `id_customer` int(11) unsigned NOT NULL,
                `registered` tinyint(1) NOT NULL DEFAULT \'0\',
                `registered_send` tinyint(1) NOT NULL DEFAULT \'0\',
                `date_add` datetime,
                `date_upd` datetime,
                PRIMARY KEY (`id_customer`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');
    }

    public static function uninstallDB()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pdgoogleanalytycs4pro_registration`');
    }
}
