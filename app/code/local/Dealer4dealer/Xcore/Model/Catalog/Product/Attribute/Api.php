<?php
class Dealer4dealer_Xcore_Model_Catalog_Product_Attribute_Api extends Mage_Api_Model_Resource_Abstract
{
    private $writeConnection;
    private $_tierPriceTable;

    private $_success           = array();
    private $_faults            = array();

    public function __construct()
    {
        $this->writeConnection  = $this->_getWriteConnection();
        $this->_tierPriceTable  = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_tier_price');
    }

    /**
     * Creates tier prices
     *
     * @param $tierPrices
     * @return stdClass
     */
    public function tierPriceCreate($tierPrices)
    {
        foreach ($tierPrices as $data) {

            if ($productId = $this->getProductIdBySku($data->sku)) {

                $values = $this->_prepareTierPrices($productId, $data->tierPrices);
                $this->_clearPreviousTiersFromProduct($productId);

                foreach($values as $tierPrice) {
                    $this->writeConnection->insert($this->_tierPriceTable, $tierPrice);

                    $this->_addSuccess($data->sku);
                }
            } else {
                $this->_addFault($data->sku, sprintf('Product %s does not exists', $data->sku));
            }
        }

        return $this->_result();
    }

    /**
     * Prepare tier prices
     *
     * @param int $productId
     * @param $tierPrices
     * @return array|null
     * @throws Mage_Api_Exception
     */
    private function _prepareTierPrices($productId, $tierPrices)
    {
        $updateValues = array();

        foreach($tierPrices as $tierPrice) {

            // Make sure the required fields are set (no wsdl validation in de api).
            if (!isset($tierPrice->qty)) {
                $this->_fault('invalid_data', 'The qty field is required');
            }

            if (!isset($tierPrice->price)) {
                $this->_fault('invalid_data', 'The price field is required');
            }

            // Set the default values for optional fields
            if (!isset($tierPrice->website_id)) {
                $tierPrice->website_id = 0;
            }

            if (!isset($tierPrice->all_groups)) {
                $tierPrice->all_groups = 0;
            }

            if (!isset($tierPrice->customer_group_id)) {
                $tierPrice->customer_group_id = Mage_Customer_Model_Group::CUST_GROUP_ALL;
            }

            $updateValues[] = array(
                'entity_id'         => $productId,
                'all_groups'        => $tierPrice->all_groups,
                'customer_group_id' => $tierPrice->customer_group_id,
                'qty'               => $tierPrice->qty,
                'value'             => $tierPrice->price,
                'website_id'        => $tierPrice->website_id,
            );
        }

        return $updateValues;
    }

    /**
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getWriteConnection()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    /**
     * Check if product exists in database
     *
     * @param string $sku
     * @return int|bool
     */
    protected function getProductIdBySku($sku)
    {
        return Mage::getModel('catalog/product')->getIdBySku($sku);
    }

    /**
     * Get table name
     *
     * @param $tableName
     * @return mixed
     */
    protected function _getTableName($tableName)
    {
        return Mage::getSingleton('core/resource')->getTableName($tableName);
    }

    /**
     * Delete previous tierprices
     *
     * @param $productId
     */
    private function _clearPreviousTiersFromProduct($productId)
    {
        $this->writeConnection->delete($this->_tierPriceTable, array(
            'entity_id = ?' => $productId
        ));
    }

    /**
     * Add a sku to the list of errors.
     *
     * @param $sku
     * @param $message
     * @return stdClass
     */
    private function _addFault($sku, $message)
    {
        $fault          = new stdClass();
        $fault->sku     = $sku;
        $fault->message = $message;

        $this->_faults[$sku] = $message;
    }

    /**
     * Add a sku to the list of successful changed products.
     *
     * @param $sku
     */
    private function _addSuccess($sku) {
        $this->_success[] = $sku;
    }

    /**
     * Create result from $_success and $_faults
     *
     * @return stdClass
     */
    private function _result()
    {
        $class = new stdClass();

        if(empty($this->_faults)) {
            return true;
        } else {
            $message = "Partially updated tierprices: \nSuccessfull updated: \n";
            if(empty($this->_success))
                $message .= "None";
            else
                $message .= implode(", ",$this->_success);
            $message .= "\nFailed to update:\n";
            foreach($this->_faults as $sku => $fault) {
                $message .= $sku . " - " . $fault . "\n";
            }
            $this->_fault('partially_updated_tierprices', $message);
        }

        return $class;
    }
}