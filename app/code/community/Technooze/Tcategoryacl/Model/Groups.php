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
class Technooze_Tcategoryacl_Model_Groups extends Technooze_Schoolgroup_Model_Schoolgroup
{
    private $_groupsArray = array();

    /**
     * @return array
     */
    public function getGroupsArray(){
        if(empty($this->_groupsArray)){
            $this->_groupsArray = array();/*'0' => ' '*/
            $collection = $this->getCollection()->addFieldToFilter('school_group_status', 1);
            foreach($collection as $group){
                $this->_groupsArray[$group->getSchoolGroupId()] = $group->getSchoolGroupId() . ' (' . $group->getSchoolGroupCode() . ')';
            }
        }
        return $this->_groupsArray;
    }
}