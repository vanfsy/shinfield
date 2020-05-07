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
class WhatnewsController extends AppController {

    public $uses = array('Whatsnews');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {

        $company_profile_id = null;
        if(!$this->user_role == 'admin'){
            $company_profile_id = $this->user_id;
        }

        if ($this->request->is('post') or $this->request->is('put')) {

            $data = $this->request->data;

            if ($this->Whatsnews->save($data)) {
                $this->Session->setFlash(__('新着情報を更新しました'));
                $this->redirect('/whatnews');
            } else {
                $this->Session->setFlash(__('新着情報が更新出来ませんでした。再度試すか管理者にご確認下さい。'));
            }
        }

        // 求人新着情報
        $arrWhatNews = $this->Whatsnews->getNewEntity();
        $this->set('arrWhatNews',$arrWhatNews);

        $defWhatNews['Whatsnews'] = $this->Whatsnews->getDefFields();
        $this->set('arrDefWhatNews',$defWhatNews);

    }

    public function del($id) {

        if($this->Whatsnews->delData($id)){
            $this->Session->setFlash(__('新着情報を削除しました'));
        }else{
            $this->Session->setFlash(__('新着情報を削除出来ませんでした'));
        }
        $this->redirect('/whatnews');

    }

}
