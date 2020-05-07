<?php
class Role extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	*/

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$validate["role_type"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["role_type"]["isCode"]  = array("rule" => "isCode", "message" => "このフィールドは英数字のみです。");
		$validate["role_type"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$validate["role_rank"]["numeric"]  = array("rule" => "numeric", "message" => "このフィールドは数字のみです。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	* */
	function isError($data){
		return parent::isError($data,'roles');
	}

	/*
	 * フィールド
	*/
	function getFields($table = 'roles'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	*/
	function getOptions(){
		$table = 'roles';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	*/
	function getTreeOptions($table = 'roles'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	*/
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM roles WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["roles"][$field];
		return $res;
	}

	/*
	 * リスト
	*/
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM roles WHERE id = '$id' AND del_flg <= 0";
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
		return parent::isSave($data['roles']);
	}

	/*
	 * ページング
	*/
	function getPagingEntity($disp_num,$pgnum,$role_type){

		// ページング用パラメータ設定
		$this->setDispNum($disp_num);
		$this->setPgNum($pgnum);

		// SQL処理
		$sql =  "SELECT role_rank FROM roles WHERE role_type = '$role_type' AND del_flg <= 0";
		$array = $this->query($sql);
		$max_rank = $array[0]["roles"]["role_rank"];

		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM roles ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND role_rank >= $max_rank ";
		$sql .=  " ORDER BY role_rank ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key] = $row["roles"];

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
		return parent::getAllEntity('roles');
	}

	/*
	 * ロールリスト
	*/
	function getRolesByRoleType($role_type){

		// SQL処理
		$sql =  "SELECT role_rank FROM roles WHERE role_type = '$role_type' AND del_flg <= 0";
		$array = $this->query($sql);
		$max_rank = $array[0]["roles"]["role_rank"];

		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM roles ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND role_rank >= $max_rank ";
		$sql .=  " ORDER BY role_rank desc ";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $key => $row){

			$arrRes[$row["roles"]["role_type"]] = $row["roles"]["name"];

		}

		return $arrRes;
	}

	/*
	 * 権限範囲の判定
	*/
	function isAuthRoleType($role_type,$role_type2){

		$flg = false;

		// SQL処理
		$sql =  "SELECT role_rank FROM roles WHERE role_type = '$role_type' AND del_flg <= 0";
		$array = $this->query($sql);
		$min_rank = $array[0]["roles"]["role_rank"];

		// SQL処理
		$sql =  "SELECT role_rank FROM roles WHERE role_type = '$role_type2' AND del_flg <= 0";
		$array = $this->query($sql);
		$max_rank = $array[0]["roles"]["role_rank"];

		if($max_rank >= $min_rank) $flg = true;

		return $flg;
	}
}
?>