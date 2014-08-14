<?php
$installer = $this;
$installer->startSetup();

//$installer->installEntities();
//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');


// Set up database tables if required

$installer->run("

DROP TABLE IF EXISTS {$installer->getTable('catalog_category_smart_product_index')};
CREATE TABLE {$installer->getTable('catalog_category_smart_product_index')} (
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Category ID',
  `product_ids` text NOT NULL DEFAULT '' COMMENT 'Product IDs',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  PRIMARY KEY (`category_id`,`store_id`),
  CONSTRAINT `FK_CAT_CAT_DYN_PRODUCT_INDEX_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CAT_CTGR_DYN_PRD_IDX_CTGR_ID_CAT_CTGR_ENTT_ENTT_ID` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Smart Merchandiser Index';

DROP TABLE IF EXISTS {$installer->getTable('catalog_category_smart_product_indexer_idx')};
CREATE TABLE {$installer->getTable('catalog_category_smart_product_indexer_idx')} (
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Category ID',
  `product_ids` text NOT NULL DEFAULT '' COMMENT 'Product IDs',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  PRIMARY KEY (`category_id`,`store_id`),
  CONSTRAINT `FK_CAT_CAT_DYN_PRODUCT_INDEX_STORE_ID_CORE_STORE_STORE_ID_IDX` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CAT_CTGR_DYN_PRD_IDX_CTGR_ID_CAT_CTGR_ENTT_ENTT_ID_IDX` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Smart Merchandiser Index Idx';

");

$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);


$attribute  = array(
    'type' => 'int',
    'label'=> 'Merchandise This Category',
    'input' => 'select',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => true,
  //  'group' => "General",
    'source'	=> 'merchandiser/entity_attribute_source_merchandiseoption'
);
$installer->addAttribute('catalog_category', 'merchandise_option', $attribute);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'merchandise_option',
 '999'
);


$attribute  = array(
    'type' => 'text',
    'label'=> 'smart_attributes',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'source'   => ''
);
$installer->addAttribute('catalog_category', 'smart_attributes', $attribute);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'smart_attributes',
 '997'
);


$installer->endSetup();
