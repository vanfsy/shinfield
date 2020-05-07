<?php
class Follower extends AppModel {

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

    function addData($member_id,$follow_member_id){

        $cnt = $this->find('count',array('conditions' => array(
                'member_id' => $member_id,
                'follow_member_id' => $follow_member_id)));

        if(empty($cnt)){
            $data['Follower']['member_id'] = $member_id;
            $data['Follower']['follow_member_id'] = $follow_member_id;
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

/**
 * フォロワー合計
 * */
    public function getSummary($member_id) {

        $count = $this->find('count',array('conditions'=>array('del_flg' => 0 ,
                                                              'member_id' => $member_id,)));

        return $count;
    }
    
    // 
    public function getFollowCount($member_id){
        $count = $this->find('count',array('conditions'=>array('del_flg' => 0 ,
                                                              'member_id' => $member_id,)));

        return $count;
    }
    
    // 
    public function getFollowerCount($member_id){
        $count = $this->find('count',array('conditions'=>array('del_flg' => 0 ,
                                                              'follow_member_id' => $member_id,)));

        return $count;
    }

    /*
     * ページング
     */
    function setWhereVal($param){
        $this->where_value = $param;
    }

    var $sort_value = null;

    /*
     * ソート設定
    */
    function setWhereSortVal($param){
        $this->sort_value = $param;
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
        $sql .=  "       FROM followers Follower ";
        $sql .=  " INNER JOIN members Member ";
        $sql .=  "         ON Member.id = Follower.member_id ";
        $sql .=  "  LEFT JOIN members FollowMember ";
        $sql .=  "         ON FollowMember.id = Follower.follow_member_id ";
        $sql .=  "  LEFT JOIN (SELECT min(follow_member_id) AS fm_i , count(*) AS cnt FROM followers GROUP BY follow_member_id ) F ";
        $sql .=  "         ON F.fm_i = Follower.follow_member_id ";
        $sql .=  "      WHERE Follower.del_flg = 0 ";
        $sql .=  "        AND Member.del_flg = 0 AND Member.status IN (0, 1) ";
        $sql .=  "        AND FollowMember.del_flg = 0 AND FollowMember.status IN (0, 1) ";
//        $sql .=  $where;

        if(!empty($this->where_value)){
            foreach($this->where_value as $key => $val){
                if($key == 'member_id' && !empty($val)){
                    $sql .=  " AND member_id = '$val' ";
                }
                if($key == 'follow_member_id' && !empty($val)){
                    $sql .=  " AND follow_member_id = '$val' ";
                }
            }
        }

        $order =  " ORDER BY Follower.modified DESC ";
        if(!empty($this->sort_value)){
            if($this->sort_value == 'new'){
                $order =  " ORDER BY Follower.modified DESC ";
            }
            if($this->sort_value == 'followerCount'){
                $order =  " ORDER BY F.cnt DESC ";
            }
            if($this->sort_value == 'new'){
                $order =  " ORDER BY FollowMember.name_kana ";
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

}
?>