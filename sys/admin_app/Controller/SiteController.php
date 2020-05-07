<?php
App::uses('AppController', 'Controller');

class SiteController extends AppController {

    public function top(){
        $this->layout = "ajax";
    }

    public function layouts($mode = "list", $param = null, $param2 = null){

        if(empty($mode)) $mode = "list";
        $this->loadModel('PageLayouts');
        $this->loadModel('PageRegions');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","layouts",$mode);

        // 初期値フィールド設定
        $fields = $this->PageLayouts->getFields();
        $this->param["entity"]["page_layouts"] = $fields;
        $this->param["error"]["page_layouts"] = $fields;

        $fields = $this->PageRegions->getFields();
        $this->param["entity"]["page_regions"] = $fields;
        $this->param["error"]["page_regions"] = $fields;
        $this->param["region_id"] = null;
        $this->param["region_name"] = null;
        $this->param["region_code"] = null;
        $this->attention_message = null;

        $layout_id = '-0';
        if(!empty($param)){
            $layout_id = $param;
        }else{
            // 新規登録の場合領域を作成
            $this->arrRegions = $this->PageRegions->getNextRegions();
        }
        $options = $this->PageRegions->getOptions($layout_id);
        $this->param["region_options"] = $options;
        $arrKey = array_keys($options);
        $main_region_id = null;
        if(isset($arrKey[0])){
            $main_region_id = $arrKey[0];
        }
        $this->param["entity"]["page_layouts"]["main_region_id"] = $main_region_id;

        $this->arrRegions = $this->PageRegions->getListEntityByParam($layout_id,"layout_id");
        $this->param["region_mod_url"] = $this->param["region_mod_url"].$layout_id."/";
        $this->param["region_del_url"] = $this->param["region_del_url"].$layout_id."/";
        $this->param["fm_action"] = $this->param["fm_action"].$layout_id."/";

        // 削除処理
        if($mode === "del"){
            $this->PageLayouts->isDelete($this->data);
            $this->redirect($this->param["redirect_url"]);
        }

        // 一覧表示
        if($mode === "list"){
            $this->param["entity"] = $this->PageLayouts->getPagingEntity($this->current_disp_num,$param);
        }

        // 新規登録画面
        if($mode === "add"){
            // セッション破棄
            $this->Session->write($this->param["session_name"],array());
        }

        // 編集更新画面
        if($mode === "mod" && $this->data["mode"] == "region"){

            $data = $this->data;
            $data["page_regions"]["layout_id"] = $data["page_layouts"]["id"];
            if($this->PageRegions->isError($data)){
                $this->PageRegions->isSave($data);
                $redirect_url = HOME_URL.'/admin/site/layouts/mod/'.$param;
                $this->redirect($redirect_url);
            }else{
                $this->param["error"]["page_regions"] = $this->PageRegions->getErrors();
            }

        }

        // 領域削除処理画面
        if($mode === "region_del"){

            $data["del"][0] = $param2;
            $this->PageRegions->isDelete($data);
            $redirect_url = HOME_URL.'/admin/site/layouts/mod/'.$param;
            $this->redirect($redirect_url);

        }

        // 編集更新画面
        if($mode === "mod"){
            // PARAM値からフィールド設定
            $entity = $this->PageLayouts->getOneEntityById($param);
            // エンティティ設定
            $this->param["entity"]["page_layouts"] = $entity["page_layouts"];

            // 編集更新画面
            if(!empty($param2)){
                $this->param["region_id"] = $param2;
                $arrRegions = $this->PageRegions->getOneEntityById($param2);
                $this->param["region_name"] = $arrRegions["page_regions"]["name"];
                $this->param["region_code"] = $arrRegions["page_regions"]["code"];
            }
        }

        // 登録更新処理
        if($mode === "complete"){

            $data = $this->data;
            $mode === "mod";
            if(count($data) > 0){
                if($this->PageLayouts->isError($data)){
                    // 更新
                    $this->PageLayouts->isSave($data);
                    // テンプレート生成
                    $arr = $this->PageLayouts->getListEntityWhere("page_contents");
                    foreach($arr as $row){
                        $id = $row["page_contents"]["id"];
                        $this->Utility->createTemplate($id);
                    }
                    // 領域更新
                    if(empty($this->data["page_layouts"]["id"])){
                        $param = $this->PageLayouts->getLastInsertID();
                        $this->PageRegions->saveNewRegionsById($param);
                    }
                    // 入力画面へ遷移
                    $this->redirect($this->param["target_url"].$param);
                }else{
                    // エラーメッセージ設定
                    $this->param["error"]["page_layouts"] = $this->PageLayouts->getErrors();
                    $this->param["entity"]["page_layouts"] = $data["page_layouts"];
                    $this->attention_message = $this->param["error_attention"];
                }
            }
        }

        // レンダリング
        $this->render($this->param["render"]);

    }

