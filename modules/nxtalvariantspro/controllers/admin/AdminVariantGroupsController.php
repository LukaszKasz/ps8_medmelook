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

class AdminVariantGroupsController extends ModuleAdminController
{
    protected $position_identifier = 'position';

    public function __construct()
    {
        $this->table = 'nxtal_variant_group';
        $this->className = 'PrestaShop\Module\Nxtalvariantspro\VariantGroup';
        $this->identifier = 'id_variant_group';
        $this->_defaultOrderBy = 'position';
        $this->lang = true;

        parent::__construct();

        $this->bootstrap = true;

        $this->fields_list = array(
            'id_variant_group' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'text-center'
            ),
            'features' => array(
                'title' => $this->l('Display product label'),
                'align' => 'text-center',
                'callback' => 'setFeatures',
                'search' => false
            ),
            'image' => array(
                'title' => $this->l('Display image'),
                'align' => 'text-center',
                'active' => 'image',
                'type' => 'bool'
            ),
            'price' => array(
                'title' => $this->l('Display price'),
                'align' => 'text-center',
                'active' => 'price',
                'type' => 'bool'
            ),
            'outofstock' => array(
                'title' => $this->l('Display outofstock products'),
                'align' => 'text-center',
                'active' => 'outofstock',
                'type' => 'bool'
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'align' => 'center',
                'position' => 'position',
                'class' => 'fixed-width-sm',
                'search' => false
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
            $this->l('%d Variant groups imported successfully.'),
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

        $output = $this->module->getControllerTabs() . parent::renderList();

        $controller = Tools::getValue('controller');
        $exportLink = '';

        if ($this->loadObject(true)->isExist(null, true)) {
            $exportLink = $this->context->link->getAdminLink($controller).'&export=1';
        }

        $output .= $this->module->importExportForm(
            $controller,
            $this->l('Variant groups'),
            'groupSample.csv',
            $exportLink
        );

        $output .= $this->module->displayPromo();

        return $output;
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $selectedAttributes = Tools::getValue('features', $obj->getFeatures());

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Group'),
                'icon' => 'icon-edit'
           ),
            'input' => array(
                array(
                    'type' => 'text',
                    'name' => 'name',
                    'lang' => true,
                    'label' => $this->l('Name'),
                    'desc' => $this-> l('Enter the name of the group, it will be displayed in the front office.')
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('Display variant label'),
                    'required' => true,
                    'name' => 'features',
                    'html_content' => $this->getFilterAttributes($selectedAttributes),
                    'desc' => $this->l('Select the specific product features, the value of these product features will be displayed as variant label in Front Office.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display product image'),
                    'name' => 'image',
                    'desc' => $this->l('Enable to display variant product image.'),
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
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display product price'),
                    'name' => 'price',
                    'desc' => $this->l('Enable to display variant product price.'),
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
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display out of stock variant'),
                    'name' => 'outofstock',
                    'desc' => $this->l('Enable to display out of stock variant products.'),
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
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'desc' => $this->l('Enable to display in the Front Office.'),
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

    public function getFilterAttributes($selectedAttributes)
    {
        $features = array();

        $attributes = $this->module->getFeatures();

        foreach ($selectedAttributes as $selectedAttribute) {
            if (isset($attributes[$selectedAttribute])) {
                $features[] = $attributes[$selectedAttribute];
            }
        }

        return $this->module->getFilterElements(
            $features,
            'feature',
            $this->l('feature')
        );
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            if (Tools::getValue('name_'.$this->module->idDefaultLang)
                && !Validate::isGenericName(Tools::getValue('name_'.$this->module->idDefaultLang))
            ) {
                $this->errors[] = $this->l('Invalid name field value. It should be generic.');
            }

            if (!Tools::getValue('features')) {
                $this->errors[] = $this->l('Invalid variant label. Choose at least one feature attribute.');
            }
        }

        if (Tools::isSubmit('imagenxtal_variant_group')
            && $idVariantGroup = (int) Tools::getValue('id_variant_group')
        ) {
            $obj = new $this->className($idVariantGroup);
            $obj->image = !$obj->image;
            $obj->update();

            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminVariantGroups').'&conf=5'
            );
        }

        if (Tools::isSubmit('pricenxtal_variant_group')
            && $idVariantGroup = (int) Tools::getValue('id_variant_group')
        ) {
            $obj = new $this->className($idVariantGroup);
            $obj->price = !$obj->price;
            $obj->update();

            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminVariantGroups').'&conf=5'
            );
        }

        if (Tools::isSubmit('outofstocknxtal_variant_group')
            && $idVariantGroup = (int) Tools::getValue('id_variant_group')
        ) {
            $obj = new $this->className($idVariantGroup);
            $obj->outofstock = !$obj->outofstock;
            $obj->update();

            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminVariantGroups').'&conf=5'
            );
        }

