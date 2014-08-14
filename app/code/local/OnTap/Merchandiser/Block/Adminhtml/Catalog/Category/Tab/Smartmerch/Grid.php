<?php
class OnTap_Merchandiser_Block_Adminhtml_Catalog_Category_Tab_Smartmerch_Grid extends Mage_Adminhtml_Block_Widget_Grid {


	/**
	*	Initial construct in the widget to set the template in use
	*/
    public function __construct() {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('smart_products_grid');
        $this->setFilterVisibility(false);
        $this->setDefaultSort(false);
        $this->setTemplate('merchandiser/smartmerch/grid.phtml');
    }

	/**
	*	Set up the columns to use in the grid
	*/
    protected function _prepareColumns() {

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'sortable' => false,
            'width' => '40',
            'index' => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index' => 'name',
            'sortable' => false
        ));
        
        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '120',
            'index' => 'sku',
            'sortable' => false
        ));
        $this->addColumn('price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'currency',
            'width' => '1',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'price',
            'sortable' => false
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('merchandiser/adminhtml_catalog_category/grid', array('category_id' => $this->getCategory()->getId(), '_current' => true));
    }

    public function getCategory() {
        return Mage::registry('category');
    }

	/**
	*	Main collection gathering for grid
	*/
    protected function _prepareCollection() {
        if ($this->getCategory()->getId()):
            $this->setDefaultFilter(array('in_category' => 1));
        endif;
        
        // Set the collection up so that we have what we need to display the grid
        $collection = Mage::getModel('catalog/product')	->getCollection()
														->addAttributeToSelect('name')
														->addAttributeToSelect('sku')
														->addAttributeToSelect('price')
														->addStoreFilter($this->getRequest()->getParam('store'))
														->joinField('position', 'catalog/category_product', 'position', 'product_id=entity_id', 'category_id=' .(int) $this->getRequest()->getParam('id', 0), 'left');
		if(Mage::helper('merchandiser')->isHideInvisibleProducts()){
			$collection->addAttributeToFilter('visibility', array('or'=>array(4,2)));
		}
		if(Mage::helper('merchandiser')->isHideDisabledProducts()){
			$collection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
		}
		
		// Check if we have Smart Merchandiser filters applied and then add the products we need
        if (Mage::helper('merchandiser')->addSmartCategory($collection)):
        
        	Mage::getModel('merchandiser/merchandiser')->setHeroProductAtTop($collection,$this->getRequest()->getParam('id', 0)); 
        	// Inject any custom sorting logic based on built-in or third-party add-ons
        	if($this->getCategory()->getMerchandiserSortingOptions() != ''):
            	$actions = Mage::helper('merchandiser')->getConfigAction();
            	if(isset($actions[$this->getCategory()->getMerchandiserSortingOptions()])):
            		$optionControllerArray = explode("::" , $actions[$this->getCategory()->getMerchandiserSortingOptions()]['controller_sm']);
            		if(is_array($optionControllerArray) && sizeof($optionControllerArray) == 2):
            			call_user_func(array($optionControllerArray[0],$optionControllerArray[1]), $collection);
            		endif;
            	endif;
         	endif;
         	
         	// Set the collection 
            $this->setCollection($collection);
            $this->_preparePage();
            
            if ($this->getCategory()->getProductsReadonly()):
                $productIds = $this->_getSelectedProducts();
                if (empty($productIds)):
                    $productIds = 0;
                endif;
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            endif;
        else:
        	// This is a normal category, so return collection via the parent.
            return parent::_prepareCollection();
        endif;
        
    }

}