    public function regions($mode = "list", $param = null){

        if(empty($mode)) $mode = "list";
        $this->loadModel('PageRegions');
        $this->loadModel('PageLayouts');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","regions",$mode);

        // 削除処理
        if($mode === "del"){
            $this->PageRegions->isDelete($this->data);
            $this->redirect($this->param["redirect_url"]);
        }

        // 一覧表示
        if($mode === "list"){
            $this->param["entity"] = $this->PageRegions->getPagingEntity($this->current_disp_num,$param);
        }

        // 詳細・確認画面表示
        if($mode === "detail" || $mode === "confirm"){

            // 詳細表示
            if($mode === "detail"){
                $entity = $this->PageRegions->getOneEntityById($param);
            }

            // 入力確認画面
            if($mode === "confirm"){
                $entity = $this->data;
                if(empty($this->data)){
                    $this->redirect($this->param["errorr_redirect"]);
                }
                // セッション値設定
                $this->Session->write($this->param["session_name"],$this->data);
            }

            // エラーチェック
            $redirect_url = '/admin/site/regions/reentry/'.$param;
            $is_err = true;
            if($is_err) $is_err = $this->PageRegions->isError($entity);
            if(!$is_err) $this->redirect($redirect_url);

            // エンティティ設定
            $this->param["entity"] = $entity;

            // セレクト値設定 レイアウト
            $layout_id = $entity["page_regions"]["layout_id"];
            $layout_name = $this->PageLayouts->getOneFieldById($layout_id,'name');
            $this->param["entity"]["page_layouts"]["name"] = $layout_name;
        }

        // 入力画面フィールド設定
        if( $mode === "add" || $mode === "mod" || $mode === "reentry" ){

            // 初期値フィールド設定
            $fields = $this->PageRegions->getFields();
            $this->param["entity"]["page_regions"] = $fields;
            $this->param["error"]["page_regions"] = $fields;

            // 新規登録画面
            if($mode === "add"){
                // セッション破棄
                $this->Session->write($this->param["session_name"],array());
                $this->param["entity"]["page_regions"]["layout_id"] = 1;    // 初期値：1
            }

            // 編集更新画面
            if($mode === "mod"){
                // PARAM値からフィールド設定
                $entity = $this->PageRegions->getOneEntityById($param);
                $this->param["entity"] = $entity;
            }

            // 編集更新画面
            if($mode === "reentry"){
                // セッションを取得
                $data = $this->Session->read($this->param["session_name"]);
                $this->param["entity"] = $data;

                // エラーメッセージ設定
                $this->PageRegions->isError($data);
                $this->param["error"]["page_regions"] = $this->PageRegions->getErrors();
            }

            // セレクトボックスリスト取得
            $this->param["entity"]["layout_options"] = $this->PageLayouts->getOptions();

        }

        // 登録更新処理
        if($mode === "complete"){

            // セッション値を登録・更新
            $data = $this->Session->read($this->param["session_name"]);
            if(count($data) > 0){
                // 登録・更新完了後セッション破棄
                if($this->PageRegions->isSave($data)){
                    $this->param["message"] = "登録・更新が完了しました。";
                    $this->Session->write($this->param["session_name"],array());
                }else{
                    $this->param["message"] = "エラーが発生しました。登録・更新は完了していません。";
                }
            }else{
                // エラーの場合入力画面へ遷移
                $this->redirect($this->param["errorr_redirect"]);
            }
        }

        // レンダリング
        $this->render($this->param["render"]);

    }

    public function parameter($mode = "list", $param = null){

        if(empty($mode)) $mode = "list";
        $this->loadModel('PageParameters');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","parameter",$mode);

        // 削除処理
        if($mode === "del"){
            $this->PageParameters->isDelete($this->data);
            $this->redirect($this->param["redirect_url"]);
        }

        // 一覧表示
        if($mode === "list"){
            $this->param["entity"] = $this->PageParameters->getPagingEntity($this->current_disp_num,$param);
        }

        // 詳細・確認画面表示
        if($mode === "detail" || $mode === "confirm"){

            // 詳細表示
            if($mode === "detail"){
                $entity = $this->PageParameters->getOneEntityById($param);
            }

            // 入力確認画面
            if($mode === "confirm"){
                $entity = $this->data;
                if(empty($this->data)){
                    $this->redirect($this->param["errorr_redirect"]);
                }
                // セッション値設定
                $this->Session->write($this->param["session_name"],$this->data);
            }

            // エラーチェック
            $redirect_url = '/admin/site/parameter/reentry/'.$param;
            $is_err = true;
            if($is_err) $is_err = $this->PageParameters->isError($entity);
            if(!$is_err) $this->redirect($redirect_url);

            // エンティティ設定
            $this->param["entity"] = $entity;

        }

        // 入力画面フィールド設定
        if( $mode === "add" || $mode === "mod" || $mode === "reentry" ){

            // 初期値フィールド設定
            $fields = $this->PageParameters->getFields();
            $this->param["entity"]["page_parameters"] = $fields;
            $this->param["error"]["page_parameters"] = $fields;

            // 新規登録画面
            if($mode === "add"){
                // セッション破棄
                $this->Session->write($this->param["session_name"],array());
            }

            // 編集更新画面
            if($mode === "mod"){
                // PARAM値からフィールド設定
                $entity = $this->PageParameters->getOneEntityById($param);
                $this->param["entity"] = $entity;
            }

            // 編集更新画面
            if($mode === "reentry"){
                // セッションを取得
                $entity = $this->Session->read($this->param["session_name"]);
                $this->param["entity"] = $entity;

                // エラーメッセージ設定
                $this->PageParameters->isError($entity);
                $this->param["error"]["page_parameters"] = $this->PageParameters->getErrors();
            }

        }

        // 登録更新処理
        if($mode === "complete"){

            // セッション値を登録・更新
            $data = $this->Session->read($this->param["session_name"]);
            if(count($data) > 0){
                // 登録・更新完了後セッション破棄
                if($this->PageParameters->isSave($data)){
                    $this->param["message"] = "登録・更新が完了しました。";
                    $this->Session->write($this->param["session_name"],array());
                }else{
                    $this->param["message"] = "エラーが発生しました。登録・更新は完了していません。";
                }
            }else{
                // エラーの場合入力画面へ遷移
                $this->redirect($this->param["errorr_redirect"]);
            }
        }

        // レンダリング
        $this->render($this->param["render"]);

    }

