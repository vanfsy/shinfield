<?php
class FormField extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
//		$validate["code"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
//		$validate["code"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'form_fields');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'form_fields'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'form_fields';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'form_fields'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM form_fields WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["form_fields"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM form_fields WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['form_fields']);
	}

	/*
	 * ページング
	 */
	function getPagingEntity($disp_num,$pgnum,$form_id){

		// ページング用パラメータ設定
		$this->setDispNum($disp_num);
		$this->setPgNum($pgnum);

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM form_fields ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND form_id = '$form_id' ";
		$sql .=  "    ORDER BY id ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){
			$arrRes["list"][$key] = $row["form_fields"];
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
		return parent::getAllEntity('form_fields');
	}

	/*
	 * リスト
	 */
	function getFieldNameByFormId($form_id){

		// SQL処理
		$sql =  "SELECT * FROM form_fields WHERE form_id = '$form_id' AND del_flg <= 0";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $row){
			$arrRes[$row["form_fields"]["field_name"]] = null;
			if($row["form_fields"]["confirm"] == 1){
				$arrRes[$row["form_fields"]["field_name"]."_confirm"] = null;
			}
		}
		return $arrRes;
	}

	/*
	 * 入力値検証
	 */
	function isFormValid($data,$form_id){

		// SQL処理
		$sql =  "SELECT * FROM form_fields WHERE form_id = '$form_id' AND del_flg <= 0";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $row){
			// 入力値
			$field_name = $row["form_fields"]["field_name"];
			$value = $data[$field_name];

			// 必須検証
			if($row["form_fields"]["required"] == 1){
				$validate[$field_name]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須です。");
			}

			// 入力値制限検証
			if($row["form_fields"]["field_valid"] == "numalpha"){
				$validate[$field_name]["alphaNumeric"]  = array("rule" => "alphaNumeric", "allowEmpty" => true , "message" => "半角英数のみです。");
			}else if($row["form_fields"]["field_valid"] == "num"){
				$validate[$field_name]["numeric"]  = array("rule" => "numeric", "allowEmpty" => true , "message" => "半角数字のみです。");
			}else if($row["form_fields"]["field_valid"] == "alpha"){
				$validate[$field_name]["isAlpha"]  = array("rule" => "isAlpha", "allowEmpty" => true , "message" => "半角英字のみです。");
			}else if($row["form_fields"]["field_valid"] == "mail"){
				$validate[$field_name]["email"]  = array("rule" => "email", "allowEmpty" => true , "message" => "不正なメールアドレスです。");
			}else if($row["form_fields"]["field_valid"] == "kana"){
				$validate[$field_name]["katakana"]  = array("rule" => "katakana", "allowEmpty" => true , "message" => "カタカナのみです。");
			}else if($row["form_fields"]["field_valid"] == "hiragana"){
				$validate[$field_name]["hiragana"]  = array("rule" => "hiragana", "allowEmpty" => true , "message" => "ひらがなのみです。");
			}

		}

		// エラーチェック
		$this->set($data);
		$this->validate = $validate;

		foreach($array as $row){
			// 入力値
			$field_name = $row["form_fields"]["field_name"];
			$name = $row["form_fields"]["description"];
			$this->error_messages[$field_name] = null;
			foreach( $this->invalidFields() as $e_key => $e_val ){
				if($e_key == $field_name){
					$this->error_messages[$field_name] = $name."は".$e_val[0];
				}
			}
		}

		$err_flg = true;
		foreach($array as $row){
			// 入力値
			$field_name = $row["form_fields"]["field_name"];
			$value = $data[$field_name];

			// 確認検証
			if($row["form_fields"]["confirm"] == 1){
				$confirm = $data[$field_name."_confirm"];
				if($value <> $confirm){
					$this->error_messages[$field_name] = "確認入力と一致していません。";
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

	var $error_messages;

	/*
	 * 入力値検証
	 */
	function getFormErrors(){
		return $this->error_messages;
	}
}
?>