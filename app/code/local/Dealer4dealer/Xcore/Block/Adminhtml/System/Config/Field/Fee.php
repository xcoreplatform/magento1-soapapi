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
    protected $_itemRenderer = [];

    /**
     * _prepareToRender
     */
    protected function _prepareToRender()
    {
        $this->addColumn('title', [
            'label' => Mage::helper('dealer4dealer_xcore')->__('Title'),
            'style' => 'width:100px',
        ]);
        $this->addColumn('base_amount', [
            'label' => Mage::helper('dealer4dealer_xcore')->__('Base amount field'),
            'renderer' => $this->_getRenderer('base_amount'),
        ]);
        $this->addColumn('amount', [
            'label' => Mage::helper('dealer4dealer_xcore')->__('Amount field'),
            'renderer' => $this->_getRenderer('amount'),
        ]);
        $this->addColumn('tax_rate', [
            'label' => Mage::helper('dealer4dealer_xcore')->__('Tax rate'),
            'renderer' => $this->_getRenderer('tax_rate'),
        ]);

        $this->_addAfter       = false;
        $this->_addButtonLabel = Mage::helper('dealer4dealer_xcore')->__('Add Fee');
    }

    /**
     * @param $field
     * @return Mage_Core_Block_Template
     */
    protected function  _getRenderer($field)
    {
        if (!isset($this->_itemRenderer[$field])) {
            $this->_itemRenderer[$field] = $this->getLayout()->createBlock(
                'dealer4dealer_xcore/adminhtml_system_config_field_renderer_' . str_replace('_','', $field) , '',
                array('is_render_to_js_template' => true)
            );
        }

        return $this->_itemRenderer[$field];
    }

    /**
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer('tax_rate')->calcOptionHash($row->getData('tax_rate')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer('base_amount')->calcOptionHash($row->getData('base_amount')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer('amount')->calcOptionHash($row->getData('amount')),
            'selected="selected"'
        );
    }
}
