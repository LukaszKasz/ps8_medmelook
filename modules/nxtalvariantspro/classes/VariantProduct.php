<?php
/**
 * Product Variants Pro
 *
 * @author    Nxtal <support@nxtal.com>
 * @copyright Nxtal 2023
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @version   1.4.0
 *
 */

namespace PrestaShop\Module\Nxtalvariantspro;

use Db;
use ObjectModel;
use Context;
use Tools;
use FeatureValue;

class VariantProduct extends ObjectModel
{
    public $id_variant_product;
    public $name;
    public $id_variant_group;
    public $type;
    public $id_feature;
    public $products;
    public $categories;
    public $manufacturers;
    public $active;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */

    public static $definition = array(
        'table' => 'nxtal_variant_product',
        'primary' => 'id_variant_product',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING),
            'id_variant_group' => array('type' => self::TYPE_INT),
            'type' => array('type' => self::TYPE_STRING),
            'id_feature' => array('type' => self::TYPE_INT),
            'products' => array('type' => self::TYPE_STRING),
            'categories' => array('type' => self::TYPE_STRING),
            'manufacturers' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        ),
    );

    public static $csvFields = array(
        'name',
        'id_variant_group',
        'type',
        'id_feature',
        'products',
        'categories',
        'manufacturers',
        'active'
    );

    public static function explode($idText)
    {
        $ids = explode(',', $idText);

        return array_filter($ids);
    }

    public static function getProductIds($type, $value, $idcategories = array(), $idManufacturers = array())
    {
        $sql = 'SELECT DISTINCT a.id_product FROM `' . _DB_PREFIX_ . 'product` a';

        $where = ' WHERE a.active = 1';

        if ($type != 'feature') {
            $where .= ' AND a.'. $type . ' = "' . $value . '"';
        }

        if ($idcategories) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'category_product` b ON(a.id_product = b.id_product)';

            $idcategories = is_int($idcategories) ? array($idcategories) : $idcategories;

            $where .= ' AND b.id_category IN ('. implode(',', $idcategories).')';
        }

        if ($idManufacturers) {
            $idManufacturers = is_int($idManufacturers) ? array($idManufacturers) : $idManufacturers;
            $where .= ' AND a.id_manufacturer IN ('. implode(',', $idManufacturers) . ')';
        }

        if ($type == 'feature') {
            $idLang = (int) Context::getContext()->language->id;

            $featureValue = new FeatureValue($value, $idLang);

            $where .= ' AND a.id_product IN (SELECT c.id_product FROM `' . _DB_PREFIX_ . 'feature_product` c INNER JOIN `' . _DB_PREFIX_ . 'feature_value_lang` d ON (c.id_feature_value = d.id_feature_value AND d.id_lang = '. (int)$idLang .') WHERE d.value = "'. pSQL($featureValue->value) .'" AND c.id_feature = '. (int)$featureValue->id_feature .')';
        }

        $sql .= $where;

        $results = Db::getInstance()->executeS($sql);

        if ($results) {
            return array_column($results, 'id_product');
        }

        return array();
    }

    public static function getVariants($idProduct = 0, $idcategories = array(), $idManufacturer = null, $raw = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'nxtal_variant_product`
			WHERE active = 1';

        $whereArray = array();

        if ($idProduct) {
            $whereArray[] = '(FIND_IN_SET('.(int) $idProduct.',`products`) OR `products` = "")';
        }

        if ($idcategories) {
            $idcategories = is_int($idcategories) ? array($idcategories) : $idcategories;

            $whereArray[] = '(CONCAT(",", `categories`, ",") REGEXP ",('.implode('|', (array)$idcategories).')," OR `categories` = "")';
        }

        if ($idManufacturer) {
            $whereArray[] = '(FIND_IN_SET('. (int)$idManufacturer.', `manufacturers`) OR `manufacturers` = "")';
        }

        if ($whereArray) {
            $sql .= ' AND (' . implode(' AND ', $whereArray) . ')';
        }

        $results = Db::getInstance()->executeS($sql);

        if ($results) {
            if ($raw == false) {
                foreach ($results as &$result) {
                    $result['products'] = self::explode($result['products']);
                    $result['categories'] = self::explode($result['categories']);
                    $result['manufacturers'] = self::explode($result['manufacturers']);
                }
            }

            return $results;
        }

        return array();
    }

    public static function deleteBYIdGroup($idVariantGroup)
    {
        return Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'nxtal_variant_product`
			WHERE id_variant_group = ' . (int) $idVariantGroup
        );
    }

    public static function setVariants($idProduct, $products)
    {
        if (!$idProduct) {
            return;
        }

        foreach ($products as $idProductVariant) {
            if ($variant = self::getVariants((int) $idProductVariant, true)) {
                $obj = new self((int) $variant['id_nxtal_variants']);
                if ($variantProducts = array_diff($variant['variant_products'], array($idProductVariant))) {
                    $obj->variant_products = implode(',', $variantProducts);
                    $obj->update();
                } else {
                    $obj->delete();
                }
            }
        }

        if ($variant = self::getVariants((int) $idProduct, true)) {
            $obj = new self((int) $variant['id_nxtal_variants']);
        } else {
            $obj = new self();
        }

        if ($products) {
            array_push($products, (int) $idProduct);
            $products = array_unique($products);
            $products = array_filter($products);
            $obj->variant_products = implode(',', $products);

            return $obj->save();
        } elseif ($variant) {
            return $obj->delete();
        }
    }

    public static function searchManufacturers($searchText)
    {
        return Db::getInstance()->executeS(
            '
			SELECT *
			FROM ' . _DB_PREFIX_ . 'manufacturer m
			WHERE m.`name` LIKE "' . pSQL($searchText) . '%"'
        );
    }

    public function add($auto_date = true, $null_values = false)
    {
        $this->setFormVars();

        return parent::add($auto_date, $null_values);
    }

    public function update($null_values = false)
    {
        $this->setFormVars();

        return parent::update($null_values);
    }

    public function setFormVars()
    {
        if ('custom' == $this->type) {
            $this->products = is_array($this->products) ? implode(',', $this->products) : $this->products;
            $this->categories = '';
            $this->manufacturers = '';
        } else {
            $this->products = '';
            $this->categories = is_array($this->categories) ? implode(',', $this->categories) : $this->categories;
            $this->manufacturers = is_array($this->manufacturers) ? implode(',', $this->manufacturers) : $this->manufacturers;
        }
    }

    public function isExist($data = null, $entry = false)
    {
        $sql = 'SELECT a.id_variant_product FROM `' . _DB_PREFIX_ . 'nxtal_variant_product` a
			WHERE 1';

        if ($entry == false && $data != null) {
            $sql .= ' AND a.`name` = "'. pSql($data['name']) . '"';

            $sql .= ' AND a.`id_variant_group` = '.(int) max((int)$data['id_variant_group'], 0);

            $sql .= ' AND a.`type` = "'. pSql($data['type']) . '"';

            $sql .= ' AND a.`id_feature` = '.(int) max((int)$data['id_feature'], 0);

            $sql .= ' AND a.`products` = "'. pSql($data['products']) . '"';

            $sql .= ' AND a.`categories` = "'. pSql($data['categories']) . '"';

            $sql .= ' AND a.`manufacturers` = "'. pSql($data['manufacturers']) . '"';

            $sql .= ' AND a.`active` = '.(int) max((int)$data['active'], 0);
        }

        return Db::getInstance()->getValue($sql);
    }

    public function importData($rows)
    {
        $count = 0;
        if ($rows) {
            foreach ($rows as $row) {
                $obj = new self();
                if (!$obj->isExist($row)) {
                    foreach (self::$csvFields as $field) {
                        $obj->$field = $row[$field];
                    }

                    if ($obj->add()) {
                        $count++;
                    }
                }
            }
        }
        return $count;
    }
}
