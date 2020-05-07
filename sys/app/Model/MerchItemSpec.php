<?php
class MerchItemSpec extends AppModel {
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
	function isError($data){
		return parent::isError($data,'merch_item_specs');
	}

	/*
	 * リスト
	 */
	function getEntityByBasicId($id){

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM merch_item_specs ";
		$sql .=  " INNER JOIN merch_items ";
		$sql .=  "    ON merch_item_specs.merch_item_id = merch_items.id ";
		$sql .=  " INNER JOIN merch_specs ";
		$sql .=  "    ON merch_item_specs.merch_spec_id = merch_specs.id ";
		$sql .=  "    WHERE merch_item_specs.del_flg = 0 ";
		$sql .=  "      AND merch_items.merch_basic_id = '$id'";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $key => $val){
			$field_name = $val["merch_specs"]["field_name"];
			$field_type = $val["merch_specs"]["field_type"];
			if($field_type === "checkbox"){
				$arrDes = explode("\n",$val["merch_item_specs"]["description"]);
				$arrNew = array();
				foreach($arrDes as $row){
					$arrDes2 = explode("=>",$row);
					@$arrNew[$arrDes2[0]] = $arrDes2[1];
				}
				$arrRes[$field_name] = $arrNew;
			}else{
				$arrRes[$field_name] = $val["merch_item_specs"]["description"];
			}
			$arrRes[$field_name."_rank"] = $val["merch_item_specs"]["rank"];
		}

		return $arrRes;
	}

	/*
	 * 商品登録処理
	 */
	function isSave($data){
		$boolean = false;
		if (empty($data)) return $boolean;

		// エラーチェック
		$this->isValid();
		$this->set($data);
		if(!$this->validates($data)){
			return $boolean;
		}

		// 不要ファイルの削除
		$merch_item_id = $data["merch_item_id"];
		$sql =  "";
		$sql .=  "     SELECT merch_item_specs.description ";
		$sql .=  "       FROM merch_item_specs ";
		$sql .=  " INNER JOIN merch_specs ";
		$sql .=  "         ON merch_item_specs.merch_spec_id = merch_specs.id ";
		$sql .=  "      WHERE merch_item_specs.merch_item_id = '$merch_item_id'";
		$sql .=  "        AND merch_specs.field_type = 'file'";
		$array = $this->query($sql);
		foreach($array as $i => $v){
			$file = $v["merch_item_specs"]["description"];
			$save_image = WWW_ROOT.'images/items/'.$file;
//			unlink($save_image);
		}
		$conditions["merch_item_id"] = $data["merch_item_id"];
		$this->deleteAll($conditions);

		// 登録処理
		foreach($data as $key => $val){
			$sql =  "SELECT * FROM merch_specs WHERE field_name = '$key'";
			$arrAs = $this->query($sql);
			if(count($arrAs) > 0){
				$db_data["id"] = "";
				$db_data["merch_item_id"] = $data["merch_item_id"];
				$db_data["merch_spec_id"] = $arrAs[0]["merch_specs"]["id"];
				$db_data["field_name"]    = $key;
				if(is_array($val)){
					$line = null;
					foreach($val as $k => $v){
						if(!empty($line)) $line .= "\n";
						$line .= $k ."=>". $v;
					}
					$db_data["description"] = $line;
				}else{
					$db_data["description"] = $val;
				}
				$db_data["rank"] = @$data[$key."_rank"];
				$this->save($db_data, true, array('merch_item_id','merch_spec_id','field_name','description','rank','created','modified'));
			}
		}

		return true;

	}

