<?php
/**
 * ColorFeatures
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2021 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.6
 * @link      https://www.silbersaiten.de
 */

class AdminColorFeaturesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        parent::__construct();
    }

    public function ajaxProcessGetTemplate()
    {
        $id_feature_value = (int)Tools::getValue('id_feature_value');
        if ($id_feature_value) {
            $feature = Db::getInstance()->getRow(
                'SELECT * FROM `' . _DB_PREFIX_ . 'color_features` WHERE `id_feature_value`=' . (int)($id_feature_value)
            );
        } else {
            $feature = array();
        }

        $this->context->smarty->assign(array(
            'feature' => $feature,
            'image_path' => _MODULE_DIR_ . $this->module->name . '/img/',
            'image' => isset($feature['texture_extension']) && $feature['texture_extension'] ? $feature['id_color_feature'] . '.' . $feature['texture_extension'] : '',
        ));

        $tpl_path = _PS_MODULE_DIR_ . 'colorfeatures/views/templates/admin/colour_block.tpl';

        if (version_compare(_PS_VERSION_, '1.7.5.0', '<')) {
            $this->ajaxDie($this->context->smarty->display($tpl_path));
        } else {
            $this->ajaxRender($this->context->smarty->fetch($tpl_path));
            die;
        }
    }

    public function ajaxProcessRemoveTextureImage()
    {
        $id_color_feature = (int)Tools::getValue('id_color_feature');
        if ($id_color_feature) {
            $color_value = Db::getInstance()->getRow(
                'SELECT * FROM `' . _DB_PREFIX_ . 'color_features` WHERE `id_color_feature`=' . (int)($id_color_feature)
            );
            $file = _PS_MODULE_DIR_ . $this->module->name . '/img/' . $id_color_feature . '.' . $color_value['texture_extension'];

            if (file_exists($file)) {
                unlink($file);
            }

            Db::getInstance()->update('color_features', array('texture_extension' => '', 'is_texture' => 0), '`id_color_feature`=' . (int)($id_color_feature));

            die(json_encode([
                'success' => false,
                'error_msg' => $this->l('Cannot delete the image!'),
                'success_msg' => $this->l('The image deleted!'),
            ]));
        }
    }

    public function initContent()
    {
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        if ($action = Tools::getValue('action')) {
            $this->replaceFiles($action);
        }

        $this->initConfigurationContent();

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    private function initConfigurationContent()
    {
        $already_replaced = $this->hasReplaced();

        $this->context->smarty->assign(array(
            'link_over' => self::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminColorFeatures', $this->context) . '&action=' . ($already_replaced ? 'back' : 'replace'),
            'over_back' => $already_replaced
        ));
        $this->content .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'colorfeatures/views/templates/admin/actions.tpl');
    }

    private function replaceFiles($action)
    {
        switch ($action) {
            case 'back':
                return $this->replaceBack();
                break;
            case 'replace':
            default:
                return $this->replace();
                break;
        }
    }

    private function hasReplaced()
    {
        $path = _PS_MODULE_DIR_ . 'ps_facetedsearch/src/Filters/Converter.php';

        if (!file_exists($path)) {
            return false;
        }

        $file = file_get_contents($path);
        return stristr($file, 'colorfeatures');
    }

    private function replace()
    {
        $faceted_module = Module::getInstanceByName('ps_facetedsearch');

        if ($faceted_module && version_compare($faceted_module->version, '3.7.1', '>=')) {
            $replacing_path = _PS_MODULE_DIR_ . 'colorfeatures/replacing_files/Converter_3_7_1.php';
        } else {
            $replacing_path = _PS_MODULE_DIR_ . 'colorfeatures/replacing_files/Converter.php';
        }

        $path = _PS_MODULE_DIR_ . 'ps_facetedsearch/src/Filters/Converter.php';

        if (file_exists($path)) {
            copy($path, $path . '__backup');
            copy($replacing_path, $path);
        }
    }

    private function replaceBack()
    {
        $path = _PS_MODULE_DIR_ . 'ps_facetedsearch/src/Filters/Converter.php';

        if (file_exists($path . '__backup')) {
            copy($path . '__backup', $path);
            unlink($path . '__backup');
        }
    }
}
