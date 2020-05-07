<?php
class Customer extends AppModel {

    var $validate = array();
    var $error_message = null;
    var $pgnum = 0;

    /*
     * 入力値検証
    */

    function isValid(){

        $validate["name"]["notEmpty"]       = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["name_kana"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["email"]["notEmpty"]      = array("rule" => "notEmpty", "message" => "必須項目です。");

        $validate["name"]["between"]        = array("rule" => array("between", 3, 20), "message" => "このフィールドは3～20文字です。");
        $validate["name_kana"]["between"]   = array("rule" => array("between", 3, 20), "message" => "このフィールドは3～20文字です。");
        $validate["company"]["between"]   = array("rule" => array("between", 3, 50), "allowEmpty" => true, "message" => "このフィールドは3～50文字です。");

        $validate["date_of_birth"]["date"]        = array("rule" => "date", "message" => "正しい日付を入力してください。");
        $validate["name_kana"]["hiragana"]        = array("rule" => "hiragana", "message" => "このフィールドはひらがなです。");
        $validate["age"]["numeric"]               = array("rule" => "numeric", "allowEmpty" => true, "message" => "年齢は数字です。");
        $validate["mailmag_mail"]["emailExt"]     = array("rule" => "emailExt", "allowEmpty" => true, "message" => "不正なメールアドレスです。");
        $validate["email"]["email"]               = array("rule" => "email", "allowEmpty" => true, "message" => "不正なメールアドレスです。");
        $validate["email"]["isUnique"]            = array("rule" => "isUnique", "allowEmpty" => true, "message" => "そのID(メール)は既に登録済みです。");

        //        $validate["family_name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
        $validate["date_of_birth"]["notEmpty"]     = array("rule" => "all", "message" => "必須項目です。");
        //        $validate["gender"]["required"]            = array("rule" => "notEmpty", "required" => true, "message" => "必須項目です。");
        //        $validate["reminder"]["required"]          = array("rule" => "notEmpty", "required" => true, "message" => "必須項目です。");

        //        $validate["age"]["notEmpty"]               = array("rule" => "notEmpty", "message" => "必須項目です。");
        //        $validate["date_of_birth"]["notEmpty"]     = array("rule" => "notEmpty", "message" => "必須項目です。");
        //        $validate["reminder"]["notEmpty"]          = array("rule" => "notEmpty", "message" => "必須項目です。");
        //        $validate["reminder_answer"]["notEmpty"]   = array("rule" => "notEmpty", "message" => "必須項目です。");
        //        $validate["code"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
        //        $validate["code"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
        $this->validate = $validate;

    }

    /*
     * エラーチェック
    * */
    function isError($data){
        $data['Customer'] = $data['customers'];
        $this->isValid();
        $this->set($data);
        return $this->validates();
    }

    /*
     * フィールド
    */
    function getFields($where = null){
        return parent::getFields($where);
    }

    /*
     * オプションリスト
    */
    function getOptions($where = ""){
        return parent::getOptions($where);
    }

    /*
     * オプションリスト
    */
    function getTreeOptions($level = 2, $where = ""){

        return parent::getTreeOptions($level, $where);
    }

    /*
     * リスト
    */
    function getOneFieldById($id,$field){

        // SQL処理
        $sql =  "SELECT `$field` FROM customers WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        $res = null;
        if(count($array) > 0) $res = $array[0]["customers"][$field];
        return $res;
    }

    /*
     * リスト
    */
    function getOneEntityById($id){

        App::import('Component', 'Utility');
        $Utility = new UtilityComponent(new ComponentCollection());
        $MasterData = $Utility->getMasterData();

        // SQL処理
        $sql =  "SELECT * FROM customers WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);

        $arrRes = null;
        if(count($array) > 0){
            $arrRes = $array[0];

            // SQL処理
            $customer_id = $array[0]["customers"]["id"];
            $sql = "SELECT * FROM customer_addresses WHERE del_flg <= 0 AND customer_id = '$customer_id' AND type = 'home'";
            $arr_2 = $this->query($sql);
            if(count($arr_2) > 0){
                $arrRes["customer_addresses"] = $arr_2[0]["customer_addresses"];
                $arrRes["customer_addresses"]["pref"] = $MasterData["flat"]["pref"][$arrRes["customer_addresses"]["pref"]];
            }

            // SQL処理
            $sql3 = "SELECT * FROM customer_addresses WHERE del_flg <= 0 AND customer_id = '$customer_id' AND type = 'delivery'";
            $arr_3 = $this->query($sql3);
            if(count($arr_3) > 0){
                $arrRes["delivery_addresses"] = $arr_3[0]["customer_addresses"];
                $arrRes["delivery_addresses"]["pref"] = $MasterData["flat"]["pref"][$arrRes["delivery_addresses"]["pref"]];
            }

            $date_of_birth = $arrRes["customers"]["date_of_birth"];
            $arrRes["customers"]["date_of_birth"] = substr(str_replace("-","/",$date_of_birth),0,10);

            // 年齢再計算
            $arrRes["customers"]["age"] = $Utility->calcAge($date_of_birth);

            // ポイント有効期限
            $modified = $arrRes["customers"]["modified"];

            $arrRes["customers"]["point_limit_date"] = $Utility->calcAge($date_of_birth);

        }

        return $arrRes;
    }

    /*
     * リスト
    */
    function getOneEntityByUserId($email){

        // SQL処理
        $sql  = "";
        $sql .= "    SELECT * ";
        $sql .= "      FROM customers ";
        $sql .= "INNER JOIN members ";
        $sql .= "        ON customers.member_id = members.id ";
        $sql .= "       AND members.del_flg <= 0 ";
        $sql .= "     WHERE ( members.user_id = '$email' ";
        $sql .= "          OR customers.m_email = '$email' ";
        $sql .= "          OR customers.email = '$email' ) ";
        $sql .= "       AND customers.del_flg <= 0";
        $sql .= "       AND customers.status = 1";
        $array = $this->query($sql);

        return $array;
    }

    /*
     * リスト
    */
    function getOneEntityByMemberId($member_id){

        // SQL処理
        $sql =  "SELECT * FROM customers WHERE member_id = '$member_id' AND del_flg <= 0";
        $array = $this->query($sql);

        $arrRes = null;
        if(count($array) > 0){
            $arrRes = $array[0];

            // SQL処理
            $customer_id = $array[0]["customers"]["id"];
            $sql = "SELECT * FROM customer_addresses WHERE del_flg <= 0 AND customer_id = '$customer_id' ";
            $arr_2 = $this->query($sql);
            foreach($arr_2 as $i => $row){
                $arrRes["customer_addresses"][$i] = $row["customer_addresses"];
            }
            $date_of_birth = $arrRes["customers"]["date_of_birth"];
            $arrRes["customers"]["date_of_birth"] = substr(str_replace("-","/",$date_of_birth),0,10);

            // 年齢再計算
//            $Utility = ClassRegistry::init('UtilityComponent');
            App::import('Component', 'Utility');
            $Utility = new UtilityComponent(new ComponentCollection());
            $arrRes["customers"]["age"] = $Utility->calcAge($date_of_birth);

            //            // ポイント有効期限
            //            $modified = $arrRes["customers"]["modified"];
            //            $arrRes["customers"]["point_limit_date"] = $Utility->addYearDbDate($modified,1);

        }

        return $arrRes;
    }

    /*
     * メルマガ送信者リスト
    */
    function getMailList(){

        $validate["email"]["email"]  = array("rule" => "emailExt", "message" => "不正なメールアドレスです。");

        // SQL処理
        $sql  = "";
        $sql .= "    SELECT * ";
        $sql .= "      FROM customers ";
        $sql .= "     WHERE mail_mag != '' ";
        $sql .= "       AND mail_mag IS NOT NULL ";
        $sql .= "       AND mail_mag != '0' ";
        $sql .= "       AND mailmag_mail != '' ";
        $sql .= "       AND mailmag_mail IS NOT NULL ";
        $sql .= "       AND del_flg <= 0";
        $sql .= "       AND status = 1";
        $sql .= "       ORDER BY name_kana ";
        $array = $this->query($sql);

        $arrRes = null;
        if(count($array) > 0){
            foreach($array as $row){
                $row["customers"]["mailmag_mail"] = trim($row["customers"]["mailmag_mail"]);
                $this->set($row["customers"]);
                $this->validate = $validate;
                if(count($this->invalidFields()) == 0){
                    $arrRes[$row["customers"]["mailmag_mail"]] = $row["customers"]["name"];
                }
            }
        }
        return $arrRes;
    }

    /*
     * 登録更新処理
    */
    function isSave($data){
        if(empty($data['customers']['mailmag_mail'])) $data['customers']['mailmag_mail'] = $data['customers']['email'];
        return $this->save($data);
    }

    /*
     * ページング 検索条件
    */
    var $free_word = null;
    function setWhere($param){

        App::import('Component', 'Utility');
        $Utility = new UtilityComponent(new ComponentCollection());
        $MasterData = $Utility->getMasterData();

        $line = null;
        $pref = null;
        $where = null;
        if(!empty($param)){
        $arrKeyWord = trim($param);
        $arrKeyWord = addslashes ( $arrKeyWord );
        $arrKeyWord = str_replace("　"," ",$arrKeyWord);
        $arrKeyWord = explode(" ",$arrKeyWord);
        foreach($arrKeyWord as $val){

            if($val == "東京") $pref = "pref13";
            if($val == "北海道") $pref = "pref01";
            $pref = $MasterData["flat"]["pref"];
            foreach($pref as $code => $value){
                $value = str_replace("府","",$value);
                $value = str_replace("県","",$value);
                if($value == $val) $pref = $code;
            }

            if(!empty($line)) $line .= " OR ";
            $line .= " customers.name LIKE '%".$val."%'\n ";
            $line .= " OR customers.name_kana LIKE '%".$val."%'\n ";
            $line .= " OR customers.company LIKE '%".$val."%'\n ";
            $line .= " OR customers.email LIKE '%".$val."%'\n ";
            $line .= " OR customers.m_email LIKE '%".$val."%'\n ";
            $line .= " OR customer_addresses.pref LIKE '%".$pref."%'\n ";
            $line .= " OR customer_addresses.address LIKE '%".$val."%'\n ";
            $line .= " OR customer_addresses.city LIKE '%".$val."%'\n ";
            $line .= " OR customer_addresses.tel LIKE '%".$val."%'\n ";
        }
        $where = " AND ( ".$line." )\n ";
        }
        $this->free_word = $where;
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
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM customers ";
        $sql .=  " INNER JOIN customer_addresses ";
        $sql .=  "         ON customers.id = customer_addresses.customer_id ";
        $sql .=  "        AND customer_addresses.type = 'home' ";
        $sql .=  "        AND customer_addresses.del_flg <= 0 ";
        $sql .=  " INNER JOIN members ";
        $sql .=  "         ON members.id = customers.member_id ";
        $sql .=  "        AND members.del_flg = 0 ";
        $sql .=  "      WHERE customers.del_flg = 0 ";
        if(!empty($this->free_word)){
            $sql .=  $this->free_word;
        }
        $sql .=  "   ORDER BY customers.created DESC ";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $arrRes["list"][$key] = $row;

        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        return $arrRes;
    }

    /*
     * 有効全件リスト取得
    */
    function getAllEntity(){
        return parent::getAllEntity('customers');
    }

    /*
     * ポイント加算更新処理
    */
    function addPoint($data,$current_point = null){

        App::import('Model','Constants');
        $Constants = new Constants();
        //        App::import('Model','OrderDetails');
        //        $OrderDetails = new OrderDetails();
        $tax = $Constants->getConstValue("shop_info","消費税");
        $tax = intval($tax) * 0.01 + 1;

        $order_id = $data["orders"]["id"];
        $customer_id = $data["orders"]["customer_id"];

        // ポイント利用していない場合は現ポイントを取得
        if(is_null($current_point)) $current_point = $this->getOneFieldById($customer_id,"point");

        $sql =  "";
        $sql .=  "     SELECT sum(point) as total_point ";
        $sql .=  "       FROM order_details ";
        $sql .=  "      WHERE order_details.del_flg <= 0 ";
        $sql .=  "        AND order_details.order_id = '$order_id' ";
        $sql .=  "        AND order_details.point_flg = 0 ";
        echo $sql;
        $array = $this->query($sql);
        $total_point = $array[0][0]["total_point"];
        //echo "現ポイント".$current_point."<br>\n";
        echo "加算ポイント".$total_point."<br>\n";

        if($total_point > 0){
            $total_point = round($total_point / $tax);
            $udata["id"]    = $customer_id;
            $udata["point"] = $total_point + intval($current_point);
            $this->save($udata, true, array("point","modified"));

            $sql = "UPDATE order_details SET point_flg = 1 WHERE order_details.order_id = '$order_id' ";
            $this->query($sql);
        }
    }

    /*
     * 購入時ポイント更新処理
    */
    function updPoint($id,$point){

        $total_point = null;
        if($point > 0){
            $curr_point = $this->getOneFieldById($id,"point");
            $total_point = $curr_point - $point;
            $data["id"]    = $id;
            $data["point"] = $total_point;
            $this->save($data, true, array("point","modified"));
        }
        return $total_point;
    }

    /*
     * 退会処理
    */
    function withdrawal($member_id){

        $sql = "SELECT * FROM customers WHERE del_flg <= 0 AND member_id = '$member_id' ";
        $array = $this->query($sql);

        // 退会処理
        $data["id"] = $array[0]["customers"]["id"];
        $data["status"] = 3;
        $data["del_flg"] = 1;
        $this->save($data, true, array('status','del_flg','modified'));
    }

    /*
     * メルマガ対象者アドレス
    */
    function getMailAdrs(){

        $sql = "SELECT email FROM customers WHERE del_flg <= 0 AND status = 1 AND mail_mag = 1 ";
        $array = $this->query($sql);
        $arrRes = null;
        foreach($array as $row){
            $arrRes[] = $row["customers"]["email"];
        }
        return $arrRes;
    }

    /*
     * パスワード変更処理
    */
    function chgPassWord($id,$pass){

        $sql = "UPDATE customers SET password = '$pass' WHERE id = '$id' ";
        $this->query($sql);

    }

    /*
     * メルマガ対象者アドレス
    */
    function exe(){

        $sql = "SELECT * FROM customers WHERE del_flg <= 0 AND point = 0 ";
        $array = $this->query($sql);
        $i = 0;
        foreach($array as $row){
            $customer_id = $row["customers"]["id"];
            $next_id = $customer_id + 1;

            $sql2 = "SELECT * FROM customers WHERE id = $next_id AND point > 0 AND member_id = 0 ";
            $array2 = $this->query($sql2);
            if(count($array2) > 0){
                $point = $array2[0]["customers"]["point"];
                $sql3  = "UPDATE customers SET point = $point WHERE id = $customer_id ";
                $this->query($sql3);
                ++$i;
                //                echo $sql3."\n";
                //                echo $sql2."\n";
            }
        }
        echo $i."\n";
    }

}
?>