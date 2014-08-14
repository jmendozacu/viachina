<?php
class OnTap_Merchandiser_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
	
    protected function _getItemsData()
    {
    	
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();
        
        //if (class_exists('Amasty_Shopby_Model_Catalog_Layer_Filter_Attribute')) {
         if(Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
        	        	
        	$options = Mage::helper('amshopby/attributes')->getAttributeOptions($attribute->getAttributeCode());
	        $optionsCount = $this->_getCount($attribute);
	        $data = array();
	
	        foreach ($options as $option) {
	            if (is_array($option['value'])) {
	                continue;
	            }
	            if (!Mage::helper('core/string')->strlen($option['value'])) {
	                continue;
	            }
	            $currentVals = Mage::helper('amshopby')->getRequestValues($this->_requestVar);
	            $ind = array_search($option['value'], $currentVals);
	            if (false === $ind){
	                $currentVals[] = $option['value'];
	            }
	            else {
	                $currentVals[$ind]  = null;
	                unset($currentVals[$ind]);    
	            }
	            
	            $currentVals = implode(',', $currentVals);
	            $cnt = isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0;    
	            if ($cnt || $this->_getIsFilterableAttribute($attribute) != self::OPTIONS_ONLY_WITH_RESULTS) {
	                $data[] = array(
	                    'label'     => $option['label'],
	                    'value'     => $currentVals,
	                    'count'     => $cnt,
	                    'option_id' => $option['value'],
	                );
	            }
	        }
        	
        }else{

        	$key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
	        $data = $this->getLayer()->getAggregator()->getCacheData($key);
	
	        if ($data === null) {
	            $options = $attribute->getFrontend()->getSelectOptions();
	            $optionsCount = $this->_getResource()->getCount($this);
	            $data = array();
	
	            foreach ($options as $option) {
	                if (is_array($option['value'])) {
	                    continue;
	                }
	                if (Mage::helper('core/string')->strlen($option['value'])) {
	                    // Check filter type
	                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
	                        if (!empty($optionsCount[$option['value']])) {
	                            $data[] = array(
	                                'label' => $option['label'],
	                                'value' => $option['label'],
	                                'id' => $option['value'],
	                                'count' => $optionsCount[$option['value']],
	                            );
	                        }
	                    }
	                    else {
	                        $data[] = array(
	                            'label' => $option['label'],
	                            'value' => $option['value'],
	                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
	                        );
	                    }
	                }
	            }
	
	            $tags = array(
	                Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$attribute->getId()
	            );
	
	            $tags = $this->getLayer()->getStateTags($tags);
	            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
	        }
        }
        return $data;
        
    }

    
    
    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    
    
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
    	if(Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
	        $currentVals = Mage::helper('amshopby')->getRequestValues($this->_requestVar);
	        if ($currentVals) {
	            $this->applyFilterToCollection($currentVals);
	          
	            //generate Status Block
	            $attribute = $this->getAttributeModel();      
	            $text = '';
	            $options = Mage::helper('amshopby/attributes')->getAttributeOptions($attribute->getAttributeCode());
	            foreach ($options as $option) {
	                $k = array_search($option['value'], $currentVals);
	                if (false !== $k){
	    
	                    $exclude = $currentVals;
	                    unset($exclude[$k]);
	                    $exclude = implode(',', $exclude);
	                    if (!$exclude)
	                        $exclude = null;
	                    
	                    $query = array(
	                        $this->getRequestVar() => $exclude,
	                        Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
	                    );
	                    //$url = Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));                
	                    $url = Mage::helper('amshopby/url')->getFullUrl($query);
	                    
	                    $text .= $option['label'] 
	//                          . '&nbsp;'
	//                          . '<a href="' . $url . '">'
	//                          . '<img src="' . $this->_getRemoveImage() . '" alt="' . Mage::helper('catalog')->__('Remove This Item') . '" />'
	                          . '<a href="' . $url . '" class="btn-remove-inline">'
	                          . '<img src="' . $this->_getBlankImage() . '" width="13" height="12" alt="' . Mage::helper('catalog')->__('Remove This Item') . '" />'
	                          . '</a>, ';
	                }
	            }
	            
	            if ($text) {
	                $text = substr($text, 0, -2);
	            }
	            
	            $state = $this->_createItem($text, $currentVals)
	                        ->setVar($this->_requestVar)
	                        ->setIsMultiValues(true);
	                        
	            $this->getLayer()->getState()->addFilter($state);
	        }
    	}else{
    		
	        $filter = $request->getParam($this->_requestVar);
	        if (is_array($filter)):
	            return $this;
	        endif;
	        if(is_numeric($filter)):
	            $text = $this->_getOptionText($filter);
	        else:
	            $attribute = $this->getAttributeModel();
	            $options = $attribute->getFrontend()->getSelectOptions();
	            $text = $filter;
	            foreach($options as $option):
	                if($option['label'] == $filter):
	                    $filter = $option['value'];
	                    break;
	                endif;
	            endforeach;
	        endif;
	        if ($filter && $text):
	            $this->_getResource()->applyFilterToCollection($this, $filter);
	            $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
	            $this->_items = array();
	        endif;
    	}
        return $this;
    }
    
    protected function _getRemoveImage()
    {
    	if(!Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
    		return parent::_getRemoveImage();
    	}
     	if (Mage::helper('merchandiser')->isVersionLessThan(1, 4)){
        	return Mage::getDesign()->getSkinUrl('images/list_remove_btn.gif');
     	}
     	else {
        	return Mage::getDesign()->getSkinUrl('images/btn_remove.gif');
     	}
    }
    
    protected function _getBlankImage()
    {
    	if(!Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
    		return parent::_getBlankImage();
    	}
        return Mage::getDesign()->getSkinUrl('images/spacer.gif');
    }
    
    protected function _getCount($attribute)
    {
    	if(!Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
    		return parent::_getCount($attribute);
    	}
        $optionsCount = array();
        if (Mage::helper('merchandiser')->isVersionLessThan(1, 4)){
            $optionsCount = Mage::getSingleton('catalogindex/attribute')->getCount(
                $attribute,
                $this->_getBaseCollectionSql()
            );
        }
        else {
            // clone select from collection with filters
            $select = $this->_getBaseCollectionSql();
            
            // reset columns, order and limitation conditions
            $select->reset(Zend_Db_Select::COLUMNS);
            $select->reset(Zend_Db_Select::ORDER);
            $select->reset(Zend_Db_Select::LIMIT_COUNT);
            $select->reset(Zend_Db_Select::LIMIT_OFFSET);
            $select->reset(Zend_Db_Select::GROUP); // TO REMOVE ENTITY ID GROUP BY - THIS CAUSING ONLY ONE COUNT
    
            $connection = $this->_getResource()->getReadConnection();
            $tableAlias = $attribute->getAttributeCode() . '_idx';
            $conditions = array(
                "{$tableAlias}.entity_id = e.entity_id",
                $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
                $connection->quoteInto("{$tableAlias}.store_id = ?", $this->getStoreId()),
            );
    
            $select
                ->join(
                    array($tableAlias => $this->_getResource()->getMainTable()),
                    join(' AND ', $conditions),
                    array('value', 'count' => "COUNT(DISTINCT {$tableAlias}.entity_id)"))
                ->group("{$tableAlias}.value");
    
            $optionsCount = $connection->fetchPairs($select);
        } 
         
        return $optionsCount;       
    }
    
    protected function _getIsFilterableAttribute($attribute)
    {
    	if(!!Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
    		return parent::_getIsFilterableAttribute($attribute);
    	}
        $res = null;
        if (Mage::helper('merchandiser')->isVersionLessThan(1, 4)){ 
            $res = $attribute->getIsFilterable();
        }
        else{
             $res = parent::_getIsFilterableAttribute($attribute);
        }
        
        return $res;  
    }
    
    protected function _getAttributeTableAlias()
    {
    	if(!Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
    		return parent::_getAttributeTableAlias();
    	}
        $alias = null;
        if (Mage::helper('merchandiser')->isVersionLessThan(1, 4)){ 
            $alias = 'attr_index_' . $this->getAttributeModel()->getId(); 
        }
        else{
            $alias = $this->getAttributeModel()->getAttributeCode() . '_idx';
        }
        return $alias;  
    }
    
    
    protected function applyFilterToCollection($value)
    {
    	if(!Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
    		return parent::applyFilterToCollection();
    	}
        $collection = $this->getLayer()->getProductCollection();
        $attribute  = $this->getAttributeModel();
        $alias      = $this->_getAttributeTableAlias();
        
        
        if(Mage::helper('merchandiser')->isVersionLessThan(1, 4)){
            $collection->getSelect()->join(
                array($alias => Mage::getSingleton('core/resource')->getTableName('catalogindex/eav')),
                $alias . '.entity_id=e.entity_id',
                array()
            )
            ->where("$alias.store_id = ?", Mage::app()->getStore()->getId())
            ->where("$alias.attribute_id = ?", $attribute->getId())
            ->where("$alias.value IN (?)", $value);
        }
        else {
            $attr = false;  // use AND logic
            $connection = $this->_getResource()->getReadConnection();
            
            if (is_array($value) && $attr) {
                
                foreach ($value as $i => $attrValue) {
                    $alias = $alias . $i;
                    $conditions = array(
                        "{$alias}.entity_id = e.entity_id",
                        $connection->quoteInto("{$alias}.attribute_id = ?", $attribute->getAttributeId()),
                        $connection->quoteInto("{$alias}.store_id = ?",     $collection->getStoreId()),
                        $connection->quoteInto("{$alias}.value = ?",      $attrValue)
                    );

                    $collection->getSelect()->join(
                        array($alias => $this->_getResource()->getMainTable()),
                        join(' AND ', $conditions),
                        array()
                    );
                }
            } else {
                
                $conditions = array(
                    "{$alias}.entity_id = e.entity_id",
                    $connection->quoteInto("{$alias}.attribute_id = ?", $attribute->getAttributeId()),
                    $connection->quoteInto("{$alias}.store_id = ?",     $collection->getStoreId()),
                    $connection->quoteInto("{$alias}.value IN(?)",      $value)
                );
                
                $collection->getSelect()->join(
                    array($alias => $this->_getResource()->getMainTable()),
                    join(' AND ', $conditions),
                    array()
                );          
            }
        }
        
        if (isset($_REQUEST['debug'])) {
            Zend_Debug::dump($collection->getSelect()->__toString());
        }
        
        if (count($value) > 1)
            $collection->getSelect()->distinct(true);

        return null;
    }      
    
    
    
    
    protected function _initItems()
    {
    	if(!Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
    		return parent::_initItems();
    	}
        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            $item = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count']
            );
            $item->setOptionId($itemData['option_id']);
            $items[] = clone $item;
        }
        $this->_items = $items;
        return $this;
    } 
    
    //start new functions
   
    // will work for both 1.3 and 1.4
    protected function _getBaseCollectionSql()
    {
    	if(!Mage::helper('core')->isModuleEnabled('Amasty_Shopby')){
    		return parent::_getBaseCollectionSql();
    	}
        $alias = $this->_getAttributeTableAlias();
        
        $baseSelect = clone parent::_getBaseCollectionSql();
        
        $oldWhere = $baseSelect->getPart(Varien_Db_Select::WHERE);
        $newWhere = array();

        foreach ($oldWhere as $cond){
            if (!strpos($cond, $alias)){
                $newWhere[] = $cond;
            }
        }
  
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND'){
            $newWhere[0] = substr($newWhere[0], 3);
        }
        
        $baseSelect->setPart(Varien_Db_Select::WHERE, $newWhere);
        
        $oldFrom = $baseSelect->getPart(Varien_Db_Select::FROM);
        $newFrom = array();
        
        foreach ($oldFrom as $name=>$val){
            if ($name != $alias){
                $newFrom[$name] = $val;
            }
        }
        $baseSelect->setPart(Varien_Db_Select::FROM, $newFrom);

        return $baseSelect;
    }
}