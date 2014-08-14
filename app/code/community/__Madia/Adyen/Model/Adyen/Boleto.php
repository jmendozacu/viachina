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
class Madia_Adyen_Model_Adyen_Boleto extends Madia_Adyen_Model_Adyen_Abstract {

    protected $_code = 'adyen_boleto';
    protected $_formBlockType = 'adyen/form_boleto';
    protected $_infoBlockType = 'adyen/info_boleto';
    protected $_paymentMethod = 'boleto';

	/**
     * 1)Called everytime the adyen_boleto is called or used in checkout
     * @descrition Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data) {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $boleto = array(
            'firstname' => $data->getFirstname(),
            'lastname' => $data->getLastname(),
            'social_security_number' => $data->getSocialSecurityNumber(),
        	'selected_brand' => $data->getBoletoType()
        );

        $info = $this->getInfoInstance();
        $info->setPoNumber(serialize($boleto));
        $info->setCcType($data->getBoletoType());

        return $this;
    }

    /**
     * Called just after asssign data
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function prepareSave() {
        //@todo encryption or so
        parent::prepareSave();
    }

}
