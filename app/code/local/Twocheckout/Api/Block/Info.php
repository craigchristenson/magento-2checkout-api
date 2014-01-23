<?php


class Twocheckout_Api_Block_Info extends Mage_Payment_Block_Info {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('twocheckout/info.phtml');
    }

    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }

}