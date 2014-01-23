<?php


class Twocheckout_Api_Model_Paypal extends Mage_Payment_Model_Method_Abstract {

    protected $_code  = 'twocheckout_paypal';
    protected $_paymentMethod = 'shared';

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('twocheckout/redirect');
    }

//get SID
    public function getSid() {
        $sid = $hashSid = Mage::getStoreConfig('payment/twocheckout/sid');
        return $sid;
    }

//get purchase routine URL
    public function getUrl() {
        $sandbox = Mage::getStoreConfig('payment/twocheckout/demo');
        if ($sandbox) {
            $url = 'https://sandbox.2checkout.com/checkout/purchase';
        } else {
            $url = 'https://www.2checkout.com/checkout/purchase';
        }
        return $url;
    }

//get custom checkout message
    public function getRedirectMessage() {
        $redirect_message = $this->getConfigData('redirect_message');
        return $redirect_message;
    }

//get order
    public function getQuote() {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        return $order;
    }

//get HTML form data
    public function getFormFields() {
        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order    = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $billing = $order->getBillingAddress();
        $shipping = $order->getShippingAddress();
        $amount = round($order->getGrandTotal(), 2);

        $params = array(
            "sid" => $this->getSid(),
            "merchant_order_id" => $order->getIncrementId(),
            "cart_order_id" => $order->getIncrementId(),
            "currency_code"   => $order->getOrderCurrencyCode(),
            "total"      => $amount,
            "paypal_direct" => 'Y',
            "return_url" => Mage::getUrl('twocheckout/redirect/fail', array('_secure' => true)),
            "2co_cart_type" => 'magento',
            "x_receipt_link_url" => Mage::getUrl('twocheckout/redirect/success', array('_secure' => true)),
            "demo" => $this->getDemo(),
            "card_holder_name" => $billing->getName(),
            "street_address" => $billing->getStreet(1),
            "street_address2" => $billing->getStreet(2),
            "city" => $billing->getCity(),
            "state" => $billing->getRegion(),
            "zip" => $billing->getPostcode(),
            "country" => $billing->getCountry(),
            "email" => $order->getCustomerEmail(),
            "phone" => $billing->getTelephone()
        );

        if ($shipping) {
            $shippingAddr = array(
                "ship_name" => $shipping->getName(),
                "ship_street_address" => $shipping->getStreet(1),
                "ship_street_address2" => $shipping->getStreet(2),
                "ship_city" => $shipping->getCity(),
                "ship_state" => $shipping->getRegion(),
                "ship_zip" => $shipping->getPostcode(),
                "ship_country" => $shipping->getCountry()
            );
            Mage::log('shipping',null,'twocheckout.log',true);
            array_merge($shippingAddr, $params);
        }

		return $params;
	}

}
