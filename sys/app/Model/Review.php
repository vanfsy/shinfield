<?php
/*
 * Copyright (c) 2019, Mallento JAPAN
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms,
 * with or without modification,
 * are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * Neither the name of the <ORGANIZATION> nor the names of its contributors may
 * be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING,
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

class Review extends AppModel {

    public $validate = array(
        'comment' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 205),
                'message' => 'このフィールドは200文字以内です'
            ),
        ),
        'member_id' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
        'item_id' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
        'rating' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'inList' => array(
                'rule' => array('inList', array('1','2','3','4','5')),
                'message' => '無効な値が選択されています。'
            ),
        ),
    );

    public function saveData($data) {

        return $this->save($data);
    }

    public function delData($id) {

        $data['Review']['id'] = $id;
        $data['Review']['del_flg'] = 1;
        return $this->save($data);

    }

    public function getDetail($id) {

        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));

        $arrDatas = $this->find('first',array('conditions' => array('Review.id' => $id,'Review.del_flg' => 0)));
        return $arrDatas;
    }

    public function getEntityByItemId($item_id) {

        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));

        $arrDatas = $this->find('all',array('conditions' => array('Review.item_id' => $item_id,'Review.del_flg' => 0),
                                                                  'order' => 'Review.created DESC','limit' => 10));
        return $arrDatas;
    }

    /*
     * ソート設定
    */
    function setWhereSortVal($param){
        $this->sort_value = $param;
    }

    /*
     * ページング
     */
    function setWhereVal($param){
        $this->where_value = $param;
    }

    /*
     * ページング
     */
    function getPagingEntity($disp_num,$pgnum){

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "    SELECT Review.* ";
        $sql .=  "         , Member.name AS name ";
        $sql .=  "         , Member.company AS company ";
        $sql .=  "      FROM Reviews Review ";
        $sql .=  " LEFT JOIN members Member ";
        $sql .=  "        ON Member.id = Review.member_id ";
        $sql .=  "     WHERE Review.del_flg = 0 ";
//        $sql .=  $where;

        if(!empty($this->where_value)){

            foreach($this->where_value as $key => $val){
                if($key == 'member_id' && !empty($val)){
                    $sql .=  " AND member_id = '$val' ";
                }
                if($key == 'category' && !empty($val)){
                    $sql .= " AND category LIKE '%$val%' ";
                }
                if($key == 'code' && !empty($val)){
                    $sql .=  " AND code LIKE '%$val%' ";
                }
                if($key == 'phone' && !empty($val)){
                    $sql .=  " AND phone LIKE '%$val%' ";
                }
                if($key == 'email' && !empty($val)){
                    $sql .=  " AND email LIKE '%$val%' ";
                }
                if($key == 'keywords' && !empty($val)){
                    $line = null;
                    $val = str_replace('　',' ',$val);
                    $word = explode(' ',$val);
                    foreach($word as $row){
                        if(!empty($line)) $line .= " OR ";
                        $line .= " title LIKE '%$row%' OR discription LIKE '%$row%' OR ";
                        $line .= " category LIKE '%$row%' OR message LIKE '%$row%' OR review LIKE '%$row%' OR ";
                        $line .= " tag1 LIKE '%$row%' OR tag2 LIKE '%$row%' OR tag3 LIKE '%$row%' OR tag4 LIKE '%$row%' OR tag5 LIKE '%$row%' ";
                    }
                    $sql .=  " AND ($line) ";
                }
                if($key == 'public_flg' && $val > 0){
                    $sql .=  " AND public_flg = '$val' ";
                }
                if($key == 'save_flg' && $val > 0){
                    $sql .=  " AND save_flg = $val ";
                }
                if($key == 'area' && !empty($val)){
                    $line = null;
                    foreach($val as $row){
                        if(!empty($line)) $line .= " OR ";
                        $line .= " area LIKE '%$row%' ";
                    }
                    $sql .=  " AND ($line) ";
                }
                if($key == 'tag' && !empty($val)){
                    $line = " tag1 LIKE '%$val%' OR tag2 LIKE '%$val%' OR tag3 LIKE '%$val%' OR tag4 LIKE '%$val%' OR tag5 LIKE '%$val%' ";
                    $sql .=  " AND ($line) ";
                }
                if($key == 'application' && !empty($val)){
                    $line = null;
                    foreach($val as $row){
                        if(!empty($line)) $line .= " OR ";
                        $line .= " application LIKE '%$row%' ";
                    }
                    $sql .=  " AND ($line) ";
                }
            }
        }
        $order =  " ORDER BY Review.modified DESC ";
        if(!empty($this->sort_value)){
            if($this->sort_value == 'new'){
                $order =  " ORDER BY Review.modified DESC ";
            }
            if($this->sort_value == 'high'){
                $order =  " ORDER BY Review.price DESC ";
            }
            if($this->sort_value == 'low'){
                $order =  " ORDER BY Review.price ASC ";
            }
            if($this->sort_value == 'sale'){
                $order =  " ORDER BY Review.sale_count DESC ";
            }
        }

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql.$order);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){
            $arrRes["list"][$key] = $row;
        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();
        $arrRes["total"] = $this->getRecordTotal();

        return $arrRes;
    }

    /*
     * ページング
     */
    public function getNewPagingEntity($disp_num,$pgnum){

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "    SELECT Review.* ";
        $sql .=  "      FROM reviews Review ";
        $sql .=  "     WHERE Review.del_flg in (0,1) ";
//        $sql .=  $where;

        if(!empty($this->where_value)){

            foreach($this->where_value as $key => $val){
                if($key == 'id' && !empty($val)){
                    $sql .=  " AND id = '$val' ";
                }
                if($key == 'member_id' && !empty($val)){
                    $sql .=  " AND member_id = '$val' ";
                }
                if($key == 'item_id' && !empty($val)){
                    $sql .=  " AND item_id = '$val' ";
                }
                if($key == 'comment' && !empty($val)){
                    $line = null;
                    $val = str_replace('　',' ',$val);
                    $word = explode(' ',$val);
                    foreach($word as $row){
                        if(!empty($line)) $line .= " OR ";
                        $line .= " comment LIKE '%$row%' ";
                    }
                    $sql .=  " AND ($line) ";
                }
                if($key == 'rating' && $val > 0){
                    $sql .=  " AND rating = '$val' ";
                }
                if($key == 'del_flg' && ($val == 0 || $val == 1)){
                    $sql .=  " AND del_flg = '$val' ";
                }
            }
        }
        $order =  " ORDER BY Review.modified DESC ";
        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql.$order);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){
            $arrRes["list"][$key] = $row;
        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();
        $arrRes["total"] = $this->getRecordTotal();

        return $arrRes;
    }

    /*
     * レビューしたかどうか？
     */
    public function isReviewCompleted($memberId, $itemId) {
        $arrData = $this->find('first', array(
            'conditions' => array(
                'Review.member_id' => $memberId,
                'Review.item_id' => $itemId,
                'Review.del_flg' => '0'
            )
        ));
        return !empty($arrData);
    }
}
