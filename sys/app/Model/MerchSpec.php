<?php
class MerchSpec extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["field_name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");

		$validate["field_name"]["isCode"]  = array("rule" => "isCode", "message" => "英数字・「_」・「-」のみです。");
		$validate["field_type"]["isCode"]  = array("rule" => "isCode", "message" => "英数字・「_」・「-」のみです。");
		$validate["field_valid"]["isCode"]  = array("rule" => "isCode", "message" => "英数字・「_」・「-」のみです。");

		$validate["field_name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["field_type"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["name"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["field_valid"]["notEmpty"] = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'merch_specs');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'merch_specs'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'merch_specs';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'merch_specs'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'merch_specs';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM merch_specs WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * リスト
	 */
	function getListEntityWhere($where = null){
		$table = 'merch_specs';
		return parent::getListEntityWhere($table, $where);
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){

		// チェックボックス処理
		if(!isset($data["merch_specs"]["listdiv_flg"])) $data["merch_specs"]["listdiv_flg"] = '0';
		if(!isset($data["merch_specs"]["required"])) $data["merch_specs"]["required"] = '0';
		if(!isset($data["merch_specs"]["unique"])) $data["merch_specs"]["unique"] = '0';
		if(!isset($data["merch_specs"]["confirm"])) $data["merch_specs"]["confirm"] = '0';

		// 最大値・最小値処理
		if(empty($data["merch_specs"]["min_len"])) $data["merch_specs"]["min_len"] = '0';
		if(empty($data["merch_specs"]["max_len"])) $data["merch_specs"]["max_len"] = '0';

		// merch_itemsにカラム追加
		$field_name = $data["merch_specs"]["field_name"];
		$field_type = $data["merch_specs"]["field_type"];
		$add_type = "TEXT  NULL";
		if($field_type == "date") $add_type = "DATETIME  NOT NULL";
		if($field_type == "money") $add_type = "DECIMAL(12,2)  NOT NULL";

		$array = $this->getFields("merch_items");
		$flg = false;
		foreach($array as $key => $row){
			if($key == $field_name){
				$flg = true;
			}
		}
		if(!$flg){
			$sql =  "ALTER TABLE `merch_items` ADD `$field_name` $add_type ";
			$this->query($sql);
		}

		return parent::isSave($data['merch_specs']);

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
		$sql .=  "     FROM merch_specs ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  " ORDER BY rank ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		// ランク再処理
		$array = $this->reSort($array,$pgnum,$disp_num);

		$arrRes["list"] = array();
		foreach($array as $key => $row){
			$arrRes["list"][$key]            = $row["merch_specs"];
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
		return parent::getAllEntity('merch_specs');
	}

	/*
	 * CSV用ヘッダー
	 */
	function getCsvHeader(){

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM merch_specs ";
		$sql .=  " INNER JOIN merch_style_specs ";
		$sql .=  "         ON merch_specs.id = merch_style_specs.merch_spec_id ";
		$sql .=  "      WHERE merch_specs.del_flg <= 0 ";
		$sql .=  "        AND merch_style_specs.merch_style_id = 1 ";
		$sql .=  "   ORDER BY merch_style_specs.rank ";
		$array = $this->query($sql);

		$res = null;
		foreach($array as $row){
			$res .= ',"'.$row["merch_specs"]["name"].'"';
		}

		return $res;
	}

	/*
	 * CSV用フィールドヘッダー
	 */
	function getCsvFieldHeader(){

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM merch_specs ";
		$sql .=  " INNER JOIN merch_style_specs ";
		$sql .=  "         ON merch_specs.id = merch_style_specs.merch_spec_id ";
		$sql .=  "      WHERE merch_specs.del_flg <= 0 ";
		$sql .=  "        AND merch_style_specs.merch_style_id = 1 ";
		$sql .=  "   ORDER BY merch_style_specs.rank ";
		$array = $this->query($sql);
		$res = array();
		foreach($array as $row){
			$res[] = $row["merch_specs"]["field_name"];
		}

		return $res;
	}

	/*
	 * リスト
	 */
	function getEntityOptions(){

		// SQL処理
		$sql = "";
		$sql .= " SELECT * ";
		$sql .= "   FROM merch_specs ";
		$sql .= "  WHERE del_flg <= 0 ";
		$sql .= "    AND field_type IN ('options')";
		$array = $this->query($sql);
		return $array;
	}

	/*
	 * セレクトボックス・チェックボックス・ラジオボタンの情報を配列で取得
	 */
	function getHtmlOptions(){

		$MerchSpecLists = ClassRegistry::init('MerchSpecLists');

		// SQL処理
		$sql = "";
		$sql .= " SELECT * ";
		$sql .= "   FROM merch_specs ";
		$sql .= "  WHERE del_flg <= 0 ";
		$sql .= "    AND field_type IN ('selectbox','checkbox','radio')";
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $row){
			$merch_spec_id = $row["merch_specs"]["id"];
			$field_name = $row["merch_specs"]["field_name"];
			$list = $MerchSpecLists->getEntityBySpecId($merch_spec_id);
			$temp = array();
			$temp[] = "-- 選択 --";
			foreach($list as $val){
				$name = $val["merch_spec_lists"]["name"];
				$value = $val["merch_spec_lists"]["value"];
				$temp[$value] = $name;
			}
			$arrRes[$field_name] = $temp;
		}

		return $arrRes;
	}

	/*
	 * 入力値検証
	 */
	function isFormValid($data,$layout_id){

		$arrRes = array();

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM merch_style_specs ";
		$sql .=  " INNER JOIN merch_specs ";
		$sql .=  "         ON merch_style_specs.merch_spec_id = merch_specs.id ";
		$sql .=  "        AND merch_specs.del_flg <= 0 ";
		$sql .=  "      WHERE merch_style_specs.merch_style_id = '$layout_id' ";
		$sql .=  "        AND merch_style_specs.del_flg <= 0 ";
		$array = $this->query($sql);

		foreach($array as $row){
			$field_name = $row["merch_specs"]["field_name"];
			$this->error_messages[$field_name] = "";
		}

		foreach($data["merch_specs"] as $field_name => $value){

			// SQL処理
			$sql =  "SELECT * FROM merch_specs WHERE field_name = '$field_name' AND del_flg <= 0";

			$array = $this->query($sql);
			$type = @$array[0]["merch_specs"]["field_type"];
			$required = @$array[0]["merch_specs"]["required"];
			$min_len = @$array[0]["merch_specs"]["min_len"];
			$max_len = @$array[0]["merch_specs"]["max_len"];

			// 必須検証
			if($required == 1){
				$validate[$field_name]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須です。");
			}

			// 入力値制限検証
			if($type == "numalpha"){
				$validate[$field_name]["alphaNumeric"]  = array("rule" => "alphaNumeric", "allowEmpty" => true , "message" => "半角英数のみです。");
			}else if($type == "num"){
				$validate[$field_name]["numeric"]  = array("rule" => "numeric", "allowEmpty" => true , "message" => "半角数字のみです。");
			}else if($type == "alpha"){
				$validate[$field_name]["isAlpha"]  = array("rule" => "isAlpha", "allowEmpty" => true , "message" => "半角英字のみです。");
			}else if($type == "mail"){
				$validate[$field_name]["email"]  = array("rule" => "email", "allowEmpty" => true , "message" => "不正なメールアドレスです。");
			}else if($type == "kana"){
				$validate[$field_name]["katakana"]  = array("rule" => "katakana", "allowEmpty" => true , "message" => "カタカナのみです。");
			}

			// 文字数判定
			if($min_len > 0){
				$validate[$field_name]["minLengthJp"]  = array("rule" => array("minLengthJp",$min_len), "allowEmpty" => true , "message" => " ".$min_len."文字以上です。");
			}
			if($max_len > 0){
				$validate[$field_name]["maxLengthJp"]  = array("rule" => array("maxLengthJp",$max_len), "allowEmpty" => true , "message" => " ".$max_len."文字以下です。");
			}

		}

		// エラーチェック
		$this->set($data["merch_specs"]);
		$this->validate = $validate;

		foreach($data["merch_specs"] as $field_name => $value){

			// SQL処理
			$sql =  "SELECT * FROM merch_specs WHERE field_name = '$field_name' AND del_flg <= 0";

			$array = $this->query($sql);
			$name = @$array[0]["merch_specs"]["name"];
			$confirm = @$array[0]["merch_specs"]["confirm"];

			$this->error_messages[$field_name] = null;
			foreach( $this->invalidFields() as $e_key => $e_val ){
				if($e_key == $field_name){
					$this->error_messages[$field_name] = $name."は".$e_val;
				}
			}
		}

		$err_flg = true;
		foreach($data["merch_specs"] as $field_name => $value){

			// SQL処理
			$sql =  "SELECT * FROM merch_specs WHERE field_name = '$field_name' AND del_flg <= 0";

			$array = $this->query($sql);
			$confirm  = @$array[0]["merch_specs"]["confirm"];
			$type     = @$array[0]["merch_specs"]["field_type"];
			$required = @$array[0]["merch_specs"]["required"];
			$value = $data["merch_specs"][$field_name];

			// 確認検証
			if($confirm == 1){
				$confirm_val = $data["merch_specs"][$field_name."_confirm"];
				if($value <> $confirm_val){
					$this->error_messages[$field_name] = "確認入力と一致していません。";
					$err_flg = false;
				}
			}
		}

		// 必須判定
		// SQL処理
		$sql =  "SELECT * FROM merch_specs WHERE required = 1 AND del_flg <= 0";

		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM merch_style_specs ";
		$sql .=  " INNER JOIN merch_specs ";
		$sql .=  "         ON merch_style_specs.merch_spec_id = merch_specs.id ";
		$sql .=  "        AND merch_specs.del_flg <= 0 ";
		$sql .=  "      WHERE merch_style_specs.merch_style_id = '$layout_id' ";
		$sql .=  "        AND merch_specs.required = 1";
		$sql .=  "        AND merch_style_specs.del_flg <= 0 ";

		$array = $this->query($sql);
		if(count($array) > 0){
			foreach($array as $row){
				$field_name = $row["merch_specs"]["field_name"];
				if(!isset($data["merch_specs"][$field_name])){
					$this->error_messages[$field_name] = "必須です。";
					$err_flg = false;
				}
			}
		}

		if(count($this->invalidFields())>0 || !$err_flg){
			return false;
		}else{
			return true;
		}
	}

	/*
	 * merch_style_id毎のデータ
	 */
	function getEntityByStyleId($merch_style_id){

		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM merch_style_specs ";
		$sql .=  " INNER JOIN merch_specs ";
		$sql .=  "         ON merch_style_specs.merch_spec_id = merch_specs.id ";
		$sql .=  "        AND merch_specs.del_flg <= 0 ";
		$sql .=  "      WHERE merch_style_specs.merch_style_id = '$merch_style_id' ";
		$sql .=  "        AND merch_style_specs.del_flg <= 0 ";
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $i => $row){
			$field_name = $row["merch_specs"]["field_name"];
			foreach($row["merch_specs"] as $key => $val){
				$arrRes["merch_specs"][$field_name][$key] = $val;
			}
		}
		return $arrRes;
	}

	/*
	 * アイテム情報をスペックに当てはめる
	 */
	function setValues($data){
		$sql =  "SELECT * FROM merch_specs WHERE del_flg <= 0";
		$array = $this->query($sql);
		if(count($array) > 0){
			foreach($array as $row){
				$field_name = $row["merch_specs"]["field_name"];
				if(!isset($data[$field_name])){
					$data[$field_name] = null;
				}
			}
		}
		return $data;
	}

	var $error_messages;

	/*
	 * 入力値検証
	 */
	function getFormErrors(){
		return $this->error_messages;
	}

}
?>