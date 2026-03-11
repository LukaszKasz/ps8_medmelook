<?php
require_once _PS_MODULE_DIR_ . 'x13eucookies/x13eucookies.php';

class X13EuCookiesAjaxModuleFrontController extends ModuleFrontController
{
    /** @var X13EuCookies $module */
    public $module;

    public function init()
    {
        $data = file_get_contents('php://input');

        if (!empty($data)) {
            $data = json_decode($data, true);

            if (!is_array($data)) {
                $this->returnErrorJsonResponse(
                    'Invalid data'
                );
            }

            switch ($data['action']) {
                case 'checkForPopup':
                    if ($this->module->shouldConsentPopupBeShowed()) {
                        $this->returnSuccessJsonResponse();
                    } else {
                        $this->returnErrorJsonResponse();
                    }
                    break;
                case 'saveConsents':
                    $consents = $data['consents'];
                    $this->module->saveConsents($consents);
                    $this->returnSuccessJsonResponse();
                    break;
                case 'resetConsents':
                    $this->module->resetModuleCookie();
                    $this->returnSuccessJsonResponse();
                    break;
                default:
                    $this->returnErrorJsonResponse(
                        'Unknown action'
                    );
            }
        }

        return;
    }

    private function returnSuccessJsonResponse($message = null, $data = [])
    {
        header('Content-Type: application/json');
        die(json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ]));
    }

    private function returnErrorJsonResponse($message = null, $data = [])
    {
        header('Content-Type: application/json');
        die(json_encode([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ]));
    }
}
