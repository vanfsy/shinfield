<?php
App::uses('FrontAppController', 'Controller');

class FormController extends FrontAppController {

//    var $view = 'Smarty';
//    var $components = array('Session','Utility','Cart','Auth','Cookie','RequestHandler');

    function beforeFilter() {

        parent::beforeFilter();
        $q = str_replace("url=","",$_SERVER['QUERY_STRING']);
        $arr = explode("/",$q);

        $slug = $arr[1];
        $this->loadModel('Form');
        $this->loadModel('FormField');
        $this->entry_list    = $this->Form->getOneEntityBySlug($slug,"entry_slug");
        $this->confirm_list  = $this->Form->getOneEntityBySlug($slug,"confirm_slug");
        $this->complete_list = $this->Form->getOneEntityBySlug($slug,"complete_slug");
    }

    public function form_(){
        $this->redirect('/');
    }

    function inquiry(){

        $this->set('form_url',HOME_URL."form/".$this->entry_list["confirm_slug"]);
        $list = $this->FormField->getFieldNameByFormId($this->entry_list["id"]);
        $this->param = array_merge($this->param , $list);
        $this->error = $list;
        $this->set('param',$this->param);
        $this->set('error',$this->error);

        // ビュー設定
        $this->render($this->entry_list["entry_slug"]);

    }

    function inquiry_confirm(){

        $this->param = array_merge($this->param , $this->data);
        $this->set('param',$this->param);

        if(!$this->FormField->isFormValid($this->data,$this->confirm_list["id"])){

            $this->error = $this->FormField->getFormErrors();
            $this->set('error',$this->error);
            $this->set('form_url',HOME_URL."form/".$this->confirm_list["confirm_slug"]);
            $this->Session->setFlash('入力に間違いがあります。', 'valid_alert');

            // ビュー設定
            $this->render($this->confirm_list["entry_slug"]);

        }else{

            $this->set('form_url',HOME_URL."form/".$this->confirm_list["complete_slug"]);
            $this->Session->write("form_data",$this->data);

            // ビュー設定
            $this->render($this->confirm_list["confirm_slug"]);

        }

    }

    function inquiry_complete(){

        $data = $this->Session->read("form_data");
        if(count($data) == 0){
            $this->redirect(HOME_URL.'form/'.$this->complete_list["entry_slug"]);
        }

        $subject = $this->complete_list["mail_subject"];
        $from_mail = $this->complete_list["email"];
        // メール送信
        $body = $this->complete_list["mail_template"];
        foreach($data as $name => $value){
            $search = '<!--{'.$name.'}-->';
            $body = str_replace($search,$value,$body);
        }
        $to_mail = $data["email"];
        $this->Utility->sendMail($body,$subject,$from_mail,$to_mail);

        // セッション破棄
        $this->Session->write("form_data",null);

        // ビュー設定
        $this->render($this->complete_list["complete_slug"]);
    }
}

?>
