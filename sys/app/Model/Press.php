<?php
class Press extends AppModel {
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
		return parent::isError($data,'presses');
	}

	/*
	 * フィールド
	*/
	function getFields($table = 'presses'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	*/
	function getOptions(){
		$table = 'presses';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	*/
	function getTreeOptions($table = 'presses'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	*/
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM presses WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["presses"][$field];
		return $res;
	}

	/*
	 * リスト
	*/
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM presses WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		if(count($array)>0){
			return $array[0];
		}else{
			return array();
		}
	}

	/*
	 * 登録更新処理
	*/
	function isSave($data){
		return parent::isSave($data['presses']);
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
		$sql .=  "     FROM presses ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key] = $row["presses"];

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
		return parent::getAllEntity('presses');
	}

}
?>