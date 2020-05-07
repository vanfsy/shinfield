<?php
class DataBranche extends AppModel {
	var $validate = array();

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["email"]["email"]          = array("rule" => "email", "allowEmpty" => true, "message" => "正しいメールを入力してください。");
		$validate["code"]["isCode"]          = array("rule" => "isCode", "message" => "CODEは英数字・メールアドレスのみです。");
		$validate["code"]["isUnique"]        = array("rule" => "isUnique", "message" => "そのCODEは既に登録済みです。");
		$validate["code"]["maxLength"]       = array("rule" => array("maxLength",255), "message" => "制限文字数を超えています。");
		$validate["limit_level"]["numeric"]  = array("rule" => "numeric", "message" => "数字のみです。");

		$validate["code"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["parent_id"]["notEmpty"]   = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["name"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["limit_level"]["notEmpty"] = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'data_branches');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'data_branches'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'data_branches';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getCodeOptions(){

		$sql =  "SELECT * FROM data_branches WHERE del_flg <= 0 ORDER BY rank";
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $row){
			$val = $row["data_branches"];
			$arrRes[$val["code"]] = " ".$val["name"];
		}
		return $arrRes;
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'data_branches';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM data_branches WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * リスト
	 */
	function getOneEntityByCode($code){

		// SQL処理
		$sql =  "SELECT * FROM data_branches WHERE code = '$code' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['data_branches']);
	}

	/*
	 * ページング
	 */
	function getPagingEntity($disp_num,$pgnum,$parent_id = 0){

		// ページング用パラメータ設定
		$this->setDispNum($disp_num);
		$this->setPgNum($pgnum);

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM data_branches ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND parent_id = '$parent_id' ";
		$sql .=  "    ORDER BY level , parent_id ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]           = $row["data_branches"];
			$id = $row["data_branches"]["id"];
			$arrRes["list"][$key]["id"]              = $id;
			$arrRes["list"][$key]["level"]           = $row["data_branches"]["level"];
			$arrRes["list"][$key]["name"] = $this->getTreeNameById($id);

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
	function getTreeNameById($id){

		$sql_top =  "SELECT * FROM data_branches WHERE del_flg = 0 AND id = '$id'";
		$array_top = $this->query($sql_top);

		$name = "親カテゴリなし";
		$i    = 0;
		if(count($array_top)>0){
			$name = $array_top[0]["data_branches"]["name"];
			$i    = $array_top[0]["data_branches"]["parent_id"];
		}

		while( $i >= 1 ){
			$sql       =  "SELECT * FROM data_branches WHERE del_flg = 0 AND id = '$i'";
			$array     = $this->query($sql);
			$name_next = $array[0]["data_branches"]["name"];
			$i         = $array[0]["data_branches"]["parent_id"];
			$name = $name_next." > ".$name;
		}

		return $name;
	}

	/*
	 * 有効全件リスト取得
	 */
	function getAllEntity(){
		return parent::getAllEntity('data_branches');
	}

	/*
	 * リスト
	 */
	function isBranchByCode($code){

		// SQL処理
		$sql =  "SELECT * FROM data_leaves WHERE code = '$code' AND del_flg <= 0";
		$array = $this->query($sql);

		$this->data_leave_id = null;
		$this->level = 0;
		$this->parent_name = null;
		$this->parent_code = null;
		$level = 0;
		if(count($array) > 0){
			$code = $array[0]["data_leaves"]["data_branch_code"];
			$id = $array[0]["data_leaves"]["id"];
			$level = $array[0]["data_leaves"]["level"];
			$parent_id = $array[0]["data_leaves"]["parent_id"];
			$this->data_leave_id = $id;
			$this->level = $level;
			$this->parent_name = $array[0]["data_leaves"]["name"];
			$this->parent_code = $array[0]["data_leaves"]["code"];
			$this->topic_path[$level]["code"] = $array[0]["data_leaves"]["code"];
			$this->topic_path[$level]["name"] = $array[0]["data_leaves"]["name"];
		}

		// SQL処理
		$sql =  "SELECT * FROM data_branches WHERE code = '$code' AND del_flg <= 0";
		$array = $this->query($sql);
		$flg = false;
		$this->branch_name = null;
		$this->limit_level = 3;
		$this->data_branch_id = null;
		$this->data_branch_code = $code;
		$this->attach_id = null;
		$this->attach_code = null;
		if(count($array) > 0){
			$data_branch_code = $array[0]["data_branches"]["code"];
			$this->branch_name = $array[0]["data_branches"]["name"];
			$this->limit_level = $array[0]["data_branches"]["limit_level"];
			$this->data_branch_code = $data_branch_code;
			$this->data_branch_id = $array[0]["data_branches"]["id"];
			$this->attach_id = $array[0]["data_branches"]["attach_id"];
			$this->topic_path[0]["code"] = $array[0]["data_branches"]["code"];
			$this->topic_path[0]["name"] = $array[0]["data_branches"]["name"];
			$flg = true;
		}
		if(empty($this->parent_code)){
			$this->parent_code = $this->data_branch_code;
		}
		if(empty($this->data_leave_id)){
			$this->data_leave_id = $this->data_branch_id;
		}
		if(!empty($this->attach_id)){
			$attach_id = $this->attach_id;
			$sql =  "SELECT * FROM data_branches WHERE id = '$attach_id' AND del_flg <= 0";
			$array = $this->query($sql);
			$this->attach_code = $array[0]["data_branches"]["code"];
		}

		// 親データのパスを追加
//echo "level=".$level."<br>";
		if($level > 1){

			for($i=1;$i<$level;++$i){
				// SQL処理
				$sql =  "SELECT * FROM data_leaves WHERE id = '$parent_id' AND del_flg <= 0";
				$array = $this->query($sql);
				$this->topic_path[$i]["code"] = $array[0]["data_leaves"]["code"];
				$this->topic_path[$i]["name"] = $array[0]["data_leaves"]["name"];
				$parent_id = $array[0]["data_leaves"]["parent_id"];
			}
		}
		ksort($this->topic_path);

		return $flg;
	}

	var $branch_name;

	function getBranchName(){
		return $this->branch_name;
	}

	var $limit_level;

	function getLimitLevel(){
		return $this->limit_level;
	}

	var $data_branch_id;

	function getDdataBranchId(){
		return $this->data_branch_id;
	}

	var $data_branch_code;

	function getDdataBranchCode(){
		return $this->data_branch_code;
	}

	var $data_leave_id;

	function getDdataLeaveId(){
		return $this->data_leave_id;
	}

	var $level;

	function getLevel(){
		return $this->level;
	}

	var $parent_name;

	function getParentName(){
		return $this->parent_name;
	}

	var $parent_code;

	function getParentCode(){
		return $this->parent_code;
	}

	var $topic_path;

	function getTopicPath(){
		return $this->topic_path;
	}

	var $attach_code;

	function getAttachCode(){
		return $this->attach_code;
	}

	var $attach_id;

	function getAttachId(){
		return $this->attach_id;
	}


}
?>