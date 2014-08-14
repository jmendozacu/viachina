<?php

class OnTap_Merchandiser_Block_Search_Result extends Mage_Adminhtml_Block_Abstract
{
    const PAGE_SEARCH_RESULTS = 30;

    public function getSearchModel()
    {
        return Mage::getSingleton('merchandiser/search');
    }

    public function _toHtml()
    {
        //$this->_getProductCollection()->setPage(1, self::PAGE_SEARCH_RESULTS);
       // $this->_getProductCollection()->addStoreFilter($this->getRequest()->getParam('store_id'));
        return parent::_toHtml();
    }
    
    public function getLoadedProductCollection(){
    	$params = $this->getRequest()->getParams();
    	
    	$producCollection = Mage::getModel('catalog/product')->getCollection();
    	$producCollection->addAttributeToSelect(array('name' , 'type'));
    	$producCollection->addAttributeToFilter('name' , array('like' => "%".$params['q']."%"));
    	
    	if(Mage::helper('merchandiser')->isHideInvisibleProducts()){
			$producCollection->addAttributeToFilter('visibility', array('or'=>array(4,2)));
		}
		if(Mage::helper('merchandiser')->isHideDisabledProducts()){
			$producCollection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
		}
    	
    	return $producCollection;
    }
    
    public function getResultCount(){
    	return $this->getLoadedProductCollection()->count();
    }

}
