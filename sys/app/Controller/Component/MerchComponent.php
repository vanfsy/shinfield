<?php
class MerchComponent extends Component {

   function __construct() {
		App::import('Component', 'Utility');
		$this->Utility = new UtilityComponent(new ComponentCollection());
		App::import('Component', 'Session');
		$this->Session = new SessionComponent(new ComponentCollection());
		App::import('Component', 'Auth');
		$this->Auth = new AuthComponent(new ComponentCollection());
   }

    function articleDetails() {

		// 募集物件取得
		$MerchBasics = ClassRegistry::init('MerchBasics');

		// 商品ID取得
		$item_id = $this->Utility->getUrlParam("itemid",0);
		$list = $MerchBasics->getOnePublicEntityByItemId($item_id);
		$this->arrArticleInfo = $list;
		return $this;

    }

    function resultsList() {
		// 実績物件取得
		$MerchBasics = ClassRegistry::init('MerchBasics');
		$disp_num = 9;
		$pgnum = 0;
		$arr_param["merch_basics.merch_style_id"] = 2;
		$MerchBasics->setArrWhere($arr_param);
		$this->arrResultsList1 = $MerchBasics->getAllPublicEntityByCategoryId(1);
		$this->arrResultsList2 = $MerchBasics->getAllPublicEntityByCategoryId(2);
		$this->arrResultsList3 = $MerchBasics->getAllPublicEntityByCategoryId(3);
		$this->arrResultsList4 = $MerchBasics->getAllPublicEntityByCategoryId(4);
		$this->arrResultsList5 = $MerchBasics->getAllPublicEntityByCategoryId(5);
		$this->arrResultsList6 = $MerchBasics->getAllPublicEntityByCategoryId(6);
		return $this;
    }

    function resultsDetails() {
		// 募集物件取得
		$MerchBasics = ClassRegistry::init('MerchBasics');

		// 商品ID取得
		$item_id = $this->Utility->getUrlParam("itemid",0);
		$list = $MerchBasics->getOnePublicEntityByItemId($item_id);
		$this->arrResultInfo = $list;
		return $this;
    }

    function articleList() {
		// 募集物件取得
		$MerchBasics = ClassRegistry::init('MerchBasics');
		$disp_num = 9;
		$pgnum = 0;
		$arr_param["merch_basics.merch_style_id"] = 1;
		$MerchBasics->setArrWhere($arr_param);
		$this->arrArticleList1 = $MerchBasics->getAllPublicEntityByCategoryId(1);
		$this->arrArticleList2 = $MerchBasics->getAllPublicEntityByCategoryId(2);
		$this->arrArticleList3 = $MerchBasics->getAllPublicEntityByCategoryId(3);
		$this->arrArticleList4 = $MerchBasics->getAllPublicEntityByCategoryId(4);
		$this->arrArticleList5 = $MerchBasics->getAllPublicEntityByCategoryId(5);
		$this->arrArticleList6 = $MerchBasics->getAllPublicEntityByCategoryId(6);
		return $this;
    }
}
?>