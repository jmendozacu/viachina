<?php

class Webguys_CustomerNavigation_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation {

	public function getLinks(){

    	$pre_links 	  = $this->_links;
    	$tmp_links    = array();
		$this->_links = array();
        $startcnt     = 100000;

        /*
         * remove not(!) magento customer account links
         */
        foreach ($pre_links as $_link) {
            $tmpName = $this->_getFormattedName( $_link->getName() );

            if( is_null( Mage::getStoreConfig( 'customernavigation/settings/show_' . $tmpName ) ) ){
                $tmp_links[($startcnt++)] = $_link;
            }
        }

        /*
         * add allowed account links
         */
		foreach ($pre_links as $_link) {
            $tmpName = $this->_getFormattedName( $_link->getName() );

			if( Mage::getStoreConfig( 'customernavigation/settings/show_' . $tmpName ) ) {
				$tmp_links[ Mage::getStoreConfig( 'customernavigation/reorder/position_' . $tmpName ) ] = $_link;
			}
		}

        /*
         * resort and add the link
         */
		ksort( $tmp_links );
		foreach ($tmp_links as $key=>$_link) {
            $tmpName = $this->_getFormattedName( $_link->getName() );

            $this->addLink($tmpName, $_link->getPath(), $_link->getLabel());
		}
        return $this->_links;
    }

    protected function _getFormattedName( $name )
    {
        return strtolower( str_replace(' ', '_', $name) );
    }
}