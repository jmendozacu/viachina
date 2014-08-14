<?php
class EM_LayeredNavigation_Block_Loading extends Mage_Core_Block_Template
{
	public function isAjaxEnabled() {
		return Mage::helper('layerednavigation')->isAjaxEnabled();
	}
	
	public function getEndpointUrl() {
		if ($this->getRequest()->getModuleName()=='catalogsearch')
			return Mage::getUrl('layernav/index/search/');
		else
			return Mage::getUrl('layernav/index/view/');
	}
}
