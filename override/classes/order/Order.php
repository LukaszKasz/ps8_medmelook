<?php

class Order extends OrderCore
{
    /*
    * module: inpostshipping
    * date: 2025-02-09 22:21:05
    * version: 2.7.1
    */
    public function getWebserviceParameters($ws_params_attribute_name = null)
    {
        if (($module = Module::getInstanceByName('inpostshipping')) && $module->active) {
            $this->webserviceParameters['fields']['inpost_point'] = [
                'getter' => 'getWsInPostPoint',
                'setter' => false,
            ];
        }
        return parent::getWebserviceParameters($ws_params_attribute_name);
    }
    /*
    * module: inpostshipping
    * date: 2025-02-09 22:21:05
    * version: 2.7.1
    */
    public function getWsInPostPoint()
    {
        
        $module = Module::getInstanceByName('inpostshipping');
        if (!$module || !$module->active) {
            return null;
        }
        $choice = new InPostCartChoiceModel($this->id_cart);
        if (!Validate::isLoadedObject($choice) || 'inpost_locker_standard' !== $choice->service) {
            return null;
        }
        $choice = new InPostCartChoiceModel($this->id_cart);
        if ($this instanceof BaseLinkerOrder) {
            
            $pointDataProvider = $module->getService('inpost.shipping.data_provider.point');
            if ($point = $pointDataProvider->getPointData($choice->point)) {
                $this->bl_delivery_point_id = $point->getId();
                $this->bl_delivery_point_name = $point->name;
                $this->bl_delivery_point_address = $point->address['line1'];
                $this->bl_delivery_point_city = $point->address_details['city'];
                $this->bl_delivery_point_postcode = $point->address_details['post_code'];
            }
        }
        return $choice->point;
    }
    /*
    * module: apaczka
    * date: 2026-03-11 18:15:58
    * version: 1.4.0
    */
    public $apaczka_supplier;
    /*
    * module: apaczka
    * date: 2026-03-11 18:15:58
    * version: 1.4.0
    */
    public $apaczka_point;
    
    /*
    * module: apaczka
    * date: 2026-03-11 18:15:58
    * version: 1.4.0
    */
    public function __construct($id = null, $id_lang = null)
    {
        if (Module::isEnabled("apaczka")) {
            self::$definition['fields']['apaczka_supplier'] =  array('type' => self::TYPE_STRING);
            self::$definition['fields']['apaczka_point'] =  array('type' => self::TYPE_STRING);
            $this->webserviceParameters['fields']['apaczka_supplier'] = [];
            $this->webserviceParameters['fields']['apaczka_point'] = [];
        }
        parent::__construct($id, $id_lang);
    }
}
