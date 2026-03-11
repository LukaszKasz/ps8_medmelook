<?php

namespace DpdPoland\Controller\Admin;

use DpdPoland\Service\ShippingService;
use DpdPolandConfiguration;
use LabelService;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Tools;

require_once(_PS_MODULE_DIR_ . 'dpdpoland/services/shipping.php');
require_once(_PS_MODULE_DIR_ . 'dpdpoland/services/pudo.php');
require_once(_PS_MODULE_DIR_ . 'dpdpoland/services/label.php');
require_once(_PS_MODULE_DIR_ . 'dpdpoland/classes/Configuration.php');

class BulkGenerateShippingController extends FrameworkBundleAdminController
{
    public function generateWithLabel()
    {
        $shippingService = new ShippingService;

        $selected = Tools::getValue('order_orders_bulk');
        $dpdConfiguration = new DpdPolandConfiguration;

        $mapper = (array)$shippingService->mapShippingListModel($selected);

        if ($shippingService::$errors) {
            $this->addFlash(
                'error',
                $this->trans(implode(", ", $shippingService::$errors), 'Admin.Notifications.Success')
            );
            return $this->redirectToRoute('admin_orders_index');
        }

        $result = $shippingService->savePackageFromPost($mapper, $dpdConfiguration->customer_fid);

        if (count($result) == 0) {
            $this->addFlash(
                'error',
                $this->trans(implode(", ", $shippingService::$errors), 'Admin.Notifications.Success')
            );
            return $this->redirectToRoute('admin_orders_index');
        }

        $labelService = new LabelService;

        $labelService->printMultipleLabels(DpdPolandConfiguration::PRINTOUT_FORMAT_A4, $selected);

        if ($labelService->getErrors()) {
            $this->addFlash(
                'error',
                $this->trans(implode(", ", $labelService->getErrors()), 'Admin.Notifications.Success')
            );
        } else {
            $this->addFlash(
                'success',
                $this->trans('Shipments have been generated', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_orders_index');
    }

    public function generate()
    {
        $shippingService = new ShippingService;

        $selected = Tools::getValue('order_orders_bulk');
        $dpdConfiguration = new DpdPolandConfiguration;

        $mapper = (array)$shippingService->mapShippingListModel($selected);

        if ($shippingService::$errors) {
            $this->addFlash(
                'error',
                $this->trans(implode(", ", $shippingService::$errors), 'Admin.Notifications.Success')
            );
            return $this->redirectToRoute('admin_orders_index');
        }

        $result = $shippingService->savePackageFromPost($mapper, $dpdConfiguration->customer_fid);

        if (count($result) == 0) {
            $this->addFlash(
                'error',
                $this->trans(implode(", ", $shippingService::$errors), 'Admin.Notifications.Success')
            );
            return $this->redirectToRoute('admin_orders_index');
        } else {
            $this->addFlash(
                'success',
                $this->trans('Shipments have been generated', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_orders_index');
    }

    public function generateLabel()
    {
        $selected = Tools::getValue('order_orders_bulk');

        $labelService = new LabelService;

        $labelService->printMultipleLabels(DpdPolandConfiguration::PRINTOUT_FORMAT_A4, $selected);

        if ($labelService->getErrors()) {
            $this->addFlash(
                'error',
                $this->trans(implode(", ", $labelService->getErrors()), 'Admin.Notifications.Success')
            );
        } else {
            $this->addFlash(
                'success',
                $this->trans('Shipments have been generated', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_orders_index');
    }
}