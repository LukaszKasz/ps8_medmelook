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

class PlatformApi
{
    const DOMAIN_DEFAULT = 'https://papi.trustmate.io';
    const DOMAIN_DEV = 'http://172.17.0.1:8666';

    const API_ORDER_SENT = 0;
    const API_ORDER_CREATED = 1;
    const API_ORDER_PAID = 2;
    const API_ORDER_IN_PREPARATION = 3;
    const API_ORDER_DELIVERED = 4;

    public static function domain()
    {
        if (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], array('presta16.test', 'presta17.test'))) {
            return self::DOMAIN_DEV;
        }

        $domain = Configuration::get('PLATFORM_API_DOMAIN');

        return $domain ? $domain : self::DOMAIN_DEFAULT;
    }

    public function install()
    {
        $onHttps = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');

        $data = array(
            'action' => 'install',
            'shop_id' => Context::getContext()->shop->domain,
            'email' => Configuration::get('PS_SHOP_EMAIL'),
            'shop_url' => Context::getContext()->shop->getBaseURL($onHttps),
            'uuid' => Configuration::get('TRUSTMATE_UUID'),
            'invitations' => (int) (bool) Configuration::get('TRUSTMATE_INVITATIONS'),
        );

        $apiOrderStatus = null;

        switch (Configuration::get('TRUSTMATE_DISPATCH_TRIGGERED_BY')) {
            case Configuration::get('PS_OS_PAYMENT'):
                $apiOrderStatus = self::API_ORDER_PAID;
                break;

            case Configuration::get('PS_OS_PREPARATION'):
                $apiOrderStatus = self::API_ORDER_IN_PREPARATION;
                break;

            case Configuration::get('PS_OS_SHIPPING'):
                $apiOrderStatus = self::API_ORDER_SENT;
                break;

            case Configuration::get('PS_OS_DELIVERED'):
                $apiOrderStatus = self::API_ORDER_DELIVERED;
                break;
        }

        if ($apiOrderStatus) {
            $data['create_invitation_on'] = $apiOrderStatus;
        }

        $result = $this->request(
            self::domain() . '/presta',
            json_encode($data)
        );

        if ($result['http_status_code'] == 422) {
            return array('error' => json_decode($result['response']));
        }

        if ($result['http_status_code'] == 409) {
            return array('error' => "Account already exists");
        }

        if ($result['http_status_code'] >= 300) {
            $this->remoteLogError(implode(' ', $result));
        }

        $this->updateMetadata();

        return $result['response'];
    }

    public function uninstall()
    {
        $domain = Context::getContext()->shop->domain;
        $data = array(
            'action' => 'uninstall',
            'shop_id' => $domain,
        );

        $result = $this->request(
            self::domain() . '/presta',
            json_encode($data)
        );

        if ($result['http_status_code'] == 422) {
            return array('error' => json_decode($result['response']));
        }

        if ($result['http_status_code'] >= 300) {
            $this->remoteLogError(implode(' ', $result));
        }

        return $result['response'];
    }

    public function enableApi()
    {
        Configuration::updateValue('PS_WEBSERVICE', 1);
        $apiAccessId = Configuration::get('TRUSTMATE_API_KEY_ID');
        $apiAccess = new WebserviceKey($apiAccessId);

        if (!$apiAccess->key) {
            $apiAccess = new WebserviceKey();
            $apiAccess->description = 'TrustMate.io integration';
            $apiAccess->key = substr(uniqid().uniqid().uniqid(), 0, 32);
            $apiAccess->save();

            $readPermissions = array(
                'GET' => 1,
            );

            $permissions = array(
                'categories' => $readPermissions,
                'combinations' => $readPermissions,
                'configurations' => $readPermissions,
                'customers' => $readPermissions,
                'currencies' => $readPermissions,
                'orders' => $readPermissions,
                'order_details' => $readPermissions,
                'order_states' => $readPermissions,
                'products' => $readPermissions,
            );

            WebserviceKey::setPermissionForAccount($apiAccess->id, $permissions);
            Configuration::updateValue('TRUSTMATE_API_KEY_ID', $apiAccess->id);
        }

        return $this->updateMetadata();
    }

    public function disableApi()
    {
        $apiAccessId = Configuration::get('TRUSTMATE_API_KEY_ID');
        $apiAccess = new WebserviceKey($apiAccessId);
        $apiAccess->delete();
        Configuration::updateValue('TRUSTMATE_API_KEY_ID', null);
        $this->updateMetadata();
    }

    public function updateMetadata()
    {
        $domain = Context::getContext()->shop->domain;

        $data = array(
            'additional_info' => array(
                'bee' => Configuration::get('TRUSTMATE_BEE'),
                'muskrat' => Configuration::get('TRUSTMATE_MUSKRAT'),
                'muskrat2' => Configuration::get('TRUSTMATE_MUSKRAT2'),
                'product_ferret' => Configuration::get('TRUSTMATE_PRODUCT_FERRET'),
                'product_ferret2' => Configuration::get('TRUSTMATE_PRODUCT_FERRET2'),
                'gorilla' => Configuration::get('TRUSTMATE_GORILLA'),
                'alpaca' => Configuration::get('TRUSTMATE_ALPACA'),
                'badger' => Configuration::get('TRUSTMATE_BADGER'),
                'badger2' => Configuration::get('TRUSTMATE_BADGER2'),
                'ferret' => Configuration::get('TRUSTMATE_FERRET'),
                'ferret2' => Configuration::get('TRUSTMATE_FERRET2'),
                'chupacabra' => Configuration::get('TRUSTMATE_CHUPACABRA'),
                'lemur' => Configuration::get('TRUSTMATE_LEMUR'),
                'owl' => Configuration::get('TRUSTMATE_OWL'),
                'hornet' => Configuration::get('TRUSTMATE_HORNET'),
                'hydra' => Configuration::get('TRUSTMATE_HYDRA'),
                'hornet_position' => Configuration::get('TRUSTMATE_HORNET_POSITION'),
                'multihornet' => Configuration::get('TRUSTMATE_MULTIHORNET'),
                'multihornet_position' => Configuration::get('TRUSTMATE_MULTIHORNET_PAGES'),
                'instant_review' => Configuration::get('TRUSTMATE_INSTANT_REVIEW'),
            ),
            'language' => Configuration::get('TRUSTMATE_LANGUAGE'),
            'platform_language' => 'PHP '.phpversion(),
            'platform' => 'Presta '._PS_VERSION_,
            'platform_module' => null,
            'trustmate_plugin_version' => Module::getInstanceByName('trustmate')->version,
        );

        $apiAccessId = Configuration::get('TRUSTMATE_API_KEY_ID');
        $apiAccess = new WebserviceKey($apiAccessId);
        if ($apiAccess) {
            $data['platform_api_key'] = $apiAccess->key;
        }

        $result = $this->request(
            self::domain() . "/shop_metadata/{$domain}",
            json_encode($data)
        );

        if ($result['http_status_code'] >= 400) {
            $this->disableApi();
        }

        if ($result['http_status_code'] == 422) {
            return array('error' => json_decode($result['response']));
        }

        if ($result['http_status_code'] == 409) {
            return array('error' => "Account already exists");
        }

        if ($result['http_status_code'] >= 300) {
            $this->remoteLogError(implode(' ', $result));
        }

        return $result['response'];
    }

    private function request($url, $data)
    {
        $handler = curl_init();
        curl_setopt_array($handler, array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => Configuration::get('TRUSTMATE_DOMAIN') ? false : true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 10,
        ));

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
        $this->request(
            TrustMateApi::domain(). '/platforms/error',
            json_encode(array(
                'uuid' => Configuration::get('TRUSTMATE_UUID'),
                'error' => 'Platform API: ' . $error,
                'host' => $_SERVER['HTTP_HOST'],
            ))
        );
    }
}
