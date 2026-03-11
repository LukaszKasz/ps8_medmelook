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
 * Class DpdPolandSenderAddressFormController Responsible for sender address form page view and actions
 */
class DpdPolandSenderAddressFormController extends DpdPolandController
{
    /**
     * Current file name
     */
	const FILENAME = 'configuration.controller';

    /**
     * @var int Sender address
     */
	private $id_sender_address = 0;

    /**
     * DpdPolandSenderAddressFormController class constructor
     */
	public function __construct()
    {
        parent::__construct();

        if (Tools::isSubmit('editSenderAddress')) {
            $this->id_sender_address = (int)Tools::getValue('id_sender_address');
        }
    }

    /**
     * Reacts at page actions
     */
    public function controllerActions()
    {
        if (Tools::isSubmit('saveSenderAddress')) {
            $this->saveSenderAddress();
        }
    }

    /**
     * Prepares form data to be displayed in page
     *
     * @return string Form data in HTML
     */
    public function getForm()
	{
		$sender_address = new DpdPolandSenderAddress((int)$this->id_sender_address);

        $this->context->smarty->assign(array(
            'object' => $sender_address,
            'saveAction' => $this->module_instance->module_url.'&menu=sender_address_form'
        ));

		if (version_compare(_PS_VERSION_, '1.6', '>='))
			return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/sender_address_form_16.tpl');

		return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/sender_address_form.tpl');
	}

    /**
     * Saves sender address in database
     */
	private function saveSenderAddress()
	{
	    $id_sender_address = (int)Tools::getValue('id_sender_address');

        $address = new DpdPolandSenderAddress((int)$id_sender_address);
        $address->alias = Tools::getValue('alias');
        $address->company = Tools::getValue('company');
        $address->name = Tools::getValue('name');
        $address->phone = Tools::getValue('phone');
        $address->postcode = Tools::getValue('postcode');
        $address->city = Tools::getValue('city');
        $address->address = Tools::getValue('address');
        $address->email = Tools::getValue('email');
        $address->id_shop = (int)$this->context->shop->id;

        if ($this->validateSenderAddress($address)) {
            $messages_controller = new DpdPolandMessagesController();

            if ($address->save()) {
                $messages_controller->setSuccessMessage(
                    $this->module_instance->l('Address saved successfully', self::FILENAME)
                );
            } else {
                $messages_controller->setErrorMessage(
                    $this->module_instance->l('Could not save address', self::FILENAME)
                );
            }
        }

        Tools::redirectAdmin($this->module_instance->module_url.'&menu=sender_address');
	}

    /**
     * Checks if sender address fields are valid
     *
     * @param DpdPolandSenderAddress $address Sender address object
     * @return bool Sender address is valid
     */
	private function validateSenderAddress(DpdPolandSenderAddress $address)
    {
        $errors = array();

        if ($address->alias === '') {
            $errors[] = $this->module_instance->l('Alias name is required', self::FILENAME);
        } elseif (!Validate::isGenericName($address->alias)) {
            $errors[] = $this->module_instance->l('Alias name is invalid', self::FILENAME);
        }

        if ($address->company === '') {
            $errors[] = $this->module_instance->l('Company name is required', self::FILENAME);
        } elseif (!Validate::isGenericName($address->company)) {
            $errors[] = $this->module_instance->l('Company name is invalid', self::FILENAME);
        }

        if ($address->name === '') {
            $errors[] = $this->module_instance->l('Name / Surname is required', self::FILENAME);
        } elseif (!Validate::isName($address->name)) {
            $errors[] = $this->module_instance->l('Name / Surname is invalid', self::FILENAME);
        }

        if ($address->phone === '') {
            $errors[] = $this->module_instance->l('Phone is required', self::FILENAME);
        } elseif (!Validate::isPhoneNumber($address->phone)) {
            $errors[] = $this->module_instance->l('Phone number is invalid', self::FILENAME);
        }

        if ($address->postcode === '') {
            $errors[] = $this->module_instance->l('Postcode is required', self::FILENAME);
        } elseif (!Validate::isPostCode($address->postcode)) {
            $errors[] = $this->module_instance->l('Postcode is invalid', self::FILENAME);
        }

        if ($address->city === '') {
            $errors[] = $this->module_instance->l('City is required', self::FILENAME);
        } elseif (!Validate::isCityName($address->city)) {
            $errors[] = $this->module_instance->l('City is invalid', self::FILENAME);
        }

        if ($address->address === '') {
            $errors[] = $this->module_instance->l('Address is required', self::FILENAME);
        } elseif (!Validate::isAddress($address->address)) {
            $errors[] = $this->module_instance->l('Address is invalid', self::FILENAME);
        }

        if ($address->email === '') {
            $errors[] = $this->module_instance->l('Email is required', self::FILENAME);
        } elseif (!Validate::isEmail($address->email)) {
            $errors[] = $this->module_instance->l('Email is invalid', self::FILENAME);
        }

        if ($errors) {
            $messages_controller = new DpdPolandMessagesController();
            $messages_controller->setErrorMessage(serialize($errors));

            return false;
        }

        return true;
    }
}