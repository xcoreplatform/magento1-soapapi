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
 * Sysconf grid renderer for payment fees
 *
 * @category    Dealer4dealer
 * @author      Sander Mangel <sander@sandermangel.nl>
 */
class Dealer4dealer_Xcore_Block_Adminhtml_System_Config_Field_Fee
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * _prepareToRender
     */
    protected function _prepareToRender()
    {
        $this->addColumn('title', [ 'label' => Mage::helper('dealer4dealer_xcore')->__('Title') ]);
        $this->addColumn('base_amount', [ 'label' => Mage::helper('dealer4dealer_xcore')->__('Base amount field') ]);
        $this->addColumn('amount', [ 'label' => Mage::helper('dealer4dealer_xcore')->__('Amount field') ]);
        $this->addColumn('tax_rate', [ 'label' => Mage::helper('dealer4dealer_xcore')->__('Tax rate') ]);

        $this->_addAfter       = false;
        $this->_addButtonLabel = Mage::helper('dealer4dealer_xcore')->__('Add Fee');
    }
}
