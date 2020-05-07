<?php
class EmailData extends AppModel {
	var $validate = array();
	var $error_message = null;
	var $pgnum = 0;

	/*
	 * 入力値検証
	 */

	function isValid(){

		$validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
		$validate["email"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");

		$this->validate = $validate;

	}

	/*
	 * エラーチェック
	 * */
	function isError($data){
		return parent::isError($data,'email_datas');
	}

	/*
	 * フィールド
	 */
	function getFields($table = 'email_datas'){
		return parent::getFields($table);
	}

	/*
	 * オプションリスト
	 */
	function getOptions(){
		$table = 'email_datas';
		return parent::getOptions($table);
	}

	/*
	 * オプションリスト
	 */
	function getTreeOptions($table = 'email_datas'){

		return parent::getTreeOptions($table);
	}

	/*
	 * リスト
	 */
	function getOneFieldById($id,$field){

		// SQL処理
		$sql =  "SELECT `$field` FROM email_datas WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$res = null;
		if(count($array) > 0) $res = $array[0]["email_datas"][$field];
		return $res;
	}

	/*
	 * リスト
	 */
	function getOneEntityById($id){

		// SQL処理
		$sql =  "SELECT * FROM email_datas WHERE id = '$id' AND del_flg <= 0";
		$array = $this->query($sql);
		$arrRes = $array[0];
		if(empty($arrRes["email_datas"]["sent_date"])){
			$arrRes["email_datas"]["status"]    = "未送信";
		}else{
			$arrRes["email_datas"]["status"]    = "送信済み";
		}
		return $arrRes;
	}

	/*
	 * 登録更新処理
	 */
	function isSave($data){
		return parent::isSave($data['email_datas']);
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
		$sql .=  "     FROM email_datas ";
		$sql .=  "    WHERE del_flg <= 0 ";
		$sql .=  "    ORDER BY sent_date ";

		// ページング用SQL文字列
		$pg_sql = $this->getPagingString($sql);
		$array = $this->query($pg_sql);

		$arrRes["list"] = array();
		foreach($array as $key => $row){

			$arrRes["list"][$key]              = $row["email_datas"];
			if(empty($row["email_datas"]["sent_date"])){
				$arrRes["list"][$key]["status"]    = "未送信";
			}else{
				$arrRes["list"][$key]["status"]    = "送信済み";
			}

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
		return parent::getAllEntity('email_datas');
	}

	/*
	 * 送信者リスト
	 */
	function setMailListByMagId($email_magazine_id){

		// SQL処理
		$sql =  "SELECT * FROM email_datas WHERE email_magazine_id = '$email_magazine_id' AND del_flg <= 0";
		$array = $this->query($sql);
		return $array;

	}

//	/*
//	 * 送信者数取得
//	 */
//	function getMailCount($table_name){
//
//		// SQL処理
//		$sql =  "";
//		$sql .=  "   SELECT DISTINCT email , count(*) as cnt";
//		$sql .=  "     FROM `$table_name` ";
//		$sql .=  "    WHERE del_flg <= 0 ";
//		$sql .=  "      AND email <> '' ";
//		$sql .=  "      AND email IS NOT NULL ";
//		$array = $this->query($sql);
//		if(count($array) > 0){
//			return $array[0][0]["cnt"];
//		}else{
//			return 0;
//		}
//
//	}

	/*
	 * メール内容設定
	 */
	function setMailList($email_magazine_id,$data,$list){

		if(count($list) > 0){

			$subject   = $data["email_magazines"]["subject"];
			$body      = $data["email_magazines"]["body"];
			$from_mail = $data["email_magazines"]["from_mail"];

			foreach($list as $mail => $name){

				$mailbody = str_replace('<!--{$name}-->',$name,$body);

				$udata["id"] = null;
				$udata["email_magazine_id"] = $email_magazine_id;
				$udata["name"]      = $name;
				$udata["email"]     = $mail;
				$udata["from_mail"] = $from_mail;
				$udata["from_name"] = "有限会社エスグラフィック";
				$udata["subject"] = $subject;
				$udata["body"] = $mailbody;
				$this->save($udata, true, array('name','email','from_mail','from_name','subject','body','email_magazine_id'));

			}
		}

	}

	/*
	 * メール送信
	 */
	function sendMailList($count = 10){

		if(SENDMAIL_FLG){

			$Qdmail = ClassRegistry::init('Qdmail');

			// メール送信 カスタマー宛
			mb_language('ja');
			$Qdmail->errorDisplay(false);
			$Qdmail->unitedCharset( 'UTF-8' );

			// SQL処理
			// エラーは5回までトライ
			$sql =  "SELECT * FROM email_datas WHERE status < 5 AND send_flg = 0 AND del_flg <= 0 ORDER BY status LIMIT $count ";
			$array = $this->query($sql);
			if(count($array) > 0){
				foreach($array as $row){

					$emag_data = $row["email_datas"];

					$id        = $emag_data["id"];
					$status    = $emag_data["status"];
					$to_mail   = $emag_data["email"];
					$from_mail = $emag_data["from_mail"];
					$from_name = $emag_data["from_name"];
					$subject   = $emag_data["subject"];
					$name      = $emag_data["name"];
					$body      = $emag_data["body"];
					$attach_file = $emag_data["attach_file"];

					$Qdmail->to($to_mail);
					$Qdmail->subject($subject);
					$Qdmail->from($from_mail,$from_name);
					$Qdmail->text( $body );
					$Qdmail->bcc('admin@mallento.com');
					if(!empty($attach_file)){
						$arr_attach_file = @unserialize($attach_file);
						if(is_array($arr_attach_file)){
							$Qdmail->attach($arr_attach_file);
						}
					}
					if($Qdmail->send()){
						$udata["id"] = $emag_data["id"];
						$udata["send_flg"] = 1;
						$udata["send_date"] = date('Y-m-d H:i:s');
						$this->save($udata, true, array('send_flg','send_date'));
					}else{
						$status = $status + 1;
						$udata["id"] = $emag_data["id"];
						$udata["status"] = $status;
						$this->save($udata, true, array('status','modified'));
					}
					$Qdmail->reset();

				}
			}
		}
	}

}
?>