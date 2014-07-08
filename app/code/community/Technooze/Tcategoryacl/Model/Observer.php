<?php
/**
 * Tcategory_ACL Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Technooze
 * @package    Tcategoryacl
 * @copyright  Copyright (c) 2014 dltr.org
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Technooze
 * @package    Tcategoryacl
 * @author     Damodar Bashyal @dbashyal
 */
class Technooze_Tcategoryacl_Model_Observer
{
    /**
     * Flag to stop observer executing more than once
     *
     * @var static bool
     */
    static protected $_singletonFlag = false;

    /**
     * This method will run when the category is saved from the Magento Admin
     * Use this function to update the category model, process the
     * data or anything you like
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveCategoryTabData(Varien_Event_Observer $observer)
    {
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;

            $category = $observer->getEvent()->getCategory();
            $category_id = $category->getId();
            if(!$category_id){
                return;
            }
            $selected_tcategoryacl = $this->_getRequest()->getParam('tcategoryacl_categories');
            try {
                /**
                 * Delete old associated tcategoryacl to this category
                 * even if any new ones are selected or not
                 */
                $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();
                $collection->addFieldToFilter('category_id', $category_id);
                $collection->load();

                foreach($collection as $old_tcategoryacl)
                {
                    $old_tcategoryacl->delete();
                }

                /*
                 * Now added new tcategoryacl related to this category.
                 */
                if(is_array($selected_tcategoryacl))
                {
                    foreach($selected_tcategoryacl as $k => $v)
                    {
                        if(empty($v) || $v == 'on')
                        {
                            continue;
                        }
                        $params = $this->_getRequest()->getParams();
                        $data = array(
                                    'status' => '1',
                                    'category_id' => $category_id,
                                    'group_id' => $params['group_id'][$k],
                                    'allow_from' => $params['allow_from'][$k],
                                    'allow_to' => $params['allow_to'][$k],
                                );

                        $model = Mage::getModel('tcategoryacl/tcategoryacl')->load(0);
                        $model->setData($data);

                        try {
                            $insertId = $model->save()->getId();
                        } catch (Exception $e){
                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        }
                    }
                }
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }

            /* @var $category Mage_Catalog_Model_Category */
            $helper = $this->_getHelper();

            // If the module isn't disabled on a global scale
            if ($helper->isModuleActive($category->getStore(), false) && !$this->_isDisabledOnRequest()) {
                if (
                        $category->dataHasChangedFor(Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ACTIVE_FROM_CODE)
                ||
                        $category->dataHasChangedFor(Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ACTIVE_TO_CODE)
                ) {
                    if ($helper->getConfig('auto_refresh_block_cache')) {
                        // Only refresh the category block cache: Mage_Catalog_Model_Category::CACHE_TAG
                        Mage::app()->cleanCache(array(Mage_Catalog_Model_Category::CACHE_TAG));
                    } else {
                        Mage::app()->getCacheInstance()->invalidateType(Mage_Core_Block_Abstract::CACHE_GROUP);
                    }
                }
            }
        }
    }

    /**
     * Add the tcategoryacl filter sql to category collections
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function catalogCategoryCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCategoryCollection();
        $this->_addGroupsCatalogFilterToCollection($collection);
    }

    /**
     * Add the tcategoryacl filter sql to catalog collections using the tcategoryacl filter resource model
     *
     * @param Varien_Data_Collection_Db (Mage_Catalog_Model_Resource_Category_Flat_Collection) $collection
     * @return void
     */
    protected function _addGroupsCatalogFilterToCollection(Varien_Data_Collection_Db $collection)
    {
        $helper = $this->_getHelper();
        if ($helper->isModuleActive() && !$this->_isDisabledOnRequest()) {
            /* @var $group Technooze_Schoolgroup_Model_Schoolgroup */
            $group = Mage::getModel('schoolgroup/schoolgroup')->getCurrentCustomerSchoolGroup();
            $this->_getResource()->addGroupsCatalogFilterToCollection($collection, $group);
        }
    }

    /**
     * "Unload" the specified catalog entity if the tcategoryacl settings specify so
     *
     * @param Mage_Catalog_Model_Abstract $entity
     * @return void
     */
    protected function _applyGroupsCatalogSettingsToEntity(Mage_Catalog_Model_Abstract $entity)
    {
        $helper = $this->_getHelper();
        if ($helper->isModuleActive() && !$this->_isDisabledOnRequest()) {
            if (!$helper->isEntityVisible($entity)) {
                $entity->setData(null)->setId(null);
                // Set flag to make it easier to implement a redirect if needed (or debug)
                $entity->setData('forbidden_by_tcategoryacl', true);
            }
        }
    }

    /**
     * "Unload" a loaded category if the customer is not allowed to view it
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function catalogCategoryLoadAfter(Varien_Event_Observer $observer)
    {
        $category = $observer->getCategory();
        $this->_applyGroupsCatalogSettingsToEntity($category);
        if ($category->getData('forbidden_by_tcategoryacl')) {
            $this->_applyHiddenEntityHandling(Mage_Catalog_Model_Category::ENTITY);
        }
    }

    /**
     * Apply the message display and redirect if configured.
     *
     * @param string $entityTypeCode
     */
    protected function _applyHiddenEntityHandling($entityTypeCode)
    {
        if ($this->_getHelper()->isModuleActive() && !$this->_isDisabledOnRequest()) {
            // Do not apply redirects and messages to customer module (order history and dashboard for example).
            // Otherwise products that where previously purchased by the customer and now are hidden from him
            // would make the customer account inaccessible.
            if (Mage::app()->getRequest()->getModuleName() !== 'customer') {
                Mage::helper('tcategoryacl/hidden')->applyHiddenEntityHandling($entityTypeCode);
            }
        }
    }

    /**
     * Return true if the request is made via the api or one of the other disabled routes
     *
     * @return boolean
     */
    protected function _isDisabledOnRequest()
    {
        $currentRoute = Mage::app()->getRequest()->getModuleName();
        return in_array($currentRoute, $this->_getHelper()->getDisabledOnRoutes());
    }

    /**
     * Helper convenience method
     *
     * @return Technooze_Tcategoryacl_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('tcategoryacl');
    }

    /**
     * Retrieve the category model
     *
     * @return Mage_Catalog_Model_Category $category
     */
    public function getCategory()
    {
        return Mage::registry('category');
    }

    public function ajaxed(Varien_Event_Observer $observer)
    {
        // to do
        return $this;
    }

    /**
     * Shortcut to getRequest
     *
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Filter resource convenience method
     *
     * @return Technooze_Tcategoryacl_Model_Resource_Filter
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('tcategoryacl/filter');
    }
}