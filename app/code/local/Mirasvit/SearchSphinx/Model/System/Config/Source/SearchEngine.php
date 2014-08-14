<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.3.1
 * @revision  598
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


/**
 * @category Mirasvit
 * @package  Mirasvit_SearchSphinx
 */
class Mirasvit_SearchSphinx_Model_System_Config_Source_SearchEngine
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => Mirasvit_SearchSphinx_Model_Config::ENGINE_FULLTEXT,
                'label' => Mage::helper('searchsphinx')->__('Built-in search engine')
            )
        );

        $options[] = array(
            'value' => Mirasvit_SearchSphinx_Model_Config::ENGINE_SPHINX,
            'label' => Mage::helper('searchsphinx')->__('External Search Engine')
        );
        
        $options[] = array(
            'value' => Mirasvit_SearchSphinx_Model_Config::ENGINE_SPHINX_EXTERNAL,
            'label' => Mage::helper('searchsphinx')->__('External Search Engine (another server)')
        );

        return $options;
    }
}
