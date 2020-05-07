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

class Config extends AppModel {

    public $validate = array(
        'name' => array(
            'maxLength' => array(
                'allowEmpty' => true,
                'rule' => array('maxLength', 60),
                'message' => 'このフィールドは60文字以内です'
            ),
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
        'number' => array(
            'allowEmpty' => true,
            'rule' => array('numeric'),
            'message' => 'このフィールドは数値のみです'
        ),
    );

    public function saveData($data) {

        return $this->save($data);
    }

    public function delData($id) {

        $data['Config']['id'] = $id;
        $data['Config']['del_flg'] = 1;
        return $this->save($data);
    }

    /*
     * 設定値取得 CODE
     * */
    public function getValByCode($code) {

        $arrDatas = $this->find('first', array('conditions' => array('Config.code' => $code, 'Config.del_flg' => 0)));
        return $arrDatas;
    }

    /*
     * ファイルからパラメータ取得
     */
    public function getParamsFromFile() {
        $params = array();
        $cfgPath = ROOT . DS . APP_DIR . DS . 'params.cfg';
        if (file_exists($cfgPath)) {
            $cfgContents = base64_decode(file_get_contents($cfgPath));
            $cfgList = explode(PHP_EOL, $cfgContents);
            foreach ($cfgList as $cfgVal) {
                $elm = explode("=", $cfgVal, 2);
                if (count($elm) == 2) {
                    $params[$elm[0]] = trim($elm[1]);
                }
            }
        }
        return $params;
    }

    /*
     * パラメーターの数値を取得する
     */
    public function getConfigFromFile($cfgKey, $defVal) {
        $cfgVal = $defVal;
        $arrDatas = $this->getParamsFromFile();
        if (isset($arrDatas[$cfgKey])) {
            if (is_numeric($arrDatas[$cfgKey])) {
                $cfgVal = intval($arrDatas[$cfgKey]);
            } else {
                $cfgVal = $arrDatas[$cfgKey];
            }
        }
        return $cfgVal;
    }

    /*
     * 最低換金申請金額
     * */
    public function getCashingMinMoney() {

        $arrRes = null;

        $code = 'min_change_money';
        $arrDatas = $this->getValByCode($code);
        if (!empty($arrDatas)) {
            $arrRes = $arrDatas['Config']['number'];
        }

        return $arrRes;
    }

    /*
     * ファイルアップロードの制限サイズ
     * */
    public function getUploadMaxSize() {

        $arrRes = null;

        $code = 'upload_max_size';
        $arrDatas = $this->getValByCode($code);
        if (!empty($arrDatas)) {
            $arrRes = $arrDatas['Config']['number'];
        }

        return $arrRes;
    }

    /*
     * ユーザー登録人数
     */
    public function getMaxMemberCnt() {
        return $this->getConfigFromFile('member_max_cnt', 5);
    }

    /*
     * 出品制限
     */
    public function getMaxItemCnt() {
        return $this->getConfigFromFile('item_max_cnt', 5);
    }

    /*
     * アイテムのアップロードできる拡張子
     */
    public function getItemUploadExtensionList() {
        $extList = $this->getConfigFromFile('item_file_extension', 'jpg,jpeg,gif');
        return explode(",", $extList);
    }

    /*
     * 換金申請 手数料取得
     * */
    public function getCashingFee($money) {
        $code = 'cashing_rate';
        $arrDatas = $this->getValByCode($code);
        if (!empty($arrDatas)) {
            $fee = ($arrDatas['Config']['number'] / 100) * $money;
            return ceil($fee);
        }
        return 0;
    }

    public function getDetail($id) {

        $arrDatas = $this->find('first', array('conditions' => array('Config.id' => $id, 'Config.del_flg' => 0, 'Config.save_flg' => 1)));
        return $arrDatas;
    }

    /*
     * 有効全件リスト取得
     * */
    public function getAllEntity() {

        $arrDatas = $this->find('all', array('conditions' => array('Config.del_flg' => 0)));
        return $arrDatas;
    }

}