    public function media($mode = "list", $param = null, $pgnum = 0){

        if(empty($mode)) $mode = "list";

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","media",$mode);

        // 削除処理
        if($mode === "del"){
        }

        // 一覧表示
        if($mode === "list"){

        }

        // 詳細・確認画面表示
        if($mode === "detail" || $mode === "confirm"){

            // 詳細表示
            if($mode === "detail"){

            }

            // 入力確認画面
            if($mode === "confirm"){

            }

        }

        // 入力画面フィールド設定
        if( $mode === "add" || $mode === "mod" || $mode === "reentry" ){

            // 新規登録画面
            if($mode === "add"){

            }

            // 編集更新画面
            if($mode === "mod"){

            }

            // 編集更新画面
            if($mode === "reentry"){

            }

            // 初期値フィールド設定
            $this->param["error"] = array("name" => "");    //実装後削除

        }

        // 登録更新処理
        if($mode === "complete"){
                    $this->param["message"] = "登録・更新が完了しました。";
        }

        // レンダリング
        $this->render($this->param["render"]);

    }

    public function design($mode = "list", $param = null){

        if(empty($mode)) $mode = "list";
//        $this->loadModel('PageContents');
//        $this->loadModel('PageContentCategories');
//        $this->loadModel('PageLayouts');
//        $this->loadModel('PageCategories');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","design",$mode);

        // 一覧表示
        if($mode === "list"){
            $this->param["message"] = "現在、準備中です。";
            $this->render('complete');
        }
    }

    public function constants($mode = "list", $param = null){

        if(empty($mode)) $mode = "list";
        $this->loadModel('Constants');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","constants",$mode);

        // 削除処理
        if($mode === "del"){
            $this->Constants->isDelete($this->data);
            $this->redirect($this->param["redirect_url"].$param);
        }

        // 一覧表示
        if($mode === "list"){
            $this->param["entity"] = $this->Constants->getPagingEntity($this->current_disp_num,$param);
        }

        // 詳細・確認画面表示
        if($mode === "detail" || $mode === "confirm"){

            // 詳細表示
            if($mode === "detail"){
                $entity = $this->Constants->getOneEntityById($param);

                // 初期値フィールド設定
                $fields = $this->Constants->getFields();
                $this->param["form"]["constants"] = $fields;
                $this->param["error"]["constants"] = $fields;

                // リスト部分
                $list = $this->Constants->getListEntityWhere($entity["constants"]["const_name"]);
                $this->param["list"] = $list;

            }

            // 入力確認画面
            if($mode === "confirm"){
                $entity = $this->data;
                if(empty($this->data)){
                    $this->redirect($this->param["errorr_redirect"]);
                }

                // エラーチェック
                $redirect_url = '/admin/site/constants/reentry/'.$param;
                $is_err = true;
                if($is_err) $is_err = $this->Constants->isError($entity);
                if(!$is_err) $this->redirect($redirect_url);

                // セッション値設定
                $this->Session->write($this->param["session_name"],$this->data);
            }

            // エンティティ設定
            $this->param["entity"] = $entity;

        }

        // 登録・更新
        if(@$this->data["mode"] === "entry"){
            $this->Constants->isError($this->data);
            if(!$this->Constants->isSave($this->data)){
                $this->param["form"]["constants"] = $this->data["constants"];
                $this->param["error"]["constants"] = $this->Constants->getErrors();
            }else{
                $this->redirect($this->param["redirect_url"].$param);
            }
        }

        // 更新用表示
        if(@$this->data["mode"] === "mod"){
            $param = $this->data["constants"]["id"];
            $entity = $this->Constants->getOneEntityById($param);
            $this->param["form"]["constants"] = $entity["constants"];
        }

        // 入力画面フィールド設定
        if( $mode === "add" || $mode === "mod" || $mode === "reentry" ){

            // 初期値フィールド設定
            $fields = $this->Constants->getFields();
            $this->param["entity"]["constants"] = $fields;
            $this->param["error"]["constants"] = $fields;

            // 新規登録画面
            if($mode === "add"){
                // セッション破棄
                $this->Session->write($this->param["session_name"],array());
            }

            // 編集更新画面
            if($mode === "mod"){
                // PARAM値からフィールド設定
                $entity = $this->Constants->getOneEntityById($param);
                $this->param["entity"] = $entity;
            }

            // 編集更新画面
            if($mode === "reentry"){
                // セッションを取得
                $data = $this->Session->read($this->param["session_name"]);
                $this->param["entity"] = $data;

                // エラーメッセージ設定
                $this->Constants->isError($data);
                $this->param["error"]["constants"] = $this->Constants->getErrors();
            }

        }

        // 登録更新処理
        if($mode === "complete"){

            // セッション値を登録・更新
            $data = $this->Session->read($this->param["session_name"]);
            if(count($data) > 0){
                // 登録・更新完了後セッション破棄
                if($this->Constants->isSave($data)){
                    $this->param["message"] = "登録・更新が完了しました。";
                    $this->Session->write($this->param["session_name"],array());
                }else{
                    $this->param["message"] = "エラーが発生しました。登録・更新は完了していません。";
                }
            }else{
                // エラーの場合入力画面へ遷移
                $this->redirect($this->param["errorr_redirect"]);
            }
        }

        // レンダリング
        $this->render($this->param["render"]);

    }

