<?php
class Dealer4dealer_Xcore_Model_Version_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Get the current module version.
     *
     * @return string
     */
    public function info()
    {
        return Mage::helper('dealer4dealer_xcore')->getVersion();
    }
}