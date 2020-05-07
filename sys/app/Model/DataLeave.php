<?php
App::uses('Sanitize', 'Utility');

class DataLeave extends AppModel {
    var $validate = array();

    /*
     * 入力値検証
     */

    function isValid(){

        $validate["email"]["email"]                = array("rule" => "email", "allowEmpty" => true, "message" => "正しいメールを入力してください。");
        $validate["email"]["isUnique"]             = array("rule" => "isUnique", "allowEmpty" => true, "message" => "そのメールは既に登録済みです。");
        $validate["code"]["isCode"]                = array("rule" => "isCode", "allowEmpty" => true, "message" => "IDは英数字・メールアドレスのみです。");
        $validate["code"]["isUnique"]              = array("rule" => "isUnique", "allowEmpty" => true, "message" => "そのIDは既に登録済みです。");
        $validate["code"]["maxLength"]             = array("rule" => array("maxLength",255), "allowEmpty" => true, "message" => "制限文字数を超えています。");

        $validate["data_branch_code"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["code"]["notEmpty"]              = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["parent_id"]["notEmpty"]         = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["name"]["notEmpty"]              = array("rule" => "notEmpty", "message" => "必須項目です。");

        $this->validate = $validate;

    }

    /*
     * エラーチェック
    function isError($data){
        return parent::isError($data,'data_leaves');
    }
     * */

    /*
     * フィールド
    function getFields($table = 'data_leaves'){
        return parent::getFields($table);
    }
     */

    /*
     * オプションリスト
    function getOptions(){
        $table = 'data_leaves';
        $where = null;
        return parent::getOptions($table,$where);
    }
     */

    /*
     * オプションリスト
     * 配列キー：ID
     * 配列値：名称
    function getTreeOptions($data_branch_code,$level = 1){

        $list = $this->getTreeEntityByBranchCode($data_branch_code);

        $arrRes = array();
        if($level == 1){

            return $arrRes;

        }else if($level > 1){

            $i = 0;
            foreach($list as $row){
                $id   = $row["id"];
                $name = $row["name"];
                $arrRes[$id] = $name;
                if(isset($row["child"])){
                    foreach($row["child"] as $c_i => $c_row){
                        $c_id   = $c_row["id"];
                        $c_name = $c_row["name"];
                        $arr_child[$c_id] = "　".$c_name;
                        $arrRes[$name] = $arr_child;
                    }
                }
                ++$i;
            }

            return $arrRes;

        }

    }
     */

    /*
     * ツリーリスト
     */
    function getTreeEntityByBranchCode($data_branch_code){

        $sql = "SELECT id FROM data_branches WHERE del_flg <= 0 AND code = '$data_branch_code'";
        $array = $this->query($sql);
        $top_parent_id = $array[0]["data_branches"]["id"];

        $sql = "SELECT MAX(level) AS max_level FROM data_leaves WHERE del_flg <= 0 AND data_branch_code = '$data_branch_code'";
        $array = $this->query($sql);
        $max_level = $array[0][0]["max_level"];
        $max = $max_level + 1;

        $arrTemp = array();
        for( $i=1;$i<$max;++$i ){
            $sql = "SELECT * FROM data_leaves WHERE del_flg <= 0 AND data_branch_code = '$data_branch_code' AND level = '$i' ";
            $array = $this->query($sql);
            foreach($array as $row){
                $parent_id   = $row["data_leaves"]["parent_id"];
                $id          = $row["data_leaves"]["id"];
                $name        = $row["data_leaves"]["name"];
                $tempRow["name"] = $name;
                $tempRow["id"]   = $id;
                $arrTemp[$i][$parent_id][] = $tempRow;
            }
        }
        krsort($arrTemp);

        $start_level = $max_level;
        $arrRes = array();
        for( $i=$start_level;$i>0;--$i ){
            foreach($arrTemp[$i] as $pi => $v){
                foreach($v as $cnt => $val){
                    $id   = $val["id"];
                    $name = $val["name"];
                    $arrRes[$pi][$cnt]     = $val;

                    foreach($arrRes as $pi2 => $v2){
                        if($pi2 == $id){
                            $arrRes[$pi][$cnt]["child"] = $v2;
                        }
                    }
                }
            }
        }

        return @$arrRes[$top_parent_id];
    }

    /*
     * リスト
    function getOneFieldById($id,$field){

        $table = 'data_leaves';
        return parent::getOneFieldById($table,$id,$field);
    }
     */

    /*
     * リスト
     */
    function getOneEntityById($id){

        // SQL処理
        $sql =  "SELECT * FROM data_leaves WHERE id = '$id' AND del_flg = 0";
        $array = $this->query($sql);
        return $array[0];
    }

    /*
     * 削除処理
    function isDelete($data){

        // マスターデータをクリアする
        Cache::write("arrMasterData",null);

        return parent::isDelete($data);

    }
     */

    /*
     * 登録更新処理
    function isSave($data){

        // SQL処理
        if($data["data_leaves"]["level"] > 1){
            $parent_id = $data["data_leaves"]["parent_id"];
            $sql =  "SELECT * FROM data_leaves WHERE id = '$parent_id' AND del_flg <= 0";
            $array = $this->query($sql);
            $parent_code = @$array[0]["data_leaves"]["code"];
            $data["data_leaves"]["parent_code"] = $parent_code;
        }
        $flg = parent::isSave($data['data_leaves']);

        // マスターデータを配列にしてキャッシュする
        $data = $this->getArrMasterData();
        Cache::write("arrMasterData",$data);

        return $flg;
    }
     */

    /*
     * 一括更新処理
     */
    function isMassSave($data,$field_name){

        if(count($data["data_leaves"][$field_name]) == 0){
            return false;
        }

        foreach($data["data_leaves"][$field_name] as $key => $val){

            $udata["id"] = $key;
            $udata[$field_name] = $val;
            $this->save($udata, true, array("$field_name"));

        }

        // マスターデータを配列にしてキャッシュする
        $data = $this->getArrMasterData();
        Cache::write("arrMasterData",$data);

        return true;
    }

    var $where_value;

    /*
     * ページング
     */
    function setWhereVal($param){
        $this->where_value = $param;
    }

    /*
     * ページング
     */
    function getPagingEntity($disp_num,$pgnum){

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

/*
        $where = null;
        if(count($this->where_value) > 0){
            foreach($this->where_value as $key => $val){
                if($key != "parent_id" && $val != "non_attach"){
                    $where .= " AND `$key` = '$val'";
                }
            }
        }
*/

        $where = null;
        if(count($this->where_value) > 0){
            foreach($this->where_value as $key => $val){
                $where .= " AND `$key` = '$val'";
            }
        }

        // SQL処理
        $sql =  "";
        $sql .=  "   SELECT * ";
        $sql .=  "     FROM data_leaves ";
        $sql .=  "    WHERE del_flg <= 0 ";
        $sql .=  $where;
        $sql .=  "    ORDER BY level , parent_id,id ";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $arrRes["list"][$key]           = $row["data_leaves"];

        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        return $arrRes;
    }

    /*
     * 管理画面 ページング総数
     */
    function getArrPgList($table_name,$where = null){

        // SQL処理
        $sql =  "";
        $sql .=  "   SELECT count(*) as count ";
        $sql .=  "     FROM data_leaves ";
        $sql .=  "    WHERE del_flg <= 0 ";
        $sql .=  $where;

        $array = $this->query($sql);
        $cnt = $array[0][0]["count"];
        $plus = $cnt % $this->disp_num;
        $max_num = ($cnt - $plus) / $this->disp_num;
        if($plus > 0){
            $max_num = $max_num + 1;
        }
        $this->max_num = $max_num;

        $array = array();
        for($i=0;$i < $max_num;$i++){
            $array[$i] = $i + 1;
        }

        return $array;
    }

    /*
     * リスト
     */
    function getTreeNameById($id){

        $sql_top =  "SELECT * FROM data_leaves WHERE del_flg = 0 AND id = '$id'";
        $array_top = $this->query($sql_top);

        $name = "親カテゴリなし";
        $i    = 0;
        if(count($array_top)>0){
            $name = $array_top[0]["data_leaves"]["name"];
            $i    = $array_top[0]["data_leaves"]["parent_id"];
        }

        while( $i >= 1 ){
            $sql       =  "SELECT * FROM data_leaves WHERE del_flg = 0 AND id = '$i'";
            $array     = $this->query($sql);
            $name_next = $array[0]["data_leaves"]["name"];
            $i         = $array[0]["data_leaves"]["parent_id"];
            $name = $name_next." > ".$name;
        }

        return $name;
    }

    /*
     * 有効全件リスト取得
    function getAllEntity(){
        return parent::getAllEntity('data_leaves');
    }
     */

    /*
     * リスト
     */
    function getOneEntityByCode($code){

        // SQL処理
        Sanitize::clean($code);
        $sql =  "SELECT * FROM data_leaves WHERE code = '$code' AND del_flg <= 0";
        $array = $this->query($sql);
        if(count($array) > 0){
            return $array[0];
        }else{
            return false;
        }
    }

    /*
     * リスト
     */
    function getEntityByParentCode($parent_code){

        // SQL処理
        Sanitize::clean($parent_code);
        $sql =  "SELECT * FROM data_leaves WHERE parent_code = '$parent_code' AND del_flg <= 0";
        $array = $this->query($sql);
        return $array;
    }

    /*
     * リスト
     */
    function getEntityByParentId($parent_id){

        // SQL処理
        Sanitize::clean($parent_id);
        $sql =  "SELECT * FROM data_leaves WHERE parent_id = '$parent_id' AND del_flg <= 0";
        $array = $this->query($sql);
        return $array;
    }

    /*
     * リスト
     */
    function getParentOptions($data_branch_code,$level){

        // SQL処理
        Sanitize::clean($data_branch_code);
        Sanitize::clean($level);
        $sql =  "SELECT * FROM data_leaves WHERE data_branch_code = '$data_branch_code' AND level = '$level' AND del_flg <= 0";
        $array = $this->query($sql);

        $arrRes[""] = "未設定";
        if(count($array) > 0){
            foreach($array as $row){
                $val = $row["data_leaves"];
                $arrRes[$val["id"]] = " ".$val["name"];
            }
        }else{
            // 該当がない場合はブランチを親とする
            $sql =  "SELECT * FROM data_branches WHERE code = '$data_branch_code' AND del_flg <= 0";
            $array = $this->query($sql);
            $val = $array[0]["data_branches"];

            // 所属データがある場合
            $attach_id = $val["attach_id"];
            if($attach_id > 0){
                $sql =  "SELECT * FROM data_leaves WHERE data_branch_id = '$attach_id' AND del_flg <= 0";
                $array = $this->query($sql);
//echo $sql;
                foreach($array as $row){
                    $val = $row["data_leaves"];
                    $arrRes[$val["id"]] = " ".$val["name"];
                }
            }else{
                $arrRes[$val["id"]] = " ".$val["name"];
            }
        }

        return $arrRes;
    }

    /*
     * 自動コード取得
     */
    function getAutoByBranchCode($data_branch_code){

        $new_code = $this->getNextByBranchCode($data_branch_code,$num = null);
        return $new_code;
    }

    /*
     * 自動コード取得
     */
    function getNextByBranchCode($data_branch_code,$num = null){

        // SQL処理
        Sanitize::clean($data_branch_code);
        $sql =  "";
        $sql .=  "  SELECT MAX(CAST(REPLACE(code,'$data_branch_code','') AS SIGNED)) AS max_code ";
        $sql .=  "    FROM data_leaves ";
        $sql .=  "   WHERE data_branch_code = '$data_branch_code' ";
        $sql .=  "     AND del_flg <= 0 ";
        $array = $this->query($sql);
        $new_code = $data_branch_code."01";
        if(count($array) > 0){
            $code = $array[0][0]["max_code"];
            $num = str_replace($data_branch_code,"",$code);
            $new_code = $data_branch_code.sprintf('%02d', $num + 1);
        }
        return $new_code;
    }

    /*
     * マスターデータ用配列
     */
    function getArrMasterData(){

        $sql_top =  "SELECT * FROM data_branches WHERE del_flg <= 0 ";
        $array_top = $this->query($sql_top);

        $arrRes = array();
        foreach( $array_top as $row ){
            $code = $row["data_branches"]["code"];
            $id = $row["data_branches"]["id"];

            $sql       =  "SELECT MAX(level) AS max_level FROM data_leaves WHERE del_flg <= 0 AND data_branch_code = '$code'";
            $array     = $this->query($sql);
            $max_level = $array[0][0]["max_level"];

            $sql       =  "SELECT * FROM data_leaves WHERE del_flg <= 0 AND data_branch_code = '$code'";
            $array     = $this->query($sql);
            foreach($array as $row_l){
                $arrRes["level".$row_l["data_leaves"]["level"]][$code][$row_l["data_leaves"]["code"]] = $row_l["data_leaves"]["name"];
                $arrRes["flat"][$code][$row_l["data_leaves"]["code"]] = $row_l["data_leaves"]["name"];
            }

            $arrRes["tree"][$code] = $this->getArrayCodeByBranchCode($code);

        }

        return $arrRes;
    }

    /*
     * ツリーリスト
     */
    function getArrayCodeByBranchCode($data_branch_code){

//        $sql = "SELECT code FROM data_branches WHERE del_flg <= 0 AND code = '$data_branch_code'";
//        $array = $this->query($sql);
//        $top_parent_code = $data_branch_code;

        $sql = "SELECT MAX(level) AS max_level FROM data_leaves WHERE del_flg <= 0 AND data_branch_code = '$data_branch_code' ";
        $array = $this->query($sql);
        $max_level = $array[0][0]["max_level"];
        $max = $max_level + 1;

        $arrTemp = array();
        for( $i=1;$i<$max;++$i ){
            $sql = "SELECT * FROM data_leaves WHERE del_flg <= 0 AND data_branch_code = '$data_branch_code' AND level = '$i' ORDER BY parent_code,code ";
            $array = $this->query($sql);
            foreach($array as $row){
                $level         = $row["data_leaves"]["level"];
                $parent_code   = $row["data_leaves"]["parent_code"];
                $code          = $row["data_leaves"]["code"];
                $name          = $row["data_leaves"]["name"];
                if($level == 1) $parent_code = $data_branch_code;
                $tempRow["name"] = $name;
                $tempRow["code"]   = $code;
                $tempRow["parent_code"]   = $row["data_leaves"]["parent_code"];
                $arrTemp[$i][$parent_code][] = $tempRow;
            }
        }
        krsort($arrTemp);

        $arrRes = $arrTemp[$max_level];
        if($max_level > 0){
            $start_level = $max_level - 1;
            for( $i=$start_level;$i>0;--$i ){
                foreach($arrTemp[$i] as $pi => $v){
                    foreach($v as $cnt => $val){
                        $code   = $val["code"];
                        $name = $val["name"];
                        $arrRes[$pi][$cnt]     = $val;
                        foreach($arrRes as $pi2 => $v2){
                            if($pi2 == $code){
                                $arrRes[$pi][$cnt]["child"] = $v2;
                            }
                        }
                    }
                }
            }

            return $arrRes[$data_branch_code];

        }else{

            return null;

        }

    }

/******* 以下拡張 ********/

    /*
     * ツリーリスト
     */
    function getArrWardByPrefCode($pref){

        $pref_code = $this->getOneEntityByCode($pref);
        $array = $this->getEntityByParentId($pref_code["data_leaves"]["id"]);
        $arrRes = array();
        if(count($array) > 0){
            foreach($array as $row){
                $id = $row["data_leaves"]["id"];
                $name = $row["data_leaves"]["name"];
                $array2 = $this->getEntityByBranchParentId("ward",$id);
                $arrRes[$name] = $array2;
            }
        }
        return $arrRes;
    }

    /*
     * ツリーリスト
     */
    function getArrLineByPrefCode($pref){

        $pref_code = $this->getOneEntityByCode($pref);
        $array = $this->getEntityByParentId($pref_code["data_leaves"]["id"]);
        $arrRes = array();
        if(count($array) > 0){
            foreach($array as $row){
                $id = $row["data_leaves"]["id"];
                $name = $row["data_leaves"]["name"];
                $array2 = $this->getEntityByBranchParentId("train_line",$id);
                $arrRes[$name] = $array2;
            }
        }
        return $arrRes;
    }

    /*
     * リスト
     */
    function getEntityByBranchParentId($data_branch_code ,$parent_id){

        // SQL処理
        Sanitize::clean($parent_id);
        $sql =  "SELECT * FROM data_leaves WHERE data_branch_code = '$data_branch_code' AND parent_id = '$parent_id' AND del_flg <= 0";
        $array = $this->query($sql);
        return $array;
    }

    /*
     * ブランドリスト
     */
    function getEntityByBrand(){

        // SQL処理
        $sql =  "SELECT * FROM data_leaves WHERE data_branch_code = 'brand'AND del_flg <= 0";
        $array = $this->query($sql);
        return $array;
    }

}
?>