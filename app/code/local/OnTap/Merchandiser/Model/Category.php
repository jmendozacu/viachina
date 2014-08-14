<?php


class OnTap_Merchandiser_Model_Category extends Mage_Catalog_Model_Category {

    /**
     * Gets the product collection, then checks if we have any Smart Merchandiser attributes defined for the category. If we do, then we add the necessary filters to prepare the updated collection according to the logic in the defined filters
     *
     */
    public function getProductCollection() {
        $prod_collection = Mage::getResourceModel('catalog/product_collection')->setStoreId($this->getStoreId());
        if (Mage::helper('merchandiser')->addSmartCategory($prod_collection)):
            return $prod_collection;    
        endif;
        return parent::getProductCollection();
    }
    
    /**
     * Determines the product count using any Smart Merchandiser filters to determine the products actually in the collection
     * 
     */
    public function setProductCount($value){
        $prod_collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('sku');
        $prod_collection_count = 0;
        if (Mage::helper('merchandiser')->addSmartCategory($prod_collection,$this->getId())):
            $prod_collection->load();
            $prod_collection_count = $prod_collection->count();
        endif;
        $this->setData('product_count',($value + $prod_collection_count));
    }

    

}
