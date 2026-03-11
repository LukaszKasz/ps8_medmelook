<?php
/**
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_2_1($module)
{
    try {
        Tools::clearCache();

        if (!$module->registerAdminChatGptTab()) {
            return false;
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}
