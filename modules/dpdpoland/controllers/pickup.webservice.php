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
 * Class DpdPolandPickup Responsible for Arrange Pickup actions management
 */
class DpdPolandPickup extends DpdPolandWS
{
    /**
     * @var int Pickup ID
     */
	public $id_pickup;

    /**
     * @var date|string Pickup date
     */
	public $pickupDate;

    /**
     * @var date|string Pickup time
     */
	public $pickupTime;

    /**
     * @var string Order type (international, domestic)
     */
	public $orderType;

    /**
     * @var bool Pickup for envelope
     */
	public $dox = false;

    /**
     * @var int|string Documents count
     */
	public $doxCount;

    /**
     * @var bool Pickup for parcels
     */
	public $parcels = false;

    /**
     * @var int|string Parcels count
     */
	public $parcelsCount;

    /**
     * @var float|string Parcels weight
     */
	public $parcelsWeight;

    /**
     * @var float|string Parcel max weight
     */
	public $parcelMaxWeight;

    /**
     * @var float|string Parcel max height
     */
	public $parcelMaxHeight;

    /**
     * @var float|string Parcel max depth
     */
	public $parcelMaxDepth;

    /**
     * @var float|string Parcel max width
     */
	public $parcelMaxWidth;

    /**
     * @var bool Pickup for pallet
     */
	public $pallet = false;

    /**
     * @var int|string Pallets count
     */
	public $palletsCount;

    /**
     * @var float|string Pallets weight
     */
	public $palletsWeight;

    /**
     * @var float|string Pallet max weight
     */
	public $palletMaxWeight;

    /**
     * @var float|string Pallet max height
     */
	public $palletMaxHeight;

    public $customerName;

    public $customerCompany;

    public $customerPhone;

    /**
     * Is it a standard parcel
     * Always must be true
     */
	const STANDARD_PARCEL = true;

    /**
     * Makes web services call to arrange a pickup
     *
     * @param string $operationType Operation type
     * @param bool $waybillsReady Are waybills ready
     * @return bool Pickup arranged successfully
     */
	public function arrange($operationType = 'INSERT', $waybillsReady = true)
	{
		list($pickupTimeFrom, $pickupTimeTo) = explode('-', $this->pickupTime);

		$settings = new DpdPolandConfiguration;

		$params = array(
			'dpdPickupParamsV3' => array(
				'operationType' => $operationType,
				'orderType' => $this->orderType,
				'pickupCallSimplifiedDetails' => array(
					'packagesParams' => $this->getPackagesParams(),
					'pickupCustomer' => array(
						'customerFullName' => $this->customerName,
						'customerName' => $this->customerCompany,
						'customerPhone' => $this->customerPhone
					),
					'pickupPayer' => array(
						'payerName' => $settings->client_name,
						'payerNumber' => $settings->client_number
					),
					'pickupSender' => $this->getSenderAddress()
				),
				'pickupDate' => $this->pickupDate,
				'pickupTimeFrom' => $pickupTimeFrom,
				'pickupTimeTo' => $pickupTimeTo,
				'waybillsReady' => $waybillsReady
			)
		);

		$result = $this->packagesPickupCallV4($params);

		if (isset($result['statusInfo']) && isset($result['statusInfo']['errorDetails']))
		{
			$errors = $result['statusInfo']['errorDetails'];
			$errors = (array_values($errors) === $errors) ? $errors : array($errors); // array must be multidimentional
			foreach ($errors as $error)
				self::$errors[] = sprintf($this->l('Error code: %s, fields: %s'), $error['code'], $error['fields']);

			return false;
		}

		if (isset($result['orderNumber']))
		{
			$this->id_pickup = (int)$result['orderNumber'];
			Configuration::updateValue('DPDPOLAND_CONFIGURATION_OK', true);
			return true;
		}
		self::$errors[] = $this->l('Order number is undefined');

		return false;
	}

    /**
     * Returns formatted sender address
     *
     * @return array Sender address
     */
	private function getSenderAddress()
    {
        $id_sender_address = (int)Tools::getValue('sender_address_selection');
        $sender_address = new DpdPolandSenderAddress((int)$id_sender_address);

        return array(
            'senderAddress' => $sender_address->address,
            'senderCity' => $sender_address->city,
            'senderFullName' => $sender_address->name,
            'senderName' => $sender_address->name,
            'senderPhone' => $sender_address->phone,
            'senderPostalCode' => DpdPoland::convertPostcode($sender_address->postcode),
        );
    }

