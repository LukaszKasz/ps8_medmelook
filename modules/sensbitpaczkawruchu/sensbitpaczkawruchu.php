<?php
/**
 * 2016 Sensbit
 *
 * MODUŁ ZOSTAŁ UDOSTĘPNIONY NA PODSTAWIE LICENCJI NA JEDNO STANOWISKO/DOMENĘ
 * NIE MASZ PRAWA DO JEGO KOPIOWANIA, EDYTOWANIA I SPRZEDAWANIA
 * W PRZYPADKU PYTAŃ LUB BŁĘDÓW SKONTAKTUJ SIĘ Z AUTOREM
 *
 * ENGLISH:
 * MODULE IS LICENCED FOR ONE-SITE / DOMAIM
 * YOU ARE NOT ALLOWED TO COPY, EDIT OR SALE
 * IN CASE OF ANY QUESTIONS CONTACT AUTHOR
 *
 * ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
 *
 * PL: ODWIEDŹ NASZ SKLEP PO WIĘCEJ PROFESJONALNYCH MODUŁÓW PRESTASHOP
 * EN: VISIT OUR ONLINE SHOP FOR MORE PROFESSIONAL PRESTASHOP MODULES
 * HTTPS://SKLEP.SENSBIT.PL
 *
 * ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
 *
 * @author    Tomasz Dacka (kontakt@sensbit.pl)
 * @copyright 2016 sensbit.pl
 * @license   One-site license (jednostanowiskowa, bez możliwości kopiowania i udostępniania innym)
 */

if (!extension_loaded('ionCube Loader')) {

    class SensbitPaczkawRuchu extends Module
    {

        public function __construct()
        {
            $this->name = 'sensbitpaczkawruchu';
            $this->tab = 'shipping_logistics';
            $this->version = '0.0.0';
            $this->author = 'Sensbit';
            $this->need_instance = 0;
            $this->bootstrap = true;

            parent::__construct();

            $this->secure_key = Tools::encrypt($this->name);
            $this->displayName = $this->l('Integracja z Orlen Paczka', 'module').' - Wymagany IonCube Loader!';
            $this->description = 'IonCube loader jest wymagany by korzystać z modułu. Włącz lub zainstaluj go na serwerze by cieszyć się z modułów od Sensbit.';
        }

        public function install()
        {
            $this->_errors[] = $this->description;
            Context::getContext()->controller->errors[] = $this->description;
            return false;
        }
    }

} else {

    require_once dirname(__FILE__).'/classes/autoload.php';

    class SensbitPaczkawRuchu extends SensbitPaczkawRuchuModule
    {

    }

}