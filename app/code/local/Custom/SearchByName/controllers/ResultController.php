<?php
  class Custom_SearchByName_ResultController extends Mage_Core_Controller_Front_Action
  {
      public function indexAction()
      {
            $query = Mage::helper('searchbyname')->getQuery();

            if($query->getQueryText() != ''){
                $query = Mage::helper('searchbyname')->setAttributes();
            }

            $query->setStoreId(Mage::app()->getStore('brands_store')->getId());

            if ($query->getQueryText() != '') {
                if (Mage::helper('searchbyname')->isMinQueryLength()) {
                    $query->setId(0)
                        ->setIsActive(1)
                        ->setIsProcessed(1);
                }
                else {
                    if ($query->getId()) {
                        $query->setPopularity($query->getPopularity()+1);
                    }
                    else {
                        $query->setPopularity(1);
                    }

                    if ($query->getRedirect()){
                        $query->save();
                        $this->getResponse()->setRedirect($query->getRedirect());
                        return;
                    }
                    else {
                        $query->prepare();
                    }
                }

                Mage::helper('searchbyname')->checkNotes();

                $this->loadLayout();
                $this->_initLayoutMessages('catalog/session');
                $this->_initLayoutMessages('checkout/session');
                $this->renderLayout();

                if (!Mage::helper('searchbyname')->isMinQueryLength()) {
                    $query->save();
                }
            }
            else {
                $this->_redirectReferer();
            }
      }
  }
?>
