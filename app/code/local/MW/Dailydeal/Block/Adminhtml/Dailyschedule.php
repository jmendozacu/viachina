<?php
/**
 * Mage World
 *
 * NOTICE OF LICENSE

 * @category    MW
 * @package     MW_Dailydeal
 * @copyright   Copyright (c) 2012 Mage World (http://www.mageworld.com)
 
 */


/**
 * Product reports admin controller
 *
 * @category   MW
 * @package    MW_Dailydeal
 * @author     Magento Developer <chinhbt@asiaconnect.com.vn>
 */
//lay tu Mage_Adminhtml_Block_Report_Product
class MW_Dailydeal_Block_Adminhtml_Dailyschedule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	
		
  public function __construct()
  {
    $this->_controller = 'adminhtml_dailyschedule';
    $this->_blockGroup = 'dailydeal';
    $this->_headerText = Mage::helper('dailydeal')->__('Manage Deals by Day');
      $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');	
    parent::__construct();
    		
    $this->_removeButton('add');
  }

}