<?php

class OnTap_Merchandiser_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    
    public function getUrl()
    {
        $query = array($this->getFilter()->getRequestVar()=>htmlentities($this->getLabel()),Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null);
        return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
    }

}
