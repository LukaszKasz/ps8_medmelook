<?php
class Hook extends HookCore
{
    /*
    * module: x13eucookies
    * date: 2024-03-18 16:26:36
    * version: 1.3.2
    */
    public static function getHookModuleExecList($hookName = null)
    {
        $modulesToInvoke = parent::getHookModuleExecList($hookName);
        if (file_exists(_PS_MODULE_DIR_ . 'x13eucookies/x13eucookies.php') && Module::isEnabled('x13eucookies')) {
            
            $x13eucookies = Module::getInstanceByName('x13eucookies');
            $modulesToInvoke = $x13eucookies->filterModules($modulesToInvoke);
        }
        return !empty($modulesToInvoke) ? $modulesToInvoke : false;
    }
}
