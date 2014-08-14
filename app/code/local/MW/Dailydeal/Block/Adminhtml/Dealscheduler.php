<?php

class MW_Dailydeal_Block_Adminhtml_Dealscheduler extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_dealscheduler';
        $this->_blockGroup = 'dailydeal';
        $this->_headerText = Mage::helper('dailydeal')->__('Deal Generator');
        $this->_addButtonLabel = Mage::helper('dailydeal')->__('Add Deal Generator');
//        $this->_addButton('refreshdeal', array(
//            'label' => Mage::helper('dailydeal')->__('Generate Deals'),
//            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/applyGenerateDeal') . '\')',
//        ));
        parent::__construct();
    }

}