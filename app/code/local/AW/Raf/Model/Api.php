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

class AW_Raf_Model_Api extends Mage_Core_Model_Abstract
{
    /**
     * Add transaction to customer
     * @param Varien_Object $obj
     * @throws Exception
     * @return obj AW_Raf_Model_Rule_Action_Transaction
     */
    public function add($obj)
    {
        if (is_array($obj)) {
            $obj = new Varien_Object($obj);
        }
        if (!$obj instanceof Varien_Object) {
            throw new Exception('Invalid param must be an array or instance of varien object');
        }

        $actionTypeInstance = Mage::getModel('awraf/source_actionType')->getActionInstanceByTypeValue($obj->getType());

        if (null !== $actionTypeInstance) {
            $actionTypeModel = Mage::getModel($actionTypeInstance);
        }

        if (!isset($actionTypeModel) || !($actionTypeModel instanceof Mage_Core_Model_Abstract)) {
            return false;
        }

        if ($obj->getWebsiteId() && $obj->getCustomerId() && $_discount = (int)$obj->getDiscount()) {
            $availableAmount = $this->getAvailableAmount(
                $obj->getCustomerId(),
                $obj->getWebsiteId()
            );

            if ($_discount < 0 && abs($_discount) > $availableAmount) {
                $obj->setDiscount($availableAmount * -1);
            }
        }
        return $actionTypeModel->createFromObject($obj);
    }

    public function saveInTransaction(array $objects)
    {
        $transaction = Mage::getModel('core/resource_transaction');
        foreach ($objects as $obj) {
            if (!$obj instanceof Mage_Core_Model_Abstract) {
                continue;
            }
            $transaction->addObject($obj);
        }
        return $transaction->save();
    }

    public function getReferral($customer, $website)
    {
        $refferal = Mage::getModel('awraf/referral')
            ->getReferral(
                new Varien_Object(
                    array(
                         'customer_id' => $customer,
                         'website_id'  => $website
                    )
                )
            )
        ;

        return $refferal;
    }

    public function getReferralByEmail($email, $website)
    {
        $referral = Mage::getModel('awraf/referral')
            ->getReferral(
                new Varien_Object(
                    array(
                         'email'      => $email,
                         'website_id' => $website
                    )
                )
            )
        ;

        return $referral;
    }

    public function createReferral($obj)
    {
        if (is_array($obj)) {
            $obj = new Varien_Object($obj);
        }
        if (!$obj instanceof Varien_Object) {
            throw new Exception('Invalid param must be an array or instance of varien object');
        }
        return Mage::getModel('awraf/referral')->create($obj);
    }

    public function getAvailableAmount($customer, $website)
    {
        $collection = Mage::getModel('awraf/rule_action_transaction')->getCollection();
        $collection
            ->addFieldToFilter('customer_id', $customer)
            ->addFieldToFilter('website_id', $website)
            ->addExpressionFieldToSelect('discount', 'IF( SUM({{discount}}) < 0, 0 , SUM({{discount}}))', 'discount')
        ;

        return $collection->getFirstItem()->getDiscount();
    }

    public function getAvailableDiscount($customer, $website)
    {
        $collection = Mage::getModel('awraf/rule_action_discount')->getCollection();
        $collection
            ->addFieldToFilter('customer_id', $customer)
            ->addFieldToFilter('website_id', $website)
            ->setOrder('discount')
        ;

        return $collection->getFirstItem();
    }
}