<?php
class FrontAppController extends AppController {

    public $helpers = array('Form', 'Html', 'Time','Calendar','Image','UploadPack.Upload','FormElements','Cache','Paging');

    public $components = array(
        'Session',
        'Cookie',
        'RequestHandler',
        'Utility',
        'Cart',
        'Auth' => array(
            // 認証時の設定
            'authenticate' => array(
                'Form' => array(
                    // 認証時に使用するモデル
                    'userModel' => 'Member',
                    // 認証時に使用するモデルのユーザ名とパスワードの対象カラム
                    'fields' => array('username' => 'user_id' , 'password'=>'password'),
                    'scope' => array( 'Member.role' => 'user'),
                ),
            ),
            // ログイン失敗時に出力するメッセージを設定
            'loginError' => 'パスワードもしくはログインIDをご確認下さい。',
            // ログインしていない場合のメッセージを設定
            'authError' => 'ご利用されるにはログインが必要です。',
            // ログインに使用するアクションを指定
            'loginAction' => array('controller' => 'mypage', 'action' => 'login'),
            // ログイン後のリダイレクト先を指定
            'loginRedirect' => array('controller' => 'mypage', 'action' => 'index'),
            // ログアウト後のリダイレクト先を指定
            'logoutRedirect' => array('controller' => 'mypage', 'action' => 'login'),
        ),
    );

    public $uses = array('Item');

    var $ext = '.html';

    //ver2.4
    public function beforeFilter() {

        $this->set('root_url',FULL_BASE_URL.'/');

        // マスターデータ取得
        $this->arrMasterData = $this->Utility->getMasterData();
        $this->set('arrMasterData',$this->arrMasterData);

        //Copyright
        $this->crYear = date('Y');

        // 検索キーワード
        $keywords = 'キーワードで探す';
        if(isset($this->request->query['keywords'])){
            $keywords = $this->request->query['keywords'];
        }
        $this->set('search_keyword',$keywords);

        $this->set('isMobile', $this->RequestHandler->isMobile());

        $this->auth_user = $this->Auth->user();
        $this->user_id = $this->auth_user['id'];
        $this->login_name = $this->auth_user['user_id'];
        $this->user_role = $this->auth_user['role'];
        $this->set('loginname',$this->login_name);
        $this->set('user_role',$this->user_role);

        if(!empty($this->user_role) && $this->user_role != 'user'){
            $this->Auth->logout();
            $this->redirect('/');
        }

        // ピックアップ情報
        $arrPickUp = $this->Item->getPickUpEntity();
        $this->set('arrPickUp',$arrPickUp);

    }

    // 非ページ管理の静的ページの処理
    function page($dir,$page,$param1 = null,$param2 = null,$param3 = null) {

        global $render_name;
        $this->render(null,null,$render_name);

    }

    function getTopicPath() {
/*
        global $slug;
        $this->loadModel('PageContents');
        return $this->PageContent->getTopicPath($slug," | ");
 *
 */

    }

}


?>