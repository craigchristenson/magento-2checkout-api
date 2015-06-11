<?php


require_once Mage::getBaseDir('lib').DS.'Twocheckout.php';

class Twocheckout_Api_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code	=	'twocheckout';
    protected $_isGateway                   = false;
    protected $_canCapture                  = false;
    protected $_canCapturePartial           = false;
    protected $_canRefund                   = true;
    protected $_canUseCheckout = true;
    protected $_isInitializeNeeded = true;
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

    public function getEnvJS()
    {
        if ($this->getDemo()) {
            $result = "<script>var TcoEnv='sandbox'</script>";
        } else {
            $result = "<script>var TcoEnv='production'</script>";
        }
        return $result;
    }

    public function __construct()
    {
        if ($this->getDemo()) {
            Twocheckout::privateKey($this->getPrivateKey());
            Twocheckout::sellerId($this->getSid());
            Twocheckout::sandbox(true);

        } else {
            Twocheckout::privateKey($this->getPrivateKey());
            Twocheckout::sellerId($this->getSid());
            Twocheckout::sandbox(false);
        }
    }

    public function initialize($paymentAction, $stateObject)
    {
        $payment = $this->getInfoInstance();
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
            Mage::throwException(Mage::helper('paygate')->__('Authorization Failed'));
        }

        if ($charge['response']['responseCode'] == 'APPROVED') {
            $payment
                ->setTransactionId($charge['response']['transactionId'])
                ->setIsTransactionClosed(false);
            $order
                ->setData('ext_order_id', $charge['response']['transactionId'])
                ->save();
        } else {
            Mage::throwException(Mage::helper('paygate')->__('Payment capturing error: %s', "Could not Authorize Transaction"));
        }
        return $this;
    }

}
