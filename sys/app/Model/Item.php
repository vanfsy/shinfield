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

class Item extends AppModel {

    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 60),
                'message' => 'このフィールドは60文字以内です'
            ),
            'minLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'minLength', 5),
                'message' => 'このフィールドは5文字以上です'
            ),
        ),
        'category' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            )
        ),
        'discription' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            )
        ),
        'message' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            )
        ),
        'password' => array(
            'custom' => array(
                'allowEmpty' => true,
                'rule' => array('custom','/^[a-zA-Z0-9\_\+\-\@\.\%]+$/'),
                'message' => 'パスワードは英数字記号を入力ください'
            ),
        ),
        'tag1' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'tag2' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'tag3' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'tag4' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'tag5' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'price' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            ),
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'このフィールドは数値のみです'
            )
        ),
        'fee' => array(
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'このフィールドは数値のみです'
            )
        ),
        'main_image_file_name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            ),
        ),
        'sub_image1_file_name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            ),
        ),
    );

    public $user_validate = array(
        'title' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'minLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'minLength', 5),
                'message' => 'このフィールドは5文字以上です'
            ),
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 60),
                'message' => 'このフィールドは60文字以内です'
            ),
        ),
        'category' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            )
        ),
        'discription' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array( 'maxLength', 1000),
                'message' => 'このフィールドは1000文字以内です'
            )
        ),
        'message' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            ),
            'maxLength' => array(
                'rule' => array( 'maxLength', 500),
                'message' => 'このフィールドは500文字以内です'
            )
        ),
        'password' => array(
            'custom' => array(
                'allowEmpty' => true,
                'rule' => array('custom','/^[a-zA-Z0-9\_\+\-\@\.\%]+$/'),
                'message' => 'パスワードは英数字記号を入力ください'
            ),
            'maxLength' => array(
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            )
        ),
        'tag1' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'tag2' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'tag3' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'tag4' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'tag5' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 20),
                'message' => 'このフィールドは20文字以内です'
            ),
        ),
        'price' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            ),
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'このフィールドは数値のみです'
            ),
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 9),
                'message' => '価格は9桁以内でご入力ください'
            ),
        ),
        'fee' => array(
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'このフィールドは数値のみです'
            )
        ),
        'agree' => array(
            'required' => array(
                'rule' => array('comparison', '==', 1),
                'message' => '誓約できない方は登録いただけません'
            ),
        ),
        'main_image_file_name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            ),
        ),
        'sub_image1_file_name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            ),
        ),
    );

    public $validate_fileup = array(
        'main_image_file_name' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 255),
                'message' => 'このフィールドは255文字以内です'
            ),
        ),
        'sub_image1_file_name' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 255),
                'message' => 'このフィールドは255文字以内です'
            ),
        ),
        'sub_image2_file_name' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 255),
                'message' => 'このフィールドは255文字以内です'
            ),
        ),
        'sub_image3_file_name' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 255),
                'message' => 'このフィールドは255文字以内です'
            ),
        ),
        'sub_image4_file_name' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array( 'maxLength', 255),
                'message' => 'このフィールドは255文字以内です'
            ),
        ),
        'main_image' => self::IMG_EXTENSION_RULES,
        'sub_image1' => self::IMG_EXTENSION_RULES,
        'sub_image2' => self::IMG_EXTENSION_RULES,
        'sub_image3' => self::IMG_EXTENSION_RULES,
        'sub_image4' => self::IMG_EXTENSION_RULES,
    );
