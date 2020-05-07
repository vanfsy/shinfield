<?php
class CartComponent extends Component {

//    var $Utility;

//    var $Session;

    public $components = array('Utility','Session','Auth');

/*
* カート情報
*/
    function cartInfo() {

        $arrRes = array();
        $list = $this->getList();
        $arrRes["list"] = $list;
        $total = $this->getSumTotal();
        $arrRes["total"] = $total;
        return $arrRes;

    }

/*
* 商品リスト
*/
    function getList() {

        $arrCart = $this->Session->read("cart");
        $arrRes = $arrCart;
        $sum_total = 0;
        $total_quantity = 0;
        if(count($arrCart) > 0){
            foreach($arrCart as $i => $row){
                $item_id = @$row["item_id"];
                $quantity = @$row["quantity"];
                $arrRes[$i]["item_name"] = @$row["item_name"];
                $price = @$row["price"];
                $arrRes[$i]["price"] = $price;
                $imgurl = @$row["imgurl"];
                $arrRes[$i]["imgurl"] = $imgurl;
                $total = $quantity * $price;
                $arrRes[$i]["total"] = $total;
                $sum_total = $sum_total + $total;
                $total_quantity = $total_quantity + $quantity;
            }
        }

        $point = $this->getPoint();
        if(empty($point)) $point = 0;

        $this->total = $sum_total;
        $this->sum_total = $sum_total - $point;
        $this->total_quantity = $total_quantity;
        return $arrRes;

    }

    var $sum_total;

    var $fee = 0;

    function getSumTotal() {
        return $this->sum_total + $this->delivery_charge + $this->fee;
    }

    var $total;

    function getTotal() {
        return $this->total;
    }

    function getTotalTax() {
        return $this->total - floor($this->total / 1.08);
    }

    var $total_quantity;

    function getTotalQuantity() {
        return $this->total_quantity;
    }

/*
* 商品リスト追加
*/
    function addList($data) {
//        $this->Session->write("cart",null);

        $is_error = true;
        if(count($data["cart"]) > 0){
            foreach($data["cart"] as $item_id => $quantity){
                if(!empty($quantity)){
                    if(is_numeric($quantity)){
                        $udata["item_id"] = $item_id;
                        $udata["quantity"] = $quantity;
                        $this->add($udata);
                    }else{
                        $is_error = false;
                        $this->err_message = "数量を入れて下さい。";
                    }
                }
            }
        }
        return $is_error;
    }

/*
* 商品追加
*/
    function add($data) {

        $arrCart = array();
        $arrCart = $this->Session->read("cart");

        $i = count($arrCart);

        $item_id = $data["item_id"];

        $Items = ClassRegistry::init('Item');
        $array = $Items->getDetail($item_id);

        $item_name = $array['Item']['title'];
        $price = $array['Item']['price'];
        $imgurl = $array['Item']['main_image_file_name'];
        $quantity = 1;
        if(is_numeric($data["quantity"])) $quantity = $data["quantity"];

        // オプションがある場合
        $options = null;
        if(isset($data["options"])){
            $options = null;
            foreach($data["options"] as $k => $v){
                if(!empty($options)) $options .= " / ";
                $options .= $k.":".$v;
            }
        }

        if($i > 0){
            $flg = true;
            foreach($arrCart as $key => $val){
                if($item_id == $val["item_id"]){
//                    $val["quantity"] = $val["quantity"] + $quantity;
                    $val["quantity"] = $quantity;
                    $flg = false;
                }
                $arrCart[$key] = $val;
            }
            if($flg){
                $arrCart[$i]["item_id"] = $item_id;
                $arrCart[$i]["item_name"] = $item_name;
                $arrCart[$i]["price"] = $price;
                $arrCart[$i]["imgurl"] = $imgurl;
                $arrCart[$i]["quantity"] = $quantity;
                $arrCart[$i]["options"] = $options;
            }
        }else{
            $arrCart[$i]["item_id"] = $item_id;
            $arrCart[$i]["item_name"] = $item_name;
            $arrCart[$i]["price"] = $price;
            $arrCart[$i]["imgurl"] = $imgurl;
            $arrCart[$i]["quantity"] = $quantity;
            $arrCart[$i]["options"] = $options;
        }
        $this->Session->write("cart",$arrCart);
    }

/*
* 数量変更
*/
    function upd($data) {

        $arrCart = array();
        $arrCart = $this->Session->read("cart");

        $item_id = $data["item_id"];
        $quantity = $data["quantity"];
        if(!is_numeric($data["quantity"])) return;

        foreach($arrCart as $key => $val){
            if($item_id == $val["item_id"]){
                $val["quantity"] = $quantity;
            }
            $arrCart[$key] = $val;
        }
        $this->Session->write("cart",$arrCart);

    }

/*
* 商品削除
*/
    function del($item_id) {
        $arrCart = $this->Session->read("cart");

        $arrNewCart = array();
        foreach($arrCart as $key => $val){
            if($item_id != $val["item_id"]){
                $arrNewCart[] = $val;
            }
        }

        $this->Session->write("cart",$arrNewCart);

    }

var $err_message;

/*
* エラー表示
*/
    function getErrors() {

        $arrCart = $this->Session->read("cart");
        $Items = ClassRegistry::init('Item');

        $err_message = null;
        if(is_array($arrCart)){
            foreach($arrCart as $key => $val){

                $item_id = $val["item_id"];
                $quantity = $val["quantity"];
                $array = $Items->getDetail($item_id);
                $stock_num = null;
                if(!empty($array)){
                    $item_name = "「".$array["Item"]["title"]."」";
                }
                if(is_numeric($quantity)){
                    if($quantity == 0){
                        $this->err_message[] = $item_name."の数量が0です。正しい個数を入力して「変更」ボタンを押して下さい。";
                    }
                }else{
                    $this->err_message[] = "正しい個数を入力して「変更」ボタンを押して下さい。";
                }

            }
        }

        return $this->err_message;

    }

