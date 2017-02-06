<?php
class Dealer4dealer_Xcore_Model_Website_Api extends Mage_Api_Model_Resource_Abstract
{

    /**
     * List all websites
     *
     * @return array
     */
    public function items()
    {
        $result = array();
        $websites = Mage::app()->getWebsites();

        foreach ($websites as $website) {
            $result[] = $website->toArray();
        }

        return $result;
    }

}