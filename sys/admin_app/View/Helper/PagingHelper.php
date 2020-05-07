<?php
App::uses('AppHelper', 'View/Helper');
class PagingHelper extends AppHelper {

/**
 * Automatically resizes an image and returns formatted IMG tag
 *
 * @param array   $pg_list
 * @param integer $prev
 * @param integer $next
 * @param integer $current_pg
 * @param string  $url
 * @param integer $count
 * @access public
 */

    function list_navi($data, $url, $count = 10) {

        $pg_list = $data["pg_list"];
        $prev = $data["prev"];
        $next = $data["next"];
        $current_pg = $data["current_pg"];
        $total = $data["total"];

        $url = '/admin'.$url."&pg=";
        $html = null;

        $html .= '<nav>';
        $html .= '<ul class="pagination">';

        if ($prev == 0){
            //$html .= "<li class=\"prev\"></li>  ";
            $html .= '<li><span aria-hidden="true">&laquo;</span></li>';
        }else{
            $html .= '<li><a href="'.$url.'1"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        if($count > count($pg_list)) $count = count($pg_list);
        $interval = round($count/2);
        $max = $current_pg + $interval + 1;
        $min = $current_pg - $interval + 1;
        if($max > (count($pg_list) + 1)){
            $max = (count($pg_list) + 1);
            $min = $max - $count;
        }
        if($min < 1){
            $min = 1;
            $max = $count + 1;
        }
        for($i = $min;$i < $max;++$i){
            if($current_pg == $i){
                $html .= '<li class="active"><span aria-hidden="true">'.$i.'</span></li> ';
            }else{
                $html .= '<li><a href="'.$url.$i.'">'.$i.'</a></li> ';
            }
        }

        if ($next == 0){
            $html .= '<li><span aria-hidden="true">&raquo;</span></li>';
        }else{
            $html .= '<li><a href="'.$url.($max - 1).'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        }
        $html .= '</ul></nav>';

        return $html;
    }

}
?>
