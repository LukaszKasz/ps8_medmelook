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
 * Class DpdPolandCountryListController Responsible for DPD countries list view and actions
 */
class DpdPolandCountryListController extends DpdPolandController
{
    /**
     * Countries list order by value
     */
	const DEFAULT_ORDER_BY = 'id_country';

    /**
     * Countries list order way value
     */
	const DEFAULT_ORDER_WAY = 'asc';

    /**
     * Current file name
     */
	const FILENAME = 'countryList.controller';

    /**
     * Success message after changing country enable / disable value
     */
	public function displaySuccessStatusChangingMessage()
	{
		$page = (int)Tools::getValue('submitFilterCountries');

		if (!$page)
			$page = 1;

		$selected_pagination = (int)Tools::getValue('pagination', $this->pagination[0]);
		$order_by = Tools::getValue('CountryOrderBy', self::DEFAULT_ORDER_BY);
		$order_way = Tools::getValue('CountryOrderWay', self::DEFAULT_ORDER_WAY);

		DpdPoland::addFlashMessage($this->l('Country status changed successfully'));

		$redirect_url = $this->module_instance->module_url;
		$redirect_url .= '&menu=country_list&pagination='.$selected_pagination;
		$redirect_url .= '&CountryOrderBy='.$order_by;
		$redirect_url .= '&CountryOrderWay='.$order_way;
		$redirect_url .= '&submitFilterCountries='.$page;

		die(Tools::redirectAdmin($redirect_url));
	}

    /**
     * Sets enable / disable value for multiple countries
     *
     * @param array $countries Selected countries
     * @param bool $disable Is action to disable countries
     */
	public function changeEnabledMultipleCountries($countries = array(), $disable = false)
	{
		foreach ($countries as $id_country)
			if (!$this->changeEnabled((int)$id_country, $disable))
				self::$errors[] = sprintf($this->l('Could not change country status, ID: %s'), $id_country);

		if (!empty(self::$errors))
		{
			$this->module_instance->outputHTML(
				$this->module_instance->displayErrors(
					self::$errors
				)
			);

			reset(self::$errors);
		}
		else
		{
			$page = (int)Tools::getValue('submitFilterCountries');

			if (!$page)
				$page = 1;

			$selected_pagination = (int)Tools::getValue('pagination', $this->pagination[0]);
			$order_by = Tools::getValue('CountryOrderBy', self::DEFAULT_ORDER_BY);
			$order_way = Tools::getValue('CountryOrderWay', self::DEFAULT_ORDER_WAY);

			DpdPoland::addFlashMessage($this->l('Selected countries statuses changed successfully'));

			$redirect_url = $this->module_instance->module_url;
			$redirect_url .= '&menu=country_list&pagination='.$selected_pagination;
			$redirect_url .= '&CountryOrderBy='.$order_by;
			$redirect_url .= '&CountryOrderWay='.$order_way;
			$redirect_url .= '&submitFilterCountries='.$page;

			die(Tools::redirectAdmin($redirect_url));
		}
	}

    /**
     * Changes country status
     *
     * @param int $id_country Country ID
     * @param bool $disable Disable or enable action
     * @return bool Status changed successfully
     */
	public function changeEnabled($id_country, $disable = false)
	{
		$country_obj = new DpdPolandCountry(DpdPolandCountry::getIdByCountry((int)$id_country));
		$country_obj->enabled = $disable ? 0 : 1;
		$country_obj->id_country = (int)$id_country;
		return $country_obj->save();
	}

    /**
     * Prepares list data to be displayed in page
     *
     * @return string Page content in HTML
     */
	public function getListHTML()
	{
		$keys_array = array('id_country', 'name', 'iso_code', 'enabled');
		$this->prepareListData($keys_array, 'Countries', new DpdPolandCountry(), self::DEFAULT_ORDER_BY, self::DEFAULT_ORDER_WAY, 'country_list');

		if (version_compare(_PS_VERSION_, '1.6', '>='))
			return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/country_list_16.tpl');

		return $this->context->smarty->fetch(_DPDPOLAND_TPL_DIR_.'admin/country_list.tpl');
	}

    /**
     * Disables specific DPD countries during module installation
     *
     * @return bool Countries disabled successfully
     */
	public static function disableDefaultCountries()
	{
		$context = Context::getContext();

		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			foreach (Country::getCountries((int)$context->language->id) as $country)
				if (!in_array($country['iso_code'], DpdPolandCountry::$default_enabled_countries)
					&& !self::disableCountryById($context, (int)$country['id_country']))
					return false;
		}
		else
			foreach (array_keys(Shop::getShops()) as $id_shop)
				foreach (Country::getCountriesByIdShop($id_shop, Configuration::get('PS_LANG_DEFAULT')) as $country)
					if (!in_array($country['iso_code'], DpdPolandCountry::$default_enabled_countries)
						&& !self::disableCountryById($context, (int)$country['id_country'], (int)$id_shop))
						return false;

		return true;
	}

    /**
     * Disables specific DPD country
     *
     * @param Context $context Context object
     * @param int $id_country Country ID
     * @param null|int $id_shop Shop ID
     * @return bool Country disabled successfully
     */
	private static function disableCountryById($context, $id_country, $id_shop = null)
	{
		if ($id_shop === null)
			$id_shop = (int)$context->shop->id;

		$dpdpoland_country = new DpdPolandCountry();
		$dpdpoland_country->id_country = (int)$id_country;
		$dpdpoland_country->id_shop = (int)$id_shop;
		$dpdpoland_country->enabled = 0;

		return $dpdpoland_country->save();
	}
}
