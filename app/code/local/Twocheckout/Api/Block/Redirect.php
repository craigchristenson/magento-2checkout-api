<?php

class Twocheckout_Api_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $tco = Mage::getModel('twocheckout/paypal');

        $form = new Varien_Data_Form();
        $form->setAction($tco->getUrl())
            ->setId('pay')
            ->setName('pay')
            ->setMethod('POST')
            ->setUseContainer(true);
        $tco->getFormFields();
        foreach ($tco->getFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value, 'size'=>200));
        }

        $html = '<html><body>';
        $html.= $tco->getRedirectMessage();
        $html.= $form->toHtml();
        $html.= '<br>';
        $html.= '<script type="text/javascript">document.getElementById("pay").submit();</script>';
        $html.= '</body></html>';


        return $html;
    }
}