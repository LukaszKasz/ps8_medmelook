<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2021 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  @version  Release: $Revision$
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once 'TrustMateInvitations.php';
require_once 'Account.php';

class TrustMateApi
{
    const DOMAIN_DEFAULT = 'https://trustmate.io';
    const DOMAIN_DEV = 'http://trustmate.test';

    public static function domain()
    {
        if (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], array('presta16.test', 'presta17.test'))) {
            return self::DOMAIN_DEV;
        }

        $domain = Configuration::get('TRUSTMATE_DOMAIN');

        return $domain ? $domain : self::DOMAIN_DEFAULT;
    }

    public function register($params)
    {
        $result = $this->jsonRequest(
            self::domain() . '/platforms/register',
            http_build_query($params)
        );

        if ($result['http_status_code'] == 422) {
            return array('error' => json_decode($result['response']));
        }

        if ($result['http_status_code'] == 409) {
            return array('error' => "Account with your email already exists");
        }

        if ($result['http_status_code'] >= 300) {
            $this->remoteLogError(implode(' ', $result));
        }

        return json_decode($result['response'], true);
    }

    public function dispatchInvitation($order)
    {
        $result = $this->jsonRequest(
            self::domain() . '/platforms/invitation',
            json_encode(TrustMateInvitations::getDispatchData($order))
        );

        if ($result['http_status_code'] >= 300) {
            $this->remoteLogError(implode(' ', $result));
        }

        return json_decode($result['response'], true);
    }

    public function updateSettings($instantReviews)
    {
        $result = $this->jsonRequest(
            self::domain() . '/platforms/account/' . Configuration::get('TRUSTMATE_UUID') . '/settings',
            json_encode(array('instantReviewActive' => (bool) $instantReviews)),
            'PATCH'
        );

        return json_decode($result['response'], true);
    }

    private function jsonRequest($url, $data, $method = 'POST')
    {
        $handler = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => Configuration::get('TRUSTMATE_DOMAIN') ? false : true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 10,
        );

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;
        }

        if ($method === 'PATCH') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PATCH';
            $options[CURLOPT_POSTFIELDS] = $data;
        }

        curl_setopt_array($handler, $options);
        $response = curl_exec($handler);
        $httpStatusCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
        $error = curl_errno($handler) ? curl_error($handler) : null;

        curl_close($handler);

        return array(
            'http_status_code' => $httpStatusCode,
            'error' => $error,
            'response' => $response,
        );
    }

    private function remoteLogError($error)
    {
        $this->jsonRequest(
            self::domain(). '/platforms/error',
            array(
                'uuid' => Configuration::get('TRUSTMATE_UUID'),
                'error' => $error,
                'host' => $_SERVER['HTTP_HOST'],
            )
        );
    }
}
