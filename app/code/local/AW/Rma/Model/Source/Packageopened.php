<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.4.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Rma_Model_Source_Packageopened
{
    const YES = 1;
    const NO = 0;

    const LABEL_YES = 'Yes';
    const LABEL_NO = 'No';

    public static function toOptionArray()
    {
        return array(
            array('value' => self::YES, 'label' => Mage::helper('awrma')->__(self::LABEL_YES)),
            array('value' => self::NO, 'label' => Mage::helper('awrma')->__(self::LABEL_NO))
        );
    }

    public static function getDefaultValue()
    {
        return self::YES;
    }

    /**
     * Returns array(value => ..., label => ...) for option with given value
     *
     * @param string $value
     *
     * @return array
     */
    public static function getOption($value)
    {
        $_options = self::toOptionArray();

        foreach ($_options as $_option) {
            if ($_option['value'] == $value) {
                return $_option;
            }
        }

        return false;
    }

    /**
     * Get label for option with given value
     *
     * @param string $value
     *
     * @return string
     */
    public static function getOptionLabel($value)
    {
        $_option = self::getOption($value);
        if (!$_option) {
            return false;
        }
        return $_option['label'];
    }
}