<?php
App::uses('FrontAppController','Controller');

class DesignsController extends FrontAppController {

    function index(){
        $this->redirect('/design/print');
    }

    function print_page(){

        // ビュー設定
        $this->render('print');
    }

    function font(){
//        $this->render('publics_rrrr');
    }

    function color(){
//        $this->render('publics_slag_test');
    }

}
