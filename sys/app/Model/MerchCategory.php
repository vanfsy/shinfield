<?php
class MerchCategory extends AppModel {
	var $validate = array();

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["notEmpty"]        = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
//	function isError($data){
		//return parent::isError($data,'merch_categories');
//	}

	/*
	 * フィールド
	function getFields($table = 'merch_categories'){
		return parent::getFields($table);
	}
	 */

	/*
	 * オプションリスト
	function getOptions(){
		$table = 'merch_categories';
		$where = null;
		return parent::getOptions($table,$where);
	}
	 */

	/*
	 * オプションリスト
	 */
	function getTreeOptions($level = 0,$id = 0){

		$where = null;
		$table = 'merch_categories';
		if($id > 0){
			$where = "id <> '$id'";
			return parent::getTreeOptions($table, $where);

		}else if($id == 0 && $level == 0){

			$sql =  "SELECT max(level) as max_level FROM merch_categories WHERE del_flg <= 0";
			$array = $this->query($sql);
			$max_level = $array[0][0]["max_level"];

			$sql =  "SELECT * FROM merch_categories WHERE del_flg <= 0 ORDER BY level,parent_id,rank ";
			$array = $this->query($sql);

			$arrCats = array();
			foreach($array as $row){
				$list["id"]        = $row["merch_categories"]["id"];
				$list["name"]      = $row["merch_categories"]["name"];
				$list["parent_id"] = $row["merch_categories"]["parent_id"];
				$list["level"]     = $row["merch_categories"]["level"];
				$arrCats[] = $list;
			}

			$arrBase = array();
			for($i = 1;$i < $max_level;++$i){
				foreach($arrCats as $row){
					if($row["level"] == $i){
						$arrBase[] = $row;
						foreach($arrCats as $val){
							if($row["id"] == $val["parent_id"]){
								$val["name"] = $row["name"].">".$val["name"];
								$arrBase[] = $val;
							}
						}
					}
				}
			}

			$arrRes = array();
			foreach($arrBase as $row){
				$id    = $row["id"];
				$name  = $row["name"];
				$arrRes[$id] = $name;
			}

			return $arrRes;

		}else if($level == 1){

			$sql =  "SELECT * FROM merch_categories WHERE del_flg <= 0 AND level = '$level' ORDER BY parent_id , rank";
			$array = $this->query($sql);

			$arrRes = array();
			foreach($array as $row){
				$id        = $row["merch_categories"]["id"];
				$arrRes[$id] = $row["merch_categories"]["name"];
			}

			return $arrRes;

		}else if($level > 1){

			$sql =  "SELECT max(level) as max_level FROM merch_categories WHERE del_flg <= 0";
			$array = $this->query($sql);
			$max_level = $array[0][0]["max_level"];

			$sql =  "SELECT * FROM merch_categories WHERE del_flg <= 0 AND level = '$max_level' ORDER BY parent_id , rank";
			$array = $this->query($sql);

			if(count($array) == 0) return array("カテゴリ未設定");

			$prev_parent_id = $array[0]["merch_categories"]["parent_id"];

			$sql_p =  "SELECT * FROM merch_categories WHERE del_flg <= 0 AND id = '$prev_parent_id' ";
			$array_p = $this->query($sql_p);
			if(count($array_p) == 0) return array("カテゴリ未設定");
			$parent_name = $array_p[0]["merch_categories"]["name"];

			$arrRes = array();
			$arrRes[] = "-- 商品カテゴリ --";
			$i = 0;
			foreach($array as $row){
				$id        = $row["merch_categories"]["id"];
				$parent_id = $row["merch_categories"]["parent_id"];
				if($prev_parent_id <> $parent_id){
					$sql_p =  "SELECT * FROM merch_categories WHERE del_flg <= 0 AND id = '$parent_id' ";
					$array_p = $this->query($sql_p);
					$parent_name = @$array_p[0]["merch_categories"]["name"];
					$i = 0;
				}
				$arrRes[$parent_name][$id] = $parent_name." &gt; ".$row["merch_categories"]["name"];
				$prev_parent_id = $row["merch_categories"]["parent_id"];
				++$i;
			}

			return $arrRes;

		}
	}

	/*
	function getOneEntityByParam($param,$field){
		return parent::getOneEntityByParam($param,$field);
	}
	*/

	/*
	 * リスト
	function getOneFieldById($id,$field){

		$table = 'merch_categories';
		return parent::getOneFieldById($table,$id,$field);
	}
	 */

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM merch_categories WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	function isSave($data){
		return parent::isSave($data['merch_categories']);
	}
	 */

