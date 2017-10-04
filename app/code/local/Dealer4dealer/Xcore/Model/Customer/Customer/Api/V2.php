<?php
class Dealer4dealer_Xcore_Model_Customer_Customer_Api_V2 extends Mage_Customer_Model_Customer_Api_V2
{
    /**
     * Retrieve customers data by filters and limit
     *
     * @param  object|array $filters
     * @param null $limit
     * @return array
     */
    public function items($filters, $limit = null)
    {
        $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');

        if($limit) {
            $collection->setOrder('updated_at', 'ASC');
            $collection->setPageSize($limit);
        }

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_mapAttributes);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $customer) {
            $data = $customer->toArray();
            $row  = array();
            foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
                $row[$attributeAlias] = (isset($data[$attributeCode]) ? $data[$attributeCode] : null);
            }
            foreach ($this->getAllowedAttributes($customer) as $attributeCode => $attribute) {
                if (isset($data[$attributeCode])) {
                    $row[$attributeCode] = $data[$attributeCode];
                }
            }
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Create new customer
     *
     * @param array $customerData
     * @return int
     */
    public function create($customerData)
    {
        $customerId = parent::create($customerData);

        if($customerId) {
            if ($customAttributes = $customerData->xcore_custom_attributes) {
                $customer = Mage::getModel('customer/customer')->load($customerId);
                foreach ($customAttributes as $attribute) {
                    $customer->setData($attribute->key, $attribute->value);
                }
                $customer->save();
            }
        }
        return $customerId;
    }

    /**
     * Update customer data
     *
     * @param int $customerId
     * @param array $customerData
     * @return boolean
     */
    public function update($customerId, $customerData)
    {
        if($customerData->xcore_custom_attributes) {
            $customAttributes = $customerData->xcore_custom_attributes;
            foreach($customAttributes as $attribute) {
                $customerData->{$attribute->key} = $attribute->value;
            }
        }

        return parent::update($customerId, $customerData);;
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