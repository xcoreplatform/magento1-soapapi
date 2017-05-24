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
     * Retrive invoices list. Filtration could be applied
     *
     * @param null|object|array $filters
     * @return array
     */
    public function items($filters = null)
    {
        $invoices = array();
        /** @var $invoiceCollection Mage_Sales_Model_Mysql4_Order_Invoice_Collection */
        $invoiceCollection = Mage::getResourceModel('sales/order_invoice_collection');
        $invoiceCollection->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('order_id')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('state')
            ->addAttributeToSelect('grand_total')
            ->addAttributeToSelect('order_currency_code');

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        try {
            $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['invoice']);
            foreach ($filters as $field => $value) {
                $invoiceCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }

        // enable retrieving invoices in batches
        $invoiceCollection->addAttributeToSort('updated_at', 'ASC');

        $listLimit = Mage::helper('dealer4dealer_xcore')->getInvoiceListLimit(0);
        $invoiceCollection->getSelect()->limit($listLimit);

        foreach ($invoiceCollection as $invoice) {
            $invoices[] = $this->_getAttributes($invoice, 'invoice');
        }
        return $invoices;
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