<?php
class MW_Dailydeal_Block_Adminhtml_Dealitems extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_dealitems';
    $this->_blockGroup = 'dailydeal';
    $this->_headerText = Mage::helper('dailydeal')->__('Deal Manager');
    $this->_addButtonLabel = Mage::helper('dailydeal')->__('Add Deal');
    parent::__construct();
  }
}