<?php

class Order extends OrderCore
{
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
