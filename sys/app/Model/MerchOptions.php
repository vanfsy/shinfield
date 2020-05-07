<?php
class MerchOptions extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のオプションがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'merch_options');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'merch_options'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'merch_options';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'merch_options'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'merch_options';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM merch_options WHERE id = '$id' AND del_flg <= 0";
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
	function getListEntityWhere($where = null){
		$table = 'merch_options';
		return parent::getListEntityWhere($table, $where);
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['merch_options']);
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
		$sql .=  "     FROM merch_options ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]            = $row["merch_options"];

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
		return parent::getAllEntity('merch_options');
	}

	/*
	 * CSV用ヘッダー
	 */
	function getCsvHeader(){

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM merch_options ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$array = $this->query($sql);

		$res = null;
		foreach($array as $row){
			$res .= ',"'.$row["merch_options"]["name"].'"';
		}

		return $res;
	}

	/*
	 * CSV用フィールドヘッダー
	 */
	function getCsvFieldHeader(){

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM merch_options ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$array = $this->query($sql);

		$res = array();
		foreach($array as $row){
			$res[] = 'option_'.$row["merch_options"]["id"];
		}

		return $res;
	}

}
?>