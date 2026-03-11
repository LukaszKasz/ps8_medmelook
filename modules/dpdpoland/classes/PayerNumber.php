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
 * Class DpdPolandPayerNumber Responsible for payer number management
 */
class DpdPolandPayerNumber extends DpdPolandObjectModel
{
    /**
     * @var int Payer number ID
     */
	public $id_dpdpoland_payer_number;

    /**
     * @var string|int Payer number
     */
	public $payer_number;

    /**
     * @var string Payer number name
     */
	public $name;

    /**
     * @var int Shop ID
     */
	public $id_shop;

    /**
     * @var datetime Date when payer number was added
     */
	public $date_add;

    /**
     * @var datetime Date when payer number was updated
     */
	public $date_upd;

    /**
     * DpdPolandPayerNumber class constructor
     * @param null|int $id_dpdpoland_payer_number DPD payer number ID
     */
	public function __construct($id_dpdpoland_payer_number = null)
	{
		parent::__construct($id_dpdpoland_payer_number);
		$this->id_shop = (int)Context::getContext()->shop->id;
	}

    /**
     * @var array Class variables and their validation types
     */
	public static $definition = array(
		'table' => _DPDPOLAND_PAYER_NUMBERS_DB_,
		'primary' => 'id_dpdpoland_payer_number',
		'multilang_shop' => true,
		'multishop' => true,
		'fields' => array(
			'payer_number' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'name' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);

    /**
     * Checks if payer number exists in database
     *
     * @param string $number Payer number
     * @param null|int $id_shop Shop ID
     * @return bool Payer number exists in database
     */
	public static function payerNumberExists($number, $id_shop = null)
	{
		if ($id_shop === null)
			$id_shop = (int)Context::getContext()->shop->id;

		return (bool)DB::getInstance()->getValue('
			SELECT `id_dpdpoland_payer_number`
			FROM `'._DB_PREFIX_._DPDPOLAND_PAYER_NUMBERS_DB_.'`
			WHERE `payer_number` = "'.pSQL($number).'"
				AND `id_shop` = "'.(int)$id_shop.'"
		');
	}

    /**
     * Collects data of merchant payer numbers saved in database
     *
     * @param null|int $id_shop Shop ID
     * @return array|false|mysqli_result|null|PDOStatement|resource Payer numbers
     */
	public static function getPayerNumbers($id_shop = null)
	{
		if ($id_shop === null)
			$id_shop = (int)Context::getContext()->shop->id;

		return DB::getInstance()->executeS('
			SELECT `id_dpdpoland_payer_number`, `payer_number`, `name`
			FROM `'._DB_PREFIX_._DPDPOLAND_PAYER_NUMBERS_DB_.'`
			WHERE `id_shop` = "'.(int)$id_shop.'"
		');
	}
}