    function isErrors() {
        if(count($this->err_message) > 0){
            return false;
        }else{
            return true;
        }
    }

/*
* ポイント利用
*/
    function setPoint($point) {

        $this->Session->write("redeem_point",$point);

    }

/*
* ポイント利用
*/
    function getPoint() {

        $point = $this->Session->read("redeem_point");
        if(empty($point)) $point = 0;
        return $point;

    }

/*
* 配送希望日
*/
    function setDeliveryPreDate($delivery_pre_date) {

        $this->Session->write("delivery_pre_date",$delivery_pre_date);

    }

/*
* 配送希望日
*/
    function getDeliveryPreDate() {

        return $this->Session->read("delivery_pre_date");

    }

/*
* 支払方法
*/
    function setPayment($payment) {

        $this->Session->write("payment",$payment);

    }

/*
* 支払方法
*/
    function getPayment() {

        return $this->Session->read("payment");

    }

/*
* ショッピングカート 購入者情報
*/
    function cartCustomer() {

    }

/*
* 決済手数料取得
*/
    function getPaymentCharge($payment) {
        if($payment != 'payment03') return 0;
        $Constants = ClassRegistry::init('Constant');
        $charge = $Constants->getConstValue("cash_on_delivery_chage","> 0");
        $this->fee = intval($charge);
        return intval($charge);
    }

var $delivery_charge = 0;

/*
* 送料取得
*/
    function getDeliveryCharge($pref) {

        $arrMasterData = $this->getMasterData();
        if(isset($arrMasterData["flat"]["pref"][$pref])){
            $pref_name = $arrMasterData["flat"]["pref"][$pref];
        }else{
            $pref_name = $pref;
        }

        $Constants = ClassRegistry::init('Constant');
        $charge = $Constants->getConstValue("pref_delivery_charge",$pref_name);

        $limit_total = $Constants->getConstValue("delivery_charge","送料無料の購入金額");
        $this->getList();
        if($limit_total < $this->getTotal()){
            $charge = 0;
        }

        $this->delivery_charge = intval($charge);
        return $this->delivery_charge;
    }

/*
* 購入者情報セッション追加
*/
    function customerEntry($data) {

        $arrCart = $this->Session->read("order");

        $arrCart["customer"] = $data;
        $this->Session->write("order",$arrCart);

    }

/*
* 購入者情報検証
*/
    function isErrorCustomer() {

        return false;

    }

/*
* 購入者情報
*/
    function getCustomer() {

        $order = $this->Session->read("order");

        return $order;
    }

    var $order_id;

