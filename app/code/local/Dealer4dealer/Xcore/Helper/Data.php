<?php
class Dealer4dealer_Xcore_Helper_Data extends Mage_Core_Helper_Abstract
{
    const PAYMENT_FIELD             = 'xcore_payment_fees';
    const CUSTOM_ATTRIBUTE_FIELD    = 'xcore_custom_attributes';
    const XPATH_PAYMENT_FEE_MAPPING = 'xcore_payment/fee/mapping';
    const XPATH_ORDER_COLUMNS_MAPPING = 'xcore_sales/order/mapping';
    const XPATH_CREDIT_COLUMNS_MAPPING = 'xcore_sales/credit/mapping';

    /**
     * Init a payment fee model.
     *
     * @param array $data
     * @return Dealer4dealer_Xcore_Model_Payment_Fee
     */
    public function initPaymentFeeModel($data)
    {
        $fee = Mage::getModel('dealer4dealer_xcore/payment_fee');
        $fee->setData($data);

        return $fee;
    }

    /**
     * Add a payment fee to the order.
     *
     * @param Mage_Sales_Model_Order $order
     * @param array $fee
     */
    public function addPaymentFee($order , $fee)
    {
        $currentFees    = $order->getData(self::PAYMENT_FIELD);
        $fee            = $this->initPaymentFeeModel($fee);

        if (!is_array($currentFees)) {
            $currentFees = array();
        }

        $currentFees[] = $fee;

        $order->setData(self::PAYMENT_FIELD, $currentFees);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return (string) Mage::getConfig()->getNode()->modules->dealer4dealer_xcore->version;

    }

    /**
     * @param null|int $storeId
     * @return array
     */
    public function getPaymentFeesData($storeId = null)
    {
        return $this->getMappingData(self::XPATH_PAYMENT_FEE_MAPPING, $storeId);
    }

    /**
     * @param string $sysconKey
     * @param null|int $storeId
     * @return array
     */
    public function getMappingData($sysconKey, $storeId = null)
    {
        $mapping = Mage::getStoreConfig($sysconKey, $storeId);
        if ($mapping) {
            $mapping = unserialize($mapping);
            return array_values((array)$mapping); // cast to array and strip any keys
        }

        return [];
    }

}