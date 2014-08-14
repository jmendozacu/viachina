<?php


class OnTap_Merchandiser_Model_Catalogpermissions extends Enterprise_CatalogPermissions_Model_Resource_Permission_Index {

    public function setCollectionLimitationCondition($collection)
    {
		if (Mage::helper('merchandiser')->addSmartCategory($collection)):
			$collection->setFlag('disable_root_category_filter', false);
		else:
			$collection->setFlag('disable_root_category_filter', true);
		endif;
        
        return $this;
    }

    

}