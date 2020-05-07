<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $helpers = array('Form', 'Html', 'Time','UploadPack.Upload','Cache','Paging');
    public $uses = array('Member');

    public $components = array(
        'Session',
        'Cookie',
        'Utility',
        'RequestHandler',
        'Auth' => array(
            // 認証時の設定
            'authenticate' => array(
                'Form' => array(
                    // 認証時に使用するモデル
                    'userModel' => 'Member',
                    // 認証時に使用するモデルのユーザ名とパスワードの対象カラム
                    'fields' => array('username' => 'user_id' , 'password'=>'password'),
                    'scope' => array( 'Member.role' => 'admin'),
                ),
            ),
            // ログイン失敗時に出力するメッセージを設定
            'loginError' => 'パスワードもしくはログインIDをご確認下さい。',
            // ログインしていない場合のメッセージを設定
            'authError' => 'ご利用されるにはログインが必要です。',
            // ログインに使用するアクションを指定
            'loginAction' => array('controller' => 'user', 'action' => 'login'),
            // ログイン後のリダイレクト先を指定
            'loginRedirect' => array('controller' => 'user', 'action' => 'index'),
            // ログアウト後のリダイレクト先を指定
            'logoutRedirect' => array('controller' => 'user', 'action' => 'login'),
        ),
    );

    var $ext = '.html';

    public function beforeFilter() {

        $this->auth_user = $this->Auth->user();
        $this->user_id = $this->auth_user['id'];
        $this->login_name = $this->auth_user['user_id'];
        $this->user_role = $this->auth_user['role'];
        $this->set('loginname',$this->login_name);
        $this->set('user_role',$this->user_role);
        if(!empty($this->user_role) && $this->user_role != 'admin'){
            $this->Auth->logout();
            $this->redirect('/');
        }
    }

}
