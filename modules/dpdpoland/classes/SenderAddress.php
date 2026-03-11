<?php
/**
* 2019 DPD Polska Sp. z o.o.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* prestashop@dpd.com.pl so we can send you a copy immediately.
*
*  @author    DPD Polska Sp. z o.o.
*  @copyright 2019 DPD Polska Sp. z o.o.
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*  International Registered Trademark & Property of DPD Polska Sp. z o.o.
*/

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Class DpdPolandSenderAddress Responsible for sender addresses management
 */
class DpdPolandSenderAddress extends DpdPolandObjectModel
{
    /**
     * @var int Sender address ID, incremental
     */
	public $id_sender_address;

    /**
     * @var string Sender address alias
     */
    public $alias;

    /**
     * @var string Sender name
     */
	public $name;

    /**
     * @var string Sender phone number
     */
	public $phone;

    /**
     * @var string Sender address
     */
	public $address;

    /**
     * @var string Sender city name
     */
	public $city;

    /**
     * @var string Sender company name
     */
	public $company;

    /**
     * @var string Sender email address
     */
	public $email;

    /**
     * @var string Sender postcode
     */
	public $postcode;

    /**
     * @var datetime Date when sender address was saved
     */
	public $date_add;

    /**
     * @var datetime Date when sender address was updated
     */
	public $date_upd;

    /**
     * @var int Shop ID
     */
	public $id_shop;

    /**
     * @var array Class variables and their validation types
     */
	public static $definition = array(
		'table' => _DPDPOLAND_SENDER_ADDRESS_DB_,
		'primary' => 'id_sender_address',
		'multilang' => false,
		'multishop' => false,
		'fields' => array(
            'alias'     =>	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'company'   =>	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'name'     =>	array('type' => self::TYPE_STRING, 'validate' => 'isName'),
            'phone'     =>	array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber'),
            'address'   =>	array('type' => self::TYPE_STRING, 'validate' => 'isAddress'),
            'city'      =>	array('type' => self::TYPE_STRING, 'validate' => 'isCityName'),
            'email'     =>	array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
            'postcode'  =>	array('type' => self::TYPE_STRING, 'validate' => 'isPostCode'),
            'id_shop'   =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'date_add'  => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd'  => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);

    /**
     * Collects list data and prepares it to be displayed
     *
     * @param string $order_by List order by criteria
     * @param string $order_way List sorting way (ascending, descending)
     * @param string $filter Criteria by which list is filtered
     * @param int $start From which element list will be displayed
     * @param int $pagination How many elements will be displayed in list
     * @return array|false|mysqli_result|null|PDOStatement|resource Collected list data
     */
	public function getList($order_by, $order_way, $filter, $start, $pagination)
	{
		$order_way = Validate::isOrderWay($order_way) ? $order_way : 'ASC';

		return Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_._DPDPOLAND_SENDER_ADDRESS_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
			'.$filter.'
			ORDER BY `'.bqSQL($order_by).'` '.pSQL($order_way).
			($start !== null && $pagination !== null ? ' LIMIT '.(int)$start.', '.(int)$pagination : '')
		);
	}

    /**
     * Collects and returns data about sender addresses saved in current shop
     *
     * @return array Sender addresses
     */
	public static function getAddresses()
    {
        $result = array();

        $addresses = Db::getInstance()->executeS('
            SELECT `id_sender_address`, `alias`
            FROM `'._DB_PREFIX_._DPDPOLAND_SENDER_ADDRESS_DB_.'`
            WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
        ');

        if (!$addresses) {
            return $result;
        }

        foreach ($addresses as $address) {
            $result[$address['id_sender_address']] = $address['alias'];
        }

        return $result;
    }

    /**
     * Calculates how many addresses are saved in current shop
     *
     * @return int Sender addresses count
     */
    public static function getAddressesCount()
    {
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(`id_sender_address`)
            FROM `'._DB_PREFIX_._DPDPOLAND_SENDER_ADDRESS_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
        ');
    }
}