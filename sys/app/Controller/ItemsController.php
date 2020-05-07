<?php
App::uses('FrontAppController','Controller');

class ItemsController extends FrontAppController {

    public $uses = array('Item','FavoriteItem','Member','Browse');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();

        $this->disp_num = $this->Session->read('sesDispNum');
        if ($this->request->is('post') &&
            @$this->request->data['formDispNum']['mode'] == 'changeDispNum') {
            $this->disp_num = $this->request->data['formDispNum']['disp_num'];
            $this->Session->write('sesDispNum',$this->disp_num);
        }
        if(empty($this->disp_num)){
            $this->disp_num = 20;
        }
        $this->set('disp_num',$this->disp_num);

        $this->sort = $this->Session->read('sesSort');
        if ($this->request->is('post') &&
            @$this->request->data['formDispNum']['mode'] == 'sort') {
            $this->sort = $this->request->data['formDispNum']['sort'];
            $this->Session->write('sesSort',$this->sort);
        }
        $this->set('setSort',$this->sort);

    }

    function index(){
        $this->search();
    }
    function sale_rank(){
        $this->loadModel('Item');

        $disp_num = $this->Session->read('sesDispNum');
        if ($this->request->is('post') &&
            $this->request->data['formDispNum']['mode'] == 'changeDispNum') {
            $disp_num = $this->request->data['formDispNum']['disp_num'];
            $this->Session->write('sesDispNum',$disp_num);
        }
        if(empty($disp_num)){
            $disp_num = 20;
        }
        $this->set('disp_num',$disp_num);

        $sort = $this->Session->read('sesSort');
        if ($this->request->is('post') &&
            $this->request->data['formDispNum']['mode'] == 'sort') {
            $sort = $this->request->data['formDispNum']['sort'];
            $this->Session->write('sesSort',$sort);
        }
        $this->set('setSort',$sort);

        $member_id = $this->user_id;
        $pgnum = Hash::get($this->request->query, 'pg');
        $param['member_id'] = $member_id;
        $this->Item->setWhereSortVal($sort);
        $this->Item->setWhereVal($param);
        //$arrData = $this->Item->getPagingEntity($disp_num,$pgnum);

        // 商品ランキング
        $arrSaleRank = $this->Item->getSaleRankAll($disp_num,$pgnum);
        $this->set('pgnum', $pgnum);
        $this->set('arrSaleRank',$arrSaleRank);
        $url = '/item/sale_rank/?';
        $this->set('url', $url);
        $this->render('sale_rank');

    }
    function seller_rank(){

        $this->loadModel('Item');

        $disp_num = $this->Session->read('sesDispNum');
        if ($this->request->is('post') &&
            $this->request->data['formDispNum']['mode'] == 'changeDispNum') {
            $disp_num = $this->request->data['formDispNum']['disp_num'];
            $this->Session->write('sesDispNum',$disp_num);
        }
        if(empty($disp_num)){
            $disp_num = 20;
        }
        $this->set('disp_num',$disp_num);

        $sort = $this->Session->read('sesSort');
        if ($this->request->is('post') &&
            $this->request->data['formDispNum']['mode'] == 'sort') {
            $sort = $this->request->data['formDispNum']['sort'];
            $this->Session->write('sesSort',$sort);
        }
        $this->set('setSort',$sort);

        $member_id = $this->user_id;
        $pgnum = Hash::get($this->request->query, 'pg');
        $param['member_id'] = $member_id;
        $this->Item->setWhereSortVal($sort);
        $this->Item->setWhereVal($param);
        $this->set('pgnum', $pgnum);
        $arrSellerRank = $this->Member->getSellerRankAll($disp_num,$pgnum);
       // print_r($arrSellerRank);exit;
        $this->set('arrSellerRank',$arrSellerRank);
        $url = '/item/seller_rank/?';
        $this->set('url', $url);
        $this->render('seller_rank');

    }
    function newData_rank(){

        $this->loadModel('Item');

        $disp_num = $this->Session->read('sesDispNum');
        if ($this->request->is('post') &&
            $this->request->data['formDispNum']['mode'] == 'changeDispNum') {
            $disp_num = $this->request->data['formDispNum']['disp_num'];
            $this->Session->write('sesDispNum',$disp_num);
        }
        if(empty($disp_num)){
            $disp_num = 20;
        }
        $this->set('disp_num',$disp_num);

        $sort = $this->Session->read('sesSort');
        if ($this->request->is('post') &&
            $this->request->data['formDispNum']['mode'] == 'sort') {
            $sort = $this->request->data['formDispNum']['sort'];
            $this->Session->write('sesSort',$sort);
        }
        $this->set('setSort',$sort);

        $member_id = $this->user_id;
        $pgnum = Hash::get($this->request->query, 'pg');
        $param['member_id'] = $member_id;
        $this->Item->setWhereSortVal($sort);
        $this->Item->setWhereVal($param);
        $arrNewDataRank = $this->Item->getFrontNewDatasAll($disp_num,$pgnum);
        $this->set('pgnum', $pgnum);
        $this->set('arrNewDataRank',$arrNewDataRank);
        $url = '/item/newdata_rank/?';
        $this->set('url', $url);
        $this->render('newdata_rank');

    }
    function pickup_rank(){

        $this->loadModel('Item');

        $disp_num = $this->Session->read('sesDispNum');
        if ($this->request->is('post') &&
            $this->request->data['formDispNum']['mode'] == 'changeDispNum') {
            $disp_num = $this->request->data['formDispNum']['disp_num'];
            $this->Session->write('sesDispNum',$disp_num);
        }
        if(empty($disp_num)){
            $disp_num = 20;
        }
        $this->set('disp_num',$disp_num);

        $sort = $this->Session->read('sesSort');
        if ($this->request->is('post') &&
            $this->request->data['formDispNum']['mode'] == 'sort') {
            $sort = $this->request->data['formDispNum']['sort'];
            $this->Session->write('sesSort',$sort);
        }
        $this->set('setSort',$sort);

        $member_id = $this->user_id;
        $pgnum = Hash::get($this->request->query, 'pg');
        $param['member_id'] = $member_id;
        $this->Item->setWhereSortVal($sort);
        $this->Item->setWhereVal($param);
        $arrPickupRank = $this->Item->getPickUpAll($disp_num,$pgnum);
        $this->set('pgnum', $pgnum);
        $this->set('arrPickupRank',$arrPickupRank);
        $url = '/item/pickup_rank/?';
        $this->set('url', $url);
        $this->render('pickup_rank');

    }
    function category($category = null){
        
        $arrCategory = Configure::read('arrCategory');
        $this->set('arrCategory', $arrCategory);
        
        if(!empty($category)){
            
            // ページ番号取得
            $pgnum = Hash::get($this->request->query, 'pg');

            $where['category_level'] = 'parent';
            $arrCategory = Configure::read('arrCategory');
            foreach($arrCategory as $parent => $row){
                if(isset($row[$category])){
                    $where['category_level'] = 'child';
                }
            }

            $where['category'] = $category;
            $where['selling'] = 1;
            $where['save_flg'] = 1;
            $this->Item->setWhereSortVal($this->sort);
            $this->Item->setWhereVal($where);
            $arrDatas = $this->Item->getPagingEntity($this->disp_num,$pgnum);
            $this->set('arrDatas', $arrDatas);

            $this->set('strCategory', $category);
            $this->set('strH2', $category);

            $url = '/item/category/'.$category.'/';
            $this->set('url', $url);
            $this->render('index');
        }

        // カテゴリ
        if(empty($category)){

            $arrDatas = $this->Item->getEntityByCategory();
            $this->set('arrDatas', $arrDatas);
            
            $this->render('category_index');
        }

    }

    function tag($tag = null){

        $arrTags = $this->Item->geTagList();

        if(!empty($tag)){

            // ページ番号取得
            $pgnum = Hash::get($this->request->query, 'pg');

            $where['tag'] = $tag;
            $this->Item->setWhereSortVal($this->sort);
            $this->Item->setWhereVal($where);
            $arrDatas = $this->Item->getPagingEntity($this->disp_num,$pgnum);
            $this->set('arrDatas', $arrDatas);

            $this->set('strCategory', $tag);
            $this->set('strH2', $tag);

            $url = '/item/category/'.$tag.'/';
            $this->set('url', $url);
            $this->render('index');
        }

        // カテゴリ
        if(empty($tag)){

            $this->set('arrDatas', $arrTags);

            $this->render('tag_index');
        }

    }

    function detail($id){

        $this->loadModel('Review');
        // item/detail
        $member_id = $this->user_id;
        $arrMember = $this->Member->getDetail($member_id);
        if(empty($arrMember)){
            $this->Session->setFlash("商品の閲覧にはログインが必要です。");
            $this->redirect('/mypage/login');
        }
        $this->set('arrMemberInfo',$arrMember);
        //
        $arrData = $this->Item->getDetail($id);
        if(empty($arrData)){
            $this->redirect('/');
        }
        $this->set('arrData',$arrData);

        // この商品を買った人はこんな商品も買っています
        $category = $arrData['Item']['category'];
        $arrLikeData = $this->Item->getEntityLikeCategory($category);
        $this->set('arrLikeData',$arrLikeData);

        // この出品者の人気商品
        $member_id = $arrData['Item']['member_id'];
        $arrSaleRanking = $this->Item->getEntityBySaleCount($member_id);
        $this->set('arrSaleRanking',$arrSaleRanking);

        // 商品ランキング
        $arrSaleRank = $this->Item->getSaleRankEntity();
        $this->set('arrSaleRank',$arrSaleRank);
        
        // 最近閲覧した商品
        $arrBrowse = $this->Browse->getEntityBySes($this->Session->id(),$this->user_id);
        $this->set('arrBrowse', $arrBrowse);

        // 閲覧情報
        $session_id = $this->Session->id();
        $this->Browse->addData($this->user_id,$id,$session_id);
        
        // レビュー表示
        $arrReview = $this->Review->getEntityByItemId($id);
        $this->set('arrReview', $arrReview);

        // レビュー投稿
//        $arrData = $this->Item->getDetail($data['Review']['item_id']);
//        $this->set('arrData',$arrData);

        // アイテムレビュー
        $reviews = $this->Review->findAllByItemId($id);
        $ratingAve = $this->Utility->calculateReviewAve($reviews);
        $this->set('ratingAve', $ratingAve);

        // レビュー可能かどうか？
        $this->loadModel('OrderItem');
        $canWriteReview = $this->OrderItem->canWriteReview($this->user_id, $id) && !$this->Review->isReviewCompleted($this->user_id, $id);
        $this->set('canWriteReview', $canWriteReview);

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($canWriteReview) {
                $data = $this->request->data;
                $data['Review']['member_id'] = @$this->user_id;
                if ($this->Review->saveData($data)) {
                    $redirect_url = '/item/detail/' . $data['Review']['item_id'];
                    $this->redirect($redirect_url);
                } else {
                    $this->Session->setFlash('レビューに失敗しました。', null, array(), 'auth');
                }
            } else {
                $this->Session->setFlash('購入者以外は投稿できません。');
            }
        }

        $authPass = $this->Session->read('password_'.$id);
        if(!empty($arrData['Item']['password']) && !$authPass){
            $this->render('password');
        }

    }

    public function password() {

        $data = $this->request->data;
        $arrData = $this->Item->getDetail($data['Item']['id']);
        $this->set('arrData',$arrData);
        
        // この商品を買った人はこんな商品も買っています
        $category = $arrData['Item']['category'];
        $arrLikeData = $this->Item->getEntityLikeCategory($category);
        $this->set('arrLikeData',$arrLikeData);
        
        // この出品者の人気商品
        $member_id = $arrData['Item']['member_id'];
        $arrSaleRanking = $this->Item->getEntityBySaleCount($member_id);
        $this->set('arrSaleRanking',$arrSaleRanking);

        // 最近閲覧した商品
        $arrBrowse = $this->Browse->getEntityBySes($this->Session->id(),$this->user_id);
        $this->set('arrBrowse', $arrBrowse);
        
        if ($this->request->is('post') || $this->request->is('put')) {
            if($this->Item->authPassword($data)) {
                $this->Session->write('password_'.$data['Item']['id'],true);
                $redirect_url = '/item/detail/'.$data['Item']['id'];
                $this->redirect($redirect_url);
            } else {
                $this->Session->setFlash('パスワードが違います',null,array(),'auth');
            }
        }

    }

    public function review_regist() {

        $this->loadModel('Review');

        $data = $this->request->data;
        $arrData = $this->Item->getDetail($data['Review']['item_id']);
        $this->set('arrData',$arrData);

        if ($this->request->is('post') || $this->request->is('put')) {
            $data['Review']['member_id']= @$this->user_id;
            if($this->Review->saveData($data)) {
                $redirect_url = '/item/detail/'.$data['Review']['item_id'];
                $this->redirect($redirect_url);
            } else {
                $this->Session->setFlash('パスワードが違います',null,array(),'auth');
            }
        }

    }

    function search(){

        // ページ番号取得
        $pgnum = Hash::get($this->request->query, 'pg');

        $keyword = Hash::get($this->request->query, 'keyword');
        $where['keywords'] = $keyword;
        $where['selling'] = 1;
        $where['save_flg'] = 1;
        $this->Item->setWhereSortVal($this->sort);
        $this->Item->setWhereVal($where);
        $arrDatas = $this->Item->getPagingEntity($this->disp_num,$pgnum);
        $this->set('arrDatas', $arrDatas);

        $this->set('strH2', '検索結果');

        $url = '/item/search/?keyword='.$keyword;
        $this->set('url', $url);
        $this->render('index');

    }

    function member($member_id){

        // ページ番号取得
        $pgnum = Hash::get($this->request->query, 'pg');

        $keyword = null;
        $where['member_id'] = $member_id;
        $where['selling'] = 1;
        $where['save_flg'] = 1;
        $this->Item->setWhereSortVal($this->sort);
        $this->Item->setWhereVal($where);
        $arrDatas = $this->Item->getPagingEntity($this->disp_num,$pgnum);
        $this->set('arrDatas', $arrDatas);
        
        $arrMember = $this->Member->getDetail($member_id);
        // 20170730 mod
        $this->set('strH2', $arrMember['Member']['company'].' '.$arrMember['Member']['name'].'さん');

        $url = '/item/member/'.$member_id.'/';
        $this->set('url', $url);
        $this->render('index');

    }

