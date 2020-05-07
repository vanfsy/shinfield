<?php
class CsvList extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["merch_basic_name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'csv_lists');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'csv_lists'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'csv_lists';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'csv_lists'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM csv_lists WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["csv_lists"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM csv_lists WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['csv_lists']);
	}

	/*
	 * CSV登録処理
	 */
	function csvInsert($data){
		$arrFields = array();
		foreach($data as $key => $row){
			if(!empty($row)) $arrFields[] = $key;
			if($key === "limit_date"){
				$data[$key] = str_replace("/","-",$row);
			}
		}
		if(empty($data["merch_basic_name"])) return false;

		$data["id"] = null;
		$arrFields[] = "modified";
		$arrFields[] = "created";

		// 登録処理
		$this->save($data, true, $arrFields);
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
		$sql .=  "     FROM csv_lists ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]              = $row["csv_lists"];

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
		return parent::getAllEntity('csv_lists');
	}

	/*
	 * 待機リスト 上限100件
	 */
	function getWaitEntity(){

		// SQL処理
		$sql = " SELECT * FROM csv_lists WHERE del_flg <= 0 ORDER BY category_name , merch_basic_name , name, color, size  LIMIT 1000 ";
		$array = $this->query($sql);

		return $array;
	}

	/*
	 * リスト
	 */
	function getOneEntityBySlug($slug,$field){

		// SQL処理
		$sql =  "";
		$sql .= " SELECT * ";
		$sql .= "   FROM csv_lists ";
		$sql .= "  WHERE `$field` = '$slug' ";
		$sql .= "    AND del_flg <= 0";
		$array = $this->query($sql);

		if(count($array) > 0){
			return $array[0]["csv_lists"];
		}else{
			return null;
		}
	}

	/*
	 * 商品情報登録更新処理
	 */
	function itemsSave(){

		// SQL処理 更新
		$sql = "";
		$sql .= " UPDATE merch_items ";
		$sql .= "      , csv_lists";
		$sql .= "    SET merch_items.merch_basic_id = csv_lists.merch_basic_id ";
		$sql .= "      , merch_items.name = csv_lists.name ";
		$sql .= "      , merch_items.sales_price = csv_lists.sales_price ";
		$sql .= "      , merch_items.code = csv_lists.code ";
		$sql .= "      , merch_items.list_image = csv_lists.list_image ";
		$sql .= "      , merch_items.size = csv_lists.size ";
		$sql .= "      , merch_items.width = csv_lists.width ";
		$sql .= "      , merch_items.length = csv_lists.length ";
		$sql .= "      , merch_items.sleeve_length1 = csv_lists.sleeve_length1 ";
		$sql .= "      , merch_items.shoulder_width = csv_lists.shoulder_width ";
		$sql .= "      , merch_items.sleeve_length2 = csv_lists.sleeve_length2 ";
		$sql .= "      , merch_items.all_length = csv_lists.all_length ";
		$sql .= "      , merch_items.waist = csv_lists.waist ";
		$sql .= "      , merch_items.leg_length = csv_lists.leg_length ";
		$sql .= "      , merch_items.wrist_length = csv_lists.wrist_length ";
		$sql .= "      , merch_items.stock_num = csv_lists.stock_num ";
		$sql .= "      , merch_items.arrival_date = csv_lists.arrival_date ";
		$sql .= "      , merch_items.description = csv_lists.description ";
/*		$sql .= "      , merch_items.material = csv_lists.material ";*/
		$sql .= "      , merch_items.color = csv_lists.color ";
		$sql .= "      , merch_items.modified = now() ";
		$sql .= "  WHERE merch_items.id = csv_lists.merch_item_id ";
		$sql .= "    AND csv_lists.del_flg <= 0 ";
		$this->query($sql);

		// SQL処理 新規登録
		$sql = "";
		$sql .= "   INSERT INTO merch_items ";
		$sql .= "             ( merch_basic_id ";
		$sql .= "             , name ";
		$sql .= "             , sales_price ";
		$sql .= "             , code ";
		$sql .= "             , list_image ";
		$sql .= "             , size ";
		$sql .= "             , width ";
		$sql .= "             , length ";
		$sql .= "             , sleeve_length1 ";
		$sql .= "             , shoulder_width ";
		$sql .= "             , sleeve_length2 ";
		$sql .= "             , all_length ";
		$sql .= "             , waist ";
		$sql .= "             , leg_length ";
		$sql .= "             , wrist_length ";
		$sql .= "             , stock_num ";
		$sql .= "             , arrival_date ";
		$sql .= "             , description ";
/*		$sql .= "             , material ";*/
		$sql .= "             , color ";
		$sql .= "             , modified ";
		$sql .= "             , created ) ";
		$sql .= "   SELECT merch_basic_id ";
		$sql .= "        , name ";
		$sql .= "        , sales_price ";
		$sql .= "        , code ";
		$sql .= "        , list_image ";
		$sql .= "        , size ";
		$sql .= "        , width ";
		$sql .= "        , length ";
		$sql .= "        , sleeve_length1 ";
		$sql .= "        , shoulder_width ";
		$sql .= "        , sleeve_length2 ";
		$sql .= "        , all_length ";
		$sql .= "        , waist ";
		$sql .= "        , leg_length ";
		$sql .= "        , wrist_length ";
		$sql .= "        , stock_num ";
		$sql .= "        , arrival_date ";
		$sql .= "        , description ";
/*		$sql .= "        , material ";*/
		$sql .= "        , color ";
		$sql .= "        , now() ";
		$sql .= "        , now() ";
		$sql .= "     FROM csv_lists ";
		$sql .= "    WHERE del_flg <= 0 ";
		$sql .= "      AND ( merch_item_id IS NULL OR merch_item_id = '') ";
		$sql .= " ORDER BY category_name , merch_basic_name , name, color, size ";
		$this->query($sql);

		$sql = "SELECT * FROM csv_lists WHERE del_flg <= 0 ";
		$array = $this->query($sql);

		$sql = "TRUNCATE csv_lists ";
		$this->query($sql);

		return $array;

	}

	/*
	 * 未設定フィールドの追加
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
				$sql =  "ALTER TABLE `csv_lists` ADD `$field` TEXT NULL ";
				$this->query($sql);
			}
		}

	}

	/*
	 * 商品情報登録更新処理
	 */
	function isAllDel(){
		$sql =  "DELETE FROM `csv_lists`";
		$this->query($sql);
	}

}
?>