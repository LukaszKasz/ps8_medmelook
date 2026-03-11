<?php
/**
 * 2022 DPD Polska Sp. z o.o.
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

require_once(_DPDPOLAND_CONTROLLERS_DIR_.'dpd_pudo_cod.service.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 *
 * @return bool
 */
function upgrade_module_1_6_0()
{
    if (!version_compare(_PS_VERSION_, '1.5', '<')) {
        if (!Tools::getValue(DpdPolandConfiguration::CARRIER_PUDO_COD)) {
            if (!DpdPolandCarrierPudoCodService::install())
                return false;
        }
    }
    return true;
}
