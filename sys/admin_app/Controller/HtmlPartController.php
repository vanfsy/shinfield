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
class HtmlPartController extends AppController {

    public $uses = array('HtmlPart');

    public function index() {
        $arrParts = $this->HtmlPart->getListParts();
        if ($this->Session->check("HtmlPartUpdateData")) {
            $reqData = $this->Session->read("HtmlPartUpdateData");
            if (isset($reqData['HtmlPart']['part_type'])) {
                $arrParts[$reqData['HtmlPart']['part_type']]['selected'] = true;
            }
            $this->Session->delete("HtmlPartUpdateData");
        }
        $this->set('arrData', $arrParts);
        $this->set('jsonStr', json_encode($arrParts, JSON_PRETTY_PRINT));
    }

    public function update() {
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->HtmlPart->saveHtml($this->request->data['HtmlPart']['part_type'], $this->request->data['HtmlPart']['part_html'])) {
                $this->Session->setFlash(__('HTMLを更新しました'));
            } else {
                $this->Session->setFlash(__('更新できません。入力にミスがあります。'));
            }
            $this->Session->write("HtmlPartUpdateData", $this->request->data);
        }
        $this->redirect(array('action' => 'index'));
    }

    public function upload_file() {
        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data['HtmlPart']['file'];
            if (isset($data['name'])) {
                $ext = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
                if (in_array($ext, array('png', 'jpg', 'gif'))) {
                    $fPath = WWW_ROOT . DS . ".." . DS . "upload" . DS . "HtmlPart";
                    if (!file_exists($fPath)) {
                        umask(0);
                        mkdir($fPath, 0777);
                    }
                    $fPath = $fPath . DS . $data['name'];
                    if (!file_exists($fPath)) {
                        move_uploaded_file($data['tmp_name'], $fPath);
                        echo "1,/upload/HtmlPart/" . $data['name'];
                    } else {
                        echo "0," . $data['name'] . "は既に存在しています。";
                    }
                } else {
                    echo "0," . $ext . "はアップロードできません。";
                }
            }
        }
    }

}
