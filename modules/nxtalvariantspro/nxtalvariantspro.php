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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/classes/VariantGroup.php';
require_once dirname(__FILE__).'/classes/VariantProduct.php';

use PrestaShop\Module\Nxtalvariantspro\VariantGroup;
use PrestaShop\Module\Nxtalvariantspro\VariantProduct;

class Nxtalvariantspro extends Module
{
    public $idContextLang;
    public $idDefaultLang;
    public $psVersion;
    public $imageDir;
    public $variantTypes = array();
    public $configVars = array();

    public function __construct()
    {
        $this->name = 'nxtalvariantspro';
        $this->author = 'Nxtal';
        $this->tab = 'front_office_features';
        $this->version = '1.4.0';
        $this->need_instance = 0;
        $this->module_key = '082e7a24499701be50e6e5f52ea87f43';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product Variants Pro');
        $this->description = $this->l('Display multiple custom product variations range to your customers to choose from at the product page.');

        $this->confirmUninstall = $this->l('Are you sure to uninstall this module? Please confirm');
        $this->idContextLang = (int) $this->context->language->id;
        $this->idDefaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->psVersion = Tools::substr(_PS_VERSION_, 0, 3);

        $this->imageDir = _PS_MODULE_DIR_ . $this->name . '/views/variant_img/';

        $this->variantTypes = array(
            'custom' => array(
                'id' => 'custom',
                'name' => $this->l('Custom'),
            ),
            'feature' => array(
                'id' => 'feature',
                'name' => $this->l('Feature'),
            ),
            'reference' => array(
                'id' => 'reference',
                'name' => $this->l('Reference'),
            ),
            'isbn' => array(
                'id' => 'isbn',
                'name' => $this->l('ISBN'),
            ),
            'ean13' => array(
                'id' => 'ean13',
                'name' => $this->l('EAN-13 or JAN barcode'),
            ),
            'upc' => array(
                'id' => 'upc',
                'name' => $this->l('UPC barcode'),
            ),
        );

        $this->configVars = array(
            'NXTAL_VARIANTSPRO_COLOR',
            'NXTAL_VARIANTSPRO_BACKGROUND',
            'NXTAL_VARIANTSPRO_BORDER',
            'NXTAL_VARIANTSPRO_COLOR_ACTIVE',
            'NXTAL_VARIANTSPRO_BACKGROUND_ACTIVE',
            'NXTAL_VARIANTSPRO_BORDER_ACTIVE',
            'NXTAL_VARIANTSPRO_MAINIMAGE',
            'NXTAL_VARIANTSPRO_CATALOG',
            'NXTAL_VARIANTSPRO_CSS'
        );
    }

    public function install()
    {
        $this->_clearCache('*');

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (!parent::install()
            || !$this->registerHook(
                array(
                    'header',
                    'actionProductSave',
                    'actionProductDelete',
                    'actionCategoryUpdate',
                    'actionManufacturerUpdate',
                    'displayAdminProductsExtra',
                    'displayRightColumnProduct',
                    'displayProductListReviews',
                    'displayProductVariants',
                    'actionAdminControllerSetMedia',
                    'actionFrontControllerSetMedia'
                )
            )
            || !$this->createTable()
            || !$this->createTabs()
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall()
            && $this->dropTable()
            && $this->uninstallTab()
            && $this->removeConfiguration()
        ;
    }

    public function createTabs()
    {
        if (_PS_VERSION_ > 1.6) {
            $this->installTab(
                'AdminProductVariantSetting',
                $this->l('Product Variants'),
                'AdminCatalog'
            );
            $this->installTab(
                'AdminProductVariants',
                $this->l('Variants'),
                'AdminProductVariantSetting'
            );
            $this->installTab(
                'AdminVariantGroups',
                $this->l('Groups'),
                'AdminProductVariantSetting'
            );
        } else {
            $this->installTab(
                'AdminProductVariants',
                $this->l('Product Variants'),
                'AdminCatalog'
            );
            $this->installTab(
                'AdminVariantGroups',
                $this->l('Groups')
            );
        }

        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = -1;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }
        return true;
    }

