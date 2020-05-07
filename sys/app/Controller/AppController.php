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
/*
    public $helpers = array('Html', 'Form', 'Session', 'UploadPack.Upload', 'Paging');
    public $components = array(
        'Session',
        'Cookie',
        'RequestHandler',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'mypage', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'mypage', 'action' => 'logout'),
            'authError' => 'ログインをして下さい',
            'authenticate' => array(
                'Form' => array(
                'scope' => array( 'Member.status' => 1,'Member.role' => 'user','Member.del_flg' => 0)
                )
            )
        )
    );

    var $ext = '.html';
*/
    public function beforeRender() {
        parent::beforeRender();
        $this->loadModel('HtmlPart');
        $this->set('HTML_HEADER_LOGO', $this->HtmlPart->getHtml(HtmlPart::HEADER_LOGO));
        $this->set('HTML_LEFT_TOP', $this->HtmlPart->getHtml(HtmlPart::LEFT_TOP));
    }
}
