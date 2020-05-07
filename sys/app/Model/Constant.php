<?php
class Constant extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	*/

	function isValid(){

		//		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のカテゴリがあります。");
		//		$validate["value"]["code"]  = array("rule" => "isCode", "message" => "このフィールドは英数字・記号です。");

		$validate["name"]["notEmpty"]   = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["value"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	function isError($data){
		return parent::isError($data,'constants');
	}
	* */

	/*
	 * フィールド
	function getFields($table = 'constants'){
		return parent::getFields($table);
	}
	*/

	/*
	 * オプションリスト
	function getOptions(){

		$table = 'constants';
		$where = null;
		return parent::getOptions($table,$where);

	}
	*/

	/*
	 * オプションリスト
	function getTreeOptions($table = 'constants'){

		return parent::getTreeOptions($table);
	}
	*/

	/*
	 * リスト
	function getOneFieldById($id,$field){

		$table = 'constants';
		return parent::getOneFieldById($table,$id,$field);
	}
	*/

	/*
	 * リスト
	*/
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM constants WHERE id = '$id' AND del_flg <= 0 ORDER BY id";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * リスト
	function getListEntityWhere($const_name){

		$table = 'constants';
		$where = "level = 1 AND const_name = '$const_name'";
		if($const_name == 'pref_delivery_charge'){
			$where .=  " ORDER BY id ASC";
		}else{
			$where .=  " ORDER BY value ASC";
		}

		return parent::getListEntityWhere($table, $where);

	}
	*/

	/*
	 * 登録更新処理
	function isSave($data){
		return parent::isSave($data['constants']);
	}
	*/

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
		$sql .=  "     FROM constants ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND level = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key] = $row["constants"];

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
	function getAllEntity(){
		return parent::getAllEntity('constants');
	}
	*/

	/*
	 * 配列取得
	*/
	function getArrOptions($const_name){

		// SQL処理
		$sql = "SELECT * FROM constants WHERE del_flg <= 0 AND level = 1 AND const_name = '$const_name' ORDER BY value ";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $row){
			$arrRes[$row["constants"]["value"]] = $row["constants"]["name"];
		}

		return $arrRes;
	}

	/*
	 * 定数取得
	*/
	function getConstValue($const_name,$name){

		// SQL処理
		$sql = "SELECT * FROM constants WHERE del_flg <= 0 AND const_name = '$const_name' AND name = '$name' ";
		$array = $this->query($sql);

		$arrRes = null;
		if(count($array) > 0){
			$arrRes = $array[0]["constants"]["value"];
		}

		return $arrRes;
	}

	/*
	 * 有効・無効更新処理
	*/
	function chgActive($id,$acteve_flg){
		$data['constants']["id"] = $id;
		$data['constants']["active_flg"] = $acteve_flg;
		parent::isSave($data['constants']);
	}

}
?>