<?php
class PublicsComponent extends Component {

    function top() {
		// 新着物件取得
		$MerchBasics = ClassRegistry::init('MerchBasics');
		$code = "whats_new";
		$list = $MerchBasics->getPickupEntityByCode($code);
		$this->arrWhatsNew = $list;
		return $this;
    }

}
?>