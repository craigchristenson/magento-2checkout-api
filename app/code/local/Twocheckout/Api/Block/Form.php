<?php


class Twocheckout_Api_Block_Form extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('twocheckout/form.phtml');
    }

    public function getTwocheckoutCcMonths()
    {
        $months[0] = $this->__('Month');
        $months = array_merge($months, Mage::getSingleton('payment/config')->getMonths());
        foreach($months as $key=>$value) {
            $monthNum = ($key < 10) ? '0'.$key : $key;
            $data[$monthNum] = $value;
        }
        unset($months);
        return $data;
    }

    public function getTwocheckoutCcYears()
    {
        $years = Mage::getSingleton('payment/config')->getYears();
        $years = array(0 => $this->__('Year')) + $years;
        return $years;
    }

    public function getPublicKey()
    {
        $twocheckout = Mage::getModel('twocheckout/payment');
        $key = $twocheckout->getPublicKey();
        return $key;
    }

    public function getSellerId()
    {
        $twocheckout = Mage::getModel('twocheckout/payment');
        $sid = $twocheckout->getSid();
        return $sid;
    }

}
