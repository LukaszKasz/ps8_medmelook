<?php

use x13eucookies\Config\ConfigKeys;

require_once _PS_MODULE_DIR_ . 'x13eucookies/x13eucookies.php';

class AdminEuCookiesListController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'x13eucookies_cookie';
        $this->identifier = 'id_xeucookies_cookie';
        $this->position_identifier = 'id_xeucookies_cookie';
        $this->className = 'XEuCookiesCookie';
        $this->lang = true;
        $this->show_form_cancel_button = false;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bootstrap = true;

        $this->_orderBy = 'position';

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
            ],
        ];

        $cookiesCategories = [];
        foreach (XEuCookiesCookieCategory::getAll() as $category) {
            $cookiesCategories[$category['id_xeucookies_cookie_category']] = $category['name'];
        }

        $this->fields_list = [
            'id_xeucookies_cookie' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 30,
            ],
            'name' => [
                'title' => $this->l('Name'),
                'width' => 'auto',
            ],
            'id_xeucookies_cookie_category' => [
                'title' => $this->l('Category'),
                'callback' => 'getNameById',
                'callback_object' => 'XEuCookiesCookieCategory',
                'type' => 'select',
                'list' => $cookiesCategories,
                'filter_key' => 'a!id_xeucookies_cookie_category',
            ],
            'provider' => [
                'title' => $this->l('Provider'),
            ],
            'provider_url' => [
                'title' => $this->l('Provider URL'),
            ],
            'details' => [
                'title' => $this->l('Details'),
                'orderby' => false,
                'callback' => 'getDescriptionClean',
            ],
            'expiration' => [
                'title' => $this->l('Expiration time'),
            ],
            'position' => [
                'title' => $this->l('Position'),
                'width' => 40,
                'filter_key' => 'a!position',
                'position' => 'position',
            ],
            'deletable' => [
                'title' => $this->l('Deletable'),
                'width' => 25,
                'active' => 'deletable',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
            ],
            'active' => [
                'title' => $this->l('Displayed'),
                'width' => 25,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
            ],
        ];

        if ($this->module->hasCacheModulesEnabled()) {
            $this->warnings[] = $this->l('You have enabled cache modules. Please make sure that you have cleared the cache after changing settings of the module.');
        }
    }

    public function renderList()
    {
        $this->initToolbar();

        $this->context->smarty->assign(
            [
                'consentModificationsDate' => Tools::displayDate(date('Y-m-d H:i:s', Configuration::get(ConfigKeys::CONSENTS_MODIFICATION_DATE)), true),
            ]
        );

        if (Configuration::get(ConfigKeys::COOKIES_OVERVIEW_REQUIRED) && Configuration::get(ConfigKeys::COOKIES_OVERVIEW_VERSION) == $this->module->version) {
            $this->context->smarty->assign('module_version', $this->module->version);
            $this->content .= $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/cookies/announcements.tpl');
        }

        $this->content .= $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/reset-consents.tpl');
        $this->content .= $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/cookies-info.tpl');

        return parent::renderList();
    }

    public function getDescriptionClean($description, $row)
    {
        return strip_tags(stripslashes($description));
    }

    public function init()
    {
        parent::init();

        Shop::addTableAssociation($this->table, ['type' => 'shop']);

        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . $this->table . '_shop` sa ON (a.`' . $this->identifier . '` = sa.`' . $this->identifier . '` AND sa.id_shop = ' . (int) $this->context->shop->id . ') ';
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND sa.`id_shop` = ' . (int) $this->context->shop->id;
        }

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            unset($this->fields_list['position']);
        }

        if (Tools::isSubmit('submitResetConsents')) {
            Configuration::updateValue(ConfigKeys::CONSENTS_MODIFICATION_DATE, time());
            Configuration::updateValue(ConfigKeys::CONSENT_HASH, sha1(uniqid()));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEuCookiesList') . '&conf=4');
        }

        if (Tools::isSubmit('submitDiscardAnnouncement')) {
            Configuration::updateValue(ConfigKeys::COOKIES_OVERVIEW_REQUIRED, 0);
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEuCookiesList') . '&conf=4');
        }

        if (Tools::isSubmit('deletablex13eucookies_cookie')) {
            $EuCookie = new XEuCookiesCookie((int) Tools::getValue('id_xeucookies_cookie'));
            $EuCookie->deletable = !$EuCookie->deletable;
            $EuCookie->setFieldsToUpdate(['deletable' => true]);
            $EuCookie->update();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEuCookiesList') . '&conf=4');
        }
    }

    public function initFormToolBar()
    {
        unset($this->toolbar_btn['back']);
        $this->toolbar_btn['save-and-stay'] = [
            'short' => 'SaveAndStay',
            'href' => '#',
            'desc' => $this->l('Save and stay'),
        ];
        $this->toolbar_btn['back'] = [
            'href' => self::$currentIndex . '&token=' . Tools::getValue('token'),
            'desc' => $this->l('Back to list'),
        ];
    }

    public function renderForm()
    {
        $this->initFormToolBar();

        $obj = $this->loadObject(true);
        $this->display = isset($obj->id) ? 'edit' : 'add';

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Cookie'),
                'icon' => 'icon-info-circle',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->l('Category'),
                    'name' => 'id_xeucookies_cookie_category',
                    'options' => [
                        'query' => XEuCookiesCookieCategory::getAll(), // your query for getting categories
                        'id' => 'id_xeucookies_cookie_category',
                        'name' => 'name',
                    ],
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'desc' => $this->l('Name of the cookie, e.g. _ga, _gid, etc. You can also use wildcard character # to match multiple cookies. e.g. _ga* will match _ga, _ga1, _ga2, etc.'),
                    'name' => 'name',
                    'lang' => false,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Provider'),
                    'desc' => $this->l('Name of the provider, e.g. Google, Facebook, etc.'),
                    'name' => 'provider',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Provider URL'),
                    'name' => 'provider_url',
                    'lang' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Details'),
                    'autoload_rte' => true,
                    'name' => 'details',
                    'lang' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Expiration'),
                    'name' => 'expiration',
                    'desc' => $this->l('e.g. 1 year, 1 month, 1 day, 1 hour, 1 minute, 1 second, Session, etc.'),
                    'lang' => true,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Deletable'),
                    'name' => 'deletable',
                    'desc' => $this->l('If you enable this option module will try to remove this cookie from the user browser. Remember that it may not work in all cases, especially if the cookie is set by a code inside a module and you did not exclude it from the Cookie Category settings. This setting will be ignored for cookies from the "Nessesary" category.'),
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'deletable_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'deletable_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
            'buttons' => [
                'back-to-list' => [
                    'href' => self::$currentIndex . '&token=' . $this->token,
                    'title' => $this->l('Back to list'),
                    'icon' => 'process-icon-back',
                ],
                'save-and-stay' => [
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ],
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association') . ':',
                'name' => 'checkBoxShopAsso',
            ];
        }

        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
        $this->tpl_form_vars['PS_FORCE_FRIENDLY_PRODUCT'] = (int) Configuration::get('PS_FORCE_FRIENDLY_PRODUCT');

        return parent::renderForm();
    }

    public function ajaxProcessUpdatePositions()
    {
        $id = (int) Tools::getValue('id');
        $positions = Tools::getValue(str_replace('id_', '', $this->identifier));
        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);
            $id = (int) $pos[2];
            if ((int) $id > 0) {
                $result = (bool) Db::getInstance()->update($this->table, ['position' => (int) $position + 1], $this->identifier . ' = ' . (int) $id);
                if ($result) {
                    echo 'ok position ' . (int) $position . ' for item ' . (int) $id . '\r\n';
                } else {
                    echo '{"hasError" : true, "errors" : "This item (' . (int) $id . ') cant be loaded"}';
                }
            }
        }
    }
}
