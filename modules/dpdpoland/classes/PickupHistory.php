<?php

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Class DpdPolandPickupHistory
 */
class DpdPolandPickupHistory extends DpdPolandObjectModel
{                
    /**
     * @var int Courier order ID, incremental
     */
	public $id_pickup_history;

    /**
     * @var string Order number
     */
    public $order_number;

    /**
     * @var string Sender address
     */
	public $sender_address;

    /**
     * @var string Sender company
     */
	public $sender_company;

    /**
     * @var string Sender name
     */
	public $sender_name;

    /**
     * @var string Sender phone
     */
	public $sender_phone;

    /**
     * @var datetime Pickup date
     */
	public $pickup_date;

    /**
     * @var string Pickup time range
     */
	public $pickup_time;

    /**
     * @var string Type
     */
	public $type;

    /**
     * @var int envelope
     */
	public $envelope;

    /**
     * @var int package
     */
	public $package;

    /**
     * @var decimal Package weight all
     */
	public $package_weight_all;

    /**
     * @var decimal Package heaviest weight
     */
	public $package_heaviest_weight;
    
    /**
     * @var decimal Package heaviest width
     */
	public $package_heaviest_width;

    /**
     * @var decimal Package heaviest length
     */
	public $package_heaviest_length;

    /**
     * @var decimal Package heaviest height
     */
	public $package_heaviest_height;
    
    /**
     * @var int Pallet
     */
	public $pallet;

    /**
     * @var decimal Pallet weight
     */
	public $pallet_weight;

    /**
     * @var decimal Pallet heaviest weight
     */
	public $pallet_heaviest_weight;
    
    /**
     * @var decimal Pallet heaviest height
     */
	public $pallet_heaviest_height;
    
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
		'table' => _DPDPOLAND_PICKUP_HISTORY_DB_,
		'primary' => 'id_pickup_history',
		'multilang' => false,
		'multishop' => true,
		'fields' => array(
            'order_number'   =>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'sender_address' =>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'sender_company' =>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
            'sender_name'    =>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
            'sender_phone'  =>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
            'pickup_date'  =>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'pickup_time'  =>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
            'type'     =>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
            'envelope'  =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'package'  =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'pallet'  =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_shop'  =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
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
	public static function getList($order_by, $order_way, $filter, $start, $pagination)
	{
		 return Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_._DPDPOLAND_PICKUP_HISTORY_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
			'.$filter.'
			ORDER BY `'.bqSQL($order_by).'` '.pSQL(Validate::isOrderWay($order_way) ? $order_way : 'ASC').
			($start !== null && $pagination !== null ? ' LIMIT '.(int)$start.', '.(int)$pagination : '')
		);
	}


    /**
     * Calculates how many addresses are saved in current shop
     *
     * @return int count
     */
    public static function getCourierOrderCount()
    {
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(`id_pickup_history`)
            FROM `'._DB_PREFIX_._DPDPOLAND_PICKUP_HISTORY_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
        ');
    }
}