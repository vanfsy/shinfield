<?php
class CashingController extends AppController {

    public $uses = array('CashingData', 'Config');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index(){

        $pgnum = Hash::get($this->request->query, 'pg');

        // 初期設定
        $arrSearchParam = array('code' => null,'company_profile_id' => null, 'public_flg' => -1,'phone' => null,'email' => null,
                                'keywords' => null,'area' => array(), 'event_category' => array(),'employment' => array(),
                                'application' => array(),'licence' => array(),'id'=>null,'name'=>null);

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
        $this->CashingData->setWhereVal($arrSearchParam);
        $arrDatas = $this->CashingData->getPagingEntity($disp_num,$pgnum);
        $this->set('arrData', $arrDatas);
        $url = '/cashing/index/?';
        $this->set('url', $url);

    }

    public function detail($id) {

        $arrData = $this->CashingData->getDetail($id);
        $arrData['CashingData']['possible_cashing_min'] = $this->Config->getCashingMinMoney();

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->CashingData->save($this->request->data)) {
                $this->Session->setFlash(__('換金申請を変更しました'));
                $this->redirect('/cashing/detail/'.$id);
            } else {
                $this->Session->setFlash(__('保存できません。入力にミスがあります。'));
            }
        }

        $this->set('arrData', $arrData);

    }

    public function del($id) {
        $rfr = $this->request->referer('/');

        $this->CashingData->id = $id;
        if (!$this->CashingData->exists()) {
            throw new NotFoundException(__('換金情報がありません。'));
        }
        if ($this->CashingData->delete()) {
            $this->Session->setFlash(__('換金情報を削除しました。'));
        } else {
            $this->Session->setFlash(__('換金情報を削除出来ませんでした。'));
        }

        $this->redirect($rfr);
    }

}

?>
