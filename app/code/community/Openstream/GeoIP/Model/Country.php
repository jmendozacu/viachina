<?php

class Openstream_GeoIP_Model_Country extends Openstream_GeoIP_Model_Abstract
{
    private $country;
    private $allowed_countries = array();

    public function __construct()
    {
        parent::__construct();

        /** @var $wrapper Openstream_GeoIP_Model_Wrapper */
//			var_dump  ( $this->get_client_ip());
//		die();
        $wrapper = Mage::getModel('geoip/wrapper');
        if ($wrapper->geoip_open($this->local_file, 0)) {
            //$this->country = $wrapper->geoip_country_code_by_addr($_SERVER['REMOTE_ADDR']);

			$this->country = $wrapper->geoip_country_code_by_addr($this->get_client_ip());

            $wrapper->geoip_close();

            $allowCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow'));
            $this->addAllowedCountry($allowCountries);
        }
    }

	function get_client_ip() {
		 $ipaddress = '';
		 if (getenv('HTTP_CLIENT_IP'))
			 $ipaddress = getenv('HTTP_CLIENT_IP');
		 else if(getenv('HTTP_X_FORWARDED_FOR'))
			 $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		 else if(getenv('HTTP_X_FORWARDED'))
			 $ipaddress = getenv('HTTP_X_FORWARDED');
		 else if(getenv('HTTP_FORWARDED_FOR'))
			 $ipaddress = getenv('HTTP_FORWARDED_FOR');
		 else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		 else if(getenv('REMOTE_ADDR'))
			 $ipaddress = getenv('REMOTE_ADDR');
		 else
			 $ipaddress = 'UNKNOWN';

		 return $ipaddress; 
	}

    public function getCountry()
    {
		//$this->country="FR";
		//echo ($this->country);
		//die();
        return $this->country;
    }

    public function isCountryAllowed($country = '')
    {
        $country = $country ? $country : $this->country;
        if (count($this->allowed_countries) && $country) {
            return in_array($country, $this->allowed_countries);
        } else {
            return true;
        }
    }

    public function addAllowedCountry($countries)
    {
        $countries = is_array($countries) ? $countries : array($countries);
        $this->allowed_countries = array_merge($this->allowed_countries, $countries);

        return $this;
    }
}