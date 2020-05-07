<?php
class PageBlock extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

//		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のカテゴリがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'page_blocks');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_blocks'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions($layout_id = null){

		$sql =  "";
		$sql .= "     SELECT * ";
		$sql .= "       FROM page_blocks ";
		$sql .= " INNER JOIN page_regions ";
		$sql .= "         ON page_blocks.region_id = page_regions.id ";
		$sql .= "      WHERE page_regions.layout_id = '$layout_id'";
		$sql .= "        AND page_blocks.del_flg = 0 ";
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $row){
			$val = $row["page_blocks"];
			$arrRes[$val["id"]] = " ".$val["name"];
		}

		return $arrRes;
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'page_blocks'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'page_blocks';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM page_blocks WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['page_blocks']);
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
		$sql .=  "     FROM page_blocks ";
		$sql .=  "    WHERE del_flg = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){
			$arrRes["list"][$key] = $row["page_blocks"];
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
		return parent::getAllEntity('page_blocks');
	}

	/*
	 * リスト
	 */
	function getBocksNames($arr_id){

		$arrRes = null;
		foreach($arr_id as $id){
			// SQL処理
			$sql =  "SELECT name FROM page_blocks WHERE id = '$id' AND del_flg = 0";
			$array = $this->query($sql);
			if(!empty($arrRes)) $arrRes .= " / ";
			$arrRes .= $array[0]["page_blocks"]["name"];
		}
		return $arrRes;
	}
}
?>