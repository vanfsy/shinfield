<?php
class PageBlockFunction extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["function_name"]["alphaNumeric"]  = array("rule" => "alphaNumeric", "message" => "このフィールドは英数字のみになります。");
		$validate["class_name"]["alphaNumeric"]  = array("rule" => "alphaNumeric", "message" => "このフィールドは英数字のみになります。");
		$validate["function_name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["class_name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'page_block_functions');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_block_functions'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions($table = 'page_content_categories'){
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'page_block_functions'){
		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){
		$table = 'page_block_functions';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM page_block_functions WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['page_block_functions']);
	}

	/*
	 * ページング
	 */
	function getPagingEntity($disp_num,$pgnum,$block_id){

		// ページング用パラメータ設定
		$this->setDispNum($disp_num);
		$this->setPgNum($pgnum);

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM page_block_functions ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND page_block_id = '$block_id' ";
		$sql .=  "      ORDER BY id DESC ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]["id"]            = $row["page_block_functions"]["id"];
			$arrRes["list"][$key]["class_name"]    = $row["page_block_functions"]["class_name"];
			$arrRes["list"][$key]["function_name"] = $row["page_block_functions"]["function_name"];

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
		return parent::getAllEntity('page_block_functions');
	}

	/*
	 * ブロック毎にクラス::メソッドを配列で返す
	 */
/*
	function getArrMethodsBySlug($slug){

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM page_block_functions ";
		$sql .=  " INNER JOIN page_blocks ";
		$sql .=  "         ON page_block_functions.page_block_id = page_blocks.id ";
		$sql .=  "      WHERE page_block_functions.del_flg <= 0 ";
		$sql .=  "        AND page_blocks.del_flg <= 0 ";
		$sql .=  "        AND page_blocks.slug = '$slug' ";
		$array = $this->query($sql);
		return $array;
	}
*/
}
?>