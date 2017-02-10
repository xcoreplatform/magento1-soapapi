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

        // Add missing shipping discount
        $baseShippingDiscountAmount = number_format($order->getBaseShippingDiscountAmount(), 4);
        $shippingDiscountAmount = number_format($order->getShippingDiscountAmount(), 4);
        $result['xcore_base_shipping_discount_amount'] = $baseShippingDiscountAmount;
        $result['xcore_shipping_discount_amount'] = $shippingDiscountAmount;

        /** @var Dealer4dealer_Xcore_Model_Payment_Fee $paymentFee */
        foreach ($this->_getPaymentFees($order) as $paymentFee) {
            $result['xcore_payment_fees'][] = $paymentFee->toArray();
        }

        /** @var Dealer4dealer_Xcore_Model_Custom_Attribute $customAttribute */
        foreach ($this->_getCustomAttributes($order) as $customAttribute) {
            $result['xcore_custom_attributes'][] = $customAttribute->toArray();
        }

        return $result;
    }

    /**
     * Get a list of all possible states
     *
     * @return mixed
     */
    public function states()
    {
        $states = Mage::getModel('sales/order_state')->getCollection()
            ->toOptionArray();

        return $states;
    }

    /**
     * Get a list of all possible statuses
     *
     * @return array
     */
    public function statuses()
    {
        $statuses = Mage::getModel('sales/order_status')->getCollection()
            ->toOptionArray();

        return $statuses;
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
        $mapping = Mage::helper('dealer4dealer_xcore')->getMappingData(Dealer4dealer_Xcore_Helper_Data::XPATH_ORDER_COLUMNS_MAPPING, $order->getStoreId());

        $response = [];
        foreach ($mapping as $column) {
            $value = $order->getData($column['column']);

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
