<?php

require_once Mage::getBaseDir('lib').DS.'Twocheckout.php';

class Twocheckout_Api_Model_Observer extends Mage_Core_Block_Abstract {

    public function issue_creditmemo_refund(Varien_Object $payment) {

        $refund = Mage::getStoreConfig('payment/twocheckout/refund');
        if ($refund == '1') {
            $order = $payment->getCreditmemo()->getOrder();
            $creditmemo = $payment->getCreditmemo()->getOrder()->getData();
            $creditmemo_amount = $payment->getCreditmemo()->getData();
            $creditmemo_comment = $payment->getCreditmemo()->getCommentsCollection()->toArray();

            if(isset($creditmemo_comment['items'][0]['comment'])) {
                $comment = $creditmemo_comment['items'][0]['comment'];
            } else {
                $comment = 'Refund issued by seller';
            }

            $username = Mage::getStoreConfig('payment/twocheckout/username');
            $password = Mage::getStoreConfig('payment/twocheckout/password');
            $sandbox = Mage::getStoreConfig('payment/twocheckout/demo');

            Twocheckout::username($username);
            Twocheckout::password($password);

            if($sandbox == "1") {
                Twocheckout::sandbox(true);
            } else {
                Twocheckout::sandbox(false);
            }

            $data = array();
            $data['invoice_id'] = $creditmemo['ext_order_id'];
            $data['comment'] = $comment;
            $data['category'] = '5';
            $data['amount'] = $creditmemo_amount['grand_total'];
            $data['currency'] = 'vendor';

            try {
                $response = Twocheckout_Sale::refund($data);
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
                $order->addStatusHistoryComment($response["response_message"]);
                $order->save();
            } catch (Twocheckout_Error $e) {
                Mage::throwException(Mage::helper('core')->__($e->getMessage()));
            }
        }
    }

    public function set_status_after_save_order(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $order->loadByIncrementId($order->getRealOrderId());
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
        $order->save();
        return true;
    }
}
