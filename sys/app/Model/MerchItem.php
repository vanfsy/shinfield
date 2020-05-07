<?php
/*
 * Copyright (c) 2019, Mallento JAPAN
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms,
 * with or without modification,
 * are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * Neither the name of the <ORGANIZATION> nor the names of its contributors may
 * be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING,
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

class MerchItem extends AppModel {
    var $validate = array();
    var $actsAs = array("Paging");
    var $isTest = IS_TEST;

    /*
     * 入力値検証
     */

    function isValid(){

//        $validate["name"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["category_id"]["notEmpty"]     = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["sales_price"]["money"]        = array("rule" => "money", "message" => "このフィールドは半角数字の金額です。");
        $validate["sales_price"]["maxLength"]    = array("rule" => array("maxLength", 7), "message" => "10,000,000円までです。");
        $validate["stock_num"]["numeric"]        = array("rule" => "numeric", "allowEmpty" => true , "message" => "このフィールドは半角数字です。");
        $validate["arrival_date"]["date"]        = array("rule" => "date", "allowEmpty" => true , "message" => "このフィールドは日付（0000/00/00形式または0000-00-00形式）です。");

        $this->validate = $validate;

    }

    /*
     * エラーチェック
     * */
    function isError($data){
        return parent::isError($data,'merch_items');
    }

    /*
     * エラーチェック
     * */
    function isFormValid($data,$layout_id){

        $data["merch_specs"] = $data["merch_items"];
        $MerchSpecs = ClassRegistry::init('MerchSpecs');
        return $MerchSpecs->isFormValid($data,$layout_id);
    }

    /*
     * 入力値検証
     */
    function getFormErrors(){
        $MerchItemSpecs = ClassRegistry::init('MerchSpecs');
        return $MerchItemSpecs->getFormErrors();
    }

    /*
     * フィールド
     */
    function getFields($table = 'merch_items'){
        return parent::getFields($table);
    }

    /*
     * オプションリスト
     */
    function getOptions(){
        $table = 'merch_items';
        $where = null;
        return parent::getOptions($table,$where);
    }

    /*
     * オプションリスト
     */
    function getTreeOptions($table = 'merch_items'){

        return parent::getTreeOptions($table);
    }

    /*
     * リスト
     */
    function getOneFieldById($id,$field){

        return parent::getOneFieldById($id,$field);
    }

    /*
     * リスト
     */
    function getOneEntityById($id,$field = null){

        // SQL処理
        if(empty($field)) $field = "id";
        $sql =  "SELECT * FROM merch_items WHERE `$field` = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        $arrRes = $array[0];

        $MerchSpecs = ClassRegistry::init('MerchSpecs');
        $list = $MerchSpecs->getEntityOptions();
        foreach($list as $i => $v){
            $value = $arrRes["merch_items"][$v["merch_specs"]["field_name"]];
            if(!empty($value)){
                $arrRes["merch_items"]["options"][$i]["field"] = $v["merch_specs"]["field_name"];
                $arrRes["merch_items"]["options"][$i]["name"]  = $v["merch_specs"]["name"];
                $arrRes["merch_items"]["options"][$i]["list"]  = explode("\n",$value);
            }
        }
        return $arrRes;
    }

    /*
     * 商品規格情報
     * 商品構成ID
     * 規格ID
     */
    function getOneEntityByBasicId($merch_basic_id){

        $arrRes = array();

        // SQL処理
/*
        $sql =  "";
        $sql .=  "     SELECT merch_specs.* ";
        $sql .=  "       FROM merch_basics ";
        $sql .=  " INNER JOIN merch_style_specs ";
        $sql .=  "         ON merch_basics.merch_style_id = merch_style_specs.merch_style_id ";
        $sql .=  "        AND merch_style_specs.del_flg <= 0 ";
        $sql .=  " INNER JOIN merch_specs ";
        $sql .=  "         ON merch_style_specs.merch_spec_id = merch_specs.id ";
        $sql .=  "        AND merch_specs.del_flg <= 0 ";
        $sql .=  "      WHERE merch_basics.id = '$merch_basic_id' ";
        $sql .=  "        AND merch_basics.del_flg <= 0 ";
        $sql .=  "   ORDER BY merch_specs.rank ";
        $arr_spec = $this->query($sql);

        if(count($arr_spec) == 0) return $arrRes;
*/
        // SQL処理
        $sql =  "SELECT * FROM merch_items WHERE `merch_basic_id` = '$merch_basic_id' AND del_flg <= 0";
        $arr_items = $this->query($sql);

        if(count($arr_items) == 0) return array();
        $arrRes["list"] = $arr_items;
/*
        foreach($arr_spec as $i => $v){
            $field_name = $v["merch_specs"]["field_name"];
            $value      = $arrRes["merch_items"][$v["merch_specs"]["field_name"]];
            $type       = $v["merch_specs"]["field_type"];

            $arrRes["spec_list"][$i]["field_type"]  = $v["merch_specs"]["field_type"];
            $arrRes["spec_list"][$i]["name"]  = $v["merch_specs"]["name"];
            $arrRes["spec_list"][$i]["field"] = $field_name;
            $arrRes["spec_list"][$i]["value"] = $value;
            $arrRes["spec_list"][$i]["disp_value"] = $value;

            // リスト形式の場合
            $sql =  "SELECT * FROM merch_spec_lists WHERE `field_name` = '$field_name' AND value = '$value' AND del_flg <= 0";
            $arr_lists = $this->query($sql);
            if(count($arr_lists) > 0){
                $arrRes["spec_list"][$i]["disp_value"] = $arr_lists[0]["merch_spec_lists"]["name"];
            }

            // リスト形式のオプション配列化
            $sql =  "SELECT * FROM merch_spec_lists WHERE `field_name` = '$field_name' AND del_flg <= 0";
            $arr_lists = $this->query($sql);
            $options = array();
            if(count($arr_lists) > 0){
                foreach($arr_lists as $k => $val){
                    $options[$val["merch_spec_lists"]["value"]] = $val["merch_spec_lists"]["name"];
                }
            }
            $arrRes["spec_list"][$i]["options"] = $options;

            // チェックボックス配列変換
            if($type == "checkbox"){

                $selected = array();
                $val = $arrRes["merch_items"][$field_name];
                $arr2 = explode("\n",$val);
                $disp_value = null;
                foreach($arr2 as $k => $row){
                    if(isset($options[$row])){
                        if(!empty($disp_value)) $disp_value .= " / ";
                        $disp_value .= $options[$row];
                        $selected[] = $row;
                    }
                }
                $arrRes["spec_list"][$i]["disp_value"] = $disp_value;

                // リスト形式のセレクト配列化
                $arrRes["spec_list"][$i]["selected"] = $selected;
            }

        }
*/
        return $arrRes;
    }

    /*
    /*
     * 商品規格情報
     * 基本ID
     * カラー
     * サイズ
     */
    function getOneEntityByParams($merch_basic_id,$size = null,$color = null){

        $arrRes = array();

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_items ";
        $sql .=  "      WHERE merch_basic_id = '$merch_basic_id' ";
        if(!empty($size)){
            $sql .=  "        AND size = '$size' ";
        }
        if(!empty($color)){
            $sql .=  "        AND color = '$color' ";
        }
        $sql .=  "        AND del_flg <= 0 ";
        $sql .=  "      ORDER BY color ";
        $array = $this->query($sql);

        $arrRes = array();
        if(count($array) > 0){
            foreach($array as $row){
                $item_id = $row["merch_items"]["id"];
                $arrRes[$item_id] = $row;
                if($row["merch_items"]["arrival_date"] == "1900-01-01") $arrRes[$item_id]["merch_items"]["arrival_date"] = null;
                if($row["merch_items"]["arrival_date"] == "0000-00-00") $arrRes[$item_id]["merch_items"]["arrival_date"] = null;
            }
        }
        return $arrRes;
    }

    /*
     * 商品規格情報
     * 商品構成ID
     * 規格ID
     */
    function setSpecFieldsByStyleId($merch_style_id,$data){

        $arrRes["spec_list"] = array();
//print_r($data);
        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT merch_specs.* ";
        $sql .=  "       FROM merch_specs ";
        $sql .=  " INNER JOIN merch_style_specs ";
        $sql .=  "         ON merch_style_specs.merch_spec_id = merch_specs.id ";
        $sql .=  "        AND merch_style_specs.merch_style_id = '$merch_style_id' ";
        $sql .=  "        AND merch_style_specs.del_flg <= 0 ";
        $sql .=  "      WHERE merch_specs.del_flg <= 0 ";
        $sql .=  "   ORDER BY merch_specs.rank ";
        $arr_spec = $this->query($sql);

        if(count($arr_spec) == 0) return $arrRes;

        $arrRes["merch_items"] = $data;
        foreach($arr_spec as $i => $v){
            $field_name = $v["merch_specs"]["field_name"];
            $value      = @$arrRes["merch_items"][$v["merch_specs"]["field_name"]];
            $type       = $v["merch_specs"]["field_type"];

            $arrRes["spec_list"][$i]["field_type"]  = $v["merch_specs"]["field_type"];
            $arrRes["spec_list"][$i]["name"]  = $v["merch_specs"]["name"];
            $arrRes["spec_list"][$i]["field"] = $field_name;
            $arrRes["spec_list"][$i]["value"] = $value;
            $arrRes["spec_list"][$i]["disp_value"] = $value;

            // リスト形式の場合
            $sql =  "SELECT * FROM merch_spec_lists WHERE `field_name` = '$field_name' AND value = '$value' AND del_flg <= 0";
            $arr_lists = $this->query($sql);
            if(count($arr_lists) > 0){
                $arrRes["spec_list"][$i]["disp_value"] = $arr_lists[0]["merch_spec_lists"]["name"];
            }

            // リスト形式のオプション配列化
            $sql =  "SELECT * FROM merch_spec_lists WHERE `field_name` = '$field_name' AND del_flg <= 0";
            $arr_lists = $this->query($sql);
            $options = array();
            if(count($arr_lists) > 0){
                foreach($arr_lists as $k => $val){
                    $options[$val["merch_spec_lists"]["value"]] = $val["merch_spec_lists"]["name"];
                }
            }
            $arrRes["spec_list"][$i]["options"] = $options;

            // チェックボックス配列変換
            if($type == "checkbox"){

                $selected = array();
                $disp_value = null;
                if(isset($arrRes["merch_items"][$field_name])){
                    $arr2 = $arrRes["merch_items"][$field_name];
                    foreach($arr2 as $k => $row){
                        $disp_value = null;
                        $selected[] = $row;
                    }
                }
                $arrRes["spec_list"][$i]["disp_value"] = $disp_value;

                // リスト形式のセレクト配列化
                $arrRes["spec_list"][$i]["selected"] = $selected;
            }

            if(is_array($value)){
                $arr_val = null;
                foreach($value as $row){
                    foreach($arr_lists as $k => $val){
                        if($row == $val["merch_spec_lists"]["value"]){
                            if(!empty($arr_val)) $arr_val .= " / ";
                            $arr_val .= $val["merch_spec_lists"]["name"];
                        }
                    }
                }
                $arrRes["spec_list"][$i]["disp_value"] = $arr_val;
            }

        }

//print_r($arrRes);
        return $arrRes;
    }

    /*
     * 登録更新処理
     */
    function isSave($data){

        // merch_item_specsの空白値を設定
        $sql =  "SELECT * FROM merch_specs WHERE del_flg <= 0";
        $array = $this->query($sql);
        foreach($array as $key => $val){
            $field_name = $val["merch_specs"]["field_name"];
            $field_type = $val["merch_specs"]["field_type"];
            if(isset($data['merch_items'][$field_name])){
                if($data['merch_items'][$field_name] == null){
                    $data['merch_items'][$field_name] = "";
                }
            }else{
                $data['merch_items'][$field_name] = "";
            }
            if($field_type === "money"){
                $money = $data['merch_items'][$field_name];
                $data['merch_items'][$field_name] = str_replace(",","",$money);
            }

        }

        // 配列変換
        if(isset($data["merch_items"])){
            foreach($data["merch_items"] as $name => $val){
                if(is_array($val)){
                    $line = null;
                    foreach($val as $k => $v){
                        if(!empty($line)) $line .= "\n";
                            $line .= $v;
                    }
                    $data["merch_items"][$name] = $line;
                }
            }
        }

        if( $this->save($data['merch_items'])){

            $id = $data['MerchItem']['id'];
            $basic_id = $this->getOneFieldById($id,"merch_basic_id");
            Cache::write("ColorList".$basic_id,null);
            Cache::write("OptionsColor".$basic_id,null);
            Cache::write("OptionsSize".$basic_id,null);
            Cache::write("SizeList".$basic_id,null);
            Cache::write("ItemFormList".$basic_id,null);

            return true;
        }else{
            return false;
        }
    }

    /*
     * 削除更新処理
     */
    function isDelete($data){

        $return = parent::isDelete($data);
        $this->updChash();
        return $return;

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
        $sql .=  "     FROM merch_items ";
        $sql .=  "    WHERE del_flg <= 0 ";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $arrRes["list"][$key]["id"]            = $row["merch_items"]["id"];
            $arrRes["list"][$key]["name"]          = $row["merch_items"]["name"];

        }
