<?php

class Dealer4dealer_Xcore_Block_Adminhtml_System_Config_Field_Fee
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
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
