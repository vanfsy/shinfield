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

class HtmlPart extends AppModel {

    const HEADER_LOGO = 1;
    const LEFT_TOP = 2;
    const TOP_BANNER = 3;
    const GUIDE_BUY = 4;
    const GUIDE_SALE = 5;
    const GUIDE_FAQ = 6;
    const GUIDE_NEWS = 7;
    const GUIDE_LAW = 8;
    const GUIDE_PRIVACY = 9;
    const GUIDE_ABOUT = 10;
    const GUIDE_CONTACT = 11;
	const PAGE_RULE = 12;

    /*
     * HTMLを保存
     */

    public function saveHtml($type, $html) {
        $arrDatas = $this->find('first', array(
            'conditions' => array('HtmlPart.part_type' => $type),
            'fields' => array('HtmlPart.part_type', 'HtmlPart.id', 'HtmlPart.part_file_flg', 'HtmlPart.part_desc', 'HtmlPart.part_html', 'HtmlPart.part_default')
        ));
        if (!empty($arrDatas)) {
            if ($arrDatas['HtmlPart']['part_file_flg']) {
                $path = WWW_ROOT . DS . ".." . DS . $arrDatas['HtmlPart']['part_html'];
                if (!file_put_contents($path, $html)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                $arrDatas['HtmlPart']['part_html'] = $html;
                return $this->save($arrDatas);
            }
        } else {
            return false;
        }
    }

    /*
     * 区分のHTML
     */

    public function getHtml($type) {
        $html = '';
        $arrDatas = $this->find('first', array('conditions' => array('HtmlPart.part_type' => $type)));
        if (!empty($arrDatas)) {
            $html = trim($arrDatas['HtmlPart']['part_html']);
            if (trim($html) == "") {
                $html = trim($arrDatas['HtmlPart']['part_default']);
            }
        }
        return $html;
    }

    /*
     * HTML一覧を取得する
     */

    public function getListParts() {
        $parts = array();
        $arrData = $this->find('all', array(
            'fields' => array('HtmlPart.part_type', 'HtmlPart.id', 'HtmlPart.part_file_flg', 'HtmlPart.part_desc', 'HtmlPart.part_html', 'HtmlPart.part_default')
        ));
        if (!empty($arrData)) {
            foreach ($arrData as $val) {
                if ($val['HtmlPart']['part_file_flg']) {
                    $path = WWW_ROOT . DS . ".." . DS . $val['HtmlPart']['part_html'];
                    $defPath = WWW_ROOT . DS . ".." . DS . $val['HtmlPart']['part_default'];
                    if (file_exists($path)) {
                        $val['HtmlPart']['part_html'] = file_get_contents($path);
                    } else {
                        $val['HtmlPart']['part_html'] = "";
                    }
                    if (file_exists($defPath)) {
                        $val['HtmlPart']['part_default'] = file_get_contents($defPath);
                    } else {
                        $val['HtmlPart']['part_default'] = "";
                    }
                }
                $parts[$val['HtmlPart']['part_type']] = array(
                    'id' => $val['HtmlPart']['id'],
                    'type' => $val['HtmlPart']['part_type'],
                    'desc' => $val['HtmlPart']['part_desc'],
                    'html' => $val['HtmlPart']['part_html'],
                    'defHtml' => $val['HtmlPart']['part_default'],
                    'selected' => false,
                );
            }
        }
        return $parts;
    }

}
