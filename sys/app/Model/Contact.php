<?php
class Contact extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	*/

	function isValid(){

		$validate["name_kana"]["katakana"]   = array("rule" => "katakana", "allowEmpty" => true, "message" => "フリガナ入力でお願いします。");
		$validate["resident_name_kana"]["katakana"]   = array("rule" => "katakana", "allowEmpty" => true, "message" => "フリガナ入力でお願いします。");
		$validate["tel"]["isCode"]           = array("rule" => "isCode", "allowEmpty" => false, "message" => "お電話番号は数字記号のみです。");
		$validate["fax"]["isCode"]           = array("rule" => "isCode", "allowEmpty" => true, "message" => "FAX番号は数字記号のみです。");
		$validate["email"]["email"]          = array("rule" => "email", "allowEmpty" => false, "message" => "不正なメールアドレスです。");
		$validate["email"]["isConfirm"]      = array("rule" => array("isConfirm","Contacts"), "allowEmpty" => true, "message" => "確認メールアドレスと一致しません。");
		$validate["postcode1"]["numeric"]    = array("rule" => "numeric", "allowEmpty" => true, "message" => "郵便番号1は数字でお願いします。");
		$validate["postcode2"]["numeric"]    = array("rule" => "numeric", "allowEmpty" => true, "message" => "郵便番号2は数字でお願いします。");
		$validate["resident_age"]["numeric"] = array("rule" => "numeric", "allowEmpty" => true, "message" => "年齢は数字でお願いします。");

		$validate["form_type"]["notEmpty"]   = array("rule" => "notEmpty", "message" => "お問合せ項目は必須です。");
		$validate["name"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "お名前は必須です。");
		$validate["tel"]["notEmpty"]         = array("rule" => "notEmpty", "message" => "お電話番号は必須です。");
		$validate["email"]["notEmpty"]       = array("rule" => "notEmpty", "message" => "メールアドレスは必須です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	* */
	function isError($data){
		$line = null;
		if(isset($data['contacts']['form_type'])){
			$form_type = $data['contacts']['form_type'];
			if(is_array($form_type)){
				foreach($form_type as $row){
					if(!empty($line)) $line .= ",";
					$line .= $row;
				}
			}
		}
		$data['contacts']['form_type'] = $line;
		return parent::isError($data,'contacts');
	}

	/*
	 * フィールド
	*/
	function getFields($table = 'contacts'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	*/
	function getOptions(){

		$table = 'contacts';
		$where = null;
		return parent::getOptions($table,$where);

	}

	/*
	 * オプションリスト
	*/
	function getTreeOptions($table = 'contacts'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	*/
	function getOneFieldById($id,$field){

		$table = 'contacts';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	*/
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM contacts WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * リスト
	*/
	function getListEntityWhere($const_name){

		$table = 'contacts';
		$where = "level = 1 AND const_name = '$const_name'";
		$where .=  " ORDER BY value ASC";

		return parent::getListEntityWhere($table, $where);

	}

	/*
	 * 登録更新処理
	*/
	function isSave($data){
		$form_type = $data['contacts']['form_type'];
		if(is_array($form_type)){
			$line = null;
			foreach($form_type as $row){
				if(!empty($line)) $line .= ",";
				$line .= $row;
			}
			$data['contacts']['form_type'] = $line;
		}
		return parent::isSave($data['contacts']);
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
		$sql .=  "     FROM contacts ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND level = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key] = $row["contacts"];

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
		return parent::getAllEntity('contacts');
	}

	/*
	 * 配列取得
	*/
	function getArrOptions($const_name){

		// SQL処理
		$sql = "SELECT * FROM contacts WHERE del_flg <= 0 AND level = 1 AND const_name = '$const_name' ORDER BY value ";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $row){
			$arrRes[$row["contacts"]["value"]] = $row["contacts"]["name"];
		}

		return $arrRes;
	}

	/*
	 * 定数取得
	*/
	function getConstValue($const_name,$name){

		// SQL処理
		$sql = "SELECT * FROM contacts WHERE del_flg <= 0 AND const_name = '$const_name' AND name = '$name' ";
		$array = $this->query($sql);

		$arrRes = null;
		if(count($array) > 0){
			$arrRes = $array[0]["contacts"]["value"];
		}

		return $arrRes;
	}

	/*
	 * 有効・無効更新処理
	*/
	function chgActive($id,$acteve_flg){
		$data['contacts']["id"] = $id;
		$data['contacts']["active_flg"] = $acteve_flg;
		parent::isSave($data['contacts']);
	}

}
?>