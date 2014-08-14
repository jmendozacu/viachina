<?php
class OnTap_Merchandiser_Block_Adminhtml_Catalog_Category_Tab_Smartmerch extends Mage_Core_Block_Template {

	// Construct the Visual Merchandiser tab in the admin panel
    public function __construct() {
        parent::__construct();
        
        $this->setId('catalog_category_smartmerch');
        $this->setTemplate('merchandiser/smartmerch/tab.phtml'); 
    }

}

