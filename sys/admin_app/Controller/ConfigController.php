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
class ConfigController extends AppController {

    public $uses = array('Config');

/*    public function beforeFilter() {
        parent::beforeFilter();
    }
*/

    public function index() {

        $user_id = null;
        if(!$this->user_role == 'admin'){
            $user_id = $this->user_id;
        }

        // 設定値情報
        $arrData = null;
        if($this->user_role == 'admin'){
            $arrData = $this->Config->getAllEntity();
        }
        $this->set('arrData',$arrData);

    }

    public function update() {

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Config->create();
            if ($this->Config->saveData($this->request->data)) {
                $this->Session->setFlash(__('設定値を更新しました'));
                $this->redirect('/config/');
                exit;
            } else {
                $this->Session->setFlash(__('更新できません。入力にミスがあります。'));
                $this->render('index');
            }
        }

    }

}
