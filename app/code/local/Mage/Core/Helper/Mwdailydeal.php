<?php

class Mage_Core_Helper_Mwdailydeal extends Mage_Core_Helper_Abstract {

    public function categoryDealInfo($_product) {
        $result = '';
        if (Mage::helper('core/data')->isModuleEnabled('MW_Dailydeal')){
            $result = Mage::helper('dailydeal/data')->categoryDealInfo($_product);
        }
        
        return $result;
    }

    public function productDealInfo($_product) {
        $result = '';
        if (Mage::helper('core/data')->isModuleEnabled('MW_Dailydeal')){
            $result = Mage::helper('dailydeal/data')->productDealInfo($_product);
        }
        
        return $result;
    }
    
    public function showImage($_product) {
        $result = '';
        if (Mage::helper('core/data')->isModuleEnabled('MW_Dailydeal')){
            $result = Mage::helper('dailydeal/data')->showImage($_product);
        }
        
        return $result;
    }
    
}