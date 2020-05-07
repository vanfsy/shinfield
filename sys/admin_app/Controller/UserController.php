<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class UserController extends AppController {

    public $uses = array('Whatsnews','Inquiry','Member', 'Item', 'Review');
    public $components = array('Session','Sendmail');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add','entry','logout','login','entry_verification','entry_complete','entry_verification_complete');
    }

    public function login() {
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Auth->login()) {
                $this->redirect('/dashboard/');
            } else {
                $password = $this->request->data['Member']['password'];
                $this->request->data['Member']['password'] = $password;
                $this->Session->setFlash(MSG_LOGIN_ERR,null,array(),'auth');
            }
        }
    }

    public function logout() {
        $this->redirect($this->Auth->logout());
        $this->redirect('/');
    }

    public function index() {

        // 初期設定
        $arrSearchParam = array('code' => null,'company_profile_id' => null, 'public_flg' => -1,'phone' => null,'email' => null,
                                'keywords' => null,'area' => array(), 'event_category' => array(),'employment' => array(),
                                'application' => array(),'licence' => array());

        if(empty($pgnum)){
            $this->Session->write('arrSearchParam',$arrSearchParam);
        }

        if($this->request->is('post')){
            $arrSearchParam = $this->request->data['SearchParam'];
            $this->Session->write('arrSearchParam',$arrSearchParam);
        }else{
            $arrSearchParam = $this->Session->read('arrSearchParam');
        }
        $this->set('arrSearchParam',$arrSearchParam);

        // 条件表示
        $strSearchParam = null;
        $this->set('strSearchParam',$strSearchParam);

        $pgnum = Hash::get($this->request->query, 'pg');
        $disp_num = 15;
        $this->Member->setWhereVal($arrSearchParam);
        $arrDatas = $this->Member->getPagingEntity($disp_num,$pgnum,'user');
        $this->set('arrMembers', $arrDatas);
        $url = '/user/index/?';
        $this->set('url', $url);

    }

    public function view($id = null) {
        $this->Member->id = $id;
        if (!$this->Member->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->Member->read(null, $id));
    }

    public function add() {
        $this->layout = 'front';
        if ($this->request->is('post')) {
            $this->CompanyProfile->create();
            $this->request->data['CompanyProfile']['password'] = $this->Auth->password($this->request->data['CompanyProfile']['password']);
//            $this->request->data['Member']['role'] = 'general';
            if ($this->CompanyProfile->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->Auth->login();
                $this->redirect('/dashboard/');
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
    }
/*
    public function entry() {
        $this->layout = 'front';
        if ($this->request->is('post')) {
            $this->MemberEntry->create();
            $data['MemberEntry'] = $this->request->data['MemberEntry'];
            $data['MemberEntry']['status'] = 0;
            if ($this->MemberEntry->save($data)) {
                $this->Session->setFlash(MSG_ENTRY_COMPLETE);

                $subject = '[ご登録申請のご確認]ご登録ありがとうございます';
                $to_mail = $data['MemberEntry']['email'];
                $from_mail = 'admin@mallento.com';
                $body = $data['MemberEntry']['person'].'様'."\n";
                $body .= '掲載企業情報にご登録ありがとうございます。'."\n";
                $body .= 'ご登録は以下のアドレスにアクセスいただいて完了いたします。'."\n"."\n";
                $id = $this->MemberEntry->getLastInsertID();
                $arrList = $this->MemberEntry->findById($id);
                $body .= FULL_BASE_URL.'/user/entry_verification/'.$arrList['MemberEntry']['hashkey'];
                if($this->Sendmail->send($body,$subject,$from_mail,$to_mail)){
                }

                $this->redirect('/user/entry_complete');
            } else {
                $this->Session->setFlash('入力エラー',null,array(),'auth');
            }
        }
    }
*/
    public function entry_complete() {
        $this->layout = 'front';
    }
/*
    public function entry_verification($hashkey = null) {
        $this->layout = 'front';
        if(empty($hashkey)){
            $this->redirect('/');
        }
        $arrData = $this->MemberEntry->find('first',array('conditions' => array('hashkey' => $hashkey,'del_flg' => 0)));
        if(!empty($arrData)){
            $userdata['Member']['username'] = $arrData['MemberEntry']['username'];
            $userdata['Member']['password'] = $arrData['MemberEntry']['password'];
            $userdata['Member']['email'] = $arrData['MemberEntry']['email'];
            $userdata['Member']['role'] = 'general';
            $this->Member->save($userdata);
            $user_id = $this->Member->getLastInsertID();
            $userEntryData['MemberEntry']['id'] = $arrData['MemberEntry']['id'];
            $userEntryData['MemberEntry']['del_flg'] = 1;
            $this->MemberEntry->save($userEntryData);
            $companyProfileData['CompanyProfile'] = $arrData['MemberEntry'];
            $companyProfileData['CompanyProfile']['user_id'] = $user_id;
            $this->CompanyProfile->save($companyProfileData);
            $this->request->data['Member'] = $serdata['Member'];
            $this->Auth->login();
            $this->redirect('/user/entry_verification_complete');
        }
    }
*/
    public function entry_verification_complete() {
        $this->layout = 'front';
    }

    public function detail($id = null) {

        $arrDatas = $this->Member->getDetail($id);
        $this->set('arrMemberDetail', $arrDatas);
        
        $itemIds = $this->Item->findAllMemberId($id);
        $reviews = $this->Review->findAllByItemId($itemIds);
        $ratingAve = $this->Utility->calculateReviewAve($reviews);
        $this->set('ratingAve', $ratingAve);

        if ($this->request->is('post') or $this->request->is('put')) {
            $id = $this->request->data['Member']['id'];
            if ($this->Member->chgStatus($this->request->data)) {
                $this->Session->setFlash(__('ステータスを更新しました'));
                $this->redirect('/user/detail/'.$id);
            } else {
                $this->Session->setFlash(__('ステータスを更新出来ませんでした。入力に間違いがあります。'));
                $arrDatas = $this->request->data;
            }
        }

    }

    public function edit($id = null) {

        $minYear = date('Y')-60;
        $maxYear = date('Y')-10;
        $defYear = date('Y')-20;
        $birthdayOption = array(
            'minYear' => $minYear,
            'maxYear' => $maxYear,
            'separator' => array('<p class="datetimeLabel">年</p>','<p class="datetimeLabel">月</p>','<p class="datetimeLabel">日</p>'),
            'default' => array('year' => $defYear,'month' => 1,'day' => 1),
            'class'=>'form-control datetimeStyle',
            'monthNames' => false
        );
        $this->set('birthdayOption', $birthdayOption);

        $arrDatas['Member'] = $this->Member->getDefFields();
        $mode = 'regist';
        if(!empty($id)){
            $arrDatas = $this->Member->getDetail($id);
            $mode = 'edit';
        }
        $this->set('mode',$mode);

        if ($this->request->is('post') or $this->request->is('put')) {

            $id = $this->request->data['Member']['id'];
            if($this->request->data['Member']['role'] == 'user'){
                $this->request->data['Member']['username'] = $this->request->data['Member']['email'];
            }


            $this->Member->validate = $this->Member->validate2;

            if ($this->Member->save($this->request->data)) {
                $this->Session->setFlash(__('ユーザ情報を更新しました'));
                $this->redirect('/user/detail/'.$id);
            } else {
                $this->Session->setFlash(__('ユーザ情報を更新出来ませんでした。入力に間違いがあります。'));
                $arrDatas = $this->request->data;
            }
        }
        $this->set('arrMemberDetail', $arrDatas);

    }

    public function del($id = null) {
        //$this->request->onlyAllow('post');

        $this->Member->id = $id;
        if (!$this->Member->exists()) {
            throw new NotFoundException(__('ユーザーが存在しません。'));
        }
        if ($this->Member->delete()) {
            $this->Session->setFlash(__('ユーザーを削除しました。'));
        } else {
            $this->Session->setFlash(__('ユーザーを削除出来ませんでした。'));
        }
        $this->redirect(array('action' => 'index'));
    }

/*
 * 管理者情報編集
 * */
    public function admin_profile() {

        $arrDatas = $this->Member->getDetail($this->user_id,'admin');
        if ($this->request->is('post') or $this->request->is('put')) {

            $arrData = $this->request->data;
            if(isset($arrData['Member']['password']) && !empty($arrData['Member']['password'])){
                $arrData['Member']['password_confirm'] = $arrData['Member']['password'];
//                $arrData['Member']['password'] = $this->Auth->password($arrData['Member']['password']);
            }else{
                $arrData['Member']['password'] = $arrDatas['Member']['password'];
                $arrData['Member']['password_confirm'] = $arrDatas['Member']['password'];
            }

            if ($this->Member->save($arrData)) {
                $this->Session->setFlash(__('管理者情報を更新しました'));
                $this->redirect('/user/admin_profile/');
            } else {
                $this->Session->setFlash(__('管理者情報を更新出来ませんでした。入力に間違いがあります。'));
                $arrDatas = $this->request->data;
            }
        }
        $this->set('arrMemberDetail', $arrDatas);

    }
}
