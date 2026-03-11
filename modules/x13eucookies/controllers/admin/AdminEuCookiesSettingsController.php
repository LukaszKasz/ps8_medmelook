<?php

/**
 * @author    x13.pl <x13@x13.pl>
 * @copyright Copyright (c) 2018-2024 - www.x13.pl
 * @license   Commercial license, only to use on restricted domains
 */
class AdminEuCookiesSettingsController extends ModuleAdminController
{
    public function init()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=x13eucookies');
    }
}
