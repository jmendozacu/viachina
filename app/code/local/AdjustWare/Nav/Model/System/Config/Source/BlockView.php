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
class AdjustWare_Nav_Model_System_Config_Source_BlockView extends Varien_Object
{
    public function toOptionArray()
    {
        $options = array();

        $options[] = array(
            'value'=> 'sidebar',
            'label' => Mage::helper('adjnav')->__('Sidebar')
        );
        $options[] = array(
            'value'=> 'top',
            'label' => Mage::helper('adjnav')->__('Top')
        );

        return $options;
    }
}