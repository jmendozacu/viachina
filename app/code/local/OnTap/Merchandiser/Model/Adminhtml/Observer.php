<?php

class OnTap_Merchandiser_Model_Adminhtml_Observer {

    /**
    *	Setup the tab in Manage Categories
    **/
    public function adminhtml_catalog_category_tabs(Varien_Event_Observer $observer) {
        try {
            $admin_tabs=$observer->getEvent()->getTabs();
            $admin_tab_block=$admin_tabs->getLayout()->createBlock('merchandiser/adminhtml_catalog_category_tab_smartmerch', 'category.smartmerch.tab');
            $admin_grid_block = $admin_tabs->getLayout()->createBlock('merchandiser/adminhtml_catalog_category_tab_smartmerch_grid', 'category.smartmerch.grid');
            $admin_tab_block->setChild('category_smartmerch_grid',$admin_grid_block);
            $admin_tabs->addTab('smartmerch', array('label' => Mage::helper('catalog')->__('Visual Merchandiser'),'content' => $admin_tab_block->toHtml()));
            
        } catch (Exception $e) {
            mage::logException($e);
        }
        return $this;
    }
    
    /**
    *	Inject our grid into catalog_category_products block 
    **/
    public function adminhtml_block_html_before(Varien_Event_Observer $observer) {
        try {
            $current_block = $observer->getEvent()->getBlock();
            if ($current_block instanceof Mage_Adminhtml_Block_Catalog_Category_Tab_Product && $current_block->getId() == "catalog_category_products"):                
                $current_block->setTemplate('merchandiser/smartmerch/widget/grid.phtml');
            endif;
        } catch (Exception $e) {
            mage::logException($e);
            mage::throwException($e->getMessage());
        }
        return $this;
    }
    
    /**
    *	Handle category save, make sure the Smart Merchandiser attributes get saved
    **/
    public function catalog_category_prepare_save(Varien_Event_Observer $observer) {
        try {
            $event=$observer->getEvent();
            if ($post_data = $event->getRequest()->getPost()):
                if (isset($post_data['smartmerch_attributes'])):
                    foreach($post_data['smartmerch_attributes'] as $key => $value):
                        if(!is_array($value)):
                            unset($post_data['smartmerch_attributes'][$key]);
                            continue;
                        endif;
                        if(array_key_exists('value', $value) && strlen(trim($value['value'])) == 0):
                            unset($post_data['smartmerch_attributes'][$key]);
                            continue;
                        endif;
                    endforeach;
                    if(count($post_data['smartmerch_attributes']) > 0):
                        $event->getCategory()->setData('smart_attributes', serialize($post_data['smartmerch_attributes']));
                    else:
                        $event->getCategory()->setData('smart_attributes','');
                    endif;
                endif;
                unset($post_data['smartmerch_attributes']);
                
                /* FOR FOR MANAGING HERO PRODUCTS [ ADD / REMOVE ] BASED ON HERO PRODUCTS FIELD  */
                $categoryProducts = $event->getCategory()->getData('posted_products');
                $heroProducts = $event->getCategory()->getData('merchandiser_heroproducts');
                $categoryObject = Mage::getModel('catalog/category')->load($event->getCategory()->getId());
                
                $mainHeroProduct =  explode(",",$categoryObject->getMerchandiserHeroproducts());
                $postedHeroProduct =  explode(",",$heroProducts);
                $mainHeroProduct = array_map('trim', $mainHeroProduct);
                $postedHeroProduct = array_map('trim', $postedHeroProduct);
                $difference = array_diff($mainHeroProduct,$postedHeroProduct);

                $productObject = Mage::getModel('catalog/product');
                if($heroProducts != $categoryObject->getMerchandiserHeroproducts()){
	                
                	$removeProducts = array();
	                if(sizeof($difference) > 0){ // 	GET REMOVED HERO PRODUCT IDS
		                foreach ($difference as $removeProductSKU){
		                	if($productId = $productObject->getIdBySku(trim($removeProductSKU))){
		                		$removeProducts[] = $productId; // GET REMOVED SKU PRODUCT IDS
		                	}
		                }
	                }
	                
	                $iCounter = 0;
	                $finalProductsArray = array();
	                
	                foreach (explode(",",$heroProducts) as $heroSKU){
	                	if($heroSKU != '' && $productId = $productObject->getIdBySku(trim($heroSKU))){
	                		if($productId > 0){
	                			$iCounter++;
	                			$finalProductsArray[$productId] = $iCounter; // ADD CURRENT HERO PRODUCT TO FINAL PRODUCT ARRAY
	                		}
	                	}
	                }
	                if(sizeof($difference) > 0 || sizeof($finalProductsArray) > 0){
		                foreach ($categoryProducts as $productId => $pos){
		                	if(!isset($finalProductsArray[$productId])){
		                		if(!in_array($productId , $removeProducts)){
		                			$iCounter++;
		                			$finalProductsArray[$productId] = $iCounter;
		                		}
		                	}
		                }
		                $event->getCategory()->setData('posted_products',$finalProductsArray);
	        		}
                }
                /* HERO PRODUCTS LOGIC ENDS */
                
            endif;
        } catch (Exception $e) {
            mage::logException($e);
            mage::throwException($e->getMessage());
        }
        return $this;
    }
    
    public function categoryCommitAfter($observer){
    	/*$indexer = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_smart_product');
    	if($indexer->getData('mode') == "real_time" && Mage::getStoreConfig('merchandiser/index/disabled') == 0){
    		$indexer->reindexEverything();
    	}*/
    	if (Mage::getConfig()->getModuleConfig('Enterprise_PageCache')): // CODE PLACED BY DAN - AVOIDING FPC ISSUE
			Enterprise_PageCache_Model_Cache::getCacheInstance()->cleanType('full_page');
		endif;
    }
    
    public function reindexCron(){
    	$indexer = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_smart_product');
    	$indexer->reindexEverything();
    }
}
