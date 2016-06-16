<?php
class Dealer4dealer_Xcore_Model_Customer_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Create Customer Group
     *
     * @param $name
     * @param $classId
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function groupCreate($name, $classId)
    {
        try {
            if(isset($name) && $name != "" && $classId >= 0) {
                $customerGroup = Mage::getModel('customer/group');
                $customerGroup->setCode($name);
                $customerGroup->setTaxClassId($classId);
                $customerGroup->save();
            } else {
                $this->_fault('customer_group_not_created', sprintf('Could not create group %s with classId %s', $name, $classId));
            }
        } catch(Exception $e) {
            $this->_fault('customer_group_not_created', sprintf('Could not create group %s with classId %s', $name, $classId));
        }

        return true;
    }
}