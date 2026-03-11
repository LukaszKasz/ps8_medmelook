<?php
/**
 * 2019 DPD Polska Sp. z o.o.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 * @author    DPD Polska Sp. z o.o.
 * @copyright 2019 DPD Polska Sp. z o.o.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska Sp. z o.o.
 */

if (!defined('_PS_VERSION_'))
    exit;

/**
 * Class DpdPolandPackageWS Responsible for management via WebServices
 */
class DpdPolandPackageWS extends DpdPolandWS
{
    /**
     * Current file name
     */
    const FILENAME = 'Package';

    /**
     * @var array Sender data used for WebServices
     */
    private $sender = array();


    /**
     * Collects error messages from WebServices
     *
     * @param array $response Response from WebServices
     * @param string $error_key Error code
     * @param array $errors Collected errors
     * @return array Error messages
     */
    private function getErrorsByKey($response, $error_key, $errors = array())
    {
        if (!empty($response))
            foreach ($response as $key => $value)
                if (is_object($value) || is_array($value))
                    $errors = $this->getErrorsByKey($value, $error_key, $errors);
                elseif ($key == $error_key)
                    $errors[] = $value;

        return $errors;
    }

    /**
     * Creates package
     *
     * @param array $package_obj Package object
     * @return bool Package created successfully
     */
    public function create($package_obj, $payerNumber)
    {
        if ($result = $this->createRemotely($package_obj, 'THIRD_PARTY', $payerNumber)) {
            if (isset($result['Status']) && $result['Status'] == 'OK') {
                $packages = $result['Packages']['Package'];

                $isMultiShipping = !isset($result['Packages']['Package']['PackageId']);
                if ($isMultiShipping) {
                    foreach ($packages as $key => $package) {
                        $package_obj[$key]->id_package_ws = (int)$package['PackageId'];
                        $package_obj[$key]->sessionId = (int)$result['SessionId'];

                        if (!$package_obj[$key]->save())
                            self::$errors[] = $this->l('Package was successfully created but we were unable to save its data locally');
                    }
                } else {
                    $package_obj[0]->id_package_ws = (int)$result['Packages']['Package']['PackageId'];
                    $package_obj[0]->sessionId = (int)$result['SessionId'];

                    if (!$package_obj[0]->save())
                        self::$errors[] = $this->l('Package was successfully created but we were unable to save its data locally');
                }
                return $packages;
            } else {
                if (isset($result['Packages']['InvalidFields']))
                    $errors = $result['Packages']['InvalidFields'];
                elseif (isset($result['Packages']['Package']['ValidationDetails']))
                    $errors = $result['Packages']['Package']['ValidationDetails'];
                elseif (isset($result['faultcode']) && isset($result['faultstring']))
                    $errors = $result['faultcode'] . ' : ' . $result['faultstring'];
                else {
                    $errors = array();

                    if ($error_ids = $this->getErrorsByKey($result, 'ErrorId')) {
                        $language = new DpdPolandLanguage();

                        foreach ($error_ids as $id_error)
                            $errors[] = $language->getTranslation($id_error);
                    } elseif ($error_messages = $this->getErrorsByKey($result, 'Info')) {
                        foreach ($error_messages as $message)
                            $errors[] = $message;
                    }

                    $errors = reset($errors);

                    if (!$errors)
                        $errors = $this->module_instance->displayName . ' : ' . $this->l('Unknown error');
                }

                if ($errors) {
                    $errors = (array)$errors;
                    $errors = (array_values($errors) === $errors) ? $errors : array($errors); // array must be multidimentional

                    foreach ($errors as $error) {
                        if (isset($error['ValidationInfo']['Info']))
                            self::$errors[] = $error['ValidationInfo']['Info'];
                        elseif (isset($error['info']))
                            self::$errors[] = $error['info'];
                        elseif (isset($error['ValidationInfo']) && is_array($error['ValidationInfo'])) {
                            $errors_formatted = reset($error['ValidationInfo']);

                            if (isset($errors_formatted['ErrorId'])) {
                                $language = new DpdPolandLanguage();
                                $error_message = $language->getTranslation($errors_formatted['ErrorId']);

                                if (!$error_message) {
                                    $error_message = isset($errors_formatted['Info']) ? $errors_formatted['Info'] :
                                        $this->l('Unknown error occured');
                                }

                                self::$errors[] = $error_message;
                            } elseif (isset($errors_formatted['Info'])) {
                                self::$errors[] = $errors_formatted['Info'];
                            }
                        } else {
                            self::$errors[] = $error;
                        }
                    }
                } else
                    self::$errors[] = $errors;

                DpdPolandLog::addError($errors);
                return false;
            }
        }

        return false;
    }

