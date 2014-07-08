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
class Technooze_Tcategoryacl_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_CONFIG_DISABLED_ROUTES = 'global/tcategoryacl/disabled_on_routes';
    const HIDE_GROUPS_ATTRIBUTE_STATE_CACHE = 'tcategoryacl_groups_state_cache';

    /**
     * On these routes the module is inactive.
     * This is important for IPN notifications and API requests to succeed
     *
     * @var array
     */
    protected $_disabledOnRoutes;

    protected $_group_id;

    /**
     * If set to false Tcategoryacl filtering is skipped
     *
     * @var bool|null
     */
    protected $_moduleActive = null;

    public function isModuleActive($store = null, $checkAdmin = true){
        $store = Mage::app()->getStore($store);
        if ($checkAdmin && $store->isAdmin()) {
            return false;
        }

        // Temporary setting has higher priority then system config setting
        if (null !== $this->getModuleActiveFlag()) {
            return $this->getModuleActiveFlag();
        }

        $setting = $this->getConfig('is_active', $store);
        return (bool)$setting;
    }

    /**
     * Return the value of the _moduleActive flag
     *
     * @return bool
     */
    public function getModuleActiveFlag()
    {
        return $this->_moduleActive;
    }

    /**
     * Return a configuration setting from within the tcategoryacl/general section.

     * @param string $field
     * @param int|string|Mage_Core_Model_Store $store
     * @return mixed
     */
    public function getConfig($field, $store = null)
    {
        return Mage::getStoreConfig('tcategoryacl/general/' . $field, $store);
    }

    /**
     * Return the route names on which the module should be inactive
     *
     * @return array
     */
    public function getDisabledOnRoutes()
    {
        if (null == $this->_disabledOnRoutes) {
            $this->_disabledOnRoutes = array_keys(
                Mage::getConfig()->getNode(self::XML_CONFIG_DISABLED_ROUTES)->asArray()
            );
        }
        return $this->_disabledOnRoutes;
    }

    /**
     * Return the entity type code from a catalog entity
     *
     * @param Mage_Catalog_Model_Abstract $entity
     * @return string
     */
    public function getEntityTypeCodeFromEntity(Mage_Catalog_Model_Abstract $entity)
    {
        // $entity::ENTITY is only possible from PHP 5.3.0, but Magento requires only 5.2.13
        return constant(get_class($entity) . '::ENTITY');
    }

    public function isEntityVisible(Mage_Catalog_Model_Abstract $entity, $customerGroupId = null)
    {
        // if the module is deactivated or a store view all entities are visible
        if (!$this->isModuleActive($entity->getStoreId())) {
            return true;
        }

        $cachedState = $entity->getData(self::HIDE_GROUPS_ATTRIBUTE_STATE_CACHE);
        if (! is_null($cachedState)) {
            return $cachedState;
        }

        // Default to the current customer group id
        if (is_null($customerGroupId)) {
            $customerGroupId = $this->getSchoolGroupId();
        }

        $hiddenCats = Mage::getModel('tcategoryacl/resource_filter')->getHiddenCategoryIds();

        $visibility = !in_array($entity->getId(), $hiddenCats);
        $entity->setData(self::HIDE_GROUPS_ATTRIBUTE_STATE_CACHE, $visibility);

        return $visibility;
    }

    /**
     * Return the customer id of the current customer
     *
     * @return int
     */
    public function getSchoolGroupId()
    {
        if(null == $this->_group_id){
            /* @var $group Technooze_Schoolgroup_Model_Schoolgroup */
            $group = Mage::getModel('schoolgroup/schoolgroup')->getCurrentCustomerSchoolGroup();

            if($group && $group->getId()){
                $this->_group_id = $group->getId();
            } else {
                $this->_group_id = 0;
            }
        }
        return $this->_group_id;
    }
}