    /**
     * Create array with pickup packages (envelopes, pallets or parcels) data for web services call
     *
     * @return array Formatted WebServices parameters
     */
	private function getPackagesParams()
	{
		return array_merge(
			$this->getEnvelopesParams(),
			$this->getPalletsParams(),
			$this->getParcelsParams()
		);
	}

	/**
	 * Returns array with envelopes data prepared for web services call
	 * In order to send envelopes, both conditions must be met:
	 * 	1. Envelopes chosen
	 * 	2. Envelopes count > 0
	 * Otherwise envelopes count will be 0.
	 * 'dox' parameter always must bet set to 0 - requirement by DPD Poland
     *
	 * @return array Envelopes parameters
	 */
	private function getEnvelopesParams()
	{
		$result = array(
			'dox' => 0, // always false even if envelopes are sent
			'doxCount' => 0
		);

		if ($this->dox && (int)$this->doxCount)
			$result['doxCount'] = (int)$this->doxCount;
		return $result;
	}

	/**
	 * Returns array with envelopes data prepared for web services call
	 * In order to send pallets, both conditions must be met:
	 * 	1. Pallets chosen
	 * 	2. Pallets count > 0
     *
	 * @return array Pallets parameters
	 */
	private function getPalletsParams()
	{
		$result = array(
			'pallet' => 0,
			'palletMaxHeight' => '',
			'palletMaxWeight' => '',
			'palletsCount' => 0,
			'palletsWeight' => ''
		);

		if ($this->pallet && (int)$this->palletsCount)
		{
			$result['pallet'] = 1;
			$result['palletMaxHeight'] = $this->palletMaxHeight;
			$result['palletMaxWeight'] = $this->palletMaxWeight;
			$result['palletsCount'] = (int)$this->palletsCount;
			$result['palletsWeight'] = $this->palletsWeight;
		}
		return $result;
	}

	/**
	 * Returns array with parcels data prepared for web services call
	 * If envelopes or pallets are sent without parcels then parcels should have all params set to 1
	 * In order to send parcels, both conditions must be met:
	 * 	1. Parcels chosen
	 * 	2. Parcels count > 0
     *
	 * @return array Parcels parameters
	 */
	private function getParcelsParams()
	{
		$result = array(
			'parcelsCount' => 0,
			'standardParcel' => self::STANDARD_PARCEL, // Always must be true
			'parcelMaxDepth' => '',
			'parcelMaxHeight' => '',
			'parcelMaxWeight' => '',
			'parcelMaxWidth' => '',
			'parcelsWeight' => ''
		);

		// If no parcels but envelopes or pallets are chosen then parcels all values should be 1
		if (!$this->parcels && ($this->dox || $this->pallet))
		{
			$result['parcelsCount'] = 1;
			$result['standardParcel'] = self::STANDARD_PARCEL; // Always must be true
			$result['parcelMaxDepth'] = 1;
			$result['parcelMaxHeight'] = 1;
			$result['parcelMaxWeight'] = 1;
			$result['parcelMaxWidth'] = 1;
			$result['parcelsWeight'] = 1;
		}
		elseif ($this->parcels && (int)$this->parcelsCount)
		{
			$result['parcelsCount'] = (int)$this->parcelsCount;
			$result['standardParcel'] = self::STANDARD_PARCEL; // Always must be true
			$result['parcelMaxDepth'] = $this->parcelMaxDepth;
			$result['parcelMaxHeight'] = $this->parcelMaxHeight;
			$result['parcelMaxWeight'] = $this->parcelMaxWeight;
			$result['parcelMaxWidth'] = $this->parcelMaxWidth;
			$result['parcelsWeight'] = $this->parcelsWeight;
		}
		return $result;
	}

	/**
	 * Get available pickup time frames for a particular date
     *
	 * @return bool Are any available time frames
	 */
	public function getCourierTimeframes()
	{
        $id_sender_address = (int)Tools::getValue('sender_address_selection');
        $sender_address = new DpdPolandSenderAddress((int)$id_sender_address);

		$params = array(
			'senderPlaceV1' => array(
				'countryCode' => DpdPoland::POLAND_ISO_CODE,
				'zipCode' => DpdPoland::convertPostcode($sender_address->postcode)
			)
		);

		$result = $this->getCourierOrderAvailabilityV1($params);

		if (!isset($result['ranges']) && !self::$errors)
			self::$errors[] = $this->l('Cannot get TimeFrames from webservices. Please check if sender\'s postal code is typed in correctly');

        if(isset($result['ranges']) && isset($result['ranges']['offset']))
            return array($result['ranges']);
		
		return (isset($result['ranges'])) ? $result['ranges'] : false;
	}
}