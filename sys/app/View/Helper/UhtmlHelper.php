<?php
class UhtmlHelper extends Helper {

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
	function admin_paging_navi($pg_list, $prev, $next, $current_pg, $url, $count = 10) {

		$url = HOME_URL.$url;
		$html = null;
		$html .= "<ul> ";
		if ($prev == 0){
			$html .= "<li class=\"prev\"></li>  ";
		}else{
			$html .= "<li class=\"prev\"><a href=\"".$url.$prev."\">&laquo; PREV</a></li> ";
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
				$html .= "<li><em>".$i."</em></li>  ";
			}else{
				$html .= "<li><a href=\"".$url.$i."\">".$i."</a></li>  ";
			}
		}

		if ($next == 0){
			$html .= "<li class=\"next\"></li>  ";
		}else{
			$html .= "<li class=\"next\"><a href=\"".$url.$next."\">NEXT &raquo;</a></li>  ";
		}
		$html .= "</ul>  ";

		return $html;
	}

	function paging_navi($entity, $url, $count = 10) {

		$pg_list = $entity["pg_list"];
		$prev = $entity["prev"];
		$next = $entity["next"];
		$current_pg = $entity["current_pg"];
		$total = $entity["total"];
		$first = $entity["first"];
		$last = $entity["last"];

		$url = HOME_URL.$url;
		$html = null;
		if ($prev == 0){
			$html .= "";
		}else{
			$html .= "<p class=\"re_10\"><a href=\"".$url.$prev."\">&laquo; 前の10件</a></p>";
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
		$html .= "<ul>\n";
		for($i = $min;$i < $max;++$i){
			if($current_pg == $i){
				$html .= "<li>".$i."</li>\n";
			}else{
				$html .= "<li><a href=\"".$url.$i."\">".$i."</a></li>\n";
			}
		}
		$html .= "</ul>\n";
		if ($next == 0){
			$html .= "";
		}else{
			$html .= "<p class=\"next_10\"><a href=\"".$url.$next."\">次の10件 &raquo;</a></p>\n";
		}

		return $html;
	}

	function items_table($list) {

		$ImageHelper = ClassRegistry::init('ImageHelper');

		$cnt = count($list);
		$col_num = 4;
		$surplus = $cnt % $col_num;
		$row_num = floor($cnt / 4);
		if($surplus > 0) ++$row_num;

		$html = null;
		$html .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

		$key = 0;
		for($i = 0;$i < $row_num;++$i){
			$html .= "  <tr  valign=\"top\">\n";
			for($j = 0;$j < $col_num;++$j){
				if(isset($list[$key])){
					$basic_name = htmlspecialchars($list[$key]["merch_basics"]["name"]);
					$items_id   = $list[$key]["merch_items"]["id"];
					$announce   = htmlspecialchars($list[$key]["merch_items"]["announce"]);
					$price      = '&yen;'.number_format($list[$key]["merch_items"]["sales_price"]);
					$image_src  = $ImageHelper->resize($list[$key]["merch_items"]["list_image"],165,165,true);

					$html_icons  = null;
					if(isset($list[$key]["merch_items"]["arr_icons"])){
						$arr_icons   = $list[$key]["merch_items"]["arr_icons"];
						$html_icons .= "<div class=\"mini_icon\">";
						foreach($arr_icons as $val){
							if ($val  == "sold_out"){
								$html_icons .= "<img src=\"".HOME_URL."images/common/icon_list01.png\" alt=\"\" />";
							} else if($val  == "new") {
								$html_icons .= "<img src=\"".HOME_URL."images/common/icon_list02.png\" alt=\"\" />";
							} else if ($val  == "price_down"){
								$html_icons .= "<img src=\"".HOME_URL."images/common/icon_list03.png\" alt=\"\" />";
							} else if($val  == "recommend"){
								$html_icons .= "<img src=\"".HOME_URL."images/common/icon_list04.png\" alt=\"\" />";
							}
						}

						$html .= "</div>";
					}


					$html .= "    <td>";
					$html .= "<div class=\"thumbGrid\">\n";
					$html .= "<dt class=\"thumb\">\n";
					$html .= "<a href=\"".HOME_URL."shop/item_detail/itemid_".$items_id."\">";
					$html .= "<img alt=\"".$basic_name."\" src=\"".$image_src."\" onerror=\"onImgErr(this)\" /></a></dt>";
					$html .= "<dd class=\"caption\">";
					$html .= "<dl>";
					$html .= "<pre>".$announce."</pre>";
					$html .= "<dt>";
					$html .= $basic_name;
					$html .= "</dt>";
					$html .= "<dd>";
					$html .= $price;
					$html .= "</dd>";
					$html .= $html_icons;
					$html .= "</dl>";
					$html .= "</dd>";
					$html .= "</div>\n";

					$html .= "&nbsp;</td>\n";

				}

				++$key;
			}
			$html .= "  </tr>\n";
		}
		$html .= "</table>\n";

		return $html;
	}

/**
 * optionタグに変換
 *
 * @param array   $list
 * @param string  $selected
 * @return html
 * @access public
 */
	function form_options($list, $selected = null) {
		$html = null;
		if(count($list) > 0){
			$html .= "<option value=\"\" selected=\"selected\" > -- 選択 -- </option>\n";
			foreach($list as $row){
				if($selected == $row["code"]){
					$html .= "<option class=\"sub_".$row["parent_code"]."\" title=\"".$row["code"]."\" label=\"".$row["name"]."\" value=\"".$row["code"]."\" selected=\"selected\" >".$row["name"]."</option>\n";
				}else{
					$html .= "<option class=\"sub_".$row["parent_code"]."\" title=\"".$row["code"]."\" label=\"".$row["name"]."\" value=\"".$row["code"]."\" >".$row["name"]."</option>\n";
				}
			}
		}
		return $html;
	}

/**
 * optionタグに変換
 *
 * @param array   $list
 * @param string  $selected
 * @return html
 * @access public
 */
	function form_tree_options($list, $selected = null) {
		$html = null;
		if(count($list) > 0){
			foreach($list as $row){
				$html .= "<optgroup title=\"".$row["name"]."\" label=\"".$row["name"]."\" >\n";
				foreach($row["child"] as $row_c){
					if($selected == $row_c["code"]){
						$html .= "<option class=\"sub_".$row_c["parent_code"]."\" title=\"".$row_c["code"]."\" label=\"".$row_c["name"]."\" value=\"".$row_c["code"]."\" selected=\"selected\" >".$row_c["name"]."</option>\n";
					}else{
						$html .= "<option class=\"sub_".$row_c["parent_code"]."\" title=\"".$row_c["code"]."\" label=\"".$row_c["name"]."\" value=\"".$row_c["code"]."\" >".$row_c["name"]."</option>\n";
					}
				}
				$html .= "</optgroup>\n";
			}
		}
		return $html;
	}
}
?>
