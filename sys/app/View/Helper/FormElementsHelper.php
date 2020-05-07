<?php
class FormElementsHelper extends AppHelper {

    function inqueryPref($selected = null) {

		$res = null;
		$res .= '<select id="pref" name="data[pref]">';
		$res .= '<option value="">選択してください</option>';
		$res .= '<optgroup label="北海道・東北地方">';
		$res .= '<option value="北海道" ';if($selected === '北海道') $res .= 'selected="selected"';$res .= '>北海道</option>';
		$res .= '<option value="青森県" ';if($selected === '青森県') $res .= 'selected="selected"';$res .= '>青森県</option>';
		$res .= '<option value="岩手県" ';if($selected === '岩手県') $res .= 'selected="selected"';$res .= '>岩手県</option>';
		$res .= '<option value="宮城県" ';if($selected === '宮城県') $res .= 'selected="selected"';$res .= '>宮城県</option>';
		$res .= '<option value="秋田県" ';if($selected === '秋田県') $res .= 'selected="selected"';$res .= '>秋田県</option>';
		$res .= '<option value="山形県" ';if($selected === '山形県') $res .= 'selected="selected"';$res .= '>山形県</option>';
		$res .= '<option value="福島県" ';if($selected === '福島県') $res .= 'selected="selected"';$res .= '>福島県</option>';
		$res .= '</optgroup>';
		$res .= '<optgroup label="関東地方">';
		$res .= '<option value="茨城県" ';if($selected === '茨城県') $res .= 'selected="selected"';$res .= '>茨城県</option>';
		$res .= '<option value="栃木県" ';if($selected === '栃木県') $res .= 'selected="selected"';$res .= '>栃木県</option>';
		$res .= '<option value="群馬県" ';if($selected === '群馬県') $res .= 'selected="selected"';$res .= '>群馬県</option>';
		$res .= '<option value="埼玉県" ';if($selected === '埼玉県') $res .= 'selected="selected"';$res .= '>埼玉県</option>';
		$res .= '<option value="千葉県" ';if($selected === '千葉県') $res .= 'selected="selected"';$res .= '>千葉県</option>';
		$res .= '<option value="東京都" ';if($selected === '東京都') $res .= 'selected="selected"';$res .= '>東京都</option>';
		$res .= '<option value="神奈川県" ';if($selected === '神奈川県') $res .= 'selected="selected"';$res .= '>神奈川県</option>';
		$res .= '</optgroup>';
		$res .= '<optgroup label="中部地方">';
		$res .= '<option value="新潟県" ';if($selected === '新潟県') $res .= 'selected="selected"';$res .= '>新潟県</option>';
		$res .= '<option value="富山県" ';if($selected === '富山県') $res .= 'selected="selected"';$res .= '>富山県</option>';
		$res .= '<option value="石川県" ';if($selected === '石川県') $res .= 'selected="selected"';$res .= '>石川県</option>';
		$res .= '<option value="福井県" ';if($selected === '福井県') $res .= 'selected="selected"';$res .= '>福井県</option>';
		$res .= '<option value="山梨県" ';if($selected === '山梨県') $res .= 'selected="selected"';$res .= '>山梨県</option>';
		$res .= '<option value="長野県" ';if($selected === '長野県') $res .= 'selected="selected"';$res .= '>長野県</option>';
		$res .= '<option value="岐阜県" ';if($selected === '岐阜県') $res .= 'selected="selected"';$res .= '>岐阜県</option>';
		$res .= '<option value="静岡県" ';if($selected === '静岡県') $res .= 'selected="selected"';$res .= '>静岡県</option>';
		$res .= '<option value="愛知県" ';if($selected === '愛知県') $res .= 'selected="selected"';$res .= '>愛知県</option>';
		$res .= '</optgroup>';
		$res .= '<optgroup label="近畿地方">';
		$res .= '<option value="三重県" ';if($selected === '三重県') $res .= 'selected="selected"';$res .= '>三重県</option>';
		$res .= '<option value="滋賀県" ';if($selected === '滋賀県') $res .= 'selected="selected"';$res .= '>滋賀県</option>';
		$res .= '<option value="京都府" ';if($selected === '京都府') $res .= 'selected="selected"';$res .= '>京都府</option>';
		$res .= '<option value="大阪府" ';if($selected === '大阪府') $res .= 'selected="selected"';$res .= '>大阪府</option>';
		$res .= '<option value="兵庫県" ';if($selected === '兵庫県') $res .= 'selected="selected"';$res .= '>兵庫県</option>';
		$res .= '<option value="奈良県" ';if($selected === '奈良県') $res .= 'selected="selected"';$res .= '>奈良県</option>';
		$res .= '<option value="和歌山県" ';if($selected === '和歌山県') $res .= 'selected="selected"';$res .= '>和歌山県</option>';
		$res .= '</optgroup>';
		$res .= '<optgroup label="中国地方">';
		$res .= '<option value="鳥取県" ';if($selected === '鳥取県') $res .= 'selected="selected"';$res .= '>鳥取県</option>';
		$res .= '<option value="島根県" ';if($selected === '島根県') $res .= 'selected="selected"';$res .= '>島根県</option>';
		$res .= '<option value="岡山県" ';if($selected === '岡山県') $res .= 'selected="selected"';$res .= '>岡山県</option>';
		$res .= '<option value="広島県" ';if($selected === '広島県') $res .= 'selected="selected"';$res .= '>広島県</option>';
		$res .= '<option value="山口県" ';if($selected === '山口県') $res .= 'selected="selected"';$res .= '>山口県</option>';
		$res .= '<option value="徳島県" ';if($selected === '徳島県') $res .= 'selected="selected"';$res .= '>徳島県</option>';
		$res .= '<option value="香川県" ';if($selected === '香川県') $res .= 'selected="selected"';$res .= '>香川県</option>';
		$res .= '<option value="愛媛県" ';if($selected === '愛媛県') $res .= 'selected="selected"';$res .= '>愛媛県</option>';
		$res .= '<option value="高知県" ';if($selected === '高知県') $res .= 'selected="selected"';$res .= '>高知県</option>';
		$res .= '</optgroup>';
		$res .= '<optgroup label="九州地方">';
		$res .= '<option value="福岡県" ';if($selected === '福岡県') $res .= 'selected="selected"';$res .= '>福岡県</option>';
		$res .= '<option value="佐賀県" ';if($selected === '佐賀県') $res .= 'selected="selected"';$res .= '>佐賀県</option>';
		$res .= '<option value="長崎県" ';if($selected === '長崎県') $res .= 'selected="selected"';$res .= '>長崎県</option>';
		$res .= '<option value="熊本県" ';if($selected === '熊本県') $res .= 'selected="selected"';$res .= '>熊本県</option>';
		$res .= '<option value="大分県" ';if($selected === '大分県') $res .= 'selected="selected"';$res .= '>大分県</option>';
		$res .= '<option value="宮崎県" ';if($selected === '宮崎県') $res .= 'selected="selected"';$res .= '>宮崎県</option>';
		$res .= '<option value="鹿児島県" ';if($selected === '鹿児島県') $res .= 'selected="selected"';$res .= '>鹿児島県</option>';
		$res .= '<option value="沖縄県" ';if($selected === '沖縄県') $res .= 'selected="selected"';$res .= '>沖縄県</option>';
		$res .= '</optgroup>';
		$res .= '<optgroup label="日本国外">';
		$res .= '<option value="日本国外" ';if($selected === '日本国外') $res .= 'selected="selected"';$res .= '>日本国外</option>';
		$res .= '</optgroup>';
		$res .= '</select>';
		return $res;

    }

	//requestAction
	function fetch() {

		App::import('Component', 'Session');
		$Session = new SessionComponent();

		$member = "ゲスト";
		$authMember = $Session->read("authuser");
		if(count($authMember) > 0 ){
			$email = $authMember["email"];
			$Customers = ClassRegistry::init('Customers');
			$conditions = array('conditions' => array("email = '$email'","del_flg <= 0"));
			$list = $Customers->find("first", $conditions);
			$authMember["user"] = $list["Customers"]["family_name"]." ".$list["Customers"]["first_name"];
			$member = $authMember["user"];
		}
		return $member."=test";
	}

}
?>
