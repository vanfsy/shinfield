<?php
class PointPayController extends AppController {

    public $uses = array('OrderPoint');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index(){

        $pgnum = Hash::get($this->request->query, 'pg');

        // 初期設定
        $arrSearchParam = array('code' => null,'company_profile_id' => null, 'public_flg' => -1,'phone' => null,'email' => null,
                                'keywords' => null,'area' => array(), 'event_category' => array(),'employment' => array(),
                                'application' => array(),'licence' => array());

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
        $this->OrderPoint->setWhereVal($arrSearchParam);
        $arrDatas = $this->OrderPoint->getPagingEntity($disp_num,$pgnum);
        $this->set('arrData', $arrDatas);
        $url = '/point_pay/index/?';
        $this->set('url', $url);

    }

    public function detail($id) {

        $arrData = $this->OrderPoint->getDetail($id);

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->OrderPoint->saveData($this->request->data)) {
                $this->Session->setFlash(__('ステータスを変更しました'));
                $this->redirect('/point_pay/detail/'.$id);
            } else {
                $this->Session->setFlash(__('保存できません。入力にミスがあります。'));
            }
        }

        $this->set('arrData', $arrData);

    }

    public function del($id) {
        $rfr = $this->request->referer('/');

        $this->OrderPoint->id = $id;
        if (!$this->OrderPoint->exists()) {
            throw new NotFoundException(__('決済情報がありません。'));
        }
        if ($this->OrderPoint->delete()) {
            $this->Session->setFlash(__('決済情報を削除しました。'));
        } else {
            $this->Session->setFlash(__('決済情報を削除出来ませんでした。'));
        }

        $this->redirect($rfr);
    }

}

?>
