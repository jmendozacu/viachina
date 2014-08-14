<?php

class OnTap_Merchandiser_Adminhtml_Catalog_CategoryController extends Mage_Adminhtml_Controller_Action
{
    
	/**
	* Load the grid according to the specified category
	*/
    public function gridAction()
    {
    	// Retrieve the category ID from the request
        $cat_id = $this->getRequest()->getParam('category_id');
        
        // Load the category
        $category = mage::getModel('catalog/category')->load($cat_id);
        
        // Register the category for later use
        mage::register('category',$category);
        mage::register('current_category',$category);
        
        // Set the response to the grid block
        $this->getResponse()->setBody($this->getLayout()->createBlock('merchandiser/adminhtml_catalog_category_tab_smartmerch_grid', 'category.smartmerch.grid')->toHtml());
    }

	/**
	* Check the ACL for category access
	*/
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/categories');
    }
}
