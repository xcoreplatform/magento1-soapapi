<?php
class Dealer4dealer_Xcore_Model_Sales_Order_Api_V2 extends Mage_Sales_Model_Order_Api_V2
{
    /**
     * Add payment fees to the sales order api.
     *
     * To add your own payment fees follow these steps.
     *
     * 1. Listen for the event dealer4dealer_xcore_sales_order_payment_fee.
     * 2. Fetch the order object in your observer with $observer->getOrder();
     * 3. Create a new dealer4dealer_xcore/payment_fee object
     * 4. Add te payment object to the xcore_payment_fees field (array) on the order.
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function info($orderIncrementId)
    {
        $result = parent::info($orderIncrementId);
        $order  = $this->_initOrder($orderIncrementId);

        // Dispatch a event so that other modules can add their payment fees to the order object.
        Mage::dispatchEvent('dealer4dealer_xcore_sales_order_payment_fee', array(
            'order' => $order,
        ));

        /** @var Dealer4dealer_Xcore_Model_Payment_Fee $paymentFee */
        foreach ($this->_getPaymentFees($order) as $paymentFee) {
            $result['payment_fees'][] = $this->_prepareResult($paymentFee);
        }

        return $result;
    }

    /**
     * @param Dealer4dealer_Xcore_Model_Payment_Fee $paymentFee
     * @return array
     */
    protected function _prepareResult($paymentFee)
    {
        return array(
            'base_amount'   => $paymentFee->getBaseAmount(),
            'amount'        => $paymentFee->getAmount(),
            'tax_rate'      => $paymentFee->getTaxRate(),
            'title'         => $paymentFee->getTitle(),
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
}
