<?php
class Dealer4dealer_Xcore_Model_Catalog_Product_Api extends Mage_Catalog_Model_Product_Api
{
    /**
     * Retrieve product info and attach the default
     * tax rate based on the tax_class_id.
     *
     * @param int|string $productId
     * @param string|int $store
     * @param stdClass   $attributes
     * @param string     $identifierType
     * @return array
     */
    public function info($productId, $store = null, $attributes = null, $identifierType = null)
    {
        $result = parent::info($productId, $store, $attributes, $identifierType);

        if (isset($result['tax_class_id'])) {
            $taxRate = $this->_getTaxRate($result['tax_class_id']);
            $result['tax_rate'] = $taxRate;
        }

        return $result;
    }

    /**
     * @return array
     * @throws Mage_Api_Exception
     */
    public function taxClassesList()
    {
        $result = array();

        $taxClasses = Mage::getModel('tax/class')
            ->getCollection()
            ->addFieldToFilter('class_type', Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
            ->setOrder('class_id', 'DESC');

        foreach($taxClasses as $taxClass) {
            $result[] = $taxClass->toArray();
        }

        return $result;
    }

    /**
     * Get the default tax rate of a product based on the default
     * calculation country and the tax class id.
     *
     * @param int $taxClassId
     * @return float
     */
    protected function _getTaxRate($taxClassId)
    {
        $countryId      = Mage::getStoreConfig('tax/defaults/country');
        $rateRequest    = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);

        $rateRequest->setCountryId($countryId);
        $rateRequest->setProductClassID($taxClassId);

        $taxRate = Mage::getSingleton('tax/calculation')->getRate($rateRequest);

        return $taxRate;
    }
}