/*
        $arrRes = array();
        foreach($array as $key => $row){
            $item_id = $row["items"]["id"];

            $sql =  "";
            $sql .=  "    SELECT ac.name,ac.parent_id ";
            $sql .=  "      FROM items_categories ic ";
            $sql .=  " LEFT JOIN article_categories ac ";
            $sql .=  "        ON ic.category_id = ac.id ";
            $sql .=  "     WHERE ic.item_id = '$item_id'";

            $arrCat = $this->query($sql);

            $sql =  "SELECT * FROM article_categories WHERE id = ".$arrCat[0]["ac"]["parent_id"];
            $arr_2 = $this->query($sql);
            if (count($arr_2) > 0){
                $name_1 = $arr_2[0]["article_categories"]["name"]." > ";
            }

            $arrRes["list"][$key]["id"]            = $item_id;
            $arrRes["list"][$key]["name"]          = $row["items"]["name"];
            $arrRes["list"][$key]["category"]      = $name_1.$arrCat[0]["ac"]["name"];

        }
*/
        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        return $arrRes;
    }

    /*
     * 商品情報
     */
    function getOneItemById($id,$public = null){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT merch_basics.*  ";
        $sql .=  "          , merch_items.*  ";
        $sql .=  "       FROM merch_items  ";
        $sql .=  " LEFT JOIN merch_basics  ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id  ";
        $sql .=  "        AND merch_basics.del_flg <= 0  ";
        $sql .=  "      WHERE merch_items.del_flg <= 0  ";
        $sql .=  "        AND merch_items.id = '$id'  ";
//        if($public == "yes"){
//            $sql .=  "        AND (merch_items.public_flg = 'yes' OR merch_items.public_flg IS NULL) ";
//        }
        $array = $this->query($sql);

        $arrRes = array();
        if(count($array) > 0){
            $arrRes = $array[0];

            // SOLD OUT 判定
/*
            $arrRes["sold_out"] = "no";
            if(strstr($array[0]["merch_items"]["icon_views"],'sold_out')){
                $arrRes["sold_out"] = "yes";
            }
            if($array[0]["merch_items"]["stock_num"] == 0 && $array[0]["merch_items"]["stock_num"] != ""){
                $arrRes["sold_out"] = "yes";
            }
*/

            $sql = "SELECT * FROM merch_specs WHERE del_flg <= 0 AND field_type = 'checkbox' ORDER BY rank ";
            $array = $this->query($sql);
            $arrRes["checkbox"] = array();
            foreach($array as $key => $val){
                $field_name = $val["merch_specs"]["field_name"];
                if(isset($arrRes["merch_items"][$field_name])){
                    $field_val = $arrRes["merch_items"][$field_name];
                    $arr_field_val = explode("\n",$field_val);
                    $arrRes["checkbox"][$field_name] = $arr_field_val;
                }
            }

            // フリーオプション配列変換
            $sql = "SELECT * FROM merch_specs WHERE del_flg <= 0 AND field_type = 'options' ORDER BY rank ";
            $array = $this->query($sql);
            foreach($array as $key => $val){
                $option_value = $arrRes["merch_items"][$val["merch_specs"]["field_name"]];
                $arrOptions = explode("\n",$option_value);
                $flg=false;
                foreach($arrOptions as $k_o => $v_o){
                    if(!empty($v_o)){
                        $flg=true;
                    }
                }
                if($flg){
                    $arrRes["merch_items"][$val["merch_specs"]["field_name"]] = $arrOptions;
                }
            }

            // オプション
            $sql =  "";
            $sql .= "     SELECT * ";
            $sql .= "       FROM merch_options ";
            $sql .= " INNER JOIN merch_item_options ";
            $sql .= "         ON merch_options.id = merch_item_options.merch_option_id ";
            $sql .= "      WHERE merch_item_options.merch_item_id = '$id' ";
            $sql .= "        AND merch_options.del_flg <= 0 ";
            $array = $this->query($sql);
            foreach($array as $key => $val){
                $row = $val["merch_options"];
                $merch_option_id = $row["id"];
                $arrRes["merch_options"][$key]["id"]    = $merch_option_id;
                $arrRes["merch_options"][$key]["name"]  = $row["name"];
                $arrRes["merch_options"][$key]["group"] = $row["group"];
                $arrRes["merch_options"][$key]["type"]  = $row["type"];

                // オプションリスト
                $sql =  "SELECT * FROM merch_option_lists WHERE merch_option_id = '$merch_option_id' AND del_flg <= 0 ";
                $array_list = $this->query($sql);
                $arrRes["merch_options"][$key]["list"] = $array_list;
            }

//            // カテゴリ
//            $basic_id = $arrRes["merch_basics"]["id"];
//            $sql =  "";
//            $sql .= "     SELECT * ";
//            $sql .= "       FROM merch_basic_categories ";
//            $sql .= " INNER JOIN merch_categories ";
//            $sql .= "         ON merch_basic_categories.merch_category_id = merch_categories.id ";
//            $sql .= "        AND merch_categories.del_flg <= 0 ";
//            $sql .= "      WHERE merch_basic_categories.merch_basic_id = '$basic_id' ";
//            $sql .= "        AND merch_basic_categories.del_flg <= 0 ";
//            $array_list = $this->query($sql);
//            foreach($array_list as $key => $val){
//                $arrRes["merch_categories"][$key] = $val["merch_categories"];
//            }
        }

        return $arrRes;

    }

    /*
     * 商品情報 基本情報ID
     */
    function getOneItemByBasicId($basic_id){

        // ユーザ情報
        $sql = "SELECT * FROM merch_items WHERE merch_basic_id = '$basic_id'";
        $array = $this->query($sql);
        return $array;
    }

    /*
     * 商品情報 画像削除
     */
    function isDeleImageItem($basic_id,$field_name){
//echo $basic_id."/".$field_name;
        $flg = false;
        $list = $this->getOneItemByBasicId($basic_id);
        if(count($list) > 0){

            $item_id = $list[0]["merch_items"]["id"];

            // アイテム情報の削除
            $data["id"] = $item_id;
            $data[$field_name] = "";

            $this->save($data, true, array("$field_name",'modified'));

        }

        return $flg;
    }

    /*
     * 未設定spac,optionフィールドの追加
     */
    function addFields($field_lists){

        $array = $this->getFields();
        foreach($field_lists as $field){
            $flg = false;
            foreach($array as $key => $row){
                if($key == $field){
                    $flg = true;
                }
            }
            if(!$flg){
                $sql =  "ALTER TABLE `merch_items` ADD `$field` TEXT NULL ";
                $this->query($sql);
            }
        }

    }

    /*
     * ピックアップ情報制限判定
     */
    function isPickUpLimit($limit,$field_name){

        // ユーザ情報
        $sql = "SELECT count(*) as cnt FROM merch_items WHERE `$field_name` <> '' AND del_flg <= 0 ";
        $array = $this->query($sql);

        $flg = true;
        if($array[0][0]["cnt"] >= $limit){
            $flg = false;
        }
        return $flg;
    }

    /*
     * フロント側表示
     */
    function getEntityByFieldName($field_name){

        $sql = "";
        $sql .= "     SELECT * ";
        $sql .= "       FROM merch_basics ";
        $sql .= " INNER JOIN merch_items ";
        $sql .= "         ON merch_basics.id = merch_items.merch_basic_id ";
        $sql .= "      WHERE merch_basics.del_flg <= 0";
        $sql .= "        AND merch_items.`$field_name` <> ''";
        $sql .= "   ORDER BY merch_basics.created DESC";
        $array = $this->query($sql);

        return $array;
    }

    /*
     * フロント側表示
     */
    function getEntityByBasicId($basic_id){

        $sql = "";
        $sql .= "     SELECT * ";
        $sql .= "       FROM merch_items ";
        $sql .= "      WHERE del_flg <= 0";
        $sql .= "        AND merch_basic_id = '$basic_id' ";
        $sql .= "   ORDER BY color,size";
        $array = $this->query($sql);
        $arrRes = array();
        foreach($array as $i => $row){
            $arrRes[$i] = $row["merch_items"];
        }
        return $arrRes;
    }

    /*
     * フロント側表示
     * カラーリスト
     */
    function getColorListByBasicId($basic_id){

        $cache_data = Cache::read("ColorList".$basic_id);

        if(empty($cache_data)){
            $sql = "";
            $sql .= "     SELECT color,list_image ";
            $sql .= "       FROM merch_items ";
            $sql .= "      WHERE del_flg <= 0";
            $sql .= "        AND merch_basic_id = '$basic_id' ";
            $sql .= "   GROUP BY color";
//            $sql .= "   ORDER BY sales_price DESC";
            $sql .= "   ORDER BY color";
            $array = $this->query($sql);
            $arrRes = array();
            foreach($array as $i => $row){
                $arrRes[$i] = $row["merch_items"];
            }

            $cache_data = $arrRes;
            Cache::write("ColorList".$basic_id,$cache_data);
        }

        return $cache_data;
    }

    /*
     * 配列カラーリスト
     */
    function getOptionsColorByBasicId($basic_id){

        $cache_data = Cache::read("OptionsColor".$basic_id);

        if(empty($cache_data)){
            $sql = "";
            $sql .= "     SELECT color,list_image ";
            $sql .= "       FROM merch_items ";
            $sql .= "      WHERE del_flg <= 0";
            $sql .= "        AND merch_basic_id = '$basic_id' ";
            $sql .= "   GROUP BY color";
            $sql .= "   ORDER BY color";
            $array = $this->query($sql);
            $arrRes = array();
            foreach($array as $row){
                $color = $row["merch_items"]["color"];
                $arrRes[$color] = $color;
            }

            $cache_data = $arrRes;
            Cache::write("OptionsColor".$basic_id,$cache_data);
        }

        return $cache_data;
    }

    /*
     * 配列サイズリスト
     */
    function getOptionsSizeByBasicId($basic_id){

        Cache::set(Array('duration' => '+1 hours'));
        $cache_data = Cache::read("OptionsSize".$basic_id);

        if(empty($cache_data)){

            $sql = "";
            $sql .= "     SELECT * ";
            $sql .= "       FROM merch_items ";
            $sql .= "      WHERE del_flg <= 0";
            $sql .= "        AND merch_basic_id = '$basic_id' ";
            $sql .= "   GROUP BY size";
            $sql .= "   ORDER BY size";
            $array = $this->query($sql);
            $arrRes = array();
            foreach($array as $row){
                $size = $row["merch_items"]["size"];
                $arrRes[$size] = $size;
            }

            $cache_data = $arrRes;
            Cache::write("OptionsSize".$basic_id,$cache_data);
        }

        return $cache_data;
    }

    /*
     * フロント側表示
     * サイズリスト
     */
    function getSizeListByBasicId($basic_id){

        $cache_data = Cache::read("SizeList".$basic_id);

        if(empty($cache_data)){

            $sql = "";
            $sql .= "     SELECT MIN(merch_items.size) AS size ";
            $sql .= "          , MIN(merch_items.width) AS width ";
            $sql .= "          , MIN(merch_items.length) AS length ";
            $sql .= "          , MIN(merch_items.sleeve_length1) AS sleeve_length1 ";
            $sql .= "          , MIN(merch_items.shoulder_width) AS shoulder_width ";
            $sql .= "          , MIN(merch_items.sleeve_length2) AS sleeve_length2 ";
            $sql .= "          , MIN(merch_items.all_length) AS all_length ";
            $sql .= "          , MIN(merch_items.waist) AS waist ";
            $sql .= "          , MIN(merch_items.leg_length) AS leg_length ";
            $sql .= "          , MIN(merch_items.wrist_length) AS wrist_length ";
            $sql .= "       FROM merch_items ";
            $sql .= " INNER JOIN sizes ";
            $sql .= "         ON sizes.size = merch_items.size ";
            $sql .= "      WHERE merch_items.del_flg <= 0";
            $sql .= "        AND merch_items.merch_basic_id = '$basic_id' ";
            $sql .= "   GROUP BY merch_items.size";
            $sql .= "   ORDER BY sizes.rank";
            $array = $this->query($sql);
            $arrRes = array();
            foreach($array as $i => $row){
                if($this->isTest) $arrRes[$i] = $row[0];
                if(!$this->isTest) $arrRes[$i] = $row["merch_items"];
            }

            $cache_data = $arrRes;
            Cache::write("SizeList".$basic_id,$cache_data);
        }

        $this->size_param = $cache_data;

        return $cache_data;
    }

    var $size_param = null;

    /*
     * フロント側表示
     * サイズリスト
     */
    function getDimenList(){

        $data["width"]          = "幅";
        $data["length"]         = "丈";
        $data["sleeve_length1"] = "袖丈";
        $data["shoulder_width"] = "肩幅";
        $data["sleeve_length2"] = "裄丈";
        $data["all_length"]     = "総丈";
        $data["waist"]          = "ウェスト";
        $data["leg_length"]     = "股下";
        $data["wrist_length"]   = "袖口丈";

        $arrRes = array();
        if(count($this->size_param) > 0){
            foreach($this->size_param as $row){
                foreach($data as $name => $val){
                    if($row[$name] > 0){
                        $arrRes[$name]["name"] = $val;
                    }
                }
            }

            foreach($arrRes as $name => $val){
                foreach($this->size_param as $row){
                    $arrRes[$name][$row["size"]] = $row[$name];
                }
            }
        }
        return $arrRes;
    }

    /*
     * フロント側表示
     * サイズリスト
     */
    function getItemFormList($basic_id){

        $cache_data = Cache::read("ItemFormList".$basic_id);
        if(empty($cache_data)){

            $sql = "";
            $sql .= "     SELECT merch_items.color, merch_items.list_image";
            $sql .= "       FROM merch_items ";
            $sql .= "      WHERE merch_items.del_flg <= 0";
            $sql .= "        AND merch_items.merch_basic_id = '$basic_id' ";
            $sql .= "   GROUP BY merch_items.color";
            $sql .= "   ORDER BY merch_items.color";
            $arr_color = $this->query($sql);

            $sql = "";
            $sql .= "     SELECT merch_items.size ";
            $sql .= "       FROM merch_items ";
            $sql .= " INNER JOIN sizes ";
            $sql .= "         ON sizes.size = merch_items.size ";
            $sql .= "      WHERE merch_items.del_flg <= 0";
            $sql .= "        AND merch_items.merch_basic_id = '$basic_id' ";
            $sql .= "   GROUP BY merch_items.size";
            $sql .= "   ORDER BY sizes.rank";
            $arr_size = $this->query($sql);

            $arrRes = null;
            foreach($arr_color as $color_row){
                $color = $color_row["merch_items"]["color"];
                $list_image = $color_row["merch_items"]["list_image"];
                $i = 0;
                foreach($arr_size as $size_row){
                    $size = $size_row["merch_items"]["size"];

                    $sql = "";
                    $sql .= "     SELECT * ";
                    $sql .= "       FROM merch_items ";
                    $sql .= "      WHERE merch_items.del_flg = 0";
                    $sql .= "        AND merch_items.merch_basic_id = '$basic_id' ";
                    $sql .= "        AND merch_items.color = '$color' ";
                    $sql .= "        AND merch_items.size = '$size' ";
                    $array = $this->query($sql);

                    if(count($array) > 0){
                        $arrRes[$color][$i] = $array[0]["merch_items"];
                    }else{
                        $arrRes[$color][$i]["id"] = "-1";
                        $arrRes[$color][$i]["list_image"] = $list_image;
                        $arrRes[$color][$i]["arrival_date"] = "0000-00-00";
                        $arrRes[$color][$i]["stock_num"] = "0";
                    }
                    ++$i;
                }

            }

            $cache_data = $arrRes;
            Cache::write("ItemFormList".$basic_id,$cache_data);
        }

        $today = date("Y-m-d");
        foreach($cache_data as $color => $row){
            foreach($row as $i => $val){
                $arrival_date = @$val["arrival_date"];
                if($arrival_date <= $today && $arrival_date != "0000-00-00"){
                    $cache_data[$color][$i]["arrival_date"] = "0000-00-00";
                    if(empty($val["stock_num"])){
                        $data["MerchItem"] = $val;
                        $data["MerchItem"]["stock_num"] = 999999;
                        $this->isSave($data);
                        $cache_data[$color][$i]["stock_num"] = "999999";
                    }
                }
            }
        }

        return $cache_data;
    }

    /*
     * 新規コード取得
     */
    function getNewCode(){

        $code = date("ymdHi");
        $sql = "SELECT count(*) as cnt FROM merch_items WHERE del_flg <= 0 AND code = '$code'";
        $array = $this->query($sql);
        $cnt = $array[0][0]["cnt"];

        while($cnt > 0){
            $code = intval($code) + 1;
            $sql = "SELECT count(*) as cnt FROM merch_items WHERE del_flg <= 0 AND code = '$code'";
            $array = $this->query($sql);
            $cnt = $array[0][0]["cnt"];
        }

        return $code;
    }

    /*
     * 在庫処理
     */
    function stockOut($item_id,$quantity){
        $sql = "SELECT * FROM merch_items WHERE del_flg <= 0 AND id = '$item_id'";
        $array = $this->query($sql);
        if(count($array) > 0){

            $stock_num = $array[0]["merch_items"]["stock_num"];
            $basic_id  = $array[0]["merch_items"]["merch_basic_id"];

            if(!empty($stock_num)){
                $last_stock_num = $stock_num - $quantity;
                if($last_stock_num < 1) $last_stock_num = 0;

                $data["id"] = $item_id;
                $data["stock_num"] = $last_stock_num;
                $this->save($data, true, array('stock_num','modified'));

            }

            Cache::write("ItemFormList".$basic_id,null);

        }

    }

