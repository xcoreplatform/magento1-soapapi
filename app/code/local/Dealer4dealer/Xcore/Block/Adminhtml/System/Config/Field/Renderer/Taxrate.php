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
 * Dropdown values for tax class field
 *
 * @category    Dealer4dealer
 * @author      Sander Mangel <sander@sandermangel.nl>
 */
class Dealer4dealer_Xcore_Block_Adminhtml_System_Config_Field_Renderer_Taxrate
    extends Mage_Core_Block_Html_Select
{
    /**
     * @return string
     */
    public function _toHtml()
    {
        $collection = Mage::getModel('tax/class')->getCollection()
            ->addFieldToFilter('class_type', 'PRODUCT');

        $this->addOption(0, $this->__('None'));
        foreach ($collection as $row) {
            $this->addOption($row->getId(), $row->getClassName());
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
