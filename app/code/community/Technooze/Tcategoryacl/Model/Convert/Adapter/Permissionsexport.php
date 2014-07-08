<?php

class Technooze_Tcategoryacl_Model_Convert_Adapter_Permissionsexport extends Mage_Dataflow_Model_Convert_Container_Abstract
{
    /* @var $_collection Technooze_Tcategoryacl_Model_Mysql4_Tcategoryacl_Collection */
    private $_collection = null;

    // for export
    public function load() {
        if(null == $this->_collection){
            $this->_collection = mage::getModel('tcategoryacl/tcategoryacl')->getCollection();

            //Show number of records loaded.
            $this->addException(Mage::helper('dataflow')->__('Loaded %s rows', $this->_collection->getSize()), Mage_Dataflow_Model_Convert_Exception::NOTICE);
        }
    }

    // for export
    public function save() {
        $this->load();

        //file path to save to
        $path = $this->getVar('path') . '/' . $this->getVar('filename');
        $f = fopen($path, 'w');
        $fields = $this->getFields();
        fputcsv($f, $fields);

        $this->_collection->clear()->addFieldToSelect($fields);
        foreach($this->_collection as $fields){
            $fields = $fields->getData();
            unset($fields['tcategoryacl_id']);
            unset($fields['status']);
            fputcsv($f, $fields);
        }
        fclose($f);
        $this->addException(Mage::helper('dataflow')->__('Export saved in %s', $path), Mage_Dataflow_Model_Convert_Exception::NOTICE);
    }

    // for export
    public function getFields() {
        $columns = $this->_collection->getFirstItem()->getData();
        unset($columns['tcategoryacl_id']);
        unset($columns['status']);
        return array_keys($columns);
    }
}