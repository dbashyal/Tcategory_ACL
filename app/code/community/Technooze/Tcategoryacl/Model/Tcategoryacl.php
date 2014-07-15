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
class Technooze_Tcategoryacl_Model_Tcategoryacl extends Mage_Core_Model_Abstract
{

    /* @var $_helper Technooze_Tcategorystatus_Helper_Data */
    private $_helper = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('tcategoryacl/tcategoryacl');
    }

    public function getHelper(){
        if(null === $this->_helper){
            $this->_helper = Mage::helper('tcategorystatus');
        }
        return $this->_helper;
    }

    public function removeInactive(){
        $collection = $this->getCollection();
        $collection->addFieldToFilter('status', '0');
        $count = $collection->count();

        foreach($collection as $row){
            $row->delete();
        }
        return $count;
    }

    public function getOrderAllowedDateRange()
    {
        /* @var $group Technooze_Schoolgroup_Model_Schoolgroup */
        $group = Mage::getModel('schoolgroup/schoolgroup')->getCurrentCustomerSchoolGroup();
        if(!$group || !$group->getId()){
            return false;
        }

        /* @var $collection Technooze_Tcategoryacl_Model_Mysql4_Tcategoryacl_Collection */
        $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();
        $collection
          ->addFieldToFilter('group_id', $group->getId())
          ->addFieldToFilter('allow_from', array('lteq' => $this->getHelper()->getDateToday()))
          ->addFieldToFilter('allow_to', array('gteq' => $this->getHelper()->getDateToday()));

        // if we get result, that means customer can still buy it, else NO.
        if(!$collection->count()){
            return false;
        }
        return $collection->getFirstItem()->getData();
    }

    public function getNextOrderAllowedDate()
    {
        /* @var $group Technooze_Schoolgroup_Model_Schoolgroup */
        $group = Mage::getModel('schoolgroup/schoolgroup')->getCurrentCustomerSchoolGroup();
        if(!$group || !$group->getId()){
            return false;
        }

        /* @var $collection Technooze_Tcategoryacl_Model_Mysql4_Tcategoryacl_Collection */
        $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();
        $collection
          ->addFieldToFilter('group_id', $group->getId())
          ->addFieldToFilter('allow_from', array('gt' => $this->getHelper()->getDateToday()))
          ->setOrder('allow_from','ASC');

        // if we get result, that means customer can still buy it, else NO.
        if(!$collection->count()){
            return false;
        }
        return $collection->getFirstItem()->getData();
    }

    public function updateFieldStatus($val = 0){
        /**
         * Get the resource model
         */
        $resource = Mage::getSingleton('core/resource');

        /**
         * Retrieve the write connection
         */
        $writeConnection = $resource->getConnection('core_write');

        /**
         * Retrieve our table name
         */
        $table = $resource->getTableName('tcategoryacl');

        $query = "UPDATE {$table} SET status = '{$val}'";

        /**
         * Execute the query
         */
        $writeConnection->query($query);
    }
}