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

class CAPICookieHelper
{
    private static $name = 'pending_consent_events';
    private static $path = '/';
    private static $secure = true;

    /**
     * Set a cookie with a specific key, value, and expiration time.
     *
     * @param string $key
     * @param string $value
     * @param int $expire Expiration time in seconds (0 for session-only cookies)
     */
    public static function setCookie($key, $value, $expire = 0)
    {
        $cookie = new Cookie(self::$name, self::$path, $expire, null, false, self::$secure);
        $cookie->__set($key, $value);
        $cookie->write();
    }

    /**
     * Retrieve a cookie value by key.
     *
     * @param string $key
     *
     * @return string|null the cookie value or null if not found
     */
    public static function getCookie($key)
    {
        $cookie = new Cookie(self::$name, self::$path);

        return $cookie->__get($key);
    }

    /**
     * Delete a cookie by key.
     *
     * @param string $key
     */
    public static function deleteCookie($key)
    {
        $cookie = new Cookie(self::$name, self::$path);
        unset($cookie->$key);
        $cookie->write();
    }
}
