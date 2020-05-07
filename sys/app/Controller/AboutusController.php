<?php

App::uses('FrontAppController', 'Controller');

class AboutusController extends FrontAppController {

    public $uses = array('Whatsnews', 'Item', 'HtmlPart');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    function index() {
        // ビュー設定
        $this->set('HTML_GUIDE_ABOUT', $this->HtmlPart->getHtml(HtmlPart::GUIDE_ABOUT));
    }

    /**
     * ガイド・ご利用案内
     * */
    function guide($type) {
        if (strtolower($type) == 'buy') {
            $this->set('HTML_GUIDE_CONTENT', $this->HtmlPart->getHtml(HtmlPart::GUIDE_BUY));

	    //yoshi20180220
	    $this->set('META_TITLE', Configure::read('html.guide'));
	    $this->set('META_KEYWORDS', Configure::read('html.guide'));
	    $this->set('META_DESCRIPTION', Configure::read('html.guide'));

        } else if (strtolower($type) == 'sale') {
            $this->set('HTML_GUIDE_CONTENT', $this->HtmlPart->getHtml(HtmlPart::GUIDE_SALE));

	    //yoshi20180220
	    $this->set('META_TITLE', Configure::read('html.guide_sale'));
	    $this->set('META_KEYWORDS', Configure::read('html.guide_sale'));
	    $this->set('META_DESCRIPTION', Configure::read('html.guide_sale'));

        } else {
            $this->redirect('/');
        }
    }

    /**
     * 特定商法取引に基づく表記
     * */
    function law() {
        $this->set('HTML_GUIDE_LAW', $this->HtmlPart->getHtml(HtmlPart::GUIDE_LAW));
    }

    /**
     * 個人情報保護について
     * */
    function privacy() {
        $this->set('HTML_GUIDE_PRIVACY', $this->HtmlPart->getHtml(HtmlPart::GUIDE_PRIVACY));
    }

    function faq() {
        $this->set('HTML_GUIDE_FAQ', $this->HtmlPart->getHtml(HtmlPart::GUIDE_FAQ));
    }

    function contact() {
        $this->set('HTML_GUIDE_CONTACT', $this->HtmlPart->getHtml(HtmlPart::GUIDE_CONTACT));
    }

    function whatnew() {
        $arrWhatNews = $this->Whatsnews->getNewEntity();
        $this->set('arrWhatNews', $arrWhatNews);
    }

}
