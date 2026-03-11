<?php
/**
 * ColorFeatures
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2019 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.1
 * @link      http://www.silbersaiten.de
 */

class BlockLayeredOverride extends BlockLayered
{
    public function generateFiltersBlock($selected_filters)
    {
        global $smarty;
        if ($filter_block = $this->getFilterBlock($selected_filters)) {
            if ($filter_block['nbr_filterBlocks'] == 0) {
                return false;
            }

            $translate = array();
            $translate['price'] = $this->l('price');
            $translate['weight'] = $this->l('weight');
            $advanced = $this->getAllCurrentFeatures();

            foreach ($filter_block['filters'] as &$filter) {
                if (isset($advanced[$filter['id_key']])) {
                    foreach ($advanced[$filter['id_key']] as $id_feature_value => $feature) {
                        if (isset($filter['values'][$id_feature_value])) {
                            $filter['values'][$id_feature_value]['color'] = $feature;
                        }
                    }
                    $filter['is_af_feature'] = true;
                }
            }

            $smarty->assign($filter_block);
            $smarty->assign(array(
                'hide_0_values' => Configuration::get('PS_LAYERED_HIDE_0_VALUES'),
                'blocklayeredSliderName' => $translate,
                'col_img_dir' => _PS_COL_IMG_DIR_
            ));

            return $smarty->fetch(_PS_MODULE_DIR_ . 'colorfeatures/views/templates/front/blocklayered_custom.tpl');
        } else {
            return false;
        }
    }

    private function getAllCurrentFeatures()
    {
        $result = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'color_features`');
        $features = array();
        foreach ($result as $feature) {
            $features[$feature['id_feature']][$feature['id_feature_value']] = $feature['value'];
        }
        return $features;
    }
}
