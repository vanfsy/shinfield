<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class Member extends AppModel {

    public $validate = array(
        'password' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'compar' => array(
                'rule' => array('comparPass'),
                'message' => '確認パスワードと一致しません'
            )
        ),
        'name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'name_kana' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'katakana' => array(
                'rule' => array('custom','/^(?:\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xB6]|\xE3\x83\xBC|\x20|\xE3\x80\x80)+$/'),
                'message' => 'カタカナで入力ください'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'company' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array('maxLength', 104),
                'message' => '入力できる文字数は100文字までになります'
            ),
        ),
        'postcode' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'address' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'phone' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'custom' => array(
                'allowEmpty' => true,
                'rule' => array('custom','/\d{2,5}-\d{2,4}-\d{4}/'),
                'message' => '電話番号を正確に入力してください(ハイフンが必要です)'
            ),
        ),
        'gender' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
        'birthday' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'date' => array(
                'allowEmpty' => true,
                'rule' => array('date', 'ymd'),
                'message' => '日付の入力が不正です'
            ),
        ),
        'nickname' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'email' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'email' => array(
                'allowEmpty' => true,
                'rule' => 'email',
                'message' => 'メールアドレスが正しくありません'
            ),
            'compar' => array(
                'rule' => array('comparPass'),
                'message' => '確認メールと一致しません'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'このユーザは既に登録されています'
            )
        ),
        'password_hint' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'mailmag_flg' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
        'bank_name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です'
            ),
        ),
        'branch_name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です'
            ),
        ),
        'branch_code' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です'
            ),
        ),
       'account_no' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です'
            ),
        ),
        'deposit_name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です'
            ),
        ),
    );

    public $validate_profile = array(
        'name' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'name_kana' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'katakana' => array(
                'rule' => array('custom','/^(?:\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xB6]|\xE3\x83\xBC|\x20|\xE3\x80\x80)+$/'),
                'message' => 'カタカナで入力ください'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'company' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array('maxLength', 104),
                'message' => '入力できる文字数は100文字までになります'
            ),
        ),
        'postcode' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'address' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'phone' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'custom' => array(
                'allowEmpty' => true,
                'rule' => array('custom','/\d{2,5}-\d{2,4}-\d{4}/'),
                'message' => '電話番号を正確に入力してください(ハイフンが必要です)'
            ),
        ),
        'gender' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
        'birthday' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'date' => array(
                'allowEmpty' => true,
                'rule' => array('date', 'ymd'),
                'message' => '日付の入力が不正です'
            ),
        ),
        'nickname' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),
        'mailmag_flg' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
    );

    public $validate_email = array(
        'user_id' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'このユーザは既に登録されています'
            )
        ),
        'email' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'このユーザは既に登録されています'
            ),
            'compar' => array(
                'rule' => array('comparMail'),
                'message' => '確認メールと一致しません'
            ),
            'email' => array(
                'allowEmpty' => true,
                'rule' => 'email',
                'message' => 'メールアドレスが正しくありません'
            ),
        ),
    );

    public $validate_password = array(

        'password' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'compar' => array(
                'rule' => array('comparPass'),
                'message' => '確認パスワードと一致しません'
            )
        ),

        'password_hint' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array('maxLength', 94),
                'message' => '入力できる文字数は90文字までになります'
            ),
        ),

    );

    public $validate_reissue_password = array(

        'password' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'compar' => array(
                'rule' => array('comparPass'),
                'message' => '確認パスワードと一致しません'
            )
        ),
        'email' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'email' => array(
                'allowEmpty' => true,
                'rule' => 'email',
                'message' => 'メールアドレスが正しくありません'
            ),
            'exists' => array(
                'rule' => array('checkExistEmail'),
                'message' => 'このメールアドレスは登録されておりません。'
            ),
        ),
    );

    public $validate_notice_email = array(
        'message_to_saller_flag' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'inList' => array(
                'rule' => array('inList', array(1, 0), false),
                'message' => '無効な値が選択されています。'
            ),
        ),
        'notification_to_saller_flag' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'inList' => array(
                'rule' => array('inList', array(1, 0), false),
                'message' => '無効な値が選択されています。'
            ),
        ),
        'purchased_to_saller_flag' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'inList' => array(
                'rule' => array('inList', array(1, 0), false),
                'message' => '無効な値が選択されています。'
            ),
        ),
    );

    public $validate_fileup = array(
        'image_file_name' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 255),
                'message' => 'このフィールドは255文字以内です'
            ),
        ),
        'image' => array(
            'extension' => array(
                'rule' => array('attachmentContentType', array('image/jpeg', 'image/png', 'image/gif')),
                'message' => '拡張子が不正です。',
            ),
        ),
    );

    var $actsAs = array(
        'UploadPack.Upload' => array(
            'image' => array(//     ☆ここでは、"_file_name"を除いたカラム名を書く
                'quality' => 100,//   ☆画質指定、デフォルトでは75
                'styles' => array(
                    'list_l' => '400w',//  ☆リサイズしたいサイズを書く
                    //'list_m' => '200x150',//  ☆リサイズしたいサイズを書く
                    'list_s' => '130x130',//  ☆リサイズしたいサイズを書く
                    'thumb' => '200w'//  ☆リサイズしたいサイズを書く
                )
            ),
        ),
    );

