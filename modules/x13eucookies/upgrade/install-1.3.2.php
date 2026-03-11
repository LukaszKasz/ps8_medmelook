<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_2()
{
    Configuration::updateValue(x13eucookies\Config\ConfigKeys::MOVE_MODAL_TO_END_BODY, 0);

    return true;
}
