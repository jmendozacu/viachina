<?php

class MW_Dailydeal_Block_Showtabs_Active extends Mage_Catalog_Block_Product_List
{

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            
            $store_id = Mage::app()->getStore()->getId();
            $tblCatalogStockItem = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
            $currenttime = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));

            $collection = MW_Dailydeal_Model_Dailydeal::getModel()
                    ->getCollection()
                    ->addFieldToFilter('status', MW_Dailydeal_Model_Status::STATUS_ENABLED)
                    ->addFieldToFilter('expire', MW_Dailydeal_Model_Status::STATUS_EXPIRE_FALSE)
                    ->addFieldToFilter('store_view', array(array('like' => '%' . Mage::app()->getStore()->getId() . '%'), array('like' => '0')))
                    ->addProductStatusFilter($store_id);

            $collection->addFieldToFilter('start_date_time', array('to' => $currenttime))
                    ->addFieldToFilter('end_date_time', array('from' => $currenttime));

            $collection->getSelect()->joinLeft(
                    array('stock' => $tblCatalogStockItem), 'stock.product_id = main_table.product_id', array('stock.qty', 'stock.is_in_stock')
            );

            $collection->getSelect()->where("stock.is_in_stock = " . Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK);

            $this->_productCollection = $collection;
            
            
        }
        return $this->_productCollection;
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        $toolbar->setAvailableOrders(array());  // clear
        $toolbar->addOrderToAvailableOrders('end_date_time', Mage::helper('dailydeal')->__('Time'));
        $toolbar->addOrderToAvailableOrders('cur_product', Mage::helper('catalog')->__('Name'));
        $toolbar->addOrderToAvailableOrders('dailydeal_price', Mage::helper('catalog')->__('Price'));

        $toolbar->setDefaultDirection('asc');


        $collection = $this->_getProductCollection();
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }

    public function getActivedeals()
    {
        return $this->_getProductCollection();
    }

    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $return = '';

        $deal = MW_Dailydeal_Model_Dailydeal::getInstance();
        if ($deal->checkDealPrice($product) == false) {
            $temp = Mage::getModel("catalog/product")->getCollection()
                    ->addAttributeToSelect(Mage::getSingleton("catalog/config")->getProductAttributes())
                    ->addAttributeToFilter("entity_id", $product->getId())
                    ->setPage(1, 1)
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->load()
                    ->getFirstItem();
            $product = $temp;
            $return = parent::getPriceHtml($product, true);
        } else {
            $this->setTemplate('catalog/product/price.phtml');
            $this->setProduct($product);
            $return = $this->toHtml();
        }

        return $return;
    }

    //getcollection 1 mang cac deal kich hoat, sap xep theo tu tu kich hoat trc tu tren xuong
    //de dang chon dc deal dc uu tien
    public function getDeals(){

        $store_id = Mage::app()->getStore()->getId();

        $tblCatalogStockItem = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
        $currenttime = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));


        $collection = MW_Dailydeal_Model_Dailydeal::getModel()->getCollection()
            ->addFieldToFilter('status', MW_Dailydeal_Model_Status::STATUS_ENABLED)
            ->addFieldToFilter('expire', MW_Dailydeal_Model_Status::STATUS_EXPIRE_FALSE)
            ->addFieldToFilter('store_view', array(array('like' => '%' . Mage::app()->getStore()->getId() . '%'), array('like' => '0')))
            ->addFieldToFilter('start_date_time', array('to' => $currenttime))
            ->addFieldToFilter('end_date_time', array('from' => $currenttime))
            ->addProductStatusFilter($store_id)
            ->getConfigSortBy();

        $collection->getSelect()->joinLeft(
            array('stock' => $tblCatalogStockItem), 'stock.product_id = main_table.product_id', array('stock.qty', 'stock.is_in_stock')
        );

        $collection->getSelect()->where("stock.is_in_stock = " . Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK);

        return $collection;
    }

}