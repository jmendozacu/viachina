<?php

/**
 * Madia Adyen Payment Module
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
 * @category	Madia
 * @package	Madia_Adyen
 * @copyright	Copyright (c) 2012 Madia (http://www.madia.nl)
 * @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Payment Gateway
 * @package    Madia_Adyen
 * @author     Omar,Muhsin <info@madia.nl>
 * @property   Madia B.V
 * @copyright  Copyright (c) 2012 Madia BV (http://www.madia.nl)
 */
class Madia_Adyen_Model_Adyen_Data_PaymentRequest3d extends Madia_Adyen_Model_Adyen_Data_Abstract {
    
	public $merchantAccount;
    public $browserInfo;
    public $md;
    public $paResponse;
    public $shopperIP;
    
    public function __construct() {
        $this->browserInfo = new Madia_Adyen_Model_Adyen_Data_BrowserInfo();
    }
	
    public function create(Varien_Object $payment, $amount, $order, $paymentMethod = null, $merchantAccount = null) {
    	$this->merchantAccount = $merchantAccount;
		
        $this->browserInfo->acceptHeader = $_SERVER['HTTP_ACCEPT'];
        $this->browserInfo->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->shopperIP = $_SERVER['REMOTE_ADDR'];
		
		$this->md = $payment->getAdditionalInformation('md');
		$this->paResponse = $payment->getAdditionalInformation('paResponse');
        return $this;
    }    
}