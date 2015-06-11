<?php


class Twocheckout_Api_RedirectController extends Mage_Core_Controller_Front_Action {

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    protected $order;

    protected function _expireAjax() {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    public function indexAction() {
        $this->getResponse()
            ->setHeader('Content-type', 'text/html; charset=utf8')
            ->setBody($this->getLayout()
                ->createBlock('twocheckout/redirect')
                ->toHtml());
    }

    public function successAction() {
        $post = [];
        foreach ($_REQUEST as $k => $v) {
            $v = htmlspecialchars($v);
            $v = stripslashes($v);
            $post[$k] = $v;
        }
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($post['merchant_order_id']);
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $hashSecretWord = Mage::getStoreConfig('payment/twocheckout/secret');
        $hashSid = Mage::getStoreConfig('payment/twocheckout/sid');
        $hashTotal = number_format($order->getBaseGrandTotal(), 2, '.', '');
        $hashOrder = $post['order_number'];

        $StringToHash = strtoupper(md5($hashSecretWord . $hashSid . $hashOrder . $hashTotal));

        if ($StringToHash == $post['key']) {
            $this->_redirect('checkout/onepage/success');
            $order->sendNewOrderEmail();
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
            $order->setData('ext_order_id',$post['order_number'] );
            $order->save();
        } else {
            $this->_redirect('checkout/onepage/success');
            $order->addStatusHistoryComment($hashTotal);
            $order->addStatusHistoryComment('Hash did not match, check secret word.');
            $order->save();
        }
    }

    public function failAction() {
        $session = Mage::getSingleton('checkout/session');
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }
            $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
            $quote->setIsActive(true)->save();
        }
        $this->_redirect('checkout/cart');
    }

}
