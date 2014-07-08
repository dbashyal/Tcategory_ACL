<?php

class Technooze_Tcategoryacl_Model_Convert_Adapter_Lock extends Mage_Dataflow_Model_Convert_Container_Abstract
{
    /**
     * Process lock properties
     */
    protected $_isLocked        = null;
    protected $_lockFile        = null;
    protected $_lockFileName    = null;

    public function getLockFileName(){
        if (null === $this->_lockFileName) {
            $varDir = Mage::getConfig()->getVarDir('locks');
            $this->_lockFileName = $varDir . DS . 'remove_inactive.lock';
        }
        return $this->_lockFileName;
    }

    /**
     * Get lock file resource
     *
     * @return resource
     */
    protected function _getLockFile()
    {
        if ($this->_lockFile === null) {
            $file = $this->getLockFileName();
            if (is_file($file)) {
                $this->_lockFile = fopen($file, 'w');
            } else {
                $this->_lockFile = fopen($file, 'x');
            }
            fwrite($this->_lockFile, date('r'));
        }
        return $this->_lockFile;
    }

    /**
     * Lock process without blocking.
     * This method allow protect multiple process runing and fast lock validation.
     *
     * @return Mage_Index_Model_Process
     */
    public function lock()
    {
        $this->_isLocked = true;
        flock($this->_getLockFile(), LOCK_EX | LOCK_NB);
        return $this;
    }

    /**
     * Lock and block process.
     * If new instance of the process will try validate locking state
     * script will wait until process will be unlocked
     *
     * @return Mage_Index_Model_Process
     */
    public function lockAndBlock()
    {
        $this->_isLocked = true;
        flock($this->_getLockFile(), LOCK_EX);
        return $this;
    }

    /**
     * Unlock process
     *
     * @return Mage_Index_Model_Process
     */
    public function unlock()
    {
        $this->_isLocked = false;
        flock($this->_getLockFile(), LOCK_UN);
        return $this;
    }

    /**
     * Check if process is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        if ($this->_isLocked !== null) {
            return $this->_isLocked;
        } else {
            $fp = $this->_getLockFile();
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                flock($fp, LOCK_UN);
                return false;
            }
            return true;
        }
    }

    /**
     * Close file resource if it was opened
     */
    public function __destruct()
    {
        if ($this->_lockFile) {
            fclose($this->_lockFile);
        }
    }
}