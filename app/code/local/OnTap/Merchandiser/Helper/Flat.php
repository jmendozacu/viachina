<?php

class OnTap_Merchandiser_Helper_Flat extends Mage_Catalog_Helper_Product_Flat
{
  
    public function isEnabled($store = null)
    {
        if(strcmp(Mage::app()->getRequest()->getControllerName(), "merchandiser") == 0 ) return false;
        $store = Mage::app()->getStore($store);
        if ($store->isAdmin()) {
            return false;
        }
        
        if (!isset($this->_isEnabled[$store->getId()])) {
            if (Mage::getStoreConfigFlag(self::XML_PATH_USE_PRODUCT_FLAT, $store)) {
                $this->_isEnabled[$store->getId()] = $this->getProcess()->getStatus() == Mage_Index_Model_Process::STATUS_PENDING;
            } else {
                $this->_isEnabled[$store->getId()] = false;
            }
        }
        return $this->_isEnabled[$store->getId()];        
    }

}
