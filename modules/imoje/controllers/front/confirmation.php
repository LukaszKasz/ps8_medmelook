<?php

/**
 * Class ImojeConfirmationModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class ImojeConfirmationModuleFrontController extends ModuleFrontController
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
		parent::initContent();
		$this->setTemplate(Imoje::buildTemplatePath('confirmation', 'front'));
	}
}
