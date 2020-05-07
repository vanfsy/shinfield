<?php

/*
 * Copyright (c) 2019, Mallento JAPAN
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms,
 * with or without modification,
 * are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * Neither the name of the <ORGANIZATION> nor the names of its contributors may
 * be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING,
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

App::import('Vendor', 'Payjp', array('file' => 'Payjp/init.php'));

class CreditTransaction extends AppModel {

    const CREDIT_API_ALIJ = 1;
    const CREDIT_API_EPSILON = 2;
    const CREDIT_API_PAYJP = 3;
    const CREDIT_TRANS_STATUS_PREPARE = 0;
    const CREDIT_TRANS_STATUS_3DS_REQUIRED = 1;
    const CREDIT_TRANS_STATUS_SUCCESS = 2;
    const CREDIT_TRANS_STATUS_FAILED = 3;
    const CREDIT_TRANS_STATUS_ERROR = 4;
    const CREDIT_TRANS_STATUS_3DS_CANCEL = 5;
    const CREDIT_TRANS_STATUS_3DS_FAILED = 6;
    const CREDIT_TRANS_STATUS_3DS_ERROR = 7;

    public function loadConfig() {
        $cfg = array(
            'test_flg' => (Configure::read('debug') == '2'),
            'api_type' => null,
            'api_info' => array(
                self::CREDIT_API_ALIJ => array(
                    'name' => 'alij',
                    'server' => 'https://payment.alij.ne.jp',
                    'testServer' => 'https://test-payment.alij.ne.jp',
                    'apiUrl' => '/service/gateway/credit',
                    'jsPath' => '/service/public/temps/alij_tokenpay.min.js',
                    'identity' => '',
                    'password' => '',
                ),
                self::CREDIT_API_EPSILON => array(
                    'name' => 'epsilon',
                    'server' => 'https://secure.epsilon.jp',
                    'testServer' => 'https://beta.epsilon.jp',
                    'apiUrl' => '/cgi-bin/order/direct_card_payment.cgi',
                    'jsPath' => '/js/token.js',
                    'identity' => '',
                    'password' => '',
                ),
                self::CREDIT_API_PAYJP => array(
                    'name' => 'payjp',
                    'server' => 'https://js.pay.jp',
                    'testServer' => 'https://js.pay.jp',
                    'apiUrl' => '',
                    'jsPath' => '',
                    'identity' => '',
                    'password' => '',
                ),
            ),
        );
        $cfgPath = ROOT . DS . APP_DIR . DS . 'payment.cfg';
        if (file_exists($cfgPath)) {
            $params = array();
            $cfgContents = file_get_contents($cfgPath);
            $cfgList = explode(PHP_EOL, $cfgContents);
            foreach ($cfgList as $cfgVal) {
                $elm = explode("=", $cfgVal, 2);
                if (count($elm) == 2) {
                    $params[$elm[0]] = trim($elm[1]);
                }
            }
            if (isset($params['test_flag']) && $params['test_flag'] == '1') {
                $cfg['test_flg'] = true;
            }
            if (isset($params['vendor'])) {
                switch ($params['vendor']) {
                    case 'alij':
                        $cfg['api_type'] = self::CREDIT_API_ALIJ;
                        if (isset($params['site_id'])) {
                            $cfg['api_info'][self::CREDIT_API_ALIJ]['identity'] = $params['site_id'];
                        }
                        if (isset($params['site_pass'])) {
                            $cfg['api_info'][self::CREDIT_API_ALIJ]['password'] = $params['site_pass'];
                        }
                        break;
                    case 'epsilon':
                        $cfg['api_type'] = self::CREDIT_API_EPSILON;
                        if (isset($params['contract_code'])) {
                            $cfg['api_info'][self::CREDIT_API_EPSILON]['identity'] = $params['contract_code'];
                        }
                        break;
                    case 'payjp':
                        $cfg['api_type'] = self::CREDIT_API_PAYJP;
                        if (isset($params['public_key'])) {
                            $cfg['api_info'][self::CREDIT_API_PAYJP]['identity'] = $params['public_key'];
                        }
                        if (isset($params['private_key'])) {
                            $cfg['api_info'][self::CREDIT_API_PAYJP]['password'] = $params['private_key'];
                        }
                        break;
                }
            }
        }
        return $cfg;
    }

    public function getApiConfig() {
        $cfg = $this->loadConfig();
        return $cfg['api_info'];
    }

}

interface PaymentApi {

    const API_RESULT_FAILED = 0;
    const API_RESULT_SUCCESS = 1;
    const API_RESULT_3DS_REQUIRED = 2;

    public function sendRequest($requestUrl, $reqData);
    public function getStatus();
    public function getErrorCode();
    public function getMessage();
    public function getTransactionId();
    public function get3DSViewName();
    public function get3DSViewVars();
}

class AlijPayment implements PaymentApi {

    private $resObj = array();

    public function sendRequest($requestUrl, $reqData) {
        $this->resObj = array(
            'state' => '2',
            'TransactionId' => '',
            'cd' => '----',
            'msg' => __('予期しないエラーが発生しました。'),
        );
        $socketConn = new HttpSocket();
        $apiRes = $socketConn->post($requestUrl, $reqData);
        if ($apiRes->isOk()) {
            parse_str($apiRes->body, $this->resObj);
        }
    }

    public function getStatus() {
        if (isset($this->resObj['state']) && $this->resObj['state'] == "1") {
            return self::API_RESULT_SUCCESS;
        }
        return self::API_RESULT_FAILED;
    }

    public function getErrorCode() {
        if (isset($this->resObj['cd'])) {
            return $this->resObj['cd'];
        }
        return '';
    }

    public function getMessage() {
        if (isset($this->resObj['msg'])) {
            return $this->resObj['msg'];
        }
        return '';
    }

    public function getTransactionId() {
        if (isset($this->resObj['TransactionId'])) {
            return $this->resObj['TransactionId'];
        }
        return '';
    }

    public function get3DSViewName() {
        return '';
    }

    public function get3DSViewVars() {
        return array();
    }

}

class EpsilonPayment implements PaymentApi {

    private $resObj = array();
    private $_3dsCallbackUrl = '';
    private $_3dsMerchantData = '';

    public function init3DSViewVars($callbackUrl, $merchantData) {
        $this->_3dsCallbackUrl = $callbackUrl;
        $this->_3dsMerchantData = $merchantData;
    }

    public function sendRequest($requestUrl, $reqData) {
        $this->resObj = array(
            'result' => '0',
            'trans_code' => '',
            'err_code' => '----',
            'err_detail' => __('予期しないエラーが発生しました。'),
            'acsurl' => '',
            'pareq' => '',
        );
        $socketConn = new HttpSocket();
        $apiRes = $socketConn->post($requestUrl, $reqData);
        if ($apiRes->isOk()) {
            $xmlStr = str_replace('x-sjis-cp932', 'UTF-8', $apiRes->body);
            $xmlObj = simplexml_load_string($xmlStr);
            foreach ($xmlObj->result as $item) {
                foreach ($item->attributes() as $key => $val) {
                    $this->resObj[$key] = (string) $val;
                }
            }
            $this->resObj['err_detail'] = mb_convert_encoding(urldecode($this->resObj['err_detail']), "UTF-8", "SJIS");
        }
    }

    public function getStatus() {
        if (isset($this->resObj['result'])) {
            switch ($this->resObj['result']) {
                case '1':
                    return self::API_RESULT_SUCCESS;
                case '5':
                    return self::API_RESULT_3DS_REQUIRED;
            }
        }
        return self::API_RESULT_FAILED;
    }

    public function getErrorCode() {
        if (isset($this->resObj['err_code'])) {
            return $this->resObj['err_code'];
        }
        return '';
    }

    public function getMessage() {
        if (isset($this->resObj['err_detail'])) {
            return $this->resObj['err_detail'];
        }
        return '';
    }

    public function getTransactionId() {
        if (isset($this->resObj['trans_code'])) {
            return $this->resObj['trans_code'];
        }
        return '';
    }

    public function get3DSViewName() {
        return 'credit_card_3ds_epsilon';
    }

    public function get3DSViewVars() {
        return array(
            'formAction' => $this->resObj['acsurl'],
            'paReq' => $this->resObj['pareq'],
            'termUrl' => $this->_3dsCallbackUrl,
            'merchantData' => $this->_3dsMerchantData,
        );
    }

}

class PayjpPayment implements PaymentApi {

    private $resObj = array();
    private $privateKey = '';

    public function __construct($privateKey) {
        $this->privateKey = $privateKey;
    }

    public function sendRequest($requestUrl, $reqData) {
        \Payjp\Payjp::setApiKey($this->privateKey);
        try {
            $apiRes = \Payjp\Charge::create($reqData);
            $this->resObj = array(
                'result' => self::API_RESULT_SUCCESS,
                'trans_code' => $apiRes->id,
            );
        } catch (\Payjp\Error\Base $e) {
            $body = $e->getJsonBody();
            $this->resObj = array(
                'result' => self::API_RESULT_FAILED,
                'trans_code' => '',
                'err_code' => $body['error']['code'],
                'err_detail' => $body['error']['message'],
            );
        } catch (Exception $e) {
            $this->resObj = array(
                'result' => self::API_RESULT_FAILED,
                'trans_code' => '',
                'err_code' => '----',
                'err_detail' => __('予期しないエラーが発生しました。'),
            );
        }
    }

    public function getStatus() {
        return $this->resObj['result'];
    }

    public function getErrorCode() {
        if (!empty($this->resObj['err_code'])) {
            return $this->resObj['err_code'];
        }
        return '';
    }

    public function getMessage() {
        if (!empty($this->resObj['err_detail'])) {
            return $this->resObj['err_detail'];
        }
        return '';
    }

    public function getTransactionId() {
        if (isset($this->resObj['trans_code'])) {
            return $this->resObj['trans_code'];
        }
        return '';
    }

    public function get3DSViewName() {
        return '';
    }

    public function get3DSViewVars() {
        return array();
    }

}
