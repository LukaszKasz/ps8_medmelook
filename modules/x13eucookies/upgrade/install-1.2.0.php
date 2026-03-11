<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($module)
{
    $module->registerHook('displayAfterTitle');
    $module->registerHook('displayAfterTitleTag');
    $module->updatePosition(Hook::getIdByName('displayHeader'), 0, 1);
    $module->updatePosition(\Hook::getIdByName('displayAfterTitle'), 0, 1);
    $module->updatePosition(\Hook::getIdByName('displayAfterTitleTag'), 0, 1);

    Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'x13eucookies_cookie_category`
        CHANGE `gtm_consent_type` `gtm_consent_type` VARCHAR(128) NOT NULL;
    ');

    // Force Consent Mode v2 values to be set
    $marketingCategoryId = (int) XEuCookiesCookieCategory::getIdByType('marketing');
    $marketingCookiesCategory = new XEuCookiesCookieCategory($marketingCategoryId);
    if (Validate::isLoadedObject($marketingCookiesCategory)) {
        $marketingCookiesCategory->gtm_consent_type = 'ad_storage,ad_personalization,ad_user_data';
        $marketingCookiesCategory->setFieldsToUpdate(['gtm_consent_type' => true]);
        $marketingCookiesCategory->update();
    }

    $functionalCategoryId = (int) XEuCookiesCookieCategory::getIdByType('nessesary');
    $functionalCookiesCategory = new XEuCookiesCookieCategory($functionalCategoryId);

    if (Validate::isLoadedObject($functionalCookiesCategory)) {
        $functionalCookiesCategory->gtm_consent_type = 'functionality_storage,security_storage';
        $functionalCookiesCategory->setFieldsToUpdate(['gtm_consent_type' => true]);
        $functionalCookiesCategory->update();
    }

    $securityCategoryId = (int) XEuCookiesCookieCategory::getIdByType('security');
    $securityCookiesCategory = new XEuCookiesCookieCategory($securityCategoryId);

    if (Validate::isLoadedObject($securityCookiesCategory)) {
        $securityCookiesCategory->gtm_consent_type = '';
        $securityCookiesCategory->setFieldsToUpdate(['gtm_consent_type' => true]);
        $securityCookiesCategory->update();
    }

    return true;
}
