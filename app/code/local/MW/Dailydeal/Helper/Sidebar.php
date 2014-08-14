<?php

class MW_Dailydeal_Helper_Sidebar extends Mage_Core_Helper_Abstract
{

    public function displayTodaydealLeft()
    {
        if (!MW_Dailydeal_Helper_Toolasiaconnect::getInstance()->isModuleOutputEnabled()) {
            return;
        }
        if (Mage::getStoreConfig('dailydeal/general/sidebardeal') == 1)
            return "mw_dailydeal/sidebar/todaydeal.phtml";
    }

    public function displayActivedealLeft()
    {
        if (!MW_Dailydeal_Helper_Toolasiaconnect::getInstance()->isModuleOutputEnabled()) {
            return;
        }
        if (Mage::getStoreConfig('dailydeal/general/sidebaractive') == 1)
            return "mw_dailydeal/sidebar/activedeal.phtml";
    }

    public function displayCalendarLeft()
    {
        if (!MW_Dailydeal_Helper_Toolasiaconnect::getInstance()->isModuleOutputEnabled()) {
            return;
        }
        if (Mage::getStoreConfig('dailydeal/general/calendar') == 1) {
            return "mw_dailydeal/sidebar/calendar.phtml";
        }
    }

    public function displayTodaydealRight()
    {
        if (!MW_Dailydeal_Helper_Toolasiaconnect::getInstance()->isModuleOutputEnabled()) {
            return;
        }
        if (Mage::getStoreConfig('dailydeal/general/sidebardeal', Mage::app()->getStore()->getStoreId()) == 2)
            return "mw_dailydeal/sidebar/todaydeal.phtml";
    }

    public function displayActivedealRight()
    {
        if (!MW_Dailydeal_Helper_Toolasiaconnect::getInstance()->isModuleOutputEnabled()) {
            return;
        }
        if (Mage::getStoreConfig('dailydeal/general/sidebaractive', Mage::app()->getStore()->getStoreId()) == 2)
            return "mw_dailydeal/sidebar/activedeal.phtml";
    }

    public function displayCalendarRight()
    {
        if (!MW_Dailydeal_Helper_Toolasiaconnect::getInstance()->isModuleOutputEnabled()) {
            return;
        }
        if (Mage::getStoreConfig('dailydeal/general/calendar', Mage::app()->getStore()->getStoreId()) == 2)
            return "mw_dailydeal/sidebar/calendar.phtml";
    }

    /**
     * Simple Products
     */
    public function getTemplateViewProduct()
    {
        if (!MW_Dailydeal_Helper_Toolasiaconnect::getInstance()->isModuleOutputEnabled()) {
            return;
        }
        return "mw_dailydeal/catalog/product/view/type/default.phtml";
    }
    
}
