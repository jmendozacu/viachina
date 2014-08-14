<?php
class OnTap_Merchandiser_Model_Merchandiser
{
    protected $data = array();

    public function moveInStockToTheTopVM($params){
    	
        $catId = $params['catId'];
        
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $cataloginventryStockItem = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
        $catalogCategoryProduct = Mage::getSingleton('core/resource')->getTableName('catalog_category_product');
        
        $sql = "SELECT category_id, si.product_id, is_in_stock, position FROM `$cataloginventryStockItem` as `si`  INNER JOIN `$catalogCategoryProduct` as `cp` ON si.product_id = cp.product_id WHERE category_id = ".$catId." AND is_in_stock = 0";
        $readresult=$write->query($sql);

        $outStockProducts = array();

        while ($row = $readresult->fetch() ) {
            $outStockProducts[] = $row['product_id'];
        }

        $sql_update = "";
        if (count($outStockProducts)) {
            foreach ($outStockProducts as $outStockProduct_id) {
                $sql_update .= "UPDATE `$catalogCategoryProduct` SET position = 99999 WHERE product_id = ".$outStockProduct_id." AND category_id = ".$catId.";";
            }
        }

        if($sql_update != ""){
            $write->query($sql_update);
        }
    }

    public function orderByCreatedDate($params)
    {   
    	$catalogCategoryProduct = Mage::getSingleton('core/resource')->getTableName('catalog_category_product');
    	$catalogProduct = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity');
    	$cataloginventryStockItem = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
    	
        $catId = $params['catId'];

        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "SELECT product_id
                    FROM `$catalogProduct` as `product`
                    INNER JOIN `$catalogCategoryProduct` as `cp` ON cp.product_id = product.entity_id
                    WHERE category_id =".$catId."
                    ORDER BY created_at DESC";
        
        $readresult=$write->query($sql);
    
        $products = array();

    
        while ($row = $readresult->fetch() ) {
            $products[] = $row['product_id'];
        }
    
        $sql_update = "";
        if (count($products)) {
            $i = 0;
            foreach ($products as $productId) {
                $i++;
                $sql_update .= "UPDATE `$catalogCategoryProduct` SET position = " . $i . " WHERE product_id = ".$productId." AND category_id = ".$catId.";";
            }
        }
    
        if($sql_update != ""){
            $write->query($sql_update);
        }   

        $sql = "SELECT category_id, si.product_id, is_in_stock, position FROM `$cataloginventryStockItem` as `si` INNER JOIN `$catalogCategoryProduct` as `cp` ON si.product_id = cp.product_id WHERE category_id = ".$catId." AND is_in_stock = 0";
        $readresult=$write->query($sql);
        
        $outStockProducts = array();
        
        while ($row = $readresult->fetch() ) {
            $outStockProducts[] = $row['product_id'];
        }
        
        $sql_update = "";
        if (count($outStockProducts)) {
            foreach ($outStockProducts as $outStockProduct_id) {
                $sql_update .= "UPDATE `$catalogCategoryProduct` SET position = 99999 WHERE product_id = ".$outStockProduct_id." AND category_id = ".$catId.";";
            }
        }
        
        if($sql_update != ""){
            $write->query($sql_update);
        }       
        
        if(!Mage::getStoreConfig('merchandiser/index/disable_catindex')){
        	Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_product')->reindexEverything();
    	}
    }

