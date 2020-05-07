<?php
class PageCategory extends AppModel {
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
		return parent::isError($data,'page_categories');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_categories'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions($table = 'page_categories'){

		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'page_categories'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM page_categories WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["page_categories"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM page_categories WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['page_categories']);
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
		$sql .=  "     FROM page_categories ";
		$sql .=  "    WHERE del_flg = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$parent_id = $row["page_categories"]["parent_id"];

			$sql =  "SELECT name FROM page_categories WHERE id = '$parent_id'";
			$arrCat = $this->query($sql);
			$arrRes["list"][$key]["parent_name"]   = "親カテゴリなし";
			if(count($arrCat) > 0){
				$arrRes["list"][$key]["parent_name"]   = $arrCat[0]["page_categories"]["name"];
			}

			$arrRes["list"][$key]["id"]            = $row["page_categories"]["id"];
			$arrRes["list"][$key]["name"]          = $row["page_categories"]["name"];

		}

		// ページング用
		$arrRes["current_pg"] = $this->getCurrentPg();
		$arrRes["pg_list"] = $this->getArrPgList($sql);
		$arrRes["prev"] = $this->getPgPrev();
		$arrRes["next"] = $this->getPgNext();

		return $arrRes;
	}

	/*
	 * リスト
	 */
	function getCategoriesNames($arr_id){

		$arrRes = null;
		if(count($arr_id) >0 ){
			foreach($arr_id as $id){
				// SQL処理
				$sql =  "SELECT name FROM page_categories WHERE id = '$id' AND del_flg = 0";
				$array = $this->query($sql);
				if(!empty($arrRes)) $arrRes .= " / ";
				$arrRes .= $array[0]["page_categories"]["name"];
			}
		}
		return $arrRes;
	}

}
?>