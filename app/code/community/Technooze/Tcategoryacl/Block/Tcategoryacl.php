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
class Technooze_Tcategoryacl_Block_Tcategoryacl extends Mage_Core_Block_Template
{
    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getTcategoryacl()
     { 
        if (!$this->hasData('tcategoryacl')) {
            $this->setData('tcategoryacl', Mage::registry('tcategoryacl'));
        }
        return $this->getData('tcategoryacl');
        
    }
}