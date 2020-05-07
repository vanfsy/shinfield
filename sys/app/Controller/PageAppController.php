<?php
class PageAppController extends AppController {

    /* ver1.2
    var $uses = array();
    var $view = 'Smarty';
    var $components = array('Session','Cookie','Qdmail','Qdsmtp','Utility','Auth','Member','Cart','RequestHandler');
    var $param = array();
    var $fm_data = array();
    var $helpers = array('Form', 'Html', 'Javascript', 'Time','Calendar','Image','FormElements','Cache','Paging');
    var $fm_action = null;
    var $arrCatList = array();
    var $topic_path = array();
    */

    //ver2.4
    public $components = array('Session','Cookie','Utility','Auth','MemberCom','Cart','RequestHandler');
    public $param = array();
    public $fm_data = array();
    public $helpers = array('Form', 'Html', 'Javascript', 'Time','Calendar','Image','FormElements','Cache','Paging');
    public $fm_action = null;
    public $arrCatList = array();
    public $topic_path = array();

    /* ver1.2
    function beforeFilter() {
    */
    //ver2.4
    public function beforeFilter() {

        mb_internal_encoding("UTF-8");
        $const = new _Const;
        $const = $const->getArrConst("publics");
        $this->set('const',$const);

        $this->set('root_url',HOME_URL);
        $this->set('topic_path',$this->getTopicPath());

        global $slug;
        global $url_dir;

        $this->loadModel('Customers');
        $this->loadModel('MerchCategories');
        $this->loadModel('PageContents');
        $this->loadModel('PageContentFunctions');
        $this->loadModel('PressPages');
        $this->loadModel('Constants');
        $this->loadModel('DataLeaves');

        $page_manage = PAGE_MANAGE;
        // 非存在テンプレート処理
        if(!$this->Utility->tempExists() && $page_manage){
            $this->redirect(HOME_URL);
            exit;
        }

        // 非存在スラッグ処理
        if(!$this->PageContents->isExistBySlug($slug,$url_dir) && $page_manage){
            $this->redirect(HOME_URL);
            exit;
        }

        // 非公開処理
        $this->Auth->userModel = 'Members';
        $this->Auth->allow('*');
        $u = $this->Auth->user();
        if($u["Members"]["user_type"] != ("admin" or "div" or "owner" or "user") && $page_manage){
            if(!$this->PageContents->isPublicBySlug($slug)){
                $this->redirect(ERROR_URL);
                exit;
            }
        }

        // ファンクション処理
        $arrMethod = $this->PageContentFunctions->getArrMethodsBySlug($slug);

        $this->functions = array();
        if(count($arrMethod) > 0){
            foreach($arrMethod as $row){
                $className = $row["class_name"];
                $methodName = $row["method_name"];

                $compoName = str_replace("Component","",$className);
                App::import('Component', $compoName);
                if(method_exists($className,$methodName)){
                    $obj = new $className;
                    $this->functions[] = $obj->$methodName();
                }
            }
        }

        // ログインユーザ情報
        $this->arrAuthCustomer = $this->MemberCom->authUser();

        //パンくず取得
        $this->arrTopicPath = $this->PageContents->getTopicPathArray($slug);

        // マスターデータ取得
        $this->arrMasterData = $this->Utility->getMasterData();

        //検索用カテゴリリスト取得
        $this->set('category_id',null);
        $list = $this->MerchCategories->getTreeOptions();
        $this->set('category_tree',$list);

        //サイド用カテゴリリスト取得
        $this->side_categories = $this->MerchCategories->getAllEntity();

        //サイド用ブランドリスト取得
        $this->side_brands = $this->DataLeaves->getEntityByBrand();

        //カート情報取得
        $this->arrCart["list"] = $this->Cart->getList();
        $this->arrCart["sumTotal"] = $this->Cart->getSumTotal();
        $this->arrCart["totalQuantity"] = $this->Cart->getTotalQuantity();
        $this->arrCart["totalTax"] = $this->Cart->getTotalTax();

        //バナー取得
        $this->arrBanners = $this->PressPages->getBannerEntity();

        //Copyright
        $this->crYear = date('Y');

        //店舗情報取得
        $shop_name = $this->Constants->getConstValue("shop_info","店舗名");
        $page = $this->PageContents->getOneEntityBySlug($slug);

        //コンテンツ内パーツ
        $content = null;
        if($page["status"] == "public"){
            $content = $page["content"];
        }
        $this->set('page_contents',$content);

        //ヘッダ情報取得
        $head_image_l = $this->PageContents->getFieldBySlug($slug,"head_image_l");
        $head_image_s = $this->PageContents->getFieldBySlug($slug,"head_image_s");
        $this->set('head_image_l',$head_image_l);
        $this->set('head_image_s',$head_image_s);

        //メタ情報取得
        $this->param["meta_title"]       = $page["meta_title"]." | ".$shop_name;
        $this->param["meta_keywords"]    = $page["meta_keywords"];
        $this->param["meta_description"] = $page["meta_description"];

        // 検索キーワード
        $keywords = 'キーワードで探す';
        if(isset($this->request->query['keywords'])){
            $keywords = $this->request->query['keywords'];
        }
echo $keywords;
        $this->set('search_keyword',$keywords);

        $this->cacheAction = '100 minutes';
//        Cache::clear('page');
        $url = explode("/",$_GET["url"]);
        $get_param = null;
        if(count($url) > 1) $get_param = $url[1];
        $this->set('get_param',$get_param);

        //スマートフォン情報取得
        $this->isSp = false;
        $useragents = array(
                            'iPhone', // Apple iPhone
                            'iPod',   // Apple iPod touch
                            'Android' // Android
                            );
        $pattern = '/'.implode('|', $useragents).'/i';
        $pcsp_mode = @$this->Cookie->read('pcsp_mode');

        // 携帯判別
        mb_internal_encoding("UTF-8");
        $this->char_code = "utf-8";
        if($this->RequestHandler->isMobile()){
//            mb_internal_encoding("SJIS");
//            $this->char_code = "Shift_JIS";
            $this->isSp = true;
        }else if ( !empty($pcsp_mode) && $pcsp_mode == "sp" ) {
            $this->isSp = true;
        }else if ( !empty($pcsp_mode) && $pcsp_mode == "pc" ) {
            $this->isSp = false;
        }else if ( preg_match($pattern, $_SERVER['HTTP_USER_AGENT']) ) {
            $this->isSp = true;
        }

        //iPad判定
        $this->isiPad = false;
        if(preg_match('/iPad/i', $_SERVER['HTTP_USER_AGENT'])){
            $this->isiPad = true;
        }

        //PC判別 PCの場合は強制的にPC/SP切り替えを排除
        $this->isPc = false;
        if(!$this->RequestHandler->isMobile() && !preg_match($pattern, $_SERVER['HTTP_USER_AGENT'])){
            $this->isPc = true;
            $this->isSp = false;
        }

    }

    function beforeRender() {

        foreach($this->functions as $row){
            foreach($row as $name => $val){
                $this->set("$name",$val);
            }
        }
        $this->set("param",$this->param);
        // SMARTY変数へセット
        foreach($this as $name => $val){
            $this->set("$name",$val);
        }
        $this->layout = 'ajax';
    }

    // 非ページ管理の静的ページの処理
    function page($dir,$page,$param1 = null,$param2 = null,$param3 = null) {

        global $render_name;
        $this->render(null,null,$render_name);

    }

    function getTopicPath() {

        global $slug;
        $this->loadModel('PageContents');
        return $this->PageContents->getTopicPath($slug," | ");

    }

    function setView($view_name,$isSp = null,$controller = null) {
        $dir = VIEW_PAGE_APP;
        if($isSp){
            $dir = VIEW_MOBILE_APP;
        }
        if(!empty($controller)){
            $render_name = $dir.$controller.DS.$view_name.".html";
            $this->render(null,null,$render_name);
        }else if($this->viewPath == 'root'){
            $render_name = $dir.$view_name.".html";
            $this->render(null,null,$render_name);
        }else{
            $render_name = $dir.$this->viewPath.DS.$view_name.".html";
            $this->render(null,null,$render_name);
        }

    }

}


?>