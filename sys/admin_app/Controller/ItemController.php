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

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ItemController extends AppController {

    public $uses = array('Item','DataLeave','Member');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $arrCategory = Configure::read('arrCategory');

        $pgnum = Hash::get($this->request->query, 'pg');

        // 初期設定
        $arrSearchParam = array('member_no' => null, 'id'=> null, 'title' => null,'public_flg' => -1,'keywords' => null,);
        
        if(empty($pgnum)){
            $this->Session->write('arrSearchParam',$arrSearchParam);
        }
        
        // 
        $arrMember = array('0' => '* 選択してください *') + $this->Member->getMemberList('list');
        $this->set('arrMember',$arrMember);

        if($this->request->is('post')){
            $arrSearchParam = $this->request->data['SearchParam'];
            $this->Session->write('arrSearchParam',$arrSearchParam);
        }else{
            $arrSearchParam = $this->Session->read('arrSearchParam');
        }
        $this->set('arrSearchParam',$arrSearchParam);
/*
        if($this->user_role == 'admin'){
            $arrList = $this->User->find('list',array('fields' => array('id','company'),'conditions' => array('del_flg' => 0)));
            $arrList[''] = '全社';
            ksort($arrList);
            $this->set('arrCompany',$arrList);
        }
*/
        // エリア
//        $arrArea = Configure::read('arrArea');

        // 公開・非公開
//        $arrPublicFlg = array('-1' => '未選択','0' => '非公開','1' => '公開');

        // 条件表示
        $strSearchParam = null;
/*
        foreach($arrSearchParam as $key => $val){
            if($key == 'code' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= 'イベントコード：'.$val;
            }
            if($key == 'company_profile_id' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= 'イベント提供会社：'.$arrList[$val];
            }
            if($key == 'public_flg' && $val > 0){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= '公開・非公開：'.$val;
            }
            if($key == 'area' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= 'エリア：';
                foreach($val as $row){
                    if(!empty($strSearchParam)) $strSearchParam .= " ";
                    $strSearchParam .= $arrArea[$row];
                }
            }
            if($key == 'event_category' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= '職種：';
                foreach($val as $row){
                    if(!empty($strSearchParam)) $strSearchParam .= " ";
                    $strSearchParam .= $arrJobCategory[$row];
                }
            }
            if($key == 'licence' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= '免許：';
                foreach($val as $row){
                    if(!empty($strSearchParam)) $strSearchParam .= " ";
                    $strSearchParam .= $arrLicence[$row];
                }
            }
            if($key == 'employment' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= '雇用形態：';
                foreach($val as $row){
                    if(!empty($strSearchParam)) $strSearchParam .= " ";
                    $strSearchParam .= $arrEmployment[$row];
                }
            }
            if($key == 'application' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= '条件：';
                foreach($val as $row){
                    if(!empty($strSearchParam)) $strSearchParam .= " ";
                    $strSearchParam .= $arrApplication[$row];
                }
            }
            if($key == 'keywords' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= 'キーワード：'.$val;
            }
            if($key == 'phone' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= '電話番号：'.$val;
            }
            if($key == 'email' && !empty($val)){
                if(!empty($strSearchParam)) $strSearchParam .= " / ";
                $strSearchParam .= 'メールアドレス：'.$val;
            }
        }

        if($this->user_role != 'admin'){
            $param['company_profile_id'] = $this->user_id;
            $this->Item->setWhereVal($param);
        }
*/
        $this->set('strSearchParam',$strSearchParam);

        $disp_num = 15;
        $arrSearchParam['save_flg'] = 1;
        $this->Item->setWhereVal($arrSearchParam);
        $arrDatas = $this->Item->getPagingEntity($disp_num,$pgnum);
        $this->set('arrItem', $arrDatas);
        $url = '/item/index/?';
        $this->set('url', $url);

    }

    public function detail($id) {

        $arrDatas = $this->Item->getDetail($id);
        $this->set('arrItemDetail', $arrDatas);

   }

    public function edit($id = null) {

        // 新規登録
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->request->data['Item']['mode'] == 'entry') {
                $this->Item->create();
                if ($this->Item->saveData($this->request->data)) {
                    $this->Session->setFlash(__('イベント情報を新規登録しました'));
                    $id = $this->Item->getInsertID();
                    $this->redirect('/item/detail/'.$id);
                } else {
                    $this->Session->setFlash(__('保存できません。入力にミスがあります。'));
                }
            }
        }

        $arrDatas['Item'] = $this->Item->getDefFields();
        $arrDatas['Member'] = $this->Member->getDefFields();

        $arrMember = $this->Member->getMemberList('list');
        $this->set('arrMemberList', $arrMember);

        $mode = 'edit';
        if(!empty($id)){
            // 編集
            $arrDatas = $this->Item->getDetail($id);
        }else{
            // 新規登録
            $mode = 'entry';
        }

        if (empty($arrDatas)) {
            $this->redirect('/item/entry/');
//            throw new NotFoundException(__('Invalid user'));
        }

        if ($this->request->is('post') or $this->request->is('put')) {
            $id = $this->request->data['Item']['id'];
            $data  = $this->request->data;

            // ファイルアップ
            if(isset($data['Item']['item_file']) && !empty($data['Item']['item_file']['size'])){
                $size = null;
                $this->Utility->fileUpTempSave($data['Item']['item_file'],$size);
                $data['Item']['file_name'] = $this->Utility->getFileName();
                $data['Item']['file_path'] = $this->Utility->getFilePath();
                $data['Item']['file_type'] = $this->Utility->getFileType();
                $data['Item']['file_size'] = $this->Utility->getFileSize();
                $data['Item']['file_extension'] = $this->Utility->getFileExtension();
            }

            if ($this->Item->saveData($data)) {
                $this->Session->setFlash(__('商品情報を更新しました'));
                if(empty($id)) $id = $this->Item->getLastInsertID();
                $this->redirect('/item/detail/'.$id);
            } else {
                $this->Session->setFlash(__('商品情報が更新出来ませんでした。入力に間違いがあります。'));
                $arrDatas = $this->request->data;
            }
        }

        $this->set('arrItemDetail', $arrDatas);

        $this->set('mode',$mode);

    }

    public function del($id) {
        $rfr = $this->request->referer('/');
        if($this->Item->delData($id)){
            $this->Session->setFlash(__('商品情報を削除しました'));
        }else{
            $this->Session->setFlash(__('商品情報を削除出来ませんでした'));
        }
        $this->redirect($rfr);
    }

    public function item_all_del() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $request = $this->request->data('Item');
            if (isset($request)) {
                $ids = array();
                foreach ($request as $deleteId) {
                    $ids[] = $deleteId;
                }
                $message = '';
                $result = '';
                $this->Item->begin();
                try {
                    $this->Item->delAllData($ids);
                    $this->Item->commit();
                    $message = '削除処理が完了致しました。';
                    $result = 'alert alert-success';
                } catch (Exception $ex) {
                    // Exception発生時はロールバック
                    $this->Item->rollback();
                    $message = '削除処理が失敗しました。';
                    $result = 'alert alert-danger';
                }
            } else {
                $message = '削除処理が失敗しました。';
                $result = 'alert alert-danger';
            }
            $this->Session->setFlash($message, null, array('class' => $result), 'auth');
            $this->autoRender = true;
            $this->redirect('/item/');
        }
    }

}
