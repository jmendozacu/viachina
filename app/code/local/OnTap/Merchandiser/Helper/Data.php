<?php

class OnTap_Merchandiser_Helper_Data extends Mage_Core_Helper_Abstract {

    const ID_EMPTY = 'empty';
	
    
    public function isAllow($pk){
        $adminhtml_sid = $_COOKIE['adminhtml'];
        if(md5(sha1(sha1($adminhtml_sid))) !== $pk || is_null($pk) || (strcmp($pk, "") == 0) ) {
            echo Mage::helper('merchandiser')->__('You are not logged in');
            return false;
        }
        return true;
    }

    public function getStockNumder(Mage_Catalog_Model_Abstract $product)
    {
        $stockQty = $product->getStockItem()->getStockQty();
        if (isset($stockQty)) {
            return $stockQty;
        } else {
            $qty = $product->getStockItem()->getQty();
            if (!$product->getStockItem()->getIsQtyDecimal()) {
                $qty = round($qty);
            }
            return $qty;
        }
    }

    public function getSearchActionUrl()
    {
        $url = Mage::getModel('core/url')->getUrl('merchandiser/search/result', array(
            '_secure'       => Mage::app()->getStore()->isFrontUrlSecure(),
            '_nosid'        => 1,
            ));
        return $url;
    }

    public function isEnabledProduct(Mage_Catalog_Model_Abstract $product)
    {
        return (Mage_Catalog_Model_Product_Status::STATUS_ENABLED == $product->getStatus());
    }

    public function getEmptyId()
    {
        return self::ID_EMPTY;
    }

    public function getSuggestUrl($pk=null)
    {
        return $this->_getUrl('merchandiser/search/suggest', array(
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure(),
            'asid' => $pk
        ));
    }

    public function getProductinfoUrl($pk=null)
    {
        return Mage::helper("adminhtml")->getUrl('merchandiser/adminhtml/getproductinfo', array(
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure(),
        ));
    }

    public function getColumnCount($_columnCount)
    {
        if(!$_columnCount) :
            if (Mage::getSingleton('customer/session')->getColumnCount())
                $_columnCount = Mage::getSingleton('customer/session')->getColumnCount();
            else 
                $_columnCount = intval(Mage::getStoreConfig('merchandiser/options/column_count'));
        endif;
        if ($_columnCount > 7) $_columnCount = 7;
        if ($_columnCount < 1) $_columnCount = 4;
        Mage::getSingleton('customer/session')->setColumnCount($_columnCount);
        return $_columnCount;
    }

    public function getAttributeCodes() {
        $attrstring = str_replace(' ', '', Mage::getStoreConfig('merchandiser/options/attribute_codes'));
        return explode(',', $attrstring);
    }

    public function getAttributeCodesCount(){
        return count($this->getAttributeCodes());
    }

    public function getMoreImageCount(){
        if(Mage::getStoreConfig('merchandiser/options/max_images_thumbnail')) 
            return Mage::getStoreConfig('merchandiser/options/max_images_thumbnail');
        return 4;
    }

    public function getShowExtraImages(){
        return Mage::getStoreConfig('merchandiser/options/show_extra_images');
    }

    public function getShowCreationDate(){
        return Mage::getStoreConfig('merchandiser/options/show_creation_date');
    }

    public function getAjaxPageLoad(){
        return Mage::getStoreConfig('merchandiser/options/ajax_page_load');
    }

    public function getStockQty($product)
    {
        $qty = 0;
        switch ($product->getTypeId()) {
            case 'simple':
                if($product->getStatus() == '1')
                    $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
                break;
            case 'grouped':
                $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
                foreach ($associatedProducts as $associatedProduct) {
                    $qty += $this->getStockQty($associatedProduct);
                }
                break;
            case 'configurable':
                $associatedProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
                foreach ($associatedProducts as $associatedProduct) {
                    $qty += $this->getStockQty($associatedProduct);
                }    
                break;
            default:
                $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
                $qty = $qty?$qty:0;
                break;
        }

        return $qty;
    }

