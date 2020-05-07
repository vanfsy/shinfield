<?php
class EmailTemplate extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["code_name"]["isCode"]  = array("rule" => "isCode", "message" => "このフィールドは英数字のみです。");
		$validate["code_name"]["isUnique"]  = array("rule" => "isUnique", "message" => "同名のデータがあります。");
		$validate["admin_mail"]["email"]  = array("rule" => "email", "message" => "不正なメールアドレスです。");

		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["code_name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["body"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["subject"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["admin_mail"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	function isValidSendMail(){

		$validate["from_mail"]["email"]  = array("rule" => "email", "message" => "不正なメールアドレスです。");
		$validate["to_mail"]["email"]    = array("rule" => "email", "message" => "不正なメールアドレスです。");

		$validate["subject"]["notEmpty"]    = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["from_mail"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["to_mail"]["notEmpty"]    = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["body"]["notEmpty"]       = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'email_templates');
	}

	/*
	 * エラーチェック
	 * */
	function isErrorSendMail($data){

		$boolean = false;
		if (empty($data)) return $boolean;

		// エラーチェック
		$this->isValidSendMail();
		$this->set($data);

		foreach( $data as $filed_name => $val ){
			$this->error_messages[$filed_name] = null;
			foreach( $this->invalidFields() as $e_key => $e_val ){
				if($e_key == $filed_name){
					$this->error_messages[$filed_name] = $e_val;
				}
			}
		}

		if(!$this->validates($data)){
			return $boolean;
		}

		// エラーチェック
		$boolean = true;
		if(count($this->invalidFields()) > 0) $boolean = false;
		return $boolean;
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'email_templates'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'email_templates';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'email_templates'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM email_templates WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["email_templates"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM email_templates WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$arrRes = $array[0];
		return $arrRes;
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['email_templates']);
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
		$sql .=  "     FROM email_templates ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){
			$arrRes["list"][$key]              = $row["email_templates"];
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
		return parent::getAllEntity('email_templates');
	}

	/*
	 * リスト
	 */
	function getTemplateByCode($code){

		// SQL処理
		$sql =  "SELECT * FROM email_templates WHERE code_name = '$code' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["email_templates"];
		return $res;
	}

	/*
	 * リスト
	 */
	function getTemplateByType($type){

		// SQL処理
		$sql =  "SELECT * FROM email_templates WHERE type = '$type' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array;
	}

	/*
	 * リスト
	 */
	function getTempOptionsByType($type){

		// SQL処理
		$sql =  "SELECT * FROM email_templates WHERE type = '$type' AND del_flg <= 0";
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $row){
			$arrRes[$row["email_templates"]["code_name"]] = $row["email_templates"]["name"];
		}
		return $arrRes;
	}
}
?>