    public function const_param($mode = "list", $param = null,$param2 = null){

        if(empty($mode)) $mode = "list";
        $this->loadModel('Constants');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","const_param",$mode);

        // 削除処理
        if($mode === "del"){
            $this->Constants->isDelete($this->data);
            $this->redirect($this->param["redirect_url"].$param);
        }

        // 有効・無効の変更処理
        if($mode === "active"){
            $this->Constants->chgActive($param,$param2);
            $this->redirect($this->param["redirect_url"]);
        }

        // 一覧表示
        if($mode === "list"){
            $this->param["entity"] = $this->Constants->getPagingEntity($this->current_disp_num,$param);
        }

        // 詳細
        if($mode === "detail"){

            $entity = $this->Constants->getOneEntityById($param);

            // 初期値フィールド設定
            $fields = $this->Constants->getFields();
            $this->param["form"]["constants"] = $fields;
            $this->param["error"]["constants"] = $fields;

            // リスト部分
            $list = $this->Constants->getListEntityWhere($entity["constants"]["const_name"]);
            $this->param["list"] = $list;

            // エンティティ設定
            $this->param["entity"] = $entity;

        }

        // 登録・更新
        if(@$this->data["mode"] === "entry"){
            $this->Constants->isError($this->data);
            if(!$this->Constants->isSave($this->data)){
                $this->param["form"]["constants"] = $this->data["constants"];
                $this->param["error"]["constants"] = $this->Constants->getErrors();
            }else{
                $this->redirect($this->param["redirect_url"].$param);
            }
        }

        // 更新用表示
        if(@$this->data["mode"] === "mod"){
            $param = $this->data["constants"]["id"];
            $entity = $this->Constants->getOneEntityById($param);
            $this->param["form"]["constants"] = $entity["constants"];
        }

        // 入力画面フィールド設定
/*
        if( $mode === "add" || $mode === "mod" || $mode === "reentry" ){

            // 初期値フィールド設定
            $fields = $this->Constants->getFields();
            $this->param["entity"]["constants"] = $fields;
            $this->param["error"]["constants"] = $fields;

            // 新規登録画面
            if($mode === "add"){
                // セッション破棄
                $this->Session->write($this->param["session_name"],array());
            }

            // 編集更新画面
            if($mode === "mod"){
                // PARAM値からフィールド設定
                $entity = $this->Constants->getOneEntityById($param);
                $this->param["entity"] = $entity;
            }

            // 編集更新画面
            if($mode === "reentry"){
                // セッションを取得
                $data = $this->Session->read($this->param["session_name"]);
                $this->param["entity"] = $data;

                // エラーメッセージ設定
                $this->Constants->isError($data);
                $this->param["error"]["constants"] = $this->Constants->getErrors();
            }

        }

        // 登録更新処理
        if($mode === "complete"){

            // セッション値を登録・更新
            $data = $this->Session->read($this->param["session_name"]);
            if(count($data) > 0){
                // 登録・更新完了後セッション破棄
                if($this->Constants->isSave($data)){
                    $this->param["message"] = "登録・更新が完了しました。";
                    $this->Session->write($this->param["session_name"],array());
                }else{
                    $this->param["message"] = "エラーが発生しました。登録・更新は完了していません。";
                }
            }else{
                // エラーの場合入力画面へ遷移
                $this->redirect($this->param["errorr_redirect"]);
            }
        }
*/
        // レンダリング
        $this->render($this->param["render"]);

    }

