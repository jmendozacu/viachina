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
 */
class TIG_PostNL_Helper_Mijnpakket extends TIG_PostNL_Helper_Data
{
    /**
     * Xpath to 'mijnpakket_login_active' setting.
     */
    const XPATH_MIJNPAKKET_LOGIN_ACTIVE = 'postnl/delivery_options/mijnpakket_login_active';

    /**
     * Log filename to log all non-specific PostNL debug messages.
     */
    const POSTNL_DEBUG_LOG_FILE = 'TIG_PostNL_MijnPakket_Debug.log';

    /**
     * Check whether MijnPakket login is active.
     *
     * @return bool
     */
    public function isMijnpakketLoginActive()
    {
        $storeId = Mage::app()->getStore()->getId();

        $isActive = Mage::getStoreConfigFlag(self::XPATH_MIJNPAKKET_LOGIN_ACTIVE, $storeId);

        return $isActive;
    }

    /**
     * Checks whether MijnPakket login is currently available for use.
     *
     * @return boolean
     */
    public function canLoginWithMijnpakket()
    {
        /**
         * MijnPakket login is only available if delivery options are enabled.
         */
        if (!Mage::helper('postnl/deliveryOptions')->isDeliveryOptionsEnabled()) {
            return false;
        }

        if (!$this->isMijnpakketLoginActive()) {
            return false;
        }

        return true;
    }

    /**
     * Get initials based on a firstname.
     *
     * @param string $firstName
     *
     * @return string
     */
    public function getInitials($firstName)
    {
        $nameParts = preg_split("/\s+/", $firstName);

        $initials = '';
        foreach ($nameParts as $name) {
            $initials .= substr($name, 0, 1) . '.';
        }

        $initials = strtoupper($initials);
        return $initials;
    }
}