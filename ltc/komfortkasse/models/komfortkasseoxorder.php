<?php
class komfortkasseOxOrder extends komfortkasseOxOrder_parent
{
	/** @overload */
	public function finalizeOrder( oxBasket $oBasket, $oUser, $blRecalculatingOrder = false) {
	    $ret = parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
		if ($ret == oxOrder::ORDER_STATE_OK) {
			$k = new Komfortkasse();
			$k->notifyorder($this->oxorder__oxordernr->value);
		}
		return $ret;
	}


}