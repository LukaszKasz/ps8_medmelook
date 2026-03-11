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
 * Class DpdPolandManifest Responsible for manifests management
 */
class DpdPolandManifest extends DpdPolandObjectModel
{
    /**
     * @var int Manifest ID
     */
	public $id_manifest;

    /**
     * @var string Manifest ID retrieved via webservices
     */
	public $id_manifest_ws;

    /**
     * @var string|int Package ID
     */
	public $id_package_ws;

    /**
     * @var datetime Date when manifest was created
     */
	public $date_add;

    /**
     * @var datetime Date when record about manifest was updated
     */
	public $date_upd;

    /**
     * @var object Manifest WebServices instance
     */
	private $webservice;

    /**
     * @var array Class variables and their validation types
     */
	public static $definition = array(
		'table' => _DPDPOLAND_MANIFEST_DB_,
		'primary' => 'id_manifest',
		'multilang' => false,
		'multishop' => false,
		'fields' => array(
			'id_manifest'			=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_manifest_ws'		=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_package_ws'			=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'date_add' 				=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' 				=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);

    /**
     * Collects list of packages IDs
     *
     * @return array Packages IDs
     */
	public function getPackages()
	{
		$packages_ids = array();

		$packages = Db::getInstance()->executeS('
			SELECT `id_package_ws`
			FROM `'._DB_PREFIX_._DPDPOLAND_MANIFEST_DB_.'`
			WHERE `id_manifest_ws` = "'.(int)$this->id_manifest_ws.'"
		');

		foreach ($packages as $package)
			$packages_ids[] = $package['id_package_ws'];

		return $packages_ids;
	}

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
			SELECT m.`id_manifest_ws` 				AS `id_manifest_ws`,
				COUNT(p.`id_parcel`) 				AS `count_parcels`,
				COUNT(DISTINCT m.`id_package_ws`)	AS `count_orders`,
				m.`date_add` 						AS `date_add`
			FROM `'._DB_PREFIX_._DPDPOLAND_MANIFEST_DB_.'` m
			LEFT JOIN `'._DB_PREFIX_._DPDPOLAND_PARCEL_DB_.'` p ON (p.`id_package_ws` = m.`id_package_ws`)
			GROUP BY `id_manifest_ws`
			'.$filter.'
			ORDER BY `'.bqSQL($order_by).'` '.pSQL($order_way).
			($start !== null && $pagination !== null ? ' LIMIT '.(int)$start.', '.(int)$pagination : '')
		);
	}

    /**
     * Checks if all fields of sender addresses are valid
     *
     * @param array $package_ids Package IDs
     * @return bool Sender addresses are valid
     */
	public static function validateSenderAddresses($package_ids)
	{
		if (!is_array($package_ids))
			return false;

		$first_package = new DpdPolandPackage((int)$package_ids[0]);
		$first_package_address = new Address((int)$first_package->id_address_sender);

		$address_keys = array('country', 'company', 'lastname', 'firstname', 'address1', 'address2', 'postcode', 'city', 'phone');
		$address = array();

		foreach ($address_keys as $key)
			if (isset($first_package_address->$key))
				$address[$key] = $first_package_address->$key;
			else
				return false;

		foreach ($package_ids as $package_id)
		{
			$package = new DpdPolandPackage((int)$package_id);
			$sender_address = new Address((int)$package->id_address_sender);
			$current_package_sender_address = array();

			foreach ($address_keys as $key)
				if (isset($sender_address->$key))
					$current_package_sender_address[$key] = $sender_address->$key;
				else
					return false;

			$differences = array_diff_assoc($address, $current_package_sender_address);

			if (!empty($differences))
				return false;
		}

		return true;
	}

    /**
     * Returns manifest WebService ID according to package ID
     *
     * @param int|string $id_package_ws Package ID
     * @return false|null|string Manifest ID
     */
	public static function getManifestIdWsByPackageIdWs($id_package_ws)
	{
		return Db::getInstance()->getValue('
			SELECT `id_manifest_ws`
			FROM `'._DB_PREFIX_._DPDPOLAND_MANIFEST_DB_.'`
			WHERE `id_package_ws` = "'.(int)$id_package_ws.'"
		');
	}

    /**
     * Returns package ID according to Manifest ID
     *
     * @param int|string $id_manifest_ws Manifest ID
     * @return false|null|string Package ID
     */
	public function getPackageIdWsByManifestIdWs($id_manifest_ws)
	{
		return Db::getInstance()->getValue('
			SELECT `id_package_ws`
			FROM `'._DB_PREFIX_._DPDPOLAND_MANIFEST_DB_.'`
			WHERE `id_manifest_ws` = "'.(int)$id_manifest_ws.'"
		');
	}

    /**
     * Creates and returns package object instance
     *
     * @return DpdPolandPackage instance
     */
	public function getPackageInstance()
	{
		if (!$this->id_package_ws)
			$this->id_package_ws = $this->getPackageIdWsByManifestIdWs($this->id_manifest_ws);

		return new DpdPolandPackage($this->id_package_ws);
	}

    /**
     * Generates manifest
     *
     * @param string $output_doc_format Document format
     * @param string $output_doc_page_format Document page format
     * @param string $policy Policy type
     * @return bool Manifest generated successfully
     */
	public function generate($output_doc_format = 'PDF', $output_doc_page_format = 'LBL_PRINTER', $policy = 'STOP_ON_FIRST_ERROR')
	{
		if (!$this->webservice)
			$this->webservice = new DpdPolandManifestWS;

		return $this->webservice->generate($this, $output_doc_format, $output_doc_page_format, $policy);
	}

    /**
     * Generates multiple manifests
     *
     * @param array $package_ids Packages IDs
     * @param string $output_doc_format Document format
     * @param string $output_doc_page_format Document page format
     * @param string $policy Policy type
     * @return bool Multiple manifests generated successfully
     */
	public function generateMultiple($package_ids, $output_doc_format = 'PDF', $output_doc_page_format = 'LBL_PRINTER', $policy = 'STOP_ON_FIRST_ERROR')
	{
		if (!$this->webservice)
			$this->webservice = new DpdPolandManifestWS;

		return $this->webservice->generateMultiple($package_ids, $output_doc_format, $output_doc_page_format, $policy);
	}
}