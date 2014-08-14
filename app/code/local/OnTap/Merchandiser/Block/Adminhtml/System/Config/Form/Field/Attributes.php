<?php

class OnTap_Merchandiser_Block_Adminhtml_System_Config_Form_Field_Attributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

    protected $_formatted = array();
 
    private function populateAttributes() {
    
    	// Set up attribute array
        $populate_options = array();
        $populate_options['category_id'] = "Clone category ID(s)";
        
        $populate_options['created_at'] = "Date Created (days ago)"; // 	MER-89 
        $populate_options['updated_at'] = "Date Modified (days ago)"; // 	MER-89
        
        $product_attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        $eav_configuration = Mage::getModel('eav/config');
        foreach ($product_attributes as $att_code):
            $cur_attribute = $eav_configuration->getAttribute('catalog_product', $att_code);
            $attribute_label = ($cur_attribute->getFrontendLabel()!='') ? $cur_attribute->getFrontendLabel() : $cur_attribute->getAttributeCode();
            // We don't handle dates at the moment, so skip
            if ($cur_attribute->getBackendType()=='datetime'): continue; endif;
            $populate_options[$cur_attribute->getAttributeId()]=str_replace("'","", $attribute_label);
        endforeach;
        
        // Sort attribtues into alphabetical order
        asort($populate_options);
        return $populate_options;
    }
    

 
    
    public function __construct() {

		// Initialise layout for attribute selection block
        $admin_layout = mage::getModel('core/layout');
        $renderAttribute =  $admin_layout->createBlock(
        		'merchandiser/adminhtml_system_config_form_renderer_select',
        		'smartmerch_dminhtml_system_config_form_renderer_select',
                array(
                		'values'	=>	$this->populateAttributes(),
                		'class' => 'vm_select'
                	 )
                )->setWidth(100);
                
        $renderLogic =  $admin_layout->createBlock(
        		'merchandiser/adminhtml_system_config_form_renderer_select',
                'smartmerch_dminhtml_system_config_form_renderer_select',
                array(
                		'values'	=>array('OR'=>'OR','AND'=>'AND'),
                		'class' => 'vm_select logic_link'
                	  )
                )->setWidth(75);
        
        
        // Add main columns
        $this->addColumn('attribute', array(
            	'label' => Mage::helper('adminhtml')->__('Attribute'),
            	'renderer' => $renderAttribute
        ));
        
        $this->addColumn('value', array(
            	'label' => Mage::helper('adminhtml')->__('Value'),
        ));

        $this->addColumn('link', array(
            	'label' => Mage::helper('adminhtml')->__('Logic'),
            	'renderer' => $renderLogic
        ));
        
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Attribute');
        
        // Render within template
        parent::__construct();
        $this->setTemplate('merchandiser/smartmerch/attributes.phtml');
        $this->setHtmlId('_cat_'.Mage::registry('category')->getId());
        
    }

}