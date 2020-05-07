<?php
class Functions extends AppModel {
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
		return parent::isError($data,'functions');
	}

	/*
	 * フィールド
	*/
	function getFields($table = 'functions'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	*/
	function getOptions(){
		$table = 'functions';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	*/
	function getTreeOptions($table = 'functions'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	*/
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM functions WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["functions"][$field];
		return $res;
	}

	/*
	 * リスト
	*/
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM functions WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	*/
	function isSave($data){
		return parent::isSave($data['functions']);
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
		$sql .=  "     FROM functions ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]["id"]              = $row["functions"]["id"];
			$arrRes["list"][$key]["method_name"]     = "FUC_".sprintf('%07d', $row["functions"]["id"]);
			$arrRes["list"][$key]["name"]            = $row["functions"]["name"];
			$arrRes["list"][$key]["description"]     = $row["functions"]["description"];
			$arrRes["list"][$key]["member_variable"] = $row["functions"]["member_variable"];
			$arrRes["list"][$key]["del_flg"]         = $row["functions"]["del_flg"];

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
		return parent::getAllEntity('functions');
	}

}
?>