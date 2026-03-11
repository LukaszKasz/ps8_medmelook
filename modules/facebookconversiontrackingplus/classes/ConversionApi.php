<?php
/**
 * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class ConversionApi // extends ObjectModel
{
    const DEFAULT_ROUND = 10;
    const FB_COOKIE_EXPIRATION = 90 * 24 * 60 * 60; // 90 days
    // Send the events through the API as long as it's true
    private $active = true;
    private $isBot = false;
    public $user_email;
    public $id_guest;
    public $id_customer;
    public $ip;

    // Full list of IPs detected
    public $all_ips;
    public static $currency_iso_code;
    private static $curl_timeout;
    private $pixels_ids = [];
    private $_fbp = false;
    private $_fbc = false;
    private $context;
    public $url;
    public $curl;
    public $ret;
    protected $logEnabled = false;
    protected $advanced_matching = false;
    private static $call_prefix = [];
    private static $user_data = [];
    private $module;
    private static $consent;
    /* List of the saved events */
    private $events = [];

    public $user_agent;
    private $product_prefix = '';
    private $combination_prefix = '';
    private $combination = false;
    // Price Precision
    private $pp = 2;
    private $usetax = true;
    /**
     * @var bool
     */
    private $logEvents = false;
    /**
     * @var bool
     */
    private $logIssues = false;
    /**
     * @var bool
     */
    private $logOthers = false;
    /**
     * @var bool|string
     */
    private $test_event_code = '';
    /**
     * @var bool|string
     */
    private $logPayload = false;
    /**
     * @var bool|string
     */
    private $testCodeEnabled = false;
    /**
     * @var string
     */
    private $external_id;

    public function __construct($module)
    {
        if (!($module instanceof Module && $module->name == 'facebookconversiontrackingplus')) {
            $this->active = false;

            return;
        }

        $this->module = $module;
        // Validate the IP before sending anything
        $this->all_ips = PixelTools::getRemoteAddr(true); // Remove the port from the returned IP
        //        Tools::dieObject($this->all_ips);
        // Check if logging should be enabled
        $this->setLogging();
        $this->ip = (is_array($this->all_ips) && isset($this->all_ips[0])) ? $this->all_ips[0] : $this->all_ips;
        if (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            if ($this->logEnabled && $this->logOthers) {
                PrestaShopLogger::addLog('[Pixel Plus][Conversions API] Detected an invalid IP [' . $this->ip . '] <br>Conversions API will not send events for this user to prevent issues. <br>No action from the user is required', 2, 403, 'PixelPlus');
            }

            return;
        }
        if (!Configuration::get('FCTP_CONVERSION_API')) {
            $this->active = false;
            if ($this->logEnabled && $this->logOthers) {
                PrestaShopLogger::addLog('[Pixel Plus][Conversions API] API Disabled', 2, 403, 'PixelPlus');
            }

            return;
        }

        // Check and set the consent
        self::$consent = PixelTools::getConsent();

        if (!self::$consent) {
            $this->active = false;
            if ($this->logEnabled && $this->logOthers) {
                PrestaShopLogger::addLog('[Pixel Plus][Conversions API] Consent not granted', 2, 403, 'PixelPlus');
            }

            return;
        }

        $this->isBot = $this->setIsBot();
        if (!$this->isBot) {
            if ($this->logEnabled && $this->logOthers) {
                PrestaShopLogger::addLog('[Pixel Plus][USER_AGENT][' . $_SERVER['HTTP_USER_AGENT'] . ']', 1, 200, 'PixelPlus');
            }
            // parent::__construct();
            $this->context = Context::getContext();
            $generated = false;

            // Set the Advanced matching options
            if (Configuration::get('FCTP_ADVANCED_MATCHING_OPTIONS')) {
                $modes = ['contact', 'personal', 'address'];
                foreach ($modes as $k => $mode) {
                    if (Configuration::get('FCTP_AMO_DATA_' . $mode) != 'on') {
                        unset($modes[$k]);
                    }
                }
                if (count($modes) > 0) {
                    $this->advanced_matching = $modes;
                }
            }
            $this->test_event_code = Configuration::get('FCTP_CONVERSION_API_TEST');
            $this->logPayload = Configuration::get('FCTP_CONVERSION_PAYLOAD');
            $this->testCodeEnabled = Configuration::get('FCTP_ENABLE_TEST_EVENTS');
            $this->pp = Configuration::get('PS_PRICE_DISPLAY_PRECISION') == false ? 2 : Configuration::get('PS_PRICE_DISPLAY_PRECISION');
            $this->usetax = !Group::getPriceDisplayMethod(Group::getCurrent()->id);
            $this->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

            // Get the Pixels with the Token configured
            $this->pixels_ids = $this->getPixelIDs();

            if (count($this->pixels_ids) > 0) {
                $this->id_customer = 0;
                if (isset($this->context->customer) && $this->context->customer !== null) {
                    //                    if (Validate::isEmail($this->context->cookie->email)) {
                    //                        $this->email = $this->context->cookie->email;
                    //                    }
                    $this->id_customer = $this->context->cookie->id_customer;
                }

                // External ID management
                if (Configuration::get('FCP_EXTERNAL_ID_USAGE') == 1 && isset($this->context->cookie->id_customer) && $this->context->cookie->id_customer > 0) {
                    $this->external_id = 'c-' . $this->context->cookie->id_customer;
                } elseif (Configuration::get('FCP_EXTERNAL_ID_USAGE') == 2 && (!isset($this->context->cookie->id_customer) || $this->context->cookie->id_customer === 0)) {
                    $this->external_id = (isset($this->context->cookie->id_customer) && $this->context->cookie->id_customer > 0) ? 'c-' . $this->context->cookie->id_customer : 'g-' . $this->context->cookie->id_guest;
                }

                $this->id_guest = $this->context->cookie->id_guest;
                $this->getCurrencyIso();
                $this->product_prefix = Configuration::get('FPF_PREFIX_' . $this->context->shop->id);
                $this->combination_prefix = Configuration::getGlobalValue('FCTP_COMBI_PREFIX_' . $this->context->shop->id);
                $this->combination = Configuration::getGlobalValue('FCTP_COMBI_' . $this->context->shop->id);

                if (Tools::getIsset('id_product')) {
                    $p = new Product((int) Tools::getValue('id_product'));
                    if ($p->id > 0) {
                        $l = $this->context->language->id;
                        $ipa = Tools::getIsset('id_product_attribute') ? Tools::getValue('id_product_attribute') : 0;
                        $this->url = $this->context->link->getProductLink($p, $p->link_rewrite[$l], Category::getLinkRewrite($p->id_category_default, $l), null, null, $this->context->shop->id, $ipa, false, false, true);
                    }
                } else {
                    $this->url = Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . '/' . ltrim(urldecode($_SERVER['REQUEST_URI']), '/');
                    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '' && PixelTools::strpos($this->url, $_SERVER['QUERY_STRING']) === false) {
                        $this->url .= urldecode($_SERVER['QUERY_STRING']);
                        $this->cleanUrlSensitiveParameters();
                    }
                }
                if ($this->active) {
                    $this->getFacebookCookies();
                }
            } else {
                $this->active = false;
                if ($this->logEnabled && $this->logOthers) {
                    PrestaShopLogger::addLog('[Pixel Plus][Conversions API] Token not configured, events won\'t be sent through the CAPI', 2, 403, 'PixelPlus');
                }

                return;
            }
        }
    }

    public function getPixelIDs()
    {
        $pixels_ids = [];
        $ids = explode(',', preg_replace('/[^,0-9]/', '', Configuration::get('FCTP_PIXEL_ID')));
        $pix_count = count($ids);
        if ($pix_count == 0) {
            PrestaShopLogger::addLog('[Pixel Plus][Conversions API] No Pixel ID Configured. Set up the Pixel ID to be able to send the Events through the API', 2, 403, 'PixelPlus');
        } else {
            for ($i = 0; $i < $pix_count; ++$i) {
                $token = Configuration::get('FCTP_CAPI_TOKEN_' . ($i + 1));
                if (!empty($token)) {
                    $pixels_ids[$i + 1] = ['id' => $ids[$i], 'token' => $token];
                }
            }
        }

        return $pixels_ids;
    }

    /**
     * Detects if a visit has bot headers to prevent bloated statistics for the Pixel's CAPI.
     *
     * @return bool true if the visit is from a bot
     */
    private function setIsBot()
    {
        // Use a static variable to cache the result during the current execution cycle
        static $isBot = null;

        // If already evaluated during this execution, return the cached result
        if ($isBot !== null) {
            return $isBot;
        }

        // Retrieve the current context and ensure the cookie object is valid
        $context = Context::getContext();
        if (!isset($context->cookie) || !is_object($context->cookie)) {
            PrestaShopLogger::addLog('[Pixel Plus][CAPI] Invalid cookie context detected.', 3, null, 'PixelPlus');

            return true; // Assume bot if the cookie cannot be reliably accessed
        }

        $cookie = $context->cookie;

        // Check if the result is already stored in the cookie
        if (isset($cookie->is_bot)) {
            $isBot = (bool) $cookie->is_bot;

            return $isBot;
        }

        // Perform bot detection
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $isBot = true;
        } else {
            $userAgent = Tools::strtolower($_SERVER['HTTP_USER_AGENT']);
            $botKeywords = [
                'bot', 'crawl', 'slurp', 'spider', 'mediapartners', 'fetch', 'facebookexternalhit',
                'Googlebot', 'Bingbot', 'yahoo', 'baidu', 'duckduckbot', 'yandex', 'ahrefsbot',
                'semrushbot', 'mj12bot', 'siteexplorer', 'sogou', 'linkedinbot', 'pinterest',
                'twitterbot', 'facebot',
            ];

            $isBot = false;
            foreach ($botKeywords as $keyword) {
                if (stripos($userAgent, $keyword) !== false) {
                    $isBot = true;
                    $this->logBotDetection($userAgent);
                    break;
                }
            }
        }

        // Save the result in the cookie for future visits
        if (method_exists($cookie, 'write')) {
            $cookie->is_bot = (int) $isBot;
            $cookie->write();
        } else {
            PrestaShopLogger::addLog('[Pixel Plus][CAPI] Failed to write bot status to cookie.', 3, null, 'PixelPlus');
        }

        return $isBot;
    }

    private function logBotDetection($userAgent)
    {
        if ($this->logEnabled && $this->logOthers) {
            PrestaShopLogger::addLog(
                sprintf('[Pixel Plus][Conversions API] BOT Detected: %s. API will not trigger events.', $userAgent),
                1,
                405,
                'PixelPlus'
            );
        }
    }

    public function getIsBot()
    {
        if (!isset($this->isBot)) {
            $this->isBot = $this->setIsBot();
        }

        return $this->isBot;
    }

    /**
     * Sets the User data array or fetches it from the cache
     *
     * @return array User data array
     */
    public function getUserData()
    {
        if (!isset($this->context)) {
            $this->context = Context::getContext();
        }
        if (empty(self::$user_data)) {
            $user_data = [
                'client_ip_address' => $this->ip,
                'client_user_agent' => $this->user_agent,
            ];

            if (isset($this->external_id)) {
                $user_data['external_id'] = $this->getHashData($this->external_id);
            }

            if (isset($this->_fbc) && $this->_fbc != '') {
                $user_data['fbc'] = $this->_fbc;
            }

            if (isset($this->_fbp) && $this->_fbp != '') {
                $user_data['fbp'] = $this->_fbp;
            }
            if ($this->advanced_matching) {
                if (!empty($this->id_customer)) {
                    $user_data = $this->getCustomerData($user_data);
                } else {
                    $ip_data = IpGeolocation::getIpData();
                    // Try to get at least the City from the customer\'s IP to prevent Facebook form creating user_data related messages
                    $user_data['country'] = $this->getHashData($ip_data['country_code']); // WAS City
                }
            }
            // Remove all empty values to prevent diagnostic messages
            if (count($user_data) != count($user_data, COUNT_RECURSIVE)) {
                $user_data = array_map('array_filter', $user_data);
            }
            self::$user_data = $user_data;
        }

        return array_filter(self::$user_data);
    }

    private function getCustomerData($user_data)
    {
        $customer = new Customer((int) $this->id_customer);
        if (isset(Context::getContext()->cart->id_address_delivery) && Context::getContext()->cart->id_address_delivery > 0) {
            $address = new Address((int) Context::getContext()->cart->id_address_delivery);
        } else {
            $address = $this->getCustomerAddress();
            if ($address !== false) {
                $address = new Address((int) $address);
            }
        }

        if (in_array('contact', $this->advanced_matching)) {
            // Add contact data phone and email
            if (Validate::isEmail($customer->email)) {
                $user_data['em'] = $this->getHashData($customer->email);
            }
            if ($address) {
                if (!isset(self::$call_prefix[$address->id_country])) {
                    $this->addCountryCallPrefix($address->id_country);
                }
                // Support for phone mobile and phone numbers, giving always priority to the mobile phone
                if (!empty($address->phone_mobile) && Validate::isPhoneNumber($address->phone_mobile)) {
                    $user_data['ph'] = $this->getHashData($this->formatPhoneNumber($address->phone_mobile, self::$call_prefix[$address->id_country]));
                }
                if (!isset($user_data['ph']) && !empty($address->phone) && Validate::isPhoneNumber($address->phone)) {
                    $user_data['ph'] = $this->getHashData($this->formatPhoneNumber($address->phone, self::$call_prefix[$address->id_country]));
                }
            }
        }

        if (in_array('personal', $this->advanced_matching)) {
            if (!empty($customer->id_gender)) {
                $gender = (int) $customer->id_gender == 2 ? 'f' : 'm';
                $user_data['ge'] = $this->getHashData($gender);
            }
            if (Validate::isGenericName($customer->firstname)) {
                $user_data['fn'] = $this->getHashData($customer->firstname);
                if (Validate::isGenericName($customer->lastname)) {
                    $user_data['ln'] = $this->getHashData($customer->lastname);
                }
            }
            if (Validate::isDate($customer->birthday)) {
                $user_data['db'] = $this->getHashData(date('Ymd', strtotime($customer->birthday)));
            }
        }

        if (($address instanceof Address) && in_array('address', $this->advanced_matching)) {
            // Country::getNameById($address->id_country);
            $user_data['country'] = $this->getHashData(Country::getIsoById($address->id_country));

            if (!empty($address->id_state)) {
                $state = new State($address->id_state);
                if (Validate::isStateIsoCode($state->iso_code)) {
                    $user_data['st'] = $this->getHashData($state->iso_code);
                }
            }

            if (!empty($address->city)) {
                $user_data['ct'] = $this->getHashData(str_replace(' ', '', $address->city));
            }

            if (Validate::isZipCodeFormat($address->postcode)) {
                $user_data['zp'] = $this->getHashData(str_replace('-', '', $address->postcode));
            }
        }

        return $user_data;
    }

    private function addCountryCallPrefix($id_country)
    {
        $prefix = Db::getInstance()->getValue('SELECT call_prefix FROM ' . _DB_PREFIX_ . 'country WHERE id_country = ' . (int) $id_country);
        if (!empty($prefix)) {
            self::$call_prefix[$id_country] = $prefix;
        }
    }

    /**
     * Check if a phone number starts with the prefix
     * It removes any non numneric characters, and it also removes any leading 0 then it checks for the prefix and adds it in case it's missing
     *
     * @param $number
     * @param $prefix
     *
     * @return mixed|string
     */
    private function formatPhoneNumber($number, $prefix)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        // Cast to int to remove any undesired leading 0
        $number = ltrim($number, '0');
        if (PixelTools::strpos($number, $prefix) !== 0) {
            $number = $prefix . $number;
        }

        return $number;
    }

    private function getFacebookCookies()
    {
        $fbCookies = ['_fbp', '_fbc'];

        foreach ($fbCookies as $cookie) {
            if ($this->handleFbclid($cookie)) {
                continue; // Skip further steps if fbclid is successfully handled
            }

            if ($this->retrieveAndValidateCookie($cookie)) {
                continue; // Skip if valid cookie is already set
            }

            // Generate a new cookie as a last resort
            $this->generateFacebookCookie($cookie);
        }
    }

    /**
     * Handle fbclid logic for _fbc cookie.
     *
     * @param string $cookie
     *
     * @return bool returns true if fbclid is valid and processed
     */
    private function handleFbclid(string $cookie): bool
    {
        if ($cookie !== '_fbc' || !Tools::getIsset('fbclid')) {
            return false;
        }

        $fbclid = Tools::getValue('fbclid');
        if (Validate::isLinkRewrite($fbclid)) {
            $this->generateFacebookCookie($cookie);

            return !empty($this->$cookie); // Return true if cookie was successfully set
        }

        return false;
    }

    /**
     * Retrieve and validate an existing cookie.
     *
     * @param string $cookie
     *
     * @return bool returns true if a valid cookie was found and set
     */
    private function retrieveAndValidateCookie(string $cookie): bool
    {
        $rawCookieValue = $_COOKIE[$cookie] ?? null;
        if ($rawCookieValue && $this->validateFacebookCookie($rawCookieValue, $cookie)) {
            $this->setFacebookCookie($cookie, $rawCookieValue);

            return true;
        }

        $prestashopCookieValue = $this->context->cookie->$cookie ?? null;
        if ($prestashopCookieValue && $this->validateFacebookCookie($prestashopCookieValue, $cookie)) {
            $this->setFacebookCookie($cookie, $prestashopCookieValue);

            return true;
        }

        return false;
    }

    /**
     * Validate the format and content of a Facebook cookie.
     *
     * @param string $data
     * @param string $cookie
     *
     * @return bool
     */
    private function validateFacebookCookie(string $data, string $cookie): bool
    {
        $parts = explode('.', $data);

        // Ensure the cookie structure has exactly 4 parts
        if (count($parts) !== 4 || $parts[0] !== 'fb') {
            return false;
        }

        // Validate the version part (2 or 0 allowed)
        $version = (int) $parts[1];
        if ($version < 0 || $version > 2) {
            return false;
        }

        // Validate the timestamp as a valid millisecond timestamp
        if (!$this->isValidMillisecondTimeStamp($parts[2])) {
            return false;
        }

        // Validate the identifier based on cookie type
        if ($cookie === '_fbp' && !is_numeric($parts[3])) {
            return false;
        }

        if ($cookie === '_fbc' && empty($parts[3])) {
            return false;
        }

        return true;
    }

    /**
     * Generate a new Facebook cookie value and store it.
     *
     * @param string $cookie
     */
    private function generateFacebookCookie(string $cookie): void
    {
        $currentTimestamp = round(microtime(true) * 1000);
        $expirationTime = time() + self::FB_COOKIE_EXPIRATION;

        $cookieValue = null;
        if ($cookie === '_fbp') {
            $cookieValue = 'fb.1.' . $currentTimestamp . '.' . Tools::passwdGen(12);
        } elseif ($cookie === '_fbc' && Tools::getIsset('fbclid')) {
            $fbclid = Tools::getValue('fbclid');
            if (Validate::isLinkRewrite($fbclid)) {
                $cookieValue = 'fb.1.' . $currentTimestamp . '.' . $fbclid;
            }
        }

        if ($cookieValue) {
            setcookie($cookie, $cookieValue, $expirationTime, '/');
            $this->setFacebookCookie($cookie, $cookieValue);
        }
    }

    /**
     * Set a Facebook cookie value in the class and PrestaShop context.
     *
     * @param string $cookie
     * @param string $value
     */
    private function setFacebookCookie(string $cookie, string $value): void
    {
        $this->$cookie = $value;
        $this->context->cookie->__set($cookie, $value);
        $this->context->cookie->write(); // Ensure persistence of the cookie
    }

    private function isValidMillisecondTimeStamp($timestamp)
    {
        return is_numeric($timestamp) && $timestamp > 0 && strlen($timestamp) >= 12;
    }

    private function getCustomerAddress()
    {
        return Db::getInstance()->getValue('SELECT id_address FROM ' . _DB_PREFIX_ . 'address WHERE id_customer = ' . (int) $this->id_customer);
    }

    /**
     * Get the current currency, resort to defaults if still hasn't been set
     *
     * @return string the Currency ISO Code
     */
    public function getCurrencyIso()
    {
        if (!isset(self::$currency_iso_code)) {
            $context = Context::getContext();
            if (isset($context->currency)) {
                self::$currency_iso_code = $context->currency->iso_code;
            } elseif (isset($this->context->cookie->id_currency)) {
                $currency = new Currency($this->context->cookie->id_currency);
                self::$currency_iso_code = $currency->iso_code;
            } else {
                $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                self::$currency_iso_code = $currency->iso_code;
            }
        }

        return self::$currency_iso_code;
    }

    public function viewContentTrigger($fb_pixel_event_id, $id_lang, $type, $id, $id_product_attribute = 0, $return = false)
    {
        // $feed_id = $fbObj->getFeedId();
        if (!(int) $id) {
            // Can't keep without an Identifier
            return;
        }
        $currency = $this->getCurrencyIso();
        if ($type === 'product') {
            $event_name = 'ViewContent';
            $content_name = Product::getProductName($id, $id_product_attribute, $id_lang);
            $content_category = $this->module->tryGetBreadcrumb($id);
            $url = Context::getContext()->link->getProductLink($id);
            $combination = '';
            if ($this->combination) {
                if ((int) $id_product_attribute == 0) {
                    $id_product_attribute = Tools::getIsset('id_product_attribute') ? Tools::getValue('id_product_attribute') : Product::getDefaultAttribute($id, 0, true);
                }
                if ($id_product_attribute > 0) {
                    $combination = $this->combination_prefix . $id_product_attribute;
                }
            }
            $content_ids = [$this->product_prefix . $id . $combination];
            $value = Product::getPriceStatic((int) $id, $this->usetax, $id_product_attribute, $this->pp, null, false, true, 1, false, null, null, null);
        } elseif ($type === 'category') {
            $event_name = 'ViewCategory';
            $url = Context::getContext()->link->getCategoryLink((int) $id);
            $content_name = new Category((int) $id);
            $content_name = $content_name->name[$id_lang];
            $content_ids = $this->module->getCategoryProducts((int) $id);
            $value = (int) Configuration::get('FCTP_CATEGORY_VALUE');
        } elseif ($type === 'CMS') {
            $event_name = 'ViewCMS';
            $url = Context::getContext()->link->getCMSLink((int) $id);
            $content_name = new CMS((int) $id);
            $content_name = $content_name->meta_title[$id_lang];
            $content_ids = [$id];
            $value = (int) Configuration::get('FCTP_CMS_VALUE');
        } else {
            // If none of the above, just return
            return;
        }

        $data = [
            'data' => [
                [
                    'event_name' => $event_name,
                    'event_time' => time(),
                    'event_id' => $fb_pixel_event_id,
                    'user_data' => $this->getUserData(),
                    'custom_data' => [
                        'content_name' => $content_name ?? '',
                        'value' => $value ?? 0,
                        'currency' => $currency,
                        'content_ids' => $content_ids ?? [],
                        'content_type' => $type !== 'CMS' ? 'product' : 'article',
                    ],
                ],
            ],
        ];
        if (isset($content_category)) {
            $data['data'][0]['custom_data']['content_category'] = $content_category;
        }
        $feed_id = $this->module->getFeedId();
        if ($feed_id > 0) {
            $data['data'][0]['custom_data']['product_catalog_id'] = $feed_id;
        }

        return $this->sendOrQueueEvent($data, $return);
    }

    public function addToCartTrigger()
    {
        PrestaShopLogger::addLog('[Pixel Plus] Trying Add To Cart...', 1, null, 'PixelPlus');
        $return = ['return' => 'error', 'message' => ''];

        // Check if the delete action is triggered
        if (Tools::getIsset('delete') && (bool) Tools::getValue('delete')) {
            $return['message'] = 'Delete action is triggered.';

            return json_encode($return);
        }
        $ipa = 0;
        $this->context = Context::getContext();
        $cart = $this->context->cart;
        $content_products = [];
        $content_ids = [];
        $content_categories = [];
        $event_id = '';
        $content_name = '';
        $value = 0;
        if (!($this->isTokenValid() || Tools::getIsset('pp_atc_event_id'))) {
            $return['message'] = 'Can\'t proceed with the AddToCartEvent. Invalid token';

            return json_encode($return);
        }
        if (!empty($cart)) {
            if (Tools::getIsset('customAjax') && Tools::getValue('customAjax') == true) {
                // Use presenter if it's available to get the latest product added to the cart
                /*
                $cartPresenter = new PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter();
                $presentedCart = $cartPresenter->present($this->context->cart);
                */
                $cart_products = $this->context->cart->getProducts();
                $product = array_pop($cart_products);
                if (Tools::getIsset('id_product') && (int) Tools::getValue('id_product') > 0) {
                    $id = (int) Tools::getValue('id_product');
                    if (Configuration::get('FCTP_COMBI_' . $this->context->shop->id)) {
                        $ipa = (int) Tools::getValue('id_product_attribute') ? (int) Tools::getValue('id_product_attribute') : 0;
                    }
                } else {
                    $id = isset($product['id_product']) ? (int) $product['id_product'] : 0;
                    if (Configuration::get('FCTP_COMBI_' . $this->context->shop->id)) {
                        $ipa = isset($product['id_product_attribute']) ? (int) $product['id_product_attribute'] : 0;
                    }
                }
                $p = new Product($id);
                $content_id = $this->product_prefix . $p->id . ($ipa > 0 ? $this->combination_prefix . $ipa : '');
                $content_cat = $this->module->tryGetBreadcrumb($p->id);

                $price = Product::getPriceStatic((int) $p->id, $this->usetax, $ipa, $this->pp, null, false, true, 1, false);
                if (method_exists('Context', 'getComputingPrecision')) {
                    $price = Tools::ps_round($price, (int) $this->context->currency->decimals * Context::getContext()->getComputingPrecision());
                }
                $value = $price;
                $quantity = Tools::getIsset('quantity') ? (int) Tools::getValue('quantity') : (int) Tools::getValue('qty');
                if ($quantity == 0) {
                    $quantity = $product['quantity'];
                }
                $content_products = [
                    [
                        'id' => $content_id,
                        'quantity' => $quantity,
                        'category' => $content_cat,
                        'price' => $price,
                    ],
                ];
                $content_ids[] = $content_id;
                $content_categories = $content_cat;
                $content_name = $p->name[$this->context->language->id];
            } else {
                $cart_products = $cart->getProducts();
                if (!empty($cart_products)) {
                    $id_product_attribute = null;
                    if (Tools::getIsset('id_product_attribute')) {
                        // 1.7
                        $id_product_attribute = Tools::getValue('id_product_attribute');
                    } elseif (Tools::getIsset('ipa')) {
                        // 1.6
                        $id_product_attribute = Tools::getValue('ipa');
                    }
                    foreach ($cart_products as $val) {
                        if ($id_product_attribute > 0 && $id_product_attribute != $val['id_product_attribute']) {
                            continue;
                        }
                        if (Tools::getValue('id_product') == (int) $val['id_product']) {
                            $content_id = $this->product_prefix . (int) $val['id_product'];
                            $combination = '';
                            if ((int) $id_product_attribute > 0 && $this->combination) {
                                $combination = $this->combination_prefix . $id_product_attribute;
                            }
                            $content_id = $content_id . $combination;
                            $content_ids[] = $content_id;

                            $content_categories = $this->module->tryGetBreadcrumb((int) $val['id_product']);
                            $content_name = $val['name'];
                            // Get the value if it's a regular call
                            // var_dump(Tools::getIsset('pp_atc_event_id'));
                            if (Tools::getIsset('pp_atc_event_id')) {
                                $event_id = Tools::getValue('pp_atc_event_id');
                            } else {
                                // Generate and save the event_id into a cookie if the call comes from the PrestaShop object
                                $event_id = Tools::passwdGen(12);
                                setcookie('pp_atc_event_id', $event_id, time() + 10, '/');
                            }
                            if (method_exists('Context', 'getComputingPrecision')) {
                                $price = Tools::ps_round($val['price_wt'], (int) $this->context->currency->decimals * Context::getContext()->getComputingPrecision());
                            } else {
                                $price = Product::getPriceStatic((int) $val['id_product'], $this->usetax, $id_product_attribute, $this->pp, null, false, true, 1, false, Context::getContext()->cookie->id_customer);
                            }
                            $data = [
                                'id' => $content_id,
                                'quantity' => (int) $val['cart_quantity'],
                                'price' => $price,
                            ];
                            if (isset($val['category']) && $val['category'] != '') {
                                $data['category'] = $val['category'];
                            }
                            $content_products[] = $data;
                            $value = $price;
                        }
                    }
                }
            }
        } elseif (Tools::getIsset('values')) {
            $params = Tools::getValue('values');
            $id = isset($params['id_product']) ? (int) $params['id_product'] : 0;
            if (Configuration::get('FCTP_COMBI_' . $this->context->shop->id)) {
                $ipa = isset($params['ipa']) ? (int) $params['ipa'] : 0;
            }
            $p = new Product($id);
            $content_id = $this->product_prefix . $p->id . ($ipa > 0 ? $this->combination_prefix . $ipa : '');
            $content_cat = $this->module->tryGetBreadcrumb($p->id);

            $price = Product::getPriceStatic((int) $p->id, $this->usetax, $ipa, $this->pp, null, false, true, 1, false);
            if (method_exists('Context', 'getComputingPrecision')) {
                $price = Tools::ps_round($price, (int) $this->context->currency->decimals * Context::getContext()->getComputingPrecision());
            }
            $value = $price;
            $content_products = [
                [
                    'id' => $content_id,
                    'quantity' => (int) $params['quantity'],
                    'category' => $content_cat,
                    'price' => $price,
                ],
            ];
            $content_ids[] = $content_id;
            $content_categories = $content_cat;
            $content_name = $p->name[$this->context->language->id];
        }
        // If the function reaches this point, it means no products were added to the cart
        if (empty($content_products)) {
            $return['message'] = 'Couldn\'t locate the products from the cart.';

            return json_encode($return);
        }

        if (!$event_id) {
            $event_id = Tools::passwdGen(12);
        }
        $data = [
            'data' => [
                [
                    'event_name' => 'AddToCart',
                    'event_time' => time(),
                    'event_id' => $event_id,
                    'user_data' => $this->getUserData(),
                    'custom_data' => [
                        'contents' => $content_products,
                        'content_ids' => $content_ids,
                        'currency' => $this->getCurrencyIso(),
                        'content_type' => 'product',
                        'content_category' => $content_categories,
                        'value' => $value,
                        'content_name' => $content_name,
                    ],
                ],
            ],
        ];

        $feed_id = $this->module->getFeedId();
        if ($feed_id > 0) {
            $data['data'][0]['custom_data']['product_catalog_id'] = $feed_id;
        }
        if ($this->sendEventToFacebook($data)) {
            return json_encode(array_merge(['return' => 'ok'], $data['data'][0]));
        }
    }

    public function initiateCheckoutTrigger($event_id, $id_cart, $ajax = false)
    {
        $cart = new Cart((int) $id_cart);
        $cart_products = $cart->getProducts();
        if (!empty($cart_products)) {
            $content_products = [];
            $content_ids = [];
            $content_categories = [];
            $num_items = 0;
            $value = 0;
            foreach ($cart_products as $val) {
                $num_items = $num_items + (int) $val['cart_quantity'];
                // content id generate
                $id_product_attribute = (int) $val['id_product_attribute'];
                $content_id = $this->product_prefix . (int) $val['id_product'];
                $combination = '';
                if ((int) $id_product_attribute > 0 && $this->combination) {
                    $combination = $this->combination_prefix . $id_product_attribute;
                }
                $content_id = $content_id . $combination;
                $content_ids[] = $content_id;
                if (!empty($val['category'])) {
                    $content_categories[] = $val['category'];
                }
                $value = $value + $val['total_wt'];
                $p_data = [
                    'id' => $content_id,
                    'quantity' => (int) $val['cart_quantity'],
                    'category' => $val['category'],
                    'price' => $val['total_wt'],
                ];
                $content_products[] = $p_data;
            }
            $value = (Configuration::get('FCTP_START_ORD_VALUE') != '') ? Configuration::get('FCTP_START_ORD_VALUE') : $value;
            $data = [
                'data' => [
                    [
                        'event_name' => 'InitiateCheckout',
                        'event_time' => time(),
                        'event_id' => $event_id,
                        'user_data' => $this->getUserData(),
                        'custom_data' => [
                            'content_type' => 'product',
                            'contents' => $content_products,
                            'content_ids' => $content_ids,
                            'currency' => $this->getCurrencyIso(),
                            'num_items' => (int) $num_items,
                            'content_category' => 'Checkout',
                            'value' => $value,
                        ],
                    ],
                ],
            ];
            $feed_id = FacebookConversionTrackingPlus::getFeedId();
            if ($feed_id > 0) {
                $data['data'][0]['custom_data']['product_catalog_id'] = $feed_id;
            }
            $this->sendEventToFacebook($data);
            if ($ajax) {
                // echo 'SetCookie';
                // External cookie is needed to prevent external cache systems to cache the event_id
                $this->context->cookie->__set('InitiateCheckout', $this->url);
                $data = $data['data'];
                if (isset($data[0])) {
                    $data = $data[0];
                }

                return json_encode($data);
            } else {
                // Cookie to prevent duplicates
                $this->context->cookie->__set('InitiateCheckout', $this->url);
            }

            return true;
        }

        return false;
    }

    public function accountRegisterTrigger($id = '', $id_lang = '', $is_guest = 0)
    {
        $event_id = Tools::passwdGen(12);
        setcookie('pp_register', $event_id, time() + 10, '/');
        $data = [
            'data' => [
                [
                    'event_name' => $is_guest ? 'GuestRegistration' : 'CompleteRegistration',
                    'event_time' => time(),
                    'event_id' => $event_id,
                    'custom_data' => [
                        'content_name' => $is_guest ? 'Registered Guest' : 'Registered Customer',
                        'currency' => $this->getCurrencyIso(),
                        'status' => $is_guest ? 'guest' : 'registered',
                        'value' => Configuration::get('FCTP_REG_VALUE'),
                    ],
                    'user_data' => $this->getUserData(),
                ],
            ],
        ];
        $this->sendEventToFacebook($data);
    }

    public function customizeProductTrigger($dataCat, $eventdid, $return = false)
    {
        if (!isset(Context::getContext()->cookie->$eventdid)) {
            $data = [
                'data' => [
                    [
                        'event_name' => 'CustomizeProduct',
                        'event_time' => time(),
                        'event_id' => $eventdid,
                        'custom_data' => $dataCat,
                        'user_data' => $this->getUserData(),
                    ],
                ],
            ];
            Context::getContext()->cookie->$eventdid = true;

            return $this->sendOrQueueEvent($data, $return);
        }
    }

    public function purchaseEventTrigger($fb_event_purchase_page, $id_order)
    {
        $order = new Order((int) $id_order);
        $cart = new Cart((int) $order->id_cart);
        if (!isset($this->id_customer) || $this->id_customer == 0) {
            $this->id_customer = (int) $order->id_customer;
        }
        $inc_shipping = !Configuration::getGlobalValue('FCTP_PURCHASE_SHIPPING_EXCLUDE');
        $tax = (bool) Configuration::getGlobalValue('FCTP_PURCHASE_TAX');
        if ($tax) {
            $order_value = ($inc_shipping ? $order->total_paid : $order->total_products_wt);
            $shipping_value = ($inc_shipping ? 0 : $order->total_shipping_tax_incl);
        } else {
            $order_value = ($inc_shipping ? $order->total_paid_tax_excl : $order->total_products);
            $shipping_value = ($inc_shipping ? 0 : $order->total_shipping_tax_excl);
        }

        $cart_products = $cart->getProducts();
        $content_products = [];
        $content_ids = [];
        $content_categories = [];
        $total_items = 0;
        foreach ($cart_products as $val) {
            $total_items = $total_items + (int) $val['cart_quantity'];
            // content id generate
            $id_product_attribute = (int) $val['id_product_attribute'];
            $content_id = $this->product_prefix . (int) $val['id_product'];
            $combination = '';

            if ($id_product_attribute > 0 && $this->combination) {
                $combination = $this->combination_prefix . $id_product_attribute;
            }
            $content_id = $content_id . $combination;
            $content_ids[] = $content_id;

            if (!empty($val['category'])) {
                $content_categories[] = $val['category'];
            }

            $data = [
                'id' => $content_id,
                'quantity' => (int) $val['cart_quantity'],
                'category' => $val['category'],
                'price' => $tax ? $val['total_wt'] : $val['total'],
            ];
            $content_products[] = $data;
        }
        $data = [
            'data' => [
                [
                    'event_name' => 'Purchase',
                    'event_time' => time(),
                    'event_id' => $fb_event_purchase_page,
                    'user_data' => $this->getUserData(),
                    'custom_data' => [
                        'content_type' => 'product',
                        'currency' => $this->getCurrencyIso(),
                        'value' => (float) $order_value,
                        'content_ids' => $content_ids,
                        'contents' => $content_products,
                        'num_items' => (int) $total_items,
                        'content_category' => implode(',', $content_categories),
                        'module' => $order->module,
                        'order_id' => (int) $id_order,
                        'order_reference' => $order->reference,
                    ],
                ],
            ],
        ];
        // Add shipping data if it has been disabled on the price total
        if ($shipping_value > 0) {
            $data['data'][0]['custom_data']['shipping'] = (float) $shipping_value;
        }
        if (!Configuration::get('FCTP_COOKIE_CONTROL') || (!isset($_COOKIE['pp_purchaseSent']) || $_COOKIE['pp_purchaseSent'] !== (int) $id_order)) {
            if ($this->sendEventToFacebook($data) == true) {
                if (Configuration::get('FCTP_COOKIE_CONTROL')) {
                    setcookie('pp_purchaseSent', (int) $id_order);
                }

                return true;
            }
        }
    }

    public function searchEventTrigger($search_query = '', $content_ids_list = [], $return = false)
    {
        $fb_pixel_event_search = Tools::passwdGen(12);
        setcookie('pp_pixel_event_id_search', $fb_pixel_event_search, time() + 10, '/');
        $value = Configuration::get('FCTP_SEARCH_VALUE');
        $data = [
            'data' => [
                [
                    'event_name' => 'Search',
                    'event_time' => time(),
                    'event_id' => $fb_pixel_event_search,
                    'user_data' => $this->getUserData(),
                    'custom_data' => [
                        'currency' => $this->getCurrencyIso(),
                        'value' => $value,
                        'search_string' => $search_query,
                        'content_type' => 'product',
                        'content_ids' => $content_ids_list,
                    ],
                ],
            ],
        ];
        $feed_id = FacebookConversionTrackingPlus::getFeedId();
        if ($feed_id > 0) {
            $data['data'][0]['custom_data']['product_catalog_id'] = $feed_id;
        }

        return $this->sendOrQueueEvent($data, $return);
    }

    public function wishlistEventTrigger($id_product, $id_product_attribute, $fb_pixel_wishlist_event_id)
    {
        $content_id = $this->product_prefix . $id_product;
        $combination = '';
        if ((int) $id_product_attribute > 0 && $this->combination) {
            $combination = $this->combination_prefix . $id_product_attribute;
        }
        $content_id = $content_id . $combination;
        $value = Configuration::get('FCTP_WISH_VALUE');
        $data = [
            'data' => [
                [
                    'event_name' => 'AddToWishlist',
                    'event_time' => time(),
                    'event_id' => $fb_pixel_wishlist_event_id,
                    'user_data' => $this->getUserData(),
                    'custom_data' => [
                        'currency' => $this->getCurrencyIso(),
                        'value' => $value,
                        'content_ids' => [$content_id],
                        'content_type' => 'product',
                    ],
                ],
            ],
        ];

        return $this->sendEventToFacebook($data, true);
    }

    public function pageViewTrigger($ev_id, $controller, $from_ajax = false)
    {
        $allowed_controllers = ['index', 'product', 'category', 'cart', 'order', 'order-opc', 'checkout', 'supercheckout', 'default', 'contact', 'cms'];
        if (in_array($controller, $allowed_controllers) || $from_ajax) {
            $data = [
                'data' => [
                    [
                        'event_name' => 'PageView',
                        'event_time' => time(),
                        'event_id' => $ev_id,
                        'user_data' => $this->getUserData(),
                    ],
                ],
            ];

            return $this->sendOrQueueEvent($data, $from_ajax);
        } else {
            if ($this->logEnabled && $this->logOthers) {
                PrestaShopLogger::addLog('[Pixel Plus] PaveView Event skipped for controller: ' . $controller . '. Not one of the default controllers', 1, null, 'PixelPlus');
            }
        }
    }

    /**
     * @return bool
     */
    public function contactTrigger($subject = '')
    {
        $fb_pixel_event_contact = Tools::passwdGen(12);
        $data = [
            'data' => [
                [
                    'event_name' => 'Contact',
                    'event_time' => time(),
                    'event_id' => $fb_pixel_event_contact,
                    'user_data' => $this->getUserData(),
                    'custom_data' => [
                        'currency' => $this->getCurrencyIso(),
                        'value' => Configuration::get('FCTP_CONTACT_US_VALUE'),
                        'subject' => $subject,
                    ],
                ],
            ],
        ];

        if ($this->sendEventToFacebook($data) == true) {
            return true;
        }

        return false;
    }

    /**
     * @param email
     *
     * @return bool
     *              11-01-2022
     */
    public function newsletterTrigger($email = '')
    {
        $fb_pixel_event_newsletter = Tools::passwdGen(12);

        $data = [
            'data' => [
                [
                    'event_name' => 'Newsletter',
                    'event_time' => time(),
                    'event_id' => $fb_pixel_event_newsletter,
                    'user_data' => $this->getUserData(),
                    'custom_data' => [
                        'currency' => $this->getCurrencyIso(),
                        'value' => Configuration::get('FCTP_NEWSLETTER_VALUE'),
                    ],
                ],
            ],
        ];

        if ($this->sendEventToFacebook($data) == true) {
            return true;
        }

        return false;
    }

    /**
     * @param value
     * Added 13 Jan
     */
    public function addPaymentInfoTrigger($value = '')
    {
        $event_id = Tools::passwdGen(12);

        $data = [
            'data' => [
                [
                    'event_name' => 'AddPaymentInfo',
                    'event_time' => time(),
                    'event_id' => $event_id,
                    'user_data' => $this->getUserData(),
                    'custom_data' => [
                        'currency' => $this->getCurrencyIso(),
                        'value' => $value,
                    ],
                ],
            ],
        ];

        if ($this->sendEventToFacebook($data) == true) {
            return json_encode(['return' => 'ok', 'event_id' => $event_id]);
        }

        return false;
    }

    /**
     * @param time interval
     * New Addition HR
     */
    public function pageTimeEvent($time = 0)
    {
        $fb_pixel_event_page_time = Tools::passwdGen(12);
        $value = Configuration::get('FCTP_PAGETIME_VALUE');

        $tmp_time = str_replace('+', '', $time);
        if (!(is_numeric($tmp_time) && $tmp_time > 0)) {
            return false;
        }
        // time already sent through ajax (even the +120)
        // $time = $time == 120 ? '+'.$time : $time;

        $data = [
            'data' => [
                [
                    'event_name' => 'Time' . $time . 's',
                    'event_time' => time(),
                    'event_id' => $fb_pixel_event_page_time,
                    'user_data' => $this->getUserData(),
                    'custom_data' => [
                        'currency' => $this->getCurrencyIso(),
                        'value' => $value,
                        'time' => $time . 's',
                    ],
                ],
            ],
        ];
        setcookie('pp_pixel_event_id', $fb_pixel_event_page_time, time() + 5, '/');
        if ($this->sendEventToFacebook($data) == true) {
            return json_encode(['return' => 'ok', 'event_id' => $fb_pixel_event_page_time]);
        }

        return false;
    }

    /**
     * @return json
     *              Added HR
     */
    public function pageViewCountEvent()
    {
        $days = (int) Configuration::get('FCTP_PAGEVIEW_COUNT_COOKIE_DAYS') == 0 ? 7 : (int) Configuration::get('FCTP_PAGEVIEW_COUNT_COOKIE_DAYS');
        $pageCountDays = time() + ($days * 86400);
        $cookieObj = new Cookie('pageviewcountevent', '', $pageCountDays);

        // Set page count cookie
        $pagecount = $cookieObj->__isset('pageviewcountevent') ? $cookieObj->__get('pageviewcountevent') + 1 : 1;
        $cookieObj->__set('pageviewcountevent', $pagecount);
        $cookieObj->write();
        if ($pagecount == 5 || $pagecount == 10 || $pagecount == 15 || $pagecount == 20) {
            $eventName = $pagecount == 20 ? 'PagesViewedMore' . $pagecount : 'PagesViewed' . $pagecount;
            $pageview_event_id = Tools::passwdGen(12);
            $data = [
                'data' => [
                    [
                        'event_name' => $eventName,
                        'event_time' => time(),
                        'event_id' => $pageview_event_id,
                        'user_data' => $this->getUserData(),
                    ],
                ],
            ];
            if ($this->sendEventToFacebook($data) == true) {
                $result = ['return' => 'ok', 'current_page' => $pagecount];

                return json_encode($result);
            }
        }

        return true;
    }

    public function discountEvent($discount_coupon = '')
    {
        $discount_coupon_event_id = Tools::passwdGen(12);
        $discount_event_value = Configuration::get('FCTP_DISCOUNT_VALUE');

        $cartRuleDetails = CartRule::getCartsRuleByCode($discount_coupon, $this->context->language->id);
        if (!$cartRuleDetails) {
            return false;
        }
        $params = [
            'currency' => $this->getCurrencyIso(),
            'value' => $discount_event_value,
            'coupon_name' => $cartRuleDetails[0]['name'],
            'free_shipping' => $cartRuleDetails[0]['free_shipping'] > 0 ? $cartRuleDetails[0]['free_shipping'] : 0,
            'limited' => $cartRuleDetails[0]['id_customer'] > 0 ? 1 : 0,
        ];

        if ($cartRuleDetails[0]['reduction_percent'] > 0) {
            $params['discount_percentage'] = $cartRuleDetails[0]['reduction_percent'];
        } else {
            $params['discount_amount'] = $cartRuleDetails[0]['reduction_amount'];
        }

        $data = [
            'data' => [
                [
                    'event_name' => 'Discount',
                    'event_time' => time(),
                    'event_id' => $discount_coupon_event_id,
                    'user_data' => $this->getUserData(),
                    'custom_data' => $params,
                ],
            ],
        ];

        if ($this->sendEventToFacebook($data) == true) {
            $result = ['return' => 'ok', 'params' => $params];

            return json_encode($result);
        }

        return false;
    }

    /**
     * Send event data to Facebook using curl
     * curl_multi used for async call
     *
     * @param data|array
     */
    private function sendEventToFacebook($data, $json_return = false)
    {
        if (!$this->active || !Configuration::get('FCTP_CONVERSION_API')) {
            return false;
        }
        if (!isset(self::$consent)) {
            self::$consent = PixelTools::getConsent();
        }
        if (!self::$consent) {
            if ($this->logEnabled && $this->logOthers) {
                PrestaShopLogger::addLog('[Pixel Plus][Conversions API] Consent check failed, conversion is not triggered.', 1, null, 'PixelPlus');
            }

            return false;
        }
        if (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            foreach ($this->pixels_ids as $inc => $pixel_id) {
                // Only perform the array_values if is a single event call
                if (!isset($data['data'][1])) {
                    $data['data'] = array_values($data['data']);
                }
                $data = $this->addCommonDataToEvent($data, $inc);
                $url = 'https://graph.facebook.com/v21.0/' . pSQL($pixel_id['id']) . '/events?access_token=' . pSQL($pixel_id['token']);
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => $this->getCurlTimeout(), // Was 30
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                    ],
                ]);
                // Condition added for multi curl async call
                if (function_exists('curl_multi_init')
                    && function_exists('curl_multi_exec') && !Configuration::get('FCTP_DISABLE_CURL_MULTI')) {
                    // !in_array('curl_multi_init', explode(',', ini_get('disable_functions')))) {
                    $mh = curl_multi_init();
                    curl_multi_add_handle($mh, $curl);

                    // execute all queries simultaneously, and continue when all are complete
                    $running = null;
                    do {
                        $status = curl_multi_exec($mh, $running);
                    } while ($running && $status == 'CURLM_OK');
                    // close the handles
                    curl_multi_remove_handle($mh, $curl);
                    curl_multi_close($mh);

                    // all of our requests are done, we can now access the results
                    $ret = curl_multi_getcontent($curl);
                } else {
                    if (!$this->logEnabled) {
                        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
                        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
                        if (defined('CURLOPT_NOSIGNAL')) {
                            curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
                        }

                        if (defined('CURLOPT_RETURNTRANSFER')) {
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        }
                    }

                    $ret = curl_exec($curl);
                    curl_close($curl);
                }
                if ($this->logEnabled) {
                    $formatResponses = $this->formatResponses($ret, $data, $pixel_id['id']);
                    if ($ret === false || isset($formatResponses['error'])) {
                        if ($this->logEvents && $this->logIssues) {
                            $response = $formatResponses['error'] ? $formatResponses['message'] : 'CURL Error: ' . curl_errno($curl) . ' - ' . curl_error($curl);
                            $this->addResponsesToLog($response, 403);
                        }
                    } else {
                        if ($this->logPayload) {
                            PrestaShopLogger::addLog(json_encode(['Event Payload', $data]), 1, 200, 'PixelPlus', 1);
                        }
                        if ($this->logEvents) {
                            $this->addResponsesToLog($formatResponses, 200);
                        }
                    }
                }
            }
            if ($json_return) {
                $data = isset($data['data'][0]) ? $data['data'][0] : $data['data'];

                return json_encode($data);
            }

            return true;
        } else {
            if ($this->logEnabled && $this->logOthers) {
                PrestaShopLogger::addLog('[Pixel Plus][Conversion API] Invalid IP found (' . $this->ip . '). Event not sent to FB:' . $this->formatResponses('{}', $data, 0), 1, 500, 'PixelPlus');
            }
        }

        return false;
    }

    private function getCurlTimeout()
    {
        if (!isset(self::$curl_timeout)) {
            self::$curl_timeout = Configuration::get('FCTP_DISABLE_SHORT_TIMEOUT') ? 30 : 2;
        }

        return self::$curl_timeout;
    }

    public function formatResponses($response, $data, $pixel_id)
    {
        $return = [];
        if (!is_array($response)) {
            $response = json_decode($response, true);
        }
        //        Tools::dieObject($data);
        $event_count = isset($response['events_received']) ? $response['events_received'] : 1;
        // $event_count = count($data['data']) >= $response['events_received'] ? count($data['data']) : $response['events_received'];
        for ($i = 0; $i < $event_count; ++$i) {
            $output = [];
            if ($event_count > 1) {
                $output[] = '[Pixel plus][Conversion API][Event Set - ' . ($i + 1) . ' of ' . $event_count . '] ';
            }
            $output[] = '[Pixel ID: ' . $pixel_id . '] ';
            $output[] = '[Event: ' . $data['data'][$i]['event_name'] . '] ';
            $output[] = '[EV-ID - ' . $data['data'][$i]['event_id'] . '] ';
            $output[] = '[External ID - ' . (isset($this->external_id) ? $this->external_id : 'Not Set') . ']';
            $output[] = '[Client IP - ' . $data['data'][$i]['user_data']['client_ip_address'] . ']';
            $output[] = '[Client IP (hashed) - ' . hash('sha256', $data['data'][$i]['user_data']['client_ip_address']) . ']';
            $output[] = '[Event URL - ' . $this->url . '] ';
            if (!empty($response)) {
                foreach ($response as $key => $resp) {
                    if (!is_array($resp)) {
                        $output[] = '[' . $key . '=>' . $resp . ']';
                    } else {
                        $output[] = '[' . $key . '=>' . implode(' ', $resp) . ']';
                    }
                }
            } else {
                if (function_exists('curl_multi_init') && !Configuration::get('FCTP_DISABLE_CURL_MULTI')) {
                    $output[] = '[Empty Facebook Response: The Multi curl feature may not be reaching Facebook, try to disable the Multi Curl from the Conversion API settings and try again] ';
                } else {
                    $output[] = '[Empty Facebook Response] ';
                }
            }
            if (isset($response['error'])) {
                $return['error'] = true;
            }
            $return['message'] = implode(' ', $output);
        }

        return $return;
    }

    private function addResponsesToLog($responses, $code)
    {
        if (!is_array($responses)) {
            $responses = [$responses];
        }
        foreach ($responses as $response) {
            PrestaShopLogger::addLog($response, 1, $code, 'PixelPlus', 1);
        }
    }

    private function setNullTestCode($configName)
    {
        $Maxminute = 1440;
        $data = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'configuration WHERE name = \'' . pSQL($configName) . '\'');
        $from_time = strtotime($data['date_upd']);
        $to_time = strtotime(date('Y-m-d H:i:s'));
        $min = round(abs($to_time - $from_time) / 60, 2);
        if ($min > $Maxminute) {
            Configuration::updateValue($configName, '');
        }
    }

    private function addCommonDataToEvent($data, $inc = 1)
    {
        $testCode = Configuration::get('FCTP_CONVERSION_API_TEST_' . $inc);
        if ($testCode != '' && $this->testCodeEnabled) {
            $this->setNullTestCode('FCTP_CONVERSION_API_TEST_' . $inc);
            $data['test_event_code'] = $testCode;
        }
        $datalen = count($data['data']);
        for ($i = 0; $i < $datalen; ++$i) {
            if ($this->_fbp) {
                $data['data'][$i]['user_data']['fbp'] = $this->_fbp;
            }
            if ($this->_fbc) {
                $data['data'][$i]['user_data']['fbc'] = $this->_fbc;
            }
            // Add the website event to all $data events
            $data['data'][$i]['action_source'] = 'website';

            if (Tools::getIsset('source_url')) {
                $this->url = Tools::getValue('source_url');
            } else {
                $this->url = $this->context->shop->getBaseURL(true, false) . $_SERVER['REQUEST_URI'];
            }
            $data['data'][$i]['event_source_url'] = urldecode($this->url);
            $data['data'][$i]['action_source'] = 'website';
        }

        return $data;
    }

    public function getHashData($val, $prefix = '')
    {
        $pre = !empty($prefix) ? $prefix . '_' : '';
        $val = trim($this->toLower($val));

        return hash('sha256', $pre . $val);
    }

    private function toLower($string)
    {
        if (function_exists('mb_strtolower')) {
            return mb_strtolower($string);
        } else {
            return strtolower($string);
        }
    }

    private function cleanUrlSensitiveParameters()
    {
        // Ensure the URL exists before attempting to clean it
        if (empty($this->url)) {
            return;
        }

        // Define sensitive data parameters to be removed (already lowercase)
        $sensitiveKeys = ['name', 'firstname', 'lastname', 'email', 'phone', 'tel', 'address', 'ip', 'cvc', 's'];
        $urlParts = explode('?', $this->url); // Split URL into base and query

        $queryUrl = [];
        if (count($urlParts) > 1) {
            // Parse the query string into an associative array
            parse_str($urlParts[1], $queryUrl);

            if (!empty($queryUrl) && is_array($queryUrl)) {
                // Loop through each query parameter to compare against sensitive keys
                foreach ($queryUrl as $key => $value) {
                    // Compare the key in a case-insensitive manner by lowercasing it
                    if (in_array(strtolower($key), $sensitiveKeys)) {
                        unset($queryUrl[$key]);  // Remove sensitive parameters
                    }
                }

                // Rebuild the cleaned URL
                $this->url = $urlParts[0] . '?' . http_build_query($queryUrl);
            }
        }
    }

    private function isTokenValid()
    {
        $hash_func = method_exists('Tools', 'hash') ? 'hash' : 'encrypt';
        // Validate Token
        $tokens = ['token', 'static_token'];
        foreach ($tokens as $token) {
            if (!Tools::getIsset($token)) {
                continue;
            }
            if (Tools::getValue($token) == Tools::getToken()
                || Tools::getValue($token) == Tools::getToken(false)) {
                return true;
            }
        }
        $context = Context::getContext();
        // PageCache creates a fictitious user with some random data, the custom password is necessary to validate the token.
        $cases = [];
        if (Module::isEnabled('pagecache') || Module::isEnabled('jprestaspeedpack')) {
            if ($context->customer->id == 0) {
                $customers = $this->getCustomersByName('fake-user-for-pagecache');
                if ($customers === false) {
                    echo Db::getInstance()->getMsgError();
                }
                foreach ($customers as $c) {
                    $cases[] = ['id' => $c['id_customer'], 'passwd' => $c['passwd']];
                }
            } else {
                $cases[] = ['id' => $context->customer->id, 'passwd' => $context->customer->password ?? ''];
            }
            $cases[] = ['id' => '', 'passwd' => 'WhateverSinceItIsInactive0_'];
        }
        foreach ($cases as $case) {
            // echo Tools::getValue('token').'<br>'.Tools::hash($context->customer->id . $case . $_SERVER['SCRIPT_NAME']);
            if ((Tools::getValue('token') == Tools::$hash_func($case['id'] . $case['passwd'] . $_SERVER['SCRIPT_NAME']))
                || (Tools::getValue('token') == Tools::$hash_func($case['id'] . $case['passwd'] . false))) {
                return true;
            }
        }

        //         // Extensive test for token, must check also the Tools function directly to compare ajax and on page calls
        //         // Update the ID with the product ID to test
        //         if (Tools::getValue('id_product') == '2608') {
        //             echo 'Token: '.Tools::getValue('token')."<br>\n";
        //             echo 'CID: '.$context->customer->id."<br>\n";
        //             echo 'PASS: '.$context->customer->password."<br>\n";
        //             echo 'Case ID: '.$case['id']."<br>\n";
        //             echo 'SN: '.$_SERVER['SCRIPT_NAME']."<br>\n";
        //             echo 'Token1: '.Tools::getToken(false)."<br>\n";
        //             echo 'Token2: '.Tools::getToken()."<br>\n";
        //             echo str_repeat('-', 50)."<br>\n<br>\n";
        //             echo 'Token3: '.Tools::hash($case['id'] . $case['passwd'] . $_SERVER['SCRIPT_NAME'])."<br>\n";
        //             echo 'Token4: '.Tools::hash($case['id'] . $case['passwd'] . false)."<br>\n";
        //
        //        }
        return false;
    }

    /*
     * Get all the fake users from pageCache module and test them all to know if any is being used.
     */
    private function getCustomersByName($cname)
    {
        return Db::getInstance()->executeS('SELECT id_customer, passwd FROM ' . _DB_PREFIX_ . 'customer WHERE firstname LIKE "' . pSQL($cname) . '" ORDER by id_customer ASC');
    }

    /**
     * Get the list of the Pixel Ids configured for the module in the API
     *
     * @return array the pixel IDs
     */
    public function getPixelsIds()
    {
        return $this->pixels_ids;
    }

    public function getActive()
    {
        return $this->active;
    }

    /** Add the event to the queue or directly send it if the return is needed */
    private function sendOrQueueEvent($data, $return)
    {
        if (!$return) {
            $this->addEventToQueue($data);
        } else {
            if ($this->sendEventToFacebook($data, true)) {
                return json_encode(array_merge(['return' => 'ok'], $data['data'][0]));
            }
        }
    }

    /** Adds the event to the queue */
    private function addEventToQueue($data)
    {
        $this->events[] = $data;
    }

    /** Prepare the $data variable to be able to send multiple events at once */
    public function sendQueuedEvents()
    {
        if (count($this->events) == 0) {
            return;
        }
        $data = [];
        foreach ($this->events as $event_data) {
            $event_data['data'] = array_values($event_data['data']);
            $data['data'][] = $event_data['data'][0];
        }
        if ($this->sendEventToFacebook($data) === false) {
            echo 'Error while sending the queued events';
        }
    }

    /** Get the log events flag variable */
    public function getLogEvents()
    {
        if (!isset($this->logEvents)) {
            $this->setLogging();
        }

        return $this->logEvents;
    }

    /**
     * @return void
     */
    public function setLogging()
    {
        // Retrieve main log configuration
        $this->logEvents = (bool) Configuration::get('FCTP_CONVERSION_LOG');

        // Determine if any specific log configurations are enabled
        $logConfigurations = Configuration::getMultiple(['FCTP_CONVERSION_IP_LOG', 'FCTP_CONVERSION_LOG_OTHER', 'FCTP_CONVERSION_PAYLOAD']);
        if ($this->logEvents || count(array_filter($logConfigurations)) > 0) {
            // Retrieve and filter allowed IPs for logging
            $allowed_ips = array_filter(explode(',', Configuration::get('FCTP_CONVERSION_IP_LOG')));

            // Enable logging if no IPs are specified or if the current IP matches any allowed IPs
            if (empty($allowed_ips) || count(array_intersect($this->all_ips, $allowed_ips)) > 0) {
                $this->logEnabled = true;
            }
        }
        // Issues and abort Logging
        $this->logIssues = (bool) Configuration::get('FCTP_CONVERSION_LOG_ISSUES');
        // Enable logging for other types if configured
        $this->logOthers = (bool) Configuration::get('FCTP_CONVERSION_LOG_OTHER');
    }

    public function sendQueuedEventsOnConsent($event_data)
    {
        // TODO
    }
}
