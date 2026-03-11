<?php


class LabelService
{

    /** @var Context */
    private $context;

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    /**
     * Prints multiple labels for selected orders
     *
     * @param string $printout_format Printout format (A4 or label)
     * @return array|null Error message
     */
    public function printMultipleLabels($printout_format = DpdPolandConfiguration::PRINTOUT_FORMAT_A4, $orders = null)
    {
        $orders = $orders == null ? Tools::getValue('orderBox') : $orders;

        if (empty($orders)) {
            return $this->displayControllerError($this->translate('No selected orders'));
        }

        $errors = array();
        foreach ($orders as $id_order) {
            $package = DpdPolandPackage::getInstanceByIdOrder((int)$id_order);

            if (!$package->id_package_ws) {
                $errors[] = sprintf($this->translate('Label is not saved for #%d order'), (int)$id_order);
            }
        }

        if ($errors) {
            return $this->displayControllerError($errors);
        }

        $waybills = DpdPolandParcel::getOrdersWaybills($orders);

        if (empty($waybills)) {
            return $this->displayControllerError($this->translate('No available packages'));
        }

        $domestic_waybills = array();
        $international_waybills = array();
        $pdf_directory = $pdf_directory = _PS_MODULE_DIR_ . 'dpdpoland/pdf/';

        foreach ($waybills as $waybill) {
            if (!isset($waybill['sessionType']) || !isset($waybill['waybill'])) {
                continue;
            }

            if ($waybill['sessionType'] == 'domestic' || $waybill['sessionType'] == 'domestic_with_cod' || $waybill['sessionType'] == 'pudo') {
                $domestic_waybills[] = $waybill['waybill'];
            } elseif ($waybill['sessionType'] == 'international') {
                $international_waybills[] = $waybill['waybill'];
            }
        }

        if (empty($domestic_waybills) && empty($international_waybills)) {
            return $this->displayControllerError($this->translate('No available labels'));
        }

        $package = new DpdPolandPackage();

        if ($domestic_waybills) {
            $pdf_content = $package->generateMultipleLabels($domestic_waybills, $printout_format, 'DOMESTIC');

            if ($pdf_content === false) {
                return $this->displayControllerError(reset(DpdPolandPackageWS::$errors));
            }

            if (empty($international_waybills)) {
                $this->displayPDF($pdf_content, 'domestic_labels');
            }

            if (!$this->savePDFFile($pdf_content, 'domestic')) {
                return $this->displayControllerError(
                    $this->translate('Could not create PDF file. Please check module folder permissions')
                );
            }
        }

        if ($international_waybills) {
            $pdf_content = $package->generateMultipleLabels($international_waybills, $printout_format, 'INTERNATIONAL');

            if ($pdf_content === false) {
                return $this->displayControllerError(reset(DpdPolandPackageWS::$errors));
            }

            if (empty($domestic_waybills)) {
                $this->displayPDF($pdf_content, 'international_labels');
            }

            if (!$this->savePDFFile($pdf_content, 'international')) {
                return $this->displayControllerError(
                    $this->translate('Could not create PDF file. Please check module folder permissions')
                );
            }
        }

        if ($domestic_waybills && $international_waybills) {
            include_once(_PS_MODULE_DIR_ . 'dpdpoland/libraries/PDFMerger/PDFMerger.php');

            $pdf = new PDFMerger;

            $pdf->addPDF($pdf_directory . 'label_domestic.pdf', 'all');
            $pdf->addPDF($pdf_directory . 'label_international.pdf', 'all');
            $pdf->merge('file', $pdf_directory . 'multiple_label.pdf');

            ob_end_clean();
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename="multiple_label.pdf"');
            readfile($pdf_directory . 'multiple_label.pdf');

            if (file_exists($pdf_directory . 'label_domestic.pdf') && is_writable($pdf_directory . 'label_domestic.pdf')) {
                unlink($pdf_directory . 'label_domestic.pdf');
            }

            if (file_exists($pdf_directory . 'label_international.pdf') && is_writable($pdf_directory . 'label_international.pdf')) {
                unlink($pdf_directory . 'label_international.pdf');
            }

            if (file_exists($pdf_directory . 'multiple_label.pdf') && is_writable($pdf_directory . 'multiple_label.pdf')) {
                unlink($pdf_directory . 'multiple_label.pdf');
            }
        }

        $url = $this->context->link->getAdminLink('AdminOrders');
        Tools::redirectAdmin($url);
        exit;
    }


    /**
     * Displays error message in module controller
     *
     * @param string|array $messages Error message(s)
     * @return array|null Error message(s)
     */
    private function displayControllerError($messages)
    {
        if (!is_array($messages)) {
            $messages = array($messages);
        }

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            return $messages;
        }

        foreach ($messages as $message) {
            $this->context->controller->errors[] = $message;
        }

        DpdPolandLog::addError(json_encode($messages));

        return null;
    }

    public function getErrors()
    {
        return $this->context->controller->errors;
    }

    private function translate($string)
    {
        return Translate::getModuleTranslation('dpdpoland', $string, 'dpdpoland');
    }


    /**
     * Makes PDF file to be downloadable
     *
     * @param string $pdf_content PDF file content
     * @param $name PDF file name
     */
    private function displayPDF($pdf_content, $name)
    {
        ob_end_clean();
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="' . pSQL($name) . '.pdf"');

        echo $pdf_content;
        exit;
    }

    /**
     * Saves PDF file in module directory
     *
     * @param string $pdf_content PDF content
     * @param string $type Shipment type (domestic, international)
     * @return bool PDF file saved successfully
     */
    private function savePDFFile($pdf_content, $type = 'domestic')
    {
        $pdf_directory = $pdf_directory = _PS_MODULE_DIR_ . 'dpdpoland/pdf/';
        $fp = fopen($pdf_directory . 'label_' . pSQL($type) . '.pdf', 'a');

        if (!$fp) {
            return false;
        }

        fwrite($fp, $pdf_content);
        fclose($fp);

        return true;
    }
}