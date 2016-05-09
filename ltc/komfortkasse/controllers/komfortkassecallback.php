<?php
/**
 * Komfortkasse
 * routing
 * 
 * @version 1.1.1-oxid
 */

class komfortkasseCallback extends oxUBase
{
	protected $_sThisTemplate = 'komfortkassecallback.tpl';
	
	public function handleRequest()
	{
		$action = Komfortkasse_Config::getRequestParameter('action');
		
		$kk = new Komfortkasse();
		$kk->$action();
		
		// do not redirect
		return false;
	}
}
?>