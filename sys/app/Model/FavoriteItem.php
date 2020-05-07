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

class FavoriteItem extends AppModel {
/*
    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
    );
*/

    public function getFavoriteEntryList($user_id,$session_key) {

        $this->bindModel(array('belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id'
                ))));
        $arrEntry = $this->find('all',array('conditions' => array('OR' => array('FavoriteItem.member_id' => $user_id,'FavoriteItem.session_key' => $session_key),
                                                                    'FavoriteItem.del_flg' => 0)));
        return $arrEntry;
    }

    public function getSummary($member_id) {

        $this->bindModel(array('belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id'
                ))));
        $arrEntry = $this->find('count',array('conditions' => array('Item.member_id' => $member_id,
                                                                    'Item.del_flg' => 0,
                                                                    'Item.save_flg' => 1,
                                                                    'FavoriteItem.del_flg' => 0)));
        return $arrEntry;
    }

}
