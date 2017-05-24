<?php
class Dealer4dealer_Xcore_Model_Sales_Order_Creditmemo_Api_V2 extends Mage_Sales_Model_Order_Creditmemo_Api_V2
{
    /**
     * Add custom fields to the sales order api.
     * See README.md for more information.
     *
     * @param string $creditmemoIncrementId
     * @return array
     */
    public function info($creditmemoIncrementId)
    {
        $result = parent::info($creditmemoIncrementId);
        $creditmemo  = $this->_getCreditmemo($creditmemoIncrementId);

        $this->dispatchEvents($creditmemo);

        // Add missing shipping discount
        $baseShippingDiscountAmount = number_format($creditmemo->getOrder()->getBaseShippingDiscountAmount(), 4);
        $shippingDiscountAmount = number_format($creditmemo->getOrder()->getShippingDiscountAmount(), 4);
        $result['xcore_base_shipping_discount_amount'] = $baseShippingDiscountAmount;
        $result['xcore_shipping_discount_amount'] = $shippingDiscountAmount;

        /** @var Dealer4dealer_Xcore_Model_Payment_Fee $paymentFee */
        foreach ($this->_getPaymentFees($creditmemo) as $paymentFee) {
            $result['xcore_payment_fees'][] = $paymentFee->toArray();
        }

        /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttribute */
        foreach ($this->_getCustomAttributes($creditmemo) as $customAttribute) {
            $result['xcore_custom_attributes'][] = $customAttribute->toArray();
        }

        return $result;
    }

    /**
     * Retrieve credit memos list. Filtration could be applied
     *
     * @param null|object|array $filters
     * @return array
     */
    public function items($filters = null)
    {
        $creditmemos = array();
        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['creditmemo']);
        /** @var $creditmemoModel Mage_Sales_Model_Order_Creditmemo */
        $creditmemoModel = Mage::getModel('sales/order_creditmemo');
        try {
            $creditMemoCollection = $creditmemoModel->getFilteredCollectionItems($filters);

            // enable retrieving credits in batches
            $creditMemoCollection->addAttributeToSort('updated_at', 'ASC');

            $listLimit = Mage::helper('dealer4dealer_xcore')->getCreditListLimit(0);
            $creditMemoCollection->getSelect()->limit($listLimit);

            foreach ($creditMemoCollection as $creditmemo) {
                $creditmemos[] = $this->_getAttributes($creditmemo, 'creditmemo');
            }
        } catch (Exception $e) {
            $this->_fault('invalid_filter', $e->getMessage());
        }
        return $creditmemos;
    }

    /**
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return array
     */
    protected function _getPaymentFees($creditmemo)
    {
        $fees = $creditmemo->getData(Dealer4dealer_Xcore_Helper_Data::PAYMENT_FIELD);

        if (is_array($fees)) {
            return $fees;
        }

        return array();
    }

    /**
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return array
     */
    protected function _getCustomAttributes($creditmemo)
    {
        $mapping = Mage::helper('dealer4dealer_xcore')->getMappingData(Dealer4dealer_Xcore_Helper_Data::XPATH_CREDIT_COLUMNS_MAPPING, $creditmemo->getStoreId());

        $response = [];
        foreach ($mapping as $column) {
            /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttributes */
            $customAttributes = Mage::getModel('dealer4dealer_xcore/custom_attribute');
            $response[] = $customAttributes->setData([
                'key'       => $column['exact_key'],
                'value'     => $creditmemo->getData($column['column'])
            ]);
        }

        return $response;
    }

    /**
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     */
    protected function dispatchEvents($creditmemo)
    {
        Mage::dispatchEvent('dealer4dealer_xcore_sales_order_creditmemo_payment_fee', array(
            'creditmemo' => $creditmemo,
        ));

        Mage::dispatchEvent('dealer4dealer_xcore_sales_order_creditmemo_custom_attributes', array(
            'creditmemo' => $creditmemo,
        ));
    }
}