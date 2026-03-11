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

class PixelTools
{
    const IPV6_DOMAIN_CHECK = 'https://ipv6.smart-modules.com/';
    private static $consent;

    public static function isJson($string)
    {
        if (method_exists('Validate', 'isJson')) {
            return Validate::isJson($string);
        } else {
            json_decode($string);

            return json_last_error() == JSON_ERROR_NONE;
        }
    }

    public static function strpos($haystack, $needle, $offset = 0)
    {
        if (method_exists('Tools', 'strpos')) {
            return Tools::strpos($haystack, $needle, $offset);
        } else {
            return strpos($haystack, $needle, $offset);
        }
    }

    /**
     * Get formatted date.
     *
     * @param string $date_str Date string
     * @param bool $full With time or not (optional)
     *
     * @return string Formatted date
     */
    public static function formatDateStr($date_str, $full = false)
    {
        $time = strtotime($date_str);
        $context = Context::getContext();
        $date_format = ($full ? $context->language->date_format_full : $context->language->date_format_lite);
        $date = date($date_format, $time);

        return $date;
    }

    /**
     * Maps a IPv4 address to IPv6 using the standard procedures
     *
     * @param $ipv4
     *
     * @return false|string returns false or the IPv6 address
     */
    public static function convertIPv4ToIPv6($ipv4)
    {
        if (filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            // Convert the IPv4 address to its IPv6 format
            return '::ffff:' . $ipv4;
        // Alternative Method
        /*
        $ipv4Octets = explode('.', $ipv4);

        // Convert each octet to its hexadecimal equivalent
        $hexOctets = array_map(function ($octet) {
            return str_pad(dechex($octet), 2, '0', STR_PAD_LEFT);
        }, $ipv4Octets);

        // Format the IPv6 address using the IPv4-mapped notation
        $ipv6 = '::ffff:' . implode(':', $hexOctets);
        */
        } else {
            return false; // Not a valid IPv4 address
        }
    }

