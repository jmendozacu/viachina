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
class AdjustWare_Nav_Block_Catalog_Layer_Params extends AdjustWare_Nav_Block_Catalog_Layer_View
{
    public function getStateInfo()
    {
       return parent::getStateInfo();
    }

    protected function _prepareLayout()
    {
        return $this;
    }
	
	protected function _beforeToHtml()
    {
        return $this;
    }
}