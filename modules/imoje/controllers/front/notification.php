<?php

use Imoje\Payment\Api;
use Imoje\Payment\Util;
use Imoje\Payment\Notification as SdkNotification;

/**
 * Class ImojeNotificationModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 * @property bool display_footer
 * @property bool display_header
 */
class ImojeNotificationModuleFrontController extends ModuleFrontController
{

	/**
	 * @throws PrestaShopException
	 * @see FrontController::init()
	 */
	public function init()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;
		$this->display_footer = false;
		$this->display_header = false;
		parent::init();
	}

	/**
	 * The 'payment' object is used for general information about the payment
	 * The 'transaction' object is used for specific transaction information in situations sensitive to transaction type or status, e.g. refunds.
	 *
	 * @throws PrestaShopException
	 * @throws Exception
	 */
	public function process()
	{

		include_once(_PS_MODULE_DIR_ . 'imoje/libraries/payment-core/vendor/autoload.php');

		$notification = new SdkNotification(Configuration::get('IMOJE_SERVICE_ID'), Configuration::get('IMOJE_SERVICE_KEY'));
		$resultCheckRequestNotification = $notification->checkRequest();

		$arrangementAfterIpn = Configuration::get('IMOJE_CREATE_ORDER_ARRANGEMENT');

		if(is_int($resultCheckRequestNotification)) {
			echo $notification->formatResponse(SdkNotification::NS_ERROR,
				$resultCheckRequestNotification);
			exit();
		}

		/*
		* Verifies if notification contains 'transaction' object and transaction is a refund.
		* If so, triggers the refund processing for this transaction
		*/
		if(isset($resultCheckRequestNotification['transaction']) && $resultCheckRequestNotification['transaction']['type'] === SdkNotification::TRT_REFUND) {
			$this->processRefund($resultCheckRequestNotification, $notification, Configuration::get('IMOJE_CREATE_ORDER_ARRANGEMENT'));

			return;
		}

		/*
		* Verifies if notification contains 'transaction' object and order creation via notification is enabled or customer made order with blik code on checkout.
		* If so, process notification to create an order
		*/
		if((isset($resultCheckRequestNotification['transaction']) && $resultCheckRequestNotification['transaction']['paymentMethod'] === 'blik'
				&& $resultCheckRequestNotification['transaction']['source'] === 'api'
				&& Configuration::get('IMOJE_BLIK_CODE_CHECKOUT'))
			|| $arrangementAfterIpn) {

			$this->processIpn($resultCheckRequestNotification, $notification);

			return;
		}

		$this->processCheckout($resultCheckRequestNotification, $notification);
	}

	/**
	 * The 'payment' object is used for general information about the payment
	 *
	 * @param array           $ipn
	 * @param SdkNotification $notification
	 *
	 * @return void
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	private function processCheckout($ipn, $notification)
	{

		$order = new Order($ipn['payment']['orderId']);

		$orderId = $order->id;

		if(!$orderId) {
			echo $notification->formatResponse(
				SdkNotification::NS_ERROR,
				SdkNotification::NC_ORDER_NOT_FOUND
			);
			exit();
		}

		$currentOrderState = $order->current_state;

		switch($ipn['payment']['status']) {
			case SdkNotification::TRS_SETTLED:
				$stateToChange = _PS_OS_PAYMENT_;
				break;
			case SdkNotification::TRS_REJECTED:
				$stateToChange = _PS_OS_ERROR_;
				break;
			case SdkNotification::TRS_CANCELLED:

				if(!Configuration::get('IMOJE_CANCEL_ORDER')) {

					echo $notification->formatResponse(
						SdkNotification::NS_OK,
						SdkNotification::NC_ORDER_CANCELLATION_IS_NOT_ENABLED,
						$currentOrderState
					);
					exit();
				}

				if($currentOrderState !== (int) Configuration::get('PAYMENT_IMOJE_NEW_STATUS')) {
					echo $notification->formatResponse(
						SdkNotification::NS_OK,
						SdkNotification::NC_INVALID_ORDER_STATUS_FOR_CANCELLATION
					);
					exit();
				}

				$stateToChange = _PS_OS_CANCELED_;
				break;
			default:
				echo $notification->formatResponse(
					SdkNotification::NS_OK,
					SdkNotification::NC_ORDER_STATUS_NOT_CHANGED,
					$currentOrderState
				);
				exit();
		}

		$orderStatusesToDoNotChange = [
			_PS_OS_REFUND_   => _PS_OS_REFUND_,
			_PS_OS_PAYMENT_  => _PS_OS_PAYMENT_,
			_PS_OS_ERROR_    => _PS_OS_ERROR_,
			_PS_OS_CANCELED_ => _PS_OS_CANCELED_,
		];

		if(isset($orderStatusesToDoNotChange[$currentOrderState])) {
			echo $notification->formatResponse(
				SdkNotification::NS_OK,
				SdkNotification::NC_ORDER_STATUS_NOT_CHANGED,
				$currentOrderState
			);
			exit();
		}

		$currencyInfo = Currency::getCurrency($order->id_currency);

		if(!SdkNotification::checkRequestAmount(
			$ipn,
			Util::convertAmountToFractional(round($order->total_paid, 2)),
			$currencyInfo["iso_code"])) {

			echo $notification->formatResponse(SdkNotification::NS_ERROR, SdkNotification::NC_AMOUNT_NOT_MATCH, $currentOrderState);
			exit();
		}

		$history = new OrderHistory();
		$history->id_order = $orderId;
		$history->changeIdOrderState($stateToChange, $orderId);
		$history->addWithemail(true);

		$payments = $order->getOrderPaymentCollection()->getResults();

		if(count($payments) > 0) {
			$payments[0]->transaction_id = $ipn['payment']['id'];
			$payments[0]->update();
		}

		if(!$this->insertTransactionId($orderId, $ipn['payment']['id'])) {

			echo $notification->formatResponse(
				SdkNotification::NS_ERROR,
				SdkNotification::NC_COULD_NOT_INSERT_TRANSACTION_ID_TO_DB,
				$currentOrderState,
				$stateToChange
			);
			exit;
		}

		echo $notification->formatResponse(
			SdkNotification::NS_OK,
			SdkNotification::NC_OK,
			$currentOrderState,
			$stateToChange
		);
		exit;
	}

	/**
	 * The 'transaction' object is used for specific transaction information in situations sensitive to transaction type or status, e.g. refunds.
	 *
	 * @param array           $ipn
	 * @param SdkNotification $notification
	 * @param bool            $createOrderArrangement
	 *
	 * @return void
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	private function processRefund($ipn, $notification, $createOrderArrangement)
	{

		if($createOrderArrangement) {

			$cart = new Cart($ipn['transaction']['orderId']);

			if(!$cart->id) {
				echo $notification->formatResponse(
					SdkNotification::NS_ERROR,
					SdkNotification::NC_CART_NOT_FOUND);
				exit;
			}

			$order = Order::getByCartId($cart->id);
		} else {
			$order = new Order($ipn['transaction']['orderId']);
		}

		if(!$order) {
			echo $notification->formatResponse(
				SdkNotification::NS_OK,
				SdkNotification::NC_ORDER_NOT_FOUND);
			exit;
		}

		$orderId = $order->id;

		if(!$orderId) {
			echo $notification->formatResponse(
				SdkNotification::NS_ERROR,
				SdkNotification::NC_ORDER_NOT_FOUND
			);
			exit();
		}

		if($ipn['transaction']['status'] !== SdkNotification::TRS_SETTLED) {
			echo $notification->formatResponse(SdkNotification::NS_ERROR, SdkNotification::NC_IMOJE_REFUND_IS_NOT_SETTLED);

			exit();
		}

		$orderCurrentState = $order->getCurrentState();

		$stateArray = [
			_PS_OS_CANCELED_,
			_PS_OS_REFUND_,
		];

		if(in_array($orderCurrentState, $stateArray)) {
			echo $notification->formatResponse(SdkNotification::NS_ERROR, SdkNotification::NC_ORDER_STATUS_IS_INVALID_FOR_REFUND);
			exit();
		}

		$api = new Api(
			Configuration::get('IMOJE_TOKEN'),
			Configuration::get('IMOJE_MERCHANT_ID'),
			Configuration::get('IMOJE_SERVICE_ID'),
			Configuration::get('IMOJE_SANDBOX')
				? Util::ENVIRONMENT_SANDBOX
				: Util::ENVIRONMENT_PRODUCTION
		);

		$apiTransaction = $api->getTransaction($ipn['originalTransactionId']);

		if(!$apiTransaction['success']) {
			echo $notification->formatResponse(SdkNotification::NS_ERROR, SdkNotification::NC_TRANSACTION_NOT_FOUND);
			exit();
		}

		if(Util::calculateAmountToRefund($apiTransaction) === 0) {
			$history = new OrderHistory();
			$history->id_order = $orderId;
			$history->changeIdOrderState(Configuration::get('PS_OS_REFUND'), $orderId);
			$history->addWithemail(true, []);

			echo $notification->formatResponse(SdkNotification::NS_OK);
			exit;
		}

		echo $notification->formatResponse(SdkNotification::NS_OK);
		exit;
	}

	/**
	 * The 'payment' object is used for general information about the payment
	 * The 'transaction' object is used for specific transaction information in situations sensitive to transaction type or status, e.g. refunds.
	 *
	 * @param array           $ipn
	 * @param SdkNotification $notification
	 *
	 * @return void
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	private function processIpn($ipn, $notification)
	{

		if($ipn['payment']['status'] !== 'settled') {
			echo $notification->formatResponse(
				SdkNotification::NS_OK,
				SdkNotification::NC_ORDER_STATUS_IS_NOT_SETTLED_ORDER_ARRANGEMENT_AFTER_IPN
			);
			exit;
		}

		$cart = new Cart($ipn['payment']['orderId']);

		if(!$cart->id) {
			echo $notification->formatResponse(
				SdkNotification::NS_ERROR,
				SdkNotification::NC_CART_NOT_FOUND);
			exit;
		}

		if(Order::getByCartId($cart->id)) {
			echo $notification->formatResponse(
				SdkNotification::NS_OK,
				SdkNotification::NC_ORDER_EXISTS_ORDER_ARRANGEMENT_AFTER_IPN);
			exit;
		}

		$currencyInfo = Currency::getCurrency($cart->id_currency);

		$cartTotal = $cart->getOrderTotal();

		if(!SdkNotification::checkRequestAmount(
			$ipn,
			Util::convertAmountToFractional($cartTotal),
			$currencyInfo["iso_code"])) {

			echo $notification->formatResponse(SdkNotification::NS_ERROR, SdkNotification::NC_AMOUNT_NOT_MATCH);
			exit();
		}

		$customer = new Customer((int) $cart->id_customer);
		$cartId = $cart->id;

		$imoje = new Imoje();
		$imoje->validateOrder($cartId,
			_PS_OS_PAYMENT_,
			$cartTotal,
			$imoje->displayName,
			null,
			[],
			null,
			false,
			$customer->secure_key
		);

		$order = new Order(Order::getIdByCartId($ipn['payment']['orderId']));

		Imoje::checkAndChangeNegativeOrderStatus($order->id);

		$payments = $order->getOrderPaymentCollection()->getResults();

		if(count($payments) > 0) {
			$payments[0]->transaction_id = $ipn['payment']['id'];
			$payments[0]->update();
		}

		if(!$this->insertTransactionId($order->id, $ipn['transaction']['id'])) {

			echo $notification->formatResponse(SdkNotification::NS_ERROR, SdkNotification::NC_COULD_NOT_INSERT_TRANSACTION_ID_TO_DB, null, _PS_OS_PAYMENT_);
			exit;
		}

		echo $notification->formatResponse(SdkNotification::NS_OK, SdkNotification::NC_OK, null, _PS_OS_PAYMENT_);
		exit();
	}

	/**
	 * @param $idOrder
	 * @param $idTransaction
	 *
	 * @return bool
	 */
	private function insertTransactionId($idOrder, $idTransaction)
	{

		$valid = false;
		try {
			$valid = Db::getInstance()->insert('imoje_transaction_list', [
				'id_order'       => pSQL($idOrder),
				'id_transaction' => pSQL($idTransaction),
			]);
		} catch(PrestaShopDatabaseException $e) {
		}

		return $valid;
	}

	/**
	 * @param array $ipn
	 *
	 * @return int
	 */
	private function retrieveOrderId($ipn)
	{
		$order = new Order($ipn['payment']['orderId']);

		return $order->id;
	}
}
