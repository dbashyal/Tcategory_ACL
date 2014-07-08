<?php
class Technooze_Tcategoryacl_Model_Resource_Filter extends Mage_Core_Model_Resource_Db_Abstract
{
    private $_hidden_categories = array();

    /**
     * Implement method required by abstract
     */
    protected function _construct()
    {
    }

    public function getHiddenCategoryIds(){
        /* @var $group Technooze_Schoolgroup_Model_Schoolgroup */
        $group = Mage::getModel('schoolgroup/schoolgroup')->getCurrentCustomerSchoolGroup();

        if(!$group || !$group->getId()){
            $groupId = 0;
        } else {
            $groupId = $group->getId();
        }

        if(isset($this->_hidden_categories[$groupId])){
            return $this->_hidden_categories[$groupId];
        }

        /* @var $collection Technooze_Tcategoryacl_Model_Mysql4_Tcategoryacl_Collection */
        $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();

        // if no date specified for any categories, no need to go further
        if(!$collection->count()){
            return array();
        }

        $collection = $collection
                            ->clear()
                            ->getSelect()
                            ->reset(Zend_Db_Select::COLUMNS)
                            ->columns('category_id')
                            ->group(array('category_id'));

        $collection = $collection->query();
        $categories_with_rule = array();
        foreach($collection as $cat){
            $categories_with_rule[$cat['category_id']] = $cat['category_id'];
        }

        $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();

        $category_permitted = $collection
                                  ->addFieldToFilter('group_id', $groupId)
                                  ->addFieldToFilter('category_id', array('in' => $categories_with_rule))
                                  /*->addFieldToFilter(array(
                                          array('attribute'=> 'allow_from','lteq' => Mage::helper('tcategorystatus')->getDateToday()),
                                          array('attribute'=> 'allow_to','gteq' => Mage::helper('tcategorystatus')->getDateToday()),
                                      )
                                  )*/
                                  ->addFieldToFilter('allow_from', array('lteq' => Mage::helper('tcategorystatus')->getDateToday()))
                                  ->addFieldToFilter('allow_to', array('gteq' => Mage::helper('tcategorystatus')->getDateToday()))
                                  ->getColumnValues('category_id');

        // return category IDs that needed to be hidden
        $this->_hidden_categories[$groupId] = array_diff($categories_with_rule, $category_permitted);

        return $this->_hidden_categories[$groupId];
    }

    /**
     * Return the collection entity table alias.
     *
     * This is ugly, but so far I haven't come up with a better way. On the other hand
     * the alias haven't changed since Magento 1.0 so I guess it's kinda safe.
     *
     * @param Varien_Data_Collection_Db $collection
     * @return string
     */
    protected function _getCollectionTableAlias(Varien_Data_Collection_Db $collection)
    {
        $tableAlias = 'main_table';
        if ($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract) {
            $tableAlias = 'e';
        }
        return $tableAlias;
    }

    /**
     * @param Varien_Data_Collection_Db $collection
     * @param Technooze_Schoolgroup_Model_Schoolgroup $group
     * @return void
     */
    public function addGroupsCatalogFilterToCollection($collection, $group)
    {
        /* @var $helper Technooze_Tcategoryacl_Helper_Data */
        //$helper = Mage::helper('tcategoryacl');

        /**
         * This is slightly complicated but it works with products and
         * categories whether the flat tables enabled or not
         *
         * @var $entityType string
         * @var $entity Mage_Catalog_Model_Abstract
         */
        //$entity = $collection->getNewEmptyItem();
        //$entityType = $helper->getEntityTypeCodeFromEntity($entity);

        $this->_addGroupsCatalogFilterToSelect(
            $collection, $group, $collection->getStoreId()
        );
    }

    /**
     * @param Varien_Data_Collection_Db $collection
     * @param Technooze_Schoolgroup_Model_Schoolgroup $group
     * @param int $storeId
     * @return void
     */
    protected function _addGroupsCatalogFilterToSelect($collection, $group, $storeId) {
        $hiddenCats = $this->getHiddenCategoryIds();
        if(count($hiddenCats)){
            $collection->addAttributeToFilter('entity_id', array('nin' => $hiddenCats));
        }
    }
}