<?php

class MW_Dailydeal_Block_adminhtml_Dealscheduler_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('dailydeal_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('dailydeal')->__('Deal Generator'));
    }

    protected function _beforeToHtml()
    {
        
        $this->addTab('conf_section', array(
            'label' => Mage::helper('dailydeal')->__('Settings'),
            'title' => Mage::helper('dailydeal')->__('Settings'),
            'content' => $this->getLayout()->createBlock( new MW_Dailydeal_Block_adminhtml_Dealscheduler_Edit_Conf_Form() )->toHtml(),
        ));

        $this->addTab('list_product', array(
            'label' => Mage::helper('dailydeal')->__('Select Product(s)'),
            'title' => Mage::helper('dailydeal')->__('Select Product(s)'),
            'url'       => $this->getUrl('*/*/product', array('_current' => true)),
//            'content' => $this->getLayout()->createBlock( new MW_Dailydeal_Block_adminhtml_Dealscheduler_Edit_Product_Form() )->toHtml(),
            'class'     => 'ajax',
        ));
        
        return parent::_beforeToHtml();
    }
}