<?php
class Form extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	*/

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のページ名があります。");
		$validate["slug"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のスラッグがあります。");
		$validate["slug"]["isSlug"]    = array("rule" => "isSlug", "message" => "英数字とアンダーバーのみです。");
		$validate["email"]["email"]    = array("rule" => "email", "message" => "正しいメールを入力してください。");

		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["slug"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	* */
	function isError($data){
		return parent::isError($data,'forms');
	}

	/*
	 * フィールド
	*/
	function getFields($table = 'forms'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	*/
	function getOptions(){
		$table = 'forms';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	*/
	function getTreeOptions($table = 'forms'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	*/
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM forms WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["forms"][$field];
		return $res;
	}

	/*
	 * リスト
	*/
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM forms WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	*/
	function isSave($data){
		return parent::isSave($data['forms']);
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
		$sql .=  "     FROM forms ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]              = $row["forms"];

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
		return parent::getAllEntity('forms');
	}

	/*
	 * リスト
	*/
	function getOneEntityBySlug($slug,$field){

		// SQL処理
		$sql =  "";
		$sql .= " SELECT * ";
		$sql .= "   FROM forms ";
		$sql .= "  WHERE `$field` = '$slug' ";
		$sql .= "    AND del_flg <= 0";
		$array = $this->query($sql);

		if(count($array) > 0){
			return $array[0]["forms"];
		}else{
			return null;
		}
	}

}
?>