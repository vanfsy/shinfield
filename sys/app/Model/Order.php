<?php
class  Order extends AppModel {
    var $validate = array();

    /*
     * 入力値検証
    */

    function isValid(){

        $validate["name_kana"]["hiragana"]          = array("rule" => "hiragana", "message" => "このフィールドはひらがなです。");
        $validate["delivery_name_kana"]["hiragana"] = array("rule" => "hiragana", "allowEmpty" => true, "message" => "このフィールドはひらがなです。");
        $validate["email"]["email"]                        = array("rule" => "email", "message" => "メールアドレスを正しく入力してください。");
        $validate["email"]["isConfirm"]                    = array("rule" => array("isConfirm","orders"), "message" => "確認メールアドレスと一致しません。");
        /*
         $validate["customer_postcode"]["numeric"]         = array("rule" => "numeric", "message" => "郵便番号は数字です。");
        $validate["customer_postcode"]["minLength"]       = array("rule" => array("minLength", 7), "allowEmpty" => true, "message" => "郵便番号は7桁です。");
        $validate["customer_postcode"]["maxLength"]       = array("rule" => array("maxLength", 7), "allowEmpty" => true, "message" => "郵便番号は7桁です。");

        $validate["customer_tel"]["numeric"]              = array("rule" => "numeric", "message" => "電話番号は数字です。");
        $validate["customer_tel1"]["minLength"]            = array("rule" => array("minLength", 2), "message" => "TEL1は不正な形式です。");
        $validate["customer_tel2"]["minLength"]            = array("rule" => array("minLength", 2), "message" => "TEL2は不正な形式です。");
        $validate["customer_tel3"]["minLength"]            = array("rule" => array("minLength", 4), "message" => "TEL3は不正な形式です。");
        $validate["customer_fax1"]["numeric"]              = array("rule" => "numeric", "allowEmpty" => true, "message" => "市外局番は数字です。");
        $validate["customer_fax2"]["numeric"]              = array("rule" => "numeric", "allowEmpty" => true, "message" => "市内局番は数字です。");
        $validate["customer_fax3"]["numeric"]              = array("rule" => "numeric", "allowEmpty" => true, "message" => "FAX番号は数字です。");
        $validate["customer_fax1"]["minLength"]            = array("rule" => array("minLength", 2), "allowEmpty" => true, "message" => "fax1は不正な形式です。");
        $validate["customer_fax2"]["minLength"]            = array("rule" => array("minLength", 2), "allowEmpty" => true, "message" => "fax2は不正な形式です。");
        $validate["customer_fax3"]["minLength"]            = array("rule" => array("minLength", 4), "allowEmpty" => true, "message" => "fax3は不正な形式です。");

        $validate["delivery_tel1"]["numeric"]              = array("rule" => "numeric", "allowEmpty" => true, "message" => "市外局番は数字です。");
        $validate["delivery_tel2"]["numeric"]              = array("rule" => "numeric", "allowEmpty" => true, "message" => "市内局番は数字です。");
        $validate["delivery_tel3"]["numeric"]              = array("rule" => "numeric", "allowEmpty" => true, "message" => "電話番号は数字です。");
        $validate["delivery_fax1"]["numeric"]              = array("rule" => "numeric", "allowEmpty" => true, "message" => "市外局番は数字です。");
        $validate["delivery_fax2"]["numeric"]              = array("rule" => "numeric", "allowEmpty" => true, "message" => "市内局番は数字です。");
        $validate["delivery_fax3"]["numeric"]              = array("rule" => "numeric", "allowEmpty" => true, "message" => "FAX番号は数字です。");

        $validate["delivery_tel1"]["minLength"]            = array("rule" => array("minLength", 2), "allowEmpty" => true, "message" => "市外局番は不正な形式です。");
        $validate["delivery_tel2"]["minLength"]            = array("rule" => array("minLength", 2), "allowEmpty" => true, "message" => "市内局番は不正な形式です。");
        $validate["delivery_tel3"]["minLength"]            = array("rule" => array("minLength", 4), "allowEmpty" => true, "message" => "電話番号は不正な形式です。");
        $validate["delivery_fax1"]["minLength"]            = array("rule" => array("minLength", 2), "allowEmpty" => true, "message" => "市外局番は不正な形式です。");
        $validate["delivery_fax2"]["minLength"]            = array("rule" => array("minLength", 2), "allowEmpty" => true, "message" => "市内局番は不正な形式です。");
        $validate["delivery_fax3"]["minLength"]            = array("rule" => array("minLength", 4), "allowEmpty" => true, "message" => "FAX番号は不正な形式です。");

        $validate["delivery_postcode1"]["minLength"]       = array("rule" => array("minLength", 3), "allowEmpty" => true, "message" => "郵便番号1は3桁です。");
        $validate["delivery_postcode2"]["minLength"]       = array("rule" => array("minLength", 4), "allowEmpty" => true, "message" => "郵便番号2は4桁です。");
        $validate["delivery_postcode1"]["maxLength"]       = array("rule" => array("maxLength", 3), "allowEmpty" => true, "message" => "郵便番号1は3桁です。");
        $validate["delivery_postcode2"]["maxLength"]       = array("rule" => array("maxLength", 4), "allowEmpty" => true, "message" => "郵便番号2は4桁です。");

        $validate["status"]["notEmpty"]                   = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["family_name"]["notEmpty"]              = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["first_name"]["notEmpty"]               = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["family_name_kana"]["notEmpty"]         = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["first_name_kana"]["notEmpty"]          = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["email"]["notEmpty"]                    = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["customer_postcode1"]["notEmpty"]       = array("rule" => "notEmpty", "message" => "郵便番号1は必須です。");
        $validate["customer_postcode2"]["notEmpty"]       = array("rule" => "notEmpty", "message" => "郵便番号2は必須です。");
        $validate["pref"]["notEmpty"]                     = array("rule" => array('range', 0, 48), "message" => "必須項目です。");

        $validate["customer_tel1"]["notEmpty"]            = array("rule" => "notEmpty", "message" => "市外局番は必須です。");
        $validate["customer_tel2"]["notEmpty"]            = array("rule" => "notEmpty", "message" => "市内局番は必須です。");
        $validate["customer_tel3"]["notEmpty"]            = array("rule" => "notEmpty", "message" => "電話番号は必須です。");

        */
        $validate["payment"]["notEmpty"]                  = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["delivery_way"]["notEmpty"]             = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["customer_address1"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["customer_address2"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "必須項目です。");

        $this->validate = $validate;

    }

    /*
     * エラーチェック
    * */
    function isError($data){
        return parent::isError($data,'orders');
    }

    /*
     * フィールド
    */
    function getFields(){
        return parent::getFields();
    }

    /*
     * オプションリスト
    */
    function getOptions(){
        $table = 'orders';
        $where = null;
        return parent::getOptions($table,$where);
    }

    /*
     * オプションリスト
    */
    function getTreeOptions($table = 'orders'){

        return parent::getTreeOptions($table);
    }

    /*
     * リスト
    */
    function getOneFieldById($id,$field){

        $table = 'orders';
        return parent::getOneFieldById($table,$id,$field);
    }

    /*
     * リスト
    */
    function getOneEntityById($id){

        // SQL処理
        $sql =  "SELECT * FROM orders WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        $status = @$array[0]["orders"]["status"];
        $naxt = $status + 1;

        $Constants = ClassRegistry::init('Constant');
        $arrConst = $Constants->getArrOptions("order_status");
        if(isset($arrConst[$naxt])){
            $array[0]["orders"]["next_status"] = $naxt;
            $array[0]["orders"]["next_status_name"] = $arrConst[$naxt];
        }else{
            $array[0]["orders"]["next_status"] = "-0";
            $array[0]["orders"]["next_status_name"] = $arrConst[0];
        }

        return $array[0];
    }

    /*
     * 登録更新処理
    */
    function isSave($data){

        $arrPrefCode = Configure::read('arrPrefCode');
        if(isset($arrPrefCode[$data["orders"]["pref"]])){
            $data["orders"]["pref"] = $arrPrefCode[$data["orders"]["pref"]];
        }
        if(isset($arrPrefCode[$data["orders"]["delivery_pref"]])){
            $data["orders"]["delivery_pref"] = $arrPrefCode[$data["orders"]["delivery_pref"]];
        }

        return $this->save($data['orders'],false);
    }

    /*
     * ページング
     */
    function setWhere($param){
        $this->where = null;
        if(!empty($param)){
            foreach($param as $key => $val){
                if(!empty($this->where)) $this->where .= " AND ";
                if($key == "credit_payment_flg"){
                    $this->where .= " credit_payment_flg = $val ";
                }
            }
            $this->where = " AND ".$this->where;
        }
    }

    var $sort_value = null;

    /*
     * ページング
    */
    function setWhereSortVal($param){
        $this->sort_value = $param;
    }

    /*
     * ページング 検索フリーワード
    */
    var $free_word = null;
    function setWhereFreewords($param){

        $line = null;
        $arrKeyWord = trim($param);
        $arrKeyWord = addslashes ( $arrKeyWord );
        $arrKeyWord = str_replace("　"," ",$arrKeyWord);
        $arrKeyWord = explode(" ",$arrKeyWord);
        foreach($arrKeyWord as $val){
            if(!empty($line)) $line .= " OR ";
            $line .= " code LIKE '%".$val."%'\n ";
            $line .= " OR name LIKE '%".$val."%'\n ";
            $line .= " OR name_kana LIKE '%".$val."%'\n ";
            $line .= " OR company LIKE '%".$val."%'\n ";
            $line .= " OR email LIKE '%".$val."%'\n ";
        }
        $where = " AND ( ".$line." )\n ";
        $this->free_word = $where;
    }

    /*
     * ページング 期間 開始年月日
    */
    var $from_date = null;
    function setWhereStartDate($param){
        $from_date = null;
        if(isset($param["y"])) $year = $param["y"];
        if(isset($param["m"])) $month = $param["m"];
        if(isset($param["d"])) $day = $param["d"];
        if(empty($year)) $year = "0000";
        if(empty($month)) $month = "00";
        if(empty($day)) $day = "00";
        if($year != "0000" and $month != "00" and $day != "00"){
            $from_date = " AND created > '".$year."-".$month."-".$day." 00:00:00' ";
        }
        $this->from_date = $from_date;
    }

    /*
     * ページング 期間 終了年月日
    */
    var $to_date = null;
    function setWhereEndDate($param){
        $to_date = null;
        if(isset($param["y"])) $year = $param["y"];
        if(isset($param["m"])) $month = $param["m"];
        if(isset($param["d"])) $day = $param["d"];
        if(empty($year)) $year = "0000";
        if(empty($month)) $month = "00";
        if(empty($day)) $day = "00";
        if($year != "0000" and $month != "00" and $day != "00"){
            $to_date = " AND created <= '".$year."-".$month."-".$day." 23:59:59' ";
        }
        $this->to_date = $to_date;
    }

    /*
     * ページング
    */
    function getPagingEntity($disp_num,$pgnum){

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "   SELECT * ";
        $sql .=  "     FROM orders ";
        $sql .=  "    WHERE del_flg <= 0 ";
        if(!empty($this->free_word)){
            $sql .=  $this->free_word;
        }
        if(!empty($this->from_date)){
            $sql .=  $this->from_date;
        }
        if(!empty($this->to_date)){
            $sql .=  $this->to_date;
        }
        if(!empty($this->sort_value)){
            if($this->sort_value == "rank"){
                $sql .=  "   ORDER BY id DESC ";
            }else if($this->sort_value == "date"){
                $sql .=  "   ORDER BY created DESC ,modified DESC ";
            }else if($this->sort_value == "name_desc"){
                $sql .=  "   ORDER BY name_kana DESC ";
            }else if($this->sort_value == "name_asc"){
                $sql .=  "   ORDER BY name_kana ASC ";
            }else if($this->sort_value == "payment_desc"){
                $sql .=  "   ORDER BY payment DESC ";
            }else if($this->sort_value == "payment_asc"){
                $sql .=  "   ORDER BY payment ASC ";
            }else if($this->sort_value == "status_desc"){
                $sql .=  "   ORDER BY status DESC ";
            }else if($this->sort_value == "status_asc"){
                $sql .=  "   ORDER BY status ASC ";
            }
        }else{
            $sql .=  "   ORDER BY created DESC ";
        }

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $arrRes["list"][$key] = $row["orders"];

        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        return $arrRes;
    }

    /*
     * 再計算処理
    */
    function reCalc($order_id,$delivery_charge = null,$fee = null,$redeem_point = null){

        // SQL処理
        $sql =  "SELECT sum(total) as total FROM order_details WHERE order_id = '$order_id' AND del_flg <= 0";
        $array = $this->query($sql);
        $sum_total = $array[0][0]["total"];

        // SQL処理
        $sql =  "SELECT * FROM orders WHERE id = '$order_id' AND del_flg <= 0";
        $array = $this->query($sql);
        $pref = $array[0]["orders"]["delivery_pref"];
        if(empty($pref)) $pref = $array[0]["orders"]["pref"];
        $payment = $array[0]["orders"]["payment"];

        $point = $redeem_point;
        if(is_null($redeem_point)){
            $point = $array[0]["orders"]["redeem_point"];
        }
        if(is_null($delivery_charge)){

            $Constants = ClassRegistry::init('Constants');
            $DataLeaves = ClassRegistry::init('DataLeaves');
            $arrMasterData = $DataLeaves->getArrMasterData();

            $pref_name = null;
            if(isset($arrMasterData["flat"]["pref"][$pref])){
                $pref_name = $arrMasterData["flat"]["pref"][$pref];
            }
            $charge = $Constants->getConstValue("pref_delivery_charge",$pref_name);

            $limit_total = $Constants->getConstValue("delivery_charge","送料無料の購入金額");

            if($limit_total < $sum_total){
                $charge = 0;
            }

            $delivery_charge = intval($charge);

        }
        if(is_null($fee)){

            if($payment == 'payment03'){
                $fee = $Constants->getConstValue("cash_on_delivery_chage","> 0");
                $fee = intval($fee);
            }

        }

        // 登録処理
        $data["id"] = $order_id;
        $data["fee"] = $fee;
        $data["delivery_charge"] = $delivery_charge;
        $data["redeem_point"] = $point;
        $data["total"] = $sum_total + $delivery_charge + $fee - $point;
        $this->save($data, true, array('delivery_charge','fee','redeem_point','total','modified'));

    }

    /*
     * 購入履歴
     */
    function getOrderHistorysByCustomerId($customer_id){

        $this->bindModel(array('hasMany' => array(
                'OrderDetail' => array(
                    'className' => 'OrderDetail',
                    'foreignKey' => 'order_id'
                ))));

        // SQL処理
        $conditions = array('del_flg' => 0,'customer_id' => $customer_id);
        $arrRes = $this->find('all',array('conditions' => $conditions, 'order' => 'created DESC'));
        return $arrRes;
    }

    /*
     * 購入履歴詳細
    */
    function orderHistorysById($order_id){

        $arrRes = null;

        // SQL処理
        $sql =  "SELECT * FROM orders WHERE id = '$order_id' AND del_flg <= 0 ORDER BY created DESC";
        $array = $this->query($sql);
        $arrRes["orders"] = $array[0]["orders"];

        $sql2  =  "";
        $sql2 .=  "    SELECT * ";
        $sql2 .=  "      FROM order_details ";
        $sql2 .=  " LEFT JOIN merch_items ";
        $sql2 .=  "        ON order_details.merch_item_id = merch_items.id ";
        $sql2 .=  "     WHERE order_details.order_id = '$order_id' ";
        $sql2 .=  "       AND order_details.del_flg <= 0";
        $array2 = $this->query($sql2);
        $arrRes["list"] = $array2;

        $maisu = 0;
        $sub = 0;
        foreach($array2 as $val){
            $maisu = $maisu + $val["order_details"]["quantity"];
            $sub = $sub + $val["order_details"]["total"];
        }
        $arrRes["orders"]["maisu"] = $maisu;
        $arrRes["orders"]["sub"] = $sub;

        return $arrRes;
    }

    //    /*
    //     * メール送信用情報
    //     */
    //    function getMailOrdersById($id){
    //
    //        // SQL処理
    //        $sql =  "SELECT * FROM orders WHERE id = '$id' AND del_flg <= 0";
    //        $array = $this->query($sql);
    //        $arrRes = $array[0]["orders"];
    //
    //        $sql =  "SELECT * FROM order_details WHERE order_id = '$id' AND del_flg <= 0";
    //        $array = $this->query($sql);
    //        foreach($array as $i => $v){
    //            $arrRes["detail"][$i] = $v;
    //        }
    //
    //        return $arrRes;
    //    }

    /*
     * 会員別新規受注登録
    */
    function addOrderByCustIdAddressId($customer_id,$address_id){

        // SQL処理
        $sql =  "SELECT * FROM customers WHERE id = '$customer_id' AND del_flg <= 0";
        $array = $this->query($sql);

        $data = array();
        if(count($array) > 0){
            $row = $array[0]["customers"];
            $data["orders"]["customer_id"]        = $row["id"];
            $data["orders"]["member_id"]          = $row["member_id"];
            $data["orders"]["code"]               = date("ymdHis").mt_rand(1111,9999);
            $data["orders"]["payment"]            = 2;
            $data["orders"]["status"]             = "0";
            $data["orders"]["name"]               = $row["name"];
            $data["orders"]["name_kana"]          = $row["name_kana"];
            $data["orders"]["email"]              = $row["email"];
            $data["orders"]["email_confirm"]      = $row["email"];
            $data["orders"]["created"]            = date("Y-m-d H:i:s");
        }else{
            return null;
        }


        // SQL処理
        $sql =  "SELECT * FROM customer_addresses WHERE customer_id = '$customer_id' AND del_flg <= 0";
        $array = $this->query($sql);

        if(count($array) > 0){
            $row = $array[0]["customer_addresses"];
            $data["orders"]["customer_postcode"] = $row["postcode"];
            $data["orders"]["pref"]               = $row["pref"];
            $data["orders"]["customer_address1"]  = $row["city"];
            $data["orders"]["customer_address2"]  = $row["address"];
            $data["orders"]["customer_tel"]       = $row["tel"];
            $data["orders"]["customer_mobile"]    = $row["mobile"];
        }else{
            return null;
        }

        // SQL処理
        $sql =  "SELECT * FROM customer_addresses WHERE id = '$address_id' AND del_flg <= 0";
        $array = $this->query($sql);
        if(count($array) > 0){
            $row = $array[0]["customer_addresses"];
            $data["orders"]["delivery_name"]      = $row["name"];
            $data["orders"]["delivery_name_kana"] = $row["name_kana"];
            $data["orders"]["delivery_postcode"]  = $row["postcode"];
            $data["orders"]["delivery_pref"]      = $row["pref"];
            $data["orders"]["delivery_address1"]  = $row["city"];
            $data["orders"]["delivery_address2"]  = $row["address"];
            $data["orders"]["delivery_tel"]       = $row["tel"];
        }else{
            return null;
        }

        if(!$this->isSave($data,false)){
            return null;
        }

        $order_id = $this->getLastInsertID();
        return $order_id;
    }

    /*
     * クレジットフラグ更新
     */
    function chgCreditPaymentOrderId($order_id,$flg){

        $data["id"]     = $order_id;
        $data["credit_payment_flg"] = $flg;
        return $this->save($data,false);

    }

    /*
     * ステータス更新
    */
    function chgStatusOrderId($order_id,$status){
        if($status === ""){
            $this->error_messages["status"] = "選択されていません。";
            return false;
        }
        $data["id"]     = $order_id;
        $data["status"] = $status;
        return $this->save($data, true, array("status","modified"));

    }

    /*
     * 受注ステータス配列取得
    */
    function getOrderStatusOptions($order_id = null){

        $status = $this->getOneFieldById($order_id,"status");

        // SQL処理
        $sql = "";
        $sql .= "   SELECT * ";
        $sql .= "     FROM constants ";
        $sql .= "    WHERE del_flg <= 0 ";
        $sql .= "      AND level = 1 ";
        $sql .= "      AND const_name = 'order_status' ";
        if($status == 5){
            $sql .= "      AND value = '5' ";
        }
        $sql .= " ORDER BY rank ";
        $array = $this->query($sql);

        $sql = "SELECT * FROM order_details WHERE del_flg <= 0 AND order_id = '$order_id' ";
        $array_1 = $this->query($sql);
        $cnt = count($array_1);

        $arrRes = array();
        foreach($array as $row){
            $value = $row["constants"]["value"];
            if($cnt > 0 && $value >= 0){
                $arrRes[$row["constants"]["value"]] = $row["constants"]["name"];
            }
            //            if($cnt == 0 && ($value == 0 || $value == 999)){
            if($cnt == 0){
                $arrRes[$row["constants"]["value"]] = $row["constants"]["name"];
            }
        }

        return $arrRes;
    }

    /*
     * カート内購入者情報
    */
    function getCustomerByCustId($customer_id){

        $data = $this->getFields();

        App::import('Component', 'Utility');
        $Utility = new UtilityComponent(new ComponentCollection());
        $arrMasterData = $Utility->getMasterData();

        // SQL処理
        $sql =  "SELECT * FROM customers WHERE id = '$customer_id' AND id > 0 AND del_flg <= 0";
        $array = $this->query($sql);

        if(count($array) > 0){
            $row = $array[0]["customers"];
            $data["company"]        = $row["company"];
            $data["name"]           = $row["name"];
            $data["name_kana"]      = $row["name_kana"];
            $data["email"]          = $row["email"];
            $data["m_email"]        = $row["m_email"];
        }

        // SQL処理
        $sql =  "SELECT * FROM customer_addresses WHERE customer_id = '$customer_id' AND customer_id > 0 AND type = 'home' AND del_flg <= 0";
        $array = $this->query($sql);

        if(count($array) > 0){
            $row = $array[0]["customer_addresses"];
            $data["customer_postcode"]  = $row["postcode"];
            $data["pref"]               = $arrMasterData['flat']['pref'][$row["pref"]];
            $data["customer_address1"]  = $row["city"];
            $data["customer_address2"]  = $row["address"];
            $data["customer_tel"]       = $row["tel"];
            $data["customer_mobile"]    = $row["mobile"];
        }

        // SQL処理
        $sql =  "SELECT * FROM customer_addresses WHERE customer_id = '$customer_id' AND customer_id > 0 AND type = 'delivery' AND del_flg <= 0";
        $array = $this->query($sql);
        if(count($array) > 0){
            $row = $array[0]["customer_addresses"];
            $data["delivery_name"]      = $row["name"];
            $data["delivery_name_kana"] = $row["name_kana"];
            $data["delivery_postcode"]  = $row["postcode"];
            $data["delivery_pref"]      = $arrMasterData['flat']['pref'][$row["pref"]];
            $data["delivery_address1"]  = $row["city"];
            $data["delivery_address2"]  = $row["address"];
            $data["delivery_tel"]       = $row["tel"];
        }

        return $data;
    }

    /*
     * 期間年
    */
    function getStartYear($current_year){
        $current_year;
        // SQL処理
        $sql =  "SELECT min(created) as min FROM orders WHERE del_flg <= 0";
        $array = $this->query($sql);
        $min = explode("-",$array[0][0]["min"]);
        return @intval($min[0]) - $current_year - 1;
    }

}
?>