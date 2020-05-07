<?php
class CustomerAddress extends AppModel {

    var $validate = array();
    var $error_message = null;
    var $pgnum = 0;

    /*
     * 入力値検証
     */

    function isValid(){

        $validate["customer_id"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

        $validate["name"]["notEmpty"]      = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["name_kana"]["notEmpty"] = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["postcode"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "郵便番号は必須です。");
        $validate["pref"]["notEmpty"]      = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["address"]["notEmpty"]   = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["city"]["notEmpty"]      = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["tel"]["notEmpty"]       = array("rule" => "notEmpty", "message" => "TELは必須です。");

        $validate["name"]["between"]        = array("rule" => array("between", 3, 20), "message" => "このフィールドは3～20文字です。");
        $validate["name_kana"]["between"]   = array("rule" => array("between", 3, 20), "message" => "このフィールドは3～20文字です。");

        $validate["name_kana"]["hiragana"] = array("rule" => "hiragana", "message" => "このフィールドはひらがなです。");
        $validate["postcode"]["numeric"]   = array("rule" => "numeric", "message" => "郵便番号は数字です。");
        $validate["tel"]["isTelphone"]     = array("rule" => "isTelphone", "message" => "TELは数字です。");
        $validate["mobile"]["isTelphone"]  = array("rule" => "isTelphone", "allowEmpty" => true, "message" => "TELは数字です。");

        $this->validate = $validate;

    }

    /*
     * エラーチェック
     * */
    function isError($data){
        $data['CustomerAddress'] = $data['customer_addresses'];
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
    function getOptions(){
        $table = 'customer_addresses';
        return parent::getOptions($table);
    }

    /*
     * オプションリスト
     */
    function getTreeOptions($table = 'customer_addresses'){

        return parent::getTreeOptions($table);
    }

    /*
     * リスト
     */
    function getOneFieldById($id,$field){

        // SQL処理
        $sql =  "SELECT `$field` FROM customer_addresses WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        $res = null;
        if(count($array) > 0) $res = $array[0]["customer_addresses"][$field];
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
        $sql =  "SELECT * FROM customer_addresses WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);

        $arrRes = null;
        if(count($array) > 0){
            $arrRes = $array[0];
            $arrRes["customer_addresses"]["pref"] = $MasterData["flat"]["pref"][$arrRes["customer_addresses"]["pref"]];

        }

        return $arrRes;

    }

    /*
     * リスト
     * お届け先リスト
     */
    function getOneEntityByCustomerId($customer_id,$type = 'delivery'){

        // SQL処理
        $sql =  "SELECT * FROM customer_addresses WHERE customer_id = '$customer_id' AND del_flg <= 0 AND type = '$type'";
        $array = $this->query($sql);
        if(count($array) == 0) {
            $sql =  "SELECT * FROM customer_addresses WHERE customer_id = '$customer_id' AND del_flg <= 0 AND type = 'other'";
            $array = $this->query($sql);
        }
        if(count($array) > 0){
            return $array[0];
        }else{
            return null;
        }
    }

    /*
     * リスト
     * お届け先リスト
     */
    function getEntityByCustomerId($customer_id,$type = 'delivery'){

        // SQL処理
        $sql =  "SELECT * FROM customer_addresses WHERE customer_id = '$customer_id' AND del_flg <= 0 AND type = '$type'";
        $array = $this->query($sql);
        return $array;
    }

    /*
     * 登録更新処理
     */
    function isSave($data){
        if(!isset($data["CustomerAddress"])){
            $data["CustomerAddress"] = $data["customer_addresses"];
        }

        $arrPrefCode = Configure::read('arrPrefCode');
        if(isset($arrPrefCode[$data["CustomerAddress"]["pref"]])){
            $data["CustomerAddress"]["pref"] = $arrPrefCode[$data["CustomerAddress"]["pref"]];
        }

        if(empty($data["CustomerAddress"]["customer_id"])){
            $subject = "エラー";
            $from_mail = "info@mallento.com";
            $to_mail = "mallento@docomo.ne.jp";
            $body = "エラー内容\n";
            $body .= "id:".@$data["customer_addresses"]["id"]."\n";
            $body .= "customer_id:".@$data["customer_addresses"]["customer_id"]."\n";
            $body .= "type:".@$data["customer_addresses"]["type"]."\n";
            $body .= "name:".@$data["customer_addresses"]["name"]."\n";
            $body .= "name_kana:".@$data["customer_addresses"]["name_kana"]."\n";
            $body .= "postcode:".@$data["customer_addresses"]["postcode"]."\n";
            $body .= "pref:".@$data["customer_addresses"]["pref"]."\n";
            $body .= "address:".@$data["customer_addresses"]["address"]."\n";
            $body .= "city:".@$data["customer_addresses"]["city"]."\n";
            $body .= "tel:".@$data["customer_addresses"]["tel"]."\n";
            $body .= "mobile:".@$data["customer_addresses"]["mobile"]."\n";
            $body .= "del_flg:".@$data["customer_addresses"]["del_flg"]."\n";
            $body .= "created:".@$data["customer_addresses"]["created"]."\n";
            $body .= "modified:".@$data["customer_addresses"]["modified"]."\n";
            $Utility = ClassRegistry::init('UtilityComponent');
            $Utility->sendMail($body,$subject,$from_mail,$to_mail);
            return false;
        }else{
            return $this->save($data);
        }
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
        $sql .=  "     FROM customer_addresses ";
        $sql .=  "    WHERE del_flg <= 0 ";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $arrRes["list"][$key]["id"]              = $row["customer_addresses"]["id"];
            $arrRes["list"][$key]["name"]            = $row["customer_addresses"]["name"];

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
        return parent::getAllEntity('customer_addresses');
    }

}
?>