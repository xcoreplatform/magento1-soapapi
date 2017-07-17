<?php
class Dealer4dealer_Xcore_Model_Custom_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * @return array
     */
    public function itemAttributes()
    {
        return $this->_getMappingsValues(Dealer4dealer_Xcore_Helper_Data::XPATH_PRODUCT_COLUMNS_MAPPING);
    }

    /**
     * @param $attribute
     * @return array
     */
    public function itemAttributeOptions($attribute)
    {
        // Get the mapped attribute
        $mappings = Mage::helper('dealer4dealer_xcore')->getMappingData(Dealer4dealer_Xcore_Helper_Data::XPATH_PRODUCT_COLUMNS_MAPPING);
        foreach($mappings as $mapping) {
            if($mapping['exact_key'] == $attribute) {
                $attribute = $mapping['column'];
            }
        }

        // Get the options
        $options = [];
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attribute);
        if ($attribute->usesSource()) {
            $sourceOptions = $attribute->getSource()->getAllOptions(false);
            foreach($sourceOptions as $sourceOption) {
                $options[] = [
                    'key'       => $sourceOption['value'],
                    'value'     => $sourceOption['label']
                ];
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public function customerAttributes()
    {
        return $this->_getMappingsValues(Dealer4dealer_Xcore_Helper_Data::XPATH_CUSTOMER_COLUMNS_MAPPING);
    }

    /**
     * @return array
     */
    public function saleAttributes()
    {
        return $this->_getMappingsValues(Dealer4dealer_Xcore_Helper_Data::XPATH_ORDER_COLUMNS_MAPPING);
    }

    /**
     * @return array
     */
    public function invoiceAttributes()
    {
        return $this->_getMappingsValues(Dealer4dealer_Xcore_Helper_Data::XPATH_INVOICE_COLUMNS_MAPPING);
    }

    /**
     * @return array
     */
    public function creditAttributes()
    {
        return $this->_getMappingsValues(Dealer4dealer_Xcore_Helper_Data::XPATH_CREDIT_COLUMNS_MAPPING);
    }

    private function _getMappingsValues($model) {

        $mapping = Mage::helper('dealer4dealer_xcore')->getMappingData($model);

        $response = [];
        foreach ($mapping as $column) {
            /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttribute */
            $customAttribute = Mage::getModel('dealer4dealer_xcore/custom_attribute');
            $customAttribute->setData([
                'key'       => $column['exact_key'],
                'value'     => $column['column']
            ]);
            $response[] = $customAttribute;
        }

        return $response;
    }
}