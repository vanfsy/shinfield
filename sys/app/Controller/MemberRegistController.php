<?php
App::uses('FrontAppController','Controller');

class MemberRegistController extends FrontAppController {

    public $uses = array('Member','Item','Config');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
        $cnt = $this->Member->find('count',array('conditions' => array('Member.del_flg' => 0)));
        $maxCnt = $this->Config->getMaxMemberCnt();
        if ($maxCnt > 0 && $maxCnt <= $cnt) {
            $this->Session->setFlash('トライアル制限を越えています。',null,array(),'auth');
            if ($this->action != 'index') {
                $this->redirect('/member_regist/index');
            }
        }
    }

    function index(){
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->redirect('/member_regist/member_form');
        }
		$this->loadModel('HtmlPart');
		$this->set('HTML_PAGE_RULE', $this->HtmlPart->getHtml(HtmlPart::PAGE_RULE));
    }

    function member_form(){

        $arrData['Member'] = $this->Member->getDefFields();
        $arrData['Member']['email_confirm'] = null;
        $arrData['Member']['password_confirm'] = null;
        $arrData['Member']['gender'] = 'male';
        $arrData['Member']['mailmag_flg'] = 1;
        $arrData['Member']['role'] = 'user';

        $sesMember = $this->Session->read('sesMember');
        if(!empty($sesMember)){
            $arrData = $sesMember;
        }

        if ($this->request->is('post') || $this->request->is('put')) {

            $data = $this->request->data;
            $data['Member']['birthday'] = implode('-',$data['Member']['birthday']);
            $this->Member->set($data);
            if($this->Member->validates()){
                $data['Member']['tmp_key'] = Security::hash(time(), 'sha1', true);
                $this->Session->write('sesMember',$data);
                $this->redirect('/member_regist/confirm');
            } else {
                $arrData = $data;
                $this->Session->setFlash('入力エラーがあります。',null,array(),'auth');
            }
        }
        $this->set('arrData',$arrData);

    }

    function confirm(){

        $data = $this->Session->read('sesMember');
        if ($this->request->is('post') || $this->request->is('put')) {
            if($this->Member->save($data)){
                $this->redirect('/member_regist/complete');
            } else {
                $this->Session->setFlash('入力エラーがあります。',null,array(),'auth');
                $this->redirect('/member_regist/member_form');
            }
        }
        $this->set('arrData',$data);

    }

    function complete(){

        $data = $this->Session->read('sesMember');
        if(!empty($data)){
            // 登録完了メール送信
            $viewVars = array(
                'password' => $data['Member']['password'],
                'mailAddress' => $data['Member']['email'],
                'tmpLink' => 'http://'.Configure::read('info.domain').'/member_regist/authenticate?key=' . $data['Member']['tmp_key']
            );
            $thankYouMail = Configure::read('mail.thankYou');
            $this->sendMail($thankYouMail, $data['Member']['email'], $viewVars);
            $this->Session->write('sesMember',null);
        }else{
            $this->redirect('/member_regist');
        }

    }

    public function authenticate() {
        // hash値のkeyがなかったらエラー
        $key = $this->request->query('key');
        if(empty($key)) {
            $this->Session->setFlash('無効なリンクです。',null,array(),'auth');
            $this->redirect('/mypage/login');
        }

        // hash値でメンバーを検索し存在なければエラー
        $authMember = $this->Member->findByTmpKey($key);
        if(empty($authMember)) {
            $this->Session->setFlash('無効なリンクです。',null,array(),'auth');
            $this->redirect('/mypage/login');
        }

        // 認証フラグを更新する
        $authMember['Member']['status'] = 1;

        $this->Member->id = $authMember['Member']['id'];
        if($this->Member->saveField('status', 1)) {
            $this->Session->setFlash('認証が完了しました。',null,array(),'auth');
        }
        $this->redirect('/mypage/login');
    }

    public function reissue() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Member->validate = $this->Member->validate_reissue_password;
            $this->Member->set($this->request->data);
            if($this->Member->validates()){
                $result = $this->Member->find('first', array('conditions' => array('email' => $this->request->data['Member']['email'])));
                $tmpKey = Security::hash(time(), 'sha1', true);
                $saveData = array('Member' => array(
                    'id' => $result['Member']['id'],
                     'password' => $this->request->data['Member']['password'],
                     'status' => 0,
                     'tmp_key' => $tmpKey
                ));
                $fields = array('password', 'status', 'tmp_key');
                $saveResult = $this->Member->save($saveData, false, $fields);
                if ($saveResult) {
                    $viewVars = array(
                        'mailAddress' => $saveResult['Member']['email'],
                        'password' => $saveResult['Member']['password_confirm'],
                        'tmpLink' => 'http://'.Configure::read('info.domain').'/member_regist/authenticate?key=' . $tmpKey,
                    );
                    $reissuePasswordMail = Configure::read('mail.reissuePassword');
                    $this->sendMail($reissuePasswordMail, $saveResult['Member']['email'], $viewVars);
                    $this->Session->setFlash('パスワードの再発行が成功しました。',null,array(),'auth');
                    $this->redirect('/mypage/login');
                } else {
                    $this->Session->setFlash('パスワードの再発行に失敗しました。',null,array(),'auth');
                }
            } else {
                $this->Session->setFlash('パスワードの再発行に失敗しました。',null,array(),'auth');
            }
        }
    }
}

?>
