<?php
class RoleDetail extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'role_details');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'role_details'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'role_details';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'role_details'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM role_details WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["role_details"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM role_details WHERE id = '$id' AND del_flg <= 0";
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

		$conditions["role_id"] = $data["role_details"]["role_id"];
		$this->deleteAll($conditions);

		// 登録処理
		foreach($data["role_detail_id"] as $val){
			$db_data["id"] = "";
			$db_data["role_id"] = $data["role_details"]["role_id"];
			$db_data["menu_id"] = $val;
			$this->save($db_data, true, array('role_id','menu_id','created','modified'));
		}

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
		$sql .=  "     FROM role_details ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key] = $row["role_details"];

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
		return parent::getAllEntity('role_details');
	}

	/*
	 * リスト
	 */
	function getMenuEntityById($id){

		// SQL処理
		$sql =  "";
		$sql .=  "    SELECT * ";
		$sql .=  "      FROM menu_lists ";
		$sql .=  " LEFT JOIN role_details ";
		$sql .=  "        ON menu_lists.id = role_details.menu_id ";
		$sql .=  "       AND role_details.role_id = '$id' ";
		$sql .=  "     WHERE menu_lists.del_flg <= 0 ";
		$sql .=  "     ORDER BY menu_lists.rank ";
		$array = $this->query($sql);
		return $array;
	}

}
?>