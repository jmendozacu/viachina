<?php
class OnTap_Merchandiser_Block_Adminhtml_Merchandiser extends Mage_Adminhtml_Block_Catalog
{
    public function __construct()
    {
        $session = Mage::getSingleton('core/session', array('name' => 'adminhtml'));
        $user = Mage::helper('adminhtml')->getCurrentUserId();
        $this->setUser($user)->setSession($session);
    }


    public function getStoreId()
    {
        return $this->getRequest()->getParam('store');
    }
}