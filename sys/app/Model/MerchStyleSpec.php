<?php
class MerchStyleSpec extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のカテゴリがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'merch_style_specs');
	}

	/*
	 * フィールド 商品項目は別処理
	 */
	function getFields($merch_style_id){
		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT merch_specs.id";
		$sql .=  "          , merch_specs.field_name";
		$sql .=  "          , merch_specs.field_type";
		$sql .=  "          , merch_specs.name";
		$sql .=  "       FROM merch_style_specs ";
		$sql .=  " INNER JOIN merch_specs ";
		$sql .=  "         ON merch_style_specs.merch_spec_id = merch_specs.id";
		$sql .=  "      WHERE merch_style_specs.merch_style_id = '$merch_style_id'";
		$sql .=  "        AND merch_style_specs.del_flg = 0";
		$sql .=  "   ORDER BY merch_specs.rank";
		$array = $this->query($sql);
		$arrRes = $array;
		foreach($array as $key => $val){
			$row = $val["merch_specs"];
			$id = $row["id"];
			$sql =  "SELECT * FROM merch_spec_lists WHERE merch_spec_id = '$id' AND del_flg = 0";
			$arrList = $this->query($sql);
			if(count($arrList) > 0){
				$resList = array();
				foreach($arrList as $key1 => $val2){
					$id   = $val2["merch_spec_lists"]["id"];
					$name = $val2["merch_spec_lists"]["name"];
					$resList[$id] = $name;
				}
				$arrRes[$key]["merch_specs"]["options"] = $resList;
			}
		}
		return $arrRes;
	}

	/*
	 * フィールド フォーム値をフィールド値に組み込む
	 */
	function getFormFields($merch_style_id,$data){
		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT merch_specs.id";
		$sql .=  "          , merch_specs.field_name";
		$sql .=  "          , merch_specs.field_type";
		$sql .=  "          , merch_specs.name";
		$sql .=  "       FROM merch_style_specs ";
		$sql .=  " INNER JOIN merch_specs ";
		$sql .=  "         ON merch_style_specs.merch_spec_id = merch_specs.id";
		$sql .=  "      WHERE merch_style_specs.merch_style_id = '$merch_style_id'";
		$sql .=  "        AND merch_style_specs.del_flg = 0";
		$sql .=  "   ORDER BY merch_specs.rank";
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $key => $val){
			$row = $val["merch_specs"];
			$field_id   = $row["field_name"];
			$arrRes[$key]["name"]         = $row["name"];
			$arrRes[$key]["field_name"]   = $field_id;
			$arrRes[$key]["field_value"]  = "";
			$merch_spec_id = $val["merch_specs"]["id"];
			$type = $val["merch_specs"]["field_type"];
			$arrRes[$key]["field_type"] = $type;
			foreach($data as $id => $value){
				if($id == $field_id){
					$arrRes[$key]["field_value"] = $value;
					$sql =  "SELECT * FROM merch_spec_lists WHERE value = '$value' AND merch_spec_id = '$merch_spec_id' AND del_flg = 0";
					$arrList = $this->query($sql);

					if(count($arrList) > 0){
						$name = $arrList[0]["merch_spec_lists"]["name"];
						$arrRes[$key]["field_value"] = $name;
					}

					if($type == "checkbox" && $value == "1"){
						$arrRes[$key]["field_value"] = "有効";
					}

					if($type == "file" && $value != ""){
						$length = 200;
						$image = WWW_ROOT.'images/items/'.$value;
						$width = "";$high = "";
						if(file_exists($image)){
							$size=getImageSize($image);
							//縦幅より横幅が大きければ横幅を固定、縦幅が大きければ縦幅を固定
							if($size[0] >= $size[1]){$width = $length; $high = $size[1] * $length / $size[0];}
							else{$width = $size[0] * $length / $size[1]; $high = $length;}
						}
						$arrRes[$key]["image_url"] = HOME_URL.'images/items/'.$value;
						$arrRes[$key]["width"] = $width;
						$arrRes[$key]["height"] = $high;
					}

				}

			}
		}
		return $arrRes;
	}

	/*
	 * エントリ用 フィールド フォーム値をフィールド値に組み込む
	 */
	function getEntryFormFields($merch_style_id,$data){
		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT merch_specs.id";
		$sql .=  "          , merch_specs.field_name";
		$sql .=  "          , merch_specs.field_type";
		$sql .=  "          , merch_specs.name";
		$sql .=  "       FROM merch_style_specs ";
		$sql .=  " INNER JOIN merch_specs ";
		$sql .=  "         ON merch_style_specs.merch_spec_id = merch_specs.id";
		$sql .=  "        AND merch_specs.del_flg <= 0";
		$sql .=  "      WHERE merch_style_specs.merch_style_id = '$merch_style_id'";
		$sql .=  "        AND merch_style_specs.del_flg <= 0";
		$sql .=  "   ORDER BY merch_specs.rank";
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $key => $val){
			$row = $val["merch_specs"];

			$arrRes[$key]["merch_specs"]["name"]         = $row["name"];
			$arrRes[$key]["merch_specs"]["field_name"]   = $row["field_name"];
			$arrRes[$key]["merch_specs"]["field_type"]   = $row["field_type"];
			$arrRes[$key]["merch_specs"]["field_value"]  = "";
			$arrRes[$key]["merch_specs"]["image_url"]  = "";

			$field_id   = $row["field_name"];
			foreach($data as $id => $value){
				if($id == $field_id){
					$arrRes[$key]["merch_specs"]["field_value"] = $value;
				}
			}

			$id = $row["id"];
			$sql =  "SELECT * FROM merch_spec_lists WHERE merch_spec_id = '$id' AND del_flg = 0";
			$arrList = $this->query($sql);
			if(count($arrList) > 0){
				$resList = array();
				foreach($arrList as $key1 => $val2){
					$id   = $val2["merch_spec_lists"]["value"];
					$name = $val2["merch_spec_lists"]["name"];
					$resList[$id] = $name;
				}
				$arrRes[$key]["merch_specs"]["options"] = $resList;
			}

			// 画像情報設定
			if($arrRes[$key]["merch_specs"]["field_type"] == "file" && $arrRes[$key]["merch_specs"]["field_value"] != ""){
				$length = 200;
				$image = WWW_ROOT.'images/items/'.$arrRes[$key]["merch_specs"]["field_value"];
				$width = "";$high = "";
				if(file_exists($image)){
					$size=getImageSize($image);
					//縦幅より横幅が大きければ横幅を固定、縦幅が大きければ縦幅を固定
					if($size[0] >= $size[1]){$width = $length; $high = $size[1] * $length / $size[0];}
					else{$width = $size[0] * $length / $size[1]; $high = $length;}
				}
				$arrRes[$key]["merch_specs"]["image_url"] = HOME_URL.'images/items/'.$arrRes[$key]["merch_specs"]["field_value"];
				$arrRes[$key]["merch_specs"]["width"] = $width;
				$arrRes[$key]["merch_specs"]["height"] = $high;
			}

		}
		return $arrRes;
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'merch_style_specs';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'merch_style_specs'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'merch_style_specs';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM merch_style_specs WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		$merch_style_id = $data["merch_style_specs"]["merch_style_id"];
		$this->deleteAll("merch_style_id = $merch_style_id", false);
		foreach($data["merch_style_specs"]["id"] as $key => $val){
			$array = array();
			$array["merch_style_specs"]["id"] = null;
			$array["merch_style_specs"]["merch_spec_id"] = $val;
			$array["merch_style_specs"]["merch_style_id"] = $data["merch_style_specs"]["merch_style_id"];
			parent::isSave($array['merch_style_specs']);
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
		$sql .=  "     FROM merch_style_specs ";
		$sql .=  "    WHERE del_flg = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]["id"]            = $row["merch_style_specs"]["id"];
			$arrRes["list"][$key]["name"]          = $row["merch_style_specs"]["name"];

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
		return parent::getAllEntity('merch_style_specs');
	}

	/*
	 * チェックボックス用
	 */
	function getCheckedValues($arr_id){

		$arrRes["options"] = array();
		foreach($arr_id["id"] as $key => $row){
			// SQL処理
			$id = $row;
			$sql =  "SELECT * FROM merch_specs WHERE id = '$id' AND del_flg = 0";
			$array = $this->query($sql);
			$arrRes["options"][$key]["id"]   = $array[0]["merch_specs"]["id"];
			$arrRes["options"][$key]["name"] = $array[0]["merch_specs"]["name"];

		}

//		// SQL処理
//		$sql =  "";
//		$sql .=  "    SELECT * ";
//		$sql .=  "      FROM merch_specs ";
//		$sql .=  " LEFT JOIN merch_style_specs ";
//		$sql .=  "        ON merch_specs.id = merch_style_specs.merch_spec_id ";
//		$sql .=  "    WHERE merch_specs.del_flg = 0 ";
//		$sql .=  "      AND merch_specs.id = '' ";
//		$array = $this->query($sql);
//
//		$arrRes["options"] = array();
//		foreach($array as $key => $row){
//
//			$arrRes["options"][$key]["id"]            = $row["merch_specs"]["id"];
//			$arrRes["options"][$key]["name"]          = $row["merch_specs"]["name"];
//
//		}

		return $arrRes;
	}

}
?>