<?php
/**
 * Komfortkasse
 * routing
 *
 * @version 1.7.3-oxid
 */

class komfortkasseCallback extends oxUBase
{
	protected $_sThisTemplate = 'komfortkassecallback.tpl';

	public function handleRequest()
	{
		$action = Komfortkasse_Config::getRequestParameter('action');

		$kk = new Komfortkasse();
		$this->_aViewData['skomfortkasseoutput'] = $kk->$action();

		// do not redirect
		return false;
	}
}
?>