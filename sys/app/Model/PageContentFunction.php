<?php
class PageContentFunction extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["function_name"]["alphaNumeric"]  = array("rule" => "alphaNumeric", "message" => "このフィールドは英数字のみになります。");
		$validate["class_name"]["alphaNumeric"]  = array("rule" => "alphaNumeric", "message" => "このフィールドは英数字のみになります。");
		$validate["function_name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["class_name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	function isError($data){
		return parent::isError($data,'page_content_functions');
	}
	 * */

	/*
	 * フィールド
	function getFields($table = 'page_content_functions'){
		return parent::getFields($table);
	}
	 */

	/*
	 * オプションリスト
	function getOptions($table = 'page_content_categories'){
		return parent::getOptions($table);
	}
	 */

	/*
	 * オプションリスト
	function getTreeOptions($table = 'page_content_functions'){
		return parent::getTreeOptions($table);
	}
	 */

	/*
	 * リスト
	function getOneFieldById($id,$field){
		$table = 'page_content_functions';
		return parent::getOneFieldById($table,$id,$field);
	}
	 */

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM page_content_functions WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	function isSave($data){
		return parent::isSave($data['page_content_functions']);
	}
	 */

	/*
	 * ページング
	 */
	function getPagingEntity($disp_num,$pgnum,$content_id){

		// ページング用パラメータ設定
		$this->setDispNum($disp_num);
		$this->setPgNum($pgnum);

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM page_content_functions ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND page_content_id = '$content_id' ";
		$sql .=  "      ORDER BY id DESC ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]["id"]            = $row["page_content_functions"]["id"];
			$arrRes["list"][$key]["class_name"]    = $row["page_content_functions"]["class_name"];
			$arrRes["list"][$key]["function_name"] = $row["page_content_functions"]["function_name"];

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
	function getAllEntity(){
		return parent::getAllEntity('page_content_functions');
	}
	 */

	/*
	 * スラッグ毎にページおよびブロックのクラス::メソッドを配列で返す
	 */
	function getArrMethodsBySlug($slug){

		// SQL処理
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM page_content_functions ";
		$sql .=  " INNER JOIN page_contents ";
		$sql .=  "         ON page_content_functions.page_content_id = page_contents.id ";
		$sql .=  "      WHERE page_content_functions.del_flg <= 0 ";
		$sql .=  "        AND page_contents.del_flg <= 0 ";
		$sql .=  "        AND page_contents.slug = '$slug' ";
		$array = $this->query($sql);
		$arrRes1 = array();
		foreach($array as $key => $row){
			$val = $row["page_content_functions"];
			$arrRes1[] = $val["class_name"]."-".$val["function_name"];
		}

		// ブロック毎の処理
		$sql =  "SELECT * FROM page_contents WHERE del_flg <= 0 AND slug = '$slug' ";
		$array = $this->query($sql);

		if(count($array) == 0) return null;
		$content_id = $array[0]["page_contents"]["id"];
		$sql =  "";
		$sql .=  "     SELECT * ";
		$sql .=  "       FROM page_blocks ";
		$sql .=  " INNER JOIN page_content_blocks ";
		$sql .=  "         ON page_blocks.id = page_content_blocks.page_block_id ";
		$sql .=  "      WHERE page_blocks.del_flg <= 0 ";
		$sql .=  "        AND page_content_blocks.del_flg <= 0 ";
		$sql .=  "        AND page_content_blocks.page_content_id  = '$content_id' ";
		$array2 = $this->query($sql);
		$arrRes2 = array();
		foreach($array2 as $row2){

			$block_id = $row2["page_blocks"]["id"];
			$sql =  "SELECT * FROM page_block_functions WHERE page_block_id = '$block_id' ";
			$array3 = $this->query($sql);
			foreach($array3 as $key3 => $row3){
				$arrRes2[] = $row3["page_block_functions"]["class_name"]."-".$row3["page_block_functions"]["function_name"];
			}
		}

		$arrRes_a = array_merge($arrRes1,$arrRes2);
		$arrayValue = array_count_values($arrRes_a);	//配列の値の数をカウントする

		$arrRes = array();
		$i = 0;
		foreach($arrayValue as $val => $cnt){
			$name = explode("-",$val);
			$arrRes[$i]["class_name"] = $name[0];
			$arrRes[$i]["method_name"] = $name[1];
			++$i;
		}
		return $arrRes;
	}
}
?>