/*
 * 確認メール検証
 * */
    public function comparMail( $check ) {
        if($this->data['Member']['email'] == $this->data['Member']['email_confirm']){
            return true;
        }else{
            return false;
        }
    }

/*
 * 確認パスワード検証
 * */
    public function comparPass( $check ) {
        if($this->data['Member']['password'] == $this->data['Member']['password_confirm']){
            return true;
        }else{
            return false;
        }
    }

/*
 * メールアドレス存在チェック
 * */
    public function checkExistEmail($check) {
        $result = $this->find('first', array('conditions' => array('email' => $check['email'])));
        if(empty($result)){
            return false;
        }
        return true;
    }

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new SimplePasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash($this->data[$this->alias]['password']);
        }
        if (isset($this->data[$this->alias]['role']) && $this->data[$this->alias]['role'] == 'user') {
            $this->data[$this->alias]['user_id'] = @$this->data[$this->alias]['email'];
        }
        return true;
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
        $table = 'members';
        return parent::getOptions($table);
    }

    /*
     * リスト
    */
    function getOneFieldById($id,$field){

        // SQL処理
        $sql =  "SELECT `$field` FROM members WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        $res = null;
        if(count($array) > 0) $res = $array[0]["members"][$field];
        return $res;
    }

    /*
     * 出品者ランキング
     */
    function getSellerRankEntity(){

        // SQL処理
        $sql = "";
        $sql .= "SELECT Member.id ";
        $sql .= "FROM `members` Member ";
        $sql .= "INNER JOIN items Item ";
        $sql .= "ON Member.id = Item.member_id ";
        $sql .= "AND Item.del_flg = 0 ";
        $sql .= "AND Item.save_flg = 1 ";
        $sql .= "AND Item.seller_rank > 0 ";
        $sql .= "WHERE Member.del_flg = 0 ";
        $sql .= "AND Member.role = 'user' ";
        $sql .= "AND Member.status = 1 ";
        $sql .= "GROUP BY Member.id  ";
        $sql .= "ORDER BY MAX(Item.seller_rank) DESC ";
        $sql .= "LIMIT 5 ";
        $arrDatas = $this->query($sql);
        return $this->__getMembersByIdList($arrDatas);
    }
    function getSellerRankAll($disp_num,$pgnum){
        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);
        // SQL処理
        $sql = "";
        $sql .= "SELECT Member.id ";
        $sql .= "FROM `members` Member ";
        $sql .= "INNER JOIN items Item ";
        $sql .= "ON Member.id = Item.member_id ";
        $sql .= "AND Item.del_flg = 0 ";
        $sql .= "AND Item.save_flg = 1 ";
        $sql .= "AND Item.seller_rank > 0 ";
        $sql .= "WHERE Member.del_flg = 0 ";
        $sql .= "AND Member.role = 'user' ";
        $sql .= "AND Member.status = 1 ";
        $sql .= "GROUP BY Member.id  ";
        $sql .= "ORDER BY Item.seller_rank";
        //$sql .= "LIMIT 100 ";
        //$arrDatas = $this->query($sql);

        $pg_sql = $this->getPagingString100($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = $this->__getMembersByIdList($array);

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();
        $arrRes["total"] = $this->getRecordTotal();
        return $arrRes;
    }

    private function __getMembersByIdList($idList) {
        return $this->find('all', array(
            'conditions' => array(
                'id' => Hash::extract($idList, "{n}.Member.id")
            )
        ));
    }

    /*
     * 出品者のレベル取得
     */
    public function getSellerLevel($id) {
        $sql = "";
        $sql .= "SELECT COUNT(Review.id) AS rate_cnt, SUM(Review.rating) AS rate_sum";
        $sql .= "  FROM `items` Item";
        $sql .= " INNER JOIN `reviews` Review ON Review.item_id = Item.id";
        $sql .= " WHERE Item.member_id = ?";
        $arrData = $this->query($sql, array($id));
        if(!empty($arrData) && $arrData[0][0]['rate_cnt'] > 0) {
            return round($arrData[0][0]['rate_sum'] / $arrData[0][0]['rate_cnt'], 1);
        }
        return 0;
    }
    
    public function getDetail($id,$role = 'user') {

        $arrDatas = $this->find('first',array('conditions' => array('id' => $id,'del_flg' => 0,'role' => $role)));
        if(empty($arrDatas)){
            return null;
        }
        $now = date("Ymd");
        $birth = @str_replace('-','',$arrDatas['Member']['birthday']);
        $arrDatas['Member']['age'] = floor(($now-$birth)/10000);

        return $arrDatas;
    }

    /*
     * リスト
    */
    function getOneEntityById($id){

        // SQL処理
        $sql =  "SELECT * FROM members WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        return $array[0];
    }

    /*
     * 管理画面 ユーザリスト
    */
    function getMemberList($mode = null){

        $conditions = array('Member.role' => 'user',
                            'Member.status' => 1,
                            'Member.del_flg' => 0);
        $arrData = $this->find('all',array('conditions' => $conditions, 'order' => 'Member.name'));

        $arrRes = null;
        if($mode == 'list'){
            if(!empty($arrData)){
                foreach($arrData as $row){
                    $id = $row['Member']['id'];
                    $name = $row['Member']['name'];
                    $company = $row['Member']['company'];
                    if(!empty($company)){
                        $name .= '['.$company.']';
                    }
                    $arrRes[$id] = $name;
                }
            }
        }else{
            $arrRes = $arrData;
        }
        return $arrRes;

    }

    /*
     * 管理画面 新着ユーザ情報
    */
    function getNewMembers(){

        $this->bindModel(array('hasOne' => array(
                'Customer' => array(
                    'className' => 'Customer',
                    'foreignKey' => 'member_id'
                ))));

        $lastweek = date("Y-m-d H:i:s",strtotime("-1 week"));
        $conditions = array('Member.user_type' => 'customer',
                            'Member.status' => 1,
                            'Member.created > ' => $lastweek,
                            'Member.del_flg' => 0,
                            'Customer.del_flg' => 0);
        $arrRes =  $this->find('all',array('conditions' => $conditions, 'order' => 'Member.created DESC'));
        return $arrRes;

    }

    /*
     * 登録更新処理
    */
    function isSave($data,$valid = true){
        if($data['members']['user_password'] == 'dummy'){
            $password = $this->getOneFieldById($data['members']['id'],'user_password');
            $data['members']['user_password'] = $password;
            $data['members']['user_password_confirm'] = $password;
        }

        return $this->save($data);
    }

    /*
     * ページング
     */
    function setWhereVal($param){
        $this->where_value = $param;
    }

    /*
     * ページング
    */
    function getPagingEntity($disp_num,$pgnum,$role_type){

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM members ";
        $sql .=  "      WHERE members.del_flg = 0 ";
        $sql .=  "        AND members.role = '$role_type' ";

        if(!empty($this->where_value)){

            foreach($this->where_value as $key => $val){
                if($key == 'id' && !empty($val)){
                    $sql .=  " AND id LIKE '%$val%' ";
                }
                if($key == 'name' && !empty($val)){
                    $sql .=  " AND name LIKE '%$val%' ";
                }
                if($key == 'phone' && !empty($val)){
                    $sql .=  " AND phone LIKE '%$val%' ";
                }
                if($key == 'email' && !empty($val)){
                    $sql .=  " AND email LIKE '%$val%' ";
                }
                if($key == 'user_rank' && !empty($val)){
                    $sql .=  " AND user_rank = '$val' ";
                }
                if($key == 'keywords' && !empty($val)){
                    $line = null;
                    $val = str_replace('　',' ',$val);
                    $word = explode(' ',$val);
                    foreach($word as $row){
                        if(!empty($line)) $line .= " OR ";
                        $line .= " name LIKE '%$row%' OR name_kana LIKE '%$row%' OR ";
                        $line .= " company LIKE '%$row%' OR nickname LIKE '%$row%'  OR comment LIKE '%$row%' ";
                    }
                    $sql .=  " AND ($line) ";
                }
                if($key == 'public_flg' && $val > 0){
                    $sql .=  " AND public_flg = '$val' ";
                }
            }
        }

        $sql .=  "   ORDER BY members.created DESC ";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){
            $arrRes["list"][$key] = $row["members"];
//            $role = $row["roles"]["name"];
//            $arrRes["list"][$key]["role"] = $role;

        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();
        $arrRes["total"] = $this->getRecordTotal();

        return $arrRes;
    }

    /*
     * 有効全件リスト取得
    */
    function getAllEntity(){
        return parent::getAllEntity('members');
    }

    /*
     * 管理者更新処理
    */
    function isUpdateAdmin($data){
        $boolean = false;
        if (empty($data)) return $boolean;

        // エラーチェック
        $this->isValid($data);
        $this->set($data);
        if(!$this->validates($data)){
            return $boolean;
        }

        // 登録処理
        $this->save($data, true, array('user_id','user_password','modified'));

        return true;

    }

    /*
     * USER_ID変更処理
    */
    function chgUserId($id,$user_id){

        $sql = "UPDATE members SET user_id = '$user_id' WHERE id = '$id' ";
        $this->query($sql);

    }

    /*
     * ステータス変更処理
    */
    function chgStatus($data){

        $id = $data['Member']['id'];
        $status = $data['Member']['status'];
        $comment = $data['Member']['admin_message'];
        $sql = "UPDATE members SET status = '$status', admin_message = '$comment' WHERE id = '$id' ";
        $this->query($sql);
        return true;

    }

    /*
     * パスワード変更処理
    */
    function chgPassWord($id,$pass){

        $sql = "UPDATE members SET user_password = '$pass' WHERE id = '$id' ";
        $this->query($sql);

    }

    /*
     * ポイント決済更新処理
    */
    function updPoint($member_id,$point){

        $sql =  "";
        $sql .=  "     UPDATE members ";
        $sql .=  "        SET members.point = (members.point - $point) ";
        $sql .=  "      WHERE members.del_flg = 0 ";
        $sql .=  "        AND members.id = $member_id ";
        return $this->query($sql);
    }

    function addPoint($member_id,$point){
        $sql =  "";
        $sql .=  "     UPDATE members ";
        $sql .=  "        SET members.point = (members.point + $point) ";
        $sql .=  "      WHERE members.del_flg = 0 ";
        $sql .=  "        AND members.id = $member_id ";
        return $this->query($sql);
    }

    /*
     * 退会処理
    */
    function withdrawal($member_id){
        // 退会処理
        $data['Member']['id'] = $member_id;
        $data['Member']['status'] = 3;
        $data['Member']['role'] = 'user';
        $data['Member']['del_flg'] = 1;
        return $this->save($data);
    }

    public function isNotice($to_member_id, $notice_column) {
        $result = $this->find('first', array(
            'conditions' => array('id' => $to_member_id),
            'fields' => array($notice_column)
        ));

        return $result['Member'][$notice_column];
    }

}
?>
