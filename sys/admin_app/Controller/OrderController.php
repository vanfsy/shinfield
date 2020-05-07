<?php
class OrderController extends AppController {

    public $uses = array('OrderItem');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index(){

        $pgnum = Hash::get($this->request->query, 'pg');

        // 初期設定
        /*$arrSearchParam = array('code' => null,'company_profile_id' => null, 'public_flg' => -1,'phone' => null,'email' => null,
                                'keywords' => null,'area' => array(), 'event_category' => array(),'employment' => array(),
                                'application' => array(),'licence' => array());*/
        
        $arrSearchParam = array('id' => null,);
        
        if(empty($pgnum)){
            $this->Session->write('arrSearchParam',$arrSearchParam);
        }

        if($this->request->is('post')){
            $arrSearchParam = $this->request->data['SearchParam'];
            $this->Session->write('arrSearchParam',$arrSearchParam);
        }else{
            $arrSearchParam = $this->Session->read('arrSearchParam');
        }
        $this->set('arrSearchParam',$arrSearchParam);

        // 条件表示
        $strSearchParam = null;
        $this->set('strSearchParam',$strSearchParam);

        $disp_num = 15;
        $this->OrderItem->setWhereVal($arrSearchParam);
        $arrDatas = $this->OrderItem->getPagingEntity($disp_num,$pgnum);
        $this->set('arrData', $arrDatas);
        $url = '/order/index/?';
        $this->set('url', $url);

    }

    public function detail($id) {

        $arrData = $this->OrderItem->getDetail($id);
        $this->set('arrData', $arrData);

    }

    public function del($id) {
        $rfr = $this->request->referer('/');

        $this->OrderItem->id = $id;
        if (!$this->OrderItem->exists()) {
            throw new NotFoundException(__('購入情報がありません。'));
        }
        if ($this->OrderItem->delete()) {
            $this->Session->setFlash(__('購入情報を削除しました。'));
        } else {
            $this->Session->setFlash(__('購入情報を削除出来ませんでした。'));
        }

        $this->redirect($rfr);
    }

}

?>
