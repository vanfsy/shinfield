<?php
class PageParameter extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["code"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["code"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'page_parameters');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_parameters'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'page_parameters';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'page_parameters'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM page_parameters WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["page_parameters"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM page_parameters WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['page_parameters']);
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
		$sql .=  "     FROM page_parameters ";
		$sql .=  "    WHERE del_flg = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]["id"]            = $row["page_parameters"]["id"];
			$arrRes["list"][$key]["name"]          = $row["page_parameters"]["name"];
			$arrRes["list"][$key]["code"]          = $row["page_parameters"]["code"];

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
		return parent::getAllEntity('page_parameters');
	}

	/*
	 * フォーム値をフィールドに設定して返す
	 */
	function getFormFields($data){

		$sql = "SELECT * FROM page_parameters WHERE del_flg = 0";
		$array = $this->query($sql);

		$content_id = 0;
		if(isset($data["page_contents"]["id"])) $content_id = $data["page_contents"]["id"];
		$arrRes = array();
		foreach($array as $key => $val){
			$page_parameter_id = $val["page_parameters"]["id"];
			$arrRes["page_parameters"][$key]["id"] = $page_parameter_id;
			$arrRes["page_parameters"][$key]["code"] = $val["page_parameters"]["code"];
			$arrRes["page_parameters"][$key]["name"] = $val["page_parameters"]["name"];
			$arrRes["page_parameters"][$key]["value"] = "";

			$sql = "";
			$sql .= " SELECT * ";
			$sql .= "   FROM page_content_parameters ";
			$sql .= "  WHERE del_flg = 0 ";
			$sql .= "    AND page_parameter_id = '$page_parameter_id'";
			$sql .= "    AND page_content_id = '$content_id'";
			$array = $this->query($sql);
			if(count($array)> 0){
				$arrRes["page_parameters"][$key]["value"] = $array[0]["page_content_parameters"]["value"];
			}
		}

		return $arrRes;

	}
}
?>