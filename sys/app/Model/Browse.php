<?php
class Browse extends AppModel {

    /*
     * 入力値検証
     */
/*
    public $validate = array(
        'subject' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            )
        ),
        'body' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '必須項目です。'
            )
        ),
        'public_flg' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '必須項目です。'
            ),
        ),
        'start_date' => array(
            'date' => array(
                'allowEmpty' => true,
                'rule' => 'date',
                'message' => '日付が正しくありません'
            ),
        ),
        'end_date' => array(
            'date' => array(
                'allowEmpty' => true,
                'rule' => 'date',
                'message' => '日付が正しくありません'
            ),
        ),
        'url' => array(
            'url' => array(
                'allowEmpty' => true,
                'rule' => 'url',
                'message' => 'URLが正しくありません'
            ),
        ),
    );
*/

    /*
     * 詳細情報
     */
    function getOneEntityById($id){

        return $this->findById($id);

    }

    /*
     * リスト
     */
    function saveData($data){

        return $this->save($data);

    }

    function addData($member_id,$item_id,$session_id){

        $cnt = $this->find('count',array('conditions' => array(
                'session_id' => $session_id,
                'member_id' => $member_id,
                'item_id' => $item_id)));

/*
        App::import('Model','Room');
        $Room = new Room();
        $arrRoom = $Room->find('first',array('conditions' => array(
                                        'Room.code' => $code,
                                        'Room.room-number' => $room_number)));
        $room_id = $arrRoom['Room']['id'];
*/

        if(empty($cnt)){
            $data['Browse']['session_id'] = $session_id;
            $data['Browse']['member_id'] = $member_id;
            $data['Browse']['item_id'] = $item_id;
            return $this->save($data);
        }else{
            return false;
        }

    }

    function delData($param,$session_id){

        $arrData = explode('__',$param);
        $code = @$arrData[0];
        $room_number = @$arrData[1];

        $resData = $this->find('first',array('conditions' => array(
                'session_id' => $session_id,
                'code' => $code,
                'room-number' => $room_number)));

        if(!empty($resData)){
            $id = $resData['Favorite']['id'];
            $data['session_id'] = $session_id;
            $data['code'] = $code;
            $data['room-number'] = $room_number;
            return $this->delete($id);
        }else{
            return false;
        }

    }

    function getCountBySes($session_id = null){

        $sql =  "";
        $sql .=  "     SELECT COUNT(Browse.id) AS cnt ";
        $sql .=  "       FROM browses Browse ";
        $sql .=  " INNER JOIN rooms Room ";
        $sql .=  "         ON Browse.code = Room.code ";
        $sql .=  "        AND Browse.`room-number` = Room.`room-number` ";
        $sql .=  "        AND Room.hide_flg = 0 ";
        $sql .=  " INNER JOIN properties Property ";
        $sql .=  "         ON Property.id = Room.property_id ";
        $sql .=  "      WHERE Browse.id > 0 ";
        $sql .=  "        AND Browse.session_id = '$session_id' ";
        $array = $this->query($sql);
        return $array[0][0]['cnt'];
    }

    function getEntityBySes($session_id = null,$member_id = null){

        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM browses Browse ";
        $sql .=  " INNER JOIN items Item ";
        $sql .=  "         ON Browse.item_id = Item.id ";
        $sql .=  "        AND Item.selling = 1 ";
        $sql .=  "        AND Item.save_flg = 1 ";
        $sql .=  "        AND Item.del_flg = 0 ";
        $sql .=  " INNER JOIN members Member ";
        $sql .=  "         ON Member.id = Item.member_id ";
        $sql .=  "      WHERE Browse.id > 0 ";
        $sql .=  "        AND Browse.session_id = '$session_id' ";
        $sql .=  "         OR Browse.member_id = '$member_id' ";
        $sql .=  "   GROUP BY Item.id ";
        $sql .= "LIMIT 12 ";
        $array = $this->query($sql);
        return $array;
    }

    var $search_param;

    function setSearchParam($param){

        $this->search_param = $param;

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
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM browses ";
        $sql .=  " INNER JOIN rooms ";
        $sql .=  "         ON browses.code = rooms.code ";
        $sql .=  "        AND browses.`room-number` = rooms.`room-number` ";
        $sql .=  "        AND rooms.hide_flg = 0 ";
        $sql .=  " INNER JOIN properties ";
        $sql .=  "         ON properties.id = rooms.property_id ";
        $sql .=  "     WHERE browses.id > 0 ";
        if(!empty($this->search_param)){
            if(isset($this->search_param['session_id']) && !empty($this->search_param['session_id'])){
                $val = $this->search_param['session_id'];
                $sql .=  " AND browses.session_id = '$val' ";
            }
        }
        $order = ' ORDER BY browses.`created` DESC';

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql.$order);
        $array = $this->query($pg_sql);
        $curret_count = count($array);
        $arrRes["list"] = $array;

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        //総数
        $arrRes["total"] = $this->getPgTotal();
        $arrRes["pg_total"] = $curret_count;

        $first = 1;
        if($arrRes["current_pg"] > 1){
            $first = ($arrRes["current_pg"] - 1) * $disp_num + 1;
        }
        $arrRes["first"] = $first;

        $last = $first + $curret_count - 1;
        $arrRes["last"] = $last;

        $arr_property = $this->query($sql);
        $arrTemp = null;
        if(!empty($arr_property)){
            foreach($arr_property as $row){
                $arrTemp[$row['properties']['id']] = $row['properties']['name'];
            }
        }
        $arrRes['property_count'] = count($arrTemp);
        $arrRes['room_count'] = $this->getPgTotal();
        return $arrRes;
    }


}
?>