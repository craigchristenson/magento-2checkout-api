<?php


class Twocheckout_Api_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getJscriptPath()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'js/twocheckout/';
    }

    public function restoreQuote()
    {
        $order = $this->_getCheckoutSession()->getLastRealOrder();
        if ($order->getId()) {
            $quote = $this->_getQuote($order->getQuoteId());
            if ($quote->getId()) {
                $quote->setIsActive(1)
                    ->setReservedOrderId(null)
                    ->save();
                $this->_getCheckoutSession()
                    ->replaceQuote($quote)
                    ->unsLastRealOrderId();
                return true;
            }
        }
        return false;
    }
}
