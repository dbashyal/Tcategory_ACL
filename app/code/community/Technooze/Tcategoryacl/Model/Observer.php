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

                        $data = array(
                                    'status' => '1',
                                    'category_id' => $category_id,
                                    'group_id' => $this->_getRequest()->getParam("group_id")[$k],
                                    'allow_from' => $this->_getRequest()->getParam("allow_from")[$k],
                                    'allow_to' => $this->_getRequest()->getParam("allow_to")[$k],
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
        }
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
}