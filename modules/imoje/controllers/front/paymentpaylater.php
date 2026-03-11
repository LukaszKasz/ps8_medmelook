<?php

use Imoje\Payment\Api;
use Imoje\Payment\Util;

/**
 * Class ImojePaymentpaylaterModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class ImojePaymentpaylaterModuleFrontController extends ModuleFrontController
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

        $cart = $this->context->cart;

        $imojeToken = Configuration::get('IMOJE_TOKEN');
        $merchantId = Configuration::get('IMOJE_MERCHANT_ID');
        $serviceId = Configuration::get('IMOJE_SERVICE_ID');

        if (!$cart->date_upd
            || !$imojeToken
            || !$merchantId
            || !$serviceId) {
            Tools::redirect('index');

            return;
        }

        $imoje = new Imoje();

        $imojeApi = new Api(
            $imojeToken,
            $merchantId,
            $serviceId,
            Configuration::get('IMOJE_SANDBOX')
                ? Util::ENVIRONMENT_SANDBOX
                : Util::ENVIRONMENT_PRODUCTION
        );

        $transaction = $imojeApi->createTransaction(
            $imoje->getDataForRequestToApi(
                $cart,
                Util::getPaymentMethod('paylater'),
                Util::getPaymentMethodCode('paylater')
            )
        );

        if (!$transaction['success']) {
            Tools::redirect($this->context->link->getPageLink('history', true));

            return;
        }

        $this->context->smarty->assign([
            'form'                    => $imojeApi->buildOrderForm($transaction),
            'checkout_link'           => $this->context->link->getPageLink(Configuration::get('PS_ORDER_PROCESS_TYPE')
                ? 'order-opc'
                : 'order'),
            'text_return_to_checkout' => $this->module->l('Please wait, you will be returned to checkout.', 'payment'),
            'loading_gif'             => Imoje::getMediaPath(_PS_MODULE_DIR_ . $imoje->name . '/assets/img/loading.gif'),
        ]);

        $this->setTemplate(Imoje::buildTemplatePath('pay', 'front'));
    }
}
