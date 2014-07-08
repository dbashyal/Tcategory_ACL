<?php
class Technooze_Tcategoryacl_Model_Catalog_Resource_Category_Flat extends Mage_Catalog_Model_Resource_Category_Flat
{
    /**
     * We need to rewrite this class to be able to filter hidden categories if the
     * flat catalog category is enabled.
     *
     * This is the version of the rewrite for Magento 1.8 and newer.
     * In Magento 1.8 the method signature changed.
     *
     * @param Mage_Catalog_Model_Category|int $parentNode
     * @param integer $recursionLevel
     * @param integer $storeId
     * @param bool $onlyActive
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0, $onlyActive = true)
    {
        $nodes = parent::_loadNodes($parentNode, $recursionLevel, $storeId, $onlyActive);

        /* @var $helper Technooze_Tcategoryacl_Helper_Data */
        $helper = Mage::helper('tcategoryacl');
        if ($helper->isModuleActive()) {
            // Filter out hidden nodes
            if (count($nodes) > 0) {
                $hiddenIds = Mage::getResourceSingleton('tcategoryacl/filter')->getHiddenCategoryIds();

                $nodes = array_diff($nodes, $hiddenIds);
            }
        }
        return $nodes;
    }
}