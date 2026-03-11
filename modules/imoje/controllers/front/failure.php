<?php

/**
 * Class ImojeFailureModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class ImojeFailureModuleFrontController extends ModuleFrontController
{

	/**
	 * @throws PrestaShopException
	 * @see FrontController::init()
	 */
	public function init()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;
		parent::init();
	}

	/**
	 * @throws PrestaShopException
	 */
	public function initContent()
	{
		global $smarty;
		parent::initContent();
		$smarty->assign('ga_key', Configuration::get('IMOJE_GA_KEY'));
		$this->setTemplate(Imoje::buildTemplatePath('failure', 'front'));
	}
}
