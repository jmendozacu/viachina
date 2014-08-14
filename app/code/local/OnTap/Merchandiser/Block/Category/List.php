<?php

class OnTap_Merchandiser_Block_Category_List extends Mage_Core_Block_Template
{
    public function getCategoryProductCollection($catId, $storeId =null)
    {
        if (is_int((int)$catId)) {
            $collection = Mage::getSingleton('merchandiser/search')
                ->addCategoryFilter($catId)
                ->getProductCollection()
                ->setStoreId($storeId);
        } else {
            $collection = $this->_getProductCollection();
        }
        return $collection;
    }

    public function getCategory()
    {
        if (!$this->getData('category')) {
            if ($this->getCategoryId()) {
                if ($category = Mage::getModel('catalog/category')->load($this->getCategoryId())) {
                    $this->setData('category', $category);
                }
            }
        }
        return $this->getData('category');
    }
    
    
    public function getCategoryId(){
    	$params = $this->getRequest()->getParams();
    	return $params['category_id'];
    }
    
    public function getProductCollection(){
    	$products = Mage::getModel('catalog/category')->load($this->getCategoryId())
		    ->getProductCollection()
		    ->addAttributeToSelect('*');
		    //->addAttributeToSort('position');
		    $products->getSelect()->order('cat_pro.position ASC');
		if(Mage::helper('merchandiser')->isHideInvisibleProducts()){
			$products->addAttributeToFilter('visibility', array('or'=>array(4,2)));
		}
		if(Mage::helper('merchandiser')->isHideDisabledProducts()){
			$products->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
		}
		return $products;
    }
}
