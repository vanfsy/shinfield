<?php
class PageContentCategory extends AppModel {
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
		return parent::isError($data,'page_content_categories');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_content_categories'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions($table = 'page_content_categories'){

		return parent::getOptions($table);
	}

	/*
	 * リスト
	 */
	function getListEntityById($id){

		// SQL処理
		$table = "page_content_categories";
		$where = " page_content_id = '$id'";
		return parent::getListEntityWhere($table, $where);
	}

	function getArrCechckedLists($id){

		// SQL処理
		$table = "page_content_categories";
		$where = " page_content_id = '$id'";
		$fieldname = "page_category_id";
		return parent::getArrCechckedLists($table, $fieldname, $where);
	}


	/*
	 * 登録更新処理
	 */
	function isSave($data){

		$conditions["page_content_id"] = $data["page_content_categories"]["page_content_id"];
		$this->deleteAll($conditions);

		if(isset($data['page_content_categories']['page_category_id'])){
			foreach($data['page_content_categories']['page_category_id'] as $id){
				$data['page_content_categories']['page_category_id'] = $id;
				$data['page_content_categories']['id'] = "";
				parent::isSave($data['page_content_categories']);
			}
		}
		return true;
	}

}
?>