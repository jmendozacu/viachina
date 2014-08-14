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
 * @category   Mymonki
 * @package    Mymonki_Ship2pay
 * @copyright  Copyright (c) 2010 Freshmind Sp. z o.o. (pawel@freshmind.pl)
 */

class Mymonki_Ship2pay_Block_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{
	/**
     * Retrieve availale payment methods
     *
     * @return array
     */
    public function getMethods()
    {
    	
    	//Get ship2pay config
    	$ship2pay = @unserialize(Mage::getStoreConfig('shipping/ship2pay/ship2pay'));
    	$ship2pay_is_enabled = Mage::getStoreConfig('shipping/ship2pay/active');
    	
    	if(!$ship2pay_is_enabled)
    		return parent::getMethods();
    	
    	if(!is_array($ship2pay))
    		return false;
    		
    	if(!array_key_exists('shipping_method', $ship2pay) || !array_key_exists('payment_method', $ship2pay))
    		return false;
    	
    	//Mage::log(var_export($ship2pay, true));
    	
    	//Get all payment methods
    	$methods = parent::getMethods();
    	$quote = $this->getQuote();
    	$total = $quote->getGrandTotal();
    	//Mage::log(var_export($methods, true));
    	
    	//Get selected shipping method
    	$shippingMethod = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod();
    	
    	//Mage::log(var_export($shippingMethod, true));
    	
    	if(!$shippingMethod)
    	{
    		if($total != 0)
    			return array();
    		else
    			$shippingMethod = "free"; //Grand total eq 0 - set shipping method to dump "free"
    	}
    		
    	$shippingMethodFlat = explode('_', $shippingMethod);

    	//Ship2Pay avaiable methods
    	$avaiable = array();
    	foreach($ship2pay['shipping_method'] as $key => $smethod)
    	{
    		if($smethod == $shippingMethodFlat[0])
    			$avaiable[$ship2pay['payment_method'][$key]] = true;
    	}

        foreach($ship2pay['shipping_method'] as $key => $smethod)
        {
            if($smethod == $shippingMethod)
                $avaiable[$ship2pay['payment_method'][$key]] = true;
        }
    	
    	//Mage::log(var_export($avaiable, true));
   
        //Remove blocked methods
        foreach ($methods as $key => $method) {
        	$code = $method->getCode();
        	if(!array_key_exists($code, $avaiable) && ($total != 0 && $code != "free"))
        		unset($methods[$key]);
        }
        
        return $methods;
    }
}