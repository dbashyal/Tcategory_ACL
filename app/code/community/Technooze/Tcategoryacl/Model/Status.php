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
class Technooze_Tcategoryacl_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    /**
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('tcategoryacl')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('tcategoryacl')->__('Disabled')
        );
    }
}