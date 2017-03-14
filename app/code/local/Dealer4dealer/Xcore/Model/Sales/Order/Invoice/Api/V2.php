<?php
class Dealer4dealer_Xcore_Model_Sales_Order_Invoice_Api_V2 extends Mage_Sales_Model_Order_Invoice_Api_V2
{
    /**
     * Add custom fields to the sales order api.
     * See README.md for more information.
     *
     * @param string $invoiceIncrementId
     * @return array
     */
    public function info($invoiceIncrementId)
    {
        $result = parent::info($invoiceIncrementId);

        $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);
        /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttribute */
        foreach ($this->_getCustomAttributes($invoice) as $customAttribute) {
            $result['xcore_custom_attributes'][] = $customAttribute->toArray();
        }

        return $result;
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return array
     */
    protected function _getCustomAttributes($invoice)
    {
        $mapping = Mage::helper('dealer4dealer_xcore')->getMappingData(Dealer4dealer_Xcore_Helper_Data::XPATH_INVOICE_COLUMNS_MAPPING, $invoice->getStoreId());

        $response = [];
        foreach ($mapping as $column) {
            /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttributes */
            $customAttributes = Mage::getModel('dealer4dealer_xcore/custom_attribute');
            $response[] = $customAttributes->setData([
                'key'       => $column['exact_key'],
                'value'     => $invoice->getData($column['column'])
            ]);
        }

        return $response;
    }

}