/*
    var $hasOne = array('JobSchedule');

    var $belongsTo = array('CompanyProfile' =>
                           array('className' => 'CompanyProfile',
                                 'conditions' => '',
                                 'order' => '',
                                 'foreignKey' => 'company_profile_id'
                           )
                     );
*/

    const IMG_EXTENSION_RULES = array(
        'extension' => array(
            'rule' => array('attachmentContentType', array('image/jpeg', 'image/png', 'image/gif')),
            'message' => '拡張子が不正です。',
        ),
    );

    const IMG_RULES = array(
        'quality' => 100, //   ☆画質指定、デフォルトでは75
        'path' => UPLOAD_PACK_PATH, //'../upload/:model/:id/:basename_:style.:extension',
        'styles' => array(
            'list_l' => '400w', //  ☆リサイズしたいサイズを書く
            //'list_m' => '200x150',//  ☆リサイズしたいサイズを書く
            'list_s' => '160x100', //  ☆リサイズしたいサイズを書く
            'thumb' => '200w'//  ☆リサイズしたいサイズを書く
        )
    );

    var $actsAs = array(
        'UploadPack.Upload' => array(
            'main_image' => self::IMG_RULES,
            'sub_image1' => self::IMG_RULES,
            'sub_image2' => self::IMG_RULES,
            'sub_image3' => self::IMG_RULES,
            'sub_image4' => self::IMG_RULES,
        ),
    );

    public function saveData($data) {

        return $this->save($data);
    }

    public function delData($id) {

        $data['Item']['id'] = $id;
        $data['Item']['del_flg'] = 1;
        return $this->save($data);

    }

    public function delAllData($deleteIds) {
        return $this->updateAll(array('Item.del_flg' => 1), array('Item.id' => $deleteIds));

    }

    public function isImageDel($id,$file_name) {

        if($file_name != 'main_image' && $file_name != 'sub_image1' &&
            $file_name != 'sub_image2' && $file_name != 'sub_image3' && $file_name != 'sub_image4'){
            return false;
        }
        $data['Item']['id'] = $id;
        $data['Item'][$file_name] = null;
        return $this->save($data);

    }

    public function isHaving($id,$member_id) {

        $param = array('conditions'=> array('Item.id' => $id ,'Item.member_id' => $member_id ,'Item.del_flg' => 0));
        $res = $this->find('count', $param);
        if($res > 0){
            return true;
        }else{
            return false;
        }

    }


    public function getScheduleAll($user_id = null) {
        if (empty($user_id)) {
            $user_id = $this->user_id;
        }
        $param = array('conditions'=> array('Item.company_profile_id' => $user_id ,'Item.del_flg' => 0), 'order'=>'');
        $res = $this->find('all', $param);
        return $res;
    }

    public function getFrontNewDatas($limit = 10) {

        $this->bindModel(array(
            'belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                    )
                )
             ),false
        );
        $arrRes = $this->find('all',array('conditions'=>array('Item.del_flg' => 0 ,'Item.save_flg' => 1),
                                            'limit' => $limit,'order' => 'Item.created DESC'));
        return $arrRes;
    }

    public function getFrontNewDatasAll($disp_num,$pgnum) {

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT Item.* ";
        $sql .=  "          , Member.name AS name ";
        $sql .=  "          , Member.company AS company ";
        // $sql .=  "          , (SELECT count(*) FROM order_items WHERE item_id = Item.id ) AS sale_count ";
        $sql .=  "       FROM items Item ";
        $sql .=  " INNER JOIN members  Member ";
        $sql .=  "         ON Member.id = Item.member_id ";
        $sql .=  "      WHERE Item.del_flg = 0 AND Item.save_flg = 1";
        //if(!empty($this->where_value)){
        //    if(isset($this->where_value['member_id']) && !empty($this->where_value['member_id'])){
        //        $sql .=  " AND Member.id = ".$this->where_value['member_id']." ";
        //    }
        //}

        $order =  " ORDER BY Item.created DESC";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString100($sql.$order);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){
            $arrRes["list"][$key] = $row;
            //$arrRes["list"][$key]['Item']['sale_count'] = $row[0]['sale_count'];
        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();
        $arrRes["total"] = $this->getRecordTotal();

        return $arrRes;
    }
    public function authPassword($data) {

        $arrRes = $this->find('count',array('conditions'=>array('id' => $data['Item']['id'] ,
                                                              'password' => $data['Item']['password'] ,
                                                              'del_flg' => 0 ,
                                                              'save_flg' => 1)));
        if($arrRes == 1){
            return true;
        }else{
            return false;
        }
    }

/**
 * タグ一覧
 * */
    public function geTagList($limit = 50) {

        $sql =  "";
        $sql .= " SELECT MAX(tag) AS tag_name ";
        $sql .= "      , count(tag) AS tag_cnt ";
        $sql .= "   FROM ( ";
        $sql .= "   SELECT `tag1` AS tag FROM `items` WHERE tag1 <> '' AND tag1 IS NOT NULL AND del_flg = 0";
        $sql .= "   UNION ALL ";
        $sql .= "   SELECT `tag2` AS tag FROM `items` WHERE tag2 <> '' AND tag2 IS NOT NULL AND del_flg = 0";
        $sql .= "   UNION ALL ";
        $sql .= "   SELECT `tag3` AS tag FROM `items` WHERE tag3 <> '' AND tag3 IS NOT NULL AND del_flg = 0";
        $sql .= "   UNION ALL ";
        $sql .= "   SELECT `tag4` AS tag FROM `items` WHERE tag4 <> '' AND tag4 IS NOT NULL AND del_flg = 0";
        $sql .= "   UNION ALL ";
        $sql .= "   SELECT `tag5` AS tag FROM `items` WHERE tag5 <> '' AND tag5 IS NOT NULL AND del_flg = 0";
        $sql .= " ) TEMP ";
        $sql .= " GROUP BY TEMP.tag ";
        $sql .= " ORDER BY count(TEMP.tag) DESC, TEMP.tag ";
        $sql .= " LIMIT $limit ";
        $array = $this->query($sql);

        $arrRes = null;
        if(!empty($array)){
            foreach($array as $row){
                $arrRes[] = $row[0]['tag_name'];
            }
        }

        return $arrRes;

    }

