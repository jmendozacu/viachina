<?php
/**
 * Layered Navigation Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Nav
 * @version      2.5.1
 * @license:     3aooztWVyQyLq3pTjVKXjSrcsuymFTgXHjf7iclwH0
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Nav_Model_System_Config_Source_Category extends Varien_Object
{
    public function toOptionArray()
    {
        $options = array();
        
        $options[] = array(
                'value'=> 'breadcrumbs',
                'label' => Mage::helper('adjnav')->__('Display')
        );
        $options[] = array(
                'value'=> 'none',
                'label' => Mage::helper('adjnav')->__('None')
        );
        
        return $options;
    }
}