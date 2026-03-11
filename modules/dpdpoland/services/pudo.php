<?php

namespace DpdPoland\Service;

use Address;
use Country;
use Customer;
use DpdPolandLog;
use Exception;
use SimpleXMLElement;
use Translate;

class PudoService
{

    const PUDO_WS_URL = 'https://mypudo.dpd.com.pl/api/pudo/details?pudoId=%s&key=4444';


    public function __construct()
    {
    }

    /**
     * Get pudo address as PrestaShop Address object from web service, when idCustomer has value save pickup address
     *
     * @param string $pudoCode
     * @param int $idCustomer
     *
     * @param string $phone
     * @return Address|null
     */
    public function getPudoAddress($pudoCode, $idCustomer = null, $phone = '000000000')
    {
        if (false == $pudoCode) {
            return null;
        }

        $ch = curl_init();

        $url = sprintf(self::PUDO_WS_URL, $pudoCode);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, sdch, br');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Encoding: gzip, deflate, sdch, br',
            'Accept-Language: en-US,en;q=0.8',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Host: mypudo.dpd.com.pl',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
        ));

        $result = curl_exec($ch);
        curl_close($ch);
        if (!$result) {
            DpdPolandLog::addError('Error in getPudoAddress curl_exec');
            return null;
        }

        $xml = new SimpleXMLElement($result);

        if (!isset($xml->PUDO_ITEMS) || !isset($xml->PUDO_ITEMS->PUDO_ITEM)) {
            return null;
        }

        if (!isset($xml->PUDO_ITEMS->PUDO_ITEM->ADDRESS1) ||
            !isset($xml->PUDO_ITEMS->PUDO_ITEM->CITY) ||
            !isset($xml->PUDO_ITEMS->PUDO_ITEM->ZIPCODE)
        ) {
            return null;
        }

        $idCountry = $idCustomer != null ? Country::getByIso('PL') : null;

        if (!$idCountry && $idCustomer != null) {
            return null;
        }

        $customer = $idCustomer != null ? new Customer($idCustomer) : null;

        $address = new Address();
        $address->address1 = $xml->PUDO_ITEMS->PUDO_ITEM->ADDRESS1;
        $address->city = $xml->PUDO_ITEMS->PUDO_ITEM->CITY;
        $address->postcode = $xml->PUDO_ITEMS->PUDO_ITEM->ZIPCODE;
        $address->id_country = $idCountry;
        $address->id_state = 0;
        $address->id_manufacturer = 0;
        $address->id_supplier = 0;
        $address->id_warehouse = 0;
        $address->alias = $this->translate('Point Pick Up');
        $address->address2 = '';
        $address->other = '';
        $address->phone = $phone;
        $address->vat_number = '';
        $address->dni = '';
        $address->deleted = 1;

        if ($customer != null) {
            $address->firstname = $customer->firstname;
            $address->lastname = $customer->lastname;
            $address->id_customer = (int)$customer->id;
            $address->company = (string)$customer->company;
        }
        try {
            if ($idCustomer != null && !$address->save()) {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }

        return $address;
    }


    /**
     * Check if pudo has COD (101) service
     *
     * @param string $pudoCode
     *
     * @return Boolean
     */
    public function hasCodService($pudoCode)
    {
        if (false == $pudoCode) {
            return null;
        }

        $ch = curl_init();

        $url = sprintf(self::PUDO_WS_URL, $pudoCode);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, sdch, br');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Encoding: gzip, deflate, sdch, br',
            'Accept-Language: en-US,en;q=0.8',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Host: mypudo.dpd.com.pl',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
        ));

        $result = curl_exec($ch);
        curl_close($ch);
        if (!$result) {
            DpdPolandLog::addError('Error in getPudoAddress curl_exec');
            return null;
        }

        $xml = new SimpleXMLElement($result);

        if (!isset($xml->PUDO_ITEMS) || !isset($xml->PUDO_ITEMS->PUDO_ITEM) || !isset($xml->PUDO_ITEMS->PUDO_ITEM->SERVICE_PUDO))
            return 0;

        if (strpos($xml->PUDO_ITEMS->PUDO_ITEM->SERVICE_PUDO, "101") !== false)
            return 1;

        return 0;
    }

    private function translate($string)
    {
        return Translate::getModuleTranslation('dpdpoland', $string, 'dpdpoland');
    }
}