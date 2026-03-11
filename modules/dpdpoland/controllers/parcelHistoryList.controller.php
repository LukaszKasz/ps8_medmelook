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
 * Class DpdPolandParcelHistoryController Responsible for parcel history list page view and actions
 */
class DpdPolandParcelHistoryController extends DpdPolandController
{
    /**
     * Default list sorting criteria
     */
	const DEFAULT_ORDER_BY 	= 'date_add';

    /**
     * Default list sorting way
     */
	const DEFAULT_ORDER_WAY = 'desc';

    /**
     * @var string Tracking URL address
     */
	private $tracking_link = 'https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1=';

    /**
     * Prepares list data to be displayed in page
     *
     * @return string Page content in HTML
     */
	public function getList()
	{
		$keys_array = array('id_order', 'id_parcel', 'receiver', 'country', 'postcode', 'city', 'address', 'date_add');
		$this->prepareListData($keys_array, 'ParcelHistories', new DpdPolandParcel(),
			self::DEFAULT_ORDER_BY, self::DEFAULT_ORDER_WAY, 'parcel_history_list');
		$this->context->smarty->assign('tracking_link', $this->tracking_link);

		if (version_compare(_PS_VERSION_, '1.6', '<'))
			return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/parcel_history_list.tpl');
		return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/parcel_history_list_16.tpl');
	}
}