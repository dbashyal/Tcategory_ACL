<?php
class Technooze_Tcategoryacl_Model_Convert_Adapter_Io extends Mage_Dataflow_Model_Convert_Adapter_Io
{
    /**
     * Load data
     *
     * @return Technooze_Tcategoryacl_Model_Convert_Adapter_Io
     */
    public function loader()
    {
        parent::load();

        $removeInactive = $this->getVar('remove_inactive', false);

        if($removeInactive && $removeInactive == 1){
            $adapter = Mage::getModel('tcategoryacl/convert_adapter_permissionsimport');
            if (method_exists($adapter, 'markToRemoveInactive')) {
                $adapter->markToRemoveInactive();
            }
        }
    }
}
