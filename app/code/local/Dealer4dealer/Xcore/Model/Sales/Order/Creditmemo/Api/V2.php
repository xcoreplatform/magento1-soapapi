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
        $customAttributes = $creditmemo->getData(Dealer4dealer_Xcore_Helper_Data::CUSTOM_ATTRIBUTE_FIELD);

        if (is_array($customAttributes)) {
            return $customAttributes;
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