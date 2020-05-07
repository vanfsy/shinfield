<?php
App::uses('FrontAppController','Controller');

class MessageController extends FrontAppController {

    public $uses = array('Member','Message','Item');

    function beforeFilter() {

        parent::beforeFilter();
        $this->Auth->allow('concern');

        // 認証者情報
        $this->set('loginMember',$this->Auth->user());

    }

    function index(){

        $member_id = $this->user_id;

        $disp_num = 20;
        $pgnum = 1;
//        $pgnum = Hash::get($this->request->query, 'pg');

        // 受信リスト
        $param['member_id'] = $member_id;
        $param['to_member_id'] = $member_id;
        $param['status'] = 1;
        $this->Message->setWhereVal($param);
        $arrToMessage = $this->Message->getPagingEntity($disp_num,$pgnum);
        $this->set('arrToMessage', $arrToMessage['list']);

        // 送信リスト
        $param = null;
        $param['member_id'] = $member_id;
        $param['from_member_id'] = $member_id;
        $param['status'] = 1;
        $this->Message->setWhereVal($param);
        $arrFromMessage = $this->Message->getPagingEntity($disp_num,$pgnum);
        $this->set('arrFromMessage', $arrFromMessage['list']);

        // 下書きリスト
        $disp_num = 5;
        $param = null;
        $param['member_id'] = $member_id;
        $param['from_member_id'] = $member_id;
        $param['status'] = 0;
        $this->Message->setWhereVal($param);
        $arrDraft = $this->Message->getPagingEntity($disp_num,$pgnum);
        $this->set('arrDraft', $arrDraft['list']);

//        $url = '/mypage/item_list/?';
//        $this->set('url', $url);

    }

/*
 * 新規送信画面
 * */
    function new_message($to_member_id, $from_item_id=null){

        $arrDef = $this->Message->getDefFields();
        $arrData['Message'] = $arrDef;

        $arrToMember = $this->Member->getDetail($to_member_id);
        $this->set('arrToMember',$arrToMember);

        // ●商品問い合わせ時メール送信 2017/12/01 add --START
        // ログインユーザ情報
        $arrMember = $this->Auth->user();

        // 送信時の商品ID
        $arrData['FromItem']['id'] = (!is_null($from_item_id)) ? $from_item_id : '';
        // ●商品問い合わせ時メール送信 2017/12/01 add --END

        // フォーム
        if ($this->request->is('post') or $this->request->is('put')) {

            $data = $this->request->data;
            $data['Message']['from_item_id'] = $from_item_id;

            if($this->Message->isSend($data)){

                // ●商品問い合わせ時メール送信 2017/12/01 add --START
                // 商品情報を取得
                $arrItem = $this->Item->getDetail($data['Message']['from_item_id']);

                // 販売者にメールを送信
                if($data['Message']['status'] == 1){
                    $messageToSeller = Configure::read('mail.messageToSaller');

                    // 本文
                    $viewVars['value']['item_id'] = $arrItem['Item']['id'];
                    $viewVars['value']['item_name'] = $arrItem['Item']['title'];
                    $viewVars['value']['company'] = $arrToMember['Member']['company'];
                    $viewVars['value']['nickname'] = $arrToMember['Member']['nickname'];
                    $viewVars['value']['body'] = $data['Message']['body'];
                    $viewVars['value']['subject'] = $data['Message']['subject'];

                    // メール送信
                    $this->sendMail($messageToSeller, $arrMember['user_id'], $viewVars);
                }
                // ●商品問い合わせ時メール送信 2017/12/01 add --END

                $this->redirect('/message/send_message/'.$data['Message']['status']);
            }else{
                $arrData['Message'] = $data['Message'];
                $this->Session->setFlash('入力エラーがあります。',null,array(),'auth');
            }
        }

        $this->set('arrData',$arrData);

    }

    function send_message($status = null){

        if($status == null){
            $this->redirect('/message');
        }

        $this->set('strStatus',$status);
        // ビュー設定
//        $this->setView('privacy',$this->isSp);
    }

/*
 * 気になる通知
 * **/
    function concern($to_member_id,$item_id){

        $this->autoRender = false;

        $arrItem = $this->Item->getDetail($item_id);

        $arrMember = $this->Auth->user();
        if(empty($this->user_id)){
            return '気になるを通知出来ませんでした'."\n".'ログインしてから改めてお試し下さい';
        }

        if(!$this->Message->isDepliConcern($to_member_id,$this->user_id)){
            return '既に気になる通知は送信済みです';
        }
        if($this->user_id == $to_member_id){
            echo '自分自身に気になる通知は出来ません';
            exit;
        }

        $data['Message']['member_id'] = $this->user_id;
        $data['Message']['to_member_id'] = $to_member_id;
        $data['Message']['from_member_id'] = $this->user_id;

        $data['Message']['subject'] = '気になる通知が届きました';
        $body = $arrMember['name'].'様より気になる通知が届きました。'."\n";
        $body .= '「'.$arrItem['Item']['title'].'」についてご興味を持たれました。'."\n";
        $body .= '商品情報はこちらを参照下さい。'."\n".'http://'.Configure::read('info.domain').'/item/detail/'.$arrItem['Item']['id'];
        $data['Message']['body'] = $body;
        $data['Message']['status'] = 1;

        // ●アラートメール送信 2017/12/01 add --START
        $data['Message']['from_item_id'] = $item_id;
        // ●アラートメール送信 2017/12/01 add --END

        if ($this->Member->isNotice($to_member_id, 'notification_to_saller_flag')) {
            if($this->Message->isSend($data)){

                // ●アラートメール送信 2017/12/01 add --START
                // 販売者に気になるメールを送信
                $notificationToSaller = Configure::read('mail.notificationToSaller');
                $viewVars['value']['item_id'] = $arrItem['Item']['id'];
                $viewVars['value']['item_name'] = $arrItem['Item']['title'];
                $viewVars['value']['company'] = $arrItem['Member']['company'];
                $viewVars['value']['nickname'] = $arrItem['Member']['nickname'];

                // メール送信
                $this->sendMail($notificationToSaller, $arrMember['user_id'], $viewVars);
                // ●アラートメール送信 2017/12/01 add --END

                return '気になるを通知しました';
            }else{
                return '気になるを通知出来ませんでした';
            }
        } else {
            $notificationToSaller = Configure::read('mail.notificationToSaller');
            $viewVars['value']['item_id'] = $arrItem['Item']['id'];
            $viewVars['value']['item_name'] = $arrItem['Item']['title'];
            $viewVars['value']['company'] = $arrItem['Member']['company'];
            $viewVars['value']['nickname'] = $arrItem['Member']['nickname'];
            // メール送信
            $this->sendMail($notificationToSaller, $arrMember['user_id'], $viewVars);
            return '気になるを通知しました';
        }


    }

