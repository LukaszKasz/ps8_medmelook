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

function upgrade_module_3_0_0($module)
{
    $sql = SensbitPaczkawRuchuTools::installSql(array(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sensbitpaczkawruchu_status_data` (
              `name` varchar(50) NOT NULL,
              `order_state_change` tinyint(1) NOT NULL,
              `order_state_id` int(11) NOT NULL,
              `autocheck` tinyint(1),
              PRIMARY KEY (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            '
    ));
    SensbitPaczkawRuchuStatus::updateStatusesData();
    return $sql && $module->uninstallTabs() && $module->installTabs();
}
