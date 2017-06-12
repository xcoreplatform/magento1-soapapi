<?php

class Dealer4dealer_Xcore_Model_Sales_Order_Shipment_Api_V2 extends Mage_Sales_Model_Order_Shipment_Api_V2
{
    /**
     * Create new shipment for order
     *
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param boolean $email
     * @param boolean $includeComment
     * @param string $xcoreYourRef
     * @return string
     */
    public function xcoreCreate($orderIncrementId, $itemsQty = array(), $comment = null, $email = false, $includeComment = false, $xcoreYourRef = null)
    {
        $shipmentId = parent::create($orderIncrementId, $itemsQty, $comment);

        if ($shipmentId) {
            $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentId);
            $shipment->setData('xcore_your_ref', $xcoreYourRef);
            $shipment->save();
        }

        return $shipmentId;
    }
}