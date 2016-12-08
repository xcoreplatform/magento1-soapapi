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
 * Sysconf grid renderer for credit columns
 *
 * @category    Dealer4dealer
 * @author      Sander Mangel <sander@sandermangel.nl>
 */
class Dealer4dealer_Xcore_Block_Adminhtml_System_Config_Field_Creditmapping
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_itemRenderer = [];

    /**
     * _prepareToRender
     */
    protected function _prepareToRender()
    {
        $this->addColumn('column', [
            'label' => Mage::helper('dealer4dealer_xcore')->__('Creditmemo field'),
            //'renderer' => $this->_getRenderer('salesflatcreditcolumn'),
        ]);
        $this->addColumn('exact_key', [
            'label' => Mage::helper('dealer4dealer_xcore')->__('Exact name'),
            'style' => 'width:150px',
        ]);

        $this->_addAfter       = false;
        $this->_addButtonLabel = Mage::helper('dealer4dealer_xcore')->__('Add Column');
    }
}
