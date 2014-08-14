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
class AdjustWare_Nav_Block_Rewrite_FrontCatalogProductList extends Mage_Catalog_Block_Product_List
{
     public function __construct(){
        parent::__construct();
        if(Mage::helper('adjnav')->isModuleEnabled('Aitoc_Aitproductslists'))
        {
              $this->setTemplate('aitcommonfiles/design--frontend--base--default--template--catalog--product--list.phtml');
        }
        elseif(Mage::helper('adjnav')->isModuleEnabled('MagicToolbox_MagicZoomPlus'))
        {
              $this->setTemplate('magiczoomplus/list.phtml');
        }
        else {
            $this->setTemplate('catalog/product/list.phtml');
        }
    }
    
}