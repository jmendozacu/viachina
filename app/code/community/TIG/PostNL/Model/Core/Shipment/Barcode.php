<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2014 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 *
 * Class TIG_PostNL_Model_Core_Shipment_Barcode@method getBarcode
 *
 * @method int getBarcodeNumber()
 * @method TIG_PostNL_Model_Core_Shipment_Barcode setBarcodeNumber(int $value)
 * @method int getBarcodeId()
 * @method TIG_PostNL_Model_Core_Shipment_Barcode setBarcodeId(int $value)
 * @method TIG_PostNL_Model_Core_Shipment_Barcode setBarcode(string $value)
 * @method int getParentId()
 * @method TIG_PostNL_Model_Core_Shipment_Barcode setParentId(int $value)
 */
class TIG_PostNL_Model_Core_Shipment_Barcode extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'postnl_shipment_barcode';

    public function _construct()
    {
        $this->_init('postnl_core/shipment_barcode');
    }

    /**
     * Load a barcode object based on a postnl shipment Id and a barcode number
     *
     * @param $parentId
     * @param $barcodeNumber
     *
     * @return TIG_PostNL_Model_Core_Shipment_Barcode
     */
    public function loadByParentAndBarcodeNumber($parentId, $barcodeNumber)
    {
        /**
         * @var TIG_PostNL_Model_Core_Resource_Shipment_Barcode_Collection $collection
         */
        $collection = $this->getCollection();
        $collection->addFieldToSelect('*')
                   ->addFieldToFilter('parent_id', array('eq' => $parentId))
                   ->addFieldToFilter('barcode_number', array('eq' => $barcodeNumber));

        if ($collection->getSize()) {
            $barcode = $collection->getFirstItem();

            $this->setData($barcode->getData());
            $this->setOrigData();
            $this->_afterLoad();
        }

        return $this;
    }
}