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

            //$auth = 'Basic ' . base64_encode($username . ':' . $password);

            $data = array();
            $data['invoice_id'] = $creditmemo['ext_order_id'];
            $data['comment'] = $comment;
            $data['category'] = '5';
            $data['amount'] = $creditmemo_amount['grand_total'];
            $data['currency'] = 'vendor';
            /*
            $headers = array(
                'Authorization: ' . $auth,
                'Accept: application/json'
            );

            $url = 'https://www.2checkout.com/api/sales/refund_invoice';

            $config = array(
                'timeout'    => 30
            );
            */
            try {
                $response = Twocheckout_Sale::refund($data);
                $order->addStatusHistoryComment($response["response_message"]);
                $order->save();
            } catch (Exception $e) {
                Mage::throwException(Mage::helper('core')->__($e->getMessage()));
            }
            


        }
    }
}
?>