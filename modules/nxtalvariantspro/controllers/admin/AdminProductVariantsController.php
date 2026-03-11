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

use PrestaShop\Module\Nxtalvariantspro\VariantGroup;
use PrestaShop\Module\Nxtalvariantspro\VariantProduct;

class AdminProductVariantsController extends ModuleAdminController
{
    public $variantGroups = array();

    public function __construct()
    {
        $this->table = 'nxtal_variant_product';
        $this->className = 'PrestaShop\Module\Nxtalvariantspro\VariantProduct';
        $this->identifier = 'id_variant_product';

        parent::__construct();

        $this->bootstrap = true;

        $this->_conf[40] = $this->l('Variant generated successfully.');

        $this->variantGroups = VariantGroup::getVariantGroups($this->module->idDefaultLang);

        $variantGroups = array();
        foreach ($this->variantGroups as $variantGroup) {
            $variantGroups[$variantGroup['id_variant_group']] = $variantGroup['name'];
        }

        $types = array();
        foreach ($this->module->variantTypes as $type) {
            $types[$type['id']] = $type['name'];
        }

        $this->fields_list = array(
            'id_variant_product' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'text-center'
            ),
            'id_variant_group' => array(
                'title' => $this->l('Variant group'),
                'align' => 'text-center',
                'filter_key' => 'a!id_variant_group',
                'type' => 'select',
                'list' => $variantGroups,
                'callback' => 'setGroup'
            ),
            'type' => array(
                'title' => $this->l('Type'),
                'align' => 'text-center',
                'filter_key' => 'a!type',
                'type' => 'select',
                'list' => $types,
                'callback' => 'setType'
            ),
            'products' => array(
                'title' => $this->l('Products'),
                'align' => 'text-center',
                'search' => false,
                'remove_onclick' => true,
                'callback' => 'setProducts'
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool'
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->_conf[33] = sprintf(
            $this->l('%d Variants imported successfully.'),
            Tools::getValue('count', 0)
        );
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add New'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $output = $this->module->getControllerTabs() .
            parent::renderList() .
            $this->generateVariantForm() .
            $this->module->displayForm();

        $controller = Tools::getValue('controller');
        $exportLink = '';

        if ($this->loadObject(true)->isExist(null, true)) {
            $exportLink = $this->context->link->getAdminLink($controller).'&export=1';
        }

        $output .= $this->module->importExportForm(
            $controller,
            $this->l('Variants'),
            'variantSample.csv',
            $exportLink
        );

        $output .= $this->module->displayPromo();

        return $output;
    }

    public function generateVariantForm()
    {
        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Generate Variants'),
                'icon' => 'icon-cog'
            ),
            'description' => $this->l('Layered variants of multiple variant groups (color, size, etc.) can be created with using this section.'),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Variant groups'),
                    'name' => 'groups[]',
                    'required' => true,
                    'class' => 'chosen',
                    'multiple' => true,
                    'options' => array(
                        'query' => $this->variantGroups,
                        'id' => 'id_variant_group',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Set variant groups, more than one variant groups must be selected.')
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Products'),
                    'required' => true,
                    'name' => 'products',
                    'html_content' => $this->getFilterAttributes(Tools::getValue('products', array())),
                    'desc' => $this->l('Select the specific products you want to make variants. All products must have the value of all the features of the selected groups.')
                ),
                array(
                    'type' => 'switch',
                    'name' => 'active',
                    'label' => $this->l('Enable variants'),
                    'desc' => $this->l('Set variant default status.'),
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
                )
            ),
            'submit' => array(
                'type' => 'submit',
                'name' => 'generateVariant',
                'icon' => 'process-icon-save',
                'class' => 'btn btn-default pull-right',
                'title' => $this->l('Save')
            ),
        );

        $helper = new HelperForm();
        $helper->token = Tools::getAdminTokenLite('AdminProductVariants');
        $helper->currentIndex = AdminController::$currentIndex;

        // Language
        $helper->default_form_language = $this->module->idDefaultLang;
        $helper->allow_employee_form_lang = $this->module->idDefaultLang;
        $helper->languages = $this->context->controller->getLanguages();

        // Title and toolbar
        $helper->submit_action = 'generateVariants';

        $helper->fields_value = array(
            'groups[]' => Tools::getValue('groups', array()),
            'active' => Tools::getValue('active', 0)
        );

        return $helper->generateForm($fields_form);
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $selectedProducts = array();
        $selectedCategories = array();
        $selectedManufacturers = array();
        $selectedIdFeature = 0;

        if ($obj->id) {
            $selectedIdFeature = $obj->id_feature;
            $selectedProducts = VariantProduct::explode($obj->products);
            $selectedCategories = VariantProduct::explode($obj->categories);
            $selectedManufacturers = VariantProduct::explode($obj->manufacturers);
        }

        $selectedIdFeature = Tools::getValue('id_feature', $selectedIdFeature);
        $selectedProducts = Tools::getValue('products', $selectedProducts);
        $selectedCategories = Tools::getValue('categories', $selectedCategories);
        $selectedManufacturers = Tools::getValue('manufacturers', $selectedManufacturers);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Variant'),
                'icon' => 'icon-edit'
           ),
            'input' => array(
                array(
                    'type' => 'text',
                    'name' => 'name',
                    'required' => true,
                    'label' => $this->l('Name'),
                    'desc' => $this-> l('Enter the name of the variant, it will not be displayed in the front office.')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Variant group'),
                    'name' => 'id_variant_group',
                    'required' => true,
                    'options' => array(
                        'query' => $this->variantGroups,
                        'id' => 'id_variant_group',
                        'name' => 'name',
                        'default' => array(
                            'label' => $this->l('--'),
                            'value' => 0
                        )
                    ),
                    'desc' => $this->l('Set variant group.')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'options' => array(
                        'query' => $this->module->variantTypes,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Set the type to create the product variants. In case of custom, you have to set the variant for the products manually otherwise the product variant will be set automatically with the products having same value in Variant type field.')
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Products'),
                    'required' => true,
                    'name' => 'products',
                    'html_content' => $this->getFilterAttributes($selectedProducts),
                    'desc' => $this->l('Select the specific products you want to display as variants in Front Office.'),
                    'form_group_class' => 'type_element_custom hide'
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Feature'),
                    'required' => true,
                    'name' => 'feature',
                    'html_content' => $this->getFilterAttributes(array($selectedIdFeature), 'feature', false),
                    'form_group_class' => 'type_element_feature hide',
                    'desc' => $this->l('Select the specific product features, the value of these product features will be displayed as variant label in Front Office.')
                ),
                array(
                    'type' => 'categories',
                    'label' => $this->l('Categories'),
                    'name' => 'categories',
                    'tree' => array(
                        'root_category' => 2,
                        'id' => 'id_category',
                        'selected_categories' => $selectedCategories,
                        'use_checkbox' => true,
                        'use_search' => true
                    ),
                    'form_group_class' => 'type_element hide',
                    'desc' => $this->l('Select the specific categories of products you want to make the variant.')
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Manufacturers'),
                    'name' => 'manufacturers',
                    'html_content' => $this->getFilterAttributes($selectedManufacturers, 'manufacturer'),
                    'desc' => $this->l('Select the specific manufacturers of products you want to make the variant.'),
                    'form_group_class' => 'type_element hide'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'desc' => $this->l('Enable to display in the Front Office'),
                    'values' => array(
                        array(
                            'id' => 'type_switch_on',
                            'value' => 1
                        ),
                        array(
                            'id' => 'type_switch_off',
                            'value' => 0
                        )
                    )
                )
            ),
            'buttons' => array(
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'btnSubmit',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                ),
                'save-and-stay' => array(
                    'title' => $this->l('Save and Stay'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            if (!Tools::getValue('name')
                || !Validate::isGenericName(Tools::getValue('name'))
            ) {
                $this->errors[] = $this->l('Invalid name field value! It should be generic.');
            }

            if (!Tools::getValue('id_variant_group')) {
                $this->errors[] = $this->l('Invalid variant group!');
            }

            if (Tools::getValue('type') == 'custom'
                && !Tools::getValue('products')
            ) {
                $this->errors[] = $this->l('Invalid variant products!');
            } elseif (Tools::getValue('type') == 'feature'
                && !Tools::getValue('id_feature')
            ) {
                $this->errors[] = $this->l('Invalid Feature selection!');
            }
        }

        if ($this->errors) {
            $this->display = 'add';
            return false;
        }

        if (Tools::isSubmit('generateVariants')) {
            $this->generateVariants();
        }

        if (Tools::isSubmit('submitnxtalvariantspro')) {
            if ($this->module->updateConfiguration()) {
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminProductVariants').'&conf=6'
                );
            }
        }

        if (Tools::isSubmit('importBtnSubmit')) {
            if (!$this->validateCSV()) {
                $this->errors[] = $this->l('Invalid CSV file. The file format must match the sample file.');
            } elseif (!$this->getCSVData()) {
                $this->errors[] = $this->l('No data found in CSV file.');
            } else {
                if ($count = $this->importCSV()) {
                    Tools::redirectAdmin(
                        self::$currentIndex.'&conf=33&count='.$count.'&token='.$this->token
                    );
                } else {
                    $this->errors[] = $this->l('Error in importing Variants, It may already exist or invalid, re-check the file data and try again.');
                }
            }
        }

        if (Tools::isSubmit('export')) {
            $this->exportCSV();
            die;
        }

        return parent::postProcess();
    }

    public function processSave()
    {
        parent::processSave();

        $this->module->_clearCache('*');

        return true;
    }

    public function getIndex($variants, $pid, $groupname)
    {
        if ($variants) {
            foreach ($variants as $key => $variant) {
                if ($groupname == $variant['name'] && in_array($pid, $variant['products'])) {
                    return $key;
                }
            }
        }

        return false;
    }

    public function generateVariants()
    {
        if (!$pIds = Tools::getValue('products')) {
            $this->errors[] = $this->l('Products must be selected!');
        }

        $gIds = Tools::getValue('groups', array());

        if (!$gIds || count($gIds) < 2) {
            $this->errors[] = $this->l('Variant groups must be selected in multiple counts!');
        }

        if (!$this->errors) {
            $products = array();

            foreach ($pIds as $pId) {
                $product = new Product($pId, true, $this->module->idContextLang);

                $features = array();

                foreach ($gIds as $gId) {
                    $group = new VariantGroup((int) $gId, $this->module->idContextLang);

                    if ($value = $this->getProductValue($group, $product)) {
                        $features[$this->getGroupFeaturesKey($group->features)] = $value;
                    }
                }

                if ($features) {
                    $products[]  = array(
                        'id' => $product->id,
                        'name' => $product->name,
                        'features' => $features
                   );
                }
            }

            $variants = array();

            foreach ($products as $product1) {
                foreach ($gIds as $gk => $gId) {
                    $group = new VariantGroup((int) $gId, $this->module->idContextLang);

                    $key = $this->getGroupFeaturesKey($group->features);

                    $feature1 = $product1['features'];

                    if (!isset($feature1[$key])) {
                        continue;
                    }

                    $value1 = $feature1[$key];

                    $index = $this->getIndex($variants, $product1['id'], $group->name);
                    if ($index === false) {
                        $variants[]  = array(
                                  'id' => $group->id,
                                  'name' => $group->name,
                                  'products' => array($value1 => $product1['id'])
                            );
                    }

                    unset($feature1[$key]);

                    foreach ($products as $product2) {
                        $feature2 = $product2['features'];

                        if (!isset($feature2[$key]) || $product1['id'] == $product2['id']) {
                            continue;
                        }

                        $value2 = $feature2[$key];

                        unset($feature2[$key]);

                        if ($feature2) {
                            if ($value1 != $value2
                                    && (!array_diff($feature1, $feature2)
                                    || count(array_intersect($feature1, $feature2)) == count($feature2))
                                ) {
                                $index = $this->getIndex($variants, $product1['id'], $group->name);

                                if ($index !== false) {
                                    $index1 = $this->getIndex($variants, $product2['id'], $group->name);

                                    if ($index1 === false) {
                                        if (!in_array($product2['id'], $variants[$index]['products'])) {
                                            $variants[$index]['products'][$value2] = $product2['id'];
                                        }
                                    }
                                }
                            }
                        } elseif ($value1 != $value2) {
                            $index = $this->getIndex($variants, $product1['id'], $group->name);

                            if ($index !== false) {
                                if (!array_key_exists($value2, $variants[$index]['products'])) {
                                    $variants[$index]['products'][$value2] = $product2['id'];
                                }
                            }
                        }
                    }
                }
            }

            if ($variants) {
                foreach ($variants as $variant) {
                    $obj = new VariantProduct();
                    $obj->name = $variant['name'] . ': '. implode(', ', $variant['products']);
                    $obj->id_variant_group = $variant['id'];
                    $obj->type = 'custom';
                    $obj->products = implode(',', $variant['products']);
                    $obj->active = Tools::getValue('active');
                    $obj->save();
                }

                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminProductVariants').'&conf=40'
                );
            } else {
                $this->errors[] = $this->l('The products does not have the similar feature of the selected groups to generate variants!');
            }
        }
    }

    public function getGroupFeatures($features)
    {
        if ($features) {
            $features = VariantProduct::explode($features);
        } else {
            $features = array('id');
        }

        return $features;
    }

    public function getGroupFeaturesKey($features)
    {
        return implode('-', $this->getGroupFeatures($features));
    }

    public function getProductValue($group, $varProduct)
    {
        $values = array();

        $key = $this->getGroupFeaturesKey($group->features);

        if (isset($values[$key])) {
            return $values[$key];
        }

        $features = Product::getFrontFeaturesStatic((int) $this->module->idContextLang, $varProduct->id);

        foreach ($this->getGroupFeatures($group->features) as $featureAttribute) {
            if (is_numeric($featureAttribute)) {
                if ($features) {
                    foreach ($features as $feature) {
                        if ($feature['id_feature'] == $featureAttribute) {
                            $values[$key] = $feature['value'];
                            break 2;
                        }
                    }
                }
            } else {
                if (isset($varProduct->$featureAttribute)
                    && $varProduct->$featureAttribute
                ) {
                    $values[$key] = $varProduct->$featureAttribute;
                    break 1;
                } elseif ('dimension' == $featureAttribute) {
                    if ($varProduct->height > 0 || $varProduct->width > 0 || $varProduct->depth > 0) {
                        $values[$key] = Tools::ps_round($varProduct->height, '2') . ' x '
                            . Tools::ps_round($varProduct->width, '2'). ' x '
                            . Tools::ps_round($varProduct->depth, '2') . ' '
                            . Configuration::get('PS_DIMENSION_UNIT');
                        break 1;
                    }
                }
            }
        }

        if (!isset($values[$key])) {
            $values[$key] = '';
        }

        return $values[$key];
    }

    public function processStatus()
    {
        $this->module->_clearCache('*');

        return parent::processStatus();
    }

    public function getFilterAttributes($selectedIds, $type = 'product', $isMultiple = true)
    {
        $elements = array();
        if ($selectedIds) {
            $selectedIds = !is_array($selectedIds) ? explode(',', $selectedIds) : $selectedIds;

            foreach ($selectedIds as $selectedId) {
                if ($type == 'product') {
                    $product = new Product($selectedId, false, $this->module->idDefaultLang);
                    if ($product->id) {
                        $elements[] = array(
                            'id_product' => $selectedId,
                            'image' => $this->module->getProductCoverImage($selectedId),
                            'reference' => $product->reference,
                            'name' => $this->getElementName($product->name, $product->id, $isMultiple)
                        );
                    }
                } elseif ($type == 'feature') {
                    $feature = new Feature($selectedId, $this->module->idDefaultLang);
                    if ($feature->id) {
                        $elements[] = array(
                            'id_feature' => $selectedId,
                            'name' => $this->getElementName($feature->name, $feature->id, $isMultiple)
                        );
                    }
                } elseif ($type == 'manufacturer') {
                    $manufacturer = new Manufacturer($selectedId);
                    if ($manufacturer->id) {
                        $elements[] = array(
                            'id_manufacturer' => $selectedId,
                            'name' => $this->getElementName($manufacturer->name, $manufacturer->id, $isMultiple)
                        );
                    }
                }
            }
        }

        return $this->module->getFilterElements($elements, $type, $this->l($type), $isMultiple);
    }

    public function getElementName($name, $id, $isMultiple)
    {
        return $isMultiple ? $name : $name . ' #' . $id;
    }

    public function setGroup($idVariantGroup)
    {
        $group = new VariantGroup((int) $idVariantGroup, $this->module->idContextLang);

        return $group->name;
    }

    public function setProducts($productIds, $obj)
    {
        if ($productIds) {
            $productIds = explode(',', $productIds);

            $products = array();

            foreach ($productIds as $idProduct) {
                $product = new Product((int)$idProduct, false, $this->module->idContextLang);

                $linkParams = array(
                    'id_product' => $idProduct,
                    'updateproducts' => 1
                );

                if ($this->module->psVersion < 1.7) {
                    $link = $this->context->link->getAdminLink('AdminProducts') . '&' . http_build_query($linkParams);
                } else {
                    $link = $this->context->link->getAdminLink(
                        'AdminProducts',
                        true,
                        $linkParams,
                        $linkParams
                    );
                }

                $products[] = array(
                    'name' => $product->name,
                    'link' => $link
                );
            }

            return $this->module->getHtmlElement(
                'product_names',
                array(
                    'products' => $products
                )
            );
        }

        return sprintf(
            $this->l('Selected by %s'),
            $this->setType($obj['type'])
        );
    }

    public function setType($type)
    {
        if (isset($this->module->variantTypes[$type]['name'])) {
            return $this->module->variantTypes[$type]['name'];
        }

        return $type;
    }

    public function validateCSV()
    {
        if (!$obj = $this->loadObject(true)) {
            return false;
        }
        $validCSVTypes = array(
            'text/plain',
            'text/csv',
            'text/x-csv',
            'application/vnd.ms-excel'
        );
        if (!isset($_FILES['import_file']['tmp_name'])
            || ($_FILES['import_file']['name']
            && !in_array(mime_content_type($_FILES['import_file']['tmp_name']), $validCSVTypes))
        ) {
            return false;
        }

        if (isset($_FILES['import_file']) && $_FILES['import_file']['name']) {
            $file = $_FILES['import_file']['tmp_name'];
            $csvData = array_map('str_getcsv', file($file));
            $key = array_shift($csvData);
            $result = array_intersect($obj::$csvFields, $key);

            if (count($result) == count($obj::$csvFields)) {
                return true;
            }
        }
        return false;
    }

    public function getCSVData()
    {
        $rows = array();

        $variantTypes = array_column($this->module->variantTypes, 'id');

        if (isset($_FILES['import_file'])) {
            $file = $_FILES['import_file']['tmp_name'];
            $csvData = array_map('str_getcsv', file($file));
            $key = array_shift($csvData);
            foreach ($csvData as $row) {
                $row = array_combine($key, $this->module->cleanData($row));

                if ($row['name']
                    && $row['id_variant_group']
                    && (new VariantGroup($row['id_variant_group']))->id
                    && $row['type']
                    && in_array($row['type'], $variantTypes)
                ) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    public function exportCSV()
    {
        if (!$obj = $this->loadObject(true)) {
            return false;
        }

        $rows = $obj::getVariants(0, array(), null, true);

        $fileName = 'variants_'.date('c').'.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$fileName);
        $output = fopen('php://output', 'w');
        fputcsv($output, $obj::$csvFields);

        foreach ($rows as $row) {
            $data = array_map(
                function ($field) use ($row) {
                    return $row[$field];
                },
                $obj::$csvFields
            );
            fputcsv(
                $output,
                $data
            );
        }
        return true;
    }

    public function importCSV()
    {
        if (!$obj = $this->loadObject(true)) {
            return false;
        }
        $rows = $this->getCSVData();
        return $obj->importData($rows);
    }
}
