<?php
class CalendarDate extends AppModel {
    var $validate = array();
    var $error_message = null;
    var $pgnum = 0;

    /*
     * 入力値検証
     */

    function isValid(){

        $validate["date"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

        $this->validate = $validate;

    }

    /*
     * エラーチェック
     * */
    function isError($data){
        return parent::isError($data,'calendar_dates');
    }

    /*
     * フィールド
     */
    function getFields($where = null){
        return parent::getFields($where);
    }

    /*
     * オプションリスト
     */
    function getOptions($where = null){
        return parent::getOptions($where);
    }

    /*
     * オプションリスト
     */
    function getTreeOptions($level = 2, $where = ""){

        return parent::getTreeOptions($level, $where);
    }

    /*
     * リスト
     */
    function getOneFieldById($id,$field){

        // SQL処理
        $sql =  "SELECT `$field` FROM calendar_dates WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        $res = null;
        if(count($array) > 0) $res = $array[0]["calendar_dates"][$field];
        return $res;
    }

    /*
     * リスト
     */
    function getOneEntityById($id){

        // SQL処理
        $sql =  "SELECT * FROM calendar_dates WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        if(count($array)>0){
            return $array[0];
        }else{
            return array();
        }
    }

    /*
     * 登録更新処理
     */
    function isSave($data,$valid = true){
        return parent::isSave($data['calendar_dates'],$valid);
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
        $sql .=  "     FROM calendar_dates ";
        $sql .=  "    WHERE del_flg <= 0 ";
        $sql .=  " ORDER BY rank ";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $arrRes["list"][$key] = $row["calendar_dates"];

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
        return parent::getAllEntity('calendar_dates');
    }

    /*
     * お届け日リスト
     */
    function getDelivDates(){

        $today = date("Y-m-d");

        // SQL処理
        $sql =  "";
        $sql .=  "   SELECT * ";
        $sql .=  "     FROM calendar_dates ";
        $sql .=  "    WHERE del_flg <= 0 ";
        $sql .=  "      AND date >= '$today' ";
        $sql .=  "      AND off_work_flg = 0 ";
        $sql .=  "    ORDER BY date ";
        $sql .=  "    LIMIT 14 ";

        $array = $this->query($sql);

        return $array;
    }

