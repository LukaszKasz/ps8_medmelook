<?php

use x13eucookies\Config\ConfigKeys;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_0($module)
{
    $module->registerHook('actionOutputHTMLBefore');

    Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'x13eucookies_cookie`
        ADD `deletable` tinyint(1) DEFAULT 1
    ');

    // Add microsoft_consent_type
    Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'x13eucookies_cookie_category`
        ADD `microsoft_consent_type` VARCHAR(128) DEFAULT NULL
    ');

    // Set consents for Microsoft Advertising
    $marketingCategoryId = (int) XEuCookiesCookieCategory::getIdByType('marketing');
    $marketingCookiesCategory = new XEuCookiesCookieCategory($marketingCategoryId);
    if (Validate::isLoadedObject($marketingCookiesCategory)) {
        $marketingCookiesCategory->microsoft_consent_type = 'ad_storage';
        $marketingCookiesCategory->setFieldsToUpdate(['microsoft_consent_type' => true]);
        $marketingCookiesCategory->update();
    }

    Configuration::updateValue(x13eucookies\Config\ConfigKeys::BLOCK_IFRAMES, 0);

    $blockedIframesText = [];
    foreach (Language::getLanguages(false) as $lang) {
        switch ($lang['iso_code']) {
            case 'pl':
                $translatedText = "[button]Zgoda na marketingowe pliki cookie[/button] jest wymagana, aby zobaczyć treść lub zobacz to na [link]";
                break;

            case 'en':
                $translatedText = '[button]Agree to marketing cookies[/button] to see the content or see it on [link]';
                break;

            default:
                $translatedText = $module->l('[button]Agree to marketing cookies[/button] to see the content or see it on [link]');
                break;
        }
        $blockedIframesText[$lang['id_lang']] = $translatedText;
    }

    Configuration::updateValue(
        x13eucookies\Config\ConfigKeys::BLOCKED_IFRAMES_TEXT,
        $blockedIframesText,
        true
    );

    Configuration::updateValue(
        x13eucookies\Config\ConfigKeys::COOKIES_OVERVIEW_REQUIRED,
        1
    );

    Configuration::updateValue(
        x13eucookies\Config\ConfigKeys::COOKIES_OVERVIEW_VERSION,
        '1.3.0'
    );

    return true;
}