    public function createTable()
    {
        $queries = array(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'nxtal_variant_group` (
			`id_variant_group` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,                
			`features` text,
			`image` int(1) UNSIGNED,                
			`price` int(1) UNSIGNED,
			`outofstock` int(1) UNSIGNED,
			`position` int(11) UNSIGNED,
			`active` int(1) UNSIGNED,
			`date_add` datetime,
			`date_upd` datetime,
			PRIMARY KEY ( `id_variant_group` ))
			ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'nxtal_variant_group_lang` (
			`id_variant_group` int(11) UNSIGNED,
			`id_lang` int(11) UNSIGNED,
			`name` text DEFAULT NULL)
			ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'nxtal_variant_product` (
			`id_variant_product` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_variant_group` int(11) UNSIGNED,
			`name` text DEFAULT NULL,
			`type` varchar(50),
			`id_feature` int(11) UNSIGNED,
			`products` text DEFAULT NULL, 
			`categories` text DEFAULT NULL, 
			`manufacturers` text DEFAULT NULL,				
			`active` int(1) UNSIGNED,				
			`date_add` datetime,
			`date_upd` datetime,
			PRIMARY KEY ( `id_variant_product` ))
			ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
        );

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    public function dropTable()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'nxtal_variant_group`, `'._DB_PREFIX_.'nxtal_variant_group_lang`, `'._DB_PREFIX_.'nxtal_variant_product`');
    }

    public function getConfigValue()
    {
        static $values = array();

        if ($values) {
            return $values;
        }

        foreach ($this->configVars as $var) {
            $values[$var] = Tools::getValue($var, Configuration::get($var));
        }
        return $values;
    }

    public function updateConfiguration()
    {
        foreach ($this->configVars as $var) {
            $val = Tools::getValue($var);

            if (!in_array($var, array('NXTAL_VARIANTSPRO_CATALOG', 'NXTAL_VARIANTSPRO_CSS', 'NXTAL_VARIANTSPRO_MAINIMAGE'))
                && $val && !Validate::isColor($val)
            ) {
                $this->context->controller->errors[] = $this->l('Invalid color value.');
                return false;
            }

            Configuration::updateValue($var, Tools::getValue($var));
        }

        return true;
    }

    public function removeConfiguration()
    {
        foreach ($this->configVars as $var) {
            Configuration::deleteByName($var);
        }

        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminProductVariants')
        );
    }

    public function displayForm()
    {
        $imageTypes = ImageType::getImagesTypes();
        foreach ($imageTypes as &$imageType) {
            $imageType['val'] = $imageType['name'];
            $imageType['name'] = sprintf($this->l('%s (%dx%d pixels)'), $imageType['name'], $imageType['width'], $imageType['height']);
        }

        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Layout'),
                'icon' => 'icon-image'
            ),
            'input' => array(
                array(
                    'type' => 'color',
                    'label' => $this->l('Variant text color'),
                    'name' => 'NXTAL_VARIANTSPRO_COLOR',
                    'desc' => $this->l('Set the variant text color.')
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Variant border color'),
                    'name' => 'NXTAL_VARIANTSPRO_BORDER',
                    'desc' => $this->l('Set the variant border color.')
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Variant background color'),
                    'name' => 'NXTAL_VARIANTSPRO_BACKGROUND',
                    'desc' => $this->l('Set the variant background color.')
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Active variant text color'),
                    'name' => 'NXTAL_VARIANTSPRO_COLOR_ACTIVE',
                    'desc' => $this->l('Set the active variant text color.')
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Active variant border color'),
                    'name' => 'NXTAL_VARIANTSPRO_BORDER_ACTIVE',
                    'desc' => $this->l('Set the active variant border color.')
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Active variant background color'),
                    'name' => 'NXTAL_VARIANTSPRO_BACKGROUND_ACTIVE',
                    'desc' => $this->l('Set the active variant background color.')
                ),
                array(
                    'type' => 'switch',
                    'name' => 'NXTAL_VARIANTSPRO_CATALOG',
                    'label' => $this->l('Display variant label on listing'),
                    'desc' => $this->l('Enable to display variant label on product listing page.'),
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Main product image type'),
                    'name' => 'NXTAL_VARIANTSPRO_MAINIMAGE',
                    'options' => array(
                        'query' => $imageTypes,
                        'id' => 'val',
                        'name' => 'name',
                        'default' => array(
                            'label' => $this->l('Do not change product image'),
                            'value' => 0
                        )
                    ),
                    'class' => 'fixed-width-xxl',
                    'desc' => $this->l('Set the image type to change the product main image of the product detail page when the mouse hovers over the variant.')
                ),
                array(
                    'type' => 'textarea',
                    'name' => 'NXTAL_VARIANTSPRO_CSS',
                    'label' => $this->l('Custom CSS'),
                    'desc' => $this->l('Set custom CSS to change the variant layout.')
                )
            ),
            'submit' => array(
                'type' => 'submit',
                'name' => 'btnConfigSubmit',
                'icon' => 'process-icon-save',
                'class' => 'btn btn-default pull-right',
                'title' => $this->l('Save')
            ),
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminProductVariants');
        $helper->currentIndex = AdminController::$currentIndex;

        // Language
        $helper->default_form_language = $this->idDefaultLang;
        $helper->allow_employee_form_lang = $this->idDefaultLang;
        $helper->languages = $this->context->controller->getLanguages();

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->submit_action = 'submit'.$this->name;

        $helper->fields_value = $this->getConfigValue();

        return $helper->generateForm($fields_form);
    }

    public function getHtmlElement($element, $params = array())
    {
        $this->context->smarty->assign(
            array(
                'element_name' => $element,
                'element_params' => $params
            )
        );

        return $this->display(
            $this->name,
            '/views/templates/front/html.tpl'
        );
    }


    public function hookHeader()
    {
        return $this->getHtmlElement(
            'variant_style',
            $this->getConfigValue()
        );
    }

    // For prestashop 1.6
    public function hookDisplayRightColumnProduct($params)
    {
        if ($this->psVersion < 1.7) {
            return $this->hookDisplayProductAdditionalInfo(array('product' => $params['product']));
        }
    }

	public function hookDisplayProductVariants($params)
    {
        if (is_object($params['product'])) {
            $idProduct = $params['product']->id;
        } else {
            $idProduct = $params['product']['id_product'];
        }

        if (!$idProduct) {
            $idProduct = (int) Tools::getValue('id_product');
        }

        return $this->getVariants($idProduct);
    }
    /*
	public function hookDisplayProductAdditionalInfo($params)
    {
        if (is_object($params['product'])) {
            $idProduct = $params['product']->id;
        } else {
            $idProduct = $params['product']['id_product'];
        }

        if (!$idProduct) {
            $idProduct = (int) Tools::getValue('id_product');
        }

        return $this->getVariants($idProduct);
    }
	*/
    public function hookDisplayProductListReviews($params)
    {
        $config = $this->getConfigValue();

        if ($config['NXTAL_VARIANTSPRO_CATALOG']) {
            if (is_object($params['product'])) {
                $idProduct = $params['product']->id;
            } else {
                $idProduct = $params['product']['id_product'];
            }

            $variantGroups = $this->getVariants($idProduct, true);

            if ($variantGroups) {
                return $this->getHtmlElement(
                    'catalog_label',
                    array(
                        'variants' => array_sum(array_column($variantGroups, 'count'))
                    )
                );
            }
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (in_array(Tools::getValue('controller'), array('AdminProductVariants', 'AdminVariantGroups', 'AdminProducts'))) {
            Media::addJsDef(
                array('nxtalvariantspro_module_link' => $this->context->link->getAdminLink('AdminModules').'&configure='. $this->name)
            );

            $this->context->controller->addCSS(
                _MODULE_DIR_.$this->name.'/views/css/back.css',
                'all'
            );
            $this->context->controller->addJqueryUI('ui.sortable');
            $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/back.js');
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->addCSS(
            _MODULE_DIR_.$this->name.'/views/css/front.css',
            'all'
        );
        $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/front.js');
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        Cache::clean($this->name.'_*');
        parent::_clearCache($template, $cache_id, $compile_id);
    }

    public function hookActionProductSave()
    {
        $this->_clearCache('*');
    }

    public function hookActionProductDelete()
    {
        $this->_clearCache('*');
    }

    public function hookActionCategoryUpdate()
    {
        $this->_clearCache('*');
    }

    public function hookActionManufacturerUpdate()
    {
        $this->_clearCache('*');
    }

    public function hookDisplayAdminProductsExtra($product)
    {
        if ($this->psVersion > 1.6) {
            $idProduct = (int)$product['id_product'];
        } else {
            $idProduct = (int)Tools::getValue('id_product');
        }

        return $this->getHtmlElement(
            'admin_image_input',
            array(
                'image_link' => $this->getUploadedImage($idProduct)
            )
        );
    }

    public function ajaxProcessUploadVariationImage()
    {
        $idProduct = (int)Tools::getValue('id_product');

        $response = array();

        if ($idProduct && isset($_FILES['image'])
            && !$_FILES['image']['error']
            && !ImageManager::validateUpload($_FILES['image'], 0, array('jpg', 'jpeg', 'png'))
        ) {
            $imageName = $idProduct . '.jpg';

            if (move_uploaded_file($_FILES['image']['tmp_name'], $this->imageDir . $imageName)) {
                $thumbName = $this->getVariationThumbName($imageName);

                @unlink($this->imageDir . $thumbName);

                $response = array(
                    'status' => true,
                    'image' => $this->getUploadedImage($idProduct)
                );
            }
        }

        if (!$response) {
            $response = array(
                'status' => false,
                'error' => $this->l('Invalid image file.')
            );
        }

        die(json_encode($response));
    }

    public function getVariationThumbName($imageName, &$width = 0, &$height = 0)
    {
        $imageSize = ImageType::getByNameNType('small_default', 'products');

        if ($imageSize) {
            $width = $imageSize['width'];
            $height = $imageSize['height'];
        } else {
            $width = 100;
            $height = 100;
        }

        return $width . 'x' . $height . '_' . $imageName;
    }

    public function ajaxProcessDeleteVariationImage()
    {
        $idProduct = (int)Tools::getValue('id_product');

        $response = array();

        $imgName = $idProduct . '.jpg';

        if (file_exists($this->imageDir . $imgName)) {
            unlink($this->imageDir . $imgName);

            $thumbName = $this->getVariationThumbName($imgName);

            @unlink($this->imageDir . $thumbName);

            $response = array(
                'status' => true
            );
        }

        if (!$response) {
            $response = array(
                'status' => false,
                'error' => $this->l('Variation image file does not exist.')
            );
        }

        die(json_encode($response));
    }

    public function ajaxProcessSearchElement()
    {
        $searchText = Tools::getValue('search_text');
        $searchType = Tools::getValue('type');

        $elements = array();

        if ($searchType == 'product') {
            if ($products = Product::searchByName(
                (int)$this->context->language->id,
                pSql($searchText),
                null,
                30
            )) {
                foreach ($products as &$product) {
                    $product['image'] = $this->getProductCoverImage($product['id_product']);
                }
                $elements = $products;
            }
        } elseif ($searchType == 'feature') {
            $features = $this->getFeatures();

            foreach ($features as $key => $feature) {
                if (stripos($feature['name'], $searchText) === false) {
                    unset($features[$key]);
                }
            }

            $elements = $features;
        } elseif ($searchType == 'manufacturer') {
            $manufacturers = VariantProduct::searchManufacturers($searchText);

            $elements = $manufacturers;
        }

        if ($elements) {
            $response = array(
                'elements' => array_splice($elements, 0, 100),
                'type' => $searchType,
                'found' => true
            );
        } else {
            $response = array(
                'found' => false
            );
        }

        die(json_encode($response));
    }

    public function getFeatures()
    {
        static $attributes = array();

        if ($attributes) {
            return $attributes;
        }

        $attributes = array(
           'name' => array(
                'id_feature' => 'name',
                'name' => $this->l('Product name')
            ),
            'dimension' => array(
                'id_feature' => 'dimension',
                'name' => $this->l('Dimension')
            ),
            'weight' => array(
                'id_feature' => 'weight',
                'name' => $this->l('Weight')
            )
        );

        $features = Feature::getFeatures($this->idContextLang);

        foreach ($features as $feature) {
            $attributes[$feature['id_feature']] = $feature;
        }

        return $attributes;
    }

    public function getVariants($idProduct, $return = false)
    {
        if (!$idProduct) {
            return array();
        }

        $variantGroups = array();

        $cacheKey = $this->name . '_' . $idProduct . '-' . (int)$return;

        if (!Cache::isStored($cacheKey)) {
            $product = new Product((int) $idProduct, false, $this->idContextLang);

            $categories = Product::getProductCategories($idProduct);

            $priceDisplay = Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer);

            $variants = VariantProduct::getVariants(
                $idProduct,
                $categories,
                $product->id_manufacturer
            );

            if ($variants) {
                foreach ($variants as $variant) {
                    $group = new VariantGroup((int) $variant['id_variant_group'], $this->idContextLang);

                    if (!$group->active) {
                        continue;
                    }

                    $productIds = array();

                    if ('custom' == $variant['type']) {
                        $productIds = array_unique($variant['products']);
                    } else {
                        $value = '';

                        if ('feature' == $variant['type']) {
                            $features = $product->getFeatures();

                            foreach ($features as $feature) {
                                if ($feature['id_feature'] == $variant['id_feature']) {
                                    $value = $feature['id_feature_value'];
                                    break;
                                }
                            }
                        } elseif (isset($product->{$variant['type']})) {
                            $value = $product->{$variant['type']};
                        }

                        if ($value) {
                            $productIds = VariantProduct::getProductIds(
                                $variant['type'],
                                $value,
                                $variant['categories'],
                                $variant['manufacturers']
                            );
                        }
                    }

                    if (count($productIds)) {
                        $products = array();

                        foreach ($productIds as $idp) {
                            $varProduct = new Product(
                                $idp,
                                true,
                                $this->idContextLang,
                                $this->context->shop->id
                            );

                            $stock = StockAvailable::getQuantityAvailableByProduct($idp);

                            if (!Product::checkAccessStatic((int) $idp, false)
                                || !$varProduct->active
                                || (!$group->outofstock && !$stock)
                                || !$varProduct->available_for_order
                            ) {
                                continue;
                            }

                            $label = '';

                            if ($group->features) {
                                $featureAttributes = VariantProduct::explode($group->features);

                                $features = Product::getFrontFeaturesStatic((int) $this->idContextLang, $idp);

                                $labels = array();

                                foreach ($featureAttributes as $featureAttribute) {
                                    if (is_numeric($featureAttribute)) {
                                        if ($features) {
                                            foreach ($features as $feature) {
                                                if ($feature['id_feature'] == $featureAttribute) {
                                                    if ($feature['value']) {
                                                        $labels[] = $feature['value'];
                                                    }
                                                    break 1;
                                                }
                                            }
                                        }
                                    } else {
                                        if ('dimension' == $featureAttribute) {
                                            if ($varProduct->height > 0 || $varProduct->width > 0 || $varProduct->depth > 0) {
                                                $labels[] = Tools::ps_round($varProduct->height, '2') . ' x '
                                                . Tools::ps_round($varProduct->width, '2'). ' x '
                                                . Tools::ps_round($varProduct->depth, '2') . ' '
                                                . Configuration::get('PS_DIMENSION_UNIT');
                                            }
                                        } elseif ('weight' == $featureAttribute && $varProduct->weight > 0) {
                                            $labels[] = Tools::ps_round($varProduct->weight, 2) . ' '
                                            . Configuration::get('PS_WEIGHT_UNIT');
                                        } elseif ('name' == $featureAttribute) {
                                            $labels[] = $varProduct->name;
                                        }
                                    }
                                }

                                $label = implode(' + ', $labels);
                            }

                            if (!$label) {
                                //continue;
                            }

                            $productPrice = 0;
                            $regularPrice = 0;

                            if ($group->price) {
                                if (!$priceDisplay || $priceDisplay == 2) {
                                    $productPrice = $varProduct->getPrice(true, null, 6);
                                    $regularPrice = $varProduct->getPriceWithoutReduct(false, null);
                                } elseif ($priceDisplay == 1) {
                                    $productPrice = $varProduct->getPrice(false, null, 6);
                                    $regularPrice = $varProduct->getPriceWithoutReduct(true, null);
                                }
                            }

                            $image = '';
                            $cover = '';

                            if ($group->image) {
                                $image = $this->getProductCoverImage($idp);

                                if (Configuration::get('NXTAL_VARIANTSPRO_MAINIMAGE')) {
                                    $cover = $this->getProductCoverImage(
                                        $idp,
                                        Configuration::get('NXTAL_VARIANTSPRO_MAINIMAGE'),
                                        false
                                    );
                                }
                            }

                            if ($label || $image || $productPrice) {
                                $products[$idp] = array(
                                    'id_product' => (int) $idp,
                                    'label' => $label,
                                    'name' => $varProduct->name,
                                    'url' => $this->context->link->getProductLink($varProduct),
                                    'image' => $image,
                                    'cover' => $cover,
                                    'price' => $productPrice ? Tools::displayPrice($productPrice) : 0,
                                    'regular_price' => $regularPrice ? Tools::displayPrice($regularPrice) : 0,
                                    'has_discount' => ($regularPrice > $productPrice) ? 1 : 0
                                );
                            }
                        }

                        if ($products) {
                            $variantGroups[] = array(
                                'id_variant_group' => $group->id,
                                'name' => $group->name,
                                'image' => $group->image,
                                'position' => $group->position,
                                'products' => $products,
                                'count' => count($products)
                            );
                        }
                    }
                }
            }

            Cache::store(
                $cacheKey,
                $variantGroups
            );
        }

        $variantGroups = Cache::retrieve($cacheKey);

        if ($return) {
            return $variantGroups;
        }

        if ($variantGroups) {
            if (!$this->isCached('variant.tpl', $this->getCacheId($cacheKey))) {
                array_multisort(array_column($variantGroups, 'position'), SORT_ASC, $variantGroups);

                $this->context->smarty->assign(
                    array(
                        'id_product' => $idProduct,
                        'variant_groups' => $variantGroups
                    )
                );
            }

            return $this->display($this->name, 'variant.tpl', $this->getCacheId($cacheKey));
        }
    }

    public function getUploadedImage($idProduct)
    {
        $imgName = $idProduct . '.jpg';

        if (file_exists($this->imageDir . $imgName)) {
            $width = 100;
            $height = 100;

            $thumbName = $this->getVariationThumbName($imgName, $width, $height);

            if (!file_exists($this->imageDir . $thumbName)) {
                ImageManager::resize(
                    $this->imageDir . $imgName,
                    $this->imageDir . $thumbName,
                    $width,
                    $height
                );
            }

            return $this->context->link->getBaseLink() . '/modules/' . $this->name . '/views/variant_img/' . $thumbName;
        }

        return false;
    }

    public function getProductCoverImage($idProduct, $imageType = 'small_default', $variantImage = true)
    {
        if ($idProduct) {
            if ($variantImage) {
                $uploadedImage = $this->getUploadedImage($idProduct);

                if ($uploadedImage) {
                    return $uploadedImage;
                }
            }

            $image = array();

            if ($idProductAttribute = (int)Product::getDefaultAttribute($idProduct)) {
                $image = Product::getCombinationImageById($idProductAttribute, $this->idContextLang);
            }

            if (!$image) {
                $image = Product::getCover((int) $idProduct);
            }

            if (!$image) {
                return $this->context->link->getImageLink(
                    '',
                    $this->context->language->iso_code.'-default',
                    $imageType ? $imageType : 'medium_default'
                );
            }

            return $this->context->link->getImageLink(
                $image['id_image'],
                $image['id_image'],
                $imageType ? $imageType : 'medium_default'
            );
        }
        return false;
    }

    // Prestashop 1.6
    public function getControllerTabs()
    {
        if ($this->psVersion < 1.7) {
            $tabs = array(
                array(
                    'name' => $this->l('Variants'),
                    'link' => $this->context->link->getAdminLink('AdminProductVariants'),
                    'active' => !!(Tools::getValue('controller') == 'AdminProductVariants')
                ),
                array(
                    'name' => $this->l('Groups'),
                    'link' => $this->context->link->getAdminLink('AdminVariantGroups'),
                    'active' => !!(Tools::getValue('controller') == 'AdminVariantGroups')
                ),
            );

            return $this->getHtmlElement(
                'controller_tabs',
                array(
                    'tabs' => $tabs
                )
            );
        }
    }

    public function getFilterElements($elements, $type, $label, $isMultiple = true)
    {
        $this->context->smarty->assign(
            array(
                'elements' => $elements,
                'type' => $type,
                'label' => $label,
                'isMultiple' => (int)$isMultiple
            )
        );

        return $this->display(
            $this->name,
            'views/templates/admin/filter_element.tpl'
        );
    }

    public function cleanData($rows)
    {
        return array_map(
            function ($row) {
                return iconv("UTF-8", "UTF-8//IGNORE", $row);
            },
            $rows
        );
    }

    public function importExportForm($controller, $label, $sampleFile, $exportLink)
    {
        $fieldsForm = array();
        $fieldsForm[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Import / Export'),
                'icon' => 'icon-download',
            ),
            'description' => $this->getHtmlElement(
                'import_export',
                array(
                    'label' => $label,
                    'sample_link' => Tools::getHttpHost(true) . __PS_BASE_URI__.
                    'modules/'.$this->name.'/'.$sampleFile,
                    'export_link' => $exportLink
                )
            ),
            'input' => array(
                array(
                    'type' => 'file',
                    'label' => $this->l('Upload CSV'),
                    'name' => 'import_file',
                    'required' => true,
                    'desc' => sprintf(
                        $this->l('Upload valid CSV file to import %s.'),
                        $label
                    )
                ),
            ),
            'buttons' => array(
                'submit' => array(
                    'type' => 'submit',
                    'name' => 'importBtnSubmit',
                    'icon' => 'process-icon-upload',
                    'class' => 'btn btn-default pull-right',
                    'title' => $this->l('Import'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite($controller);
        $helper->currentIndex = AdminController::$currentIndex;
        $helper->submit_action = 'submitImportCSV';

        // Language
        $helper->default_form_language = $this->idDefaultLang;
        $helper->allow_employee_form_lang = $this->idContextLang;
        $helper->languages = $this->context->controller->getLanguages();

        return $helper->generateForm($fieldsForm);
    }

    public function displayPromo()
    {
        $modules = array(
            array(
                'name' => $this->l('Advanced Wishlist | Favorites | Save for later'),
                'image' => 'https://medias2.prestastore.com/img/pico/49822.jpg',
                'link' => 'https://addons.prestashop.com/en/wishlist-gift-card/49822-advanced-wishlist-favorites-save-for-later.html',
                'demo' => 'https://addons.prestashop.com/demo/FO39785.html',
                'rating' => 45
            ),
            array(
                'name' => $this->l('Product Combination Images Swatch | Attributes'),
                'image' => 'https://medias2.prestastore.com/img/pico/49820.jpg',
                'link' => 'https://addons.prestashop.com/en/combinaisons-customization/49820-product-combination-images-swatch-attributes.html',
                'demo' => 'https://addons.prestashop.com/demo/FO39783.html',
                'rating' => 5
            ),
            array(
                'name' => $this->l('Product Video (Youtube, Vimeo, Link, Upload) + SEO'),
                'image' => 'https://addons.prestashop.com/img/pico/85673.jpg',
                'link' => 'https://addons.prestashop.com/en/videos-music/85673-product-video-youtube-vimeo-link-upload-seo.html',
                'demo' => 'https://addons.prestashop.com/demo/FO75642.html',
                'rating' => 0
            ),
            array(
                'name' => $this->l('Products Help - Ask Question about Product'),
                'image' => 'https://medias2.prestastore.com/img/pico/52112.jpg',
                'link' => 'https://addons.prestashop.com/en/faq-frequently-asked-questions/52112-products-help-ask-question-about-product.html',
                'demo' => 'https://addons.prestashop.com/demo/FO42080.html',
                'rating' => 0
            ),
            array(
                'name' => $this->l('Custom Product Variants'),
                'image' => 'https://medias2.prestastore.com/img/pico/50341.jpg',
                'link' => 'https://addons.prestashop.com/en/cross-selling-product-bundles/50341-custom-product-variants.html',
                'demo' => 'https://addons.prestashop.com/demo/FO40305.html',
                'rating' => 0
            ),
            array(
                'name' => $this->l('Advanced Product Importer & Affiliate | Multi websites'),
                'image' => 'https://medias2.prestastore.com/img/pico/49628.jpg',
                'link' => 'https://addons.prestashop.com/en/marketplaces/49628-advanced-product-importer-affiliate-multi-websites.html',
                'demo' => 'https://addons.prestashop.com/demo/BO39590.html',
                'rating' => 0
            ),
            array(
                'name' => $this->l('Customer Account Settings, Delete Customer Account'),
                'image' => 'https://addons.prestashop.com/img/pico/86277.jpg',
                'link' => 'https://addons.prestashop.com/en/legal/86277-customer-account-settings-delete-customer-account.html',
                'demo' => 'https://addons.prestashop.com/demo/FO76251.html',
                'rating' => 0
            ),
            array(
                'name' => $this->l('Display Add To Cart Button In Product List'),
                'image' => 'https://addons.prestashop.com/img/pico/51591.jpg',
                'link' => 'https://addons.prestashop.com/en/registration-ordering-process/51591-display-add-to-cart-button-in-product-list.html',
                'demo' => 'https://addons.prestashop.com/demo/FO41558.html',
                'rating' => 0
            ),
            array(
                'name' => $this->l('Advanced Multiple Carts - No More Abandoned Carts'),
                'image' => 'https://addons.prestashop.com/img/pico/52162.jpg',
                'link' => 'https://addons.prestashop.com/en/remarketing-shopping-cart-abandonment/52162-advanced-multiple-carts-no-more-abandoned-carts.html',
                'demo' => 'https://addons.prestashop.com/demo/FO42130.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Advanced Multi Wishlist with Discounts and Notification'),
                'image' => 'https://addons.prestashop.com/img/pico/51872.jpg',
                'link' => 'https://addons.prestashop.com/en/wishlist-gift-card/51872-advanced-multi-wishlist-with-discounts-and-notification.html',
                'demo' => 'https://addons.prestashop.com/demo/FO41840.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Price Tax Switcher | Display Price Tax incl. or excl.'),
                'image' => 'https://addons.prestashop.com/img/pico/50070.jpg',
                'link' => 'https://addons.prestashop.com/en/price-management/50070-price-tax-switcher-display-price-tax-incl-or-excl.html',
                'demo' => 'https://addons.prestashop.com/demo/FO40034.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Product Combination Attribute Images'),
                'image' => 'https://addons.prestashop.com/img/pico/53007.jpg',
                'link' => 'https://addons.prestashop.com/en/combinaisons-customization/53007-product-combination-attribute-images.html',
                'demo' => 'https://addons.prestashop.com/demo/FO42975.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Post Payment & Pay for me'),
                'image' => 'https://addons.prestashop.com/img/pico/51024.jpg',
                'link' => 'https://addons.prestashop.com/en/other-payment-methods/51024-post-payment-pay-for-me.html',
                'demo' => 'https://addons.prestashop.com/demo/FO40991.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Advanced Ticker Notification Banner Slider'),
                'image' => 'https://addons.prestashop.com/img/pico/85107.jpg',
                'link' => 'https://addons.prestashop.com/en/sliders-galleries/85107-advanced-ticker-notification-banner-slider.html',
                'demo' => 'https://addons.prestashop.com/demo/FO75076.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Product FAQ | Product Questions & Answers'),
                'image' => 'https://addons.prestashop.com/img/pico/86383.jpg',
                'link' => 'https://addons.prestashop.com/en/faq-frequently-asked-questions/86383-product-faq-product-questions-answers.html',
                'demo' => 'https://addons.prestashop.com/demo/FO76357.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Advanced Customer Reviews and Ratings, Images, Videos'),
                'image' => 'https://addons.prestashop.com/img/pico/88531.jpg',
                'link' => 'https://addons.prestashop.com/en/customer-reviews/88531-advanced-customer-reviews-and-ratings-images-videos.html',
                'demo' => 'https://addons.prestashop.com/demo/FO78514.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Advance Product Comparison | Attributes and Features'),
                'image' => 'https://addons.prestashop.com/img/pico/86837.jpg',
                'link' => 'https://addons.prestashop.com/en/price-comparison/86837-advance-product-comparison-attributes-and-features.html',
                'demo' => 'https://addons.prestashop.com/demo/FO76812.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Redirections - 301, 302, 404, Register, Login & Logout'),
                'image' => 'https://addons.prestashop.com/img/pico/87573.jpg',
                'link' => 'https://addons.prestashop.com/en/url-redirects/87573-redirections-301-302-404-register-login-logout.html',
                'demo' => 'https://addons.prestashop.com/demo/FO77550.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('WhatsApp Live Chat With Customers - Multiple Agents'),
                'image' => 'https://addons.prestashop.com/img/pico/87470.jpg',
                'link' => 'https://addons.prestashop.com/en/support-online-chat/87470-whatsapp-live-chat-with-customers-multiple-agents.html',
                'demo' => 'https://addons.prestashop.com/demo/FO77447.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Product Variants Pro | Custom Variant Groups'),
                'image' => 'https://addons.prestashop.com/img/pico/86590.jpg',
                'link' => 'https://addons.prestashop.com/en/combinaisons-customization/86590-product-variants-pro-custom-variant-groups.html',
                'demo' => 'https://addons.prestashop.com/demo/FO76564.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Recently Viewed and Ordered Products and Text'),
                'image' => 'https://addons.prestashop.com/img/pico/88794.jpg',
                'link' => 'https://addons.prestashop.com/en/cross-selling-product-bundles/88794-recently-viewed-and-ordered-products-and-text.html',
                'demo' => 'https://addons.prestashop.com/demo/FO78777.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Notify Me Pro - Price Drop, Back in Stock, New Arrivals'),
                'image' => 'https://addons.prestashop.com/img/pico/89522.jpg',
                'link' => 'https://addons.prestashop.com/en/emails-notifications/89522-notify-me-pro-price-drop-back-in-stock-new-arrivals.html',
                'demo' => 'https://addons.prestashop.com/demo/FO79514.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Proposed Delivery Date and Cost by Country and Zipcode'),
                'image' => 'https://addons.prestashop.com/img/pico/89099.jpg',
                'link' => 'https://addons.prestashop.com/en/delivery-date/89099-proposed-delivery-date-and-cost-by-country-and-zipcode.html',
                'demo' => 'https://addons.prestashop.com/demo/FO79083.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Fast Order with Instant and File Search - B2B & B2C'),
                'image' => 'https://addons.prestashop.com/img/pico/89882.jpg',
                'link' => 'https://addons.prestashop.com/en/b2b/89882-fast-order-with-instant-and-file-search-b2b-b2c.html',
                'demo' => 'https://addons.prestashop.com/demo/FO79877.html',
                'rating' => 0,
            ),
            array(
                'name' => $this->l('Display Product Combination Attributes'),
                'image' => 'https://addons.prestashop.com/img/pico/89906.jpg',
                'link' => 'https://addons.prestashop.com/en/product-page/89906-display-product-combination-attributes.html',
                'demo' => 'https://addons.prestashop.com/demo/FO79901.html',
                'rating' => 0,
            )
        );

        $random = array_rand($modules, 6);

        $modules = array_filter(
            $modules,
            function ($m, $i) use ($random) {
                if (in_array($i, $random)) {
                    return $m;
                }
            },
            ARRAY_FILTER_USE_BOTH
        );

        $this->context->smarty->assign('modules', $modules);

        return $this->display($this->name, 'views/templates/admin/promo.tpl');
    }
}
