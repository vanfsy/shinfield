<?php
class PageLayout extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のカテゴリがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'page_layouts');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_layouts'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions($table = 'page_layouts'){

		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'page_layouts'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM page_layouts WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["page_layouts"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM page_layouts WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['page_layouts']);
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
		$sql .=  "     FROM page_layouts ";
		$sql .=  "    WHERE del_flg = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]["id"]            = $row["page_layouts"]["id"];
			$arrRes["list"][$key]["name"]          = $row["page_layouts"]["name"];

		}

		// ページング用
		$arrRes["current_pg"] = $this->getCurrentPg();
		$arrRes["pg_list"] = $this->getArrPgList($sql);
		$arrRes["prev"] = $this->getPgPrev();
		$arrRes["next"] = $this->getPgNext();

		return $arrRes;
	}

}
?>