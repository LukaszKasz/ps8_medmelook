<?php

use Imoje\Payment\Api;
use Imoje\Payment\Util;

/**
 * Class ImojePaymentblikModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class ImojePaymentblikModuleFrontController extends ModuleFrontController
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
	 * @throws Exception
	 */
	public function initContent()
	{
		parent::initContent();

		$imojeToken = Configuration::get('IMOJE_TOKEN');
		$merchantId = Configuration::get('IMOJE_MERCHANT_ID');
		$serviceId = Configuration::get('IMOJE_SERVICE_ID');

		if(!$imojeToken
			|| !$merchantId
			|| !$serviceId) {
			Tools::redirect('index');

			return;
		}

		// region check/get $_POST vars
		$post = [
			'profileId'        => isset($_POST['profileId']) && $_POST['profileId']
				? $_POST['profileId']
				: '',
			'deactivate'       => isset($_POST['deactivate']) && $_POST['deactivate']
				? $_POST['deactivate']
				: '',
			'blikKey'          => isset($_POST['blikKey']) && $_POST['blikKey']
				? $_POST['blikKey']
				: '',
			'checkTransaction' => isset($_POST['checkTransaction']) && $_POST['checkTransaction']
				? $_POST['checkTransaction']
				: '',
			'cartId'           => isset($_POST['cartId']) && $_POST['cartId']
				? $_POST['cartId']
				: '',
			'transactionId'    => isset($_POST['transactionId']) && $_POST['transactionId']
				? $_POST['transactionId']
				: '',
			'rememberBlikCode' => isset($_POST['rememberBlikCode']) && $_POST['rememberBlikCode']
				? $_POST['rememberBlikCode']
				: '',
			'continuePayment'  => isset($_POST['continuePayment']) && $_POST['continuePayment']
				? $_POST['continuePayment']
				: '',
			'blikCode'         => isset($_POST['blikCode']) && $_POST['blikCode']
			&& is_numeric($_POST['blikCode'])
			&& (strlen($_POST['blikCode']) === 6)
				? $_POST['blikCode']
				: '',
		];
		// endregion

		// region initialize Api
		$imojeApi = new Api(
			$imojeToken,
			$merchantId,
			$serviceId,
			Configuration::get('IMOJE_SANDBOX')
				? Util::ENVIRONMENT_SANDBOX
				: Util::ENVIRONMENT_PRODUCTION
		);
		// endregion

		// region check trx in imoje and make response for ajax call
		if($post['checkTransaction'] && $post['transactionId']) {
			$transaction = $imojeApi->getTransaction(
				$post['transactionId']
			);

			if(!$transaction['success']) {
				Util::doResponseJson(json_encode([
					'status' => $transaction['success'],
				]));
			}

			if($transaction['body']['transaction']['status'] === 'settled') {

				$urlSuccess = $this->context->link->getModuleLink('imoje', 'success');

				if(!$post['cartId']) {

					Util::doResponseJson(
						json_encode(
							$this->prepareSuccessResponse(
								$transaction,
								$urlSuccess
							)
						)
					);
				}

				$cart = new Cart($post['cartId']);

				if(!$cart->id) {
					Util::doResponseJson(
						json_encode(
							$this->prepareSuccessResponse(
								$transaction,
								$urlSuccess
							)
						)
					);
				}

				$order = Order::getByCartId($post['cartId']);

				$customer = new Customer($cart->id_customer);

				Util::doResponseJson(
					json_encode(
						$this->prepareSuccessResponse(
							$transaction,
							$order
								? $this->context->link->getPageLink('order-confirmation', null, null, [
								'id_cart'   => $cart->id,
								'id_module' => $this->module->id,
								'id_order'  => $order->id,
								'key'       => $customer->secure_key,
							])
								: (Configuration::get('IMOJE_GA_KEY')
								? $this->context->link->getModuleLink('imoje', 'success', [
									'ga_cart_id' => $post['cartId'],
									'ga_hash'    => hash('sha256', $post['cartId'] . $cart->secure_key),
								])
								: $urlSuccess)
						)
					)
				);
			}

			Util::doResponseJson(
				json_encode(
					$this->prepareSuccessResponse($transaction)
				)
			);
		}
		// endregion

		$cart = $this->context->cart;

		if(!$cart->date_upd) {
			Tools::redirect('index');

			return;
		}

		$imoje = new Imoje();

		$paymentMethodBlik = Util::getPaymentMethod('blik');
		$paymentMethodCodeBlik = Util::getPaymentMethodCode('blik');
		$paymentMethodCodeBlikOneclick = Util::getPaymentMethodCode('blik_oneclick');

		if(!Configuration::get('IMOJE_BLIK_CODE_CHECKOUT')) {

			$transaction = $imojeApi->createTransaction(
				$imoje->getDataForRequestToApi(
					$cart,
					$paymentMethodBlik,
					$paymentMethodCodeBlik
				)
			);

			if(!$transaction['success']) {

				PrestaShopLogger::addLog($this->module->l('Could not initialize transaction:', 'pbl')
				. ' '
				. json_encode( $transaction ), 3, null, 'imoje');

				Tools::redirect($this->context->link->getPageLink('history', true));

				return;
			}

			$this->context->smarty->assign([
				'form'                    => $imojeApi->buildOrderForm($transaction),
				'ga_key'                  => Configuration::get('IMOJE_GA_KEY'),
				'blik_msg'                => true,
				'checkout_link'           => $this->context->link->getPageLink(Configuration::get('PS_ORDER_PROCESS_TYPE')
					? 'order-opc'
					: 'order'),
				'text_return_to_checkout' => $this->module->l('Please wait, you will be returned to checkout.', 'payment'),
			]);

			$this->setTemplate(Imoje::buildTemplatePath('pay', 'front'));

			return;
		}

		// region deactivate profile
		if($post['profileId'] && $post['deactivate'] && $post['blikKey']) {

			$deactivate = $imojeApi->createTransaction(
				json_encode([
					'blikProfileId' => $post['profileId'],
					'blikKey'       => $post['blikKey'],
				]), $imojeApi->getDeactivateBlikProfileUrl()
			);

			Util::doResponseJson(json_encode([
				'status' => $deactivate['success'],
			]));
		}
		// endregion

		// region debit profile
		if($post['profileId']
			&& ($post['blikKey']
				|| ($post['rememberBlikCode'] && $post['blikCode']))) {

			$data = json_decode($imoje->getDataForRequestToApi(
				$cart,
				$paymentMethodBlik,
				$paymentMethodCodeBlikOneclick,
				$post['profileId']
			), true);

			$transaction = $imojeApi->createTransaction(
				$imojeApi->prepareBlikOneclickData(
					$post['profileId'],
					$data['amount'],
					$data['currency'],
					$data['orderId'],
					$data['title'],
					$data['clientIp'],
					$post['blikKey'],
					$post['blikCode']
				), $imojeApi->getDebitBlikProfileUrl()
			);

			if(!$transaction['success']) {
				Util::doResponseJson(json_encode([
					'status' => $transaction['success'],
				]));
			}

			Util::doResponseJson(json_encode($this->prepareSuccessResponse($transaction)));
		}
		// endregion

		$activeProfile = [];
		$profileId = null;

		$isCustomerLogger = $this->context->customer->isLogged();
		//        $isImojeBlikOneclick = Configuration::get('IMOJE_BLIK_ONECLICK');
		$isImojeBlikOneclick = false;
		$createOrderArrangement = Configuration::get('IMOJE_CREATE_ORDER_ARRANGEMENT');

		if($isCustomerLogger && $isImojeBlikOneclick && $createOrderArrangement) {

			$profileList = $imojeApi->getBlikProfileList(
				Util::getCid($this->context->customer->id . $this->context->customer->email)
			);

			if((isset($profileList['success']) && $profileList['success'])
				&& $profileList['body']['blikProfiles']) {

				foreach($profileList['body']['blikProfiles'] as $profile) {

					if($profile['isActive']) {

						$isProfileLabel = isset($profile['aliasList'][1]);

						foreach($profile['aliasList'] as $k => $v) {

							if(!$isProfileLabel) {

								$profile['aliasList'][$k]['label'] = $profile['label'];
							}
						}

						$profileId = $profile['id'];
						$activeProfile = $profile;
						break;
					}
				}
			}
		}

		if($post['blikCode']) {

			$transaction = $imojeApi->createTransaction(
				$imoje->getDataForRequestToApi(
					$cart,
					$paymentMethodBlik,
					$post['rememberBlikCode']
						? $paymentMethodCodeBlikOneclick
						: $paymentMethodCodeBlik,
					$post['blikCode']
				)
			);

			if(!$transaction['success']) {
				Util::doResponseJson(json_encode([
					'status' => $transaction['success'],
				]));
			}

			Util::doResponseJson(json_encode($this->prepareSuccessResponse($transaction)));
		}

		// region display blik code template
		$this->context->smarty->assign([
			'create_order_arrangement' => $createOrderArrangement,
			'payment_blik_url'         => $this->context->link->getModuleLink(
				'imoje',
				'paymentblik'),
			'profile_id'               => $profileId,
			'payment_tip'              => $this->module->l('Please wait, payment is processing.', 'paymentblik'),
			'accept_tip1'              => $this->module->l('Now accept your payment in bank application.', 'paymentblik'),
			'accept_tip2'              => $this->module->l('You will be informed via e-mail about it end status, or you can check your payment status later.', 'paymentblik'),
			'failure_tip1'             => $this->module->l('Unable to complete your payment request.', 'paymentblik'),
			'failure_tip2'             => $this->module->l('Try again later or contact with shop staff.', 'paymentblik'),
			'active_profile'           => $activeProfile,
			'is_customer_logged'       => $isCustomerLogger,
			'is_imoje_blik_oneclick'   => $isImojeBlikOneclick && $createOrderArrangement,
		]);

		$this->setTemplate(Imoje::buildTemplatePath('payBlikCode', 'front'));
		// endregion

	}

	/**
	 * @param array  $transaction
	 * @param string $urlRedirect
	 * @param bool   $reject
	 *
	 * @return array
	 */
	private function prepareSuccessResponse($transaction, $urlRedirect = '', $reject = false)
	{

		$array = [
			'status' => $transaction['success'],
			'body'   => [
				'transaction' => [
					'status' => $transaction['body']['transaction']['status'],
					'id'     => $transaction['body']['transaction']['id'],
				],
			],
		];

		if($urlRedirect) {
			$array['body']['urlRedirect'] = $urlRedirect;
		}

		if($reject) {
			$array['body']['rejected'] = $reject;
		}

		if(isset($transaction['body']['transaction']['statusCode']) && $transaction['body']['transaction']['statusCode']) {
			$array['body']['error'] = $this->getErrorMessage($transaction['body']['transaction']['statusCode']);
		}

		if(isset($transaction['body']['code']) && $transaction['body']['code']) {
			$array['body']['code'] = $transaction['body']['code'];
		}

		if(isset($transaction['body']['newParamAlias']) && $transaction['body']['newParamAlias']) {
			$array['body']['newParamAlias'] = $transaction['body']['newParamAlias'];
		}

		return $array;
	}

	/**
	 * @param string $code
	 *
	 * @return string
	 */
	private function getErrorMessage($code)
	{

		$tEnterT6 = $this->module->l('Insert BLIK code.', 'paymentblik');
		$tTryAgain = $this->module->l('Please try again.', 'paymentblik');

		switch($code) {
			case 'BLK-ERROR-210000':
				return $this->module->l('Payment failed.', 'paymentblik') . ' ' . $tTryAgain;
			case 'BLK-ERROR-210001':
				return $this->module->l('Technical break in your bank. Pay later or use another bank\'s application.', 'paymentblik');
			case 'BLK-ERROR-210002':
				return $this->module->l('Alias not found. To proceed the payment you need to pay with BLIK code.', 'paymentblik');
			case 'BLK-ERROR-210003':
			case 'BLK-ERROR-210004':
				return $this->module->l('Alias declined. To proceed the payment you need to pay with BLIK code.', 'paymentblik');
			case 'BLK-ERROR-210005':
				$msg = $this->module->l('You have entered wrong BLIK code.', 'paymentblik');
				break;
			case 'BLK-ERROR-210006':
				$msg = $this->module->l('BLIK code expired.', 'paymentblik');
				break;
			case 'BLK-ERROR-210007':
			case 'BLK-ERROR-210008':
				$msg = $this->module->l('Something went wrong with BLIK code.', 'paymentblik');
				break;
			case 'BLK-ERROR-210009':
				$msg = $this->module->l('Payment declined at the banking application.', 'paymentblik');
				break;
			case 'BLK-ERROR-210010':
			case 'BLK-ERROR-210011':
				$msg = $this->module->l('Payment failed - not confirmed on time in the banking application.', 'paymentblik');
				break;
			case 'BLK-ERROR-210012':
				$msg = $this->module->l('Inserted wrong PIN code in banking application.', 'paymentblik');
				break;
			case 'BLK-ERROR-210013':
				$msg = $this->module->l('Payment failed (security).', 'paymentblik');
				break;
			case 'BLK-ERROR-210014':
				$msg = $this->module->l('Limit exceeded in your banking application.', 'paymentblik');
				break;
			case 'BLK-ERROR-210015':
				$msg = $this->module->l('Insufficient funds in your bank account.', 'paymentblik');
				break;
			case 'BLK-ERROR-210016':
				$msg = $this->module->l('Issuer declined.', 'paymentblik');
				break;
			case 'BLK-ERROR-210017':
				$msg = $this->module->l('Transaction not found.', 'paymentblik');
				break;
			case 'BLK-ERROR-210018':
				$msg = $this->module->l('Bad IBAN.', 'paymentblik');
				break;
			case 'BLK-ERROR-210019':
				$msg = $this->module->l('Transfer not possible.', 'paymentblik');
				break;
			case 'BLK-ERROR-210020':
				$msg = $this->module->l('Return late.', 'paymentblik');
				break;
			case 'BLK-ERROR-210021':
				$msg = $this->module->l('Return amount exceeded.', 'paymentblik');
				break;
			case 'BLK-ERROR-210022':
				$msg = $this->module->l('Transfer late.', 'paymentblik');
				break;
			default:
				return $this->module->l('Payment failed.', 'paymentblik') . ' ' . $tEnterT6;
		}

		return $msg . ' ' . $tEnterT6;
	}

	/**
	 * Verify that error code needs to retype BLIK code in frontend
	 *
	 * @param string $code
	 *
	 * @return bool
	 */
	private function checkRetypeCode($code)
	{
		$array = [
			'BLK-ERROR-210002',
			'BLK-ERROR-210003',
			'BLK-ERROR-210004',
			'BLK-ERROR-210005',
			'BLK-ERROR-210006',
			'BLK-ERROR-210007',
			'BLK-ERROR-210008',
			'BLK-ERROR-210009',
			'BLK-ERROR-210010',
			'BLK-ERROR-210011',
			'BLK-ERROR-210012',
			'BLK-ERROR-210013',
			'BLK-ERROR-210014',
			'BLK-ERROR-210015',
			'BLK-ERROR-210016',
			'BLK-ERROR-210017',
			'BLK-ERROR-210018',
			'BLK-ERROR-210019',
			'BLK-ERROR-210020',
			'BLK-ERROR-210021',
			'BLK-ERROR-210022',

		];

		return in_array($code, $array);
	}

	/**
	 * Verify that error code needs to create new paramAlias
	 *
	 * @param string $code
	 *
	 * @return bool
	 */
	private function checkNewParamAlias($code)
	{

		return in_array($code, [
			'BLK-ERROR-210002',
			'BLK-ERROR-210003',
			'BLK-ERROR-210004',
		]);
	}
}
