<?php
/* *
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
 * EN: ODWIEDŹ NASZ SKLEP PO WIĘCEJ PROFESJONALNYCH MODUŁÓW PRESTASHOP
 * PL: VISIT OUR ONLINE SHOP FOR MORE PROFESSIONAL PRESTASHOP MODULES
 * HTTPS://SKLEP.SENSBIT.PL
 *
 * ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** *
 *
 * @author    Tomasz Dacka (kontakt@sensbit.pl)
 * @copyright 2016 sensbit.pl
 * @license   One-site license (jednostanowiskowa, bez możliwości kopiowania i udostępniania innym)
 */


require_once dirname(__FILE__).'/../sensbitpaczkawruchu.php';

function upgrade_module_1_3_2($module)
{
    $install_sql = array();

    $is_column = (int)SensbitPaczkawRuchuTools::dbCheckColumn('sensbitpaczkawruchu_shipment', 'id_dispatch_order');
    if (!$is_column) {
        $install_sql[] = 'ALTER TABLE `'._DB_PREFIX_.'sensbitpaczkawruchu_shipment` ADD `id_dispatch_order` INT(11) NOT NULL DEFAULT 0;';
    }

    $is_column = (int)SensbitPaczkawRuchuTools::dbCheckColumn('sensbitpaczkawruchu_shipment', 'sending_method');
    if (!$is_column) {
        $install_sql[] = 'ALTER TABLE `'._DB_PREFIX_.'sensbitpaczkawruchu_shipment` ADD `sending_method` VARCHAR(50) NULL;';
    }
    if(empty($install_sql)){
        return true;
    }
    return SensbitPaczkawRuchuTools::installSql($install_sql);
}
