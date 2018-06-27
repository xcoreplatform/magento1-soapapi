<?php

class Dealer4dealer_Xcore_Model_Sales_Order_Invoice_Api_V2 extends Mage_Sales_Model_Order_Invoice_Api_V2
{
    /**
     * Retrieve invoice list. Filtration could be applied. Also, limit.
     *
     * @param null|object|array $filters
     * @param null $limit
     * @return array
     */
    public function items($filters = null, $limit = null)
    {
        $invoices = array();
        /** @var $invoiceCollection Mage_Sales_Model_Mysql4_Order_Invoice_Collection */
        $invoiceCollection = Mage::getResourceModel('sales/order_invoice_collection');
        $invoiceCollection->addAttributeToSelect('entity_id')
                          ->addAttributeToSelect('order_id')
                          ->addAttributeToSelect('increment_id')
                          ->addAttributeToSelect('created_at')
                          ->addAttributeToSelect('updated_at')
                          ->addAttributeToSelect('state')
                          ->addAttributeToSelect('grand_total')
                          ->addAttributeToSelect('order_currency_code');

        if($limit) {
            $invoiceCollection->setOrder('updated_at', 'ASC');
            $invoiceCollection->setPageSize($limit);
        }

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
        foreach ($invoiceCollection as $invoice) {
            $invoices[] = $this->_getAttributes($invoice, 'invoice');
        }
        return $invoices;








        $invoices = array();
        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['invoice']);
        /** @var $invoiceModel Mage_Sales_Model_Order_Invoice */
        $invoiceModel = Mage::getModel('sales/order_invoice');
        try {
            $invoiceCollection = $invoiceModel->getFilteredCollectionItems($filters);

            if($limit) {
                $invoiceCollection->setOrder('updated_at', 'ASC');
                $invoiceCollection->setPageSize($limit);
            }

            foreach ($invoiceCollection as $creditmemo) {
                $invoices[] = $this->_getAttributes($creditmemo, 'invoice');
            }
        } catch (Exception $e) {
            $this->_fault('invalid_filter', $e->getMessage());
        }
        return $invoices;
    }
}