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
 * Class DpdPolandConfigurationController Responsible for setting page view and actions
 */
class DpdPolandConfigurationController extends DpdPolandController
{
    /**
     * @var array Available services (carriers) IDs
     */
	public $available_services_ids = array();

    /**
     * Name of settings saving action
     */
	const SETTINGS_SAVE_ACTION = 'saveModuleSettings';

    /**
     * Current file name
     */
	const FILENAME = 'configuration.controller';

    /**
     * Displays settings page content
     *
     * @return string Settings page content in HTML
     */
	public function getSettingsPage()
	{
		$configuration_obj = new DpdPolandConfiguration();

		$payment_modules = array();
		foreach (DpdPoland::getPaymentModules() as $payment_module)
		{
			$module = Module::getInstanceByName($payment_module['name']);

			if (!Validate::isLoadedObject($module))
				continue;

			$payment_modules[] = array(
				'displayName' => $module->displayName,
				'name' => $payment_module['name']
			);
		}

		$this->context->smarty->assign(array(
			'saveAction' => $this->module_instance->module_url,
			'settings' => $configuration_obj,
			'payer_numbers' => DpdPolandPayerNumber::getPayerNumbers(),
			'payment_modules' => $payment_modules,
			'zones' => Zone::getZones(),
			'carrier_zones' => array(
				'classic' => $this->getZonesForCarrier(DpdPolandConfiguration::CARRIER_CLASSIC_ID),
				'standard' => $this->getZonesForCarrier(DpdPolandConfiguration::CARRIER_STANDARD_ID),
				'standard_cod' => $this->getZonesForCarrier(DpdPolandConfiguration::CARRIER_STANDARD_COD_ID),
                'pudo' => $this->getZonesForCarrier(DpdPolandConfiguration::CARRIER_PUDO_ID),
                'pudo_cod' => $this->getZonesForCarrier(DpdPolandConfiguration::CARRIER_PUDO_COD_ID)
			)
		));

		if (version_compare(_PS_VERSION_, '1.6', '>='))
			return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/configuration_16.tpl');

		return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/configuration.tpl');
	}

    /**
     * Collects and returns carrier zones
     *
     * @param int|string $carrier_type Carrier type
     * @return array Carrier zones
     */
	private function getZonesForCarrier($carrier_type)
	{
		require_once(_DPDPOLAND_CONTROLLERS_DIR_.'service.php');

		$id_carrier = (int)Configuration::get($carrier_type);
		$carrier = DpdPolandService::getCarrierByReference((int)$id_carrier);

		if (Validate::isLoadedObject($carrier))
			$id_carrier = $carrier->id;

		$carrier_zones = DpdPolandConfiguration::getCarrierZones((int)$id_carrier);
		$carrier_zones_list = array();

		foreach ($carrier_zones as $zone)
			$carrier_zones_list[] = $zone['id_zone'];

		return $carrier_zones_list;
	}

    /**
     * Creates selected DPD sevices (carriers)
     * Deletes other DPD carriers
     */
	public function createDeleteCarriers()
	{
		require_once(_DPDPOLAND_CONTROLLERS_DIR_.'service.php');
		require_once(_DPDPOLAND_CONTROLLERS_DIR_.'dpd_classic.service.php');
		require_once(_DPDPOLAND_CONTROLLERS_DIR_.'dpd_standard.service.php');
		require_once(_DPDPOLAND_CONTROLLERS_DIR_.'dpd_standard_cod.service.php');
        require_once(_DPDPOLAND_CONTROLLERS_DIR_.'dpd_pudo.service.php');
        require_once(_DPDPOLAND_CONTROLLERS_DIR_.'dpd_pudo_cod.service.php');

		if (Tools::getValue(DpdPolandConfiguration::CARRIER_CLASSIC))
		{
			if (!DpdPolandCarrierClassicService::install())
				self::$errors[] = $this->l('Could not save DPD international shipment (DPD Classic) service');
		}
		else
		{
			if (!DpdPolandCarrierClassicService::delete())
				self::$errors[] = $this->l('Could not delete DPD international shipment (DPD Classic) service');
		}

		if (Tools::getValue(DpdPolandConfiguration::CARRIER_STANDARD))
		{
			if (!DpdPolandCarrierStandardService::install())
				self::$errors[] = $this->l('Could not save DPD domestic shipment - Standard service');
		}
		else
		{
			if (!DpdPolandCarrierStandardService::delete())
				self::$errors[] = $this->l('Could not delete DPD domestic shipment - Standard service');
		}

		if (Tools::getValue(DpdPolandConfiguration::CARRIER_STANDARD_COD))
		{
			if (!DpdPolandCarrierStandardCODService::install())
				self::$errors[] = $this->l('Could not save DPD domestic shipment - Standard with COD service');
		}
		else
		{
			if (!DpdPolandCarrierStandardCODService::delete())
				self::$errors[] = $this->l('Could not delete DPD domestic shipment - Standard with COD service');
		}

        if (Tools::getValue(DpdPolandConfiguration::CARRIER_PUDO))
        {
            if (!DpdPolandCarrierPudoService::install())
                self::$errors[] = $this->l('Could not save DPD Poland Reception Point Pickup service');
        }
        else
        {
            if (!DpdPolandCarrierPudoService::delete())
                self::$errors[] = $this->l('Could not delete DPD Poland Reception Point Pickup service');
        }

        if (Tools::getValue(DpdPolandConfiguration::CARRIER_PUDO_COD))
        {
            if (!DpdPolandCarrierPudoCodService::install())
                self::$errors[] = $this->l('Could not save DPD Poland Reception Point Pickup with COD service');
        }
        else
        {
            if (!DpdPolandCarrierPudoCodService::delete())
                self::$errors[] = $this->l('Could not delete DPD Poland Reception Point Pickup with COD service');
        }
	}

