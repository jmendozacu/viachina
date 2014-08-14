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
class Magecon_ProductVideo_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_ENABLED = 'productvideo/general_settings/general_enabled';
    const XML_PATH_ENABLED_IMAGES = 'productvideo/general_settings/enabled';

    public function isEnabled() {
        return Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    public function isImagesEnabled() {
        return Mage::getStoreConfig(self::XML_PATH_ENABLED_IMAGES);
    }

}

?>