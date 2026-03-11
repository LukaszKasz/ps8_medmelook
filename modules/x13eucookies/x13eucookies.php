<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/x13eucookies.ion.php';

/**
 * Class X13EuCookies.
 */
class X13EuCookies extends x13eucookies\EuCookiesModuleCore
{
    /**
     * X13EuCookies.
     *
     * {@inheritDoc}
     */
    public function __construct()
    {
        $this->name = 'x13eucookies';
        $this->tab = 'front_office_features';
        $this->version = '1.3.5';
        $this->author = 'x13.pl';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = [
            'min' => '1.6.1.0',
            'max' => '8.99.99',
        ];

        parent::__construct();

        $this->displayName = $this->l('EU Cookies - cookie blocking banner - Consent mode V2');
        $this->description = $this->l('A module that will allow you to adjust the store to the requirements for cookies. You can add categories, then assign cookies to selected categories. Allow your shop visitors to choose cookies they want to use or block.');
        $this->confirmUninstall = $this->l('This will remove all your settings. Are you sure?');
    }

    /**
     * Translation key available outside ioncube files
     *
     * @return void
     */
    public function initTranslations()
    {
        static::$x13Translations = [
            'You cannot disable required cookie category' => $this->l('You cannot disable required cookie category'),
            'Settings' => $this->l('Settings'),
            'Enabled' => $this->l('Enabled'),
            'Disabled' => $this->l('Disabled'),
            'Save' => $this->l('Save'),
        ];
    }
}
