<?php
/**
 * Dealer4dealer_Xcore extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Dealer4dealer
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Dropdown values for the sales_flat_order table,
 * is referenced by 'amount' and 'baseamount'
 *
 * @category    Dealer4dealer
 * @author      Sander Mangel <sander@sandermangel.nl>
 */
class Dealer4dealer_Xcore_Block_Adminhtml_System_Config_Field_Renderer_Salesflatordercolumn
    extends Mage_Core_Block_Html_Select
{
    /**
     * @return string
     */
    public function _toHtml()
    {
        /* @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        /* @var $resource Varien_Db_Adapter_Interface */
        $readConnection = $resource->getConnection('core_read');

        $dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');

        $results = $readConnection->fetchAll("
SELECT
  `column_name`
FROM
  `information_schema`.`columns`
WHERE
  `table_schema` = '{$dbname}'
   AND `table_name` = '{$resource->getTableName('sales/order')}'
   AND `data_type` IN ('decimal','float')
ORDER BY
  `table_name`, `ordinal_position`
        ");
        
        foreach ($results as $row) {
            $this->addOption($row['column_name'], $row['column_name']);
        }

        return parent::_toHtml();
    }

    /**
     * @param string $value
     * @return string
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