    public function getStockQtyHtml($product)
    {
        $qty = (int)$this->getStockQty($product);
        return $this->__('Stock: ').$qty;
    }

    public function getBundlePrice($product_id){
    	

	    $bundled_product = new Mage_Catalog_Model_Product();
	    $bundled_product->load($product_id);
	    $db_selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
	            $bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
	    );
	    $bundled_items = array();
	    foreach($db_selectionCollection as $option) { 
	        if ($option->getPrice()!="0.0000"){                 
	            $bundled_prices[]=$option->getPrice();
	        }
	    }
	
	    sort($bundled_prices);
	
	    $min_price=$bundled_prices[0];
	    $max_price_tmp=array_slice($bundled_prices, -1, 1, false);
	    $max_price=$max_price_tmp[0];
	
	    return '<div class="price-box">
                                <p class="price-from">
                <span class="price-label">From:</span>
                                    <span class="price">'.round($min_price,2).'</span>                                                                    </p>
                                <p class="price-to">
                    <span class="price-label">To:</span>
                                            <span class="price">'.round($max_price , 2).'</span>                                                                                    </p>
                    </div>';
	    
	    
	  //  return "Min: " . $min_price . '<br>' . "Max: " . $max_price;
    }


    public function buildSmartCategory($smart_cat_attr) {
        try {
        
            $smartAttribute_filter = array();
            $smartAttribute_data = unserialize($smart_cat_attr);
            $eav_configuration = Mage::getModel('eav/config');

			$allowed_operators = array('>' => 'gt',
				'<' => 'lt',
				'!' => 'neq',
				'>=' => 'gteq',
				'<=' => 'lteq',
				'=' => 'eq'
			);
            
            
            foreach ($smartAttribute_data as $cur_filter):
                $cur_type = 'eq';
                if (	$cur_filter['attribute']!='0' && 
                		is_array($cur_filter) &&
                		array_key_exists('value', $cur_filter) &&
                		array_key_exists('attribute', $cur_filter)
                	):
                	
                	
                	
                    $cur_attribute = $eav_configuration->getAttribute('catalog_product', $cur_filter['attribute']);
                    
                    
                    switch ($cur_attribute->getFrontendInput()):
                        case 'price':
                            $ranger = substr($cur_filter['value'],0,2);
                            $ranger_to_strip=2;
                            $rightRanger = substr($ranger, 1);
                            if (is_numeric($rightRanger)):
                                $ranger=substr($cur_filter['value'],0,1);
                                $ranger_to_strip=1;
                            endif;
                            if (!is_numeric($ranger)):
                                if (!array_key_exists($ranger, $allowed_operators)): $ranger='=';endif;
                                $cur_type = $allowed_operators[$ranger];
                                $cur_filter['value'] = substr($cur_filter['value'],$ranger_to_strip);
                            endif;
                            break;
                            
                        case 'select':
                            $populate_options = $cur_attribute->getSource()->getAllOptions(true);
                            $cur_filter['value']=str_replace('*','',$cur_filter['value']);
                            foreach ($populate_options as $option):
                                if (substr_count($option['label'],$cur_filter['value']) > 0):
                                    $cur_filter['value']=$option['value'];
                                    break;
                                endif;
                            endforeach;
                            break;
                            
                        case 'multiselect':
                            $populate_options = $cur_attribute->getSource()->getAllOptions(true);
                            $cur_filter['value'] = str_replace('*','(\w+)',$cur_filter['value']);
                            $cur_filter['value'] = str_replace('/','\/',$cur_filter['value']);
                            foreach ($populate_options as $option):
                                if (preg_match('/'.$cur_filter['value'].'/i',$option['label'], $matches)):
                                    $smartAttribute_filter[] = array('value' => array('finset' => array($option['value'])), 'attribute' => $cur_attribute, 'join' => 'inner', 'link' => 'OR');
                                endif;
                            endforeach;
                            continue;
                            break;
                    endswitch;

					// Handle wildcard type operators
                    if ($cur_filter['value'] == '*'):
                        $cur_filter['value'] = '%_%';
                        $cur_type = 'like';
                    endif;
                    if (strpos($cur_filter['value'], '*') !== false):
                        $cur_filter['value'] = str_replace('*','%',$cur_filter['value']);
                        $cur_type = 'like';
                    endif;
                    
                    // Handle boolean type operators
                    switch (strtolower($cur_filter['value'])):
                    	case 'yes':
                    	case 'true':
                    		$cur_filter['value']=1;
                    		break;
                    	case 'no':
                    	case 'false':
                    		$cur_filter['value']=0;
                    		break;
                    endswitch;
                    		
                    if (!array_key_exists('link', $cur_filter)): $cur_filter['link'] = 'AND'; endif;
                    $smartAttribute_filter[] = array('value' => array($cur_type => $cur_filter['value']), 'attribute' => $cur_attribute, 'join' => 'inner', 'link' => $cur_filter['link']);
                endif;
            endforeach;
            return $smartAttribute_filter;
        } catch (Exception $e) {
            mage::logException($e);
        }
    }

    private function endsWith($haystack, $needle) {  // ADDED  - MER-80
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    public function addSmartCategory($collection, $cur_category=false,$utilise_idx=true) {
    	
    	$query_params = Mage::app()->getRequest()->getParams();
    	if(isset($query_params['cat']) && $query_params['cat'] >0):
    		$cur_category = $query_params['cat'];
    	endif;
    	
        if (!$cur_category):
            $cur_category = mage::registry('current_category');
        elseif (is_numeric($cur_category)):
            $cur_category = Mage::getModel('catalog/category')->load($cur_category);
        endif;
        
        //$attrAliasPrefix = $collection->getAttributeAlias();
        $resourceModel = new OnTap_Merchandiser_Model_Resource_Mysql4_Product_Collection ;
        $attrAliasPrefix = $resourceModel->getAttributeAlias();
        
        if (is_object($cur_category)) {
        	
        	
        	$varsionVars = Mage::getVersionInfo(); 
        	
        	if( $cur_category->getMerchandiseOption() != 2):return false;endif;
        	
            if ($cur_category->getSmartAttributes()==null):
                $smartcatattr = Mage::getResourceModel('catalog/category')->getAttributeRawValue($cur_category->getId(), 'smart_attributes', $cur_category->getStoreId());
                $cur_category->setSmartAttributes($smartcatattr);
            endif;
            
            if (strlen(trim($cur_category->getSmartAttributes()))>=1 || $cur_category->getSmartAttributes() || $cur_category->getMerchandiserHeroproducts() != '') {
                if (mage::getStoreConfig('merchandiser/index/disabled') == 0 && $utilise_idx!=false && !Mage::app()->getStore()->isAdmin()):
                    try {
                        $db_readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
                        $db_select = $db_readAdapter->select()->from('catalog_category_smart_product_index')
                                ->where("category_id= {$cur_category->getId()} AND store_id = {$cur_category->getStoreId()}");
                        $smart_cat_indexed_data = $db_readAdapter->fetchAll($db_select);
                        if (is_array($smart_cat_indexed_data) && count($smart_cat_indexed_data) > 0):
                            $smartAttribute_data = array_shift($smart_cat_indexed_data);
                            
                            if($smartAttribute_data['product_ids'] != ''){
	                            $collection->addFieldToFilter('entity_id', array('in' => array(explode(',', $smartAttribute_data['product_ids']))));
	                            
	                            
	                            if($cur_category->getMerchandiserHeroproducts() != '' && $collection->getFlag('is_smart') == false){ // HERO PRODUCT FUNCTIONALITY
				                	$heroSKUArray = explode("," , $cur_category->getMerchandiserHeroproducts());
				                	$heroProducts = '';
				                	
				                	$iCounter = 1;
				                	$selectExpression = "";
				                	foreach ($heroSKUArray as $hSku){
				                		$heroProducts .= "'".trim($hSku) . "',";
				                		$selectExpression .= "IF(prod_entity.sku = '".trim($hSku)."' , ".$iCounter." , ";
				                		$iCounter++;
				                	}
				                	$selectExpression .= '999 ';
				                	for($ic=1;$ic<$iCounter;$ic++){
				                		$selectExpression .= ")";
				                	}
				                	
				                	if(strlen($heroProducts) > 0){
				                		$heroProducts = substr($heroProducts,0,strlen($heroProducts)-1);
				                	}
				                	
				                	$collection->getSelect()->join( array('prod_entity'=> 'catalog_product_entity'), 'prod_entity.entity_id = e.entity_id', array('prod_entity.sku'));
				                	
				               		$collection->getSelect()->columns(array('skupos' => new Zend_Db_Expr($selectExpression)));
				               		/* ADDED SKUPOS AS VIRTUAL COLUMN THAT CONTAINS POSITION OF SKU FROM HERO PRODUCT TEXT FIELD */
				                }
                            
	                            if($collection->getFlag('is_smart') == false){
	                            	$collection->getSelect()->distinct(true);
	                            	//$collection->getSelect()->group('e.entity_id');
	                            	$collection->setFlag('is_smart', true);
	                            }
                            	return true;
                            }
                        endif;
                    } catch (Exception $e) {
                        mage::logException($e);
                    }
                endif;
                
                //$smartAttribute_filter = $this->buildSmartCategory($cur_category->getSmartAttributes());
                
                /* NEW LOGIC STARTS */
                $selectAttribute = array();
                $filterCondition = '';
                $allowed_operators = array('>' => '>','<' => '<','!' => 'neq','>=' => '>=','<=' => '<=','=' => 'eq');
                $previousLink = "";
                $attributeConditions = unserialize($cur_category->getSmartAttributes());
                
                $numberOfAttributes =  sizeof($attributeConditions);
                $iCounter = 1;
                foreach ($attributeConditions as $condition){
                	
                	/* MER-89 */
                	if ($condition['attribute'] && ( $condition['attribute'] == 'created_at' ||  $condition['attribute'] == 'updated_at')){
                		$cur_type = '=';
                		$allowed_operators_date = array('>' => '<','<' => '>','>=' => '<=','<=' => '>=');
                		$ranger = substr($condition['value'],0,2);
                        $ranger_to_strip=2;
                        $rightRanger = substr($ranger, 1);
                        if (is_numeric($rightRanger)):
                            $ranger=substr($condition['value'],0,1);
                            $ranger_to_strip=1;
                        endif;
                        if (!is_numeric($ranger)):
                            if (!array_key_exists($ranger, $allowed_operators_date)): $ranger='<';endif;
                            $cur_type = $allowed_operators_date[$ranger];
                            $condition['value'] = substr($condition['value'],$ranger_to_strip);
                        endif;
                        
                        if(!is_numeric($condition['value'])){
                        	continue;
                		}
                        
                        $dateValue = date('Y-m-d',strtotime('-'.$condition['value'].' days'));
                        
                        if($cur_type == "="){
                        	$dateStart = date('Y-m-d 00:00:00', strtotime($dateValue));
                        	$dateEnd = date('Y-m-d 23:59:59', strtotime($dateValue));
                        	
                        	$filterCondition .= " e.".$condition['attribute']. " between '".$dateStart."' AND '".$dateEnd."' $conditionLink" ;
                		}else{
                			$filterCondition .= " e.".$condition['attribute']." " . $cur_type . " '".$dateValue."' $conditionLink" ;
                		}
                        
                        continue;
                	}
                	
                	if ($condition['attribute'] && $condition['attribute'] == 'category_id'){
                        if (Mage::app()->getStore()->isAdmin()){
                            $setCategories = explode(',', $condition['value']['eq']);
                            $rebuiltCategories = array();
                            $unused_Cats = array();
                            foreach ($setCategories as $categoryId){
                                $rebuiltCategories = array_merge($rebuiltCategories,$unused_Cats,array($categoryId));
                            }
                            $condition['value']['eq'] = implode(',',array_unique(array_filter($rebuiltCategories)));
                            //$smart_conditions = array('cat_index.product_id=e.entity_id',$collection->getConnection()->quoteInto('cat_index.category_id IN (' . $condition['value'] . ')', ""));
                            $smart_conditions = array('cat_index.product_id=e.entity_id');
                            $filterCondition .= 'cat_index.category_id IN (' . $condition['value'] . ') ' .$condition['link']  ;
                            $joinCond = join(' AND ', $smart_conditions);
                            $fromPart = $collection->getSelect()->getPart(Zend_Db_Select::FROM);

                            if (isset($fromPart['cat_index'])){
                                $fromPart['cat_index']['joinCondition'] = $joinCond;
                                $collection->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
                            }else{
                                $collection->getSelect()->join(array('cat_index' => $collection->getTable('catalog/category_product_index')), $joinCond, array('cp_category_id' => 'category_id'));
                            }
                        }else{
                            $collection->setFlag('category_ids', $condition['value']);
                            $collection->setFlag('store_id', $cur_category->getStoreId());
                        }
                        $previousLink = $condition['link'];
                        $iCounter++;
                        continue;
                	}
                	
                	$attributeModel = Mage::getModel('catalog/resource_eav_attribute')->load($condition['attribute']);
                	$cur_type = "=";
                	if ($condition['value'] == '*'){ $condition['value'] = '%_%'; $cur_type = 'like';}
                    if (strpos($condition['value'], '*') !== false){
                        $condition['value'] = str_replace('*','%',$condition['value']);
                        $cur_type = 'like';
                    }
                    switch (strtolower($condition['value'])){
                    	case 'yes':
                    	case 'true':
                    		$condition['value']=1;
                    		break;
                    	case 'no':
                    	case 'false':
                    		$condition['value']=0;
                    		break;
                    }
                	if($attributeModel->getFrontendInput() == "multiselect" || $attributeModel->getFrontendInput() == "select"){
                		
                		if(substr($condition['value'],0,1) == '!'){ // MER-90 - CODE FOR NOT OPERATOR
                			$cur_type = '!=';
                			$condition['value'] = substr($condition['value'],1);
                		}
                		
                		$populate_options = $attributeModel->getSource()->getAllOptions(true);
                		foreach ($populate_options as $option){
                			if($option['label'] == $condition['value']){
                				$condition['value'] = $option['value'];
                			}
                		}
                	}elseif($attributeModel->getFrontendInput() == "price"){
                		$ranger = substr($condition['value'],0,2);
                        $ranger_to_strip=2;
                        $rightRanger = substr($ranger, 1);
                        if (is_numeric($rightRanger)):
                            $ranger=substr($condition['value'],0,1);
                            $ranger_to_strip=1;
                        endif;
                        if (!is_numeric($ranger)):
                            if (!array_key_exists($ranger, $allowed_operators)): $ranger='=';endif;
                            $cur_type = $allowed_operators[$ranger];
                            $condition['value'] = substr($condition['value'],$ranger_to_strip);
                        endif;
                	}
					
                	$conditionLink = $condition['link'];
                	if($iCounter == $numberOfAttributes){
                		$conditionLink = "";
                	}

                	//if($attributeModel->getIsGlobal() != 0 || $cur_category->getStoreId() == 0){ // OLDER CONDITION
                	
                	//	PRICE ATTRIBUTE IS NOT TAKING STORE SPECIFIC VALUE
                	//	USED_FOR_SORT_BY IS ALSO AFFECTING JOIN QUERY
                	//	ATTRIBUTE SCOPE IS AFFECTING JOIN QUERY
                	//	NO STORE SPECIFIC DATA FATCHING FROM ADMIN SIDE
                	//	MER - 84
                	if(($attributeModel->getUsedForSortBy() != 1 && $attributeModel->getIsGlobal() == 1) || ($cur_category->getStoreId() == 0 && $utilise_idx!=false ) || $attributeModel->getFrontendInput() == "price" || (Mage::app()->getStore()->isAdmin())){
                		if($attributeModel->getFrontendInput() == "multiselect"){ // USED FIND_IN_SET FOR MULTISELECT ATTRIBUTES
                			if($cur_type == '!='){
                				$filterCondition .= " NOT FIND_IN_SET('". $condition['value'] ."' , `".$attrAliasPrefix.$attributeModel->getAttributeCode() . "`.`value`) ".$conditionLink;
                			}else{
                				$filterCondition .= " FIND_IN_SET('". $condition['value'] ."' , `".$attrAliasPrefix.$attributeModel->getAttributeCode() . "`.`value`) ".$conditionLink;
                			}
                		}else{
                			$filterCondition .= " `".$attrAliasPrefix.$attributeModel->getAttributeCode() . "`.`value` ".$cur_type. " '" . $condition['value'] . "' " .$conditionLink;
                		}
                	}else{
                		if($attributeModel->getFrontendInput() == "multiselect"){
                			if($cur_type == '!='){
                				$filterCondition .= " NOT FIND_IN_SET('". $condition['value'] ."' , IF(".$attrAliasPrefix.$attributeModel->getAttributeCode().".value_id > 0, ".$attrAliasPrefix.$attributeModel->getAttributeCode().".value, ".$attrAliasPrefix.$attributeModel->getAttributeCode()."_default.value)) ".$conditionLink;
                			}else{
                				$filterCondition .= " FIND_IN_SET('". $condition['value'] ."' , IF(".$attrAliasPrefix.$attributeModel->getAttributeCode().".value_id > 0, ".$attrAliasPrefix.$attributeModel->getAttributeCode().".value, ".$attrAliasPrefix.$attributeModel->getAttributeCode()."_default.value)) ".$conditionLink;
                			}
                		}else{
                			$filterCondition .= " IF(".$attrAliasPrefix.$attributeModel->getAttributeCode().".value_id > 0, ".$attrAliasPrefix.$attributeModel->getAttributeCode().".value, ".$attrAliasPrefix.$attributeModel->getAttributeCode()."_default.value) " .$cur_type. " '" . $condition['value'] . "' " .$conditionLink;
                		}
                	}

                	
                	$selectAttribute[] = $attributeModel->getAttributeCode();
                	$joinType = "left";
                	//if($previousLink == "AND") $joinType = "inner"; // INNER JOIN IS NO LONGER IN USE, ITS RESTRICTING COLLECTION - TROUBLE FOR HERO PRODUCTS MER-84
                	$collection->addAttributeToSelect($attributeModel->getAttributeCode() , $joinType);
                	
                	$previousLink = $condition['link'];
                	$iCounter++;
                }
                
                if(strlen($filterCondition) > 0){
                	 // XXX: remove redunant "OR" or "AND" condition linker
            	    if ($this->endsWith($filterCondition, " OR") || $this->endsWith($filterCondition, " AND")) {
                	    $filterCondition = substr($filterCondition, 0, -3);
            	    }
                }
                
                if($cur_category->getMerchandiserHeroproducts() != '' && $collection->getFlag('is_smart') == false){ // HERO PRODUCT FUNCTIONALITY
                	$heroSKUArray = explode("," , $cur_category->getMerchandiserHeroproducts());
                	$heroProducts = '';
                	
                	$iCounter = 1;
                	$selectExpression = "";
                	foreach ($heroSKUArray as $hSku){
                		$heroProducts .= "'".trim($hSku) . "',";
                		$selectExpression .= "IF(prod_entity.sku = '".trim($hSku)."' , ".$iCounter." , ";
                		$iCounter++;
                	}
                	$selectExpression .= '999 ';
                	for($ic=1;$ic<$iCounter;$ic++){
                		$selectExpression .= ")";
                	}
                	
                	if(strlen($heroProducts) > 0){
                		$heroProducts = substr($heroProducts,0,strlen($heroProducts)-1);
                	}
                	
                	$collection->getSelect()->join( array('prod_entity'=> 'catalog_product_entity'), 'prod_entity.entity_id = e.entity_id', array('prod_entity.sku'));
                	if(strlen($filterCondition) > 0){
                	   
               			$filterCondition = "prod_entity.sku in ($heroProducts) OR (" . $filterCondition . ")";
                	}else{
                		$filterCondition = "prod_entity.sku in ($heroProducts)";
                	}
               		$collection->getSelect()->columns(array('skupos' => new Zend_Db_Expr($selectExpression)));
               		/* ADDED SKUPOS AS VIRTUAL COLUMN THAT CONTAINS POSITION OF SKU FROM HERO PRODUCT TEXT FIELD */
                }
                if($collection->getFlag('is_smart') == false){
	                if(strlen($filterCondition) > 0){
	                	$collection->getSelect()->where($filterCondition);
	                }
	                $collection->getSelect()->distinct(true);
	            	//$collection->getSelect()->group('e.entity_id');
	                $collection->setFlag('is_smart', true);
                }
                if(Mage::app()->getStore()->isAdmin() && $varsionVars['minor'] != 5){
                	// GROUPBY ENTITY_ID IS RESULTING WRONG COUNT IN MAGENTO 1.5.* - AFFECTS PAGINATION
                	// MER-84
                	$collection->getSelect()->group('e.entity_id');
                }
				
                //echo $collection->getSelect() . "<br/><br/>"; 
                
               	//print_r($collection->getData());
              	//exit;  
               /* NEW LOGIC ENDS */
                
                return true;
            }else{
            	
            	$collection->addFieldToFilter('entity_id' , 0); // FOR RETURNING COLLECTION OBJECT WITH NO ROWS
                
                return true;
            }
        }
        return false;
    }

    
    public function getExtensionVersion()
	{
    	return (string) Mage::getConfig()->getNode()->modules->OnTap_Merchandiser->version;
	}
	
	public function getConfigAction(){
		$xml_actions = Mage::getStoreConfig('merchandiser/actions');
        $actions = array();
        foreach ($xml_actions as $xml_key =>$xml_action) {
        	$action = array();
            $action['name'] = (string)$xml_action['name'];
            $action['controller_sm'] = (string)$xml_action['controller_sm'];
            $action['controller_vm'] = (string)$xml_action['controller_vm'];
            $actions[$xml_key] = $action;
     	}
        return $actions;
    }
    
    public function isHideInvisibleProducts(){
    	return Mage::getStoreConfig('merchandiser/options/hide_invisible');
    }
    
    public function isHideDisabledProducts(){
    	return Mage::getStoreConfig('merchandiser/options/hide_disabled');
    }
    
    public function isHideProductPositionField(){
    	return Mage::getStoreConfig('merchandiser/options/hide_product_position_field');
    }
    
    public function getAttrAlias(){
		$abClass = new Mage_Eav_Model_Entity_Collection_Abstract;
		return $abClass->_getAttributeTableAlias('');
	}
	
	public function isVersionLessThan($major=5, $minor=3)
    {
        $curr = explode('.', Mage::getVersion()); // 1.3. compatibility
        $need = func_get_args();
        foreach ($need as $k => $v){
            if ($curr[$k] != $v)
                return ($curr[$k] < $v);
        }
        return false;
    }
}