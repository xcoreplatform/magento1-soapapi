<?php
class Dealer4dealer_Xcore_Model_Product_Api extends Mage_Api_Model_Resource_Abstract
{
    public function items()
    {
        $arr_products=array();
        $products=Mage::getModel("catalog/product")
            ->getCollection()
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', 'DESC')
            ->setPageSize(5);

        foreach ($products as $product) {
            $arr_products[] = $product->toArray();
        }

        return $arr_products;
    }

    public function taxClassesList()
    {
        $result = array();

        $taxClasses = Mage::getModel("tax/class")
            ->getCollection()
            ->addFieldToFilter('class_type','PRODUCT')
            ->setOrder('class_id', 'DESC');

        foreach($taxClasses as $taxClass) {
            $result[] = $taxClass->toArray();
        }

        return $result;
    }
}