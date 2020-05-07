<?php
class PagingBehavior extends ModelBehavior {
/**
      * ページング処理
*/

	var $pgnum = 0;
	var $disp_num = 0;
	var $max_num = 0;
	var $table_name = null;

	function setDispNum(&$model,$disp_num) {
		$this->disp_num = $disp_num;
	}

	function setPgNum(&$model,$pgnum) {
		$this->pgnum = $pgnum;
	}

	function getPagingString(&$model,$sql_string) {
		// ページング処理
		if($this->pgnum > 1){
			$start = ( $this->pgnum - 1 ) * $this->disp_num;
		}else{
			$start = 0;
		}

		$sql_string .=  " LIMIT $start ,".$this->disp_num." ";
		return $sql_string;
	}

	/*
	 * 管理画面 ページング前項
	 */
	function getPgPrev(&$model){
		$pg = 0;
		if($this->pgnum > 0) $pg = $this->pgnum - 1;
		return $pg;
	}

	/*
	 * 管理画面 ページング次項
	 */
	function getPgNext(&$model){
		$pg = 2;
		echo $this->pgnum;
		if($this->pgnum > 0) $pg = $this->pgnum + 1;
		if($this->pgnum >= $this->max_num ) $pg = 0;
		return $pg;
	}

	/*
	 * 管理画面 ページング総数
	 */
	function getArrPgList(&$model,$table_name){
		$sql = "SELECT count(*) as count FROM ".$table_name." WHERE del_flg = 0";

		$array = $model->query($sql);
		$cnt = $array[0][0]["count"];
		$plus = $cnt % $this->disp_num;
		$max_num = ($cnt - $plus) / $this->disp_num;
		if($plus > 0){
			$max_num = $max_num + 1;
		}
		$this->max_num = $max_num;

		$array = array();
		for($i=0;$i < $max_num;$i++){
			$array[$i] = $i + 1;
		}

		return $array;
	}

	/*
	 * 管理画面 ページング現在のPG
	 */
	function getCurrentPg(&$model){
		$current_pg = 1;
		if(!is_numeric($this->pgnum)) $this->pgnum = 1;
		if($this->pgnum > 0) $current_pg = $this->pgnum;
		return $current_pg;
	}

}
?>
