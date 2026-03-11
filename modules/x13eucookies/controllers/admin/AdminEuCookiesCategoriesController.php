<?php

use x13eucookies\Config\ConfigKeys;
require_once _PS_MODULE_DIR_ . 'x13eucookies/x13eucookies.php';

class AdminEuCookiesCategoriesController extends ModuleAdminController
{
    private $blockedCategoriesIds = [];
    public function __construct()
    {
        $this->table = 'x13eucookies_cookie_category';
        $this->identifier = 'id_xeucookies_cookie_category';
        $this->position_identifier = 'position';
        $this->className = 'XEuCookiesCookieCategory';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->list_no_link = true;
        $this->show_form_cancel_button = false;

        $this->bootstrap = true;

        // Do not allow tinker with the default categories
        $this->blockedCategoriesIds = XEuCookiesCookieCategory::getDefaultCategories();
        $this->list_skip_actions['delete'] = $this->blockedCategoriesIds;

        $this->_orderBy = 'position';

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            ]
        ];

        $this->fields_list = [
            'id_xeucookies_cookie_category' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->l('Name'),
                'width' => 'auto',
            ],
            'details' => [
                'title' => $this->l('Details'),
                'width' => 500,
                'orderby' => false,
                'callback' => 'getDescriptionClean',
            ],
            'position' => [
                'title' => $this->l('Position'),
                'width' => 40,
                'filter_key' => 'a!position',
                'position' => 'position',
            ],
            // 'type' => [
            //     'title' => $this->l('Category type'),
            //     'align' => 'center',
            //     'type' => 'text',
            //     'orderby' => false,
            // ],
            'required' => [
                'title' => $this->l('Required to function'),
                'class' => 'fixed-width-xs',
                'active' => 'required',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
            ],
            'active' => [
                'title' => $this->l('Displayed'),
                'class' => 'fixed-width-xs',
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
        $this->context->smarty->assign(array(
            'consentModificationsDate' => Tools::displayDate(date('Y-m-d H:i:s', Configuration::get(ConfigKeys::CONSENTS_MODIFICATION_DATE)), true)
        ));

        $this->content .= $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/reset-consents.tpl');

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
            $this->_where = ' AND sa.`id_shop` = ' . (int) Context::getContext()->shop->id;
        }

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            unset($this->fields_list['position']);
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

    public function initProcess()
    {
        parent::initProcess();

        if (Tools::isSubmit('required' . $this->table) && $this->id_object) {
            $this->toggleProperty('required', $this->id_object);
            if (!$this->errors) {
                Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminEuCookiesCategories') . '&conf=5');
            }
        }

        if (Tools::isSubmit('submitResetConsents')) {
            Configuration::updateValue(ConfigKeys::CONSENTS_MODIFICATION_DATE, time());
            Configuration::updateValue(ConfigKeys::CONSENT_HASH, sha1(uniqid()));
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminEuCookiesCategories') . '&conf=4');
        }
    }

    public function renderForm()
    {
        $this->initFormToolBar();

        $obj = $this->loadObject(true);
        $this->display = isset($obj->id) ? 'edit' : 'add';

        $this->context->controller->addJqueryPlugin('tagify');

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Cookies category'),
                'icon' => 'icon-folder-close',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'desc' => $this->l('The name of the category.'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Details'),
                    'desc' => $this->l('The details of the category. For example, the purpose of the cookies in this category.'),
                    'name' => 'details',
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'autoload_rte' => true,
                ],
                [
                    'type' => 'free',
                    'label' => $this->l('Blocked modules'),
                    'name' => 'modules_selector',
                    'col' => 8,
                ],
                [
                    'type' => 'html',
                    'label' => '',
                    'name' => '',
                    'html_content' => '<h2   style="font-size:17px; margin:0px;">' . $this->l('Google Consent Mode V2') . '</h2>',
                ],
                [
                    'type' => 'html',
                    'label' => '',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">' . $this->l('We set up Consent Mode V2 following Google recommendations, change it at your own risk.') . '</div>',
                ],
                [
                    'type' => 'html',
                    'label' => '',
                    'name' => '',
                    'form_group_class' => (Configuration::get(ConfigKeys::GTM_CONSENTS) ? 'hide' : ''),
                    'html_content' => '<div class="alert alert-warning">' . $this->l('This option will be ignored. You can enable sending Consent Mode choices in the module settings. If you use Google Ads or Google Analytics it is required to do so.') . '</div>',
                ],
                [
                    'type' => 'tags',
                    'label' => $this->l('Consent Mode V2 / GTM consent type to grant'),
                    'desc' => $this->l('This feature works only if the corresponding option is enabled in the settings of the module. The designation for the type of consent in Google Tag Manager is referred to as "ad_storage" for instance. If you utilize GTM integration and the consent mode, it is essential to assign the appropriate cookie category to the corresponding consent type. Leave it blank if GTM is not in use.'),
                    'name' => 'gtm_consent_type',
                    'hint' => $this->l('Comma separated, for example: ad_storage,ad_personalization,ad_user_data'),
                    'required' => false,
                    'lang' => false,
                    'form_group_class' => !Configuration::get(ConfigKeys::GTM_CONSENTS) ? 'x13eucookies-tags-setting-blocked' : '',
                ],
                [
                    'type' => 'html',
                    'label' => '',
                    'name' => '',
                    'html_content' => '<h2   style="font-size:17px; margin:0px;">' . $this->l('Microsoft Advertising consents') . '</h2>',
                ],
                [
                    'type' => 'html',
                    'label' => '',
                    'name' => '',
                    'html_content' => '<div class="alert alert-info">' . $this->l('We set up Microsoft Advertising consents following Microsoft recommendations, change it at your own risk.') . '</div>',
                ],
                [
                    'type' => 'html',
                    'label' => '',
                    'name' => '',
                    'form_group_class' => (Configuration::get(ConfigKeys::MICROSOFT_CONSENTS) ? 'hide' : ''),
                    'html_content' => '<div class="alert alert-warning">' . $this->l('This option will be ignored. You can enable sending Microsoft Advertising choices in the module settings.') . '</div>',
                ],
                [
                    'type' => 'tags',
                    'label' => $this->l('Microsoft Advertising consents'),
                    'desc' => $this->l('This feature works only if the corresponding option is enabled in the settings of the module. As of 02.2024 Microsoft only supports "ad_storage" recommended for "Marketing" cookies.'),
                    'name' => 'microsoft_consent_type',
                    'hint' => $this->l('Comma separated, for example: ad_storage,ad_personalization,ad_user_data'),
                    'required' => false,
                    'lang' => false,
                    'form_group_class' => !Configuration::get(ConfigKeys::MICROSOFT_CONSENTS) ? 'x13eucookies-tags-setting-blocked' : '',
                ],
                [
                    'type' => 'html',
                    'label' => '',
                    'name' => '',
                    'html_content' => '<h2   style="font-size:17px; margin:0px;">' . $this->l('Smarty and JavaScript') . '</h2>',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('JavaScript code to run after consent'),
                    'desc' => $this->l('This code will be executed (once, while saving cookie preferences) after the user has given consent to the cookies in this category.'),
                    'name' => 'js_with_consent',
                    'rows' => 5,
                    'cols' => 40,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('JavaScript code to run after revoking consent'),
                    'desc' => $this->l('This code will be executed (once, while saving cookie preferences) after the user has revoked consent to the cookies in this category.'),
                    'name' => 'js_without_consent',
                    'rows' => 5,
                    'cols' => 40,
                ],
                [
                    'type' => 'free',
                    'label' => $this->l('Smarty and JavaScript checks'),
                    'name' => 'inline_checks',
                    'col' => 8,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Displayed'),
                    'name' => 'active',
                    'required' => false,
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
                [
                    'type' => 'switch',
                    'label' => $this->l('Required for the website to function'),
                    'hint' => $this->l('Most of the time you will have this option enabled only for the "Necessary" category.'),
                    'desc' => $this->l('If enabled, cookies from this category will be required for your store to function, and it will be impossible to discard them.'),
                    'name' => 'required',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'required_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'required_off',
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
            ]
        ];
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association') . ':',
                'name' => 'checkBoxShopAsso',
            ];
        }

        // Modules selector feature
        $modules = Module::getModulesOnDisk(true);
        $selectedModules = !empty($obj->blocked_modules) ? json_decode($obj->blocked_modules, true) : [];

        foreach ($modules as $key => &$module) {
            if (in_array($module->name, $selectedModules)) {
                $module->checked = true;
            } else {
                $module->checked = false;
            }

            if ($module->name === 'x13eucookies') {
                unset($modules[$key]);
            }
        }
        unset($module);

        usort($modules, function ($a, $b) {
            if ($a->checked === true && $b->checked === false) {
                return -1;
            }
            if ($a->checked === false && $b->checked === true) {
                return 1;
            }

            return strcmp($a->displayName, $b->displayName);
        });

        $this->context->smarty->assign(
            [
                'is_required_category' => $obj->required,
                'modules' => $modules,
            ]
        );

        $modulesSelector = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/modules_selector.tpl');

        $this->context->smarty->assign(
            [
                'id_xeucookies_cookie_category' => $obj->id,
            ]
        );
        $inlineChecks = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/inline_checks.tpl');

        $this->fields_value = [
            'modules_selector' => $modulesSelector,
            'inline_checks' => $inlineChecks,
        ];

        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
        $this->tpl_form_vars['PS_FORCE_FRIENDLY_PRODUCT'] = (int) Configuration::get('PS_FORCE_FRIENDLY_PRODUCT');

        return parent::renderForm();
    }

    public function processAdd()
    {
        $object = parent::processAdd();
        $this->updateAssoShop($object->id);

        return true;
    }

    public function processUpdate()
    {
        $object = parent::processUpdate();
        $this->updateAssoShop($object->id);

        return true;
    }

    public function toggleProperty($property, $id)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            if ($this->tabAccess['edit'] !== '1') {
                $this->errors[] = $this->l('You do not have permission to edit this.');
                return;
            }
        } else {
            if (!$this->access('edit')) {
                $this->errors[] = $this->l('You do not have permission to edit this.');
                return;
            }
        }

        $object = new $this->className($id);
        if (!Validate::isLoadedObject($object)) {
            $this->errors[] = $this->l('An error occurred while updating an object.');
        }

        $object->$property = !$object->$property;
        $object->setFieldsToUpdate([$property => true]);
        if (!$object->save()) {
            $this->errors[] = $this->l('An error occurred while updating an object.');
        }
    }

    public function processSave()
    {
        $_POST['blocked_modules'] = json_encode(Tools::getValue('blocked_modules', []));

        return parent::processSave();
    }

    public function ajaxProcessUpdatePositions()
    {
        $id = (int) (Tools::getValue('id'));
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
