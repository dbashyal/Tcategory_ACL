<?php

class Technooze_Tcategoryacl_Model_Convert_Adapter_Permissionsimport extends Mage_Dataflow_Model_Convert_Container_Abstract
{
    /* @var $_lock Technooze_Tcategoryacl_Model_Convert_Adapter_Lock */
    private $_lock = null;

    public function getLockModel(){
        if(null === $this->_lock){
            $this->_lock = Mage::getSingleton('tcategoryacl/convert_adapter_lock');
        }
        return $this->_lock;
    }

    public function markToRemoveInactive()
    {
        // not sure how to do this otherways :(
        $this->getLockModel()->lock();

        // mark all entries as status 0, so we can remove old entries
        // as we gonna set status = 1 for new ones
        // other ideas are welcome :)
        Mage::getModel('tcategoryacl/tcategoryacl')->updateFieldStatus(0);
    }

    public function finish(){
        $file = $this->getLockModel()->getLockFileName();
        if(file_exists($file)){
            // that means we want to remove old entries
            // but first make sure, we have entries with status=1
            // to make sure, import did proceed and updated/imported records.
            try{
                unlink($file);
                $count = Mage::getModel('tcategoryacl/tcategoryacl')->removeInactive();
                $message = Mage::helper('dataflow')->__('Removed "%s" inactive/old entries.', $count);
                $this->addException($message);
            } catch (Exception $e){
                Mage::log($e->getMessage());
            }
        }
    }

    public function saveRow(array $importData)
    {
        $importData['status'] = 1;
        //$this->markToRemoveInactive();

        /* @var $collection Technooze_Tcategoryacl_Model_Mysql4_Tcategoryacl_Collection */
        $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();
        $collection->addfieldToFilter('group_id', $importData['group_id']);
        $collection->addfieldToFilter('category_id', $importData['category_id']);

        // delete previous entries
        foreach($collection as $row){
            $row->delete();
        }

        // now insert new data
        $model = Mage::getModel('tcategoryacl/tcategoryacl')->load(0);
        $model->setData($importData)->save();
    }
}