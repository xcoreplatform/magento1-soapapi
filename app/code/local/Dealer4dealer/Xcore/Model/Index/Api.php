<?php
class Dealer4dealer_Xcore_Model_Index_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Reindex all the indexes of Magento.
     *
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function reindexAll()
    {
        try {
            $indexCollection = Mage::getModel('index/process')->getCollection();
            foreach ($indexCollection as $index) {
                $index->reindexAll();
            }

        } catch (Exception $e) {
            $this->_fault('reindex_all_failed', "Reindex failed because: " . $e);
        }

        return true;
    }

    /**
     * Reindex a specific index.
     *
     * @param $type
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function reindex($type)
    {
        try {
            $process = Mage::getModel('index/indexer')->getProcessByCode($type);
            if($process) {
                $process->reindexAll();
            } else {
                $this->_fault('reindex_failed', "Reindex of '" . $type . "' failed because not found type.");
            }
        } catch (Exception $e) {
            $this->_fault('reindex_failed', "Reindex of '" . $type . "' failed because: ". $e);
        }
        return true;
    }
}