    /*
     * カレンダー連動リスト
     */
    function getEntityByDate($year,$month,$nextYear,$nextMonth){

        $cache_exp = Cache::read("cacheExpCalenderData");
        $cache_data = Cache::read("cacheCalenderData");

        if($cache_exp == $year.$month){
            return $cache_data;
        }

        $cache_exp = Cache::write("cacheExpCalenderData",$year.$month);

        $month = sprintf('%02d',$month);
        $nextMonth = sprintf('%02d',$nextMonth);

        // SQL処理
        $sql =  "";
        $sql .=  "   SELECT * ";
        $sql .=  "     FROM calendar_dates ";
        $sql .=  "    WHERE del_flg = 0 ";
        $sql .=  "      AND ( (year = '$year' AND month = '$month') OR (year = '$nextYear' AND month = '$nextMonth') ) ";
        $sql .=  " ORDER BY `date` ";
        $array = $this->query($sql);

        App::import('Component', 'Utility');
        $Utility = new UtilityComponent(new ComponentCollection());

        $arrRes = array();
        $deliver = null;
        foreach($array as $key => $row){
            $arrRes[$key] = $row["calendar_dates"];
            $deliver_date = str_replace("-","/",$row["calendar_dates"]['deliver_date']);
            $arrRes[$key]["deliver_date"] = $deliver_date;

            $arrRes[$key]['today'] = 0;
            $arrRes[$key]['deliver'] = 0;
            if($row["calendar_dates"]['date'] == date('Y-m-d')){
                $arrRes[$key]['today'] = 1;
                $deliver = $row["calendar_dates"]['deliver_date'];
            }
            if($row["calendar_dates"]['date'] == date('Y-m-d',strtotime($deliver))){
                $arrRes[$key]['deliver'] = 1;
            }

            switch ($row["calendar_dates"]["day_week"]) {
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

        // 当月
        $i = 0;
        $arrCalender[0][0] = array('day_week' => 0, 'day' => '','today' => 0, 'deliver' => 0);
        $arrCalender[0][1] = array('day_week' => 1, 'day' => '','today' => 0, 'deliver' => 0);
        $arrCalender[0][2] = array('day_week' => 2, 'day' => '','today' => 0, 'deliver' => 0);
        $arrCalender[0][3] = array('day_week' => 3, 'day' => '','today' => 0, 'deliver' => 0);
        $arrCalender[0][4] = array('day_week' => 4, 'day' => '','today' => 0, 'deliver' => 0);
        $arrCalender[0][5] = array('day_week' => 5, 'day' => '','today' => 0, 'deliver' => 0);
        $arrCalender[0][6] = array('day_week' => 6, 'day' => '','today' => 0, 'deliver' => 0);
        foreach($arrRes as $key => $row){
            $w = $row['day_week'];
            $arrCalender[$i][$w] = $row;
            if($w == 6){
                ++$i;
                $arrCalender[$i][0] = array('day_week' => 0, 'day' => '','today' => 0, 'deliver' => 0);
                $arrCalender[$i][1] = array('day_week' => 1, 'day' => '','today' => 0, 'deliver' => 0);
                $arrCalender[$i][2] = array('day_week' => 2, 'day' => '','today' => 0, 'deliver' => 0);
                $arrCalender[$i][3] = array('day_week' => 3, 'day' => '','today' => 0, 'deliver' => 0);
                $arrCalender[$i][4] = array('day_week' => 4, 'day' => '','today' => 0, 'deliver' => 0);
                $arrCalender[$i][5] = array('day_week' => 5, 'day' => '','today' => 0, 'deliver' => 0);
                $arrCalender[$i][6] = array('day_week' => 6, 'day' => '','today' => 0, 'deliver' => 0);
            }
        }

        $arrCalenderData = array();

        foreach($arrCalender as $key => $row){
            if($key == 0){
                $ym = $row[6]['year'].'-'.$row[6]['month'];
                $arrCalenderData[$ym][] = $row;
            }
            if($key > 0 && isset($row[0]['year']) && isset($row[0]['month']) && $row[0]['year'] == $year && $row[0]['month'] == $month){
                $ym = $row[0]['year'].'-'.$row[0]['month'];
                $arrCalenderData[$ym][] = $row;
            }
            if($key > 0 && isset($row[6]['year']) && isset($row[6]['month']) && $row[6]['year'] == $nextYear && $row[6]['month'] == $nextMonth){
                $ym = $row[6]['year'].'-'.$row[6]['month'];
                $arrCalenderData[$ym][] = $row;

            }else if($key > 0 && isset($row[0]['year']) && isset($row[0]['month']) && $row[0]['year'] == $nextYear && $row[0]['month'] == $nextMonth){
                $ym = $row[0]['year'].'-'.$row[0]['month'];
                $arrCalenderData[$ym][] = $row;

            }
        }

        Cache::write("cacheCalenderData",$arrCalenderData);

        return $arrCalenderData;
    }

    var $is_date_save;

    function isDateSave(){
        return $this->is_date_save;
    }

    /*
     * カレンダー連動リスト
     */
    function isMassSave($date){
        $calendar_dates = $date["calendar_dates"];
        if(count($calendar_dates['list']) > 0){
            foreach($calendar_dates['list'] as $row){

                $year     = $date['calendar_dates']['year'];
                $month    = sprintf('%02d',$date['calendar_dates']['month']);
                $day      = sprintf('%02d',$row['day']);
                $deliver_date = str_replace("/","-",$row['deliver_date']);
                $off_work_flg = $row['off_work_flg'];

                $udata['calendar_dates']['year']     = $year;
                $udata['calendar_dates']['month']    = $month;
                $udata['calendar_dates']['id']       = $row['id'];
                $udata['calendar_dates']['day']      = $day;
                $udata['calendar_dates']['day_week'] = $row['day_week'];
                $udata['calendar_dates']['deliver_date'] = $deliver_date;
//                $udata['calendar_dates']['merch_basic_id'] = $date['calendar_dates']['merch_basic_id'];
//                $udata['calendar_dates']['merch_item_id'] = $date['calendar_dates']['merch_item_id'];
                $udata['calendar_dates']['date'] = $year."-".$month."-".$day;
                $udata['calendar_dates']['off_work_flg'] = $off_work_flg;
                $this->isSave($udata);
            }
        }

        return true;
    }

    /*
     * 配達希望日リスト
     */
    function getArrDelivDates(){

        $today = date("Y-m-d");
        $arr_today = explode("-",$today);
        $regist1 = mktime(0,0,0,$arr_today[1],$arr_today[2],$arr_today[0]);
        $regist1 = $regist1 + (14 * 24 * 60 * 60);
        $limit_date = date("Y-m-d",$regist1);

        // SQL処理
        $sql =  "";
        $sql .=  "   SELECT * ";
        $sql .=  "     FROM calendar_dates ";
        $sql .=  "    WHERE del_flg <= 0 ";
        $sql .=  "      AND deliver_date >= '$limit_date' ";
        $sql .=  "      AND off_work_flg <> 1 ";
        $sql .=  " ORDER BY `deliver_date` ";
        $sql .=  " LIMIT 14 ";
        $array = $this->query($sql);

        $arrRes = array();
        if(count($array) > 0){
            foreach($array as $row){
                $deliver_date = $row["calendar_dates"]["deliver_date"];
                $arr_deliver_date = explode("-",$deliver_date);
                $key = $arr_deliver_date[0]."/".$arr_deliver_date[1]."/".$arr_deliver_date[2];
                $value = $arr_deliver_date[0]."年".$arr_deliver_date[1]."月".$arr_deliver_date[2]."日";
                $arrRes[$key] = $value;
            }
        }

        return $arrRes;
    }

}
?>