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
use Tools;
use ObjectModel;
use Validate;
use Language;
use Configuration;
use PrestaShop\Module\Nxtalvariantspro\VariantProduct;

class VariantGroup extends ObjectModel
{
    public $id_variant_group;
    public $name;
    public $image;
    public $features;
    public $price;
    public $outofstock;
    public $position;
    public $active;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */

    public static $definition = array(
        'table' => 'nxtal_variant_group',
        'primary' => 'id_variant_group',
        'multilang' => true,
        'fields' => array(
            'features' => array('type' => self::TYPE_STRING),
            'image' => array('type' => self::TYPE_INT),
            'price' => array('type' => self::TYPE_INT),
            'outofstock' => array('type' => self::TYPE_INT),
            'position' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        ),
    );

    public static $csvFields = array(
        'name',
        'features',
        'image',
        'price',
        'outofstock',
        'active'
    );

    public function getFeatures()
    {
        $features = explode(',', $this->features);
        return array_filter($features);
    }

    public static function getVariantGroups($idLang)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'nxtal_variant_group` a
			LEFT JOIN `' . _DB_PREFIX_ . 'nxtal_variant_group_lang` b
			ON (a.id_variant_group = b.id_variant_group AND b.id_lang = '. (int) $idLang.')
			WHERE a.active = 1';

        return Db::getInstance()->executeS($sql);
    }

    public function add($autoDate = true, $nullValues = false)
    {
        if ($this->position <= 0) {
            $this->position = self::getHigherPosition() + 1;
        }

        $this->features = is_array($this->features) ? implode(',', $this->features) : $this->features;

        if (!parent::add($autoDate, $nullValues) || !Validate::isLoadedObject($this)) {
            return false;
        }

        return true;
    }

    public function update($null_values = false)
    {
        $this->features = is_array($this->features) ? implode(',', $this->features) : $this->features;

        return parent::update($null_values);
    }

    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
				FROM `'._DB_PREFIX_.self::$definition['table'].'`';

        $position = Db::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }

    public function delete()
    {
        $idVariantGroup = $this->id;

        if (!parent::delete()) {
            return false;
        }
        self::cleanPositions();

        VariantProduct::deleteBYIdGroup($idVariantGroup);

        return true;
    }

    public static function cleanPositions()
    {
        $return = true;

        $sql = 'SELECT `id_variant_group`
				FROM '._DB_PREFIX_.self::$definition['table'].'
				ORDER BY `position` ASC';
        $result = Db::getInstance()->executeS($sql);

        $i = 0;
        foreach ($result as $value) {
            $return = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.self::$definition['table'].'`
			SET `position` = ' . (int) $i++ . '
			WHERE `id_variant_group` = ' . (int) $value['id_variant_group']);
        }

        return $return;
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            '
			SELECT `id_variant_group`, `position`
			FROM '._DB_PREFIX_.self::$definition['table'].'
			ORDER BY `position` ASC'
        )) {
            return false;
        }

        foreach ($res as $option) {
            if ((int) $option['id_variant_group'] == (int) $this->id) {
                $moved_option = $option;
            }
        }

        if (!isset($moved_option) || !isset($position)) {
            return false;
        }

        return Db::getInstance()->execute(
            '
			UPDATE `'._DB_PREFIX_.self::$definition['table'].'`
			SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($way
                ? '> ' . (int) $moved_option['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $moved_option['position'] . ' AND `position` >= ' . (int) $position)
        )
        && Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.self::$definition['table'].'`
			SET `position` = ' . (int) $position . '
			WHERE `id_variant_group` = ' . (int) $moved_option['id_variant_group']);
    }

    public function isExist($data = null, $entry = false)
    {
        $sql = 'SELECT a.id_variant_group FROM `' . _DB_PREFIX_ . 'nxtal_variant_group` a
			LEFT JOIN `' . _DB_PREFIX_ . 'nxtal_variant_group_lang` b
			ON (a.id_variant_group = b.id_variant_group AND b.id_lang = '. (int) Configuration::get('PS_LANG_DEFAULT') .')
			WHERE 1';

        if ($entry == false && $data != null) {
            $sql .= ' AND b.`name` = "'. pSql($data['name']) . '"';

            $sql .= ' AND a.`features` = "'. pSql($data['features']) . '"';

            $sql .= ' AND a.`image` = '.(int) max((int)$data['image'], 0);

            $sql .= ' AND a.`price` = '.(int) max((int)$data['price'], 0);

            $sql .= ' AND a.`outofstock` = '.(int) max((int)$data['outofstock'], 0);

            $sql .= ' AND a.`active` = '.(int) max((int)$data['active'], 0);
        }

        return Db::getInstance()->getValue($sql);
    }

    public function importData($rows)
    {
        $count = 0;
        if ($rows) {
            $languages = Language::getIds();

            foreach ($rows as $row) {
                $obj = new self();
                if (!$obj->isExist($row)) {
                    foreach (self::$csvFields as $field) {
                        if ($field == 'name') {
                            foreach ($languages as $idLang) {
                                $obj->$field[$idLang] = $row[$field];
                            }
                        } else {
                            $obj->$field = $row[$field];
                        }
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
