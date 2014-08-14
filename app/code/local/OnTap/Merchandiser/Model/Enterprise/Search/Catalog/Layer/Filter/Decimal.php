<?php
class OnTap_Merchandiser_Model_Enterprise_Search_Catalog_Layer_Filter_Decimal extends Enterprise_Search_Model_Catalog_Layer_Filter_Decimal
{
    /**
     * Adds support for Smart Merchandiser with Magento Enterprise
     *
     * Add params to faceted search
     *
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Category
     */
    public function addFacetCondition()
    {
        $collection = $this->getLayer()->getProductCollection();
        if($collection instanceof Enterprise_Search_Model_Resource_Collection) {
            return parent::addFacetCondition();
        }
        return $this;
    }

    
}
