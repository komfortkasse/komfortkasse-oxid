<?php

// in KK, an Order is an Array providing the following members:
// number, date, email, customer_number, payment_method, amount, currency_code, exchange_rate, language_code, invoice_number
// delivery_ and billing_: _firstname, _lastname, _company, _street, _postcode, _city, _countrycode
// products: an Array of item numbers

/**
 * Komfortkasse
 * Config Class
 *
 * @version 1.7.3-oxid
 */
class Komfortkasse_Order
{

    // return all order numbers that are "open" and relevant for transfer to kk
    public static function getOpenIDs()
    {
        $ret = array ();

        $oDb = oxDb::getDb();

        $sql = "select oxordernr from oxorder where oxpaid='0000-00-00 00:00:00' and oxstorno=0 and (";
        $paycodes = preg_split('/,/', Komfortkasse_Config::getConfig(Komfortkasse_Config::payment_methods));
        for($i = 0; $i < count($paycodes); $i++) {
            $sql .= " oxpaymenttype like '" . $paycodes [$i] . "' ";
            if ($i < count($paycodes) - 1) {
                $sql .= " or ";
            }
        }
        $paycodes = preg_split('/,/', Komfortkasse_Config::getConfig(Komfortkasse_Config::payment_methods_invoice));
        for($i = 0; $i < count($paycodes); $i++) {
            if ($i == 0) {
                $sql .= " or ";
            }
            $sql .= " oxpaymenttype like '" . $paycodes [$i] . "' ";
            if ($i < count($paycodes) - 1) {
                $sql .= " or ";
            }
        }
        $paycodes = preg_split('/,/', Komfortkasse_Config::getConfig(Komfortkasse_Config::payment_methods_cod));
        for($i = 0; $i < count($paycodes); $i++) {
            if ($i == 0) {
                $sql .= " or ";
            }
            $sql .= " oxpaymenttype like '" . $paycodes [$i] . "' ";
            if ($i < count($paycodes) - 1) {
                $sql .= " or ";
            }
        }
        $sql .= " )";
        $result = $oDb->getAll($sql);

        foreach ($result as $line) {
            if ($line [0]) {
                $ret [] = $line [0];
            }
        }

        return $ret;

    }


    public static function getOrder($number)
    {
        $sSelect = "select oxid from oxorder where oxordernr = '" . $number . "'";
        $soxId = oxDb::getDb()->getOne($sSelect);
        $oOrder = oxNew("oxorder");
        if (!$oOrder->load($soxId)) {
            return;
        }

        $oCur = $oOrder->getConfig()->getActShopCurrencyObject();
        if (method_exists($oOrder, 'getOrderCurrency')) {
            $oCur = $oOrder->getOrderCurrency();
        }
        $oLang = oxRegistry::getLang();
        $lang_code = $oLang->getLanguageAbbr($oOrder->getOrderLanguage());

        $oCountryBill = oxNew('oxcountry');
        $oCountryBill->load($oOrder->getFieldData('oxbillcountryid'));
        $billing_country = $oCountryBill ? $oCountryBill->getFieldData('oxisoalpha2') : null;
        $oCountryDel = oxNew('oxcountry');
        $oCountryDel->load($oOrder->getFieldData('oxdelcountryid'));
        $delivery_country = $oCountryDel ? $oCountryDel->getFieldData('oxisoalpha2') : null;

        $ret = array ();
        $ret ['number'] = $number;
        $ret ['date'] = date("d.m.Y", strtotime($oOrder->getFieldData('oxorderdate')));
        $ret ['email'] = $oOrder->getFieldData('oxbillemail');

        // customer number from oxuser
        $cSelect = "select oxcustnr from oxuser where oxid = '" . $oOrder->getFieldData('oxuserid') . "'";
        $ret ['customer_number'] = oxDb::getDb()->getOne($cSelect);

        $ret ['payment_method'] = $oOrder->getFieldData('oxpaymenttype');
        $ret ['amount'] = $oOrder->getTotalOrderSum();
        $ret ['currency_code'] = $oCur->name;
        $ret ['exchange_rate'] = $oCur->rate;
        $ret ['language_code'] = $lang_code . '-' . $billing_country;
        $ret ['delivery_firstname'] = $oOrder->getFieldData('oxdelfname');
        $ret ['delivery_lastname'] = $oOrder->getFieldData('oxdellname');
        $ret ['delivery_company'] = $oOrder->getFieldData('oxdelcompany');
        $ret ['delivery_street'] = trim($oOrder->getFieldData('oxdelstreet') . ' ' . $oOrder->getFieldData('oxdelstreetnr'));
        $ret ['delivery_postcode'] = $oOrder->getFieldData('oxdelzip');
        $ret ['delivery_city'] = $oOrder->getFieldData('oxdelcity');
        $ret ['delivery_countrycode'] = $billing_country;
        $ret ['billing_firstname'] = $oOrder->getFieldData('oxbillfname');
        $ret ['billing_lastname'] = $oOrder->getFieldData('oxbilllname');
        $ret ['billing_company'] = $oOrder->getFieldData('oxbillcompany');
        $ret ['billing_street'] = trim($oOrder->getFieldData('oxbillstreet') . ' ' . $oOrder->getFieldData('oxbillstreetnr'));
        $ret ['billing_postcode'] = $oOrder->getFieldData('oxbillzip');
        $ret ['billing_city'] = $oOrder->getFieldData('oxbillcity');
        $ret ['billing_countrycode'] = $delivery_country;
        $ret ['invoice_number'] [] = $oOrder->getFieldData('oxbillnr');
        $ret ['shipping_number'] [] = $oOrder->getFieldData('oxtrackcode');
        $billdate = $oOrder->getFieldData('oxbilldate');
        if ($billdate && $billdate <> '0000-00-00')
            $ret ['invoice_date'] = date("d.m.Y", strtotime($billdate));

        $order_products = $oOrder->getOrderArticles(true);
        foreach ($order_products as $product) {
            if ($product->getArticle()->getFieldData('oxartnum')) {
                $ret ['products'] [] = $product->getArticle()->getFieldData('oxartnum');
            } else {
                $ret ['products'] [] = $product->getArticle()->getFieldData('oxtitle');
            }
        }

        return $ret;

    }


