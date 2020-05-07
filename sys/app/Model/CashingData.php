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

class CashingData extends AppModel {

    public $validate = array(
        'member_id' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
        'money' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'このフィールドは数値のみです'
            ),
            'comparison' => array(
                'rule' => array('comparPossibleCashing'),
                'message' => '換金申請できる金額や最低換金額以上に満ちていません'
            ),
        ),
        'possible_cashing' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'このフィールドは数値のみです'
            ),
        ),

    );

/*
 * 換金申請可能金額検証
 * */
    public function comparPossibleCashing( $check ) {
        $requestMoney = $this->data['CashingData']['money'] + $this->data['CashingData']['fee'];
        if($this->data['CashingData']['possible_cashing'] >= $requestMoney){
            if ($this->data['CashingData']['possible_cashing_min'] <= $requestMoney) {
                return true;
            }
        }
        return false;
    }

    public function saveData($data) {

        return $this->save($data);
    }

    public function delData($id) {

        $data['CashingData']['id'] = $id;
        $data['CashingData']['del_flg'] = 1;
        return $this->save($data);

    }



    public function getDetail($id) {

        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));

        $arrDatas = $this->find('first',array('conditions' => array('CashingData.id' => $id,'CashingData.del_flg' => 0)));
        return $arrDatas;
    }

    public function getDetailByMemberId($member_id) {
/*
        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));
*/
        $arrDatas = $this->find('all',array('fields' => array('SUM(CashingData.money) AS money','SUM(CashingData.fee) AS fee'),
                                            'conditions' => array('CashingData.member_id' => $member_id,
                                                                  'CashingData.status' => 0,
                                                                  'CashingData.del_flg' => 0),
        ));
        $arrRes = null;
        $arrRes['CashingData']['total_money'] = 0;
        $arrRes['CashingData']['total_fee'] = 0;
        if(!empty($arrDatas)){
            $arrRes['CashingData']['total_money'] = $arrDatas[0][0]['money'] + $arrDatas[0][0]['fee'];
            $arrRes['CashingData']['total_fee'] = $arrDatas[0][0]['fee'];
        }
        return $arrRes;
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
        $sql .=  "    SELECT * ";
        $sql .=  "      FROM cashing_datas CashingData ";
        $sql .=  " LEFT JOIN members Member ";
        $sql .=  "        ON Member.id = CashingData.member_id ";
        $sql .=  "     WHERE CashingData.del_flg = 0 ";
//        $sql .=  $where;

        if(!empty($this->where_value)){

            foreach($this->where_value as $key => $val){
                if($key == 'name' && !empty($val)){
                    $sql .=  " AND Member.name LIKE '%$val%' ";
                }
                if($key == 'id' && !empty($val)){
                    $sql .=  " AND CashingData.id = '$val' ";
                }
                if($key == 'member_id' && !empty($val)){
                    $sql .=  " AND member_id = '$val' ";
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
                        $line .= " title LIKE '%$row%' OR description LIKE '%$row%' OR head_description LIKE '%$row%' OR ";
                        $line .= " list_description LIKE '%$row%' OR benefit LIKE '%$row%' OR CompanyProfile.company LIKE '%$row%' OR ";
                        $line .= " CompanyProfile.person LIKE '%$row%' OR CompanyProfile.address LIKE '%$row%' OR CompanyProfile.business_lineup LIKE '%$row%' OR ";
                        $line .= " CompanyProfile.representative LIKE '%$row%' OR CompanyProfile.capital LIKE '%$row%' OR CompanyProfile.employee LIKE '%$row%' ";
                    }
                    $sql .=  " AND ($line) ";
                }
                if($key == 'public_flg' && $val > 0){
                    $sql .=  " AND public_flg = '$val' ";
                }
                if($key == 'area' && !empty($val)){
                    $line = null;
                    foreach($val as $row){
                        if(!empty($line)) $line .= " OR ";
                        $line .= " area LIKE '%$row%' ";
                    }
                    $sql .=  " AND ($line) ";
                }
                if($key == 'job_category' && !empty($val)){
                    $line = null;
                    foreach($val as $row){
                        if(!empty($line)) $line .= " OR ";
                        $line .= " job_category LIKE '%$row%' ";
                    }
                    $sql .=  " AND ($line) ";
                }
                if($key == 'employment' && !empty($val)){
                    $line = null;
                    foreach($val as $row){
                        if(!empty($line)) $line .= " OR ";
                        $line .= " employment LIKE '%$row%' ";
                    }
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
        $order =  " ORDER BY CashingData.modified DESC ";

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
 * 有効全件リスト取得
 * */
    public function getAllEntity() {

        $arrDatas = $this->find('all',array('conditions' => array('CashingData.del_flg' => 0)));
        return $arrDatas;

    }

}