    /**
     * Creates package remotely
     *
     * @param array $package_obj Package object
     * @param string $payerType Payer type
     * @return bool Package created successfully
     */
    private function createRemotely($package_obj, $payerType, $payerNumber)
    {
        $params = array(
            'openUMLFeV9' => array('packages' => []),
            'pkgNumsGenerationPolicyV1' => 'STOP_ON_FIRST_ERROR',
            'langCode' => 'PL'
        );
        foreach ($package_obj as $item) {
            $receiver = $this->prepareReceiverAddress($item);
            if (!$receiver)
                return false;

            $package = [
                'parcels' => $item->parcels,
                'payerType' => $payerType,
                'thirdPartyFID' => $payerNumber,
                'receiver' => $receiver,
                'ref1' => $item->ref1,
                'ref2' => $item->ref2,
                'ref3' => _DPDPOLAND_REFERENCE3_,
                'reference' => null,
                'sender' => $this->prepareSenderAddress($item->id_sender_address),
                'services' => $this->prepareServicesData($item),
            ];
            array_push($params['openUMLFeV9']['packages'], $package);
        }

        return $this->generatePackagesNumbersV8($params);
    }

    /**
     * Formats receiver address and prepares it to be used via WebServices
     *
     * @param DpdPolandPackage $package_obj Package object
     * @return array|bool
     */
    private function prepareReceiverAddress(DpdPolandPackage $package_obj)
    {
        $address = new Address((int)$package_obj->id_address_delivery);

        if (Validate::isLoadedObject($address)) {
            $customer = new Customer((int)$address->id_customer);

            if (Validate::isLoadedObject($customer)) {
                return array(
                    'address' => $address->address1 . ' ' . $address->address2,
                    'city' => $address->city,
                    'company' => $address->company,
                    'countryCode' => Country::getIsoById((int)$address->id_country),
                    'email' => isset($address->other) && !empty(trim($address->other)) ? $address->other : $customer->email,
                    'fid' => null,
                    'name' => $address->firstname . ' ' . $address->lastname,
                    'phone' => isset($address->phone) && !empty(trim($address->phone)) ? $address->phone : $address->phone_mobile,
                    'postalCode' => DpdPoland::convertPostcode($address->postcode)
                );
            } else {
                self::$errors[] = $this->l('Customer does not exists');
                return false;
            }
        } else {
            self::$errors[] = $this->l('Receiver address does not exists');
            return false;
        }

        return true;
    }

    /**
     * Formats sender address and prepares it to be used via WebServices
     *
     * @param null|int $id_sender_address Address ID
     */
    private function prepareSenderAddress($id_sender_address = null)
    {
        $sender_address = new DpdPolandSenderAddress((int)$id_sender_address);

        return array(
            'address' => $sender_address->address,
            'city' => $sender_address->city,
            'company' => $sender_address->company,
            'countryCode' => DpdPoland::POLAND_ISO_CODE,
            'email' => $sender_address->email,
            'name' => $sender_address->name,
            'phone' => $sender_address->phone,
            'postalCode' => DpdPoland::convertPostcode($sender_address->postcode)
        );
    }

