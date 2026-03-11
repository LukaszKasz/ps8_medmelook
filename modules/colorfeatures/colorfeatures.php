<?php
/**
 * ColorFeatures
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2022 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.11
 * @link      https://www.silbersaiten.de
 */

class ColorFeatures extends Module
{
    private $ps_v;

    public function __construct()
    {
        $this->name = 'colorfeatures';
        $this->tab = 'front_office_features';
        $this->version = '1.0.11';
        $this->author = 'silbersaiten';
        $this->module_key = '4fdbd1fad2c355ea6cf1a1442d13061a';

        parent::__construct();

        $this->displayName = $this->l('Color features');
        $this->description = $this->l('Adds a color type for features.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->ps_v = Tools::substr(_PS_VERSION_, 0, 3);

        /** The names of the script files take into account the version of the Prestashop */
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $this->ps_v = 1.7;
        }
    }

    public function install()
    {
        if (parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('actionFeatureValueSave')
            && $this->registerHook('actionFeatureValueDelete')
            && $this->registerHook('actionGetProductPropertiesAfter')) {
            $this->addTab();

            return Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'color_features (
                `id_color_feature` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_feature` int(11) unsigned NOT NULL,
                `id_feature_value` int(11) unsigned NOT NULL,
                `is_texture` TINYINT(1) NOT NULL default \'0\',
                `value` varchar(255) NOT NULL,
                `texture_extension` varchar(8) NOT NULL,
                PRIMARY KEY(`id_color_feature`))
                ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8');
        }

        return false;
    }

    /**
     * We need the controller access (for ajax)
     */
    private function addTab()
    {
        $tab = new Tab();
        $tab->module = $this->name;
        /* We need active tab only for PS 1.7.* */
        $tab->active = version_compare(_PS_VERSION_, '1.7.1', '>=');
        $tab->class_name = 'AdminColorFeatures';
        $tab->id_parent = (int)Tab::getIdFromClassName('CONFIGURE');
        $tab->icon = 'invert_colors';

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Color features settings';
        }

        $tab->add();
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminColorFeatures'));
    }

    public function hookDisplayHeader($params)
    {
        unset($params);
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $feature_ids = array_keys(self::getAllCurrentFeatures());

            if ($feature_ids) {
                $feature_names = Db::getInstance()->executeS('SELECT name FROM `' . _DB_PREFIX_ . 'feature_lang` WHERE `id_lang`=' . (int)$this->context->language->id . ' AND `id_feature` IN (' . implode(',', $feature_ids) . ');');
            } else {
                $feature_names = array();
            }

            if (count($feature_names)) {
                $feature_names = array_column($feature_names, 'name');
            }

            $this->context->controller->registerStylesheet(
                'modules-color_features',
                'modules/' . $this->name . '/views/css/color_features.css',
                array('media' => 'all', 'priority' => 150)
            );

            $this->smarty->assign(array(
                'feature_names' => implode(',', $feature_names)
            ));

            // #TODO NEED IMPROVEMENT
//            return $this->display(__FILE__, 'script_values.tpl');
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        Media::addJsDef(array(
            'af_img_path' => _MODULE_DIR_ . $this->name . '/img/',
        ));

        if (version_compare(_PS_VERSION_, '1.7.3', '>=')) {
            if ($this->context->controller instanceof AdminProductsController || $this->context->controller->php_self === 'AdminProducts') {
                Media::addJsDef(array(
                    'advanced_features' => json_encode(self::getAllCurrentFeatures()),
                    'is_multifeatures_enabled' => Module::isInstalled('multifeatures') && Module::isEnabled('multifeatures')
                ));
                $this->context->controller->addCss($this->_path . 'views/css/admin_color_features.css');
                $this->context->controller->addJs($this->_path . 'views/js/admin_products_' . $this->ps_v . '.js');
            }
        } else {
            Media::addJsDef(array(
                'advanced_features' => json_encode(self::getAllCurrentFeatures()),
                'is_multifeatures_enabled' => Module::isInstalled('multifeatures') && Module::isEnabled('multifeatures')
            ));
            $this->context->controller->addCss($this->_path . 'views/css/admin_color_features.css');
            $this->context->controller->addJs($this->_path . 'views/js/admin_products_' . $this->ps_v . '.js');
        }

        if ($this->context->controller instanceof AdminFeaturesController) {
            $this->context->controller->addJqueryPlugin(array('growl'));
            Media::addJsDef(array(
                'af_tpl_path' => $this->context->link->getAdminLink('AdminColorFeatures', true),
                'admin_img_path' => __PS_BASE_URI__ . 'img/admin/',
            ));

            $this->context->controller->addJqueryPlugin('colorpicker');
            $this->context->controller->addJs($this->_path . 'views/js/admin_color_features.js');
            $this->context->controller->addCss($this->_path . 'views/css/admin_color_features.css');
        }
    }

    public static function getAllCurrentFeatures()
    {
        $result = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'color_features`');

        $features = array();
        foreach ($result as $feature) {
            if ($feature['is_texture']) {
                $features[$feature['id_feature']][$feature['id_feature_value']] = $feature['id_color_feature'] . '.' . $feature['texture_extension'];
            } else {
                $features[$feature['id_feature']][$feature['id_feature_value']] = $feature['value'];
            }
        }

        return $features;
    }

    public function hookActionFeatureValueSave($param)
    {
        if ((int)Tools::getValue('is_colour_feature')) {
            $color = Tools::getValue('color');
            $feature = new FeatureValue((int)$param['id_feature_value']);
            $file_ext = '';

            if ($_FILES['texture']['size']) {
                $filename = $_FILES["texture"]["name"];
                $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
            }

            $af_feature = array(
                'id_feature' => $feature->id_feature,
                'id_feature_value' => $feature->id,
                'is_texture' => $file_ext ? 1 : 0,
                'value' => $color,
                'texture_extension' => $file_ext,
            );

            if ($id_color_feature = (int)Db::getInstance()->getValue('SELECT `id_color_feature` FROM `' . _DB_PREFIX_ . 'color_features` WHERE `id_feature_value`=' . (int)$param['id_feature_value'])) {
                Db::getInstance()->update('color_features', $af_feature, 'id_feature_value=' . (int)$param['id_feature_value']);
            } else {
                Db::getInstance()->insert('color_features', $af_feature);
            }

            if (!$id_color_feature) {
                $id_color_feature = (int)Db::getInstance()->getValue('SELECT `id_color_feature` FROM `' . _DB_PREFIX_ . 'color_features` WHERE `id_feature_value`=' . (int)$param['id_feature_value']);
            }

            if ($_FILES['texture']['size']) {
                $dir = _PS_MODULE_DIR_ . $this->name . '/img/' . $id_color_feature . '.' . $file_ext;
                $this->uploadImage($dir, $file_ext);
            }
        }
    }

    public function hookActionFeatureValueDelete($param)
    {
        $colour_feature = Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'color_features` WHERE `id_feature_value`=' . (int)$param['id_feature_value']
        );

        $file = _PS_MODULE_DIR_ . $this->name . '/img/' . $colour_feature['id_color_feature'] . '.' . $colour_feature['texture_extension'];

        if (file_exists($file)) {
            unlink($file);
        }

        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'color_features` WHERE `id_feature_value`=' . (int)$param['id_feature_value']
        );
    }

    protected function uploadImage($dir, $ext)
    {
        if (isset($_FILES['texture']['tmp_name']) && !empty($_FILES['texture']['tmp_name'])) {
            $max_size = isset($this->max_image_size) ? $this->max_image_size : 0;
            if ($error = ImageManager::validateUpload($_FILES['texture'], Tools::getMaxUploadSize($max_size))) {
                $this->errors[] = $error;
            }

            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmp_name) {
                return false;
            }

            if (!move_uploaded_file($_FILES['texture']['tmp_name'], $tmp_name)) {
                return false;
            }

            if (!ImageManager::checkImageMemoryLimit($tmp_name)) {
                $this->errors[] = $this->l('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings.');
            }

            if (empty($this->errors) && !ImageManager::resize($tmp_name, $dir, null, null, $ext)) {
                $this->errors[] = $this->l('An error occurred while uploading the image.');
            }

            if (count($this->errors)) {
                return false;
            } else {
                unlink($tmp_name);
                return true;
            }
        }

        return true;
    }

    public function hookActionGetProductPropertiesAfter($params)
    {
        foreach ($params['product']['features'] as &$feature) {
            $query = new DbQuery();
            $query->select('cf.*');
            $query->from('color_features', 'cf');
            $query->leftJoin('feature_value', 'fv', 'fv.id_feature_value = cf.id_feature_value AND fv.id_feature = cf.id_feature');
            $query->innerJoin('feature_value_lang', 'fvl', 'fvl.id_feature_value = cf.id_feature_value AND fvl.id_lang = ' . $params['id_lang'] . ' AND fvl.value = "' . $feature['value'] . '"');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

            if (is_array($result) && count($result)) {
                $this->smarty->assign(array(
                    'image_path' => _MODULE_DIR_ . $this->name . '/img/',
                    'color_feature' => $result,
                    'feature' => $feature
                ));

                $html_str = $this->display(__FILE__, 'product_feature.tpl');
                $html_str = trim($html_str);
                $html_str = str_replace(["\n", "\r"], '', $html_str);
                $html_str = preg_replace('/\>\s+\</m', '><', $html_str);
                $feature['value'] = $html_str;
            }
        }
        unset($feature);
    }
}
