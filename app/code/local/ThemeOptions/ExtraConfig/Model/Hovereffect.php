<?php class ThemeOptions_ExtraConfig_Model_Hovereffect
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'top', 'label'=>Mage::helper('ExtraConfig')->__('Slide Top')),
            
            array('value'=>'bottom', 'label'=>Mage::helper('ExtraConfig')->__('Slide Bottom')) 
        );
    }

}?>