<?php
App::uses('Component', 'Controller');
class MemberComComponent extends Component {

//    var $utility;

    public $components = array('Utility','Session','Auth','Cookie');
/*
   function __construct() {
        App::import('Component', 'UtilityComponent');
        $this->Utility = new Utility();
        App::import('Component', 'Session');
        $this->Session = new SessionComponent();
        App::import('Component', 'Auth');
        $this->Auth = new AuthComponent();
        App::import('Component', 'Cookie');
        $this->Cookie = new CookieComponent();
   }
*/

/*
* メンバーIDの保存
*/
    function saveLoginId($data) {

        if(isset($data["saveMemberId"])){
            $is_save = $data["saveMemberId"][0];
            $member_id = $data["members"]["email"];
            if($is_save == "yes"){
                $exp =  60*60;
                $this->Cookie->write("save_member_id", $member_id, true, $exp);
            }
        }
    }

/*
* 保存メンバーIDの取得
*/
    function getSaveLoginId() {
        return $this->Cookie->read("save_member_id");
    }

/*
* 保存メンバーIDの取得
*/
    function isSaveLoginId() {
        $email = $this->Cookie->read("save_member_id");
        if(!empty($email)){
            return "yes";
        }else{
            return "no";
        }
    }

/*
* ログアウト
*/
    function authInfo() {
        $authMember = $this->Session->read("authuser");
        if(count($authMember) > 0 ){
            $email = $authMember["email"];
            $Customers = ClassRegistry::init('Customer');
            $conditions = array('conditions' => array("email = '$email'","del_flg <= 0"));
            $list = $Customers->find("first", $conditions);
            $authMember["user"] = $list["Customer"]["name"];
            return $authMember;
        }else{
            return null;
        }
    }

/*
* 認証者情報
*/
    function authUser() {
        $authMember = $this->Session->read("authuser");
        $user["name"] = "ゲスト";
        $user["auth"] = false;
        if(count($authMember) > 0 ){
            foreach($authMember as $name => $val){
                $user[$name] = $val;
            }
            $member_id = $authMember["id"];
            $Customers = ClassRegistry::init('Customer');
            $list = $Customers->getOneEntityByMemberId($member_id);
            $user["name"] = $list["customers"]["name"];
            $user["point"] = $list["customers"]["point"];
            $user["auth"] = true;
        }
        return $user;

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
            header("location: ".HOME_URL."member/login/");
            exit;
        }

        $authMember["logintime"] = time();
        $this->Session->write("authuser",$authMember);

        return $authMember;
    }

/*
* 登録処理
*/
    function regist($data) {

            //完了処理
            $Members           = ClassRegistry::init('Member');
            $Customers         = ClassRegistry::init('Customer');
            $CustomerAddresses = ClassRegistry::init('CustomerAddress');
            $init_status = 1;

            $password = $data["members"]["user_password"];
            $data["Member"]["admin_password"] = $password;
            $data["Member"]["user_password"] = $this->Auth->password($password);
            $data["Member"]["user_password_confirm"] = $this->Auth->password($data["members"]["user_password_confirm"]);

            $data["Member"]["user_type"] = "customer";
            $data["Member"]["user_id"] = $data["customers"]["email"];
            $data["Member"]["status"] = $init_status;
            if(!$Members->isSave($data)){
                print_r($data["Member"]);
                print_r($Members->validationErrors);
            }
            $Members->isError($data);
            $member_id = $Members->getLastInsertID();
            $data["Customer"]["member_id"] = $member_id;
            $data["Customer"]["email"] = $data["customers"]["email"];
            $data["Customer"]["status"] = $init_status;
            if($member_id >0) $Customers->isSave($data);
            $customer_id = $Customers->getLastInsertID();

            // 自宅住所
            $data["CustomerAddress"]["type"] = "home";
            $data["CustomerAddress"]["name"] = $data["customers"]["name"];
            $data["CustomerAddress"]["name_kana"] = $data["customers"]["name_kana"];
            $data["CustomerAddress"]["customer_id"] = $customer_id;
//print_r($data);exit;
            if($customer_id >0) $CustomerAddresses->isSave($data);

            // 届け先住所
            $data["customer_addresses"]["id"] = null;
            $data["customer_addresses"]["type"] = "delivery";

            if(!empty($data["orders"]["delivery_name"]))      $data["customer_addresses"]["name"]      = $data["orders"]["delivery_name"];
            if(!empty($data["orders"]["delivery_name_kana"])) $data["customer_addresses"]["name_kana"] = $data["orders"]["delivery_name_kana"];
            if(!empty($data["orders"]["delivery_postcode"]))  $data["customer_addresses"]["postcode"]  = $data["orders"]["delivery_postcode"];
            if(!empty($data["orders"]["delivery_pref"]))      $data["customer_addresses"]["pref"]      = $data["orders"]["delivery_pref"];
            if(!empty($data["orders"]["delivery_address2"]))  $data["customer_addresses"]["address"]   = $data["orders"]["delivery_address2"];
            if(!empty($data["orders"]["delivery_address1"]))  $data["customer_addresses"]["city"]      = $data["orders"]["delivery_address1"];
            if(!empty($data["orders"]["delivery_tel"]))       $data["customer_addresses"]["tel"]       = $data["orders"]["delivery_tel"];

//print_r($data);
            if($customer_id >0) $CustomerAddresses->isSave($data);

            // メール送信
            $body = "";
            $body .= "お名前：".$data["customers"]["name"]."\n";
            $body .= "お名前（カナ）：".$data["customers"]["name_kana"]."\n";
            $body .= "メール：".$data["customers"]["email"]."\n";
            $body .= "郵便番号：".$data["customer_addresses"]["postcode"]."\n";
//            $body .= "住所：".$const["pref"][$data["customer_addresses"]["pref"]]." ";
            $body .= $data["customer_addresses"]["city"]." ".$data["customer_addresses"]["address"]."\n";
            $body .= "電話番号：".$data["customer_addresses"]["tel"]."\n";
            $body .= "携帯番号：".$data["customer_addresses"]["mobile"]."\n";
            $template_code = "customer_entry";
            $add_body = "以下の内容で送信しました。";
            $customer_mail = $data["customers"]["email"];

            $trans_word["name"] = $data["customers"]["name"];
            $trans_word["id"] = $data["customers"]["email"];
            $trans_word["pass"] = $password;
            $trans_word["customer"] = $body;

            $this->Utility->sendMailTemplate($trans_word,$add_body,$customer_mail,$template_code);

    }

/*
* 退会処理
*/
    function resign($member_id) {

        $Customers = ClassRegistry::init('Customer');
        $Members = ClassRegistry::init('Member');
        $EmailTemplates = ClassRegistry::init('EmailTemplate');

        App::import('Component', 'Utility');
        $this->Utility = new UtilityComponent(new ComponentCollection());

        $arrCustomer = $Customers->getOneEntityByMemberId($member_id);
        $arrCustomer = $arrCustomer["customers"];
        $Customers->withdrawal($member_id);
        $Members->withdrawal($member_id);

        $obj = $EmailTemplates->getTemplateByCode("withdrawal");

        $body = $obj["body"];
        $body = str_replace('<!--{$name}-->',$arrCustomer["name"],$body);
        $to_mail = $arrCustomer["email"];

        // メール送信 カスタマー宛
        $add_body = "会員の退会がありました。以下の内容で送信しました。\n\n";
        $this->Utility->sendMail($body,$obj["subject"],$obj["admin_mail"],$to_mail,$add_body);

    }
}
?>