<?php
class Dealer4dealer_Xcore_Model_Customer_Customer_Api_V2 extends Mage_Customer_Model_Customer_Api_V2
{
    /**
     * Retrieve customer data
     *
     * @param int $customerId
     * @param array $attributes
     * @return array
     */
    public function info($customerId, $attributes = null)
    {
        $result = parent::info($customerId, $attributes);

        $customer = Mage::getModel('customer/customer')->load($customerId);

        /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttribute */
        foreach ($this->_getCustomAttributes($customer) as $customAttribute) {
            $result['xcore_custom_attributes'][] = $customAttribute->toArray();
        }

        return $result;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     */
    protected function _getCustomAttributes($customer)
    {
        $mapping = Mage::helper('dealer4dealer_xcore')->getMappingData(Dealer4dealer_Xcore_Helper_Data::XPATH_CUSTOMER_COLUMNS_MAPPING, $customer->getStoreId());

        $response = [];
        foreach ($mapping as $column) {
            $value = $customer->getData($column['column']);

            // Get the frontend value instead of option value
            if($value) {
                $attribute = $customer->getResource()->getAttribute($column['column']);
                if ($attribute) {
                    $value = $attribute->getFrontend()->getValue($customer);
                }
            }

            /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttributes */
            $customAttributes = Mage::getModel('dealer4dealer_xcore/custom_attribute');
            $response[] = $customAttributes->setData([
                'key'       => $column['exact_key'],
                'value'     => $value
            ]);
        }

        return $response;
    }

    /**
     * @param Mage_Customer_Model_Customer
     */
    protected function dispatchEvents($customer)
    {
        Mage::dispatchEvent('dealer4dealer_xcore_customer_customer_custom_attributes', array(
            'customer' => $customer,
        ));
    }
}