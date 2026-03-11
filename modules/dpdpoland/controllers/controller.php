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
 * Class DpdPolandController Responsible for view and actions in module pages
 */
class DpdPolandController
{
    /**
     * @var Context Context object
     */
	protected $context;

    /**
     * @var Module Module instance
     */
	protected $module_instance;

    /**
     * @var array Available paginations
     */
	protected $pagination = array(10, 20, 50, 100, 300);

    /**
     * @var int Default pagination
     */
    private $default_pagination = 50;

    /**
     * @var array Collected pages errors
     */
	public static $errors = array();

    /**
     * @var array Collected pages notices
     */
	public static $notices = array();

    /**
     * @var string Child class name
     */
	private $child_class_name;


    /**
     * DpdPolandController class constructor
     */
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->module_instance = Module::getInstanceByName('dpdpoland');
		$this->child_class_name = get_class($this);
	}

    /**
     * Translates texts
     *
     * @param string $text Text
     * @return string Translated text
     */
	protected function l($text)
	{
		$child_class_name = $this->child_class_name;
		$reflection = new ReflectionClass($child_class_name);
		$filename = $reflection->hasConstant('FILENAME') ?
            $reflection->getConstant('FILENAME') : false;

		return $this->module_instance->l($text, $filename);
	}

    /**
     * Collects and formats lists filter parameters
     *
     * @param array $keys_array Filter keys
     * @param string $table Database table name
     * @return string Formatted filter query
     */
	protected function getFilterQuery($keys_array = array(), $table)
	{
		$sql = '';

		foreach ($keys_array as $key)
			if ($this->context->cookie->__isset($table.'Filter_'.$key))
			{
				$value = $this->context->cookie->{$table.'Filter_'.$key};
				if (Validate::isSerializedArray($value))
				{
					$date = $this->module_instance->unSerialize($value);

					if (!empty($date[0]))
						$sql .= '`'.bqSQL($key).'` > "'.pSQL($date[0]).'" AND ';

					if (!empty($date[1]))
						$sql .= '`'.bqSQL($key).'` < "'.pSQL($date[1]).'" AND ';
				}
				else
				{
					if ($value != '')
						$sql .= '`'.bqSQL($key).'` LIKE "%'.pSQL($value).'%" AND ';
				}
			}

		if ($sql)
			$sql = ' HAVING '.Tools::substr($sql, 0, -4); // remove 'AND ' from the end of query

		return $sql;
	}

    /**
     * Prepares list data to be displayed in page
     *
     * @param array $keys_array Filter keys
     * @param string $table Database table name
     * @param string $model Class name
     * @param string $default_order_by Default sorting value
     * @param string $default_order_way Default sorting way value
     * @param string $menu_page Current menu page
     * @param null $selected_pagination
     */
	public function prepareListData($keys_array, $table, $model, $default_order_by, $default_order_way, $menu_page)
	{
		if (Tools::isSubmit('submitFilterButton'.$table))
		{
			foreach ($_POST as $key => $value)
			{
				if (strpos($key, $table.'Filter_') !== false) // looking for filter values in $_POST
				{
					if (is_array($value))
						$this->context->cookie->$key = serialize($value);
					else
						$this->context->cookie->$key = $value;
				}
			}
		}

		if (Tools::isSubmit('submitReset'.$table))
		{
			foreach ($keys_array as $key)
			{
				if ($this->context->cookie->__isset($table.'Filter_'.$key))
				{
					$this->context->cookie->__unset($table.'Filter_'.$key);
					$_POST[$table.'Filter_'.$key] = null;
				}
			}
		}

		if (version_compare(_PS_VERSION_, '1.6', '>='))
			$page = (int)Tools::getValue('submitFilterButton'.$table);
		else
			$page = (int)Tools::getValue('submitFilter'.$table);

		if (!$page)
			$page = 1;

        $selected_pagination = (int)Tools::getValue('pagination', $this->default_pagination);
		$start = ($selected_pagination * $page) - $selected_pagination;

		$order_by = Tools::getValue($table.'OrderBy', $default_order_by);
		$order_way = Tools::getValue($table.'OrderWay', $default_order_way);

		$filter = $this->getFilterQuery($keys_array, $table);

		$table_data = $model->getList($order_by, $order_way, $filter, $start, $selected_pagination);
		$list_total = count($model->getList($order_by, $order_way, $filter, null, null));

		$total_pages = ceil($list_total / $selected_pagination);

		if (!$total_pages)
			$total_pages = 1;

		$this->context->smarty->assign(array(
			'full_url' => $this->module_instance->module_url.'&menu='.$menu_page.'&'.$table.'OrderBy='.$order_by.'&'.$table.'OrderWay='.$order_way,
			'table_data' => $table_data,
			'page' => $page,
			'selected_pagination' => $selected_pagination,
			'pagination' => $this->pagination,
			'total_pages' => $total_pages,
			'list_total' => $list_total,
			'filters_has_value' => (bool)$filter,
			'order_by' => $order_by,
			'order_way' => $order_way,
			'order_link' => 'index.php?controller=AdminOrders&vieworder&token='.Tools::getAdminTokenLite('AdminOrders')
		));
	}
}