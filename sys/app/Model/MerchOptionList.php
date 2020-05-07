<?php
class MerchOptionList extends AppModel {
	var $validate = array();

	/*
	 * 入力値検証
	 */

	function isValid(){

//		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のオプション名があります。");
		$validate["price"]["numeric"]  = array("rule" => "numeric", "message" => "このフィールドは金額です。");

		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'merch_option_lists');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'merch_option_lists'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'merch_option_lists';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'merch_option_lists'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'merch_option_lists';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM merch_option_lists WHERE id = '$id' AND del_flg <= 0";
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
		$table = 'merch_option_lists';
		return parent::getListEntityWhere($table, $where);
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['merch_option_lists']);
	}

	/*
	 * ページング
	 */
	function getPagingEntity($disp_num,$pgnum,$merch_option_id){

		// ページング用パラメータ設定
		$this->setDispNum($disp_num);
		$this->setPgNum($pgnum);

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM merch_option_lists ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND merch_option_id = '$merch_option_id' ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){
			$arrRes["list"][$key] = $row["merch_option_lists"];
		}

		// ページング用
		$arrRes["current_pg"] = $this->getCurrentPg();
		$where = "merch_option_id = '$merch_option_id' ";
		$arrRes["pg_list"] = $this->getArrPgList($sql);
		$arrRes["prev"] = $this->getPgPrev();
		$arrRes["next"] = $this->getPgNext();

		return $arrRes;
	}

	/*
	 * 有効全件リスト取得
	 */
	function getAllEntity(){
		return parent::getAllEntity('merch_option_lists');
	}

}
?>