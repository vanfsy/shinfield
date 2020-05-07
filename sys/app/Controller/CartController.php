<?php

App::uses('FrontAppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

class CartController extends FrontAppController {
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('addItem', 'delItem', 'index', 'login');
    }

    public $uses = array('Member', 'OrderItem', 'OrderPoint', 'Item', 'CreditTransaction');

    function index() {

        $this->arrEntity = array();
        $this->arrErrors = array();
        $this->arrEntity['members']['email'] = null;
        $this->arrErrors['login'] = null;
        $this->arrCart = array();

        // 商品追加
        if (isset($this->data["mode"]) && $this->data["mode"] == "add") {
            $this->Cart->add($this->data);
            $this->redirect(HOME_URL . "cart/");
            exit;
        }

        // 数量変更
        if (isset($this->data["mode"]) && $this->data["mode"] == "upd") {
            $this->Cart->upd($this->data);
            $this->redirect(HOME_URL . "cart/");
            exit;
        }

        // 商品削除
        if (isset($this->request->query["mode"]) && $this->request->query["mode"] == "del") {
            $item_id = $this->request->query['item_id'];
            if (!empty($item_id)) {
                $this->Cart->del($item_id);
                $this->redirect(HOME_URL . "cart/");
                exit;
            }
        }
        /*
          if(isset($this->data["mode"]) && $this->data["mode"] == "login"){

          $this->arrMemberEntry = $this->data;

          $this->loadModel('Member');
          $user_id  = $this->data["members"]["email"];
          $password = $this->Auth->password($this->data["members"]["user_password"]);
          $conditions = array('conditions' => array('user_id' => $user_id,'user_password' => $password,'del_flg' => 0));
          $array = $this->Member->find("first", $conditions);
          $arrMember = @$array["Member"];
          if(isset($array["Member"]) && $arrMember["user_type"] == "customer" && $arrMember["status"] > 0){
          $arrMember["logintime"] = time();
          $arrMember["user_password"] = time(); //セキュリティのため偽装する
          $member_id = $arrMember["id"];
          $conditions = array('conditions' => array("member_id = '$member_id'","del_flg <= 0"));
          $arrCustomer = $this->Customer->find("first",$conditions);
          $arrMember["customer_id"] = $arrCustomer["Customer"]["id"];

          // ログイン情報の保存
          $this->Session->write("authuser",$arrMember);
          $this->redirect(HOME_URL."cart/printing/");
          exit;

          }else{
          $this->arrEntity = $this->data;
          $this->arrErrors["login"] = "IDかパスワードが異なっています。";
          }
          }
         */
        // エラー表示
        $this->arrCart["err"] = $this->Cart->getErrors();
        $this->arrCart["is_err"] = $this->Cart->isErrors();

        // カート内商品リスト表示
        $this->arrCart["list"] = $this->Cart->getList();
        $this->arrCart["sumTotal"] = $this->Cart->getSumTotal();
        $this->arrCart["totalQuantity"] = $this->Cart->getTotalQuantity();
        $this->arrCart["totalTax"] = $this->Cart->getTotalTax();

        $this->set('arrCart', $this->arrCart);
        $this->set('arrErrors', $this->arrErrors);
        $this->set('arrEntity', $this->arrEntity);

        $arrMember = $this->Member->getDetail($this->user_id);
        $this->set('arrAuthMember', $arrMember);
    }

    function login($param) {
        $this->set('mode', $param);
        if ($this->request->is('post') || $this->request->is('put')) {
            $redirect_url = '/cart/' . $param;
            if ($this->Auth->login()) {
                $this->redirect($redirect_url);
            } else {
                $this->Session->setFlash(MSG_LOGIN_ERR, null, array(), 'auth');
            }
        }
    }

    /**
     * ポイント決済
     * */
    function clearance() {

        $this->arrEntity = array();
        $this->arrErrors = array();
        $this->arrEntity['members']['email'] = null;
        $this->arrErrors['login'] = null;
        $this->arrCart = array();

        // カート内商品リスト表示
        $arrCartList = $this->Cart->getList();
        $this->arrCart["list"] = $arrCartList;
        $this->arrCart["sumTotal"] = $this->Cart->getSumTotal();
        $this->arrCart["totalQuantity"] = $this->Cart->getTotalQuantity();
        $this->arrCart["totalTax"] = $this->Cart->getTotalTax();

        $this->set('arrCart', $this->arrCart);
        $this->set('arrErrors', $this->arrErrors);
        $this->set('arrEntity', $this->arrEntity);

        $arrMember = $this->Member->getDetail($this->user_id);
        $this->set('arrAuthMember', $arrMember);

        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data;
            $pdate['OrderItem']['member_id'] = $arrMember['Member']['id'];
            $pdate['OrderItem']['name'] = $arrMember['Member']['name'];
            $pdate['OrderItem']['name_kana'] = $arrMember['Member']['name_kana'];
            $pdate['OrderItem']['company'] = $arrMember['Member']['company'];
            $pdate['OrderItem']['postcode'] = $arrMember['Member']['postcode'];
            $pdate['OrderItem']['address'] = $arrMember['Member']['address'];
            $pdate['OrderItem']['phone'] = $arrMember['Member']['phone'];
            $pdate['OrderItem']['gender'] = $arrMember['Member']['gender'];
            $pdate['OrderItem']['email'] = $arrMember['Member']['email'];
            if ($data['OrderItem']['mode'] == 'order') {
                foreach ($arrCartList as $row) {
                    $pdate['OrderItem']['id'] = null;
                    $pdate['OrderItem']['item_id'] = null;
                    $pdate['OrderItem']['title'] = null;
                    $pdate['OrderItem']['price'] = null;
                    $pdate['OrderItem']['quantity'] = null;
                    $pdate['OrderItem']['total'] = null;
                    $pdate['OrderItem']['item_id'] = $row['item_id'];
                    $pdate['OrderItem']['title'] = $row['item_name'];
                    $pdate['OrderItem']['price'] = $row['price'];
                    $pdate['OrderItem']['quantity'] = $row['quantity'];
                    $pdate['OrderItem']['total'] = $row['total'];
                    $pdate['OrderItem']['sale_ym'] = date('Ym');
                    $this->OrderItem->save($pdate);
                }
            }
            $this->redirect('/cart/clearance_complete');
        }
    }

    function clearance_complete() {

        $this->loadModel('Item');

        $this->arrEntity = array();
        $this->arrErrors = array();
        $this->arrEntity['members']['email'] = null;
        $this->arrErrors['login'] = null;
        $this->arrCart = array();

        // カート内商品リスト表示
        $arrCartList = $this->Cart->getList();
        if (empty($arrCartList)) {
            $this->redirect('/cart');
        }

        $this->arrCart["list"] = $arrCartList;
        $this->arrCart["sumTotal"] = $this->Cart->getSumTotal();
        $this->arrCart["totalQuantity"] = $this->Cart->getTotalQuantity();
        $this->arrCart["totalTax"] = $this->Cart->getTotalTax();

        $this->set('arrCart', $this->arrCart);
        $this->set('arrErrors', $this->arrErrors);
        $this->set('arrEntity', $this->arrEntity);

        // 売上カウント
        $memberLIst = [];
        foreach ($arrCartList as $key => $row) {
            $this->Item->addSaleCount($row['item_id']);
            $itemDetail = $this->Item->getDetail($row['item_id']);
            $arrCartList[$key]['nickname'] = $itemDetail['Member']['nickname'];
            $memberLIst[$key] = $itemDetail;
        }
        $this->Cart->orderComplete($this->user_id);

        $arrMember = $this->Member->getDetail($this->user_id);
        // ポイント購入完了メール送信
        $purchaseComplete = Configure::read('mail.purchaseComplete');
        $this->sendMail($purchaseComplete, $arrMember['Member']['email'], array('datas' => $arrCartList));

        foreach ($memberLIst as $member) {
			$this->Member->addPoint($member['Member']['id'], $member['Item']['price']);	//2018.08.20 Fix bug
            if ($member['Member']['purchased_to_saller_flag'] == 1) {
                $purchasedToSaller = Configure::read('mail.purchasedToSaller');
                $this->sendMail($purchasedToSaller, $member['Member']['email'], array('datas' => $member['Item']));
            }
        }
        $this->set('arrAuthMember', $arrMember);
    }

    function buy_point() {

        $error = null;
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data;
            $this->Session->write('sesBuyPoint', $data);

            if (!isset($data['point']) || empty($data['point'])) {
                $error['point'] = 'ポイントが選択されていません';
            }
            if (!isset($data['payment_method']) || empty($data['payment_method'])) {
                $error['payment_method'] = '決済方法が選択されていません';
            }
            if (empty($error)) {
                $this->redirect('/cart/' . $data['payment_method']);
            } else {
                
            }
        }
        $this->set('arrError', $error);
    }

    function credit_card() {
        // ----------------------------- //
        // セッション確認
        // ----------------------------- //
        $sesBuyPoint = $this->Session->read('sesBuyPoint');

        if (!isset($sesBuyPoint['payment_method'], $sesBuyPoint['point']) || $sesBuyPoint['payment_method'] != 'credit_card') {
            $this->redirect('/cart/buy_point');
        }

        $arrPointRateList = Configure::read('arrPointRateList');
        if (!isset($arrPointRateList[$sesBuyPoint['point']])) {
            $this->redirect('/cart/buy_point');
        }

        $point = $sesBuyPoint['point'];
        $price = $arrPointRateList[$sesBuyPoint['point']];

        $point_str = sprintf('%sPT購入(%s円)', number_format($point), number_format($price));
        $this->set('point_str', $point_str);

        // ----------------------------- //
        // 決済通知先
        // ----------------------------- //
        $apiConf = $this->CreditTransaction->loadConfig();
        $apiInfo = $apiConf['api_info'];
        $apiType = $apiConf['api_type'];
        $isTestServer = $apiConf['test_flg'];
        $serverAddress = $apiInfo[$apiType]['server'];
        if ($isTestServer) {
            $serverAddress = $apiInfo[$apiType]['testServer'];
        }
        $this->set('apiType', $apiType);
        $this->set('paymentJs', $apiInfo[$apiType]['name']);
        $this->set('includeJs', $serverAddress . $apiInfo[$apiType]['jsPath']);
        $this->set('identity', $apiInfo[$apiType]['identity']);

        $error = null;
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data('OrderPoint');
            // トークンチェック
            if (!isset($data['paymentToken']) || empty($data['paymentToken'])) {
                $error['token'] = __('トークンが設定されていません。');
            }
            // API区分チェック
            $error['apiType'] = __('決済情報設定が不正です。');
            if (isset($data['apiType'])) {
                switch ($data['apiType']) {
                    case CreditTransaction::CREDIT_API_ALIJ:
                    case CreditTransaction::CREDIT_API_EPSILON:
                    case CreditTransaction::CREDIT_API_PAYJP:
                        if ($apiType != $data['apiType']) {
                            $error['apiType'] = __('決済情報設定が更新されましたので、再度お試しください。');
                        } else {
                            unset($error['apiType']);
                        }
                        break;
                }
            }

            // ---------------------------- //
            // エラーが無い場合
            // ---------------------------- //
            if (empty($error)) {
                // トランザクション登録
                $creTranInfo = array(
                    'transaction_time' => date('Y-m-d H:i:s'),
                    'is_test_server' => $isTestServer,
                    'member_id' => $this->user_id,
                    'api_type' => $apiType,
                    'token' => $data['paymentToken'],
                    'money' => $price,
                    'status' => CreditTransaction::CREDIT_TRANS_STATUS_PREPARE,
                    'response_id' => '',
                    'error_code' => '',
                );
                $this->CreditTransaction->create(array(
                    'CreditTransaction' => $creTranInfo
                ));
                $this->CreditTransaction->save();
                $creTranInfo['id'] = $this->CreditTransaction->id;

                $arrMember = $this->Member->getDetail($this->user_id);
                $requestUrl = $serverAddress . $apiInfo[$apiType]['apiUrl'];
                $requestParams = null;
                $apiObj = null;
                switch ($apiType) {
                    case CreditTransaction::CREDIT_API_ALIJ:
                        // DOC → https://www.alij.ne.jp/system/gateway/1_sale/sale.html
                        $requestParams = array(
                            'SiteId' => $apiInfo[$apiType]['identity'],
                            'SitePass' => $apiInfo[$apiType]['password'],
                            'token' => $creTranInfo['token'],
                            'Amount' => $price,
                            'SiteTransactionId' => $creTranInfo['id'],
                        );
                        $apiObj = new AlijPayment();
                        break;
                    case CreditTransaction::CREDIT_API_EPSILON:
                        // DOC → http://www.epsilon.jp/developer/spec01.html
                        $requestParams = array(
                            'contract_code' => $apiInfo[$apiType]['identity'],
                            'user_id' => 'x',
                            'user_name' => $arrMember['Member']['name'],
                            'user_mail_add' => $arrMember['Member']['email'],
                            'item_code' => 'PT' . $point,
                            'item_name' => 'ポイント' . $point,
                            'order_number' => $creTranInfo['id'],
                            'st_code' => '11000-0000-00000',
                            'mission_code' => '1',
                            'item_price' => $price,
                            'process_code' => '1',
                            'card_st_code' => '10',
                            'tds_check_code' => '1',
                            'pay_time' => '',
                            'keitai' => '0',
                            'security_check' => '1',
                            'user_agent' => $this->request->header('User-Agent'),
                            'token' => $creTranInfo['token'],
                        );
                        $apiObj = new EpsilonPayment();
                        $apiObj->init3DSViewVars(Router::url('/cart/credit_security_confirm/', true), $creTranInfo['id']);
                        break;
                    case CreditTransaction::CREDIT_API_PAYJP:
                        // DOC → https://pay.jp/docs/started
                        $requestParams = array(
                            'card' => $creTranInfo['token'],
                            'currency' => 'jpy',
                            'amount' => $price,
                            'description' => Configure::read('info.siteName') . 'ポイント購入',
                            'metadata' => array(
                                'ポイント数' => $point,
                            ),
                        );
                        $apiObj = new PayjpPayment($apiInfo[$apiType]['password']);
                        break;
                    default:
                        $error['type'] = __('クレジット決済設定情報が不正です。');
                        break;
                }
                if (!empty($apiObj)) {
                    $apiObj->sendRequest($requestUrl, $requestParams);
                    switch ($apiObj->getStatus()) {
                        case PaymentApi::API_RESULT_SUCCESS:
                            $creTranInfo['status'] = CreditTransaction::CREDIT_TRANS_STATUS_SUCCESS;
                            $creTranInfo['response_id'] = $apiObj->getTransactionId();
                            $this->CreditTransaction->save(array('CreditTransaction' => $creTranInfo));
                            $this->saveOrderAndAddPoint($arrMember, $sesBuyPoint);
                            $this->redirect('/cart/complete/credit_card?TransactionId=' . $creTranInfo['response_id']);
                            break;
                        case PaymentApi::API_RESULT_3DS_REQUIRED:
                            $creTranInfo['status'] = CreditTransaction::CREDIT_TRANS_STATUS_3DS_REQUIRED;
                            $this->CreditTransaction->save(array('CreditTransaction' => $creTranInfo));
                            $apiObj->set3DSSession($this->Session);
                            $this->autoLayout = false;
                            $this->view = $apiObj->get3DSViewName();
                            $this->set($apiObj->get3DSViewVars());
                            break;
                        default:
                            $creTranInfo['status'] = CreditTransaction::CREDIT_TRANS_STATUS_FAILED;
                            $creTranInfo['error_code'] = $apiObj->getErrorCode();
                            $this->CreditTransaction->save(array('CreditTransaction' => $creTranInfo));
                            $error['payment'] = __('決済処理に失敗しました。<br>入力内容をご確認の上、再度お試しください。<br>エラーコード:') . $creTranInfo['error_code'] . $apiObj->getMessage();
                            break;
                    }
                }
            }
        }

        $this->set('arrError', $error);
    }

    function credit_security_confirm() {
        $apiInfo = $this->CreditTransaction->getApiConfig();
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data;
            $creTranInfo = $this->CreditTransaction->find('first', array('id' => $data['MD']));
            $apiType = $creTranInfo['CreditTransaction']['api_type'];
            switch ($apiType) {
                case self::CREDIT_API_EPSILON:
                    $serverAddress = $apiInfo[$apiType]['server'];
                    if ($creTranInfo['CreditTransaction']['is_test_server']) {
                        $serverAddress = $apiInfo[$apiType]['testServer'];
                    }
                    $socketConn = new HttpSocket();
                    $apiRes = $socketConn->post($serverAddress, array(
                        'contract_code' => $apiInfo[$apiType]['identity'],
                        'order_number' => $creTranInfo['CreditTransaction']['id'],
                        'tds_check_code' => '2',
                        'tds_pares' => $data['PaRes'],
                    ));
                    if ($apiRes->isOK()) {
                        // 結果取込
                        $apiObj = new EpsilonPayment();
                        $apiObj->setResult($apiRes->body);
                        if ($apiObj->getStatus() == PaymentApi::API_RESULT_SUCCESS) {
                            $creTranInfo['CreditTransaction']['status'] = self::CREDIT_TRANS_STATUS_SUCCESS;
                            $creTranInfo['CreditTransaction']['response_id'] = $apiObj->getTransactionId();
                            $this->CreditTransaction->save(array('CreditTransaction' => $creTranInfo));
                            // ポイント追加処理
                            $arrMember = $this->Member->getDetail($this->user_id);
                            $sesBuyPoint = $this->Session->read('sesBuyPoint');
                            $this->saveOrderAndAddPoint($arrMember, $sesBuyPoint);
                            $this->redirect('/cart/complete/credit_card?TransactionId=' . $creTranInfo['CreditTransaction']['response_id']);
                        } else {
                            $creTranInfo['CreditTransaction']['status'] = self::CREDIT_TRANS_STATUS_3DS_FAILED;
                            $creTranInfo['CreditTransaction']['error_code'] = $apiObj->getErrorCode();
                            $this->CreditTransaction->save($creTranInfo);
                            $this->set('msg', __('3-Dセキュア認証に失敗しました。<br>エラーコード:') . $creTranInfo['CreditTransaction']['error_code']);
                        }
                    } else {
                        $creTranInfo['CreditTransaction']['status'] = self::CREDIT_TRANS_STATUS_3DS_ERROR;
                        $creTranInfo['CreditTransaction']['error_code'] = $apiRes->code;
                        $this->CreditTransaction->save($creTranInfo);
                        $this->set('msg', __('3-Dセキュア認証でエラーが発生しました。<br>ステータスコード:') . $apiRes->code);
                    }
                    break;
                default:
                    $this->set('msg', __('3-Dセキュア認証情報が設定されてません。'));
            }
        } else {
            $this->set('msg', __(''));
        }
    }

    /*
     * ポイント追加処理
     */
    private function saveOrderAndAddPoint($arrMember, $sesBuyPoint) {
        //購入履歴保存
        $pdate['OrderPoint'] = $arrMember['Member'];
        $pdate['OrderPoint']['id'] = null;
        $pdate['OrderPoint']['member_id'] = $arrMember['Member']['id'];
        $pdate['OrderPoint']['status'] = 1;
        $pdate['OrderPoint']['payment'] = $sesBuyPoint['payment_method'];
        $pdate['OrderPoint']['point'] = $sesBuyPoint['point'];
        unset($pdate['OrderPoint']['created']);
        unset($pdate['OrderPoint']['modified']);
        $this->OrderPoint->save($pdate);

        //ポイント追加
        $member_id = $arrMember['Member']['id'];
        $point = $sesBuyPoint['point'];
        $this->Member->addPoint($member_id, $point);

        // ポイント購入完了メール送信
        $pointPurchaseComplete = Configure::read('mail.pointPurchaseComplete');
        $viewVars = array('purchasePoint' => $sesBuyPoint['point'], 'paymentAmount' => Configure::read('arrPointRateList')[$sesBuyPoint['point']]);
        $this->sendMail($pointPurchaseComplete, $arrMember['Member']['email'], $viewVars);
    }

    function bank_transfer() {
        $sesBuyPoint = $this->Session->read('sesBuyPoint');
        $arrMember = $this->Member->getDetail($this->user_id);
        $this->set('arrAuthMember', $arrMember);
        $pdate['OrderPoint'] = $arrMember['Member'];
        // 銀行振込
        if ($sesBuyPoint['payment_method'] == 'bank_transfer') {
            $pdate['OrderPoint']['id'] = null;
            $pdate['OrderPoint']['member_id'] = $arrMember['Member']['id'];
            $pdate['OrderPoint']['status'] = 0;
            $pdate['OrderPoint']['payment'] = $sesBuyPoint['payment_method'];
            $pdate['OrderPoint']['point'] = $sesBuyPoint['point'];
            unset($pdate['OrderPoint']['created']);
            unset($pdate['OrderPoint']['modified']);
            $this->OrderPoint->save($pdate);
            // ポイント購入完了メール送信
            $pointPurchaseComplete = Configure::read('mail.pointPurchaseComplete');
            $viewVars = array('purchasePoint' => $sesBuyPoint['point'], 'paymentAmount' => Configure::read('arrPointRateList')[$sesBuyPoint['point']]);
            $this->sendMail($pointPurchaseComplete, $arrMember['Member']['email'], $viewVars);
        }
    }

    function complete() {

        //$sesBuyPoint = $this->Session->read('sesBuyPoint');
        $arrMember = $this->Member->getDetail($this->user_id);
        $this->set('arrAuthMember', $arrMember);
        //$pdate['OrderPoint'] = $arrMember['Member'];

        // クレジット決済
//        if ($sesBuyPoint['payment_method'] == 'credit_card') {
//            $pdate['OrderPoint']['id'] = null;
//            $pdate['OrderPoint']['member_id'] = $arrMember['Member']['id'];
//            $pdate['OrderPoint']['status'] = 1;
//            $pdate['OrderPoint']['payment'] = $sesBuyPoint['payment_method'];
//            $pdate['OrderPoint']['point'] = $sesBuyPoint['point'];
//            $this->OrderPoint->save($pdate);
//            // ポイント購入完了メール送信
//            $pointPurchaseComplete = Configure::read('mail.pointPurchaseComplete');
//            $viewVars = array('purchasePoint' => $sesBuyPoint['point'], 'paymentAmount' => Configure::read('arrPointRateList')[$sesBuyPoint['point']]);
//            $this->sendMail($pointPurchaseComplete, $arrMember['Member']['email'], $viewVars);
//            $member_id = $arrMember['Member']['id'];
//            $point = $sesBuyPoint['point'];
//            $this->Member->addPoint($member_id, $point);
//        }

        // 銀行振込
//        if ($sesBuyPoint['payment_method'] == 'bank_transfer') {
//            $pdate['OrderPoint']['id'] = null;
//            $pdate['OrderPoint']['member_id'] = $arrMember['Member']['id'];
//            $pdate['OrderPoint']['status'] = 0;
//            $pdate['OrderPoint']['payment'] = $sesBuyPoint['payment_method'];
//            $pdate['OrderPoint']['point'] = $sesBuyPoint['point'];
//            $this->OrderPoint->save($pdate);
//        }
    }

    /*
      function payment(){

      $this->arrEntity = array();
      $this->arrErrors = array();
      $this->arrEntity['members']['email'] = null;
      $this->arrErrors['login'] = null;
      $this->arrCart = array();

      // カート内商品リスト表示
      $this->arrCart["list"] = $this->Cart->getList();
      $this->arrCart["sumTotal"] = $this->Cart->getSumTotal();
      $this->arrCart["totalQuantity"] = $this->Cart->getTotalQuantity();
      $this->arrCart["totalTax"] = $this->Cart->getTotalTax();

      $this->set('arrCart',$this->arrCart);
      $this->set('arrErrors',$this->arrErrors);
      $this->set('arrEntity',$this->arrEntity);

      $arrMember = $this->Member->getDetail($this->user_id);
      $this->set('arrAuthMember',$arrMember);
      }

      function customer(){

      $this->loadModel('Order');
      $this->loadModel('CalendarDate');
      $this->loadModel('Member');
      $this->loadModel('Customer');
      $this->loadModel('CustomerAddress');

      $this->arrCart = array();
      $this->arrEntity = array();
      $this->arrErrors = array();

      // カート内商品リスト表示
      $this->arrCart["list"] = $this->Cart->getList();

      // カートに商品な無い場合
      if(count($this->arrCart["list"]) == 0){
      $this->redirect(HOME_URL."cart/");
      }

      // 認証者情報
      $this->arrMemberInfo = $this->MemberCom->authUser();

      // 初期値設定
      $member_id  = null;
      $customer_id = null;
      if(isset($this->arrMemberInfo["id"])){
      $member_id  = $this->arrMemberInfo["id"];
      $customer_id = $this->arrMemberInfo["customer_id"];
      }

      $fields = $this->Order->getCustomerByCustId($customer_id);

      $this->arrCart["orders"] = $fields;
      $fields = $this->Order->getFields();
      $this->arrCart["error"] = $fields;
      $this->arrCart["orders"]["packing_slip"] = 1;
      $this->arrCart["orders"]["delivery_way"] = 'delivery01';

      // お届け先リスト
      $this->set('arrAddressList',$this->CustomerAddress->getEntityByCustomerId($customer_id));

      // セッション注文者情報を設定
      $cust = $this->Cart->getCustomer();

      if(isset($cust["customer"]["mode"])){
      foreach($this->arrCart["orders"] as $name => $val){
      $this->arrCart["orders"][$name] = @$cust["customer"]["orders"][$name];
      }
      }

      // エラー初期値
      $error_flg = true;

      // 未ログイン会員
      if(!isset($this->arrMemberInfo["id"])){

      $this->arrEntity["customers"]["mail_mag"] = 0;
      $this->arrErrors["members"]["admin_password"] = null;
      $this->arrErrors["members"]["admin_password"] = null;

      }

      // 注文者情報登録更新
      $data = $this->request->data;
      if(isset($data["mode"]) && $data["mode"] == 'entry'){

      $data["orders"]["email_confirm"] = $data["orders"]["email"];
      $data["orders"]["customer_id"] = $customer_id;
      $data["orders"]["member_id"] = $member_id;

      // 届け先未入力
      $orders = $data["orders"];
      if(empty($orders["delivery_name"])) $data["orders"]["delivery_name"] = $orders["name"];
      if(empty($orders["delivery_name_kana"])) $data["orders"]["delivery_name_kana"] = $orders["name_kana"];
      if(empty($orders["delivery_postcode"])) $data["orders"]["delivery_postcode"] = $orders["customer_postcode"];
      if(empty($orders["delivery_pref"])) $data["orders"]["delivery_pref"] = $orders["pref"];
      if(empty($orders["delivery_address1"])) $data["orders"]["delivery_address1"] = $orders["customer_address1"];
      if(empty($orders["delivery_address2"])) $data["orders"]["delivery_address2"] = $orders["customer_address2"];
      if(empty($orders["delivery_tel"])) $data["orders"]["delivery_tel"] = $orders["customer_tel"];

      if(!isset($this->arrMemberInfo["id"])){
      $data["customers"]["company"] = $data["orders"]["company"];
      $data["customers"]["name"] = $data["orders"]["name"];
      $data["customers"]["m_email"] = $data["orders"]["m_email"];
      $data["customers"]["password"] = $data["members"]["user_password"];
      $data["customers"]["name_kana"] = $data["orders"]["name_kana"];
      $data["customers"]["email"] = $data["orders"]["email"];
      if($data["customers"]["mail_mag"] == 1){
      $data["customers"]["mailmag_mail"] = $data["orders"]["email"];
      }

      $data["customer_addresses"]["postcode"] = $data["orders"]["customer_postcode"];
      $data["customer_addresses"]["pref"] = $data["orders"]["pref"];
      $data["customer_addresses"]["city"] = $data["orders"]["customer_address1"];
      $data["customer_addresses"]["address"] = $data["orders"]["customer_address2"];
      $data["customer_addresses"]["tel"] = $data["orders"]["customer_tel"];
      $data["customer_addresses"]["mobile"] = $data["orders"]["customer_mobile"];

      // エラー値設定
      $password = $data["members"]["user_password"];
      $data["members"]["admin_password"] = $password;
      $data["members"]["user_password"] = $password;
      $data["members"]["user_password_confirm"] = $data["members"]["user_password_confirm"];
      $data["members"]["user_id"] = $data["orders"]["email"];
      $data["members"]["name"] = $data["orders"]["name"];
      $data["members"]["email"] = $data["orders"]["email"];
      $data["members"]["email_confirm"] = $data["orders"]["email"];

      $data['Member'] = $data['members'];
      $this->Member->isValid();
      $this->Member->set($data);
      if (!$this->Member->validates()) {
      $error_flg = false;
      }

      $data['Customer'] = $data['customers'];
      $this->Customer->isValid();
      $this->Customer->set($data);

      if (!$this->Customer->validates()) {
      $error_flg = false;
      }

      $data['CustomerAddress'] = $data['customer_addresses'];
      $this->CustomerAddress->isValid();
      $this->CustomerAddress->set($data);
      if (!$this->CustomerAddress->validates()) {
      $error_flg = false;
      }

      // エラー値設定
      $this->arrErrors["members"] = $this->Member->validationErrors;
      $this->arrErrors["customers"] = $this->Customer->validationErrors;
      $this->arrErrors["customer_addresses"] = $this->CustomerAddress->validationErrors;

      $this->arrEntity["members"] = $data["members"];
      $this->arrEntity["customers"] = $data["customers"];
      }

      $data['Order'] = $data['orders'];
      $this->Order->isValid();
      $this->Order->set($data);
      if (!$this->Order->validates()) {
      $error_flg = false;
      }
      //            if(!$this->Order->isError($data)) $error_flg = false;

      // 登録処理
      if($error_flg){

      if(!isset($this->arrMemberInfo["id"])){
      $this->MemberCom->regist($data);
      // ログイン情報の保存
      $user_id  = $data["orders"]["email"];
      $password = $this->Auth->password($password);
      $conditions = array('conditions' => array("user_id" => $user_id,"user_password" => $password,"del_flg" => 0));
      $array = $this->Member->find("first", $conditions);
      $arrMember = $array["Member"];
      $arrMember["logintime"] = time();
      $arrMember["user_password"] = time(); //セキュリティのため偽装する
      $member_id = $arrMember["id"];
      $conditions = array('conditions' => array("member_id = '$member_id'","del_flg <= 0"));
      $arrCustomer = $this->Customer->find("first",$conditions);
      $arrMember["customer_id"] = $arrCustomer["Customer"]["id"];
      $data["orders"]["customer_id"] = $arrCustomer["Customer"]["id"];
      $data["orders"]["member_id"] = $array["Member"]["id"];
      $this->Session->write("authuser",$arrMember);
      }

      $this->Cart->customerEntry($data);

      // 確認へ遷移
      $this->redirect(HOME_URL."cart/confirm/");
      exit;
      }else{
      $this->Session->setFlash('入力に間違いがあります。', 'valid_alert');
      $this->arrCart["orders"] = $data["orders"];
      $this->arrCart["error"] = $this->Order->validationErrors;
      }
      }
      $this->set('arrCart',$this->arrCart);
      $this->set('arrMemberInfo',$this->arrMemberInfo);
      $this->set('arrEntity',$this->arrEntity);

      // ビュー設定
      //        $this->setView('customer',$this->isSp);

      }

      function confirm(){

      $this->loadModel('Order');

      $view = "confirm";
      $this->arrCart = array();

      $this->arrCart["list"] = $this->Cart->getList();
      $this->arrCart["sumTotal"] = $this->Cart->getSumTotal();
      $this->arrCart["totalQuantity"] = $this->Cart->getTotalQuantity();
      $this->arrCart["totalTax"] = $this->Cart->getTotalTax();
      $this->arrCart["error"] = null;

      // カートに商品な無い場合
      if(count($this->arrCart["list"]) == 0){
      $this->redirect(HOME_URL."cart/");
      }

      $this->arrCart["customer"] = $this->Cart->getCustomer();

      // 注文者情報登録更新
      $this->data = $this->request->data;
      if(isset($this->data["mode"]) && $this->data["mode"] == 'entry'){
      $data = $this->arrCart["customer"]["customer"];

      if($this->Order->isError($data)){
      $this->Cart->orderComplete();

      $body = $this->Cart->getMailBody();

      $customer_mail = $this->Cart->getCustomerMail();
      $template_code = "order_form";
      $add_body = "以下の内容で受注しました。";

      // メール送信
      $this->Utility->sendMailTemplate($body,$add_body,$customer_mail,$template_code);

      // クレジット以外の場合
      if($data["orders"]["payment"] != "payment01"){
      // 確認へ遷移
      $this->redirect(HOME_URL."cart/complete/");
      exit;
      }

      // クレジット決済の場合（無地以外選択の場合）
      if($data["orders"]["payment"] == "payment01" && $this->arrCart["print"] != "printing01"){
      $this->redirect(HOME_URL."cart/complete/");
      exit;
      }

      // クレジット決済の場合（無地選択の場合）
      if($data["orders"]["payment"] == "payment01" && $this->arrCart["print"] == "printing01"){

      mb_internal_encoding("SJIS");

      /* テスト */
