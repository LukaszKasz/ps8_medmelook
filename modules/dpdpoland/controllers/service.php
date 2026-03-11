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
 * Class DpdPolandService Responsible for DPD services (carriers) management
 */
class DpdPolandService
{
    /**
     * DPD carriers images directory name
     */
	const IMG_DIR = 'DPD_services';

    /**
     * DPD carriers images extension
     */
	const IMG_EXTENSION = 'jpg';

    /**
     * @var Module Module instance
     */
	protected $module_instance;

    /**
     * DpdPolandService class constructor
     */
	public function __construct()
	{
		$this->module_instance = Module::getInstanceByName('dpdpoland');
	}

	/**
	 * Delete existing carrier
	 *
	 * @param int $id_carrier ID of carrier to delete
	 * @return boolean Carrier deleted successfully
	 */
	protected static function deleteCarrier($id_carrier)
	{
		if (!$id_carrier)
			return true;

		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$id_carrier = (int)DpdPolandCarrier::getIdCarrierByReference((int)$id_carrier);
			$carrier = new Carrier((int)$id_carrier);
		}
		else
			$carrier = Carrier::getCarrierByReference($id_carrier);

		if (!Validate::isLoadedObject($carrier))
			return true;

		if ($carrier->deleted)
			return true;

		$carrier->deleted = 1;

		return (bool)$carrier->save();
	}

    /**
     * Assigns PrestaShop customer groups for carrier on PS 1.4
     *
     * @param int $id_carrier Carrier ID
     * @param array $groups Groups IDs
     * @return bool Groups successfully assigned for carrier
     */
	protected static function setGroups14($id_carrier, $groups)
	{
		foreach ($groups as $id_group)
			if (!Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'carrier_group`
					(`id_carrier`, `id_group`)
				VALUES
					("'.(int)$id_carrier.'", "'.(int)$id_group.'")
			'))
				return false;

		return true;
	}

    /**
     * Assigns customer groups for carrier on PS 1.5 and PS 1.6
     *
     * @param Carrier $carrier Carrier object
     * @return bool Customer groups successfully assigned for carrier
     */
	protected static function assignCustomerGroupsForCarrier($carrier)
	{
		$groups = array();

		foreach (Group::getGroups((int)Context::getContext()->language->id) as $group)
			$groups[] = $group['id_group'];

		if (version_compare(_PS_VERSION_, '1.5.5', '<'))
		{
			if (!self::setGroups14((int)$carrier->id, $groups))
				return false;
		}
		else
			if (!$carrier->setGroups($groups))
				return false;

		return true;
	}

    /**
     * Returns carrier according to it's reference
     *
     * @param string|int $reference Carrier reference
     * @return bool|Carrier Carrier object
     */
	public static function getCarrierByReference($reference)
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$id_carrier = (int)DpdPolandCarrier::getIdCarrierByReference($reference);
			$carrier = new Carrier((int)$id_carrier);
		}
		else
			$carrier = Carrier::getCarrierByReference((int)$reference);

		return $carrier;
	}
}