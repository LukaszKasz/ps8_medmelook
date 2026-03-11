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

/**
 * Class DpdPolandCSV Responsible for prices import management
 */
class DpdPolandCSV extends DpdPolandObjectModel
{
    /**
     * @var int Shop ID
     */
	public $id_shop;

    /**
     * @var datetime Date when price rule was created
     */
	public $date_add;

    /**
     * @var datetime Date when price rule was updated
     */
	public $date_upd;

    /**
     * @var int CSV rule ID
     */
	public $id_csv;

    /**
     * @var string Country ISO code
     */
	public $iso_country;

    /**
     * @var float Price from which price rule is applied
     */
	public $price_from;

    /**
     * @var float Price to which price rule is applied
     */
	public $price_to;

    /**
     * @var float Weight from which price rule is applied
     */
	public $weight_from;

    /**
     * @var float Weight to which price rule is applied
     */
	public $weight_to;

    /**
     * @var float Shipment price
     */
	public $parcel_price;

    /**
     * @var int DPD service (carrier) ID
     */
	public $id_carrier;

    /**
     * @var float Additional COD carrier price
     */
	public $cod_price;

    /**
     * Country column number in CSV file
     */
	const COLUMN_COUNTRY 			= 0;

    /**
     * Price From column number in CSV file
     */
	const COLUMN_PRICE_FROM 		= 1;

    /**
     * Price To column number in CSV file
     */
	const COLUMN_PRICE_TO 			= 2;

    /**
     * Weight From column number in CSV file
     */
	const COLUMN_WEIGHT_FROM 		= 3;

    /**
     * Weight To column number in CSV file
     */
	const COLUMN_WEIGHT_TO 			= 4;

    /**
     * Shipment price column number in CSV file
     */
	const COLUMN_PARCEL_PRICE 		= 5;

    /**
     * Carrier ID column number in CSV file
     */
	const COLUMN_CARRIER 			= 6;

    /**
     * COD additional price column number in CSV file
     */
	const COLUMN_COD_PRICE			= 7;

    /**
     * File upload field name
     */
	const CSV_FILE 					= 'DPD_GEOPOST_CSV_FILE';

    /**
     * @var array Class variables and their validation types
     */
	public static $definition = array(
		'table' => _DPDPOLAND_PRICE_RULE_DB_,
		'primary' => 'id_csv',
		'multilang' => false,
		'multishop' => true,
		'fields' => array(
			'id_csv'			=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_shop'			=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'iso_country'		=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'price_from'		=>	array('type' => self::TYPE_STRING, 'validate' => 'isFloat'),
			'price_to'			=>	array('type' => self::TYPE_STRING, 'validate' => 'isFloat'),
			'weight_from'		=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'weight_to'			=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'parcel_price'		=>	array('type' => self::TYPE_STRING, 'validate' => 'isFloat'),
			'cod_price'			=>	array('type' => self::TYPE_STRING, 'validate' => 'isFloat'),
			'id_carrier'		=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'date_add'			=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd'			=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);

    /**
     * Collects data from database about price rules
     *
     * @param string|int $start From which element data should be taken
     * @param string|int $limit How many elements should be taken
     * @return array|false|mysqli_result|null|PDOStatement|resource Price rules data
     */
	public static function getAllData($start = '', $limit = '')
	{
		return DB::getInstance()->executeS('
			SELECT `id_csv`, `iso_country`, `price_from`, `price_to`, `weight_from`, `weight_to`, `parcel_price`,
				`cod_price`, `id_carrier`
			FROM `'._DB_PREFIX_._DPDPOLAND_PRICE_RULE_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
			'.($start && $limit ? 'LIMIT '.(int)$start.', '.(int)$limit : '')
		);
	}

    /**
     * Deletes all price rules for current shop
     *
     * @return bool Price rules deleted successfully
     */
	public static function deleteAllData()
	{
		return DB::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_._DPDPOLAND_PRICE_RULE_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
		');
	}

    /**
     * Collects price rules data of current shop
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource Price rules data
     */
	public static function getCSVData()
	{
		return DB::getInstance()->executeS('
			SELECT `iso_country`, `price_from`, `price_to`, `weight_from`, `weight_to`, `parcel_price`, `id_carrier`,
				`cod_price`
			FROM `'._DB_PREFIX_._DPDPOLAND_PRICE_RULE_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
		');
	}

    /**
     * Returns carrier price according to cart products / parameters
     *
     * @param float $total_weight Sum of products weight
     * @param int $id_carrier Carrier ID
     * @param Cart $cart Cart object
     * @return bool|int|float Carrier price
     */
	public static function getPrice($total_weight, $id_carrier, Cart $cart)
	{
		if ($id_country = (int)Tools::getValue('id_country'))
			$iso_country = Country::getIsoById($id_country);
		else
		{
			$address = new Address((int)$cart->id_address_delivery);
			$iso_country = Country::getIsoById((int)$address->id_country);
		}
		
		if(!$iso_country)
			$iso_country = 'PL';

		$cart_total_price = $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
		$id_currency_pl = Currency::getIdByIsoCode(_DPDPOLAND_CURRENCY_ISO_, (int)Context::getContext()->shop->id);
		$currency_from = new Currency((int)$cart->id_currency);
		$currency_to = new Currency((int)$id_currency_pl);
		$cart_total_price = Tools::convertPriceFull($cart_total_price, $currency_from, $currency_to);

		$price_rules = DB::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_._DPDPOLAND_PRICE_RULE_DB_.'`
			WHERE (`iso_country` = "'.pSQL($iso_country).'" OR `iso_country` = "*")
				AND (`weight_from` <= "'.pSQL($total_weight).'"
					AND `weight_to` >= "'.pSQL($total_weight).'"
					OR `price_from` <= "'.pSQL($cart_total_price).'"
					AND `price_to` >= "'.pSQL($cart_total_price).'")
				AND `id_carrier` = "'.(int)$id_carrier.'"
				AND `id_shop` = "'.(int)Context::getContext()->shop->id.'"
		');

		if (!$price_rules)
			return false;

		$available_prices_count = count($price_rules);

		for ($i = 0; $i < $available_prices_count; $i++)
			if ($price_rules[$i]['iso_country'] != '*' && !Country::getByIso($price_rules[$i]['iso_country'])) //if country is not deleted
				unset($price_rules[$i]);

		if (!$price_rules)
			return false;

        $matching_price_rule = null;
		if (is_array($price_rules)) {
            foreach ($price_rules as $price_rule) {
                if ($price_rule['price_from'] <= $cart_total_price &&
                    $price_rule['price_to'] > $cart_total_price &&
                    $price_rule['weight_from'] <= $total_weight &&
                    $price_rule['weight_to'] > $total_weight
                ) {
                    $matching_price_rule = $price_rule;
                    break;
                }
            }
        }

        if (null == $matching_price_rule) {
            $matching_price_rule = $price_rules[0]; //accept first matching rule
        }

		if (!$matching_price_rule['cod_price'])
            $matching_price_rule['cod_price'] = 0; //CSV validation allows empty value of COD price

		$price = $matching_price_rule['parcel_price'];

		if ($id_carrier == _DPDPOLAND_STANDARD_COD_ID_ || $id_carrier == _DPDPOLAND_PUDO_COD_ID_)
			$price += $matching_price_rule['cod_price'];

		return $price;
	}
}