//                    $this->param["shopco"] = "RMS43256";
//                    $this->param["hostid"] = "43256002";
    /**/
    /*
      $this->param["shopco"] = "RMS04626";
      $this->param["hostid"] = "04626002";
     */
//                    $this->param["code"] = $body["code"];
//                    $this->param["total"] = $body["total"];
//                    $this->param["customer_mail"] = $customer_mail;
//                    mb_convert_variables('SJIS','UTF8',$this->param);
    // クレジット決済URL
//                    $this->credit_url = "https://test.remise.jp/rpgw2/pc/card/paycard.aspx"; // テスト
//                    $this->credit_url = "https://ssl01.remise.jp/rpgw2/pc/card/paycard.aspx"; // 本番
//                    $this->set('param',$this->param);
//                    $this->set('credit_url',$this->credit_url);
    // ビュー設定
//                    $view = "credit";
//                }
//            }else{
//                $this->arrCart["error"] = $this->Order->getErrors();
//            }
//        }
//print_r($this->arrCart);
//        $this->set('arrCart',$this->arrCart);
    // ビュー設定
//        $this->render($view);
//        $this->setView($view,$this->isSp);
//    }
    // ポイント利用処理
    /*
      function redeem_point(){
      if(isset($this->data)){
      $point = $this->data["orders"]["redeem_point"];
      $this->Session->write('input_point',$point);
      $error = null;
      $point = str_replace(",","",$point);
      if($point > $this->arrAuthCustomer["point"]){
      $error = "ポイントが不足です。";
      }
      if(!is_numeric($point)){
      $error = "ポイントの入力は半角数字のみです";
      }
      if(empty($error)){
      $this->Cart->setPoint($point);
      }else{
      $this->Session->write('point_err',$error);
      }
      }
      $this->redirect(HOME_URL."cart/confirm/");
      }
     */

    function delItem($item_id) {

        $this->Cart->del($item_id);
        $this->redirect('/cart/');
    }

    function addItem() {

        $this->layout = null;
        $this->autoRender = false;

        $data['item_id'] = $this->request->query['item_id'];
        $data['quantity'] = @$this->request->query['quantity'];
        $data['item_name'] = @$this->request->query['item_name'];
        $data['price'] = @$this->request->query['price'];
        $data['imgurl'] = @$this->request->query['imgurl'];
        $this->Cart->add($data);

        $arrList = $this->Cart->getList();
        $result = null;
        if (!empty($arrList)) {
            foreach ($arrList as $i => $row) {
                $result['items'][$i]['name'] = $row['item_name'];
                $result['items'][$i]['price'] = $row['price'];
                $result['items'][$i]['quantity'] = $row['quantity'];
                $result['items'][$i]['imgurl'] = @$row['imgurl'];
                $result['items'][$i]['total'] = $row['total'];
            }
        }
        $result['total'] = $this->Cart->getTotal();

        // Ajax以外の通信の場合
        if (!$this->request->is('ajax')) {
            $this->redirect('/cart/');
//            throw new BadRequestException();
        }

        $status = !empty($result);
        if (!$status) {
            $error = array(
                'message' => 'データがありません',
                'code' => 404
            );
        }

        return json_encode(compact('status', 'result', 'error'));
    }

    function getItem() {

        $this->layout = null;
        $this->autoRender = false;

        $arrList = $this->Cart->getList();
        $result = null;
        if (!empty($arrList)) {
            foreach ($arrList as $i => $row) {
                $result['items'][$i]['name'] = $row['merch_items']['name'];
                $result['items'][$i]['price'] = $row['merch_items']['sales_price'];
                $result['items'][$i]['size'] = $row['merch_items']['size'];
                $result['items'][$i]['color'] = $row['merch_items']['color'];
                $result['items'][$i]['quantity'] = $row['merch_items']['quantity'];
                $result['items'][$i]['total'] = $row['merch_items']['total'];
            }
        }
        $result['total'] = $this->Cart->getTotal();

        // Ajax以外の通信の場合
        if (!$this->request->is('ajax')) {
            throw new BadRequestException();
        }

        $status = !empty($result);
        if (!$status) {
            $error = array(
                'message' => 'データがありません',
                'code' => 404
            );
        }

        return json_encode(compact('status', 'result', 'error'));
    }

}

?>