/*
	function moveFiles($data){
echo print_r($data);
		foreach($data as $key => $val){
			$sql =  "SELECT * FROM merch_specs WHERE field_name = '$key'";
			$arrAs = $this->query($sql);
			if(count($arrAs) > 0){
				$type = $arrAs[0]["merch_specs"]["field_type"];
				if($type == "file"){
					$temp_image = WWW_ROOT.'images/items/temp/'.$val;
					$save_image = WWW_ROOT.'images/items/'.$val;
					if(file_exists($temp_image)){
						rename($temp_image,$save_image);
					}
				}
			}
		}
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

		$conditions["item_id"] = $data["id"];
		$this->deleteAll($conditions);

		// 登録処理
		foreach($data as $key => $val){
			$sql =  "SELECT * FROM merch_specs WHERE field_name = '$key'";
			$arrAs = $this->query($sql);
			if(count($arrAs) > 0){
				$db_data["id"] = "";
				$db_data["item_id"] = $data["id"];
				$db_data["article_spec_id"] = $arrAs[0]["merch_item_specs"]["id"];
				$db_data["description"] = $val;
				$this->save($db_data, true, array('item_id','article_spec_id','description','created','modified'));
			}
		}

		return true;

	}
*/

//	/*
//	 * 店舗リスト ページング
//	 */
//	function getListItemAdmin($disp_num,$pgnum){
//
//		// ページング処理
//		if($pgnum > 1){
//			$start = ( $pgnum - 1 ) * $disp_num;
//		}else{
//			$start = 0;
//		}
//
//		// SQL処理
//		$sql =  "";
//		$sql .=  "   SELECT * ";
//		$sql .=  "     FROM items ";
//		$sql .=  "    WHERE del_flg = 0 ";
//		$sql .=  " ORDER BY id DESC LIMIT $start ,$disp_num";
//		$array = $this->query($sql);
//
//		$arrRes = array();
//		foreach($array as $row){
//			$item_id            = $array[0]["items"]["id"];
//			$sql =  "SELECT * FROM items_categories WHERE item_id = '$item_id'";
//			$arrCat = $this->query($sql);
//
//			$arrRes["id"]            = $item_id;
//			$arrRes["name"]            = "";
//			$arrRes["category"]        = "";
//
//		}
//
//		return $arrRes;
//	}
//
//	/*
//	 * 管理画面 ページング総数
//	 */
//	function getArrPgList($disp_num){
//		$sql = "SELECT count(*) as count FROM items WHERE del_flg = 0";
//
//		$array = $this->query($sql);
//		$cnt = $array[0][0]["count"];
//		$plus = $cnt % $disp_num;
//		$pgnum = ($cnt - $plus) / $disp_num;
//		if($plus > 0){
//			$pgnum = $pgnum + 1;
//		}
//		$this->pgnum = $pgnum;
//
//		$array = array();
//		for($i=0;$i < $pgnum;$i++){
//			$array[$i] = $i + 1;
//		}
//		return $array;
//	}
//
//	/*
//	 * 管理画面 ページング前項
//	 */
//	function getPgPrev($pgnum){
//		$pg = 0;
//		if($pgnum > 0) $pg = $pgnum - 1;
//		return $pg;
//	}
//
//	/*
//	 * 管理画面 ページング次項
//	 */
//	function getPgNext($pgnum){
//		$pg = 2;
//		if($pgnum > 0) $pg = $pgnum + 1;
//		if($pgnum >= $this->pgnum) $pg = 0;
//		return $pg;
//	}

	/*
	 * 管理画面 予約情報
	 */
	function getOneSpecByIdName($item_id,$field_name){

		$sql =  "";
		$sql .=  "    SELECT * ";
		$sql .=  "      FROM merch_item_specs ";
		$sql .=  "     WHERE merch_item_id = '$item_id'";
		$sql .=  "       AND field_name = '$field_name'";
		$array = $this->query($sql);
		return $array;
	}

	/*
	 * スペック ランク
	 */
	function getOneRankByBasicId($basic_id,$field_name){

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM merch_item_specs ";
		$sql .=  " INNER JOIN merch_items ";
		$sql .=  "    ON merch_item_specs.merch_item_id = merch_items.id ";
		$sql .=  " INNER JOIN merch_specs ";
		$sql .=  "    ON merch_item_specs.merch_spec_id = merch_specs.id ";
		$sql .=  "    WHERE merch_item_specs.del_flg <= 0 ";
		$sql .=  "      AND merch_specs.field_name = '$field_name'";
		$sql .=  "      AND merch_items.merch_basic_id = '$basic_id'";
		$array = $this->query($sql);
		if(count($array) > 0){
			return $array[0]["merch_item_specs"]["rank"];
		}else{
			return 0;
		}

	}

}
?>