/**
 * カテゴリ別新着集計（カテゴリ一覧に使用する）
 * */
    public function getEntityByCategory($limit = 4) {

        $arrCategory = Configure::read('arrCategory');
        $this->bindModel(array(
            'belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                    )
                )
             ),false
        );

        foreach($arrCategory as $category => $val){

            $arrRes[$category] = $this->find('all',array('conditions'=>array('Item.del_flg' => 0 ,
                                                              'Item.save_flg' => 1,
                                                              'Item.category LIKE' => '%'.$category.'%',
                                                            ),
                                            'limit' => $limit,'order' => 'Item.created DESC'));

        }

        $this->unbindModel(
            array(
                'belongsTo'=>array('Member'),
            ),false
        );

        return $arrRes;
    }

/**
 * 出品商品 集計
 * */
    public function getSummary($member_id) {

        $arrData = $this->find('all',array('fields' => array('MAX(selling) as selling','count(selling) AS CNT'),
                                          'conditions'=>array('del_flg' => 0 ,
                                                              'member_id' => $member_id,
                                                              'save_flg' => 1),
                                            'group' => 'selling'));

        $arrList = array(1 => 0, 0 => 0, 2 => 0,);
        if(!empty($arrData)){
            foreach($arrData as $row){
                $arrList[$row[0]['selling']] = $row[0]['CNT'];
            }
        }

        $arrSelling = Configure::read('arrSelling');
        $arrRes = null;
        foreach($arrSelling as $k => $val){
            $arrRes[$val] = $arrList[$k];
        }
        return $arrRes;
    }

    public function getDetail($id) {

        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));

        $arrDatas = $this->find('first',array('conditions' => array('Item.id' => $id,'Item.del_flg' => 0,'Item.save_flg' => 1)));
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
        $sql .=  "    SELECT Item.* ";
        $sql .=  "         , Member.name AS name, Member.id AS member_no ";
        $sql .=  "         , Member.company AS company ";
        $sql .=  "         , Member.nickname AS nickname ";
        $sql .=  "      FROM items Item ";
        $sql .=  " LEFT JOIN members Member ";
        $sql .=  "        ON Member.id = Item.member_id ";
        $sql .=  "     WHERE Item.del_flg = 0 ";
