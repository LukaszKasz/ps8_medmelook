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
 * Class DpdPolandParcel Responsible for DPD parcels management
 */
class DpdPolandParcel extends DpdPolandObjectModel
{
    /**
     * @var int Parcel ID
     */
    public $id_parcel;

    /**
     * @var int|string Parcel WebService ID
     */
    public $id_package_ws;

    /**
     * @var string Parcel waybill
     */
    public $waybill;

    /**
     * @var string Parcel content
     */
    public $content;

    /**
     * @var float Parcel weight
     */
    public $weight;

    /**
     * @var float Parcel weightAdr
     */
    public $weight_adr;

    /**
     * @var float Parcel height
     */
    public $height;

    /**
     * @var float Parcel length
     */
    public $length;

    /**
     * @var float Parcel width
     */
    public $width;

    /**
     * @var int|string Parcel number
     */
    public $number;

    /**
     * @var datetime Date when parcel was created
     */
    public $date_add;

    /**
     * @var datetime Date when parcel data was updated
     */
    public $date_upd;

    /**
     * @var array Class variables and their validation types
     */
    public static $definition = array(
        'table' => _DPDPOLAND_PARCEL_DB_,
        'primary' => 'id_parcel',
        'multilang' => false,
        'multishop' => false,
        'fields' => array(
            'id_parcel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_package_ws' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'waybill' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
            'content' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
            'weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'weight_adr' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'height' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'length' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'width' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'number' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        )
    );

    /**
     * Collects orders waybills data
     *
     * @param array $orders_ids Orders IDs
     * @return array|false|mysqli_result|null|PDOStatement|resource Orders Waybills
     */
    public static function getOrdersWaybills(array $orders_ids)
    {
        if (empty($orders_ids)) {
            return array();
        }

        $waybills = Db::getInstance()->executeS('
			SELECT parc.`waybill`, pack.`sessionType`
			FROM `' . _DB_PREFIX_ . _DPDPOLAND_PACKAGE_DB_ . '` pack
			LEFT JOIN `' . _DB_PREFIX_ . _DPDPOLAND_PARCEL_DB_ . '` parc
				ON (parc.`id_package_ws` = pack.`id_package_ws`)
			WHERE pack.`id_order` IN (' . implode(',', $orders_ids) . ')
		');

        return $waybills;
    }

    /**
     * Collects parcels data for order
     *
     * @param int $id_order Order ID
     * @param null|int|string $id_package_ws Package WebServices ID
     * @return array|false|mysqli_result|null|PDOStatement|resource Order parcels
     * @throws PrestaShopDatabaseException
     */
    public static function getParcels($id_order, $id_package_ws = null)
    {
        if ($id_package_ws) {
            $parcels = Db::getInstance()->executeS('
				SELECT `id_parcel`, `content`, `weight`, `height`, `length`, `width`, `number`, `weight_adr`
				FROM `' . bqSQL(_DB_PREFIX_ . self::$definition['table']) . '`
				WHERE `id_package_ws`=' . (int)$id_package_ws
            );
            return $parcels;
        }

        $products = DpdPolandParcelProduct::getShippedProducts($id_order);

        $parcels = array();

        $weight = $height = $length = $width = 0;

        $products_count = count($products);

        if ($products_count == 1) {
            $product = reset($products);
            $height = DpdPoland::convertDimension($product['height']);
            $length = DpdPoland::convertDimension($product['length']);
            $width = DpdPoland::convertDimension($product['width']);
        }

        $content = DpdPolandParcel::getParcelContent($products);

        foreach ($products as $product) {
            $weight += DpdPoland::convertWeight($product['weight']);
        }

        $parcels[] = array(
            'number' => 1,
            'content' => $content,
            'weight' => $weight,
            'height' => sprintf('%.6f', $height),
            'length' => sprintf('%.6f', $length),
            'width' => sprintf('%.6f', $width),
            'weight_adr' => 0.0
        );

        return $parcels;
    }

    private static function getContentBySKU(array $products)
    {
        $content = '';
        $products_count = count($products);

        foreach ($products as $product) {
            $content .= $product['reference'];

            if (--$products_count)
                $content .= ', ';
        }
        return $content;
    }

    private static function getContentByProductId(array $products)
    {
        $content = '';
        $products_count = count($products);

        foreach ($products as $product) {
            $content .= $product['id_product'] . '_' . $product['id_product_attribute'];

            if (--$products_count)
                $content .= ', ';
        }
        return $content;

    }

    private static function getContentByProductName(array $products)
    {
        $content = '';
        $products_count = count($products);

        foreach ($products as $product) {
            $content .= $product['name'];

            if (--$products_count)
                $content .= ', ';
        }
        return $content;

    }

    /**
     * Return parcel content
     * @param $settings
     * @param array $products
     */
    public static function getParcelContent(array $products)
    {
        $settings = new DpdPolandConfiguration;
        switch ($settings->parcel_content_source) {
            case DpdPolandConfiguration::PARCEL_CONTENT_SOURCE_SKU:
                return DpdPolandParcel::getContentBySKU($products);
            case DpdPolandConfiguration::PARCEL_CONTENT_SOURCE_PRODUCT_ID:
                return DpdPolandParcel::getContentByProductId($products);
            case DpdPolandConfiguration::PARCEL_CONTENT_SOURCE_PRODUCT_NAME:
                return DpdPolandParcel::getContentByProductName($products);
            default:
                return '';
        }
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

        $id_shop = (int)Context::getContext()->shop->id;
        $id_lang = (int)Context::getContext()->language->id;

        $list = DB::getInstance()->executeS('
			SELECT
				p.`id_order` 								AS `id_order`,
				par.`waybill`								AS `id_parcel`,
				CONCAT(a.`firstname`, " ", a.`lastname`) 	AS `receiver`,
				cl.`name` 									AS `country`,
				a.`postcode` 								AS `postcode`,
				a.`city`									AS `city`,
				CONCAT(a.`address1`, " ", a.`address2`)		AS `address`,
				p.`date_add` 								AS `date_add`
			FROM `' . _DB_PREFIX_ . _DPDPOLAND_PARCEL_DB_ . '` par
			LEFT JOIN `' . _DB_PREFIX_ . _DPDPOLAND_PACKAGE_DB_ . '` p ON (p.`id_package_ws` = par.`id_package_ws`)
			LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.`id_order` = p.`id_order`)
			LEFT JOIN `' . _DB_PREFIX_ . 'address` a ON (a.`id_address` = p.`id_address_delivery`)
			LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = "' . (int)$id_lang . '")' .
            (version_compare(_PS_VERSION_, '1.5', '<') ? ' ' : 'WHERE o.`id_shop` = "' . (int)$id_shop . '" ') .
            $filter .
            ($order_by && $order_way ? ' ORDER BY `' . bqSQL($order_by) . '` ' . pSQL($order_way) : '') .
            ($start !== null && $pagination !== null ? ' LIMIT ' . (int)$start . ', ' . (int)$pagination : '')
        );

        if (!$list)
            $list = array();

        return $list;
    }
}