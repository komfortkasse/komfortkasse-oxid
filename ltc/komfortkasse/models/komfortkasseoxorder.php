<?php
class komfortkasseOxOrder extends komfortkasseOxOrder_parent
{
	/** @overload */
	public function finalizeOrder( oxBasket $oBasket, $oUser, $blRecalculatingOrder = false) {
		if (parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder) == oxOrder::ORDER_STATE_OK) {
			$k = new Komfortkasse();
			$k->notifyorder($this->oxorder__oxordernr->value);
		}		
	}

	
}