        if (Tools::isSubmit('productnxtal_variant_group')
            && $idVariantGroup = (int) Tools::getValue('id_variant_group')
        ) {
            $obj = new $this->className($idVariantGroup);
            $obj->product = !$obj->product;
            $obj->update();

            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminVariantGroups').'&conf=5'
            );
        }

        if (Tools::isSubmit('catalognxtal_variant_group')
            && $idVariantGroup = (int) Tools::getValue('id_variant_group')
        ) {
            $obj = new $this->className($idVariantGroup);
            $obj->catalog = !$obj->catalog;
            $obj->update();

            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminVariantGroups').'&conf=5'
            );
        }

        if ($this->errors) {
            $this->display = 'add';
            return false;
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
                    $this->errors[] = $this->l('Error in importing Variant Group, It may already exist or invalid, re-check the file data and try again.');
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

    public function processStatus()
    {
        $this->module->_clearCache('*');

        return parent::processStatus();
    }

    public function processPosition()
    {
        $this->module->_clearCache('*');

        return parent::processPosition();
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) (Tools::getValue('way'));
        $id = (int) (Tools::getValue('id'));
        $positions = Tools::getValue('variant_group');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $id) {
                if ($obj = new VariantGroup((int) $pos[2], (int) $this->module->idContextLang)) {
                    if (isset($position) && $obj->updatePosition($way, $position)) {
                        echo sprintf(
                            $this->l('ok position %d for %s %d'),
                            (int) $position,
                            $obj->name,
                            (int) $pos[1]
                        ). '\r\n';
                    } else {
                        $response = sprintf(
                            $this->l('Can not update %s %d to position %d'),
                            $obj->name,
                            (int) $id,
                            (int) $position
                        );
                        echo '{"hasError" : true, "errors" : "' . $response . '"}';
                    }
                } else {
                    $response = sprintf(
                        $this->l("This %s (%d) can't be loaded"),
                        $obj->name,
                        (int) $id
                    );
                    echo '{"hasError" : true, "errors" : "' . $response . '"}';
                }

                break;
            }
        }
    }

    public function setFeatures($attributes)
    {
        if ($attributes) {
            $attributes = explode(',', $attributes);
            foreach ($attributes as &$attribute) {
                foreach ($this->module->getFeatures() as $attrOption) {
                    if ($attrOption['id_feature'] == $attribute) {
                        $attribute = $attrOption['name'];
                        break 1;
                    }
                }
            }

            return implode(', ', $attributes);
        }
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
        if (isset($_FILES['import_file'])) {
            $file = $_FILES['import_file']['tmp_name'];
            $csvData = array_map('str_getcsv', file($file));
            $key = array_shift($csvData);
            foreach ($csvData as $row) {
                $row = array_combine($key, $this->module->cleanData($row));

                if ($row['name']) {
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
        $rows = $obj::getVariantGroups($this->module->idDefaultLang);
        $fileName = 'variant_groups_'.date('c').'.csv';

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
