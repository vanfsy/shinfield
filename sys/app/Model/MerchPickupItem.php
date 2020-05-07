<?php
class MerchPickupItem extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のカテゴリがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$validate["count"]["numeric"]  = array("rule" => "numeric", "message" => "このフィールドは数字です。");
		$validate["count"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'merch_pickup_items');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'merch_pickup_items'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'merch_pickup_items';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'merch_pickup_items'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'merch_pickup_items';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM merch_pickup_items WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		if(count($array) > 0){
			return $array[0];
		}else{
			return $array[0]["merch_pickup_items"]["id"] = 1;
		}
	}

	/*
	 * リスト
	 */
	function getListEntityWhere($where = null){
		$table = 'merch_pickup_items';
		return parent::getListEntityWhere($table, $where);
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){

		$data = $data["merch_pickup_items"];
		$boolean = false;
		if (empty($data)) return $boolean;

		// エラーチェック
		$this->isValid();
		$this->set($data);
		if(!$this->validates($data)){
			return $boolean;
		}

		// SQL処理
		$merch_pickup_id = $data["merch_pickup_id"];
		$rank            = $data["rank"];
		$sql =  "SELECT * FROM merch_pickup_items WHERE merch_pickup_id = '$merch_pickup_id' AND rank = '$rank' AND del_flg <= 0";
		$array = $this->query($sql);
		$data["id"] = null;
		if(count($array) > 0) $data["id"] = $array[0]["merch_pickup_items"]["id"];

		// 登録処理
		if($data["id"] > 0 ){
			return $this->save($data, true, array('merch_pickup_id','target_table','target_id','merch_basic_id','rank','modified'));
		}else{
			return $this->save($data, true, array('merch_pickup_id','target_table','target_id','merch_basic_id','rank','modified','created'));
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
		$sql .=  "     FROM merch_pickup_items ";
		$sql .=  "    WHERE del_flg <= 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]            = $row["merch_pickup_items"];

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
		return parent::getAllEntity('merch_pickup_items');
	}

	/*
	 * リスト
	 */
	function getOneEntityByPickupId($merch_pickup_id){

		// SQL処理
		$sql =  "SELECT * FROM merch_pickups WHERE id = '$merch_pickup_id' AND del_flg <= 0";
		$array = $this->query($sql);
		$count = $array[0]["merch_pickups"]["count"];

		$sql =  "SELECT * FROM merch_pickup_items WHERE merch_pickup_id = '$merch_pickup_id' AND del_flg <= 0";
		$array = $this->query($sql);

		$arrRes = array();
		$rank = 1;
		for($i = 0; $i < $count; $i++) {
			$flg = false;
			foreach($array as $row){
				if($row["merch_pickup_items"]["rank"] == $rank){
					$arrRes[$i] = $row["merch_pickup_items"];
					$merch_basic_id = $arrRes[$i]["merch_basic_id"];

					$sql = "";
					$sql .= "     SELECT * ";
					$sql .= "       FROM merch_basics ";
					$sql .= " INNER JOIN merch_items ";
					$sql .= "         ON merch_basics.id = merch_items.merch_basic_id ";
					$sql .= "      WHERE merch_basics.id = '$merch_basic_id' ";
					$sql .= "        AND merch_basics.del_flg <= 0";
					$array_b = $this->query($sql);

					$arrRes[$i]["code"] = @$array_b[0]["merch_items"]["code"];
					$arrRes[$i]["name"] = @$array_b[0]["merch_basics"]["name"];
					$flg = true;
				}
			}
			if(!$flg){
				$arrRes[$i]["id"] = null;
				$arrRes[$i]["rank"] = $rank;
				$arrRes[$i]["code"] = null;
				$arrRes[$i]["name"] = "未設定";
			}
			++$rank;
		}

		return $arrRes;
	}

	/*
	 * フロント側表示
	 */
	function getEntityByPickupId($merch_pickup_id){

		// SQL処理
		$sql =  "SELECT * FROM merch_pickup_items WHERE merch_pickup_id = '$merch_pickup_id' AND del_flg <= 0 ORDER BY rank";
		$array = $this->query($sql);
		$arrRes = array();
		App::import('Model', 'MerchBasics');
		$MerchBasics = new MerchBasics;
		foreach($array as $row){
				$merch_basic_id = $row["merch_pickup_items"]["merch_basic_id"];

				$sql = "";
				$sql .= "     SELECT * ";
				$sql .= "       FROM merch_basics ";
				$sql .= " INNER JOIN merch_items ";
				$sql .= "         ON merch_basics.id = merch_items.merch_basic_id ";
				$sql .= "      WHERE merch_basics.id = '$merch_basic_id' ";
				$sql .= "        AND merch_basics.del_flg <= 0";
				$array_b = $this->query($sql);
				$res["merch_basics"] = $array_b[0]["merch_basics"];
				$res["merch_items"] = $array_b[0]["merch_items"];

//				$merch_item_id = $array_b[0]["merch_items"]["id"];
//				$list = $MerchBasics->getOneItemSpecsById($merch_item_id);
//				$res["merch_item_specs"] = $list["merch_item_specs"];
//				$res["merch_pickup_items"] = $row["merch_pickup_items"];
				$arrRes[] = $res;
		}

		return $arrRes;
	}

}
?>