    public function moveInStockToTheTopSM($collection){
    	Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);
		$collection->getSelect()->order('is_in_stock desc');
    }
    
    public function moveSaleAtTopSM($collection){ 
		$joinTable = Mage::getSingleton('core/resource')->getTableName('catalog_product_index_price');
		try{
			$collection->getSelect()->joinInner(array('price_index_table' => $joinTable),'e.entity_id = price_index_table.entity_id',array('price','final_price','website_id','customer_group_id'));
		}catch (Exception  $e){
			
		}
		$collection->getSelect()->order( array('(price_index_table.price<>price_index_table.final_price) DESC , position ASC'));	
		
		
	}
	
	public function moveSaleAtTopVM($params){
		$catId = $params['catId'];
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $priceTable = Mage::getSingleton('core/resource')->getTableName('catalog_product_index_price');
        $catalogCategoryProduct = Mage::getSingleton('core/resource')->getTableName('catalog_category_product');
        
        $query = "SELECT cat_product.*,price_index.price,price_index.final_price FROM $catalogCategoryProduct as cat_product INNER JOIN $priceTable as price_index ON cat_product.product_id = price_index.entity_id WHERE category_id = ".$catId." GROUP BY product_id ORDER BY (price_index.price<>price_index.final_price) DESC ;";
        $readresult=$write->query($query);

     	$sql_update = "";
     	$position = 1;
        while ($row = $readresult->fetch() ) {
     		$sql_update .= "UPDATE `$catalogCategoryProduct` SET position = ".$position." WHERE product_id = ".$row['product_id']." AND category_id = ".$catId.";";
     		$position++;
        }

        if($sql_update != ""){
            $write->query($sql_update);
        }
        /*$_category = Mage::getModel('catalog/category')->load($catId);
        Mage::dispatchEvent('catalog_category_save_commit_after', array(
       		'category' => $_category,
    	));
        Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_product')->reindexEverything();
        if (Mage::getConfig()->getModuleConfig('Enterprise_PageCache')): // CODE PLACED BY DAN - AVOIDING FPC ISSUE
			Mage::app()->getCacheInstance()->cleanType('full_page');
		endif;*/
	}
	
	public function moveSaleAtBottomSM($collection){ 
		$joinTable = Mage::getSingleton('core/resource')->getTableName('catalog_product_index_price');
		try{
			$collection->getSelect()->joinInner(array('price_index_table' => $joinTable),'e.entity_id = price_index_table.entity_id',array('price','final_price','website_id','customer_group_id'));
		}catch (Exception  $e){
		}
		$collection->getSelect()->order( array('(price_index_table.price<>price_index_table.final_price) ASC , position ASC'));	
	}
	
	public function moveSaleAtBottomVM($params){
		$catId = $params['catId'];
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $priceTable = Mage::getSingleton('core/resource')->getTableName('catalog_product_index_price');
        $catalogCategoryProduct = Mage::getSingleton('core/resource')->getTableName('catalog_category_product');
        
        $query = "SELECT cat_product.*,price_index.price,price_index.final_price FROM $catalogCategoryProduct as cat_product INNER JOIN $priceTable as price_index ON cat_product.product_id = price_index.entity_id WHERE category_id = ".$catId." GROUP BY product_id ORDER BY (price_index.price<>price_index.final_price) ASC ;";
        $readresult=$write->query($query);

     	$sql_update = "";
     	$position = 1;
        while ($row = $readresult->fetch() ) {
     		$sql_update .= "UPDATE `$catalogCategoryProduct` SET position = ".$position." WHERE product_id = ".$row['product_id']." AND category_id = ".$catId.";";
     		$position++;
        }

        if($sql_update != ""){
            $write->query($sql_update);
        }
        /*$_category = Mage::getModel('catalog/category')->load($catId);
        Mage::dispatchEvent('catalog_category_save_commit_after', array(
       		'category' => $_category,
    	));
        Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_product')->reindexEverything();
        if (Mage::getConfig()->getModuleConfig('Enterprise_PageCache')): // CODE PLACED BY DAN - AVOIDING FPC ISSUE
			Mage::app()->getCacheInstance()->cleanType('full_page');
		endif;*/
	} 
	
	public function setHeroProductAtTop($collection,$categoryId){
		/* THIS FUNCTION USE TO ADD SORTING ON SKUPOS VIRTUAL FIELD SEE HELPER/DATA.PHP::addSmartCategory()*/
		$categoryModel = Mage::getModel('catalog/category')->load($categoryId);
		if($categoryModel->getMerchandiserHeroproducts() != ''){
			try{
       			$collection->getSelect()->order("skupos ASC");
			}catch (Exception $e){
				// TO AVOID BREAK OF EXECUTION
			}
        }
	}
}