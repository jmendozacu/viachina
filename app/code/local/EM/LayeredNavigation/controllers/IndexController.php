<?php
class EM_LayeredNavigation_IndexController extends Mage_Core_Controller_Front_Action
{
	public function viewAction() {
		$categoryId = Mage::app()->getRequest()->getParam('id');
		
		$currLayout = Mage::app()->getRequest()->getParam('curr');
		
		$category   = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($categoryId);
		if (!$category->getId())
			return;
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			Mage::register('current_category', $category);
			$this->loadLayout('default');
			$this->getLayout()->getBlock('root')->setTemplate($currLayout);
			
			$result             = array();
			$result['robots']	= $this->getLayout()->getBlock('head')->getRobots();
			$result['layer']    = $this->getLayout()->getBlock('em.catalog.leftnav')->toHtml();
			$result['products'] = $this->getLayout()->getBlock('product_list')->toHtml();
			$this->getResponse()->setHeader('Content-type', 'application/json');			
			$this->getResponse()->setBody(json_encode($result));
			
		} else {
			$this->_redirectReferer();
		}
		
	}
	
	public function searchAction() {
//		if ($_SERVER['REQUEST_METHOD'] != 'POST')
//			Mage::app()->getFrontController()->getResponse()->setRedirect(
//				Mage::getBaseUrl()
//				//Mage::getUrl('catalogsearch/result/', array( '_current' => true))
//			);
		
		$query = Mage::helper('catalogsearch')->getQuery();
		/* @var $query Mage_CatalogSearch_Model_Query */
		
		$query->setStoreId(Mage::app()->getStore()->getId());
		
		$currLayout = Mage::app()->getRequest()->getParam('curr');
			
		if ($query->getQueryText() != '') {
			if (Mage::helper('catalogsearch')->isMinQueryLength()) {
				$query->setId(0)->setIsActive(1)->setIsProcessed(1);
			} else {
				if ($query->getId()) {
					$query->setPopularity($query->getPopularity() + 1);
				} else {
					$query->setPopularity(1);
				}
				
				if ($query->getRedirect()) {
					$query->save();
					$this->getResponse()->setRedirect($query->getRedirect());
					return;
				} else {
					$query->prepare();
				}
			}
			
			Mage::helper('catalogsearch')->checkNotes();
			
			$this->loadLayout();
			$this->getLayout()->getBlock('root')->setTemplate($currLayout);
			
			$result             = array();
			$result['layer']    = $this->getLayout()->getBlock('em.catalog.leftnav')->toHtml();
			$result['products'] = $this->getLayout()->getBlock('product_list')->toHtml();
			$this->getResponse()->setHeader('Content-type', 'application/json');
			$this->getResponse()->setBody(json_encode($result));
			
			if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
				$query->save();
			}
		
			
		} else {
			$this->_redirectReferer();
		}
	}

}