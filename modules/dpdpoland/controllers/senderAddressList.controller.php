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
 * Class DpdPolandSenderAddressListController Responsible for
 */
class DpdPolandSenderAddressListController extends DpdPolandController
{
    /**
     * Default list sorting criteria
     */
	const DEFAULT_ORDER_BY = 'date_add';

    /**
     * Default list sorting way
     */
	const DEFAULT_ORDER_WAY = 'desc';

    /**
     * Current file name
     */
	const FILENAME = 'manifestList.controller';

    /**
     * Reacts at page actions
     */
    public function controllerActions()
    {
        if (Tools::isSubmit('deleteSenderAddress')) {
            $this->deleteSenderAddress();
        }
    }

    /**
     * Prepares list data to be displayed in page
     *
     * @return string Page content in HTML
     */
	public function getListHTML()
	{
		$keys_array = array('id_sender_address', 'company', 'name', 'city', 'email', 'alias');
		$this->prepareListData($keys_array, 'SenderAddress', new DpdPolandSenderAddress, self::DEFAULT_ORDER_BY, self::DEFAULT_ORDER_WAY, 'sender_address');

		$this->context->smarty->assign('form_url', $this->module_instance->module_url.'&menu=sender_address_form');

		if (version_compare(_PS_VERSION_, '1.6', '<'))
			return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/sender_address_list.tpl');
		return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/sender_address_list_16.tpl');
	}

    /**
     * Deletes sender address
     */
    private function deleteSenderAddress()
    {
        $id_sender_address = (int)Tools::getValue('id_sender_address');
        $sender_address = new DpdPolandSenderAddress((int)$id_sender_address);
        $messages_controller = new DpdPolandMessagesController();

        if ($sender_address->delete()) {
            $messages_controller->setSuccessMessage(
                $this->module_instance->l('Address deleted successfully', self::FILENAME)
            );
        } else {
            $messages_controller->setErrorMessage(
                $this->module_instance->l('Could not delete address', self::FILENAME)
            );
        }

        Tools::redirectAdmin($this->module_instance->module_url.'&menu=sender_address');
    }
}