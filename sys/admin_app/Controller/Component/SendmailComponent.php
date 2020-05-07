<?php
App::uses('Component', 'Controller');
class SendmailComponent extends Component {

/*
 * メールテンプレート送信処理
 */
	public function sendMailTemplate($body,$add_body = null,$to_mail = null, $template_code = null,$is_send_admin = false){
		$flg = true;
		$Qdmail         = ClassRegistry::init('Qdmail');
		$EmailTemplates = ClassRegistry::init('EmailTemplates');

		$obj = $EmailTemplates->getTemplateByCode($template_code);

		$obj_body = $this->convOrderTemplate($body,$template_code);

/*

		if(is_array($body)){
			$obj_body = $obj["body"];
			foreach($body as $key => $val){
				if(!is_array($val)){
					$serch_word = '<!--{$'.$key.'}-->';
					$obj_body = str_replace($serch_word,$val,$obj_body);
				}
			}
		}else{
			$obj_body = str_replace('<!--{$order}-->',$body,$obj["body"]);
		}

		if(isset($body["detail"])){
			// 商品詳細変換
			$arr1 = explode('<!--{loop_dateil}-->',$obj_body);
			$top_body = $arr1[0];
			$str = $arr1[1];
			$arr2 = explode('<!--{/loop}-->',$str);
			$str_line = $arr2[0];
			$bottom_body = $arr2[1];

			$trans_line = null;
			foreach($body["detail"] as $val){
				$temp_body = $str_line;
				$temp_body = str_replace('<!--{$item_name}-->',$val["item_name"],$temp_body);
				$temp_body = str_replace('<!--{$options}-->',$val["options"],$temp_body);
				$temp_body = str_replace('<!--{$price}-->',$val["price"],$temp_body);
				$temp_body = str_replace('<!--{$quantity}-->',$val["quantity"],$temp_body);
				$temp_body = str_replace('<!--{$total}-->',$val["total"],$temp_body);
				$trans_line .= $temp_body;
			}
			$obj_body = $top_body.$trans_line.$bottom_body;
		}
*/

		// メール送信 カスタマー宛
		mb_language('ja');
		$Qdmail->errorDisplay(false);
		$param = array('host'=> SMTP_HOST,'port'=> SMTP_PORT,'from'=> SMTP_FROM ,'protocol'=> SMTP_PROTOCOL ,'user'=> SMTP_USER ,'pass' => SMTP_PASS ,);
		$Qdmail->smtp(true);
		$Qdmail->smtpServer($param);
		$Qdmail->unitedCharset( 'UTF-8' );

		$Qdmail->to($to_mail);
		$Qdmail->subject($obj["subject"]);
		$Qdmail->from($obj["admin_mail"],'Evenear（イベニア）');
		$Qdmail->bcc("admin@mallento.com");
		$Qdmail->text( $obj_body );
		if($Qdmail->send()){
			if($is_send_admin){
				// メール送信 ショップオーナー宛
				$Qdmail->to($obj["admin_mail"],'Evenear（イベニア）');
				$Qdmail->subject($obj["name"]);
				$Qdmail->from($to_mail);
				$Qdmail->text( $add_body."------------------------------------------\n\n".$obj_body );
				$Qdmail->bcc("admin@mallento.com");
				$Qdmail->send();
			}
		}else{
			$flg = false;
		}

		return $flg;

	}


/*
 * メール送信処理
 */
	public function send($body,$subject,$from_mail,$to_mail){

		$flg = true;
		$Qdmail         = ClassRegistry::init('Qdmail');

		mb_language('ja');
		$Qdmail->errorDisplay(false);
		$param = array('host'=> SMTP_HOST,'port'=> SMTP_PORT,'from'=> SMTP_FROM ,'protocol'=> SMTP_PROTOCOL ,'user'=> SMTP_USER ,'pass' => SMTP_PASS ,);
		$Qdmail->smtp(true);
		$Qdmail->smtpServer($param);
		$Qdmail->unitedCharset( 'UTF-8' );

		$Qdmail->to($to_mail);
		$Qdmail->subject($subject);
		$Qdmail->from($from_mail);
		$Qdmail->text( $body );
		if($Qdmail->send()){
			// メール送信 ショップオーナー宛
			$Qdmail->to($from_mail);
			$Qdmail->subject($subject);
			$Qdmail->from($from_mail);
			$Qdmail->text( "以下の内容で送信しました。------------------------------------------\n\n".$body );
			$Qdmail->send();
		}else{
			$flg = false;
		}

		return $flg;

	}
}
?>