<?php
class OnTap_Merchandiser_Model_Search extends Mage_CatalogSearch_Model_Advanced
{
    /**
     * Prepare product collection
     *
     * @param Mage_CatalogSearch_Model_Mysql4_Advanced_Collection | Enterprise_Search_Model_Resource_Collection $collection
     * @return Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setStore(Mage::app()->getStore())
            ->addStoreFilter();
        return $this;
    }


    /**
     * Retrieve array of attributes used in advanced search
     *
     * @return array
     */
    public function getAttributes()
    {
        /* @var $attributes Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection */
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $product = Mage::getModel('catalog/product');
            $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($product->getResource()->getTypeId())
                ->addHasOptionsFilter()
                ->setCodeFilter(array('name', 'sku', 'visibility'))
                ->setOrder('main_table.attribute_id', 'asc')
                ->load();
            foreach ($attributes as $attribute) {
                $attribute->setEntity($product->getResource());
            }
            $this->setData('attributes', $attributes);
        }
        return $attributes;
    }

    /**
     * Prepare search condition for attribute
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @return mixed
     */
    protected function _prepareCondition($attribute, $value)
    {
        $condition = false;

        if (is_array($value)) {
            if (!empty($value['from']) || !empty($value['to'])) { // range
                $condition = $value;
            } else if ($attribute->getBackendType() == 'varchar') { // multiselect
                $condition = array('in_set' => $value);
            } else if (!isset($value['from']) && !isset($value['to'])) { // select
                $condition = array('in' => $value);
            }
        } else {
            if (strlen($value) > 0) {
                if (in_array($attribute->getBackendType(), array('varchar', 'text', 'static'))) {
                    $condition = array('like' => '%' . $value . '%'); // text search
                } else {
                    $condition = $value;
                }
            }
        }

        return $condition;
    }

    /**
     * Add advanced search filters to product collection
     *
     * @param   array $values
     * @return  Mage_CatalogSearch_Model_Advanced
     */
    public function addFilters($values)
    {
        $attributes     = $this->getAttributes();
        $hasConditions  = false;
        $allConditions  = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (!isset($values[$attribute->getAttributeCode()])) {
                continue;
            }
            $value = $values[$attribute->getAttributeCode()];
            {
                $condition = $this->_prepareCondition($attribute, $value);
                if ($condition === false) {
                    continue;
                }
                $this->_addSearchCriteria($attribute, $value);

                $table = $attribute->getBackend()->getTable();
                if ($attribute->getBackendType() == 'static'){
                    $attributeId = $attribute->getAttributeCode();
                } else {
                    $attributeId = $attribute->getId();
                }
                $allConditions[$table][$attributeId] = $condition;
                if ('visibility' == $attribute->getAttributeCode() && 1 < count($allConditions)) {
                    $allConditions[$table][$attributeId]['useJoin'] = true;
                };
            }
        }

        if ($allConditions) {
            $this->addFieldsToFilter($allConditions);
        } else if (!$hasConditions) {
            Mage::throwException(Mage::helper('catalogsearch')->__('Please specify at least one search term.'));
        }

        return $this;
    }

    public function addFieldsToFilter($fields)
    {
        if ($fields) {
            $collection = $this->getProductCollection();
            $select = $collection->getSelect()
                ->distinct(true);
            $vCount =1;
            foreach ($fields as $table => $conditions) {
                if ('catalog_product_entity' == $table) {
                    foreach ($conditions as $attrId => $condValue) {
                        $relation = array_keys($condValue);
                        $relation = $relation[0];
                        $value = array_values($condValue);
                        $value = $value[0];
                        $select->orWhere("{$attrId} {$relation} ?", $value);
                    }
                } else {
                    $tableAlias = 'table'.$vCount++;
                    $conditionString = null;
                    foreach ($conditions as $attrId => $condValue) {
                        $useJoin = false;
                        if (isset($condValue['useJoin'])) {
                            $useJoin = $condValue['useJoin'];
                            unset($condValue['useJoin']);
                        }
                        $relation = array_keys($condValue);
                        $relation = $relation[0];
                        $value = array_values($condValue);
                        $value = $value[0];
                        if ($useJoin) {
                            if (is_array($value)) {
                                $value = "'".implode("', '", $value)."'";
                            }
                            $conditionString = " AND {$tableAlias}.attribute_id={$attrId} AND {$tableAlias}.value {$relation} ({$value})";
                        } else {
                            $select->orWhere("{$tableAlias}.attribute_id={$attrId} AND {$tableAlias}.value {$relation} (?)", $value);
                        }
                    }
                    $select->joinInner(array($tableAlias => $table), $tableAlias.'.entity_id = e.entity_id'.$conditionString, null);
                }
            }
        }
    }

    public function addCategoryFilter($catId)
    {
        if (is_int((int)$catId)) {
            $collection = $this->getProductCollection()
                ->setStore(Mage::app()->getStore())
                ->addStoreFilter();
            $select = $collection->getSelect()/*->distinct(true)*/;

            $catalogCategoryProduct = Mage::getSingleton('core/resource')->getTableName('catalog_category_product');
			
            $select->joinInner(array('cp' => $catalogCategoryProduct), 'cp.product_id = e.entity_id', null);
            $select->where("cp.category_id = ?", $catId);
            $select->order('cp.position');
        }
        return $this;
    }

    /**
     * Retrieve advanced search product collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Advanced_Collection
     */
    public function getProductCollection() {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getResourceModel('catalogsearch/advanced_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addStoreFilter()
                ->setOrder('cp.position');
            }

        return $this->_productCollection;
    }
}