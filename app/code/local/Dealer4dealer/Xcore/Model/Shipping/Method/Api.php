<?php
class Dealer4dealer_Xcore_Model_Shipping_Method_Api extends  Mage_Api_Model_Resource_Abstract
{
    /**
     * Get a list of payment methods.
     *
     * @return array
     */
    public function info()
    {
        $result     = array();
        $carriers    = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach ($carriers as $ccode => $carrier) {
            if(!$title = Mage::getStoreConfig("carriers/$ccode/title"))
                $title = $ccode;

            foreach($carrier->getAllowedMethods() as $mcode => $method) {
                $code = $ccode . "_" . $mcode;

                $result[] = array(
                    'code'  => $code,
                    'label' => $title. " - ".$method,
                );
            }
        }
        return $result;
    }
}