	/*
	 * ページング
	 */
	function getPagingEntity($disp_num,$pgnum,$parent_id){

		// ページング用パラメータ設定
		$this->setDispNum($disp_num);
		$this->setPgNum($pgnum);

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM merch_categories ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND parent_id = '$parent_id' ";
		$sql .=  "    ORDER BY level , parent_id ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]           = $row["merch_categories"];
			$id = $row["merch_categories"]["id"];
			$arrRes["list"][$key]["id"]              = $id;
			$arrRes["list"][$key]["level"]           = $row["merch_categories"]["level"];
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

		$sql_top =  "SELECT * FROM merch_categories WHERE del_flg = 0 AND id = '$id'";
		$array_top = $this->query($sql_top);

		$name = "親カテゴリなし";
		$i    = 0;
		if(count($array_top)>0){
			$name = $array_top[0]["merch_categories"]["name"];
			$i    = $array_top[0]["merch_categories"]["parent_id"];
		}

		while( $i >= 1 ){
			$sql       =  "SELECT * FROM merch_categories WHERE del_flg = 0 AND id = '$i'";
			$array     = $this->query($sql);
			$name_next = $array[0]["merch_categories"]["name"];
			$i         = $array[0]["merch_categories"]["parent_id"];
			$name = $name_next." > ".$name;
		}

		return $name;
	}

	/*
	 * パンくずリスト
	 */
	function getTopicPath($id){

		$top = "<a href='".HOME_URL."admin/merch/category/list/1/'>TOP階層</a>";
		$name = null;
		while( $id > 0 ){

			$sql =  "SELECT * FROM merch_categories WHERE del_flg = 0 AND id = '$id'";
			$array = $this->query($sql);
			$name_next = $array[0]["merch_categories"]["name"];
			$t_id      = $array[0]["merch_categories"]["id"];
			$id        = $array[0]["merch_categories"]["parent_id"];
			if(empty($name)){
				$name = $name_next;
			}else{
				$name = "<a href='".HOME_URL."admin/merch/category/list/1/$t_id'>".$name_next."</a> > ".$name;
			}
		}

		if(!empty($name)){
			$name = $top." > ".$name;
		}else{
			$name = "TOP階層";
		}
		return $name;

	}

	/*
	 * ヘッダ画像取得
	 */
	function getFieldByCategoryId($category_id,$field_name){

		// SQL処理
		$sql =  "SELECT * FROM merch_categories WHERE id = '$category_id' AND del_flg <= 0";
		$array = $this->query($sql);
		$field = null;
		if(count($array)>0){
			$field = $array[0]["merch_categories"][$field_name];
			$parent_id = $array[0]["merch_categories"]["parent_id"];
			$level = $array[0]["merch_categories"]["level"];

			while($level > 1){
				if(!empty($field)){
					break;
				}
				$sql =  "SELECT * FROM merch_categories WHERE id = '$parent_id' AND del_flg <= 0";
				$array = $this->query($sql);
				if(count($array) > 0){
					$field     = $array[0]["merch_categories"][$field_name];
					$parent_id = $array[0]["merch_categories"]["parent_id"];
					$level     = $array[0]["merch_categories"]["level"];
				}else{
					break;
				}
			}
		}

		return $field;
	}

	/*
	 * ヘッダ画像削除
	 */
	function isDeleImageItem($id,$field_name){

		$data["merch_categories"]["id"] = $id;
		$data["merch_categories"][$field_name] = "null";

		return $this->isSave($data);
	}

	/*
	 * フロント側 パンくずリスト
	 */
	function getTopicPathArray($id,$item_name = null){
		// SQL処理
		$sql =  "SELECT * FROM merch_categories WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		if(count($array) == 0) return null;

		$name = $array[0]["merch_categories"]["name"];
		$parent_id = $array[0]["merch_categories"]["parent_id"];
		$dir = "/shop";

		$home_url = substr(HOME_URL, 0, (strlen(HOME_URL)-1));
		$topic_path[0]["name"] = $name;
		$topic_path[0]["url"]  = $home_url.$dir."/item_category/category_".$id;
		$topic_path[0]["name"] = $name;
		if(empty($item_name)){
			$topic_path[0]["url"]  = null;
			$i = 1;
		}else{
			$topic_path[0]["name"]  = $item_name;
			$topic_path[0]["url"]   = null;
			$topic_path[1]["name"]  = $name;
			$topic_path[1]["url"]   = $home_url.$dir."/list/category_".$id;
			$i = 2;
		}

		$id = $parent_id;
		while( $id > 0 ){

			// SQL処理
			$sql =  "SELECT * FROM merch_categories WHERE id = '$id' AND del_flg <= 0";
			$array = $this->query($sql);
			$name = $array[0]["merch_categories"]["name"];
			$parent_id = $array[0]["merch_categories"]["parent_id"];
			$topic_path[$i]["name"] = $name;
			$topic_path[$i]["url"]  = $home_url.$dir."/item_category/category_".$id;
			$id = $parent_id;
			++$i;
		}

		// TOP追加
		$sql =  "SELECT * FROM page_contents WHERE slug = 'top' AND del_flg <= 0";
		$array = $this->query($sql);

		$top_path["name"] = $array[0]["page_contents"]["name"];
		$top_path["url"] = HOME_URL;
		$topic_path[] = $top_path;

		$max = count($topic_path);
		$cnt = $max - 1;
		$arrRes = array();
		for($i=0;$i<$max;++$i){
			$arrRes[$i] = $topic_path[$cnt];
			--$cnt;
		}

		return $arrRes;
	}

	/*
	 * 有効全件リスト取得
	function getAllEntity(){
		return parent::getAllEntity('merch_categories');
	}
	 */

	/*
	 * ブランドリスト取得
	function getEntityByBrand(){
		return parent::getAllEntity('merch_categories');
	}
	*/

}
