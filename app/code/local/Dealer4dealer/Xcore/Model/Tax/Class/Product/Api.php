<?php
class Dealer4dealer_Xcore_Model_Tax_Class_Product_Api extends Mage_Api_Model_Resource_Abstract
{
    public function items()
    {
        $taxClasses = Mage::getModel('tax/class_source_product')->getAllOptions();

        $result = array();
        foreach ($taxClasses as $taxClass) {
            $result[] = array(
                'tax_class_id'  => $taxClass['value'],
                'label'         => $taxClass['label'],
            );
        }

        return $result;
    }
}