    public function menu($mode = "list", $param = null){

        if(empty($mode)) $mode = "list";
        $this->loadModel('MenuLists');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","menu",$mode);

        // 初期値フィールド設定
        $fields = $this->MenuLists->getFields();
        $this->param["entity"]["menu_lists"] = $fields;
        $this->param["error"]["menu_lists"] = $fields;

        // 削除処理
        if($mode === "del"){
            $this->MenuLists->isDelete($this->data);
            $this->redirect($this->param["redirect_url"]);
        }

        // ソート変更
        if(@$this->data["mode"] == "sort"){
            $this->MenuLists->rankSort($this->data);
            $this->redirect('/admin/site/menu/list/'.$param);
        }

        // 一覧表示
        if($mode === "list"){
            $this->param["entity"] = $this->MenuLists->getPagingEntity($this->current_disp_num,$param);
        }

//        // 詳細・確認画面表示
//        if($mode === "detail" || $mode === "confirm"){
//
//            // 詳細表示
//            if($mode === "detail"){
//                $entity = $this->MenuLists->getOneEntityById($param);
//
//                // 初期値フィールド設定
//                $fields = $this->MenuLists->getFields();
//                $this->param["form"]["menu_lists"] = $fields;
//                $this->param["error"]["menu_lists"] = $fields;
//
//            }
//
//            // 入力確認画面
//            if($mode === "confirm"){
//                $this->data["menu_lists"]["all_name"] = $this->data["menu_lists"]["category_name"]." ".$this->data["menu_lists"]["memu_name"];
//                $entity = $this->data;
//                if(empty($this->data)){
//                    $this->redirect($this->param["errorr_redirect"]);
//                }
//
//                // エラーチェック
//                $redirect_url = '/admin/site/menu/reentry/'.$param;
//                $is_err = true;
//                if($is_err) $is_err = $this->MenuLists->isError($entity);
//                if(!$is_err) $this->redirect($redirect_url);
//
//                // セッション値設定
//                $this->Session->write($this->param["session_name"],$this->data);
//            }
//
//            // エンティティ設定
//            $this->param["entity"] = $entity;
//
//        }

        // 新規登録画面
        if($mode === "add"){
            // セッション破棄
            $this->Session->write($this->param["session_name"],array());
        }

        // 編集更新画面
        if($mode === "mod"){
            // PARAM値からフィールド設定
            $entity = $this->MenuLists->getOneEntityById($param);
            $this->param["entity"] = $entity;
        }

//        // 編集更新画面
//        if($mode === "reentry"){
//            // セッションを取得
//            $data = $this->Session->read($this->param["session_name"]);
//            $this->param["entity"] = $data;
//
//            // エラーメッセージ設定
//            $this->MenuLists->isError($data);
//            $this->param["error"]["menu_lists"] = $this->MenuLists->getErrors();
//
//        }

        // 登録更新処理
        if($mode === "complete"){
            if(count($this->data) > 0){
                // 登録・更新
                $this->data["menu_lists"]["all_name"] = $this->data["menu_lists"]["category_name"]." ".$this->data["menu_lists"]["memu_name"];
                if($this->MenuLists->isError($this->data)){
                    $this->MenuLists->isSave($this->data);
                    $this->Session->write("attention_message",$this->param["complete_attention"]);
                    $this->redirect($this->param["mod_redirect"].$param);
                }else{
                    // エラーメッセージ設定
                    $this->param["error"]["menu_lists"] = $this->MenuLists->getErrors();
                    $this->attention_message = $this->param["error_attention"];
                }
            }
            $this->param["entity"] = $this->data;
        }

        // レンダリング
        $this->render($this->param["render"]);

    }

    public function master_h($mode = "list", $param = null, $param2 = null){

        if(empty($mode)) $mode = "list";

        $this->loadModel('DataBranches');

        $this->param = $this->getArrConst("site","master_h",$mode);

        // 所属リスト配列設定
        $this->param["attach_list"] = $this->DataBranches->getOptions();
        $this->param["attach_list"][0] = "未選択";
        ksort($this->param["attach_list"]);

        // 初期値フィールド設定
        $fields = $this->DataBranches->getFields();
        $this->param["entity"]["data_branches"] = $fields;
        $this->param["error"]["data_branches"] = $fields;

        // 削除処理
        if($mode === "del"){
            $this->DataBranches->isDelete($this->data);
            $this->redirect($this->param["redirect_url"]);
        }

        // 一覧表示
        if($mode === "list"){
            $this->param["entity"] = $this->DataBranches->getPagingEntity($this->current_disp_num,$param,$param2);
        }

        // 詳細・確認画面表示
        if($mode === "detail"){

            // エンティティ設定
            $entity = $this->DataBranches->getOneEntityById($param);
            $this->param["entity"] = $entity;

        }

        // 入力確認画面
        if($mode === "confirm"){

            // データチェック
            $entity = $this->data;
            if(empty($this->data)){
                $this->redirect($this->param["errorr_redirect"]);
            }

            // セッション値設定
            $this->Session->write($this->param["session_name"],$this->data);

            // エラーチェック
            $redirect_url = '/admin/site/master_h/reentry/';
            $is_err = true;
            if($is_err) $is_err = $this->DataBranches->isError($entity);
            if(!$is_err) $this->redirect($redirect_url);

            // エンティティ設定
            $this->param["entity"] = $entity;

        }

        // 新規登録画面
        if($mode === "add"){

            // セッション破棄
            $this->Session->write($this->param["session_name"],array());

        }

        // 編集入力画面
        if( $mode === "mod" ){

            $entity = $this->DataBranches->getOneEntityById($param);
            $this->param["entity"] = $entity;

        }

        // 編集更新画面
        if($mode === "reentry"){

            // セッションを取得
            $data = $this->Session->read($this->param["session_name"]);
            $this->param["entity"] = $data;

            // エラーメッセージ設定
            $this->DataBranches->isError($data);
            $this->param["error"]["data_branches"] = $this->DataBranches->getErrors();

        }

        // 登録更新処理
        if($mode === "complete"){

            // セッション値を登録・更新
            $data = $this->Session->read($this->param["session_name"]);
            if(count($data) > 0){
                // 登録・更新完了後セッション破棄
                if($this->DataBranches->isSave($data)){
                    $this->param["message"] = "登録・更新が完了しました。";
                    $this->param["target_url"] = $this->param["target_url"];
                    $this->Session->write($this->param["session_name"],array());
                }else{
                    $this->param["message"] = "エラーが発生しました。登録・更新は完了していません。";
                }
            }else{
                // エラーの場合入力画面へ遷移
                $this->redirect($this->param["errorr_redirect"]);
            }
        }

        // レンダリング
        $this->render($this->param["render"]);

    }

