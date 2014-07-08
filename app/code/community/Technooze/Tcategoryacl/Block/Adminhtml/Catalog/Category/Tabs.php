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
class Technooze_Tcategoryacl_Block_Adminhtml_Catalog_Category_Tabs extends Mage_Adminhtml_Block_Catalog_Category_Tabs
{
        /**
         * Initialize Tabs
         *
         */
        public function __construct()
        {
            parent::__construct();
        }

    /**
     * Prepare Layout Content
     *
     * @return Mage_Adminhtml_Block_Catalog_Category_Tabs
     */
    protected function _prepareLayout()
    {
        $this->addTabAfter('tcategoryacl', array(
            'label'     => Mage::helper('tcategoryacl')->__('Group Permissions'),
            'content'   => $this->getLayout()->createBlock(
                'tcategoryacl/adminhtml_catalog_category_tab_tcategoryacl',
                'category.tcategoryacl.grid'
            )->toHtml(),
        ), 'products');
        return parent::_prepareLayout();
    }
}
