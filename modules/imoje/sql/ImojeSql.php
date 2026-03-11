<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    imoje
 * @copyright 2022-now imoje
 * @license   GNU General Public License
 **/

class ImojeSql
{

    /**
     * @var string
     */
    const TABLE_NAME = _DB_PREFIX_ . 'imoje_transaction_list';

    /**
     * @return bool
     */
    public static function install()
    {
        $sql = [];

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'
            . _DB_PREFIX_
            . 'imoje_transaction_list` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_order` '
            . 'int(10) UNSIGNED NOT NULL, `id_transaction` varchar(255) NOT NULL, PRIMARY KEY  (`id`)) ENGINE='
            . _MYSQL_ENGINE_
            . ' DEFAULT CHARSET=utf8;';

        $valid = true;

        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @return bool
     */
    public static function uninstall()
    {

        $sql = array();

        $valid = true;

        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @param string $orderId
     *
     * @return string|void
     */
    public static function getTransactionId($orderId)
    {

        return Db::getInstance()->getValue("SELECT `" . self::TABLE_NAME . "`.`id_transaction` FROM `" . self::TABLE_NAME . "` WHERE  `" . self::TABLE_NAME . "`.`id_order` = '" . pSQL($orderId) . "';");
    }
}
