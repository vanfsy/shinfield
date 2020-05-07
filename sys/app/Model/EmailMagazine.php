<?php
class EmailMagazine extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["subject"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["body"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'email_magazines');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'email_magazines'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'email_magazines';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'email_magazines'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM email_magazines WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["email_magazines"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM email_magazines WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$arrRes = $array[0];
		if(empty($arrRes["email_magazines"]["sent_date"])){
			$arrRes["email_magazines"]["status"]    = "未送信";
		}else{
			$arrRes["email_magazines"]["status"]    = "送信済み";
		}
		return $arrRes;
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['email_magazines']);
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
		$sql .=  "     FROM email_magazines ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "    ORDER BY save_flg DESC , sent_date DESC ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]              = $row["email_magazines"];
			if(empty($row["email_magazines"]["sent_date"])){
				$arrRes["list"][$key]["status"]    = "未送信";
			}else{
				$arrRes["list"][$key]["status"]    = "送信済み";
			}

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
		return parent::getAllEntity('email_magazines');
	}

}
?>