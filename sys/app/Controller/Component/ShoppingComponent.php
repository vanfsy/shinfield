<?php
class ShoppingComponent extends Component {

	var $utility;

   function __construct() {
		App::import('Component', 'Utility');
		$this->Utility = new UtilityComponent(new ComponentCollection());
   }

/*
* おすすめ商品
*/
    function recommend() {

//		$MerchBasics = ClassRegistry::init('MerchBasics');
//		$param["items"] = $MerchBasics->getAllItemsByCategoryIdListdiv($category_id,$listdiv_rank1,$listdiv_rank2);
////echo print_r($param["items"]);
//		return $param;

    }

/*
* おすすめ商品
*/
    function topItems() {

		$MerchItems = ClassRegistry::init('MerchItems');
		$list = $MerchItems->getEntityByFieldName("top_pickup");
		return $list;

    }

}
?>