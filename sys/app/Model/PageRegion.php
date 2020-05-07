<?php
class PageRegion extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

//		$validate["name"]["isUnique"]      = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["name"]["notEmpty"]      = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["code"]["isCode"]        = array("rule" => "isCode",   "message" => "このフィールドは英数字・アンダーバーのみです。");
		$validate["code"]["isUnique"]      = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["code"]["notEmpty"]      = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'page_regions');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'page_regions'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions($layout_id = '0'){
		$table = 'page_regions';
		$where = null;
		if(!empty($layout_id)) $where = "layout_id = '$layout_id'";
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'page_regions'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM page_regions WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["page_regions"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM page_regions WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['page_regions']);
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
		$sql .=  "     FROM page_regions ";
		$sql .=  "    WHERE del_flg = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$layout_id = $row["page_regions"]["layout_id"];

			$sql =  "SELECT name FROM page_layouts WHERE id = '$layout_id'";
			$arrCat = $this->query($sql);
			$arrRes["list"][$key]["layout_name"]   = "レイアウトなし";
			if(count($arrCat) > 0){
				$arrRes["list"][$key]["layout_name"]   = $arrCat[0]["page_layouts"]["name"];
			}

			$arrRes["list"][$key]["id"]            = $row["page_regions"]["id"];
			$arrRes["list"][$key]["name"]          = $row["page_regions"]["name"];
			$arrRes["list"][$key]["code"]          = $row["page_regions"]["code"];

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
		return parent::getAllEntity('page_regions');
	}

	/*
	 * リスト取得 ByレイアウトID
	 */
	function getEntityByLayoutId($layout_id){
		// SQL処理
		$sql =  "SELECT * FROM page_regions WHERE layout_id = '$layout_id' AND del_flg <= 0 ORDER BY id";
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $key => $val){
			$arrRes["page_regions"][$key] = $val["page_regions"];
		}
		return $arrRes;
	}

	/*
	 * レイアウト新規登録用領域リスト
	 */
	function getNextRegions(){

		$sql =  "SELECT MAX(id) as max_id FROM page_layouts";
		$array = $this->query($sql);
		$lyout_id = $array[0][0]["max_id"] + 1;

		$sql =  "SELECT * FROM page_regions WHERE layout_id = '0' AND del_flg <= 0 ORDER BY id";
		$array = $this->query($sql);
		if(empty($array)){
			$arrRegions["MainContents$lyout_id"] = "メインコンテンツ";
			$arrRegions["Header$lyout_id"] = "ヘッダ";
			$arrRegions["LeftSide$lyout_id"] = "左サイドバー";
			$arrRegions["RightSide$lyout_id"] = "右サイドバー";
			$arrRegions["Footer$lyout_id"] = "フッタ";
			$arrRegions["UpperContents$lyout_id"] = "上部コンテンツ";
			$arrRegions["UnderContents$lyout_id"] = "下部コンテンツ";
			foreach($arrRegions as $code => $name){

				// 登録処理
				$data["id"] = null;
				$data["layout_id"] = 0;
				$data["name"] = $name;
				$data["code"] = $code;
				$this->save($data, true, array("layout_id","name","code","created","modified"));
			}
			$sql =  "SELECT * FROM page_regions WHERE layout_id = '0' AND del_flg <= 0 ORDER BY id";
			$array = $this->query($sql,false);
		}

		return $array;
	}

	/*
	 * レイアウト新規登録用領域リストのレイアウトID更新
	 */
	function saveNewRegionsById($layout_id){
		$sql =  "SELECT * FROM page_regions WHERE layout_id = '0' AND del_flg <= 0";
		$array = $this->query($sql);
		foreach($array as $row){
			$data["id"] = $row["page_regions"]["id"];
			$data["layout_id"] = $layout_id;
			$this->save($data, true, array("layout_id","modified"));
		}

	}

/*** 拡張 **********************/

	/*
	 * ブロック配置用リスト取得 ByレイアウトID
	 */
	function getNoMainEntityByLayoutId($layout_id){

		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM page_regions ";
		$sql .=  " INNER JOIN page_layouts ";
		$sql .=  "         ON page_regions.layout_id = page_layouts.id ";
		$sql .=  "      WHERE page_regions.del_flg <= 0 ";
		$sql .=  "        AND page_layouts.main_region_id <> page_regions.id ";
		$sql .=  "        AND page_regions.layout_id = '$layout_id' ";
		$array = $this->query($sql);

		$arrRes = array();
		foreach($array as $key => $val){
			$arrRes[$key]["id"] = $val["page_regions"]["id"];
			$arrRes[$key]["name"] = $val["page_regions"]["name"];
		}

		return $arrRes;

	}

	/*
	 * オプションリスト
	 */
	function getRegionOptions(){

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM page_regions r ";
		$sql .=  " INNER JOIN page_layouts l ";
		$sql .=  "         ON r.layout_id = l.id ";
		$sql .=  "      WHERE r.del_flg = 0 ";
		$sql .=  "        AND l.main_region_id <> r.id ";
		$sql .=  "   ORDER BY r.rank";
		$array = $this->query($sql);
		$arrRes = array();
		foreach($array as $row){
			$arrRes[$row["r"]["id"]] = $row["l"]["name"].">".$row["r"]["name"];
		}
		return $arrRes;
	}

	/*
	 * リスト
	 */
	function getLayoutRegionById($id){

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM page_regions r ";
		$sql .=  " INNER JOIN page_layouts l ";
		$sql .=  "         ON r.layout_id = l.id ";
		$sql .=  "      WHERE r.del_flg = 0 ";
		$sql .=  "        AND l.main_region_id <> r.id ";
		$sql .=  "        AND r.id = '$id' ";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["l"]["name"]." > ".$array[0]["r"]["name"];
		return $res;
	}

}
?>