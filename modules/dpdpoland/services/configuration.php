<?php

namespace DpdPoland\Service;

use Address;
use DpdPolandConfiguration;
use Order;
use Product;

class ConfigurationService
{

    public function __construct()
    {
    }

    /**
     * Collects Ref1 parameter from order
     *
     * @param Order $order Order object
     * @param $products
     * @return int|string Ref1 parameter
     */
    public function getDefaultRef1(Order $order, $products)
    {
        $configuration = new DpdPolandConfiguration();

        if ($configuration->ref1 == DpdPolandConfiguration::ADDITIONAL_TYPE_NONE) {
            return '';
        }

        if ($configuration->ref1 == DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC) {
            return $configuration->ref1_static;
        }
        if ($configuration->ref1 == DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC) {
            switch ($configuration->ref1_dynamic) {
                case DpdPolandConfiguration::DYNAMIC_ORDER_ID:
                    return (int)$order->id;
                case DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE:
                    return $order->reference;
                case DpdPolandConfiguration::DYNAMIC_INVOICE_ID:
                    return (int)$order->invoice_number;
                case DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS:
                    $shipping_address = new Address((int)$order->id_address_delivery);
                    return $shipping_address->other;
                case DpdPolandConfiguration::DYNAMIC_PRODUCT_NAME:
                    return count($products) > 0 ? $products[0]['name'] : '';
            }
        }

        return '';
    }

    /**
     * Collects Ref2 parameter from order
     *
     * @param Order $order Order object
     * @param $products
     * @return int|string Ref2 parameter
     */
    public function getDefaultRef2(Order $order, $products)
    {
        $configuration = new DpdPolandConfiguration();

        if ($configuration->ref2 == DpdPolandConfiguration::ADDITIONAL_TYPE_NONE) {
            return '';
        }

        if ($configuration->ref2 == DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC) {
            return $configuration->ref2_static;
        }

        if ($configuration->ref2 == DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC) {
            switch ($configuration->ref2_dynamic) {
                case DpdPolandConfiguration::DYNAMIC_ORDER_ID:
                    return (int)$order->id;
                case DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE:
                    return $order->reference;
                case DpdPolandConfiguration::DYNAMIC_INVOICE_ID:
                    return (int)$order->invoice_number;
                case DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS:
                    $shipping_address = new Address((int)$order->id_address_delivery);
                    return $shipping_address->other;
                case DpdPolandConfiguration::DYNAMIC_PRODUCT_NAME:
                    return count($products) > 0 ? $products[0]['name'] : '';
            }
        }

        return '';
    }

    /**
     * Collects Data1 parameter from order
     *
     * @param Order $order Order object
     * @param $products
     * @return int|string Data1 parameter
     */
    public function getDefaultCustomerData1(Order $order, $products)
    {
        $configuration = new DpdPolandConfiguration();

        if ($configuration->customer_data_1 == DpdPolandConfiguration::ADDITIONAL_TYPE_NONE) {
            return '';
        }

        if ($configuration->customer_data_1 == DpdPolandConfiguration::ADDITIONAL_TYPE_STATIC) {
            return $configuration->customer_data_static;
        }

        if ($configuration->customer_data_1 == DpdPolandConfiguration::ADDITIONAL_TYPE_DYNAMIC) {
            switch ($configuration->customer_data_dynamic) {
                case DpdPolandConfiguration::DYNAMIC_ORDER_ID:
                    return (int)$order->id;
                case DpdPolandConfiguration::DYNAMIC_ORDER_REFERENCE:
                    return $order->reference;
                case DpdPolandConfiguration::DYNAMIC_INVOICE_ID:
                    return (int)$order->invoice_number;
                case DpdPolandConfiguration::DYNAMIC_SHIPPING_ADDRESS:
                    $shipping_address = new Address((int)$order->id_address_delivery);
                    return $shipping_address->other;
                case DpdPolandConfiguration::DYNAMIC_PRODUCT_NAME:
                    return count($products) > 0 ? $products[0]['name'] : '';
            }
        }

        return '';
    }
}