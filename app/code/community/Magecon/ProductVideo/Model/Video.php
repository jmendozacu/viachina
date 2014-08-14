<?php

/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_ProductVideo
 * @version    2.0.0
 * @copyright  Copyright (c) 2012 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
class Magecon_ProductVideo_Model_Video extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('productvideo/video');
    }

    public function loadByAttribute($attribute, $value) {
        $collection = $this->getResourceCollection()
                ->addAttributeToFilter($attribute, $value);

        foreach ($collection as $object) {
            return $object;
        }
        return false;
    }

}