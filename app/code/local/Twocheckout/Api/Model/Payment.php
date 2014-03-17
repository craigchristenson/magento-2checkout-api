<?php


require_once Mage::getBaseDir('lib').DS.'Twocheckout'.DS.'TwocheckoutApi.php';

class Twocheckout_Api_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code	=	'twocheckout';
    protected $_isGateway                   = false;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = false;
    protected $_canRefund                   = true;
    protected $_canUseCheckout = true;
    protected $_isInitializeNeeded = false;
    protected $_formBlockType = 'twocheckout/form';
    protected $_infoBlockType = 'twocheckout/info';



    public function getSid() {
        $sid = $this->getConfigData('sid');
        return $sid;
    }

    public function getPrivateKey() {
        $key = $this->getConfigData('private_key');
        return $key;
    }

    public function getPublicKey() {
        $key = $this->getConfigData('public_key');
        return $key;
    }

    public function getDemo() {
        $demo = $this->getConfigData('demo');
        return $demo;
    }

    public function getApiUrl()
    {
        if ($this->getDemo()) {
            $link = '<script src="https://sandbox.2checkout.com/checkout/api/script/publickey/key.js"></script>
                        <script src="https://sandbox.2checkout.com/checkout/api/2co.min.js"></script>';
        } else {
            $link = '<script src="https://www.2checkout.com/checkout/api/script/publickey/key.js"></script>
                        <script src="https://www.2checkout.com/checkout/api/2co.min.js"></script>';
        }
        return $link;
    }

    public function __construct()
    {
        if ($this->getDemo()) {
            TwocheckoutApi::setCredentials($this->getSid(), $this->getPrivateKey(), 'sandbox');
        } else {
            TwocheckoutApi::setCredentials($this->getSid(), $this->getPrivateKey());
        }
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();
        $shipping = $order->getShippingAddress();
        $amount = round($order->getGrandTotal(), 2);

        try {

            $params = array(
                "sellerId" => $this->getSid(),
                "merchantOrderId" => $order->getIncrementId(),
                "token"      => Mage::app()->getRequest()->getParam('token'),
                "currency"   => $order->getOrderCurrencyCode(),
                "total"      => $amount,
                "billingAddr" => array(
                    "name" => $billing->getName(),
                    "addrLine1" => $billing->getStreet(1),
                    "addrLine2" => $billing->getStreet(2),
                    "city" => $billing->getCity(),
                    "state" => $billing->getRegion(),
                    "zipCode" => $billing->getPostcode(),
                    "country" => $billing->getCountry(),
                    "email" => $order->getCustomerEmail(),
                    "phoneNumber" => $billing->getTelephone()
                )
            );

            if ($shipping) {
                $shippingAddr = array(
                    "name" => $shipping->getName(),
                    "addrLine1" => $shipping->getStreet(1),
                    "addrLine2" => $shipping->getStreet(2),
                    "city" => $shipping->getCity(),
                    "state" => $shipping->getRegion(),
                    "zipCode" => $shipping->getPostcode(),
                    "country" => $shipping->getCountry(),
                    "email" => $order->getCustomerEmail(),
                    "phoneNumber" => $billing->getTelephone()
                );
                array_merge($shippingAddr, $params);
            }

            $charge = Twocheckout_Charge::auth($params);

        } catch (Twocheckout_Error $e) {
            Mage::throwException(Mage::helper('paygate')->__($e->getMessage()));
        }

        if ($charge['response']['responseCode'] == 'APPROVED') {
            $payment
                ->setTransactionId($charge['response']['transactionId'])
                ->setIsTransactionClosed(0);
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
            $order->setData('ext_order_id',$charge['response']['transactionId'] );
            $order->save();
        } else {
            Mage::throwException(Mage::helper('paygate')->__('Payment capturing error: %s', "Could not Authorize Transaction"));
        }
        return $this;
    }

}
