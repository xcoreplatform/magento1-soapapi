<?php
class Dealer4dealer_Xcore_Model_Sales_Order_Creditmemo_Api_V2 extends Mage_Sales_Model_Order_Creditmemo_Api_V2
{
    /**
     * Retrieve credit memos list. Filtration could be applied. Also, limit.
     *
     * @param null|object|array $filters
     * @param null $limit
     * @return array
     */
    public function items($filters = null, $limit = null)
    {
        $creditmemos = array();
        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['creditmemo']);
        /** @var $creditmemoModel Mage_Sales_Model_Order_Creditmemo */
        $creditmemoModel = Mage::getModel('sales/order_creditmemo');
        try {
            $creditMemoCollection = $creditmemoModel->getFilteredCollectionItems($filters);

            if($limit) {
                $creditMemoCollection->setOrder('updated_at', 'ASC');
                $creditMemoCollection->setPageSize($limit);
            }

            foreach ($creditMemoCollection as $creditmemo) {
                $creditmemos[] = $this->_getAttributes($creditmemo, 'creditmemo');
            }
        } catch (Exception $e) {
            $this->_fault('invalid_filter', $e->getMessage());
        }
        return $creditmemos;
    }

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

        return $result;
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