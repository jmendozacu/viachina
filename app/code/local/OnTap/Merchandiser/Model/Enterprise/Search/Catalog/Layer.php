<?php

class OnTap_Merchandiser_Model_Enterprise_Search_Catalog_Layer extends Enterprise_Search_Model_Catalog_Layer
{
    /**
     * Adds support for Smart Merchandiser with Magento Enterprise
     *
     * Retrieve current layer product collection
     *
     * @return Enterprise_Search_Model_Resource_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])):
            return $this->_productCollections[$this->getCurrentCategory()->getId()];
        endif;
        
        // Check if we have any smart attributes defined
        if (	trim($this->getCurrentCategory()->getSmartAttributes()) != '' &&
        		$this->getCurrentCategory()->getSmartAttributes() &&
        		!is_null($this->getCurrentCategory()->getSmartAttributes())):
            $collection = mage::getSingleton('catalog/layer')->getProductCollection();
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
            return $collection;
        endif;
        
        // Fallback to parent collection
        return parent::getProductCollection();
        
        
    }

}
