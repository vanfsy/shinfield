<?php
class MenuList extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["all_name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");

		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["controller"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["action"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["category_name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["memu_name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'menu_lists');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'menu_lists'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'menu_lists';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'menu_lists'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM menu_lists WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["menu_lists"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM menu_lists WHERE id = '$id' AND del_flg <= 0";
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
		return parent::isSave($data['menu_lists']);
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
		$sql .=  "     FROM menu_lists ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  " ORDER BY rank ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		// ランク再処理
		if(empty($this->sort_value) or $this->sort_value == "rank"){
		$array = $this->reSort($array,$pgnum,$disp_num);
		}

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key] = $row["menu_lists"];

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
		return parent::getAllEntity('menu_lists');
	}

	/*
	 * リスト
	 */
	function getMenuEntityByRoleType($role_type){

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM menu_lists ";
		if($role_type != "dev"){
			$sql .=  " INNER JOIN role_details ";
			$sql .=  "         ON menu_lists.id = role_details.menu_id ";
			$sql .=  " INNER JOIN roles ";
			$sql .=  "         ON role_details.role_id = roles.id ";
		}
		$sql .=  "      WHERE menu_lists.del_flg <= 0 ";
		if($role_type != "dev"){
			$sql .=  "        AND roles.role_type = '$role_type' ";
		}
		$sql .=  "        ORDER BY menu_lists.rank ";
		$array = $this->query($sql);

		$arr_id[1] = 1;
		$arr_id[2] = 2;
		if(count($array) > 0){
			foreach($array as $row){
				if($row["menu_lists"]["id"] == 1){
					unset($arr_id[1]);
				}
				if($row["menu_lists"]["id"] == 2){
					unset($arr_id[2]);
				}
			}
		}

		if(count($arr_id) > 0){
			$in_id = null;
			foreach($arr_id as $id){
				if(!empty($in_id)) $in_id .= ",";
				$in_id .= "'".$id."'";
			}
			$sql = "SELECT * FROM menu_lists WHERE id IN (".$in_id.")";
			$array_add = $this->query($sql);
			$array = array_merge($array_add,$array);
		}

		$arrRes = array();
		$max = count($array);
		$j = 0;
		for($i = 0;$i < $max; $i++){
			$controller          = $array[$i]["menu_lists"]["controller"];
			$action              = $array[$i]["menu_lists"]["action"];
			$category_name       = $array[$i]["menu_lists"]["category_name"];
			$next_category_name  = @$array[($i+1)]["menu_lists"]["category_name"];
			$memu_name           = $array[$i]["menu_lists"]["memu_name"];
			$arrRes[$category_name][$j]["controller"]    = $controller;
			$arrRes[$category_name][$j]["action"]        = $action;
			$arrRes[$category_name][$j]["category_name"] = $category_name;
			$arrRes[$category_name][$j]["memu_name"]     = $memu_name;
			if($category_name == $next_category_name){
				++$j;
			}else{
				$j = 0;
			}
		}
		return $arrRes;
	}

	/*
	 * 権限範囲の判定
	 */
	function isAuthMenu($controller,$action,$role_type){
//echo $controller."/".$action;
		if($controller == "app" && $action == "top"){
			return true;
		}

		if($controller == "member" && $action == "login"){
			return true;
		}

		if($controller == "member" && $action == "logout"){
			return true;
		}

		if($role_type == "dev"){
			return true;
		}

		// SQL処理
		// メニューに入っていないものは権限チェックせず
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM menu_lists ";
		$sql .=  "      WHERE controller = '$controller' ";
		$sql .=  "        AND action = '$action' ";
		$sql .=  "        AND del_flg <= 0 ";
		$array = $this->query($sql);
		if(count($array) === 0){
			return true;
		}

		$flg = false;

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM roles ";
		$sql .=  " INNER JOIN role_details ";
		$sql .=  "         ON roles.id = role_details.role_id";
		$sql .=  "        AND role_details.del_flg <= 0";
		$sql .=  " INNER JOIN menu_lists ";
		$sql .=  "         ON role_details.menu_id = menu_lists.id";
		$sql .=  "        AND menu_lists.del_flg <= 0";
		$sql .=  "      WHERE menu_lists.controller = '$controller' ";
		$sql .=  "        AND menu_lists.action = '$action' ";
		$sql .=  "        AND roles.role_type = '$role_type' ";
		$array = $this->query($sql);

		if(count($array) > 0) $flg = true;

		return $flg;
	}

}
?>