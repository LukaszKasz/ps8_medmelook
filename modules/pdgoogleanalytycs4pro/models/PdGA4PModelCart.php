<?php
/**
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Analytycs 4 Pro 1.6.x and 1.7.x Module © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek @ PrestaDev.pl
* @license   Do not edit, modify or copy this file, if you wish to customize it, contact us at info@prestadev.pl.
* @link      http://prestadev.pl
* @package   PD Google Analytycs 4 Pro 1.6.x and 1.7.x Module
* @version   1.0.2
* @date      01-05-2021
*/



class PdGA4PModelCart extends ObjectModel
{
    public $id_cart;
    public $id_order;
    public $client_id;
    public $session_id;
    public $order_send;
    public $refund_send;
    public $to_refund;
    public $date_add = '0000-00-00 00:00:00';
    public $date_upd = '0000-00-00 00:00:00';

    public static $definition = array(
        'table' => 'pdgoogleanalytycs4pro_cart',
        'primary' => 'id_cart',
        'multilang' => false,
        'fields' => array(
            'id_cart' =>        array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'id_order' =>        array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'client_id' =>       array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false),
            'session_id' =>       array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false),
            'order_send' =>      array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'refund_send' =>     array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'to_refund' =>      array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false)
        )
    );

    public function __construct($id_cart = false)
    {
        parent::__construct($id_cart);
    }

    public function add($autodate = true, $null_values = false)
    {
        return parent::add($autodate, $null_values);
    }

    public function delete()
    {
        if ((int)$this->id_cart === 0) {
            return false;
        }
        return parent::delete();
    }

    public function update($null_values = false)
    {
        if ((int)$this->id_cart === 0) {
            return false;
        }
        return parent::update($null_values);
    }


    public static function getGAClientIdByIdCart($id_cart)
    {
        return Db::getInstance()->getValue(
            '
            SELECT `client_id`
            FROM `'._DB_PREFIX_.'pdgoogleanalytycs4pro_cart`
            WHERE `id_cart` = '.(int)$id_cart
        );
    }

    public static function getGASessionIdByIdCart($id_cart)
    {
        return Db::getInstance()->getValue(
            '
            SELECT `session_id`
            FROM `'._DB_PREFIX_.'pdgoogleanalytycs4pro_cart`
            WHERE `id_cart` = '.(int)$id_cart
        );
    }

    public static function getCartToSendByIdCart($id_cart)
    {
        $res = Db::getInstance()->getRow(
            '
            SELECT `order_send`
            FROM `'._DB_PREFIX_.'pdgoogleanalytycs4pro_cart`
            WHERE `id_cart` = '.(int)$id_cart
        );
        if (isset($res['order_send'])) {
            return $res['order_send'];
        } else {
            return 2;
        }
    }

    public static function getCartIdToSendByIdCustomer($id_customer)
    {
        return (int)Db::getInstance()->getValue('
            SELECT MAX(pd.`id_cart`)
            FROM `'._DB_PREFIX_.'orders` o
            LEFT JOIN `'._DB_PREFIX_.'pdgoogleanalytycs4pro_cart` pd
                ON pd.`id_cart` = o.`id_cart`
            WHERE o.`id_customer` = '.(int)$id_customer.'
                AND pd.`order_send` = 0
        ');
    }


    public static function getLastCartIdByIdCustomer($id_customer)
    {
        return (int)Db::getInstance()->getValue(
            '
            SELECT MAX(pd.`id_cart`)
            FROM `'._DB_PREFIX_.'orders` o
            LEFT JOIN `'._DB_PREFIX_.'pdgoogleanalytycs4pro_cart` pd
                ON pd.`id_cart` = o.`id_cart`
            WHERE o.`id_customer` = '.(int)$id_customer
        );
    }


    public static function getCartsToSendRefundByIdCart($id_cart)
    {
        $res = Db::getInstance()->getRow(
            '
            SELECT `refund_send`
            FROM `'._DB_PREFIX_.'pdgoogleanalytycs4pro_cart`
            WHERE `id_cart` = '.(int)$id_cart
        );
        if (isset($res['refund_send'])) {
            return $res['refund_send'];
        } else {
            return 2;
        }
    }

    public static function installDB()
    {
        return Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdgoogleanalytycs4pro_cart` (
                `id_cart` int(11) unsigned NOT NULL,
                `id_order` int(11) unsigned NOT NULL,
                `client_id` varchar(64) NOT NULL,
                `session_id` varchar(64) NOT NULL,
                `order_send` tinyint(1) NOT NULL DEFAULT \'0\',
                `refund_send` tinyint(1) NOT NULL DEFAULT \'0\',
                `to_refund` tinyint(1) NOT NULL DEFAULT \'0\',
                `date_add` datetime,
                `date_upd` datetime,
                PRIMARY KEY (`id_cart`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');
    }

    public static function uninstallDB()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pdgoogleanalytycs4pro_cart`');
    }
}
