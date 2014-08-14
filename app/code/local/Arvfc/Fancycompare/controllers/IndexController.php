<?php
class Arvfc_Fancycompare_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

    /**
     * Show compare list
     */
    public function showAction()
    {
		echo $this->getLayout()->createBlock('fancycompare/product_compare_list')->setTemplate('fancycompare/fclist.phtml')->toHtml();
	}

    /**
     * Add item to compare list
     */
    public function addtocompareAction()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId
            && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())
        ) {
			$_items = Mage::helper('catalog/product_compare')->getItemCollection();
			foreach($_items as $_index => $_item) {
				if ($_item->getId() == $productId) {
					echo json_encode(array('status'=>'success'));
					return;
				}
			}
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
            }

            Mage::helper('catalog/product_compare')->calculate();
			$data = $this->getproductFCData($product);
			echo json_encode(array('status'=>'success', 'pData'=>$data, 'pid'=>$productId));
			return;
        } else {
			$result = array('status' => 'error',
							 'message' =>  'An error occurred while adding item to compare.',);
			echo json_encode($result);
			exit;
		}
    }

	public function getproductFCData($_product)
	{
		$str = '
		<div class="thumb_holder">
			<img class="thumbnail" src="'.$_product->getThumbnailUrl().'">
		</div>
		<p class="description">
		<a href="'.$_product->getProductUrl().'" class="product-name">
		'.$_product->getName().'</a></p>
		<a href="javascript:;" class="fc_remove"><span class="delete" title="Remove"></span></a>
		';
		return $str;
	}

    /**
     * Remove item from compare list
     */
    public function removeAction()
    {
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if($product->getId()) {
                /** @var $item Mage_Catalog_Model_Product_Compare_Item */
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();
                    /*Mage::getSingleton('catalog/session')->addSuccess(
                        $this->__('The product %s has been removed from comparison list.', $product->getName())
                    );*/
                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                    Mage::helper('catalog/product_compare')->calculate();

					$_cnt = Mage::helper('catalog/product_compare')->getItemCount();
					echo json_encode(array('status'=>'success', 'fc_count'=>$_cnt));
					return;
                }
            }
        }
    }

    /**
     * Remove all items from comparison list
     */
    public function clearAction()
    {
		$result = array();
        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        try {
            $items->clear();
            Mage::helper('catalog/product_compare')->calculate();
        } catch (Mage_Core_Exception $e) {
			$result['status'] = 'error';
			$result['message'] =  'An error occurred while clearing comparison list.';
			echo json_encode($result);
			return;
        } catch (Exception $e) {
			$result['status'] = 'error';
			$result['message'] =  'An error occurred while clearing comparison list.';
			echo json_encode($result);
			return;
        }
		echo json_encode(array('status'=>'success'));
		return;
    }

    /**
     * Add items to cart
     */
	public function addtocartAction()
	{
		try{
			$result = array();
			$productId = (int) $this->getRequest()->getParam('product');
			if (!$productId) {
				$result['status'] = 'error';
				$result['message'] =  'Invalid product.';
				echo json_encode($result);
				exit;
			}
			$qty = '1';
		 
			$product = Mage::getSingleton('catalog/product')->load($productId);
			$session = Mage::getSingleton('core/session', array('name'=>'frontend'));
			$cart = Mage::helper('checkout/cart')->getCart();
			$cart->addProduct($product, $qty);
		 
			$session->setLastAddedProductId($product->getId());
			$session->setCartWasUpdated(true);
			$cart->save();
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

			$result['status']="success";
			$result['message']="Added!";
			echo json_encode($result);
		} catch (Exception $e) {
			$result['status'] = 'error';
			$result['message'] =  $e->getMessage();
			echo json_encode($result);
		}
	}

    /**
     * Add items to wishlist
     */
	public function addtowishlistAction()
	{
		if (!Mage::helper('customer')->isLoggedIn()) {
			$result = array('status' => 'error',
							 'message' =>  'You are not logged in.');
			echo json_encode($result);
			exit;
		}
		$wishlist = Mage::helper('wishlist')->getWishlist();
        if (!$wishlist) {
			$result = array('status' => 'error',
							 'message' =>  'An error occurred while adding item to wishlist.');
			echo json_encode($result);
			exit;
        }

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
			$result = array('status' => 'error',
							 'message' =>  'An error occurred while adding item to wishlist.');
			echo json_encode($result);
			exit;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
			$result = array('status' => 'error',
							 'message' =>  'An error occurred while adding item to wishlist.');
			echo json_encode($result);
			exit;
        }

        try {
            $result = $wishlist->addNewItem($product);
            if (is_string($result)) {
				$result = array('status' => 'error',
							 'message' =>  'An error occurred while adding item to wishlist.');
				echo json_encode($result);
				exit;
            }
            $wishlist->save();
            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist'  => $wishlist,
                    'product'   => $product,
                    'item'      => $result
                )
            );
            Mage::helper('wishlist')->calculate();

			$result = array('status' => 'success',
							 'message' =>  'Added to wishlist.');
			echo json_encode($result);
			exit;
        }
        catch (Mage_Core_Exception $e) {
			$result = array('status' => 'error',
							 'message' =>  'An error occurred while adding item to wishlist.');
			echo json_encode($result);
			exit;
        }
        catch (Exception $e) {
			$result = array('status' => 'error',
							 'message' =>  'An error occurred while adding item to wishlist.');
			echo json_encode($result);
			exit;
        }
	}
}