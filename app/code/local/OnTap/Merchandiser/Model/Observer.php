<?php

class OnTap_Merchandiser_Model_Observer {

    public function catalog_product_collection_apply_limitations_after(Varien_Event_Observer $observer) {
        try {
            $collection = $observer->getCollection();

            if ($collection->hasFlag('category_ids')):
            
            	// Get the category IDs
                $categories = $collection->getFlag('category_ids');
                $setCategories = explode(',', $categories);
                
                // Set up our empty arrays for processing
                $sub_categories = array();
                $master_categories = array();
                
                // Iterate through the categories
                foreach ($setCategories as $category_id):
                    // Merge into the master category array
                    $master_categories = array_merge($master_categories, $sub_categories, array($category_id));
                endforeach;
                $categories = implode(',', array_unique(array_filter($master_categories)));
                $condition_constraint = array('cat_index.product_id=e.entity_id',$collection->getConnection()->quoteInto('cat_index.category_id IN (' . $categories . ')', ""));
                
                // Handle store scope
                if (!Mage::app()->getStore()->isAdmin()):
						$condition_constraint[] = $collection->getConnection()->quoteInto('cat_index.store_id=?', $collection->getFlag('store_id'));
                endif;
                $condition_join = join(' AND ', $condition_constraint);
                $condition_from = $collection->getSelect()->getPart(Zend_Db_Select::FROM);

                if (isset($condition_from['cat_index'])):
                    $condition_from['cat_index']['joinCondition'] = $condition_join;
                    $collection->getSelect()->setPart(Zend_Db_Select::FROM, $condition_from);
                else:
                    $collection->getSelect()->join(array('cat_index' => $collection->getTable('catalog/category_product_index')), $condition_join, array('cat_index_category_id' => 'category_id'));
                endif;
            endif;
        } catch (Exception $e) {
            mage::logException($e);
        }
        return $this;
    }
    
    public function runAction(){

		$helper = Mage::helper('merchandiser');
		$actions = $helper->getConfigAction();

		$category = Mage::getModel('catalog/category');
		$tree = $category->getTreeModel();
		$tree->load();
		$ids = $tree->getCollection()->getAllIds();
		if ($ids){
		    foreach ($ids as $id){
		        $cat = Mage::getModel("catalog/category");
		        $cat->load($id);
		        $action_index = $cat->getCatalogAction();
		        if (is_null($action_index)) continue;

		        $params = array();
		        $params['catId'] = (int)$id;

		        $string = explode('::', $actions[$action_index]['controller']);
		        $controllerName = $string[0];
		        $functionName = $string[1];
		        if (is_array($string)){
		            call_user_func(array($controllerName,$functionName), $params);
		        }
		        echo $id;
		    }
		}
	}
	
	public function applySortsToCollection($observer){
		try{
    		$request = Mage::app()->getRequest()->getParams();
	    	$currentOrder = Mage::getStoreConfig('catalog/frontend/default_sort_by'); 
	    	// LOWEST PRIRITY - SYSTEM CONFIGURATION DEFAULT ORDER FOR CATEGORY PRODUCTS
			if (Mage::registry('current_category')):
		    	$currentCategory =  Mage::getModel('catalog/category')->load(Mage::registry('current_category')->getId());
		    	
		    	if(isset($request['cat']) && $request['cat'] > 0){
		    		$currentCategory =  Mage::getModel('catalog/category')->load($request['cat']);
		    	}
		    	
		    	if($currentCategory->getMerchandiseOption() == 2){
			    	if($currentCategory->getDefaultSortBy() != ''){
			    		$currentOrder = $currentCategory->getDefaultSortBy();
			    		// CATEGORY DEFAUL ORDER FOR ASSIGNED PRODUCTS
			    	}
			    	
			    	if(Mage::getSingleton('catalog/session')->getSortOrder() != ''){
			    		$currentOrder = Mage::getSingleton('catalog/session')->getSortOrder();
			    		// TAKE ORDER STORED IN SESSION 
			    	}
			    	if(isset($request['order']) && $request['order']!= ''){
			    		$currentOrder = $request['order'];
			    		// TAKE ORDER FROM REQUEST OBJECT
			    	}
			    	$direction = 'ASC';
			    	if(isset($request['dir']) && $request['dir'] == 'DESC'){
			    		$direction = $request['dir'];
			    	}// CURRENTLY WE ARE NOT USING DIRECTION - BUT WE CAN ADD FOR FUTURE PREFERENCE 
			    	
			    	
			    	if($currentOrder != '' && $currentOrder == 'position'){
			    		
			    		$actions = Mage::helper('merchandiser')->getConfigAction();
			    		
						$_collection = $observer->getEvent()->getCollection();
						$_collection->getSelect()->reset(Zend_Db_Select::ORDER);
						Mage::getModel('merchandiser/merchandiser')->setHeroProductAtTop($_collection,$currentCategory->getId());
						if($currentCategory->getMerchandiserSortingOptions() != ''){
							if(isset($actions[$currentCategory->getMerchandiserSortingOptions()])){
	            				$optionControllerArray = explode("::" , $actions[$currentCategory->getMerchandiserSortingOptions()]['controller_sm']);
	            				if(is_array($optionControllerArray) && sizeof($optionControllerArray) == 2){
	            					call_user_func(array($optionControllerArray[0],$optionControllerArray[1]), $_collection);
	            				}
	            			}
						}
			    	}
		    	}
			endif;
    	}catch (Exception $e){
    		Mage::log($e->getMessage());
    	}
	}
	public function productCollectionLoadAfter($observer){
		if($currentCategory = Mage::registry('current_category')){
			$categoryObject = Mage::getModel('catalog/category')->load($currentCategory->getId());
			if($categoryObject->getMerchandiseOption() == 2 && Mage::getStoreConfig('catalog/seo/product_use_categories') == 1){
				$colelction = $observer->getCollection();
				$colelction->addUrlRewrite(); 
			}
		}
	}
	
	public function addJs(Varien_Event_Observer $observer)
    { // THIS FUNCTION FOR REMOVE CONFLICTION WITH MGT-AmazingWysiwyg EXTENSION
    	$request = Mage::app()->getRequest();
    	
    	$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;
    	
    	if($request->getModulename() != "merchandiser" && isset($modulesArray['Mgt_AmazingWysiwyg'])){
	        $block = $observer->getEvent()->getBlock();
	        if ($block && $block instanceof Mage_Adminhtml_Block_Page_Head) {
	            $transport = $observer->getEvent()->getTransport();
	            $transportHtml = $transport->getHtml();
	            $transportHtml .= $block->getLayout()->createBlock('mgt_amazing_wysiwyg_adminhtml/js')->setTemplate('mgt_amazing_wysiwyg/js.phtml')->toHtml();
	            $transport->setHtml($transportHtml); 
	        }
    	}
    }
}
