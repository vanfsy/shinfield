<?php
class  OrderPoint extends AppModel {


    // ●ユーザーマイページにポイント購入履歴がないので追加する 2017/12/01 change --START
    public function getEntryByMemberId($member_id) {

        $arrEntry = $this->find('all',array(
                      'conditions' => array('OrderPoint.member_id' => $member_id,
                                          'OrderPoint.status' => 1,
                                          'OrderPoint.del_flg' => 0),
                      'order' => array('OrderPoint.modified DESC'), //並び順を文字列または配列で指定
                    )
                );
        return $arrEntry;
    }

    /*
    public function getEntryByMemberId($member_id) {

        $this->bindModel(array('belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id'
                ))));
        $arrEntry = $this->find('all',array('conditions' => array('OrderPoint.member_id' => $member_id,
                                                                  'Item.del_flg' => 0,
                                                                  'Item.save_flg' => 1,
                                                                  'OrderPoint.del_flg' => 0)));
        return $arrEntry;
    }
    */
    // ●ユーザーマイページにポイント購入履歴がないので追加する 2017/12/01 change --END

    public function getDetail($id) {

        $this->bindModel(array('belongsTo' => array(
                'Member' => array(
                    'className' => 'Member',
                    'foreignKey' => 'member_id'
                ))));

        $arrDatas = $this->find('first',array('conditions' => array('OrderPoint.id' => $id,'OrderPoint.del_flg' => 0)));
        return $arrDatas;
    }

    /*
     * ステータス変更
     */
    public function saveData($data) {

        App::import('Model','Member');
        $Member = new Member;

        $arrOrderPoint = $this->getDetail($data['OrderPoint']['id']);
        $flg = false;

        // 決済完了後の申請中変更は不可
        if($data['OrderPoint']['status'] == 0 && $arrOrderPoint['OrderPoint']['status'] != 0){
            return false;
        }

        // 決済申請
        if($data['OrderPoint']['status'] == 1 && $arrOrderPoint['OrderPoint']['status'] == 0){
            if($this->save($data)){
                $memberData['Member']['id'] = $arrOrderPoint['Member']['id'];
                $point = $arrOrderPoint['OrderPoint']['point'] + $arrOrderPoint['Member']['point'];
                $memberData['Member']['point'] = $point;
                if($Member->save($memberData)){
                    $flg = true;
                }
            }
        }

        // 決済申請意外
        if($data['OrderPoint']['status'] != 1){
            if($this->save($data)){
                $flg = true;
            }
        }
        return $flg;
    }

    /*
     * 販売者用 売上リスト
     */
    function getSaleReport($member_id, $limit = null){

        $this->bindModel(array('belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id'
                ))));

        $arrParam = array('fields' => array('min(OrderPoint.sale_ym) as sale_ym',
                                                              'sum(OrderPoint.total) as total',
                                                              'count(*) as count'),
                                            'conditions' => array('Item.member_id' => $member_id,
                                                                  'Item.del_flg' => 0,
                                                                  'Item.save_flg' => 1,
                                                                  'OrderPoint.del_flg' => 0),
                                            'group' => 'sale_ym',
                                            'order' => 'sale_ym DESC');
        if(!empty($limit)){
            $arrParam['limit'] = $limit;
        }

        $arrEntry = $this->find('all',$arrParam);

        $arrRes = null;
        $maxTotal = 0;
        if(!empty($arrEntry)){
            foreach($arrEntry as $i => $row){
                $arrRes[$i] = $row[0];
                if($row[0]['total'] > $maxTotal) $maxTotal = $row[0]['total'];
            }
        }
        $this->maxTotal = $maxTotal;
        return $arrRes;

    }

    function getMaxTotal(){
        return $this->maxTotal;
    }

    /*
     * 販売者用 月間売上リスト
     */
    function getMonthSaleData($member_id,$ym){

        $this->bindModel(array('belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id'
                ))));
        $arrEntry = $this->find('all',array('fields' => array('min(Item.id) as item_id',
                                                              'min(Item.title) as title',
                                                              'min(Item.selling) as selling',
                                                              'sum(OrderPoint.total) as total',
                                                              'count(Item.id) as count'),
                                            'conditions' => array('Item.member_id' => $member_id,
                                                                  'Item.del_flg' => 0,
                                                                  'Item.save_flg' => 1,
                                                                  'OrderPoint.sale_ym' => $ym,
                                                                  'OrderPoint.del_flg' => 0),
                                            'group' => 'Item.id',
                                            'order' => 'sale_ym DESC'));
        $arrRes = null;
        $maxTotal = 0;
        if(!empty($arrEntry)){
            foreach($arrEntry as $i => $row){
                $arrRes[$i] = $row[0];
                if($row[0]['total'] > $maxTotal) $maxTotal = $row[0]['total'];
            }
        }
        $this->maxTotal = $maxTotal;
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
        $sql .=  "      FROM order_points OrderPoint ";
        $sql .=  " LEFT JOIN members Member ";
        $sql .=  "        ON Member.id = OrderPoint.member_id ";
        $sql .=  "     WHERE OrderPoint.del_flg = 0 ";
//        $sql .=  $where;

        if(!empty($this->where_value)){

            foreach($this->where_value as $key => $val){
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
        $order =  " ORDER BY OrderPoint.modified DESC ";

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
        $sql .=  "       FROM items Item ";
        $sql .=  " INNER JOIN members Member ";
        $sql .=  "         ON Member.id = Item.member_id ";
        $sql .=  "      WHERE Item.del_flg = 0 ";
//        $sql .=  $where;

        if(!empty($this->where_value)){
            if(isset($this->where_value['member_id']) && !empty($this->where_value['member_id'])){

            }
        }
        $order =  " ORDER BY Item.modified DESC ";

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