<?php
class Estimate extends AppModel {
    var $validate = array();
    var $error_message = null;
    var $pgnum = 0;

    /*
     * 入力値検証
    */

    function isValid(){

        $validate["name_kana"]["hiragana"]   = array("rule" => "hiragana", "allowEmpty" => false , "message" => "ふりがなはひらがなです。");
        $validate["email"]["email"]          = array("rule" => "email", "allowEmpty" => false, "message" => "不正なメールアドレスです。");
        $validate["tel"]["isCode"]           = array("rule" => "isCode", "allowEmpty" => false, "message" => "お電話番号は数字記号のみです。");
        $validate["postcode1"]["numeric"]    = array("rule" => "numeric", "allowEmpty" => false, "message" => "郵便番号は数字でお願いします。");
        $validate["postcode2"]["numeric"]    = array("rule" => "numeric", "allowEmpty" => false, "message" => "郵便番号は数字でお願いします。");

        $validate["name"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "お名前は必須です。");
        $validate["name_kana"]["notEmpty"]   = array("rule" => "notEmpty", "message" => "ふりがなは必須です。");
        $validate["email"]["notEmpty"]       = array("rule" => "notEmpty", "message" => "メールアドレスは必須です。");
        $validate["postcode1"]["notEmpty"]   = array("rule" => "notEmpty", "message" => "郵便番号は必須です。");
        $validate["postcode2"]["notEmpty"]   = array("rule" => "notEmpty", "message" => "郵便番号は必須です。");
        $validate["pref"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "都道府県は必須です。");
        $validate["city"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "市区町村は必須です。");
        $validate["tel"]["notEmpty"]         = array("rule" => "notEmpty", "message" => "電話番号は必須です。");

        $this->validate = $validate;

    }

    /*
     * エラーチェック
    * */
    function isError($data){
        return parent::isError($data);
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

        $table = 'estimates';
        $where = null;
        return parent::getOptions($table,$where);

    }

    /*
     * オプションリスト
    */
    function getTreeOptions($table = 'estimates'){

        return parent::getTreeOptions($table);
    }

    /*
     * リスト
    */
    function getOneFieldById($id,$field){

        $table = 'estimates';
        return parent::getOneFieldById($table,$id,$field);
    }

    /*
     * リスト
    */
    function getOneEntityById($id){

        // SQL処理
        $sql =  "SELECT * FROM estimates WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);

        $arrRes = null;
        if(count($array) > 0){
            $arrRes = $array[0];
            $dl_path = null;
            $file_path = $arrRes["estimates"]["file_path"];
            $path = WWW_ROOT.'images/files/'.$file_path;
            $url_path = HOME_URL.'images/files/'.$file_path;
            if(file_exists($path) && !empty($file_path)){
                $dl_path = $url_path;
            }

            $arrRes["estimates"]["dl_path"] = $dl_path;
        }
        return $arrRes;
    }

    /*
     * リスト
    */
    function getListEntityWhere($const_name){

        $table = 'estimates';
        $where = "level = 1 AND const_name = '$const_name'";
        $where .=  " ORDER BY value ASC";

        return parent::getListEntityWhere($table, $where);

    }

    function isDelete($data){

        $array_data = $data["del"];
        foreach($array_data as $id){

            // ファイル削除
            $this->isFileDelete($id);
        }

        return parent::isDelete($data);

    }

    /*
     * 登録更新処理
    */
    function isSave($data){
        return $this->save($data);
    }

    var $sort_value = null;

    /*
     * ページング
    */
    function setWhereSortVal($param){
        $this->sort_value = $param;
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
        $sql .=  "     FROM estimates ";
        $sql .=  "    WHERE del_flg <= 0 ";

        if(!empty($this->sort_value)){
            if($this->sort_value == "new"){
                $sql .=  "   ORDER BY created DESC,modified DESC ";
                //            }else if($this->sort_value == "new"){
                //                $sql .=  "   ORDER BY merch_basics.created DESC ,merch_basics.modified DESC ";
                //            }else if($this->sort_value == "id_desc"){
                //                $sql .=  "   ORDER BY merch_basics.id DESC ";
                //            }else if($this->sort_value == "id_asc"){
                //                $sql .=  "   ORDER BY merch_basics.id ASC ";
                //            }else if($this->sort_value == "name_desc"){
                //                $sql .=  "   ORDER BY merch_basics.name DESC ";
                //            }else if($this->sort_value == "name_asc"){
                //                $sql .=  "   ORDER BY merch_basics.name ASC ";
                //            }else if($this->sort_value == "price_desc"){
                //                $sql .=  "   ORDER BY merch_items.sales_price DESC ";
                //            }else if($this->sort_value == "price_asc"){
                //                $sql .=  "   ORDER BY merch_items.sales_price ASC ";
                //            }else if($this->sort_value == "sortdate"){
                //                $sql .=  "   ORDER BY merch_basics.created DESC ";
                //            }else if($this->sort_value == "sortprice"){
                //                $sql .=  "   ORDER BY merch_items.sort_fee ASC ";
            }
        }else{
            $sql .=  "   ORDER BY rank,modified DESC ";
        }

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $arrRes["list"][$key] = $row["estimates"];

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
        return parent::getAllEntity('estimates');
    }

    /*
     * リスト
    */
    function isFileDelete($id){

        // SQL処理
        $sql =  "SELECT * FROM estimates WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);

        if(count($array) == 0){
            return null;
        }
        $path = explode("/",$array[0]["estimates"]["file_path"]);
        $dirPath = WWW_ROOT.'images/files/'.$path[0];
        if(!file_exists($dirPath)){
            return null;
        }
        if ($dir = opendir($dirPath)) {
            while (($file = readdir($dir)) !== false) {
                if ($file != "." && $file != "..") {
                    $path = $dirPath."/".$file;
                    @unlink( $path );
                }
            }
            closedir($dir);
        }
        rmdir($dirPath);

        $data["id"] = $array[0]["estimates"]["id"];
        $data["file_path"] = null;
        $data["file_name"] = null;
        $this->save($data, true, array("file_path","file_name"));

    }

}
?>