    public static function updateOrder($order, $status, $callbackid)
    {
        $sSelect = "select oxid from oxorder where oxordernr = '" . $order ['number'] . "'";
        $soxId = oxDb::getDb()->getOne($sSelect);
        $oOrder = oxNew("oxorder");
        if (!$oOrder->load($soxId)) {
            return;
        }

        if ($status == 'PAID') {
            $aParams = array ();
            $aParams ['oxorder__oxpaid'] = date("Y-m-d H:i:s");
            $aParams ['oxorder__oxtransid'] = $callbackid;
            $oOrder->assign($aParams);
            $oOrder->save();
        } else if ($status == 'CANCELLED') {
            $oOrder->cancelOrder();
        }

    }


    public static function getInvoicePdfPrepare()
    {
        // nothing to prepare
    }


    public static function getInvoicePdf($invoiceNumber)
    {
        $className = 'invoicepdfoxorder';
        if ($invoiceNumber && class_exists($className)) {
            $sSelect = "select oxid from oxorder where oxbillnr = '" . $invoiceNumber . "'";
            $soxId = oxDb::getDb()->getOne($sSelect);
            $oOrder = oxNew($className);
            $oOrder->setAdminMode(true);
            if ($oOrder->load($soxId)) {
                if (method_exists(oxUtils, 'getInstance')) {
                    // < 4.9.0
                    $oUtils = oxUtils::getInstance();
                } else {
                    // >= 4.9.0
                    $oUtils = oxRegistry::getUtils();
                }
                ob_start();
                $oOrder->genPdf($invoiceNumber . '.pdf', $oOrder->getFieldData('oxlang'));
                $sPDF = ob_get_contents();
                ob_end_clean();
                $oUtils->setHeader("Pragma: public");
                $oUtils->setHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                $oUtils->setHeader("Expires: 0");
                $oUtils->setHeader("Content-type: application/pdf");
                $oUtils->setHeader("Content-Disposition: attachment; filename=" . $invoiceNumber . '.pdf');
                $oUtils->showMessageAndExit($sPDF);
            }
        }

    }


    public static function getRefund($number)
    {
        // not implemented
    }


    public static function getRefundIDs()
    {
        // not implemented
        return array ();

    }


    public static function updateRefund($refundIncrementId, $status, $callbackid)
    {
        // not implemented
    }
}

?>