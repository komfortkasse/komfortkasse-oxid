<?php

/** 
 * Komfortkasse
 * Config Class
 * 
 * @version 1.4.0.2-oxid
 */
class Komfortkasse_Config
{
    const activate_export = 'kkActivateExport';
    const activate_update = 'kkActivateUpdate';
    const payment_methods = 'kkPaymentMethods';
    const status_open = ''; // not used in oxid module
    const status_paid = 'kkStatusPaid';
    const status_cancelled = 'kkStatusCancelled';
    const payment_methods_invoice = 'kkPaymentMethodsInvoice';
    const status_open_invoice = ''; // not used in oxid module
    const status_paid_invoice = 'kkStatusPaidInvoice';
    const status_cancelled_invoice = 'kkStatusCancelledInvoice';
    const payment_methods_cod = 'kkPaymentMethodsCod';
    const status_open_cod = ''; // not used in oxid module
    const status_paid_cod = 'kkStatusPaidCod';
    const status_cancelled_cod = 'kkStatusCancelledCod';
    const encryption = 'kkEncryption';
    const accesscode = 'kkAccesscode';
    const apikey = 'kkApikey';
    const publickey = 'kkPublickey';
    const privatekey = 'kkPrivatekey';


    public static function setConfig($constant_key, $value)
    {
        $sVarType = is_bool($value) ? 'bool' : 'str';
        oxRegistry::getConfig()->saveShopConfVar($sVarType, $constant_key, $value, null, 'module:komfortkasse');
    
    }


    public static function getConfig($constant_key)
    {
        // PAID und CANCELLED wird in OXID nicht durch einen Status repräsentiert - logik ist in updateOrder() hartcodiert
        if ($constant_key == self::status_paid || $constant_key == self::status_paid_invoice || $constant_key == self::status_paid_cod)
            return 'PAID';
        if ($constant_key == self::status_cancelled || $constant_key == self::status_cancelled_invoice || $constant_key == self::status_cancelled_cod)
            return 'CANCELLED';
        
        return oxRegistry::getConfig()->getConfigParam($constant_key);
    
    }


    public static function getRequestParameter($key)
    {
        if (method_exists(oxConfig, 'getParameter')) {
            // < 4.9.0
            return urldecode(oxConfig::getParameter($key));
        } else {
            // >= 4.9.0
            return urldecode(oxRegistry::getConfig()->getRequestParameter($key));
        }
    
    }


    public static function getVersion()
    {
        if (method_exists(oxConfig, 'getInstance')) {
            // < 4.9.0
            $myConfig = oxConfig::getInstance();
        } else {
            // >= 4.9.0
            $myConfig = oxRegistry::getConfig();
        }
        $oBaseShop = oxNew("oxshop");
        $oBaseShop->load($myConfig->getBaseShopId());
        return $oBaseShop->oxshops__oxversion->value;
        
    }
}
?>