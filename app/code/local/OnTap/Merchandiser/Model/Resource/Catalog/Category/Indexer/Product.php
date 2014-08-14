<?php

class OnTap_Merchandiser_Model_Resource_Catalog_Category_Indexer_Product extends Mage_Index_Model_Mysql4_Abstract {

    /**
     * Category table
     *
     * @var string
     */
    protected $_categoryTable;

    /**
     * Category product table
     *
     * @var string
     */
    protected $_categoryProductTable;

    /**
     * Product website table
     *
     * @var string
     */
    protected $_productWebsiteTable;

    /**
     * Store table
     *
     * @var string
     */
    protected $_storeTable;

    /**
     * Group table
     *
     * @var string
     */
    protected $_groupTable;

    /**
     * Array of info about stores
     *
     * @var array
     */
    protected $_storesInfo;
    protected $_categoryModel;

    /**
     * Model initialization
     *
     */
    protected function _construct() {
        $this->_init('merchandiser/catalog_category_smart_product_index', 'category_id');
        $this->_categoryTable = $this->getTable('catalog/category');
        $this->_categoryProductTable = $this->getTable('catalog/category_product');
        $this->_productWebsiteTable = $this->getTable('catalog/product_website');
        $this->_storeTable = $this->getTable('core/store');
        $this->_groupTable = $this->getTable('core/store_group');
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductSave(Mage_Index_Model_Event $event) {
        return $this;
    }

    /**
     * Process Catalog Product mass action
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event) {
        return $this;
    }

    /**
     * Return array of used root category id - path pairs
     *
     * @return array
     */
    protected function _getRootCategories() {
        $rootCategories = array();
        $stores = $this->_getStoresInfo();
        foreach ($stores as $storeInfo) {
            if ($storeInfo['root_id']) {
                $rootCategories[$storeInfo['root_id']] = $storeInfo['root_path'];
            }
        }
        return $rootCategories;
    }

    /**
     * Process category index after category save
     *
     * @param Mage_Index_Model_Event $event
     */
    public function catalogCategorySave(Mage_Index_Model_Event $event) {
        $event_data = $event->getNewData();
        
        /**
         * Determine is Smart Merchandiser was involved here
         */
        if (isset($event_data['smart_products_was_changed'])) {
            try {
                $this->beginTransaction();
                $this->clearTemporaryIndexTable();
                $idxTable = $this->getIdxTable();
                $idxAdapter = $this->_getIndexAdapter();
                $stores = $this->_getStoresInfo();
                /**
                * Build index for each store
                */
                foreach ($stores as $storeData):
                    $storeId = $storeData['store_id'];
                    $category_current = $event_data['affected_category'];
                    $this->buildTempIndex($category_current, $storeId, $idxAdapter, $idxTable);
                endforeach;
                $this->syncData();
                /**
                *	Clean up temporary table
                */
                $this->clearTemporaryIndexTable();
                $this->commit();
            } catch (Exception $e) {
                $this->rollBack();
                throw $e;
            }
            return $this;
        }
    }

    /**
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function reindexAll() {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();
            $idxTable = $this->getIdxTable();
            $idxAdapter = $this->_getIndexAdapter();
            $stores = $this->_getStoresInfo();
            /**
             * Build index for each store
             */
            foreach ($stores as $storeData):
                $storeId = $storeData['store_id'];
                $rootId = $storeData['root_id'];

                if (is_null($this->_categoryModel)):
                    $this->_categoryModel = Mage::getModel('catalog/category');
                endif;
                

                //$category_treeModel = $this->_categoryModel->getTreeModel()->loadNode($rootId);
                //$category_nodes = $category_treeModel->loadChildren()->getChildren();

                /*$nodeIds = array();
                foreach ($category_nodes as $node):
                    $nodeIds[] = $node->getId();
                endforeach;*/

                //$collection = $this	->_categoryModel->getCollection()
                $collection = Mage::getModel('catalog/category')->getCollection()
									->addAttributeToFilter('is_active', 1)
									->addAttributeToSelect(array('smart_attributes','merchandiser_heroproducts'))
									//->addAttributeToFilter('smart_attributes' , array('neq' => ''))
									->addAttributeToFilter(array(array('attribute'=>'smart_attributes','neq'=>''),array('attribute'=>'merchandiser_heroproducts', 'neq'=>'')))
				//					->addIdFilter($nodeIds)
									->addAttributeToSort('entity_id')
									->setStoreId($storeId)
									//->load()
									;
			

                foreach ($collection as $category_current):
                    $this->buildTempIndex($category_current, $storeId, $idxAdapter, $idxTable);
                endforeach;
            endforeach;
            $this->syncData();

            /**
             * Clean up temporary tables
             */
            $this->clearTemporaryIndexTable();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    protected function buildTempIndex($category_current, $storeId, $idxAdapter, $idxTable) {
    	// Set up collection based upon store ID provided
        $productCollection = Mage::getResourceModel('catalog/product_collection')->setStoreId($storeId);
        $writeAdapter = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        // Determine if category has Smart Merchandiser filters applied to it
        if (Mage::helper('merchandiser')->addSmartCategory($productCollection, $category_current->getId(), false)) {
        	
            $productCollection->load();
            
            $writeAdapter->query("DELETE FROM $idxTable WHERE category_id = " . $category_current->getId() . " AND store_id = " . $storeId);
            
            $collection_ids = $productCollection->getAllIds();
            $collection_ids = array_unique($collection_ids);
            
            
            //$idxAdapter->insert($idxTable, array('category_id' => $category_current->getId(), 'product_ids' => implode(',', $collection_ids), 'store_id' => $storeId));
            /* $idxAdapter->insert IS COMMENTED BECUASE THIS FUNCTION REMOVING OLDER ENTRIES OF THE TABLE EVEN IF THEY ARE FROM ANOTHER TABLE */
            
          //  echo "INSERT INTO $idxTable VALUES (".$category_current->getId()." , '".implode(',', $collection_ids)."',".$storeId.")<br/>" ;
            
            
            $writeAdapter->query("INSERT INTO $idxTable VALUES (".$category_current->getId()." , '".implode(',', $collection_ids)."',".$storeId.")");
        }
    }

    /**
     * Get array with store|website|root_categry path information
     *
     * @return array
     */
    protected function _getStoresInfo() {
        if (is_null($this->_storesInfo)) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                    ->from(array('s' => $this->getTable('core/store')), array('store_id', 'website_id'))
                    ->join(
                            array('sg' => $this->getTable('core/store_group')), 'sg.group_id = s.group_id', array())
                    ->join(
                    array('c' => $this->getTable('catalog/category')), 'c.entity_id = sg.root_category_id', array(
                'root_path' => 'path',
                'root_id' => 'entity_id'
                    )
            );
            $this->_storesInfo = $adapter->fetchAll($select);
        }

        return $this->_storesInfo;
    }

    /**
     * Retrieve temporary decimal index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null) {
        return $this->getTable('merchandiser/catalog_category_smart_product_indexer_idx');
    }

}
