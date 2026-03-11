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
 * @author    DPD Polska Sp. z o.o.
 * @copyright 2019 DPD Polska Sp. z o.o.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska Sp. z o.o.
 */

if (!defined('_PS_VERSION_'))
    exit;

/**
 * Class DpdPolandSenderAddressListController Responsible for
 */
class DpdPolandPickupHistoryListController extends DpdPolandController
{
    /**
     * Default list sorting criteria
     */
    const DEFAULT_ORDER_BY = 'order_number';

    /**
     * Default list sorting way
     */
    const DEFAULT_ORDER_WAY = 'desc';

    /**
     * Current file name
     */
    const FILENAME = 'manifestList.controller';

    /**
     * Prepares list data to be displayed in page
     *
     * @return string Page content in HTML
     */
    public function getListHTML()
    {
        $keys_array = array('id_pickup_history', 'order_number', 'sender_address', 'sender_company', 'sender_name', 'sender_phone', 'pickup_date', 'pickup_time', 'type', 'envelope', 'package', 'pallet');
        $this->prepareListData($keys_array, 'PickupHistory', new DpdPolandPickupHistory, self::DEFAULT_ORDER_BY, self::DEFAULT_ORDER_WAY, 'pickup_history');

        $this->context->smarty->assign('form_url', $this->module_instance->module_url . '&menu=pickup_history_form');

        if (version_compare(_PS_VERSION_, '1.6', '<'))
            return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/pickup_history_list.tpl');
        return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_ . 'admin/pickup_history_list_16.tpl');
    }

    /**
     * Save pickup
     * @param $pickup
     * @param $id_sender_address
     * @throws PrestaShopException
     */
    public function save($pickup)
    {
        $id_sender_address = (int)Tools::getValue('sender_address_selection');
        $sender_address = new DpdPolandSenderAddress((int)$id_sender_address);

        $pickup_history = new DpdPolandPickupHistory;
        $pickup_history->order_number = $pickup->id_pickup;
        $pickup_history->sender_address = $sender_address->alias;
        $pickup_history->sender_company = $pickup->customerCompany;
        $pickup_history->sender_name = $pickup->customerName;
        $pickup_history->sender_phone = $pickup->customerPhone;
        $pickup_history->pickup_date = $pickup->pickupDate;
        $pickup_history->pickup_time = $pickup->pickupTime;
        $pickup_history->type = $pickup->orderType;
        $pickup_history->envelope = $pickup->doxCount;
        $pickup_history->package = $pickup->parcelsCount;
        $pickup_history->package_weight_all = $pickup->parcelsWeight;
        $pickup_history->package_heaviest_weight = $pickup->parcelMaxWeight;
        $pickup_history->package_heaviest_width = $pickup->parcelMaxWidth;
        $pickup_history->package_heaviest_length = $pickup->parcelMaxDepth;
        $pickup_history->package_heaviest_height = $pickup->parcelMaxHeight;
        $pickup_history->pallet = $pickup->palletsCount;
        $pickup_history->pallet_weight = $pickup->palletsWeight;
        $pickup_history->pallet_heaviest_weight = $pickup->palletMaxWeight;
        $pickup_history->pallet_heaviest_height = $pickup->palletMaxHeight;
        $pickup_history->id_shop = (int)$this->context->shop->id;

        $pickup_history->save();
    }
}