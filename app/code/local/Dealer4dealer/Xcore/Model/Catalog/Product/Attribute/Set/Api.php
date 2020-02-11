<?php
class Dealer4dealer_Xcore_Model_Catalog_Product_Attribute_Set_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Get the attribute group info.
     *
     * @param $attributeSetId
     * @param $attributeSetGroupName
     * @return array
     * @throws Mage_Api_Exception
     */
    public function groupInfo($attributeSetId, $attributeSetGroupName)
    {
        /** @var Mage_Eav_Model_Entity_Attribute_Group $group */
        $group = Mage::getModel('eav/entity_attribute_group')->getCollection()
            ->addFieldToFilter('attribute_set_id', $attributeSetId)
            ->getFirstItem();

        if (!$group->getId()) {
           $this->_fault('not_found');
        }

        return $this->_prepareResponse($group);
    }

    /**
     * @param Mage_Eav_Model_Entity_Attribute_Group $group
     * @return array
     */
    protected function _prepareResponse($group)
    {
        return array(
            'attribute_group_id'        => $group->getId(),
            'attribute_set_id'          => $group->getAttributeSetId(),
            'attribute_group_name'      => $group->getAttributeGroupName(),
            'sort_order'                => $group->getSortOrder(),
            'default_id'                => $group->getDefaultId(),
        );
    }
}