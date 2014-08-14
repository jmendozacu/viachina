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
 * @package    AW_Raf
 * @version    2.1.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Raf_Helper_Order extends Mage_Core_Helper_Abstract
{
    public function getAppliedRafAmounts($order)
    {
        $quoteAddressId = null;
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $quoteAddressId = Mage::getModel('sales/quote_address_item')
                ->load($item->getQuoteItemId())->getQuoteAddressId()
            ;
            if (!$quoteAddressId) {
                $appliedAmount = Mage::helper('awraf')->getAppliedAmount();
                $appliedDiscount = Mage::helper('awraf')->getAppliedDiscount();

            } else {
                $appliedAmount = Mage::helper('awraf')->getDiscountByType('amount', $quoteAddressId);
                $appliedDiscount = Mage::helper('awraf')->getDiscountByType('discount', $quoteAddressId);
            }
            break;
        }

        return array(
            'amount' => $appliedAmount,
            'discount' => $appliedDiscount,
            'quote_address' => $quoteAddressId
        );
    }

    public function getReferralInfo($order, $orderInfo)
    { 
        $customerId = $order->getCustomerId();
        $websiteId = $order->getStore()->getWebsite()->getId();

        $selfCustomer = false;
        $discountObject = false;
        if ($customerId) {
            $ref = Mage::getSingleton('awraf/api')->getReferral($customerId, $websiteId);
            $discountObject = Mage::getSingleton('awraf/api')->getAvailableDiscount($customerId, $websiteId);
            $orderInfo->setAppliedDiscountInfo($discountObject->toArray());
            if (!$ref->getId()) {
                $selfCustomer = true;
                $customerId = null;
            }
        } else {
            $ref = Mage::getModel('awraf/referral')->load(Mage::helper('awraf')->getReferral());
            $customerId = $ref->getReferrer();
        }
        if (!$customerId && !$selfCustomer) {
            $customerId = Mage::helper('awraf')->getReferrer();
        }
        
        return array(
            'customer' => $customerId,
            'referral' => $ref,
            'discount_obj' => $discountObject
        );       
    }
}