    function datalist(){

    }

    function sendinglist(){

        $member_id = $this->user_id;

        $disp_num = 20;
        $pgnum = 1;
        $pgnum = Hash::get($this->request->query, 'pg');

        // 受信リスト
        $param['member_id'] = $member_id;
        $param['from_member_id'] = $member_id;
        $param['status'] = 1;
        $this->Message->setWhereVal($param);
        $arrMessage = $this->Message->getPagingEntity($disp_num,$pgnum);
        $this->set('arrMessage', $arrMessage);

        $url = '/message/sendinglist/?';
        $this->set('url', $url);

    }
    function receivedlist(){

        $member_id = $this->user_id;

        $disp_num = 20;
        $pgnum = 1;
        $pgnum = Hash::get($this->request->query, 'pg');

        // 受信リスト
        $param['member_id'] = $member_id;
        $param['to_member_id'] = $member_id;
        $param['status'] = 1;
        $this->Message->setWhereVal($param);
        $arrMessage = $this->Message->getPagingEntity($disp_num,$pgnum);
        $this->set('arrMessage', $arrMessage);

        $url = '/message/receivedlist/?';
        $this->set('url', $url);

    }
    function draftlist(){

        $member_id = $this->user_id;

        $disp_num = 20;
        $pgnum = 1;
        $pgnum = Hash::get($this->request->query, 'pg');

        // 受信リスト
        $param['member_id'] = $member_id;
        $param['from_member_id'] = $member_id;
        $param['status'] = 0;
        $this->Message->setWhereVal($param);
        $arrMessage = $this->Message->getPagingEntity($disp_num,$pgnum);

        $this->set('arrMessage', $arrMessage);

        $url = '/message/draftlist/?';
        $this->set('url', $url);

    }
    function trashlist(){

        $member_id = $this->user_id;

        $disp_num = 20;
        $pgnum = 1;
        $pgnum = Hash::get($this->request->query, 'pg');

        // 受信リスト
        $param['member_id'] = $member_id;
        $param['status'] = 2;
        $this->Message->setWhereVal($param);
        $arrMessage = $this->Message->getPagingEntity($disp_num,$pgnum);
        $this->set('arrMessage', $arrMessage);

        $url = '/message/draftlist/?';
        $this->set('url', $url);

    }

    function message_detail($id){

        $arrData = $this->Message->getDetail($id);

        // 既読処理
        $this->Message->opened($id);

        if($arrData['Message']['status'] == 0){
            $this->redirect('/message/draft/'.$id);
        }
        $arrSendData['Message'] = array('subject' => null,'body' => null);

        // 送信フォーム
        if ($this->request->is('post') or $this->request->is('put')) {

            $data = $this->request->data;
            $this->Message->set($data);
            if($this->Message->validates() && $this->Message->isSend($data)){
                $this->redirect('/message/send_message/'.$data['Message']['status']);
            }else{
                $arrSendData['Message'] = $data['Message'];
                $this->Session->setFlash('入力エラーがあります。',null,array(),'auth');
            }
        }

        $this->set('intMessageId',$id);
        $this->set('arrData',$arrData);
        $this->set('arrSendData',$arrSendData);

    }

/*
 * 下書き送信画面
 * */
    function draft($id){

        $arrData = $this->Message->getDetail($id);

        // 送信フォーム
        if ($this->request->is('post') or $this->request->is('put')) {

            $data = $this->request->data;
            if($this->Message->isSend($data)){
                $this->redirect('/message/send_message/'.$data['Message']['status']);
            }else{
                $arrSendData['Message'] = $data['Message'];
                $this->Session->setFlash('入力エラーがあります。',null,array(),'auth');
            }
        }

        $this->set('intMessageId',$id);
        $this->set('arrData',$arrData);

    }

/*
 * ゴミ箱移動
 * */
    function trash($id){

        $data['Message']['id'] = $id;
        $data['Message']['status'] = 2;
        if($this->Message->save($data)){
            $this->redirect('/message/trashlist');
        }

    }

/*
 * 削除
 * */
    public function del($id) {

        $data['Message']['id'] = $id;
        $data['Message']['del_flg'] = 1;
        if($this->Message->save($data)){
            $this->redirect('/message/trashlist');
        }

    }

}
