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


class AW_Rma_Block_Customer_Backlink extends Mage_Core_Block_Template
{
    /**
     * Url for "back" link
     *
     * @var string
     */
    private $_backLink;

    /**
     * Evaluate and sets backlink
     *
     * @param string $backlink
     * @param bool   $parseid
     */
    public function setBackLink($backlink, $parseid = false)
    {
        if ($parseid) {
            $this->_backLink = $this->getUrl($backlink, array('id' => $this->getRequest()->getParam('id')));
        } else {
            $this->_backLink = $this->getUrl($backlink);
        }
        return $this->_backLink;
    }

    /**
     * Returns backlink url
     *
     * @return string
     */
    public function getBackLink()
    {
        return $this->_backLink;
    }
}