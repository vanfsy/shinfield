<?php
class DateCoefficient extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

//		$validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のカテゴリがあります。");
//		$validate["value"]["code"]  = array("rule" => "isCode", "message" => "このフィールドは英数字・記号です。");

		$validate["name"]["notEmpty"]   = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["value"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'date_coefficients');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'date_coefficients'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){

		$table = 'date_coefficients';
		$where = null;
		return parent::getOptions($table,$where);

	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'date_coefficients'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		$table = 'date_coefficients';
		return parent::getOneFieldById($table,$id,$field);
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM date_coefficients WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array[0];
	}

	/*
	 * リスト
	 */
	function getListEntityWhere($const_name){

		$table = 'date_coefficients';
		$where = "level = 1 AND const_name = '$const_name'";
		$where .=  " ORDER BY value ASC";

		return parent::getListEntityWhere($table, $where);

	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		if(isset($data["date_coefficients"]["month"])){
			$data["date_coefficients"]["month"] = sprintf('%02d',$data["date_coefficients"]["month"]);
		}
		if(isset($data["date_coefficients"]["day"])){
			$data["date_coefficients"]["day"] = sprintf('%02d',$data["date_coefficients"]["day"]);
		}
		return parent::isSave($data['date_coefficients']);
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
		$sql .=  "     FROM date_coefficients ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND level = 0 ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key] = $row["date_coefficients"];

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
		return parent::getAllEntity('date_coefficients');
	}

	/*
	 * カレンダー連動リスト
	 */
	function getEntityByDate($year,$month,$def_coefficient){

		// SQL処理
//		$sql =  "SELECT * FROM date_coefficients WHERE id = '$id' AND del_flg <= 0";
//		$array = $this->query($sql);
		$month = sprintf('%02d',$month);

		// SQL処理
		$sql =  "";
		$sql .=  "   SELECT * ";
		$sql .=  "     FROM date_coefficients ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "      AND year = '$year' ";
		$sql .=  "      AND month = '$month' ";
		$sql .=  "      ORDER BY `day` ";
		$array = $this->query($sql);

		$last_day = date("t", mktime(0, 0, 0, $month, 1, $year));

		$arrRes = array();

		if(count($array) > 0){
			$this->is_date_save = true;
			foreach($array as $key => $val){
				$arrRes[$key] = $val["date_coefficients"];
			}
		}else{
			$this->is_date_save = false;

			for($i=0;$i<$last_day;++$i){
				$arrRes[$i]["id"] = null;
				$arrRes[$i]["coefficient"] = $def_coefficient;
				$arrRes[$i]["day"] = $i + 1;
				$arrRes[$i]["day_week"] = date( "w", mktime(0, 0, 0, $month, ($i + 1), $year ));
			}
		}

		foreach($arrRes as $key => $val){
			switch ($val["day_week"]) {
				case (1):
					$day_week = "月曜";
					break;
				case (2):
					$day_week = "火曜";
					break;
				case (3):
					$day_week = "水曜";
					break;
				case (4):
					$day_week = "木曜";
					break;
				case (5):
					$day_week = "金曜";
					break;
				case (6):
					$day_week = "土曜";
					break;
				default:
					$day_week = "日曜";
					break;
			}
			$arrRes[$key]["day_week_disp"] = $day_week;
		}

		return $arrRes;
	}

	var $is_date_save;

	function isDateSave(){
		return $this->is_date_save;
	}

	/*
	 * カレンダー連動リスト
	 */
	function isMassSave($date){
		$date_coefficients = $date["date_coefficients"];
		if(count($date_coefficients['list']) > 0){
			foreach($date_coefficients['list'] as $row){
				$udata['date_coefficients']['year']     = $date['date_coefficients']['year'];
				$udata['date_coefficients']['month']    = $date['date_coefficients']['month'];
				$udata['date_coefficients']['id']       = $row['id'];
				$udata['date_coefficients']['day']      = $row['day'];
				$udata['date_coefficients']['day_week'] = $row['day_week'];
				$udata['date_coefficients']['coefficient'] = $row['coefficient'];
				$this->isSave($udata);
			}
		}

		return true;
	}
}
?>