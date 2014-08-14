<?php

class MW_Dailydeal_Block_Adminhtml_Dealitems_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('dailydeal_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('dailydeal')->__('Daily Deal Information'));
    }

    protected function _beforeToHtml()
    {
        
        $this->addTab('list_product', array(
            'label' => Mage::helper('dailydeal')->__('Select Product'),
            'title' => Mage::helper('dailydeal')->__('Select Product'),
            'content' => $this->getLayout()->createBlock( new MW_Dailydeal_Block_Adminhtml_Dealitems_Edit_Product_Form() )->toHtml()
        ));

        $this->addTab('conf_section', array(
            'label' => Mage::helper('dailydeal')->__('Deal Information'),
            'title' => Mage::helper('dailydeal')->__('Deal Information'),
            'content' => $this->getLayout()->createBlock( new MW_Dailydeal_Block_Adminhtml_Dealitems_Edit_Conf_Form() )->toHtml(),
        ));

        $id = $this->getRequest()->getParam("id");
        if(isset($id)){
            $this->addTab('report', array(
                'label' => Mage::helper('dailydeal')->__('Deal Report'),
                'title' => Mage::helper('dailydeal')->__('Deal Report'),
                'content' => $this->getLayout()->createBlock(new MW_Dailydeal_Block_Adminhtml_Dealitems_Edit_Report_Form())->toHtml(),
            ));
        }
            
        return parent::_beforeToHtml();
    }
}