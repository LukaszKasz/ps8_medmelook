<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_3($module)
{
    $module->registerHook('actionFrontControllerInitAfter');

    return true;
}