/**
 * お気に入り
 * */
    public function favorite($id) {

        $session_key = null;
        $user_id = @$this->user_id;
        if(empty($user_id)){
            $this->Session->write('redirect_url','/item/favorite/'.$id);
            $this->redirect('/mypage/login');
        }
        $session_key = @$this->Session->id();
        $data['item_id'] = $id;
        $data['member_id'] = $user_id;
        $data['session_key'] = $session_key;
        $cnt1 = $this->FavoriteItem->find('count',array('conditions' => array('item_id' => $id,'session_key' => $session_key)));
        $cnt2 = $this->FavoriteItem->find('count',array('conditions' => array('item_id' => $id,'member_id' => $user_id)));
        $res ='favorite_failure';
        if($cnt1 == 0 && $cnt2 == 0){
            $this->FavoriteItem->save($data);
            $res ='favorite_success';
        }
        $this->redirect('/item/detail/'.$id.'?favres='.$res);
    }

/**
 * ダウンロード
 * */
    function download($item_id){

        $this->autoRender = false;

        $arrData = $this->Item->getDownloadFileInfo($item_id,$this->user_id);
        if(empty($arrData)){
            $this->redirect('/');
        }

        $file_name = $arrData['Item']['file_name'];
        $file_path = $arrData['Item']['file_path'];

        // TODO ファイルが存在しない場合の処理
        if(empty($file_path)){
            $this->redirect('/');
        }

        $this->response->file($file_path);
        $this->response->download($file_name);

    }
}

?>