    /**
     * Checks if settings are valid before saving them
     */
	public function validateSettings()
	{
		if (!Tools::getValue(DpdPolandConfiguration::LOGIN))
			self::$errors[] = $this->l('Login can not be empty');
        $configuration_obj = new DpdPolandConfiguration();
		if (!isset($configuration_obj->password) && !Tools::getValue(DpdPolandConfiguration::PASSWORD))
			self::$errors[] = $this->l('Password can not be empty');

		if (!Tools::getValue(DpdPolandConfiguration::CLIENT_NUMBER))
			self::$errors[] = $this->l('Default client number must be set');

		if (Tools::isSubmit(DpdPolandConfiguration::CARRIER_STANDARD_COD))
		{
			$checked = false;

			foreach (DpdPoland::getPaymentModules() as $payment_module)
				if (Tools::isSubmit(DpdPolandConfiguration::COD_MODULE_PREFIX.$payment_module['name']))
					$checked = true;

			if (!$checked)
				self::$errors[] = $this->l('At least one COD payment method must be checked');
		}

		if (!Tools::getValue(DpdPolandConfiguration::WEIGHT_CONVERSATION_RATE))
			self::$errors[] = $this->l('Weight conversation rate can not be empty');
		elseif (!Validate::isUnsignedFloat(Tools::getValue(DpdPolandConfiguration::WEIGHT_CONVERSATION_RATE)))
			self::$errors[] = $this->l('Weight conversation rate is not valid');

		if (!Tools::getValue(DpdPolandConfiguration::DIMENSION_CONVERSATION_RATE))
			self::$errors[] = $this->l('Dimension conversation rate can not be empty');
		elseif (!Validate::isUnsignedFloat(Tools::getValue(DpdPolandConfiguration::DIMENSION_CONVERSATION_RATE)))
			self::$errors[] = $this->l('Dimension conversation rate is not valid');

		if (!Tools::getValue(DpdPolandConfiguration::CUSTOMER_FID))
			self::$errors[] = $this->l('Customer FID can not be empty');
		elseif (!ctype_alnum(Tools::getValue(DpdPolandConfiguration::CUSTOMER_FID)))
			self::$errors[] = $this->l('Customer FID is not valid');

		if (!Tools::getValue(DpdPolandConfiguration::WS_URL))
			self::$errors[] = $this->l('Web Services URL can not be empty');
		elseif (!Validate::isUrl(Tools::getValue(DpdPolandConfiguration::WS_URL)))
			self::$errors[] = $this->l('Web Services URL is not valid');
	}

    /**
     * Saves settings into database
     */
	public function saveSettings()
	{
		if (DpdPolandConfiguration::saveConfiguration())
		{
			if (!DpdPolandConfiguration::saveZonesForCarriers())
				DpdPoland::addFlashError($this->l('Settings saved successfully but could not assign zones for carriers'));
			else
			{
				DpdPoland::addFlashMessage($this->l('Settings saved successfully'));
				Tools::redirectAdmin($this->module_instance->module_url.'&menu=configuration');
			}
		}
		else
			DpdPoland::addFlashError($this->l('Could not save settings'));
	}
}