/*
* キャッシュID別削除処理
*/
    function chachClearByID($basic_id) {

        Cache::write("ColorList".$basic_id,null);
        Cache::write("OptionsColor".$basic_id,null);
        Cache::write("OptionsSize".$basic_id,null);
        Cache::write("SizeList".$basic_id,null);
        Cache::write("ItemFormList".$basic_id,null);

    }


    /*
     * 商品情報キャッシュ更新処理
     */
/*
    function updChash(){
        $sql = "SELECT * FROM merch_basics WHERE del_flg <= 0";
        $array = $this->query($sql);
        if(count($array) > 0){
            foreach($array as $row){
                $basic_id  = $row["merch_basics"]["id"];
echo $basic_id."<br>";
                Cache::write("ColorList".$basic_id,null);
                Cache::write("OptionsColor".$basic_id,null);
                Cache::write("OptionsSize".$basic_id,null);
                Cache::write("SizeList".$basic_id,null);
                Cache::write("ItemFormList".$basic_id,null);

                // フロント側表示 カラーリスト
                $this->getColorListByBasicId($basic_id);

                // 配列カラーリスト
                $this->getOptionsColorByBasicId($basic_id);

                // 配列サイズリスト
                $this->getOptionsSizeByBasicId($basic_id);

                // フロント側表示 サイズリスト
                $this->getSizeListByBasicId($basic_id);

                // フロント側表示 サイズリスト
                $this->getItemFormList($basic_id);
            }
        }

    }
*/
}
?>