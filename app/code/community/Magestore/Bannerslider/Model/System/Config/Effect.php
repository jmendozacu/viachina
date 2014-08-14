<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class Magestore_Bannerslider_Model_System_Config_Effect
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'random', 'label'=>Mage::helper('bannerslider')->__('random')),
            array('value' => 'simpleFade', 'label'=>Mage::helper('bannerslider')->__('simpleFade')),
            array('value' => 'curtainTopLeft', 'label'=>Mage::helper('bannerslider')->__('curtainTopLeft')),
            array('value' => 'curtainTopRight', 'label'=>Mage::helper('bannerslider')->__('curtainTopRight')),
            array('value' => 'curtainBottomLeft', 'label'=>Mage::helper('bannerslider')->__('curtainBottomLeft')),
            array('value' => 'curtainBottomRight', 'label'=>Mage::helper('bannerslider')->__('curtainBottomRight')),
            array('value' => 'curtainSliceLeft', 'label'=>Mage::helper('bannerslider')->__('curtainSliceLeft')),
            array('value' => 'curtainSliceRight', 'label'=>Mage::helper('bannerslider')->__('curtainSliceRight')),
            array('value' => 'blindCurtainTopLeft', 'label'=>Mage::helper('bannerslider')->__('blindCurtainTopLeft')),
            array('value' => 'blindCurtainTopRight', 'label'=>Mage::helper('bannerslider')->__('blindCurtainTopRight')),
            array('value' => 'blindCurtainBottomLeft', 'label'=>Mage::helper('bannerslider')->__('blindCurtainBottomLeft')),
            array('value' => 'blindCurtainBottomRight', 'label'=>Mage::helper('bannerslider')->__('blindCurtainBottomRight')),
            array('value' => 'blindCurtainSliceBottom', 'label'=>Mage::helper('bannerslider')->__('blindCurtainSliceBottom')),
            array('value' => 'blindCurtainSliceTop', 'label'=>Mage::helper('bannerslider')->__('blindCurtainSliceTop')),
            array('value' => 'stampede', 'label'=>Mage::helper('bannerslider')->__('stampede')),
            array('value' => 'mosaic', 'label'=>Mage::helper('bannerslider')->__('mosaic')),
            array('value' => 'mosaicReverse', 'label'=>Mage::helper('bannerslider')->__('mosaicReverse')),
            array('value' => 'mosaicRandom', 'label'=>Mage::helper('bannerslider')->__('mosaicRandom')),
            array('value' => 'mosaicSpiral', 'label'=>Mage::helper('bannerslider')->__('mosaicSpiral')),
            array('value' => 'mosaicSpiralReverse', 'label'=>Mage::helper('bannerslider')->__('mosaicSpiralReverse')),
            array('value' => 'topLeftBottomRight', 'label'=>Mage::helper('bannerslider')->__('topLeftBottomRight')),
            array('value' => 'bottomRightTopLeft', 'label'=>Mage::helper('bannerslider')->__('bottomRightTopLeft')),
            array('value' => 'bottomLeftTopRight', 'label'=>Mage::helper('bannerslider')->__('bottomLeftTopRight')),
            array('value' => 'bottomLeftTopRight', 'label'=>Mage::helper('bannerslider')->__('bottomLeftTopRight')),
            array('value' => 'scrollLeft', 'label'=>Mage::helper('bannerslider')->__('scrollLeft')),
            array('value' => 'scrollRight', 'label'=>Mage::helper('bannerslider')->__('scrollRight')),
            array('value' => 'scrollHorz', 'label'=>Mage::helper('bannerslider')->__('scrollHorz')),
            array('value' => 'scrollBottom', 'label'=>Mage::helper('bannerslider')->__('scrollBottom')),
            array('value' => 'scrollTop', 'label'=>Mage::helper('bannerslider')->__('scrollTop')),
        );
    }
 
}
