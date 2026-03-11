<?php
/** * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 * ****************************************************
 * *                    Pixel Plus                    *
 * *          http://www.smart-modules.com            *
 *
 * Versions:
 * To check the complete changelog. open versions.txt file
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class FacebookConversionTrackingPlusAjaxConversionModuleFrontController extends ModuleFrontController
{
    private $api = false;

    public function init()
    {
        parent::init();
        if (Tools::getValue('action') == 'viewCookies' || Tools::getValue('action') == 'updateConsent') {
            // Use the ajax process method
            return;
        }
        // Prevent indexing
        header('Content-Type: application/json');
        header('X-Robots-Tag: noindex, nofollow', true);
        $context = $this->context;
        $cookie_id = 'CookieValidate' . ($context->cookie->id_customer > 0 ? $context->cookie->id_customer : $context->cookie->id_guest);
        $return = '';
        if (Tools::getValue('trackRegister')) {
            $return = $this->module->trackAjaxRegistration();
        } elseif (Tools::getIsset('cookieConsent') && ($cookie_id || Configuration::get('FCTP_BLOCK_BASIC'))) {
            $cookie = '';
            $value = '';
            if (Configuration::get('FCTP_BLOCK_SCRIPT')) {
                $cookie = Configuration::get('FCTP_COOKIE_NAME');
                if ($cookie != '') {
                    $value = Configuration::get('FCTP_COOKIE_VALUE');
                }
            }
            $return = PixelTools::checkCookies($cookie, $value);
        } elseif (Tools::getIsset('localStorageConsent') && Tools::getValue('token') == Tools::encrypt('CookieValidate' . ($context->cookie->id_customer > 0 ? $context->cookie->id_customer : $context->cookie->id_guest))) {
            // Set the cookie to validate the consent
            $context->cookie->__set('fctp_localstorage_consent', 1);
            $return = true;
        } elseif (Tools::getIsset('simple_event')) { // Events without API usage
            $e = Tools::getValue('simple_event');
            switch ($e) {
                case 'InitiateCheckout':
                    $this->context->cookie->__set('InitiateCheckout', Tools::getValue('source_url'));
                    $return = true;
                    break;
                case 'Purchase':
                    $return = $this->trackPurchaseEvent(false);
                    //                    $id_order = (int) Tools::getValue('id_order');
                    //                    if (!Configuration::get('FCTP_COOKIE_CONTROL') || (!isset($_COOKIE['pp_purchaseSent']) || $_COOKIE['pp_purchaseSent'] !== (int) $id_order)) {
                    //                        if (Configuration::get('FCTP_COOKIE_CONTROL')) {
                    //                            setcookie('pp_purchaseSent', (int) $id_order);
                    //                        }
                    //                        $return = $this->module->trackAjaxConversion((int) Tools::getValue('id_customer'));
                    //                    }
                    break;
            }
        } elseif (Tools::getIsset('event')) {
            $e = Tools::getValue('event');
            $event_id = Tools::passwdGen(12);
            // if (Configuration::get('FCTP_CONVERSION_API')) {
            $this->api = new ConversionApi($this->module);
            if ($this->api->getActive()) {
                switch ($e) {
                    case 'Purchase':
                        $return = $this->trackPurchaseEvent();
                        break;
                    case 'InitiateCheckout':
                        if (Tools::getIsset('id_cart')) {
                            $id_cart = (int) Tools::getValue('id_cart');
                            $return = $this->api->initiateCheckoutTrigger($event_id, $id_cart, true);
                        }
                        break;
                    case 'ViewContent':
                        // Track a combination change on 1.6 versions
                        $return = $this->api->ViewContentTrigger(
                            Tools::strlen(Tools::getValue('event_id') == 12) ? Tools::getValue('event_id') : $event_id,
                            $context->language->id,
                            'product',
                            (int) Tools::getValue('id_product'),
                            (int) Tools::getValue('id_product_attribute'),
                            true
                        );
                        break;
                    case 'PageView':
                        $return = $this->api->pageViewTrigger(pSQL(Tools::getValue('pageview_event_id')), '', true);
                        break;
                    case 'AddToCart':
                        $return = $this->api->addToCartTrigger();
                        break;
                    case 'Search':
                        $return = $this->api->searchEventTrigger(Tools::getValue('search_query'), Tools::getValue('content_ids_list'), true);
                        break;
                    case 'Contact':
                        $subject_chosen = Tools::getValue('subject') ?? $this->module->l('No specified subject');
                        $return = $this->api->contactTrigger($subject_chosen);
                        break;
                    case 'Newsletter':
                        $return = $this->api->newsletterTrigger();
                        break;
                    case 'AddPaymentInfo':
                        $value = Tools::getValue('value') ?? 0;
                        $return = $this->api->addPaymentInfoTrigger($value);
                        break;
                    case 'Pagetime':
                        if ($this->validateTime(Tools::getValue('time'))) {
                            $return = $this->api->pageTimeEvent(Tools::getValue('time'));
                        }
                        break;
                    case 'Pageviewcount':
                        $return = $this->api->pageViewCountEvent();
                        break;
                    case 'Discount':
                        $value = Tools::getValue('discount_coupon') ?? 0;
                        $return = $this->api->discountEvent($value);
                        break;
                    case 'AddToWishlist':
                        if (!Tools::getIsset('id_product')) {
                            return false;
                        }
                        $id_product = (int) Tools::getValue('id_product');
                        $id_product_attribute = Tools::getIsset('id_product_attribute') ? (int) Tools::getValue('id_product_attribute') : 0;
                        $return = $this->api->wishlistEventTrigger($id_product, $id_product_attribute, Tools::passwdGen(12));
                        break;
                    default:
                        return false;
                }
            }
        }
        if (is_bool($return)) {
            echo $return ? '{"return":"ok"}' : '{"return":"error"}';
        } elseif ($return != '' && PixelTools::isJson($return)) {
            echo $return;
        } else {
            // Should always return something
            echo '{"return":"error"}';
        }
        exit;
    }

    public function display()
    {
        exit;
    }

    private function trackPurchaseEvent($api = true)
    {
        $id_order = (int) Tools::getValue('id_order');
        if (!$id_order) {
            echo '{"return":"error", "msg":"No order ID"}';

            return;
        }
        if (!Tools::getIsset('id_customer')) {
            echo '{"return":"error", "msg":"No customer"}';

            return;
        }

        $id_customer = (int) Tools::getValue('id_customer');

        if (!Configuration::get('FCTP_COOKIE_CONTROL') || (!isset($_COOKIE['pp_purchaseSent']) || $_COOKIE['pp_purchaseSent'] !== (int) $id_order)) {
            if (Configuration::get('FCTP_COOKIE_CONTROL')) {
                /* Backup Method, needs more testing
                if ($api && (!isset($_COOKIE['pp_purchaseSent']) || $_COOKIE['pp_purchaseSent'] !== (int) $id_order)) {
                    // Call the API
                    $event_id = Tools::getValue('event_id') ? Tools::getValue('event_id') : Tools::passwdGen(12);
                    $this->api->purchaseEventTrigger($event_id, $id_order);
                } else {
                    setcookie('pp_purchaseSent', (int)$id_order);
                }
                */
                setcookie('pp_purchaseSent', (int) $id_order);
            }

            return $this->module->trackAjaxConversion($id_customer);
        }
    }

    private function validateTime($value)
    {
        return preg_match('/^(\+)?\d+$/', $value) && (int) $value % 30 === 0;
    }

    /**
     * Creates a temporal list of the active cookies,
     * removing the ones we don't need and returns it as a JSON object
     *
     * @return void
     */
    public function displayAjaxViewCookies()
    {
        $config = 'FCTP_MICRO_TOKEN';
        $cookies_to_skip = [
            'date_add',
            'id_lang',
            'id_currency',
            'id_customer',
            'id_guest',
            'is_guest',
            'id_connections',
            'customer_lastname',
            'customer_firstname',
            'passwd',
            'logged',
            'email',
            'id_cart',
            'id_address_invoice',
            'id_address_delivery',
            'session_id',
            'session_token',
            'checksum',
            'last_visited_category',
        ];

        $response = [
            'success' => false,
            'cookies_list' => [],
        ];

        if (Tools::getIsset('pp_token') && Tools::getIsset('pp_token') == Configuration::getGlobalValue($config)) {
            $age = PixelTools::getConfigurationAge($config);
            if (!empty($age) && $age <= 15) {
                $response['success'] = true;
                $response['cookies_list'] = array_diff_key($this->context->cookie->getAll(), array_flip($cookies_to_skip));
            }
            // Add special cases
            if (Module::isEnabled('lgcookieslaw') && isset($response['cookies_list']['lgcookieslaw'])) {
                // Try to get the cookies value.
                $lgcookieslaw_module = Module::getInstanceByName('lgcookieslaw');
                $response['cookies_list']['lgcookieslaw'] = json_encode($lgcookieslaw_module->getCookieValues());
            }
        }
        header('Content-Type: application/json');

        echo json_encode($response);
        exit;
    }

    public function displayAjaxUpdateConsent()
    {
        $this->module->updateConsent((int) Tools::getValue('consent'));
    }
}
