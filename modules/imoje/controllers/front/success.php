<?php

/**
 * Class ImojeSuccessModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class ImojeSuccessModuleFrontController extends ModuleFrontController
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

		$gaCartId = (int) Tools::getValue('ga_cart_id');
		$gaHash = Tools::getValue('ga_hash');
		$gaKey = Configuration::get('IMOJE_GA_KEY');

		if(!$gaKey || $gaCartId <= 0) {
			$this->setTemplate(Imoje::buildTemplatePath('success', 'front'));

			return;
		}

		$smarty->assign('ga_key', $gaKey);

		try {
			$cart = new Cart($gaCartId);

			if(hash(
				'sha256',
				sprintf('%s%s',
					$cart->id,
					$cart->secure_key)
				) === $gaHash) {

				$products = [];

				foreach($cart->getProducts() as $product) {
					$products[] = [
						'item_id'       => $cart->id,
						'item_name'     => $product['name'],
						'item_category' => $product['category'],
						'item_price'    => $product['price'],
						'quantity'      => $product['quantity'],
					];
				}

				$getOrderTotal = $cart->getOrderTotal();

				$smarty->assign('ga_conversion', json_encode([
					'transaction_id' => $cart->id,
					'value'          => $getOrderTotal,
					'tax'            => $getOrderTotal - $cart->getOrderTotal(false),
					'shipping'       => $cart->getTotalShippingCost(),
					'currency'       => Currency::getCurrency($cart->id_currency)['iso_code'],
					'items'          => $products,
				]));
			}
		} catch(Exception $e) {
			Logger::addLog(
				sprintf('%s %s', __METHOD__, $e->getMessage()),
				1
			);
		}

		$this->setTemplate(Imoje::buildTemplatePath('success', 'front'));
	}
}
