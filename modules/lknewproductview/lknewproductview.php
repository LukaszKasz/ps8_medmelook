<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class lkNewProductView extends Module implements \PrestaShop\PrestaShop\Core\Module\WidgetInterface
{
    public function __construct()
    {
        $this->name = 'lknewproductview';
        $this->tab = 'front_office_features';
        $this->version = '1.0.9';
        $this->author = 'Łukasz Kasztelan';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('My module', [], 'Modules.Mymodule.Admin');
        $this->description = $this->trans('Description of my module.', [], 'Modules.Mymodule.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Mymodule.Admin');

        $this->templateFile = 'module:lknewproductview/views/templates/hook/LkNewProductView.tpl';

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->trans('No name provided', [], 'Modules.Mymodule.Admin');
        }
    }

    // public function install()
    // {
    //     return parent::install();
    // }

    // public function uninstall()
    // {
    //     return parent::uninstall();
    // }
    public function renderWidget($hookName, array $configuration)
    {
        return $this->fetch($this->templateFile);
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        // TODO: Implement getWidgetVariables() method.
    }
}