    public function master_m($mode = "list", $code_and_id = null, $param = null){

        if(empty($mode)) $mode = "list";

        $this->loadModel('DataBranches');
        $this->loadModel('DataLeaves');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","master_m",$mode);

        //コード・ID取得
        $arr_code_and_id = explode(".",$code_and_id);
        $code = $arr_code_and_id[0];
        $id = null;
        if(isset($arr_code_and_id[1])) $id = $arr_code_and_id[1];

        // 存在しないCODEはマスタートップへ遷移
        if(!$this->DataBranches->isBranchByCode($code)){
            $redirect_url = '/admin/site/master_h/';
            $this->redirect($redirect_url);
        }
        $data_branch_code = $this->DataBranches->getDdataBranchCode();
        $branch_name      = $this->DataBranches->getBranchName();
        $limit_level      = $this->DataBranches->getLimitLevel();
        $data_branch_id   = $this->DataBranches->getDdataBranchId();
        $level            = $this->DataBranches->getLevel();
        $parent_id        = $this->DataBranches->getDdataLeaveId();
        $parent_name      = $this->DataBranches->getParentName();
        $parent_code      = $this->DataBranches->getParentCode();
        $topic_path       = $this->DataBranches->getTopicPath();
        $attach_id        = $this->DataBranches->getAttachId();
        $attach_code      = $this->DataBranches->getAttachCode();
        if($attach_id == 0) $attach_id = null;

        // 親データの設定
        $current_level = $level + 1;
        $is_down_level = "no";
        if($current_level < $limit_level){
            $is_down_level = "yes";
        }
        $this->param["is_down"] = $is_down_level;

        // 所属データ設定
        $this->param["add_attach_id"] = 0;
        if(isset($this->data["attach_id"])){
            $attach_id = $this->data["attach_id"];
            $attach_code = $this->DataLeaves->getOneFieldById($this->data["attach_id"],"code");
            $this->param["add_attach_id"] = $attach_id;
        }

        // タイトル設定
        $this->param["h2"] = $branch_name.$this->param["h2"];
        $this->param["data_name"] = str_replace("リスト","名",$branch_name);

        // 初期値フィールド設定
        $fields = $this->DataLeaves->getFields();
        $this->param["entity"]["data_leaves"] = $fields;
        $this->param["error"]["data_leaves"] = $fields;
        $this->param["data_branch_code"] = $data_branch_code;
        $this->param["data_branch_id"] = $data_branch_id;
        $this->param["parent_code"] = $parent_code;
        $this->param["entity"]["data_leaves"]["parent_id"] = $parent_id;
        $this->param["entity"]["data_leaves"]["level"] = $current_level;
        $this->param["topic_path"] = $topic_path;

        // セレクト用リスト設定
        $this->param["parent_list"] = $this->DataLeaves->getParentOptions($data_branch_code,$level);
        $this->param["attach_list"] = $this->DataLeaves->getParentOptions($data_branch_code,$level);
        $this->param["attach_id"] = $attach_id;

        // 初期ページナンバー
        if(!isset($this->data["disp_num"])){
            $this->Session->write("current_disp_num",50);
            $this->current_disp_num = 50;
            $this->set('current_disp_num',50);
        }

        // 削除処理
        if($mode === "del"){
            $this->DataLeaves->isDelete($this->data);
            $this->redirect($this->param["redirect_url"].$code);
        }

        // 上位データ変更
        if(isset($this->data["mode"])){
            if($this->data["mode"] === "chg_attach"){
                $attach_id = $this->data["attach_id"];
                $param_code = $this->DataLeaves->getOneFieldById($attach_id,"code");
                $this->redirect(HOME_URL."admin/site/master_m/list/".$code.".".$param_code);
            }
        }

        // 一覧表示
        if($mode === "list"){

/*
            $where["data_branch_code"] = $data_branch_code;
            $where["level"] = $current_level;

            if($parent_code != $data_branch_code){
                $where["parent_code"] = $parent_code;
            }

            if($attach_id > 0){
                $where["parent_code"] = $attach_code;
            }

            if(!isset($this->data["attach_id"])){
                $where["parent_code"] = "non_attach";
            }
*/

            $selected_code = 0;
            if(!empty($id)) $selected_code = $id;
            $arr_list = $this->DataLeaves->getOneEntityByCode($selected_code);
            $selected_id = $arr_list["data_leaves"]["id"];

            $where["parent_code"] = $code;
            if($selected_id > 0) $where["parent_id"] = $selected_id;

            $this->DataLeaves->setWhereVal($where);
            $this->param["entity"] = $this->DataLeaves->getPagingEntity($this->current_disp_num,$param);

            $this->param["add_url"] = $this->param["add_url"].$parent_code;

            $arr_list = $this->DataLeaves->getOneEntityByCode($selected_code);
            $this->param["attach_id"] = $arr_list["data_leaves"]["id"];
        }

        // 詳細・確認画面表示
        if($mode === "detail"){

            // エンティティ設定
            $entity = $this->DataLeaves->getOneEntityById($id);
            $this->param["entity"] = $entity;

        }

        // 新規登録画面
        if($mode === "add"){

            if($attach_id > 0){
                $this->param["entity"]["data_leaves"]["parent_id"] = $attach_id;
            }
            $this->param["entity"]["data_leaves"]["code"] = $this->DataLeaves->getAutoByBranchCode($data_branch_code);
            $this->param["entity"]["data_leaves"]["parent_code"] = $parent_code;

            // セッション破棄
            $this->Session->write($this->param["session_name"],array());
        }

        // 編集更新画面
        if($mode === "mod"){
            // PARAM値からフィールド設定
            $entity = $this->DataLeaves->getOneEntityById($id);
            $this->param["entity"] = $entity;
        }

        // 編集更新画面
        if($mode === "reentry"){

            // セッションを取得
            $data = $this->Session->read($this->param["session_name"]);
            $this->param["entity"] = $data;

            // エラーメッセージ設定
            $this->DataLeaves->isError($data);
            $this->param["error"]["data_leaves"] = $this->DataLeaves->getErrors();

        }

        // 入力確認画面
        if($mode === "confirm"){
            $entity = $this->data;
            if(empty($this->data)){
                $this->redirect($this->param["errorr_redirect"].$code);
            }

            // セッション値設定
            $this->Session->write($this->param["session_name"],$this->data);

            // エラーチェック
            $redirect_url = '/admin/site/master_m/reentry/'.$code;
            $is_err = true;
            if($is_err) $is_err = $this->DataLeaves->isError($entity);
            if(!$is_err) $this->redirect($redirect_url);

            // エンティティ設定
            $this->param["entity"] = $entity;

        }

        // 登録更新処理
        if($mode === "complete"){

            // セッション値を登録・更新
            $data = $this->Session->read($this->param["session_name"]);

            if(count($data) > 0){
                // 登録・更新完了後セッション破棄
                if($this->DataLeaves->isSave($data)){
                    $this->param["message"] = "登録・更新が完了しました。";
                    $this->param["target_url"] = $this->param["target_url"].$code;
                    $this->Session->write($this->param["session_name"],array());
                }else{
                    $this->param["message"] = "エラーが発生しました。登録・更新は完了していません。";
                }
            }else{
                // エラーの場合入力画面へ遷移
                $this->redirect($this->param["errorr_redirect"]);
            }
        }

        // レンダリング
        $this->render($this->param["render"]);

    }

