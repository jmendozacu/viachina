<?php
/**
 * Layered Navigation Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Nav
 * @version      2.5.1
 * @license:     3aooztWVyQyLq3pTjVKXjSrcsuymFTgXHjf7iclwH0
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Nav_Block_Rewrite_CatalogsearchResult extends Mirasvit_SearchIndex_Block_Results
{
    /**
     * Retrieve Search result list HTML output, wrapped with <div>
     *
     * @return string
     */
    public function getProductListHtml()
    {
        $html = parent::getProductListHtml();
        $html = Mage::helper('adjnav')->wrapProducts($html);
        return $html;
    }
    
    /**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */ 
    public function setListCollection()
    {
            $this->getListBlock()
               ->setCollection($this->_getProductCollection());
        return $this;
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) 
        {
            $this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        }
        return $this->_productCollection;
    }
    
    public function _toHtml()
    {
        $html = parent::_toHtml();

        if(!$this->getResultCount())
        {
            $html = Mage::helper('adjnav')->wrapProducts($html);
        }    
        return $html;
    }

    /**
     * Rewrite parent
     * Return current search content
     * @return string
     */
    public function getCurrentContent()
    {
        $uid = Mage::helper('mstcore/debug')->start();

        $currentIndex = $this->getCurrentIndex();
        $html = $this->getContentBlock($currentIndex)->toHtml();
        $html = Mage::helper('adjnav')->wrapProducts($html);

        Mage::helper('mstcore/debug')->end($uid, $html);

        return $html;
    }

}