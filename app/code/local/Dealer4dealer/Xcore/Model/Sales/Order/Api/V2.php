<?php
class Dealer4dealer_Xcore_Model_Sales_Order_Api_V2 extends Mage_Sales_Model_Order_Api_V2
{
    /**
     * Retrieve list of orders. Filtration could be applied
     *
     * @param null|object|array $filters
     * @param null $limit
     * @return array
     */
    public function items($filters = null, $limit = null)
    {
        $orders = array();

        //TODO: add full name logic
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';

        /** @var $orderCollection Mage_Sales_Model_Mysql4_Order_Collection */
        $orderCollection = Mage::getModel("sales/order")->getCollection();

        if($limit) {
            $orderCollection->setOrder('updated_at', 'ASC');
            $orderCollection->setPageSize($limit);
        }

        $billingFirstnameField = "$billingAliasName.firstname";
        $billingLastnameField = "$billingAliasName.lastname";
        $shippingFirstnameField = "$shippingAliasName.firstname";
        $shippingLastnameField = "$shippingAliasName.lastname";
        $orderCollection->addAttributeToSelect('*')
                        ->addAddressFields()
                        ->addExpressionFieldToSelect('billing_firstname', "{{billing_firstname}}",
                                                     array('billing_firstname' => $billingFirstnameField))
                        ->addExpressionFieldToSelect('billing_lastname', "{{billing_lastname}}",
                                                     array('billing_lastname' => $billingLastnameField))
                        ->addExpressionFieldToSelect('shipping_firstname', "{{shipping_firstname}}",
                                                     array('shipping_firstname' => $shippingFirstnameField))
                        ->addExpressionFieldToSelect('shipping_lastname', "{{shipping_lastname}}",
                                                     array('shipping_lastname' => $shippingLastnameField))
                        ->addExpressionFieldToSelect('billing_name', "CONCAT({{billing_firstname}}, ' ', {{billing_lastname}})",
                                                     array('billing_firstname' => $billingFirstnameField, 'billing_lastname' => $billingLastnameField))
                        ->addExpressionFieldToSelect('shipping_name', 'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                                                     array('shipping_firstname' => $shippingFirstnameField, 'shipping_lastname' => $shippingLastnameField)
                        );

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['order']);
        try {
            foreach ($filters as $field => $value) {
                $orderCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        foreach ($orderCollection as $order) {
            $orders[] = $this->_getAttributes($order, 'order');
        }
        return $orders;
    }

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

        foreach($result['items'] as $item) {
            $product = Mage::getModel('catalog/product')->load($item['product_id']);
            $item['xcore_base_sku'] = $product->getSku();
        }

        /** @var Dealer4dealer_Xcore_Model_Payment_Fee $paymentFee */
        foreach ($this->_getPaymentFees($order) as $paymentFee) {
            $result['xcore_payment_fees'][] = $paymentFee->toArray();
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
        $states = Mage::getSingleton('sales/order_config')->getStates();

        $response = [];

        foreach($states as $key => $value)
        {
            $response[] = [
                'key'   => $key,
                'value' => $value,
            ];
        }

        return $response;
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
     */
    protected function dispatchEvents($order)
    {
        Mage::dispatchEvent('dealer4dealer_xcore_sales_order_payment_fee', array(
            'order' => $order,
        ));
    }
}
