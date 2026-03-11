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

class IpGeolocation
{
    public static function getIpData()
    {
        $context = Context::getContext();
        $ip = self::getVisitorIp();
        $data = [];
        if (isset($context->cookie->fpf_country) && !empty($context->cookie->fpf_country)) {
            $data = json_decode($context->cookie->fpf_country, true);
        }
        if (empty($data) || empty($data['country_code']) || $data['ip'] !== $ip) {
            $data = self::getGeolocationData($ip);
            $context->cookie->fpf_country = json_encode([
                'ip' => $ip,
                'country_code' => Tools::strtolower($data['country_code']),
            ]);
        }

        return $data;
    }

    /**
     * Get the current user IP
     * If multi flag is set, return an array of IPs otherwise return the first IP detected
     *
     * @return string returns the IP of the visitor
     */
    public static function getVisitorIp($multi = false)
    {
        $keys_to_check = ['HTTP_CLIENT_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            if (!$multi) {
                $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
                $_SERVER['HTTP_CLIENT_IP'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
            } else {
                $keys_to_check[] = 'HTTP_CF_CONNECTING_IP';
            }
        }

        $ret = [];
        foreach ($keys_to_check as $key) {
            if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
                if (PixelTools::strpos($_SERVER[$key], ',') !== false) {
                    $ret += explode(',', $_SERVER[$key]);
                } else {
                    $ret[] = $_SERVER[$key];
                }
            }
        }
        foreach ($ret as $key => $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                unset($ret[$key]);
            }
        }
        $ret = array_unique($ret);
        if (!$multi) {
            return reset($ret);
        }

        return array_values($ret);
    }

    private static function getGeolocationData($ip)
    {
        // Limit the time to fetch the IP data
        $stream_context = @stream_context_create(
            [
                'http' => ['timeout' => 0.08],
            ]
        );
        // Use 3 IP detection systems and choose the best
        $output = ['ip' => $ip];
        $ipdat = @json_decode(Tools::file_get_contents('http://ip-api.com/json/' . $ip, false, $stream_context));
        if (is_object($ipdat) && (isset($ipdat->country) || isset($ipdat->countryCode))) {
            $output = [
                'country_code' => @$ipdat->countryCode,
            ];
            if (isset($output['country_code']) && $output['country_code'] != '') {
                return $output;
            }
        }
        // 2nd option
        $ipdat = @json_decode(Tools::file_get_contents('http://www.geoplugin.net/json.gp?ip=' . $ip, false, $stream_context), true);
        if ($ipdat != null && isset($ipdat->geoplugin_countryCode)) {
            $output = [
                'country_code' => @$ipdat->geoplugin_countryCode,
            ];
        }
        if (isset($output['country_code']) && $output['country_code'] != '') {
            return $output;
        }

        // If nothing has been found check the other IP systems. After 2 tries return the default country
        return ['country_code' => Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'))];
    }
}
