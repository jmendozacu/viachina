<?php

class AnattaDesign_AwesomeCheckout_Block_Onepage_Progress extends Mage_Checkout_Block_Onepage_Progress {

	public function getActive() {
		$active = $this->getRequest()->getParam( 'section', 'shipping' );
		return $active;
	}

	public function getShippingAddressHtml() {
		$address = $this->getShipping();
		$data = array(
			Mage::helper( 'anattadesign_awesomecheckout' )->getFullname( $address ),
			$address->getStreetFull(),
			$address->getCity() . ', ' . $address->getCountryModel()->getIso3Code() . ' ' . $address->getPostcode()
		);

		return join( '<br/>', $data );
	}

}