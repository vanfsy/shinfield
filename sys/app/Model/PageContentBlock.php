<?php
class PageContentBlock extends AppModel {
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
		return parent::isError($data,'page_content_blocks');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_content_blocks'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions($layout_id = null){
		$table = 'page_content_blocks';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'page_content_blocks'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'page_content_blocks';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getListEntityById($id){

		// SQL処理
		$table = "page_content_blocks";
		$where = " page_content_id = '$id'";
		return parent::getListEntityWhere($table, $where);
	}

	function getArrCechckedLists($id){

		// SQL処理
		$table = "page_content_blocks";
		$where = " page_content_id = '$id'";
		$fieldname = "page_block_id";
		return parent::getArrCechckedLists($table, $fieldname, $where);
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		$conditions["page_content_id"] = $data["page_content_blocks"]["page_content_id"];
		$this->deleteAll($conditions);
		$data['page_content_blocks']['page_content_id'] = $data["page_content_blocks"]["page_content_id"];
		foreach($data['page_content_blocks']['page_block_id'] as $region_id => $arr_id){
			$data['page_content_blocks']['page_region_id'] = $region_id;
			$arr_id = explode(",",$arr_id);
			foreach($arr_id as $id){
				$data['page_content_blocks']['page_block_id'] = $id;
				$data['page_content_blocks']['id'] = "";
				if($id > 0){
					parent::isSave($data['page_content_blocks']);
				}
			}
		}
		return true;
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
		$sql .=  "     FROM page_content_blocks ";
		$sql .=  "    WHERE del_flg = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$region_id = $row["page_content_blocks"]["region_id"];

			$sql =  "";
			$sql .=  "     SELECT * ";
			$sql .=  "       FROM page_regions r ";
			$sql .=  " INNER JOIN page_layouts l ";
			$sql .=  "         ON r.layout_id = l.id ";
			$sql .=  "      WHERE r.del_flg = 0 ";
			$sql .=  "        AND l.main_region_id <> r.id ";
			$sql .=  "        AND r.id = '$region_id' ";

			$arrCat = $this->query($sql);
			$arrRes["list"][$key]["layout_region_name"]   = "レイアウト・領域なし";
			if(count($arrCat) > 0){
				$arrRes["list"][$key]["layout_region_name"]   = $arrCat[0]["l"]["name"]." > ".$arrCat[0]["r"]["name"];
			}

			$arrRes["list"][$key]["id"]            = $row["page_content_blocks"]["id"];
			$arrRes["list"][$key]["name"]          = $row["page_content_blocks"]["name"];

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
		return parent::getAllEntity('page_content_blocks');
	}

	/*
	 * 配置可能ブロックリスト取得
	 */
	function getDeployableEntityByContentId($id){

		$sql = "SELECT * FROM page_blocks WHERE del_flg <= 0";
		$array = $this->query($sql);
		$arrRes = array();

		$i = 0;
		foreach($array as $key => $val){
			$block_id = $val["page_blocks"]["id"];
			$sql = "SELECT * FROM page_content_blocks WHERE page_block_id = '$block_id' AND page_content_id = '$id' AND del_flg <= 0";
			$array2 = $this->query($sql);

			if(count($array2) == 0){
				$arrRes[$i]["id"]   = $block_id;
				$arrRes[$i]["name"] = $val["page_blocks"]["name"];
				++$i;
			}
		}

		return $arrRes;

	}

	/*
	 * 配置済みブロックリスト取得
	 */
	function getDeployedEntityByContentId($content_id,$layout_id){

		$sql = "SELECT * FROM page_regions WHERE layout_id = '$layout_id'";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $key => $val){

			$region_id = $val["page_regions"]["id"];
			$sql = "SELECT * FROM page_content_blocks WHERE page_content_id = '$content_id' AND page_region_id = '$region_id'";
			$array2 = $this->query($sql);

			$blocks_id = null;
			$i = 0;
			foreach($array2 as $row){

				$block_id = $row["page_content_blocks"]["page_block_id"];
				$sql = "SELECT name,id FROM page_blocks WHERE id = '$block_id'";
				$array3 = $this->query($sql);
				if(count($array3) > 0){
//					$arrRes[$region_id][$i]["page_region_id"]   = $region_id;
					$arrRes[$region_id][$i]["id"]   = $row["page_content_blocks"]["page_block_id"];
					$arrRes[$region_id][$i]["name"] = $array3[0]["page_blocks"]["name"];
					if($blocks_id != "") $blocks_id .= ",";
					$blocks_id .= $array3[0]["page_blocks"]["id"];
					++$i;
				}

			}
			if(!empty($blocks_id)) $arrRes[$region_id]["blocks_id"] = $blocks_id;

		}
/*
		$sql =  "";
		$sql .=  "     SELECT page_blocks.id , page_blocks.name, page_content_blocks.page_region_id";
		$sql .=  "       FROM page_blocks ";
		$sql .=  "  LEFT JOIN page_content_blocks ";
		$sql .=  "         ON page_blocks.id = page_content_blocks.page_block_id ";
		$sql .=  "        AND page_content_blocks.page_content_id = '$id' ";
		$sql .=  "      WHERE page_blocks.del_flg <= 0 ";
		$sql .=  "        AND page_content_blocks.id IS NOT NULL ";
//echo $sql;
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $key => $val){
			$arrRes[$key]["page_region_id"] = $val["page_content_blocks"]["page_region_id"];
			$arrRes[$key]["id"]             = $val["page_blocks"]["id"];
			$arrRes[$key]["name"]           = $val["page_blocks"]["name"];
		}
*/
		return $arrRes;

	}

}
?>