    /**
     * Formats data and prepares it to be used via WebServices
     *
     * @param DpdPolandPackage $package_obj Package object
     */
    private function prepareServicesData(DpdPolandPackage $package_obj)
    {
        $services = array();
        if ($package_obj->cod_amount !== null || $package_obj->sessionType == 'domestic_with_cod' || $package_obj->sessionType == 'pudo_cod') {
            if ($package_obj->cod_amount !== null) {
                $services['cod'] = array(
                    'amount' => $package_obj->cod_amount,
                    'currency' => _DPDPOLAND_CURRENCY_ISO_
                );
            } else {
                $services['cod'] = array(
                    'amount' => 0,
                    'currency' => _DPDPOLAND_CURRENCY_ISO_
                );
            }

        }

        if ($package_obj->declaredValue_amount !== null) {
            $services['declaredValue'] = array(
                'amount' => $package_obj->declaredValue_amount,
                'currency' => _DPDPOLAND_CURRENCY_ISO_
            );
        }

        if ($package_obj->cud) {
            $services['cud'] = 1;
        }

        if ($package_obj->rod) {
            $services['rod'] = 1;
        }

        if ($package_obj->dpde) {
            $services['dpdExpress'] = 1;
        }

        if ($package_obj->dpdnd) {
            $services['guarantee'] = array('type' => 'DPDNEXTDAY');
        }

        if ($package_obj->dpdtoday) {
            $services['guarantee'] = array('type' => 'DPDTODAY');
        }

        if ($package_obj->dpdfood) {
            $services['dpdFood'] = array('limitDate' => $package_obj->dpdfood_limit_date);
        }

        if ($package_obj->dpdsaturday) {
            $services['guarantee'] = array('type' => 'SATURDAY');
        }

        if ($package_obj->dpdlq) {
            $services['dpdLQ'] = 1;
        }

        if ($package_obj->duty) {
            $services['duty'] = array(
                'amount' => $package_obj->duty_amount,
                'currency' => $package_obj->duty_currency
            );
        }

        // DPD PUDO SERVICE DATA PREPARATION
        $order = new Order($package_obj->id_order);

        // Check if order has pudo service as carrier
        if ($package_obj->sessionType == 'pudo' || $package_obj->sessionType == 'pudo_cod') {
            // Get pudo code from pudo_cart mappings table
            $pudoCode = Db::getInstance()->getValue('
                  SELECT `pudo_code`
                  FROM `' . _DB_PREFIX_ . 'dpdpoland_pudo_cart`
                  WHERE `id_cart` = ' . (int)$order->id_cart . '
            ');
            $services['dpdPickup'] = array(
                'pudo' => $pudoCode,
            );
        }
        return $services;
    }

    /**
     * Collects and returns sender address
     *
     * @param null|int $id_sender_address Sender address ID
     * @return array Sender address
     */
    public function getSenderAddress($id_sender_address = null)
    {
        if (!$this->sender)
            return $this->prepareSenderAddress($id_sender_address);

        return $this->sender;
    }

    /**
     * Generates multiple labels for selected packages
     *
     * @param array $waybills Packages waybills
     * @param string $outputDocPageFormat Document page format
     * @param string $session_type Session type
     * @return bool Multiple labels generated successfully
     */
    public function generateMultipleLabels($waybills, $outputDocPageFormat, $session_type, $outputLabelType)
    {
        if (!in_array($outputDocPageFormat, array(DpdPolandConfiguration::PRINTOUT_FORMAT_A4, DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL)))
            $outputDocPageFormat = DpdPolandConfiguration::PRINTOUT_FORMAT_A4;

        $this->prepareSenderAddress();

        $session = array(
            'packages' => array(
                'parcels' => array()
            ),
            'sessionType' => $session_type
        );

        foreach ($waybills as $waybill) {
            $session['packages']['parcels'][] = array('waybill' => $waybill);
        }

        $params = array(
            'dpdServicesParamsV1' => array(
                'policy' => 'IGNORE_ERRORS',
                'session' => $session
            ),
            'outputDocFormatV1' => 'PDF',
            'outputDocPageFormatV1' => $outputDocPageFormat,
            'outputLabelType' => $outputLabelType,
            'pickupAddress' => $this->sender
        );

        if (!$result = $this->generateSpedLabelsV4($params)) {
            return false;
        }

        if (isset($result['session']) && $result['session']['statusInfo']['status'] == 'OK') {
            return $result['documentData'];
        } else {
            if (isset($result['session']['statusInfo']['status'])) {
                self::$errors[] = $result['session']['statusInfo']['status'];

                return false;
            }

            $error = isset($result['session']['packages']['statusInfo']['description']) ?
                $result['session']['packages']['statusInfo']['description'] :
                $result['session']['statusInfo']['description'];
            self::$errors[] = $error;

            return false;
        }
    }

    /**
     * Generates labels for package
     *
     * @param DpdPolandPackage $package Package object
     * @param string $outputDocFormat Document format
     * @param string $outputDocPageFormat Document page format
     * @param $outputLabelType
     * @param string $policy Policy type
     * @return bool Labels generated successfully
     */
    public function generateLabels(DpdPolandPackage $package, $outputDocFormat, $outputDocPageFormat, $policy, $outputLabelType)
    {
        if (!in_array($outputDocPageFormat, array(DpdPolandConfiguration::PRINTOUT_FORMAT_A4, DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL)))
            $outputDocPageFormat = DpdPolandConfiguration::PRINTOUT_FORMAT_A4;

        $this->prepareSenderAddress();

        $params = array(
            'dpdServicesParamsV1' => array(
                'policy' => $policy,
                'session' => array(
                    'sessionId' => (int)$package->sessionId,
                    'sessionType' => $package->getSessionType()
                )
            ),
            'outputDocFormatV1' => $outputDocFormat,
            'outputDocPageFormatV1' => $outputDocPageFormat,
            'outputLabelType' => $outputLabelType,
            'pickupAddress' => $this->sender
        );

        if (!$result = $this->generateSpedLabelsV4($params))
            return false;

        if (isset($result['session']) && $result['session']['statusInfo']['status'] == 'OK') {
            $package->labels_printed = 1;
            $package->update();
            return $result['documentData'];
        } else {
            if (isset($result['session']['statusInfo']['status'])) {
                self::$errors[] = $result['session']['statusInfo']['status'];

                return false;
            }

            $error = isset($result['session']['packages']['statusInfo']['description']) ?
                $result['session']['packages']['statusInfo']['description'] :
                $result['session']['statusInfo']['description'];
            self::$errors[] = $error;

            return false;
        }
    }

    /**
     * Generates multiple labels for selected packages
     *
     * @param array $package_ids Packages IDs
     * @param string $outputDocFormat Document format
     * @param string $outputDocPageFormat Document page format
     * @param $outputLabelType
     * @param string $policy Policy type
     * @return bool Labels generated successfully
     */
    public function generateLabelsForMultiplePackages($package_ids, $outputDocFormat, $outputDocPageFormat, $policy, $outputLabelType)
    {
        $sessionType = '';
        $packages = array();

        foreach ($package_ids as $id_package_ws) {
            $package = new DpdPolandPackage((int)$id_package_ws);

            if (!$sessionType || $sessionType == $package->getSessionType())
                $sessionType = $package->getSessionType();
            else {
                self::$errors[] = $this->l('Manifests of DOMESTIC shipments cannot be mixed with INTERNATIONAL shipments');
                return false;
            }

            $packages[] = array(
                'packageId' => (int)$id_package_ws
            );
        }

        $this->prepareSenderAddress();

        $params = array(
            'dpdServicesParamsV1' => array(
                'policy' => $policy,
                'session' => array(
                    'packages' => $packages,
                    'sessionType' => $sessionType
                )
            ),
            'outputDocFormatV1' => $outputDocFormat,
            'outputDocPageFormatV1' => $outputDocPageFormat,
            'outputLabelType' => $outputLabelType,
            'pickupAddress' => $this->sender
        );

        if (!$result = $this->generateSpedLabelsV4($params))
            return false;

        if (isset($result['session']['statusInfo']['status']) && $result['session']['statusInfo']['status'] == 'OK') {
            foreach ($packages as $id_package_ws) {
                $package = new DpdPolandPackage($id_package_ws);
                $package->labels_printed = 1;
                $package->update();
            }

            return $result['documentData'];
        } else {
            $packages = $result['session']['statusInfo'];
            $packages = (array_values($packages) === $packages) ? $packages : array($packages); // array must be multidimentional

            foreach ($packages as $package)
                if (isset($package['description']))
                    self::$errors[] = $package['description'];
                elseif (isset($package['status']))
                    self::$errors[] = $package['status'];

            return false;
        }
    }
}