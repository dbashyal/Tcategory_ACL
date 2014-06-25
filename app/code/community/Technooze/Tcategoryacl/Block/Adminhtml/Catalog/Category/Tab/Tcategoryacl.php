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
class Technooze_Tcategoryacl_Block_Adminhtml_Catalog_Category_Tab_Tcategoryacl extends Mage_Adminhtml_Block_Widget_Grid
{
    private $_collectionReset = false;

    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_tcategoryacl');
        $this->setDefaultSort('tcategoryacl_id');
        $this->setUseAjax(true);
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        $category = Mage::registry('category');

        if(!is_object($category))
        {
            $id = $this->getRequest()->getParam('id', 0);
            $category = Mage::getModel('catalog/category')->load($id);

            Mage::register('category', $category);
        }

        return $category;
    }

    /**
     * @param $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        $tcategoryaclIds = $this->_getSelectedTcategoryacl();
        switch($column->getId()){
            case 'selected_tcategoryacl':
                if (empty($tcategoryaclIds)) {
                    $tcategoryaclIds = array(0);
                }
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('tcategoryacl_id', array('in'=>$tcategoryaclIds));
                } elseif(!empty($tcategoryaclIds)) {
                    $this->getCollection()->addFieldToFilter('tcategoryacl_id', array('nin'=>$tcategoryaclIds));
                }
                break;
            case 'group_id':
            case 'allow_from':
            case 'allow_to':
                $this->resetCollection();
                $this->getCollection()->addFieldToFilter($column->getId(), array('like'=>$column->getFilter()->getValue()));
                $this->getCollection()->addFieldToFilter('status', '1');
                break;
            default: parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    private function resetCollection(){
        if($this->_collectionReset){
            return false;
        }
        $this->_collectionReset = true;
        /* @var $collection Technooze_Tcategoryacl_Model_Mysql4_Tcategoryacl_Collection */
        $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('selected_tcategoryacl'=>1));
        }
        /* @var $collection Technooze_Tcategoryacl_Model_Mysql4_Tcategoryacl_Collection */
        $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();
        $collection->getSelect()->joinRight('customer_group', 'customer_group_id=group_id');

        $i = 0;
        $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
        $date = date('Y-m-d 00:00:00', $currentTimestamp);

        foreach($collection as $v){
            // lets have fake ids for empty tcategoryacl_id, so checkbox can be selected
            if(!$v->getData('tcategoryacl_id')){
                $v->setData('tcategoryacl_id', $this->getCategory()->getId() . $i++);
                $v->setId($v->getData('tcategoryacl_id'));
            }

            // lets auto select different groups in each row
            if(!$v->getData('group_id')){
                $v->setData('group_id', $v->getData('customer_group_id'));
            }

            // lets auto select different groups in each row
            if(!$v->getData('allow_from')){
                $v->setData('allow_from', $date);
            }

            // lets auto select different groups in each row
            if(!$v->getData('allow_to')){
                $v->setData('allow_to', $date);
            }
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        if (!$this->getCategory()->getTcategoryaclReadonly()) {
            $this->addColumn('selected_tcategoryacl', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'selected_tcategoryacl',
                'field_name' => 'tcategoryacl_categories',
                'renderer'  => 'tcategoryacl/adminhtml_widget_grid_column_renderer_checkbox',
                'values'    => $this->_getSelectedTcategoryacl(),
                'align'     => 'center',
                'index'     => 'tcategoryacl_id'
            ));
        }
        $this->addColumn('tcategoryacl_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'tcategoryacl_id'
        ));
        $this->addColumn('group_id', array(
            'header'    => Mage::helper('catalog')->__('Group ID'),
            'width'     => '320',
            'type'     => 'select',
            'options'     => Mage::getModel('tcategoryacl/groups')->getGroupsArray(),
            'renderer'  => 'tcategoryacl/adminhtml_widget_grid_column_renderer_select',
            'index'     => 'group_id'
        ));
        $this->addColumn('allow_from', array(
            'header'    => Mage::helper('catalog')->__('Allow From'),
            /*'type'      => 'input',*/
            'renderer'  => 'tcategoryacl/adminhtml_widget_grid_column_renderer_datetimeinput',
            'index'     => 'allow_from'
        ));
        $this->addColumn('allow_to', array(
            'header'    => Mage::helper('catalog')->__('Allow To'),
            'renderer'  => 'tcategoryacl/adminhtml_widget_grid_column_renderer_datetimeinput',
            'index'     => 'allow_to'
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('tcategoryacl/adminhtml_category/index', array('_current'=>true));
    }

    /**
     * @return array
     */
    protected function _getSelectedTcategoryacl()
    {
        $selected_tcategoryacl = array();
        $category = $this->getCategory();
        $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();
        $collection->addFieldToFilter('category_id', $category->getId());
        $collection->load();

        foreach($collection as $v)
        {
            $selected_tcategoryacl[] = $v->getTcategoryaclId();
        }
        return $selected_tcategoryacl;
    }
}

