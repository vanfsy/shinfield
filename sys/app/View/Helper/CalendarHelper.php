<?php
/**
 * カレンダーヘルパー.
 *
 */
class CalendarHelper extends AppHelper {
    var $_defaultLang = 'en';
    var $_week = array(
        'en' => array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
        'ja' => array('日', '月', '火', '水', '木', '金', '土')
    );

    /**
     * カレンダーを生成する.
     *
     * @param string $lang 言語
     * @param integer $date 日付
     * @return カレンダー
     */
    function makeCalendar($lang = null, $date = null, $url = null,$time_line_url = null) {
        if (is_null($date)) {
            $date = date('Ymd');
        }
        if (is_null($lang)) {
            $lang = $this->_defaultLang;
        }

        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);
        if (!checkdate($month, $day, $year)) {
            $this->log('Invalid date format!');
        }

        $prev_year = $year;
        $next_year = $year;
        $prev_month = $month - 1;
        $next_month = $month + 1;
        if($month == 12){
            $prev_year = $year;
            $next_year = $year + 1;
            $prev_month = 11;
            $next_month = 1;
        }else if($month == 1){
            $prev_year = $year - 1;
            $next_year = $year;
            $prev_month = 12;
            $next_month = 2;
        }
        $prev_month = sprintf('%02d', $prev_month);
        $next_month = sprintf('%02d', $next_month);
        $prev = "<a href=\"".$url.$prev_year.$prev_month."\">".$prev_year."年".$prev_month."月</a>";
        $next = "<a href=\"".$url.$next_year.$next_month."\">".$next_year."年".$next_month."月</a>";
        return $this->output(
            "<div id=\"calendar\"><div id=\"calendar-header\"><div class=\"prev\"><p>&lt;&lt;".$prev."</p></div><div class=\"current\"><p>".$year."年".$month."月</p></div><div class=\"next\"><p>".$next."&gt;&gt;</p></div><br class=\"clear\" /></div><table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1 schedule\">".
            $this->_makeWeekHeader($lang).$this->_makeCalendarContent($year, $month, $date,$time_line_url)."</table></div>"
        );
    }

    /**
     * 週ヘッダーを生成する.
     *
     * @param string $lang 言語
     * @return 週ヘッダー
     */
    function _makeWeekHeader($lang = null) {
        if (is_null($lang)) {
            $lang = $this->_defaultLang;
        }
        $cell = array();
        foreach ($this->_week[$lang] as $weekId => $week) {
            array_push($cell, "<th width=\"14%\" align=\"center\" class=\"week_".strtolower($this->_week[$this->_defaultLang][$weekId])."\">".$week."</td>");
        }
        return "<tr id=\"week_header\">".implode("", $cell)."</tr>";
    }

    /**
     * カレンダーコンテンツを生成する.
     *
     * @param integer $year 年
     * @param integer $month 月
     * @param integer $selecteday 選択日
     * @return カレンダーコンテンツ
     */
    function _makeCalendarContent($year, $month, $selectedDay,$time_line_url = null) {
        if (!is_numeric($year) || !is_numeric($month) || !is_numeric($selectedDay)) {
            throw new Exception("Invalid parameters!");
        }

        $calendar = array();
        $weekNo = 1; // 月内での週番号(第○週)
        $last = substr($this->_getLastDay($year, $month), 6, 2);
        for ($day = 1; $day <= $last; $day++) {
            $date = $year.$month.sprintf("%02d", $day); // Ymd形式
            $weekId = $this->_getWeekIdOfDay($year, $month, $day, $this->_defaultLang);
            $attr = array();
            if ($this->_getToday() == $date) {
                array_push($attr, 'today');
            }
            if ($selectedDay == $date) {
                array_push($attr, 'selected');
            }
            // TODO 祝日判定
            if (false) {
                array_push($attr, 'holiday');
            }
            $calendar[$weekNo][$weekId] = array('day' => $day, 'attribute' => $attr);
            // 翌週へ
            if ($weekId == count($this->_week[$this->_defaultLang]) - 1) {
                $weekNo++;
            }
        }
//        debug($calendar);
        $default = array('day' => '', 'attribute' => array());
        $c = array();
        for ($rowIdx = 1; $rowIdx <= count($calendar); $rowIdx++) {
            $elements = array();
            for ($colIdx = 0; $colIdx < count($this->_week[$this->_defaultLang]); $colIdx++) {
                $day = $default;
                if (array_key_exists($colIdx, $calendar[$rowIdx])) {
                    $day = $calendar[$rowIdx][$colIdx];
                }
//                debug($day);
                $link_day = $day['day'];
                if($link_day != null){
                    $link_url = $time_line_url.$this->_getFirstWeekDay($year, $month, $link_day);
                }
                if($time_line_url != null) {
                    if($colIdx == 0){
                        $link_day = "<div class=\"day\">" . $link_day . "</div>";
                    }else if(!empty($link_url)){
                        $link_day = "<div class=\"day\"><a href='".$link_url."'>" . $link_day . "</a></div>";
                    }
                }
                $dataClass = "reservation_".$year.sprintf("%02d",$month).sprintf("%02d",$day['day']);
                array_push(
                    $elements,
                    "<td class=\"".strtolower($this->_week[$this->_defaultLang][$colIdx])." ".implode(" ", $day['attribute'])."\">".$link_day."<div class=\"reserv\" id=\"".$dataClass."\"></div></td>"
                );
            }
            array_push($c, "<tr class=\"week\">".implode("", $elements)."</tr>");
        }
        return implode("", $c);
    }

    /**
     * 月初の日付を返す.
     *
     * @param integer $year 年
     * @param integer $month 月
     * @param unknown_type $format フォーマット
     * @return 月初の日付
     */
    function _getFirstDay($year, $month, $format = 'Ymd') {
        return date($format, mktime(0, 0, 0, $month, 1, $year));
    }
    /**
     * 月末の日付を返す.
     *
     * @param integer $year 年
     * @param integer $month 月
     * @param string $format フォーマット
     * @return 月末の日付
     */
    function _getLastDay($year, $month, $format = 'Ymd') {
        return date($format, mktime(0, 0, 0, $month + 1, 0, $year));
    }
    /**
     * 日付を返す.
     *
     * @param integer $year 年
     * @param integer $month 月
     * @param integer $day 日
     * @param string $format フォーマット
     * @return 月末の日付
     */
    function _getDay($year, $month, $day, $format = 'Ymd') {
        return date($format, mktime(0, 0, 0, $month, $day, $year));
    }
    /**
     * 今日の日付を返す.
     *
     * @param string $format フォーマット
     * @return 今日の日付
     */
    function _getToday($format = 'Ymd') {
        return date($format);
    }
    /**
     * 曜日IDを返す.
     *
     * @param integer $year 年
     * @param integer $month 月
     * @param integer $day 日
     * @param string $lang 言語
     * @return 曜日ID
     */
    function _getWeekIdOfDay($year, $month, $day, $lang = null) {
        if (is_null($lang)) {
            $lang = $this->_defaultLang;
        }
        return $this->_getDay($year, $month, $day, 'w');
    }
    /**
     * 曜日を返す.
     *
     * @param integer $year 年
     * @param integer $month 月
     * @param integer $day 日
     * @param string $lang 言語
     * @return 曜日
     */
    function _getWeekOfDay($year, $month, $day, $lang = null) {
        if (is_null($lang)) {
            $lang = $this->_defaultLang;
        }
        return $this->_week[$lang][$this->_getDay($year, $month, $day, 'w')];
    }
    /**
     * 週初（月）の日付を返す.
     *
     * @param integer $year 年
     * @param integer $month 月
     * @param integer $day 日
     * @return 週初（月）の日付Ymd
     */
    function _getFirstWeekDay($year, $month, $day) {
        $arrWDay = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
        $def_date = mktime(0, 0, 0, $month, $day, $year);
        $d = date("D", $def_date);
        foreach($arrWDay as $key => $val){
            if($d == $val) $wd_num = $key;
        }
        $w_date = $def_date - (3600 * 24 * $wd_num);
        return date("Ymd", $w_date);
    }

    /**
     * 当年から指定した範囲の年を配列で返す.
     *
     * @param integer $satrt 当年より+-年
     * @param integer $end  当年より+-年
     * @return array("開始年",,,,"終了年")
     */
    function arrYearList($start = 0, $end = 5) {
		$arrRes = array();

		$s_year = date("Y") + $start;
		$e_year = date("Y") + $end;
		for($i = $s_year; $i < $e_year; $i++){
			$arrRes[$i] = $i;
		}
    	return $arrRes;
    }

    /**
     * 1～31の範囲を配列で返す.
     *
     * @return array("1",,,,"31")
     */
    function arrDayList() {
		for($i = 1; $i <= 31; $i++){
			$arrRes[$i] = $i;
		}
		return $arrRes;
    }

    /**
     * 1～12の範囲を配列で返す.
     *
     * @return array("1",,,,"12")
     */
    function arrMonthList() {
		for($i = 1; $i <= 12; $i++){
			$arrRes[$i] = $i;
		}
		return $arrRes;
    }

    /**
     * 本日より14日後から22日後までの範囲を配列で返す.
     *
     * @return array("2011/10/01" => "2011年10月01日",,,,)
     */
    function arrOrderDate($int = 22) {
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		$stamp = mktime(0, 0, 0, $month, $day, $year);
		$array = array();
		for($i=0;$i<$int;++$i){
			$stamp = $stamp + (60 * 60 * 24);
			$value = date('Y/m/d', $stamp);
			$label = date('Y年m月d日', $stamp);
			$array[$value] = $label;
		}

		return $array;
    }
}
?>
