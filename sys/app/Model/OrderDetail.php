<?php
class  OrderDetail extends AppModel {
    var $validate = array();

    /*
     * 入力値検証
     */

    function isValid(){

        $validate["price"]["numeric"]           = array("rule" => "numeric", "message" => "このフィールドは数字です。");
        $validate["quantity"]["numeric"]        = array("rule" => "numeric", "message" => "このフィールドは数字です。");
        $validate["total"]["numeric"]           = array("rule" => "numeric", "message" => "このフィールドは数字です。");
        $validate["merch_basic_id"]["numeric"]  = array("rule" => "numeric", "message" => "このフィールドは数字です。");
        $validate["merch_item_id"]["numeric"]   = array("rule" => "numeric", "message" => "このフィールドは数字です。");

        $validate["price"]["notEmpty"]          = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["quantity"]["notEmpty"]       = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["total"]["notEmpty"]          = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["merch_basic_id"]["notEmpty"] = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["merch_item_id"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

        $this->validate = $validate;

    }

    /*
     * エラーチェック
     * */
    function isError($data){
        return parent::isError($data,'order_details');
    }

    /*
     * フィールド
     */
    function getFields($table = 'order_details'){
        return parent::getFields($table);
    }

    /*
     * オプションリスト
     */
    function getOptions(){
        $table = 'order_details';
        $where = null;
        return parent::getOptions($table,$where);
    }

    /*
     * オプションリスト
     */
    function getTreeOptions($table = 'order_details'){

        return parent::getTreeOptions($table);
    }

    /*
     * リスト
     */
    function getOneFieldById($id,$field){

        $table = 'order_details';
        return parent::getOneFieldById($table,$id,$field);
    }

    /*
     * リスト
     */
    function getOneEntityById($id){

        // SQL処理
        $sql = "";
        $sql .= "    SELECT * ";
        $sql .= "      FROM order_details ";
        $sql .= " LEFT JOIN orders ";
        $sql .= "        ON orders.id = order_details.order_id ";
        $sql .= "     WHERE order_details.id = '$id' ";
        $sql .= "       AND order_details.del_flg = 0";
        $array = $this->query($sql);
        return $array[0];
    }

    /*
     * リスト
     */
    function getListEntityWhere($order_id){

        $table = 'order_details';
        $where = "order_id = '$order_id'";
        return parent::getListEntityWhere($table,$where);

    }

    /*
     * 登録更新処理
     */
    function isSave($data){

        return $this->save($data['order_details']);
    }

    function setWhere($param){
        $this->where = $param;
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
        $sql .=  "       FROM order_details ";
        $sql .=  " INNER JOIN orders ";
        $sql .=  "         ON order_details.order_id = orders.id ";
        $sql .=  "        AND orders.del_flg = 0 ";
        $sql .=  "      WHERE order_details.del_flg = 0 ";

        $sql .=  " ORDER BY orders.modified DESC ";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $arrRes["list"][$key]["OrderDetail"] = $row["order_details"];
            $arrRes["list"][$key]["Order"]       = $row["orders"];

        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        return $arrRes;
    }

    /*
     * リスト
     */
    function getListDetailsByOrderId($order_id,$ext_flg = false){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM order_details ";
        $sql .=  " INNER JOIN merch_items ";
        $sql .=  "         ON order_details.merch_item_id = merch_items.id ";
        $sql .=  "      WHERE order_details.del_flg <= 0 ";
        $sql .=  "        AND order_details.order_id = '$order_id' ";
        $array = $this->query($sql);

        $arrRes = array();
        foreach($array as $key => $row){

            $arrRes[$key] = $row["order_details"];
            $arrRes[$key]["code"] = $row["merch_items"]["code"];

        }

        if($ext_flg){
            // SQL処理
            $sql =  "";
            $sql .=  "     SELECT count(*) as cnt ";
            $sql .=  "       FROM order_details ";
            $sql .=  "      WHERE order_details.del_flg <= 0 ";
            $sql .=  "        AND order_details.merch_basic_id = '-1' ";
            $sql .=  "        AND order_details.order_id = '$order_id' ";
            $array = $this->query($sql);
            $cnt = $array[0][0]["cnt"];
            $cnt = 20 - $cnt;
            for($i = 0; $i < $cnt; $i++){
                $data["order_details"]["id"] = null;
                $data["order_details"]["order_id"] = $order_id;
                $data["order_details"]["merch_basic_id"] = '-1';
                $data["order_details"]["merch_item_id"] = '-1';
                $this->isSave($data);
            }

            // SQL処理
            $sql =  "";
            $sql .=  "     SELECT * ";
            $sql .=  "       FROM order_details ";
            $sql .=  "      WHERE order_details.del_flg <= 0 ";
            $sql .=  "        AND order_details.merch_basic_id = '-1' ";
            $sql .=  "        AND order_details.order_id = '$order_id' ";
            $array = $this->query($sql);
            foreach($array as $key => $row){
                $arrRes[] = $row["order_details"];
            }

        }

        return $arrRes;
    }

    /*
     * カスタマー毎購入履歴取得
     */
    function getListDetailsByCustomerId($customer_id){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM order_details ";
        $sql .=  " INNER JOIN orders ";
        $sql .=  "         ON order_details.order_id = orders.id ";
        $sql .=  "        AND orders.del_flg <= 0 ";
        $sql .=  " INNER JOIN merch_items ";
        $sql .=  "         ON order_details.merch_item_id = merch_items.id ";
        $sql .=  "        AND merch_items.del_flg <= 0 ";
/*
        $sql .=  " INNER JOIN merch_basics ";
        $sql .=  "         ON merch_items.merch_basics_id = merch_basics.id ";
        $sql .=  "        AND merch_basics.del_flg <= 0 ";
*/
        $sql .=  "      WHERE order_details.del_flg <= 0 ";
        $sql .=  "        AND orders.customer_id = '$customer_id' ";
        $sql .=  "        ORDER BY order_details.created DESC ";
        $array = $this->query($sql);

        return $array;
    }

    /*
     * 小計再計算
     */
    function updSum($id,$price,$quantity,$code = null,$basic_name = null,$merch_basic_id = null){

        $quantity = round($quantity);
        $price = round($price);
        $total = $price * $quantity;
        $code = Sanitize::clean($code);
        $basic_name = Sanitize::clean($basic_name);

        // SQL処理
        if($merch_basic_id == -1){
            $sql = "UPDATE order_details SET code = '$code' , basic_name = '$basic_name' , price = '$price' , quantity = '$quantity' , total = '$total' , modified = now() WHERE order_details.id = '$id'";
        }else{
            $sql = "UPDATE order_details SET price = '$price' , quantity = '$quantity' , total = '$total' , modified = now() WHERE order_details.id = '$id'";
        }
        $this->query($sql);

    }

}
?>