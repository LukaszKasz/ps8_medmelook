<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class OlkFormyPlatnosci extends Module
{
    public function __construct()
    {
        $this->name = 'olkformyplatnosci';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'TwojeImie';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('OLK Formy Płatności');
        $this->description = $this->l('Wyświetla informacje o formach płatności na stronie produktu.');

        $this->confirmUninstall = $this->l('Czy na pewno chcesz odinstalować?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
    
        return parent::install() &&
            $this->registerHook('displayProductAdditionalInfo') &&
            Configuration::updateValue('OLK_PAYMENT_METHODS_INFO', 'Default value');
    }


    public function uninstall()
    {
        return parent::uninstall() && Configuration::deleteByName('OLK_PAYMENT_METHODS_INFO');
    }

    public function getContent()
    {
        $output = null;
    
        if (Tools::isSubmit('submit'.$this->name)) {
            $my_module_name = strval(Tools::getValue('OLK_PAYMENT_METHODS_INFO'));
            if (!$my_module_name || empty($my_module_name) || !Validate::isGenericName($my_module_name)) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('OLK_PAYMENT_METHODS_INFO', $my_module_name);
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
                    'name' => 'OLK_PAYMENT_METHODS_INFO',
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
        $helper->fields_value['OLK_PAYMENT_METHODS_INFO'] = Configuration::get('OLK_PAYMENT_METHODS_INFO');

        return $helper->generateForm($fields_form);
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        $cmsId = intval(Configuration::get('OLK_PAYMENT_METHODS_INFO', '')); // Pobierz numer ID strony CMS z konfiguracji i zamień na liczbę

        $cms = new CMS($cmsId, $this->context->language->id);  // ID strony CMS to 11
        if (Validate::isLoadedObject($cms)) {
            $cmsLink = $this->context->link->getCMSLink($cms);
            $cmsContent = $cms->content;

            // Możesz użyć Smarty do sformatowania wyświetlania
            $this->context->smarty->assign(array(
                'cms_link' => $cmsLink,
                'cms_content' => $cmsContent
            ));

            return $cmsContent;
            //return $this->display(__FILE__, 'views/templates/hook/displayCmsContent.tpl');
        }

        return ''; // Zwróć pusty string, jeśli CMS nie został znaleziony
    }

    // public function hookDisplayProductAdditionalInfo($params)
    // {
    //     // Testowe wstrzyknięcie HTML
    //     return '<div class="cms-content"><img src="http://192.168.30.100/medmelook/img/cms/Kolor bordowy.webp" alt="" width="50" height="50" /></div>';
    // }

}
