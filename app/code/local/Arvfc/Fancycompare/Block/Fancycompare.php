<?php
class Arvfc_Fancycompare_Block_Fancycompare extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFancycompare()     
     { 
        if (!$this->hasData('fancycompare')) {
            $this->setData('fancycompare', Mage::registry('fancycompare'));
        }
        return $this->getData('fancycompare');
        
    }
}