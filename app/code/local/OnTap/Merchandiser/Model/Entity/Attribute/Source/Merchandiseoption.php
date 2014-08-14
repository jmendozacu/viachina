<?php
class OnTap_Merchandiser_Model_Entity_Attribute_Source_Merchandiseoption extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('eav')->__('Visual Merchandiser'),
                    'value' =>  1
                ),
                array(
                    'label' => Mage::helper('eav')->__('Smart Merchandiser'),
                    'value' =>  2
                ),
            );
        }
        return $this->_options;
    }
}