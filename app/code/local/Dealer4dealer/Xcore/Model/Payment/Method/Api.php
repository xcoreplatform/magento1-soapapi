<?php
class Dealer4dealer_Xcore_Model_Payment_Method_Api extends  Mage_Api_Model_Resource_Abstract
{
    /**
     * Get a list of payment methods.
     *
     * @return array
     */
    public function info()
    {
        $result     = array();
        $methods    = Mage::getSingleton('payment/config')->getActiveMethods();

        foreach ($methods as $code => $method) {

            $title = Mage::getStoreConfig('payment/' . $code . '/title');

            $result[] = array(
                'code'  => $code,
                'label' => $title,
            );
        }

        return $result;
    }
}