<?php
class PageContentParameter extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'page_content_parameters');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_content_parameters'){

		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'page_content_parameters';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'page_content_parameters'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'page_content_parameters';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getListEntityById($id){

		// SQL処理
		$table = "page_content_parameters";
		$where = " page_content_id = '$id'";
		return parent::getListEntityWhere($table, $where);
	}

/*
	function getArrCechckedLists($id){

		// SQL処理
		$table = "page_content_parameters";
		$where = " page_content_id = '$id'";
		$fieldname = "page_block_id";
		return parent::getArrCechckedLists($table, $fieldname, $where);
	}
*/

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		$page_content_id = $data["page_content_id"];
		$conditions["page_content_id"] = $page_content_id;
		$this->deleteAll($conditions);

		foreach($data['page_content_parameters'] as $id => $val){
			$data['page_content_parameters']['page_parameter_id'] = $id;
			$data['page_content_parameters']['page_content_id'] = $page_content_id;
			$data['page_content_parameters']['value'] = $val;
			$data['page_content_parameters']['id'] = "";
			parent::isSave($data['page_content_parameters']);
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
		$sql .=  "     FROM page_content_parameters ";
		$sql .=  "    WHERE del_flg = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

//			$region_id = $row["page_content_parameters"]["region_id"];
//
//			$sql =  "";
//			$sql .=  "     SELECT * ";
//			$sql .=  "       FROM page_regions r ";
//			$sql .=  " INNER JOIN page_layouts l ";
//			$sql .=  "         ON r.layout_id = l.id ";
//			$sql .=  "      WHERE r.del_flg = 0 ";
//			$sql .=  "        AND l.main_region_id <> r.id ";
//			$sql .=  "        AND r.id = '$region_id' ";
//
//			$arrCat = $this->query($sql);
//			$arrRes["list"][$key]["layout_region_name"]   = "レイアウト・領域なし";
//			if(count($arrCat) > 0){
//				$arrRes["list"][$key]["layout_region_name"]   = $arrCat[0]["l"]["name"]." > ".$arrCat[0]["r"]["name"];
//			}

			$arrRes["list"][$key]["id"]            = $row["page_content_parameters"]["id"];
			$arrRes["list"][$key]["name"]          = $row["page_content_parameters"]["name"];

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
		return parent::getAllEntity('page_content_parameters');
	}

	/*
	 * リスト
	 */
	function getParametersNamesById($content_id){

		$arrRes = null;
		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM page_content_parameters ";
		$sql .=  " INNER JOIN page_parameters ";
		$sql .=  "         ON page_content_parameters.page_parameter_id = page_parameters.id ";
		$sql .=  "      WHERE page_parameters.del_flg <= 0 ";
		$sql .=  "        AND page_content_parameters.del_flg <= 0 ";
		$sql .=  "        AND page_content_parameters.page_content_id = '$content_id' ";
		$array = $this->query($sql);
		foreach($array as $key => $val){
			if(!empty($arrRes)) $arrRes .= " / ";
			$arrRes .= $val["page_parameters"]["name"]."：".$val["page_content_parameters"]["value"];
		}
		return $arrRes;
	}

	/*
	 * コンテンツID毎のページパラメータリストを返す
	 */
	function getParametersById($content_id){

		$arrRes = null;
		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM page_content_parameters ";
		$sql .=  " INNER JOIN page_parameters ";
		$sql .=  "         ON page_content_parameters.page_parameter_id = page_parameters.id ";
		$sql .=  "      WHERE page_parameters.del_flg <= 0 ";
		$sql .=  "        AND page_content_parameters.del_flg <= 0 ";
		$sql .=  "        AND page_content_parameters.page_content_id = '$content_id' ";
		$array = $this->query($sql);
		foreach($array as $key => $val){
			$arrRes[$val["page_parameters"]["code"]] = $val["page_content_parameters"]["value"];
		}
		return $arrRes;
	}

	/*
	 * リスト
	 */
	function getFormParametersNames($data){

		$arrRes = null;
		foreach($data["page_content_parameters"] as $key => $val){
			$sql =  "SELECT * FROM page_parameters WHERE del_flg = 0 AND id = '$key' ";
			$array = $this->query($sql);
			if(!empty($arrRes)) $arrRes .= " / ";
			$arrRes .= $array[0]["page_parameters"]["name"]."：".$val;
		}
		return $arrRes;
	}

}
?>