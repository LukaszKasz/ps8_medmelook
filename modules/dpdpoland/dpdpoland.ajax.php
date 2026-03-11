<?php
/**
 * 2019 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 * @author    DPD Polska Sp. z o.o.
 * @copyright 2019 DPD Polska Sp. z o.o.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska Sp. z o.o.
 */

include_once(dirname(__FILE__) . '/../../config/config.inc.php');

if (version_compare(_PS_VERSION_, '1.5', '<')) {
    include_once(dirname(__FILE__) . '/../../init.php');
}

/** @var DpdPoland $module_instance */
$module_instance = Module::getInstanceByName('dpdpoland');

if (!Tools::isSubmit('token') || (Tools::isSubmit('token')) && Tools::getValue('token') != sha1(_COOKIE_KEY_ . $module_instance->name)) exit;

if (Tools::isSubmit('save_pudo_id')) {
    echo $module_instance->savePudoMapCodeFrontend();
    exit;
}

if (Tools::isSubmit('call_pudo_address')) {
    $pudo_code = Tools::getValue('pudo_code');
    $pudoAddress = $module_instance->pudoService->getPudoAddress($pudo_code);
    if ($pudoAddress != null)
        echo $pudoAddress->address1 . ', ' . $pudoAddress->postcode . ' ' . $pudoAddress->city.', '.$pudo_code;
    else
        echo $pudo_code;

    exit;
}

if (Tools::isSubmit('call_has_pudo_cod')) {
    $pudo_code = Tools::getValue('pudo_code');
    echo $module_instance->pudoService->hasCodService($pudo_code);

    exit;
}

if (Tools::isSubmit('getFormattedAddressHTML')) {
    $id_address = (int)Tools::getValue('id_address');
    echo $module_instance->getFormattedAddressHTML($id_address);
    exit;
}

if (Tools::isSubmit('getFormattedSenderAddressHTML')) {
    $id_address = (int)Tools::getValue('id_address');
    echo $module_instance->getFormattedSenderAddressHTML($id_address);
    exit;
}

if (Tools::isSubmit('getProducts')) {
    echo json_encode($module_instance->searchProducts(Tools::getValue('q')));
    exit;
}

if (Tools::isSubmit('savePackagePrintLabels')) {
    if (!$id_package_ws = $module_instance->savePackageFromPost()) {
        die(json_encode(array(
            'error' => reset(DpdPoland::$errors)
        )));
    }

    $printout_format = Tools::getValue('dpdpoland_printout_format');

    if ($printout_format != DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL && $printout_format != DpdPolandConfiguration::PRINTOUT_FORMAT_A4)
        $printout_format = DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL;

    die(json_encode(array(
        'error' => false,
        'id_package_ws' => (int)$id_package_ws,
        'link_to_labels_pdf' => '?printLabels&id_package_ws=' . (int)$id_package_ws . '&printout_format=' . $printout_format . '&token=' . Tools::getValue('token')
    )));
}

if (Tools::isSubmit('printLabels')) {
    $printout_format = Tools::getValue('dpdpoland_printout_format');

    if ($printout_format != DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL && $printout_format != DpdPolandConfiguration::PRINTOUT_FORMAT_A4)
        $printout_format = DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL;

    die(json_encode(array(
        'error' => false,
        'link_to_labels_pdf' => '?printLabels&id_package_ws=' . (int)Tools::getValue('id_package_ws') .
            '&printout_format=' . $printout_format . '&token=' . Tools::getValue('token')
    )));
}

if (Tools::getValue('addDPDClientNumber')) {
    $result = $module_instance->addDPDClientNumber();
    die(json_encode($result));
}

if (Tools::getValue('deleteDPDClientNumber')) {
    $result = $module_instance->deleteDPDClientNumber();
    die(json_encode($result));
}

if (Tools::getValue('getPayerNumbersTableHTML')) {
    $html = $module_instance->getPayerNumbersTableHTML();
    die(json_encode($html));
}

if (Tools::getValue('calculateTimeLeft')) {
    $time_left = $module_instance->calculateTimeLeft();
    die(json_encode($time_left));
}

if (Tools::getValue('getTimeFrames')) {
    $html = $module_instance->getTimeFrames();
    die(json_encode($html));
}