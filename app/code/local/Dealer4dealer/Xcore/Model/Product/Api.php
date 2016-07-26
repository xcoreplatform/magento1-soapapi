<?php
class Dealer4dealer_Xcore_Model_Product_Api extends Mage_Api_Model_Resource_Abstract
{
    public function items()
    {
        $result = array();
        $products = Mage::getModel("catalog/product")
            ->getCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setOrder('entity_id', 'DESC')
            ->setPageSize(5);

        foreach ($products as $product) {
            $result[] = $product->toArray();
        }

        return $result;
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