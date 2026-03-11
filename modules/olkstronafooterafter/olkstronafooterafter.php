<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class OLKStronaFooterAfter extends Module
{
    public function __construct()
    {
        $this->name = 'olkstronafooterafter';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Łukasz Kaszelan';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('OLK Strona pod Footerem');
        $this->description = $this->l('Wyświetla stronę pod Footerem.');

        $this->confirmUninstall = $this->l('Czy na pewno chcesz odinstalować?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
    
        return parent::install() &&
            $this->registerHook('displayFooterAfter') &&    
            Configuration::updateValue('OLK_FOOTER_AFTER_INFO', 'Default value');
    }


    public function uninstall()
    {
        return parent::uninstall() && Configuration::deleteByName('OLK_FOOTER_AFTER_INFO');
    }

    public function getContent()
    {
        $output = null;
    
        if (Tools::isSubmit('submit'.$this->name)) {
            $my_module_name = strval(Tools::getValue('OLK_FOOTER_AFTER_INFO'));
            if (!$my_module_name || empty($my_module_name) || !Validate::isGenericName($my_module_name)) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('OLK_FOOTER_AFTER_INFO', $my_module_name);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Wpisz numer ID strony z CMS'),
                    'name' => 'OLK_FOOTER_AFTER_INFO',
                    'size' => 20,
                    'required' => true
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper->fields_value['OLK_FOOTER_AFTER_INFO'] = Configuration::get('OLK_FOOTER_AFTER_INFO');

        return $helper->generateForm($fields_form);
    }

    public function hookDisplayFooterAfter($params)
    {
        $cmsId = intval(Configuration::get('OLK_FOOTER_AFTER_INFO', '')); // Pobierz numer ID strony CMS z konfiguracji i zamień na liczbę

        $cms = new CMS($cmsId, $this->context->language->id);  // ID strony CMS to 11
        if (Validate::isLoadedObject($cms)) {
            $cmsLink = $this->context->link->getCMSLink($cms);
            $cmsContent = $cms->content;

            // Możesz użyć Smarty do sformatowania wyświetlania
            // $this->context->smarty->assign(array(
            //     'cms_link' => $cmsLink,
            //     'cms_content' => $cmsContent
            // ));

            // return $cmsContent;


            //

            $this->context->smarty->assign('htmlPage', $cmsContent);
            return $this->display(__FILE__, 'views/templates/hook/displayCmsContent.tpl');
        
        }

        return ''; // Zwróć pusty string, jeśli CMS nie został znaleziony
    }
}