    public function master_c($master_code, $param = null){

        if(empty($master_code)){
            $this->redirect(HOME_URL."admin");
        }

        if(!file_exists(APP.'views'.DS.'admin_site'.DS.$master_code.'.html')){
            $this->redirect(HOME_URL."admin");
        }

        $this->loadModel('DataBranches');
        $this->loadModel('DataLeaves');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","master_c");

        // タイトル設定
        $list = $this->DataBranches->getOneEntityByCode($master_code);
        $this->param["h2"] = $list["data_branches"]["name"].$this->param["h2"];

        // 初期値フィールド設定
        $fields = $this->DataLeaves->getFields();
        $this->param["entity"]["data_leaves"] = $fields;
        $this->param["error"]["data_leaves"] = $fields;
        $this->param["form"]["data_leaves"] = $fields;
        $this->param["fm_action"] .= $master_code;

        // 削除処理
        if($this->data["mode"] === "del"){
            $this->DataLeaves->isDelete($this->data);
            $this->redirect($this->param["fm_action"]);
        }

        // 一括更新
        if($this->data["mode"] === "massup"){

            if($this->DataLeaves->isMassSave($this->data,"point")){
                $this->Session->write("attention_message",$this->param["complete_attention"]);
                $this->redirect($this->param["fm_action"]);
            }

        }

//        // 編集更新画面
//        if($this->data["mode"] === "mod"){
//            // PARAM値からフィールド設定
////            $entity = $this->DataLeaves->getOneEntityById($id);
//            $this->param["entity"] = $entity;
//        }

        // 一覧表示
        $where["parent_code"] = $master_code;
        $this->DataLeaves->setWhereVal($where);
        $this->current_disp_num = 1000;
        $this->param["entity"] = $this->DataLeaves->getPagingEntity($this->current_disp_num,$param);

        // 登録更新処理
/*
        if($this->data["mode"] === "complete"){

            // セッション値を登録・更新
            $data = $this->Session->read($this->param["session_name"]);

            if(count($data) > 0){
                // 登録・更新完了後セッション破棄
                if($this->DataLeaves->isSave($data)){
                    $this->param["message"] = "登録・更新が完了しました。";
                    $this->param["target_url"] = $this->param["target_url"].$master_code;
                    $this->Session->write($this->param["session_name"],array());
                }else{
                    $this->param["message"] = "エラーが発生しました。登録・更新は完了していません。";
                }
            }else{
                // エラーの場合入力画面へ遷移
                $this->redirect($this->param["errorr_redirect"]);
            }
        }
*/
        // レンダリング
        $this->render($master_code);

    }