    var $customer_email;

/*
* 受注処理
*/
    function orderComplete($member_id) {

/*
        $cart = $this->getList();
        if(count($cart) == 0){
            header("location: ".HOME_URL."cart/");
            exit;
        }

        $Orders = ClassRegistry::init('Order');
        $order = $this->Session->read("order");
        $sel_printing = $this->Session->read("sel_printing");

        $arrPrefCode = Configure::read('arrPrefCode');

        //購入者メールアドレス
        $this->customer_email = $order["customer"]["orders"]["email"];

        $payment = $order["customer"]["orders"]["payment"];
        $pref    = $order["customer"]["orders"]["delivery_pref"];

        if(isset($arrPrefCode[$pref])){
            $pref = $arrPrefCode[$pref];
        }

        $order["customer"]["orders"]["delivery_charge"] = $this->getDeliveryCharge($pref);
        $fee = $this->getPaymentCharge($payment);
        $order["customer"]["orders"]["fee"] = $fee;
        $order["customer"]["orders"]["code"] = date("ymdHis").mt_rand(1111,9999);
        $order["customer"]["orders"]["total"] = $this->getSumTotal();

        if(!isset($order["customer"]["orders"]["customer_id"])){
            $order["customer"]["orders"]["customer_id"] = 0;
            $order["customer"]["orders"]["member_id"] = 0;
        }

        // ポイント利用
        $redeem_point = $this->getPoint();
        $order["customer"]["orders"]["redeem_point"] = $redeem_point;

        if($Orders->isError($order["customer"])){
            $customer_id = @$order["customer"]["orders"]["customer_id"];
            $order["customer"]["orders"]["id"] = null;
            $order["customer"]["orders"]["created"] = date("Y-m-d H:i:s");
            $order["customer"]["orders"]["modified"] = date("Y-m-d H:i:s");

            $Orders->isSave($order["customer"]);
            $this->order_id = $Orders->getLastInsertID();

        }else{
            print_r($Orders->getErrors());
        }

        $OrderDetails = ClassRegistry::init('OrderDetail');
        $MerchItems = ClassRegistry::init('MerchItem');

        foreach($cart as $row){

            $item_id  = $row["item_id"];
            $item_name  = $row["item_name"];
            $quantity = $row["quantity"];
            $price = $row["price"];
            $total = $row["total"];
            $data["order_details"]["id"]             = null;
            $data["order_details"]["order_id"]       = $this->order_id;
            $data["order_details"]["merch_item_id"]  = $item_id;
            $data["order_details"]["item_name"]      = $item_name;
            $data["order_details"]["price"]          = $price;
            $data["order_details"]["quantity"]       = $quantity;
            $data["order_details"]["total"]          = $total;
            $data["order_details"]["point"]          = $this->getSumPoint($row);

            $OrderDetails->isSave($data);

            // 在庫処理
            $MerchItems->stockOut($item_id,$quantity);
        }
*/
        // ポイント更新
        $redeem_point = $this->getSumTotal();
        $Member = ClassRegistry::init('Member');
        $total_point = $Member->updPoint($member_id,$redeem_point);

        // セッション破棄
        $this->Session->write("order",null);
        $this->Session->write("cart",null);
        $this->Session->write("redeem_point",null);
        $this->Session->write("payment",null);
        $this->Session->write("delivery_pre_date",null);
        $this->Session->write("sel_printing",null);

    }

/*
* 受注処理
*/
    function adminOrderComplete($order_id) {

        $OrderDetails = ClassRegistry::init('OrderDetail');
        $MerchItems = ClassRegistry::init('MerchItem');

        $details = $OrderDetails->getListDetailsByOrderId($order_id);

        // 在庫処理
        foreach($details as $row){
            $item_id  = $row["merch_item_id"];
            $quantity = $row["quantity"];
            $MerchItems->stockOut($item_id,$quantity);
        }

    }

/*
* 送信用メール本文
*/
    function getMailBody($order_id = null,$ext_flg = false) {

        if(!empty($order_id)) $this->order_id = $order_id;

        $Orders = ClassRegistry::init('Order');
        $OrderDetails = ClassRegistry::init('OrderDetail');
        $arrMasterData = $this->getMasterData();

        $array  = $Orders->getOneEntityById($this->order_id);
        $arrOrder  = $array["orders"];
        $arrDetail = $OrderDetails->getListDetailsByOrderId($this->order_id,$ext_flg);

        $arrBody = array();
        $arrBody["date"]        = $arrOrder["modified"];
        $arrBody["code"]        = $arrOrder["code"];
        $arrBody["name"]        = $arrOrder["name"];
        $arrBody["name_kana"]   = $arrOrder["name_kana"];
        $arrBody["company"]     = $arrOrder["company"];
        $arrBody["email"]              = $arrOrder["email"];
        $arrBody["m_email"]              = $arrOrder["m_email"];
        $arrBody["customer_postcode"] = $arrOrder["customer_postcode"];
        $arrBody["pref"]               = @$arrMasterData["flat"]["pref"][$arrOrder["pref"]];
        $arrBody["customer_address1"]  = $arrOrder["customer_address1"];
        $arrBody["customer_address2"]  = $arrOrder["customer_address2"];
        $arrBody["customer_tel"]      = $arrOrder["customer_tel"];
        $arrBody["customer_mobile"]      = $arrOrder["customer_mobile"];
        $arrBody["payment"]            = @$arrMasterData["flat"]["payment"][$arrOrder["payment"]];
        $arrBody["print"]             = @$arrMasterData["flat"]["printing"][$arrOrder["print"]];
        $arrBody["packing_slip"]      = "同梱しない";
        if($arrOrder["packing_slip"] == 1){
            $arrBody["packing_slip"]      = "同梱する";
        }
        $arrBody["comment"]  = $arrOrder["comment"];

        $arrBody["delivery_pre_date"]  = $arrOrder["delivery_pre_date"];
        $arrBody["delivery_pre_time"]  = @$arrMasterData["flat"]["delivery_pre_time"][$arrOrder["delivery_pre_time"]];

        $arrBody["delivery_name"]      = $arrOrder["delivery_name"];
        $arrBody["delivery_name_kana"] = $arrOrder["delivery_name_kana"];
        $arrBody["delivery_postcode"] = $arrOrder["delivery_postcode"];
        $arrBody["delivery_pref"] = null;
        if(!empty($arrOrder["delivery_pref"])){
            $arrBody["delivery_pref"] = @$arrMasterData["flat"]["pref"][$arrOrder["delivery_pref"]];
        }
        $arrBody["delivery_address1"] = $arrOrder["delivery_address1"];
        $arrBody["delivery_address2"] = $arrOrder["delivery_address2"];
        $arrBody["delivery_tel"]     = $arrOrder["delivery_tel"];

        $item_total = 0;
        foreach($arrDetail as $k => $row){
            if(!empty($row["total"])){
                $arrBody["detail"][$k]["item_code"] = $row["code"];
                $arrBody["detail"][$k]["item_name"] = $row["basic_name"].$row["item_name"];
                $arrBody["detail"][$k]["options"]   = $row["options"];
                $arrBody["detail"][$k]["price"]     = $row["price"];
                $arrBody["detail"][$k]["quantity"]  = $row["quantity"];
                $arrBody["detail"][$k]["sum_total"] = $row["total"];
                $item_total = $item_total + intval($row["quantity"]);
            }
        }

        $arrBody["fee"]             = $arrOrder["fee"];
        $arrBody["delivery_charge"] = $arrOrder["delivery_charge"];
        $arrBody["redeem_point"]    = $arrOrder["redeem_point"];
        $arrBody["total"]           = $arrOrder["total"];
        $arrBody["item_total"]      = $item_total;

        $this->customer_email = $arrOrder["email"];

        return $arrBody;
    }

/*
* 購入者メールアドレス
*/
    function getCustomerMail() {
        return $this->customer_email;
    }

/*
* 認証確認
*/
    function getAuth() {

        $authMember = $this->Session->read("authuser");
        if(empty($authMember)){
            header("location: ".HOME_URL."member/login/");
            exit;
        }

        $limittime = $authMember["logintime"] + (2 * 60 * 60);
        if(time() > $limittime ){
            $this->Session->write("authuser",array());
            header("location: ".HOME_URL."member/login/");
            exit;
        }

        $authMember["logintime"] = time();
        $this->Session->write("authuser",$authMember);

        return $authMember;
    }

/*
* ポイント計算
*/
    function getSumPoint($row) {
/*
        $arrMasterData = $this->getMasterData();
        $shop_def_point = $arrMasterData["flat"]["shop"]["shop_def_point"];
        $shop_def_point = str_replace("%","",$shop_def_point);
        $shop_def_point = intval($shop_def_point);
        $point = 0;

        $quantity = $row["items"]["quantity"];
        $price    = $row["items"]["sales_price"];
        $base_point  = $row["merch_basics"]["point"];
        $start_date  = $row["merch_basics"]["point_start_date"];
        $end_date    = $row["merch_basics"]["point_end_date"];
        $today = date("Y-m-d");
        $uni_point = 0;
        $uni_point = $quantity * $price * $shop_def_point / 100 / 1.08;
        if($start_date <= $today or $start_date == "0000-00-00"){
            if($end_date >= $today or $end_date == "0000-00-00"){
                if($base_point > 0){
                    $uni_point = $quantity * $price * ($base_point / 100) / 1.08;
                }
            }
        }
        $point = $point + floor($uni_point);

        return $point;
*/
    }

/*
 * マスターデータ取得
 */
    function getMasterData() {
        // マスターデータ取得
        $DataLeaves      = ClassRegistry::init('DataLeave');
        $master_data = Cache::read("arrMasterData");
        // マスターデータ有効期限切れの場合
        if(empty($master_data)){
            $master_data = $DataLeaves->getArrMasterData();
            Cache::write("arrMasterData",$master_data);
        }
        return $master_data;
    }
}
?>