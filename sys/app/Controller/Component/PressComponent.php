<?php
class PressComponent extends Component {

	var $utility;

   function __construct() {
		App::import('Component', 'Utility');
		$this->Utility = new UtilityComponent(new ComponentCollection());
   }

/*
* 新着情報
*/
    function whatsNew() {

		// 情報取得
		$PressPages = ClassRegistry::init('PressPages');
		$conditions = array('conditions' => array('press_id = 1','del_flg <= 0'),'order' => array('subject DESC'));
		$list = $PressPages->find("all", $conditions);
		$arrRes = array();
		$now = date("Y-m-d",time());
		foreach($list as $key => $val){
			$row = $val["PressPages"];
			if($row["start_date"] < $now && $row["end_date"] == "1900-01-01"){
				$arrRes[] = $row;
			}else if($row["end_date"] >= $now){
				$arrRes[] = $row;
			}
		}
		return $arrRes;

    }

}
?>