//        $sql .=  $where;

        if(!empty($this->where_value)){

            foreach($this->where_value as $key => $val){
                if($key == 'member_no' && !empty($val)){
                    $sql .=  " AND (Member.name LIKE '%$val%' OR Member.id LIKE '%$val%') ";
                }
                if($key == 'member_id' && !empty($val)){
                    $sql .=  " AND member_id = '$val' ";
                }
                if($key == 'id' && !empty($val)){
                   $sql .=  " AND Item.id = '$val' ";
                }
                if($key == 'category' && !empty($val)){
                    $sql .= " AND category LIKE '%$val%' ";
                }
                if($key == 'title' && !empty($val)){
                    $sql .=  " AND title LIKE '%$val%' ";
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
        $order =  " ORDER BY Item.modified DESC ";
        if(!empty($this->sort_value)){
            if($this->sort_value == 'new'){
                $order =  " ORDER BY Item.modified DESC ";
            }
            if($this->sort_value == 'high'){
                $order =  " ORDER BY Item.price DESC ";
            }
            if($this->sort_value == 'low'){
                $order =  " ORDER BY Item.price ASC ";
            }
            if($this->sort_value == 'sale'){
                $order =  " ORDER BY Item.sale_count DESC ";
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
     * 販売者用商品リスト ページング
     */
    function getSalePagingEntity($disp_num,$pgnum){

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT Item.* ";
        $sql .=  "          , Member.name AS name ";
        $sql .=  "          , Member.company AS company ";
        $sql .=  "          , (SELECT count(*) FROM order_items WHERE item_id = Item.id ) AS sale_count ";
        $sql .=  "       FROM items Item ";
        $sql .=  " INNER JOIN members Member ";
        $sql .=  "         ON Member.id = Item.member_id ";
        $sql .=  "      WHERE Item.del_flg = 0 ";
//        $sql .=  $where;

        if(!empty($this->where_value)){
            if(isset($this->where_value['member_id']) && !empty($this->where_value['member_id'])){
                $sql .=  " AND Member.id = ".$this->where_value['member_id']." ";
            }
            if(isset($this->where_value['sale_ym']) && !empty($this->where_value['sale_ym'])){
                $y = substr($this->where_value['sale_ym'],0,4);
                $m = substr($this->where_value['sale_ym'],4,2);
                $start = $y.'-'.$m.'-01 00:00:00';
                $end = date('Y-m-d 23:59:59', mktime(0, 0, 0, $m + 1, 0, $y));
                $sql .=  " AND Item.created >= '$start' AND Item.created <= '$end' ";
            }
        }
        $order =  " ORDER BY Item.modified DESC ";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql.$order);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){
            $arrRes["list"][$key] = $row;
            $arrRes["list"][$key]['Item']['sale_count'] = $row[0]['sale_count'];
        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();
        $arrRes["total"] = $this->getRecordTotal();

        return $arrRes;
    }

    public function getDownloadFileInfo($item_id,$member_id) {

        // 購入済み確認
        App::import('Model','OrderItem');
        $OrderItem = new OrderItem;
        $cnt = $OrderItem->find('count', array(
            'conditions' => array(
                    'OrderItem.item_id =' => $item_id,
                    'OrderItem.member_id =' => $member_id,
                    'OrderItem.del_flg =' => 0,
                    'OrderItem.created >' => date("Y-m-d H:i:s",strtotime("-7 day")), // 7日経過したものはダウンロード不可
                )
            )
        );
        if(empty($cnt)){
            return null;
        }

        // 該当ファイル情報取得
        $arrDatas = $this->find('first',array('conditions' => array('Item.id' => $item_id,'Item.del_flg' => 0,'Item.save_flg' => 1)));
        return $arrDatas;
    }

     /*
     * 売上カウント
      */
    function addSaleCount($id){
        // SQL処理
        $sql = "UPDATE items SET sale_count = sale_count + 1 WHERE id = $id ";
        $this->query($sql);
        $sql = "UPDATE items SET sale_rank = sale_rank + 1 WHERE id = $id ";
        $this->query($sql);

        $arrData = $this->getDetail($id);
        $member_id = $arrData['Item']['member_id'];
        $sql = "UPDATE items SET seller_rank = seller_rank + 1 WHERE member_id = $member_id ";
        $this->query($sql);
    }

     /*
     * ピックアップ
      */
    function getPickUpEntity(){
        
        // SQL処理
        
        /*
        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));

        $conditions = array('Item.del_flg' => 0,'Item.save_flg' => 1,'Item.pickup > ' => 0);
        $arrRes = $this->find('all',array('conditions' => $conditions, 'order' => 'Item.pickup', 'limit' => 4));
        
        return $arrRes;
        */
        
        // ランキング上位10件からランダムに4件取得する
        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));
        $conditions = array('Item.del_flg' => 0,'Item.save_flg' => 1,'Item.sale_rank > ' => 0);
        $arrRes = $this->find('all',array('conditions' => $conditions, 'order' => 'Item.sale_rank', 'limit' => 10));
        
        $randCnt = 4;
        if (count($arrRes) < 4) {
            $randCnt = count($arrRes);
        }
        $rand = @array_rand($arrRes, $randCnt);
        
        $ret = array();
        foreach((array)$rand as $rkey){
            $ret[] = $arrRes[$rkey];
        }
        
        return $ret;
    }
    function getPickUpAll($disp_num,$pgnum){
        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT Item.* ";
        $sql .=  "          , Member.name AS name ";
        $sql .=  "          , Member.company AS company ";
        // $sql .=  "          , (SELECT count(*) FROM order_items WHERE item_id = Item.id ) AS sale_count ";
        $sql .=  "       FROM items Item ";
        $sql .=  " INNER JOIN members  Member ";
        $sql .=  "         ON Member.id = Item.member_id ";
        $sql .=  "      WHERE Item.del_flg = 0 AND Item.save_flg = 1 AND Item.sale_rank >= 0";
        //if(!empty($this->where_value)){
        //    if(isset($this->where_value['member_id']) && !empty($this->where_value['member_id'])){
        //        $sql .=  " AND Member.id = ".$this->where_value['member_id']." ";
        //    }
        //}

        $order =  " ORDER BY Item.sale_rank DESC";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString100($sql.$order);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){
            $arrRes["list"][$key] = $row;
            //$arrRes["list"][$key]['Item']['sale_count'] = $row[0]['sale_count'];
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
     * 商品ランキング
     */
    function getSaleRankEntity(){
        // SQL処理
        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));
        $conditions = array('Item.del_flg' => 0,'Item.save_flg' => 1,'Item.sale_rank > ' => 0);
        $arrRes = $this->find('all',array('conditions' => $conditions, 'order' => 'Item.sale_rank DESC', 'limit' => 4));
        return $arrRes;
    }
    /*
        * 商品ランキング
        */
    function getSaleRankAll($disp_num,$pgnum){
        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT Item.* ";
        $sql .=  "          , Member.name AS name ";
        $sql .=  "          , Member.company AS company ";
       // $sql .=  "          , (SELECT count(*) FROM order_items WHERE item_id = Item.id ) AS sale_count ";
        $sql .=  "       FROM items Item ";
        $sql .=  " INNER JOIN members  Member ";
        $sql .=  "         ON Member.id = Item.member_id ";
        $sql .=  "      WHERE Item.del_flg = 0 AND Item.save_flg = 1 AND Item.sale_rank >= 0";
        //if(!empty($this->where_value)){
        //    if(isset($this->where_value['member_id']) && !empty($this->where_value['member_id'])){
        //        $sql .=  " AND Member.id = ".$this->where_value['member_id']." ";
        //    }
        //}

        $order =  " ORDER BY Item.sale_rank DESC";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString100($sql.$order);
        $array = $this->query($pg_sql);

        $arrRes["list"] = array();
        foreach($array as $key => $row){
            $arrRes["list"][$key] = $row;
            //$arrRes["list"][$key]['Item']['sale_count'] = $row[0]['sale_count'];
        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();
        $arrRes["total"] = $this->getRecordTotal();

        return $arrRes;
/*
        // SQL処理
        $this->bindModel(array('belongsTo' => array(
            'Member' => array(
                'className' => 'Member',
                'foreignKey' => 'member_id'
            ))));
        $conditions = array('Item.del_flg' => 0,'Item.save_flg' => 1,'Item.sale_rank > ' => 0);
        $arrRes = $this->find('all',array('conditions' => $conditions, 'order' => 'Item.sale_rank', 'limit' => 100));
        return $arrRes; */
    }
/*
 * 販売数ランキングリスト
 * */
    public function getEntityBySaleCount($member_id,$limit = 4) {

        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));

        $arrRes = $this->find('all',array('conditions'=>array('Item.del_flg' => 0 ,'Item.save_flg' => 1,'Item.selling' => 1, 'Item.member_id' => $member_id),
                                            'limit' => $limit,'order' => 'Item.sale_count DESC'));
        return $arrRes;
    }

