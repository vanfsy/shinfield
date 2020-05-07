<?php
class MerchSpecList extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){
/*TODO 同カテゴリ内で重複を禁止するように修正*/
//		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
//		$validate["value"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のデータがあります。");
		$validate["value"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'merch_spec_lists');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'merch_spec_lists'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'merch_spec_lists';
		$where = null;
		return parent::getOptions($table,$where);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'merch_spec_lists'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'merch_spec_lists';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM merch_spec_lists WHERE id = '$id' AND del_flg = 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['merch_spec_lists']);
	}

	/*
	 * ページング
	 */
	function getPagingEntity($disp_num,$pgnum,$field_name){
		// ページング用パラメータ設定
		$this->setDispNum($disp_num);
		$this->setPgNum($pgnum);

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM merch_spec_lists ";
		$sql .=  "    WHERE del_flg = 0 ";
		$sql .=  "      AND field_name = '$field_name' ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]            = $row["merch_spec_lists"];

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
		return parent::getAllEntity('merch_spec_lists');
	}

	function addFileNames($entity){

		$arrRes = array();
		foreach($entity as $i => $row){
			$arrRes[$i] = $row;
			foreach($this->arrImages as $name => $val){
				if($name == $i){
					$arrRes[$i] = $val;
				}
			}
		}
		return $arrRes;
	}

	/*
	 * リスト
	 */
	function getEntityBySpecId($merch_spec_id){

		// SQL処理
		$sql =  "SELECT * FROM merch_spec_lists WHERE merch_spec_id = '$merch_spec_id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array;
	}

	/*
	 * リスト
	 */
	function getOneEntityByFldAndVal($field_name,$value){

		// SQL処理
		$sql =  "SELECT * FROM merch_spec_lists WHERE field_name = '$field_name' AND value = '$value' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array;
	}

}
?>