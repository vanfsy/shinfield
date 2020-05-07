<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

class Whatsnews extends AppModel {

    public function getNewEntity() {

        $arrWhatNews = $this->find('all',array('conditions' => array('del_flg' => 0),'order' => 'comment_date DESC'));
        return $arrWhatNews;

    }

    public function delData($id) {

        $data['Whatsnews']['id'] = $id;
        $data['Whatsnews']['del_flg'] = 1;
        return $this->save($data);

    }

}
