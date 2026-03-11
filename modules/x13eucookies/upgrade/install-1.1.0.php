<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0($module)
{
    $result = true;

    $result &= Configuration::updateValue(\x13eucookies\Config\ConfigKeys::SWITCH_DENY_BUTTON, true);
    $result &= Configuration::updateValue(\x13eucookies\Config\ConfigKeys::CENTER_LOGO, true);
    $result &= Configuration::updateValue(\x13eucookies\Config\ConfigKeys::DISPLAY_BACKDROP, true);
    $result &= Configuration::updateValue(\x13eucookies\Config\ConfigKeys::BACKDROP_OPACITY, '0.5');
    $result &= Configuration::updateValue(\x13eucookies\Config\ConfigKeys::BACKDROP_COLOR, '#000000');
    $result &= Configuration::updateValue(\x13eucookies\Config\ConfigKeys::GROUPS_EMPTY_COOKIES, false);
    $result &= Configuration::updateValue(\x13eucookies\Config\ConfigKeys::CONSENT_HASH, sha1(uniqid()));
    $result &= Configuration::updateValue(\x13eucookies\Config\ConfigKeys::NAVIGATION_HOOK, 'displayNav');

    $result &= $module->registerHook('displayNav');
    $result &= $module->registerHook('displayNav2');
    $result &= $module->registerHook('displayX13EuCookiesNav');

    if ($module->ps_version >= 1.7) {
        $result &= $module->registerHook('actionDispatcherBefore');
    }

    return (bool) $result;
}
