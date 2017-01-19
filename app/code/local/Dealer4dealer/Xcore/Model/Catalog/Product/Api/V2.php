<?php
class Dealer4dealer_Xcore_Model_Catalog_Product_Api_V2 extends Mage_Catalog_Model_Product_Api_V2
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

        $product = Mage::getModel('catalog/product')->load($productId);

        /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttribute */
        foreach ($this->_getCustomAttributes($product) as $customAttribute) {
            $result['xcore_custom_attributes'][] = $customAttribute->toArray();
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

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getCustomAttributes($product)
    {
        $mapping = Mage::helper('dealer4dealer_xcore')->getMappingData(Dealer4dealer_Xcore_Helper_Data::XPATH_PRODUCT_COLUMNS_MAPPING);

        /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttributes */
        $customAttributes = Mage::getModel('dealer4dealer_xcore/custom_attribute');
        $response = [];

        foreach ($mapping as $column) {
            $response[] = $customAttributes->setData([
                'key'       => $column['exact_key'],
                'value'     => $product->getData($column['column'])
            ]);
        }

        return $response;
    }

    /**
     * @param Mage_Catalog_Model_Product
     */
    protected function dispatchEvents($product)
    {
        Mage::dispatchEvent('dealer4dealer_xcore_product_product_custom_attributes', array(
            'product' => $product,
        ));
    }
}