/*
 * 商品リスト
 * */
    public function getEntityByMemberId($member_id,$limit = 4) {

        $arrRes = $this->find('all',array('conditions'=>array('del_flg' => 0 ,'save_flg' => 1,'member_id' => $member_id),
                                            'limit' => $limit,'order' => 'created DESC'));
        return $arrRes;
    }

/*
 * 販売者別商品数
 * */
    public function getCountByMemberId($member_id) {

        $arrRes = $this->find('count',array('conditions'=>array('del_flg' => 0 ,'save_flg' => 1,'Item.selling' => 1, 'member_id' => $member_id),));
        return $arrRes;
    }

/*
 * 同カテゴリ商品リスト
 * */
    public function getEntityLikeCategory($category,$limit = 4) {

        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));

        $arrRes = $this->find('all',array('conditions'=>array('Item.del_flg' => 0 ,'Item.save_flg' => 1,'Item.selling' => 1,'Item.category' => $category),
                                            'limit' => $limit,'order' => 'Item.sale_count DESC'));

        if(count($arrRes) < $limit){
            $limit = $limit - count($arrRes);

            $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));

            $arrRes2 = $this->find('all',array('conditions'=>array('Item.del_flg' => 0 ,'Item.save_flg' => 1,'Item.selling' => 1),
                                            'limit' => $limit,'order' => 'Item.sale_count DESC'));
            foreach($arrRes2 as $val){
                $arrRes[] = $val;
            }
        }
        return $arrRes;

    }

    public function findAllMemberId($member_id) {
        $itemIds = $this->find('all', array(
                'conditions' => array(
                    'Item.member_id' => $member_id
                )
            ));

        if (empty($itemIds)) {
            return null;
        } else {
            return Set::extract('/Item/id', $itemIds);
        }
    }

}
