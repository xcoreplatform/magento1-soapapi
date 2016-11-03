<?php
class Dealer4dealer_Xcore_Model_Sales_Order_Api_V2 extends Mage_Sales_Model_Order_Api_V2
{
    /**
     * Add custom fields to the sales order api.
     * See README.md for more information.
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function info($orderIncrementId)
    {
        $result = parent::info($orderIncrementId);
        $order  = $this->_initOrder($orderIncrementId);

        $this->dispatchEvents($order);

        /** @var Dealer4dealer_Xcore_Model_Payment_Fee $paymentFee */
        foreach ($this->_getPaymentFees($order) as $paymentFee) {
            $result['xcore_payment_fees'][] = $this->_preparePaymentFeeResult($paymentFee);
        }
        
        /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttribute */
        foreach ($this->_getCustomAttributes($order) as $customAttribute) {
            $result['xcore_custom_attributes'][] = $this->_prepareCustomAttributeResult($customAttribute);
        }

        return $result;
    }

    /**
     * @param Dealer4dealer_Xcore_Model_Payment_Fee $paymentFee
     * @return array
     */
    protected function _preparePaymentFeeResult($paymentFee)
    {
        return array(
            'base_amount'   => $paymentFee->getBaseAmount(),
            'amount'        => $paymentFee->getAmount(),
            'tax_rate'      => $paymentFee->getTaxRate(),
            'title'         => $paymentFee->getTitle(),
        );
    }

    /**
     * @param Dealer4dealer_Xcore_Model_Custom_Attribute $customAttribute
     * @return array
     */
    protected function _prepareCustomAttributeResult($customAttribute)
    {
        return array(
            'key'   => $customAttribute->getKey(),
            'value' => $customAttribute->getValue(),
        );
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    protected function _getPaymentFees($order)
    {
        $fees = $order->getData(Dealer4dealer_Xcore_Helper_Data::PAYMENT_FIELD);

        if (is_array($fees)) {
            return $fees;
        }

        return array();
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    protected function _getCustomAttributes($order)
    {
        $customAttributes = $order->getData(Dealer4dealer_Xcore_Helper_Data::CUSTOM_ATTRIBUTE_FIELD);

        if (is_array($customAttributes)) {
            return $customAttributes;
        }

        return array();
    }

    /**
     * @param Mage_Sales_Model_Order $order
     */
    protected function dispatchEvents($order)
    {
        Mage::dispatchEvent('dealer4dealer_xcore_sales_order_payment_fee', array(
            'order' => $order,
        ));

        Mage::dispatchEvent('dealer4dealer_xcore_sales_order_custom_attributes', array(
            'order' => $order,
        ));
    }
}
