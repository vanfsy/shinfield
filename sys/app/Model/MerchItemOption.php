<?php
class MerchItemOption extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $arr_error_messages = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["merch_basic_id"]["numeric"]  = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["merch_item_id"]["numeric"]   = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["merch_option_id"]["numeric"] = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["width"]["numeric"]           = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["sleeve_length1"]["numeric"]  = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["shoulder_width"]["numeric"]  = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["sleeve_length2"]["numeric"]  = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");

		$validate["all_length"]["numeric"]      = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["waist"]["numeric"]           = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["leg_length"]["numeric"]      = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["wrist_length"]["numeric"]    = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");
		$validate["stock_num"]["numeric"]       = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");

		$validate["arrival_date"]["date"]       = array("rule" => "date", "allowEmpty" => true, "message" => "不正な日付です。");
		$validate["sales_price"]["numeric"]     = array("rule" => "numeric", "allowEmpty" => true, "message" => "数値のみです。");

		$validate["size"]["notEmpty"]           = array("rule" => "notEmpty", "message" => "必須です。");
		$validate["sales_price"]["notEmpty"]    = array("rule" => "notEmpty", "message" => "必須です。");
//		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のオプションがあります。");
//		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'merch_item_options');
	}

	/*
	 * エラーメッセージ
	 */
	function getErrorMessages(){
		return $this->arr_error_messages;
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'merch_item_options'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'merch_item_options';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'merch_item_options'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'merch_item_options';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM merch_item_options WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		if(count($array)>0){
			return $array[0];
		}else{
			return null;
		}
	}

	/*
	 * リスト
	 */
	function getEntityByItemId($item_id,$row_num = 3){

		// SQL処理
		$sql =  "SELECT * FROM merch_item_options WHERE merch_item_id = '$item_id' AND del_flg <= 0";
		$array = $this->query($sql);
		$arrRes["merch_item_options"] = array();
		foreach($array as $i => $v){
			$arrRes["merch_item_options"][$i] = $v["merch_item_options"];
			if($v["merch_item_options"]["arrival_date"] == "0000-00-00" ){
				$arrRes["merch_item_options"][$i]["arrival_date"] = "";
			}
		}

		$fields = $this->getFields();
		$cnt = count($arrRes["merch_item_options"]);
		if($row_num <= $cnt){
			$arrRes["merch_item_options"][] = $fields;
		}else{
			$inter = $row_num - $cnt;
			$start = $cnt;
			$end = $start + $inter;
			for($i=$start;$i<$end;++$i){
				$arrRes["merch_item_options"][$i] = $fields;
			}
		}
		return $arrRes;
	}

	/*
	 * リスト
	 */
	function getListEntityWhere($where = null){
		$table = 'merch_item_options';
		return parent::getListEntityWhere($table, $where);
	}

	/*
	 * 登録更新処理
	 */
	function isOptionsSave($data){

		$error_messages = null;
		$save_flg = true;
		foreach($data["merch_item_options"] as $i => $row){
			$error_messages[$i] = $this->getFields();
			$flg = false;
			foreach($row as $name => $val){
				if(!empty($val) && $name != "merch_basic_id" && $name != "merch_item_id" && $name != "id" ){
					$flg = true;
				}
			}
			if($flg){
				$save_data = $row;
				$check_data["merch_item_options"] = $row;
				$check_data["merch_item_options"]["sales_price"] = str_replace(",","",$row["sales_price"]);
				if($this->isError($check_data)){
					parent::isSave($save_data);
				}else{
					$error_messages[$i] = $this->error_messages;
					$save_flg = false;
				}
			}

			// 値無し・IDありは削除する
			$del_flg = true;
			foreach($row as $name => $val){
				if(!empty($val) && $name != "merch_basic_id" && $name != "merch_item_id" && $name != "id" ){
					$del_flg = false;
				}
			}
			if($del_flg){
				$del_id = $row["id"];
				if($del_id > 0){
					$this->delete($del_id);
				}
			}
		}
		$this->arr_error_messages = $error_messages;

		return $save_flg;

	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){

		$boolean = false;
		if (empty($data)) return $boolean;

		// エラーチェック
		$this->isValid();
		$this->set($data);
		if(!$this->validates($data)){
			return $boolean;
		}

		// 不要ファイルの削除
		$conditions["merch_item_id"] = $data["merch_item_id"];
		$this->deleteAll($conditions);

		// 登録処理
		foreach($data["merch_option_id"] as $key => $val){
			$db_data["id"] = null;
			$db_data["merch_basic_id"]  = $data["merch_basic_id"];
			$db_data["merch_item_id"]   = $data["merch_item_id"];
			$db_data["merch_option_id"] = $key;
			$this->save($db_data, true, array('merch_basic_id','merch_item_id','merch_option_id','created','modified'));
		}

		return true;

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
		$sql .=  "     FROM merch_item_options ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){
			$arrRes["list"][$key] = $row["merch_item_options"];
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
		return parent::getAllEntity('merch_item_options');
	}

	/*
	 * オプションリスト
	 */
	function getOptionEntityByBasicId($basics_id){

		// SQL処理
		$sql =  "SELECT * FROM merch_options WHERE del_flg <= 0";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $key => $val){
			$arrRes[$key]["merch_options"] = $val["merch_options"];
			$merch_option_id = $val["merch_options"]["id"];
			$arrRes[$key]["merch_options"]["checked"] = null;

			$sql  = "";
			$sql .= "     SELECT * ";
			$sql .= "       FROM merch_basics ";
			$sql .= " INNER JOIN merch_item_options ";
			$sql .= "         ON merch_basics.id = merch_item_options.merch_basic_id ";
			$sql .= "      WHERE merch_item_options.merch_option_id = '$merch_option_id'";
			$sql .= "        AND merch_basics.id = '$basics_id'";
			$sql .= "        AND merch_item_options.del_flg <= 0";
			$array = $this->query($sql);
			if(count($array) > 0){
				$arrRes[$key]["merch_options"]["checked"] = "checked='checked'";
			}else{
				// 新規登録時は初期値を設定
				$sql =  "SELECT * FROM merch_options WHERE `check` = '1' AND id = '$merch_option_id' AND del_flg <= 0";
				$array = $this->query($sql);
				if(count($array) > 0) $arrRes[$key]["merch_options"]["checked"] = "checked='checked'";
			}
		}
		return $arrRes;
	}

	/*
	 * オプションリスト
	 */
	function getOptionIdsByBasicId($basics_id){

		// SQL処理
		$sql =  "SELECT * FROM merch_options WHERE del_flg <= 0";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $key => $val){
			$merch_option_id = $val["merch_options"]["id"];

			$sql  = "";
			$sql .= "     SELECT * ";
			$sql .= "       FROM merch_basics ";
			$sql .= " INNER JOIN merch_item_options ";
			$sql .= "         ON merch_basics.id = merch_item_options.merch_basic_id ";
			$sql .= "      WHERE merch_item_options.merch_option_id = '$merch_option_id'";
			$sql .= "        AND merch_basics.id = '$basics_id'";
			$sql .= "        AND merch_item_options.del_flg <= 0";
			$array = $this->query($sql);
			if(count($array) > 0){
				$arrRes[$merch_option_id] = 1;
			}
		}

		return $arrRes;
	}

	/*
	 * オプションリスト
	 */
	function getArrNames($array){
		$arrRes = array();
		foreach($array as $key => $val){
			if($val == 1){
				$sql =  "SELECT * FROM merch_options WHERE id = '$key' AND del_flg <= 0";
				$array = $this->query($sql);
				$arrRes[] = $array[0]["merch_options"]["name"];
			}
		}
		return $arrRes;
	}

}
?>