    /**
     * Get the visitor IP. Store it in a session cookie if it's an IPv6 address
     * Get multiple values if $multi is set to have better coverage of proxy connections, like CloudFlare
     *
     * @return bool|string
     */
    public static function getRemoteAddr($multi = false)
    {
        // Create or Initialize the Cookie
        // Expires on session end
        $ip_cookie = new Cookie('pp_ip_data', '', 0);

        // Initialize an empty array for valid IPs
        $valid_ips = [];

        // Check if the cookie is set and contains a valid IPv6 address
        if ($ip_cookie->__isset('ipv6') && filter_var($ip_cookie->ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $valid_ips[] = $ip_cookie->ipv6;
        } else {
            // Check if the cookie has a cached IPv4 array and if it is valid
            if ($ip_cookie->__isset('ipv4_array')) {
                $cached_ips = json_decode($ip_cookie->ipv4_array, true);
                if (is_array($cached_ips)) {
                    foreach ($cached_ips as $ip) {
                        if (filter_var($ip, FILTER_VALIDATE_IP)) {
                            $valid_ips[] = $ip;
                        }
                    }
                }
            }

            // If no valid IPs from cache, get the visitor's IP address(es)
            if (empty($valid_ips)) {
                $ips = IpGeolocation::getVisitorIp($multi);

                // Handle IP type configuration
                if (in_array(Configuration::get('PP_IP_TYPE'), ['try', 'force'])) {
                    // Iterate through the IPs and filter them
                    foreach ($ips as $ip) {
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
                            $ip2 = self::tryToGetIPV6([$ip]);
                            if ($ip2 !== false) {
                                $valid_ips[] = $ip2[0];
                            }
                        } else {
                            $valid_ips[] = $ip;
                        }
                    }
                } else {
                    // If no IP type configuration, just filter the IPs for validity
                    foreach ($ips as $ip) {
                        if (filter_var($ip, FILTER_VALIDATE_IP)) {
                            $valid_ips[] = $ip;
                        }
                    }
                }

                // Cache the IPv4 array if no valid IPv6 address is found
                if (empty($valid_ips) && !empty($ips)) {
                    $ip_cookie->__set('ipv4_array', json_encode($ips));
                }
            }

            // If a valid IPv6 address is found, set it in the cookie
            if (!empty($valid_ips) && filter_var($valid_ips[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
                $ip_cookie->__set('ipv6', $valid_ips[0]);
            }
        }

        // If $multi is true, return all valid IPs, otherwise return the first one
        if ($multi) {
            return $valid_ips;
        } else {
            return !empty($valid_ips) ? $valid_ips[0] : null;
        }
    }

    /**
     * Try to get the IPv6 address from a IPV6 spefic domain by performing a simple call
     * If the returned IP is empty it's not a IPV6.
     * Force the use of IPV6 generation if the configuration is set
     *
     * @param string The original IP
     *
     * @return string|bool The returning IP or false if the IP can't be fetched in IPV6 format
     */
    private static function tryToGetIPV6($ip)
    {
        $stream_context = @stream_context_create(['http' => ['timeout' => 0.1]]);
        $ip2 = @Tools::file_get_contents(self::IPV6_DOMAIN_CHECK, false, $stream_context);

        if (!empty($ip2)) {
            return $ip2;
        }
        if ((filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) && Configuration::get('PP_IP_TYPE') == 'force') {
            return self::convertIPv4ToIPv6($ip);
        }

        return false;
    }

    private static function insertItemsIntoArray($array, $items, $position)
    {
        if ($position < 0) {
            // If the position is negative, adjust it to insert from the end of the array
            $position = count($array) + $position + 1;
        }

        if ($position < 0 || $position > count($array)) {
            // Position is out of bounds
            return false;
        }

        array_splice($array, $position, 0, $items);

        return $array;
    }

    public static function insertItems($originalArray, $itemsToInsert, $positionToInsert)
    {
        return self::insertItemsIntoArray($originalArray, $itemsToInsert, $positionToInsert);
    }

    public static function getConfigurationAge($configuration_name)
    {
        if (empty($configuration_name)) {
            return false;
        }

        // Query to get the `date_upd` field for the given configuration name.
        $query = 'SELECT date_upd FROM ' . _DB_PREFIX_ . 'configuration WHERE name = \'' . pSQL($configuration_name) . '\'';
        $dates = Db::getInstance()->executeS($query);

        if (empty($dates)) {
            return false;
        }

        $current_time = time();
        $min_date_diff = PHP_INT_MAX; // Start with the maximum possible integer.

        // Calculate the smallest date difference.
        foreach ($dates as $date) {
            $date_diff = $current_time - strtotime($date['date_upd']);
            if ($date_diff < $min_date_diff) {
                $min_date_diff = $date_diff;
            }
        }

        return $min_date_diff;
    }

    /**
     * Get the consent status
     *
     * @return bool The GDPR Consent
     */
    public static function getConsent()
    {
        $context = Context::getContext();
        if (!isset(self::$consent)) {
            $consent = true;
            $context->smarty->assign(
                [
                    'consent_check' => Configuration::get('FCTP_BLOCK_SCRIPT'),
                    'consent_mode_check_cookies' => Configuration::get('FCTP_BLOCK_SCRIPT_MODE') == 'cookies',
                    'cookie_reload' => (int) Configuration::get('FCTP_COOKIE_RELOAD'),
                    'cookie_check_button' => Configuration::get('FCTP_COOKIE_BUTTON'),
                ]
            );
            if (Configuration::get('FCTP_BLOCK_SCRIPT')) {
                $consent = false;
                if (Configuration::get('FCTP_BLOCK_SCRIPT_MODE') == 'local_storage') {
                    $consent = self::checkLocalStorage();
                    if (!$consent) {
                        self::checkLocalStorageData();
                    }
                } else {
                    $cookie = Configuration::get('FCTP_COOKIE_NAME');
                    if ($cookie != '') {
                        $value = Configuration::get('FCTP_COOKIE_VALUE');
                        $consent = self::checkCookies($cookie, $value);
                    }
                }
            }
            self::$consent = $consent;
        }

        return self::$consent;
    }

    private static function checkLocalStorageData()
    {
        $lsv = Configuration::get('FCTP_LOCAL_STORAGE_VAR_PATH');
        if ($lsv !== '') {
            $lsv = array_map('trim', explode('>>', $lsv));
            Context::getContext()->smarty->assign(
                'pp_local_storage_data',
                [
                    'var' => trim(array_shift($lsv)), // Moved down or it will remove the $lsv value
                    'values' => trim(json_encode($lsv)),
                    'last_value' => Configuration::get('FCTP_LOCAL_STORAGE_VALUE'),
                ]
            );
        }
    }

    /**
     * Checks if the permission has been granted. If the cookie value has three pipes it checks the additional possible values
     *
     * @param $cookie string the cookie name
     * @param $value string the value or the array of values to look for
     *
     * @return bool if the permission has been granted
     */
    public static function checkCookies($cookie, $value)
    {
        $context = Context::getContext();
        $value = explode('|||', $value);
        foreach ($value as $val) {
            if (Configuration::get('FCTP_COOKIE_EXTERNAL')) {
                if (isset($_COOKIE[$cookie])) {
                    if ($val != '') {
                        if (PixelTools::strpos($_COOKIE[$cookie], $val) !== false
                            || PixelTools::strpos(urlencode($_COOKIE[$cookie]), $val) !== false) {
                            return true;
                        }
                    } else {
                        return true;
                    }
                }
            } else {
                //                $cookie_value = '';
                if ($cookie == 'lgcookieslaw' && Module::isEnabled('lgcookieslaw')) {
                    $lgcookieslaw_module = Module::getInstanceByName('lgcookieslaw');
                    $cookie_value = json_encode($lgcookieslaw_module->getCookieValues());
                } else {
                    $cookie_value = $context->cookie->__get($cookie);
                }
                if (PixelTools::strpos($cookie_value, $val) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check the Internal Storage to validate the
     */
    private static function checkLocalStorage()
    {
        $context = Context::getContext();

        // Search the local storage consent cookie
        return $context->cookie->__isset('fctp_localstorage_consent')
            && $context->cookie->fctp_localstorage_consent;
    }

    public static function hash(string $string)
    {
        if (method_exists('Tools', 'hash')) {
            return Tools::hash($string);
        }

        // Use encrypt for older PS versions
        return Tools::encrypt($string);
    }

    public static function getCustomModuleForViewContent()
    {
        if (Module::isEnabled('dynamicproduct')) {
            return 'dynamicproduct';
        }

        return false;
    }
}
