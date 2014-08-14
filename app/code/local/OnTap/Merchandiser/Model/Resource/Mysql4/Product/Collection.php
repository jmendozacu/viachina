<?php

class OnTap_Merchandiser_Model_Resource_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{

    /**
     * Retreive clear select
     *
     * @return Varien_Db_Select
     */
    protected function _getClearSelect()
    {
    	// Process the select according to the parent method
        $select = parent::_getClearSelect();
        
        // Determine if we need to do anything special for a Smart Merchandiser category
        if($this->hasFlag('is_smart')):
           $select->reset(Zend_Db_Select::GROUP); 
           $select->reset(Zend_Db_Select::DISTINCT);         
        endif;
        return $select;
    }
    
    public function addCategoryFilter(Mage_Catalog_Model_Category $category)
    {
    	if($category->getMerchandiseOption() == 2 && !Mage::getSingleton('admin/session')->isLoggedIn()):
    		return $this;
    	endif;
        $this->_productLimitationFilters['category_id'] = $category->getId();
        if ($category->getIsAnchor()):
            unset($this->_productLimitationFilters['category_is_anchor']);
        else:
            $this->_productLimitationFilters['category_is_anchor'] = 1;
        endif;

        if ($this->getStoreId() == Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID):
            $this->_applyZeroStoreProductLimitations();
        else:
            $this->_applyProductLimitations();
        endif;

        return $this;
    }
    
    public function isEnabledFlat()
    {
        // Flat Data can be used only on frontend
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        
        $params = Mage::app()->getRequest()->getParams();
        if(isset($params['cat']) && $currentCategory = Mage::getModel('catalog/category')->load($params['cat'])){
        	if($currentCategory->getMerchandiseOption() == 2){
        		return false;
        	}
        }
        if($currentCategory = Mage::registry('current_category')){
        	if($currentCategory->getMerchandiseOption() == 2){
        		return false;
        	}
        }
        
       return parent::isEnabledFlat();
    }
    
    
	// XXX: GoMage_Navigation compatibility
	public function getSearchedEntityIds()
	{
		return false;
	}

	// XXX: GoMage_Navigation compatibility
	public function getSelectCountSql()
	{
		//if (class_exists('GoMage_Navigation_Model_Resource_Eav_Mysql4_Collection')) {
		if(Mage::helper('core')->isModuleEnabled('GoMage_Navigation')){ // MER - 86 TO REMOVE WARNING IN SYSTEM LOG
			$select = GoMage_Navigation_Model_Resource_Eav_Mysql4_Collection::getSelectCountSql();
			$select->reset(Zend_Db_Select::GROUP);
			return $select;
		}
		return parent::getSelectCountSql();
	}
	
	public function getAttributeAlias(){
		return $this->_getAttributeTableAlias(''); 
	}
}
