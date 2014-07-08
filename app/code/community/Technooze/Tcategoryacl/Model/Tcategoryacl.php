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
    public function _construct()
    {
        parent::_construct();
        $this->_init('tcategoryacl/tcategoryacl');
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