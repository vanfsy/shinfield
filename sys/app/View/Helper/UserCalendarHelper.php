<?php
/**
 * カレンダーヘルパー.
 *
 */
class UserCalendarHelper extends AppHelper {
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
        $prev = $url.$prev_year.$prev_month;
        $next = $url.$next_year.$next_month;
        return $this->output(
            "<div class=\"calendar\" id=\"calendar\"><p class=\"month\">".$year."年".$month."月</p>".
            "<ul class=\"btn\">".
            "  <li><span id=\"prev_month\" style=\"cursor: pointer\" onclick=\"fncLoad('".$prev."');false\"><img src=\"http://www.fourc-s.com/images/calendar_btn_01.gif\" width=\"50\" height=\"20\" alt=\"前月\" /></span></li>".
            "  <li><span id=\"next_month\" style=\"cursor: pointer\" onclick=\"fncLoad('".$next."');false\"><img src=\"http://www.fourc-s.com/images/calendar_btn_02.gif\" width=\"50\" height=\"20\" alt=\"来月\" /></span></li>".
            "</ul>".
            "<table id=\"calendar-content\">".
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
            array_push($cell, "<th class=\"week_".strtolower($this->_week[$this->_defaultLang][$weekId])."\">".$week."</th>");
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
                        $link_day = "<div class=\"day\"><a href='#' onclick = \"javascript:window.open('".$link_url."','week','width=800,scrollbars=yes');return false\">" . $link_day . "</a></div>";
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
}
?>
