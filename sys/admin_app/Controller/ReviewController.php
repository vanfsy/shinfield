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
class ReviewController extends AppController {

    public $uses = array('Review');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $pgnum = Hash::get($this->request->query, 'pg');
        $arrSearchParam = array();
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
        $disp_num = 15;
        $this->Review->setWhereVal($arrSearchParam);
        // すでにgetPagingEntityがあったので違う名前で新しくメソッドを追加した
        $arrDatas = $this->Review->getNewPagingEntity($disp_num,$pgnum);
        $this->set('arrData', $arrDatas);
        $url = '/review/index/?';
        $this->set('url', $url);
    }

    public function detail($id) {
        $arrData = $this->Review->findById($id);
        $this->set('arrData', $arrData);
    }

    public function edit($id) {
        if ($this->request->is('post') or $this->request->is('put')) {
            if ($this->Review->save($this->request->data['Review'])) {
                $this->Session->setFlash(__('レビュー情報を更新しました'));
                $this->redirect('/review/detail/'.$id);
            } else {
                $this->Session->setFlash(__('レビュー情報を更新出来ませんでした。入力に間違いがあります。'));
            }
        }
        $arrData = $this->Review->findById($id);
        $this->set('arrData', $arrData);
    }

    public function delete($id) {
        $rfr = $this->request->referer('/');

        $this->Review->id = $id;
        if (!$this->Review->exists()) {
            throw new NotFoundException(__('レビューがありません。'));
        }
        if ($this->Review->delete()) {
            $this->Session->setFlash(__("レビューを削除しました。"));
        } else {
            $this->Session->setFlash(__("レビューを削除出来ませんでした。"));
        }

        $this->redirect($rfr);
    }
}
