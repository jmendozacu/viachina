<?php
class OnTap_Merchandiser_Block_Adminhtml_Catalog_Category_Tree extends Mage_Adminhtml_Block_Catalog_Category_Tree {

    public function getBreadcrumbsJavascript($path, $javascriptVarName) {
        if (empty($path)):
            return '';
        endif;

        $tree_categories = Mage::getResourceSingleton('catalog/category_tree')->setStoreId($this->getStore()->getId())->loadBreadcrumbsArray($path);
        if (empty($tree_categories)):
            return '';
        endif;
        foreach ($tree_categories as $key => $category):
            $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('sku');
            $count = 0;

            if (mage::helper('merchandiser')->addSmartCategory($collection, $category['entity_id'])):
                $count = $collection->count();
                $category['product_count'] += $count;
            endif;
            $tree_categories[$key] = $this->_getNodeJson($category);
        endforeach;
        return '<script type="text/javascript">'. $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($tree_categories) . ';'. ($this->canAddSubCategory() ? '$("add_subcategory_button").show();' : '$("add_subcategory_button").hide();'). '</script>';
    }

}
