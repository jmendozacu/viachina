<?php
class OnTap_Merchandiser_AdminhtmlController extends Mage_Adminhtml_Controller_Action
{
    protected $helper;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->helper = Mage::helper('merchandiser');
        return parent::__construct($request, $response, $invokeArgs);
    }

    public function initAction()
    {
        $session = Mage::getSingleton('core/session', array('name'=>'frontend'));
        if (!$session->getData('user')) {
            $user = Mage::getSingleton('admin/session')->getUser();
            $session->setData('user', $user);
        }
        var_export($session->getData('user'));
    }

    public function openAction()
    {
        $catId      = $this->getRequest()->getParam('category_id');
        $formKey    = Mage::getSingleton('core/session')->getFormKey();
        $this->_redirect("merchandiser/adminhtml/index", array(
            'category_id'   => $catId,
           // 'adminFormKey'  => $formKey,
        ));
    }
    
    public function saveOrderingAction()
    {
        $catId = $this->getRequest()->getParam('category_id');
        $params = $this->getRequest()->getParams();
        if (is_int((int)$catId)) {
            $_category = Mage::getModel('catalog/category')->load($catId);
            $products = $this->getRequest()->getParam('product');
            
            /* HEROPRODUCT FUNCTIONALITY - LOGIC */
            //$heroProducts = $this->getRequest()->getParam('hero_product_values' , '');
            $heroProductsIds = array();
            $insertQuery = "";
            $iCounter = 0;
            if(isset($params['heroproducts'])){            	// ADDING HERO PRODUCTS TO CATEGORY
	        	$heroProductsIds = $params['heroproducts'];
	        	foreach ($heroProductsIds as $heroProductId){
	        		if($heroProductId && $heroProductId > 0){
            			$iCounter++;
            			$insertQuery .= "('$catId' ,'$heroProductId' , '$iCounter' ),";
            		}
	        	}
	        }
            
            /*if($heroProducts != ''){
            	$productObject = Mage::getModel('catalog/product');
            	foreach (explode(",",$heroProducts) as $hproduct){
            		$productId = $productObject->getIdBySku(trim($hproduct));
            		if($productId){$iCounter++;
            			$insertQuery .= "('$catId' ,'$productId' , '$iCounter' ),";
            			$heroProductsIds[] = $productId;
            		}
            	}
            }*/
            
            $catalogCategoryProduct = Mage::getSingleton('core/resource')->getTableName('catalog_category_product');
            
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write->query("DELETE FROM `$catalogCategoryProduct` WHERE category_id = ".$catId.";");
            $productIds = "";
            if (is_array($products)) {
                unset($products[$this->helper->getEmptyId()]);
                if(sizeof($products) > 0){
	                $insertQuery = "";
	                foreach ($products as $product_id => $product_pos) {
	                	$product_pos = $iCounter + $product_pos; // AFTER HERO PRODUCTS
	                	if(in_array($product_id,$heroProductsIds)){ continue; }// EXCLUDE HEROPRODUCTS FROM NORMAL PRODUCTS 
		                $insertQuery .= "('$catId' ,'$product_id' , '$product_pos' ),";
	                }
	                if($insertQuery != "" && strlen($insertQuery) > 0){
	                	$insertQuery = substr($insertQuery , 0 , strlen($insertQuery) - 1);
	                    $write->query("INSERT INTO `$catalogCategoryProduct` VALUES ".$insertQuery);
	                }
                }
            }
			// THIS CODE IS PLACE FOR REINDEX ENTERPRISE 1.13 INDEX
            /* REBUILDING HERO SKUS FOR HERO PRODUCTS INPUT FIELD */
            $heroProductSkus = '';
            $productsCollectionForHeroSKUs = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id', array('in' => $heroProductsIds));
            foreach ($productsCollectionForHeroSKUs as $heroProductModelObject){
            	$heroProductSkus .= $heroProductModelObject->getSku() . ",";
            }
            //if(strlen($heroProductSkus) > 0) { $heroProductSkus = substr($heroProductSkus,0,strlen($heroProductSkus) -1); }
            $heroProductSkus = strlen($heroProductSkus)>0?substr($heroProductSkus,0,strlen($heroProductSkus) -1):$heroProductSkus;
            
            
			// THIS CODE IS PLACE FOR REINDEX ENTERPRISE 1.13 INDEX
            /*Mage::dispatchEvent('catalog_category_save_commit_after', array(
           		'category' => $_category,
        	));*/ // NOT REQUIRED IF CATEGORY SAVE FUNCTION IS FIRED
          	$_category->setMerchandiserHeroproducts($heroProductSkus);
           	$_category->save();
        	
        	if(!Mage::getStoreConfig('merchandiser/index/disable_catindex')){
            	Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_product')->reindexEverything();
        	}
        	
            if (Mage::getConfig()->getModuleConfig('Enterprise_PageCache')): // CODE PLACED BY DAN - AVOIDING FPC ISSUE
				Mage::app()->getCacheInstance()->cleanType('full_page');
			endif;
            
        }
        $this->_redirect("merchandiser/adminhtml", array('category_id' => $catId, 'up'=>1));
    }

    public function changeColumnAction()
    {
        $catId = $this->getRequest()->getParam('category_id');
        $storeId = $this->getRequest()->getParam('store_id') ? $this->getRequest()->getParam('store_id') : Mage::app()->getStore()->getStoreId();
        $columnCount = $this->getRequest()->getParam('column_count') ? $this->getRequest()->getParam('column_count') : intval(Mage::getStoreConfig('merchandiser/options/column_count'));
        //$this->_redirect("merchandiser/merchandiser", array('category_id' => $catId, 'up'=>1,'store_id' => $storeId, 'column_count' => $columnCount, 'asid' => md5(sha1(sha1($_COOKIE['adminhtml']))) ));
        $this->_redirect("merchandiser/adminhtml", array('category_id' => $catId, 'up'=>1,'column_count' => $columnCount));
    }

    

    

    public function processActionAction(){
        $catId = $this->getRequest()->getParam('category_id');      
        $columnCount = $this->getRequest()->getParam('column_count') ? $this->getRequest()->getParam('column_count') : intval(Mage::getStoreConfig('merchandiser/options/column_count'));

        $params = array();
        $params['catId'] = $catId;
        $params['column_count'] = $column_count;

        $helper = Mage::helper('merchandiser');
        $actions = $helper->getConfigAction();
        $action_index = $this->getRequest()->getParam('action_index');
        $string = explode('::', $actions[$action_index]['controller_vm']);
        $controllerName = $string[0];
        $functionName = $string[1];
        if (is_array($string)){
            call_user_func(array($controllerName,$functionName), $params);

            $_category = Mage::getModel('catalog/category')->load($catId);
            
            
            /* PRESERVE HERO PRODUCTS AT TOP IN WITH ANY SORTING */
            $catalogCategoryProduct = Mage::getSingleton('core/resource')->getTableName('catalog_category_product');
            $categoryList = new OnTap_Merchandiser_Block_Category_List;
            $categoryProducts = $categoryList->getProductCollection();
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $heroProducts = $_category->getData('merchandiser_heroproducts');
            $iCounter = 0;
            $finalProductsArray = array();
            $productObject = Mage::getModel('catalog/product');
            foreach (explode(",",$heroProducts) as $heroSKU){ // PLACE HERO PRODUCTS FIRST
            	if($heroSKU != '' && $productId = $productObject->getIdBySku(trim($heroSKU))){
            		if($productId > 0){
            			$iCounter++;
            			$finalProductsArray[] = $productId;
            			$insertQuery .= "('$catId' ,'$productId' , '$iCounter' ),";
            		}
            	}
            }
            if($heroProducts != '' && sizeof($finalProductsArray) > 0){ // CALL ALL ASSIGNED PRODUCTS
            	foreach ($categoryProducts as $product){
            		$productId = $product->getId();
            		if(!in_array($productId,$finalProductsArray)){ // EXCLUDE HERO PRODUCTS FROM NORMAL PRODUCTS
            			$iCounter++;
            			$insertQuery .= "('$catId' ,'$productId' , '$iCounter' ),";
            		}
            	}
            	if($insertQuery != "" && strlen($insertQuery) > 0){
                	$insertQuery = substr($insertQuery , 0 , strlen($insertQuery) - 1);
                	$write->query("DELETE FROM `$catalogCategoryProduct` WHERE category_id = ".$catId.";");  // REMOVE ALL PRODUCTS FROM CATEGORY
                    $write->query("INSERT INTO `$catalogCategoryProduct` VALUES ".$insertQuery); // ADD UPDATED PRODUCTS WITH POSITIONS TO CATEGORY
                }
            }
            /* HERO PRODUCT FUNCTIONALITY ENDS  */
            
	        Mage::dispatchEvent('catalog_category_save_commit_after', array(
	       		'category' => $_category,
	    	));
	        if(!Mage::getStoreConfig('merchandiser/index/disable_catindex')){
            	Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_product')->reindexEverything();
        	}
	        if (Mage::getConfig()->getModuleConfig('Enterprise_PageCache')):
				Mage::app()->getCacheInstance()->cleanType('full_page');
			endif;
        }
        //$this->_redirect("merchandiser/merchandiser", array('category_id' => $catId, 'up'=>1,'store_id' => $storeId, 'column_count' => $columnCount, 'asid' => md5(sha1(sha1($_COOKIE['adminhtml']))) ));
        $this->_redirect("merchandiser/adminhtml", array('category_id' => $catId));
    }

    public function attachActionAction(){
        try {
            $catId = $this->getRequest()->getParam('category_id');
            $action_index = (int)$this->getRequest()->getParam('action_index');
            $category = Mage::getModel("catalog/category")->load($catId);
            $category->setCatalogAction($action_index);
            $category->save();
            echo 'success';
        } catch (Exception $e) {
            echo 'fail';
        }
        exit();
    }
    
    public function indexAction(){
    	$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function loadajaxAction()
    {
        $block =  $this->getLayout()->createBlock('merchandiser/category_list')->setTemplate('merchandiser/new/category/ajax.phtml');
        echo $block->toHtml();
    }
    
	public function searchAction(){
    	$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function getproductinfoAction(){
        $res = "";
        $sku = $this->getRequest()->getParam('sku', false);
        if($this->getRequest()->getParam('sku', false)) {
        	$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
            if($_product) :
            	$productBox =  $this->getLayout()->createBlock('merchandiser/adminhtml_catalog_product_list')->setTemplate('merchandiser/new/category/productbox.phtml');
                $productBox->setPid($_product->getId());
                $res =$productBox->toHtml();
          	endif;
      	}
        echo $res;
    }
    public function vmcaptureAction(){
    	$collection = Mage::getModel('catalog/product')->getCollection();
    	$collection->joinField('position', 'catalog/category_product', 'position', 'product_id=entity_id', 'category_id=' .(int) $this->getRequest()->getParam('id', 0), 'left');
    	$catId = $this->getRequest()->getParam('category_id');
    	$category = Mage::getModel("catalog/category")->load($catId);
    	if(Mage::helper('merchandiser')->addSmartCategory($collection,$category)){
    		if($collection->count() && $collection->count() > 0){
    			
    			$catalogCategoryProduct = Mage::getSingleton('core/resource')->getTableName('catalog_category_product'); // PRODUCT POSITION TABLE
    		
    			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
            	$write->query("DELETE FROM `$catalogCategoryProduct` WHERE category_id = ".$catId.";"); // DELETE ALL PREVIOUS PRODUCTS FROM CURRENT CATEGORY
            	
            	$insertQuery = "";
            	$iCounter = 1;
              	foreach ($collection as $product){
              		$product_id = $product->getId();
              		$product_pos = $iCounter++;
	                $insertQuery .= "('$catId' ,'$product_id' , '$product_pos' ),";
                }
                if($insertQuery != "" && strlen($insertQuery) > 0){
                	$insertQuery = substr($insertQuery , 0 , strlen($insertQuery) - 1);
                    $write->query("INSERT INTO `$catalogCategoryProduct` VALUES ".$insertQuery);
                }
                $category->setMerchandiseOption(1)->save();
                // THIS CODE IS PLACE FOR REINDEX ENTERPRISE 1.13 INDEX
                if(!Mage::getStoreConfig('merchandiser/index/disable_catindex')){
                	Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_product')->reindexEverything();
            	}
            	
            	if (Mage::getConfig()->getModuleConfig('Enterprise_PageCache')): // CODE PLACED BY DAN - AVOIDING FPC ISSUE
					Mage::app()->getCacheInstance()->cleanType('full_page');
				endif;
    		}
    	}
    }
    
    
}