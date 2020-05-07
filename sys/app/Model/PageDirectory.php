<?php
class PageDirectory extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["isDirName"]                    = array("rule" => "isControllerName", "message" => "英数字以外は使用できません。");
		$validate["controller_name"]["isControllerName"]  = array("rule" => "isControllerName", "message" => "英数字以外は使用できません。");
		$validate["name"]["isUnique"]                     = array("rule" => "isUnique", "message" => "既に同名のディレクトリがあります。");
		$validate["name"]["notEmpty"]                     = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["controller_name"]["isUnique"]          = array("rule" => "isUnique", "message" => "既に同名のファンクション名があります。");
		$validate["controller_name"]["notEmpty"]          = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'page_directories');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_directories'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions($table = 'page_directories'){

		$array = parent::getOptions($table);
		$arrRes = array();

		foreach($array as $key => $val){
			$arrRes["/".trim($val)] = "/".trim($val);
		}

		return $arrRes;

	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'page_directories'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM page_directories WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["page_directories"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM page_directories WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		if(count($array) > 0){
			return $array[0];
		}else{
			return null;
		}
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['page_directories']);
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
		$sql .=  "     FROM page_directories ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key] = $row["page_directories"];

		}

		// ページング用
		$arrRes["current_pg"] = $this->getCurrentPg();
		$arrRes["pg_list"] = $this->getArrPgList($sql);
		$arrRes["prev"] = $this->getPgPrev();
		$arrRes["next"] = $this->getPgNext();

		return $arrRes;
	}

	/*
	 * 存在しないコントローラを削除
	 */
	function delController(){

		// SQL処理
		$sql =  "SELECT * FROM page_directories WHERE del_flg <= 0";
		$array = $this->query($sql,false);
		$arrDirectoris = array();
		foreach($array as $val){
			$c_name = $val["page_directories"]["controller_name"];
			$arrDirectoris[$c_name] = "";
		}

		$dir_path = ROOT."/app/controllers/";
		$dir = dir($dir_path);

		// 該当するコントローラーが存在するか確認
		$flg = false;
		$file = $dir->read();
		$arr_controller = array();
		while($file = $dir->read()) {
			$f = explode(".",$file);
			$d_name = str_replace("_controller","",$f[0]);
			if (@$f[1] == "php" && $d_name != "app" && $d_name != "page_app" && $d_name != "publics"){
				if(!isset($arrDirectoris[$d_name])){
					unlink($dir_path.$file);
				}
			}
		}
		$dir->close();

	}

	function isDelete($data){

		if(parent::isDelete($data)){

			// コントローラを削除
			$this->delController();
			return true;

		}else{
			return false;
		}

	}

}
?>