<?php
$installer = $this;
$installer->startSetup();

//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);


$attribute  = array(
    'type' => 'text',
    'label'=> 'Hero Products',
 //   'input' => 'select',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
   // 'default' => true,
 //   'group' => "General",
    'source'   => ''
);
$installer->addAttribute('catalog_category', 'merchandiser_heroproducts', $attribute);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'merchandiser_heroproducts',
 '988'
);




$installer->endSetup();