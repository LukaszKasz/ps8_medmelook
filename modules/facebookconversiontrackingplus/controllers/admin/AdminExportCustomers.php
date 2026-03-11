<?php
/** * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)* @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 * ****************************************************
 * *                    Pixel Plus                    *
 * *          http://www.smart-modules.com            *
 *
 * Versions:
 * To check the complete changelog. open versions.txt file
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminExportCustomersController extends ModuleAdminController
{
    public function initContent()
    {
        if ($this->module->getProcess(Tools::getValue('typexp'))) {
            $module_folder = _PS_MODULE_DIR_ . $this->module->name . '/csv/';
            $filename = [1 => 'export-customers.csv', 2 => 'export-newsletter.csv', 3 => 'export-all.csv'];
            $file = $module_folder . $filename[Tools::getValue('typexp')];
            if (file_exists($file)) {
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename=' . basename($file) . ';');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            } else {
                exit('No customers found or the file is not generated.');
            }
        } else {
            exit('Unable to generate file, due to invalid input.');
        }
        exit;
    }
}
