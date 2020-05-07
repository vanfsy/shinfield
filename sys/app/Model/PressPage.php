<?php
class PressPage extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["start_date"]["notEmpty"]   = array("rule" => "date", "allowEmpty" => true, "message" => "0000/00/00の形式で正しい日付を入力してください。");
		$validate["end_date"]["notEmpty"]     = array("rule" => "date", "allowEmpty" => true, "message" => "0000/00/00の形式で正しい日付を入力してください。");
		$validate["url"]["url"]               = array("rule" => array('url',true), "allowEmpty" => true, "message" => "httpから始まる正しいURLを入力してください。");

		$validate["subject"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["headline"]["notEmpty"] = array("rule" => "notEmpty", "message" => "必須項目です。");
//		$validate["body"]["notEmpty"]     = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	function isError($data){
		return parent::isError($data,'press_pages');
	}
	 * */

	/*
	 * フィールド
	function getFields($table = 'press_pages'){
		return parent::getFields($table);
	}
	 */

	/*
	 * オプションリスト
	function getOptions(){
		$table = 'press_pages';
		return parent::getOptions($table);
	}
	 */

	/*
	 * オプションリスト
	function getTreeOptions($table = 'press_pages'){

		return parent::getTreeOptions($table);
	}
	 */

	/*
	 * リスト
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM press_pages WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["press_pages"][$field];
		return $res;
	}
	 */

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM press_pages WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$arrRes = $array[0];

		$start_date = $arrRes["press_pages"]["start_date"];
		$end_date   = $arrRes["press_pages"]["end_date"];
		if($start_date == "1900-01-01"){
			$arrRes["press_pages"]["start_date"] = "";
		}else{
			$arrRes["press_pages"]["start_date"] = substr(str_replace("-","/",$start_date),0,10);
		}
		if($end_date == "1900-01-01"){
			$arrRes["press_pages"]["end_date"] = "";
		}else{
			$arrRes["press_pages"]["end_date"] = substr(str_replace("-","/",$end_date),0,10);
		}

		return $arrRes;
	}

	/*
	 * リスト
	 */
	function getEntityByPressId($press_id){

		// SQL処理
		$sql =  "SELECT * FROM press_pages WHERE press_id = '$press_id' AND del_flg <= 0 ORDER BY rank";
		$array = $this->query($sql);

		return $array;
	}

	/*
	 * 登録更新処理
	function isSave($data){

		App::import('Component', 'Utility');
		$Utility = new UtilityComponent();

		$start_date = $Utility->convDbDate($data["press_pages"]["start_date"]);
		$end_date   = $Utility->convDbDate($data["press_pages"]["end_date"]);
		if($start_date == "1900-01-01" && !empty($end_date)){
			$start_date = date("Y-m-d",time());
		}
		$data["press_pages"]["start_date"] = $start_date;
		$data["press_pages"]["end_date"] = $end_date;

		if(empty($data["press_pages"]["url"])) $data["press_pages"]["url"] = "";

		return parent::isSave($data['press_pages'],false);
	}
	 */

	/*
	 * ページング
	 */
	function getPagingEntity($disp_num,$pgnum,$press_id){

		// ページング用パラメータ設定
		$this->setDispNum($disp_num);
		$this->setPgNum($pgnum);

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM press_pages ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND press_id = '$press_id' ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key] = $row["press_pages"];

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
	function getAllEntity(){
		return parent::getAllEntity('press_pages');
	}
	 */

	/*
	 * バナー
	 */
	function getBannerEntity(){

		// SQL処理
		$press_id = 1;
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM press_pages ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND subject = 'banner' ";

		// ページング用SQL文字列
		$array = $this->query($sql);
		$arrRes["list"] = array();
		foreach($array as $key => $row){
			$arrRes["list"][$key] = $row["press_pages"];
		}

		return $arrRes;
	}

}
