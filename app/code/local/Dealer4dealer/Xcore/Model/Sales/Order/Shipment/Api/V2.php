<?php

class Dealer4dealer_Xcore_Model_Sales_Order_Shipment_Api_V2 extends Mage_Sales_Model_Order_Shipment_Api_V2
{
    /**
     * Retrieve shipments by filters and limit
     *
     * @param null|object|array $filters
     * @param null $limit
     * @return array
     */
    public function items($filters = null, $limit = null)
    {
        $shipments = array();
        //TODO: add full name logic
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                                  ->addAttributeToSelect('increment_id')
                                  ->addAttributeToSelect('created_at')
                                  ->addAttributeToSelect('total_qty')
                                  ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                                  ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                                  ->joinAttribute('order_increment_id', 'order/increment_id', 'order_id', null, 'left')
                                  ->joinAttribute('order_created_at', 'order/created_at', 'order_id', null, 'left');

        if($limit) {
            $shipmentCollection->setOrder('updated_at', 'ASC');
            $shipmentCollection->setPageSize($limit);
        }

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        try {
            $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['shipment']);
            foreach ($filters as $field => $value) {
                $shipmentCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        foreach ($shipmentCollection as $shipment) {
            $shipments[] = $this->_getAttributes($shipment, 'shipment');
        }

        return $shipments;
    }

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