<?php
class MerchBasic extends AppModel {
    var $validate = array();
//    var $actsAs = array("Paging");
    var $isTest = IS_TEST;

    /*
     * 入力値検証
     */

    function isValid(){

        $validate["name"]["notEmpty"]         = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["category_id"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["point"]["numeric"]         = array("rule" => "numeric", "allowEmpty" => true , "message" => "このフィールドは半角数字です。");
        $validate["point_start_date"]["date"] = array("rule" => "date", "allowEmpty" => true , "message" => "このフィールドは日付（0000/00/00形式または0000-00-00形式）です。");
        $validate["point_end_date"]["date"]   = array("rule" => "date", "allowEmpty" => true , "message" => "このフィールドは日付（0000/00/00形式または0000-00-00形式）です。");

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
    function getOptions($where = null){
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

        return parent::getOneFieldById($id,$field);
    }

    /*
     * リスト
     */
    function getOneEntityById($id){

        // SQL処理
        $sql =  "SELECT * FROM merch_basics WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        if(count($array) > 0){
            if($array[0]["merch_basics"]["point_start_date"] == "1900-01-01") $array[0]["merch_basics"]["point_start_date"] = null;
            if($array[0]["merch_basics"]["point_start_date"] == "0000-00-00") $array[0]["merch_basics"]["point_start_date"] = null;
            if($array[0]["merch_basics"]["point_end_date"] == "1900-01-01") $array[0]["merch_basics"]["point_end_date"] = null;
            if($array[0]["merch_basics"]["point_end_date"] == "0000-00-00") $array[0]["merch_basics"]["point_end_date"] = null;
            return $array[0];
        }else{
            return null;
        }
    }

    /*
     * リスト
     */
    function getOneEntityByItemId($item_id){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_basics ";
        $sql .=  " INNER JOIN merch_styles ";
        $sql .=  "         ON merch_basics.merch_style_id = merch_styles.id ";
        $sql .=  " INNER JOIN merch_items ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id ";
        $sql .=  "        AND merch_items.del_flg <= 0 ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0 ";
        $sql .=  "        AND merch_items.id = '$item_id' ";
        $array = $this->query($sql);

        $arrRes = array();
        if(isset($array[0]["merch_basics"])){
            $arrRes["merch_basics"] = $array[0]["merch_basics"];

            // ピックアップの配列化
            $pickup = $array[0]["merch_basics"]["pickup"];
            $pickup = explode("],[",$pickup);
            $pickup = str_replace("[","",$pickup);
            $pickup = str_replace("]","",$pickup);
            $arrRes["merch_basics"]["pickup"] = $pickup;
        }
        if(isset($array[0]["merch_items"])){
            $arrRes["merch_items"] = $array[0]["merch_items"];
        }
//print_r($arrRes["merch_items"]);

        $sql2 = "SELECT * FROM merch_specs WHERE del_flg <= 0 ";
        $array2 = $this->query($sql2);
        foreach($array2 as $row){
            $val = $row["merch_specs"];
            $field_name = $val["field_name"];
//            if(isset($arrRes["merch_items"][$field_name])){
//echo $field_name."<br>";
                @$arrRes["merch_specs"][$field_name] = $val;
//            }
        }

        if(isset($array[0]["merch_items"]["attention"])){

            $arrAttention = $array[0]["merch_items"]["attention"];
            $arrAttention = explode("\n",$arrAttention);
            $arrRes["merch_items"]["arr_attention"] = $arrAttention;
        }

        $area_square = @$array[0]["merch_items"]["area_square"];
        $area_square = explode("\n",$area_square);
        $arrRes["merch_items"]["arr_area_square"] = $area_square;

        return $arrRes;
    }

    /*
     * 登録更新処理
     */
    function isSave($data,$valid = true){
        if(isset($data['merch_basics']['new_item']['check'])){
            $check = $data['merch_basics']['new_item']['check'];
            $value = $data['merch_basics']['new_item']['value'];
            $data['merch_basics']['new_item'] = $check;
            if(!empty($value)){
                $data['merch_basics']['new_item'] = $value;
            }
        }
        if(isset($data['merch_basics']['sale_item']['check'])){
            $check = $data['merch_basics']['sale_item']['check'];
            $value = $data['merch_basics']['sale_item']['value'];
            $data['merch_basics']['sale_item'] = $check;
            if(!empty($value)){
                $data['merch_basics']['sale_item'] = $value;
            }
        }
        if(isset($data['merch_basics']['pickup'])){
            $pickup = $data['merch_basics']['pickup'];
            if(is_array($pickup)){
                $resPickup = null;
                foreach($pickup as $val){
                    if(!empty($resPickup)) $resPickup .= ",";
                    $resPickup .= "[".$val."]";
                }
                $data['merch_basics']['pickup'] = $resPickup;
            }
        }
        if(empty($data['merch_basics']['point'])){
            $data['merch_basics']['point'] = 0;
        }
        return parent::isSave($data['merch_basics']);
    }

    var $category_id = null;

    /*
     * ページング
     */
    function setWhereCategoryId($id){
        $this->category_id = $id;
    }

    var $brand = null;

    /*
     * ページング
     */
    function setWhereBrand($brand){
        $this->brand = $brand;
    }

    var $basic_name = null;

    /*
     * ページング
     */
    function setWhereName($param){
        $this->basic_name = $param;
    }

    var $sort_value = null;

    /*
     * ページング
     */
    function setWhereSortVal($param){
        $this->sort_value = $param;
    }

    var $refine_value = null;

    /*
     * ページング
     */
    function setWhereRefineVal($param){
        $this->refine_value = $param;
    }

    var $is_sale_item = false;

    /*
     * ページング
     */
    function isSaleItems($param = false){
        $this->is_sale_item = $param;
    }

    var $str_where = array();

    /*
     * ページング
     * WHERE条件
     */
    function setArrWhere($arr_param){
        $str_where = null;
        if(isset($arr_param["keywords"])){
            $line = null;
            $arrKeyWord = trim($arr_param["keywords"]);
            $arrKeyWord = $this->paramSanitize($arrKeyWord);
            $arrKeyWord = str_replace("　"," ",$arrKeyWord);
            $arrKeyWord = explode(" ",$arrKeyWord);
            foreach($arrKeyWord as $val){
                if(!empty($line)) $line .= " OR ";
                $line .= " merch_basics.name LIKE '%".$val."%'\n ";
                $line .= " OR merch_basics.category_name LIKE '%".$val."%'\n ";
                $line .= " OR merch_basics.brand LIKE '%".$val."%'\n ";
//                $line .= " OR merch_items.list_description LIKE '%".$val."%'\n ";
//                $line .= " OR merch_items.detail_description LIKE '%".$val."%'\n ";
            }
            $str_where .= " AND ( ".$line." )\n ";
        }

        $this->str_where = $str_where;
    }

    /*
     * ページング
     * 施設一覧
     * WHERE条件
     */
    function getWhere(){
        $sql =  null;
        if(!empty($this->basic_name)){
            $sql .=  "        AND merch_basics.name LIKE '%".$this->basic_name."%' ";
        }
        if(!empty($this->category_id)){
            $category_id = $this->category_id;
            $sql .=  "        AND merch_basics.merch_category_id = '$category_id' ";
        }
        if(!empty($this->brand)){
            $brand = $this->brand;
            $sql .=  "        AND merch_basics.brand = '$brand' ";
        }
        if($this->is_sale_item){
            $sql .=  "        AND merch_basics.sale_item <> '' AND merch_basics.sale_item IS NOT NULL ";
        }
        if(!empty($this->str_where)){
            $sql .=  $this->str_where;
        }
        return $sql;
    }

    /*
     * ページング
     */
    function getPagingEntity($disp_num,$pgnum){

        App::import('Model','MerchCategories');
        $MerchCategories = new MerchCategories();

        if(empty($pgnum)) $pgnum = 1;

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_basics ";
        $sql .=  " INNER JOIN merch_styles ";
        $sql .=  "         ON merch_basics.merch_style_id = merch_styles.id ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0 ";
        $sql .=  $this->getWhere();
        if(!empty($this->sort_value)){
            if($this->sort_value == "rank"){
                $sql .=  "   ORDER BY merch_basics.rank,merch_basics.modified DESC ";
            }else if($this->sort_value == "new"){
                $sql .=  "   ORDER BY merch_basics.created DESC ,merch_basics.modified DESC ";
            }else if($this->sort_value == "id_desc"){
                $sql .=  "   ORDER BY merch_basics.id DESC ";
            }else if($this->sort_value == "id_asc"){
                $sql .=  "   ORDER BY merch_basics.id ASC ";
            }else if($this->sort_value == "name_desc"){
                $sql .=  "   ORDER BY merch_basics.name DESC ";
            }else if($this->sort_value == "name_asc"){
                $sql .=  "   ORDER BY merch_basics.name ASC ";
            }else if($this->sort_value == "price_desc"){
                $sql .=  "   ORDER BY merch_items.sales_price DESC ";
            }else if($this->sort_value == "price_asc"){
                $sql .=  "   ORDER BY merch_items.sales_price ASC ";
            }else if($this->sort_value == "sortdate"){
                $sql .=  "   ORDER BY merch_basics.created DESC ";
            }else if($this->sort_value == "sortprice"){
                $sql .=  "   ORDER BY merch_items.sort_fee ASC ";
            }
        }else{
            $sql .=  "   ORDER BY merch_basics.rank,merch_basics.modified DESC ";
        }

        $array = $this->query($sql);
        $total = count($array);

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $curr_count = count($array);

//        // ランク再処理
//        if(empty($this->sort_value) or $this->sort_value == "rank"){
//        $array = $this->reSort($array,$pgnum,$disp_num);
//        }

        $DataLeaves = ClassRegistry::init('DataLeaves');
        $arrMasterData = $DataLeaves->getArrMasterData();

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $arrRes["list"][$key]["merch_basics"]  = $row["merch_basics"];
            $arrRes["list"][$key]["style_name"]    = $row["merch_styles"]["name"];
            $s_date = $row["merch_basics"]["point_start_date"];
            $e_date = $row["merch_basics"]["point_end_date"];
            $camp = false;
            $today = date("Y-m-d");
            $blank = "0000-00-00";
            if($s_date == $blank && $e_date > $today){
                $camp = true;
            }
            if($s_date != $blank && $s_date < $today && $e_date == $blank){
                $camp = true;
            }
            if($s_date != $blank && $e_date != $blank && $e_date > $today){
                $camp = true;
            }
            $arrRes["list"][$key]["merch_basics"]["camp"] = $camp;
        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        //総数
        $arrRes["total"] = $total;
        // 開始数
        $first = $disp_num * $pgnum + 1 - $disp_num;
        if($pgnum == 1) $first = 1;
        $arrRes["first"] = $first;
        // 終了数
        $last = $first + $curr_count - 1;
        $arrRes["last"]  = $last;
        // 現在件数
        $arrRes["curr"]  = $curr_count;

        return $arrRes;
    }

    /*
     * 管理画面 ページング総数
     */
    function getArrPgList($table_name,$where = null){

        $sql =  "";
        $sql .=  "     SELECT count(*) as count ";
        $sql .=  "       FROM merch_basics ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0 ";
        $sql .=  $this->getWhere();
        if(!empty($this->str_where)){
            $sql .=  $this->str_where;
        }

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
     * 有効全件リスト取得
     */
    function getAllEntity(){

        $sql =  "";
        $sql .=  "     SELECT DISTINCT merch_basics.id ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN merch_categories  ";
        $sql .=  "         ON merch_basics.merch_category_id = merch_categories.id ";
        $sql .=  "        AND merch_categories.del_flg <= 0  ";
        $sql .=  " INNER JOIN merch_items  ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  "        AND (merch_basics.public_flg = '1' OR merch_basics.public_flg IS NULL) ";
        $array = $this->query($sql);
        return $array;
    }

    /*
     * ページング
     */
    function getSortEntity(){

        App::import('Model','MerchCategories');
        $MerchCategories = new MerchCategories();

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_basics ";
        $sql .=  " INNER JOIN merch_styles ";
        $sql .=  "         ON merch_basics.merch_style_id = merch_styles.id ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0 ";
        $sql .=  $this->getWhere();
        $sql .=  "   ORDER BY merch_basics.rank,merch_basics.modified DESC ";

        $array = $this->query($sql);

        $arrRes["list"] =$array;
        foreach($array as $key => $row){
            $arrRes["list"][$key]["merch_basics"]  = $row["merch_basics"];
            $arrRes["list"][$key]["merch_basics"]["rank"]  = $key + 1;
        }

        return $arrRes;
    }

    function itemsCopyById($id){

        parent::addCopyById($id ,"id");
        $merch_basic_id = $this->getLastInsertID();

        App::import('Model','MerchItems');
        $MerchItems = new MerchItems();
        $MerchItems->addCopyById($id ,"merch_basic_id","merch_basic_id",$merch_basic_id);
        $new_merch_item_id = $MerchItems->getLastInsertID();

        // コード再設定
        $data["id"] = $new_merch_item_id;
        $data["code"] = $MerchItems->getNewCode();
//        $data["top_pickup"] = "";
//        $data["recommend"] = "";
        $MerchItems->save($data, true, array("code","top_pickup","recommend"));

        return $new_merch_item_id;
    }

/*
 * カテゴリIDで抽出
 */
/*
    /*
     * ピックアップリスト
     */
    function getPickupEntityByPickupId($merch_pickup_id){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_basics ";
        $sql .=  " INNER JOIN merch_styles ";
        $sql .=  "         ON merch_basics.merch_style_id = merch_styles.id ";
        $sql .=  " INNER JOIN merch_items ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id ";
        $sql .=  "        AND merch_items.del_flg <= 0 ";
        $sql .=  " INNER JOIN merch_pickup_items ";
        $sql .=  "         ON merch_basics.id = merch_pickup_items.merch_basic_id ";
        $sql .=  "        AND merch_pickup_items.del_flg <= 0 ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0 ";
        $sql .=  "        AND merch_pickup_items.merch_pickup_id = '$merch_pickup_id' ";
        $sql .=  "   ORDER BY merch_pickup_items.rank ";
        $array = $this->query($sql);
        return $array;
    }

/*
 *
 * カテゴリ毎リスト表示
 */
    function getOptionsByCategoryId($category_id){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT *  ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  "        AND merch_category_id = '$category_id' ";
        $sql .= "    ORDER BY rank DESC";
        $array = $this->query($sql);

        $arrRes = array();
        foreach($array as $row){
            $id = $row["merch_basics"]["id"];
            $name = $row["merch_basics"]["brand"];
            $name .= " ".$row["merch_basics"]["name"];
            $arrRes[$id] = $name;
        }

        return $arrRes;

    }

/******* 以下フロント *************/

    /*
     * リスト
     */
    function getOnePublicEntityByItemId($item_id){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_basics ";
        $sql .=  " INNER JOIN merch_styles ";
        $sql .=  "         ON merch_basics.merch_style_id = merch_styles.id ";
        $sql .=  " INNER JOIN merch_items ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id ";
        $sql .=  "        AND merch_items.del_flg <= 0 ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0 ";
        $sql .=  "        AND (merch_basics.public_flg = '0' OR merch_basics.public_flg IS NULL) ";
        $sql .=  "        AND merch_items.id = '$item_id' ";
        $array = $this->query($sql);
        $arrRes = array();
        if(isset($array[0]["merch_basics"])){
            $arrRes["merch_basics"] = $array[0]["merch_basics"];

            // ピックアップの配列化
            $pickup = $array[0]["merch_basics"]["pickup"];
            $pickup = explode("],[",$pickup);
            $pickup = str_replace("[","",$pickup);
            $pickup = str_replace("]","",$pickup);
            $arrRes["merch_basics"]["pickup"] = $pickup;
        }
//        if(isset($array[0]["merch_basic_categories"])){
//            $arrRes["merch_basic_categories"] = $array[0]["merch_basic_categories"];
//        }
        if(isset($array[0]["merch_items"])){
            $arrRes["merch_items"] = $array[0]["merch_items"];
        }

        $sql2 = "SELECT * FROM merch_specs WHERE del_flg <= 0 ";
        $array2 = $this->query($sql2);
        foreach($array2 as $row){
            $val = $row["merch_specs"];
            $field_name = $val["field_name"];
            if(isset($arrRes["merch_items"][$field_name])){
                $arrRes["merch_specs"][$field_name] = $val;
            }
        }

        return $arrRes;
    }

/*
 *
 * リスト
 * フロント側表示
 */
    function getAllPublicEntityByCategoryId($category_id){

        // 抽出条件
        $where = null;
        if(count($this->arr_param) > 0){
            foreach($this->arr_param as $key => $val){
                if(!empty($where)) $where .= " AND ";
                $where .=  " ".$key." = '".$val."' ";
            }
        }
        if(!empty($where)) $where = " AND ".$where;

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT merch_basics.*  ";
        $sql .=  "          , merch_items.*  ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN merch_items  ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id  ";
        $sql .=  "        AND merch_items.del_flg <= 0  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  "        AND (merch_basics.public_flg = '0' OR merch_basics.public_flg IS NULL) ";
        $sql .=  "        AND merch_basics.merch_category_id LIKE '%".$category_id."%' ";
        $sql .= $where;
        $sql .= "    ORDER BY merch_basics.created DESC";
        $array = $this->query($sql);
        $total = count($array);

        $arrRes = array();

        foreach($array as $key => $val){

            $arrRes["list"][$key] = $val;
            $merch_item_id = $val["merch_items"]["id"];

        }

        return $arrRes;

    }

/*
 * フロント側表示
 * 基本情報取得
 */

    function getOneBasicById($id){
        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN merch_categories  ";
        $sql .=  "         ON merch_basics.merch_category_id = merch_categories.id ";
        $sql .=  "        AND merch_categories.del_flg <= 0  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  "        AND merch_basics.id = '$id' ";
        $sql .=  "        AND (merch_basics.public_flg = '1' OR merch_basics.public_flg IS NULL) ";
        $array = $this->query($sql);
        $arrRes = null;
        if(count($array) > 0){
            $arrRes = $array[0];
        }
        return $arrRes;

    }

/*
 * フロント側表示
 * ピックアップ情報取得
 */

    function getPickupEntityByCode($code){
        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT merch_basics.*  ";
        $sql .=  "          , merch_items.*  ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN merch_items  ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id  ";
        $sql .=  "        AND merch_items.del_flg <= 0  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  "        AND merch_basics.pickup LIKE '%$code%' ";
        $sql .=  "        AND (merch_basics.public_flg = '1' OR merch_basics.public_flg IS NULL) ";
        $sql .= "    ORDER BY merch_basics.created DESC";
        $array = $this->query($sql);

        return $array;

    }



/*
 * スペシャルプライス情報
 */
    function getSpecialEntity(){

        $cache_data = Cache::read("SpecialEntity");

        if(empty($cache_data)){

        $sql =  "";
        $sql .=  "     SELECT *  ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN merch_items  ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id  ";
        $sql .=  "        AND merch_items.del_flg <= 0  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  "        AND merch_basics.sale_item <> '' AND merch_basics.sale_item IS NOT NULL ";
        $sql .=  "   GROUP BY merch_basics.id ";
        $sql .=  "   ORDER BY merch_basics.modified DESC";
        $sql .=  "      LIMIT 6";
        $array = $this->query($sql);

            $cache_data = $array;
            Cache::write("SpecialEntity",$cache_data);
        }

        return $cache_data;

    }

/*
 * 新着情報
 */
    function getNewItemEntity(){

        $cache_data = Cache::read("NewItemEntity");

        if(empty($cache_data)){

        $sql =  "";
        $sql .=  "     SELECT *  ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN merch_items  ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id  ";
        $sql .=  "        AND merch_items.del_flg <= 0  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  "        AND merch_basics.new_item <> '' AND merch_basics.new_item IS NOT NULL ";
        $sql .=  "   GROUP BY merch_basics.id ";
        $sql .=  "   ORDER BY merch_basics.modified DESC";
        $sql .=  "      LIMIT 6";
        $array = $this->query($sql);

            $cache_data = $array;
            Cache::write("NewItemEntity",$cache_data);
        }

        return $cache_data;

    }

/*
 * ランキング情報
 */
    function getRankingItemEntity(){

        $cache_data = Cache::read("TopRanking");

        if(empty($cache_data)){

        $sql =  "";
        $sql .=  "     SELECT *  ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN merch_items  ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id  ";
        $sql .=  "        AND merch_items.del_flg <= 0  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  "        AND merch_basics.ranking_item <> '' AND merch_basics.ranking_item IS NOT NULL ";
        $sql .=  "   GROUP BY merch_basics.id ";
        $sql .=  "   ORDER BY merch_basics.ranking_item ";
        $sql .=  "      LIMIT 6";
        $array = $this->query($sql);

            $cache_data = $array;
            Cache::write("TopRanking",$cache_data);
        }

        return $cache_data;

    }

/*
 * TOPカテゴリ別情報
 */
    function getTopItemEntityByCategory(){

        $cache_data = Cache::read("TopItemEntityByCategory");

        if(empty($cache_data)){

            $sql =  "";
            $sql .=  "     SELECT *  ";
            $sql .=  "       FROM merch_basics ";
            $sql .=  " INNER JOIN merch_items ";
            $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id ";
            $sql .=  "        AND merch_items.del_flg = 0 ";
            $sql .=  "      WHERE merch_basics.del_flg = 0 ";
            $sql .=  "        AND merch_basics.public_flg = 1 ";
            $sql .=  "   GROUP BY merch_basics.id, merch_basics.category_name ";
            $sql .=  "   ORDER BY merch_basics.category_name ,merch_basics.modified DESC ";
            $array = $this->query($sql);

            $arrRes = null;
            if(!empty($array)){
                $category_name = null;
                $count = 0;
                foreach($array as $row){
                    if(empty($category_name)){
                        $category_name = $row['merch_basics']['category_name'];
                    }
                    if($count > 6 && $category_name != $row['merch_basics']['category_name']){
                        $category_name = $row['merch_basics']['category_name'];
                        $count = 0;
                    }
                    if($count < 6){
                        $arrRes[$category_name][] = $row;
                    }
                    ++$count;
                }
            }

            $cache_data = $arrRes;
            Cache::write("TopItemEntityByCategory",$cache_data);
        }

        return $cache_data;

    }

/*
 *
 * カテゴリ・商品名からIDを抽出
 */
    function getIdByNameAndCategoryName($category_name,$basic_name){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  "        AND merch_basics.category_name = '$category_name' ";
        $sql .=  "        AND merch_basics.name = '$basic_name' ";
        $array = $this->query($sql,false);

        if(count($array) > 0){
            return $array[0]["merch_basics"]["id"];
        }else{
            return null;
        }

    }

/*
 *
 * リスト
 * フロント側表示
 */
    function getBasicsEntity($disp_num,$pgnum){

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN (SELECT * FROM merch_items WHERE del_flg <= 0 GROUP BY merch_basic_id ORDER BY sales_price ASC ) merch_items ";
        $sql .=  "         ON merch_items.merch_basic_id = merch_basics.id  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  $this->getWhere();
        $sql .=  "        AND (merch_basics.public_flg = '1' OR merch_basics.public_flg IS NULL) ";
        $sql .= "    ORDER BY merch_basics.rank ,merch_basics.modified";
        $array = $this->query($sql);
        $total = count($array);

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);
        $curr_count = count($array);
        $arrRes = array();

        foreach($array as $key => $val){
            $arrRes["list"][$key]["merch_basics"] = $val["merch_basics"];
            if($this->isTest) $arrRes["list"][$key]["merch_items"] = $val["merch_items"];
            if(!$this->isTest) $arrRes["list"][$key]["merch_items"] = $val["merch_items"];

        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getSiteArrPgList();
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        //総数
        $arrRes["total"] = $total;
        // 開始数
        $first = $disp_num * $pgnum;
        if($pgnum == 1) $first = 1;
        $arrRes["first"] = $first;
        // 終了数
        $last = $first + $curr_count - 1;
        $arrRes["last"]  = $last;

        return $arrRes;

    }

    /*
     * 商品リスト ページング総数
     */
    function getSiteArrPgList($where = null){
        if(!empty($where)) $where .= " AND ";

        $sql =  "";
        $sql .=  "     SELECT count(*) as count  ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        $sql .=  $this->getWhere();
        $sql .=  "        AND (merch_basics.public_flg = '1' OR merch_basics.public_flg IS NULL) ";

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
 *
 * リスト
 * フロント側表示
 * 商品検索用
 */
    function getItemsSearch($disp_num,$pgnum,$keyword = null){

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT merch_basics.*  ";
        $sql .=  "          , merch_items.*  ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN merch_items  ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id  ";
        $sql .=  "        AND merch_items.del_flg <= 0  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        if(!empty($keyword)){
            $sql .=  "        AND ( merch_basics.name LIKE '%".$keyword."%' OR merch_items.description_1 LIKE '%".$keyword."%'OR merch_items.code LIKE '%".$keyword."%' ) ";
        }
//        $sql .=  "        AND (merch_items.public_flg = 'yes' OR merch_items.public_flg IS NULL) ";
        $sql .=  "   ORDER BY merch_basics.created DESC";

        $array = $this->query($sql);
        $total = count($array);

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);
        $curr_count = count($array);

        $arrRes = array();

        foreach($array as $key => $val){

            $arrRes[$key] = $val;
            $merch_item_id = $val["merch_items"]["id"];

            // アイコン配列変換
            $icon_views = $val["merch_items"]["icon_views"];
            $arrDes = explode("\n",$icon_views);
            foreach($arrDes as $k => $row){
                $value = explode("=>",$row);
                $arrRes[$key]["merch_items"]["arr_icons"][$k] = $value[0];
            }

        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getSearchArrPgList($keyword);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        //総数
        $arrRes["total"] = $total;
        // 開始数
        $first = $disp_num * $pgnum;
        if($pgnum == 1) $first = 1;
        $arrRes["first"] = $first;
        // 終了数
        $last = $first + $curr_count - 1;
        $arrRes["last"]  = $last;

        return $arrRes;

    }

    /*
     * 商品リスト ページング総数
     */
    function getSearchArrPgList($keyword = null){
//        if(!empty($where)) $where .= " AND ";
//        $sql = "SELECT count(*) as count FROM ".$table_name." WHERE ".$where." del_flg <= 0";

        $sql =  "";
        $sql .=  "     SELECT count(*) as count ";
        $sql .=  "       FROM merch_basics  ";
        $sql .=  " INNER JOIN merch_items  ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id  ";
        $sql .=  "        AND merch_items.del_flg <= 0  ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0  ";
        if(!empty($keyword)){
            $sql .=  "        AND ( merch_basics.name LIKE '%".$keyword."%' OR merch_items.description_1 LIKE '%".$keyword."%'OR merch_items.code LIKE '%".$keyword."%' ) ";
        }
//        $sql .=  "        AND (merch_items.public_flg = 'yes' OR merch_items.public_flg IS NULL) ";
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
     * CSV用情報
     */
    function getCsvBody(){

        App::import('Model','MerchCategories');
        $MerchCategories = new MerchCategories();

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM merch_basics ";
        $sql .=  "  LEFT JOIN merch_items ";
        $sql .=  "         ON merch_basics.id = merch_items.merch_basic_id ";
        $sql .=  "      WHERE merch_basics.del_flg <= 0 ";
        if(!empty($this->basic_name)){
            $sql .=  "        AND merch_basics.name LIKE '%".$this->basic_name."%' ";
        }
        if(!empty($this->category_id)){
            $sql .=  "        AND merch_basics.merch_category_id = '".$this->category_id."' ";
        }
        $sql .=  "   ORDER BY merch_basics.merch_category_id ,merch_basics.code,merch_items.code,merch_items.color,merch_items.size ";

        // ページング用SQL文字列
        $array = $this->query($sql);

        $res = null;
        foreach($array as $key => $row){

            $merch_basic_id = $row["merch_basics"]["id"];
            $merch_item_id  = $row["merch_items"]["id"];

            $res .= '"'.$merch_basic_id.'"';                            // 基本ID
            $res .= ',"'.$merch_item_id.'"';                            // 商品ID
            $res .= ',"'.$row["merch_basics"]["category_name"].'"';        // カテゴリ名
            $res .= ',"'.$row["merch_basics"]["brand"].'"';        // ブランド
            $res .= ',"'.$row["merch_basics"]["name"].'"';                // 商品名
            $res .= ',"'.$row["merch_basics"]["code"].'"';                // 商品コード
            $res .= ',"'.$row["merch_items"]["name"].'"';                // サブ商品名
            $res .= ',"'.$row["merch_items"]["code"].'"';                // 商品番号
            $res .= ',"'.$row["merch_items"]["sales_price"].'"';        // 販売価格
            $res .= ',"'.$row["merch_items"]["list_image"].'"';            // 商品一覧用画像
            $res .= ',"'.$row["merch_items"]["description"].'"';        // 商品説明

            $res .= ',"'.$row["merch_items"]["color"].'"';        // カラー
            $res .= ',"'.$row["merch_items"]["size"].'"';        // サイズ
            $res .= ',"'.$row["merch_items"]["width"].'"';        // 幅
            $res .= ',"'.$row["merch_items"]["length"].'"';        // 丈
            $res .= ',"'.$row["merch_items"]["sleeve_length1"].'"';        // 袖丈
            $res .= ',"'.$row["merch_items"]["shoulder_width"].'"';        // 肩幅
            $res .= ',"'.$row["merch_items"]["sleeve_length2"].'"';        // 裄丈
            $res .= ',"'.$row["merch_items"]["all_length"].'"';        // 総丈
            $res .= ',"'.$row["merch_items"]["waist"].'"';        // ウェスト
            $res .= ',"'.$row["merch_items"]["leg_length"].'"';        // 股下
            $res .= ',"'.$row["merch_items"]["wrist_length"].'"';        // 袖口丈
            $res .= ',"'.$row["merch_items"]["stock_num"].'"';        // 在庫数
            $res .= ',"'.$row["merch_items"]["arrival_date"].'"';        // 入荷日

            // スペック
/*
//            $sql =  "SELECT * FROM merch_specs WHERE del_flg <= 0 ";
//            $arr_2 = $this->query($sql);
//            foreach($arr_2 as $key_2 => $row_2){
//
//                $merch_spec_id = $row_2["merch_specs"]["id"];
//                // SQL処理
//                $sql =  "";
//                $sql .=  "     SELECT merch_item_specs.description ";
//                $sql .=  "       FROM merch_item_specs ";
//                $sql .=  "    WHERE merch_item_specs.del_flg <= 0 ";
//                $sql .=  "      AND merch_item_specs.merch_item_id = '$merch_item_id'";
//                $sql .=  "      AND merch_item_specs.merch_spec_id = '$merch_spec_id'";
//                $arr_2 = $this->query($sql);
//                $description = @$arr_2[0]["merch_item_specs"]["description"];
//                $res .= ',"'.$description.'"';
//            }
*/

//            // オプション
//            $sql =  "SELECT * FROM merch_options WHERE del_flg <= 0 ";
//            $arr_3 = $this->query($sql);
//            foreach($arr_3 as $key_3 => $row_3){
//
//                $merch_option_id = $row_3["merch_options"]["id"];
//                // SQL処理
//                $sql =  "";
//                $sql .=  "     SELECT * ";
//                $sql .=  "       FROM merch_item_options ";
//                $sql .=  "    WHERE merch_item_options.del_flg <= 0 ";
//                $sql .=  "      AND merch_item_options.merch_item_id = '$merch_item_id'";
//                $sql .=  "      AND merch_item_options.merch_option_id = '$merch_option_id'";
//                $arr_3 = $this->query($sql);
//                if(count($arr_3) > 0){
//                    $res .= ',"1"';
//                }else{
//                    $res .= ',"0"';
//                }
//            }

            $res .= "\n";

        }

        return $res;
    }

    /*
     * ランクソート
     */
    function rankSort($data){
//print_r($data);
        $arrRank = $data["sort"];
        $arrPriorRank1 = array();
        $arrPriorRank2 = array();
        $arrNewRank    = array();
        $arrCount = count($data["sort"]);

//print_r($arrRank);
        // ランク設定
        foreach($arrRank as $key => $val){
            $arr = explode(",",$key);
            $knum = sprintf('%011d', $val);
            if($arr[0] > $val){
                $arrPriorRank[$knum."a"]["id"] = $arr[1];
                $arrPriorRank[$knum."a"]["orank"] = $arr[0];
            }else if($arr[0] < $val){
                $arrPriorRank[$knum."c"]["id"] = $arr[1];
                $arrPriorRank[$knum."c"]["orank"] = $arr[0];
            }else{
                $arrPriorRank[$knum."b"]["id"] = $arr[1];
                $arrPriorRank[$knum."b"]["orank"] = $arr[0];
            }
        }
        Ksort($arrPriorRank);
//print_r($arrPriorRank);

        $arrUpDatas = array();
        $i = 1;
        foreach($arrPriorRank as $row){
            $arrUpDatas[$i] = $row;
            ++$i;
        }
//print_r($arrUpDatas);

        // 更新処理
        foreach($arrUpDatas as $rank => $row){
            if($row["orank"] != $rank){
                $data["id"] = $row["id"];
                $data["rank"] = $rank;
                $this->save($data, true, array('rank','modified'));
            }
        }

    }

    // 消費税対応
    function taxUpdate(){

//        $sql = 'UPDATE `merch_items` SET old_price = sales_price , sales_price = round((sales_price / 1.05) * 1.08)';
//        $this->query($sql);


//        $sql = 'SELECT distinct sales_price , old_price FROM merch_items ';
//        $arrItems = $this->query($sql);

        $arrPrice = array();
        for ($count = 100; $count < 5000; $count++){
            $arrPrice[$count]['origin_new'] = round(($count / 1.05) * 1.08);

            $temp1['old'] = '￥'.$count;
            $temp1['new'] = '￥'.number_format(round(($count / 1.05) * 1.08));
            $arrPrice[$count][] = $temp1;

            $temp2['old'] = '￥'.number_format($count);
            $temp2['new'] = '￥'.number_format(round(($count / 1.05) * 1.08));
            $arrPrice[$count][] = $temp2;

            $temp3['old'] = '\\'.number_format($count);
            $temp3['new'] = '\\'.number_format(round(($count / 1.05) * 1.08));
            $arrPrice[$count][] = $temp3;

            $temp3['old'] = '\\'.$count;
            $temp3['new'] = '\\'.number_format(round(($count / 1.05) * 1.08));
            $arrPrice[$count][] = $temp3;

            $temp4['old'] = '\\'.number_format($count);
            $temp4['new'] = '\\'.number_format(round(($count / 1.05) * 1.08));
            $arrPrice[$count][] = $temp4;
        }

        $sql =  'SELECT * FROM merch_basics WHERE del_flg = 0 AND public_flg = 1';
        $list = $this->query($sql);
        foreach($list as $row){
            $html_point = $row["merch_basics"]["html_point"];
            $html_point_new = $row["merch_basics"]["html_point"];
            $arrTempPrice = $arrPrice;
            $arrList = array();
            foreach($arrTempPrice as $num => $arrVal){
                foreach($arrVal as $val){
                    if(strstr($html_point_new,$val["old"])){
                        $arrList[$num] = $val;
                        unset($arrTempPrice[$num]);
                        unset($arrTempPrice[$arrVal['origin_new']]);
                    }
                }
            }
            foreach($arrList as $lnum => $larrVal){
                $html_point_new = str_replace($larrVal["old"],$larrVal["new"],$html_point_new);
            }
            if($html_point == $html_point_new){
echo $row["merch_basics"]["id"]."\n".$row["merch_basics"]["html_point"]."\n\n-----------------------------\n\n";
            }

            $data["id"] = $row["merch_basics"]["id"];
            $data["html_point"] = $html_point_new;
            $this->save($data, true, array('html_point','modified'));
        }

    }

}
