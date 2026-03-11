<?php

class Hook extends HookCore
{
    public static function getHookModuleExecList($hookName = null)
    {
        $modulesToInvoke = parent::getHookModuleExecList($hookName);

        if (file_exists(_PS_MODULE_DIR_ . 'x13eucookies/x13eucookies.php') && Module::isEnabled('x13eucookies')) {
            /** @var X13EuCookies $x13eucookies */
            $x13eucookies = Module::getInstanceByName('x13eucookies');
            $modulesToInvoke = $x13eucookies->filterModules($modulesToInvoke);
        }

        return !empty($modulesToInvoke) ? $modulesToInvoke : false;
    }
}
