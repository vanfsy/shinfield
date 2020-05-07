<?php
class  MerchBasicCategory extends AppModel {
	var $validate = array();

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["merch_category_id"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		if($data["merch_basic_categories"]["merch_category_id"] == 0){
			$data["merch_basic_categories"]["merch_category_id"] = null;
		}
		return parent::isError($data,'merch_basic_categories');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'merch_basic_categories'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'merch_basic_categories';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'merch_basic_categories'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'merch_basic_categories';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM merch_basic_categories WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 商品基本情報IDからリストを取得
	 */
	function getListByMerchBasicId($merch_basic_id){
		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM merch_basic_categories ";
		$sql .=  " INNER JOIN merch_categories ";
		$sql .=  "         ON merch_basic_categories.merch_category_id = merch_categories.id ";
		$sql .=  "    WHERE merch_basic_categories.del_flg <= 0 ";
		$sql .=  "      AND merch_basic_id = '$merch_basic_id'";
		$array = $this->query($sql);
		return $array;
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){

		$conditions["merch_basic_id"] = $data["merch_basic_categories"]["merch_basic_id"];
		$this->deleteAll($conditions);

		return parent::isSave($data['merch_basic_categories']);
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
		$sql .=  "     FROM merch_basic_categories ";
		$sql .=  "    WHERE del_flg = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]["id"]            = $row["merch_basic_categories"]["id"];
			$arrRes["list"][$key]["name"]          = $row["merch_basic_categories"]["name"];

		}

		// ページング用
		$arrRes["current_pg"] = $this->getCurrentPg();
		$arrRes["pg_list"] = $this->getArrPgList($sql);
		$arrRes["prev"] = $this->getPgPrev();
		$arrRes["next"] = $this->getPgNext();

		return $arrRes;
	}

	/*
	 * 商品登録処理
	 */
/*
	function isRegist($data){
		$boolean = false;
		if (empty($data)) return $boolean;

		// エラーチェック
		$this->isValid();
		$this->set($data);
		if(!$this->validates($data)){
			return $boolean;
		}

		// 登録処理
		$db_data["item_id"] = $data["id"];
		$db_data["category_id"] = $data["category_id"];
		$this->save($db_data, true, array('item_id','category_id','created','modified'));

		return true;

	}
*/
	/*
	 * 予約更新処理
	 */
/*
	function isUpdate($data){
		$boolean = false;
		if (empty($data)) return $boolean;

		// エラーチェック
		$this->isValid();
		$this->set($data);
		if(!$this->validates($data)){
			return $boolean;
		}

		// 登録処理
		$item_id = $data["id"];
		$sql = "SELECT * FROM items_categories WHERE item_id = '$item_id'";
		$array = $this->query($sql);
		$id = $array[0]["items_categories"]["id"];

		$db_data["id"] = $id;
		$db_data["category_id"] = $data["category_id"];
		$this->save($db_data, true, array('category_id','modified'));

		return true;

	}
*/
	/*
	 * 管理画面 予約情報
	 */
/*
	function getOneCategoryId($item_id){

		// ユーザ情報
		$sql = "SELECT * FROM items_categories WHERE item_id = '$item_id'";
		$array = $this->query($sql);
		$id = $array[0]["items_categories"]["category_id"];
		return $id;
	}
*/
	/*
	 * 管理画面 予約情報
	 */
/*
	function getOneItemCategoryTree($item_id){

		// ユーザ情報
		$sql = "SELECT * FROM items_categories WHERE item_id = '$item_id'";
		$array = $this->query($sql);
		$id = $array[0]["items_categories"]["category_id"];

		// SQL処理
		$sql =  "SELECT parent_id, name FROM article_categories WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		$arr_1 = $array[0]["article_categories"];

		$sql =  "SELECT * FROM article_categories WHERE id = ".$arr_1["parent_id"];
		$arr_2 = $this->query($sql);
		$name_1 = "";
		if (count($arr_2) > 0){
			$name_1 = $arr_2[0]["article_categories"]["name"]." > ";
		}
		return $name_1.$arr_1["name"];
	}
*/
}
?>