<?php 


// TODO TEST THIS!!!
// ALTER TABLE `sales_flat_order` ADD `adyen_boleto_pdf` TEXT NOT NULL ;
$installer = $this;

/* @var $installer Madia_Adyen_Model_Entity_Setup */
$installer->startSetup();

$installer->addAttribute('order', 'adyen_boleto_pdf', array(
		'label' => 'Adyen Boleto PDF',
		'visible' => true,
		'required' => false,
		'type' => 'text'));


?>