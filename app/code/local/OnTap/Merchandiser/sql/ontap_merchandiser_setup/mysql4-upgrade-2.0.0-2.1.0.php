<?php
$installer = $this;
$installer->startSetup();

//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);


$attribute  = array(
    'type' => 'text',
    'label'=> 'Sorting Options',
 //   'input' => 'select',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => true,
 //   'group' => "General",
    'source'   => ''
);
$installer->addAttribute('catalog_category', 'merchandiser_sorting_options', $attribute);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'merchandiser_sorting_options',
 '998'
);

$attributeId = $installer->getAttributeId($entityTypeId, 'merchandise_option');
$installer->run("
INSERT INTO `{$installer->getTable('catalog_category_entity_int')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '1'
        FROM `{$installer->getTable('catalog_category_entity')}`;
");


$installer->endSetup();