<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/11/14
 * Time: 5:30 AM
 */
class Custom_SearchByName_Block_Catalog_Product_Collection extends Mage_Catalog_Block_Product_Abstract
{
    public function getMostSearchedCollection($limit = 8){
        $collection = Mage::getModel('searchbyname/query')->getCollection()
            ->setPopularQueryFilter(Mage::app()->getStore('brands_store')->getId())
            ->setPageSize($limit);

        return $collection;
    }

    public function getMostSoldCollection($limit = 8){
        $collection = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->setOrder('order_qty', 'desc');

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $collection->setPageSize(8)->setCurPage(1);
        $this->setProductCollection($collection);
        return $collection;
    }
}