<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_1()
{
    Configuration::updateValue(x13eucookies\Config\ConfigKeys::GTM_CONSENTS_URL_PASSTHROUGH, 0);
    Configuration::updateValue(x13eucookies\Config\ConfigKeys::GTM_CONSENTS_ADS_DATA_REDACTION, 1);

    // Temporary disabled
    Configuration::updateValue(x13eucookies\Config\ConfigKeys::BLOCK_IFRAMES, 0);

    return true;
}
