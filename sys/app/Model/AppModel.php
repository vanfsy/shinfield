<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

    var $validate = array();
    var $error_messages = null;

    // エラーメッセージ取得
    function getErrors(){
        return $this->error_messages;
    }

    /*
     * エラー判定
    */
    function isError($data){
        $table = $this->table;
        $boolean = false;
        if (empty($data)) return $boolean;

        // エラーチェック
        $this->isValid();
        $this->set($data);
        $fields = $this->getFields();
        foreach( $fields as $filed_name => $val ){
            $this->error_messages[$filed_name] = $val;
            foreach( $this->invalidFields() as $e_key => $e_val ){
                if($e_key == $filed_name){
                    $this->error_messages[$filed_name] = $e_val[0];
                }
            }
        }
        if(!$this->validates($data)){
            return $boolean;
        }

        // エラーチェック
        $boolean = true;
        if(count($this->invalidFields()) > 0) $boolean = false;
        return $boolean;
    }

    /*
     * 初期フィールド
    */
    function getDefFields(){
        $table = $this->table;
        $sql =  "SHOW COLUMNS FROM `$table`";
        $array = $this->query($sql);
        $arrRes = array();
        foreach($array as $row){
            $val = $row["COLUMNS"];
            $arrRes[$val["Field"]] = $val["Default"];
        }

        return $arrRes;

    }

    /*
     * フィールド
    */
    function getFields($where = null){
        $table = $this->table;

        $sql =  "SHOW COLUMNS FROM `$table` $where";
        $array = $this->query($sql);
        $arrRes = array();
        foreach($array as $row){
            $val = $row["COLUMNS"];
            $arrRes[$val["Field"]] = null;
        }

        return $arrRes;

    }

    /*
     * オプションリスト
    */
    function getOptions($where = ""){

        $table = $this->table;

        // SQL処理
        if ($where != null) $where = "AND ".$where;

        $sql =  "SELECT * FROM $table WHERE del_flg <= 0 $where ORDER BY rank,id";
        //        $sql = Sanitize::clean($sql);
        $array = $this->query($sql);
        $arrRes = array();
        foreach($array as $row){
            $val = $row[$table];
            $arrRes[$val["id"]] = " ".$val["name"];
        }

        return $arrRes;
    }

    /*
     * リスト
    */
    function getOneFieldById($id,$field){
        $table = $this->table;

        // SQL処理
        $sql =  "SELECT `$field` FROM `$table` WHERE id = '$id' AND del_flg <= 0";
        //        $sql = Sanitize::clean($sql);
        $array = $this->query($sql);
        $res = null;
        if(count($array) > 0) $res = $array[0]["$table"][$field];
        return $res;
    }

    /*
     * リスト
    */
    function getOneEntityByParam($param,$field){

        // SQL処理
        $table = $this->table;
        $sql =  "SELECT * FROM `$table` WHERE `$field` = '$param' AND del_flg <= 0";
        $array = $this->query($sql);
        $res = null;
        if(count($array) > 0) $res = $array[0]["$table"];
        return $res;
    }

    /*
     * リスト
    */
    function getListEntityByParam($param,$field){

        // SQL処理
        $table = $this->table;
        $sql =  "SELECT * FROM `$table` WHERE `$field` = '$param' AND del_flg <= 0";
        $array = $this->query($sql);
        $res = null;
        if(count($array) > 0) $res = $array;
        return $res;
    }

    /*
     * リスト
    */
    function getListEntityWhere($where = null){

        $table = $this->table;

        // SQL処理
        if(!empty($where)) $where = " AND ".$where;
        $sql =  "SELECT * FROM `$table` WHERE del_flg <= 0 $where";
        //        $sql = Sanitize::clean($sql);
        $array = $this->query($sql);
        return $array;
    }

    /*
     * リスト
    */
    function getArrCechckedLists($fieldname, $where = null){

        $table = $this->table;
        // SQL処理
        $sql =  "SELECT * FROM `$table` WHERE $where AND del_flg <= 0";
        //        $sql = Sanitize::clean($sql);
        $array = $this->query($sql);
        $arrRes = array();
        foreach($array as $key => $val){
            $arrRes[$key] = $val[$table][$fieldname];
        }
        return $arrRes;
    }

    /*
     * オプションリスト
    */
    function getTreeOptions($level = 2, $where = ""){

        $table = $this->table;
        // SQL処理
        if ($where != null) $where = "AND ".$where;

        $sql_top =  "SELECT * FROM $table WHERE del_flg <= 0 AND parent_id = 0 $where ORDER BY rank";
        //        $sql_top = Sanitize::clean($sql_top);
        $array_top = $this->query($sql_top);

        $arrRes = array();
        foreach($array_top as $row){
            $val = $row[$table];
            $arrRes[$val["id"]] = " ".$val["name"];

            $parent_id = $val["id"];
            $sql =  "SELECT * FROM $table WHERE del_flg <= 0 AND parent_id = $parent_id $where ORDER BY rank";
            //            $sql = Sanitize::clean($sql);
            $array = $this->query($sql);
            foreach($array as $row_ch){
                $val_ch = $row_ch[$table];
                $arrRes[$val_ch["id"]] = " 　　".$val_ch["name"];

                $parent_id2 = $val_ch["id"];
                $sql2 =  "SELECT * FROM $table WHERE del_flg <= 0 AND parent_id = $parent_id2 $where ORDER BY rank";
                //                $sql2 = Sanitize::clean($sql2);
                $array2 = $this->query($sql2);
                foreach($array2 as $row_ch2){
                    $val_ch2 = $row_ch2[$table];
                    $arrRes[$val_ch2["id"]] = " 　　　　".$val_ch2["name"];

                    $parent_id3 = $val_ch2["id"];
                    $sql3 =  "SELECT * FROM $table WHERE del_flg <= 0 AND parent_id = $parent_id3 $where ORDER BY rank";
                    //                    $sql3 = Sanitize::clean($sql3);
                    $array3 = $this->query($sql3);
                    foreach($array3 as $row_ch3){
                        $val_ch3 = $row_ch3[$table];
                        $arrRes[$val_ch3["id"]] = " 　　　　　　".$val_ch3["name"];
                    }
                }
            }
        }

        return $arrRes;
    }

    /*
     * 登録更新処理
    */
    /*
    function isSave($data,$valid = true){
        $table = $this->table;

        $boolean = false;
        if (empty($data)) return $boolean;

        // エラーチェック
        if($valid === true){
            $this->isValid();
            $this->set($data);
            if(!$this->validates($data)){
                return $boolean;
            }
        }

        // 編集不可チェック
        if(isset($data["id"])){
            $cnt = $this->find('count',array('conditions' => array('id' => $data["id"],'del_flg' => '-1')));
            if($cnt > 0) return $boolean;
        }

        $arrFields = array();
        foreach($data as $key => $row){
            $arrFields[] = $key;
        }

        if(@$data["id"] > 0 ){
            $arrFields[] = "modified";
        }else{
            $arrFields[] = "rank";
            $arrFields[] = "modified";
            $arrFields[] = "created";

            $total = $this->find('count',array('conditions' => array('del_flg <= ' => '0')));
            $data["rank"] = $total + 1;

        }

        // 登録処理
        $this->save($data, true, $arrFields);

        return true;

    }
*/

    /*
     * 登録更新処理
    */
    function isDelete($data){

        $array_data = $data["del"];
        foreach($array_data as $row){

            // 編集不可チェック
            $cnt = $this->find('count',array('conditions' => array('id' => $row,'del_flg' => '-1')));
            if($cnt > 0) return false;

            // 登録処理
            $data["id"] = $row;
            $data["del_flg"] = "1";
            $this->save($data, true, array('del_flg','modified'));
        }

        return true;

    }

    /**
     * ページング処理
     */

    var $pgnum = 0;
    var $disp_num = 0;
    var $max_num = 0;
    var $table_name = null;
    var $pg_total = 0;

    function setDispNum($disp_num) {
        $this->disp_num = $disp_num;
    }

    function setPgNum($pgnum) {
        $this->pgnum = $pgnum;
    }

    function getPagingString($sql_string) {
        // ページング処理
        if($this->pgnum > 1){
            $start = ( $this->pgnum - 1 ) * $this->disp_num;
        }else{
            $start = 0;
        }

        $sql_string .=  " LIMIT $start ,".$this->disp_num." ";
        return $sql_string;
    }

    function getPagingString100($sql_string) {
        // ページング処理
        if($this->pgnum > 1){
            $start = ( $this->pgnum - 1 ) * $this->disp_num;
        }else{
            $start = 0;
        }
        $offset = $this->disp_num;
        if($start >= 100){
            $start = 100;
        }
        if( $start + $offset >=100){
            $offset  = 100- $start;
        }

        $sql_string .=  " LIMIT $start ,".$offset." ";

        return $sql_string;
    }
    /*
     * 管理画面 ページング前項
    */
    function getPgPrev(){
        $pg = 0;
        if($this->pgnum > 0) $pg = $this->pgnum - 1;
        return $pg;
    }

    /*
     * 管理画面 ページング次項
    */
    function getPgNext(){
        $pg = 2;
        if($this->pgnum > 0) $pg = $this->pgnum + 1;
        if($this->pgnum >= $this->max_num ) $pg = 0;
        return $pg;
    }

    /*
     * 管理画面 ページング総数
    */
    function getArrPgList($sql){

        $array = $this->query($sql);

        $cnt = count($array);
        $this->rec_total = $cnt;
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
     * 管理画面 ページング現在のPG
    */
    function getCurrentPg(){
        $current_pg = 1;
        if(!is_numeric($this->pgnum)) $this->pgnum = 1;
        if($this->pgnum > 0) $current_pg = $this->pgnum;
        return $current_pg;
    }

    /*
     * レコード総数
    */
    function getRecordTotal(){
        return $this->rec_total;
    }

    /*
     * 有効全件リスト取得
    */
    function getAllEntity(){
        $table = $this->table;
        $sql = "SELECT * FROM `$table` WHERE del_flg <= 0 ORDER BY id,rank";
        //        $sql = Sanitize::clean($sql);
        $array = $this->query($sql);

        $arrRes = array();
        foreach($array as $key => $val){
            $arrRes["$table"][$key] = $val["$table"];
        }
        return $arrRes;
    }

    /*
     * ランクソート
    */
    function rankSort($data){

        $arrRank = $data["sort"];
        $arrPriorRank1 = array();
        $arrPriorRank2 = array();
        $arrNewRank    = array();

        // ランク設定
        foreach($arrRank as $key => $val){
            $arr = explode(",",$key);
            if($arr[0] != $val){
                $arrPriorRank1[$val][$arr[0]] = $arr[1];
            }else{
                $arrPriorRank2[$val] = $arr[1];
            }
        }

        if(count($arrPriorRank1) == 0){
            return;
        }

        Ksort($arrPriorRank1);
        Ksort($arrPriorRank2);

        // 優先ランク設定
        $ranks = array_keys($arrPriorRank1);
        $rank = $ranks[0];
        foreach($arrPriorRank1 as $key => $rows){
            if($rank < $key) $rank = $key;
            foreach($rows as $i => $val){
                $arrNewRank[$rank]["id"] = $val;
                $arrNewRank[$rank]["origin_rank"] = $i;
                ++$rank;
            }
        }

        Ksort($arrNewRank);

        //ソートする上限と下限の取得
        $min = 10000000000000;
        $max = 0;
        foreach($arrRank as $key => $val){
            $arr = explode(",",$key);
            if($arr[0] != $val){
                $base_rank = $arr[0];
                $target_rank = $val;
                if($base_rank < $target_rank){
                    if($min > $base_rank) $min = $base_rank;
                    if($max < $target_rank) $max = $target_rank;
                }else if($base_rank > $target_rank){
                    if($min > $target_rank) $min = $target_rank;
                    if($max < $base_rank) $max = $base_rank;
                }
            }
        }

        // 更新情報設定
        $arrUpDatas = array();
        for($i = $min;$i <= $max;++$i){
            if(isset($arrNewRank[$i])){
                $arrUpDatas[$i] = $arrNewRank[$i]["id"];
            }else{
                $arrUpDatas[$i] = 0;
            }
        }
        for($i = $min;$i <= $max;++$i){
            if(isset($arrPriorRank2[$i])){
                foreach($arrUpDatas as $rank => $id){
                    if($id == 0){
                        $arrUpDatas[$rank] = $arrPriorRank2[$i];
                        break;
                    }
                }
            }
        }

        // 更新処理
        foreach($arrUpDatas as $rank => $id){
            $data["id"] = $id;
            $data["rank"] = $rank;
            $this->save($data, true, array('rank','modified'));
        }

    }

    /*
     * ランクソート
    */
    function rankCondSort($data,$arrCond = null){

        $arrRank = $data["sort"];
        $table_name = $this->table;
        $where = null;
        if(is_array($arrCond)){
            foreach($arrCond as $key => $val){
                $where .= " AND `$key` = '$val' ";
            }

        }

        $sql =  "SELECT * FROM `$table_name` WHERE del_flg <= 0 $where ORDER BY rank";
        $array = $this->query($sql);
        $arrRank2 = array();
        foreach($array as $row){
            $id = $row[$table_name]["id"];
            $rank = $row[$table_name]["rank"];
            $key = $rank.",".$id;

            $flg = true;
            foreach($arrRank as $i => $val){
                $arrKey = explode(",",$i);
                if($arrKey[1] == $id){
                    $arrRank2[$i] = $val;
                    $flg = false;
                }
            }
            if($flg){
                $arrRank2[$key] = $rank;
            }
        }

        // ランク設定
        $dum_rank = 1;
        foreach($arrRank2 as $key => $val){
            $arr = explode(",",$key);
            if($arr[0] != $val){
                $arrPriorRank1[$val][$arr[0]] = $arr[1];
            }else{
                $arrPriorRank2[$dum_rank] = $arr[1];
                ++$dum_rank;
            }
        }
        Ksort($arrPriorRank1);

        // 優先ランク設定
        $ranks = array_keys($arrPriorRank1);
        $rank = $ranks[0];
        foreach($arrPriorRank1 as $key => $rows){
            if($rank < $key) $rank = $key;
            foreach($rows as $i => $val){
                $arrNewRank[$rank] = $val;
                ++$rank;
            }
        }

        $max = count($array);
        for($i = 0;$i < $max;++$i){
            $rank = $i + 1;
            if(!isset($arrNewRank[$rank])){
                $arrNewRank[$rank] = "none";
            }
        }

        $dum_rank = 1;
        foreach($arrNewRank as $rank => $row){
            if($row == "none"){

                if(isset($arrPriorRank2[$dum_rank])){
                    $arrNewRank[$rank] = @$arrPriorRank2[$dum_rank];
                    ++$dum_rank;
                }
            }
        }
        ksort($arrNewRank);

        // 更新処理
        $rank = 1;
        foreach($arrNewRank as $val){
            if($val != "none"){
                $data["id"] = $val;
                $data["rank"] = $rank;
                $this->save($data, true, array('rank','modified'));
                ++$rank;
            }
        }

    }

    /*
     * 重複検証のカスタマイズ
    */

    function isUnique($fields, $or = true) {
        if (!is_array($fields)) {
            $fields = func_get_args();
            if (is_bool($fields[count($fields) - 1])) {
                $or = $fields[count($fields) - 1];
                unset($fields[count($fields) - 1]);
            }
        }

        foreach ($fields as $field => $value) {
            if (is_numeric($field)) {
                unset($fields[$field]);

                $field = $value;
                if (isset($this->data[$this->alias][$field])) {
                    $value = $this->data[$this->alias][$field];
                } else {
                    $value = null;
                }
            }

            if (strpos($field, '.') === false) {
                unset($fields[$field]);
                $fields[$this->alias . '.' . $field] = $value;
            }
        }
        if ($or) {
            $fields = array('or' => $fields);
        }
        if (!empty($this->id)) {
            $fields[$this->alias . '.' . $this->primaryKey . ' !='] =  $this->id;
        }
        $return = ($this->find('count', array('conditions' => array($fields,'del_flg <= ' => '0'), 'recursive' => -1)) == 0);
        return $return;
    }

    /*
     * 複製処理
    */

    function addCopyById($id ,$field_name,$param_name = null,$param_value = null) {

        $table_name = $this->table;
        $sql = "SELECT * FROM ".$table_name." WHERE del_flg <= 0 AND $field_name = '$id'";
        //        $sql = Sanitize::clean($sql);
        $array = $this->query($sql);
        foreach($array as $row){
            $sql = "SELECT count(*) as count FROM ".$table_name." WHERE del_flg <= 0";
            //            $sql = Sanitize::clean($sql);
            $array = $this->query($sql);
            $rank = ($array[0][0]["count"]) + 1;

            $data = $row[$table_name];
            $arrFields = array_keys($data);
            $data["id"] = null;
            $data["rank"] = $rank;
            $data["modified"] = null;
            $data["created"] = null;
            if(!empty($param_name)) $data[$param_name] = $param_value;
            $this->save($data, true, $arrFields);
        }

    }

    /*
     * ソート再処理
    */

    function reSort($array,$pgnum,$disp_num) {

        if(count($array) == 0) return array();
        $start_rank = 1;
        if($pgnum > 1){
            $start_rank = ($pgnum - 1) * $disp_num + 1;
        }

        $key = array_keys($array[0]);
        $table = $key[0];

        $arrRes = $array;
        foreach($array as $i => $row){

            // ランク更新
            $rank = $row[$table]["rank"];
            $id = $row[$table]["id"];
            if($rank != $start_rank){
                $data["id"] = $id;
                $data["rank"] = $start_rank;
                if($id > 0) $this->save($data, true, array('rank','modified'));
            }

            $arrRes[$i][$table]["rank"] = $start_rank;

            ++$start_rank;
        }

        return $arrRes;
    }

    function paramSanitize($param) {
        $param = trim($param);
        $param = str_replace("'"," ",$param);
        $param = Sanitize::clean($param);

        return $param;
    }

    /*** 以下 独自バリデーション *******************************************************/

    function katakana($check, $strict = false) {
        $check = is_array($check) ? array_shift($check) : $check;
        return preg_match('/^(?:\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xB6]|\xE3\x83\xBC|\x20|\xE3\x80\x80)+$/',$check);
    }

    function hiragana($check, $strict = false) {
        $check = is_array($check) ? array_shift($check) : $check;
        $check = str_replace(" ","",$check);
        $check = str_replace("　","",$check);
        return preg_match( '/^[ ぁ-ん ]+$/u',$check);
    }

    /**
     * 英数字記号あり
     *
     * @param mixed $check Value to check
     * @return boolean Success
     * @access public
     */
    function isCode($check) {
        $check = is_array($check) ? array_shift($check) : $check;
        return preg_match('/^[a-zA-Z0-9\_\+\-\@\.\%]+$/',$check);
    }

    /**
     * TEL用 数字記号あり
     *
     * @param mixed $check Value to check
     * @return boolean Success
     * @access public
     */
    function isTelphone($check) {
        $check = is_array($check) ? array_shift($check) : $check;
        return preg_match('/^[0-9\-]+$/',$check);
    }

    /*
     * 確認検証
    */
    function isConfirm($fields,$table) {
        $flg = false;

        foreach ($fields as $field => $value) {

            $confirm = $field."_confirm";
            $conf_value = $this->data[$table][$confirm];
            if($value == $conf_value){
                $flg = true;
            }
        }
        return $flg;
    }

    /**
     * 英数字記号あり
     *
     * @param mixed $check Value to check
     * @return boolean Success
     * @access public
     */
    function isControllerName($check) {
        $check = is_array($check) ? array_shift($check) : $check;
        return preg_match('/^[a-zA-Z0-9]+$/',$check);
    }

    /**
     * 英字のみ
     *
     * @param mixed $check Value to check
     * @return boolean Success
     * @access public
     */
    function isAlpha($check) {
        $check = is_array($check) ? array_shift($check) : $check;
        return preg_match('/^[a-zA-Z]+$/',$check);
    }

    /**
     * スラッグ用
     *
     * @param mixed $check Value to check
     * @return boolean Success
     * @access public
     */
    function isSlug($check) {
        $check = is_array($check) ? array_shift($check) : $check;
        return preg_match('/^[a-zA-Z0-9_]+$/',$check);
    }

    /**
     * 全角対応文字数チェック
     */
    function minLengthJp($check, $min) {
        $check_str = array_shift($check);
        $length = mb_strlen($check_str, mb_detect_encoding($check_str));
        return ($length >= $min);
    }

    /**
     * 全角対応文字数チェック
     */
    function maxLengthJp($check, $max) {
        $check_str = array_shift($check);
        $length = mb_strlen($check_str, mb_detect_encoding($check_str));
        return ($length <= $max);
    }

    /**
     * 全角対応文字数チェック
     */
    function isEqControllerName($check) {
        $check = is_array($check) ? array_shift($check) : $check;
        $sql = "SELECT * FROM page_directories WHERE del_flg <= 0 AND controller_name = '$check'";
        $array = $this->query($sql);
        if(count($array) > 0){
            return false;
        }else{
            return true;
        }
    }

    /**
     * ゆるゆるチェック
     */
    public function emailExt($check) {
        $check = is_array($check) ? array_shift($check) : $check;
        return preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\.\+\_\-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/',$check);
    }

    //For PHP 5.2.x or earlier
    public function emailExtended($data, $deep = false) {
        $pattern = '/.+@(docomo.ne.jp|ezweb.ne.jp)$/i';
        $check = preg_replace_callback($pattern, create_function('$matches', '
        $patterns = array("/\.{2,}/", "/\.@/");
        $replacements = array(".", "@");
        return preg_replace($patterns, $replacements, $matches[0]);
        '), array_shift($data));
        return Validation::email($check, $deep);
    }

    public function begin() {
        $dataSource = $this->getDataSource();
        $dataSource->begin($this);
    }

    public function commit() {
        $dataSource = $this->getDataSource();
        $dataSource->commit($this);
    }

    public function rollback() {
        $dataSource = $this->getDataSource();
        $dataSource->rollback($this);
    }
}