    public function coefficient($mode, $param = null, $param2 = null){

        $current_year = date("Y");
        $current_month = date("m");

        if(empty($mode)){
             $mode = $current_year."_".$current_month;
        }else{
            $arr_ym = explode("_",$mode);
            $current_year = $arr_ym[0];
            if(isset($arr_ym[1])){
                $current_month = $arr_ym[1];
            }
        }

        $this->loadModel('DateCoefficients');
        $this->loadModel('DataLeaves');

        // ページ共通パラメータ取得
        $this->param = $this->_const->getArrConst("site","coefficient");

        // 初期値フィールド設定
        $fields = $this->DataLeaves->getFields();
        $this->param["entity"]["data_leaves"] = $fields;
        $this->param["error"]["data_leaves"] = $fields;
        $this->param["form"]["data_leaves"] = $fields;

        $list = $this->DataLeaves->getOneEntityByCode("coefficient01");
        $this->param["form"]["data_leaves"] = $list["data_leaves"];
        $def_coefficient = $list["data_leaves"]["value"];

        $this->param["entity"]["date_coefficients"]["year"] = $current_year;
        $this->param["entity"]["date_coefficients"]["month"] = $current_month;
        $this->param["ym"] = $mode;

        // 年月表示更新画面
        if($this->data["mode"] === "chgDate"){
            $this->redirect($this->param["fm_action"].$mode);
        }

        // 基本係数更新処理
        if($this->data["mode"] === "coefficient_upd"){
            $this->DataLeaves->isSave($this->data);
            $this->redirect($this->param["fm_action"].$mode);
        }

        // 一括更新
        if($this->data["mode"] === "massup"){

            if($this->DateCoefficients->isMassSave($this->data)){
                $this->redirect($this->param["fm_action"].$mode);
            }

        }

        // 一覧表示
        $this->param["entity"]["date_coefficients"]["list"] = $this->DateCoefficients->getEntityByDate($current_year,$current_month,$def_coefficient);
        $this->param["is_date_save"] = $this->DateCoefficients->isDateSave();

        // レンダリング
        $this->render($this->param["render"]);

    }

    protected function getArrConst($dir,$page = null,$mode = null) {
        // ページ共通パラメータ取得
        /* マスタ上位データ管理 共通 -------------------------------------*/
        $arrField["add_url"] = HOME_URL."admin/site/master_h/add/";
        $arrField["session_name"] = "data_admin_site";
        $arrField["errorr_redirect"] = HOME_URL."admin/site/master_h/add/";

        /* マスタ上位データ管理 一覧 */
        $arrField["title"] = "マスタデータ管理";
        $arrField["h2"] = "上位マスタデータ一覧";
        $arrField["render"] = "master_h_list";
        $this->arr_const["site"]["master_h"]["top"] = $arrField;

        /* マスタ上位データ管理 一覧 */
        $arrField["title"] = "マスタデータ管理";
        $arrField["h2"] = "上位マスタデータ一覧";
        $arrField["render"] = "master_h_list";
        $arrField["fm_action"] = HOME_URL."admin/site/master_h/del/";
        $this->arr_const["site"]["master_h"]["list"] = $arrField;

        /* マスタ上位データ管理 詳細 */
        $arrField["h2"] = "上位マスタデータ詳細";
        $arrField["render"] = "master_h_detail";
        $arrField["fm_action"] = HOME_URL."admin/site/master_h/detail/";
        $arrField["reentry_url"] = HOME_URL."admin/site/master_h/mod/";
        $arrField["redirect_url"] = HOME_URL."admin/site/master_h/detail/";
        $arrField["complete_url"] = "";
        $this->arr_const["site"]["master_h"]["detail"] = $arrField;

        /* マスタ上位データ管理 新規登録 */
        $arrField["h2"] = "上位マスタデータ 新規登録入力画面";
        $arrField["render"] = "master_h_entry";
        $arrField["fm_action"] = HOME_URL."admin/site/master_h/confirm/";
        $this->arr_const["site"]["master_h"]["add"] = $arrField;

        /* マスタ上位データ管理 編集 */
        $arrField["h2"] = "上位マスタデータ 編集入力画面";
        $arrField["render"] = "master_h_entry";
        $this->arr_const["site"]["master_h"]["mod"] = $arrField;

        /* マスタ上位データ管理 編集 */
        $arrField["h2"] = "上位マスタデータ 再入力画面";
        $arrField["render"] = "master_h_entry";
        $this->arr_const["site"]["master_h"]["reentry"] = $arrField;

        /* マスタ上位データ管理 確認 */
        $arrField["h2"] = "上位マスタデータ 入力確認画面";
        $arrField["render"] = "master_h_detail";
        $arrField["complete_url"] = HOME_URL."admin/site/master_h/complete/";
        $arrField["reentry_url"] = HOME_URL."admin/site/master_h/reentry/";
        $this->arr_const["site"]["master_h"]["confirm"] = $arrField;

        /* マスタ上位データ管理 削除 */
        $arrField["render"] = "master_h_list";
        $arrField["redirect_url"] = HOME_URL."admin/site/master_h/detail/";
        $this->arr_const["site"]["master_h"]["del"] = $arrField;

        /* マスタ上位データ管理 完了 */
        $arrField["h2"] = "上位マスタデータ 登録編集完了画面";
        $arrField["render"] = "complete";
        $arrField["target_url"] = HOME_URL."admin/site/master_h/list/";
        $this->arr_const["site"]["master_h"]["complete"] = $arrField;

        return $this->arr_const[$dir][$page][$mode];
    }
}

?>
